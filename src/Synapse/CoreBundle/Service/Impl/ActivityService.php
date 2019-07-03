<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\Appointments;
use Synapse\CoreBundle\Entity\Contacts;
use Synapse\CoreBundle\Entity\Note;
use Synapse\CoreBundle\Entity\Referrals;
use Synapse\CoreBundle\Repository\ActivityLogRepository;
use Synapse\CoreBundle\Repository\AppointmentsRepository;
use Synapse\CoreBundle\Repository\AppointmentsTeamsRepository;
use Synapse\CoreBundle\Repository\ContactsRepository;
use Synapse\CoreBundle\Repository\ContactsTeamsRepository;
use Synapse\CoreBundle\Repository\NoteRepository;
use Synapse\CoreBundle\Repository\NoteTeamsRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\ReferralRepository;
use Synapse\CoreBundle\Repository\ReferralRoutingRulesRepository;
use Synapse\CoreBundle\Repository\ReferralsInterestedPartiesRepository;
use Synapse\CoreBundle\Repository\ReferralsTeamsRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Repository\SearchRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\StudentConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\RestBundle\Exception\ValidationException;


/**
 * @DI\Service("activity_service")
 */
class ActivityService extends AbstractService
{
    const SERVICE_KEY = 'activity_service';


    // This would be the list of  features available
    private $featureListArray = [
        'Referrals',
        'Referrals Reason Routed',
        'Log Contacts',
        'Booking',
        'Notes',
        'Email'
    ];

    /*
     * Default permission array , if there is no permissions found for a feature, this permission array would be assigned for that feature (i.e no permissions)
     * This would ensure that there are no notices in the logs
     */
    private $defaultPermissionArray = [
        'public_view' => 0,
        'team_view' => 0
    ];



    //Scaffolding
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Manager
     */
    public $rbacManager;


    //services

    /**
     * @var OrgPermissionsetService
     */
    private $orgPermissionSetService;

    //Repositories
    /**
     *
     * @var ActivityLogRepository
     */
    private $activityLogRepository;

    /**
     * @var AppointmentsRepository
     */
    private $appointmentsRepository;

    /**
     * @var AppointmentsTeamsRepository
     */
    private $appointmentsTeamsRepository;

    /**
     * @var ContactsRepository
     */
    private $contactRepository;

    /**
     * @var ContactsTeamsRepository
     */
    private $contactsTeamsRepository;

    /**
     * @var NoteRepository
     */
    private $noteRepository;

    /**
     * @var NoteTeamsRepository
     */
    private $noteTeamsRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var ReferralRepository
     */
    private $referralRepository;

    /**
     * @var ReferralsInterestedPartiesRepository
     */
    private $referralsInterestedPartiesRepository;

    /**
     * @var ReferralsTeamsRepository
     */
    private $referralsTeamsRepository;

    /**
     * @var SearchRepository
     */
    private $searchRepository;

