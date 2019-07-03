<?php
namespace Synapse\UploadBundle\Service\Impl;

use BCC\ResqueBundle\Queue;
use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Snc\RedisBundle\Doctrine\Cache\RedisCache;
use SoftDeleteable\Fixture\Entity\User;
use SplFileObject;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgGroupRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\GroupUploadConstants;
use Synapse\CoreBundle\Util\CSVReader;
use Synapse\CoreBundle\Util\Helper;
use Synapse\UploadBundle\Job\UpdateGroupDataFile;
use Synapse\UploadBundle\Util\Constants\UploadConstant;


/**
 * Handle group uploads
 *
 * @DI\Service("group_upload_service")
 */
class GroupUploadService extends AbstractService
{

    // Constants

    const SERVICE_KEY = 'group_upload_service';

    const UPLOAD_ERROR = 'Upload Errors';

    // Scaffolding

    /**
     * @var RedisCache
     */
    private $cache;

    /**
     * @var Container
     */
    private $container;

    /**
     * Object containing the loaded file
     *
     * @var CSVReader
     */
    private $fileReader;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    // Private variables

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var Resque
     */
    private $resque;

    /**
     * @var \Swift_Transport
     */
    private $swiftMailer;

    // Member Variables

    /**
     * @var array
     */
    private $affectedExternalIds;

    /**
     * @var array
     */
    private $createable;

    /**
     * @var array
     */
    private $creates;

    /**
     * @var array
     */
    private $jobs;

    /**
     * @var int
     */
    private $orgId;

    /**
     * @var int
     */
    private $totalRows;

    /**
     * @var int
     */
    private $uploadId;

    /**
     * @var int
     */
    private $userId;

    // Services

    /**
     * @var AlertNotificationsService
     */
    private $alertService;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var UploadFileLogService
     */
    private $uploadFileLogService;

    // Repositories

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var OrgGroupRepository
     */
    private $orgGroupRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * GroupUploadService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        //scaffolding
        $this->container = $container;
        $this->creates = [];
        $this->jobs = [];
        $this->queue = 'default';
        $this->cache = $this->container->get(SynapseConstant::REDIS_CLASS_KEY);
        $this->mailer = $this->container->get(SynapseConstant::MAILER_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->swiftMailer = $this->container->get(SynapseConstant::SWIFT_MAILER_TRANSPORT_REAL_KEY);

        //Class Variables
        $this->creates = [];
        $this->jobs = [];
        $this->queue = 'default';

        // Services
        $this->alertService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->uploadFileLogService = $this->container->get(UploadFileLogService::SERVICE_KEY);

        // Repositories
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgGroupRepository = $this->repositoryResolver->getRepository(OrgGroupRepository::REPOSITORY_KEY);

    }

