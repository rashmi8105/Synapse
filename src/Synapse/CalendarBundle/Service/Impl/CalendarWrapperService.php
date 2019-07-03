<?php
namespace Synapse\CalendarBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CalendarBundle\Repository\OrgCorporateGoogleAccessRepository;
use Synapse\CoreBundle\Entity\OrgPersonFaculty;
use Synapse\CoreBundle\Repository\AppointmentsRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OfficeHoursRepository;
use Synapse\CoreBundle\Repository\OfficeHoursSeriesRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Util\Constants\CalendarConstant;
use Synapse\RestBundle\Entity\CalendarTimeSlotsReponseDto;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Service\Impl\OrganizationService;

/**
 * @DI\Service("calendarwrapper_service")
 */
class CalendarWrapperService extends AbstractService
{
    const SERVICE_KEY = 'calendarwrapper_service';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var GoogleCalendarService
     */
    private $googleCalendarService;

    /**
     * @var GoogleFormatService
     */
    private $googleFormatService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var AppointmentsRepository
     */
    private $appointmentsRepository;

    /**
     * @var MetadataListValuesRepository
     */
    private $metadataListRepository;

    /**
     * @var OfficeHoursRepository
     */
    private $officeHoursRepository;

    /**
     * @var OfficeHoursSeriesRepository
     */
    private $officeHoursSeriesRepository;

    /**
     * @var OrgCorporateGoogleAccessRepository
     */
    private $orgCorporateGoogleAccessRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $personFacultyRepository;

    /**
     *
     * @param RepositoryResolver $repositoryResolver
     * @param Container $container
     * @param Logger $logger
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "container" = @DI\Inject("service_container"),
     *            "logger" = @DI\Inject("logger")
     *
     *            })
     */
    public function __construct($repositoryResolver, $container, $logger)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->logger = $logger;

        //Service Initialization
        $this->googleCalendarService = $this->container->get(GoogleCalendarService::SERVICE_KEY);
        $this->googleFormatService = $this->container->get(GoogleFormatService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);

        //Repository Initialization
        $this->appointmentsRepository = $this->repositoryResolver->getRepository(AppointmentsRepository::REPOSITORY_KEY);
        $this->metadataListRepository = $this->repositoryResolver->getRepository(MetadataListValuesRepository::REPOSITORY_KEY);
        $this->officeHoursRepository = $this->repositoryResolver->getRepository(OfficeHoursRepository::REPOSITORY_KEY);
        $this->officeHoursSeriesRepository = $this->repositoryResolver->getRepository(OfficeHoursSeriesRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->personFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgCorporateGoogleAccessRepository = $this->repositoryResolver->getRepository(OrgCorporateGoogleAccessRepository::REPOSITORY_KEY);
    }

