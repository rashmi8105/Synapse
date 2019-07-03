<?php
namespace Synapse\CoreBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CalendarBundle\Service\Impl\CalendarFactoryService;
use Synapse\CalendarBundle\Service\Impl\CalendarIntegrationService;
use Synapse\CalendarBundle\Service\Impl\CalendarWrapperService;
use Synapse\CalendarBundle\Service\Impl\CronofyCalendarService;
use Synapse\CalendarBundle\Service\Impl\CronofyService;
use Synapse\CalendarBundle\Service\Impl\CronofyWrapperService;
use Synapse\CoreBundle\Entity\ActivityCategory;
use Synapse\CoreBundle\Entity\AppointmentRecepientAndStatus;
use Synapse\CoreBundle\Entity\Appointments;
use Synapse\CoreBundle\Entity\AppointmentsTeams;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\InvalidArgumentException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\job\BulkAppointmentJob;
use Synapse\CoreBundle\job\SendAppointmentReminderJob;
use Synapse\CoreBundle\Repository\ActivityCategoryRepository;
use Synapse\CoreBundle\Repository\AppointmentRecepientAndStatusRepository;
use Synapse\CoreBundle\Repository\AppointmentsRepository;
use Synapse\CoreBundle\Repository\AppointmentsTeamsRepository;
use Synapse\CoreBundle\Repository\CalendarSharingRepository;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EbiMetadataRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\EmailTemplateRepository;
use Synapse\CoreBundle\Repository\FeatureMasterLangRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OfficeHoursRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\TeamMembersRepository;
use Synapse\CoreBundle\Repository\TeamsRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\PermissionConstInterface;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\AppointmentsConstant;
use Synapse\PersonBundle\Repository\ContactInfoRepository;
use Synapse\RestBundle\Entity\ActivityLogDto;
use Synapse\RestBundle\Entity\AppointmentsDto;
use Synapse\RestBundle\Entity\AppointmentsReponseDto;
use Synapse\RestBundle\Entity\AttendeesDto;
use Synapse\RestBundle\Entity\CalendarTimeSlotsReponseDto;
use Synapse\RestBundle\Entity\RelatedActivitiesDto;
use Synapse\RestBundle\Exception\RestException;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SearchBundle\DAO\QuickSearchDAO;
use Synapse\StudentViewBundle\Service\Impl\StudentAppointmentService;

/**
 * @DI\Service("appointments_service")
 */
class AppointmentsService extends AppointmentHelperService implements PermissionConstInterface
{

    const SERVICE_KEY = 'appointments_service';

    const APPOINMENT_TYPE = 'AppointmentType';

    const CONTACT_INFO = 'contactInfo';

    const EVENT_TYPE = 'EventType';

    const LIT_ATTENDEES = 'attendees';

    const LIT_TIMEZONE = 'timezone';

    const LIT_APPOINTMENT = 'appointment';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Manager
     */
    protected $rbacManager;

    /**
     * @var Resque
     */
    private $resque;

    // Services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var ActivityLogService
     */
    private $activityLogService;

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationService;

    /**
     * @var CalendarFactoryService
     */
    private $calendarFactoryService;

    /**
     * @var CalendarIntegrationService
     */
    private $calendarIntegrationService;

    /**
     * @var CalendarWrapperService
     */
    private $calendarWrapperService;

    /**
     * @var CronofyCalendarService
     */
    private $cronofyCalendarService;

    /**
     * @var CronofyWrapperService
     */
    private $cronofyWrapperService;

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
    private $loggerService;

    /**
     * @var MapworksActionService
     */
    private $mapworksActionService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var RelatedActivitiesService
     */
    private $relatedActivitiesService;

    /**
     * @var StudentAppointmentService
     */
    private $studentAppointmentService;

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
     * @var AppointmentRecepientAndStatusRepository
     */
    private $appointmentRecipientAndStatusRepository;

    /**
     * @var AppointmentsRepository
     */
    private $appointmentsRepository;

    /**
     * @var AppointmentsTeamsRepository
     */
    private $appointmentTeamsRepository;

    /**
     * @var CalendarSharingRepository
     */
    private $calendarSharingRepository;

    /**
     * @var ContactInfoRepository
     */
    private $contactInfoRepository;

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var EbiMetadataRepository
     */
    private $ebiMetadataRepository;

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
     * @var OfficeHoursRepository
     */
    private $officeHoursRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

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
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var TeamMembersRepository
     */
    private $teamMembersRepository;

    /**
     * @var TeamsRepository
     */
    private $teamsRepository;


    /**
     * AppointmentsService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "container" = @DI\Inject("service_container"),
     *            "logger" = @DI\Inject("logger")
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
        $this->logger = $logger;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);

        //Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->activityLogService = $this->container->get(ActivityLogService::SERVICE_KEY);
        $this->alertNotificationService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->calendarIntegrationService = $this->container->get(CalendarIntegrationService::SERVICE_KEY);
        $this->cronofyCalendarService = $this->container->get(CronofyCalendarService::SERVICE_KEY);
        $this->calendarWrapperService = $this->container->get(CalendarWrapperService::SERVICE_KEY);
        $this->calendarFactoryService = $this->container->get(CalendarFactoryService::SERVICE_KEY);
        $this->cronofyWrapperService = $this->container->get(CronofyWrapperService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->featureService = $this->container->get(FeatureService::SERVICE_KEY);
        $this->loggerService = $this->container->get(LoggerHelperService::SERVICE_KEY);
        $this->mapworksActionService = $this->container->get(MapworksActionService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->relatedActivitiesService = $this->container->get(RelatedActivitiesService::SERVICE_KEY);
        $this->studentAppointmentService = $this->container->get(StudentAppointmentService::SERVICE_KEY);
        $this->userManagementService = $this->container->get(UserManagementService::SERVICE_KEY);

        // DAOs
        $this->quickSearchDao = $this->container->get(QuickSearchDAO::DAO_KEY);

        // Repositories
        $this->activityCategoryRepository = $this->repositoryResolver->getRepository(ActivityCategoryRepository::REPOSITORY_KEY);
        $this->appointmentRecipientAndStatusRepository = $this->repositoryResolver->getRepository(AppointmentRecepientAndStatusRepository::REPOSITORY_KEY);
        $this->appointmentsRepository = $this->repositoryResolver->getRepository(AppointmentsRepository::REPOSITORY_KEY);
        $this->appointmentTeamsRepository = $this->repositoryResolver->getRepository(AppointmentsTeamsRepository::REPOSITORY_KEY);
        $this->calendarSharingRepository = $this->repositoryResolver->getRepository(CalendarSharingRepository::REPOSITORY_KEY);
        $this->contactInfoRepository = $this->repositoryResolver->getRepository(ContactInfoRepository::REPOSITORY_KEY);
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(MetadataListValuesRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->emailTemplateRepository = $this->repositoryResolver->getRepository(EmailTemplateRepository::REPOSITORY_KEY);
        $this->featureMasterLangRepository = $this->repositoryResolver->getRepository(FeatureMasterLangRepository::REPOSITORY_KEY);
        $this->officeHoursRepository = $this->repositoryResolver->getRepository(OfficeHoursRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->teamMembersRepository = $this->repositoryResolver->getRepository(TeamMembersRepository::REPOSITORY_KEY);
        $this->teamsRepository = $this->repositoryResolver->getRepository(TeamsRepository::REPOSITORY_KEY);
    }

    /**
     * Used for creating appointments
     *
     * @param AppointmentsDto $appointmentsDto
     * @return AppointmentsDto
     * @throws \Exception
     */
    public function create(AppointmentsDto $appointmentsDto)
    {
        $organizationId = $appointmentsDto->getOrganizationId();
        $this->rbacManager->checkAccessToOrganization($organizationId);
        $attendees = $appointmentsDto->getAttendees();
        $facultyId = $appointmentsDto->getPersonId();
        // finding out the list of students
        $studentArray = [];
        foreach ($attendees as $student) {
            $studentArray[] = $student->getStudentId();
        }

        $this->rbacManager->assertPermissionToEngageWithStudents($studentArray, $facultyId);

        $logContent = $this->loggerService->getLog($appointmentsDto);
        $this->logger->debug("Creating Appointment " . $logContent);
        $this->dateValidation($appointmentsDto->getSlotStart(), $appointmentsDto->getSlotEnd());
        $personIdProxy = $appointmentsDto->getPersonIdProxy();
        if ($personIdProxy) {
            try {
                $this->personService->findPerson($personIdProxy);
            } catch (\Exception $exp) {
                throw new ValidationException([
                    'PersonIdProxy Not Found.'
                ], 'PersonIdProxy Not Found.', 'Person_not_found');
            }
        }

        $startDate = $appointmentsDto->getSlotStart()->setTimeZone(new \DateTimeZone('UTC'));
        $endDate = $appointmentsDto->getSlotEnd()->setTimeZone(new \DateTimeZone('UTC'));

        foreach ($attendees as $attendee) {
            $studentId = $attendee->getStudentId();
            $overlapsExistingStudentAppointment = $this->appointmentRecipientAndStatusRepository->doAppointmentsExistWithinTimeframe($organizationId, $studentId, $startDate->format('Y-m-d H:i:s'), $endDate->format('Y-m-d H:i:s'), true);

            if ($overlapsExistingStudentAppointment) {
                $studentPersonObject = $this->personRepository->find($studentId);
                $studentFirstName = $studentPersonObject->getFirstname();
                $studentLastName = $studentPersonObject->getLastname();

                $message = "$studentFirstName $studentLastName has an existing appointment in this timeframe. Either adjust this appointment's start and end times, or the start and end times of the student's appointment.";
                throw new ValidationException([$message], $message);
            }
        }

        $appointmentsDto->setSlotStart($startDate);
        $appointmentsDto->setSlotEnd($endDate);
        $appointments = $this->appointmentsRepository->createAppointments($appointmentsDto);


        $teamShare = $appointmentsDto->getShareOptions()[0]->getTeamsShare();
        $teamsArray = $appointmentsDto->getShareOptions()[0]->getTeamIds();

        if ($teamShare && $appointments) {
            $this->addTeam($appointments, $teamsArray);
        }
        $person = $appointments->getPerson();
        $activityCategory = $appointments->getActivityCategory();

        // Update appointment_id in office_hours table
        if (!$appointmentsDto->getIsFreeStanding()) {
            $officehours = $this->officeHoursRepository->find($appointmentsDto->getOfficeHoursId());
            if (isset($officehours)) {
                $officehours->setAppointments($appointments);

            }
        }
        $this->officeHoursRepository->flush();

        // update in appointment_recepient_and_status table
        $taskType = "create";

        if (count($attendees) > 10) {
            $jobNum = uniqid();
            $bulkAppointmentJob = new BulkAppointmentJob();
            $bulkAppointmentJob->args = array(
                'jobNumber' => $jobNum,
                'attendees' => serialize($attendees),
                'appointments' => $appointments->getId(),
                'person' => $person->getId(),
                'personIdProxy' => $personIdProxy
            );
            $this->resque->enqueue($bulkAppointmentJob, true);

        } else {
            $this->updateAppointmentRecipientAndStatus($attendees, $appointments, $person, false, $taskType, [], $personIdProxy);
        }


        $appointmentsDto->setAppointmentId($appointments->getId());


        // Creating Alert Notification for Appointment_Created
        $event = "Appointment_Created";
        $this->alertNotificationService->createNotification($event, $activityCategory->getShortName(), $person, null, $appointments);
        // Resque job start here
        $job = new SendAppointmentReminderJob();
        $job->args = array(
            'appointment' => $appointments->getId()
        );
        // enqueue your job to run 1 day before appointment startDate

        $reminderDate = clone $appointments->getStartDateTime();
        $reminderDate->sub(new \DateInterval('PT24H'));
        $this->resque->enqueueAt($reminderDate, $job);
        // Resque job end here

        $this->markActivityLog($attendees, $appointments, $activityCategory, $facultyId);

        // Modification for adding it as related Activity..
        $activityLogId = $appointmentsDto->getActivityLogId();
        if (isset($activityLogId)) {
            $relatedActivitiesDto = new RelatedActivitiesDto();
            $relatedActivitiesDto->setActivityLog($activityLogId);
            $relatedActivitiesDto->setAppointment($appointments->getId());
            $relatedActivitiesDto->setOrganization($organizationId);
            $this->relatedActivitiesService->createRelatedActivities($relatedActivitiesDto);
        }

        // Sync to external calendar.
        $currentTime = new \DateTime('now');
        $this->calendarIntegrationService->syncOneOffEvent($organizationId, $facultyId, $appointments->getId(), 'appointment', 'create', $currentTime);

        return $appointmentsDto;
    }

