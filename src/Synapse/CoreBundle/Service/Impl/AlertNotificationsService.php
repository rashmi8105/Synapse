<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdate;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRepository;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestRepository;
use Synapse\CampusResourceBundle\EntityDto\SystemMessage;
use Synapse\CampusResourceBundle\Repository\OrgCampusAnnouncementLangRepository;
use Synapse\CoreBundle\Entity\AlertNotifications;
use Synapse\CoreBundle\Entity\Appointments;
use Synapse\CoreBundle\Entity\Contacts;
use Synapse\CoreBundle\Entity\Note;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\Referrals;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\AlertNotificationReferralRepository;
use Synapse\CoreBundle\Repository\AlertNotificationsRepository;
use Synapse\CoreBundle\Repository\AppointmentRecepientAndStatusRepository;
use Synapse\CoreBundle\Repository\MapworksActionRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\ReferralRepository;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\ReportsBundle\Entity\ReportsRunningStatus;
use Synapse\RestBundle\Entity\AlertNotificationsDto;
use Synapse\RestBundle\Entity\FacultyDto;
use Synapse\RestBundle\Entity\StudentsDto;
use Synapse\SearchBundle\Entity\OrgSearch;
use Synapse\SearchBundle\Repository\OrgSearchSharedByRepository;
use Synapse\StaticListBundle\Entity\OrgStaticList;
use Synapse\StaticListBundle\Repository\OrgStaticListRepository;

/**
 * @DI\Service("alertNotifications_service")
 */
class AlertNotificationsService extends AbstractService
{

    const SERVICE_KEY = 'alertNotifications_service';

    /**
     * @var int
     */
    private $notificationCountVisibleByUser = 100;

    /**
     * @var array
     */
    private $csvDownloadEvents = [
        'Activity_Download',
        'Custom_Search_Download',
        'My_Students_Download',
        'Predefined_Search_Download',
        'Survey_Download',
        'Survey_Completion_Download',
        'Team_Activity_Download',
        'Retention_Completion_Data_Generated'
    ];

    /**
     * @var array
     */
    private $uploadEvents = [
        'Course_Import',
        'AcademicUpdate_Import',
        'Course-faculty_Import',
        'Course-student_Import',
        'Student_Upload_Notification',
        'Faculty_Upload_Notification',
        'Group_faculty_Upload_Notification',
        'Group_student_Upload_Notification',
        'Group_Upload_Notification',
        'Student_Data_Generated',
        'StaticList_Upload_Notification'
    ];

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    // Services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var NotificationChannelService
     */
    private $notificationChannelService;

    /**
     * @var UserManagementService
     */
    private $userManagementService;

    // Repositories

    /**
     * @var AcademicUpdateRepository
     */
    private $academicUpdateRepository;

    /**
     * @var AcademicUpdateRequestRepository
     */
    private $academicUpdateRequestRepository;

    /**
     * @var AlertNotificationReferralRepository
     */
    private $alertNotificationReferralRepository;

    /**
     * @var AlertNotificationsRepository
     */
    private $alertNotificationsRepository;

    /**
     * @var AppointmentRecepientAndStatusRepository
     */
    private $appointmentsRecipientAndStatusRepository;

    /**
     * @var MapworksActionRepository
     */
    private $mapworksActionRepository;

    /**
     * @var MetadataListValuesRepository
     */
    private $metadataListValuesRepository;

    /**
     * @var OrgCampusAnnouncementLangRepository
     */
    private $orgCampusAnnouncementLangRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
 * @var OrgPersonStudentRepository
 */
    private $orgPersonStudentRepository;

    /**
     * @var OrgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;

    /**
     * @var OrgSearchSharedByRepository
     */
    private $orgSearchSharedByRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var ReferralRepository
     */
    private $referralRepository;

    /**
     * @var OrgStaticListRepository
     */
    private $staticListRepository;