    /**
     * Function to communicate Google Library to create events and send back the response to Mapworks
     *
     * @param array $pcsSyncData
     * @throws ValidationException | \Exception
     */
    public function syncPCS($pcsSyncData)
    {
        if ($pcsSyncData['calendarSettings']['campusSettings'] == 'G') {
            $personId = $pcsSyncData['personId'];
            $orgId = $pcsSyncData['orgId'];
            $personData = $this->getGoogleCredentials($personId, $orgId);
            $this->googleCalendarService->authenticateGoogleAPI($personData);
            $mafEventId = $pcsSyncData['mafEventId'];
            $googleAppointmentId = null;
            if (isset($pcsSyncData['googleAppointmentId'])) {
                $googleAppointmentId = $pcsSyncData['googleAppointmentId'];
            }
            // Create, update, Cancel appointments in Google and bring back the response to Mapworks.
            if ($pcsSyncData['eventType'] == 'appointment') {
                switch ($pcsSyncData['crudType']) {
                    case 'create':
                        $googleEvents = $this->googleFormatService->formatMAFAppointmentDataToGoogle($pcsSyncData);
                        $events = $this->googleCalendarService->createAppointment($googleEvents);
                        $this->updatePCSReference($events, 'appointment', 'create', $mafEventId);
                        break;
                    case 'update':
                        $googleEvents = $this->googleFormatService->formatMAFAppointmentDataToGoogle($pcsSyncData);
                        $this->googleCalendarService->updateAppointment($googleEvents, $googleAppointmentId);
                        $events = $this->googleCalendarService->updateAppointment($googleEvents, $googleAppointmentId);
                        if (isset($pcsSyncData['officeHourToAppointment'])) {
                            $type = 'create';
                        } else {
                            $type = 'update';
                        }
                        $this->updatePCSReference($events, 'appointment', $type, $mafEventId);
                        break;
                    case 'delete':
                        $events = $this->googleCalendarService->cancelAppointment($googleAppointmentId);
                        $this->updatePCSReference($events, 'appointment', 'delete', $mafEventId);
                        break;
                    default:
                        $this->logger->error("Invalid option found in appointment to create events in Google Calendar");
                        break;
                }
            }
            // Create, update, Cancel Office hours in Goolge and bring back the response to Mapworks
            if ($pcsSyncData['eventType'] == 'officehour') {
                switch ($pcsSyncData['crudType']) {
                    case 'create':
                        $googleEvents = $this->googleFormatService->formatMAFOfficeHourDataToGoogle($pcsSyncData);
                        $events = $this->googleCalendarService->createAppointment($googleEvents);
                        $this->updatePCSReference($events, 'officehour', 'create', $mafEventId);
                        break;
                    case 'update':
                        $googleEvents = $this->googleFormatService->formatMAFOfficeHourDataToGoogle($pcsSyncData);
                        $this->googleCalendarService->updateAppointment($googleEvents, $googleAppointmentId);
                        $events = $this->googleCalendarService->updateAppointment($googleEvents, $googleAppointmentId);
                        $this->updatePCSReference($events, 'officehour', 'update', $mafEventId);
                        break;
                    case 'delete':
                        $events = $this->googleCalendarService->cancelAppointment($googleAppointmentId);
                        $this->updatePCSReference($events, 'officehour', 'delete', $mafEventId);
                        break;
                    default:
                        $this->logger->error("Invalid option found in office hour to create events in Google Calendar");
                        break;
                }
            }
            // Create, update, Cancel Office Hour series in Google and bring back the response to Mapworks
            if ($pcsSyncData['eventType'] == 'officehourseries') {
                switch ($pcsSyncData['crudType']) {
                    case 'create':
                        $googleEvents = $this->googleFormatService->formatMAFOfficeHourSeriesDataToGoogle($pcsSyncData);
                        $events = $this->googleCalendarService->createAppointment($googleEvents);
                        $masterGoogleId = $events->id;
                        $events = $this->googleCalendarService->getInstance($masterGoogleId);
                        $this->updateOfficeHourSeriesWithPCSData($events, $mafEventId, $personId, $orgId, $masterGoogleId);
                        break;
                    case 'update':
                        $googleEvents = $this->googleFormatService->formatMAFOfficeHourSeriesDataToGoogle($pcsSyncData);
                        $this->googleCalendarService->cancelAppointment($googleAppointmentId);
                        $events = $this->googleCalendarService->createAppointment($googleEvents);
                        $masterGoogleId = $events->id;
                        $events = $this->googleCalendarService->getInstance($masterGoogleId);
                        $this->updateOfficeHourSeriesWithPCSData($events, $mafEventId, $personId, $orgId, $masterGoogleId);
                        break;
                    case 'delete':
                        $isCancelled = $this->googleCalendarService->cancelAppointment($googleAppointmentId);
                        if (empty($isCancelled)) {
                            $this->updatePCSReferenceOnDeletedSeries($mafEventId);
                        }
                        break;
                    default:
                        $this->logger->error("Invalid option found in office hour to create events in Google Calendar");
                        break;
                }
            }
        }
    }

