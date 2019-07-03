<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\StaticListBundle\Entity\OrgStaticListStudents;
use Synapse\StaticListBundle\Repository\OrgStaticListRepository;
use Synapse\StaticListBundle\Repository\OrgStaticListStudentsRepository;
use Synapse\UploadBundle\Service\Impl\SynapseValidatorService;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;

class CreateStaticList extends ContainerAwareJob
{
    // Scaffolding
    private $cache;

    /**
     * @var Manager
     */
    private $rbacManager;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    //Services

    /**
     * @var SynapseValidatorService
     */
    private $synapseValidatorService;

    /**
     * @var UploadFileLogService
     */
    private $uploadFileLogService;


    // Repositories

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionSetRepository;

    /**
     * @var OrgStaticListStudentsRepository
     */
    private $orgStaticListStudentsRepository;

    public function run($args)
    {
        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $orgId = $args['orgId'];
        $userId = $args['userId'];
        $orgStaticListId = $args['staticListId'];

        $errors = [];
        $validRows = 0;
        $unchangedRowCount = 0;
        $createdRowCount = 0;

        $requiredItems = ['StudentId'];

        // Scaffolding
        $this->cache = $this->getContainer()->get(SynapseConstant::REDIS_CLASS_KEY);
        $this->rbacManager = $this->getContainer()->get(Manager::SERVICE_KEY);
        $this->repositoryResolver = $this->getContainer()->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);

        //Services
        $this->synapseValidatorService = $this->getContainer()->get(SynapseValidatorService::SERVICE_KEY);
        $this->uploadFileLogService = $this->getContainer()->get(UploadFileLogService::SERVICE_KEY);


        // Repositories
        $this->orgPermissionSetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgStaticListStudentsRepository = $this->repositoryResolver->getRepository(OrgStaticListStudentsRepository::REPOSITORY_KEY);

        $orgStaticList = "";

        foreach ($creates as $id => $data) {

            $requiredMissing = false;

            foreach ($requiredItems as $item) {

                // We wait until the last possible moment to lowercase the required
                //  items so we can display camel case column headers to the user
                if (!array_key_exists(strtolower($item), $data) || (empty($data[strtolower($item)]) && $data[strtolower($item)] != 0)) {
                    $errors[$id][] = [
                        'name' => $item,
                        'value' => '',
                        'errors' => [
                            "{$item} is a required field"
                        ]
                    ];
                    $requiredMissing = true;
                }
            }

            if ($requiredMissing) {
                continue;
            }

            $personStudentId = $data['studentid'];

            $organization = $this->synapseValidatorService->validateContents('Organization', $orgId, array(
                'class' => OrganizationRepository::REPOSITORY_KEY,
                'keys' => array(
                    'id' => $orgId
                )
            ));

            $personStudent = $this->synapseValidatorService->validateContents('StudentId', $personStudentId, array(
                'class' => PersonRepository::REPOSITORY_KEY,
                'keys' => array(
                    'organization' => $organization,
                    'externalId' => $personStudentId
                )
            ));

            $personFaculty = $this->synapseValidatorService->validateContents('Coordinator or Faculty/Staff ID', $userId, array(
                'class' => PersonRepository::REPOSITORY_KEY,
                'keys' => array(
                    'organization' => $organization,
                    'id' => $userId
                )
            ));

            $orgStaticList = $this->synapseValidatorService->validateContents('Static List ID', $orgStaticListId, array(
                'class' => OrgStaticListRepository::REPOSITORY_KEY,
                'keys' => array(
                    'id' => $orgStaticListId,
                    'organization' => $organization,
                    'person' => $personFaculty
                ),
                'Static List Doesn\'t exist'
            ));

            $denied = 0;

            if (is_object($personStudent) && is_object($personFaculty) && is_object($orgStaticList) && !$this->rbacManager->hasCoordinatorAccess($personFaculty)) {
                $checkFacultyAccessToStudent = $this->orgPermissionSetRepository->checkAccessToStudent($userId, $personStudent->getId());
                if (!$checkFacultyAccessToStudent) {
                    $this->synapseValidatorService->setErrors(['name' => 'StudentId', 'value' => '', 'errors' => ["{$personStudentId} cannot be added due to permission constraints. "]]);
                    $denied = 1;
                }
            }

            if ($orgStaticList && $denied != 1) {
                $staticListStudentsEntity = $this->orgStaticListStudentsRepository->findOneBy([
                    'organization' => $organization,
                    'orgStaticList' => $orgStaticList,
                    'person' => $personStudent
                ]);
                if ($staticListStudentsEntity) {
                    $unchangedRowCount++;
                    continue;
                }
            }

            $errorsTrack = $this->synapseValidatorService->getErrors();
            $this->synapseValidatorService->clearErrors();
            if (sizeof($errorsTrack) > 0) {
                $errors[$id] = $errorsTrack;
            } else {
                $staticListStudentsEntityObj = new OrgStaticListStudents();
                $staticListStudentsEntityObj->setOrganization($organization);
                $staticListStudentsEntityObj->setPerson($personStudent);
                $staticListStudentsEntityObj->setOrgStaticList($orgStaticList);
                $this->orgStaticListStudentsRepository->persist($staticListStudentsEntityObj, false);
                $createdRowCount++;
                $validRows++;
                $errorsTrack = $this->synapseValidatorService->getErrors();
                if (sizeof($errorsTrack) > 0) {
                    $errors[$id] = $errorsTrack;
                }
            }
        }

        // update valid rows and other row information
        $this->uploadFileLogService->updateValidRowCount($uploadId, $validRows);
        $this->uploadFileLogService->updateCreatedRowCount($uploadId, $createdRowCount);
        $this->uploadFileLogService->updateUpdatedRowCount($uploadId, $unchangedRowCount);
        $this->uploadFileLogService->updateErrorRowCount($uploadId, count($errors));

        // if you want to include unchanged rows for the upload, uncomment this and make sure
        // that the upload includes unchanged rows as a valid row.

        $this->orgStaticListStudentsRepository->flush();
        $this->orgStaticListStudentsRepository->clear();
        $jobs = $this->cache->fetch("organization.{$orgId}.upload.{$uploadId}.jobs");
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $this->cache->save("organization.{$orgId}.upload.{$uploadId}.jobs", $jobs);
        $this->cache->save("organization:{$orgId}:upload:{$uploadId}:job:{$jobNumber}:errors", $errors);
        return $errors;
    }
}
