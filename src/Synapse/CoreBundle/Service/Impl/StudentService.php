<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Repository\OrgCourseFacultyRepository;
use Synapse\AcademicBundle\Repository\OrgCourseStudentRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\AcademicBundle\Service\Impl\CourseFacultyStudentValidatorService;
use Synapse\CampusConnectionBundle\Service\Impl\CampusConnectionService;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgPersonStudent;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\StudentDbViewLog;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\DataProcessingExceptionHandler;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\ActivityCategoryLangRepository;
use Synapse\CoreBundle\Repository\ActivityCategoryRepository;
use Synapse\CoreBundle\Repository\ActivityLogRepository;
use Synapse\CoreBundle\Repository\AppointmentRecepientAndStatusRepository;
use Synapse\CoreBundle\Repository\EbiMetadataListValuesRepository;
use Synapse\CoreBundle\Repository\FeatureMasterLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\OrgGroupStudentsRepository;
use Synapse\CoreBundle\Repository\OrgGroupTreeRepository;
use Synapse\CoreBundle\Repository\OrgMetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetDatablockRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetMetadataRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentCohortRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\OrgTalkingPointsRepository;
use Synapse\CoreBundle\Repository\PersonEbiMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonOrgMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\ReferralRepository;
use Synapse\CoreBundle\Repository\SearchRepository;
use Synapse\CoreBundle\Repository\StudentDbViewLogRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\PermissionConstInterface;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\Service\Utility\EntityValidationService;
use Synapse\CoreBundle\Service\Utility\URLUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\PersonConstant;
use Synapse\CoreBundle\Util\Constants\ReferralConstant;
use Synapse\CoreBundle\Util\Constants\StudentConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\PersonBundle\DTO\PersonDTO;
use Synapse\RestBundle\Entity\AppointmentListArrayResponseDto;
use Synapse\RestBundle\Entity\OrgGroupDto;
use Synapse\RestBundle\Entity\StudentContactsDto;
use Synapse\RestBundle\Entity\StudentDetailsResponseDto;
use Synapse\RestBundle\Entity\StudentGroupsListDto;
use Synapse\RestBundle\Entity\StudentListArrayResponseDto;
use Synapse\RestBundle\Entity\StudentListHeaderResponseDto;
use Synapse\RestBundle\Entity\StudentOpenAppResponseDto;
use Synapse\RestBundle\Entity\StudentOpenReferralsDto;
use Synapse\RestBundle\Entity\StudentPolicyDto;
use Synapse\RestBundle\Entity\StudentProfileResponseDto;
use Synapse\RestBundle\Entity\StudentReferralsDto;
use Synapse\RestBundle\Entity\StudentTalkingPointsDto;
use Synapse\RestBundle\Entity\TalkingPointsDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\RiskBundle\Entity\RiskGroupPersonHistory;
use Synapse\RiskBundle\Repository\RiskGroupPersonHistoryRepository;
use Synapse\RiskBundle\Repository\RiskGroupRepository;
use Synapse\RiskBundle\Service\Impl\RiskGroupService;
use Synapse\SurveyBundle\Repository\OrgPersonStudentSurveyLinkRepository;

/**
 * @DI\Service("student_service")
 */
class StudentService extends AbstractService implements  PermissionConstInterface
{

    const SERVICE_KEY = 'student_service';


    private $mapPersonDtoFieldToDB = [
        'photo_link' => 'photo_url',
        'primary_campus_connection_id' => 'person_id_primary_connect'
    ];

    //Variables

    /**
     * @var string
     */
    private $allStudentGroupExternalID = "ALLSTUDENTS";

    //Scaffolding

    /**
     * @var Container
     */
    private $container;
    /**
     * @var Manager
     */
    public $rbacManager;

    //Services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var ActivityService
     */
    private $activityService;

    /**
     * @var CampusConnectionService
     */
    private $campusConnectionService;

    /**
     * @var CourseFacultyStudentValidatorService
     */
    private $courseFacultyStudentValidatorService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var EntityValidationService
     */
    private $entityValidationService;

    /**
     * @var GroupService
     */
    private $groupService;

    /**
     * @var OrgPermissionsetService
     */
    private $orgPermissionService;

    /**
     * @var OrganizationService
     */
    private $orgService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var RiskGroupService
     */
    private $riskGroupService;

    /**
     * @var URLUtilityService
     */
    private $urlUtilityService;

    //Repositories

    /**
     * @var OrgAcademicYearRepository
     */
    private $academicYearRepository;
    /**
     * @var ActivityCategoryRepository
     */
    private $activityCategoryRepository;

    /**
     * @var ActivityCategoryLangRepository
     */
    private $activityCategoryLangRepository;

    /**
     * @var ActivityLogRepository
     */
    private $activityLogRepository;

    /**
     * @var AppointmentRecepientAndStatusRepository
     */
    private $appointmentsRecipientAndStatusRepository;

    /**
     * @var EbiMetadataListValuesRepository
     */
    private $ebiMetadataListValuesRepository;

    /**
     * @var FeatureMasterLangRepository
     */
    private $featureMasterLangRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgCourseFacultyRepository
     */
    private $orgCourseFacultyRepository;
    /**
     * @var OrgCourseStudentRepository
     */
    private $orgCourseStudentRepository;

    /**
     * @var OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;
    /**
     * @var OrgGroupStudentsRepository
     */
    private $orgGroupStudentsRepository;
    /**
     * @var OrgGroupTreeRepository
     */
    private $orgGroupTreeRepository;
    /**
     * @var OrgMetadataListValuesRepository
     */
    private $orgMetadataListValuesRepository;
    /**
     * @var OrgPermissionsetDatablockRepository
     */
    private $orgPermissionsetDatablockRepository;
    /**
     * @var OrgPermissionsetMetadataRepository
     */
    private $orgPermissionsetMetadataRepository;
    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;
    /**
     * @var OrgPersonStudentCohortRepository
     */
    private $orgPersonStudentCohortRepo;
    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;
    /**
     * @var OrgPersonStudentSurveyLinkRepository
     */
    private $orgPersonStudentSurveyLinkRepository;
    /**
     * @var OrgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;

    /**
     * @var OrgTalkingPointsRepository
     */
    private $orgTalkingPointsRepository;

    /**
     * @var PersonEbiMetaDataRepository
     */
    private $personEbiMetadataRepository;
    /**
     * @var PersonOrgMetaDataRepository
     */
    private $personOrgMetadataRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var ReferralRepository
     */
    private $referralRepository;

    /**
     * @var RiskGroupPersonHistoryRepository
     */
    private $riskGroupPersonHistoryRepository;

    /**
     * @var RiskGroupRepository
     */
    private $riskGroupRepository;

    /**
     * @var SearchRepository
     */
    private $searchRepository;

    /**
     * @var StudentDbViewLogRepository
     */
    private $studentDbViewLogRepository;


