<?php
namespace Synapse\UploadBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use Doctrine\Common\Cache\RedisCache;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\AppointmentsConstant;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\UploadBundle\Entity\UploadFileLog;
use Synapse\UploadBundle\EntityDto\UploadFileHistoryDto;
use Synapse\UploadBundle\Job\UploadHistoryPageJob;
use Synapse\UploadBundle\Repository\UploadFileLogRepository;
use Synapse\UploadBundle\Service\UploadFileLogServiceInterface;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * @DI\Service("upload_file_log_service")
 */
class UploadFileLogService extends AbstractService implements UploadFileLogServiceInterface
{

    const SERVICE_KEY = 'upload_file_log_service';

    // Class Constants

    const PAGE_NO = 1;

    const OFFSET = 25;

    const ORG_ROLE_REPO = "SynapseCoreBundle:OrganizationRole";

    //Class variable

    private static $__uploadEndStates = array('S', 'F', 'E');

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
     * @var Resque
     */
    private $resque;

    // Services

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationsService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

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


    // Repositories

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var MetadataListValuesRepository
     */
    private $metadataListValues;

    /**
     * @var OrganizationRoleRepository
     */
    private $orgRoleRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     *
     * @var UploadFileLogRepository
     */
    private $uploadFileLogRepository;

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

