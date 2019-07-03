<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\OrgGroupFaculty;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class AddGroupFaculty extends ContainerAwareJob
{

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
        $orgGroupFacultyRepository = $repositoryResolver->getRepository('SynapseCoreBundle:OrgGroup');
        $validator = $this->getContainer()->get('group_faculty_upload_validator_service');
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
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
                        UploadConstant::VALUE => $data[$item],
                        UploadConstant::ERRORS => $validator->getErrors()
                    ];
                    $requiredMissing = true;
                }
            }

            if ($requiredMissing) {
                continue;
            }

            $person = $personService->findOneByExternalIdOrg($data[strtolower(UploadConstant::EXTERNALID)], $orgId);

            if (! $person) {
                $errors[$id][] = [
                    'name' => UploadConstant::EXTERNALID,
                    UploadConstant::VALUE => '',
                    UploadConstant::ERRORS => [
                        "Faculty ID does not exist in system"
                    ]
                ];
                continue;
            }

            $orgGroupFaculty = new OrgGroupFaculty();
            $orgGroupFaculty->setOrganization($organization);
            $orgGroupFaculty->setPerson($person);
            $orgGroupFaculty->setOrgGroup($orgGroup);
            $orgGroupFacultyRepository->persist($orgGroupFaculty);

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