    /**
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
     *
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        //Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->rbacManager = $this->container->get(SynapseConstant::TINYRBAC_MANAGER);


        //Services
        $this->orgPermissionSetService = $this->container->get(OrgPermissionsetService::SERVICE_KEY);

        //Repositories
        $this->activityLogRepository = $this->repositoryResolver->getRepository(ActivityLogRepository::REPOSITORY_KEY);
        $this->appointmentsRepository = $this->repositoryResolver->getRepository(AppointmentsRepository::REPOSITORY_KEY);
        $this->appointmentsTeamsRepository = $this->repositoryResolver->getRepository(AppointmentsTeamsRepository::REPOSITORY_KEY);
        $this->contactRepository = $this->repositoryResolver->getRepository(ContactsRepository::REPOSITORY_KEY);
        $this->contactsTeamsRepository = $this->repositoryResolver->getRepository(ContactsTeamsRepository::REPOSITORY_KEY);
        $this->noteRepository = $this->repositoryResolver->getRepository(NoteRepository::REPOSITORY_KEY);
        $this->noteTeamsRepository = $this->repositoryResolver->getRepository(NoteTeamsRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->referralRepository = $this->repositoryResolver->getRepository(ReferralRepository::REPOSITORY_KEY);
        $this->referralsInterestedPartiesRepository = $this->repositoryResolver->getRepository(ReferralsInterestedPartiesRepository::REPOSITORY_KEY);
        $this->referralsTeamsRepository = $this->repositoryResolver->getRepository(ReferralsTeamsRepository::REPOSITORY_KEY);
        $this->searchRepository = $this->repositoryResolver->getRepository(SearchRepository::REPOSITORY_KEY);

    }

    private function getIntActivity($perArr){
        $perArrInt = [];
        if(in_array('"E"', $perArr)){
            foreach ($perArr as $value){
                if($value != '"E"'){
                    $perArrInt[] = $value;
                }
            }
            $actArr = implode(",", $perArrInt);
        }
        else {
            $actArr = implode(",", $perArr);
        }

        return $actArr;
    }

    /**
     * Get All activities for the student respect to faculty
     *
     * @param integer $studentId
     * @param integer $orgId
     * @param boolean $isInteraction
     * @param integer $facultyId
     * @return array
     */
    public function getAllActivities($studentId, $orgId, $isInteraction, $facultyId)
    {
        $this->logger->debug(">>>>Get All Activities" . "StudentID" . $studentId . "OrganizationID" . $orgId . "Is Interaction" . $isInteraction . "FacultyId" . $facultyId);
        $perArr = $this->getPermission($orgId, $facultyId);
        $sharingAccess = $this->getSharingAccess($facultyId, $studentId);
        $actArr = implode(",", $perArr);

        $coordinatorRoleIds = $this->organizationRoleRepository->getCoordinatorRoleID();
        $coordinatorRoleIds = array_map('current', $coordinatorRoleIds);
        if (empty($coordinatorRoleIds)) {
            // TODO: We should never need the default value of '1' here, but can't remove it right now because of unknown bad things happening if we do.
            // Technical debt ticket: ESPRJ-10590
            $coordinatorRoleIds = [1];
        }

        $searchKey = 'Activity_All';

        if ($isInteraction) {
            $searchKey .= "_Interaction";
            $actArr = $this->getIntActivity($perArr);
        }

        $tokenValues = array(
            StudentConstant::STUD_ID => $studentId,
            'activityArr' => $actArr,
            StudentConstant::FACULTY => $facultyId,
            StudentConstant::ORGID => $orgId,
            StudentConstant::NOTE_TEAM_ACCESS => $sharingAccess[StudentConstant::NOTES][StudentConstant::TEAM_VIEW],
            'notePublicAccess' => $sharingAccess[StudentConstant::NOTES][StudentConstant::PUBLIC_VIEW],
            'contactTeamAccess' => $sharingAccess[StudentConstant::LOG_CONTACTS][StudentConstant::TEAM_VIEW],
            'contactPublicAccess' => $sharingAccess[StudentConstant::LOG_CONTACTS][StudentConstant::PUBLIC_VIEW],
            'referralTeamAccess' => $sharingAccess[StudentConstant::REFERRALS][StudentConstant::TEAM_VIEW],
            'referralPublicAccess' => $sharingAccess[StudentConstant::REFERRALS][StudentConstant::PUBLIC_VIEW],
            'referralPublicAccessReasonRouted' => $sharingAccess[StudentConstant::REFERRALS_REASON_ROUTED][StudentConstant::PUBLIC_VIEW],
            'referralTeamAccessReasonRouted' => $sharingAccess[StudentConstant::REFERRALS_REASON_ROUTED][StudentConstant::TEAM_VIEW],
            'appointmentTeamAccess' => $sharingAccess[StudentConstant::BOOKING][StudentConstant::TEAM_VIEW],
            'appointmentPublicAccess' => $sharingAccess[StudentConstant::BOOKING][StudentConstant::PUBLIC_VIEW],
            'emailTeamAccess' => $sharingAccess[StudentConstant::EMAIL][StudentConstant::TEAM_VIEW],
            'emailPublicAccess' => $sharingAccess[StudentConstant::EMAIL][StudentConstant::PUBLIC_VIEW],
            'roleIds' => $coordinatorRoleIds
        );

        if ($isInteraction) {
            $activityArr = $this->activityLogRepository->getActivityAllInteraction($tokenValues);
        } else {
            $activityArr = $this->activityLogRepository->getActivityAll($tokenValues);
        }

        $dataArr = array();
        foreach ($activityArr as $cntArr) {

            if ($cntArr[StudentConstant::ACTIVITY_TYPE] == "N") {
                $cntArr[StudentConstant::ACTIVITY_ID] = $cntArr['NoteId'];
                $cntArr[StudentConstant::ACTIVITY_TYPE] = "Note";
                $cntArr[StudentConstant::ACTIVITY_CONTACT_TYPE_ID] = "";
                $cntArr[StudentConstant::ACTIVITY_CONTACT_TYPE_TEXT] = "";
                $cntArr[StudentConstant::ACTIVITY_REFERRAL_STATUS] = "";
                $cntArr[StudentConstant::ACTIVITY_DESCRIPTION] = $cntArr['noteDescription'];

                $cntArr[StudentConstant::RELATED_ACTIVITY] = $this->getRelatedActivities($cntArr[StudentConstant::ACTIVITY_LOG_ID], $isInteraction, $orgId, $facultyId);
            }
            if ($cntArr[StudentConstant::ACTIVITY_TYPE] == "R") {
                $cntArr[StudentConstant::ACTIVITY_ID] = $cntArr['ReferralId'];
                $cntArr[StudentConstant::ACTIVITY_TYPE] = "Referral";
                $cntArr[StudentConstant::ACTIVITY_CONTACT_TYPE_ID] = "";
                $cntArr[StudentConstant::ACTIVITY_CONTACT_TYPE_TEXT] = "";
                $cntArr[StudentConstant::ACTIVITY_DESCRIPTION] = $cntArr['referralDescription'];
                if (!isset($cntArr[StudentConstant::ACTIVITY_REFERRAL_STATUS])) {
                    $cntArr[StudentConstant::ACTIVITY_REFERRAL_STATUS] = '';
                }
                $cntArr[StudentConstant::RELATED_ACTIVITY] = $this->getRelatedActivities($cntArr[StudentConstant::ACTIVITY_LOG_ID], $isInteraction, $orgId, $facultyId);
            }
            if ($cntArr[StudentConstant::ACTIVITY_TYPE] == "A") {
                $cntArr['activity_date'] = (isset($cntArr['app_created_date'])) ? $cntArr['app_created_date'] : '';
                $cntArr[StudentConstant::ACTIVITY_ID] = $cntArr['AppointmentId'];
                $cntArr[StudentConstant::ACTIVITY_TYPE] = "Appointment";
                $cntArr[StudentConstant::ACTIVITY_CONTACT_TYPE_ID] = "";
                $cntArr[StudentConstant::ACTIVITY_CONTACT_TYPE_TEXT] = "";
                $cntArr[StudentConstant::ACTIVITY_REFERRAL_STATUS] = "";
                $cntArr[StudentConstant::ACTIVITY_DESCRIPTION] = $cntArr['appointmentDescription'];
                // finding out the related Activities
                $cntArr[StudentConstant::RELATED_ACTIVITY] = $this->getRelatedActivities($cntArr[StudentConstant::ACTIVITY_LOG_ID], $isInteraction, $orgId, $facultyId);
            }
            if ($cntArr[StudentConstant::ACTIVITY_TYPE] == "C") {
                $cntArr['activity_date'] = (isset($cntArr['contact_created_date'])) ? $cntArr['contact_created_date'] : '';
                $cntArr[StudentConstant::ACTIVITY_ID] = $cntArr['ContactId'];
                $cntArr[StudentConstant::ACTIVITY_TYPE] = StudentConstant::CONTACT;
                $cntArr[StudentConstant::ACTIVITY_REFERRAL_STATUS] = '';
                $cntArr[StudentConstant::ACTIVITY_DESCRIPTION] = $cntArr['contactDescription'];
                $cntArr[StudentConstant::RELATED_ACTIVITY] = $this->getRelatedActivities($cntArr[StudentConstant::ACTIVITY_LOG_ID], $isInteraction, $orgId, $facultyId);
            }
            if ($cntArr[StudentConstant::ACTIVITY_TYPE] == "E") {
                $cntArr[StudentConstant::ACTIVITY_ID] = $cntArr['EmailId'];
                $cntArr[StudentConstant::ACTIVITY_TYPE] = StudentConstant::EMAIL;
                $cntArr[StudentConstant::ACTIVITY_REFERRAL_STATUS] = '';
                $cntArr[StudentConstant::ACTIVITY_DESCRIPTION] = $cntArr['activity_email_subject'];
                $cntArr['email_subject'] = $cntArr['activity_email_subject'];
                $cntArr['email_body'] = $cntArr['activity_email_body'];
                $cntArr[StudentConstant::RELATED_ACTIVITY] = $this->getRelatedActivities($cntArr[StudentConstant::ACTIVITY_LOG_ID], $isInteraction, $orgId, $facultyId);
            }

            if (isset($cntArr['act_date'])) {
                $cntArr['activity_date'] = $cntArr['act_date'];
            }
            $dataArr[] = $cntArr;
        }

        return $dataArr;
    }

