<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Constants\GroupUploadConstants;
use Synapse\CoreBundle\Entity\OrgGroupStudents;
use Synapse\CoreBundle\Util\Constants\GroupConstant;
use Synapse\UploadBundle\Service\Impl\GroupUploadValidatorService;

/**
 * This job as part bulk faculty group upload
 * This is calling from GroupStudentBulkUploadService
 */
class AddStudentGroup extends ContainerAwareJob
{

    const LOGGER = 'logger';

    const VALIDATOR_SERVICE = 'groupupload_validator_service';

    const VALUE = 'value';

    const ERRORS = 'errors';

    public $errors = [];

    private $groupRepo;

    private $groupStudentRepo;

    private $orgStudentRepo;

    private $validRows;

    private $createdRows;

    private $unchangedRows;

    private $deletedRows;

    /**
     * @var GroupUploadValidatorService
     */
    private $groupUploadValidatorService;

    public function run($args)
    {
        $logger = $this->getContainer()->get(self::LOGGER);

        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $userId = $args['userId'];
        $organizationId = $args['orgId'];
        $this->errors = [];
        $this->validRows = 0;
        $this->unchangedRows = 0;
        $this->deletedRows = 0;
        $this->createdRows = 0;

        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $cache = $this->getContainer()->get('synapse_redis_cache');
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $orgService = $this->getContainer()->get('org_service');
        $this->groupUploadValidatorService = $this->getContainer()->get(self::VALIDATOR_SERVICE);
        $personService = $this->getContainer()->get('person_service');

        $this->groupRepo = $repositoryResolver->getRepository('SynapseCoreBundle:OrgGroup');

        $this->groupStudentRepo = $repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupStudents');
        $this->orgStudentRepo = $repositoryResolver->getRepository('SynapseCoreBundle:OrgPersonStudent');
        $organization = $orgService->find($organizationId);
        $logger->info("After all init");
        foreach ($creates as $id => $data) {

            $logger->info(GroupUploadConstants::MODULE_NAME . __FILE__ . __FUNCTION__ . " :  " . $data[strtolower(GroupUploadConstants::GROUP_ID)]);

            $requiredMissing = false;
            $requiredMissing = $this->checkRequired($id, $data);
            if ($requiredMissing) {
                continue;
            }

            /**
             * Checking there is ALL Student Group Id
             */
            if(isset( $data[strtolower(GroupUploadConstants::REMOVE)]) && strtolower(GroupConstant::SYS_GROUP_EXTERNAL_ID) == strtolower($data[strtolower(GroupUploadConstants::GROUP_ID)]) && strtolower($data[strtolower(GroupUploadConstants::REMOVE)]) == 'remove')
            {
                $this->errors[$id][] = [
                'name' => GroupUploadConstants::GROUP_ID,
                self::VALUE => '',
                self::ERRORS => [
                    "Students cannot be removed from the All Students Group."
                    ]
                    ];
                continue;
            }
            $person = $this->orgStudentRepo->getPersonStudentByExternalId($data[strtolower(GroupUploadConstants::EXTERNAL_ID)], $organizationId);
            if (! $person) {
                $this->errors[$id][] = [
                    'name' => GroupUploadConstants::EXTERNAL_ID,
                    self::VALUE => '',
                    self::ERRORS => [
                        "External ID does not exist"
                    ]
                ];
                continue;
            }

            $group = $this->groupUploadValidatorService->validateContents(GroupUploadConstants::GROUP_ID, $data[strtolower(GroupUploadConstants::GROUP_ID)], array(
                'class' => 'SynapseCoreBundle:OrgGroup',
                'keys' => array(
                    'externalId' => $data[strtolower(GroupUploadConstants::GROUP_ID)],
                    'organization' => $organizationId
                )
            ), "Group ID does not exist");

            $groupStudent = $this->groupStudentRepo->findOneBy([
                'organization' => $organizationId,
                'orgGroup' => $group,
                'person' => $person
            ]);

            if (strtolower($data[strtolower(GroupUploadConstants::REMOVE)]) == strtolower(GroupUploadConstants::REMOVE) && (! $groupStudent)) {
                $this->errors[$id][] = [
                    'name' => GroupUploadConstants::REMOVE,
                    self::VALUE => '',
                    self::ERRORS => [
                        "ExternalId does not exist"
                    ]
                ];
                continue;
            }
            $this->groupUploadValidatorService->validateStudentCommandCol($data);
            $errorsTrack = $this->groupUploadValidatorService->getErrors();

            if (sizeof($errorsTrack) > 0) {
                $this->errors[$id] = $errorsTrack;
                $this->groupUploadValidatorService->clearErrors();
            } else {

                $this->manageStudent($groupStudent, $organization, $group, $person, $data);
                $this->validRows ++;
            }
        }

        $uploadFileLogService->updateValidRowCount($uploadId, $this->validRows);
        $uploadFileLogService->updateCreatedRowCount($uploadId, $this->createdRows);
        $uploadFileLogService->updateErrorRowCount($uploadId, count($this->errors));
        $uploadFileLogService->updateUpdatedRowCount($uploadId, $this->deletedRows + $this->unchangedRows);

        $this->groupRepo->flush();
        $this->groupRepo->clear();
        $jobs = $cache->fetch("groupstudentbulk.upload.{$uploadId}.jobs");
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $cache->save("groupstudentbulk.upload.{$uploadId}.jobs", $jobs);

        $cache->save("groupstudentbulk.upload.{$uploadId}.job.{$jobNumber}.errors", $this->errors);

        return $this->errors;
    }

    public function manageStudent($groupStudent, $organization, $group, $person, $data)

    {
        $logger = $this->getContainer()->get(self::LOGGER);
        if ($groupStudent) {
            /**
             * Update Or Remove
             */

            $logger->info("$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ I AM UPATING/REMOVING" . $data[strtolower(GroupUploadConstants::REMOVE)]);

            if (strtolower($data[strtolower(GroupUploadConstants::REMOVE)]) == 'remove') {
                $logger->info("$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ /REMOVING");
                $this->groupStudentRepo->remove($groupStudent);
                $this->deletedRows ++;
            } else {
                $this->unchangedRows ++;
            }
        } else {
            /**
             * Insert
             */
            $groupStudent = new OrgGroupStudents();

            $groupStudent->setOrganization($organization);
            $groupStudent->setOrgGroup($group);
            $groupStudent->setPerson($person);
            $this->groupStudentRepo->persist($groupStudent, false);

            $rbacManager = $this->getContainer()->get('tinyrbac.manager');
            $rbacManager->refreshPermissionCache($person->getId());

            $this->createdRows ++;
        }
    }

    public function getRequired()
    {
        return [
            GroupUploadConstants::GROUP_ID,
            GroupUploadConstants::EXTERNAL_ID
        ];
    }

    public function checkRequired($id, $data)
    {
        $requiredMissing = false;
        $requiredItems = $this->getRequired();
        foreach ($requiredItems as $item) {
            if (! array_key_exists(strtolower($item), $data) || empty($data[strtolower($item)])) {
                $this->errors[$id][] = [
                    'name' => $item,
                    self::VALUE => '',
                    self::ERRORS => [
                        "{$item} is a required field"
                    ]
                ];
                $requiredMissing = true;
            }
        }
        return $requiredMissing;
    }
}