    /**
     * Function to validate, is that person granted the access for Google calendar.
     *
     * @param int $personId
     * @param int $orgId
     * @return null|OrgPersonFaculty
     * @throws ValidationException
     */
    public function getGoogleCredentials($personId, $orgId)
    {
        $orgCorporateAccess = $this->orgCorporateGoogleAccessRepository->findOneBy([
            'organization' => $orgId
        ]);
        $personObj = $this->personFacultyRepository->findOneBy([
            CalendarConstant::PERSON => $personId,
            CalendarConstant::ORGANIZATION => $orgId
        ]);
        if (isset($orgCorporateAccess) && $orgCorporateAccess->getStatus() == 1) {
            $personObj->setOauthCalAccessToken($orgCorporateAccess->getOauthCalAccessToken());
            $personObj->setOauthCalRefreshToken($orgCorporateAccess->getOauthCalRefreshToken());
        }
        if (empty($personObj->getOauthCalAccessToken()) && empty($personObj->getOauthCalRefreshToken())) {
            throw new ValidationException([
                'Appointments are not synced with Google - Missing google credentials'
            ], 'Appointments are not synced with Google - Missing google credentials', 'google_credentials_missed');
        }
        return $personObj;
    }

    /**
     * Function to update the PCS (Google) details into Mapworks when appointments/officehours are created or changed
     *
     * @param object $response
     * @param string $calendarType
     * @param string $crudType
     * @param int $mafEventId
     */
    public function updatePCSReference($response, $calendarType, $crudType, $mafEventId)
    {
        $syncOn = new \DateTime('now');
        $syncOn->setTimezone(new \DateTimeZone('UTC'));
        if ($calendarType == 'appointment') {
            $appointments = $this->appointmentsRepository->find($mafEventId);
            switch ($crudType) {
                case 'create':
                    $appointments->setGoogleAppointmentId($response->id);                   
                    break;
                case 'update':
                    $appointments->setGoogleAppointmentId($response->id);
                    break;
                case 'delete':
                    $appointments->setGoogleAppointmentId(NULL);
                    break;
            }
            $appointments->setLastSynced($syncOn);
            $this->appointmentsRepository->flush();
        }
        if ($calendarType == 'officehour') {
            $officeHours = $this->officeHoursRepository->find($mafEventId);
            switch ($crudType) {
                case 'create':
                    $officeHours->setGoogleAppointmentId($response->id);
                    break;
                case 'delete':
                    $officeHours->setGoogleAppointmentId(NULL);
                    break;
            }
            $officeHours->setLastSynced($syncOn);
            $this->officeHoursRepository->flush();
        }
    }