    public function getStudentNotes($studentId, $orgId, $isInteraction, $facultyId)
    {
        $this->logger->debug(">>>>Get Student Notes" . "StudentID" . $studentId . "OrganizationID" . $orgId . "FacultyId" . $facultyId);
        $perArr = $this->getPermission($orgId, $facultyId, 'N');
        $sharingAccess = $this->getSharingAccess($facultyId, $studentId);

        $this->activityLogRepository = $this->repositoryResolver->getRepository(StudentConstant::ACTIVITY_LOG_SERVICE);

        $searchKey = 'Activity_Note';
        $tokenValues = array(
            StudentConstant::STUD_ID => $studentId,
            StudentConstant::FACULTY => $facultyId,
            StudentConstant::ORGID => $orgId,
            StudentConstant::PUBLIC_ACCESS => $sharingAccess[StudentConstant::NOTES][StudentConstant::PUBLIC_VIEW],
            StudentConstant::TEAM_ACCESS => $sharingAccess[StudentConstant::NOTES][StudentConstant::TEAM_VIEW]
        );
        $query = $this->getEbiSearchQuery($searchKey, $tokenValues);
        $searchRepository = $this->repositoryResolver->getRepository(StudentConstant::EBI_SEARCH_REPO);
        $notesArr = $searchRepository->getQueryResult($query);
        $finalReturnArr = array();
        foreach ($notesArr as $notes) {
            $notes[StudentConstant::ACTIVITY_TYPE] = "Note";
            $notes[StudentConstant::ACTIVITY_CONTACT_TYPE_ID] = "";
            $notes[StudentConstant::ACTIVITY_CONTACT_TYPE_TEXT] = "";
            $notes[StudentConstant::ACTIVITY_REFERRAL_STATUS] = "";
            $notes[StudentConstant::RELATED_ACTIVITY] = $this->getRelatedActivities($notes[StudentConstant::ACTIVITY_LOG_ID], $isInteraction, $orgId, $facultyId);
            $finalReturnArr[] = $notes;
        }
        $this->logger->info(">>>>Get Student Notes");
        return $finalReturnArr;
    }

    /**
     * Email Activity need to write logic
     * @param unknown $studentId
     * @param unknown $orgId
     * @param unknown $isInteraction
     * @param unknown $facultyId
     * @return multitype:
     */
    public function getStudentEmailList($studentId, $orgId, $isInteraction, $facultyId)
    {
        $this->logger->debug(">>>> Get Student Email" . "StudentID - " . $studentId );
        $perArr = $this->getPermission($orgId, $facultyId, 'E');
        $sharingAccess = $this->getSharingAccess($facultyId, $studentId);

        $this->activityLogRepository = $this->repositoryResolver->getRepository(StudentConstant::ACTIVITY_LOG_SERVICE);

        if (! $isInteraction) {
            $searchKey = 'Activity_Email';
        } else {
            $searchKey = 'Activity_Email';
        }
        $tokenValues = array(
            StudentConstant::STUD_ID => $studentId,
            'facultyId' => $facultyId,
            StudentConstant::ORGID => $orgId,
            StudentConstant::PUBLIC_ACCESS => $sharingAccess[StudentConstant::EMAIL][StudentConstant::PUBLIC_VIEW],
            StudentConstant::TEAM_ACCESS => $sharingAccess[StudentConstant::EMAIL][StudentConstant::TEAM_VIEW]
        );

        $query = $this->getEbiSearchQuery($searchKey, $tokenValues);

        $searchRepository = $this->repositoryResolver->getRepository(StudentConstant::EBI_SEARCH_REPO);
        $emailArr = $searchRepository->getQueryResult($query);
        $finalReturnArr = array();
        foreach ($emailArr as $email) {
            $email[StudentConstant::ACTIVITY_TYPE] = StudentConstant::EMAIL;
            $email[StudentConstant::ACTIVITY_REFERRAL_STATUS] = '';
            $email['activity_email_subject'] = $email['activity_email_subject'];
            $email['activity_email_body'] = $email['activity_email_body'];
            $email[StudentConstant::RELATED_ACTIVITY] = $this->getRelatedActivities($email[StudentConstant::ACTIVITY_LOG_ID], $isInteraction, $orgId, $facultyId);
            $finalReturnArr[] = $email;
        }
        $this->logger->info(">>>> Get Student Email");

        return $finalReturnArr;
    }

    public function getStudentAppointmentList($studentId, $orgId, $isInteraction, $facultyId, $sharingViewAccess)
    {
        $this->logger->debug(">>>> Get StudentAppointment List" . "StudentID" . $studentId . "OrganizationID" . $orgId . "Is Interaction" . $isInteraction . "FacultyId" . $facultyId);
        $perArr = $this->getPermission($orgId, $facultyId, 'A');
        $this->activityLogRepository = $this->repositoryResolver->getRepository(StudentConstant::ACTIVITY_LOG_SERVICE);

        $appArr = $this->activityLogRepository->getStudentAppointments($studentId, $orgId, $facultyId, $sharingViewAccess);

        $finalReturnArr = array();
        foreach ($appArr as $app) {
            $app[StudentConstant::ACTIVITY_TYPE] = "Appointment";
            $app[StudentConstant::ACTIVITY_CONTACT_TYPE_ID] = "";
            $app[StudentConstant::ACTIVITY_CONTACT_TYPE_TEXT] = "";
            $app[StudentConstant::ACTIVITY_REFERRAL_STATUS] = "";
            $app[StudentConstant::RELATED_ACTIVITY] = $this->getRelatedActivities($app[StudentConstant::ACTIVITY_LOG_ID], $isInteraction, $orgId, $facultyId);
            $finalReturnArr[] = $app;
        }
        $this->logger->info(">>>> Get StudentAppointment List");
        return $finalReturnArr;
    }

