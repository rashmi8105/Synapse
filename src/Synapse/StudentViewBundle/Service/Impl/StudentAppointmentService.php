<?php
namespace Synapse\StudentViewBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CalendarBundle\Exception\CronofyException;
use Synapse\CalendarBundle\Service\Impl\CalendarFactoryService;
use Synapse\CalendarBundle\Service\Impl\CalendarIntegrationService;
use Synapse\CalendarBundle\Service\Impl\GoogleFormatService;
use Synapse\CampusResourceBundle\Repository\OrgCampusResourceRepository;
use Synapse\CoreBundle\Entity\AppointmentRecepientAndStatus;
use Synapse\CoreBundle\Entity\Appointments;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\job\SendAppointmentReminderJob;
use Synapse\CoreBundle\Repository\ActivityCategoryRepository;
use Synapse\CoreBundle\Repository\AppointmentRecepientAndStatusRepository;
use Synapse\CoreBundle\Repository\AppointmentsRepository;
use Synapse\CoreBundle\Repository\CalendarSharingRepository;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OfficeHoursRepository;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\OrgFeaturesRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\ActivityLogService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\MapworksActionService;
use Synapse\CoreBundle\Service\Impl\NotificationChannelService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Impl\UserManagementService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\AcademicUpdateConstant;
use Synapse\CoreBundle\Util\Constants\AppointmentsConstant;
use Synapse\CoreBundle\Util\Constants\StudentConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\MultiCampusBundle\EntityDto\CampusDto;
use Synapse\MultiCampusBundle\EntityDto\ListCampusDto;
use Synapse\PersonBundle\Repository\ContactInfoRepository;
use Synapse\RestBundle\Entity\ActivityLogDto;
use Synapse\RestBundle\Entity\AppointmentsDto;
use Synapse\RestBundle\Entity\AppointmentsReponseDto;
use Synapse\RestBundle\Entity\AttendeesDto;
use Synapse\RestBundle\Entity\CalendarTimeSlotsReponseDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\StudentViewBundle\EntityDto\CampusConnectionDto;
use Synapse\StudentViewBundle\EntityDto\ListCampusConnectionDto;
use Synapse\StudentViewBundle\Service\StudentAppointmentServiceInterface;
use Synapse\StudentViewBundle\Util\Constants\StudentViewErrorConstants;

/**
 * @DI\Service("studentappointment_service")
 */
class StudentAppointmentService extends StudentAppointmentServiceHelper implements StudentAppointmentServiceInterface
{

    const SERVICE_KEY = 'studentappointment_service';

    // Scaffolding

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
    private $alertNotificationsService;

    /**
     * @var CalendarIntegrationService
     */
    private $calendarIntegrationService;

    /**
     * @var CalendarFactoryService
     */
    private $calendarFactoryService;

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
     * @var GoogleFormatService
     */
    private $googleFormatService;

    /**
     * @var MapworksActionService
     */
    private $mapworksActionService;

    /**
     * @var NotificationChannelService
     */
    private $notificationChannelService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var PersonService
     */
    private $personService;

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
     * @var AppointmentsRepository
     */
    private $appointmentsRepository;

    /**
     * @var AppointmentRecepientAndStatusRepository
     */
    private $appointmentRecipientAndStatusRepository;

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
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var MetadataListValuesRepository
     */
    private $metadataListValuesRepository;

    /**
     * @var OfficeHoursRepository
     */
    private $officeHoursRepository;

    /**
     * @var OrganizationLangRepository
     */
    private $organizationLangRepository;

    /**
     * @var OrgAcademicTermRepository
     */
    private $orgAcademicTermRepository;

    /**
     * @var OrgCampusResourceRepository
     */
    private $orgCampusResourceRepository;