    /**
     * StudentService constructor.
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

        //Scaffolding
        $this->container = $container;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);

        //Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->activityService = $this->container->get(ActivityService::SERVICE_KEY);
        $this->campusConnectionService = $this->container->get(CampusConnectionService::SERVICE_KEY);
        $this->courseFacultyStudentValidatorService = $this->container->get(CourseFacultyStudentValidatorService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->entityValidationService =  $this->container->get(EntityValidationService::SERVICE_KEY);
        $this->groupService = $this->container->get(GroupService::SERVICE_KEY);
        $this->orgService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->orgPermissionService = $this->container->get(OrgPermissionsetService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->riskGroupService = $this->container->get(RiskGroupService::SERVICE_KEY);
        $this->urlUtilityService = $this->container->get(URLUtilityService::SERVICE_KEY);

        //Repositories
        $this->academicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->activityCategoryRepository = $this->repositoryResolver->getRepository(ActivityCategoryRepository::REPOSITORY_KEY);
        $this->activityCategoryLangRepository = $this->repositoryResolver->getRepository(ActivityCategoryLangRepository::REPOSITORY_KEY);
        $this->activityLogRepository = $this->repositoryResolver->getRepository(ActivityLogRepository::REPOSITORY_KEY);
        $this->appointmentsRecipientAndStatusRepository = $this->repositoryResolver->getRepository(AppointmentRecepientAndStatusRepository::REPOSITORY_KEY);
        $this->ebiMetadataListValuesRepository = $this->repositoryResolver->getRepository(EbiMetadataListValuesRepository::REPOSITORY_KEY);
        $this->featureMasterLangRepository = $this->repositoryResolver->getRepository(FeatureMasterLangRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgCourseFacultyRepository = $this->repositoryResolver->getRepository(OrgCourseFacultyRepository::REPOSITORY_KEY);
        $this->orgCourseStudentRepository = $this->repositoryResolver->getRepository(OrgCourseStudentRepository::REPOSITORY_KEY);
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgGroupFacultyRepository::REPOSITORY_KEY);
        $this->orgGroupStudentsRepository = $this->repositoryResolver->getRepository(OrgGroupStudentsRepository::REPOSITORY_KEY);
        $this->orgGroupTreeRepository = $this->repositoryResolver->getRepository(OrgGroupTreeRepository::REPOSITORY_KEY);
        $this->orgMetadataListValuesRepository = $this->repositoryResolver->getRepository(OrgMetadataListValuesRepository::REPOSITORY_KEY);
        $this->orgPermissionsetDatablockRepository = $this->repositoryResolver->getRepository(OrgPermissionsetDatablockRepository::REPOSITORY_KEY);
        $this->orgPermissionsetMetadataRepository = $this->repositoryResolver->getRepository(OrgPermissionsetMetadataRepository::REPOSITORY_KEY);
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->orgPersonStudentCohortRepo = $this->repositoryResolver->getRepository(OrgPersonStudentCohortRepository::REPOSITORY_KEY);
        $this->orgPersonStudentSurveyLinkRepository = $this->repositoryResolver->getRepository(OrgPersonStudentSurveyLinkRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->personEbiMetadataRepository = $this->repositoryResolver->getRepository(PersonEbiMetaDataRepository::REPOSITORY_KEY);
        $this->personOrgMetadataRepository = $this->repositoryResolver->getRepository(PersonOrgMetaDataRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->referralRepository = $this->repositoryResolver->getRepository(ReferralRepository::REPOSITORY_KEY);
        $this->riskGroupPersonHistoryRepository = $this->repositoryResolver->getRepository(RiskGroupPersonHistoryRepository::REPOSITORY_KEY);
        $this->riskGroupRepository = $this->repositoryResolver->getRepository(RiskGroupRepository::REPOSITORY_KEY);
        $this->searchRepository = $this->repositoryResolver->getRepository(SearchRepository::REPOSITORY_KEY);
        $this->studentDbViewLogRepository = $this->repositoryResolver->getRepository(StudentDbViewLogRepository::REPOSITORY_KEY);
    }

    /**
     * Get student activity list with respect to faculty, also return the count of all specific activity
     *
     * @param integer $studentId
     * @param string $category
     * @param boolean $isInteraction
     * @param integer $organizationId
     * @param integer $facultyId
     * @return StudentListHeaderResponseDto
     */
    public function getStudentActivityList($studentId, $category, $isInteraction, $organizationId, $facultyId)
    {
        $this->rbacManager->checkAccessToOrganizationUsingPersonId($studentId);
        //check for non-participant student permissions
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        $this->personRepository->findOneBy([
            'id' => $studentId,
            'organization' => $organizationId
        ], new SynapseValidationException('Not a valid Student Id.'));

        if ($isInteraction == "true") {
            $isInteraction = true;
        } else {
            $isInteraction = false;
        }

        $activityArray = [];
        $noteActivityCount = 0;
        $contactActivityCount = 0;
        $referralActivityCount = 0;
        $appointmentActivityCount = 0;
        $emailActivityCount = 0;

        $activityArray = $this->getActivityArrayFromCategory($category, $studentId, $organizationId, $isInteraction, $facultyId);
        $orgAcademicYearId = null;
        $currentAcademicYear = $this->academicYearService->findCurrentAcademicYearForOrganization($organizationId);
        if (isset($currentAcademicYear['org_academic_year_id'])) {
            $orgAcademicYearId = $currentAcademicYear['org_academic_year_id'];
        }

        $featureIdContacts = $this->featureMasterLangRepository->findOneBy(['featureName' => 'Log Contacts']);
        $contactActivityCount = $this->activityLogRepository->getContactActivityCount($facultyId, $studentId, $featureIdContacts->getId(), $orgAcademicYearId);

        $featureIdNotes = $this->featureMasterLangRepository->findOneBy(['featureName' => 'Notes']);
        $noteActivityCount = $this->activityLogRepository->getNoteActivityCount($facultyId, $studentId, $featureIdNotes->getId(), $orgAcademicYearId);

        $featureIdAppointments = $this->featureMasterLangRepository->findOneBy(['featureName' => 'Booking']);
        $appointmentActivityCount = $this->activityLogRepository->getAppointmentsActivityCount($facultyId, $studentId, $featureIdAppointments->getId(), $orgAcademicYearId);

        $featureIdEmail = $this->featureMasterLangRepository->findOneBy(['featureName' => 'Email']);
        $emailActivityCount = $this->activityLogRepository->getEmailActivityCount($facultyId, $studentId, $featureIdEmail->getId(), $orgAcademicYearId);

        $featureIdReferrals = $this->featureMasterLangRepository->findOneBy(['featureName' => 'Referrals']);
        $referralActivityCount = $this->activityLogRepository->getReferralsActivityCount($facultyId, $studentId, $organizationId, $featureIdReferrals->getId(), $orgAcademicYearId);

        if ($isInteraction) {
            $sharingAccess = $this->activityService->getSharingAccess($facultyId, $studentId);
            $searchKey = 'Activity_Contact_Int_Count';
            $tokenValues = array(
                'studentId' => $studentId,
                'facultyId' => $facultyId,
                'orgId' => $organizationId,
                'publicAccess' => $sharingAccess['Log Contacts']['public_view'],
                'teamAccess' => $sharingAccess['Log Contacts']['team_view']
            );
            $query = $this->getEbiSearchQuery($searchKey, $tokenValues);
            $contactActivityCountArray = $this->searchRepository->getQueryResult($query);
            $contactActivityCount = 0;
            if (count($contactActivityCountArray) > 0) {
                $contactActivityCount = $contactActivityCountArray[0]['cnt'];
            }
        }
        if (!$this->activityService->getPermission($organizationId, $facultyId, 'C', false)) {
            $contactActivityCount = 0;
        }

        $totalCount = $noteActivityCount + $appointmentActivityCount + $contactActivityCount + $referralActivityCount + $emailActivityCount;
        $activityResponse = new StudentListHeaderResponseDto();

        $activityArrayObject = $this->getActivityObjectArray($activityArray);
        $activityResponse->setPersonId($studentId);
        $activityResponse->setTotalActivities($totalCount);
        $activityResponse->setTotalNotes($noteActivityCount);
        $activityResponse->setTotalContacts($contactActivityCount);
        $activityResponse->setTotalAppointments($appointmentActivityCount);
        $activityResponse->setTotalEmail($emailActivityCount);
        $activityResponse->setTotalReferrals($referralActivityCount);
        $activityResponse->setShowInteractionContactType($isInteraction);
        $activityResponse->setActivities($activityArrayObject);

        return $activityResponse;
    }
    
    /**
     * Converting array of activities to array of StudentListArrayResponseDto
     *
     * @param array $activityArray
     * @return StudentListArrayResponseDto[]
     */
    public function getActivityObjectArray($activityArray)
    {
        $activityObjectArray = [];
        foreach ($activityArray as $activity) {
            $activityObject = $this->activityResponseListArr($activity);
            if (isset($activity['related_activities']) && count($activity['related_activities']) > 0) {
                $relatedActivityObjectArray = $this->recForRelatedActivities($activity);
                $activityObject->setRelatedActivities($relatedActivityObjectArray);
            }
            $activityObjectArray[] = $activityObject;
        }
        return $activityObjectArray;
    }

    /**
     * Converting array of relatedActivities to array of StudentListArrayResponseDto
     *
     * @param array $activity
     * @return StudentListArrayResponseDto[]
     */
    public function recForRelatedActivities($activity)
    {
        $relatedActivityObjArr = array();
        foreach ($activity[StudentConstant::RELATED_ACTIVITY] as $relatedActivity) {
            
            $relatedActivityObj = $this->activityResponseListArr($relatedActivity);
            $relatedActivityObj->setRelatedActivityId($relatedActivity[StudentConstant::RELATED_ACTIVITY_ID]);
            
            if (isset($relatedActivity[StudentConstant::RELATED_ACTIVITY]) && count($relatedActivity[StudentConstant::RELATED_ACTIVITY]) > 0) {
                $relatedActivityObjArrSu = $this->recForRelatedActivities($relatedActivity);
                $relatedActivityObj->setRelatedActivities($relatedActivityObjArrSu);
            }
            
            $relatedActivityObjArr[] = $relatedActivityObj;
        }
        return $relatedActivityObjArr;
    }