    public function getStudentReferralList($studentId, $orgId, $isInteraction, $facultyId)
    {
        $this->logger->debug(">>>> Get StudentReferral List" . "StudentID" . $studentId . "OrganizationID" . $orgId . "Is Interaction" . $isInteraction . "FacultyId" . $facultyId);
        $perArr = $this->getPermission($orgId, $facultyId, 'R');
        $this->activityLogRepository = $this->repositoryResolver->getRepository(StudentConstant::ACTIVITY_LOG_SERVICE);
        $sharingAccess = $this->getSharingAccess($facultyId, $studentId);

        $coordinatorRoleIds = $this->organizationRoleRepository->getCoordinatorRoleID();
        $coordinatorRoleIds = array_map('current', $coordinatorRoleIds);
        if (empty($coordinatorRoleIds)) {
            // TODO: We should never need the default value of '1' here, but can't remove it right now because of unknown bad things happening if we do.
            // Technical debt ticket: ESPRJ-10590
            $coordinatorRoleIds = [1];
        }

        $searchKey = 'Activity_Referral';
        $tokenValues = array(
            StudentConstant::STUD_ID => $studentId,
            StudentConstant::FACULTY => $facultyId,
            StudentConstant::ORGID => $orgId,
            StudentConstant::PUBLIC_ACCESS => $sharingAccess[StudentConstant::REFERRALS][StudentConstant::PUBLIC_VIEW],
            StudentConstant::TEAM_ACCESS => $sharingAccess[StudentConstant::REFERRALS][StudentConstant::TEAM_VIEW],
            StudentConstant::PUBLIC_ACCESS_REASON_ROUTED => $sharingAccess[StudentConstant::REFERRALS_REASON_ROUTED][StudentConstant::PUBLIC_VIEW],
            StudentConstant::TEAM_ACCESS_REASON_ROUTED => $sharingAccess[StudentConstant::REFERRALS_REASON_ROUTED][StudentConstant::TEAM_VIEW],
            'roleIds' => $coordinatorRoleIds
        );

        $referrals = $this->activityLogRepository->getActivityReferral($tokenValues);
        $finalReturnArr = array();
        foreach ($referrals as $ref) {
            $ref[StudentConstant::ACTIVITY_TYPE] = "Referral";
            $ref[StudentConstant::ACTIVITY_CONTACT_TYPE_ID] = "";
            $ref[StudentConstant::ACTIVITY_CONTACT_TYPE_TEXT] = "";
            if (! isset($ref[StudentConstant::ACTIVITY_REFERRAL_STATUS])) {
                $ref[StudentConstant::ACTIVITY_REFERRAL_STATUS] = '';
            }
            $ref[StudentConstant::RELATED_ACTIVITY] = $this->getRelatedActivities($ref[StudentConstant::ACTIVITY_LOG_ID], $isInteraction, $orgId, $facultyId);
            $finalReturnArr[] = $ref;
        }
        $this->logger->info(">>>> Get StudentReferral List");
        return $finalReturnArr;
    }

    public function getStudentContacts($studentId, $orgId, $isInteraction, $facultyId)
    {
        $this->logger->debug(">>>> Get Student Contacts" . "StudentID" . $studentId . "OrganizationID" . $orgId . "Is Interaction" . $isInteraction . "FacultyId" . $facultyId);
        $perArr = $this->getPermission($orgId, $facultyId, 'C');
        $sharingAccess = $this->getSharingAccess($facultyId, $studentId);
        $this->activityLogRepository = $this->repositoryResolver->getRepository(StudentConstant::ACTIVITY_LOG_SERVICE);

        if (! $isInteraction) {
            $searchKey = 'Activity_Contact';
        } else {
            $searchKey = 'Activity_Contact_Interaction';
        }
        $tokenValues = array(
            StudentConstant::STUD_ID => $studentId,
            'facultyId' => $facultyId,
            StudentConstant::ORGID => $orgId,
            StudentConstant::PUBLIC_ACCESS => $sharingAccess[StudentConstant::LOG_CONTACTS][StudentConstant::PUBLIC_VIEW],
            StudentConstant::TEAM_ACCESS => $sharingAccess[StudentConstant::LOG_CONTACTS][StudentConstant::TEAM_VIEW]
        );

        $query = $this->getEbiSearchQuery($searchKey, $tokenValues);

        $searchRepository = $this->repositoryResolver->getRepository(StudentConstant::EBI_SEARCH_REPO);
        $contactsArr = $searchRepository->getQueryResult($query);
        $finalReturnArr = array();
        foreach ($contactsArr as $contact) {
            if(array_key_exists('contact_created_date', $contact)){
                $contact['activity_date'] = $contact['contact_created_date'];
            }else{
                $contact['activity_date'] = '';
            }
            $contact[StudentConstant::ACTIVITY_TYPE] = StudentConstant::CONTACT;
            $contact[StudentConstant::ACTIVITY_REFERRAL_STATUS] = '';
            $contact[StudentConstant::RELATED_ACTIVITY] = $this->getRelatedActivities($contact[StudentConstant::ACTIVITY_LOG_ID], $isInteraction, $orgId, $facultyId);
            $finalReturnArr[] = $contact;
        }

        $this->logger->info(">>>> Get Student Contacts");
        return $finalReturnArr;
    }