    /**
     * AlertNotificationService Constructor
     *
     * @DI\InjectParams({
     *      "repositoryResolver" = @DI\Inject("repository_resolver"),
     *      "logger" = @DI\Inject("logger"),
     *      "container" = @DI\Inject("service_container")
     * })
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

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->notificationChannelService = $this->container->get(NotificationChannelService::SERVICE_KEY);
        $this->userManagementService = $this->container->get(UserManagementService::SERVICE_KEY);

        // Repositories
        $this->academicUpdateRepository = $this->repositoryResolver->getRepository(AcademicUpdateRepository::REPOSITORY_KEY);
        $this->academicUpdateRequestRepository = $this->repositoryResolver->getRepository(AcademicUpdateRequestRepository::REPOSITORY_KEY);
        $this->alertNotificationReferralRepository = $this->repositoryResolver->getRepository(AlertNotificationReferralRepository::REPOSITORY_KEY);
        $this->alertNotificationsRepository = $this->repositoryResolver->getRepository(AlertNotificationsRepository::REPOSITORY_KEY);
        $this->appointmentsRecipientAndStatusRepository = $this->repositoryResolver->getRepository(AppointmentRecepientAndStatusRepository::REPOSITORY_KEY);
        $this->mapworksActionRepository = $this->repositoryResolver->getRepository(MapworksActionRepository::REPOSITORY_KEY);
        $this->metadataListValuesRepository = $this->repositoryResolver->getRepository(MetadataListValuesRepository::REPOSITORY_KEY);
        $this->orgCampusAnnouncementLangRepository = $this->repositoryResolver->getRepository(OrgCampusAnnouncementLangRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->orgSearchSharedByRepository = $this->repositoryResolver->getRepository(OrgSearchSharedByRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->referralRepository = $this->repositoryResolver->getRepository(ReferralRepository::REPOSITORY_KEY);
        $this->staticListRepository = $this->repositoryResolver->getRepository(OrgStaticListRepository::REPOSITORY_KEY);

    }


    /**
     * Lists the 100 most recent notifications for a user , based on the unread flag
     *
     * @param int $loggedInUserId
     * @return AlertNotificationsDto
     */
    public function listNotifications($loggedInUserId)
    {
        $loggedInUser = $this->personRepository->find($loggedInUserId);
        //Grab the 100 most recent notifications for that user (Frontend artificially limits the results to 100 records)

        $searchColumns = [
            'person' => $loggedInUserId
        ];
        $sortColumns = [
            'createdAt' => 'DESC',
            'id' => 'DESC'
        ];

        $alertNotifications = $this->alertNotificationsRepository->findBy(
            $searchColumns,
            $sortColumns,
            $this->notificationCountVisibleByUser
        );

        $notificationStatusCounts = $this->alertNotificationsRepository->getNotificationStatusCounts($loggedInUserId);
        $alert = new AlertNotificationsDto();

        $organization = $loggedInUser->getOrganization();
        $organizationId = $organization->getId();

        //Add system message (if there is one) to the AlertNotificationsDTO
        $this->getAnnouncementAlerts($loggedInUserId, $alert, $loggedInUser);
        $alertNotificationData = [];
        //If there are alert notifications, proceed.
        if (!empty($alertNotifications)) {
            $alert->setPersonId($loggedInUserId);
            $alert->setUnseenNotificationCount($notificationStatusCounts['seen_notification_count']);
            //Iterate over each notification, placing the appropriate info in the AlertNotificationDTO
            foreach ($alertNotifications as $alertNotification) {
                $event = $alertNotification->getEvent();
                if ($event == 'announcement-alert' || $event == 'announcement-banner') {
                    continue;
                }

                try {
                    $alertNotificationsDto = new AlertNotificationsDto();

                    $alertNotificationId = $alertNotification->getId();
                    $alertNotificationsDto->setAlertId($alertNotificationId);

                    $alertNotificationHasBeenRead = $alertNotification->getIsRead();
                    $alertNotificationsDto->setIsRead($alertNotificationHasBeenRead);

                    $alertNotificationHasBeenSeen = $alertNotification->getIsSeen();
                    $alertNotificationsDto->setIsSeen($alertNotificationHasBeenSeen);

                    $alertNotificationsDto->setAlertReason($event);

                    // In many cases, the "activity_type" expected is the same as the "alert_reason".
                    // In the cases where it isn't, it will be reset below.
                    $alertNotificationsDto->setActivityType($event);

                    $reason = $alertNotification->getReason();
                    $alertNotificationsDto->setReason($reason);

                    $activityDate = $alertNotification->getCreatedAt();
                    $alertNotificationsDto->setActivityDate($activityDate);

                    $fileName = $alertNotification->getOrgCourseUploadFile();
                    if (!empty($fileName)) {
                        $alertNotificationsDto->setOrgCourseUploadFile($fileName);
                    }
                    // If it is an appointment notification...
                    if (!empty($alertNotification->getAppointments())) {
                        $alertNotificationsDto->setActivityType('Appointment');
                        $appointmentId = $alertNotification->getAppointments()->getId();
                        $alertNotificationsDto->setActivityId($appointmentId);

                        $appointmentAttendees = $this->appointmentsRecipientAndStatusRepository->getAttendeesWithHistory($appointmentId);

                        $students = $this->getStudentAppointmentAttendees($appointmentAttendees, $organizationId);
                        // if the students are non participating in current academic year, ignore that students otherwise empty notification will be created

                        if (empty($students)) {
                            continue;
                        }

                        $alertNotificationsDto->setStudents($students);
                        $faculty = $this->appointmentFaculty($appointmentAttendees);
                        $alertNotificationsDto->setFaculties($faculty);

                        // If it is a referral notification...
                    } elseif (!empty($alertNotification->getReferrals())) {
                        $referralObject = $alertNotification->getReferrals();
                        if (!empty($referralObject)) {

                            $mapworksActionObject = $this->mapworksActionRepository->findOneBy(array('eventKey' => $event));

                            if ($mapworksActionObject) {
                                $actionString = $mapworksActionObject->getAction();
                                //Do not attach Referral to Notification if the student is made non participating
                                //Do not want a link back to the referral that can be clicked
                                if ($actionString != 'student_made_nonparticipant') {
                                    $referralId = $referralObject->getId();
                                    $alertNotificationsDto->setActivityType('Referral');
                                    $alertNotificationsDto->setActivityId($referralId);
                                }
                            } else {
                                $actionString = $event;
                            }


                            // Set the student whom the referral is about.
                            $studentDto = new StudentsDto();
                            $personObjectForStudent = $referralObject->getPersonStudent();
                            $personIdForStudent = $personObjectForStudent->getId();
                            $studentMemberOfCurrentAcademicYear = $this->userManagementService->isStudentMemberOfCurrentAcademicYear($personIdForStudent, $organizationId);
                            // if the student is non participating in current academic year, ignore that student otherwise empty notification will be created

                            if (!$studentMemberOfCurrentAcademicYear && $actionString != 'student_made_nonparticipant') {
                                continue;
                            }
                            $studentDto->setStudentId($personIdForStudent);
                            $studentDto->setStudentFirstName($personObjectForStudent->getFirstname());
                            $studentDto->setStudentLastName($personObjectForStudent->getLastname());
                            $studentDto->setStudentStatus($this->studentStatus($personIdForStudent));
                            $alertNotificationsDto->setStudents([$studentDto]);

                            // Set the person who created the referral.
                            $userFirstName = $referralObject->getPersonFaculty()->getFirstname();
                            $userLastName = $referralObject->getPersonFaculty()->getLastname();
                            $alertNotificationsDto->setUserName("$userLastName, $userFirstName");


                            $alertNotificationReferral = $this->alertNotificationReferralRepository->findOneBy(['alertNotification' => $alertNotification]);
                            if($mapworksActionObject && $mapworksActionObject->getReceivesNotification() && $alertNotificationReferral) {
                                $alertNotificationsDto->setNotificationBodyText($alertNotificationReferral->getNotificationBodyText());
                                $alertNotificationsDto->setNotificationHoverText($alertNotificationReferral->getNotificationHoverText());
                            }
                        }

                        // If it is a shared search notification...
                    } elseif (!empty($alertNotification->getOrgSearch())) {
                        $alertNotificationsDto->setActivityType('SharedSearch');
                        $orgSearchId = $alertNotification->getOrgSearch()->getId();
                        $alertNotificationsDto->setActivityId($orgSearchId);

                        $activityDate = $alertNotification->getOrgSearch()->getCreatedAt();
                        $alertNotificationsDto->setActivityDate($activityDate);

                        $orgSearchSharedByObject = $this->orgSearchSharedByRepository->findOneBy(['orgSearch' => $orgSearchId]);

                        if (!empty($orgSearchSharedByObject)) {
                            $sharedByPersonObject = $orgSearchSharedByObject->getPersonSharedBy();
                            if (!empty($sharedByPersonObject)) {
                                $sharerFirstName = $sharedByPersonObject->getFirstname();
                                $sharerLastName = $sharedByPersonObject->getLastname();
                                $alertNotificationsDto->setSharedBy("$sharerFirstName $sharerLastName");
                            }
                        }

                        // If it is an org static list notification...
                    } elseif (!empty($alertNotification->getOrgStaticList())) {
                        $alertNotificationsDto->setActivityType('SharedStaticList');

                        $orgStaticListObject = $alertNotification->getOrgStaticList();
                        $orgStaticListId = $orgStaticListObject->getId();
                        $alertNotificationsDto->setActivityId($orgStaticListId);

                        $sharedByPersonId = $orgStaticListObject->getPersonIdSharedBy();
                        if (!empty($sharedByPersonId)) {
                            $sharedByPersonObject = $this->personRepository->find($sharedByPersonId);
                            if (!empty($sharedByPersonObject)) {
                                $sharerFirstName = $sharedByPersonObject->getFirstname();
                                $sharerLastName = $sharedByPersonObject->getLastname();
                                $alertNotificationsDto->setSharedBy("$sharerFirstName $sharerLastName");
                            }

                        }

                        // If it is an academic update notification...
                    } elseif (!empty($alertNotification->getAcademicUpdate())) {
                        $academicUpdateObject = $alertNotification->getAcademicUpdate();
                        $academicUpdateId = $academicUpdateObject->getId();
                        $alertNotificationsDto->setActivityId($academicUpdateId);

                        if ($event == 'academic-updates-cancelled' || $event == 'academic-updates-reminder') {
                            $academicUpdateRequestObject = $academicUpdateObject->getAcademicUpdateRequest();

                            if (!empty($academicUpdateRequestObject)) {
                                $academicUpdateRequestCreator = $academicUpdateRequestObject->getPerson();
                                $facultyDto = new FacultyDto();
                                $facultyDto->setFacultyId($academicUpdateRequestCreator->getId());
                                $facultyDto->setFacultyFirstName($academicUpdateRequestCreator->getFirstname());
                                $facultyDto->setFacultyLastName($academicUpdateRequestCreator->getLastname());
                                $alertNotificationsDto->setFaculties([$facultyDto]);
                            }

                        } else {
                            $alertNotificationsDto->setActivityType('AcademicUpdate');

                            $activityDate = $academicUpdateObject->getUpdateDate();
                            $alertNotificationsDto->setActivityDate($activityDate);

                            $studentId = $academicUpdateObject->getPersonStudent()->getId();

                            $studentMemberOfCurrentAcademicYear = $this->userManagementService->isStudentMemberOfCurrentAcademicYear($studentId, $organizationId);
                            // if the student is non participating in current academic year, ignore that student otherwise empty notification will be created
                            if (!$studentMemberOfCurrentAcademicYear) {
                                continue;
                            }

                        }

                        // If it is a report notification...
                    } elseif (!empty($alertNotification->getReportsRunningStatus())) {
                        $alertNotificationsDto->setActivityType('Report');

                        $reportsRunningStatusId = $alertNotification->getReportsRunningStatus()->getId();
                        $alertNotificationsDto->setReportRunningStatusId($reportsRunningStatusId);

                        $reportURLPaths = [
                            'CR' => '/reports/completion-report/',
                            'EXEC' => '/reports/executive-summary-report/',
                            'FUR' => '/reports/faculty-usage-report/',
                            'GPA' => '/reports/gpa-report/',
                            'MAR' => '/reports/our-mapworks-activity/',
                            'OSR' => '/reports/our-students-report/',
                            'PRO-SR' => '/reports/profile-snapshot-report/',
                            'PRR' => '/reports/persistence-retention-report/',
                            'SUR-FR' => '/reports/survey-factor-report/',
                            'SUR-SR' => '/reports/survey-snapshot-report/',
                            'SUB-COM' => '/reports/compare/'
                        ];

                        $reportURL = $reportURLPaths[$event] . $reportsRunningStatusId;
                        $alertNotificationsDto->setOrgCourseUploadFile($reportURL);

                        // If it is an upload notification...
                    } elseif (in_array($event, $this->uploadEvents)) {
                        $alertNotificationsDto->setActivityType('import');

                        // If it is a calendar notification...
                    } elseif ($event == 'Calendar_Enabled' || $event == 'Calendar_Disabled') {
                        $alertNotificationsDto->setActivityType('Calendar');

                        // If it is a bulk action notification for academic updates...
                    } elseif ($event == 'bulk-action-completed' || $event == 'academic-updates-completed') {
                        $alertNotificationsDto->setActivityType('Bulk');

                        // If it is a csv download notification...
                    } elseif (in_array($event, $this->csvDownloadEvents)) {
                        $alertNotificationsDto->setActivityType('csv_download');
                    }
                    else
                    {
                        $mapworksActionObject = $this->mapworksActionRepository->findOneBy(array('eventKey' => $event));
                        if ($mapworksActionObject) {
                            $alertNotificationsDto->setNotificationBodyText($mapworksActionObject->getNotificationBodyText());
                            $alertNotificationsDto->setNotificationHoverText($mapworksActionObject->getNotificationHoverText());
                        }
                    }

                    $alertNotificationData[] = $alertNotificationsDto;
                } catch (\Exception $e) {
                    continue;
                }
            }
            $alert->setAlerts($alertNotificationData);
        }

        return $alert;
    }

