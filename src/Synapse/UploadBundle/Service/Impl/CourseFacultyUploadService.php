<?php
namespace Synapse\UploadBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use Doctrine\Common\Cache\RedisCache;
use JMS\DiExtraBundle\Annotation as DI;
use SplFileObject;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgCourseFacultyRepository;
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
use Synapse\CoreBundle\Util\CSVReader;
use Synapse\UploadBundle\Job\AddCourseFaculty;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Handle course uploads
 *
 * @DI\Service("course_faculty_upload_service")
 */
class CourseFacultyUploadService extends AbstractService
{

    const SERVICE_KEY = 'course_faculty_upload_service';

    const UNIQUESECTIONID = 'UniqueCourseSectionId';

    const FACULTYID = 'FacultyID';

    const PERMISSIONSET = 'PermissionSet';

    const UPLOAD_ERROR = 'Upload Errors';

    // Class Variables

    /**
     * Object containing the loaded file
     *
     * @var \SPLFileObject
     */
    private $fileObject;

    private $affectedExternalIds;

    private $updateable;

    private $createable;

    private $updates;

    private $creates;

    private $jobs;

    private $totalRows;

    private $uploadId;

    private $orgId;

    private $queue;

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
    private $swiftMailer;

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
    private  $ebiConfigRepository;

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
     * @var OrgCourseFacultyRepository
     */
    private $orgCourseFacultyRepository;

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

        // Scaffolding
        $this->cache = $this->container->get('synapse_redis_cache');
        $this->mailer = $this->container->get('mailer');
        $this->resque = $this->container->get('bcc_resque.resque');
        $this->swiftMailer = $this->container->get('swiftmailer.transport.real');
        $this->updates = [];
        $this->creates = [];
        $this->jobs = [];
        $this->queue = 'default';

        // Services
        $this->alertService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
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
        $this->orgCourseFacultyRepository = $this->repositoryResolver->getRepository(OrgCourseFacultyRepository::REPOSITORY_KEY);
    }

    public function validateColumns($filePath)
    {
        if (! file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        $this->fileObject = new CSVReader($filePath, true, true);
        $this->affectedExternalIds = [];

        //The values in this arrays contains the names of the indexes (keys) that should exist in the data array
        $required = array(strtolower(self::UNIQUESECTIONID), strtolower(self::FACULTYID), strtolower(self::PERMISSIONSET));

        foreach ($this->fileObject as $idx => $row) {

            if(count(array_intersect_key(array_flip($required), $row)) !== count($required)) {
                $findAvailArrays = array_intersect_key(array_flip($required), $row);
                //print_r( array_diff($findAvailArrays, $required) );
            }

            return;
        }
    }

    /**
     * Finds out the total rows  the creatable records and the to be updated records
     * @param string $filePath
     * @param int $orgId
     * @return array
     * @throws \Exception
     */
    public function load($filePath, $orgId)
    {
        if (! file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        $this->orgId = $orgId;

        $this->fileObject = new CSVReader($filePath, true, true);
        $this->affectedExternalIds = [];

        foreach ($this->fileObject as $idx => $row) {
            $this->affectedExternalIds[] = $row[strtolower(self::FACULTYID)];
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
            if (in_array($row[strtolower(self::FACULTYID)], $this->createable)) {
                $person = $row[strtolower(self::FACULTYID)];
                $this->create($idx, $row);
            }
            if (($i % $batchSize) === 0) {
                $this->queueForWrite();
            }

            $processed[] = $row[strtolower(self::FACULTYID)];
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

    public function generateDumpCSV($orgId)
    {
        $this->orgId = isset($this->orgId) ? $this->orgId : $orgId;

        $orgCourseFacultys = $this->orgCourseFacultyRepository->getCourseFacultyForOrganization($orgId);
        $rows = [];

        foreach ($orgCourseFacultys as $orgCourseFaculty) {
            $rows[] = [
                self::UNIQUESECTIONID => $orgCourseFaculty['UniqueCourseSectionID'],
                self::FACULTYID => $orgCourseFaculty[0]->getPerson()->getExternalId(),
                self::PERMISSIONSET => $orgCourseFaculty['PermissionSet']
            ];
        }

        $file = new SplFileObject("data://course_faculty_uploads/{$this->orgId}-latest-course-faculty-data.csv", 'w');

        $file->fputcsv([
            self::UNIQUESECTIONID,
            self::FACULTYID,
            self::PERMISSIONSET
        ]);

        foreach ($rows as $fields) {
            $file->fputcsv($fields);
        }
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
            $job = new AddCourseFaculty();
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
        $createObject = 'Synapse\UploadBundle\Job\UpdateCourseFacultyDataFile';
        $job = new $createObject();
        $job->args = array(
            'orgId' => $orgId
        );

        return $this->resque->enqueue($job, true);
    }

    /**
     * Sends the notifications/emails to the coordinators
     *
     * @param int $organizationId - the organization id
     * @param string $entity - The name of the Upload that is using this function
     * @param int $errorCount - the number of errors the upload has
     * @param string $downloadFailedLogFile - the error file name
     */
    public function sendFTPNotification($organizationId, $entity, $errorCount, $downloadFailedLogFile)
    {
        $coordinators = $this->organizationRoleRepository->getCoordinators($organizationId);

        $subjectEntity = ucfirst($entity);
        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);

        foreach ($coordinators as $coordinator) {
            $this->emailService->sendUploadCompletionEmail($coordinator->getPerson(), $organizationId, "{$subjectEntity}_Upload_Notification", $errorCount, $systemUrl . "#/errors/course_faculty_uploads/{$this->orgId}-{$this->uploadId}-upload-errors.csv");

            /**
             * The ugly string replace is because the Alert Notification is
             * expecting "Course-faculty_Import" instead of "Course_faculty_Import"
             */
            $this->sendAlert($coordinator->getPerson(), $organizationId, str_replace("_", "-", $subjectEntity) . "_Import", "Course-faculty Import", $errorCount, $downloadFailedLogFile);

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

    /**
     * creates and sends notification alert after the upload has completed
     *
     * @param $user
     * @param $orgId
     * @param $alertKey
     * @param $reason
     * @param $errorCount
     * @param null $uploadFile
     */
    private function sendAlert($user, $orgId, $alertKey, $reason, $errorCount, $uploadFile = null)
    {
        $errorFileFlag = false;

        if ($errorCount > 0) {
            $errorFileFlag = true;
        }
        $this->alertService->createNotification($alertKey, $reason, $user, null, null, null, $uploadFile, null, null, null, $errorFileFlag);
    }

    
}