    private function addTeam($appointment, $teamsArray)
    {
        $appointmentTeams = '';
        foreach ($teamsArray as $team) {
            if ($team->getIsTeamSelected()) {
                $appointmentsTeams = new AppointmentsTeams();
                $team = $this->teamsRepository->find($team->getId());
                $this->isExists($team, 'team_not_found', 'Team not found.');
                $appointmentsTeams->setAppointmentsId($appointment);
                $appointmentsTeams->setTeamsId($team);
                $appointmentTeams = $this->appointmentTeamsRepository->createAppointmentsTeams($appointmentsTeams);
            }
        }
        return $appointmentTeams;
    }

    /**
     * Check faculty permission with respect to student for an appointment - create/view
     *
     * @param int $personId - The ID of the person that this appointment belongs to.
     *                        One of: The logged in faculty, the managed faculty, the proxied-as faculty for an appointment, or the student who created it
     * @param array $attendees
     * @param string $mode - "view" for viewing, "create" for updating OR creating.
     * @param string $shareOptionsPermission - a string similar to the format 'booking-public-view'
     * @return bool
     */
    private function checkAppointmentPermissionToStudents($personId, $attendees, $mode, $shareOptionsPermission)
    {
        foreach ($attendees as $attendee) {

            // Avoid checking the faculty and student appointment access check if the student/attendee and personId are same.
            if ($personId != $attendee)
            {
                if (!$this->rbacManager->hasStudentAppointmentAccess($shareOptionsPermission, null, $attendee, $personId)) {
                    throw new AccessDeniedException("You do not have permission to $mode appointment for student $attendee");
                }
            }

        }

        return true;
    }

    /**
     * Method creating activity log for appointment
     *
     * @param array $attendees
     * @param Appointments $appointments
     * @param ActivityCategory $activityCategory
     * @param int $facultyId
     * @param array $existingAttendees
     */
    private function markActivityLog($attendees, $appointments, $activityCategory, $facultyId, $existingAttendees = [])
    {
        foreach ($attendees as $attendee) {
            if ($attendee->getIsSelected() && (!in_array($attendee->getStudentId(), $existingAttendees))) {
                $activityLogDto = new ActivityLogDto();
                $appointmentActivityDate = $appointments->getStartDateTime();
                $appointmentActivityDate->setTimezone(new \DateTimeZone('UTC'));
                $activityLogDto->setActivityDate($appointmentActivityDate);
                $activityLogDto->setActivityType("A");
                $appointmentId = $appointments->getId();
                $activityLogDto->setAppointments($appointmentId);
                $orgId = $appointments->getOrganization()->getId();
                $activityLogDto->setOrganization($orgId);
                $activityLogDto->setPersonIdFaculty($facultyId);
                $studentId = $attendee->getStudentId();
                $activityLogDto->setPersonIdStudent($studentId);
                $reasonText = $activityCategory->getShortName();
                $activityLogDto->setReason($reasonText);
                $this->activityLogService->createActivityLog($activityLogDto);

                // add notification for student
                $personStudentObj = $this->personService->findPerson($studentId);
                $this->alertNotificationService->createNotification("Appointment_Created", $activityCategory->getShortName(), $personStudentObj, null, $appointments);

            }
        }
    }

    /**
     * Cancel scheduled appointment based on organization,appointment Id
     * returns cancelled appointment Id.
     *
     * @param int $organizationId
     * @param int $appointmentId
     * @param boolean $isJob
     * @throws ValidationException
     * @return integer
     */
    public function cancelAppointment($organizationId, $appointmentId, $isJob = false)
    {
        $officehoursData = $this->officeHoursRepository->findOneBy([
            'appointments' => $appointmentId
        ]);
        if (!$isJob) {
            $this->rbacManager->checkAccessToOrganization($organizationId);
        }

        $this->logger->debug(">>>> Cancel Appointment for Organization Id" . $organizationId . "AppointmentID" . $appointmentId);
        $appointmentEntity = $this->appointmentsRepository->find($appointmentId);
        $this->isObjectExist($appointmentEntity, 'Appointment Not Found.', 'appointment_not_found');

        $startDateTime = $appointmentEntity->getStartDateTime();
        $endDateTime = $appointmentEntity->getEndDateTime();
        $currentDateTime = new \DateTime('now');

        if ($currentDateTime > $startDateTime) {
            $this->logger->error("Appointments Service  -  CancelAppointment - Past Appointment can not be cancelled ");
            throw new ValidationException([
                'Past Appointment can not be cancelled.'
            ], 'Past Appointment can not be cancelled.', 'appointment_cancel_error');
        }

        $staffFirstName = $appointmentEntity->getPerson()->getFirstname() . " " . $appointmentEntity->getPerson()->getLastname();
        $this->activityLogService->deleteActivityLogByType($appointmentId, 'A');
        $this->appointmentsRepository->remove($appointmentEntity);
        $this->appointmentsRepository->flush();
        // Fetch student list for email before soft delete
        $recipientPerson = $this->appointmentRecipientAndStatusRepository->findBy([
            'appointments' => $appointmentId
        ]);

        // soft delete appointment recipient when appointment is cancelled
        $appointmentRASEntity = $this->appointmentRecipientAndStatusRepository->findBy([
            'appointments' => $appointmentId
        ]);
        $this->removeRAS($appointmentRASEntity);
        $this->removeFromOfficeHours($appointmentId);
        $this->alertNotificationService->deleteAlertByActivityId($appointmentId, 'appointments');
        // Send email to student list
        if (count($recipientPerson) > 0) {
            $emailMessage['subject'] = "Appointment cancelled";
            $emailMessage['email_key'] = "Appointment_Cancel_Staff_to_Student";

            $tokenValues = array();
            $tokenValues['staff_name'] = $staffFirstName;

            $studentAppointmentPageUrl = $this->ebiConfigService->generateCompleteUrl('StudentDashboard_AppointmentPage', $organizationId);
            if ($studentAppointmentPageUrl) {
                $tokenValues['student_dashboard'] = $studentAppointmentPageUrl;
            } else {
                $tokenValues['student_dashboard'] = "";
            }

            $formattedStartDateTime = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, $startDateTime, 'm/d/Y h:ia');
            $formattedEndDateTime = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, $endDateTime, 'h:ia e');