    /**
     * @param $alertNotificationIds
     * @return mixed
     *
     * @deprecated API looks to no longer be used.
     */
    public function deleteNotificationViewStatus($alertNotificationIds)
    {
        $this->alertNotificationsRepository->removeSelected($alertNotificationIds);

        $this->alertNotificationsRepository->flush();
        $this->logger->info(">>>> deleteNotificationsViewStatus for alert Notification Ids" );
        return $alertNotificationIds;
    }

    /**
     * Creates a notification alert for diffrent activity
     *
     * @param string $event
     * @param string $reason
     * @param Person $person
     * @param Referrals|null $referral
     * @param Appointments|null $appointment
     * @param OrgSearch|null $orgSearch
     * @param string|null $courseUploadFileName
     * @param AcademicUpdate|null $academicUpdate
     * @param OrgStaticList|null $orgStaticList
     * @param Organization|null $organization
     * @param bool $errorFileFlag
     * @param ReportsRunningStatus|null $reportRunningStatus
     * @param Note|null $note
     * @param Contacts|null $contact
     * @param null $staticListStudentsObj
     * @param string|null $email
     * @return AlertNotifications
     */
    public function createNotification($event, $reason, Person $person, Referrals $referral = null, Appointments $appointment = null, OrgSearch $orgSearch = null, $courseUploadFileName = null, AcademicUpdate $academicUpdate = null, OrgStaticList $orgStaticList = null, Organization $organization = null, $errorFileFlag = false, ReportsRunningStatus $reportRunningStatus = null, Note $note = null, Contacts $contact = null, $staticListStudentsObj = null, $email = null)
    {
        $alertNotification = new AlertNotifications();

        $alertNotification->setReason($reason);
        $alertNotification->setEvent($event);

        if (!empty($organization)) {
            $alertNotification->setOrganization($organization);
        }
        if ($person) {
            $alertNotification->setPerson($person);
        }
        if ($referral) {
            $alertNotification->setReferrals($referral);
            $alertNotification->setOrganization($referral->getOrganization());
        }

        if ($appointment) {
            $alertNotification->setAppointments($appointment);
            $alertNotification->setOrganization($appointment->getOrganization());
        }
        if ($orgSearch) {
            $alertNotification->setOrgSearch($orgSearch);
            $alertNotification->setOrganization($orgSearch->getOrganization());
        }
        if ($courseUploadFileName) {
            if ($errorFileFlag) {
                $alertNotification->setOrgCourseUploadFile($courseUploadFileName);
            }
            $alertNotification->setOrganization($person->getOrganization());
        }
        if ($academicUpdate) {
            $alertNotification->setAcademicUpdate($academicUpdate);
            $alertNotification->setOrganization($academicUpdate->getOrg());
        }
        if ($orgStaticList) {
            $alertNotification->setOrgStaticList($orgStaticList);
            $alertNotification->setOrganization($orgStaticList->getOrganization());
        }
        if ($reportRunningStatus) {
            $alertNotification->setReportsRunningStatus($reportRunningStatus);
            $alertNotification->setOrganization($reportRunningStatus->getOrganization());
        }
        if ($note) {
            $alertNotification->setOrganization($note->getOrganization());
        }
        if ($contact) {
            $alertNotification->setOrganization($contact->getOrganization());
        }
        if ($staticListStudentsObj) {
            $alertNotification->setOrganization($staticListStudentsObj->getOrganization());
        }

        if ($email) {
            $alertNotification->setOrganization($email->getOrganization());
        }

        $alertNotification->setIsRead(false);
        $alertNotification->setIsSeen(false);

        $this->alertNotificationsRepository->create($alertNotification);
        $this->alertNotificationsRepository->flush();

        $this->notificationChannelService->sendNotificationToAllRegisteredChannels($person, $event);
        return $alertNotification;
    }


