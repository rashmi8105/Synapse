<?php
namespace Synapse\CoreBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use DateTime;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\Email;
use Synapse\CoreBundle\Entity\EmailTeams;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\job\BulkEmailJob;
use Synapse\CoreBundle\Repository\ActivityCategoryLangRepository;
use Synapse\CoreBundle\Repository\ActivityCategoryRepository;
use Synapse\CoreBundle\Repository\EmailRepository;
use Synapse\CoreBundle\Repository\EmailTeamsRepository;
use Synapse\CoreBundle\Repository\EmailTemplateRepository;
use Synapse\CoreBundle\Repository\FeatureMasterLangRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetFeaturesRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\TeamMembersRepository;
use Synapse\CoreBundle\Repository\TeamsRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\ActivityLogDto;
use Synapse\RestBundle\Entity\EmailDto;
use Synapse\RestBundle\Entity\RelatedActivitiesDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;
use Synapse\RestBundle\Entity\TeamIdsDto;
use Synapse\RestBundle\Exception\ValidationException;


/**
 * @DI\Service("email_activity_service")
 */
class EmailActivityService extends AbstractService
{

    const SERVICE_KEY = 'email_activity_service';

    const GROUP_ITEMKEY = 'group_item_key';

    const EMAIL_ID = 'emailId';


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

    // Services

    /**
     * @var ActivityLogService
     */
    private $activityLogService;

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

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
    private $loggerhelperService;

    /**
     * @var OrganizationService
     */
    private $orgService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var RelatedActivitiesService
     */
    private $relatedActivitiesService;

    // Repositories

    /**
     * @var ActivityCategoryRepository
     */
    private $activityRepository;

    /**
     * @var ActivityCategoryLangRepository
     */
    private $activityCategoryLangRepository;
       /**
     * @var EmailRepository
     */
    private $emailRepository;

    /**
     * @var EmailTemplateRepository
     */
    private $emailTemplateRepository;

    /**
     * @var FeatureMasterLangRepository
     */
    private $featureMasterLangRepository;

    /**
     * @var MetadataListValuesRepository
     */
    private $metaDataListValuesRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrganizationlangRepository
     */
    private $organizationLangRepository;

    /**
     * @var OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;

    /**
     * @var OrgPermissionsetFeaturesRepository
     */
    private $orgPermissionsetFeaturesRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var TeamMembersRepository
     */
    private $teamMembersRepository;

