<?php

namespace Synapse\UploadBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Snc\RedisBundle\Doctrine\Cache\RedisCache;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgGroupRepository;
use Synapse\CoreBundle\Repository\OrgGroupStudentsRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\EntityService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\CSVReader;
use Synapse\UploadBundle\Job\AddGroupStudent;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;


/**
* Handle group student uploads
*
* @DI\Service("group_student_upload_service")
*/
class GroupStudentUploadService extends AbstractService
{

    const SERVICE_KEY = 'group_student_upload_service';

    //Private class variables

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
     * @var int
     */
    private $groupId;

    /**
     * @var array
     */
    private $jobs;

    /**
     * Object containing the loaded file
     * @var CSVReader
     */
    private $CSVReader;

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

    //Scaffolding

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
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationService;

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
     * @var EntityService
     */
    private $entityService;

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


    //Repositories

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var OrgGroupRepository
     */
    private $orgGroupRepository;

    /**
     * @var OrgGroupStudentsRepository
     */
    private $orgGroupStudentsRepository;

    /**
     * GroupStudentUploadService constructor
     *
     * @DI\InjectParams({
     *      "repositoryResolver" = @DI\Inject("repository_resolver"),
     *      "logger" = @DI\Inject("logger"),
     *      "container" = @DI\Inject("service_container"),
     * })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        //Scaffolding
        $this->cache = $this->container->get(SynapseConstant::REDIS_CLASS_KEY);
        $this->mailer = $this->container->get(SynapseConstant::MAILER_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->swiftMailer = $this->container->get(SynapseConstant::SWIFT_MAILER_TRANSPORT_REAL_KEY);

        //Class Variables
        $this->creates = [];
        $this->jobs = [];
        $this->queue = 'default';
        $this->updates = [];

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->alertNotificationService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->entityService = $this->container->get(EntityService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->uploadFileLogService = $this->container->get(UploadFileLogService::SERVICE_KEY);
        $this->CSVUtilityService = $this->container->get(CSVUtilityService::SERVICE_KEY);

        // Repositories
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgGroupRepository = $this->repositoryResolver->getRepository(OrgGroupRepository::REPOSITORY_KEY);
        $this->orgGroupStudentsRepository = $this->repositoryResolver->getRepository(OrgGroupStudentsRepository::REPOSITORY_KEY);
    }

    /**
     * Load the file into memory
     *
     * @param string $filePath Path to the current file
     * @param int $organizationId
     * @param int $groupId
     * @return SPLFileObject Returns a raw SPLFileObject
     * @throws \Exception
     */
    public function load($filePath, $organizationId, $groupId)
    {

        if (!file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        $this->orgId = $organizationId;
        $this->groupId = $groupId;

        $this->CSVReader = new CSVReader($filePath, true, true);
        $this->affectedExternalIds = [];

        foreach ($this->CSVReader as $key => $row) {
            $this->affectedExternalIds[] = $row['externalid'];
        }

        $orgGroupStudents = $this->orgGroupStudentsRepository->findBy(['orgGroup' => $groupId]);
        $existingStudents = [];
        foreach ($orgGroupStudents as $orgGroupStudent) {
            $existingStudents[$orgGroupStudent->getPerson()->getExternalId()] = true;
        }
        $existingStudents = $existingStudents ? $existingStudents : [];
        $this->createable = array_diff($this->affectedExternalIds, array_keys($existingStudents));
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
     * @param int $uploadId
     * @return array|bool Returns array containg information about the upload, or false on failure
     */
    public function process($uploadId)
    {
        $this->uploadId = $uploadId;
        try {
            if ($this->totalRows < SynapseConstant::EXPRESS_QUEUE_COUNT) {
                $this->queue = SynapseConstant::EXPRESS_QUEUE;
            } else {
                $queues = json_decode($this->ebiConfigService->get('Upload_Queues'));
                $randomQueue = mt_rand(0, count($queues) - 1);
                $this->queue = $queues[$randomQueue];
            }
        } catch (\Exception $e) {
            $this->queue = SynapseConstant::DEFAULT_QUEUE;
        }

        $processed = [];
        $batchSize = SynapseConstant::DEFAULT_UPLOAD_BATCH_SIZE;
        $rowCount = 1;
        foreach ($this->CSVReader as $key => $row) {
            if ($key === 0) {
                continue;
            }
            if (in_array($row['externalid'], $processed)) {
                continue;
            }
            if (in_array($row['externalid'], $this->createable)) {
                $this->create($key, $row);
            }
            if (($rowCount % $batchSize) === 0) {
                $this->queueForWrite();
            }

            $processed[] = $row['externalid'];
            $rowCount++;
        }

        $this->queueForWrite();

        $this->cache->save("organization.{$this->orgId}.upload.{$this->uploadId}.jobs", $this->jobs);

        if (!count($this->jobs)) {
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

        $spool->flushQueue($this->swiftMailer);
    }

    private function sendAlert($user, $orgId, $alertKey, $reason, $errorCount, $uploadFile = null)
    {
        $errorFileFlag = false;

        if ($errorCount > 0) {
            $errorFileFlag = true;
        }
            $this->alertNotificationService->createNotification($alertKey, $reason, $user, null, null, null, $uploadFile, null, null, null, $errorFileFlag);
    }

    /**
     * Generates data error CSV file for specified group
     *
     * @param array $errors
     * @return string
     */
    public function generateErrorCSV($errors)
    {
        $list = [];

        foreach ($this->CSVReader as $idx => $row) {
            if (isset($errors[$idx])) {
                $rowErrors = '';
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
                $row['upload errors'] = $rowErrors;
                $list[] = $row;
            }
        }

        $errorCSVFile = new \SplFileObject(SynapseConstant::S3_ROOT . SynapseConstant::GROUP_STUDENT_UPLOAD_ERROR_PATH . "{$this->orgId}-{$this->uploadId}" . SynapseConstant::GROUP_STUDENT_UPLOAD_ERROR_FILE, 'w');


        $csvHeaders = $this->CSVReader->getColumns();
        $csvHeaders['upload errors'] = 'Upload Errors';
        $errorCSVFile->fputcsv($csvHeaders);


        foreach ($list as $fields) {
            $errorCSVFile->fputcsv($fields);
        }

        $errorFilename = "errors/{$this->orgId}-{$this->uploadId}" . SynapseConstant::GROUP_STUDENT_UPLOAD_ERROR_FILE;
        return $errorFilename;
    }

    /**
     * Generates data dump CSV file for specified group
     *
     * @param int $groupId
     * @param int $organizationId
     * @return bool|string
     */
    public function generateGroupStudentDownloadCSV($groupId, $organizationId)
    {
        $orgGroupStudents = $this->orgGroupStudentsRepository->listExternalIdsForStudentsInGroup($organizationId, $groupId);
        $CSVFilename = $this->CSVUtilityService->generateCSV(SynapseConstant::S3_ROOT . SynapseConstant::GROUP_STUDENT_UPLOAD_PATH, "{$organizationId}-{$groupId}" . SynapseConstant::GROUP_STUDENT_UPLOAD_DUMP_FILE, $orgGroupStudents, ['external_id' => 'externalId']);
        return $CSVFilename;
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
            $job = new AddGroupStudent();
            $job->queue = $this->queue;
            $job->args = array(
                'creates' => $this->creates,
                'jobNumber' => $jobNumber,
                'uploadId' => $this->uploadId,
                'orgId' => $this->orgId,
                'groupId' => $this->groupId
            );

            $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
        }

        $this->creates = [];
    }

    /**
     * Creates a CSV file containing a list of external IDs for all students within the passed
     * in group and organization and does not include students of subgroups.
     *
     * @param int $orgId
     * @param int $groupId
     * @return string - The file name of the CSV generated
     * @throws AccessDeniedException
     */
    public function createCsvOfStudentsInGroup($orgId, $groupId)
    {
        $isValidGroup = $this->orgGroupRepository->findBy([
            'id' => $groupId,
            'organization' => $orgId
        ]);

        if (!empty($isValidGroup)) {
            $this->generateGroupStudentDownloadCSV($groupId, $orgId);
            return "{$orgId}-{$groupId}-latest-student-dump.csv";
        }else{
            throw new AccessDeniedException("You don't have permission to download this file.");
        }
    }
}
