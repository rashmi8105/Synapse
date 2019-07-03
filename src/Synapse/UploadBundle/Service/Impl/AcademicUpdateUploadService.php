<?php
namespace Synapse\UploadBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use Doctrine\Common\Cache\RedisCache;
use JMS\DiExtraBundle\Annotation as DI;
use SplFileObject;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRepository;
use Synapse\AcademicUpdateBundle\Service\Impl\AcademicUpdateService;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\EmailTemplateRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Util\CSVReader;
use Synapse\UploadBundle\Job\UpdateRiskFlagsJob;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Handle Academic Update uploads
 *
 * @DI\Service("academic_update_upload_service")
 */
class AcademicUpdateUploadService extends AbstractService
{

    const SERVICE_KEY = 'academic_update_upload_service';

    const UNIQUECOURSESECID = 'UniqueCourseSectionId';

    const STUDENTID = 'StudentId';

    const FAILURERISK = 'FailureRisk';

    const UPLOAD_ERROR = 'Upload Errors';

    const INPROGRESSGRADE = 'InProgressGrade';

    const FINALGRADE = 'FinalGrade';

    const ABSENCES = 'Absences';

    const COMMENTS = 'Comments';

    const SENTTOSTUDENT = 'SentToStudent';

    /**
     * Object containing the loaded file
     *
     * @var \SPLFileObject
     */
    private $fileObject;

    /**
     * @var AcademicUpdateService
     */
    private $academicUpdateService;

    /**
     * @var UploadFileLogService
     */
    private $uploadFileLogService;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var array
     */
    private $affectedExternalIds;

    /**
     * @var array
     */
    private $createable;

    /**
     * @var RedisCache
     */
    private $cache;

    /**
     * @var bool
     */
    private $includeSentToStudentColumnInDownload = true;

    /**
     * @var bool
     */
    private $includeReferForAssistanceColumnInDownload = true;

    /**
     * @var Resque
     */
    private $resque;

    /**
     * @var array
     */
    private $studentsNeedingRiskFlagsUpdated;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var array
     */
    private $updates;