    public function load($filePath, $orgId, $userId)
    {
        $this->logger->info("*************************************************************************");
        $this->logger->info(GroupUploadConstants::MODULE_NAME . __FILE__ . " loading");

        if (! file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        $this->userId = $userId;
        $this->orgId = $orgId;
        $this->fileReader = new CSVReader($filePath, true, true);

        $this->affectedExternalIds = [];


        foreach ($this->fileReader as $idx => $row) {
            $this->affectedExternalIds[] = $row[strtolower(GroupUploadConstants::PARENT_GROUP_ID)] . "-" . $row[strtolower(GroupUploadConstants::GROUP_ID)] . "-" . $row[strtolower(GroupUploadConstants::GROUP_NAME)];
        }

        $this->createable = $this->affectedExternalIds;
        $this->totalRows = count($this->createable);
        $fileData = [
            'totalRows' => $this->totalRows,
            'new' => count($this->createable)
        ];

        return $fileData;
    }

    /**
     * Processes the currently loaded file
     *
     * @return array|bool Returns array containg information about the upload, or false on failure
     */
    public function process($uploadId)
    {
        $this->logger->info("*************************************************************************");
        $this->logger->info(GroupUploadConstants::MODULE_NAME . __FUNCTION__ . "  proccessing");
        $this->uploadId = $uploadId;
        try {
            if ($this->totalRows < UploadConstant::EXPRESS_QUEUE_COUNT) {
                $this->queue = UploadConstant::EXPRESS_QUEUE;
            } else {
                $queues = json_decode($this->ebiConfigService->get('Upload_Queues'));
                $this->queue = $queues[mt_rand(0, count($queues) - 1)];
            }
        } catch (\Exception $e) {
            $this->queue = 'default';
        }
        $processed = [];
        $i = 1;
        foreach ($this->fileReader as $idx => $row) {

            $this->logger->info("^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^");

            $this->logger->info(GroupUploadConstants::MODULE_NAME . __FUNCTION__ . $idx);
            $processingRow = $row[strtolower(GroupUploadConstants::PARENT_GROUP_ID)] . "-" . $row[strtolower(GroupUploadConstants::GROUP_ID)] . "-" . $row[strtolower(GroupUploadConstants::GROUP_NAME)];
            $this->logger->info($processingRow);
            if ($idx === 0) {
                continue;
            }

            if (in_array($processingRow, $processed)) {
                continue;
            }

            if (in_array($processingRow, $this->createable)) {
                $this->create($idx, $row);
            }

            $processed[] = $processingRow;
            $i ++;
        }

        $this->queueForWrite();

        $this->cache->save("subgroup.upload.{$this->uploadId}.jobs", $this->jobs);

        if (! count($this->jobs)) {
            $this->uploadFileLogService->updateValidRowCount($uploadId, 0);
        }

        return [
            'jobs' => $this->jobs
        ];
    }

    /**
     * Sends the FTPnotifications/emails
     *
     * @param int $organizationId
     * @param string $entity
     * @param int $errorCount
     * @param string $downloadFailedLogFile
     */
    public function sendFTPNotification($organizationId, $entity, $errorCount, $downloadFailedLogFile)
    {
        $coordinators = $this->organizationRoleRepository->getCoordinators($organizationId);

        $subjectEntity = ucfirst($entity);
        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);

        foreach ($coordinators as $coordinator) {
            $this->sendAlert($coordinator->getPerson(), $organizationId, "{$subjectEntity}_Upload_Notification", "{$subjectEntity} Import", $errorCount, $downloadFailedLogFile);
            $this->emailService->sendUploadCompletionEmail($coordinator->getPerson(), $organizationId, "{$subjectEntity}_Upload_Notification", $errorCount, $systemUrl . "#/errors/group_uploads/{$this->orgId}-{$this->uploadId}-upload-errors.csv");
        }

        $transport = $this->mailer->getTransport();
        if (!$transport instanceof \Swift_Transport_SpoolTransport) {
            return;
        }

        $spool = $transport->getSpool();
        if (!$spool instanceof \Swift_MemorySpool) {
            return;
        }

        $spool->flushQueue($this->container->get('swiftmailer.transport.real'));
    }

    private function sendAlert($user, $orgId, $alertKey, $reason, $errorCount, $uploadFile = null)
    {
        $errorFileFlag = false;

        if ($errorCount > 0) {
            $errorFileFlag = true;
        }
            $this->alertService->createNotification($alertKey, $reason, $user, null, null, null, $uploadFile, null, null, null, $errorFileFlag);
    }

    /**
     * Function to create error CSV
     *
     * @param int $orgId
     * @param array $errors
     * @return string $errorFilename
     */
    public function generateErrorCSV($orgId,$errors)
    {
        $list = [];
        $this->logger->info(__FUNCTION__);
        foreach ($this->fileReader as $idx => $row) {
            if (isset($errors[$idx])) {
                $rowErrors = '';
                $rowErrors = $this->createRow($idx, $errors, $rowErrors);
                $row[strtolower(self::UPLOAD_ERROR)] = $rowErrors;
                $list[] = $row;
            }
        }
        $this->logger->info(__FUNCTION__);

        $errorCSVFile = new SplFileObject("data://group_uploads/errors/{$orgId}-{$this->uploadId}-upload-errors.csv", 'w');

        $csvHeaders = $this->fileReader->getColumns();
        $csvHeaders[strtolower(self::UPLOAD_ERROR)] = self::UPLOAD_ERROR;
        $errorCSVFile->fputcsv($csvHeaders);

        foreach ($list as $fields) {
            $errorCSVFile->fputcsv($fields);
        }

        $errorFilename = "errors/{$this->orgId}-{$this->uploadId}-upload-errors.csv";
        return $errorFilename;
    }