    public function getRelatedActivities($activityLogId, $isInteraction, $orgId, $facultyId)
    {
        $this->logger->debug(">>>> Get Related Activities" . "Activity Log Id" . $activityLogId . "Is Interaction" . $isInteraction . "Organization" . $orgId . "FacultyId" . $facultyId);
        $this->activityLogRepository = $this->repositoryResolver->getRepository(StudentConstant::ACTIVITY_LOG_SERVICE);
        $this->relatedActivityRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:RelatedActivities");
        $relateActivityArr = $this->relatedActivityRepository->findByActivityLog($activityLogId);
        if (! $relateActivityArr) {
            return null;
        }
        $returnActivityArr = array();

        foreach ($relateActivityArr as $relatedActivity) {
            $activityArr = array();

            $relatedActivityId = $relatedActivity->getId();
            $noteDetails = $relatedActivity->getNote();
            $contactDetails = $relatedActivity->getContacts();
            $appointmentDetails = $relatedActivity->getAppointment();
            $referralDetails = $relatedActivity->getReferrals();
            $emailDetails = $relatedActivity->getEmail();

            if ($noteDetails) {
                if ($noteDetails->getAccessPrivate() && $noteDetails->getPersonIdFaculty()->getId() != $facultyId) {
                    continue;
                }
                if (! $this->getPermission($orgId, $facultyId, 'N', false)) {
                    continue;
                }
                if ($noteDetails->getAccessTeam() && (! $this->rbacManager->hasTeamAccess($noteDetails, $facultyId))){
                    continue;
                }
                $noteId = $noteDetails->getId();
                $getNoteActivityLog = $this->activityLogRepository->findOneByNote($noteId);
                $activityArr[StudentConstant::RELATED_ACTIVITY_ID] = $relatedActivityId;
                $activityArr[StudentConstant::ACTIVITY_ID] = $noteId;
                $activityArr[StudentConstant::ACTIVITY_LOG_ID] = $getNoteActivityLog->getId();
                $activityArr[StudentConstant::ACTIVITY_DATE] = $noteDetails->getNoteDate();
                $activityArr[StudentConstant::ACTIVITY_TYPE] = "Note";
                $activityArr[StudentConstant::ACTIVITY_CREATED_BY_ID] = $noteDetails->getPersonIdFaculty()->getId();
                $activityArr[StudentConstant::ACTIVITY_CREATED_FIRST_NAME] = $noteDetails->getPersonIdFaculty()->getFirstname();
                $activityArr[StudentConstant::ACTIVITY_CREATED_LAST_NAME] = $noteDetails->getPersonIdFaculty()->getLastname();
                $activityArr[StudentConstant::ACTIVITY_REASON_ID] = $noteDetails->getActivityCategory()->getId();
                $activityArr[StudentConstant::ACTIVITY_REASON_TEXT] = $noteDetails->getActivityCategory()->getShortName();
                $activityArr[StudentConstant::ACTIVITY_DESCRIPTION] = $noteDetails->getNote();
                $activityArr[StudentConstant::ACTIVITY_REFERRAL_STATUS] = "";
                $activityArr[StudentConstant::RELATED_ACTIVITY] = self::getRelatedActivities($getNoteActivityLog->getId(), $isInteraction, $orgId, $facultyId);
            } elseif ($contactDetails) {

                if ($contactDetails->getAccessPrivate() && $contactDetails->getPersonIdFaculty()->getId() != $facultyId) {
                    continue;
                }
                if ($contactDetails->getAccessTeam() && (! $this->rbacManager->hasTeamAccess($contactDetails, $facultyId)) ){
                    continue;
                }
                if ($contactDetails && ($isInteraction) && ($contactDetails->getContactTypesId()
                    ->getParentContactTypesId()
                    ->getId() != 1)) {
                    continue;
                } else
                    if ($contactDetails && $this->getPermission($orgId, $facultyId, 'C', false)) {
                        $contactId = $contactDetails->getId();
                        $getContactActivityLog = $this->activityLogRepository->findOneByContacts($contactId);
                        $activityArr['activity_date'] = $contactDetails->getContactDate();
                        $activityArr[StudentConstant::RELATED_ACTIVITY_ID] = $relatedActivityId;
                        $activityArr[StudentConstant::ACTIVITY_LOG_ID] = $getContactActivityLog->getId();
                        $activityArr[StudentConstant::ACTIVITY_ID] = $contactId;
                        $activityArr[StudentConstant::ACTIVITY_DATE] = $contactDetails->getContactDate();
                        $activityArr[StudentConstant::ACTIVITY_TYPE] = StudentConstant::CONTACT;
                        $activityArr[StudentConstant::ACTIVITY_CREATED_BY_ID] = $contactDetails->getPersonIdFaculty()->getId();
                        $activityArr[StudentConstant::ACTIVITY_CREATED_FIRST_NAME] = $contactDetails->getPersonIdFaculty()->getFirstname();
                        $activityArr[StudentConstant::ACTIVITY_CREATED_LAST_NAME] = $contactDetails->getPersonIdFaculty()->getLastname();
                        $activityArr[StudentConstant::ACTIVITY_REASON_ID] = $contactDetails->getActivityCategory()->getId();
                        $activityArr[StudentConstant::ACTIVITY_REASON_TEXT] = $contactDetails->getActivityCategory()->getShortName();
                        $activityArr[StudentConstant::ACTIVITY_DESCRIPTION] = $contactDetails->getNote();
                        $activityArr[StudentConstant::ACTIVITY_CONTACT_TYPE_ID] = $contactDetails->getContactTypesId()->getId();
                        $contactLang = $this->repositoryResolver->getRepository("SynapseCoreBundle:ContactTypesLang");
                        $contactTypeId = $contactDetails->getContactTypesId()->getId();
                        $contactLangObj = $contactLang->findOneByContactTypesId($contactTypeId);
                        $activityArr[StudentConstant::ACTIVITY_CONTACT_TYPE_TEXT] = $contactLangObj->getDescription();
                        $activityArr[StudentConstant::ACTIVITY_REFERRAL_STATUS] = "";
                        $activityArr[StudentConstant::RELATED_ACTIVITY] = self::getRelatedActivities($getContactActivityLog->getId(), $isInteraction, $orgId, $facultyId);
                    } else {
                        continue;
                    }
            }elseif ($emailDetails) {
                if ($emailDetails->getAccessPrivate() && $emailDetails->getPersonIdFaculty()->getId() != $facultyId) {
                    continue;
                }
                if ($emailDetails->getAccessTeam() && (! $this->rbacManager->hasTeamAccess($emailDetails, $facultyId))){
                    continue;
                }
                if (! $this->getPermission($orgId, $facultyId, 'E', false)) {
                    continue;
                }
                $emailId = $emailDetails->getId();
                $getEmailActivityLog = $this->activityLogRepository->findOneByEmail($emailId);
                $activityArr[StudentConstant::RELATED_ACTIVITY_ID] = $relatedActivityId;
                $activityArr[StudentConstant::ACTIVITY_ID] = $emailId;
                $activityArr[StudentConstant::ACTIVITY_LOG_ID] = $getEmailActivityLog->getId();
                $activityArr[StudentConstant::ACTIVITY_DATE] = $emailDetails->getCreatedAt();
                $activityArr[StudentConstant::ACTIVITY_TYPE] = "Email";
                $activityArr[StudentConstant::ACTIVITY_CREATED_BY_ID] = $emailDetails->getPersonIdFaculty()->getId();
                $activityArr[StudentConstant::ACTIVITY_CREATED_FIRST_NAME] = $emailDetails->getPersonIdFaculty()->getFirstname();
                $activityArr[StudentConstant::ACTIVITY_CREATED_LAST_NAME] = $emailDetails->getPersonIdFaculty()->getLastname();
                $activityArr[StudentConstant::ACTIVITY_REASON_ID] = $emailDetails->getActivityCategory()->getId();
                $activityArr[StudentConstant::ACTIVITY_REASON_TEXT] = $emailDetails->getActivityCategory()->getShortName();
                $activityArr[StudentConstant::ACTIVITY_DESCRIPTION] = $emailDetails->getEmailSubject();
                $activityArr[StudentConstant::ACTIVITY_REFERRAL_STATUS] = "";
                $activityArr[StudentConstant::RELATED_ACTIVITY] = self::getRelatedActivities($getEmailActivityLog->getId(), $isInteraction, $orgId, $facultyId);
            } elseif ($appointmentDetails && $this->getPermission($orgId, $facultyId, 'A', false)) {
                if ($appointmentDetails->getAccessTeam() && (! $this->rbacManager->hasTeamAccess($appointmentDetails, $facultyId))){
                    continue;
                }
                $appointmentId = $appointmentDetails->getId();
                $getAppActivityLog = $this->activityLogRepository->findOneByAppointments($appointmentId);
                if($getAppActivityLog)
                {
                    $activityArr['activity_date'] = $appointmentDetails->getStartDateTime();
                    $activityArr[StudentConstant::RELATED_ACTIVITY_ID] = $relatedActivityId;
                    $activityArr[StudentConstant::ACTIVITY_LOG_ID] = $getAppActivityLog->getId();
                    $activityArr[StudentConstant::ACTIVITY_ID] = $appointmentId;
                    $activityArr[StudentConstant::ACTIVITY_TYPE] = "Appointment";
                    $activityArr[StudentConstant::ACTIVITY_CREATED_BY_ID] = $appointmentDetails->getPerson()->getId();
                    $activityArr[StudentConstant::ACTIVITY_CREATED_FIRST_NAME] = $appointmentDetails->getPerson()->getFirstname();
                    $activityArr[StudentConstant::ACTIVITY_CREATED_LAST_NAME] = $appointmentDetails->getPerson()->getLastname();
                    $activityArr[StudentConstant::ACTIVITY_REASON_ID] = $appointmentDetails->getActivityCategory()->getId();
                    $activityArr[StudentConstant::ACTIVITY_REASON_TEXT] = $appointmentDetails->getActivityCategory()->getShortName();
                    $activityArr[StudentConstant::ACTIVITY_DESCRIPTION] = $appointmentDetails->getDescription();
                    $activityArr[StudentConstant::ACTIVITY_CONTACT_TYPE_ID] = '';
                    $activityArr[StudentConstant::ACTIVITY_CONTACT_TYPE_TEXT] = "";
                    $activityArr[StudentConstant::ACTIVITY_REFERRAL_STATUS] = "";

                    $activityArr[StudentConstant::RELATED_ACTIVITY] = self::getRelatedActivities($getAppActivityLog->getId(), $isInteraction, $orgId, $facultyId);
                }

            } elseif ($referralDetails && $this->getPermission($orgId, $facultyId, 'R', false)) {

                if ($referralDetails->getAccessPrivate() && $referralDetails->getPersonIdFaculty()->getId() != $facultyId) {
                    continue;
                }
                if ($referralDetails->getAccessTeam() &&(! $this->rbacManager->hasTeamAccess($referralDetails, $facultyId))){
                    continue;
                }
                $referralId = $referralDetails->getId();
                $getRefActivityLog = $this->activityLogRepository->findOneByReferrals($referralId);
                $activityArr[StudentConstant::RELATED_ACTIVITY_ID] = $relatedActivityId;
                $activityArr[StudentConstant::ACTIVITY_LOG_ID] = $getRefActivityLog->getId();
                $activityArr[StudentConstant::ACTIVITY_ID] = $referralId;
                $activityArr[StudentConstant::ACTIVITY_TYPE] = "Referral";
                $activityArr[StudentConstant::ACTIVITY_CREATED_BY_ID] = $referralDetails->getPersonIdFaculty()->getId();
                $activityArr[StudentConstant::ACTIVITY_CREATED_FIRST_NAME] = $referralDetails->getPersonIdFaculty()->getFirstname();
                $activityArr[StudentConstant::ACTIVITY_CREATED_LAST_NAME] = $referralDetails->getPersonIdFaculty()->getLastname();
                $activityArr[StudentConstant::ACTIVITY_REASON_ID] = $referralDetails->getActivityCategory()->getId();
                $activityArr[StudentConstant::ACTIVITY_REASON_TEXT] = $referralDetails->getActivityCategory()->getShortName();
                $activityArr[StudentConstant::ACTIVITY_DESCRIPTION] = $referralDetails->getNote();
                $activityArr[StudentConstant::ACTIVITY_CONTACT_TYPE_ID] = '';
                $activityArr[StudentConstant::ACTIVITY_CONTACT_TYPE_TEXT] = "";
                $activityArr[StudentConstant::ACTIVITY_REFERRAL_STATUS] = $referralDetails->getStatus();
                $activityArr[StudentConstant::RELATED_ACTIVITY] = self::getRelatedActivities($getRefActivityLog->getId(), $isInteraction, $orgId, $facultyId);
            }
            if(isset($activityArr[StudentConstant::ACTIVITY_ID]))
            {
                $returnActivityArr[] = $activityArr;
            }
        }
        $this->logger->info(">>>> Get Related Activities");
        return $returnActivityArr;
    }