    /**
     * @var array
     */
    private $jobs;

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
    private $organizationId;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var AcademicUpdateRepository
     */
    private $academicUpdateRepository;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Swift_Transport
     */
    private $swiftMailer;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var EmailTemplateRepository
     */
    private $emailTemplateRepository;

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
     *
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container"),
     *            })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        $this->cache = $this->container->get('synapse_redis_cache');
        $this->resque = $this->container->get('bcc_resque.resque');
        $this->creates = [];
        $this->updates = [];
        $this->jobs = [];
        $this->queue = 'default';
        $this->mailer = $this->container->get('mailer');
        $this->swiftMailer = $this->container->get('swiftmailer.transport.real');

        // Services
        $this->academicUpdateService = $this->container->get(AcademicUpdateService::SERVICE_KEY);
        $this->alertService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->uploadFileLogService = $this->container->get(UploadFileLogService::SERVICE_KEY);

        // Repositories
        $this->academicUpdateRepository = $this->repositoryResolver->getRepository(AcademicUpdateRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->emailTemplateRepository = $this->repositoryResolver->getRepository(EmailTemplateRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
    }

    /** 
     * Load the file into memory
     *
     * @param string $filePath Path to the current file
     * @param int $organizationId
     * @param int $userId
     * @throws \Exception
     *
     * @return array
     */
    public function load($filePath, $organizationId, $userId)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found");
        }
        $this->organizationId = $organizationId;
        $this->userId = $userId;

        $this->fileObject = new CSVReader($filePath, true, true);
        $this->affectedExternalIds = [];

        foreach ($this->fileObject as $idx => $row) {
            $this->studentsNeedingRiskFlagsUpdated[] = $row[strtolower(self::STUDENTID)];
            $this->affectedExternalIds[] = $row[strtolower(self::UNIQUECOURSESECID)] . "-" . $row[strtolower(self::STUDENTID)];
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
     * @param int $uploadId
     * @param  array $serverArray
     * @return array|bool Returns array containing information about the upload, or false on failure
     */
    public function process($uploadId, $serverArray = [])
    {
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
        $batchSize = 10;
        $i = 1;
        foreach ($this->fileObject as $idx => $row) {

            if ($idx === 0) {
                continue;
            }

            if (in_array($row[strtolower(self::UNIQUECOURSESECID)] . "-" . $row[strtolower(self::STUDENTID)], $processed)) {
                continue;
            }

            if (in_array($row[strtolower(self::UNIQUECOURSESECID)] . "-" . $row[strtolower(self::STUDENTID)], $this->createable)) {
                $this->create($idx, $row);
            }

            if (($i % $batchSize) === 0) {
                $this->queueForWrite($serverArray);
            }

            $processed[] = $row[strtolower(self::UNIQUECOURSESECID)] . "-" . $row[strtolower(self::STUDENTID)];
            $i++;
        }

        $this->queueForWrite($serverArray);

        $this->cache->save("organization.{$this->organizationId}.upload.{$this->uploadId}.jobs", $this->jobs);

        if (!count($this->jobs)) {
            $this->uploadFileLogService->updateValidRowCount($uploadId, 0);
        }

        return [
            'jobs' => $this->jobs
        ];
    }

    /**
     * Function to send notification in email
     * @param int $organizationId
     * @param string $entity
     * @param int $errorCount
     * @param string $downloadFailedLogFile
     */
    public function sendFTPNotification($organizationId, $entity, $errorCount, $downloadFailedLogFile)
    {
        $coordinators = $this->organizationRoleRepository->getCoordinators($organizationId);

        $subjectEntity = ucfirst($entity);

        foreach ($coordinators as $coordinator) {
            $this->emailService->sendUploadCompletionEmail($coordinator->getPerson(), $organizationId, "{$subjectEntity}_Upload_Notification", $errorCount, $downloadFailedLogFile);
        }

        $transport = $this->mailer->getTransport();
        if (!$transport instanceof \Swift_Transport_SpoolTransport) {
            return;
        }

        $spool = $transport->getSpool();
        if (!$spool instanceof \Swift_MemorySpool) {
            return;
        }

        $spool->flushQueue($this->swiftMailer);
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
     * Generates the dump academic update csv
     *
     * @param $organizationId
     */
    public function generateDumpCSV($organizationId)
    {
        $academicUpdates = $this->academicUpdateRepository->getAcademicUpdatesByOrg($organizationId);
        if ($organizationId) {
            $this->organizationId = $organizationId;
        }

        $file = new SplFileObject("data://academic_update_uploads/{$this->organizationId}-academic-updates-data.csv", 'w');

        // Get the headers.
        // Also set includeSentToStudentColumnInDownload and includeReferForAssistanceColumnInDownload
        $columnHeaders = $this->getHeaders($organizationId);

        $file->fputcsv($columnHeaders);

        if (isset($academicUpdates) && count($academicUpdates) > 0) {

            foreach ($academicUpdates as $data) {
                $academicUpdateRow = [
                    self::UNIQUECOURSESECID => isset($data['UniqueCourseSectionID']) ? $data['UniqueCourseSectionID'] : "",
                    self::STUDENTID => isset($data['StudentID']) ? $data['StudentID'] : "",
                    self::FAILURERISK => isset($data[self::FAILURERISK]) ? $data[self::FAILURERISK] : "",
                    self::INPROGRESSGRADE => isset($data[self::INPROGRESSGRADE]) ? $data[self::INPROGRESSGRADE] : "",
                    self::FINALGRADE => isset($data[self::FINALGRADE]) ? $data[self::FINALGRADE] : "",
                    self::ABSENCES => isset($data[self::ABSENCES]) ? $data[self::ABSENCES] : "",
                    self::COMMENTS => isset($data[self::COMMENTS]) ? $data[self::COMMENTS] : "",
                ];

                if ($this->includeSentToStudentColumnInDownload) {
                    $academicUpdateRow['SentToStudent'] = isset($data['SentToStudent']) ? $data['SentToStudent'] : 0;
                }

                if ($this->includeReferForAssistanceColumnInDownload) {
                    $academicUpdateRow['referForAssistance'] = isset($data['referForAssistance']) ? $data['referForAssistance'] : 0;
                }

                $file->fputcsv($academicUpdateRow);
            }
        }
    }

    private function create($idx, $person)
    {
        $this->creates[$idx] = $person;
    }

    public function getFileObject()
    {
        return $this->fileObject;
    }

    /**
     * This function will return the headers for either the template or
     * the Existing Download file. If the file is an template, always
     * ignore Refer for assistance header
     *
     * TODO: Fix incorrect behavior: sets the member variables includeSentToStudentColumnInDownload and includeReferForAssistanceColumnInDownload - ESPRJ-11724
     *
     * @param $organizationId
     * @param bool|false $isTemplate
     * @return array
     */
    public function getHeaders($organizationId, $isTemplate = false)
    {
        $organization = $this->organizationRepository->find($organizationId);

        // Check whether the organization has send_to_student and refer_for_assistance enabled.
        $organizationHasSendToStudentEnabled = $organization->getSendToStudent();
        $organizationHasReferForAcademicAssistanceEnabled = $organization->getReferForAcademicAssistance();

        // If send_to_student is disabled organizationally, check the academic update data to find out if at least one record has a "true" value set. If so, include it in the dumpfile
        $this->includeSentToStudentColumnInDownload = true;
        if (!$organizationHasSendToStudentEnabled) {
            $academicUpdateWithSendToStudent = $this->academicUpdateRepository->findOneBy(['org' => $organization, 'sendToStudent' => true]);
            if (!$academicUpdateWithSendToStudent) {
                $this->includeSentToStudentColumnInDownload = false;
            }
        }

        // If refer_for_assistance is disabled organizationally, check the academic update data to find out if at least one record has a "true" value set. If so, include it in the dumpfile
        $this->includeReferForAssistanceColumnInDownload = true;
        if (!$organizationHasReferForAcademicAssistanceEnabled) {
            $academicUpdateWithReferForAssistance = $this->academicUpdateRepository->findOneBy(['org' => $organization, 'referForAssistance' => true]);
            if (!$academicUpdateWithReferForAssistance) {
                $this->includeReferForAssistanceColumnInDownload = false;
            }
        }

        $headerArray = [
            self::UNIQUECOURSESECID,
            self::STUDENTID,
            self::FAILURERISK,
            self::INPROGRESSGRADE,
            self::FINALGRADE,
            self::ABSENCES,
            self::COMMENTS
        ];

        if (($isTemplate && $organizationHasSendToStudentEnabled) || (!$isTemplate && $this->includeSentToStudentColumnInDownload)) {
            $headerArray[] = 'SentToStudent';
        }

        if (!$isTemplate && $this->includeReferForAssistanceColumnInDownload) {
            $headerArray[] = 'ReferForAssistance';
        }
        return $headerArray;

    }

    /**
     * Run Academic update upload job
     *
     * @param  array $serverArray
     */
    private function queueForWrite($serverArray = [])
    {
        if (count($this->creates)) {

            $this->creates = array_change_key_case($this->creates, CASE_LOWER);

            $createObject = 'Synapse\UploadBundle\Job\CreateAcademicUpdate';
            $jobNumber = uniqid();
            $job = new $createObject();
            $job->queue = $this->queue;
            $job->args = array(
                'creates' => $this->creates,
                'jobNumber' => $jobNumber,
                'uploadId' => $this->uploadId,
                'userId' => $this->userId,
                'orgId' => $this->organizationId,
                'serverArray' => $serverArray
            );

            $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
        }

        $this->creates = [];
    }

    /**
     * Creates a new job object for updating risk flags and then adds that job to the queue.
     *
     * @param int $orgId
     * @return null|\Resque_Job_Status
     */
    public function createRiskFlagsUpdateJob($orgId)
    {
        $job = new UpdateRiskFlagsJob();
        $job->args = array(
            'orgId' => $orgId,
            'studentsToUpdate' => implode(',', array_unique($this->studentsNeedingRiskFlagsUpdated))
        );

        return $this->resque->enqueue($job, true);
    }
}