    /**
     * Creates a record in the alert_notifications table for a report that has just been generated.
     *
     * @param ReportsRunningStatus $reportInstance
     */
    public function createReportNotification($reportInstance)
    {
        $alertNotification = new AlertNotifications();

        $shortCode = $reportInstance->getReports()->getShortCode();
        $alertNotification->setEvent($shortCode);

        $reportName = $reportInstance->getReports()->getName();
        $alertNotification->setReason($reportName);

        $personObject = $reportInstance->getPerson();
        $alertNotification->setPerson($personObject);

        $organizationObject = $personObject->getOrganization();
        $alertNotification->setOrganization($organizationObject);

        $alertNotification->setReportsRunningStatus($reportInstance);

        $alertNotification->setIsRead(false);
        $alertNotification->setIsSeen(false);

        $this->alertNotificationsRepository->create($alertNotification);
        $this->alertNotificationsRepository->flush();

        $this->notificationChannelService->sendNotificationToAllRegisteredChannels($personObject, $shortCode);
    }


    /**
     * Creates a record in the alert_notifications table for a CSV that has just been created.
     *
     * @param string $event
     * @param string $reason
     * @param string $filePath
     * @param int $personId
     */
    public function createCSVDownloadNotification($event, $reason, $filePath, $personId)
    {
        $alertNotification = new AlertNotifications();

        $alertNotification->setEvent($event);
        $alertNotification->setReason($reason);
        $alertNotification->setOrgCourseUploadFile($filePath);

        $personObject = $this->personRepository->find($personId);
        $alertNotification->setPerson($personObject);

        $organizationObject = $personObject->getOrganization();
        $alertNotification->setOrganization($organizationObject);

        $alertNotification->setIsRead(false);
        $alertNotification->setIsSeen(false);

        $this->alertNotificationsRepository->create($alertNotification);
        $this->alertNotificationsRepository->flush();

        $this->notificationChannelService->sendNotificationToAllRegisteredChannels($personObject, $event);
    }