    /**
     * EmailActivityService constructor.
     *
     * @param $logger @DI\InjectParams({
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

        //Scaffolding
        $this->container = $container;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);

        //Services
        $this->activityLogService = $this->container->get(ActivityLogService::SERVICE_KEY);
        $this->alertNotificationService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->featureService = $this->container->get(FeatureService::SERVICE_KEY);
        $this->loggerhelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);
        $this->orgService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->relatedActivitiesService = $this->container->get(RelatedActivitiesService::SERVICE_KEY);

        //Repositories
        $this->activityRepository = $this->repositoryResolver->getRepository(ActivityCategoryRepository::REPOSITORY_KEY);
        $this->activityCategoryLangRepository = $this->repositoryResolver->getRepository(ActivityCategoryLangRepository::REPOSITORY_KEY);
        $this->emailRepository = $this->repositoryResolver->getRepository(EmailRepository::REPOSITORY_KEY);
        $this->emailTeamsRepository = $this->repositoryResolver->getRepository(EmailTeamsRepository::REPOSITORY_KEY);
        $this->emailTemplateRepository = $this->repositoryResolver->getRepository(EmailTemplateRepository::REPOSITORY_KEY);
        $this->featureMasterLangRepository = $this->repositoryResolver->getRepository(FeatureMasterLangRepository::REPOSITORY_KEY);
        $this->metaDataListValuesRepository = $this->repositoryResolver->getRepository(MetadataListValuesRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->organizationLangRepository = $this->repositoryResolver->getRepository(OrganizationlangRepository::REPOSITORY_KEY);
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgGroupFacultyRepository::REPOSITORY_KEY);
        $this->orgPermissionsetFeaturesRepository = $this->repositoryResolver->getRepository(OrgPermissionsetFeaturesRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->teamMembersRepository = $this->repositoryResolver->getRepository(TeamMembersRepository::REPOSITORY_KEY);
        $this->teamsRepository = $this->repositoryResolver->getRepository(TeamsRepository::REPOSITORY_KEY);
    }

    /**
     * Create email for students
     *
     * @param EmailDto $emailDto
     * @param boolean $isJob
     * @return EmailDto $emailDto
     * @throws AccessDeniedException
     */
    public function createEmail(EmailDto $emailDto, $isJob = false)
    {
        $this->validateEmail($emailDto);
        // Create email for multiple students
        $personStudentIds = $emailDto->getPersonStudentId();
        $personStudentIds = explode(',', $personStudentIds);

        $organizationId = $emailDto->getOrganizationId();
        $staffId = $emailDto->getPersonStaffId();

        // check for non participant student
        $this->rbacManager->assertPermissionToEngageWithStudents($personStudentIds, $staffId);
        if (count($personStudentIds) > 1 && !$isJob) {
            // call job
            $job = new BulkEmailJob();

            $jobNumber = uniqid();

            $job->args = array(
                'jobNumber' => $jobNumber,
                'emailDto' => serialize($emailDto)
            );
            $this->resque->enqueue($job, true);
        } else {

            $organizationSetInEmailDto = $this->organizationRepository->find($organizationId);
            if (!$organizationSetInEmailDto) {
                throw new SynapseValidationException('Organization not found.');
            }

            $shareOptionPermission = $this->getShareOptionPermission($emailDto, 'email');
            $date = new \DateTime('now');
            $personStaff = $this->personRepository->find($staffId);
            if (!$personStaff) {
                throw new ValidationException([
                    'Person Not Found.'
                ], 'Person Not Found.', 'person_not_found');
            }
            $activityCategory = $this->activityRepository->find($emailDto->getReasonCategorySubitemId());
            $this->isActivityCategoryExists($activityCategory);
            $organizationAttachedToThePersonStaffInTheEmailDto = $personStaff->getOrganization();
            $organizationLangDetails = $this->orgService->getOrganizationDetailsLang($organizationAttachedToThePersonStaffInTheEmailDto);
            $this->isOrgLangExists($organizationLangDetails);

            $teamShare = $emailDto->getShareOptions()[0]->getTeamsShare();
            $teamsArray = $emailDto->getShareOptions()[0]->getTeamIds();
            $facultyId = $personStaff->getId();
            $reasonText = $activityCategory->getShortName();
            $emailBCCSent = false;
            $sentStudentCount = 0;

            $feature = $this->featureMasterLangRepository->findOneBy(['featureName' => 'Email']);
            $featureId = $feature->getId();
            foreach ($personStudentIds as $personStudentId) {
                $email = new Email();
                $email->setOrganization($organizationSetInEmailDto);
                $studentId = $personStudentId;
                $personStudent = $this->personRepository->find($studentId);
                if (!$personStudent) {
                    throw new ValidationException([
                        'Person Not Found.'
                    ], 'Person Not Found.', 'person_not_found');
                }
                $featureAccess = $this->featureService->verifyFacultyAccessToStudentForFeature($staffId, $organizationId, $studentId, $shareOptionPermission, $featureId);
                if (!$featureAccess) {
                    if ($isJob) {
                        continue;
                    } else {
                        throw new AccessDeniedException('You do not have permission to create email');
                    }
                }

                //Last contact date  should only be set for  interaction contacts
                $email->setPersonIdStudent($personStudent);
                $email->setPersonIdFaculty($personStaff);
                $email->setActivityCategory($activityCategory);
                $subject = $emailDto->getEmailSubject();
                $email->setEmailSubject($subject);
                $emailBCC = $emailDto->getEmailBccList();
                $email->setEmailBccList($emailBCC);
                $emailBody = $emailDto->getEmailBody();
                $email->setEmailBody($emailBody);
                $email->setAccessPrivate($emailDto->getShareOptions()[0]
                    ->getPrivateShare());
                $email->setAccessPublic($emailDto->getShareOptions()[0]
                    ->getPublicShare());
                $email->setAccessTeam($teamShare);
                $email = $this->emailRepository->createEmail($email);
                $this->isEmailCreated($email);
                if ($teamShare && $email) {
                    $this->addTeam($email, $teamsArray);
                }

                $currentDateTime = new DateTime();
                $currentOrgDateTime = $this->dateUtilityService->getTimezoneAdjustedCurrentDateTimeForOrganization($organizationId);
                $lastActivity = $currentOrgDateTime->format('m/d/y') . "- Email";
                $personStudent->setLastActivity($lastActivity);
                $this->emailRepository->flush();
                $emailDto->setEmailId($email->getId());
                // send Email
                if ($subject) {
                    $emailMessage = [];
                    $emailMessage['subject'] = $subject;
                    $emailMessage['email_body'] = $emailBody;
                    $emailMessage['email_key'] = "Email_Notification_Staff_to_Student";
                    $emailMessage['email_bcc'] = $emailBCC;
                    $emailMessage['replyTo'] = $emailDto->getEmail();
                    $this->sendEmail($personStudent, $personStaff, $emailMessage, $emailBCCSent);
                    $emailBCCSent = true;
                }
                $activityLogDto = new ActivityLogDto();
                $activityLogDto->setActivityDate($date);
                $activityLogDto->setActivityType("E");
                $emailId = $emailDto->getEmailId();
                $activityLogDto->setEmail($emailId);
                $activityLogDto->setOrganization($organizationId);
                $activityLogDto->setPersonIdFaculty($facultyId);
                $studentId = $personStudent->getId();
                $activityLogDto->setPersonIdStudent($studentId);
                $activityLogDto->setReason($reasonText);
                $this->activityLogService->createActivityLog($activityLogDto);
                $activityLogId = $emailDto->getActivityLogId();
                if (isset($activityLogId)) {
                    $relatedActivitiesDto = new RelatedActivitiesDto();
                    $relatedActivitiesDto->setActivityLog($activityLogId);
                    $relatedActivitiesDto->setEmail($emailDto->getEmailId());
                    $relatedActivitiesDto->setOrganization($organizationId);
                    $this->relatedActivitiesService->createRelatedActivities($relatedActivitiesDto);
                }
                $sentStudentCount++;
            }
            // After finishing bulk action send notification to logged in person
            if ($isJob) {
                $this->alertNotificationService->createNotification('bulk-action-completed', 'The ' . $sentStudentCount . ' emails you sent have completed successfully ', $personStaff, null, null, null, null, null, null, null, null, null, null, null, null, $email);
            }
        }


        $this->logger->info(">>>> Email Created Successfully");
        return $emailDto;
    }
    private function validateEmail($emailDto){

        $personStudentIds = $emailDto->getPersonStudentId();
        if (trim($personStudentIds) == '') {
            $this->logger->error("From field is required." );
            throw new ValidationException([
                'From field is required.'
            ], 'From field is required.', 'email_from_field is required');
        }

        $subject = $emailDto->getEmailSubject();
        if (trim($subject) == '') {
            $this->logger->error("Subject field is required." );
            throw new ValidationException([
                'Subject field is required.'
            ], 'Subject field is required.', 'subject_from_field is required');
        }
    }

