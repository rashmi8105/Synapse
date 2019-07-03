<?php
namespace Synapse\CoreBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\SecurityContext;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CampusResourceBundle\Repository\OrgCampusResourceRepository;
use Synapse\CoreBundle\Entity\ActivityCategory;
use Synapse\CoreBundle\Entity\ActivityCategoryLang;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\ReferralHistory;
use Synapse\CoreBundle\Entity\ReferralRoutingRules;
use Synapse\CoreBundle\Entity\Referrals;
use Synapse\CoreBundle\Entity\ReferralsInterestedParties;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\job\BulkReferralJob;
use Synapse\CoreBundle\Repository\ActivityCategoryLangRepository;
use Synapse\CoreBundle\Repository\ActivityCategoryRepository;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\EmailTemplateRepository;
use Synapse\CoreBundle\Repository\FeatureMasterLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgFeaturesRepository;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetFeaturesRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\ReferralHistoryRepository;
use Synapse\CoreBundle\Repository\ReferralRepository;
use Synapse\CoreBundle\Repository\ReferralRoutingRulesRepository;
use Synapse\CoreBundle\Repository\ReferralsInterestedPartiesRepository;
use Synapse\CoreBundle\Repository\ReferralsTeamsRepository;
use Synapse\CoreBundle\Repository\TeamMembersRepository;
use Synapse\CoreBundle\Repository\TeamsRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\PermissionConstInterface;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\ReferralConstant;
use Synapse\RestBundle\Entity\ActivityLogDto;
use Synapse\RestBundle\Entity\AssignToResponseDto;
use Synapse\RestBundle\Entity\ReferralListResponseDto;
use Synapse\RestBundle\Entity\ReferralListResponseHeaderDto;
use Synapse\RestBundle\Entity\ReferralsDTO;
use Synapse\RestBundle\Entity\RelatedActivitiesDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;
use Synapse\RestBundle\Entity\TeamIdsDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\StaticListBundle\Repository\OrgStaticListStudentsRepository;
use Synapse\StaticListBundle\Util\Constants\StaticListConstant;


/**
 * @DI\Service("referral_service")
 */
class ReferralService extends ReferralHelperService implements PermissionConstInterface
{

    const SERVICE_KEY = 'referral_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Manager
     */
    public $rbacManager;

    /**
     * @var Resque
     */
    private $resque;

    /**
     * @var SecurityContext
     */
    private $securityContext;


    // Member Variables

    /**
     * @var Person
     */
    private $user;


    // Services

    /**
     * @var ActivityLogService
     */
    private $activityLogService;

    /**
     * @var AlertNotificationsService
     */
    private $alertService;

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
     * @var FeatureService
     */
    private $featureService;

    /**
     * @var LoggerHelperService
     */
    private $loggerHelperService;

    /**
     * @var MapworksActionService
     */
    private $mapworksActionService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var OrgPermissionsetService
     */
    private $orgPermissionsetService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var RelatedActivitiesService
     */
    private $relatedActivitiesService;

    /**
     * @var UserManagementService
     */
    private $userManagementService;


    // Repositories

    /**
     * @var ActivityCategoryRepository
     */
    private $activityCategoryRepository;

    /**
     * @var ActivityCategoryLangRepository
     */
    private $activityCategoryLangRepository;

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var EmailTemplateRepository
     */
    private $emailTemplateRepository;

    /**
     * @var FeatureMasterLangRepository
     */
    private $featureMasterLangRepository;

    /**
     * @var ReferralsInterestedPartiesRepository
     */
    private $interestedPartiesRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrgCampusResourceRepository
     */
    private $orgCampusResourceRepository;

    /**
     * @var OrgFeaturesRepository
     */
    private $orgFeaturesRepository;

    /**
     * @var OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;

    /**
     * @var OrgPermissionsetFeaturesRepository
     */
    private $orgPermissionsetFeaturesRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var ReferralRepository
     */
    private $referralRepository;

    /**
     * @var ReferralHistoryRepository
     */
    private $referralHistoryRepository;

    /**
     * @var ReferralRoutingRulesRepository
     */
    private $referralRoutingRulesRepository;

    /**
     * @var ReferralsTeamsRepository
     */
    private $referralsTeamsRepository;

    /**
     * @var OrgStaticListStudentsRepository
     */
    private $staticListStudentsRepository;

    /**
     * @var TeamMembersRepository
     */
    private $teamMembersRepository;

    /**
     * @var TeamsRepository
     */
    private $teamRepository;


    /**
     * ReferralService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        //scaffolding
        $this->container = $container;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->securityContext = $this->container->get(SynapseConstant::SECURITY_CONTEXT_CLASS_KEY);

        // Services
        $this->activityLogService = $this->container->get(ActivityLogService::SERVICE_KEY);
        $this->alertService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->featureService = $this->container->get(FeatureService::SERVICE_KEY);
        $this->loggerHelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);
        $this->mapworksActionService = $this->container->get(MapworksActionService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->orgPermissionsetService = $this->container->get(OrgPermissionsetService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->relatedActivitiesService = $this->container->get(RelatedActivitiesService::SERVICE_KEY);
        $this->userManagementService =  $this->container->get(UserManagementService::SERVICE_KEY);

        // Repositories
        $this->activityCategoryRepository = $this->repositoryResolver->getRepository(ActivityCategoryRepository::REPOSITORY_KEY);
        $this->activityCategoryLangRepository = $this->repositoryResolver->getRepository(ActivityCategoryLangRepository::REPOSITORY_KEY);
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->emailTemplateRepository = $this->repositoryResolver->getRepository(EmailTemplateRepository::REPOSITORY_KEY);
        $this->featureMasterLangRepository = $this->repositoryResolver->getRepository(FeatureMasterLangRepository::REPOSITORY_KEY);
        $this->interestedPartiesRepository = $this->repositoryResolver->getRepository(ReferralsInterestedPartiesRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->orgCampusResourceRepository = $this->repositoryResolver->getRepository(OrgCampusResourceRepository::REPOSITORY_KEY);
        $this->orgFeaturesRepository = $this->repositoryResolver->getRepository(OrgFeaturesRepository::REPOSITORY_KEY);
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgGroupFacultyRepository::REPOSITORY_KEY);
        $this->orgPermissionsetFeaturesRepository = $this->repositoryResolver->getRepository(OrgPermissionsetFeaturesRepository::REPOSITORY_KEY);
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->referralsTeamsRepository = $this->repositoryResolver->getRepository(ReferralsTeamsRepository::REPOSITORY_KEY);
        $this->referralRoutingRulesRepository = $this->repositoryResolver->getRepository(ReferralRoutingRulesRepository::REPOSITORY_KEY);
        $this->referralRepository = $this->repositoryResolver->getRepository(ReferralRepository::REPOSITORY_KEY);
        $this->referralHistoryRepository = $this->repositoryResolver->getRepository(ReferralHistoryRepository::REPOSITORY_KEY);
        $this->staticListStudentsRepository = $this->repositoryResolver->getRepository(OrgStaticListStudentsRepository::REPOSITORY_KEY);
        $this->teamMembersRepository = $this->repositoryResolver->getRepository(TeamMembersRepository::REPOSITORY_KEY);
        $this->teamRepository = $this->repositoryResolver->getRepository(TeamsRepository::REPOSITORY_KEY);

    }


    public function setUser($user)
    {
        $this->user = $user;
    }


    /**
     * creates a referral
     *
     * @param ReferralsDTO $referralsDTO
     * @param bool $isBulkAction
     * @return ReferralsDTO
     * @throws AccessDeniedException
     */
    public function createReferral(ReferralsDTO $referralsDTO, $isBulkAction = false)
    {
        $studentIds = $referralsDTO->getPersonStudentId();
        $studentsIds = explode(",", $studentIds);
        $organizationId = $referralsDTO->getOrganizationId();
        $staffId = $referralsDTO->getPersonStaffId();
        $this->rbacManager->assertPermissionToEngageWithStudents($studentsIds, $staffId);
        $shareOptionPermissions = $this->getShareOptionPermission($referralsDTO, PermissionConstInterface::ASSET_REFERRALS);
        $referralDateTime = new \DateTime('now');
        $teamShare = $referralsDTO->getShareOptions()[0]->getTeamsShare();
        $selectedTeams = $referralsDTO->getShareOptions()[0]->getTeamIds();
        $assignTo = $referralsDTO->getAssignedToUserId();

        if ($assignTo > 0) {
            $this->rbacManager->checkAccessToOrganizationUsingPersonId($assignTo);
        }
        $lastActivityDate = clone $referralDateTime;

        $lastActivity = $lastActivityDate->format(SynapseConstant::TWO_DIGIT_YEAR_DATE_FORMAT) . "- Referral";

        $tokenValues = [];
        $creatorArrayWithReferralCount = [];
        $assigneeArrayWithReferralCount = [];
        $interestedPartyArrayWithReferralCount = [];

        $sendCreateReferralCommunicationToStudent = true; //determines if the all communication for the referrals are successful

        foreach ($studentsIds as $studentId) {
            // bulkactions/permissions?type=R - student validated on this api
            $referral = new Referrals();
            $personStaff = $this->personService->findPerson($staffId);
            $organization = $this->organizationService->find($organizationId);
            $activityCategory = $this->activityCategoryRepository->find($referralsDTO->getReasonCategorySubitemId());
            $organizationLang = $this->organizationService->getOrganizationDetailsLang($organizationId);
            $activityCategoryLang = $this->activityCategoryLangRepository->findOneBy(array(
                'activityCategoryId' => $activityCategory,
                'language' => $organizationLang->getLang()
            ));
            $referral->setActivityCategory($activityCategory);
            $personStudent = $this->personService->findPerson($studentId);
            $feature = $this->featureMasterLangRepository->findOneBy(['featureName' => 'Referrals']);
            $featureId = $feature->getId();
            $referralAssigneeKey = $referralsDTO->getAssignedToUserKey();
            //$shareOptionPermissions will always come as an array with two indices: reason routing (0) and non-reason routed referrals(1). The first index will always be reason routed.
            //If the referral assignee key is "Central Coordinator", it is a reason-routed referral.
            if ($referralAssigneeKey == "Central Coordinator") {
                $featureAccess = $this->featureService->verifyFacultyAccessToStudentForFeature($staffId, $organizationId, $studentId, $shareOptionPermissions[0], $featureId, true);
            } else {
                $featureAccess = $this->featureService->verifyFacultyAccessToStudentForFeature($staffId, $organizationId, $studentId, $shareOptionPermissions[1], $featureId);
            }
            //If they don't have create referral permissions on normal or reason routed referrals, and it's not a job, throw an AccessDeniedException.
            if (!$featureAccess) {
                if ($isBulkAction) {
                    continue;
                } else {
                    $this->logger->error("Referral Service - Create Referral - Do not have permission to create referral for student -" . $studentId);
                    throw new AccessDeniedException('Do not have permission to create referral');
                }
            }
            //Validate that the assignee and the interested parties have access to the student
            $this->validateFacultyAccessToStudent($studentId, $referralsDTO);
            $referral->setPersonStudent($personStudent);
            $referral->setPersonFaculty($personStaff);
            $referral->setOrganization($organization);
            $referral->setNote($referralsDTO->getComment());
            $referral->setReferralDate($referralDateTime);
            $referral->setAccessPrivate($referralsDTO->getShareOptions()[0]
                ->getPrivateShare());
            $referral->setAccessPublic($referralsDTO->getShareOptions()[0]
                ->getPublicShare());
            $referral->setAccessTeam($teamShare);
            // Assign this Referral
            $referral = $this->setPersonAssignTo($referralsDTO, $referral, $studentId, $organization);
            $referral->setIsHighPriority($referralsDTO->getHighPriorityConcern());
            $referral->setIsLeaving($referralsDTO->getStudentIndicatedToLeave());
            $referral->setIsDiscussed($referralsDTO->getIssueDiscussedWithStudent());
            $referral->setReferrerPermission($referralsDTO->getIssueRevealedToStudent());
            $notifyStudent = $referralsDTO->getNotifyStudent();
            $referral->setNotifyStudent($notifyStudent);
            $status = 'O';
            $referral->setStatus($status);
            if ($assignTo <= 0) {
                $referral->setIsReasonRouted(1);
            } else {
                //direct referral
                $referral->setIsReasonRouted(0);
                $featurePermission = $this->orgPermissionsetService->getStudentFeature($studentId, $assignTo);
                $hasReceiveReferralPermission = $this->getDirectAndReasonRoutedPerm($featurePermission);
                $receiveReferral = false;
                if (isset($hasReceiveReferralPermission['receive_referrals'])) {
                    $receiveReferral = $hasReceiveReferralPermission['receive_referrals'];
                }
                if (!$receiveReferral) {
                    $referral->setPersonAssignedTo(null);
                }
                $referral->setUserKey($referralsDTO->getAssignedToUserKey());
            }
            // Check if logged in faculty is a member of all the teams selected on referral
            $selectedTeamIds = [];
            foreach ($selectedTeams as $selectedTeam) {
                if ($selectedTeam->getIsTeamSelected()) {
                    $selectedTeamIds[] = $selectedTeam->getId();
                }
            }
            $teamsForFaculty = $this->teamMembersRepository->findBy(['person' => $referral->getPersonFaculty()]);
            $teamIdsForFaculty = [];
            foreach ($teamsForFaculty as $teamForFaculty) {
                $teamIdsForFaculty[] = $teamForFaculty->getTeamId()->getId();
            }
            $inaccessibleTeamIds = array_diff($selectedTeamIds, $teamIdsForFaculty);
            if (!empty($inaccessibleTeamIds)) {
                throw new AccessDeniedException("Access Denied: You're trying to share a referral with a team you're not a member of.");
            }
            $referral = $this->referralRepository->createReferral($referral);
            if ($teamShare && $referral) {
                $this->addTeams($referral, $selectedTeams);
            }
            $interestedParties = $referralsDTO->getInterestedParties();
            if ($interestedParties && $referral) {
                // Mark the referral to the interested parties
                $this->addInterestedParties($referral, $interestedParties);
            }
            $languageId = $organizationLang->getLang()->getId();

            if ($assignTo == 0) {
                // Assign to central coordinator
                $activityCategory = $referral->getActivityCategory();
                $this->assignToCentral($organization, $activityCategory, $referral, $studentId);
            }

            $this->referralRepository->flush();
            // referral history
            $referralHistory = '';
            if (!$isBulkAction) {
                $referralHistory = $this->createReferralHistoryRecord($referral, 'create', $personStaff);
            }

            // Send Email Notification
            $tokenValues = $this->mapReferralToTokenVariables($organizationId, $referral);
            $notificationReason = $activityCategoryLang->getDescription();
            $studentCommunication = '';
            if ($notifyStudent) {
                $studentCommunication = $this->sendCreateReferralCommunicationToStudent($referral, $tokenValues, $notificationReason, $referralHistory);
            }
            if (!$isBulkAction) {
                $staffCommunication = $this->sendCreateReferralCommunication($referral, $activityCategory, $assignTo, $tokenValues, $notificationReason, $referralHistory);
                if ((is_array($staffCommunication) || is_array($studentCommunication)) && $sendCreateReferralCommunicationToStudent) {
                    $sendCreateReferralCommunicationToStudent = false; // once set to false  for a referral, will throw an exception
                }
            } else {
                // For bulk referral, get the assignee, creator and interested parties with student count.

                $assignedToPerson = $assignTo;
                if ($referral->getIsReasonRouted()) {
                    $assignedToPerson = $this->getReasonRoutedReferralAssignee($organization, $activityCategory, $studentId, true);
                }
                // creator array with student count
                if (isset($creatorArrayWithReferralCount[$staffId])) {
                    $creatorArrayWithReferralCount[$staffId]++;
                } else {
                    $creatorArrayWithReferralCount[$staffId] = 1;
                }
                // assigned person array with student count
                if (isset($assigneeArrayWithReferralCount[$assignedToPerson])) {
                    $assigneeArrayWithReferralCount[$assignedToPerson]++;
                } else {
                    $assigneeArrayWithReferralCount[$assignedToPerson] = 1;
                }


                // Interested party array with student count.

                $interestedPartiesArray = [];
                if (isset($tokenValues['interested_parties'])) {
                    foreach ($tokenValues['interested_parties'] as $interestedParty) {
                        $interestedPartyId = $interestedParty['$$interested_party_id$$'];
                        $interestedPartiesArray[$interestedParty['$$interested_party_id$$']] = $interestedParty;
                        if (isset($interestedPartyArrayWithReferralCount[$interestedPartyId])) {
                            $interestedPartyArrayWithReferralCount[$interestedPartyId]++;
                        } else {
                            $interestedPartyArrayWithReferralCount[$interestedPartyId] = 1;
                        }
                    }
                }
            }
            $personStudent->setLastActivity($lastActivity);
            $referralsDTO->setreferralId($referral->getId());
            $referralsDTO->setLangId($languageId);
            $referralsDTO->setReasonCategorySubitem($activityCategoryLang->getDescription());
            $this->referralRepository->flush();
            $activityLogDto = new ActivityLogDto();
            $activityLogDto->setActivityDate($referralDateTime);
            $activityLogDto->setActivityType("R");
            $referralId = $referral->getId();
            $activityLogDto->setReferrals($referralId);
            $activityLogDto->setOrganization($organizationId);
            $facultyId = $personStaff->getId();
            $activityLogDto->setPersonIdFaculty($facultyId);
            $studentId = $personStudent->getId();
            $activityLogDto->setPersonIdStudent($studentId);
            $reasonText = $referral->getActivityCategory()->getShortName();
            $activityLogDto->setReason($reasonText);
            $this->activityLogService->createActivityLog($activityLogDto);
            $activityLogId = $referralsDTO->getActivityLogId();
            if (isset($activityLogId)) {
                $relatedActivitiesDto = new RelatedActivitiesDto();
                $relatedActivitiesDto->setActivityLog($activityLogId);
                $relatedActivitiesDto->setReferral($referralId);
                $relatedActivitiesDto->setOrganization($organizationId);
                $this->relatedActivitiesService->createRelatedActivities($relatedActivitiesDto);
            }
            // the assigned to person might change if no receive referral permission etc, set the exact in response
            if (!$isBulkAction) {
                if (!$referral->getPersonAssignedTo()) {
                    $referralsDTO->setAssignedToUserId(0);
                } else {
                    $referralsDTO->setAssignedToUserId($referral->getPersonAssignedTo()->getId());
                }
            }
        }
        // Send Email notification for bulk referral.
        if ($isBulkAction) {
            $notificationReason = count($studentsIds) . ' Referrals have been created successfully ';
            //This code will not be hit in non bulk actions, so if any bulk action happened, there should always be a last referral to use below
            //In this case, we just need a referral history record, a single referral does not make a lot of sense, but it has to be there to make this code work
            //TODO: Technical Debt created for this ESPRJ-14993
            $referralHistory = $this->createReferralHistoryRecord($referral, 'bulk_action', $personStaff);
            $this->sendBulkCreateReferralCommunication($organizationId, $tokenValues, $creatorArrayWithReferralCount, $assigneeArrayWithReferralCount, $interestedPartyArrayWithReferralCount, $referral, $referralHistory, $interestedPartiesArray);
        } elseif (!$sendCreateReferralCommunicationToStudent) {
            throw new SynapseValidationException("Expected Communication using Email or Notifications failed");
        }
        return $referralsDTO;
    }