    /**
     * When the sync is enabled by the user, all future newly created, modified, cancelled
     * appointments, office hours and series should be synced with Google.
     * This function will be called through job
     *
     * @param int $personId
     * @param int $orgId
     * @throws ValidationException
     * @throws \Exception
     */
    public function initialSyncToPcs($personId, $orgId)
    {
        $personData = $this->getGoogleCredentials($personId, $orgId);
        $this->googleCalendarService->authenticateGoogleAPI($personData);
        $currentDate = new \DateTime('now');
        $currentDate->setTimezone(new \DateTimeZone('UTC'));

        // Sync future appointments which was newly created in Mapworks should be synced to Google.
        $futureAppointments = $this->appointmentsRepository->getFutureAppointmentForFaculty($orgId, $personId, $currentDate->format('Y-m-d H:i:s'));
        if (!empty($futureAppointments)) {
            foreach ($futureAppointments as $futureAppointment) {
                $appointment = $this->appointmentsRepository->find($futureAppointment['appointment_id']);
                $appointmentData = $this->googleFormatService->getGoogleSyncData($appointment, $personData, 'appointment');
                $googleEvents = $this->googleFormatService->formatMAFAppointmentDataToGoogle($appointmentData);
                $officeHour = $this->officeHoursRepository->findOneBy([
                    'appointments' => $futureAppointment['appointment_id']
                ]);
                if (isset($officeHour) && $officeHour->getGoogleAppointmentId()) {
                    $events = $this->googleCalendarService->updateAppointment($googleEvents, $officeHour->getGoogleAppointmentId());
                } else {
                    $events = $this->googleCalendarService->createAppointment($googleEvents);
                }
                $this->updatePCSReference($events, 'appointment', 'create', $appointment->getId());
            }
        }


        // Sync future office hours which was newly created in Mapworks should be synced to Google.
        $futureOfficeHours = $this->officeHoursRepository->getFutureOfficeHoursForFaculty($orgId, $personId, $currentDate);
        if (!empty($futureOfficeHours)) {
            foreach ($futureOfficeHours as $futureOfficeHour) {
                $officeHourData = $this->googleFormatService->getGoogleSyncData($futureOfficeHour, $personData, 'officehour');
                $googleEvents = $this->googleFormatService->formatMAFOfficeHourDataToGoogle($officeHourData);
                $events = $this->googleCalendarService->createAppointment($googleEvents);
                $this->updatePCSReference($events, 'officehour', 'create', $futureOfficeHour->getId());
            }
        }

        /// Sync future office hour series which was newly created in Mapworks should be synced to Google.
        $futureOfficeHoursSeries = $this->officeHoursSeriesRepository->getFutureOfficeHourSeriesForFaculty($orgId, $personId);
        if (!empty($futureOfficeHoursSeries)) {
            foreach ($futureOfficeHoursSeries as $officeHourId) {
                $officeHoursSeries = $this->officeHoursSeriesRepository->find($officeHourId['office_hours_series_id']);
                $officeHourData = $this->googleFormatService->getGoogleSyncData($officeHoursSeries, $personData, 'officehourseries');
                $googleEvents = $this->googleFormatService->formatMAFOfficeHourSeriesDataToGoogle($officeHourData);
                $events = $this->googleCalendarService->createAppointment($googleEvents);
                $seriesGoogleEventId = $events->id;
                $events = $this->googleCalendarService->getInstance($seriesGoogleEventId);
                $this->updateOfficeHourSeriesWithPCSData($events, $officeHoursSeries->getId(), $personId, $orgId, $seriesGoogleEventId);
            }
        }

        // Sync future appointments which was modified in mapworks when the sync was disabled, this should be synced to Google
        // when it is re-enabled again by the user.
        if ($personData->getGoogleSyncDisabledTime()) {
            $modifiedDate = $personData->getGoogleSyncDisabledTime();
            $futureModifiedAppointments = $this->appointmentsRepository->getFutureAppointmentsModified($orgId, $personId, $currentDate->format('Y-m-d H:i:s'), $modifiedDate->format('Y-m-d H:i:s'));
            if (!empty($futureModifiedAppointments)) {
                foreach ($futureModifiedAppointments as $futureAppointment) {
                    $modifiedAppointment = $this->appointmentsRepository->find($futureAppointment['appointment_id']);
                    $googleEventId = $modifiedAppointment->getGoogleAppointmentId();
                    $appointmentData = $this->googleFormatService->getGoogleSyncData($modifiedAppointment, $personData, 'appointment');
                    $googleEvents = $this->googleFormatService->formatMAFAppointmentDataToGoogle($appointmentData);
                    $events = $this->googleCalendarService->updateAppointment($googleEvents, $googleEventId);
                    $this->updatePCSReference($events, 'appointment', 'update', $modifiedAppointment->getId());
                }
            }
            //Sync modified office hours
            $futureOfficeHours = $this->officeHoursRepository->getFutureOfficeHoursModified($orgId, $personId, $currentDate, $modifiedDate);
            if (!empty($futureOfficeHours)) {
                foreach ($futureOfficeHours as $officeHours) {
                    $googleEventId = $officeHours->getGoogleAppointmentId();
                    $officeHourData = $this->googleFormatService->getGoogleSyncData($officeHours, $personData, 'officehour');
                    $googleEvents = $this->googleFormatService->formatMAFOfficeHourDataToGoogle($officeHourData);
                    $events = $this->googleCalendarService->updateAppointment($googleEvents, $googleEventId);
                    $this->updatePCSReference($events, 'officehour', 'update', $officeHours->getId());
                }
            }

            // Sync modified office hours series
            $modifiedOfficeHoursSeries = $this->officeHoursSeriesRepository->getFutureOfficeHoursSeriesModified($orgId, $personId, $modifiedDate->format('Y-m-d H:i:s'));
            if (!empty($modifiedOfficeHoursSeries)) {
                foreach ($modifiedOfficeHoursSeries as $officeHourId) {
                    $officeHoursSeries = $this->officeHoursSeriesRepository->find($officeHourId['office_hours_series_id']);
                    $officeHourData = $this->googleFormatService->getGoogleSyncData($officeHoursSeries, $personData, 'officehourseries');
                    $googleEvents = $this->googleFormatService->formatMAFOfficeHourSeriesDataToGoogle($officeHourData);
                    $googleEventId = $officeHoursSeries->getGoogleMasterAppointmentId();
                    $events = $this->googleCalendarService->updateAppointment($googleEvents, $googleEventId);
                    $seriesGoogleEventId = $events->id;
                    $events = $this->googleCalendarService->getInstance($seriesGoogleEventId);
                    $this->updateOfficeHourSeriesWithPCSData($events, $officeHoursSeries->getId(), $personId, $orgId, $seriesGoogleEventId);
                }
            }

            //delete events from Google which appointments are deleted in mapworks
            $deleteAppointments = $this->appointmentsRepository->getDeletedFutureAppointments($orgId, $personId, $currentDate->format('Y-m-d H:i:s'), $modifiedDate->format('Y-m-d H:i:s'), 'S');
            if (!empty($deleteAppointments)) {
                foreach ($deleteAppointments as $deletedAppointment) {
                    $googleAppointmentId = $deletedAppointment['google_appointment_id'];
                    $this->googleCalendarService->cancelAppointment($googleAppointmentId);
                    $this->officeHoursRepository->updateOfficeHoursWithNULL('Appointments', 'google_appointment_id', $deletedAppointment['id']);
                }
                $this->officeHoursRepository->flush();
            }
            // delete events from Google which office hours are deleted from Mapworks
            $deleteOfficeHours = $this->officeHoursRepository->getDeletedFutureOfficeHours($orgId, $personId, $currentDate->format('Y-m-d H:i:s'), $modifiedDate->format('Y-m-d H:i:s'));
            if (!empty($deleteOfficeHours)) {
                foreach ($deleteOfficeHours as $deletedOfficeHours) {
                    $googleAppointmentId = $deletedOfficeHours['google_appointment_id'];
                    $this->googleCalendarService->cancelAppointment($googleAppointmentId);
                    $this->officeHoursRepository->updateOfficeHoursWithNULL('office_hours', 'google_appointment_id', $deletedOfficeHours['id']);
                    if (!empty($deletedOfficeHours['office_hours_series_id'])) {
                        $officeHourSeriesId[] = $deletedOfficeHours['office_hours_series_id'];
                    }
                }
                $this->officeHoursRepository->flush();
            }

            // Delete google master appointment id from office_hours_series if all slots are deleted from office_hours
            if (!empty($officeHourSeriesId)) {
                $seriesId = array_unique($officeHourSeriesId);
                $this->officeHoursRepository->updateOfficeHoursWithNULL('office_hours_series', 'google_master_appointment_id', $seriesId);
                $this->officeHoursSeriesRepository->flush();
            }

        }
    }