    public function deleteAlertByActivityId($activityId, $activityField)
    {
        $this->logger->debug(">>>> delete Alert By Activity Id" . $activityId . "Activity Field" . $activityField);
        $activityAlerts = $this->alertNotificationsRepository->findBy([
            $activityField => $activityId
        ]);
        if (! empty($activityAlerts)) {
            foreach ($activityAlerts as $alerts) {
                $this->alertNotificationsRepository->remove($alerts);
            }
            $this->alertNotificationsRepository->flush();
        }
        $this->logger->info(">>>> delete Alert By Activity Id" );
    }


    /**
     * Get Participating Appointment Attendees and load those attendees into an array of StudentsDtos
     *
     * @param array $appointmentAttendees
     * @param integer $organizationId
     * @return array
     */
    private function getStudentAppointmentAttendees($appointmentAttendees, $organizationId)
    {
        $students = array();
        $longitudinalStudentManagement = false;
        foreach ($appointmentAttendees as $attendees) {
            $personId = $attendees['person_id_student'];
            $studentObject = $this->userManagementService->getMultiyearOrganizationStudentObject($personId, $longitudinalStudentManagement, $organizationId);
            if ($studentObject) {
                $studentsDto = new StudentsDto();
                $studentsDto->setStudentId($personId);
                $studentsDto->setStudentFirstName($attendees['firstname']);
                $studentsDto->setStudentLastName($attendees['lastname']);
                if ($longitudinalStudentManagement) {
                    $studentIsActive = $studentObject->getIsActive() == true ? true : false;
                } else {
                    $studentIsActive = $studentObject->getStatus() == 1 ? true : false;
                }
                $studentsDto->setStudentStatus($studentIsActive);
                $students[] = $studentsDto;
            }

        }

        return $students;
    }