    /**
     * Ensure that the potential referral assignee and all interested parties have access to the student.
     * If they do not have access, throw an exception.
     *
     * @param int $studentId
     * @param ReferralsDto $referralsDTO
     * @throws AccessDeniedException
     */
    public function validateFacultyAccessToStudent($studentId, $referralsDTO)
    {
        $facultyIds = [];

        $assignedToUserId = $referralsDTO->getAssignedToUserId();
        if (!empty($assignedToUserId)) {
            $facultyIds[] = $assignedToUserId;
        }

        $interestedParties = $referralsDTO->getInterestedParties();
        if ($interestedParties) {
            foreach ($interestedParties as $interestedParty) {
                if (!empty($interestedParty['id'])) {
                    $facultyIds[] = $interestedParty['id'];
                }
            }
        }

        foreach ($facultyIds as $facultyId) {
            $isStudentAccessible = $this->rbacManager->checkAccessToStudent($studentId, $facultyId);

            if (!$isStudentAccessible) {

                $facultyPersonObject = $this->personRepository->find($facultyId);
                $facultyFirstname = $facultyPersonObject->getFirstname();
                $facultyLastname = $facultyPersonObject->getLastname();

                $this->logger->error("ReferralService::validateFacultyAccessToStudent() - faculty: $facultyId does not have access to student: $studentId");
                throw new AccessDeniedException("$facultyFirstname $facultyLastname does not have access to the student.");
            }
        }
    }


    /**
     * Assign referral to coordinator (But priority will take primary connection if reason set to primary)
     *
     * @param Organization $organization
     * @param ActivityCategory $activityCategory
     * @param Referrals $referral
     * @param int $studentId
     */
    public function assignToCentral($organization, $activityCategory, $referral, $studentId)
    {
        $referralRoutingRulesInstance = $this->referralRoutingRulesRepository->findOneBy(["organization" => $organization, 'activityCategory' => $activityCategory->getId()]);
        $this->setReferralAssigneeBasedOnRouting($referralRoutingRulesInstance, $organization, $referral, $studentId);
    }