        // Services
        $this->alertNotificationsService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);

        // Repositories
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->metadataListValues = $this->repositoryResolver->getRepository(MetadataListValuesRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->uploadFileLogRepository = $this->repositoryResolver->getRepository(UploadFileLogRepository::REPOSITORY_KEY);
    }

    public function findAllStudentUploadLogs($organizationId)
    {
        return $this->uploadFileLogRepository->findBy(array(
            UploadConstant::UPLOADTYPE => 'S',
            UploadConstant::ORGANIZATION_ID => $organizationId
        ));
    }

    private function isUploadInEndState ($uploadStatus) {

        return in_array( $uploadStatus, self::$__uploadEndStates);
    }

    private function findOneUploadLogFor ($uploadType, $uploadId, $filter=array()) {

        $criteria = array( UploadConstant::UPLOADTYPE => $uploadType,
                            'id' => $uploadId
                        );

        $criteria = array_merge( $criteria, $filter);

        $upload = $this->uploadFileLogRepository->findOneBy( $criteria);

        if ( isset($upload) && is_null( $upload->getViewed()) && $this->isUploadInEndState( $upload->getStatus()) )
        {
            $upload->setViewed( true);
            $this->uploadFileLogRepository->update($upload);
        }

        return $this->uploadFileLogRepository->findOneBy( $criteria);
    }

    public function findUploadLog ($id)
    {
        $upload = $this->uploadFileLogRepository->findOneBy(array(

            'id' => $id
        ));

        if ( is_null( $upload->getViewed()) && $this->isUploadInEndState( $upload->getStatus()) )
        {
            $upload->setViewed( true);
            $this->uploadFileLogRepository->update($upload);
        }

        return $upload;
    }

    public function findOneStudentUploadLog($id)
    {
        return $this->findOneUploadLogFor( UploadConstant::UPLOAD_TYPE_STUDENTS, $id);
    }


    public function createGroupStudentUploadLog($organizationId, $key, $columns, $rowCount, $jobNumber, $groupId, $personId = null)
    {
        $uploadFileLog = new UploadFileLog();
        $uploadFileLog->setOrganizationId($organizationId);
        $uploadFileLog->setPersonId($personId);
        $uploadFileLog->setUploadType('S2G');
        $uploadFileLog->setUploadedColumns(implode(', ', $columns));
        $uploadFileLog->setUploadedRowCount($rowCount);
        $uploadFileLog->setUploadDate(new \DateTime());
        $uploadFileLog->setUploadedFilePath($key);
        $uploadFileLog->setStatus('Q');
        $uploadFileLog->setJobNumber($jobNumber);
        $uploadFileLog->setGroupId($groupId);
        $this->uploadFileLogRepository->persist($uploadFileLog);

        return $uploadFileLog;
    }

    public function findAllGroupStudentUploadLogs($groupId)
    {
        return $this->uploadFileLogRepository->findBy(array(
            UploadConstant::UPLOADTYPE => 'S2G',
            'groupId' => $groupId
        ));
    }

    public function findOneGroupStudentUploadLog($groupId, $id)
    {
       return $this->findOneUploadLogFor( UploadConstant::UPLOAD_TYPE_STUDENT_GROUP, $id, array( 'groupId' => $groupId ));
    }


    public function findAllCourseUploadLogs($organizationId)
    {
        return $this->uploadFileLogRepository->findBy(array(
            UploadConstant::UPLOADTYPE => 'C',
            UploadConstant::ORGANIZATION_ID => $organizationId
        ));
    }

    public function findOneCourseUploadLog($id)
    {
        return $this->findOneUploadLogFor( UploadConstant::UPLOAD_TYPE_COURSES, $id);
    }

    public function findAllCourseStudentUploadLogs()
    {
        return $this->uploadFileLogRepository->findBy(array(
            UploadConstant::UPLOADTYPE => 'T'
        ));
    }

    public function findOneCourseStudentUploadLog($id)
    {
        return $this->findOneUploadLogFor( UploadConstant::UPLOAD_TYPE_COURSE_STUDENTS, $id);
    }

    public function createTalkingPointsUploadLog($organizationId, $key, $columns, $rowCount, $jobNumber, $personId = null, $uploadType = 'TP')
    {
        $uploadFileLog = $this->createUploadService($organizationId, $key, $columns, $rowCount, $jobNumber, $personId, $uploadType);
        return $uploadFileLog;
    }

    public function createAcademicUpdateUploadLog($organizationId, $key, $columns, $rowCount, $jobNumber, $personId = null, $uploadType = 'A')
    {
        $uploadFileLog = $this->createUploadService($organizationId, $key, $columns, $rowCount, $jobNumber, $personId, $uploadType);
        return $uploadFileLog;
    }

    public function createRiskVariableUploadLog($organizationId, $key, $columns, $rowCount, $jobNumber, $personId = null, $uploadType = 'RV')
    {
        $uploadFileLog = $this->createUploadService($organizationId, $key, $columns, $rowCount, $jobNumber, $personId, $uploadType);
        return $uploadFileLog;
    }

	public function createElementsUploadLog($organizationId, $key, $columns, $rowCount, $jobNumber, $personId = null, $uploadType = 'SRE')
    {
        $uploadFileLog = $this->createUploadService($organizationId, $key, $columns, $rowCount, $jobNumber, $personId, $uploadType);
        return $uploadFileLog;
    }


    public function createHelpUploadLog($organizationId, $key, $columns, $rowCount, $jobNumber, $personId = null)
    {
        $uploadFileLog = new UploadFileLog();
        $uploadFileLog->setOrganizationId($organizationId);
        $uploadFileLog->setPersonId($personId);
        $uploadFileLog->setUploadType('H');
        $uploadFileLog->setUploadedColumns(implode(', ', $columns));
        $uploadFileLog->setUploadedRowCount($rowCount);
        $uploadFileLog->setUploadDate(new \DateTime());
        $uploadFileLog->setUploadedFilePath($key);
        $uploadFileLog->setErrorFilePath($key . '.log');
        $uploadFileLog->setStatus('S');
        $uploadFileLog->setJobNumber($jobNumber);
        $this->uploadFileLogRepository->persist($uploadFileLog);

        return $uploadFileLog;
    }

    public function createSurveyUploadLog($organizationId, $key, $columns, $rowCount, $jobNumber, $personId = null, $type)
    {
        if ($type == 'block') {
            $uploadType = 'SB';
        }

        if ($type = 'marker') {
            $uploadType = 'SM';
        }

        $uploadFileLog = $this->createUploadService($organizationId, $key, $columns, $rowCount, $jobNumber, $personId = null, $uploadType);
        return $uploadFileLog;
    }

    public function findAllCourseFacultyUploadLogs()
    {
        return $this->uploadFileLogRepository->findBy(array(
            UploadConstant::UPLOADTYPE => 'P'
        ));
    }

    public function findOneCourseFacultyUploadLog($id)
    {
        return $this->findOneUploadLogFor( UploadConstant::UPLOAD_TYPE_COURSE_FACULTIES, $id);
    }


    public function findAllFacultyUploadLogs($organizationId)
    {
        return $this->uploadFileLogRepository->findBy(array(
            UploadConstant::UPLOADTYPE => 'F',
            UploadConstant::ORGANIZATION_ID => $organizationId
        ));
    }

    public function findOneFacultyUploadLog($id)
    {
        return $this->findOneUploadLogFor( UploadConstant::UPLOAD_TYPE_FACULTIES, $id);
    }


    public function updateJobErrorPath($upload)
    {
        $upload->setErrorFilePath("errors/{$upload->getOrganizationId()}-{$upload->getId()}-upload-errors.csv");
        $this->uploadFileLogRepository->update($upload);
    }

    public function updateErrorCount($id, $count)
    {
        $upload = $this->uploadFileLogRepository->findOneById($id);
        $upload->setErrorCount($count);
        $this->uploadFileLogRepository->update($upload);
    }

    public function updateValidRowCount($id, $count)
    {
        $this->cache->getRedis()->incrBy('upload.' . $id . '.valid', $count);
    }

    public function saveValidRowCount($id, $count)
    {
        $upload = $this->uploadFileLogRepository->findOneById($id);
        $upload->setValidRowCount($count);
        $this->uploadFileLogRepository->update($upload);
    }

    /**
     * @param $id
     * @param $count
     */
    public function updateCreatedRowCount($id, $count)
    {
        $this->cache->getRedis()->incrBy('upload.' . $id . '.created', $count);
    }

    /**
     * @param $id
     * @param $count
     */
    public function saveCreatedRowCount($id, $count)
    {
        $upload = $this->uploadFileLogRepository->findOneById($id);
        $upload->setCreatedRowCount($count);
        $this->uploadFileLogRepository->update($upload);
    }

    /**
     * @param $id
     * @param $count
     */
    public function updateUpdatedRowCount($id, $count)
    {
        $this->cache->getRedis()->incrBy('upload.' . $id . '.updated', $count);
    }

    /**
     * @param $id
     * @param $count
     */
    public function saveUpdatedRowCount($id, $count)
    {
        $upload = $this->uploadFileLogRepository->findOneById($id);
        $upload->setUpdatedRowCount($count);
        $this->uploadFileLogRepository->update($upload);
    }

    /**
     * @param $id
     * @param $count
     */
    public function updateErrorRowCount($id, $count)
    {
        $this->cache->getRedis()->incrBy('upload.' . $id . '.error', $count);
    }


    /**
     * @param $id
     * @param $count
     */
    public function saveErrorRowCount($id, $count)
    {
        $upload = $this->uploadFileLogRepository->findOneById($id);
        $upload->setErrorRowCount($count);
        $this->uploadFileLogRepository->update($upload);
    }

    /**
     * Saves file hash
     * @param  int $id   upload id
     * @param  string $hash md5 hash of file
     * @return void
     */
    public function updateFileHash($upload, $hash)
    {
        $upload->setUploadedFileHash($hash);
        $this->uploadFileLogRepository->update($upload);
    }


    public function hasPendingView($orgId, $type)
    {
        $upload = $this->uploadFileLogRepository->findPendingView($orgId, $type);
        return is_null($upload) ? false : $upload;
    }

    public function ebiHasPendingView($type)
    {
        $upload = $this->uploadFileLogRepository->findPendingViewEbi($type);
        return is_null($upload) ? false : $upload;
    }

    public function groupHasPendingView($groupId, $orgId)
    {
        $upload = $this->uploadFileLogRepository->findGroupPendingView($groupId, $orgId);
        return is_null($upload) ? false : $upload;
    }

    public function updateJobStatus($jobNumber, $status, $message = null)
    {
        $upload = $this->uploadFileLogRepository->findOneBy(array(
            'jobNumber' => $jobNumber
        ));
        $upload->setStatus($status);
        if (! is_null($message)) {
            $upload->setStatusMessage($message);
        }
        $this->uploadFileLogRepository->update($upload);
    }

    public function updateJobStatusById($id, $status, $message = null)
    {
        $upload = $this->uploadFileLogRepository->findOneById($id);
        $upload->setStatus($status);
        if (! is_null($message)) {
            $upload->setStatusMessage($message);
        }
        $this->uploadFileLogRepository->update($upload);
    }

    public function getStatus($id)
    {
        $upload = $this->cache->fetch(UploadConstant::UPLOAD . "." . $id . '.status');
        return $upload;
    }

    public function findSurveyUploadLog($id)
    {
        return $this->uploadFileLogRepository->findOneBy(array(
            'id' => $id
        ));
    }

    public function getLastRowByType($type)
    {
        return $this->uploadFileLogRepository->getLastRowByType($type);
    }

    /**
     * Get the last successfully uploaded FTP file.
     *
     * @param int $organizationId
     * @param string $uploadType
     * @return mixed
     */
    public function getLastFTP($organizationId, $uploadType)
    {
        $upload = $this->uploadFileLogRepository->findBy(
            ['organizationId' => $organizationId, 'personId' => null, 'uploadType' => $uploadType, 'status' => 'S'],
            ['id' => 'DESC'],
            1
        );

        return $upload[0];
    }

    /**
     * Function to send notification in email
     * @param int $userId
     * @param int $organizationId
     * @param string $emailKey
     * @param array $upload
     */
    public function sendEmail($userId, $organizationId, $emailKey, $upload = [])
    {
        $uploadFile = "";
        $emailUploadFile = "";
        $downloadFailedLogFile = "";

        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
        $baseURL = $systemUrl . "#/";
        if (!empty($upload) && is_array($upload)) {
            $uploadId = $upload['id'];
        }

        if ($emailKey == 'Course_Student_Upload_Notification') {
            $event = 'Course-student_Import';
            $reason = "course student import";
            $uploadFile = "errors/{$organizationId}-{$uploadId}-upload-errors.csv";
            $emailUploadFile = "errors/course_student_uploads/{$organizationId}-{$uploadId}-upload-errors.csv";
        } elseif ($emailKey == 'Course_Faculty_Upload_Notification') {
            $event = 'Course-faculty_Import';
            $reason = "course faculty import";
            $uploadFile = "errors/{$organizationId}-{$uploadId}-upload-errors.csv";
            $emailUploadFile = "errors/course_faculty_uploads/{$organizationId}-{$uploadId}-upload-errors.csv";
        } elseif ($emailKey == 'Course_Upload_Notification') {
            $event = 'Course_Import';
            $reason = "course import";
            $uploadFile = "errors/{$organizationId}-{$uploadId}-upload-errors.csv";
            $emailUploadFile = "errors/course_uploads/{$organizationId}-{$uploadId}-upload-errors.csv";
        } elseif ($emailKey == 'AcademicUpdate_Upload_Notification') {
            $event = 'AcademicUpdate_Import';
            $reason = "Academic Update import";
            $uploadFile = "errors/{$organizationId}-{$uploadId}-upload-errors.csv";
            $emailUploadFile = "errors/academic_update_uploads/{$organizationId}-{$uploadId}-upload-errors.csv";
        }

        if (!is_object($userId)) {
            $user = $this->personRepository->findOneBy(array(
                'id' => $userId
            ));
        }
        $errorFileFlag = false;
        if (!empty($uploadFile)) {
            $uploadFileLog = $this->uploadFileLogRepository->findOneById($uploadId);
            if ($uploadFileLog->getErrorCount() > 0) {
                $errorFileFlag = true;
                $uploadEmailText = 'Click <a class="external-link" href="DOWNLOAD_URL" target="_blank" style="color: rgb(41, 114, 155);text-decoration: underline;">here </a> to download error file.';
                $downloadFailedLogFile = str_replace('DOWNLOAD_URL', $baseURL . $emailUploadFile, $uploadEmailText);
            }
        } else {
            $downloadFailedLogFile = "";
        }

        $coordinatorInfo = $this->personService->getCoordinator($organizationId, null);
        foreach ($coordinatorInfo['coordinators'] as $coordinator) {

            $tokenValues = array();
            $tokenValues['user_first_name'] = $coordinator['firstname'];
            $tokenValues['download_failed_log_file'] = $downloadFailedLogFile;
            $tokenValues[AppointmentsConstant::EMAIL_SKY_LOGO] = "";
            if ($systemUrl) {
                $tokenValues[AppointmentsConstant::EMAIL_SKY_LOGO] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
            }
            $organizationLang = $this->organizationService->getOrganizationDetailsLang($organizationId);
            $languageId = $organizationLang->getLang()->getId();

            $emailTemplate = $this->emailTemplateLangRepository->getEmailTemplateByKey($emailKey, $languageId);

            $responseArray = array();
            if ($emailTemplate) {
                $emailBody = $emailTemplate->getBody();
                $email = $coordinator['email'];
                $emailBody = $this->emailService->generateEmailMessage($emailBody, $tokenValues);

                $bcc = $emailTemplate->getEmailTemplate()->getBccRecipientList();
                $subject = $emailTemplate->getSubject();
                $from = $emailTemplate->getEmailTemplate()->getFromEmailAddress();
                $responseArray['email_detail'] = array(
                    'from' => $from,
                    'subject' => $subject,
                    'bcc' => $bcc,
                    'body' => $emailBody,
                    'to' => $email,
                    'emailKey' => $emailKey,
                    UploadConstant::ORGANIZATION_ID => $organizationId
                );
            }
            $emailInst = $this->emailService->sendEmailNotification($responseArray['email_detail']);
            $this->emailService->sendEmail($emailInst);

        }
        $this->alertNotificationsService->createNotification($event, $reason, $user, null, null, null, $uploadFile, null, null, null, $errorFileFlag);
    }

    public function findAllUploadLogs($uploadId, $uploadType)
    {
        return $this->findOneUploadLogFor( $uploadType, $uploadId );
    }


    public function createUploadService($organizationId, $key, $columns, $rowCount, $jobNumber, $personId = null, $uploadType)
    {
        $uploadFileLog = new UploadFileLog();
        $uploadFileLog->setOrganizationId($organizationId);
        $uploadFileLog->setPersonId($personId);
        $uploadFileLog->setUploadType($uploadType);
        if($uploadType != 'CI'){
            $uploadFileLog->setUploadedColumns(implode(', ', $columns));
        }
        $uploadFileLog->setUploadedRowCount($rowCount);
        $uploadFileLog->setUploadDate(new \DateTime());
        $uploadFileLog->setUploadedFilePath($key);
        $uploadFileLog->setErrorFilePath($key . '.log');
        $uploadFileLog->setStatus('Q');
        $uploadFileLog->setJobNumber($jobNumber);
        return $this->uploadFileLogRepository->persist($uploadFileLog);
    }

    public function createStudentUploadLog($organizationId, $key, $columns, $rowCount, $jobNumber, $personId = null)
    {
        $uploadFileLog = new UploadFileLog();
        $uploadFileLog->setOrganizationId($organizationId);
        $uploadFileLog->setPersonId($personId);
        $uploadFileLog->setUploadType('S');
        $uploadFileLog->setUploadedColumns(implode(', ', $columns));
        $uploadFileLog->setUploadedRowCount($rowCount);
        $uploadFileLog->setUploadDate(new \DateTime());
        $uploadFileLog->setUploadedFilePath($key);
        $uploadFileLog->setStatus('Q');
        $uploadFileLog->setJobNumber($jobNumber);
        $this->uploadFileLogRepository->persist($uploadFileLog);

        return $uploadFileLog;
    }

    public function createCourseUploadLog($organizationId, $key, $columns, $rowCount, $jobNumber, $personId = null)
    {
        $uploadFileLog = new UploadFileLog();
        $uploadFileLog->setOrganizationId($organizationId);
        $uploadFileLog->setPersonId($personId);
        $uploadFileLog->setUploadType('C');
        $uploadFileLog->setUploadedColumns(implode(', ', $columns));
        $uploadFileLog->setUploadedRowCount($rowCount);
        $uploadFileLog->setUploadDate(new \DateTime());
        $uploadFileLog->setUploadedFilePath($key);
        $uploadFileLog->setErrorFilePath($key . '.log');
        $uploadFileLog->setStatus('Q');
        $uploadFileLog->setJobNumber($jobNumber);
        $this->uploadFileLogRepository->persist($uploadFileLog);
        return $uploadFileLog;
    }

    public function createCourseStudentUploadLog($organizationId, $key, $columns, $rowCount, $jobNumber, $personId = null)
    {
        $uploadFileLog = new UploadFileLog();
        $uploadFileLog->setOrganizationId($organizationId);
        $uploadFileLog->setPersonId($personId);
        $uploadFileLog->setUploadType('T');
        $uploadFileLog->setUploadedColumns(implode(', ', $columns));
        $uploadFileLog->setUploadedRowCount($rowCount);
        $uploadFileLog->setUploadDate(new \DateTime());
        $uploadFileLog->setUploadedFilePath($key);
        $uploadFileLog->setErrorFilePath($key . '.log');
        $uploadFileLog->setStatus('Q');
        $uploadFileLog->setJobNumber($jobNumber);
        $this->uploadFileLogRepository->persist($uploadFileLog);
        return $uploadFileLog;
    }

    public function createCourseFacultyUploadLog($organizationId, $key, $columns, $rowCount, $jobNumber, $personId = null)
    {
        $uploadFileLog = new UploadFileLog();
        $uploadFileLog->setOrganizationId($organizationId);
        $uploadFileLog->setPersonId($personId);
        $uploadFileLog->setUploadType('P');
        $uploadFileLog->setUploadedColumns(implode(', ', $columns));
        $uploadFileLog->setUploadedRowCount($rowCount);
        $uploadFileLog->setUploadDate(new \DateTime());
        $uploadFileLog->setUploadedFilePath($key);
        $uploadFileLog->setErrorFilePath($key . '.log');
        $uploadFileLog->setStatus('Q');
        $uploadFileLog->setJobNumber($jobNumber);
        $this->uploadFileLogRepository->persist($uploadFileLog);
        return $uploadFileLog;
    }

    public function createFacultyUploadLog($organizationId, $key, $columns, $rowCount, $jobNumber, $personId = null)
    {
        $uploadFileLog = new UploadFileLog();
        $uploadFileLog->setOrganizationId($organizationId);
        $uploadFileLog->setPersonId($personId);
        $uploadFileLog->setUploadType('F');
        $uploadFileLog->setUploadedColumns(implode(', ', $columns));
        $uploadFileLog->setUploadedRowCount($rowCount);
        $uploadFileLog->setUploadDate(new \DateTime());
        $uploadFileLog->setUploadedFilePath($key);
        $uploadFileLog->setErrorFilePath($key . '.log');
        $uploadFileLog->setStatus('Q');
        $uploadFileLog->setJobNumber($jobNumber);
        $this->uploadFileLogRepository->persist($uploadFileLog);
        return $uploadFileLog;
    }

    public function markStatusInvalid($id , $orgId){

        $uploadFileLog =  $this->uploadFileLogRepository->findOneBy(array(
            'id' => $id,
            'organizationId' => $orgId
        ));
        if($uploadFileLog){
            $uploadFileLog->setStatus('W');
            $this->uploadFileLogRepository->update($uploadFileLog);
        }else{
            throw new ValidationException([
                "Invalid ID"
            ], "Invalid ID", "Invalid ID");
        }
    }

    protected function getDateByTimezone($timez, $metadataListValues)
    {
        $timezone = $metadataListValues->findByListName($timez);
        if ($timezone) {
            $timezone = $timezone[0]->getListValue();
        }
        try {
            $currentNow = new \DateTime('now', new \DateTimeZone($timezone));
            $currentNow->setTimezone(new \DateTimeZone('UTC'));
        } catch (Exception $e) {
            $currentNow = new \DateTime('now');
        }
        $currentDate = $currentNow->format(ReportsConstants::YMD);
        return $currentDate;
    }
    private function isObjectExist($object, $message, $key)
    {
        if (! isset($object)) {
            throw new ValidationException([
                $message
                ], $message, $key);
        }
    }

    /**
     * Gets the organization upload history
     *
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param int|null $pageNumber
     * @param int|null $offset
     * @param string $sortBy
     * @param string $filter
     * @param bool $isCSV
     * @param bool $isJob
     * @return array
     */
    public function listHistory($loggedInUserId, $organizationId, $pageNumber = null, $offset = null, $sortBy = '', $filter, $isCSV = false, $isJob = false)
    {
        if (!$isJob) {
            $this->checkIsCoordinator($organizationId, $loggedInUserId);
        }
        $organization = $this->organizationService->find($organizationId);
        if(empty($organization)){
            throw new SynapseValidationException("Organization Not Found.");
        }

        $person = $this->personRepository->find($loggedInUserId);
        if(empty($person)){
            throw new SynapseValidationException('Person not found.');
        }

        $currentDateTime = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, new \DateTime(), 'Ymd_HisT');

        $pageNumber = (int)$pageNumber;
        if (!$pageNumber) {
            $pageNumber = SynapseConstant::DEFAULT_PAGE_NUMBER;
        }
        $offset = (int)$offset;
        if (!$offset) {
            $offset = SynapseConstant::DEFAULT_RECORD_COUNT;
        }
        $startPoint = ($pageNumber * $offset) - $offset;
        $endPoint = $offset;

        $uploadFileHistoryData = [];

        if ($isCSV) {
            $jobNumber = uniqid();
            $uploadHistoryPageJob  = new UploadHistoryPageJob();
            $uploadHistoryPageJob->args = array(
                'jobNumber' => $jobNumber,
                'loggedInUser' => $loggedInUserId,
                'currentDateTime' => $currentDateTime,
                'orgId' => $organizationId,
                'pageNo' => $pageNumber,
                'offset' => $offset,
                'sortBy' => $sortBy,
                'filter' => $filter
            );
            $this->resque->enqueue($uploadHistoryPageJob, true);
            return [SynapseConstant::DOWNLOAD_IN_PROGRESS_MESSAGE];
        }
        $uploadType = $this->getUploadTypeByFilter($filter);

        $uploadFileHistory = $this->uploadFileLogRepository->listHistory($organizationId, $startPoint, $endPoint, $sortBy, $uploadType, $isJob);

        if ($isJob) {
            return $uploadFileHistory;
        }
        $countQuery = "SELECT FOUND_ROWS() cnt";
        $cntQuery = $this->uploadFileLogRepository->getQueryResultSet($countQuery);
        $totalCount = $cntQuery[0]['cnt'];

        $totalPageCount = ceil($totalCount / $offset);
        $uploadFileHistoryData['total_records'] = (int)$totalCount;
        $uploadFileHistoryData['total_pages'] = (int)$totalPageCount;
        $uploadFileHistoryData['records_per_page'] = $offset;
        $uploadFileHistoryData['current_page'] = $pageNumber;

        $uploadFileHistoryDataSet = [];
        $url = '';
        foreach ($uploadFileHistory as $history) {

            extract($this->getUploadType($history['type'], $history['file_name']));
            $uploadFileHistoryDto = new UploadFileHistoryDto();
            $uploadFileHistoryDto->setId($history['upload_file_log_id']);

            $fileName = $history['upload_file_name'];
            $errorFilePath = $history['error_count'] > 0 ? $history['error'] : '';
            $uploadFileHistoryDto->setFileName($fileName);
            $uploadFileHistoryDto->setType($uploadType);

            $uploadDate = new \DateTime($history['uploaded_date']);
            $uploadFileHistoryDto->setUploadedDate($uploadDate);

            if (empty($history['lastname']) && empty($history['firstname'])) {
                $uploadFileHistoryDto->setUploadedBy('FTP');
            } else {
                $uploadFileHistoryDto->setUploadedBy($history['lastname'] . ', ' . $history['firstname']);
            }
            $uploadFileHistoryDto->setSuccess($history['file_name']);
            $uploadFileHistoryDto->setError($errorFilePath);
            $uploadFileHistoryDto->setRepositoryUrl($url);
            $uploadFileHistoryDataSet[] = $uploadFileHistoryDto;
        }
        $uploadFileHistoryData['data'] = $uploadFileHistoryDataSet;

        return $uploadFileHistoryData;
    }

    private function getUploadType($type, $fileName)
    {
        $responseArr = [];
        $uploadType = '';
        if ($type == 'F') {
            $uploadType = 'Faculty Upload';
            $url = 'faculty_uploads';
            $responseArr = [
                'uploadType' => $uploadType,
                'url' => $url
            ];
        } elseif ($type == 'S') {
            $uploadType = 'Student Upload';
            $url = 'student_uploads';
            $responseArr = [
                'uploadType' => $uploadType,
                'url' => $url
            ];
        } elseif ($type == 'C') {
            $uploadType = 'Courses';
            $url = 'course_uploads';
            $responseArr = [
                'uploadType' => $uploadType,
                'url' => $url
            ];
        } elseif ($type == 'T') {
            $uploadType = 'Student Course';
            $url = 'course_student_uploads';
            $responseArr = [
                'uploadType' => $uploadType,
                'url' => $url
            ];
        } elseif ($type == 'P') {
            $uploadType = 'Faculty Course';
            $url = 'course_faculty_uploads';
            $responseArr = [
                'uploadType' => $uploadType,
                'url' => $url
            ];
        }
        elseif ($type == 'G') {
        	$uploadType = 'Subgroups';
        	$url = 'group_uploads';
        	$responseArr = [
        	'uploadType' => $uploadType,
        	'url' => $url
        	];
        }
        elseif ($type == 'GF') {
        	$uploadType = 'Group Faculty';
        	$url = 'group_uploads';
        	$responseArr = [
        	'uploadType' => $uploadType,
        	'url' => $url
        	];
        }
        elseif ($type == 'GS') {
        	$uploadType = 'Group Student';
        	$url = 'group_uploads';
        	$responseArr = [
        	'uploadType' => $uploadType,
        	'url' => $url
        	];
        }
        elseif ($type == 'A') {
        	$uploadType = 'Academic Updates';
        	$url = 'academic_update_uploads';
        	$responseArr = [
        	'uploadType' => $uploadType,
        	'url' => $url
        	];
        }
        elseif ($type == 'S2G') {
        	$uploadType = 'Group Student';
        	$url = 'group_uploads';
        	$responseArr = [
        	'uploadType' => $uploadType,
        	'url' => $url
        	];
        }
        return $responseArr;
    }

    private function checkIsCoordinator($orgId, $userId)
    {
        $this->logger->debug(" Check User is Coorindator of an Organization having Organization Id " . $orgId);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(self::ORG_ROLE_REPO);
        $isCoordinator = $this->orgRoleRepository->getUserCoordinatorRole($orgId, $userId);
        if (! $isCoordinator) {
            throw new AccessDeniedException();
        }
        $this->logger->info("Upload History - Check User is Coorindator for Organization");
    }

    private function getUploadTypeByFilter($filter)
    {
        $uploadType = (array)json_decode(trim($filter));

        if(empty($filter) || (array_key_exists('type', $uploadType) && empty($uploadType['type']))){

            return array('F','S','C','T','P','G','GF','GS','A', 'S2G');
        }
        $uploadTypeArr = array(
            'student_upload' => array('S'),
            'faculty_upload' => array('F'),
            'group_faculty' => array('GF'),
            'group_student' => array('GS', 'S2G'),
            'subgroups' => array('G'),
            'courses' => array('C'),
            'student_course' => array('T'),
            'faculty_course' => array('P'),
            'academic_updates' => array('A')
        );

        return $uploadTypeArr[$uploadType['type']];
    }
}