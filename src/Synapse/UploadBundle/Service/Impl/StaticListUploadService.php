<?php
namespace Synapse\UploadBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use Doctrine\ORM\Mapping\Cache;
use JMS\DiExtraBundle\Annotation as DI;
use SplFileObject;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Stopwatch\Stopwatch;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\EmailTemplateRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\CSVReader;
use Synapse\StaticListBundle\Repository\OrgStaticListStudentsRepository;
use Synapse\StaticListBundle\Service\Impl\StaticListService;
use Synapse\UploadBundle\Util\Constants\UploadConstant;


/**
 * Handle Static List uploads
 *
 * @DI\Service("staticlist_upload_service")
 */
class StaticListUploadService extends AbstractService
{

    const SERVICE_KEY = 'staticlist_upload_service';

    const STUDENTID = 'StudentID';

    const STUDENTFIRSTNAME = 'FirstName';

    const STUDENTLASTNAME = 'LastName';

    const STUDENTPRIMARYEMAIL = 'PrimaryEmail';

    const UPLOAD_ERROR = 'Upload Errors';

    private $createable;

    private $creates;

    private $jobs;

    private $totalRows;

    private $uploadId;

    private $orgId;

    private $userId;

    private $staticListId;

    // Scaffolding

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Resque
     */
    private $resque;

    /**
     * @var \Swift_Mailer
     */
    private $transport;

    /**
     * @var \Swift_MailTransport
     */
    private $transportReal;

    /**
     * Object containing the loaded file
     *
     * @var \SPLFileObject
     */
    private $fileObject;

    // Services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

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

    /**
     * @var PersonService
     */
    private $personService;

    // Repositories

     /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

     /**
     * @var EmailTemplateRepository
     */
    private $emailTemplateRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var OrgStaticListStudentsRepository
     */
    private $staticListStudentsRepository;