    /**
     * When sync disabled, if the user decided to remove the appointments which was already synced in Google.
     * This will called through resque Job
     *
     * @param int $personId
     * @param int $orgId
     * @param bool $removeMafToPcs
     * @param bool $syncStatus
     * @throws ValidationException
     * @throws \Exception
     */
    public function removeEvent($personId, $orgId, $removeMafToPcs, $syncStatus)
    {
        if ($removeMafToPcs) {
            // Remove Mapwork appointments from Google
            $syncedMafAppointments = $this->appointmentsRepository->getSyncedMafAppointments($orgId, $personId);
            $personData = $this->getGoogleCredentials($personId, $orgId);
            $this->googleCalendarService->authenticateGoogleAPI($personData);
            if (!empty($syncedMafAppointments)) {
                foreach ($syncedMafAppointments as $syncedMafAppointment) {
                    $googleAppointmentId = $syncedMafAppointment->getGoogleAppointmentId();
                    $events = $this->googleCalendarService->cancelAppointment($googleAppointmentId);
                    $this->updatePCSReference($events, 'appointment', 'delete', $syncedMafAppointment->getId());
                }
            }

            // Remove Mapwork office hours from Google
            $syncedMafOfficeHours = $this->officeHoursRepository->getSyncedMafOfficeHours($orgId, $personId);
            if (!empty($syncedMafOfficeHours)) {
                foreach ($syncedMafOfficeHours as $syncedMafOfficeHour) {
                    $googleAppointmentId = $syncedMafOfficeHour->getGoogleAppointmentId();
                    $events = $this->googleCalendarService->cancelAppointment($googleAppointmentId);
                    $this->updatePCSReference($events, 'officehour', 'delete', $syncedMafOfficeHour->getId());
                }
            }
            // Delete google master appointment id from office_hours_series if all series is deleted from office_hours
            $syncedMafSeries = $this->officeHoursSeriesRepository->getSyncedOfficeHourSeries($orgId, $personId);
            if (!empty($syncedMafSeries)) {
                foreach ($syncedMafSeries as $syncedSeries) {
                    $googleAppointmentId = $syncedSeries['google_master_appointment_id'];
                    $this->googleCalendarService->cancelAppointment($googleAppointmentId);
                    $this->updatePCSReferenceOnDeletedSeries($syncedSeries['id']);
                }
                $this->officeHoursSeriesRepository->flush();
            }
        }
        // Update tokens
        if (!$syncStatus) {
            $personObj = $this->personFacultyRepository->findOneBy([
                CalendarConstant::PERSON => $personId,
                CalendarConstant::ORGANIZATION => $orgId
            ]);
            $orgCorporateAccess = $this->orgCorporateGoogleAccessRepository->findOneBy([
                'organization' => $orgId
            ]);
            if (!$orgCorporateAccess->getStatus()) {
                $personObj->setGoogleEmailId(NULL);
            }
            $personObj->setOauthOneTimeToken(NULL);
            $personObj->setOauthCalAccessToken(NULL);
            $personObj->setOauthCalRefreshToken(NULL);
            $this->personFacultyRepository->flush();
        }
    }