    private function appointmentFaculty($appointmentFaculty)
    {
        $faculty = array();
        if (count($appointmentFaculty) > 0) {
            $facultyId = $appointmentFaculty[0]['person_id_faculty'];
            $facultyStatusObj = $this->orgPersonFacultyRepository->findOneByPerson($facultyId);
            $facultyStatus = NULL;
            if ($facultyStatusObj) {
                $facultyDto = new FacultyDto();
                $facultyDto->setFacultyId($facultyId);
                $facultyDto->setFacultyFirstName($appointmentFaculty[0]['facultyFirstname']);
                $facultyDto->setFacultyLastName($appointmentFaculty[0]['facultyLastname']);
                $facultyStatus = (strlen($facultyStatusObj->getStatus())) ? $facultyStatusObj->getStatus() : '1';
                $facultyDto->setFacultyStatus($facultyStatus);
                $faculty[] = $facultyDto;
            }
        }

        return $faculty;
    }

    public function studentStatus($studentId)
    {
        $this->logger->debug(">>>> Student Status" . $studentId);
        $studentStatusObj = $this->orgPersonStudentRepository->findOneByPerson($studentId);
        $studentStatus = NULL;
        if ($studentStatusObj) {
            $studentStatus = (strlen($studentStatusObj->getStatus())) ? $studentStatusObj->getStatus() : '1';
        }
        $this->logger->info(">>>> Student Status" );
        return $studentStatus;
    }