    /**
     * add email to teams
     *
     * @param string $email
     * @param array $teamsArray
     * @return string
     */
    private function addTeam($email, $teamsArray)
    {

        $emailsTeams = '';
        foreach ($teamsArray as $team) {
            if ($team->getIsTeamSelected()) {
                $emailsTeam = new EmailTeams();
                $team = $this->teamsRepository->find($team->getId());
                $this->isTeamExists($team);
                $emailsTeam->setEmailId($email);
                $emailsTeam->setTeamsId($team);
                $emailsTeams = $this->emailTeamsRepository->createEmailTeams($emailsTeam);
            }
        }
        return $emailsTeams;
    }

    /**
     * Gets the email activity details
     *
     * @param int $emailId -  Email Activity Id
     * @return EmailDto
     */
    public function viewEmail($emailId)
    {
        $email = $this->emailRepository->find($emailId);
        if(!$email){
            throw new SynapseValidationException('EmailId not found');
        }

        $studentId = $email->getPersonIdStudent()->getId();

        // check for non participant student
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        // check if contact has is team accessed
        if (!$this->rbacManager->hasAssetAccess([
            'email-public-view',
            'email-private-view',
            'email-teams-view'
        ], $email)
        ) {
            throw new AccessDeniedException('Do not have permission to view email');
        }

        $emailDto = new EmailDto();
        $emailDto->setEmailId($emailId);
        $organizationId = $email->getOrganization()->getId();
        $emailDto->setOrganizationId($organizationId);
        $emailDto->setPersonStudentId($studentId);
        $facultyId = $email->getPersonIdFaculty()->getId();
        $emailDto->setPersonStaffId($facultyId);

        $personFaculty = $this->personRepository->find($facultyId);
        if(!$personFaculty){
            throw new SynapseValidationException('Faculty not found');
        }

        $name = $personFaculty->getFirstname() . ' ' . $personFaculty->getLastname();
        $emailDto->setEmail($name);

        $emailDto->setReasonCategorySubitemId($email->getActivityCategory()
            ->getId());

        $organizationLangObject = $this->organizationLangRepository->findOneBy(array(
            'organization' => $organizationId
        ));

        $organizationLangId = $organizationLangObject->getLang()->getId();
        $this->isOrgLangExists($organizationLangId);

        $activityCategory = $this->activityRepository->find($emailDto->getReasonCategorySubitemId());
        $this->isActivityCategoryExists($activityCategory);
        $activityCategoryObject = $this->activityCategoryLangRepository->findOneBy(array(
            'activityCategoryId' => $activityCategory,
            'language' => $organizationLangObject->getLang()
        ));
        if ($activityCategoryObject) {
            $emailDto->setReasonCategorySubitem($activityCategoryObject->getDescription());
        }

        $emailDto->setEmailBccList($email->getEmailBccList());
        $emailDto->setEmailSubject($email->getEmailSubject());
        $emailDto->setEmailBody($email->getEmailBody());

        $teamDtoData = array();
        $emailTeam = $this->emailTeamsRepository->findBy([
            self::EMAIL_ID => $email
        ]);
        $emailTeamIds = array();
        $shareOptionsDto = new ShareOptionsDto();
        $shareOptionsDto->setPublicShare($email->getAccessPublic());
        $shareOptionsDto->setPrivateShare($email->getAccessPrivate());
        $teamShare = $email->getAccessTeam();
        $shareOptionsDto->setTeamsShare($teamShare);
        foreach ($emailTeam as $emailTeam) {
            $emailTeamIds[] = $emailTeam->getTeamsId()->getId();
        }

        $teams = $this->teamMembersRepository->getTeams($facultyId);
        if ($teamShare && !empty($teams)) {
            foreach ($teams as $team) {
                $teamId = $team['team_id'];
                $teamDto = new TeamIdsDto();
                $teamDto->setId($teamId);
                $teamDto->setTeamName($team['team_name']);
                if (in_array($teamId, $emailTeamIds)) {
                    $teamDto->setIsTeamSelected(true);
                } else {
                    $teamDto->setIsTeamSelected(false);
                }
                $teamDtoData[] = $teamDto;
            }
        }
        $shareOptionsDto->setTeamIds($teamDtoData);
        $shareOptionsDtoResponse[] = $shareOptionsDto;
        $emailDto->setShareOptions($shareOptionsDtoResponse);
        return $emailDto;
    }

