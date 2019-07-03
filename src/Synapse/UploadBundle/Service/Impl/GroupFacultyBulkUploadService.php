<?php
namespace Synapse\UploadBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use Doctrine\Common\Cache\RedisCache;
use JMS\DiExtraBundle\Annotation as DI;
use SplFileObject;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\GroupUploadConstants;
use Synapse\CoreBundle\Util\CSVReader;
use Synapse\UploadBundle\Job\UpdateGroupFacultyDataFile;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Handle Academic Update bulk uploads for group faculty
 *
 * @DI\Service("groupfacultybulk_upload_service")
 */
class GroupFacultyBulkUploadService extends AbstractService
{

    // Constants

    const SERVICE_KEY = 'groupfacultybulk_upload_service';

    const UPLOAD_ERROR = 'Upload Errors';

    
    // Private variables
    
    /**
     * @var array
     */
    private $affectedExternalIds;

    /**
     * @var array
     */
    private $creatable;

    /**
     * @var array
     */
    private $creates;

    /**
     * @var CSVReader - Object containing the loaded file
     */
    private $fileReader;

    /**
     * @var array
     */
    private $jobs;

    /**
     * @var int
     */
    private $orgId;

    /**
     * @var string
     */
    private $queue;

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
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Resque
     */
    private $resque;

    /**
     * @var \Swift_Transport
     */
    private $swiftTransport;


    //Services

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
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var UploadFileLogService
     */
    private $uploadFileLogService;


    // Repositories

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;


    /**
     * GroupFacultyBulkUploadService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container"),
     *            })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     *
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        // Member variable initialization
        $this->creates = [];
        $this->jobs = [];
        $this->queue = 'default';

        // Scaffolding
        $this->cache = $this->container->get(SynapseConstant::REDIS_CLASS_KEY);
        $this->mailer = $this->container->get(SynapseConstant::MAILER_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->swiftTransport = $this->container->get(SynapseConstant::SWIFT_MAILER_TRANSPORT_REAL_KEY);

        // Services
        $this->alertService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->uploadFileLogService = $this->container->get(UploadFileLogService::SERVICE_KEY);

        // Repositories
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgGroupFacultyRepository::REPOSITORY_KEY);
    }

    /**
     * Loads the csv file as a CSV Reader Object
     *
     * @param $filePath - the path to the file in AWS
     * @param $orgId - the organization doing the upload
     * @param $userId - the person performing the upload
     * @return array
     * @throws \Exception
     */
    public function load($filePath, $orgId, $userId)
    {
        $this->logger->info("*************************************************************************");
        $this->logger->info(GroupUploadConstants::MODULE_NAME . __FILE__ . " loading");

        if (! file_exists($filePath)) {
            $this->logger->error("File NOT Found " . $filePath);
            throw new \Exception("File not found");
        }

        $this->userId = $userId;
        $this->orgId = $orgId;
        $this->fileReader = new CSVReader($filePath, true, true);

        $this->affectedExternalIds = [];

        $rowVal = 0;

        foreach ($this->fileReader as $idx => $row) {
            $this->affectedExternalIds[] = $row[strtolower(GroupUploadConstants::GROUP_ID)] . "-" . $row[strtolower(GroupUploadConstants::EXTERNAL_ID)];
        }

        $this->creatable = $this->affectedExternalIds;
        $this->totalRows = count($this->creatable);
        $fileData = [
            'totalRows' => $this->totalRows,
            'new' => count($this->creatable)
        ];

        return $fileData;
    }

    /**
     * Processes the currently loaded file
     *
     * @param $uploadId
     * @return array|bool Returns array containg information about the upload, or false on failure
     */
    public function process($uploadId)
    {
        $this->logger->info("*************************************************************************");
        $this->logger->info(GroupUploadConstants::MODULE_NAME . __FUNCTION__ . "  processing");
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
        $batchSize = 30;
        $i = 1;
        foreach ($this->fileReader as $idx => $row) {
            $this->logger->info("^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^");

            $this->logger->info(GroupUploadConstants::MODULE_NAME . __FUNCTION__ . $idx);
            $processingRow = $row[strtolower(GroupUploadConstants::GROUP_ID)] . "-" . $row[strtolower(GroupUploadConstants::EXTERNAL_ID)];

            $this->logger->info($processingRow);
            if ($idx === 0) {
                continue;
            }

            if (in_array($processingRow, $processed)) {
                continue;
            }

            if (in_array($processingRow, $this->creatable)) {
                $this->create($idx, $row);
            }

            if (($i % $batchSize) === 0) {
                $this->queueForWrite();
            }

            $processed[] = $processingRow;
            $i ++;
        }

        $this->queueForWrite();

        $this->cache->save("groupfacultybulk.upload.{$this->uploadId}.jobs", $this->jobs);

        if (! count($this->jobs)) {
            $this->uploadFileLogService->updateValidRowCount($uploadId, 0);
        }

        return [
            'jobs' => $this->jobs
        ];
    }


    /**
     * Sends an email and an alert notification about completion of an upload to each coordinator, and also provides a link to the errors file for downloading
     *
     * @param int $organizationId
     * @param string $groupUploadType
     * @param int $errorCount
     * @param string $downloadFailedLogFile
     */
    public function sendFTPNotification($organizationId, $groupUploadType, $errorCount, $downloadFailedLogFile)
    {
        $coordinators = $this->organizationRoleRepository->getCoordinators($organizationId);

        $groupUploadType = ucfirst($groupUploadType);
        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);