    /**
     * Get campus announcements
     *
     * TODO::Clean up this code.
     *
     * @param $loggedInUserId
     * @param AlertNotificationsDto $alertNotificationsDto
     * @param Person $person
     * @return mixed
     */
    private function getAnnouncementAlerts($loggedInUserId, $alertNotificationsDto, $person)
    {
        $organizationId = $person->getOrganization()->getId();
        $currentDateTime = new \DateTime();
        $orgPersonFaculty = $this->orgPersonFacultyRepository->findBy(array(
            'person' => $loggedInUserId,
            "organization" => $organizationId
        ));
        $orgAnnouncementsArr = [];
        $announcementCount = 0;
        if (isset($orgPersonFaculty) && ! empty($orgPersonFaculty)) {
            $orgAnnouncementsList = $this->orgCampusAnnouncementLangRepository->listCampusAnnouncementsForFaculty($currentDateTime,$person->getOrganization());
            foreach ($orgAnnouncementsList as $orgAnnouncements) {
                /*
                 * As a faculty login list campus announcements messages for all the coordinator
                 */
                $orgAnnouncementsFaculty = $this->alertNotificationsRepository->findBy(array(
                    'person' => $loggedInUserId,
                    "isRead" => 1,
                    "orgAnnouncements" => $orgAnnouncements['id'],
                    "event" => array(
                        "announcement-alert",
                        "announcement-banner"
                    )
                ));
                if (isset($orgAnnouncementsFaculty) && ! empty($orgAnnouncementsFaculty)) {
                    continue;
                } else {
                    $systemMessageDto = new SystemMessage();
                    $systemMessageDto->setId($orgAnnouncements['id']);
                    $systemMessageDto->setMessage($orgAnnouncements['message']);
                    $notificationCreatedDate = $this->dateUtilityService->adjustDateTimeToOrganizationTimezone($person->getOrganization()->getId(), new \DateTime($orgAnnouncements['created_at']));
                    $systemMessageDto->setStartDateTime($notificationCreatedDate);
                    $orgAnnouncementsArr[] = $systemMessageDto;
                }
            }
            $alertNotificationsDto->setSystemMessage($orgAnnouncementsArr);
            $alertNotificationsDto->setOrgAnnouncementCount($announcementCount);
            return $alertNotificationsDto;
        }
    }

