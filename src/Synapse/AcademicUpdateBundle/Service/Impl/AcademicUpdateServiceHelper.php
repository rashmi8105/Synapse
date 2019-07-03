<?php
namespace Synapse\AcademicUpdateBundle\Service\Impl;

use Aws\Common\Signature\time;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Entity\OrgCourses;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdate;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequestCourse;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequestFaculty;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateCreateDto;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDetailsStudentDto;
use Synapse\AcademicUpdateBundle\EntityDto\FacultiesDetailsResponseDto;
use Synapse\AcademicUpdateBundle\Exception\AcademicUpdateCreateException;
use Synapse\CoreBundle\Entity\EmailTemplateLang;
use Synapse\CoreBundle\Entity\MetadataListValues;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\AcademicUpdateConstant;
use Synapse\CoreBundle\Util\UtilServiceHelper;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * Class AcademicUpdateServiceHelper
 * @package Synapse\AcademicUpdateBundle\Service\Impl
 *
 * Inheritance pattern is incorrect.
 * @deprecated
 */
class AcademicUpdateServiceHelper extends AcademicUpdateServiceBaseHelper
{

    // Scaffolding

    /**
     * @var Container
     */
    protected $container;


    // Services

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationService;

    /**
     * @var DataProcessingUtilityService
     */
    private $dataProcessingUtilityService;

    /**
     * @var CSVUtilityService
     */
    private $CSVUtilityService;

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
     * @var UtilServiceHelper
     */
    private $utilServiceHelper;


    // Repositories

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;