    public function getPermission($orgId, $facultyId, $category = null, $exception = true)
    {
        $this->logger->debug(">>>> Get Permission" . "Organization Id" . $orgId . "FacultyId" . $facultyId);
        $this->featureService = $this->container->get('feature_service');
        $this->personService = $this->container->get('person_service');
        $feature = $this->featureService->getFeaturesStatusByOrg($orgId);
        $person = $this->personService->find($facultyId);
        $loggedUser = $this->personService->getLoggedInUserDetails($person);
        $personPermission = $this->personService->getPersonPermission($facultyId, $loggedUser->getType(), $feature);

        $this->checkPermission($personPermission);
        $finalArr = $this->getFinalPerm($personPermission);
        if (! is_null($category)) {
            if (! in_array('"' . $category . '"', $finalArr) && $exception) {
                throw new ValidationException([
                    StudentConstant::PERMISSION_ACCESS
                ], StudentConstant::PERMISSION_ACCESS, StudentConstant::PERMISSION_ACCESS);
            } elseif (! in_array('"' . $category . '"', $finalArr)) {
                return false;
            }
        }
        $this->logger->info(">>>> Get Permission");
        return $finalArr;
    }


    /**
     * Return permission array  for a faculty
     *
     * @param  integer $userId
     * @param integer|null $studentId
     * @return array
     */
    public function getSharingAccess($userId, $studentId = null)
    {

        if ($studentId) {
            $featurePermissionForUser = $this->orgPermissionSetService->getStudentFeatureOnly($studentId, $userId);
        } else {

            $featurePermissionForUser = $this->orgPermissionSetService->getUserFeaturesPermission($userId);
            $sharingAccess = $featurePermissionForUser['user_feature_permissions'][0];
            $featurePermissionArray = array();
            foreach ($sharingAccess as $featureKey => $featurePermission) {
                $featureKeyArray = explode("_", $featureKey);
                if (in_array('share', $featureKeyArray)) {
                    unset($featureKeyArray[count($featureKeyArray) - 1]);
                    $featureArrayKey = implode("_", $featureKeyArray);
                    if ($featureKeyArray[0] == "referrals") {
                        $featurePermissionArray['referrals'] = $featurePermission['direct_referral'];
                        $featurePermissionArray['referrals_reason_routed'] = $featurePermission['reason_routed_referral'];
                    } else {
                        $featurePermissionArray[$featureArrayKey] = $featurePermission;
                    }
                }
            }
            $featurePermissionForUser = $featurePermissionArray;
        }

        $permissionArray = []; // this stores the final permission array that needs to be returned.
        //gather all the permissions for the user.
        foreach ($featurePermissionForUser as $featureName => $sharingDetails) {
            $featureName = $this->orgPermissionSetService->formatNameUpperCase($featureName);
            $permissionArray[$featureName]['public_view'] = (int)$sharingDetails['public_share']['view'];
            $permissionArray[$featureName]['team_view'] = (int)$sharingDetails['teams_share']['view'];
        }


        // loop through each of the feature, if any permission is missing for a feature ,add default permission to that feature
        foreach ($this->featureListArray as $feature) {
            if (!array_key_exists($feature, $permissionArray)) {
                $permissionArray[$feature] = $this->defaultPermissionArray;
            }
        }
        return $permissionArray;
    }