    public function createRow($idx, $errors, $rowErrors)
    {
        foreach ($errors[$idx] as $id => $column) {
            if ($id) {
                $rowErrors .= "\r";
            }
            if (count($column['errors']) > 0) {
                $rowErrors .= "{$column['name']} - ";
                $rowErrors .= implode("{$column['name']} - ", $column['errors']);
            } else {
                $rowErrors .= "{$column['name']} - {$column['errors'][0]}";
            }
        }
        return $rowErrors;
    }

    private function create($idx, $person)
    {
        $this->logger->info(__FUNCTION__);
        $this->creates[$idx] = $person;
    }

    private function queueForWrite()
    {
        if (count($this->creates)) {

            $this->creates = array_change_key_case($this->creates,CASE_LOWER);

            $this->logger->info(__FUNCTION__);

            $createObject = 'Synapse\UploadBundle\Job\CreateSubGroup';
            $jobNumber = uniqid();
            $job = new $createObject();
            $job->queue = $this->queue;
            $job->args = array(
                'creates' => $this->creates,
                'jobNumber' => $jobNumber,
                'uploadId' => $this->uploadId,
                'userId' => $this->userId,
                'orgId' => $this->orgId
            );

            $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
        }

        $this->creates = [];
    }

    /**
     * Creates existing subgroup CSV
     * @param $orgId
     */
    public function createExisting($orgId)
    {
        $groupDetails = $this->orgGroupRepository->getGroupsWithPathsForOrganization($orgId);
        $file = new SplFileObject("data://group_uploads/{$orgId}-subgroup-existing.csv", 'w');
        $header = $this->getHeader();
        $file->fputcsv($header);
        if (count($groupDetails) > 0) {
            foreach ($groupDetails as $groupDetail) {
                $groupDetailArray = [];
                $groupDetailArray['GroupId'] = $groupDetail['Group_ID'];
                $groupDetailArray['GroupName'] = $groupDetail['Group_Name'];
                $groupDetailArray['ParentGroupId'] = $groupDetail['Parent_Group_ID'];
                $groupDetailArray['FullPathNames'] = $groupDetail['FullPathNames'];
                $groupDetailArray['FullPathGroupIDs'] = $groupDetail['FullPathGroupIDs'];
                $file->fputcsv($groupDetailArray);
            }
        }
    }

    /**
     * Launches the background task to update the data file.
     * @TODO modify this to be more like the original SynapseUploadService implemenation
     *     to reduce code duplication. Create a common method that accepts the job class as a parameter.
     * @param  int $orgId organization ID
     * @return int job ID
     */
    public function updateDataFile($orgId)
    {
        $job = new UpdateGroupDataFile();
        $job->args = array(
            'orgId' => $orgId
        );

        return $this->resque->enqueue($job, true);
    }

    public function getCounts($orgId)
    {
        $response = [];
        $groupRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgGroup");
        $groupFacultyRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgGroupFaculty");
        $groupStudentRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgGroupStudents");
        $response['total_subgroups'] = $groupRepo->getSubGroupCounts($orgId);
        $response['total_faculty'] =  $groupFacultyRepo->getGroupStaffCountOrg($orgId);
        $response['total_student'] = $groupStudentRepo->getGroupStudentCountOrg($orgId);
        return $response;

    }

    /**
     * Returns the CSV headers
     *
     * @return array
     */
    public function getHeader()
    {
        $header = [
            GroupUploadConstants::GROUP_ID,
            GroupUploadConstants::GROUP_NAME,
            GroupUploadConstants::PARENT_GROUP_ID,
            GroupUploadConstants::FULL_PATH_NAMES,
            GroupUploadConstants::FULL_PATH_GROUP_IDS
        ];
        return $header;
    }
}