    /**
     * Updates notification is viewed status as read
     *
     * @param int $alertNotificationId
     * @param Person $loggedInUser
     * @return AlertNotifications
     * @throws SynapseValidationException
     */
    public function updateNotificationReadStatus($alertNotificationId, $loggedInUser)
    {
        $organizationObject = $loggedInUser->getOrganization();
        $alertNotificationObject = $this->alertNotificationsRepository->findOneBy(
            [
                'person' => $loggedInUser,
                'id' => $alertNotificationId,
                'organization' => $organizationObject
            ], new SynapseValidationException("Alert notification not found."));

        $alertNotificationObject->setIsRead(1);
        $alertNotificationObject->setIsSeen(1);
        $this->alertNotificationsRepository->flush();
        return $alertNotificationObject->getId();
    }

    /**
     * Updates all alert notifications as seen for the passed in person
     *
     * @param $personId
     * @return bool
     */
    public function updateAllUnseenNotificationsAsSeenForUser($personId)
    {
        $successfullyUpdated = $this->alertNotificationsRepository->updateAllUnseenNotificationsAsSeenForUser($personId);
        return $successfullyUpdated;
    }

    /**
     * Updates all alert notifications as read and seen for the passed in person
     *
     * @param $personId
     * @return bool
     */
    public function updateAllUnreadNotificationsAsReadForUser($personId)
    {
        $successfullyUpdated = $this->alertNotificationsRepository->updateAllUnreadNotificationsAsReadForUser($personId);
        return $successfullyUpdated;
    }
}