<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\OrgGroupStudents;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class AddGroupStudent extends ContainerAwareJob
{

    private $groupStudentRepo;

    public function run($args)
    {
        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $orgId = $args['orgId'];
        $groupId = $args['groupId'];

        $personService = $this->getContainer()->get('person_service');
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $cache = $this->getContainer()->get('synapse_redis_cache');
        $organizationRepository = $repositoryResolver->getRepository('SynapseCoreBundle:Organization');
        $orgGroupRepository = $repositoryResolver->getRepository('SynapseCoreBundle:OrgGroup');
        $orgGroupStudentsRepository = $repositoryResolver->getRepository('SynapseCoreBundle:OrgGroup');
        $validator = $this->getContainer()->get('group_student_upload_validator_service');
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $this->orgStudentRepo = $repositoryResolver->getRepository('SynapseCoreBundle:OrgPersonStudent');

        $this->groupStudentRepo = $repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupStudents');
        $errors = [];

        $validRows = 0;

        $requiredItems = [
            UploadConstant::EXTERNALID
        ];

        $organization = $organizationRepository->findOneById($orgId);
        $orgGroup = $orgGroupRepository->findOneById($groupId);

        foreach ($creates as $id => $data) {

            $requiredMissing = false;

            foreach ($requiredItems as $item) {

                if (! array_key_exists(strtolower($item), $data) || empty($data[strtolower($item)])) {
                    $errors[$id][] = [
                        'name' => $item,
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                            "$item is a required field"
                        ]
                    ];
                    $requiredMissing = true;
                }

                if (! $validator->validate(strtolower($item), $data[strtolower($item)], $orgId)) {
                    $errors[$id][] = [
                        'name' => $item,
                        UploadConstant::VALUE => $data[strtolower($item)],
                        UploadConstant::ERRORS => $validator->getErrors()
                    ];
                    $requiredMissing = true;
                }
            }

            if ($requiredMissing) {
                continue;
            }


            $person = $this->orgStudentRepo->getPersonStudentByExternalId($data[strtolower(UploadConstant::EXTERNALID)], $orgId);

            if (! $person) {
                $errors[$id][] = [
                    'name' => UploadConstant::EXTERNALID,
                    UploadConstant::VALUE => '',
                    UploadConstant::ERRORS => [
                        "Student ID does not exist"
                    ]
                ];
                continue;
            }
            $groupStudent = $this->groupStudentRepo->findOneBy([
                'organization' => $orgId,
                'orgGroup' => $orgGroup,
                'person' => $person
                ]);
            if($groupStudent)
            {
                $errors[$id][] = [
                'name' => UploadConstant::EXTERNALID,
                UploadConstant::VALUE => '',
                UploadConstant::ERRORS => [
                $person->getFirstname() ." ".$person->getLastname()." already exists in " . $orgGroup->getGroupName()
                    ]
                    ];

                continue;
            }

            $orgGroupStudents = new OrgGroupStudents();
            $orgGroupStudents->setOrganization($organization);
            $orgGroupStudents->setPerson($person);
            $orgGroupStudents->setOrgGroup($orgGroup);
            $orgGroupStudentsRepository->persist($orgGroupStudents);

            $rbacManager = $this->getContainer()->get('tinyrbac.manager');
            $rbacManager->refreshPermissionCache($person->getId());

            $validRows ++;
        }

        $uploadFileLogService->updateValidRowCount($uploadId, $validRows);
        $uploadFileLogService->updateCreatedRowCount($uploadId, $validRows);
        $uploadFileLogService->updateErrorRowCount($uploadId, count($errors));

        $jobs = $cache->fetch("organization.{$orgId}.upload.{$uploadId}.jobs");
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $cache->save("organization.{$orgId}.upload.{$uploadId}.jobs", $jobs);

        $cache->save("organization:{$orgId}:upload:{$uploadId}:job:{$jobNumber}:errors", $errors);

        return $errors;
    }
}