    /**
     * AcademicUpdateServiceHelper constructor.
     *
     * @param RepositoryResolver $repositoryResolver
     * @param Logger $logger
     * @param Container $container
     *
     * NOTE: This constructor is missing the DI/Inject due to the child class having it.
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        // Scaffolding
        $this->container = $container;

        // Services
        $this->alertNotificationService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->dataProcessingUtilityService = $this->container->get(DataProcessingUtilityService::SERVICE_KEY);
        $this->CSVUtilityService = $this->container->get(CSVUtilityService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->utilServiceHelper = $this->container->get(UtilServiceHelper::SERVICE_KEY);

        // Repositories
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
    }


    /**
     * @param object $object
     * @param string $message
     * @param string $key
     *
     * @deprecated
     */
    protected function isObjectExist($object, $message, $key)
    {
        if (!isset($object) || empty($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    /**
     * @param string $timez - name of timezone ("Central", "Eastern", etc)
     * @param MetadataListValues $metadataListValues - MetadataListValues repository
     * @return string
     *
     * @deprecated Please use a corresponding function from DateUtilityService
     */
    protected function getDateByTimezone($timez, $metadataListValues)
    {
        $timezone = $metadataListValues->findByListName($timez);
        if ($timezone) {
            $timezone = $timezone[0]->getListValue();
        }
        try {
            $currentNow = new \DateTime('now', new \DateTimeZone($timezone));
            $currentNow->setTimezone(new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            $currentNow = new \DateTime('now');
        }
        $currentDate = $currentNow->format(AcademicUpdateConstant::YMD);
        return $currentDate;
    }

    /**
     * @param string $timezone - name of timezone ("Central", "Eastern", etc)
     * @return array|null
     *
     * @deprecated Please use a corresponding function from DateUtilityService
     */
    public function getOrganizationTimezone($timezone)
    {
        $timezone = $this->repositoryResolver->getRepository(AcademicUpdateConstant::METADATA_REPO)->findByListName($timezone);
        if ($timezone) {
            $timezone = $timezone[0]->getListValue();
        }
        return $timezone;
    }

    /**
     * Sends Email Notification.
     *
     * @param EmailTemplateLang $emailTemplate
     * @param array $tokenValues
     * @param array $emailDetails
     */
    protected function sendEmailNotification($emailTemplate, $tokenValues, $emailDetails)
    {
        $responseArray = [];
        if ($emailTemplate) {
            $emailBody = $emailTemplate->getBody();
            $emailBody = $this->emailService->generateEmailMessage($emailBody, $tokenValues);
            $bcc = $emailTemplate->getEmailTemplate()->getBccRecipientList();
            $subject = $emailTemplate->getSubject() . " " . $emailDetails[AcademicUpdateConstant::KEY_SUBJECT];
            $from = $emailTemplate->getEmailTemplate()->getFromEmailAddress();
            $responseArray[AcademicUpdateConstant::HELP_EMAIL_DETAIL] = array(
                'from' => $from,
                AcademicUpdateConstant::KEY_SUBJECT => $subject,
                'bcc' => $bcc,
                'body' => $emailBody,
                'to' => $emailDetails[AcademicUpdateConstant::KEY_FACULTY_EMAIL],
                AcademicUpdateConstant::KEY_EMAILKEY => $emailDetails[AcademicUpdateConstant::KEY_EMAILKEY],
                'organizationId' => $emailDetails['orgId']
            );
        }
        $emailInst = $this->emailService->sendEmailNotification($responseArray[AcademicUpdateConstant::HELP_EMAIL_DETAIL]);
        $this->emailService->sendEmail($emailInst);
    }

    /**
     * @param array $staffList
     * @param array $aList
     * @param string $userType
     * @return array
     */
    protected function bindStaffDetails($staffList, $aList, $userType)
    {
        $facultyDetails = array();
        if ($userType == AcademicUpdateConstant::STR_USER_TYPE) {
            $facultiesDetailsResponseDto = new FacultiesDetailsResponseDto();
            $facultiesDetailsResponseDto->setStaffFirstname("me");
            $facultiesDetailsResponseDto->setStaffLastname("");
            $facultyDetails[] = $facultiesDetailsResponseDto;
        }
        if (array_key_exists($aList[AcademicUpdateConstant::AU_REQUEST_ID], $staffList)) {
            foreach ($staffList[$aList[AcademicUpdateConstant::AU_REQUEST_ID]] as $staff) {
                $facultiesDetailsResponseDto = new FacultiesDetailsResponseDto();
                $facultiesDetailsResponseDto->setStaffFirstname($staff['firstname']);
                $facultiesDetailsResponseDto->setStaffLastname($staff['lastname']);
                $facultyDetails[] = $facultiesDetailsResponseDto;
            }
        }
        return $facultyDetails;
    }

    /**
     * @param array|null $academicUpdateFaculties
     * @return array
     */
    protected function mapRequestStaffValue($academicUpdateFaculties)
    {
        $staffList = array();
        if (isset($academicUpdateFaculties) && count($academicUpdateFaculties) > 0) {
            foreach ($academicUpdateFaculties as $staff) {
                $staffList[$staff[AcademicUpdateConstant::AU_REQUEST_ID]][] = $staff;
            }
        }
        return $staffList;
    }

    /**
     * @param boolean $isCoordinator
     * @param string $message
     * @param string $key
     */
    protected function isCoordinator($isCoordinator, $message, $key)
    {
        if (!$isCoordinator) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    /**
     * Returns $trueValue if $left == $right, otherwise returns $falseValue.
     * Same functionality as a ternary operator.
     *
     * @param int $left
     * @param int $right
     * @param string $trueValue
     * @param string $falseValue
     * @return mixed
     */
    protected function checkCondition($left, $right, $trueValue, $falseValue)
    {
        $return = $falseValue;
        if ($left == $right) {
            $return = $trueValue;
        }
        return $return;
    }

    /**
     * Check the AU competed for a student + course
     *
     * @param array $detailsArray
     * @return boolean
     */
    protected function checkDatacompleted($detailsArray)
    {
        $response = true;
        if (is_null($detailsArray[AcademicUpdateConstant::STUDENT_RISK]) && is_null($detailsArray['student_grade']) && is_null($detailsArray['student_absences']) && is_null($detailsArray[AcademicUpdateConstant::STUDENT_COMMENTS]) && !$detailsArray['student_refer']) {
            $response = false;
        } else {
            $response = true;
        }

        return $response;
    }

    /**
     * If array is empty, throws AcademicUpdateCreateException error.
     * Otherwise returns true.
     *
     * @param array $array
     * @return boolean
     * @throws AcademicUpdateCreateException
     */
    protected function checkArrayCount($array)
    {
        if (count($array) > 0) {
            return true;
        } else {
            throw new AcademicUpdateCreateException();
        }
    }

    /**
     * Throws ValidationException if the due date is before the org's current date.
     * Otherwise returns true.
     *
     * @param \DateTime $orgCurrentUtcDate
     * @param \DateTime $dueDate
     * @throws ValidationException
     * @return boolean
     */
    protected function validateDueDate($orgCurrentUtcDate, $dueDate)
    {
        if ($orgCurrentUtcDate > $dueDate) {
            throw new ValidationException([
                'Due Date Grater then creation date'
            ], 'Due Date Grater then creation date', 'academic_update_due_date_error');
        } else {
            return true;
        }
    }

    /**
     * Returns either 'targeted' or 'bulk' depending on the type of the update.
     *
     * @param AcademicUpdateCreateDto $academicUpdateCreateDto
     * @return string
     */
    protected function getUpdateType($academicUpdateCreateDto)
    {
        $selectedStudents = $academicUpdateCreateDto->getStudents();
        $selectedStaff = $academicUpdateCreateDto->getStaff();
        $selectedCourses = $academicUpdateCreateDto->getCourses();
        if ($selectedStudents->getIsAll() || $selectedStaff->getIsAll() || $selectedCourses->getIsAll()) {
            $updateType = 'bulk';
        } else {
            $updateType = 'targeted';
        }
        return $updateType;
    }

    /**
     * Get all(Open|Closed) academic update details and create a CSV file for these academic update details
     *
     * @param array $academicUpdateLists
     * @param string $loggedInUserId
     * @param string $currentDate
     * @param string $organizationId
     */
    protected function getAcademicUpdateDetailsCSV($academicUpdateLists, $loggedInUserId, $currentDate, $organizationId)
    {
        $csvHeader = [
            'Request Name',
            'Description',
            'Date Range - From',
            'Date Range - To',
            'Completed Total',
            'Total Updates',
            'Status'
        ];

        $filePath = SynapseConstant::S3_ROOT . SynapseConstant::S3_ROASTER_UPLOAD_DIRECTORY . '/';
        $fileName = "{$organizationId}-{$loggedInUserId}-{$currentDate}-view-academic-updates-roaster.csv";
        $fh = @fopen($filePath . $fileName, 'w');
        fputcsv($fh, $csvHeader);

        if (isset($academicUpdateLists) && count($academicUpdateLists) > 0) {
            foreach ($academicUpdateLists as $academicUpdateList) {
                $requestCreatedDate = new \DateTime($academicUpdateList['requestCreated']);
                $requestDueDate = new \DateTime($academicUpdateList['requestDue']);

                $formattedOrganizationAdjustedRequestCreatedDatetime =  $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, $requestCreatedDate, 'm/d/Y');
                $formattedOrganizationAdjustedRequestDueDatetime = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, $requestDueDate, 'm/d/Y');

                $academicUpdateList['requestCreated'] = $formattedOrganizationAdjustedRequestCreatedDatetime;
                $academicUpdateList['requestDue'] = $formattedOrganizationAdjustedRequestDueDatetime;

                if ($academicUpdateList['status'] == 'closed' || $academicUpdateList['pastDueDate']) {
                    $status = 'closed';
                } else {
                    $status = $academicUpdateList['status'];
                }
                $academicUpdateList = [

                    $academicUpdateList['name'],
                    $academicUpdateList['description'],
                    $formattedOrganizationAdjustedRequestCreatedDatetime,
                    $formattedOrganizationAdjustedRequestDueDatetime,
                    $academicUpdateList['completedTotal'],
                    $academicUpdateList['totalUpdates'],
                    $status
                ];

                fputcsv($fh, $academicUpdateList);
            }

        }
        fclose($fh);
    }

    /**
     * Export an academic update request as a CSV file.
     *
     * @param array $academicUpdateRequestDetails
     * @param int $loggedInUserId
     * @param int $organizationId
     * @return string
     */
    protected function formatIndividualAcademicUpdateRequestAsCSV($academicUpdateRequestDetails, $loggedInUserId, $organizationId)
    {
        $timeStamp = time();

        $headers = [
            'request_name' => 'Request Name',
            'request_description' => 'Request Description',
            'request_created' => 'Request Created',
            'request_due' => 'Request Due',
            'request_from' => 'Request From',
            'academic_update_status' => 'Update Status',
            'course_name' => 'Course Name',
            'course_section_id' => 'Course Section',
            'student_lastname' => 'Student Last Name',
            'student_firstname' => 'Student First Name',
            'student_external_id' => 'External ID',
            'student_risk' => 'Student Risk',
            'student_grade' => 'Student In Progress Grade',
            'student_absences' => 'Student Absences',
            'student_comments' => 'Student Comments',
            'student_refer' => 'Student Refer',
            'student_send' => 'Student Notify'
        ];

        return $this->CSVUtilityService->generateCSV('data://roaster_uploads/', "{$organizationId}-{$loggedInUserId}-{$timeStamp}-view-academic-updates-roaster.csv", $academicUpdateRequestDetails, $headers);

    }

    /**
     * Update Academic Update Details.
     *
     * @param AcademicUpdate $academicUpdate
     * @param AcademicUpdateDetailsStudentDto $updateDetail
     * @param \DateTime $orgCurrentUtcDate
     * @param Person $personUpdated
     * @return int
     */
    protected function setAcademicUpdate($academicUpdate, $updateDetail, $orgCurrentUtcDate, $personUpdated)
    {
        $academicUpdate->setUpdateDate($orgCurrentUtcDate);

        // replace empty strings with null, and capitalize the first letter if not null.
        $failureRiskLevel = ucfirst($updateDetail->getStudentRisk());
        $failureRiskLevel = empty($failureRiskLevel) ? null : $failureRiskLevel;
        $academicUpdate->setFailureRiskLevel($failureRiskLevel);

        $inProgressGrade = $updateDetail->getStudentGrade();
        $inProgressGrade = empty($inProgressGrade) ? null : $inProgressGrade;
        $academicUpdate->setGrade($inProgressGrade);

            $absences = $updateDetail->getStudentAbsences();
            $absences = (isset($absences) && is_numeric($absences)) ? $absences : null;
            $academicUpdate->setAbsence($absences);

        $comment = $updateDetail->getStudentComments();
        $comment = (isset($comment)) ? $comment : null;
        $academicUpdate->setComment($comment);

        $academicUpdate->setReferForAssistance($updateDetail->getStudentAcademicAssistRefer());
        $academicUpdate->setSendToStudent($updateDetail->getStudentSend());
        $academicUpdate->setIsBypassed($updateDetail->getIsBypassed());

        // Person responded
        $academicUpdate->setPersonFacultyResponded($personUpdated);
        $studentId = 0;

        $academicUpdate->setStatus('saved');

        return $studentId;
    }

    /**
     * Checks whether to notify student or not.
     *
     * @param boolean $notifyStudent
     * @param array $studentIds
     * @param Organization $organization
     * @return bool
     */
    public function checkEmailSendToStudent($notifyStudent, $studentIds, $organization)
    {
        if ($notifyStudent) {
            return $this->sendStudentNotification($studentIds, $organization);
        } else {
            return false;
        }
    }


    /**
     * @param AcademicUpdateRequest $auRequest
     * @param Organization $organization
     * @param Person $personStaff
     */
    protected function addSelectedFaculty($auRequest, $organization, $personStaff)
    {
        $auUpdateRepo = $this->repositoryResolver->getRepository(AcademicUpdateConstant::AU_UPDATE_REPO);
        if ($auRequest->getSelectFaculty() == AcademicUpdateConstant::SELECT_TYPE_INDIVIDUAL) {
            $academicUpdateStaff = new AcademicUpdateRequestFaculty();
            $academicUpdateStaff->setOrg($organization);
            $academicUpdateStaff->setPerson($personStaff);
            $academicUpdateStaff->setAcademicUpdateRequest($auRequest);
            $auUpdateRepo->persist($academicUpdateStaff, false);
        }
    }

    /**
     * @param AcademicUpdateRequest $auRequest
     * @param Organization $organization
     * @param OrgCourses $studentCourse
     */
    protected function addSelectedCourse($auRequest, $organization, $studentCourse)
    {
        $auUpdateRepo = $this->repositoryResolver->getRepository(AcademicUpdateConstant::AU_UPDATE_REPO);
        if ($auRequest->getSelectCourse() == AcademicUpdateConstant::SELECT_TYPE_INDIVIDUAL) {
            $academicUpdateCourse = new AcademicUpdateRequestCourse();
            $academicUpdateCourse->setOrg($organization);
            $academicUpdateCourse->setOrgCourses($studentCourse);
            $academicUpdateCourse->setAcademicUpdateRequest($auRequest);
            $auUpdateRepo->persist($academicUpdateCourse, false);
        }
    }

    /**
     * Sends academic update related emails to students.
     * TODO:: Move this under the Mapworks Action code.
     *
     * @param array $studentIds
     * @param Organization $organization
     * @return int
     */
    protected function sendStudentNotification($studentIds, $organization)
    {
        $personDetails = $this->personRepository->getUsersByUserIds($studentIds);

        $emailKey = 'Academic_Update_Notification_Student';
        $ebiLanguage = $this->ebiConfigRepository->findOneBy(['key' => 'Ebi_Lang']);
        $emailTemplate = $this->emailTemplateLangRepository->getEmailTemplateByKey($emailKey, $ebiLanguage->getValue());
        $emailTemplateBody = $emailTemplate->getBody();
        $responseArray = [];
        $tokenValues = [];
        if (count($personDetails) > 0) {

            $organizationId = $personDetails[0]['organization_id'];
            $systemApiUrl = $this->ebiConfigService->get('System_API_URL');
            $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
            $tokenValues['Skyfactor_Mapworks_logo'] = $systemUrl . 'images/Skyfactor-Mapworks-login.png';

            foreach ($personDetails as $personDetail) {

                $usernameEncrypt = $this->dataProcessingUtilityService->encrypt($personDetail['username'], SynapseConstant::ENCRYPTION_METHOD, SynapseConstant::ENCRYPTION_HASH);
                $usernameEncrypt = base64_encode($usernameEncrypt);

                $studentLoginUrl = $systemApiUrl . "api/v1/email/$usernameEncrypt/academicupdate";

                //If the organization has LDAP or SAML enabled, the student should be redirected to login instead of being automatically logged in.
                if ($organization->getIsLdapSamlEnabled()) {
                    $studentLoginUrl = $this->ebiConfigService->generateCompleteUrl('Student_Course_List_Page', $organizationId);
                }

                $tokenValues['studentname'] = $personDetail['user_firstname'] . " " . $personDetail['user_lastname'];
                $tokenValues['student_update_link'] = $studentLoginUrl;

                $emailBody = $this->emailService->generateEmailMessage($emailTemplateBody, $tokenValues);

                $responseArray['email_detail'] = array(
                    'from' => $emailTemplate->getEmailTemplate()->getFromEmailAddress(),
                    'subject' => $emailTemplate->getSubject(),
                    'bcc' => $emailTemplate->getEmailTemplate()->getBccRecipientList(),
                    'body' => $emailBody,
                    'emailKey' => $emailKey,
                    'to' => $personDetail['username'],
                    'organizationId' => $organizationId
                );

                $emailInstance = $this->emailService->sendEmailNotification($responseArray['email_detail']);
                $emailSentStatus = $this->emailService->sendEmail($emailInstance);
                return $emailSentStatus;
            }
        }
    }
}