    /**
     * @var OrgFeaturesRepository
     */
    private $orgFeaturesRepository;

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
     * StudentAppointmentService constructor.
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
        //Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->resque = $this->container->get('bcc_resque.resque');

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->activityLogService = $this->container->get(ActivityLogService::SERVICE_KEY);
        $this->alertNotificationsService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->calendarIntegrationService = $this->container->get(CalendarIntegrationService::SERVICE_KEY);
        $this->calendarFactoryService = $this->container->get(CalendarFactoryService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->googleFormatService = $this->container->get(GoogleFormatService::SERVICE_KEY);
        $this->mapworksActionService = $this->container->get(MapworksActionService::SERVICE_KEY);
        $this->notificationChannelService = $this->container->get(NotificationChannelService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->userManagementService = $this->container->get(UserManagementService::SERVICE_KEY);

        // Repositories
        $this->activityCategoryRepository = $this->repositoryResolver->getRepository(ActivityCategoryRepository::REPOSITORY_KEY);
        $this->appointmentRecipientAndStatusRepository = $this->repositoryResolver->getRepository(AppointmentRecepientAndStatusRepository::REPOSITORY_KEY);
        $this->appointmentsRepository = $this->repositoryResolver->getRepository(AppointmentsRepository::REPOSITORY_KEY);
        $this->calendarSharingRepository = $this->repositoryResolver->getRepository(CalendarSharingRepository::REPOSITORY_KEY);
        $this->contactInfoRepository = $this->repositoryResolver->getRepository(ContactInfoRepository::REPOSITORY_KEY);
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->metadataListValuesRepository = $this->repositoryResolver->getRepository(MetadataListValuesRepository::REPOSITORY_KEY);
        $this->officeHoursRepository = $this->repositoryResolver->getRepository(OfficeHoursRepository::REPOSITORY_KEY);
        $this->orgAcademicTermRepository = $this->repositoryResolver->getRepository(OrgAcademicTermRepository::REPOSITORY_KEY);
        $this->orgFeaturesRepository = $this->repositoryResolver->getRepository(OrgFeaturesRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->orgCampusResourceRepository = $this->repositoryResolver->getRepository(OrgCampusResourceRepository::REPOSITORY_KEY);
        $this->organizationLangRepository = $this->repositoryResolver->getRepository(OrganizationlangRepository::REPOSITORY_KEY);
    }

    /**
     * Gets upcoming appointments for a given student ID
     *
     * @param int $studentId
     * @param string $timezone
     * @return AppointmentsReponseDto
     */
    public function getStudentsUpcomingAppointments($studentId, $timezone)
    {
        $this->logger->debug(StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_003 . $studentId);
        $isValidStudent = $this->personRepository->findOneBy(array(
            'id' => $studentId
        ));
        $this->isObjectExist($isValidStudent, StudentConstant::INVALID_STUDENT, StudentConstant::INVALID_STUDENT_KEY, StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_006, $this->logger);
        $timezone = $this->metadataListValuesRepository->findByListName($timezone);
        $orgTimeZone = "";
        if ($timezone) {
            $orgTimeZone = $timezone[0]->getListValue();
        }
        
        $currentDate = $this->getDateByTimezone($orgTimeZone, AppointmentsConstant::DATE_FORMAT);
        $this->logger->debug(StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_004 . $studentId);
        $upcomingAppointments = $this->appointmentRecipientAndStatusRepository->getStudentsUpcomingAppointments($studentId, $currentDate);
        $appointmentResponse = new AppointmentsReponseDto();
        if (isset($upcomingAppointments) && count($upcomingAppointments) > 0) {
            $calendarTimeSlotsArray = [];
            foreach ($upcomingAppointments as $appointment) {
                $facultyId = $appointment['personId'];
                $personFaculty = $this->personService->findPerson($facultyId);
                $timeZone = $personFaculty->getOrganization()->getTimeZone();

                $calendarTimeSlotsResponseDto = new CalendarTimeSlotsReponseDto();
                $calendarTimeSlotsResponseDto->setAppointmentId($appointment['appointment_id']);
                $calendarTimeSlotsResponseDto->setOrganizationId($appointment[AppointmentsConstant::KEY_ORGANIZATION_ID]);
                $calendarTimeSlotsResponseDto->setCampusName($appointment['organizationName']);
                $startApp = $appointment['startDateTime'];
                $endApp = $appointment['endDateTime'];
                $calendarTimeSlotsResponseDto->setSlotStart($startApp);
                $calendarTimeSlotsResponseDto->setSlotEnd($endApp);
                $calendarTimeSlotsResponseDto->setAppointmentTimeZone($timeZone);
                $calendarTimeSlotsResponseDto->setPersonId($facultyId);
                $calendarTimeSlotsResponseDto->setPersonFirstName($appointment['firstname']);
                $calendarTimeSlotsResponseDto->setPersonLastName($this->isNull($appointment['lastname'], ''));
                $calendarTimeSlotsResponseDto->setPersonTitle($appointment['title']);
                $calendarTimeSlotsResponseDto->setLocation($this->isNull($appointment['location'], ''));
                $calendarTimeSlotsResponseDto->setReasonId($appointment['reasonId']);
                $calendarTimeSlotsResponseDto->setReason($this->isNull($appointment['reason'], ''));
                $calendarTimeSlotsResponseDto->setDescription($this->isNull($appointment['description'], ''));
                $calendarTimeSlotsResponseDto->setOfficeHoursId($this->isNull($appointment['officeHoursId'], ''));
                $calendarTimeSlotsArray[] = $calendarTimeSlotsResponseDto;
            }
            $appointmentResponse->setCalendarTimeSlots($calendarTimeSlotsArray);
        }
        $this->logger->info(StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_005);
        return $appointmentResponse;
    }

    /**
     * Get the list of students active campus
     *
     * @param integer $studentId
     * @return object ListCampusDto
     */
    public function getStudentCampuses($studentId)
    {
        $this->logger->debug(StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_007 . $studentId);
        $studentObject = $this->personRepository->findOneBy(array(
            'id' => $studentId
        ));
        $this->isObjectExist($studentObject, StudentConstant::INVALID_STUDENT, StudentConstant::INVALID_STUDENT_KEY, StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_009, $this->logger);
        $this->logger->debug(StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_010 . $studentId);

        $person = $this->personService->findPerson($studentId);
        $organization = $this->organizationLangRepository->findOneByOrganization($person->getOrganization()->getId());
        $features = $this->orgFeaturesRepository->getListFeaturesByStudent($studentId);
        $orgFeature = $this->mapOrganizationFeatureArray($features);

        $campusDetails = $this->orgPersonStudentRepository->getCampusDetails($studentId);
        $listCampusDto = new ListCampusDto();
        if (isset($campusDetails) && count($campusDetails) > 0) {
            $campusesArray = [];
            foreach ($campusDetails as $campusDetail) {
                $campusDto = new CampusDto();
                $campusDto->setOrganizationId($campusDetail['organization_id']);
                $campusDto->setCampusId($person->getOrganization()->getCampusId());
                $campusDto->setCampusName($organization->getOrganizationName());
                $results = [];
                if (array_key_exists($campusDetail['organization_id'], $orgFeature)) {
                    $results = $orgFeature[$campusDetail['organization_id']];
                }
                $features = $this->bindOrgFeatures($results);
                $campusDto->setOrgFeatures($features);
                $campusesArray[] = $campusDto;
            }
            $listCampusDto->setCampus($campusesArray);
        }
        $this->logger->info(StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_008);
        return $listCampusDto;
    }

    /**
     * Lists faculty that a student can make an appointment with. It's used during creation of an appointment by a student.
     *
     * Checks to ensure the student is active and also a member of the current academic year. If not, it throws an exception
     *
     * @param integer $studentId
     * @param integer $orgId
     * @param string $timezone
     * @return ListCampusConnectionDto data object
     * @throws AccessDeniedException
     */
    public function getStudentCampusConnections($studentId, $orgId, $timezone)
    {
        $this->logger->debug(StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_011 . StudentViewErrorConstants::VAR_STUDENT_ID . " - " . $studentId . " - " . StudentViewErrorConstants::VAR_ORG_ID . " - " . $orgId);                
        $isValidStudent = $this->personRepository->findOneBy(array(
            'id' => $studentId
        ));
        
        $organization = $this->organizationService->find($orgId);
        
        $studentIsActive = $this->userManagementService->isStudentActive($studentId, $orgId);
        if (!$studentIsActive) {
            throw new AccessDeniedException('Inactive/Non-Participant Student Cannot Create Appointments');
        }

        $this->isObjectExist($organization, AcademicUpdateConstant::ORG_NOT_FOUND, AcademicUpdateConstant::ORG_NOT_FOUND_KEY, StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_011, $this->logger);
        $this->isObjectExist($isValidStudent, StudentConstant::INVALID_STUDENT, StudentConstant::INVALID_STUDENT_KEY, StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_011, $this->logger);
        $timezone = $this->metadataListValuesRepository->findByListName($timezone);
        $timeZone = "";
        if ($timezone) {
            $timeZone = $timezone[0]->getListValue();
        }
        $currentDate = $this->getDateByTimezone($timeZone, AppointmentsConstant::DATE_FORMAT);
        $this->logger->debug(StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_012 . $studentId . " - " . StudentViewErrorConstants::VAR_ORG_ID . " - " . $orgId);
        $campusConnections = $this->orgPersonFacultyRepository->getStudentCampusConnection($studentId, $orgId, $currentDate, true);
        $listCampusConnectionDto = new ListCampusConnectionDto();
        if (isset($campusConnections) && count($campusConnections) > 0) {
            $campusConnectionArray = [];
            foreach ($campusConnections as $campusCon) {
                $campusConnectionDto = new CampusConnectionDto();
                $campusConnectionDto->setPersonId($campusCon['person_id']);
                $campusConnectionDto->setPersonFirstname($campusCon['fname']);
                $campusConnectionDto->setPersonLastname($this->isNull($campusCon['lname'], ''));
                $campusConnectionDto->setPersonTitle($this->isNull($campusCon['title'], ''));
                $campusConnectionArray[] = $campusConnectionDto;
            }
            $listCampusConnectionDto->setCampusConnection($campusConnectionArray);
        }
        $this->logger->info(StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_013);
        return $listCampusConnectionDto;
    }

    /**
     * Get all available office hours for a faculty.
     *
     * @param int $organizationId
     * @param string $timezone
     * @param int $facultyId
     * @param string $filter
     * @return AppointmentsReponseDto
     */
    public function getFacultyOfficeHours($organizationId, $timezone, $facultyId, $filter = 'week')
    {
        $this->logger->debug("Student View - List of office hour slots for given faculty and organization - facultyId - " . $facultyId . " - orgId - " . $organizationId . "  filter - " . $filter);

        $organization = $this->organizationService->find($organizationId);
        $this->isObjectExist($organization, "Organization Not Found.", "organization_not_found", "Student View - List of office hour slots for given faculty and organization - ", $this->logger);
        $personFaculty = $this->personService->findPerson($facultyId);
        $this->isObjectExist($personFaculty, "Person Not Found.", "Person_not_found", "Student View - List of office hour slots for given faculty and organization - ", $this->logger);

        $currentDateTimeObject = new \DateTime();
        $currentDateTimeString = $currentDateTimeObject->format('Y-m-d H:i:s');

        if ($filter == "term") {
            $academicTerm = $this->orgAcademicTermRepository->getAcademicTermDates($currentDateTimeObject, $organizationId);
            $dateRange = $this->getCurrentAcademicTerm($currentDateTimeObject, $academicTerm, $timezone);

            $startDate = $this->dateUtilityService->convertToUtcDatetime($organizationId, $dateRange['fromDate']->format('Y-m-d'));
            $endDate = $this->dateUtilityService->convertToUtcDatetime($organizationId, $dateRange['toDate'], true);
        } elseif ($filter == "today") {
            $currentDateStringForOrganization = $this->dateUtilityService->getCurrentFormattedDateTimeForOrganization($organizationId, 'Y-m-d');
            $filterEndDate = $this->dateUtilityService->convertToUtcDatetime($organizationId, $currentDateStringForOrganization, true);

            $startDate = $currentDateTimeString;
            $endDate = $filterEndDate;
        } else {
            $dateRange = $this->getDateRange($filter, $currentDateTimeString);

            $startDate = $dateRange['fromDate'];
            $endDate = $this->dateUtilityService->convertToUtcDatetime($organizationId, $dateRange['toDate'], true);
        }

        $officeHours = [];
        $pcsEvents = [];
        $pcsCalendarIds = [];
        if ($startDate != "" && $endDate != "") {
            $officeHours = $this->officeHoursRepository->getOfficeHoursForFaculty($facultyId, $organizationId, $startDate, $endDate);
            if (!empty($officeHours)) {
                $pcsCalendarIds = array_column($officeHours, 'google_appointment_id');
            }
            $calendarSettings = $this->calendarIntegrationService->facultyCalendarSettings($organizationId, $facultyId);
            if ($calendarSettings['facultyPCSTOMAF'] == "y") {
                $googleSyncStatus = $calendarSettings['google_sync_status'];
                $pcsEvents = $this->calendarFactoryService->getBusyEvents($facultyId, $organizationId, $pcsCalendarIds, $startDate, $endDate, $googleSyncStatus);

            }
        }

        $appointmentsResponseDto = new AppointmentsReponseDto();
        if (isset($officeHours) && count($officeHours) > 0) {
            $appointmentsResponseDto->setPersonId($facultyId);
            $appointmentsResponseDto->setFirstName($this->isNull($personFaculty->getFirstname(), ''));
            $appointmentsResponseDto->setLastName($this->isNull($personFaculty->getLastname(), ''));
            $appointmentsResponseDto->setPersonTitle($this->isNull($personFaculty->getTitle(), ''));

            $availableOfficeHours = [];
            foreach ($officeHours as $officeHour) {
                $slotStart = $this->dateUtilityService->adjustDateTimeToOrganizationTimezone($organizationId, new \DateTime($officeHour['slot_start']));
                $slotEnd = $this->dateUtilityService->adjustDateTimeToOrganizationTimezone($organizationId, new \DateTime($officeHour['slot_end']));
                $hasConflict = $this->isOfficeHourConflictWithPCSEvents($pcsEvents, $slotStart, $slotEnd);
                if ($officeHour['overlaps_appointment'] == '0' && $hasConflict == false) {
                    $calendarTimeSlotsResponseDto = new CalendarTimeSlotsReponseDto();
                    $calendarTimeSlotsResponseDto->setOrganizationId($officeHour['organization_id']);
                    $calendarTimeSlotsResponseDto->setSlotStart($slotStart);
                    $calendarTimeSlotsResponseDto->setSlotEnd($slotEnd);
                    $calendarTimeSlotsResponseDto->setLocation($this->isNull($officeHour['location'], ''));
                    $calendarTimeSlotsResponseDto->setOfficeHoursId($officeHour['office_hours_id']);
                    $calendarTimeSlotsResponseDto->setSlotType($this->isNull($officeHour['slot_type'], ''));

                    $availableOfficeHours[] = $calendarTimeSlotsResponseDto;
                }
            }
            $appointmentsResponseDto->setCalendarTimeSlots($availableOfficeHours);
        }
        return $appointmentsResponseDto;
    }

    /**
     * Method used to create student appointments
     *
     * @param int $studentId
     * @param AppointmentsDto $appointmentsDto
     * @param string $timezone
     * @return AppointmentsDto
     */
    public function createStudentAppointment($studentId, AppointmentsDto $appointmentsDto, $timezone)
    {
        $this->logger->debug(StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_017 . StudentViewErrorConstants::VAR_STUDENT_ID . " - " . $studentId);
        $appointmentsDto->setSlotStart(Helper::getUtcDate($appointmentsDto->getSlotStart()));
        $appointmentsDto->setSlotEnd(Helper::getUtcDate($appointmentsDto->getSlotEnd()));
        $this->dateValidation($appointmentsDto->getSlotStart(), $appointmentsDto->getSlotEnd());
        $personFacultyId = $appointmentsDto->getPersonId();
        $officeHoursId = $appointmentsDto->getOfficeHoursId();
        $orgId = $appointmentsDto->getOrganizationId();

        $organization = $this->organizationService->find($orgId);
        $this->isObjectExist($organization, AcademicUpdateConstant::ORG_NOT_FOUND, AcademicUpdateConstant::ORG_NOT_FOUND_KEY, StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_017, $this->logger);
        $personFaculty = $this->personService->findPerson($personFacultyId);
        $personStudent = $this->personService->findPerson($studentId);
        $this->isObjectExist($personFaculty, AppointmentsConstant::PERSON_NOT_FOUND, AppointmentsConstant::PERSON_NOT_FOUND_KEY, StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_017, $this->logger);
        $this->isObjectExist($personStudent, AppointmentsConstant::STUDENT_NOT_FOUND, AppointmentsConstant::STUDENT_NOT_FOUND_KEY, StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_017, $this->logger);

        //check selected time slot is available or (booked or removed)
        $officeHours = $this->officeHoursRepository->checkSlotAvailable($officeHoursId, $personFacultyId, $orgId);
        $offHour = $this->officeHoursRepository->find($officeHoursId);
        $this->isObjectExist($officeHours, AppointmentsConstant::APPOINTMENT_SLOT_NOT_FOUND, AppointmentsConstant::APPOINTMENT_SLOT_NOT_FOUND_KEY, StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_017, $this->logger);

        $organizationTimezone = $personFaculty->getOrganization()->getTimeZone();
        $timezones = $this->metadataListValuesRepository->findByListName($organizationTimezone);
        if ($timezones) {
            $timezone = $timezones[0]->getListValue();
        } else {
            $timezone = "";
        }

        $appointments = new Appointments();
        $appointments->setOrganization($organization);
        $appointments->setPerson($personStudent);
        $activityCategory = $this->activityCategoryRepository->find($appointmentsDto->getDetailId());
        $appointments->setActivityCategory($activityCategory);
        $appointments->setTitle($appointmentsDto->getDetail());
        $appointments->setLocation($appointmentsDto->getLocation());
        $appointments->setDescription($appointmentsDto->getDescription());
        $appointments->setType($appointmentsDto->getType());
        $start = $appointmentsDto->getSlotStart();
        $end = $appointmentsDto->getSlotEnd();

        $startDatetimeString = $start->format('Y-m-d H:i:s');
        $endDatetimeString = $end->format('Y-m-d H:i:s');

        $overlappingAppointmentsExistForFaculty = $this->appointmentRecipientAndStatusRepository->doAppointmentsExistWithinTimeframe($orgId, $personFacultyId, $startDatetimeString, $endDatetimeString);

        if ($overlappingAppointmentsExistForFaculty) {
            $validationErrorMessage = "The faculty you are trying to book an appointment with already has appointments during that timeframe. Please try a different timeframe.";
            throw new ValidationException([$validationErrorMessage], $validationErrorMessage);
        }

        $appointments->setStartDateTime($start);
        $appointments->setEndDateTime($end);
        $appointments->setSource('S');
        $this->logger->info(StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_018);

        //Student sharing option by default its private.
        $appointments->setAccessPrivate(1);
        $appointments->setAccessPublic(0);
        $appointments->setAccessTeam(0);
        $appointment = $this->appointmentsRepository->createAppointment($appointments);
        $offHour->setAppointments($appointment);
        $this->updateAppointmentRAS($personFaculty, $personStudent, $organization, $appointment);

        // Call prepare email notification to staff
        $appointmentStartDate = clone $start;
        $appointmentEndDate = clone $end;
        $appointmentStartDate->setTimezone(new \DateTimeZone($timezone));
        $appointmentEndDate->setTimezone(new \DateTimeZone($timezone));
        $emailArr[AppointmentsConstant::APP_EMAIL_TYPE] = 'book';
        $emailArr[AppointmentsConstant::APP_DATE_TIME] = $appointmentStartDate->format("m/d/Y h:ia") . " to " . $appointmentEndDate->format("h:ia T");
        $this->logger->info(StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_020);

        $dashboardUrl = $this->ebiConfigService->generateCompleteUrl("Gateway_Staff_Landing_Page", $orgId);
        $this->prepareAppointmentEmail($emailArr, $orgId, $dashboardUrl, $personFaculty, $personStudent);

        $this->appointmentsRepository->flush();
        $appointmentsDto->setAppointmentId($appointment->getId());
        $this->logger->info(StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_017 . StudentViewErrorConstants::VAR_APPID . " - " . $appointment->getId());
        $lastActivityDate = new \DateTime('now');
        $lastActivityDate->setTimezone(new \DateTimeZone($timezone));

        $lastActivity = $lastActivityDate->format('m/d/y') . "- Appointment";
        $personStudent->setLastActivity($lastActivity);

        // Creating Alert Notification for Appointment_Created
        $event = "Student_Appointment_Created";
        $this->logger->info(StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_019);
        $this->alertNotificationsService->createNotification($event, $activityCategory->getShortName(), $personStudent, null, $appointment);

        $event = "Appointment_Created";
        $this->logger->info(StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_019);
        $this->alertNotificationsService->createNotification($event, $activityCategory->getShortName(), $personFaculty, null, $appointment);
        // Send the notification to faculty
        $this->notificationChannelService->sendNotificationToAllRegisteredChannels($personFaculty, "faculty_event_change");
        // Send event changed notification to delegated person.
        $delegatedPersons = $this->calendarSharingRepository->getSelectedProxyUsers($personFacultyId);
        foreach ($delegatedPersons as $delegatedPerson) {
            $delegatedPersonId = $delegatedPerson['delegated_to_person_id'];
            $delegatedPerson = $this->personRepository->find($delegatedPersonId, new SynapseValidationException('Person not found.'));
            $this->notificationChannelService->sendNotificationToAllRegisteredChannels($delegatedPerson, "delegates_event_change");
        }

        // Resque job start here
        $todayDateTime = new \DateTime('now');
        $todayDateTime->setTimezone(new \DateTimeZone('UTC'));
        $job = new SendAppointmentReminderJob();
        $job->args = array(
            'appointment' => $appointment->getId()
        );
        // enqueue your job to run 1 day before appointment startDate
        $reminderDate = clone $appointment->getStartDateTime();
        $reminderDate->sub(new \DateInterval('PT24H'));
        $this->resque->enqueueAt($reminderDate, $job);
        $this->logger->info(StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_021);
        //Resque job end here
        $this->markActivityLog($studentId, $appointment, $activityCategory, $personFacultyId);
        $this->logger->info(StudentViewErrorConstants::ERR_STUDENT_VIEW_AGENDA_022 . " - " . $appointment->getId());
        // Sync to external calendar.
        $this->calendarIntegrationService->syncOneOffEvent($orgId, $personFacultyId, $appointments->getId(), 'appointment', 'create');
        return $appointmentsDto;
    }

    /**
     * Cancel student booked appointment
     *
     * @param int $studentId
     * @param int $appointmentId
     * @param bool $mailToStudent
     * @param bool $markingStudentAsNonParticipating
     * @return integer $appointmentId
     */
    public function cancelStudentAppointment($studentId, $appointmentId, $mailToStudent = false, $markingStudentAsNonParticipating = false)
    {
        $appointmentEntity = $this->appointmentsRepository->find($appointmentId);
        $this->isObjectExist($appointmentEntity, 'Appointment Not Found.', 'appointment_not_found', 'Student View - Cancel an appointment by a student', $this->logger);

        $personStudent = $this->personService->findPerson($studentId);
        $this->isObjectExist($personStudent, 'Student Not Found.', 'Student_Not_Found', 'Student View - Cancel an appointment by a student', $this->logger);

        $organizationId = $appointmentEntity->getOrganization()->getId();
        $currentDateTime = $this->dateUtilityService->getTimezoneAdjustedCurrentDateTimeForOrganization($organizationId);

        $fromDate = $appointmentEntity->getStartDateTime();
        $fromDate = $this->dateUtilityService->adjustDateTimeToOrganizationTimezone($organizationId, $fromDate);

        $toDate = $appointmentEntity->getEndDateTime();
        $toDate = $this->dateUtilityService->adjustDateTimeToOrganizationTimezone($organizationId, $toDate);

        $this->dateValidation($currentDateTime, $fromDate, 'pastAppointment');

        // Fetch student list for email before soft delete <<1>>
        $recipientPerson = $this->appointmentRecipientAndStatusRepository->findOneBy([
            'appointments' => $appointmentId,
            'personIdStudent' => $studentId
        ]);
        $this->isObjectExist($recipientPerson, 'Student not found in the appointment', 'Appointment_Student_Not_Found', 'Student View - Cancel an appointment by a student', $this->logger);
        $personFaculty = $this->personService->findPerson($recipientPerson->getPersonIdFaculty());
        $this->appointmentRecipientAndStatusRepository->remove($recipientPerson);
        $this->appointmentRecipientAndStatusRepository->flush();

        /**
         * check if only one student appointment remaining
         * If yes then make it as a open slot means delete this appointment from appointment, ARS and activity log and
         * update office hour for to remove this appointment
         */

        $recipientPersonCount = $this->appointmentRecipientAndStatusRepository->findOneBy([
            'appointments' => $appointmentId
        ]);
        $facultyId = $personFaculty->getId();
        if (!$recipientPersonCount) {
            $officeHoursData = $this->officeHoursRepository->findOneBy([
                'appointments' => $appointmentId
            ]);
            $externalCalendarEventId = $appointmentEntity->getGoogleAppointmentId();
            $this->activityLogService->deleteActivityLogByType($appointmentId, 'A');
            $this->appointmentsRepository->remove($appointmentEntity);
            $this->appointmentsRepository->flush();
            $this->removeFromOfficeHours($appointmentId);
            $officeHourId = '';
            if ($officeHoursData) {
                $officeHourId = $officeHoursData->getId();
            }
            $this->calendarIntegrationService->syncOneOffEvent($organizationId, $facultyId, $appointmentEntity->getId(), 'appointment', 'delete', $externalCalendarEventId, $officeHourId);
        } else {
            $this->calendarIntegrationService->syncOneOffEvent($organizationId, $facultyId, $appointmentId, 'appointment', 'update');
        }
        //Creating Alert Notification to co-ordinator when appointment Canceled
        $activityCategory = $appointmentEntity->getActivityCategory();
        $person = $appointmentEntity->getPerson();
        $event = "Appointment_Cancelled";
        $this->alertNotificationsService->createNotification($event, $activityCategory->getShortName(), $person, null, $appointmentEntity);

        // Send the notification to faculty
        $this->notificationChannelService->sendNotificationToAllRegisteredChannels($personFaculty, "faculty_event_change");

        // Send event changed notification to delegated person.
        $delegatedPersons = $this->calendarSharingRepository->getSelectedProxyUsers($facultyId);
        foreach ($delegatedPersons as $delegatedPerson) {
            $delegatedPersonId = $delegatedPerson['delegated_to_person_id'];
            $delegatedPerson = $this->personRepository->find($delegatedPersonId, new SynapseValidationException('Person not found.'));
            $this->notificationChannelService->sendNotificationToAllRegisteredChannels($delegatedPerson, "delegates_event_change");
        }

        //Call prepare email notification to staff
        $dashboardUrl = $this->ebiConfigService->generateCompleteUrl("Gateway_Staff_Landing_Page", $organizationId);
        $emailArray['emailType'] = 'cancel';
        $emailArray['appDateTime'] = $fromDate->format("m/d/Y h:ia") . " to " . $toDate->format("h:ia T");
        $this->prepareAppointmentEmail($emailArray, $organizationId, $dashboardUrl, $personFaculty, $personStudent, $mailToStudent, $markingStudentAsNonParticipating);
        return $appointmentId;
    }
    
    private function removeFromOfficeHours($appointmentId)
    {      
        $officeHours = $this->officeHoursRepository->findBy([
            AppointmentsConstant::FIELD_APPOINTMENT => $appointmentId
            ]);
        if (isset($officeHours)) {
            foreach ($officeHours as $officeHour) {
                $officeHour->setAppointments(null);
            }
        }
    }
    
    private function updateAppointmentRAS($person, $personStudent, $organization, $appointment)
    {
        $appointmentRAStatus = new AppointmentRecepientAndStatus();
        $appointmentRAStatus->setOrganization($organization);
        $appointmentRAStatus->setAppointments($appointment);
        $appointmentRAStatus->setPersonIdFaculty($person);
        $appointmentRAStatus->setPersonIdStudent($personStudent);
        $this->appointmentRecipientAndStatusRepository->createAppointmentsRAStatus($appointmentRAStatus);
    }

    private function markActivityLog($studentId, $appointments, $activityCategory, $facultyId)
    {        
        $activityLogDto = new ActivityLogDto();
        $appointmentActivityDate = $appointments->getStartDateTime();
        $activityLogDto->setActivityDate($appointmentActivityDate);
        $activityLogDto->setActivityType("A");
        $appointmentId = $appointments->getId();
        $activityLogDto->setAppointments($appointmentId);
        $orgId = $appointments->getOrganization()->getId();
        $activityLogDto->setOrganization($orgId);
        $activityLogDto->setPersonIdFaculty($facultyId);
        $activityLogDto->setPersonIdStudent($studentId);
        $reasonText = $activityCategory->getShortName();
        $activityLogDto->setReason($reasonText);
        $this->activityLogService->createActivityLog($activityLogDto);
    }

    /**
     * Send appointment email to student and faculty
     *
     * @param array $emailArray
     * @param int $organizationId
     * @param string $dashboardUrl
     * @param Person $personFaculty
     * @param Person $personStudent
     * @param bool $mailToStudent
     * @param bool $markingStudentAsNonParticipating
     * @return null
     */
    private function prepareAppointmentEmail($emailArray, $organizationId, $dashboardUrl, $personFaculty, $personStudent, $mailToStudent = false, $markingStudentAsNonParticipating = false)
    {
        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
        $tokenValues['Skyfactor_Mapworks_logo'] = "";
        if ($systemUrl) {
            $tokenValues['Skyfactor_Mapworks_logo'] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
        }

        $tokenValues["staff_dashboard"] = "";
        if ($dashboardUrl) {
            $tokenValues["staff_dashboard"] = $dashboardUrl;
        }

        $organizationLang = $this->organizationService->getOrganizationDetailsLang($organizationId);
        $languageId = $organizationLang->getLang()->getId();
        if ($emailArray['emailType'] == "book") {
            $emailTemplateKey = 'Appointment_Book_Student_to_Staff';
        } else {
            if($markingStudentAsNonParticipating) {
                $emailTemplateKey = 'Archived_Cancel_Appointment_Staff';
            }
            else {
                $emailTemplateKey = 'Appointment_Cancel_Student_to_Staff';
            }
        }
        $emailTemplate = $this->emailTemplateLangRepository->getEmailTemplateByKey($emailTemplateKey, $languageId);
        if ($emailTemplate) {
            $emailDetails['orgId'] = $organizationId;
            $tokenValues['staff_name'] = $personFaculty->getFirstname() . " " . $personFaculty->getLastname();
            $tokenValues['student_name'] = $personStudent->getFirstname() . " " . $personStudent->getLastname();
            $tokenValues['app_datetime'] = $emailArray['appDateTime'];
            $emailDetails['staff_email'] = $personFaculty->getUsername();
            $emailDetails['email_key'] = $emailTemplateKey;
            $this->sendEmailNotification($emailTemplate, $tokenValues, $emailDetails, $this->emailService);
            if ($mailToStudent) {
                $this->logger->info("*********************  : Send EMail TO Student");
                $emailTemplate = $this->emailTemplateLangRepository->getEmailTemplateByKey('Appointment_Cancel_Staff_to_Student', $languageId);
                $emailTemplate->setSubject('Appointment cancelled');
                $tokenValuesStudent = [];
                $tokenValuesStudent['staff_name'] = $tokenValues['staff_name'];

                $studentAppointmentPageUrl = $this->ebiConfigService->generateCompleteUrl("StudentDashboard_AppointmentPage", $organizationId);
                if ($studentAppointmentPageUrl) {
                    $tokenValuesStudent['student_dashboard'] = $studentAppointmentPageUrl;
                } else {
                    $tokenValuesStudent['student_dashboard'] = "";
                }

                $tokenValuesStudent['app_datetime'] = $tokenValues['app_datetime'];
                // Including sky factor mapworks logo in email template
                $tokenValuesStudent['Skyfactor_Mapworks_logo'] = "";
                if ($systemUrl) {
                    $tokenValuesStudent['Skyfactor_Mapworks_logo'] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
                }
                $tokenValuesStudent['student_name'] = $personStudent->getFirstname();
                $emailDetailsStudent = [];
                $emailDetailsStudent['staff_email'] = $personStudent->getUsername();
                $emailDetailsStudent['email_key'] = 'Appointment_Cancel_Staff_to_Student';
                $emailDetailsStudent['orgId'] = $organizationId;
                $this->sendEmailNotification($emailTemplate, $tokenValuesStudent, $emailDetailsStudent, $this->emailService, true);
            }
        }

    }

    /**
     * This function will validate Mapwork office hour data has conflicts with any PCS events, it will return true if any conflicts found.
     *
     * @param object $pcsEvents
     * @param DateTime $slotStartDate
     * @param DateTime $slotEndDate
     * @return bool
     */
    private function isOfficeHourConflictWithPCSEvents($pcsEvents, $slotStartDate, $slotEndDate)
    {
        $hasConflict = false;
        if (!empty($pcsEvents)) {
            foreach ($pcsEvents as $event) {
                $startDate = $event->getSlotStart();
                $endDate = $event->getSlotEnd();
                if ($startDate > $slotStartDate) {
                    $endSlotDate = $slotEndDate;
                    $startSlotDate = $startDate;
                } else {
                    $endSlotDate = $endDate;
                    $startSlotDate = $slotStartDate;
                }
                if ($endSlotDate > $startSlotDate) {
                    $hasConflict = true;
                    break;
                }
            }
        }
        return $hasConflict;
    }
}