        foreach ($coordinators as $coordinator) {
            $this->sendAlert($coordinator->getPerson(), "{$groupUploadType}_Upload_Notification", "{$groupUploadType} Import", $errorCount, $downloadFailedLogFile);
            $this->emailService->sendUploadCompletionEmail($coordinator->getPerson(), $organizationId, "{$groupUploadType}_Upload_Notification", $errorCount, $systemUrl . "#/errors/group_uploads/{$this->orgId}-{$this->uploadId}-upload-errors.csv");
        }

        $transport = $this->container->get('mailer')->getTransport();
        if (!$transport instanceof \Swift_Transport_SpoolTransport) {
            return;
        }

        $spool = $transport->getSpool();
        if (!$spool instanceof \Swift_MemorySpool) {
            return;
        }

        $spool->flushQueue($this->swiftTransport);
    }

    /**
     * Calls createNotification() from AlertNotificationsServices
     *
     * @param Person $user
     * @param string $alertKey
     * @param string $reason
     * @param int $errorCount
     * @param string|null $uploadFile
     */
    private function sendAlert($user, $alertKey, $reason, $errorCount, $uploadFile = null)
    {
        $errorFileFlag = false;

        if ($errorCount > 0) {
            $errorFileFlag = true;
        }

        $this->alertService->createNotification($alertKey, $reason, $user, null, null, null, $uploadFile, null, null, null, $errorFileFlag);
    }

     /**
     * @param string $orgId
     * @param array $errors
     * @return string
     */
    public function generateErrorCSV($orgId,$errors)
    {
        $list = [];
        $this->logger->info(__FUNCTION__);
        foreach ($this->fileReader as $idx => $row) {
            if (isset($errors[$idx])) {
                $rowErrors = '';
                $rowErrors = $this->createRow($idx, $errors, $rowErrors);
                $row[self::UPLOAD_ERROR] = $rowErrors;
                $list[] = $row;
            }
        }
        $this->logger->info(__FUNCTION__);

        $errorCSVFile = new SplFileObject("data://group_uploads/errors/{$orgId}-{$this->uploadId}-upload-errors.csv", 'w');

        $csvHeaders = $this->fileReader->getColumns();
        $csvHeaders[self::UPLOAD_ERROR] = self::UPLOAD_ERROR;
        $errorCSVFile->fputcsv($csvHeaders);

        foreach ($list as $fields) {
            $errorCSVFile->fputcsv($fields);
        }

        $errorFilename = "errors/{$orgId}-{$this->uploadId}-upload-errors.csv";
        return $errorFilename;
    }

    /**
     * @param string $idx
     * @param array $errors
     * @param string $rowErrors
     * @return string
     */
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

    /**
     * @param string $idx
     * @param $person
     * @return void
     */
    private function create($idx, $person)
    {
        $this->logger->info(__FUNCTION__);
        $this->creates[$idx] = $person;
    }

    /**
     * @return void
     */
    private function queueForWrite()
    {
        if (count($this->creates)) {
            $this->logger->info(__FUNCTION__);

            $createObject = 'Synapse\UploadBundle\Job\AddFacultyGroup';
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
     * Creates the group Faculty dump file
     * @param $orgId
     */
    public function createExisting($orgId)
    {
        $this->logger->info("****************************************************" . __FUNCTION__);
        $groupDetails = $this->orgGroupFacultyRepository->getGroupFacultyList($orgId);
        $filename = "{$orgId}-faculty-bulk-existing.csv";
        $file = new SplFileObject("data://group_uploads/{$filename}", 'w');
        $header = $this->getHeader();
        $file->fputcsv($header);
        if (count($groupDetails) > 0) {
            foreach ($groupDetails as $groupDetail) {
                $temp = [];
                $temp[GroupUploadConstants::EXTERNAL_ID] = $groupDetail['ExternalId'];
                $temp[GroupUploadConstants::FIRSTNAME] = $groupDetail['Firstname'];
                $temp[GroupUploadConstants::LASTNAME] = $groupDetail['Lastname'];
                $temp[GroupUploadConstants::PRIMARY_EMAIL] = $groupDetail['PrimaryEmail'];
                $temp[GroupUploadConstants::FULL_PATH_NAMES] = $groupDetail['FullPathNames'];
                $temp[GroupUploadConstants::FULL_PATH_GROUP_IDS] = $groupDetail['FullPathGroupIDs'];
                $temp[GroupUploadConstants::GROUP_NAME] = $groupDetail['GroupName'];
                $temp[GroupUploadConstants::GROUP_ID] = $groupDetail['GroupId'];
                $temp[GroupUploadConstants::PERMISSION_SET] = $groupDetail['PermissionSet'];
                $temp[GroupUploadConstants::INVISIBLE] = $groupDetail['Invisible'];
                $temp[GroupUploadConstants::REMOVE] = "";
                $file->fputcsv($temp);
            }
        }
    }

    /**
     * This will generate the existing download CSV file on the dump files queue.
     *
     * @param int $orgId
     * @return mixed
     */
    public function updateDataFile($orgId)
    {
        $job = new UpdateGroupFacultyDataFile();
        $job->args = array(
            'orgId' => $orgId
        );

        return $this->resque->enqueue($job, true);
    }

    /**
     * Returns an array of the header column names.
     *
     * @return array
     */
    public function getHeader()
    {
        $header = [
            GroupUploadConstants::EXTERNAL_ID,
            GroupUploadConstants::FIRSTNAME,
            GroupUploadConstants::LASTNAME,
            GroupUploadConstants::PRIMARY_EMAIL,
            GroupUploadConstants::FULL_PATH_NAMES,
            GroupUploadConstants::FULL_PATH_GROUP_IDS,
            GroupUploadConstants::GROUP_NAME,
            GroupUploadConstants::GROUP_ID,
            GroupUploadConstants::PERMISSION_SET,
            GroupUploadConstants::INVISIBLE,
            GroupUploadConstants::REMOVE,
        ];
        return $header;
    }
}