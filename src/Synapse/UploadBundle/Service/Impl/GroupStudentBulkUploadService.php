<?php
namespace Synapse\UploadBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use Doctrine\Common\Cache\RedisCache;
use JMS\DiExtraBundle\Annotation as DI;
use SplFileObject;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\GroupUploadConstants;
use Synapse\CoreBundle\Util\CSVReader;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Handle group student bulk upload
 *
 * @DI\Service("groupstudentbulk_upload_service")
 */
class GroupStudentBulkUploadService extends AbstractService
{

    const SERVICE_KEY = 'groupstudentbulk_upload_service';

    const UPLOAD_ERROR = 'Upload Errors';

    // Member Variables
    /**
     * @var array
     */
    private $affectedExternalIds;

    /**
     * @var array
     */
    private $creates;

    /**
     * @var array
     */
    private $createable;

    /**
     * Object containing the loaded file
     *
     * @var CSVReader
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

    // Services

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationsService;

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
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * GroupStudentBulkUploadService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container"),
     *            })
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        // Member Variables
        $this->creates = [];
        $this->jobs = [];
        $this->queue = 'default';

        // Scaffolding
        $this->cache = $this->container->get(SynapseConstant::REDIS_CLASS_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->mailer = $this->container->get(SynapseConstant::MAILER_KEY);
        $this->swiftTransport = $this->container->get(SynapseConstant::SWIFT_MAILER_TRANSPORT_REAL_KEY);

        // Services
        $this->alertNotificationsService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->uploadFileLogService = $this->container->get(UploadFileLogService::SERVICE_KEY);

        // Repositories
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);

    }

    public function load($filePath, $orgId, $userId)
    {
        $this->logger->info(GroupUploadConstants::MODULE_NAME . __FILE__ . " loading");

        if (!file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        $this->userId = $userId;
        $this->orgId = $orgId;
        $this->fileReader = new CSVReader($filePath, true, true);

        $this->affectedExternalIds = [];

        $rowVal = 0;

        foreach ($this->fileReader as $idx => $row) {
            $this->affectedExternalIds[] = $row[strtolower(GroupUploadConstants::GROUP_ID_LOWER)] . "-" . $row[strtolower(GroupUploadConstants::EXTERNAL_ID_LOWER)];
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
        $batchSize = 30;
        $i = 1;
        foreach ($this->fileReader as $idx => $row) {
            $this->logger->info("^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^");

            $this->logger->info(GroupUploadConstants::MODULE_NAME . __FUNCTION__ . $idx);
            $processingRow = $row[strtolower(GroupUploadConstants::GROUP_ID_LOWER)] . "-" . $row[strtolower(GroupUploadConstants::EXTERNAL_ID_LOWER)];

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

            if (($i % $batchSize) === 0) {
                $this->queueForWrite();
            }

            $processed[] = $processingRow;
            $i++;
        }

        $this->queueForWrite();

        $this->cache->save("groupstudentbulk.upload.{$this->uploadId}.jobs", $this->jobs);

        if (!count($this->jobs)) {
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
            $this->sendAlert($coordinator->getPerson(), $organizationId, "{$groupUploadType}_Upload_Notification", "{$groupUploadType} Import", $errorCount, $downloadFailedLogFile);
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

    private function sendAlert($user, $orgId, $alertKey, $reason, $errorCount, $uploadFile = null)
    {
        $errorFileFlag = false;

        if ($errorCount > 0) {
            $errorFileFlag = true;
        }
        $this->alertNotificationsService->createNotification($alertKey, $reason, $user, null, null, null, $uploadFile, null, null, null, $errorFileFlag);
    }

    public function generateErrorCSV($orgId, $errors)
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
            $this->logger->info(__FILE__ . __FUNCTION__);

            $createObject = 'Synapse\UploadBundle\Job\AddStudentGroup';
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

    public function createExisting($orgId)
    {
        $this->logger->info("****************************************************" . __FUNCTION__);
        $groupRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgGroupStudents");
        $groupDetails = $groupRepo->getGroupStudentsList($orgId);
        $this->logger->info($orgId . "****************************************************" . __FUNCTION__);
        $filename = "{$orgId}-students-bulk-existing.csv";
        $file = new SplFileObject("data://group_uploads/{$filename}", 'w');
        $header = $this->getHeader();
        $file->fputcsv($header);
        if (count($groupDetails) > 0) {
            foreach ($groupDetails as $groupDetail) {
                $temp = [];
                $temp['ExternalId'] = $groupDetail['External_ID'];
                $temp['GroupId'] = $groupDetail['Group_Id'];
                $temp['Remove'] = "";

                $file->fputcsv($temp);
            }
        }
        /*  $ebiConfigService = $this->container->get('ebi_config_service');
         $awsBucket = $this->ebiConfigService->get('AWS_Bucket');
         return "https://{$awsBucket}.s3.amazonaws.com/group-uploads/".$filename; */
        //return GroupUploadConstants::GROUP_AMAZON_URL . $filename;
    }

    public function getHeader()
    {
        $header = [
            GroupUploadConstants::EXTERNAL_ID,
            GroupUploadConstants::GROUP_ID,
            GroupUploadConstants::REMOVE
        ];
        return $header;
    }
}