    /**
     * @DI\InjectParams({
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

        // Scaffolding
        $this->container = $container;
        $this->cache = $this->container->get(SynapseConstant::REDIS_CLASS_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->transport = $this->container->get(SynapseConstant::MAILER_KEY);
        $this->transportReal = $this->container->get(SynapseConstant::SWIFT_MAILER_TRANSPORT_REAL_KEY);
        $this->creates = [];
        $this->updates = [];
        $this->jobs = [];
        $this->queue = 'default';

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->alertService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->uploadFileLogService = $this->container->get(UploadFileLogService::SERVICE_KEY);

        // Repositories
        $this->emailTemplateRepository = $this->repositoryResolver->getRepository(EmailTemplateRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->staticListStudentsRepository = $this->repositoryResolver->getRepository(OrgStaticListStudentsRepository::REPOSITORY_KEY);
    }

     /**
     * Load the file into memory
     *
     * @param string $filePath Path to the current file
     * @param int $orgId
     * @param int $userId
     * @param int $staticListId
     * @return array
     * @throws \Exception
     */
    public function load($filePath, $orgId, $userId, $staticListId)
    {
        if (! file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        $this->orgId = $orgId;
        $this->userId = $userId;
        $this->staticListId = $staticListId;

        $this->fileObject = new CSVReader($filePath, true, true);
        $this->affectedExternalIds = [];

        $rowVal = 0;
        foreach ($this->fileObject as $idx => $row) {
            $this->affectedExternalIds[] = $row[strtolower(self::STUDENTID)];
        }

        $existingStaticlistUpdates = null;
        $existingStaticlistUpdates = $existingStaticlistUpdates ? $existingStaticlistUpdates : [];
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
     * @return array|bool Returns array containing information about the upload, or false on failure
     */
    public function process($uploadId)
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
        $batchSize = 30;
        $i = 1;
        foreach ($this->fileObject as $idx => $row) {

            if ($idx === 0) {
                continue;
            }

            $this->create($idx, $row);

            if (($i % $batchSize) === 0) {
                $this->queueForWrite();
            }

            $i ++;
        }

        $this->queueForWrite();

        $this->cache->save("organization.{$this->orgId}.upload.{$this->uploadId}.jobs", $this->jobs);

        if (! count($this->jobs)) {
            $this->uploadFileLogService->updateValidRowCount($uploadId, 0);
        }

        return [
            'jobs' => $this->jobs
        ];
    }

    /**
     * Send FTP Notification
     *
     * @param int $organizationId
     * @param int $errorCount
     * @param string $downloadFailedLogFile
     * @param int $loggedInPersonId
     * @return null
     */
    public function sendFTPNotification($organizationId, $errorCount, $downloadFailedLogFile, $loggedInPersonId)
    {
        $loggedInPerson = $this->personService->findPerson($loggedInPersonId);
        $errorFileFlag = false;
        if ($errorCount > 0) {
            $errorFileFlag = true;
        }
        $this->alertService->createNotification('StaticList_Upload_Notification', 'Static List Import', $loggedInPerson, null, null, null, $downloadFailedLogFile, null, null, null, $errorFileFlag);
        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
        $errorFilePath = $systemUrl . "#/errors/staticlist_uploads/{$this->orgId}-{$this->uploadId}-upload-errors.csv";
        $this->emailService->sendUploadCompletionEmail($loggedInPerson, $organizationId, "StaticList_Upload_Notification", $errorCount, $errorFilePath);

        if (!$this->transport instanceof \Swift_Transport_SpoolTransport) {
            return;
        }

        $spool = $this->transport->getSpool();
        if (!$spool instanceof \Swift_MemorySpool) {
            return;
        }

        $spool->flushQueue($this->transportReal);
    }

    /**
     * Generate CSV dump file
     *
     * @param int $organizationId
     * @param int $staticListId
     * @return string
     * @throws SynapseValidationException
     */
    public function generateDumpCSV($organizationId, $staticListId)
    {
        $organizationId = isset($this->orgId) ? $this->orgId : $organizationId;
        $staticListId = isset($this->staticListId) ? $this->staticListId : $staticListId;
        $currentAcademicYear = $this->academicYearService->findCurrentAcademicYearForOrganization($organizationId);
        if (isset($currentAcademicYear['org_academic_year_id'])) {
            $getStaticListStudents = $this->staticListStudentsRepository->getStaticListStudentsByOrg($organizationId, $staticListId, $currentAcademicYear['org_academic_year_id']);
        } else {
            // currently this is ran in a job and will kill the job
            throw new SynapseValidationException("Academic year is not active");
        }
        $rows = [];
        if (isset($getStaticListStudents) && count($getStaticListStudents) > 0) {

            foreach ($getStaticListStudents as $data) {

                $rows[] = [
                    'StaticList' => isset($data['name']) ? $data['name'] : "",
                    'StudentID' => isset($data['external_id']) ? $data['external_id'] : "",
                    'FirstName' => isset($data['firstname']) ? $data['firstname'] : "",
                    'LastName' => isset($data['lastname']) ? $data['lastname'] : "",
                    'PrimaryEmail' => isset($data['primary_email']) ? $data['primary_email'] : "",
                ];
            }
        }

        $filename = "data://staticlist_uploads/{$organizationId}-{$staticListId}-staticlists-data.csv";
        $file = new SplFileObject($filename, 'w');

        $file->fputcsv([
            'StaticList',
            'StudentID',
            'FirstName',
            'LastName',
            'PrimaryEmail'
        ]);

        foreach ($rows as $field) {
            $file->fputcsv($field);
        }
        return isset($fileName) ? $fileName : "{$organizationId}-{$staticListId}-staticlists-data.csv";
    }

    private function create($idx, $person)
    {
        $this->creates[$idx] = $person;
    }

    public function getFileObject()
    {
        return $this->fileObject;
    }

    private function queueForWrite()
    {
        if (count($this->creates)) {

            $this->creates = array_change_key_case($this->creates,CASE_LOWER);
            $createObject = 'Synapse\UploadBundle\Job\CreateStaticList';
            $jobNumber = uniqid();
            $job = new $createObject();
            $job->queue = $this->queue;
            $job->args = array(
                'creates' => $this->creates,
                'jobNumber' => $jobNumber,
                'uploadId' => $this->uploadId,
                'userId' => $this->userId,
                'orgId' => $this->orgId,
                'staticListId' => $this->staticListId
            );

            $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
        }

        $this->creates = [];
    }

    public function updateDataFile($orgId, $staticListId)
    {
        $createObject = 'Synapse\UploadBundle\Job\UpdateStaticListDataFile';
        $job = new $createObject();
        $job->args = array(
            'orgId' => $orgId,
            'staticListId' => $staticListId
        );

        return $this->resque->enqueue($job, true);
    }
}