    /**
     * Update Google response to Mapworks as part of office hour series.
     *
     * @param object $events
     * @param int $seriesId
     * @param int $personId
     * @param int $orgId
     * @param string $seriesGoogleEventId
     */
    public function updateOfficeHourSeriesWithPCSData($events, $seriesId, $personId, $orgId, $seriesGoogleEventId)
    {
        $officeHourSeries = $this->officeHoursSeriesRepository->find($seriesId);
        if ($officeHourSeries) {
            $syncOn = new \DateTime('now');
            $syncOn->setTimezone(new \DateTimeZone('UTC'));
            $officeHourSeries->setGoogleMasterAppointmentId($seriesGoogleEventId);
            $officeHourSeries->setLastSynced($syncOn);
            if (!empty($events)) {
                $reflector = new \ReflectionClass($events);
                $classProperty = $reflector->getProperty('modelData');
                $classProperty->setAccessible(true);
                $officeHourData = $classProperty->getValue($events);
                if (!empty($officeHourData)) {
                    foreach ($officeHourData['items'] as $officeHour) {
                        $startTime = $officeHour['start']['dateTime'];
                        $startDateString = strtotime($startTime);
                        $startDate = date('Y-m-d H:i:s', $startDateString);
                        $slotStart = new \DateTime($startDate);
                        $endTime = $officeHour['end']['dateTime'];
                        $endDateString = strtotime($endTime);
                        $endDate = date('Y-m-d H:i:s', $endDateString);
                        $slotEnd = new \DateTime($endDate);
                        $officeHourEntity = $this->officeHoursRepository->findOneBy(['organization' => $orgId, 'person' => $personId,
                            'officeHoursSeries' => $seriesId, 'slotStart' => $slotStart, 'slotEnd' => $slotEnd]);
                        $officeHourId = $officeHour['id'];
                        if (!empty($officeHourEntity)) {
                            $currentTime = new \DateTime('now');
                            $currentTime->setTimezone(new \DateTimeZone('UTC'));
                            if ($slotStart < $currentTime) {
                                $this->googleCalendarService->cancelAppointment($officeHourId);
                            } else {
                                $officeHourEntity->setGoogleAppointmentId($officeHourId);
                            }
                            $officeHourEntity->setLastSynced($syncOn);
                        } else {
                            $this->googleCalendarService->cancelAppointment($officeHourId);
                        }
                    }
                    $this->officeHoursSeriesRepository->flush();
                }
            }
        }
    }