    public function getEbiSearchQuery($searchKey, $tokenValues)
    {
        $this->logger->debug(">>>> Get EBI Search Query" . "searchKey" . $searchKey);
        $searchRepository = $this->repositoryResolver->getRepository(StudentConstant::EBI_SEARCH_REPO);
        $query_by_key = $searchRepository->findOneByQueryKey($searchKey);
        if ($query_by_key) {
            $returnQuery = $query_by_key->getQuery();
        }
        $returnQuery = Helper::generateQuery($returnQuery, $tokenValues);
        $this->logger->info(">>>> Get EBI Search Query");
        return $returnQuery;
    }

    public function checkPermission($personPermission)
    {
        $this->logger->info(">>>> Check Permission for personPermission");
        if (count($personPermission) == 0) {
            $this->logger->error("Activity Service  -  CheckPermission - No Permission Access" . StudentConstant::PERMISSION_ACCESS);
            throw new ValidationException([
                StudentConstant::PERMISSION_ACCESS
            ], StudentConstant::PERMISSION_ACCESS, StudentConstant::PERMISSION_ACCESS);
        }
    }

    public function getFinalPerm($personPermission)
    {
        $this->logger->debug(">>>> Get Final Permission for person Permission");
        $finalArr = array();
        foreach ($personPermission as $key => $permission) {
            if ($permission) {
                switch ($key) {
                    case 'referrals':
                        $finalArr[] = '"R"';
                        break;
                    case 'notes':
                        $finalArr[] = '"N"';
                        break;
                    case 'log_contacts':
                        $finalArr[] = '"C"';
                        break;
                    case 'booking':
                        $finalArr[] = '"A"';
                        break;
                    case 'email':
                        $finalArr[] = '"E"';
                        break;
                    default:
                        break;
                }
            }
        }
        $this->logger->info(">>>> Get Final Permission for person Permission" );
        return $finalArr;
    }