    /**
     * Converting activity array to StudentListArrayResponseDto
     *
     * @param array $activity
     * @return StudentListArrayResponseDto
     */
    public function activityResponseListArr($activity)
    {
        $activityObjList = new StudentListArrayResponseDto();
        if (! is_object($activity[StudentConstant::ACTIVITY_DATE])) {
            $activity[StudentConstant::ACTIVITY_DATE] = new \DateTime($activity[StudentConstant::ACTIVITY_DATE]);
            $activity[StudentConstant::ACTIVITY_DATE]->setTimezone(new \DateTimeZone('UTC'));
            if($activity[StudentConstant::ACTIVITY_TYPE] == "Contact"){
                $activity[StudentConstant::ACTIVITY_DATE] = $activity[StudentConstant::ACTIVITY_DATE]->format("Y-m-d");
            }else{
                $activity[StudentConstant::ACTIVITY_DATE] = $activity[StudentConstant::ACTIVITY_DATE]->format("Y-m-d\TH:i:sO");
            }
        }else{
            $activity[StudentConstant::ACTIVITY_DATE] = $activity[StudentConstant::ACTIVITY_DATE]->format("Y-m-d\TH:i:sO");
        }
        $activityObjList->setActivityId($activity[StudentConstant::ACTIVITY_ID]);
        $activityObjList->setActivityLogId($activity[StudentConstant::ACTIVITY_LOG_ID]);
        $activityObjList->setActivityDate($activity[StudentConstant::ACTIVITY_DATE]);
        $activityObjList->setActivityType($activity[StudentConstant::ACTIVITY_TYPE]);
        $activityObjList->setActivityCreatedById($activity[StudentConstant::ACTIVITY_CREATED_BY_ID]);
        $activityObjList->setActivityCreatedByFirstName($activity[StudentConstant::ACTIVITY_CREATED_FIRST_NAME]);
        $activityObjList->setActivityCreatedByLastName($activity[StudentConstant::ACTIVITY_CREATED_LAST_NAME]);
        $activityObjList->setActivityReasonId($activity[StudentConstant::ACTIVITY_REASON_ID]);
        $activityObjList->setActivityReasonText($activity[StudentConstant::ACTIVITY_REASON_TEXT]);
        $activityObjList->setActivityDescription($activity[StudentConstant::ACTIVITY_DESCRIPTION]);
        $activityObjList->setActivityReferralStatus($activity[StudentConstant::ACTIVITY_REFERRAL_STATUS]);

        if(isset($activity['email_subject'])){
            $activityObjList->setActivityEmailSubject($activity['email_subject']);
        }
        if(isset($activity['email_body'])){
            $activityObjList->setActivityEmailBody($activity['email_body']);
        }

        if ($activityObjList->getActivityType() == StudentConstant::CONTACT) {
            $activityObjList->setActivityContactTypeId($activity[StudentConstant::ACTIVITY_CONTACT_TYPE_ID]);
            $activityObjList->setActivityContactTypeText($activity[StudentConstant::ACTIVITY_CONTACT_TYPE_TEXT]);
        }
        return $activityObjList;
    }

    /**
     * Get activity details for the student based on activity category
     *
     * @param string $category
     * @param int $studentId
     * @param int $organizationId
     * @param boolean $isInteraction
     * @param int $facultyId
     * @return array $activityData
     * @throws SynapseValidationException
     */
    private function getActivityArrayFromCategory($category, $studentId, $organizationId, $isInteraction, $facultyId)
    {
        switch ($category) {
            case "all":
                $activityData = $this->activityService->getAllActivities($studentId, $organizationId, $isInteraction, $facultyId);
                break;
            case "note":
                $activityData = $this->activityService->getStudentNotes($studentId, $organizationId, $isInteraction, $facultyId);
                break;
            case "appointment":
                $sharingAccess = $this->activityService->getSharingAccess($facultyId, $studentId);
                $sharingViewAccess = $sharingAccess['Booking'];
                $activityData = $this->activityService->getStudentAppointmentList($studentId, $organizationId, $isInteraction, $facultyId, $sharingViewAccess);
                break;
            case "referral":
                $activityData = $this->activityService->getStudentReferralList($studentId, $organizationId, $isInteraction, $facultyId);
                break;
            case "email":
                $activityData = $this->activityService->getStudentEmailList($studentId, $organizationId, $isInteraction, $facultyId);
                break;
            case "Contact":
                $activityData = $this->activityService->getStudentContacts($studentId, $organizationId, $isInteraction, $facultyId);
                break;
            default:
                throw new SynapseValidationException("Not a valid Category.");
                break;
        }
        return $activityData;
    }


    /**
     * Gets student personal information, including survey completion details.
     *
     * @param int $organizationId
     * @param int $studentId
     * @param Person $loggedUser
     * @param int $loggedUserId
     * @param string $dateTimeString
     * @return StudentDetailsResponseDto
     * @throws SynapseValidationException
     * @throws AccessDeniedException
     */
    public function getStudentProfile($organizationId, $studentId, $loggedUser, $loggedUserId, $dateTimeString = 'now')
    {
        $studentObject = $this->personRepository->find($studentId, new SynapseValidationException('The student ID is not valid'));

        $hasPermission = $this->rbacManager->checkAccessToStudent($studentId, $loggedUserId);
        if (!$hasPermission) {
            throw new AccessDeniedException('You do not have permission to view this student');
        }

        $personExternalId = $studentObject->getExternalId();
        $organization = $studentObject->getOrganization();
        $personContactInfoArray = $studentObject->getContacts();

        $studentDetailsResponseDto = new StudentDetailsResponseDto();

        if (count($personContactInfoArray)) {
            $studentDetailsResponseDto->setMobileNumber($personContactInfoArray[0]->getPrimaryMobile());
            $studentDetailsResponseDto->setPhoneNumber($personContactInfoArray[0]->getHomePhone());
        }

        $studentDetailsResponseDto->setId($studentId);
        $studentDetailsResponseDto->setStudentExternalId($personExternalId);
        $studentDetailsResponseDto->setStudentFirstName($studentObject->getFirstname());
        $studentDetailsResponseDto->setStudentLastName($studentObject->getLastname());
        $studentDetailsResponseDto->setAuthUsername($studentObject->getAuthUsername());
        $studentDetailsResponseDto->setPrimaryEmail($studentObject->getUsername());

        $riskAndIntentToLeaveArray = $this->orgPermissionService->getRiskIndicatorForStudent($studentId, $organization, $loggedUserId);

        $riskLevel = $studentObject->getRiskLevel();
        if (isset($riskAndIntentToLeaveArray['risk_indicator']) && $riskAndIntentToLeaveArray['risk_indicator']) {
            if (isset($riskLevel)) {
                $studentDetailsResponseDto->setStudentRiskStatus($riskLevel->getRiskText());
            } else {
                $studentDetailsResponseDto->setStudentRiskStatus('gray');
            }
            $studentDetailsResponseDto->setRiskUpdatedDate($studentObject->getRiskUpdateDate());
        }

        $intentToLeave = $studentObject->getIntentToLeave();
        if (isset($riskAndIntentToLeaveArray['intent_to_leave']) && $riskAndIntentToLeaveArray['intent_to_leave']) {
            if (isset($intentToLeave)) {
                $studentDetailsResponseDto->setStudentIntentToLeave($intentToLeave->getText());
            } else {
                $studentDetailsResponseDto->setStudentIntentToLeave('dark gray');
            }
        }

        $lastViewed = $todayViewed = new \DateTime($dateTimeString);

        $lastViewLogObject = $this->studentDbViewLogRepository->findOneBy(array(
            'personIdStudent' => $studentObject,
            'personIdFaculty' => $loggedUser,
            'organization' => $organization
        ), array(
            'id' => 'DESC'
        ));

        if (!is_object($lastViewLogObject)) {
            $studentDbViewLogObject = new StudentDbViewLog();
            $studentDbViewLogObject->setOrganization($organization);
            $studentDbViewLogObject->setPersonIdFaculty($loggedUser);
            $studentDbViewLogObject->setPersonIdStudent($studentObject);
            $studentDbViewLogObject->setLastViewedOn($todayViewed);
            $this->studentDbViewLogRepository->createStudentDbViewLog($studentDbViewLogObject);
        } else {
            $lastViewed = $lastViewLogObject->getLastViewedOn();
            $studentDetailsResponseDto->setLastViewedDate($lastViewed);
            $lastViewLogObject->setLastViewedOn($todayViewed);
        }
        $this->studentDbViewLogRepository->flush();

        $studentDetailsResponseDto->setLastViewedDate($lastViewed);
        $orgPersonStudent = $this->orgPersonStudentRepository->findOneBy(['person' => $studentObject], new SynapseValidationException('Person is not a Student'));
        $currentAcademicYear = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);
        $personYearObj = $this->orgPersonStudentYearRepository->findOneBy(['person' => $studentObject, 'orgAcademicYear' => $currentAcademicYear]);
        $studentStatus = $personYearObj->getIsActive();
        $studentDetailsResponseDto->setStudentStatus((int)$studentStatus);
        $studentDetailsResponseDto->setPhotoUrl($orgPersonStudent->getPhotoUrl());