    /**
     * check CREATE referral access
     * @param unknown $feature
     * @return boolean
     */
    private function getUserAccess($feature)
    {
        if (!empty($feature)) {
            foreach ($feature as $key => $featureItem) {
                if ((isset($featureItem['create']) && $featureItem['create'])) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * Get referral direct and reason routed CREATE referral permission
     * @param unknown $getFeaturePermission
     */
    private function getDirectAndReasonRoutedPerm($featurePermission, $faculty = null)
    {
        $refDirectReason = [];
        $refShare = [];
        if ($featurePermission) {
            if ($faculty) {
                if (!empty($featurePermission['user_feature_permissions'][0])) {
                    $refShare = $featurePermission['user_feature_permissions'][0]['referrals_share'];
                }
            } else {
                if (!empty($featurePermission['student_feature_permissions'][0])) {
                    $refShare = $featurePermission['student_feature_permissions'][0]['referrals_share'];
                }
            }
            if (!empty($refShare)) {
                foreach ($refShare as $key => $value) {
                    $feature = $value;
                    if (is_array($feature)) {
                        $refDirectReason[$key] = $this->getUserAccess($feature);
                    }
                    if ($key == 'receive_referrals') {
                        $refDirectReason[$key] = $value;
                    }
                }
            }
        }
        return $refDirectReason;
    }


    /**
     * Add interested parties in referral
     *
     * @param Referrals $referral
     * @param array $referralInterestedPartiesArray
     * @throws SynapseValidationException
     * @return void
     */
    private function addInterestedParties($referral, $referralInterestedPartiesArray)
    {
        $studentDetails = $referral->getPersonStudent();
        $studentId = $studentDetails->getId();
        $organization = $referral->getOrganization();

        $orgPersonStudent = $this->orgPersonStudentRepository->findOneBy(['organization' => $organization, 'person' => $studentDetails]);
        if (!$orgPersonStudent) {
            throw new SynapseValidationException("Student does not exist");
        }
        $activityCategory = $referral->getActivityCategory();
        if (!$activityCategory) {
            throw new SynapseValidationException("Activity category does not exist");
        }
        $referralRoutingRulesInstance = $this->referralRoutingRulesRepository->findOneBy(["organization" => $organization, 'activityCategory' => $activityCategory->getId()]);

        foreach ($referralInterestedPartiesArray as $referralInterestedParties) {

            if ($referralInterestedParties["id"] > 0) {
                $referralInterestedParty = new ReferralsInterestedParties();
                $person = $this->personRepository->find($referralInterestedParties["id"]);
                $referralInterestedParty->setReferrals($referral);
                $referralInterestedParty->setPerson($person);
                $referralInterestedParty->setUserKey($referralInterestedParties["user_key"]);
                $this->interestedPartiesRepository->createReferralsInterestedParties($referralInterestedParty);

            } elseif ($referralInterestedParties["id"] == 0) {
                // Send mail to Central Referral
                $this->assignToCentral($organization, $activityCategory, $referral, $studentId);

                //check if primary campus check is available in setting
                if ($referralRoutingRulesInstance->getIsPrimaryCampusConnection()) {
                    //assign to central
                    if ($orgPersonStudent->getPersonIdPrimaryConnect()) {
                        $facultyDetails = $orgPersonStudent->getPersonIdPrimaryConnect();
                        $referralInterestedParty = new ReferralsInterestedParties();
                        $referralInterestedParty->setReferrals($referral);
                        $referralInterestedParty->setPerson($facultyDetails);
                        $this->interestedPartiesRepository->createReferralsInterestedParties($referralInterestedParty);
                    }
                }
            } else {
                // If the interested party is the primary campus connection
                if ($orgPersonStudent->getPersonIdPrimaryConnect()) {
                    $facultyDetails = $orgPersonStudent->getPersonIdPrimaryConnect();
                    $referralInterestedParty = new ReferralsInterestedParties();
                    $referralInterestedParty->setReferrals($referral);
                    $referralInterestedParty->setPerson($facultyDetails);
                    $this->interestedPartiesRepository->createReferralsInterestedParties($referralInterestedParty);
                }
            }
        }
    }


    /**
     *
     * Send email notification to assignee
     *
     * @param Person $personStudent
     * @param int $organizationId
     * @param string $emailKey
     * @param int $organizationLanguageId
     * @param array $tokenValues
     * @return null
     * @throws SynapseValidationException
     */
    public function sendToAssignee($personStudent, $organizationId, $emailKey, $organizationLanguageId, $tokenValues)
    {
        $studentContactEmail = $personStudent->getUsername();
        if (!is_null($studentContactEmail)) {
            $emailTemplateObject = $this->emailTemplateRepository->findOneBy(['emailKey' => $emailKey]);
            if ($emailTemplateObject) {
                $emailTemplateLangObject = $this->emailTemplateLangRepository->findOneBy(['emailTemplate' => $emailTemplateObject]);
            } else {
                throw new SynapseValidationException("Email template for key $emailKey not found");
            }
            if ($emailTemplateLangObject) {
                $emailResponse = [];

                $emailBody = $emailTemplateLangObject->getBody();
                $emailBody = $this->emailService->generateEmailMessage($emailBody, $tokenValues);
                $bccRecipientList = $emailTemplateLangObject->getEmailTemplate()->getBccRecipientList();
                $emailSubject = $emailTemplateLangObject->getSubject();
                $fromEmailAddress = $emailTemplateLangObject->getEmailTemplate()->getFromEmailAddress();

                $primaryCoordinatorPersonObject = $this->personService->getFirstPrimaryCoordinatorPerson($organizationId);
                if ($primaryCoordinatorPersonObject) {
                    $replyToEmailAddress = $primaryCoordinatorPersonObject->getUsername();
                } else {
                    $replyToEmailAddress = $fromEmailAddress;
                }

                $emailResponse['email_detail'] = array(
                    'from' => $fromEmailAddress,
                    'subject' => $emailSubject,
                    'bcc' => $bccRecipientList,
                    'body' => $emailBody,
                    'to' => $studentContactEmail,
                    'emailKey' => $emailKey,
                    'organizationId' => $organizationId,
                    'replyTo' => $replyToEmailAddress
                );

                $emailInstance = $this->emailService->sendEmailNotification($emailResponse['email_detail']);
                $this->emailService->sendEmail($emailInstance);

            }


        }
    }


    /**
     * View referral by Id
     *
     * @param int $referralId
     * @return ReferralsDTO
     */
    public function getReferral($referralId)
    {
        $referral = $this->referralRepository->find($referralId, new SynapseValidationException("Referral Id is Not Valid"));
        $referralStudentId = $referral->getPersonStudent()->getId();

        //check for non-participant student permissions
        $this->rbacManager->assertPermissionToEngageWithStudents([$referralStudentId]);

        // Added permission check to view reason routed referral
        if ($referral->getAccessPublic()) {
            $checkAssetAccess = [self::PERM_REFERRALS_PUBLIC_VIEW, self::PERM_REASON_REFERRALS_PUBLIC_VIEW];
        } elseif ($referral->getAccessPrivate()) {
            $checkAssetAccess = [self::PERM_REFERRALS_PRIVATE_VIEW, self::PERM_REASON_REFERRALS_PRIVATE_VIEW];
        } else {
            $checkAssetAccess = [self::PERM_REFERRALS_TEAMS_VIEW, self::PERM_REASON_REFERRALS_TEAMS_VIEW];
        }

        // check if notes has is referral access
        if (!$this->rbacManager->hasAssetAccess($checkAssetAccess, $referral)) {
            $this->logger->error(" Referral Service - getReferral - Do not have permission to view this referral -" . $referralId);
            throw new AccessDeniedException(self::REFERRAL_VIEW_EXCEPTION);
        }
        $referralDto = new ReferralsDTO();
        $organizationId = $referral->getOrganization()->getId();
        $referralDto->setOrganizationId($organizationId);

        $orgLanguage = $this->organizationService->getOrganizationDetailsLang($organizationId);
        $referralDto->setLangId($orgLanguage->getLang()->getId());

        $referralDto->setReferralId($referral->getId());
        $referralDto->setPersonStaffId($referral->getPersonFaculty()->getId());
        $referralDto->setPersonStudentId($referralStudentId);
        $referralDto->setStatus($referral->getStatus());
        $referralDto->setReasonCategorySubitemId($referral->getActivityCategory()->getId());
        $activityCategoryLanguageObject = $this->activityCategoryLangRepository->findOneBy(array(
            'activityCategoryId' => $referral->getActivityCategory(),
            'language' => $orgLanguage->getLang()
        ));
        $referralDto->setReasonCategorySubitem($activityCategoryLanguageObject->getDescription());

        $isStudentActive = $this->userManagementService->isStudentActive($referralStudentId, $organizationId);
        $referralDto->setStudentStatus($isStudentActive);

        $personAssignTo = $referral->getPersonAssignedTo();
        if ($personAssignTo) {
            $assignToPersonId = $personAssignTo->getId();

            // check if the person assigned to is still a valid person
            $assignedToPersonObject = $this->personRepository->findOneBy([
                'id' => $assignToPersonId,
                'organization' => $organizationId
            ]);

            // makes sure the the assigned is still a faculty.
            $assignedToFacultyObject = $this->orgPersonFacultyRepository->findOneBy([
                'person' => $assignToPersonId,
                'organization' => $organizationId
            ]);
        }
        if ($assignedToPersonObject && $assignedToFacultyObject) {
            $referralDto->setAssignedToUserId($personAssignTo->getId());
            $assignDto = new AssignToResponseDto();
            $assignDto->setUserId($personAssignTo->getId());
            $assignDto->setFirstName($personAssignTo->getFirstname());
            $assignDto->setLastName($personAssignTo->getLastname());
            $referralDto->setAssignedTo($assignDto);
            $referralDto->setAssignedToUserKey($referral->getUserKey());
        } else {
            $referralDto->setAssignedToUserId(0);
            $referralDto->setAssignedToUserKey('');
            $assignDto = new AssignToResponseDto();
            //Find primary coordinator based on referral organization id
            $primaryCoordinatorObject = $this->personService->getFirstPrimaryCoordinatorPerson($organizationId);
            $assignDto->setUserId($primaryCoordinatorObject->getId());
            $assignDto->setFirstName($primaryCoordinatorObject->getFirstname());
            $assignDto->setLastName($primaryCoordinatorObject->getLastname());
            $referralDto->setAssignedTo($assignDto);
        }

        // Interested Parties
        $interestedPartiesArray = [];
        $interestedParties = $this->interestedPartiesRepository->findBy([
            'referrals' => $referral
        ]);


        if ($interestedParties && count($interestedParties) > 0) {
            foreach ($interestedParties as $interestedParty) {
                $interestedPartyPersonId = $interestedParty->getPerson()->getId();
                // check if  the interested Party still exists
                $interestedPartyPersonObject = $this->personRepository->findOneBy([
                    'id' => $interestedPartyPersonId,
                    'organization' => $organizationId
                ]);

                // makes sure the the interested party is still a faculty.
                $interestedPartyFacultyObject = $this->orgPersonFacultyRepository->findOneBy([
                    'person' => $interestedPartyPersonId,
                    'organization' => $organizationId
                ]);

                if (!empty($interestedPartyPersonObject) && $interestedPartyFacultyObject) {
                    $interestedPartiesArray[] = [
                        'id' => $interestedPartyPersonObject->getId(),
                        'first_name' => $interestedPartyPersonObject->getFirstname(),
                        'last_name' => $interestedPartyPersonObject->getLastname(),
                        'user_key' => $interestedParty->getUserKey()
                    ];

                }
            }
        }

        $referralDto->setInterestedParties($interestedPartiesArray);
        $comment = is_null($referral->getNote()) ? "" : $referral->getNote();
        $referralDto->setComment($comment);
        $referralDto->setIssueDiscussedWithStudent((bool)$referral->getIsDiscussed());

        $referralDto->setHighPriorityConcern((bool)$referral->getIsHighPriority());
        $referralDto->setIssueRevealedToStudent((bool)$referral->getReferrerPermission());
        $referralDto->setStudentIndicatedToLeave((bool)$referral->getIsLeaving());
        $referralDto->setNotifyStudent((bool)$referral->getNotifyStudent());

        // Sharing Details
        $shareDto = new ShareOptionsDto();
        $shareDto->setPrivateShare((bool)$referral->getAccessPrivate());
        $shareDto->setPublicShare((bool)$referral->getAccessPublic());
        $shareDto->setTeamsShare((bool)$referral->getAccessTeam());

        // Team Info
        $referralTeams = $this->referralsTeamsRepository->findBy([
            'referrals' => $referral
        ]);


        $teamInfo = [];
        if (count($referralTeams) > 0) {
            foreach ($referralTeams as $referralTeam) {
                $teamObject = $referralTeam->getTeams();
                $teamId = $teamObject->getId();
                $teamObject = $this->teamRepository->findOneBy([
                    'id' => $teamId,
                    'organization' => $organizationId
                ]);
                if ($teamObject) {
                    $teamInfoObject = new TeamIdsDto();
                    $teamInfoObject->setId($teamId);
                    $teamInfoObject->setTeamName($teamObject->getTeamName());
                    $teamInfo[] = $teamInfoObject;
                }
            }
        }
        $shareDto->setTeamIds($teamInfo);
        $referralDto->setShareOptions([
            $shareDto
        ]);

        //Include referral created person's first name and last name
        $personStaff = $referral->getPersonFaculty();
        $referralDto->setReferredByFirstName($personStaff->getFirstname());
        $referralDto->setReferredByLastName($personStaff->getLastname());
        return $referralDto;
    }


    /**
     * Edit a referral
     *
     * @param ReferralsDTO $referralsDTO
     * @param int $loggedInUserId
     * @return Referrals $referral
     */
    public function editReferral(ReferralsDTO $referralsDTO, $loggedInUserId = null)
    {
        if (!$this->user) {
            $this->user = $this->securityContext->getToken()->getUser();
        }
        $activityCategory = $this->activityCategoryRepository->find($referralsDTO->getReasonCategorySubitemId());
        $referral = $this->findReferral($referralsDTO->getReferralId());
        $studentId = $referralsDTO->getPersonStudentId();
        // check for non participant student
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);
        $shareOptionPermission = $this->getShareOptionPermission($referralsDTO, "referrals");
        if (!$this->rbacManager->hasStudentAccess($shareOptionPermission, $referralsDTO, $studentId)) {
            throw new AccessDeniedException("Access denied to editing student's referral");
        }

        //Verify that the assignee and interested parties have access to the student
        $this->validateFacultyAccessToStudent($studentId, $referralsDTO);

        $organization = $referral->getOrganization();
        $oldReferralInterestedParties = $this->interestedPartiesRepository->findBy(['referrals' => $referral]);

        $oldReferralInterestedPartiesArray = [];
        $oldReferralInterestedPartiesRef = [];
        $interestedParties = $referralsDTO->getInterestedParties();

        $previousReasonRoutedReferralAssignee = $this->getReasonRoutedReferralAssignee($organization, $activityCategory, $studentId, true);
        $newAssignee = $referralsDTO->getAssignedToUserId();
        $previousAssignee = ($referral->getPersonAssignedTo()) ? $referral->getPersonAssignedTo()->getId() : $previousReasonRoutedReferralAssignee;

        if ($newAssignee <= 0) {
            $referral->setIsReasonRouted(1);
            $referral->setUserKey(NULL);
        } else {
            $referral->setIsReasonRouted(0);
            $referral->setUserKey($referralsDTO->getAssignedToUserKey());
        }
        $organizationId = $referral->getOrganization()->getId();
        if ($oldReferralInterestedParties) {
            foreach ($oldReferralInterestedParties as $oldReferralInterestedParty) {
                $oldReferralInterestedPartiesArray[] = $oldReferralInterestedParty->getPerson()->getId();
                $oldReferralInterestedPartiesRef[] = $oldReferralInterestedParty;
            }
        }

        // set modified by logged in user
        $loggedInUser = $this->personRepository->find($loggedInUserId);
        $referral->setModifiedBy($loggedInUser);

        $interestedPartiesIds = array_column($interestedParties, "id");
        $newInterestedParties = array_diff($interestedPartiesIds, $oldReferralInterestedPartiesArray);
        $removedInterestedParties = array_diff($oldReferralInterestedPartiesArray, $interestedPartiesIds);
        $areInterestedPartiesAdded = boolval($newInterestedParties);
        $areInterestedPartiesRemoved = boolval($removedInterestedParties);

        //If the assignee is different, this referral update is primarily considered a "reassignment" referral and needs different communications (Notification/Email)
        //If the assignee is the same, this referral update is primarily considered a "content change only" referral and gets different communication (Notification)
        //If only interested parties are affected, we need a "add/removed interested parties", one of the two
        $mapworksAction = $this->determineMapworksActionFromEditedReferral($referralsDTO, $referral, $newAssignee, $previousAssignee, $areInterestedPartiesAdded, $areInterestedPartiesRemoved);

        if ($mapworksAction == 'reassign') {
            if ($referral->getIsReasonRouted()) {
                $newAssignee = $this->getReasonRoutedReferralAssignee($organization, $activityCategory, $studentId, true);
            }
            $personObject = $this->personRepository->find($newAssignee);
            if (!$personObject) {
                throw new SynapseValidationException('Person does not exist');
            }
            $referral->setPersonAssignedTo($personObject);
        }

        $referralHistoryObject = $this->createReferralHistoryRecord($referral, $mapworksAction, $loggedInUser);
        $tokenValues = $this->mapReferralToTokenVariables($organizationId, $referral, $referralHistoryObject, $previousReasonRoutedReferralAssignee);

        // Before removing interested parties, get the token values so that notification can be sent after removing the interested parties
        $tokenValuesForExistingInterestedParties = [];
        if ($areInterestedPartiesRemoved) {
            //Loading all possible Email tokens from Referral for later re-use
            $tokenValuesForExistingInterestedParties = $tokenValues['interested_parties'];
        }
        if ((count($interestedParties) || count($oldReferralInterestedPartiesArray)) > 0) {
            $this->updateInterestedParties($interestedParties, $oldReferralInterestedPartiesArray, $referral, $oldReferralInterestedPartiesRef);
        }
        // After adding the interested parties, get the token values so that notification can be sent after adding the interested parties
        $tokenValuesForUpdatedInterestedParties = [];
        if ($interestedParties) {
            //Loading all possible Email tokens from Referral for later re-use
            $tokenValues = $this->mapReferralToTokenVariables($organizationId, $referral, $referralHistoryObject, $previousReasonRoutedReferralAssignee);
            $tokenValuesForUpdatedInterestedParties = $tokenValues['interested_parties'];
        }

        if ($areInterestedPartiesRemoved) {
            $tokenValues['interested_parties'] = array_merge($tokenValuesForExistingInterestedParties, $tokenValuesForUpdatedInterestedParties);
        }

        $newStatus = strtoupper($referralsDTO->getStatus());

        $referral->setStatus($newStatus);

        // If logged in person is coordinator then they can edit the referral details, comments, and sharing options.
        if (!empty($this->checkIsCoordinator($referralsDTO->getOrganizationId(), $loggedInUserId))) {

            $referral->setNote($referralsDTO->getComment());

            // Update Sharing Options
            $privateShareOption = $referralsDTO->getShareOptions()[0]->getPrivateShare();
            $publicShareOption = $referralsDTO->getShareOptions()[0]->getPublicShare();
            $teamShareOption = $referralsDTO->getShareOptions()[0]->getTeamsShare();

            $referral->setAccessPrivate($privateShareOption);
            $referral->setAccessPublic($publicShareOption);
            $referral->setAccessTeam($teamShareOption);

            // If the user chose Team Sharing Option, make sure the the team(s) is/are saved correctly.
            // For the other sharing options, remove any previously saved teams.
            if ($teamShareOption) {
                $this->reconcileTeams($referral, $referralsDTO, $loggedInUserId);
            } else {
                $existingReferralsTeamsRecords = $this->referralsTeamsRepository->findBy(['referrals' => $referral->getId()]);
                foreach ($existingReferralsTeamsRecords as $existingReferralsTeamsRecord) {
                    $this->referralsTeamsRepository->removeReferralsTeam($existingReferralsTeamsRecord);
                }
            }
            // Coordinator can edit all checkboxes options also
            $referral->setIsHighPriority($referralsDTO->getHighPriorityConcern());
            $referral->setIsLeaving($referralsDTO->getStudentIndicatedToLeave());
            $referral->setIsDiscussed($referralsDTO->getIssueDiscussedWithStudent());
            $referral->setReferrerPermission($referralsDTO->getIssueRevealedToStudent());

            /*
             * Notify Student Option
             * Once this option is set, it can not be unset even by Coordinators
             * Student is sent an email ONLY on the initial setting of this option, subsequent edits do not resend the email
             *
             * See Details:
             * https://jira-mnv.atlassian.net/browse/ESPRJ-12101
             * https://jira-mnv.atlassian.net/wiki/pages/viewpage.action?spaceKey=ESPr&title=Referrals
             */
            $requestedNotifyStudentValue = $referralsDTO->getNotifyStudent();
            $isNotifyStudentAlreadySet = $referral->getNotifyStudent();
            if (!$isNotifyStudentAlreadySet) {
                $referral->setNotifyStudent($requestedNotifyStudentValue);
            }

        }
        $this->referralRepository->flush();


        if ($mapworksAction) {

            // Set all communications to default true
            $updaterCommunicationSent = true;
            $assigneeCommunicationSent = true;
            $creatorCommunicationSent = true;
            $interestedPartyCommunicationSent = true;
            $oldAssigneeCommunicationSent = true;
            $studentCommunicationSent = true;

            // get reason to be set for all communication
            $organizationLang = $this->organizationService->getOrganizationDetailsLang($organizationId);
            $activityCategoryLang = $this->activityCategoryLangRepository->findOneBy(array(
                'activityCategoryId' => $activityCategory,
                'language' => $organizationLang->getLang()
            ));
            $notificationReason = $activityCategoryLang->getDescription();

            if (!$isNotifyStudentAlreadySet && $requestedNotifyStudentValue) { //Student is being notified for the first time that they have a referral created on them.
                $studentCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, 'create', 'student', 'referral', $referral->getPersonIdStudent()->getId(), $notificationReason, $referral, $referralHistoryObject, $tokenValues);
            }

            $referralPersonId = ($referral->getModifiedBy()) ? $referral->getModifiedBy()->getId() : $referral->getPersonIdFaculty()->getId();

            //updating interested parties
            if (isset($tokenValues['interested_parties'])) {
                foreach ($tokenValues['interested_parties'] as $interestedParty) {
                    $interestedPartyTokenValues = array_merge($tokenValues, $interestedParty);
                    if (in_array($interestedParty['$$interested_party_id$$'], $newInterestedParties)) {
                        $interestedPartyCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, 'add_interested_party', 'interested_party', 'referral', $interestedParty['$$interested_party_id$$'], $notificationReason, $referral, $referralHistoryObject, $interestedPartyTokenValues);
                    } elseif (in_array($interestedParty['$$interested_party_id$$'], $removedInterestedParties)) {
                        $interestedPartyCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, 'remove_interested_party', 'interested_party', 'referral', $interestedParty['$$interested_party_id$$'], $notificationReason, $referral, $referralHistoryObject, $interestedPartyTokenValues);

                    } else {
                        if ($mapworksAction == 'update_content') {
                            $interestedPartyCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, $mapworksAction, 'interested_party', 'referral', $interestedParty['$$interested_party_id$$'], $notificationReason, $referral, $referralHistoryObject, $interestedPartyTokenValues);
                        }
                    }
                }
            }