    /**
     * Assumes the user has access to the student, verifying whether the user has access to the activity itself
     * Activity Array contains an activity_log row
     * featureAccess has to do with user's permission to activity, not the sharing option
     * TODO: More uniformity in the activity entities would save lots of work here
     *
     * @param int $organizationId
     * @param int $loggedInUserId
     * @param array $activity - ['referrals_id' => int|null, 'appointments_id' = > int|null, 'notes_id' => int|null, 'contacts_id' => int|null...]
     * @param array $featureAccess - [0 => ['public_view' => int|null, 'team_view' => int|null...]]
     * @param array $teamIdsOfUserArray
     * @return bool
     */
    public function verifyAccessToActivity($organizationId, $loggedInUserId, $activity, $featureAccess, $teamIdsOfUserArray)
    {
        $activityObject = null;
        $activityTeams = null;
        $personIdsWithAccessToActivity = [];
        $isPubliclyAccessible = false;

        //Determine what level of access the user has
        $hasAccessToPublic = boolval($featureAccess["public_view"]);
        $hasAccessToTeam = boolval($featureAccess["team_view"]);
        $hasAccessToReasonReferralPublic = boolval($featureAccess["reason_referrals_public_view"]);
        $hasAccessToReasonReferralTeam = boolval($featureAccess["reason_referrals_teams_view"]);


        //Determine what kind of activity the activity is
        //Get all teams associated with this activity if there are any
        //Get all individuals who have direct access to activity (for private activities)
        if (isset($activity['referrals_id'])) {
            $referralId = $activity['referrals_id'];
            $activityObject = $this->referralRepository->find($referralId);
            $interestedParties = $this->referralsInterestedPartiesRepository->findBy(['referrals' => $referralId]);
            $personIdsWithAccessToActivity = $this->getAllowedPersonIdsFromActivityObject($activityObject, 'Referral', $organizationId);
            foreach ($interestedParties as $interestedParty) {
                $personIdsWithAccessToActivity[] = $interestedParty->getPerson()->getId();
            }

            //Determine what level of access the activity is
            $isActivityPublic = $activityObject->getAccessPublic();
            $isActivityTeam = $activityObject->getAccessTeam();
            $isReasonRouted = $activityObject->getIsReasonRouted();

            //Do I have access through either Reason Routing or Directly
            $accessiblePublicDirectReferral = $hasAccessToPublic && !$isReasonRouted && $isActivityPublic;
            $accessibleTeamDirectReferral = $hasAccessToTeam && !$isReasonRouted && $isActivityTeam;
            $accessiblePublicReasonRoutedReferral = $hasAccessToReasonReferralPublic && $isReasonRouted && $isActivityPublic;
            $accessibleTeamReasonRoutedReferral = $hasAccessToReasonReferralTeam && $isReasonRouted && $isActivityTeam;

            //Check if I can access public
            $isPubliclyAccessible = $accessiblePublicReasonRoutedReferral || $accessiblePublicDirectReferral;

            //Get teams if it is a team activity and I have access
            if ($accessibleTeamDirectReferral || $accessibleTeamReasonRoutedReferral) {
                $activityTeams = $this->referralsTeamsRepository->findBy(['referrals' => $referralId]);
            }
        } elseif (isset($activity['note_id'])) {
            $noteId = $activity['note_id'];
            $activityObject = $this->noteRepository->find($noteId);
            $personIdsWithAccessToActivity = $this->getAllowedPersonIdsFromActivityObject($activityObject, 'Note');

            //Determine what level of access the activity is
            $isActivityPublic = $activityObject->getAccessPublic();
            $isActivityTeam = $activityObject->getAccessTeam();

            //Check if I can access public
            $isPubliclyAccessible = $isActivityPublic && $hasAccessToPublic;

            //Get teams if it is a team activity and I have access
            if ($isActivityTeam && $hasAccessToTeam) {
                $activityTeams = $this->noteTeamsRepository->findBy(['note' => $noteId]);
            }
        } elseif (isset($activity['contacts_id'])) {
            $contactId = $activity['contacts_id'];
            $activityObject = $this->contactRepository->find($contactId);
            $personIdsWithAccessToActivity = $this->getAllowedPersonIdsFromActivityObject($activityObject, 'Contact');

            //Determine what level of access the activity is
            $isActivityPublic = $activityObject->getAccessPublic();
            $isActivityTeam = $activityObject->getAccessTeam();

            //Check if I can access public
            $isPubliclyAccessible = $isActivityPublic && $hasAccessToPublic;

            //Get teams if it is a team activity and I have access
            if ($isActivityTeam && $hasAccessToTeam) {
                $activityTeams = $this->contactsTeamsRepository->findBy(['contacts' => $contactId]);
            }

        } elseif (isset($activity['appointments_id'])) {
            $appointmentId = $activity['appointments_id'];
            $activityObject = $this->appointmentsRepository->find($appointmentId);
            $personIdsWithAccessToActivity = $this->getAllowedPersonIdsFromActivityObject($activityObject, 'Appointment');

            //Determine what level of access the activity is
            $isActivityPublic = $activityObject->getAccessPublic();
            $isActivityTeam = $activityObject->getAccessTeam();

            //Check if I can access public
            $isPubliclyAccessible = $isActivityPublic && $hasAccessToPublic;

            //Get teams if it is a team activity and I have access
            if ($isActivityTeam && $hasAccessToTeam) {
                $activityTeams = $this->appointmentsTeamsRepository->findBy(['appointments' => $appointmentId]);
            }
        }

        if (isset($activityObject)) {
            //If this activity is public, you already have access to student, you are in
            if ($isPubliclyAccessible) {
                return true;
            }

            //If you are directly connected, no need to check further
            //Covers these scenarios:
            // Private, Public Create Only, Team Create Only, Team Referrals Assigned Outside of Team,
            // No Assignee with default to Primary Coordinator
            foreach ($personIdsWithAccessToActivity as $personId) {
                if ($loggedInUserId == $personId) {
                    return true;
                }
            }

            //Checking team membership for team activities if not directly connected
            //Assumes Team Activities are deleted or turned private when a team is deleted
            if (isset($activityTeams)) {
                foreach ($activityTeams as $activityTeam) {
                    if (in_array($activityTeam->getTeams()->getId(), $teamIdsOfUserArray)) {
                        return true;
                    }
                }
            }

        }

        return false;
    }


    /**
     * Retrieve Allowed Person Ids from a Generic Activity Object
     * TODO: More uniformity in the activity entities would save lots of work
     *
     * @param Appointments|Referrals|Contacts|Note|object|null $activityObject
     * @param string $type -  'Referral'|'Note'|'Contact'|'Appointment'
     * @param int $organizationId
     * @return array
     */
    public function getAllowedPersonIdsFromActivityObject($activityObject, $type, $organizationId = null)
    {
        $personIds = [];
        if (isset($activityObject) && $type !== null) {
            $facultyPerson = $activityObject->getPersonIdFaculty();
            if (isset($facultyPerson)) {
                $personIds[] = $facultyPerson->getId();
            }
            if ($type == 'Referral') {
                $assignedPerson = $activityObject->getPersonAssignedTo();
                if (isset($assignedPerson)) {
                    $personIds[] = $assignedPerson->getId();
                } elseif (isset($organizationId)) {
                    $coordinatorId = $this->organizationRoleRepository->findFirstPrimaryCoordinatorIdAlphabetically($organizationId);
                    if ($coordinatorId) {
                        $personIds[] = $coordinatorId;
                    }
                }

            }
        }
        return $personIds;
    }


    /**
     * Retrieve Activity Id from activity array for given activity code (activity_type column from table activity_log)
     *
     *
     * @param array $activity - ['activity_code' => 'A'|'C'|'N'|'R'|'E'|'L', 'referrals_id' => int|null, 'appointments_id' = > int|null, 'notes_id' => int|null, 'contacts_id' => int|null...]
     * @return int
     */
    public function getActivityId($activity)
    {

        $activityId = null;
        if ($activity['activity_code'] == 'A') {
            $activityId = $activity['appointments_id'];
        }
        if ($activity['activity_code'] == 'C') {
            $activityId = $activity['contacts_id'];
        }
        if ($activity['activity_code'] == 'N') {
            $activityId = $activity['note_id'];
        }
        if ($activity['activity_code'] == 'R') {
            $activityId = $activity['referrals_id'];
        }
        if ($activity['activity_code'] == 'E') {
            $activityId = $activity['email_id'];
        }
        //login activity is not stored in a separate table, no id column for this
        if ($activity['activity_code'] == 'L') {
            $activityId = null;
        }
        return $activityId;
    }

}