    /**
     * Deletes an email
     *
     * @param int $emailId
     * @return mixed
     */
    public function deleteEmail($emailId)
    {
        $email = $this->findEmail($emailId);
        $studentId = $email->getPersonIdStudent()->getId();

        // check for non participant student
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        $emailTeam = $this->emailTeamsRepository->findBy([
            'emailId' => $emailId
        ]);
        if (isset($emailTeam)) {
            foreach ($emailTeam as $team) {
                $this->emailTeamsRepository->deleteEmailTeam($team);
            }
        }
        $this->emailRepository->deleteEmail($email);
        $this->activityLogService->deleteActivityLogByType($emailId, 'E');
        $this->emailRepository->flush();
        return $emailId;
    }

    /**
     * find email by id
     *
     * @param object|int $id
     * @return null|object
     */
    public function findEmail($id)
    {
        if(is_object($id)){
            $emailId = $id->getId();
        }else{
            $emailId = $id;
        }
        $this->logger->debug(">>>>Finding Email" . $emailId);
        $email = $this->emailRepository->find($emailId);
        if (! $email) {
            $this->logger->error("Email Service - Find Email - " . "Email Id" . $emailId . "Not Found");
            throw new ValidationException([
                'email Not Found.'
            ], 'email Not Found.', 'email_not_found');
        }
        return $email;
    }