            if ($referral->getIsReasonRouted()) {
                $assignee = $this->personRepository->find($newAssignee);
                if (isset($assignee)) {
                    $tokenValues = array_merge($this->mapworksActionService->getTokenVariablesFromPerson('current_assignee', $assignee), $tokenValues);
                }
            }

            // send communication when mapworks action is not related to ONLY adding/removing interested party
            if (!in_array($mapworksAction, ['add_interested_party', 'remove_interested_party'])) {
                $updaterCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, $mapworksAction, 'updater', 'referral', $referralPersonId, $notificationReason, $referral, $referralHistoryObject, $tokenValues);

                //New Assignee Communication
                if ($referralPersonId != $newAssignee) {
                    $assigneeCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, $mapworksAction, 'current_assignee', 'referral', $newAssignee, $notificationReason, $referral, $referralHistoryObject, $tokenValues);
                }

                //Old Assignee Communication
                if ($mapworksAction == 'reassign' && $referralPersonId != $previousAssignee) {
                    //Since this is an edit, we can also have an old assignee
                    $oldAssigneeCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, $mapworksAction, 'previous_assignee', 'referral', $previousAssignee, $notificationReason, $referral, $referralHistoryObject, $tokenValues);
                }

                //Creator Communication
                $referralCreator = $referral->getPersonIdFaculty()->getId();
                if ($referralPersonId != $referralCreator) {
                    $creatorCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, $mapworksAction, 'creator', 'referral', $referralCreator, $notificationReason, $referral, $referralHistoryObject, $tokenValues);
                }
            }

            $allCommunicationsExpectedSent = $oldAssigneeCommunicationSent && $updaterCommunicationSent && $assigneeCommunicationSent && $interestedPartyCommunicationSent && $studentCommunicationSent && $creatorCommunicationSent;
            if (!$allCommunicationsExpectedSent) {
                // What would be a better exception?  Tech Debt?  Create a new exception type?
                throw new SynapseValidationException("Expected Communication using Email or Notifications failed");
            }
        }
        return $referral;
    }


    /**
     * Delete referral for a referral ID
     *
     * @param int $referralId
     */
    public function deleteReferral($referralId)
    {
        $referral = $this->referralRepository->find($referralId, new SynapseValidationException('Referral not found'));

        $studentId = $referral->getPersonStudent()->getId();

        // check if the student is part of current academic year
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        $referralInterestedParties = $this->interestedPartiesRepository->findByReferrals($referral);
        if ($referralInterestedParties) {
            foreach ($referralInterestedParties as $party) {
                $this->interestedPartiesRepository->removeReferralsInterestedParties($party);
            }
        }

        $referralTeams = $this->referralsTeamsRepository->findByReferrals($referral);
        if ($referralTeams) {
            foreach ($referralTeams as $team) {
                $this->referralsTeamsRepository->removeReferralsTeam($team);
            }
        }
        $this->activityLogService->deleteActivityLogByType($referralId, 'R');
        $this->referralRepository->removeReferrals($referral);
        $this->referralRepository->flush();
        $this->alertService->deleteAlertByActivityId($referralId, 'referrals');
    }


    /**
     * Gets recent referral summary data for the faculty dashboard
     *
     * @param Person $person
     * @return array $recentReferralSummary
     * @throws SynapseValidationException
     */
    public function getReferralSummaryForPerson(Person $person)
    {
        $orgAcademicYear = $this->orgAcademicYearRepository->getCurrentAcademicYear($person->getOrganization()->getId());
        if (empty($orgAcademicYear)) {
            throw new SynapseValidationException('Organization does not have a current Academic Year');
        } // getCurrentAcademicYear returns an array which causes php to throw an warning if $orgAcademicYear['org_academic_year_id'] does not exists

        $students = $this->orgPermissionsetRepository->getStudentsForStaff($person->getId(), $orgAcademicYear['org_academic_year_id']);
        $totalReferralsReceived = $this->referralRepository->getCountOfReferralsAssignedToUser($person->getId(), $person->getOrganization()->getId(), $students, $orgAcademicYear['org_academic_year_id'], null, $orgAcademicYear['start_date'], $orgAcademicYear['end_date']);
        $countOfReferralsAssignedToUser = $totalReferralsReceived[0]['total_referrals'];
        $totalOpenReferralsReceived = $this->referralRepository->getCountOfReferralsAssignedToUser($person->getId(), $person->getOrganization()->getId(), $students, $orgAcademicYear['org_academic_year_id'], 'open', $orgAcademicYear['start_date'], $orgAcademicYear['end_date']);
        $countOfOpenReferralsAssignedToUser = $totalOpenReferralsReceived[0]['total_referrals'];;
        $countOfReferralsCreatedByUser = $this->referralRepository->getSentReferralCount($person, $orgAcademicYear['start_date'], $orgAcademicYear['end_date'], $students);
        $countOfOpenReferralsCreatedByUser = $this->referralRepository->getSentOpenReferralCount($person, $orgAcademicYear['start_date'], $orgAcademicYear['end_date'], $students);


        $recentReferralSummary['totalOpenReferralsReceived'] = $countOfOpenReferralsAssignedToUser;
        $recentReferralSummary['totalReferralsReceived'] = $countOfReferralsAssignedToUser;
        $recentReferralSummary['totalOpenReferralsSent'] = $countOfOpenReferralsCreatedByUser;
        $recentReferralSummary['totalReferralsSent'] = $countOfReferralsCreatedByUser;

        return $recentReferralSummary;
    }


    /**
     * Gets recent referral details for the faculty dashboard
     *
     * @param Person $person
     * @param int $numberOfRecords
     * @param int $offset
     * @return array $recentReferralDetails
     * @throws SynapseValidationException
     */
    public function getRecentReferralDetails(Person $person, $numberOfRecords, $offset)
    {
        $orgAcademicYear = $this->orgAcademicYearRepository->getCurrentAcademicYear($person->getOrganization()->getId());
        if (empty($orgAcademicYear)) {
            throw new SynapseValidationException('Organization does not have a current Academic Year');
        } // getCurrentAcademicYear returns an array which causes php to throw an warning if $orgAcademicYear['org_academic_year_id'] does not exists

        $students = $this->orgPermissionsetRepository->getStudentsForStaff($person->getId(), $orgAcademicYear['org_academic_year_id']);
        $openReferralsAssignedToUser = $this->referralRepository->getReferralsAssignedToUser($person->getId(), $person->getOrganization()->getId(), $students, $orgAcademicYear['org_academic_year_id'], 'open', $orgAcademicYear['start_date'], $orgAcademicYear['end_date'], $numberOfRecords, $offset);

        $recentReferralDetails = [];

        if ($openReferralsAssignedToUser) {

            foreach ($openReferralsAssignedToUser as $referral) {
                $referralDate = $this->dateUtilityService->convertDatabaseStringToISOString($referral['referral_date']);
                $referral['referral_date'] = $referralDate;
                $referral['reason_text'] = (string)$referral['description'];
                $referral['student_status'] = (strlen($referral['status'])) ? $referral['status'] : '1';

                // TODO: This probably isn't used code, as the front end doesn't seem to use student_classlevel for referrals
                $classLevelResults = $this->staticListStudentsRepository->findClassLeveByStudentID($referral['student_id']);
                if (!empty($classLevelResults)) {
                    $classLevels = $this->staticListStudentsRepository->findClassLevelForStudent($classLevelResults[0]['ebi_metadata_id'], $classLevelResults[0]['list_value']);
                    $classLevel = $classLevels[0]['class_level'];
                } else {
                    $classLevel = '';
                }
                $referral['student_classlevel'] = $classLevel;

                $recentReferralDetails[] = $referral;
            }
        }

        return $recentReferralDetails;
    }


    public function getReceivedReferralByPerson(Person $person)
    {
        $this->referralRepository = $this->repositoryResolver->getRepository(ReferralConstant::REFERRALS_REPO);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(ReferralConstant::ORG_PERSON_STUDENT_REPO);
        $this->staticListStudentsRepository = $this->repositoryResolver->getRepository(StaticListConstant::STATICLIST_STUDENTS_REPO);
        $tokenValues = [];
        $tokenValues['personid'] = $person->getId();
        $tokenValues['orgid'] = $person->getOrganization()->getId();
        $query = $this->getEbiSearchQuery('My_Open_Referrals_Received_List', $tokenValues);
        $results = $this->referralRepository->getReceviedReferralByPerson($query);
        $referralResponse = new ReferralListResponseHeaderDto();
        $referralResponse->setPersonId($person->getId());
        $refferalDetail = [];

        if (count($results) > 0) {
            foreach ($results as $result) {
                $detail = new ReferralListResponseDto();

                $detail->setReferralId($result['referral_id']);

                $detail->setStudentId($result[ReferralConstant::PERSONIDSTUDENT]);
                $detail->setStudentFirstName($result[ReferralConstant::FIRST_NAME]);
                $detail->setStudentLastName($result['lastname']);

                $detail->setRiskLevel(($result[ReferralConstant::RISKLEVEL]) ? $result[ReferralConstant::RISKLEVEL] : "");
                $detail->setRiskModelId(($result[ReferralConstant::RISK_TEXT]) ? $result[ReferralConstant::RISK_TEXT] : "");
                $detail->setRiskText($result[ReferralConstant::RISK_TEXT]);

                $detail->setStudentIntentToLeave($result['intent_to_leave']);

                $detail->setImageName(($result[ReferralConstant::IMAGENAME]) ? $result[ReferralConstant::IMAGENAME] : "");

                $detail->setLastActivity(($result[ReferralConstant::LAST_ACTIVITY]) ? $result[ReferralConstant::LAST_ACTIVITY] : "");
                $detail->setStudentLogins(($result[ReferralConstant::LOGIN_COUNT]) ? $result[ReferralConstant::LOGIN_COUNT] : 0);

                $studentStatusObj = $this->orgPersonStudentRepository->findOneByPerson($result[ReferralConstant::PERSONIDSTUDENT]);
                if ($studentStatusObj) {
                    $studentStatus = (strlen($studentStatusObj->getStatus())) ? $studentStatusObj->getStatus() : '1';
                    $detail->setStudentStatus($studentStatus);
                }
                /*
                 * To fetch student class level
                 */
                $classLevelResults = $this->staticListStudentsRepository->findClassLeveByStudentID($result[ReferralConstant::PERSONIDSTUDENT]);
                if (!empty($classLevelResults)) {
                    $classLevels = $this->staticListStudentsRepository->findClassLevelForStudent($classLevelResults[0]['ebi_metadata_id'], $classLevelResults[0]['list_value']);
                    $classLevel = $classLevels[0]['class_level'];
                } else {
                    $classLevel = '';
                }
                $detail->setStudentClasslevel($classLevel);
                $refferalDetail[] = $detail;
            }
        }
        $referralResponse->setReferrals($refferalDetail);
        return $referralResponse;
    }


    public function getSentReferralByPerson(Person $person)
    {
        $this->referralRepository = $this->repositoryResolver->getRepository(ReferralConstant::REFERRALS_REPO);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(ReferralConstant::ORG_PERSON_STUDENT_REPO);
        $this->staticListStudentsRepository = $this->repositoryResolver->getRepository(StaticListConstant::STATICLIST_STUDENTS_REPO);
        $tokenValues = [];
        $tokenValues['personid'] = $person->getId();
        $query = $this->getEbiSearchQuery('My_Open_Referrals_Sent_List', $tokenValues);
        $results = $this->referralRepository->getSentReferralByPerson($query);
        $referralResponse = new ReferralListResponseHeaderDto();
        $referralResponse->setPersonId($person->getId());
        $refferalDetail = [];

        if (count($results) > 0) {
            foreach ($results as $result) {
                $detail = new ReferralListResponseDto();

                $detail->setReferralId($result['referral_id']);

                $detail->setStudentId($result[ReferralConstant::PERSONIDSTUDENT]);
                $detail->setStudentFirstName($result[ReferralConstant::FIRST_NAME]);
                $detail->setStudentLastName($result['lastname']);

                $detail->setRiskLevel(($result[ReferralConstant::RISKLEVEL]) ? $result[ReferralConstant::RISKLEVEL] : "");
                $detail->setRiskModelId(($result[ReferralConstant::RISK_TEXT]) ? $result[ReferralConstant::RISK_TEXT] : "");
                $detail->setRiskText($result[ReferralConstant::RISK_TEXT]);

                $detail->setStudentIntentToLeave($result['intent_to_leave']);

                $detail->setImageName(($result[ReferralConstant::IMAGENAME]) ? $result[ReferralConstant::IMAGENAME] : "");

                $detail->setLastActivity(($result[ReferralConstant::LAST_ACTIVITY]) ? $result[ReferralConstant::LAST_ACTIVITY] : "");
                $detail->setStudentLogins(($result[ReferralConstant::LOGIN_COUNT]) ? $result[ReferralConstant::LOGIN_COUNT] : 0);
                $studentStatusObj = $this->orgPersonStudentRepository->findOneByPerson($result[ReferralConstant::PERSONIDSTUDENT]);
                if ($studentStatusObj) {
                    $studentStatus = (strlen($studentStatusObj->getStatus())) ? $studentStatusObj->getStatus() : '1';
                    $detail->setStudentStatus($studentStatus);
                }
                /*
                 * To fetch student class level
                 */
                $classLevelResults = $this->staticListStudentsRepository->findClassLeveByStudentID($result[ReferralConstant::PERSONIDSTUDENT]);
                if (!empty($classLevelResults)) {
                    $classLevels = $this->staticListStudentsRepository->findClassLevelForStudent($classLevelResults[0]['ebi_metadata_id'], $classLevelResults[0]['list_value']);
                    $classLevel = $classLevels[0]['class_level'];
                } else {
                    $classLevel = '';
                }
                $detail->setStudentClasslevel($classLevel);
                $refferalDetail[] = $detail;
            }
        }
        $referralResponse->setReferrals($refferalDetail);
        return $referralResponse;
    }


    private function getServiceInstance($service)
    {
        return $this->container->get($service);
    }


    /**
     * This method update the interested parties of existing referral
     *
     * @param array $interestedParties
     * @param array $oldReferralInterestedPartiesArray
     * @param Referrals $referral
     * @param array $oldReferralInterestedPartiesRef
     */
    private function updateInterestedParties($interestedParties, $oldReferralInterestedPartiesArray, $referral, $oldReferralInterestedPartiesRef)
    {
        $referralInterestedPartiesInsert = [];
        $interestedPartiesIds = [];
        $referralInterestedPartiesRemove = [];
        $interestedPartyObject = $this->interestedPartiesRepository->findOneBy(array('referrals' => $referral->getId()));
        foreach ($interestedParties as $interestedParty) {
            $interestedPartiesIds[] = $interestedParty['id'];
            if (!in_array($interestedParty['id'], $oldReferralInterestedPartiesArray)) {
                $interestedParties = [];
                $interestedParties['id'] = $interestedParty['id'];
                $interestedParties['user_key'] = $interestedParty['user_key'];
                $referralInterestedPartiesInsert[] = $interestedParties;
                if ($interestedParty['id'] == -1) {
                    $studentDetails = $this->orgPersonStudentRepository->findOneBy(array('organization' => $referral->getOrganization(), 'person' => $referral->getPersonIdStudent()));
                    if ($studentDetails->getPersonIdPrimaryConnect()) {
                        $interestedPartyObject->setPerson($studentDetails->getPersonIdPrimaryConnect());
                    }
                } elseif ($interestedParty['id'] == 0) {
                    if ($interestedPartyObject) {
                        $interestedPartyObject->setPerson(null);
                    }
                }
            }
        }
        foreach ($oldReferralInterestedPartiesArray as $key => $value) {
            if (!in_array($value, $interestedPartiesIds)) {
                $referralInterestedPartiesRemove[] = $oldReferralInterestedPartiesRef[$key];
            }
        }
        if (count($referralInterestedPartiesInsert) > 0) {
            $this->addInterestedParties($referral, $referralInterestedPartiesInsert);
        }

        if ($referralInterestedPartiesRemove && count($referralInterestedPartiesRemove) > 0) {
            foreach ($referralInterestedPartiesRemove as $referralInterestedPartyRemove) {
                $this->interestedPartiesRepository->removeReferralsInterestedParties($referralInterestedPartyRemove);
            }
        }
        $this->interestedPartiesRepository->flush();
    }


    /**
     * Send activity reference email to primary coordinator and primary campus connection
     *
     * @param ReferralRoutingRules $referralRoutingRulesInstance
     * @param Organization $organization
     * @param Referrals $referral
     * @param int $studentId
     * @return void
     */
    private function setReferralAssigneeBasedOnRouting($referralRoutingRulesInstance, $organization, $referral, $studentId = null)
    {
        if ($referralRoutingRulesInstance) {
            if ($referralRoutingRulesInstance->getIsPrimaryCoordinator()) {
                //assign to central
                $referral->setPersonAssignedTo(null);
            } else if ($referralRoutingRulesInstance->getIsPrimaryCampusConnection()) {
                $this->setPrimaryAssignee($organization, $referral, $studentId);
            } else {
                $activityRefPerson = $referralRoutingRulesInstance->getPerson()->getId();
                $personAssignTo = $this->personService->findPerson($activityRefPerson);
                $featurePermission = $this->orgPermissionsetService->getStudentFeature($studentId, $activityRefPerson);
                $receiveReferralPermissionArray = $this->getDirectAndReasonRoutedPerm($featurePermission);
                $receiveReferral = false;
                if (isset($receiveReferralPermissionArray['receive_referrals'])) {
                    $receiveReferral = $receiveReferralPermissionArray['receive_referrals'];
                }
                if (!$receiveReferral) {
                    $referral->setPersonAssignedTo(null);
                } else if ($receiveReferral && !$receiveReferralPermissionArray['reason_routed_referral']) {
                    $referral->setPersonAssignedTo(null);
                } else {
                    $referral->setPersonAssignedTo($personAssignTo);
                }
            }
        }
    }


    public function setPrimaryAssignee($organization, $referral, $studentId)
    {
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(ReferralConstant::ORG_PERSON_STUDENT_REPO);
        $studentDetails = $this->orgPersonStudentRepository->findOneBy(array(
            'organization' => $organization,
            'person' => $studentId
        ));
        if ($studentDetails->getPersonIdPrimaryConnect()) {
            $personAssignTo = $this->personService->findPerson($studentDetails->getPersonIdPrimaryConnect());
            $personPrimryConnect = $personAssignTo->getId();
            $featurePermission = $this->orgPermissionsetService->getStudentFeature($studentId, $personPrimryConnect);
            $hasReceiveRefferlPerm = $this->getDirectAndReasonRoutedPerm($featurePermission);
            $receiveReferral = false;
            if (isset($hasReceiveRefferlPerm['receive_referrals'])) {
                $receiveReferral = $hasReceiveRefferlPerm['receive_referrals'];
            }
            if (!$receiveReferral) {
                $referral->setPersonAssignedTo(null);
            } else {
                $referral->setPersonAssignedTo($personAssignTo);
            }
        } else {
            // Implementation for bulk, but should take care of individual referral assigned to primary connection
            // which does not exist
            // if there is no primary campus connection
            // assign it to central coordinator
            $this->assignReferralToCentralCoordinator($referral);
        }
    }


    private function setPersonAssignTo($referralsDTO, $referral, $studentId = null, $organization = null)
    {
        $assignTo = $referralsDTO->getAssignedToUserId();
        $personFacultyRepo = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgPersonFaculty');
        if ($assignTo > 0) {
            // Lookup faculty
            $personAssignedTo = $this->personService->findPerson($assignTo);
            $inactiveFaculty = $personFacultyRepo->findOneBy(['person' => $personAssignedTo, 'status' => 0]);
            if ($inactiveFaculty) {
                throw new ValidationException([
                    'Assign TO faculty is Inactive'
                ], 'Assign TO faculty is Inactive', "ref_faculty_inactive");
            }
            if (!$this->rbacManager->checkAccessToStudent($studentId, $assignTo)) {
                $this->logger->error(" Referral Service - Do not have permission to student -" . $studentId);
                throw new AccessDeniedException(self::REFERRAL_VIEW_EXCEPTION);
            }
            $referral->setPersonAssignedTo($personAssignedTo);
        } elseif ($assignTo == -1) {
            // Lookup Primary Campus Connection
            $this->setPrimaryAssignee($organization, $referral, $studentId);
        } else {
            // If Assign to is zero "0"
            // Assign to central coordinator
            // As per design the assign to faculty id will be null in the DB in this case
            $activityRef = $this->referralRoutingRulesRepository->findOneBy(array(
                ReferralConstant::ORGANIZATION => $organization,
                'activityCategory' => $referralsDTO->getReasonCategorySubitemId()
            ));
            if ($activityRef->getIsPrimaryCoordinator()) {
                $this->assignReferralToCentralCoordinator($referral);
            } elseif ($activityRef->getIsPrimaryCampusConnection()) {
                $this->setPrimaryAssignee($organization, $referral, $studentId);
            } else {
                $activityRefPerson = $activityRef->getPerson()->getId();
                $personAssignTo = $this->personService->findPerson($activityRefPerson);

                $featurePermission = $this->orgPermissionsetService->getStudentFeature($studentId, $activityRefPerson);
                $hasReceiveRefferlPerm = $this->getDirectAndReasonRoutedPerm($featurePermission);
                $receiveReferral = false;
                if (isset($hasReceiveRefferlPerm['receive_referrals'])) {
                    $receiveReferral = $hasReceiveRefferlPerm['receive_referrals'];
                }
                if (!$receiveReferral) {
                    $referral->setPersonAssignedTo(null);
                } else if ($receiveReferral && !$hasReceiveRefferlPerm['reason_routed_referral']) {
                    $referral->setPersonAssignedTo(null);
                } else {
                    $referral->setPersonAssignedTo($personAssignTo);
                }
            }
        }
        return $referral;
    }


    /**
     * Nulls out the personAssignedTo field on the referral passed in, which will then ensure it's assigned to the
     * central coordinator.
     *
     * @param Referrals $referral
     */
    private function assignReferralToCentralCoordinator($referral)
    {
        // Nothing to do as the design is keep the db column as null
        // if the referral is assigned to central coordinator
        $referral->setPersonAssignedTo(null);
    }


    /**
     * Returns the token values for the email template.
     *
     * @param Person $person
     * @param Person $studentDetails
     * @param int $organizationId
     * @param int $languageId
     * @param \DateTime $referralCreatedDate
     * @return array
     */
    private function setTokenValues($person, $studentDetails, $organizationId, $languageId, $referralCreatedDate)
    {
        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
        $tokenValues[ReferralConstant::EMAIL_SKY_LOGO] = "";
        if ($systemUrl) {
            $tokenValues[ReferralConstant::EMAIL_SKY_LOGO] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
        }
        $tokenValues["first_name"] = $person->getFirstname();
        $tokenValues["last_name"] = $person->getLastname();
        $tokenValues[ReferralConstant::FIELD_STUDENTNAME] = $studentDetails->getFirstname() . " " . $studentDetails->getLastname();

        $dateCreated = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, $referralCreatedDate, 'm/d/Y h:ia T');
        $tokenValues["date_of_creation"] = $dateCreated;

        $dashboardUrl = $this->ebiConfigService->generateCompleteUrl('Staff_ReferralPage', $organizationId);

        if ($dashboardUrl) {
            $tokenValues['staff_referralpage'] = $dashboardUrl;
        } else {
            $tokenValues['staff_referralpage'] = "";
        }


        $primaryCoordinatorObject = $this->personService->getFirstPrimaryCoordinatorPerson($organizationId);

        if ($primaryCoordinatorObject) {
            $coordinatorEmail = $primaryCoordinatorObject->getUserName();
            $tokenValues["coordinator_first_name"] = $primaryCoordinatorObject->getFirstname();
            $tokenValues["coordinator_last_name"] = $primaryCoordinatorObject->getLastname();
            $tokenValues["email_address"] = $coordinatorEmail;
            $tokenValues["title"] = $primaryCoordinatorObject->getTitle();
        }

        return $tokenValues;
    }


    private function checkIsCoordinator($orgId, $userId)
    {
        $this->logger->debug(" Check User is Coorindator of an Organization having Organization Id " . $orgId);
        $isCoordinator = $this->organizationRoleRepository->getUserCoordinatorRole($orgId, $userId);
        return $isCoordinator;
    }


    /**
     * Referral will be closed or reopened based on status
     *
     * @param ReferralsDTO $referralsDto
     * @param int $loggedInPersonId
     * @param int $referralId
     * @return Referrals
     */
    public function changeReferralStatus($referralsDto, $loggedInPersonId, $referralId)
    {
        $referral = $this->findReferral($referralId);

        //Frontend should be protecting us.  However, if somehow, the status is sent as the same as the current status, we don't want to do anything
        if ($referralsDto->getStatus() == $referral->getStatus()) {
            return $referral;
        }

        //Referral closing validations
        $this->validateClosingOrOpeningReferral($referral, $loggedInPersonId);

        $organization = $referral->getOrganization();
        $organizationId = $organization->getId();
        $organizationLang = $this->organizationService->getOrganizationDetailsLang($organizationId);

        $activityCategory = $this->activityCategoryRepository->find($referral->getActivityCategory());
        $activityCategoryLangObject = $this->activityCategoryLangRepository->findOneBy(array(ReferralConstant::ACTIVITY_CATEGORY_ID => $activityCategory, ReferralConstant::LANG => $organizationLang->getLang()));

        $closerOrReopener = $this->personRepository->find($loggedInPersonId);

        if (!isset($closerOrReopener)) {
            throw new SynapseValidationException('Referral Updater not found');
        }

        $creator = $referral->getPersonFaculty();
        $student = $referral->getPersonStudent();


        $referral->setStatus($referralsDto->getStatus());
        $this->referralRepository->flush();

        if ($referralsDto->getStatus() == 'O') {
            $mapworksAction = 'reopen';
            $recipientType = 'reopener';
        } else {
            $mapworksAction = 'close';
            $recipientType = 'closer';
        }
        $referralHistoryObject = $this->createReferralHistoryRecord($referral, $mapworksAction, $closerOrReopener);

        $tokenValues = $this->mapReferralToTokenVariables($organizationId, $referral);

        $activityDescription = $activityCategoryLangObject->getDescription();

        $interestedPartyCommunicationSent = true;

        ///updating interested parties
        if (!empty($tokenValues['interested_parties'])) {
            foreach ($tokenValues['interested_parties'] as $interestedParty) {
                $interestedPartyTokenValues = array_merge($tokenValues, $interestedParty);
                $oneInterestedPartyCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, $mapworksAction, 'interested_party', 'referral', $interestedParty['$$interested_party_id$$'], $activityDescription, $referral, $referralHistoryObject, $interestedPartyTokenValues);
                if (!$oneInterestedPartyCommunicationSent) {
                    $interestedPartyCommunicationSent = false;
                }
            }
        }

        if ($referral->getIsReasonRouted()) {
            $assigneeId = $this->getReasonRoutedReferralAssignee($organization, $activityCategory, $student->getId(), true);
            $assignee = $this->personRepository->find($assigneeId);
            if ($assignee) {
                $tokenValues = array_merge($this->mapworksActionService->getTokenVariablesFromPerson('current_assignee', $assignee), $tokenValues);
            }
        } else {
            $assigneeId = $referral->getPersonAssignedTo()->getId();
        }

        //Closer/Reopener should get a notification but no email
        $closerOrReopenerId = $closerOrReopener->getId();
        $closerOrReOpenerCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, $mapworksAction, $recipientType, 'referral', $closerOrReopenerId, $activityDescription, $referral, $referralHistoryObject, $tokenValues);


        // If the Closer/Reopener and Assignee are the same , closer overrides assignee communication so it doesn't happen
        if ($closerOrReopenerId == $assigneeId) {
            $assigneeCommunicationSent = true;
        } else {
            // If referral is being reopened / closed, send an alert notification to the assignee.
            $assigneeCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, $mapworksAction, 'current_assignee', 'referral', $assigneeId, $activityDescription, $referral, $referralHistoryObject, $tokenValues);
        }

        // If the Closer/Reopener and Creator are the same, closer overrides creator communication so it doesn't happen
        $creatorId = $creator->getId();
        if ($closerOrReopenerId == $creatorId) {
            $creatorCommunicationSent = true;
        } else {
            $creatorCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, $mapworksAction, 'creator', 'referral', $creatorId, $activityDescription, $referral, $referralHistoryObject, $tokenValues);
        }

        if (!($creatorCommunicationSent && $assigneeCommunicationSent && $interestedPartyCommunicationSent && $closerOrReOpenerCommunicationSent)) {
            // What would be a better exception?  Tech Debt?  Create a new exception type?
            throw new SynapseValidationException("Expected Communication using Email or Notifications failed");
        }

        return $referral;
    }


    /**
     * Get the assignee's person ID for a reason routed referral.
     *
     * TODO: This logic is dispersed in some form or fashion throughout this entire class.
     * TODO: Please use this method going forward to determine the assignee for a reason routed referral.
     *
     * @param Organization $organization
     * @param ActivityCategory $activityCategory
     * @param int $studentId
     * @param boolean $throwException
     * @return int
     */
    private function getReasonRoutedReferralAssignee($organization, $activityCategory, $studentId, $throwException = false)
    {
        $organizationId = $organization->getId();
        $checkPermissionsFlag = false;

        //Get the referral routing rules for the passed in activity type.
        $referralRoutingRulesObject = $this->referralRoutingRulesRepository->findOneBy([
            'activityCategory' => $activityCategory,
            'organization' => $organization
        ]);

        //Get the type of referral routing being done.
        $isPrimaryCoordinatorRouted = $referralRoutingRulesObject->getIsPrimaryCoordinator();
        $isPrimaryCampusConnectionRouted = $referralRoutingRulesObject->getIsPrimaryCampusConnection();

        //If the referral is reason routed to the primary coordinator, get the primary coordinator's person ID.
        if ($isPrimaryCoordinatorRouted) {
            $assigneeId = $this->personService->getFirstPrimaryCoordinatorPersonId($organizationId);

            //If the referral is reason routed to the student's primary campus connection.
        } else if ($isPrimaryCampusConnectionRouted) {
            //Get the student's primary campus connection.
            $primaryCampusConnectionArray = $this->getPrimaryCampusConnection($organizationId, $studentId);

            //If there is a primary campus connection for the student, get that person's ID.
            // Otherwise, use the primary coordinator's ID.
            if ($primaryCampusConnectionArray) {
                $assigneeId = $primaryCampusConnectionArray['person_id'];
                $checkPermissionsFlag = true;
            } else {
                $assigneeId = $this->personService->getFirstPrimaryCoordinatorPersonId($organizationId);
            }

        } else {
            //The routing is designated to a faculty / staff member. Get that member's ID.
            $routingFacultyPersonObject = $referralRoutingRulesObject->getPerson();

            //If the faculty exists, get their person ID value and set the flag to check permissions.
            if ($routingFacultyPersonObject) {
                $assigneeId = $routingFacultyPersonObject->getId();
                $checkPermissionsFlag = true;
            } else {
                if ($throwException) {
                    throw new SynapseValidationException("There is not a person designated to receive referrals for this type of activity.");
                } else {
                    $assigneeId = null;
                }
            }
        }

        //If permissions need to be checked (The only case in which permissions are to be ignored is when the primary coordinator is the assignee)
        if ($checkPermissionsFlag) {
            //Get the feature permissions for the user.
            $featurePermissions = $this->orgPermissionsetService->getStudentFeature($studentId, $assigneeId);

            //Get the referral permissions for the user.
            $referralPermissions = $this->getDirectAndReasonRoutedPerm($featurePermissions);
            $receiveReferral = false;

            //If the referral permissions are set, get them and assign them to the variable.
            if (isset($referralPermissions['receive_referrals'])) {
                $receiveReferral = $referralPermissions['receive_referrals'];
            }

            //If the user does not have receive referral permissions, OR the user has receive referral permissions but
            // lacks reason routed referral permissions, send the referral to the primary coordinator.
            if (!$receiveReferral || ($receiveReferral && !$referralPermissions['reason_routed_referral'])) {
                $assigneeId = $this->personService->getFirstPrimaryCoordinatorPersonId($organizationId);
            }
        }

        return $assigneeId;
    }


    /**
     * Validation to ensure that only below person has permission to close the referral
     * 1) Person who created the referral.
     * 2) Person assigned the referral
     * 3) Coordinator
     *
     * @param Referrals $referral
     * @param int $referralCloserPersonId
     * @return null|ValidationException
     */
    private function validateClosingOrOpeningReferral($referral, $referralCloserPersonId)
    {
        $referralCreatorPersonId = $referral->getPersonIdFaculty()->getId();
        $referralAssigneePersonObject = $referral->getPersonAssignedTo();
        $hasCoordinatorAccess = $this->rbacManager->hasCoordinatorAccess();

        if ($referralAssigneePersonObject) {
            $referralAssigneePersonId = $referralAssigneePersonObject->getId();
        } else {
            $referralAssigneePersonId = null;
        }

        if ($referralCloserPersonId != $referralCreatorPersonId && $referralCloserPersonId != $referralAssigneePersonId && !$hasCoordinatorAccess) {
            throw new SynapseValidationException('Access denied. You do not have permission to close the referral.');
        }
    }


    /**
     * During referral creation or editing, gets the list of possible assignees or interested parties for the given student.
     * These are people connected to the student via courses or groups with appropriate permissions.
     * This function can include a primary campus connection and other campus connections.
     * It does not include campus resources or the referral coordinator (reason-routing entry).
     *
     * @param int $organizationId
     * @param int $facultyId
     * @param int $studentId
     * @return array
     * @throws AccessDeniedException
     */
    public function getReferralCampusConnections($organizationId, $facultyId, $studentId)
    {
        $dataToReturn = [];
        $dataToReturn['organization_id'] = $organizationId;
        $dataToReturn['student_id'] = $studentId;
        $dataToReturn['faculty_id'] = $facultyId;

        // Student participation check for current academic year.
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        $referralFeatureIsEnabled = $this->orgFeaturesRepository->isFeatureEnabledForOrganization($organizationId, 'Referrals');

        if ($referralFeatureIsEnabled) {
            $referralPermissions = $this->getReferralPermissions($organizationId, $facultyId, $studentId);

            if ($referralPermissions['private_create'] || $referralPermissions['teams_create'] || $referralPermissions['public_create']) {

                // Get an array containing all campus connections for this student that have receive referral permission
                $campusConnections = $this->referralRepository->getPossibleReferralAssigneesByStudent($studentId);

                $campusConnectionsToReturn = [];

                $primaryCampusConnection = $this->getPrimaryCampusConnection($organizationId, $studentId);

                // If the primary campus connection is in the campus connections list, don't include it in the campus connections list to avoid duplication.
                // If the primary campus connection is not in the campus connections list, then the primary campus connection is either not connected to the student,
                // doesn't have permission to receive referrals, or is invisible, so we won't list that person as a primary campus connection either.
                if ($primaryCampusConnection) {
                    $primaryCampusConnectionId = $primaryCampusConnection['person_id'];
                    $primaryCampusConnectionIsACampusConnection = false;

                    foreach ($campusConnections as $campusConnection) {
                        if ($campusConnection['person_id'] == $primaryCampusConnectionId) {
                            $primaryCampusConnectionIsACampusConnection = true;
                        } else {
                            $campusConnectionsToReturn[] = $campusConnection;
                        }
                    }

                    if (!$primaryCampusConnectionIsACampusConnection) {
                        $primaryCampusConnection = [];
                    }
                } else {
                    $campusConnectionsToReturn = $campusConnections;
                }

                $dataToReturn['primary_campus_connection'] = $primaryCampusConnection;
                $dataToReturn['campus_connections'] = $campusConnectionsToReturn;
            }
        }

        return $dataToReturn;
    }


    /**
     * Gets the campus resources able to receive referrals and available to the student,
     * as well as the reason routing entry being called "referral coordinator"
     *
     * @param int $organizationId
     * @param int $facultyId
     * @param int $studentId
     * @return array
     */
    public function getReferralCampusResources($organizationId, $facultyId, $studentId)
    {
        //check for non-participant student permissions
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        $referralCampusResourcesToReturn = [];
        $referralFeatureIsEnabled = $this->orgFeaturesRepository->isFeatureEnabledForOrganization($organizationId, 'Referrals');

        if ($referralFeatureIsEnabled) {
            $referralPermissions = $this->getReferralPermissions($organizationId, $facultyId, $studentId);

            $campusResources = [];
            $referralCoordinator = [];

            if ($referralPermissions['private_create'] || $referralPermissions['teams_create'] || $referralPermissions['public_create']) {
                $campusResources = $this->getCampusResourcesForReferralCreation($organizationId, [$studentId]);
            }

            if ($referralPermissions['reason_referrals_private_create'] || $referralPermissions['reason_referrals_teams_create'] || $referralPermissions['reason_referrals_public_create']) {
                $referralCoordinator = $this->getReasonRoutingEntry($organizationId);
            }

            $referralCampusResourcesToReturn['campus_resources'] = $campusResources;
            $referralCampusResourcesToReturn['reason_routing'] = $referralCoordinator;
        }

        return $referralCampusResourcesToReturn;
    }


    /**
     * Gets all possible referral assignees for a bulk action, including the primary campus connection, campus resources,
     * and reason routing (which gets called "Referral Coordinator")
     *
     * @param int $organizationId
     * @param int $facultyId
     * @param array $studentIds
     * @return array $referralCampusResourcesToReturn
     * @throws AccessDeniedException
     */
    public function getPossibleAssigneesForBulkAction($organizationId, $facultyId, $studentIds)
    {
        $referralCampusResourcesToReturn = [];

        // Student participation check for current academic year.
        $this->rbacManager->assertPermissionToEngageWithStudents($studentIds);

        $referralsAreEnabled = $this->orgFeaturesRepository->isFeatureEnabledForOrganization($organizationId, 'Referrals');

        if ($referralsAreEnabled) {
            $referralPermissions = $this->getReferralPermissions($organizationId, $facultyId);
            $primaryCampusConnectionReferralRoutingIsEnabled = $this->orgFeaturesRepository->isFeatureEnabledForOrganization($organizationId, 'Primary Campus Connection Referral Routing');

            $primaryCampusConnection = [];
            $campusResources = [];
            $referralCoordinator = [];

            if ($referralPermissions['public_create']) {
                if ($primaryCampusConnectionReferralRoutingIsEnabled) {
                    $primaryCampusConnection = [
                        'person_id' => -1,
                        'first_name' => 'Primary Campus',
                        'last_name' => 'Connection'
                    ];
                }

                $campusResources = $this->getCampusResourcesForReferralCreation($organizationId, $studentIds);
            }

            if ($referralPermissions['reason_referrals_public_create']) {
                $referralCoordinator = $this->getReasonRoutingEntry($organizationId);
            }

            $referralCampusResourcesToReturn['primary_campus_connection'] = $primaryCampusConnection;
            $referralCampusResourcesToReturn['campus_resources'] = $campusResources;
            $referralCampusResourcesToReturn['reason_routing'] = $referralCoordinator;
        }

        return $referralCampusResourcesToReturn;
    }


    /**
     * Gets a collection of flags for a given faculty member that represents either:
     * 1: The possible referral permission flags the faculty member has across all students they are connected with or
     * 2: The referral permission flags the faculty member has for the provided studentId
     *
     * Flags represented in the array, regardless of whether it's for one student or all students:
     *         [
     *          'private_create' => 1,
     *          'teams_create' => 1,
     *          'public_create' => 1,
     *          'public_view' => 1,
     *          'teams_view' => 1,
     *          'reason_referrals_private_create' => 1,
     *          'reason_referrals_teams_create' => 1,
     *          'reason_referrals_public_create' => 1,
     *          'reason_referrals_teams_view' => 1,
     *          'reason_referrals_public_view' => 1
     *         ]
     *
     * @param int $organizationId
     * @param int $facultyId
     * @param int|null $studentId
     * @return array
     */
    private function getReferralPermissions($organizationId, $facultyId, $studentId = null)
    {
        if ($studentId) {
            // TODO: Replace this with fixing the repository method to just return the IDs we need as a non-nested array.
            $permissionSetIdRows = $this->orgGroupFacultyRepository->getPermissionsByFacultyStudent($facultyId, $studentId);
            $permissionSetIds = array_column($permissionSetIdRows, 'org_permissionset_id');
        } else {
            $permissionSetIds = $this->orgGroupFacultyRepository->getAllPermissionSetsForFaculty($organizationId, $facultyId);
        }

        if (empty($permissionSetIds)) {
            throw new AccessDeniedException();
        }

        $featureMasterLangEntry = $this->featureMasterLangRepository->findOneBy(['featureName' => 'Referrals']);
        $referralFeatureId = $featureMasterLangEntry->getId();

        $referralPermissions = $this->orgPermissionsetFeaturesRepository->getFeaturePermissions($permissionSetIds, $organizationId, $referralFeatureId);

        return $referralPermissions;
    }


    /**
     * Get an array representing the primary campus connection for the given student
     *
     * TODO: Centralize this somewhere. This isn't particularly referrals specific (except the user_key)
     *
     * @param int $organizationId
     * @param int $studentId
     * @return array
     */
    private function getPrimaryCampusConnection($organizationId, $studentId)
    {
        $primaryCampusConnectionReferralRoutingIsEnabled = $this->orgFeaturesRepository->isFeatureEnabledForOrganization($organizationId, 'Primary Campus Connection Referral Routing');

        $primaryCampusConnection = [];
        if ($primaryCampusConnectionReferralRoutingIsEnabled) {
            $primaryCampusConnectionForStudent = $this->orgPersonStudentRepository->findOneBy(['person' => $studentId])->getPersonIdPrimaryConnect();
            if ($primaryCampusConnectionForStudent) {
                $primaryCampusConnection = [
                    'person_id' => $primaryCampusConnectionForStudent->getId(),
                    'first_name' => $primaryCampusConnectionForStudent->getFirstname(),
                    'last_name' => $primaryCampusConnectionForStudent->getLastname(),
                    'title' => $primaryCampusConnectionForStudent->getTitle(),
                    'user_key' => 'PCC-' . $primaryCampusConnectionForStudent->getId()
                ];
            }
        }

        return $primaryCampusConnection;
    }


    /**
     * Gets the available campus resources that can receive referrals for one or more students
     *
     * @param int $organizationId
     * @param array $studentIds
     * @return array
     */
    private function getCampusResourcesForReferralCreation($organizationId, $studentIds)
    {
        $campusResourcesToReturn = [];
        $campusResources = $this->orgCampusResourceRepository->getCampusResourcesForReferralCreation($organizationId, $studentIds);
        foreach ($campusResources as $campusResource) {
            $campusResourcesToReturn[] = [
                'person_id' => $campusResource['faculty_id'],
                'resource_name' => $campusResource['resource_name'],
                'first_name' => $campusResource['firstname'],
                'last_name' => $campusResource['lastname'],
                'user_key' => 'CR-' . $campusResource['faculty_id'] . '-' . $campusResource['resource_name']
            ];
        }

        return $campusResourcesToReturn;
    }


    /**
     * Get the reason routing entry given that the organization has reason routed referrals enabled
     *
     * @param int $organizationId
     * @return array
     */
    private function getReasonRoutingEntry($organizationId)
    {
        $reasonRouting = [];
        $reasonRoutingIsEnabled = $this->orgFeaturesRepository->isFeatureEnabledForOrganization($organizationId, 'Reason Routing');
        if ($reasonRoutingIsEnabled) {
            $reasonRouting = [
                'person_id' => 0,
                'first_name' => 'Referral',
                'last_name' => 'Coordinator'
            ];
        }
        return $reasonRouting;
    }


    /**
     * Mapping referral with token variables
     *
     * @param int $organizationId
     * @param Referrals $referralObject
     * @param ReferralHistory $mostRecentReferralHistoryObject | null $mostRecentReferralHistoryObject
     * @param int $previousReasonRoutedReferralAssignee
     * @param bool $isJob
     * @return array
     * @throws SynapseValidationException
     */
    public function mapReferralToTokenVariables($organizationId, $referralObject, $mostRecentReferralHistoryObject = null, $previousReasonRoutedReferralAssignee = null, $isJob = false)
    {
        $tokenValues = [];
        $shouldThrowException = ($isJob) ? false : true;

        $creator = $referralObject->getPersonFaculty();
        if ($creator) {
            $tokenValues = array_merge($tokenValues, $this->mapworksActionService->getTokenVariablesFromPerson('creator', $creator));
        }

        if ($referralObject->getIsReasonRouted()) {
            $assigneeId = $this->getReasonRoutedReferralAssignee($referralObject->getOrganization(), $referralObject->getActivityCategory(), $referralObject->getPersonStudent()->getId(), $shouldThrowException);
            $assignee = $this->personRepository->find($assigneeId);
            if (!$assignee && $shouldThrowException) {
                throw new SynapseValidationException('No Assignee for this referral exists. An Assignee is needed to complete this action.');
            }
        } else {
            $assignee = $referralObject->getPersonAssignedTo();
        }

        if ($assignee) {
            $tokenValues = array_merge($tokenValues, $this->mapworksActionService->getTokenVariablesFromPerson('current_assignee', $assignee));
        }

        $student = $referralObject->getPersonStudent();
        if ($student) {
            $tokenValues = array_merge($tokenValues, $this->mapworksActionService->getTokenVariablesFromPerson('student', $student));
        }
        if ($referralObject->getNotifyStudent()) {
            $tokenValues['$$student_dashboard$$'] = $this->ebiConfigService->getSystemUrl($organizationId);
        }

        $updater = $referralObject->getModifiedBy();
        if ($updater) {
            $tokenValues = array_merge($tokenValues, $this->mapworksActionService->getTokenVariablesFromPerson('updater', $updater));
        }

        $interestedParties = $this->interestedPartiesRepository->findBy([
            'referrals' => $referralObject
        ]);
        if ($interestedParties) {
            $tokenValues = array_merge($tokenValues, $this->getInterestedPartyTokenVariables($interestedParties));
        }

        if ($mostRecentReferralHistoryObject) {
            $mostRecentReferralHistoryObjectAssignee = ($mostRecentReferralHistoryObject->getPersonAssignedTo()) ? $mostRecentReferralHistoryObject->getPersonAssignedTo() : $previousReasonRoutedReferralAssignee;
            $tokenValues = array_merge($tokenValues, $this->mapworksActionService->getTokenVariablesFromPerson('previous_assignee', $mostRecentReferralHistoryObjectAssignee));
        }

        $primaryCoordinatorPersonObject = $this->personService->getFirstPrimaryCoordinatorPerson($organizationId);
        $tokenValues = array_merge($tokenValues, $this->mapworksActionService->getTokenVariablesFromPerson('coordinator', $primaryCoordinatorPersonObject));


        $referralCreatedDate = $referralObject->getCreatedAt();
        $dateCreated = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, $referralCreatedDate, SynapseConstant::DEFAULT_CSV_COLUMN_DATETIME_FORMAT);
        $tokenValues['$$date_of_creation$$'] = $dateCreated;

        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
        $tokenValues['$$Skyfactor_Mapworks_logo$$'] = "";
        if ($systemUrl) {
            $tokenValues['$$Skyfactor_Mapworks_logo$$'] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
        }

        $dashboardUrl = $this->ebiConfigService->generateCompleteUrl('Staff_ReferralPage', $organizationId);

        if ($dashboardUrl) {
            $tokenValues['$$staff_referralpage$$'] = $dashboardUrl;
        } else {
            $tokenValues['$$staff_referralpage$$'] = "";
        }

        return $tokenValues;
    }


    /**
     * Preparing token variables for interested parties
     *
     * @param array $interestedParties
     * @return array
     */
    public function getInterestedPartyTokenVariables($interestedParties)
    {
        $tokenVariables = [];
        if ($interestedParties && count($interestedParties) > 0) {
            foreach ($interestedParties as $interestedParty) {
                $interestedPartyArray = [];
                $interestedPartyPersonObject = $interestedParty->getPerson();
                if ($interestedPartyPersonObject) {
                    $interestedPartyArray = array_merge($interestedPartyArray, $this->mapworksActionService->getTokenVariablesFromPerson('interested_party', $interestedPartyPersonObject));
                    $interestedPartyArray['$$interested_party_id$$'] = $interestedPartyPersonObject->getId();
                }
                $tokenVariables['interested_parties'][] = $interestedPartyArray;
            }
        }

        return $tokenVariables;
    }


    /**
     * Determine mapworks action
     *
     * @param ReferralsDTO $referralDTO
     * @param Referrals $referral
     * @param int $assignTo
     * @param int $assignedId
     * @param bool $areInterestedPartiesAdded
     * @param bool $areInterestedPartiesRemoved
     * @return null|string
     */
    public function determineMapworksActionFromEditedReferral($referralDTO, $referral, $assignTo, $assignedId, $areInterestedPartiesAdded, $areInterestedPartiesRemoved)
    {
        if ($assignTo === $assignedId) {
            if ($this->didReferralContentChange($referralDTO, $referral)) {
                $mapworksAction = 'update_content';
            } elseif ($areInterestedPartiesAdded) {
                $mapworksAction = 'add_interested_party';
            } elseif ($areInterestedPartiesRemoved) {
                $mapworksAction = 'remove_interested_party';
            } else {
                //Nothing Changed, no action required
                $mapworksAction = null;
            }
        } else {
            $mapworksAction = 'reassign';
        }
        return $mapworksAction;
    }


    /** Create Referral History record based on referral object and action
     *
     * @param Referrals $referral
     * @param string $action
     * @param null|Person $loggedInPerson
     * @return ReferralHistory
     * @throws SynapseValidationException
     */
    public function createReferralHistoryRecord($referral, $action, $loggedInPerson = null)
    {

        //verifying referral object
        if (!is_object($referral)) {
            throw new SynapseValidationException("Cannot create referral history record, referral does not exist.");
        }

        $referralHistory = new ReferralHistory();
        $referralHistory->setAccessPrivate($referral->getAccessPrivate());
        $referralHistory->setAccessPublic($referral->getAccessPublic());
        $referralHistory->setAccessTeam($referral->getAccessTeam());
        $referralHistory->setAction($action);
        $referralHistory->setActivityCategory($referral->getActivityCategory());
        $referralHistory->setDiscussed($referral->getIsDiscussed());
        $referralHistory->setHighPriority($referral->getIsHighPriority());
        $referralHistory->setLeaving($referral->getIsLeaving());
        $referralHistory->setNote($referral->getNote());
        $referralHistory->setNotifyStudent($referral->getNotifyStudent());
        $referralHistory->setPersonAssignedTo($referral->getPersonAssignedTo());
        $referralHistory->setReasonRouted($referral->getIsReasonRouted());
        $referralHistory->setReferral($referral);
        $referralHistory->setReferrerPermission($referral->getReferrerPermission());
        $referralHistory->setUserKey($referral->getUserKey());
        $referralHistory->setStatus($referral->getStatus());
        if ($loggedInPerson) {
            $referralHistory->setCreatedBy($loggedInPerson);
        }
        $this->referralHistoryRepository->persist($referralHistory);
        return $referralHistory;
    }


    /**
     * Create Bulk referrals
     *
     * @param ReferralsDTO $referralsDTO
     * @return ReferralsDTO
     */
    public function createBulkReferral($referralsDTO)
    {
        // call job
        $job = new BulkReferralJob();
        $jobNumber = uniqid();
        $job->args = array(
            'jobNumber' => $jobNumber,
            'referralDto' => serialize($referralsDTO)
        );
        $this->resque->enqueue($job, true);
        return $referralsDTO;
    }


    /**
     *  Sends notification to student when a new referral is created
     *
     * @param Referrals $referral
     * @param $tokenValues
     * @param $notificationReason
     * @param ReferralHistory | string $referralHistoryObject
     * @return bool |array
     */
    public function sendCreateReferralCommunicationToStudent($referral, $tokenValues, $notificationReason, $referralHistoryObject)
    {
        $errorInCommunication = [];
        // If notify student then only notification will create for student
        $organizationId = $referral->getOrganization()->getId();
        $studentId = $referral->getPersonIdStudent()->getId();
        $studentCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, 'create', 'student', 'referral', $studentId, $notificationReason, $referral, $referralHistoryObject, $tokenValues);
        if (!$studentCommunicationSent) {
            $errorInCommunication[] = 'Email or Notification failed for students';
        }
        if (!empty($errorInCommunication)) {
            return $errorInCommunication;
        } else {
            return true;
        }
    }


    /**
     * Send Referral Email/Notification to Assignee, creator, student and interested parties , for single referrals only
     *
     * @param Referrals $referral
     * @param ActivityCategory $activityCategory
     * @param int $assignTo
     * @param array $tokenValues
     * @param string $notificationReason
     * @param null|ReferralHistory $referralHistory
     * @return bool|array
     */
    public function sendCreateReferralCommunication($referral, $activityCategory, $assignTo, $tokenValues, $notificationReason, $referralHistory = null)
    {
        $organizationObject = $referral->getOrganization();
        $organizationId = $organizationObject->getId();
        $studentId = $referral->getPersonIdStudent()->getId();
        $errorInCommunication = [];

        $creatorId = $referral->getPersonFaculty()->getId();
        // Send notification to creator
        $creatorCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, 'create', 'creator', 'referral', $creatorId, $notificationReason, $referral, $referralHistory, $tokenValues);

        if (!$creatorCommunicationSent) {
            $errorInCommunication[] = 'Email or Notification failed for creators';
        }
        //updating interested parties
        if (isset($tokenValues['interested_parties'])) {
            foreach ($tokenValues['interested_parties'] as $interestedParty) {
                $interestedPartyTokenValues = array_merge($tokenValues, $interestedParty);

                // Send Notification to interested parties.
                $oneInterestedPartyCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, 'create', 'interested_party', 'referral', $interestedParty['$$interested_party_id$$'], $notificationReason, $referral, $referralHistory, $interestedPartyTokenValues);
                if (!$oneInterestedPartyCommunicationSent) {
                    $errorInCommunication[] = 'Email or Notification failed for interested parties';
                }
            }
        }
        if ($referral->getIsReasonRouted()) {
            $assignTo = $this->getReasonRoutedReferralAssignee($organizationObject, $activityCategory, $studentId, true);
        }
        // Send notification to assignees.
        if ($assignTo != $creatorId) {
            $assigneeCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, 'create', 'current_assignee', 'referral', $assignTo, $notificationReason, $referral, $referralHistory, $tokenValues);
            if (!$assigneeCommunicationSent) {
                $errorInCommunication[] = 'Email or Notification failed for assignee';
            }
        }
        if (!empty($errorInCommunication)) {
            return $errorInCommunication;
        } else {
            return true;
        }
    }


    /**
     * Send Bulk referral email/notification to creator, assignee and interested parties.
     *
     * @param int $organizationId
     * @param array $tokenValues
     * @param array $creatorArrayWithReferralCount
     * @param array $assigneeArrayWithReferralCount
     * @param array $interestedPartyArrayWithReferralCount
     * @param Referrals $referral
     * @param ReferralHistory $referralHistory
     * @param array $interestedPartiesIdentifyingTokens -  this would be an empty array if there are no interested parties. Example array:
     *          [
     *              Interested Party Id => [
     *                  "$$interested_party_first_name$$" => Interested party's name,
     *                  "$$interested_party_last_name$$" => Interested party's name,
     *                  "$$interested_party_title$$" => Interested party's title
     *              ]
     *          ]
     *
     * @return bool|array
     */
    public function sendBulkCreateReferralCommunication($organizationId, $tokenValues, $creatorArrayWithReferralCount, $assigneeArrayWithReferralCount, $interestedPartyArrayWithReferralCount, $referral, $referralHistory, $interestedPartiesIdentifyingTokens = [])
    {
        $errorInCommunication = [];
        $notificationReason = null; // For bulk action notification reason will be empty
        // Send email notification to creator
        foreach ($creatorArrayWithReferralCount as $creatorId => $creatorReferralCount) {
            $tokenValues['$$referral_student_count$$'] = $creatorReferralCount;
            $creatorBulkCommunicationsSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, 'bulk_action', 'creator', 'referral', $creatorId, $notificationReason, $referral, $referralHistory, $tokenValues);
            if (!$creatorBulkCommunicationsSent) {
                $errorInCommunication[] = 'Email or Notification failed for creator - ' . $creatorId;
            }
        }
        //Send email notification to assignee
        foreach ($assigneeArrayWithReferralCount as $assigneeId => $assigneeReferralCount) {
            $tokenValues['$$referral_student_count$$'] = $assigneeReferralCount;
            $assigneeBulkCommunicationsSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, 'bulk_action', 'current_assignee', 'referral', $assigneeId, $notificationReason, $referral, $referralHistory, $tokenValues);
            if (!$assigneeBulkCommunicationsSent) {
                $errorInCommunication[] = 'Email or Notification failed for assignee - ' . $assigneeId;
            }
        }
        // Send email notification to interested parties.

        foreach ($interestedPartyArrayWithReferralCount as $interestedPartyId => $interestedPartyReferralCount) {
            $tokenValues['$$referral_student_count$$'] = $interestedPartyReferralCount;
            $interestedPartyTokenVariableArray = array_merge($tokenValues, $interestedPartiesIdentifyingTokens[$interestedPartyId]);
            $interestedPartyBulkCommunicationsSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, 'bulk_action', 'interested_party', 'referral', $interestedPartyId, $notificationReason, $referral, $referralHistory, $interestedPartyTokenVariableArray);
            if (!$interestedPartyBulkCommunicationsSent) {
                $errorInCommunication[] = 'Email or Notification failed for interested parties - ' . $interestedPartyId;
            }
        }
        if (!empty($errorInCommunication)) {
            return $errorInCommunication;
        } else {
            return true;
        }
    }


    /**
     * Send communication status related to referrals When student participation status updates
     *
     * @param int $studentId
     * @param int $organizationId
     * @param string $mapworksAction
     * @param  Person $loggedInPerson
     * @param null $isJob
     * @return bool
     */
    public function sendCommunicationsRelatedToReferralsUponStudentParticipationStatusUpdate($studentId, $organizationId, $mapworksAction, $loggedInPerson = null, $isJob = null)
    {
        $interestedPartyCommunicationSent = false;
        $creatorCommunicationSent = false;
        $assigneeCommunicationSent = false;

        $openReferrals = $this->referralRepository->findBy(['personStudent' => $studentId, 'status' => 'O']);
        if (empty($openReferrals)) {
            return true;
        }

        foreach ($openReferrals as $openReferral) {
            $tokenValues = $this->mapReferralToTokenVariables($organizationId, $openReferral);

            // If referral is being reopened / closed, send an alert notification to the assignee.
            $creatorId = $openReferral->getPersonFaculty()->getId();

            $organization = $this->organizationService->find($organizationId);
            $activityCategory = $openReferral->getActivityCategory();
            if ($openReferral->getIsReasonRouted()) {
                $shouldThrowException = ($isJob) ? false : true;
                $assigneeId = $this->getReasonRoutedReferralAssignee($organization, $activityCategory, $studentId, $shouldThrowException);
            } else {
                if ($openReferral->getPersonAssignedTo()) {
                    $assigneeId = $openReferral->getPersonAssignedTo()->getId();
                } else {
                    // if PersonAssignedTo is null in case of job, ignore that otherwise throw an exception
                    if ($isJob) {
                        continue;
                    } else {
                        throw new SynapseValidationException("No Assignee for this referral exists. An Assignee is needed to complete this action.");
                    }
                }
            }

            $referralHistory = $this->createReferralHistoryRecord($openReferral, $mapworksAction, $loggedInPerson);
            $assigneeCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, $mapworksAction, 'current_assignee', 'referral', $assigneeId, null, $openReferral, $referralHistory, $tokenValues);

            if ($assigneeId != $creatorId) {
                $creatorCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, $mapworksAction, 'creator', 'referral', $creatorId, null, $openReferral, $referralHistory, $tokenValues);
            } else {
                $creatorCommunicationSent = true;
            }

            //updating interested parties
            $interestedPartyCommunicationSent = true;
            if (!empty($tokenValues['interested_parties'])) {
                foreach ($tokenValues['interested_parties'] as $interestedParty) {
                    $interestedPartyTokenValues = array_merge($tokenValues, $interestedParty);
                    $oneInterestedPartyCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, $mapworksAction, 'interested_party', 'referral', $interestedParty['$$interested_party_id$$'], null, $openReferral, $referralHistory, $interestedPartyTokenValues);
                    if (!$oneInterestedPartyCommunicationSent) {
                        $interestedPartyCommunicationSent = false;
                    }
                }
            }

        }

        if ($interestedPartyCommunicationSent && $creatorCommunicationSent && $assigneeCommunicationSent) {
            return true;
        }
        return false;
    }
}