            $tokenValues['app_datetime'] = "$formattedStartDateTime to $formattedEndDateTime";

            // Including sky factor mapworks logo in email template
            $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
            $tokenValues['Skyfactor_Mapworks_logo'] = "";
            if ($systemUrl) {
                $tokenValues['Skyfactor_Mapworks_logo'] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
            }
            foreach ($recipientPerson as $rPerson) {
                $facultyId = $rPerson->getPersonIdFaculty()->getId();
                $faculty = $rPerson->getPersonIdFaculty();
                $personStudent = $this->personService->findPerson($rPerson->getPersonIdStudent());
                if (isset($personStudent)) {
                    $tokenValues['student_name'] = $personStudent->getFirstname();
                    $this->sendToStudents($personStudent, $organizationId, $emailMessage, $tokenValues, $isJob);
                }
            }
        }

        // Creating Alert Notification for Appointment_Cancelled
        $activityCategory = $appointmentEntity->getActivityCategory();
        $person = $appointmentEntity->getPerson();
        $event = "Appointment_Cancelled";
        $this->alertNotificationService->createNotification($event, $activityCategory->getShortName(), $person, null, $appointmentEntity);
        $this->logger->info(">>>> Cancel Appointment for Organization Id");
        if(empty($facultyId)) {
            $facultyId = $appointmentEntity->getPerson()->getId();
        }

        // Remove from external calendar.
        $externalCalendarEventId = $appointmentEntity->getGoogleAppointmentId();
        $officeHourId = NULL;
        if ($officehoursData) {
            $officeHourId = $officehoursData->getId();
        }
        $this->calendarIntegrationService->syncOneOffEvent($organizationId, $facultyId, $appointmentEntity->getId(), 'appointment', 'delete', $externalCalendarEventId, $officeHourId);
        return $appointmentId;
    }

    private function removeRAS($appointmentRASEntity)
    {
        if (isset($appointmentRASEntity) && count($appointmentRASEntity) > 0) {
            foreach ($appointmentRASEntity as $aRASEntity) {
                $this->appointmentRecipientAndStatusRepository->remove($aRASEntity);
            }
        }
        $this->appointmentRecipientAndStatusRepository->flush();
    }

    private function removeFromOfficeHours($appointmentId)
    {
        $officehours = $this->officeHoursRepository->findBy([
            AppointmentsConstant::FIELD_APPOINTMENT => $appointmentId
        ]);
        if (isset($officehours)) {
            foreach ($officehours as $officehour) {
                $officehour->setAppointments(null);
            }
        }
    }

    /**
     * Edit Appointment
     *
     * @param AppointmentsDto $appointmentsDto
     * @param bool $isTest
     * @param int $loggedInPersonId
     * @return AppointmentsDto
     * @throws SynapseValidationException | AccessDeniedException
     */
    public function editAppointment(AppointmentsDto $appointmentsDto, $isTest = false, $loggedInPersonId = null)
    {
        if (!$isTest) {
            $this->rbacManager->checkAccessToOrganization($appointmentsDto->getOrganizationId());
        }
        $organizationId = $appointmentsDto->getOrganizationId();
        $creatorId = $appointmentsDto->getPersonId();
        $attendees = $appointmentsDto->getAttendees();

        foreach($attendees as $student){
            $studentArray[] = $student->getStudentId();
        }

        $this->rbacManager->assertPermissionToEngageWithStudents($studentArray);

        $shareOptionPermission = $this->getShareOptionPermission($appointmentsDto, 'booking');
        $facultyId = $this->appointmentRecipientAndStatusRepository->getAppointmentFaculty($appointmentsDto->getAppointmentId());
        if ($creatorId != $loggedInPersonId) {

            $this->checkAppointmentPermissionToStudents($facultyId, $studentArray, 'create', $shareOptionPermission);
        }

        // Getting Person Object - personId
        $person = $this->personService->findPerson($appointmentsDto->getPersonId());
        //To get faculty data from RAS if appointment created by student
        $personFaculty = $this->personService->findPerson($facultyId);
        $organization = $person->getOrganization();

        $dateNow = new \DateTime('now');
        $editSlotStart = $appointmentsDto->getSlotStart();
        $editSlotStart->setTimezone(new \DateTimeZone('UTC'));

        $newStartDateTime = $appointmentsDto->getSlotStart()->setTimeZone(new \DateTimeZone('UTC'));
        $newEndDateTime = $appointmentsDto->getSlotEnd()->setTimeZone(new \DateTimeZone('UTC'));

        $appointmentId = $appointmentsDto->getAppointmentId();
        $appointmentObject = $this->appointmentsRepository->find($appointmentId);

        $existingStartDateTime = $appointmentObject->getStartDateTime();
        $existingEndDateTime = $appointmentObject->getEndDateTime();

        //Get all existing appointment attendees for later comparison.
        $existingAppointmentAttendeeIds = [];
        $existingAppointmentRecipientAndStatusArray = $this->appointmentRecipientAndStatusRepository->findBy(['appointments' => $appointmentObject]);

        //Build an array of the student IDs that are already attending that appointment
        foreach ($existingAppointmentRecipientAndStatusArray as $existingAppointmentRecipientAndStatus) {
            $existingAppointmentAttendeeIds[] = $existingAppointmentRecipientAndStatus->getPersonIdStudent()->getId();
        }

        //Build an array of student IDs that the faculty wants to attend the appointment.
        $desiredAppointmentAttendeeIds = [];
        foreach ($attendees as $attendee) {
            $desiredAppointmentAttendeeIds[] = $attendee->getStudentId();
        }

        //Get the difference between the current list of students and the new list of students.
        $studentsIdsAddedToAppointment = array_diff($desiredAppointmentAttendeeIds, $existingAppointmentAttendeeIds);

        //If the start date or end dates have changed, or if there's a difference in the appointment attendees, verify there are no overlapping appointments for the new students.
        if ($existingStartDateTime != $newStartDateTime || $existingEndDateTime != $newEndDateTime || !empty($studentsIdsAddedToAppointment)) {
            foreach ($desiredAppointmentAttendeeIds as $desiredAppointmentAttendeeId) {
                $overlapsExistingStudentAppointment = $this->appointmentRecipientAndStatusRepository->doAppointmentsExistWithinTimeframe($organizationId, $desiredAppointmentAttendeeId, $newStartDateTime->format('Y-m-d H:i:s'), $newEndDateTime->format('Y-m-d H:i:s'), true, $appointmentId);

                if ($overlapsExistingStudentAppointment) {
                    $studentPersonObject = $this->personRepository->find($desiredAppointmentAttendeeId);
                    $studentFirstName = $studentPersonObject->getFirstname();
                    $studentLastName = $studentPersonObject->getLastname();

                    $message = "$studentFirstName $studentLastName has an existing appointment in this timeframe. Either adjust this appointment's start and end times, or the start and end times of the student's appointment.";
                    throw new SynapseValidationException($message);
                }
            }
        }

        $this->dateValidation($dateNow, $newStartDateTime, 'pastDate');
        // fetching appointment from db to update
        $appointments = $this->appointmentsRepository->find($appointmentsDto->getAppointmentId());
        // To check whether the appointment available for edit
        $this->isObjectExist($appointments, "Appointment Not Found.", "appointment_not_found");
        // here you will check if appointment Access
        if (!$this->rbacManager->hasAssetAccess([
            "booking-public-create",
            "booking-private-create",
            "booking-teams-create"
        ], $appointments, $facultyId, $appointmentAccess = true)
        ) {
            $this->logger->error("Appointment Service - Edit Appointment - Do not have permission to edit this Appointment");
            throw new AccessDeniedException('Appointment Edit exception');
        }

        // To check slot start date whether it is a past appointment in DB

        $dbStartDate = $appointments->getStartDateTime();
        $this->dateValidation($dateNow, $dbStartDate, "pastDate");

        // Validating referencing values are available
        $this->isObjectExist($organization, "Organization Not Found.", "organization_not_found");
        $this->isObjectExist($person, "Person Not Found.", "Person_not_found");
        // Call setters to set the Appointments property values
        $appointments->setPerson($person);
        // Getting Person Object - personProxy
        $personProxy = ($appointmentsDto->getPersonIdProxy() == 0 ? NULL : $appointmentsDto->getPersonIdProxy());
        if ($personProxy) {
            $personProxy = $this->personService->findPerson($personProxy);
            $appointments->setPersonIdProxy($personProxy);
        }
        // Call setters to set the Appointments property values
        $appointments->setOrganization($organization);
        $appointments->setTitle($appointmentsDto->getDetail());
        $activityCategory = $this->activityCategoryRepository->find($appointmentsDto->getDetailId());
        $appointments->setActivityCategory($activityCategory);
        $appointments->setLocation($appointmentsDto->getLocation());
        $appointments->setDescription($appointmentsDto->getDescription());
        $appointments->setIsFreeStanding($appointmentsDto->getIsFreeStanding());
        $appointments->setType($appointmentsDto->getType());

        $appointmentsDto->setSlotStart($newStartDateTime);
        $appointmentsDto->setSlotEnd($newEndDateTime);
        $appointments->setStartDateTime($newStartDateTime);
        $appointments->setEndDateTime($newEndDateTime);
        /*----appointment sharing-----------*/
        $appointments->setAccessPrivate($appointmentsDto->getShareOptions()[0]->getPrivateShare());
        $appointments->setAccessPublic($appointmentsDto->getShareOptions()[0]->getPublicShare());
        $teamShare = $appointmentsDto->getShareOptions()[0]->getTeamsShare();
        $appointments->setAccessTeam($teamShare);

        $teamsArray = $appointmentsDto->getShareOptions()[0]->getTeamIds();

        $appointmentTeams = $this->appointmentTeamsRepository->getAppointmentsTeamIds($appointmentsDto->getAppointmentId());
        $teamIds = array_map('current', $appointmentTeams);
        $newTeamArray = array();
        if ($teamsArray) {
            foreach ($teamsArray as $teamArray) {
                if ($teamArray->getIsTeamSelected()) {
                    if (!in_array($teamArray->getId(), $teamIds)) {
                        $newTeamArray[] = $teamArray;
                    }
                } else {
                    if (in_array($teamArray->getId(), $teamIds)) {
                        $appointmentTeam = $this->appointmentTeamsRepository->findBy([
                            'appointmentsId' => $appointmentsDto->getAppointmentId(),
                            'teamsId' => $teamArray->getId()
                        ]);
                        if (!empty($appointmentTeam[0])) {
                            $this->appointmentTeamsRepository->deleteAppointmentTeam($appointmentTeam[0]);
                        }
                    }
                }
            }
        }
        if ($teamShare && $appointments && $newTeamArray) {
            $this->addTeam($appointments, $newTeamArray);
        }
        //appointment sharing
        $this->appointmentsRepository->flush();

        // Editing students list "AppointmentRecepientAndStatus" * Soft delete exiting students * Create as fresh
        $appointmentRASEntity = $this->appointmentRecipientAndStatusRepository->findBy([
            "appointments" => $appointmentsDto->getAppointmentId()
        ]);
        $existingAttendees = [];
        if (isset($appointmentRASEntity) && (count($appointmentRASEntity) > 0)) {
            foreach ($appointmentRASEntity as $appointmentRecipient) {
                $existingAttendees[] = $appointmentRecipient->getPersonIdStudent()->getId();
            }
        }

        $this->removeRAS($appointmentRASEntity);
        $attendees = $appointmentsDto->getAttendees();
        $this->markActivityLog($attendees, $appointments, $activityCategory, $facultyId, $existingAttendees);
        $taskType = "update";
        $this->updateAppointmentRecipientAndStatus($attendees, $appointments, $personFaculty, false, $taskType, $existingAttendees, $personProxy);
        $officeHours = $this->officeHoursRepository->findBy([
            "appointments" => $appointmentsDto->getAppointmentId()
        ]);
        // Creating Alert Notification for Appointment_Edited

        $event = "Appointment_Edited";
        $this->alertNotificationService->createNotification($event, $activityCategory->getShortName(), $person, null, $appointments);

        if (isset($officeHours)) {
            foreach ($officeHours as $officeHour) {
                $updatedAppointmentStartTime = $appointments->getStartDateTime()->setTimeZone(new \DateTimeZone('UTC'));
                $updatedAppointmentEndTime = $appointments->getEndDateTime()->setTimeZone(new \DateTimeZone('UTC'));
                //if any changes from start or end date, make the appointment as free standing
                if (($updatedAppointmentStartTime == $officeHour->getSlotStart()) && ($updatedAppointmentEndTime == $officeHour->getSlotEnd())) {
                    $appointmentsDto->setOfficeHoursId($officeHour->getId());
                } else {
                    $officeHour->setAppointments(null);
                    $appointments->setIsFreeStanding(true);
                    $appointments->setType('F');
                    $appointmentsDto->setIsFreeStanding(true);
                    $appointmentsDto->setType('F');
                    $appointmentsDto->setOfficeHoursId($officeHour->getId());
                    $this->calendarIntegrationService->syncOneOffEvent($organizationId, $facultyId, $officeHour->getId(), 'office_hour', 'create', $dateNow);
                }
                $this->officeHoursRepository->flush();
            }
        }
        // If sync is enabled then appointment changes has to be synced to external calendar.
        $this->calendarIntegrationService->syncOneOffEvent($organizationId, $facultyId, $appointments->getId(), 'appointment', 'update', $dateNow);

        return $appointmentsDto;
    }

    /**
     * Sets the recipients and statuses for an appointment, whether new or updated.
     * The "taskType" string refers to whether the appointment is new or updated.
     *
     * @param array $attendees
     * @param Appointments $appointments
     * @param Person $person
     * @param bool $hasAttendedFlag
     * @param string $taskType - 'create' or 'update'
     * @param array $existingStudents
     * @param int|null $proxyUserId
     * @param bool $isJob
     * @throws AccessDeniedException
     */
    public function updateAppointmentRecipientAndStatus($attendees, $appointments, $person, $hasAttendedFlag = false, $taskType, $existingStudents, $proxyUserId = null, $isJob = false)
    {
        $organization = $person->getOrganization();
        $organizationId = $organization->getId();

        if (count($attendees) > 0) {
            $this->logger->info("--------------------------1");
            $userRole = "Staff";
            $emailMessage['subject'] = "Appointment created";
            $emailMessage['email_key'] = "Appointment_Book_" . $userRole . '_to_Student';

            $tokenValues = array();
            $tokenValues['staff_name'] = $person->getFirstname() . " " . $person->getLastname();

            $studentAppointmentPageUrl = $this->ebiConfigService->generateCompleteUrl('StudentDashboard_AppointmentPage', $organizationId);
            if ($studentAppointmentPageUrl) {
                $tokenValues['student_dashboard'] = $studentAppointmentPageUrl;
            } else {
                $tokenValues['student_dashboard'] = "";
            }

            $this->logger->info("--------------------------2");
            $fromDate = $appointments->getStartDateTime();
            $toDate = $appointments->getEndDateTime();

            $formattedFromDate = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, $fromDate, 'm/d/Y h:ia');
            $formattedToDate = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, $toDate, 'h:ia e');

            $appointmentDateTimeToken = "$formattedFromDate to $formattedToDate";
            $tokenValues['app_datetime'] = $appointmentDateTimeToken;
            $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
            $tokenValues['Skyfactor_Mapworks_logo'] = "";
            if ($systemUrl) {
                $tokenValues['Skyfactor_Mapworks_logo'] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
            }
            $this->logger->info("--------------------------3");

            foreach ($attendees as $attendee) {
                $appointmentRAStatus = new AppointmentRecepientAndStatus();
                $studentId = $attendee->getStudentId();
                if (!$proxyUserId) {

                    // This is done because getShareOptionPermission() normally gotten
                    // needs the appointmentsDTO to work. At this point, the appointments
                    // is an entity object and we need to mock it away
                    if ($appointments->getAccessTeam()) {
                        $shareOptionPermissions = "booking-teams-create";
                    } else if ($appointments->getAccessPrivate()) {
                        $shareOptionPermissions = "booking-private-create";
                    } else {
                        $shareOptionPermissions = "booking-public-create";
                    }

                    $featureId = $this->featureMasterLangRepository->findOneBy(['featureName' => 'Booking']);
                    $featureAccess = $this->featureService->verifyFacultyAccessToStudentForFeature($person->getId(), $organization->getId(), $studentId, $shareOptionPermissions, $featureId->getId());
                    if (!$featureAccess) {
                        if ($isJob) {
                            // silently fail if this is a job
                            continue;
                        } else {
                            // throw an exception if the person
                            // does not have access to the student
                            throw new AccessDeniedException('You do not have permission to create an Appointment');
                        }
                    }
                }

                $this->logger->info("--------------------------4");
                $personStudent = $this->personService->findPerson($studentId);
                if ($personStudent) {
                    $this->logger->info("--------------------------5");
                    if ($attendee->getIsSelected()) {
                        if ($taskType == "update") {
                            if (in_array($studentId, $existingStudents)) {
                                $emailMessage['subject'] = "Appointment updated";
                                $emailMessage['email_key'] = "Appointment_Update_" . $userRole . '_to_Student';
                            } else {
                                $emailMessage['subject'] = "Appointment created";
                                $emailMessage['email_key'] = "Appointment_Book_" . $userRole . '_to_Student';
                            }
                        } elseif ($taskType == "create") {
                            $lastActivityDate = new \DateTime('now', new \DateTimeZone('UTC'));
                            $lastActivity = $lastActivityDate->format('m/d/y') . "- Appointment";
                            $personStudent->setLastActivity($lastActivity);
                        }

                        $appointmentRAStatus->setOrganization($organization);
                        $appointmentRAStatus->setAppointments($appointments);
                        $appointmentRAStatus->setPersonIdFaculty($person);
                        $appointmentRAStatus->setPersonIdStudent($personStudent);
                        $appointmentRAStatus->setHasAttended($hasAttendedFlag);
                        $this->logger->info("--------------------------6");
                        $this->appointmentRecipientAndStatusRepository->createAppointmentsRAStatus($appointmentRAStatus);
                        $this->logger->info("--------------------------7");
                    } else {
                        $emailMessage['subject'] = "Appointment cancelled";
                        $emailMessage['email_key'] = "Appointment_Cancel_" . $userRole . '_to_Student';
                        //To update activity log for removed student
                        if ($taskType == "update" && in_array($studentId, $existingStudents)) {
                            $appointmentId = $appointments->getId();
                            $this->activityLogService->deleteActivityLogByType($appointmentId, 'A', $studentId);
                        }
                    }
                    $tokenValues['student_name'] = $personStudent->getFirstname();
                    $this->sendToStudents($personStudent, $organization->getId(), $emailMessage, $tokenValues, $isJob);
                }
            }
            // After finishing bulk action send notification to logged in person
            if ($isJob) {
                $this->alertNotificationService->createNotification('bulk-action-completed', count($attendees) . ' appointments have been created successfully ', $this->personRepository->findOneById($person->getId()), null, $appointments);
            }
        }
        $this->appointmentRecipientAndStatusRepository->flush();
    }

    /**
     * Send Email to students
     *
     * @param Person $personStudent
     * @param int $organizationId
     * @param array $emailMessage
     * @param array $tokenValues eg: array(staff_name, student_dashboard, app_datetime, Skyfactor_Mapworks_logo, student_name, app_location, staff_email, staff_mobile)
     * @param bool $isJob
     * @throws SynapseValidationException
     */
    public function sendToStudents($personStudent, $organizationId, $emailMessage, $tokenValues, $isJob = false)
    {
        if (!$isJob) {
            $this->rbacManager->checkAccessToOrganization($organizationId);
        }

        $this->logger->debug(">>>> Send Email to Person Student for" . "organization Id" . $organizationId);
        $studentContactEmail = $personStudent->getUsername();
        if (!$studentContactEmail) {
            throw new SynapseValidationException("Student contact email not found");
        }
        $emailKey = $emailMessage['email_key'];
        $emailTemplateObject = $this->emailTemplateRepository->findOneBy(['emailKey' => $emailKey]);
        if (!$emailTemplateObject) {
            throw new SynapseValidationException("Email template key $emailKey not found");
        }
        $emailTemplateLangObject = $this->emailTemplateLangRepository->findOneBy(['emailTemplate' => $emailTemplateObject]);
        if (!$emailTemplateLangObject) {
            throw new SynapseValidationException("Email template not found");
        }
        $emailBody = $emailTemplateLangObject->getBody();
        $emailBody = $this->emailService->generateEmailMessage($emailBody, $tokenValues);
        $bcc = $emailTemplateLangObject->getEmailTemplate()->getBccRecipientList();
        $subject = $emailTemplateLangObject->getSubject();
        $from = $emailTemplateLangObject->getEmailTemplate()->getFromEmailAddress();
        $emailParameters = [
            'from' => $from,
            'subject' => $subject,
            'bcc' => $bcc,
            'body' => $emailBody,
            'to' => $studentContactEmail,
            'emailKey' => $emailKey,
            'organizationId' => $organizationId
        ];
        $emailInstance = $this->emailService->sendEmailNotification($emailParameters);
        $this->emailService->sendEmail($emailInstance);
        $this->logger->info(">>>> Send Email to Person Student");
    }


    /**
     * View appointment
     *
     * @param int $organizationId
     * @param int $personId - The ID of the person that this appointment is supposed to belong to.
     *                        One of: The logged in faculty, the managed faculty, the proxied-as faculty for an appointment, or the student who created it
     * @param int $appointmentId
     * @return array
     * @throws AccessDeniedException | RestException | InvalidArgumentException
     */
    public function viewAppointment($organizationId, $personId, $appointmentId)
    {
        $this->rbacManager->checkAccessToOrganization($organizationId);

        // TODO: Fix the appointment table and the recipient_and_status tables so that the faculty ID is either part of the main appointment record, or separated into a faculty table.
        $appointmentToView = $this->appointmentsRepository->viewAppointment($organizationId, $appointmentId);

        if (!$appointmentToView) {
            $userMessage = 'Appointment not found. Please contact client services if this continues.';
            throw new SynapseValidationException($userMessage);
        }

        $scheduledFacultyId = $appointmentToView['faculty_id'];
        $ownerId = $appointmentToView['person_id'];
        $delegatedCreatorId = $appointmentToView['person_id_proxy'];

        $orgAcademicYearId =  null;
        $currentAcademicYear = $this->academicYearService->findCurrentAcademicYearForOrganization($organizationId);
        if (isset($currentAcademicYear['org_academic_year_id'])) {
            $orgAcademicYearId = $currentAcademicYear['org_academic_year_id'];
        }
        $attendees = $this->appointmentRecipientAndStatusRepository->getParticipantAttendeesForAppointment($organizationId, $scheduledFacultyId, $appointmentId, $orgAcademicYearId);

        $studentIdArray = [];
        if (!$attendees) {
            $userMessage = 'Appointment attendees not found. Please contact client services if this continues.';
            throw new SynapseValidationException($userMessage);
        } else {
            $studentIdArray = array_column($attendees, "student_id");
        }

        // Do some complex permissions checking to see if the logged in person is allowed to view the given appointment
        $personIsAllowedToViewThisAppointment = false;

        // Is the person the one this appointment was scheduled for? If so, allow them to see it
        if ($personId == $scheduledFacultyId) {
            $personIsAllowedToViewThisAppointment = true;
            // Is the person the owner of this appointment? If so, allow them to see it
        } else if ($personId == $ownerId) {
            $personIsAllowedToViewThisAppointment = true;
            // Is the person the original creator of this appointment? If so, allow them to see it
        } else if ($personId == $delegatedCreatorId) {
            $personIsAllowedToViewThisAppointment = true;
        } else {
            // Is the person allowed to see appointments for the owner through delegation? If so, allow them to see it
            $facultyAllowedToManageCalendarForOwner = $this->calendarSharingRepository->getSelectedProxyUsers($ownerId);
            $facultyIdsAllowedToManageCalendarForOwner = (count($facultyAllowedToManageCalendarForOwner) > 0) ? array_column($facultyAllowedToManageCalendarForOwner, 'delegated_to_person_id') : [];
            if (in_array($personId, $facultyIdsAllowedToManageCalendarForOwner)) {
                $personIsAllowedToViewThisAppointment = true;
            } else {
                // Is this person given access to the appointment via some other kind of sharing option? If so, allow them to see it.
                if ($appointmentToView['access_public']) {
                    $appointmentSharingOption = "booking-public-view";
                } else if ($appointmentToView['access_team']) {
                    $appointmentSharingOption = "booking-teams-view";
                } else if ($appointmentToView['access_private']) {
                    $appointmentSharingOption = "booking-private-view";
                } else {
                    throw new InvalidArgumentException("No sharing options on appointmentId $appointmentId set. Bad data present.");
                }

                $personIsAllowedToViewThisAppointment = $this->checkAppointmentPermissionToStudents($personId, $studentIdArray, 'view', $appointmentSharingOption);
            }
        }

        // Out of all of the above possibilities, none of them are true. Explode.
        if (!$personIsAllowedToViewThisAppointment) {
            throw new AccessDeniedException();
        }

        // Reformat share options with the property names the JSON needs for the front end
        $shareOptions = [];
        $shareOptions['private_share'] = $appointmentToView['access_private'];
        $shareOptions['public_share'] = $appointmentToView['access_public'];
        $shareOptions['teams_share'] = $appointmentToView['access_team'];

        if ($appointmentToView['access_team']) {
            $shareOptions['team_ids'] = $this->getAppointmentTeam($appointmentToView);
        }

        // Determine if the appointment happened in the past or not for setting in the response JSON
        $currentDateTime = new \DateTime('now');
        if ($currentDateTime > $appointmentToView['slot_start']) {
            $isPastAppointment = true;
        } else {
            $isPastAppointment = false;
        }
        $attendeeList = [];
        if (!empty($attendees)) {
            foreach ($attendees as $attendee) {
                $attendee['is_attended'] = ($attendee['is_attended'] == 1) ? true : false;
                $attendeeList[] = $attendee;
            }
        }
        // Build the formatted response array for converting into JSON in the controller
        $responseArray = [];
        $responseArray['share_options'][] = $shareOptions;
        $responseArray['attendees'] = $attendeeList;
        $responseArray['appointment_id'] = $appointmentToView['appointment_id'];
        $responseArray['person_id'] = $appointmentToView['person_id'];
        $responseArray['organization_id'] = $appointmentToView['organization_id'];
        $responseArray['detail'] = $appointmentToView['detail'];
        $responseArray['detail_id'] = $appointmentToView['detail_id'];
        $responseArray['location'] = $appointmentToView['location'];
        $responseArray['description'] = $appointmentToView['description'];
        $responseArray['office_hours_id'] = $appointmentToView['office_hours_id'];
        $responseArray['is_free_standing'] = $appointmentToView['is_free_standing'];
        $responseArray['pcs_calendar_id'] = $appointmentToView['google_appointment_id'];
        $responseArray['type'] = $appointmentToView['type'];
        $responseArray['slot_start'] = $appointmentToView['slot_start'];
        $responseArray['slot_end'] = $appointmentToView['slot_end'];
        $responseArray['is_past'] = $isPastAppointment;

        return $responseArray;
    }


    /**
     * get Appointment teams data
     * @param unknown $appointment
     * @return multitype:multitype:boolean unknown
     */
    private function getAppointmentTeam($appointment)
    {
        $teamAppointment = array();
        $teamDtoData = array();
        $appointmentTeams = $this->appointmentTeamsRepository->getAppointmentsTeamIds($appointment['appointment_id']);
        $teamAppointment = array_map('current', $appointmentTeams);
        $teams = $this->teamMembersRepository->getTeams($appointment['person_id']);
        $teamShare = $appointment['access_team'];
        if ($teamShare && !empty($teams)) {
            foreach ($teams as $team) {
                $team_id = $team['team_id'];
                $teamDto = [];
                $teamDto['id'] = $team_id;
                $teamDto['team_name'] = $team['team_name'];
                if (in_array($team_id, $teamAppointment)) {
                    $teamDto['is_team_selected'] = true;
                } else {
                    $teamDto['is_team_selected'] = false;
                }
                $teamDtoData[] = $teamDto;
            }
        }
        return $teamDtoData;
    }

    public function saveAppointmentAttendees(AppointmentsDto $appointmentsDto)
    {
        $this->rbacManager->checkAccessToOrganization($appointmentsDto->getOrganizationId());
        $logContent = $this->loggerService->getLog($appointmentsDto);
        $this->logger->debug(">>>> Save Appointment Attendees" . $logContent);
        // Instance for Appointments entity
        $appointments = $this->appointmentsRepository->find($appointmentsDto->getAppointmentId());
        $this->isObjectExist($appointments, AppointmentsConstant::APPOINTMENT_NOT_FOUND, AppointmentsConstant::APPOINTMENT_NOT_FOUND_KEY);
        $person = $this->personService->findPerson($appointmentsDto->getPersonId());
        $organization = $person->getOrganization();
        $this->isObjectExist($organization, AppointmentsConstant::ORG_NOT_FOUND, AppointmentsConstant::ORG_NOT_FOUND_KEY);
        $this->isObjectExist($person, AppointmentsConstant::PERSON_NOT_FOUND, AppointmentsConstant::PERSON_NOT_FOUND_KEY);
        $appointments->setPerson($person);
        $personProxy = ($appointmentsDto->getPersonIdProxy() == 0 ? NULL : $appointmentsDto->getPersonIdProxy());
        if ($personProxy) {
            $personProxy = $this->personService->findPerson($personProxy);
            $appointments->setPersonIdProxy($personProxy);
        }
        // Call setters to set the Appointments property values
        $appointments->setOrganization($organization);
        $attendees = $appointmentsDto->getAttendees();
        if (count($attendees) > 0) {
            foreach ($attendees as $attendee) {
                $student_id = $attendee->getStudentId();
                $personStudent = $this->personService->findPerson($student_id);
                $appointmentRAStatus = $this->appointmentRecipientAndStatusRepository->findBy([
                    AppointmentsConstant::FIELD_APPOINTMENT => $appointments,
                    'personIdStudent' => $personStudent
                ]);
                if ($appointmentRAStatus) {
                    foreach ($appointmentRAStatus as $appointment_rsa) {
                        $appointment_rsa->setHasAttended($attendee->getIsAttended());
                    }
                }
            }
        }
        $this->appointmentRecipientAndStatusRepository->flush();
        $appointmentsDto->setAppointmentId($appointmentsDto->getAppointmentId());
        $this->logger->info(">>>> Save Appointment Attendees");
        return $appointmentsDto;
    }

    public function checkIfActAsProxy($userId)
    {
        $this->logger->debug(">>>>Check If UserId" . $userId . " can Act As Proxy");
        $canActAsProxy = false;
        $checkIfDelegateUser = $this->calendarSharingRepository->findOneBy(array(
            'personIdSharedto' => $userId,
            'isSelected' => 1
        ));
        if ($checkIfDelegateUser) {
            $canActAsProxy = true;
        }
        $this->logger->info(">>>>Check If UserId can Act As Proxy");
        return $canActAsProxy;
    }

    /**
     * Listing appointments/office hours for a particular user for the given frequency range (past, others)
     *
     * @param int $organizationId
     * @param int $personId
     * @param string $timePeriod
     * @return AppointmentsReponseDto
     */
    public function getAppointmentsByUser($organizationId, $personId, $timePeriod)
    {
        $pcsCalendarId = [];
        $this->rbacManager->checkAccessToOrganization($organizationId);

        $person = $this->personService->findPerson($personId);
        $currentDate = new \DateTime('now');
        $currentDate->setTimezone(new \DateTimeZone('UTC'));
        $fromDate = $currentDate->format('Y-m-d');
        $currentDateTime = $currentDate->format('Y-m-d H:i:s');
        $dateRange = $this->dateUtilityService->getDateRange($timePeriod, $fromDate);
        $fromDate = $dateRange['from_date'];
        $toDate = $dateRange['to_date'];

        $organization = $person->getOrganization();
        // Validating referencing values are available
        $this->isObjectExist($organization, 'Organization Not Found.', 'organization_not_found');
        $this->isObjectExist($person, 'Person Not Found.', 'Person_not_found');

        $orgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);
        $timeSlots = $this->officeHoursRepository->getUsersAppointments($personId, $fromDate, $toDate, $timePeriod, $currentDateTime, $orgAcademicYearId);

        $calendar = [];
        $calendarSettings = $this->calendarIntegrationService->facultyCalendarSettings($organizationId, $personId);
        $appointmentResponse = new AppointmentsReponseDto();
        if (count($timeSlots) > 0) {
            $appointmentResponse->setPersonId($personId);
            $appointmentResponse->setOrganizationId($organizationId);
            $appointmentResponse->setFirstName($person->getFirstname());
            $appointmentResponse->setLastName($person->getLastname());
            $appointmentResponse->setPersonIdProxy((int)$timeSlots[0]['person_id_proxy']);
            foreach ($timeSlots as $timeSlot) {
                $calendarSlotDto = new CalendarTimeSlotsReponseDto();

                if ($timeSlot['office_hours_id'] > 0) {
                    $officeHoursId = (int)$timeSlot['office_hours_id'];
                    $calendarSlotDto->setOfficeHoursId($officeHoursId);
                    $startDate = ($officeHoursId) ? $timeSlot['slot_start'] : $timeSlot['start_date_time'];
                    $endDate = ($officeHoursId) ? $timeSlot['slot_end'] : $timeSlot['end_date_time'];
                    $startApp = new \DateTime($startDate);
                    $endApp = new \DateTime($endDate);
                    $calendarSlotDto->setSlotStart($startApp);
                    $calendarSlotDto->setSlotEnd($endApp);
                    $calendarSlotDto->setLocation(is_null($timeSlot['location']) ? "" : $timeSlot['location']);
                    $calendarSlotDto->setSlotType($timeSlot['slot_type']);
                    $calendarSlotDto->setReason(is_null($timeSlot['title']) ? "" : $timeSlot['title']);
                    $calendarSlotDto->setReasonId((int)$timeSlot['activity_category_id']);
                    $calendarSlotDto->setAppointmentId((int)$timeSlot['appointments_id']);
                    if ($timeSlot['appointments_id'] > 0) {
                        $calendarSlotDto->setAttendees($this->getAttendees($timeSlot['appointments_id']));
                        $calendarSlotDto->setLocation(is_null($timeSlot['app_loc']) ? "" : $timeSlot['app_loc']);
                        $googleAppointmentId = $timeSlot['appointment_google_appointment_id'];
                    } else {
                        $calendarSlotDto->setAttendees([]);
                        $googleAppointmentId = $timeSlot['office_hour_google_appointment_id'];
                    }
                    $calendarSlotDto->setIsSlotCancelled((bool)$timeSlot['is_cancelled']);
                    if ($calendarSettings['facultyMAFTOPCS'] == "y" && $calendarSettings['google_sync_status']) {
                        $calendarSlotDto->setPcsCalendarId($googleAppointmentId);
                    }
                    $pcsCalendarId[] = $googleAppointmentId;
                } else {
                    // Freestanding
                    $appointmentResponse->setPersonIdProxy((int)$timeSlot['person_id_proxy']);
                    $calendarSlotDto->setOfficeHoursId(0);
                    $startApp = new \DateTime($timeSlot['start_date_time']);
                    $endApp = new \DateTime($timeSlot['end_date_time']);
                    $calendarSlotDto->setSlotStart($startApp);
                    $calendarSlotDto->setSlotEnd($endApp);
                    $calendarSlotDto->setLocation(is_null($timeSlot['location']) ? "" : $timeSlot['location']);
                    $calendarSlotDto->setSlotType($timeSlot['type']);
                    $calendarSlotDto->setReason(is_null($timeSlot['title']) ? "" : $timeSlot['title']);
                    $calendarSlotDto->setReasonId((int)$timeSlot['activity_category_id']);
                    $calendarSlotDto->setAppointmentId((int)$timeSlot['appointments_id']);
                    $calendarSlotDto->setIsSlotCancelled((bool)$timeSlot['is_cancelled']);
                    if ($calendarSettings['facultyMAFTOPCS'] == "y" && $calendarSettings['google_sync_status']) {
                        $calendarSlotDto->setPcsCalendarId($timeSlot['appointment_google_appointment_id']);
                    }
                    $calendarSlotDto->setAttendees($this->getAttendees($timeSlot['appointments_id']));
                    $pcsCalendarId[] = $timeSlot['appointment_google_appointment_id'];
                }
                $calendarSlotDto->setIsConflictedFlag(false);
                $calendar[] = $calendarSlotDto;

            }

        }

        // Fetch all external events if syncing is ON
        if ($calendarSettings['facultyPCSTOMAF'] == "y" && $timePeriod != 'past') {
            $googleSyncStatus = $calendarSettings['google_sync_status'];
            $pcsEvents = $this->calendarFactoryService->getBusyEvents($personId, $organizationId, $pcsCalendarId, $fromDate, $toDate, $googleSyncStatus);
            $calendar = array_merge($pcsEvents, $calendar);
        }

        if (!empty($calendar)) {
            // Checking conflict event, setting isConflictedFlag and sorting the calendar variable.
            $calenderCount = count($calendar);
            for ($indexOne = 0; $indexOne < $calenderCount; $indexOne++) {
                $event1 = $calendar[$indexOne];
                for ($indexTwo = 1; $indexTwo < $calenderCount; $indexTwo++) {
                    $event2 = $calendar[$indexTwo];
                    //skip setting conflict if both index are same
                    if ($indexTwo != $indexOne) {
                        //setting conflicts in all scenario
                        if (($event1->getSlotStart() > $event2->getSlotStart()) && ($event1->getSlotStart() < $event2->getSlotEnd())) {
                            $event1->setIsConflictedFlag(true);
                            $event2->setIsConflictedFlag(true);
                        }

                        if (($event1->getSlotEnd() > $event2->getSlotStart()) && ($event1->getSlotEnd() < $event2->getSlotEnd())) {
                            $event1->setIsConflictedFlag(true);
                            $event2->setIsConflictedFlag(true);
                        }

                        if (($event2->getSlotStart() > $event1->getSlotStart()) && ($event2->getSlotStart() < $event1->getSlotEnd())) {
                            $event1->setIsConflictedFlag(true);
                            $event2->setIsConflictedFlag(true);
                        }

                        if (($event2->getSlotEnd() > $event1->getSlotStart()) && ($event2->getSlotEnd() < $event1->getSlotEnd())) {
                            $event1->setIsConflictedFlag(true);
                            $event2->setIsConflictedFlag(true);
                        }

                        if ($event1->getSlotStart() == $event2->getSlotStart()) {
                            $event1->setIsConflictedFlag(true);
                            $event2->setIsConflictedFlag(true);
                        }
                    }
                }
            }

            // Perform sorting
            usort($calendar, function ($event1, $event2) {
                $event1StartDate = $event1->getSlotStart();
                $event1EndDate = $event1->getSlotEnd();
                $event1TimeDifference = strtotime($event1EndDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT)) - strtotime($event1StartDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT));

                $event2StartDate = $event2->getSlotStart();
                $event2EndDate = $event2->getSlotEnd();
                $event2TimeDifference = strtotime($event2EndDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT)) - strtotime($event2StartDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT));

                $formattedEvent1Date = $event1StartDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
                $formattedEvent2Date = $event2StartDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);

                // If more than one event are having equal time difference and their start time is same then show the    external event first
                if ($event1TimeDifference == $event2TimeDifference && $formattedEvent1Date == $formattedEvent2Date && ($event1->getSlotType() == 'B')) {
                    $showAsFirstEvent = 1;
                } else if (($event1TimeDifference > $event2TimeDifference) && ($event1StartDate == $event2StartDate)) {
                    // More than one event has same start time then longest event should be displayed first.
                    $showAsFirstEvent = 1;
                } else {
                    $showAsFirstEvent = 0;
                }
                $moveThisEventToTop = ($event1StartDate < $event2StartDate || $showAsFirstEvent == 1) ? -1 : 1;
                return $moveThisEventToTop;
            });
        }
        $appointmentResponse->setCalendarTimeSlots($calendar);
        return $appointmentResponse;
    }


    /**
     * Get Participating Attendees And load attendees into array of AttendeeDtos
     *
     * @param int $appointmentId
     * @return array
     */
    private function getAttendees($appointmentId)
    {
        $appointment = $this->appointmentsRepository->find($appointmentId);

        $attendeesDtoArray = array();
        $longitudinalStudentManagement = false;

        if ($appointment) {
            $attendees = $this->appointmentRecipientAndStatusRepository->findBy(['appointments' => $appointment]);
            foreach ($attendees as $attendee) {
                $attendeeDto = new AttendeesDto();

                $studentObject = $attendee->getPersonIdStudent();
                $personId = $studentObject->getId();
                $organizationId = $studentObject->getOrganization()->getId();

                $multiYearStudentObject = $this->userManagementService->getMultiyearOrganizationStudentObject($personId, $longitudinalStudentManagement, $organizationId);
                if ($multiYearStudentObject) {
                    $attendeeDto->setStudentId($personId);
                    $attendeeDto->setStudentFirstName($studentObject->getFirstname());
                    $attendeeDto->setStudentLastName($studentObject->getLastname());
                    $attendeeDto->setIsAttended((bool)$attendee->getHasAttended());

                    if ($longitudinalStudentManagement) {
                        $isStudentActive = ($multiYearStudentObject->getIsActive() == true) ? true : false;
                    } else {
                        $isStudentActive = ($multiYearStudentObject->getStatus() == 1) ? true : false;
                    }
                    $attendeeDto->setStudentStatus($isStudentActive);
                }
                $attendeesDtoArray[] = $attendeeDto;
            }
        }
        return $attendeesDtoArray;
    }

    public function getOrganizationLang($orgId)
    {
        $this->logger->debug(">>>>Get Organization Language" . $orgId);
        return $this->organizationService->getOrganizationDetailsLang($orgId);
    }

    /**
     * Fetch today's upcoming appointments to show on dashboard
     *
     * @param string $filter
     * @param int $organizationId
     * @param int $personId
     * @param string $timezone
     * @throws SynapseValidationException
     * @return array
     */
    public function viewTodayAppointment($filter, $organizationId, $personId, $timezone)
    {
        $this->rbacManager->checkAccessToOrganization($organizationId);

        if (strtolower($filter) != 'today') {
            $this->logger->error("Appointment Service - View Today Appointment - Filter Today Not Found");
            throw new SynapseValidationException('Filter Today Not Found.');
        }

        $operate = $timezone[0];
        $hours = $timezone[1].$timezone[2];
        $minutes = $timezone[3].$timezone[4];
        $startConvertText = "PT".$hours."H".$minutes."M";

        $convertFromDateTime = new \DateTime('now');
        $currentDateTimeUTC = clone($convertFromDateTime);
        $startDateTime = $currentDateTimeUTC->format('Y-m-d H:i:s');
        //getting user time zone date
        if ($operate == "-") {
            $convertFromDateTime->sub(new \DateInterval($startConvertText));
        } else {
            $convertFromDateTime->add(new \DateInterval($startConvertText));
        }

        $currentDateTimeInUserTimeZone = clone($convertFromDateTime);

        $endDateInUserTimeZone = $currentDateTimeInUserTimeZone->setTime(23, 59, 59);
        //Adding difference hours in UTC end date to get user end date time to fetch appointment
        if ($operate == "-") {
            $endDateInUserTimeZone->add(new \DateInterval($startConvertText));
        } else {
            $endDateInUserTimeZone->sub(new \DateInterval($startConvertText));
        }

        $endDateTime = $endDateInUserTimeZone->format('Y-m-d H:i:s');

        $date = new \DateTime('now');
        $currentDate = $date->setTime(0, 0, 0);
        $orgAcademicYear = $this->orgAcademicYearRepository->getCurrentAcademicDetails($currentDate, $organizationId);
        $academicYearStartDate = $orgAcademicYear[0]['startDate'];
        $academicYearEndDate = $orgAcademicYear[0]['endDate'];

        $appointments = $this->appointmentsRepository->viewTodayAppointment(
            $startDateTime,
            $endDateTime,
            $personId,
            $organizationId,
            $academicYearStartDate,
            $academicYearEndDate
        );

        $appointmentsArray = array();
        $appointmentsArray['person_id'] = $personId;
        $appointmentsArray['todays_total_appointments'] = count($appointments);

        $appointmentsResponseArray = [];
        if (count($appointments) > 0) {
            foreach ($appointments as $appointment) {
                $students = $this->getAttendees($appointment['appointment_id']);
                $appointment['attendees'] = $students;
                $appointmentsResponseArray[] = $appointment;
            }
        }
        $appointmentsArray['appointments'] = $appointmentsResponseArray;

        return $appointmentsArray;
    }

    /**
     * This method sends reminder to appointment attendees and students
     *
     * @param int $appointmentId
     */
    public function getReminder($appointmentId)
    {
        $this->logger->debug(">>>>Get Reminder - " . $appointmentId);
        $appointmentList = $this->appointmentsRepository->getAppointmentList($appointmentId);
        if ($appointmentList) {
            $organizationId = $appointmentList[0]->getOrganization()->getId();
            $emailMessage['subject'] = "Appointment Reminder";
            $emailMessage['email_key'] = "Appointment_Reminder_Staff_to_Student";
            // Get skyfactor logo path
            $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
            $logoUrl = '';
            if ($systemUrl) {
                $logoUrl = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
            }
            foreach ($appointmentList as $attendee) {
                $startDateTime = $attendee->getAppointments()->getStartDateTime();
                $endDateTime = $attendee->getAppointments()->getEndDateTime();
                $formattedStartDateTime = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, $startDateTime, 'm/d/Y h:ia');
                $formattedEndDateTime = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, $endDateTime, 'h:ia e');
                $appointmentDateTime = "$formattedStartDateTime to $formattedEndDateTime";

                // Send appointment reminder email to student

                if (count($attendee) > 0) {
                    $tokenValues = array();
                    // Include skyfactor logo in reminder email
                    $tokenValues['Skyfactor_Mapworks_logo'] = $logoUrl;
                    $tokenValues['app_datetime'] = $appointmentDateTime;
                    $tokenValues['app_location'] = $attendee->getAppointments()->getLocation();
                    $tokenValues['staff_name'] = $attendee->getPersonIdFaculty()->getFirstname() . " " . $attendee->getPersonIdFaculty()->getLastname();
                    $tokenValues['staff_email'] = $attendee->getPersonIdFaculty()
                        ->getContacts()[0]
                        ->getPrimaryEmail();
                    $tokenValues['staff_mobile'] = $attendee->getPersonIdFaculty()
                        ->getContacts()[0]
                        ->getPrimaryMobile();
                    $tokenValues['student_name'] = $attendee->getPersonIdStudent()->getFirstname() . " " . $attendee->getPersonIdStudent()->getLastname();

                    $studentAppointmentPageUrl = $this->ebiConfigService->generateCompleteUrl('StudentDashboard_AppointmentPage', $organizationId);
                    if ($studentAppointmentPageUrl) {
                        $tokenValues['student_dashboard'] = $studentAppointmentPageUrl;
                    } else {
                        $tokenValues['student_dashboard'] = "";
                    }

                    $personStudent = $this->personService->findPerson($attendee->getPersonIdStudent());
                    $this->sendToStudents($personStudent, $organizationId, $emailMessage, $tokenValues, true);
                }
            }
        }
        $this->logger->debug(">>>>Get Reminder" . $appointmentId);
    }


    private function isExists($object, $key, $error)
    {
        if (!isset($object)) {
            throw new ValidationException([
                $error
            ], $error, $key);
        }
    }

    /**
     * Checks any conflicts with external calendar
     *
     * @param AppointmentsDto $appointmentsDto
     * @param int $loggedInPersonId
     * @param int $organizationId
     * @throws SynapseValidationException
     * @return boolean
     */
    public function checkExternalCalendarForAppointmentConflict(AppointmentsDto $appointmentsDto, $loggedInPersonId, $organizationId)
    {
        $orgPersonFacultyObject = $this->orgPersonFacultyRepository->findOneBy([
            "person" => $loggedInPersonId,
            "organization" => $organizationId
        ]);

        $isConflictedFlag = false;

        $eventStartDateTime = $appointmentsDto->getSlotStart();
        $eventStartDateTime->setTimezone(new \DateTimeZone('UTC'));
        $eventEndDateTime = $appointmentsDto->getSlotEnd();
        $eventEndDateTime->setTimezone(new \DateTimeZone("UTC"));

        if ($appointmentsDto->getAppointmentId()) {
            $appointmentId = $appointmentsDto->getAppointmentId();
        } else {
            $appointmentId = null;
        }

        $isOverlappingAppointment = $this->appointmentsRepository->isOverlappingAppointments($loggedInPersonId, $organizationId, $eventStartDateTime->format("Y-m-d H:i:s"), $eventEndDateTime->format("Y-m-d H:i:s"), $appointmentId);
        if ($isOverlappingAppointment) {
            $isConflictedFlag = true;
        } else if (isset($orgPersonFacultyObject) && $orgPersonFacultyObject->getPcsToMafIsActive() === "y") {
            $externalCalendarEvents = $this->cronofyWrapperService->getFreeBusyEventsForConflictValidation($eventStartDateTime->format("Y-m-d"), $eventEndDateTime->format("Y-m-d"), $loggedInPersonId, $organizationId);

            if (count($externalCalendarEvents->firstPage["free_busy"]) > 0) {
                foreach ($externalCalendarEvents->firstPage["free_busy"] as $externalCalendarEvent) {
                    $localTimeZone = new \DateTimeZone($externalCalendarEvent['start']['tzid']);
                    $startTime = $externalCalendarEvent['start']['time'];
                    $startDateTime = (strpos($startTime, 'T') !== false) ? $startTime : $startTime . ' 00:00:00';
                    $externalEventStartDateTime = new \DateTime($startDateTime, $localTimeZone);
                    $externalEventStartDateTime->setTimezone(new \DateTimeZone('UTC'));

                    $endTime = $externalCalendarEvent['end']['time'];
                    $endDateTime = (strpos($endTime, 'T') !== false) ? $endTime : $endTime . ' 23:59:59';
                    $externalEventEndDateTime = new \DateTime($endDateTime, $localTimeZone);
                    $externalEventEndDateTime->setTimezone(new \DateTimeZone('UTC'));

                    if ($externalEventStartDateTime > $eventStartDateTime){
                        $slotStartDate = $externalEventStartDateTime;
                        $slotEndDate = $eventEndDateTime;
                    } else {
                        $slotStartDate = $eventStartDateTime;
                        $slotEndDate = $externalEventEndDateTime;
                    }

                    if ($slotEndDate > $slotStartDate){
                        $isConflictedFlag = true;
                        break;
                    }
                }
            }
        }
        return $isConflictedFlag;
    }

    /**
     * Cancelling Student Appointments
     *
     * @param integer $studentId
     * @param string $currentDate
     * @return array
     */
    public function cancelStudentAppointments($studentId , $currentDate){
        $studentsUpcomingAppointments = $this->appointmentRecipientAndStatusRepository->getStudentsUpcomingAppointments($studentId, $currentDate);

        $listOfCancelledAppointmentIds = [];
        foreach ($studentsUpcomingAppointments as $studentsUpcomingAppointment) {
            $appointmentId = $studentsUpcomingAppointment['appointment_id'];

            $appointmentId = $this->studentAppointmentService->cancelStudentAppointment($studentId, $appointmentId, true, true);
            $listOfCancelledAppointmentIds[] = $appointmentId;

        }

        return $listOfCancelledAppointmentIds;
    }
}