        // Find and set data about the student's most recent survey.
        $studentSurveyArray = $this->orgPersonStudentSurveyLinkRepository->listSurveysForStudent($studentId, $organizationId);
        $assessmentBanner = [];
        if (count($studentSurveyArray) > 0) {
            $mostRecentSurvey = $studentSurveyArray[0];
            $assessmentBanner['survey_name'] = $mostRecentSurvey['survey_name'];

            if (($mostRecentSurvey['survey_completion_status'] == 'Assigned' || is_null($mostRecentSurvey['survey_completion_status']))) {
                $assessmentBanner['survey_completion_status'] = 'NOT RESPONDED';
            } else {
                $assessmentBanner['survey_completion_status'] = 'RESPONDED';
            }

            $openDate = new \DateTime($mostRecentSurvey['open_date']);
            $assessmentBanner['survey_launch_date'] = $openDate;

            $closeDate = new \DateTime($mostRecentSurvey['close_date']);
            $assessmentBanner['survey_close_date'] = $closeDate;
        }
        $studentDetailsResponseDto->setAssessmentBanner($assessmentBanner);
        return $studentDetailsResponseDto;
    }

    /**
     * List referral details for the student
     *
     * @param int $loggedInUserId
     * @param int $studentId
     * @return StudentReferralsDto $studentReferrals
     * @throws AccessDeniedException
     */
    public function listReferrals($loggedInUserId, $studentId)
    {
        //check for non-participant student permissions
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        $sharingAccess = $this->activityService->getSharingAccess($loggedInUserId, $studentId);
        $sharingViewAccess = $sharingAccess['Referrals'];
        $sharingViewAccessReferralsReasonRouted = $sharingAccess[StudentConstant::REFERRALS_REASON_ROUTED];

        $loggedInUser = $this->personRepository->findOneById($loggedInUserId);
        $organizationId = $loggedInUser->getOrganization()->getId();
        //get organization language.
        $organizationLanguage = $this->orgService->getOrganizationDetailsLang($organizationId);
        $organizationLanguageLangId = $organizationLanguage->getLang()->getId();

        $referrals = $this->referralRepository->getFacultyStudentReferral($loggedInUserId, $studentId, $organizationId, $sharingViewAccess, $sharingViewAccessReferralsReasonRouted);
        $totalReferrals = count($referrals);

        // get Open referral
        $openReferrals = $this->referralRepository->getFacultyStudentReferral($loggedInUserId, $studentId, $organizationId, $sharingViewAccess, $sharingViewAccessReferralsReasonRouted, 'O');
        $totalOpenReferrals = count($openReferrals);
        //if logged in user and primary coordinator is same then bring yellow pop up.
        $isPrimaryCoordinator = false;
        $primaryCoordinators = $this->personService->getAllPrimaryCoordinators($organizationId, $organizationLanguageLangId, ReferralConstant::PRIMARYCOORDINATOR);
        if ($primaryCoordinators) {
            $primaryCoordinator = $primaryCoordinators[0];
            $primaryCoordinatorId = $primaryCoordinator->getPerson()->getId();
            if ($primaryCoordinatorId == $loggedInUserId) {
                $isPrimaryCoordinator = true;
            }
        }
        $openReferralsForMe = $this->referralRepository->getOpenReferral($loggedInUserId, $studentId, $isPrimaryCoordinator);

        $totalOpenReferralsForMe = count($openReferralsForMe);

        $studentReferrals = new StudentReferralsDto();
        $studentReferrals->setPersonStudentId($studentId);
        $studentReferrals->setPersonStaffId($loggedInUserId);
        $studentReferrals->setTotalReferralsCount($totalReferrals);
        $studentReferrals->setTotalOpenReferralsCount($totalOpenReferrals);
        $studentReferrals->setTotalOpenReferralsAssignedToMe($totalOpenReferralsForMe);

        $myOpenReferrals = array();
        if (!empty($openReferralsForMe)) {

            $organizationDetailsLanguage = $this->orgService->getOrganizationDetailsLang($organizationId);
            $organizationLanguage = $organizationDetailsLanguage->getLang();

            foreach ($openReferralsForMe as $referral) {
                $openReferralsDto = new StudentOpenReferralsDto();
                $openReferralsDto->setReferralId($referral['id']);
                $activityCategoryName = $this->activityCategoryLangRepository->findOneBy(array('activityCategoryId' => $referral['activity_category_id'], 'language' => $organizationLanguage));
                $openReferralsDto->setReasonCategorySubitemId($referral['activity_category_id']);
                $openReferralsDto->setReasonCategorySubitem($activityCategoryName->getDescription());
                $openReferralsDto->setComment($referral['note']);
                $myOpenReferrals[] = $openReferralsDto;
            }
            $studentReferrals->setReferrals($myOpenReferrals);
        }
        return $studentReferrals;
    }

    /**
     * Get student contacts details
     *
     * @param int $loggedInUserId
     * @param int $studentId
     * @param int $organizationId
     * @return StudentContactsDto $studentContactsDto
     * @throws AccessDeniedException|ValidationException
     */
    public function studentContacts($loggedInUserId, $studentId, $organizationId)
    {
        //check for non-participant student permissions
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        $this->activityService->getPermission($organizationId, $loggedInUserId, 'C');
        $sharingAccess = $this->activityService->getSharingAccess($loggedInUserId, $studentId);

        //primary coordinator role
        $coordinatorRolesIds = "1";

        $searchKey = 'Activity_Count';
        $activity = '"C"';
        $tokenValues = array(
            'studentId' => $studentId,
            'acivityArr' => $activity,
            'faculty' => $loggedInUserId,
            'orgId' => $organizationId,
            'noteTeamAccess' => $sharingAccess['Notes']['team_view'],
            'notePublicAccess' => $sharingAccess['Notes']['public_view'],
            'contactTeamAccess' => $sharingAccess['Log Contacts']['team_view'],
            'contactPublicAccess' => $sharingAccess['Log Contacts']['public_view'],
            'referralTeamAccess' => $sharingAccess['Referrals']['team_view'],
            'referralPublicAccess' => $sharingAccess['Referrals']['public_view'],
            'referralPublicAccessReasonRouted' => $sharingAccess[StudentConstant::REFERRALS_REASON_ROUTED]['public_view'],
            'referralTeamAccessReasonRouted' => $sharingAccess[StudentConstant::REFERRALS_REASON_ROUTED]['team_view'],
            'appointmentTeamAccess' => $sharingAccess['Booking']['team_view'],
            'appointmentPublicAccess' => $sharingAccess['Booking']['public_view'],
            'emailTeamAccess' => $sharingAccess['Email']['team_view'],
            'emailPublicAccess' => $sharingAccess['Email']['public_view'],
            'roleIds' => $coordinatorRolesIds
        );
        $query = $this->getEbiSearchQuery($searchKey, $tokenValues);

        $activityCount = $this->searchRepository->getQueryResult($query);
        $activityArray['C'] = 0;
        foreach ($activityCount as $count) {
            if ($count['activity_type'] == 'C') {
                $activityArray[$count['activity_type']] = $activityArray[$count['activity_type']] + 1;
            }
        }
        $studentContactsDto = new StudentContactsDto();
        $studentContactsDto->setPersonStudentId($studentId);
        $studentContactsDto->setPersonStaffId($loggedInUserId);
        $studentContactsDto->setTotalContacts($activityArray['C']);

        return $studentContactsDto;
    }

    /**
     *  Get student open appointments details
     *
     * @param int $studentId
     * @param int $loggedInPersonId
     * @param int $organizationId
     * @return StudentOpenAppResponseDto $studentOpenAppointment
     * @throws SynapseValidationException|AccessDeniedException
     */
    public function getStudentsOpenAppointments($studentId, $loggedInPersonId, $organizationId)
    {
        //check for non-participant student permissions
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        $studentOpenAppointment = new StudentOpenAppResponseDto();

        $fromDate = new \DateTime('-1 week');
        $toDate = new \DateTime('+ 1 day');
        $fromDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
        $toDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
        $currentDate = new \DateTime();
        $currentDateTime = $currentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);

        $this->personRepository->findOneBy([
            'id' => $studentId,
            'organization' => $organizationId
        ], new SynapseValidationException("Not a valid Student."));

        $studentOpenAppointment->setPersonStudentId($studentId);
        $studentOpenAppointment->setPersonStaffId($loggedInPersonId);

        $sharingAccess = $this->activityService->getSharingAccess($loggedInPersonId, $studentId);
        $sharingViewAccess = $sharingAccess['Booking'];

        $totalUpcomingAppointments = $this->appointmentsRecipientAndStatusRepository->getTotalUpcomingAppointments($studentId, $organizationId, $currentDateTime, $loggedInPersonId, $sharingViewAccess);
        $totalAppointments = $this->appointmentsRecipientAndStatusRepository->getTotalAppointments($studentId, $organizationId, $fromDate, $toDate);

        $studentOpenAppointment->setTotalAppointments($totalUpcomingAppointments);

        $appointmentByLoggedInUser = 0;
        $todayAppointmentByLoggedInUser = 0;
        $appointments = array();
        foreach ($totalAppointments as $appointmentDetails) {
            $appointment = $this->appointmentsRecipientAndStatusRepository->findOneBy(array('appointments' => $appointmentDetails['id'], 'personIdStudent' => $studentId));
            if ($appointment->getPersonIdFaculty()->getId() == $loggedInPersonId) {
                $appointmentByLoggedInUser++;
                $appointmentStartDate = $appointmentDetails['startDate'];
                if (($appointmentStartDate >= $currentDate)) {
                    $todayAppointmentByLoggedInUser++;
                    $appointmentsList = new AppointmentListArrayResponseDto();
                    $appointmentsList->setAppointmentId($appointmentDetails['id']);
                    $appointmentsList->setStartDate($appointmentDetails['startDate']);
                    $appointmentsList->setEndDate($appointmentDetails['endDate']);
                    $appointments[] = $appointmentsList;
                }
            }
        }
        $studentOpenAppointment->setTotalAppointmentsByMe($appointmentByLoggedInUser);
        $studentOpenAppointment->setTotalSameDayAppointmentsByMe($todayAppointmentByLoggedInUser);
        $studentOpenAppointment->setAppointments($appointments);
        return $studentOpenAppointment;
    }

    /**
     * Get students talking points
     *
     * @param int $studentId
     * @param int $loggedInPersonId
     * @param int $orgId
     * @param string $debug
     * @return StudentTalkingPointsDto
     * @throws ValidationException
     */
    public function getTalkingPoints($studentId, $loggedInPersonId, $orgId ,$debug = "false")
    {
        //check for non-participant student permissions
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);
	    // Temporary hack to hide talking points for specific insitutions
        try{
            $configVal  = $this->ebiConfigService->get('Disabled_TP_Orgs');
            if($configVal != ""){
                $disabledOrgs = explode(',', $configVal);
            }else{
                $disabledOrgs =  array();
            }
        } catch (\Exception $e) {
            $disabledOrgs = [];
        }

        if (in_array($orgId, $disabledOrgs)) {
            $studentTalkingPointsDto = new StudentTalkingPointsDto();
            return $studentTalkingPointsDto;
        }
        // End temporary hack to hide talking points for specific institutions

        $this->rbacManager->checkAccessToOrganizationUsingPersonId($studentId);
        $this->personRepository = $this->repositoryResolver->getRepository(StudentConstant::PERSON_REPO);
        $this->orgTalkingPointsRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgTalkingPoints");
        $isValidStudent = $this->personRepository->findOneBy(array(
            'id' => $studentId,
            StudentConstant::ORGANIZATION => $orgId
        ));
        if (! $isValidStudent) {
            throw new ValidationException([
                StudentConstant::INVALID_STUDENT
            ], StudentConstant::INVALID_STUDENT, StudentConstant::INVALID_STUDENT_KEY);
        }
        $organizationLang = $this->orgService->getOrganizationDetailsLang($orgId);
        $orgLangId = $organizationLang->getLang()->getId();
        
        $surveyBlockArr = array();
        $profileBlockArr =  array();
        
        // get permissible survey and profile blocks w.r.t student and logged in faculty
        $permissionIds = $this->orgPermissionService->getPermissionSetIds($studentId, $loggedInPersonId);
        $permissionIds = (count($permissionIds)>0)?$permissionIds:array(-1);
        $surveyBlockRS = $this->orgPermissionsetDatablockRepository->getAllSurveyblockIdByPermissions($permissionIds);
        $profileBlockRS = $this->orgPermissionsetDatablockRepository->getAllblockIdByPermissions($permissionIds);
        $surveyBlockArr = array_column($surveyBlockRS, 'block_id');
        $profileBlockArr = array_column($profileBlockRS, 'block_id');
        
        if(count($surveyBlockArr) == 0){
            $surveyBlockArr = array(-1);
        }
        if(count($profileBlockArr) ==  0){
            $profileBlockArr = array(-1);
        }

        $profileTalkingPoints = $this->orgTalkingPointsRepository->getOrgTalkingPointsBasedOnProfileItems($studentId, $orgLangId, $profileBlockArr);
        $surveyTalkingPoints = $this->orgTalkingPointsRepository->getOrgTalkingPointsBasedOnSurveyQuestions($studentId, $orgLangId, $surveyBlockArr);
        $talkingPoints = array_merge($profileTalkingPoints, $surveyTalkingPoints);

        // Sort talking points by date, in descending order.
        usort($talkingPoints, function($a, $b) {
            if ($a['sourceModifiedAt'] == $b['sourceModifiedAt']) {
                return 0;
            }
            return ($a['sourceModifiedAt'] < $b['sourceModifiedAt']) ? 1 : -1;
        });

        $studentTalkingPointsDto = new StudentTalkingPointsDto();
        if (count($talkingPoints) > 0)
        {
            $studentTalkingPointsDto->setPersonStudentId($studentId);
            $studentTalkingPointsDto->setPersonStaffId($loggedInPersonId);
            $studentTalkingPointsDto->setOrganizationId($orgId);
            $strength = [];
            $weakness = [];
            $sCount = 0;
            $wCount = 0;

            foreach ($talkingPoints as $talkingPoint) {
                $talkingDto = new TalkingPointsDto();
                $talkingDto->setTalkingPointId($talkingPoint['talkingPointsId']);
                $talkingDto->setTalkingPointDate(Helper::getUtcDate($talkingPoint['sourceModifiedAt']));
                $talkingDto->setDescription($talkingPoint['description']);

                $title = trim($talkingPoint['title']);
                if (!empty($title)) {
                    if ($talkingPoint['source'] == 'P') {
                        if ($talkingPoint['scope'] == 'T' && !empty($talkingPoint['orgAcademicTerm'])) {
                            $title = $title . ': ' . $talkingPoint['orgAcademicTerm'];
                            if (!empty($talkingPoint['orgAcademicYear'])) {
                                $title = $title . ', ' . $talkingPoint['orgAcademicYear'];
                            }
                        } elseif ($talkingPoint['scope'] == 'Y' && !empty($talkingPoint['orgAcademicYear'])) {
                            $title = $title . ': ' . $talkingPoint['orgAcademicYear'];
                        }
                    } elseif ($talkingPoint['source'] == 'S') {
                        $title = $talkingPoint['surveyName'] . ': ' . $title;
                    }
                }
                $talkingDto->setTitle($title);

                if ($talkingPoint['talkingPointsType'] == "S") {
                    $strength[] = $talkingDto;
                    $sCount += 1;
                }
                if ($talkingPoint['talkingPointsType'] == "W") {
                    $weakness[] = $talkingDto;
                    $wCount += 1;
                }
            }

            $studentTalkingPointsDto->setTalkingPointsWeaknessCount($wCount);
            $studentTalkingPointsDto->setTalkingPointsStrengthsCount($sCount);
            $studentTalkingPointsDto->setWeakness($weakness);
            $studentTalkingPointsDto->setStrength($strength);
        }
        return $studentTalkingPointsDto;
    }

    /**
     * Get student profile information and ISP information.
     *
     * @param int $studentId
     * @param Person $loggedInPerson
     * @return StudentProfileResponseDto
     * @throws AccessDeniedException
     */
    public function getStudentDetails($studentId, $loggedInPerson)
    {
        //Check if student is in organization
        $this->rbacManager->checkAccessToOrganizationUsingPersonId($studentId);

        //check for non-participant student permissions
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        //Get org and person ids of logged in user
        $userPersonId = $loggedInPerson->getId();

        //Get permissionset Ids (In a nested array form) for faculty-student relationship
        $permissionSetsForFacultyAndStudent = $this->orgGroupFacultyRepository->getPermissionsByFacultyStudent($userPersonId, $studentId);

        $dataBlocks = [];
        $allowedISPIds = [];
        $studentProfileItems = [];
        $orgStudentProfileItems = [];
        //If there are permission sets, dataBlocks, and accessible ISPs, implode those lists for later use.
        if (count($permissionSetsForFacultyAndStudent) > 0) {
            $permissionSetIdsForFacultyAndStudents = array_column($permissionSetsForFacultyAndStudent, 'org_permissionset_id');
            $dataBlocks = $this->orgPermissionsetDatablockRepository->getAllblockIdByPermissions($permissionSetIdsForFacultyAndStudents);
            $allowedISPIds = $this->orgPermissionsetMetadataRepository->getAllMetadataIdByPermissions($permissionSetIdsForFacultyAndStudents);
        }

        //If the user has permissions to profile data, get that profile data.
        if (count($dataBlocks) > 0) {
            $accessibleDataBlockIdsForUser = array_column($dataBlocks, 'block_id');
            $studentProfileItems = $this->personEbiMetadataRepository->getStudentProfileInformation($studentId, $accessibleDataBlockIdsForUser);
        }

        if (count($allowedISPIds) > 0) {
            $accessibleISPIdsForUser = array_column($allowedISPIds, 'profile_id');
            $orgStudentProfileItems = $this->personOrgMetadataRepository->getStudentISPProfileInformation($studentId, $accessibleISPIdsForUser);
        }

        $studentProfileResponseDTO = new StudentProfileResponseDto();
        $studentProfileResponseDTO->setPersonStaffId($userPersonId);
        $studentProfileResponseDTO->setPersonStudentId($studentId);

        //Format the ISP return data into the proper array structure
        $studentProfileData = [];
        if (count($orgStudentProfileItems)) {
            $orgProfileItemsArray = [];
            $orgProfileItemsArray['block_name'] = "isp";
            foreach ($orgStudentProfileItems as $orgStudentProfileItem) {
                if ($orgStudentProfileItem['metadata_type'] == 'S') {
                    $listName = $this->orgMetadataListValuesRepository->getListValues($orgStudentProfileItem['org_metadata_id'], $orgStudentProfileItem['metadata_value']);
                    $orgProfileItemValue = $listName[0]['listName'];
                } else if ($orgStudentProfileItem['metadata_type'] == 'D') {
                    $dateValues = explode(' ', $orgStudentProfileItem['metadata_value']);
                    $orgProfileItemValue = ($dateValues[0]) ? $dateValues[0] : $orgStudentProfileItem['metadata_value'];
                } else {
                    $orgProfileItemValue = $orgStudentProfileItem['metadata_value'];
                }

                $orgProfileItemsArray['items'][] = [
                    "year_name" => $orgStudentProfileItem['year_name'],
                    "term_name" => $orgStudentProfileItem['term_name'],
                    "name" => $orgStudentProfileItem['meta_name'],
                    "value" => $orgProfileItemValue
                ];
            }
            $studentProfileData[] = $orgProfileItemsArray;
        }

        if (count($studentProfileItems) > 0) {
            //Get the descriptions of each different profile block applicable to the student
            $profileDataBlocksApplicableToStudent = array_unique(array_column($studentProfileItems, 'datablock_desc'));

            foreach ($profileDataBlocksApplicableToStudent as $profileDataBlockName) {
                //Get all profile items the student has that are in that profile block
                $profileItemsInDataBlock = $this->getProfileItemsInDataBlock($studentProfileItems, $profileDataBlockName);
                $profileItemsArray = [];
                if (count($profileItemsInDataBlock) > 0) {
                    $profileItemsArray['block_name'] = $profileDataBlockName;
                    foreach ($profileItemsInDataBlock as $profileItemInDataBlock) {
                        if ($profileItemInDataBlock['metadata_type'] == 'S') {
                            $listName = $this->ebiMetadataListValuesRepository->getListValues($profileItemInDataBlock['ebi_metadata_id'], $profileItemInDataBlock['metadata_value']);
                            $profileItemValue = $listName[0]['listName'];
                        } else if ($profileItemInDataBlock['metadata_type'] == 'D') {
                            $dateValues = explode(' ', $profileItemInDataBlock['metadata_value']);
                            $profileItemValue = ($dateValues[0]) ? $dateValues[0] : $profileItemInDataBlock['metadata_value'];
                        } else {
                            $profileItemValue = $profileItemInDataBlock['metadata_value'];
                        }

                        $profileItemsArray['items'][] = [
                            "year_name" => $profileItemInDataBlock['year_name'],
                            "term_name" => $profileItemInDataBlock['term_name'],
                            "name" => $profileItemInDataBlock['meta_name'],
                            "value" => $profileItemValue
                        ];
                    }
                }
                $studentProfileData[] = $profileItemsArray;
            }
        }
        $studentProfileResponseDTO->setProfile($studentProfileData);
        return $studentProfileResponseDTO;
    }

    /**
     * sets profile item to datablocks
     *
     * @param array $profileItems
     * @param string $name
     * @return array
     */
    private function getProfileItemsInDataBlock($profileItems, $name)
    {
        $dataBlockArray = [];
        if (count($profileItems) > 0) {
            foreach ($profileItems as $profileItem) {
                
                if ($profileItem['datablock_desc'] == $name) {
                    $dataBlockArray[] = $profileItem;
                }
            }
        }
        return $dataBlockArray;
    }

    private function getEbiSearchQuery($searchKey, $tokenValues)
    {
        $searchRepository = $this->repositoryResolver->getRepository(StudentConstant::EBI_SEARCH_REPO);
        $query_by_key = $searchRepository->findOneByQueryKey($searchKey);
        if ($query_by_key) {
            $returnQuery = $query_by_key->getQuery();
        }
        $returnQuery = Helper::generateQuery($returnQuery, $tokenValues);
        return $returnQuery;
    }

    /**
     * Get group list of student associated
     *
     * @param int $organizationId
     * @param int $studentId
     * @param boolean $isParticipantCheckRequired - indicates whether participation check required or not.
     * @throws SynapseValidationException|AccessDeniedException
     * @return StudentGroupsListDto
     */
    public function getStudentGroupsList($organizationId, $studentId, $isParticipantCheckRequired = true)
    {
        $this->rbacManager->checkAccessToOrganizationUsingPersonId($studentId);
        //check for non-participant student permissions
        if ($isParticipantCheckRequired) {
            // This check may not be required while trying to get the data for non-participant students
            $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);
        }

        $organization = $this->organizationRepository->find($organizationId);
        if (!$organization) {
            throw new SynapseValidationException("Organization not found.");
        }

        $orgPersonStudent = $this->orgPersonStudentRepository->findOneBy(array(
            'organization' => $organization,
            'person' => $studentId
        ));
        if (!$orgPersonStudent) {
            throw new SynapseValidationException("Student not found.");
        }
        $checkCoordinator = $this->rbacManager->checkIfCoordinator();

        $accessMap = $this->rbacManager->getAccessMap();
        $groups = $accessMap['groups'];
        $studentGroups = $this->orgGroupStudentsRepository->getStudentGroupsDetails($studentId, $organizationId);

        // Hide groups from students (ESPRJ-5191), but show to coordinators (ESPRJ-9241).
        if (!$checkCoordinator) {
            foreach ($studentGroups as $key => $group) {
                if (!array_key_exists($group['group_id'], $groups)) {
                    unset($studentGroups[$key]);
                } else {
                    continue;
                }
            }
        }

        $groupList = $this->getStudentGroupsResponse($studentGroups);
        $studentGroupList = new StudentGroupsListDto();
        $studentGroupList->setStudentId($studentId);
        $studentGroupList->setOrganizationId($organizationId);
        $studentGroupList->setGroups($groupList);
        return $studentGroupList;
    }

    private function getStudentGroupsResponse($studentGroups)
    {
        $groupArr = array();
        foreach ($studentGroups as $groups) {
            $groupDetails = new OrgGroupDto();
            $groupDetails->setGroupId($groups['group_id']);
            $groupDetails->setGroupName($groups['group_name']);
            $groupArr[] = $groupDetails;
        }
        
        return $groupArr;
    }

    /**
     * Soft deletes a student. It does not actually remove any records.
     * @param int $personId
     * @return bool
     */
    public function softDeleteById($personId)
    {
        /** @var OrgPersonStudentRepository */
        $orgPersonStudentRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_STUDENT);

        try {
            $orgStudent = $orgPersonStudentRepository->findOneBy([
                'person' => $personId
            ]);
            $orgPersonStudentRepository->remove($orgStudent);
            $status = true;
        } catch (\Exception $e) {
            $status = false;
        }

        return $status;
    }

    /**
     * @param int $studentId
     * @param int $groupId
     * @return bool
     */
    public function addGroup($studentId, $groupId)
    {
        /** @var OrgGroupStudentsRepository $repo */
        $repo = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupStudents');
        $repo->addStudentGroupAssoc($studentId, $groupId);
        return true;
    }

    /**
     * Removes the student from all the descendant groups for the group id provided
     * @param int $studentId
     * @param int $groupId
     * @return bool
     */
    public function removeGroup($studentId, $groupId)
    {
        // finds all the child groups for provided groupid
        $groups = $this->orgGroupTreeRepository->findAllDescendantGroups($groupId);
       
        if(count($groups) == 1 && $groups[0]['external_id'] == $this->allStudentGroupExternalID){
            throw new ValidationException([
                "Student cannot be removed from ALLSTUDENTS group"
            ]);
        }
        //  removing the students from all the child groups
        foreach($groups as $group){
            if($group['external_id'] != $this->allStudentGroupExternalID){
                $this->orgGroupStudentsRepository ->removeStudentGroupAssoc($studentId, $group['group_id']);
            }
        }
        return true;
    }

    /**
     * Add a student ($studentId) to a course ($courseId). Returns true on success.
     * 
     * @param int $studentId
     * @param int $courseId
     * @throws ValidationException
     * @return bool
     */
    public function addCourse($studentId, $courseId)
    {
        $person = $this->personService->findPerson($studentId);
        $organizationId = $person->getOrganization()->getId();
        // Added this to check Student in course.
        $this->courseFacultyStudentValidatorService->validateAdditionOfStudentToCourse($studentId, $organizationId, $courseId);
        $this->orgCourseStudentRepository->addStudentCourseAssoc($studentId, $organizationId, $courseId);
        return true;
    }

    /**
     * @param int $studentId
     * @param int $courseId
     * @return bool
     */
    public function removeCourse($studentId, $courseId)
    {
        /** @var OrgCourseStudentRepository $repo */
        $repo = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgCourseStudent');
        $repo->removeStudentCourseAssoc($studentId, $courseId);
        return true;
    }

    public function updatePolicy(StudentPolicyDto $studentPolicyDto)
	{		
		$this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_STUDENT);
		$this->logger->info('Update Student Privacy Policy');
		$studentId = $studentPolicyDto->getStudentId();		
		$organizationId = $studentPolicyDto->getOrganizationId();		
		$privacyPolicy = $studentPolicyDto->getIsPrivacyPolicyAccepted();				
		$student = $this->orgPersonStudentRepository->findBy(array(
            PersonConstant::FIELD_ORGANIZATION => $organizationId,
            PersonConstant::FIELD_PERSON => $studentId
        ));
		$orgRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Organization');
		$organization = $orgRepository->find($organizationId);		
		$timezone = $organization->getTimezone();
        $timezone = $this->repositoryResolver->getRepository('SynapseCoreBundle:MetadataListValues')->findByListName($timezone);
        if ($timezone) {
            $timezone = $timezone[0]->getListValue();
        }		
        if (empty($student)) {
            throw new ValidationException([
                PersonConstant::ERROR_PERSON_NOT_FOUND
            ], PersonConstant::ERROR_PERSON_NOT_FOUND, PersonConstant::ERROR_PERSON_NOT_FOUND_KEY);
        }		
		try {
            $orgStudent = $this->orgPersonStudentRepository->findOneBy([
                'person' => $studentId,
				'organization' => $organizationId
            ]);
			$currentDate = new \DateTime('now');
			$date = Helper::getUtcDate($currentDate, $timezone);
			$orgStudent->setIsPrivacyPolicyAccepted($privacyPolicy);           
			$orgStudent->setPrivacyPolicyAcceptedDate($date);
            $this->orgPersonStudentRepository->flush();
        } catch (\Exception $e) { 
			$this->logger->error('Issue in Update Student Privacy Policy');
            throw new ValidationException([
                StudentConstant::QUERY_ERROR
                ], StudentConstant::QUERY_ERROR, StudentConstant::QUERY_ERROR);
        }
        $this->logger->info('Update Student Privacy Policy is completed - student ID -'.$studentId. 'Organization ID - '. $organizationId );
	}

    
    public function manageStudentGroupMembership($studentId, $groupIds){
        
        $status = false;
        
        foreach ($groupIds as $groupData) {
        
        	$action = $groupData['action'];
        	$groupId = $groupData['group_id'];
        
        	if ($action == "add") {
        		$status = $this->addGroup($studentId,$groupId);
        	}
        	
        	if ($action == "delete") {
        		$status = $this->removeGroup($studentId, $groupId);
        	}
        }
        return $status;
    }
    
    public function manageStudentCourseMembership($studentId, $courseIds){
        
        $status = false;
        foreach ($courseIds as $courseData) {
        
        	$action = $courseData['action'];
        	$courseId = $courseData['course_id'];
        
        	if ($action == "add") {
        		$status = $this->addCourse($studentId, $courseId);
        	}
        	 
        	if ($action == "delete") {
        		$status = $this->removeCourse($studentId, $courseId);
        	}
        }
        return $status;
        
    }


    /**
     * @param Person $personObject
     * @param PersonDTO $personDTO
     * @param Organization $organization
     * @param Person $loggedInUser
     * @param DataProcessingExceptionHandler $dataProcessingExceptionHandler
     * @return DataProcessingExceptionHandler
     */
    public function determineStudentUpdateType($personObject, $personDTO, $organization, $loggedInUser, $dataProcessingExceptionHandler)
    {
        // Getting person student object with person object
        $orgPersonStudent = $this->orgPersonStudentRepository->findOneBy([
            'person' => $personObject,
            'organization' => $personObject->getOrganization()
        ]);

        // Create|Update|Remove Student
        $actionStudent = null;
        if ($orgPersonStudent && (is_null($personDTO->getIsStudent()) || $personDTO->getIsStudent() == true)) {
            $actionStudent = 'update_student';
        } else if ($orgPersonStudent && !$personDTO->getIsStudent()) {
            $actionStudent = 'remove_student';
        } else if (!$orgPersonStudent && $personDTO->getIsStudent()) {
            $actionStudent = 'create_student';
        }

        switch ($actionStudent) {
            case "update_student":
                $dataProcessingExceptionHandler = $this->updateStudent($orgPersonStudent, $personDTO, $dataProcessingExceptionHandler);
                break;
            case 'remove_student':
                $this->removeStudent($orgPersonStudent, $loggedInUser);
                break;
            case 'create_student':
                $dataProcessingExceptionHandler = $this->createStudent($personDTO, $personObject, $loggedInUser, $organization, $dataProcessingExceptionHandler);
                break;
        }

        return $dataProcessingExceptionHandler;
    }


    /**
     * Update student with all validation and returns error object if any
     *
     * @param OrgPersonStudent $orgPersonStudent
     * @param PersonDTO $personDTO
     * @param DataProcessingExceptionHandler $dataProcessingExceptionHandler
     * @return DataProcessingExceptionHandler
     */
    public function updateStudent($orgPersonStudent, $personDTO, $dataProcessingExceptionHandler)
    {
        // Validating PhotoURL
        if (!empty($personDTO->getPhotoLink())) {
            $studentPhotoLink = $this->modifyStudentPhotoLink($personDTO->getPhotoLink(), $orgPersonStudent);
            if (is_array($studentPhotoLink)) {
                $dataProcessingExceptionHandler->addErrors($studentPhotoLink['photo_link'], "photo_link");
            }
        }

        // Validating PrimaryCampusConnectionId
        if (!empty($personDTO->getPrimaryCampusConnectionId())) {
            $primaryCampusConnection = $this->modifyStudentPrimaryCampusConnection($personDTO->getPrimaryCampusConnectionId(), $orgPersonStudent);
            if (is_array($primaryCampusConnection)) {
                $dataProcessingExceptionHandler->addErrors($primaryCampusConnection['primary_campus_connection_id'], "primary_campus_connection_id");
            }
        }

        // Validating RiskGroupId
        if (!empty($personDTO->getRiskGroupId())) {
            $riskGroup = $this->modifyStudentRiskGroup($personDTO->getRiskGroupId(), $orgPersonStudent);
            if (is_array($riskGroup)) {
                $dataProcessingExceptionHandler->addErrors($riskGroup['risk_group_id'], "risk_group_id");
            }
        }

        $this->entityValidationService->nullifyFieldsToBeCleared($orgPersonStudent, $personDTO->getFieldsToClear(), $this->mapPersonDtoFieldToDB);

        //persisting and unset student object
        $orgPersonStudent = $this->orgPersonStudentRepository->persist($orgPersonStudent);
        unset($orgPersonStudent);
        return $dataProcessingExceptionHandler;
    }


    /**
     * Remove student user
     *
     * @param OrgPersonStudent $orgPersonStudent
     * @param Person $loggedInUser
     * @return boolean
     */
    public function removeStudent($orgPersonStudent, $loggedInUser)
    {
        $currentDate = new \DateTime();

        // Remove an existing student, if OrgPersonStudent exists and is_student is false.
        $orgPersonStudent->setDeletedAt($currentDate);
        $orgPersonStudent->setDeletedBy($loggedInUser);
        $this->orgPersonStudentRepository->persist($orgPersonStudent);
        unset($orgPersonStudent);
        return true;
    }

    /**
     * Creating new student with all validation and returns error object if any
     *
     * @param PersonDTO $personDTO
     * @param Person $personObject
     * @param Person $loggedInUser
     * @param Organization $organization
     * @param DataProcessingExceptionHandler $dataProcessingExceptionHandler
     * @return DataProcessingExceptionHandler
     */
    public function createStudent($personDTO, $personObject, $loggedInUser, $organization, $dataProcessingExceptionHandler)
    {
        // OrgPersonStudent not exists and is_student is true, create the student
        $orgPersonStudent = new OrgPersonStudent();
        //create student auth key using external Id
        $studentAuthKey = $this->personService->generateAuthKey($personDTO->getExternalId(), 'Student');
        $currentDate = new \DateTime();

        //setting valid values to the orgPersonStudent Object
        $orgPersonStudent->setPerson($personObject);
        $orgPersonStudent->setOrganization($organization);
        $orgPersonStudent->setCreatedAt($currentDate);
        $orgPersonStudent->setCreatedBy($loggedInUser);
        $orgPersonStudent->setModifiedAt($currentDate);
        $orgPersonStudent->setModifiedBy($loggedInUser);
        $orgPersonStudent->setAuthKey($studentAuthKey);
        // Student's status should be active, if its new person student
        $orgPersonStudent->setStatus(1);

        // Validating PhotoURL
        if (!empty($personDTO->getPhotoLink())) {
            $studentPhotoLink = $this->modifyStudentPhotoLink($personDTO->getPhotoLink(), $orgPersonStudent);
            if (is_array($studentPhotoLink)) {
                $dataProcessingExceptionHandler->addErrors($studentPhotoLink['photo_link'], "photo_link");
            }
        }

        // Validating PrimaryCampusConnectionId
        if (!empty($personDTO->getPrimaryCampusConnectionId())) {
            $primaryCampusConnection = $this->modifyStudentPrimaryCampusConnection($personDTO->getPrimaryCampusConnectionId(), $orgPersonStudent);
            if (is_array($primaryCampusConnection)) {
                $dataProcessingExceptionHandler->addErrors($primaryCampusConnection['primary_campus_connection_id'], "primary_campus_connection_id");
            }
        }

        // Validating RiskGroupId
        if (!empty($personDTO->getRiskGroupId())) {
            $riskGroup = $this->modifyStudentRiskGroup($personDTO->getRiskGroupId(), $orgPersonStudent);
            if (is_array($riskGroup)) {
                $dataProcessingExceptionHandler->addErrors($riskGroup['risk_group_id'], "risk_group_id");
            }
        }

        //persisting and unset student object
        $orgPersonStudent = $this->orgPersonStudentRepository->persist($orgPersonStudent);
        unset($orgPersonStudent);

        //adding the student to the AllStudents group
        $this->groupService->addStudentSystemGroup($organization, $personObject);

        return $dataProcessingExceptionHandler;
    }

    /**
     * Validate and modify Student Photo Link
     *
     * @param string $photoLink
     * @param OrgPersonStudent $orgPersonStudent
     * @return OrgPersonStudent $orgPersonStudent | array
     */
    public function modifyStudentPhotoLink($photoLink, $orgPersonStudent)
    {
        $isValidPhoto = $this->urlUtilityService->validatePhotoURL($photoLink);
        if ($isValidPhoto) {
            $orgPersonStudent->setPhotoUrl($photoLink);
            return $orgPersonStudent;
        } else {
            $personErrorArray['photo_link'] = "Invalid Photo URL. Please try another URL.";
            return $personErrorArray;
        }
    }

    /**
     * Validate and Modify Primary Campus Connection
     *
     * @param string $primaryCampusConnectionId
     * @param OrgPersonStudent $orgPersonStudent
     * @return OrgPersonStudent $orgPersonStudent | array
     */
    public function modifyStudentPrimaryCampusConnection($primaryCampusConnectionId, $orgPersonStudent)
    {
        $organization = $orgPersonStudent->getOrganization();
        $personId = $orgPersonStudent->getPerson()->getId();
        $responsePrimaryCampus = $this->campusConnectionService->validatePrimaryCampusConnectionId($primaryCampusConnectionId, $organization, $personId);
        if (is_bool($responsePrimaryCampus)) {
            $validPerson = $this->personRepository->findOneBy(['externalId' => $primaryCampusConnectionId, 'organization' => $organization]);
            $orgPersonStudent->setPersonIdPrimaryConnect($validPerson);
            return $orgPersonStudent;
        } else {
            $personErrorArray['primary_campus_connection_id'] = $responsePrimaryCampus;
            return $personErrorArray;
        }
    }

    /**
     * Validate $riskGroupId and Insert $riskGroupId to RiskGroupPersonHistory
     *
     * @param int $riskGroupId
     * @param OrgPersonStudent $orgPersonStudent
     * @return OrgPersonStudent $orgPersonStudent | array
     */
    public function modifyStudentRiskGroup($riskGroupId, $orgPersonStudent)
    {
        $organization = $orgPersonStudent->getOrganization();
        $personObject = $orgPersonStudent->getPerson();
        $currentDate = new \DateTime();
        $responseRiskGroup = $this->riskGroupService->validateRiskGroupBelongsToOrganization($organization->getId(), $riskGroupId);
        if (is_bool($responseRiskGroup)) {
            $riskGroup = $this->riskGroupRepository->find($riskGroupId);
            $riskGroupPersonHistoryExist = $this->riskGroupPersonHistoryRepository->findOneBy(['person' => $personObject, 'riskGroup' => $riskGroup]);
            if (empty($riskGroupPersonHistoryExist)) {
                $riskGroupPersonHistoryObject = new RiskGroupPersonHistory();
                $riskGroupPersonHistoryObject->setPerson($personObject);
                $riskGroupPersonHistoryObject->setRiskGroup($riskGroup);
                $riskGroupPersonHistoryObject->setAssignmentDate($currentDate);
                //persisting and unset RiskGroupPersonHistory object
                $this->riskGroupPersonHistoryRepository->persist($riskGroupPersonHistoryObject);
                unset($riskGroupPersonHistoryObject);
            }
            return $orgPersonStudent;
        } else {
            $personErrorArray['risk_group_id'] = $responseRiskGroup;
            return $personErrorArray;
        }
    }

    /**
     * Checks to see if the person is a student
     *
     * @param int studentId
     * @return bool
     */
    public function isPersonAStudent($studentId)
    {
        $personStudentObject = $this->orgPersonStudentRepository->findOneBy([
            "person" => $studentId
        ]);

        if ($personStudentObject) {
            return true;
        }

        return false;
    }
}