    /**
     * Arrange data to send email and create notification that email is being sent.
     *
     * @param Person $personStudent
     * @param Person $personFaculty
     * @param array $emailMessage
     */
    private function sendEmail($personStudent, $personFaculty, $emailMessage, $emailBCCSent = false)
    {
        $emailTemplate = $this->emailTemplateRepository->findOneBy(['emailKey' => $emailMessage['email_key']]);
        $organizationId = $personFaculty->getOrganization()->getId();

        if (isset($personStudent)) {
            $studentEmail = $personStudent->getUsername();

            if ($studentEmail && $studentEmail != null) {
                $emailBody = $emailMessage['email_body'];
                $emailKey = $emailMessage['email_key'];
                //if $emailBCCSent is false, include bcc email first time only
                $bcc = (! $emailBCCSent)? implode(',',array_unique(explode(',',$emailMessage['email_bcc']))) : '';
                $subject = $emailMessage['subject'];
                $from = $emailTemplate->getFromEmailAddress();
                $replyTo = $emailMessage['replyTo'];

                $notification = array(
                    'from' => $from,
                    'subject' => $subject,
                    'bcc' => $bcc,
                    'body' => nl2br($emailBody),
                    'to' => $studentEmail,
                    'emailKey' => $emailKey,
                    'organizationId' => $organizationId,
                    'replyTo' => $replyTo
                );

                $emailNotificationsDTO = $this->emailService->sendEmailNotification($notification);
                $this->emailService->sendEmail($emailNotificationsDTO, true, $personFaculty);

            }
        }
        $this->logger->info(">>>> Email sent to person_id: " . $personStudent->getId() . " (student) from person_id: " . $personFaculty->getId() . " (faculty)");
    }

    public function getOrganizationLang($orgId)
    {
        $this->logger->debug(">>>>Get Organization Lang" . $orgId);
        return $this->orgService->getOrganizationDetailsLang($orgId);
    }

    private function isActivityCategoryExists($activityCategory)
    {
        if (! $activityCategory) {
            throw new ValidationException([
                'Reason category not found.'
            ], 'Reason category not found.', 'reason_category_not_found');
        }
        return $activityCategory;
    }

    private function isOrgLangExists($orgLang)
    {
        if (! $orgLang) {
            throw new ValidationException([
                'Organization language not found.'
            ], 'Organization language not found.', 'organization_language_not_found.');
        }
        return $orgLang;
    }


    private function isTeamExists($team)
    {
        if (! $team) {
            throw new ValidationException([
                'Team not found.'
            ], 'Team not found.', 'team_not_found');
        }
    }

    private function isEmailCreated($email)
    {
        if (! $email) {
            throw new ValidationException([
                'Email not created.'
            ], 'Email not created.', 'email_not_created.');
        }
    }
}