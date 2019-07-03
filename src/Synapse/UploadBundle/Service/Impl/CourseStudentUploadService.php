<?php
namespace Synapse\UploadBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use SplFileObject;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgCourseStudentRepository;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\CSVReader;
use Synapse\UploadBundle\Job\AddCourseStudent;
use Synapse\UploadBundle\Util\Constants\UploadConstant;


/**
 * Handle course uploads
 *
 * @DI\Service("course_student_upload_service")
 */
class CourseStudentUploadService extends AbstractService
{

    const SERVICE_KEY = 'course_student_upload_service';

    const STUDID = 'StudentId';

    const UNIQUESECTIONID = 'UniqueCourseSectionId';

    const STUDID_LOWERCASE = 'studentid';

    const UNIQUESECTIONID_LOWERCASE = 'uniquecoursesectionid';

    const UPLOAD_ERROR = 'Upload Errors';

    // Class Variables
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
     * Object containing the loaded file
     *
     * @var \SPLFileObject
     */
    private $fileObject;

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
     * @var array
     */
    private $updateable;

    /**
     * @var array
     */
    private $updates;

    /**
     * @var int
     */
    private $uploadId;

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
    private $mailer;

    /**
     * @var \Swift_Transport
     */
    private $swiftMailer;

    // Services

    /**
     * @var AlertNotificationsService
     */
    private $alertService;

    /**
     * @var CSVUtilityService
     */
    private $CSVUtilityService;

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
     * @var PersonService
     */
    private $personService;

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
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var OrganizationlangRepository
     */
    private $organizationLangRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var OrgCourseStudentRepository
     */
    private $orgCourseStudentRepository;

    /**
     * CourseStudentUploadService constructor.
     *
     * @DI\InjectParams({
     *      "repositoryResolver" = @DI\Inject("repository_resolver"),
     *      "logger" = @DI\Inject("logger"),
     *      "container" = @DI\Inject("service_container"),
     *      })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        // Class variable initialization
        $this->creates = [];
        $this->jobs = [];
        $this->queue = 'default';
        $this->updates = [];

        // Scaffolding
        $this->cache = $this->container->get('synapse_redis_cache');
        $this->resque = $this->container->get('bcc_resque.resque');
        $this->mailer = $this->container->get('mailer');
        $this->swiftMailer = $this->container->get('swiftmailer.transport.real');

        // Services
        $this->alertService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->CSVUtilityService = $this->container->get(CSVUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->uploadFileLogService = $this->container->get(UploadFileLogService::SERVICE_KEY);

        // Repositories
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->organizationLangRepository = $this->repositoryResolver->getRepository(OrganizationlangRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgCourseStudentRepository = $this->repositoryResolver->getRepository(OrgCourseStudentRepository::REPOSITORY_KEY);
    }

    /**
     * Load the file into memory
     * @param string $filePath
     * @param int $orgId
     * @return array $fileData
     * @throws \Exception
     */
    public function load($filePath, $orgId)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        $this->orgId = $orgId;

        $this->fileObject = new CSVReader($filePath, true, true);
        $this->affectedExternalIds = [];

        foreach ($this->fileObject as $idx => $row) {
            $this->affectedExternalIds[] = $row[strtolower(self::STUDID)];
        }

        $this->createable = $this->affectedExternalIds;
        $this->updateable = array_diff($this->affectedExternalIds, $this->createable);
        $this->totalRows = count($this->affectedExternalIds);

        $fileData = [
            'totalRows' => $this->totalRows,
            'new' => count($this->createable),
            'existing' => count($this->updateable)
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
        foreach ($this->fileObject as $idx => $row) {
            if ($idx === 0) {
                continue;
            }
            if (in_array($row[strtolower(self::STUDID)], $this->createable)) {
                $person = $row[strtolower(self::STUDID)];
                $this->create($idx, $row);
            }
            if (($i % $batchSize) === 0) {
                $this->queueForWrite();
            }

            $processed[] = $row[strtolower(self::STUDID)];
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
     * Function to send notification in email and bell notification
     *
     * @param int $organizationId
     * @param string $entity
     * @param int $errorCount
     * @param string $downloadFailedLogFile - Unused parameter. Don't expect this to help with anything. It won't.
     */
    public function sendFTPNotification($organizationId, $entity, $errorCount, $downloadFailedLogFile)
    {
        $coordinators = $this->organizationRoleRepository->getCoordinators($organizationId);

        $subjectEntity = ucfirst($entity);

        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);

        foreach ($coordinators as $coordinator) {
            $this->emailService->sendUploadCompletionEmail($coordinator->getPerson(), $organizationId, "{$subjectEntity}_Upload_Notification", $errorCount, $systemUrl . "#/errors/course_student_uploads/{$this->orgId}-{$this->uploadId}-upload-errors.csv");

            /**
             * The ugly string replace is because the Alert Notification is
             * expecting "Course-student_Import" instead of "Course_student_Import"
             */
            $this->sendAlert($coordinator->getPerson(), $organizationId, str_replace("_", "-", $subjectEntity) . "_Import", "Course-student Import", $errorCount, "errors/{$this->orgId}-{$this->uploadId}-upload-errors.csv");
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
     * Generate the course-student dump file for an organization
     * TODO:: This function should be abstracted into a black listed unit test file, it does not need to be unit tested
     *
     * @param int $organizationId
     * @return string
     */
    public function generateDumpCSV($organizationId)
    {

        $this->orgId = isset($this->orgId) ? $this->orgId : $organizationId;

        $orgCourseStudents = $this->orgCourseStudentRepository->getCourseStudentDumpDataForOrganization($organizationId);

        $filePath = SynapseConstant::S3_ROOT . SynapseConstant::S3_COURSE_STUDENT_UPLOADS_DIRECTORY . '/';

        $fileName = "{$this->orgId}-latest-course-student-data.csv";

        $columnHeaders = [
            'StudentId' => 'StudentId',
            'UniqueCourseSectionId' => 'UniqueCourseSectionId',
        ];

        $newFileName = $this->CSVUtilityService->generateCSV($filePath, $fileName, $orgCourseStudents, $columnHeaders);

        return $newFileName;

    }

    private function create($idx, $person)
    {
        $this->creates[$idx] = $person;
    }

    private function queueForWrite()
    {
        if (count($this->creates)) {

            $this->creates = array_change_key_case($this->creates,CASE_LOWER);

            $jobNumber = uniqid();
            $job = new AddCourseStudent();
            $job->queue = $this->queue;
            $job->args = array(
                'creates' => $this->creates,
                'jobNumber' => $jobNumber,
                'uploadId' => $this->uploadId,
                'orgId' => $this->orgId
            );

            $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
        }

        $this->creates = [];
    }

    public function getFileObject()
    {
        return $this->fileObject;
    }

    public function updateDataFile($orgId)
    {
        $createObject = 'Synapse\UploadBundle\Job\UpdateCourseStudentDataFile';
        $job = new $createObject();
        $job->args = array(
            'orgId' => $orgId
        );

        return $this->resque->enqueue($job, true);
    }
}