    /**
     * Update Google response to Mapworks when the office hour series is deleted in Google.
     * @param int $seriesId
     */
    public function updatePCSReferenceOnDeletedSeries($seriesId)
    {
        $officeHourSeries = $this->officeHoursSeriesRepository->find($seriesId);
        $syncOn = new \DateTime('now');
        $syncOn->setTimezone(new \DateTimeZone('UTC'));
        if (!empty($officeHourSeries)) {
            $officeHourSeries->setGoogleMasterAppointmentId(NULL);
            $officeHourSeries->setLastSynced($syncOn);
        }
        $officeHours = $this->officeHoursRepository->findBy(['officeHoursSeries' => $seriesId]);
        if (!empty($officeHours)) {
            foreach ($officeHours as $officeHour) {
                if (!empty($officeHour->getGoogleAppointmentId())) {
                    $officeHour->setGoogleAppointmentId(NULL);
                    $officeHour->setLastSynced($syncOn);
                }
            }
        }
    }


    /**
     * Function to get the list of busy and tentative events from Google for the given user in a period of time
     *
     * @param int $personId
     * @param int $organizationId
     * @param array $pcsCalendarIds
     * @param string $fromDate
     * @param string $toDate
     * @return array
     * @throws \Exception
     */
    public function listBusyTentativeEventsFromGoogle($personId, $organizationId, $pcsCalendarIds, $fromDate, $toDate)
    {
        $fromDate = new \DateTime($fromDate);
        $fromDate = $fromDate->format('Y-m-d\TH:i:s\Z');
        $toDate = new \DateTime($toDate);
        $toDate = $toDate->format('Y-m-d\T23:59:59\Z');
        $personObj = $this->getGoogleCredentials($personId, $organizationId);
        $this->googleCalendarService->authenticateGoogleAPI($personObj);
        $gmailId = $personObj->getGoogleEmailId();
        if ($gmailId) {
            $events = $this->googleCalendarService->listEvents($gmailId, $fromDate, $toDate);
        }
        $eventList = array();
        if (!empty($events) && is_array($events)) {
            $email = array_shift(array_keys($events));
            foreach ($events[$email] as $event) {
                $reflector = new \ReflectionClass($event);
                $classProperty = $reflector->getProperty('modelData');
                $classProperty->setAccessible(true);
                $eventDetails = $classProperty->getValue($event);
                $calendarSlotDto = new CalendarTimeSlotsReponseDto();
                $currentDate = new \DateTime('now');
                $currentDate->setTimezone(new \DateTimeZone('UTC'));

                $eventStartDate = NULL;
                $eventEndDate = NULL;
                $startDateGreaterThanCurrentDate = false;
                if (isset($eventDetails['start']['dateTime'])) {
                    $eventStartDate = new \DateTime($eventDetails['start']['dateTime']);
                    $eventEndDate = new \DateTime($eventDetails['end']['dateTime']);
                    if ($currentDate <= $eventStartDate) {
                        $startDateGreaterThanCurrentDate = true;
                    }
                } else {
                    $calendarTimeZone = $this->googleCalendarService->getCalendarTimeZone();
                    $timeZoneObject = new \DateTimeZone($calendarTimeZone);
                    $eventStartDate = new \DateTime($eventDetails['start']['date'] . ' 00:00:00', $timeZoneObject);
                    $eventStartDate->setTimezone(new \DateTimeZone('UTC'));
                    $eventEndDate = new \DateTime($eventDetails['start']['date'] . ' 23:59:59', $timeZoneObject);
                    $eventEndDate->setTimezone(new \DateTimeZone('UTC'));
                    $startDateGreaterThanCurrentDate = true;
                }
                if (!in_array($event->id, $pcsCalendarIds) && $startDateGreaterThanCurrentDate && empty($event->transparency)) {
                    $calendarSlotDto->setOfficeHoursId(0);
                    $calendarSlotDto->setOfficeHoursId(0);
                    $calendarSlotDto->setSlotStart($eventStartDate);
                    $calendarSlotDto->setSlotEnd($eventEndDate);
                    if ($event->status == 'confirmed') {
                        $calendarSlotDto->setSlotType('B');
                    } else {
                        $calendarSlotDto->setSlotType('F');
                    }
                    $calendarSlotDto->setAppointmentId(0);
                    $calendarSlotDto->setIsConflictedFlag(false);
                    $eventList[] = $calendarSlotDto;
                }
            }
        }
        return $eventList;
    }
}
