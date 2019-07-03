<?php
namespace Synapse\CalendarBundle\Service\Impl;

use DateTime;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\ExpressionLanguage\Token;
use Synapse\CalendarBundle\Entity\OrgCronofyHistory;
use Synapse\CalendarBundle\Entity\OrgCronofyCalendar;
use Synapse\CalendarBundle\Exception\CronofyException;
use Synapse\CalendarBundle\Repository\OrgCronofyCalendarRepository;
use Synapse\CalendarBundle\Repository\OrgCronofyHistoryRepository;
use Synapse\CoreBundle\Entity\Appointments;
use Synapse\CoreBundle\Entity\OfficeHours;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\AppointmentsRepository;
use Synapse\CoreBundle\Repository\CalendarSharingRepository;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\EmailTemplateRepository;
use Synapse\CoreBundle\Repository\OfficeHoursRepository;
use Synapse\CoreBundle\Repository\OfficeHoursSeriesRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\MapworksActionService;
use Synapse\CoreBundle\Service\Impl\NotificationChannelService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\JobBundle\Service\Impl\JobService;
use Synapse\RestBundle\Entity\CalendarTimeSlotsReponseDto;

/**
 * @DI\Service("cronofy_wrapper_service")
 */
class CronofyWrapperService extends AbstractService
{
    const SERVICE_KEY = 'cronofy_wrapper_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    // Services

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationService;

    /**
     * @var CronofyCalendarService
     */
    private $cronofyCalendarService;

    /**
     * @var CronofyFormatService
     */
    private $cronofyFormatService;

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
     * @var JobService
     */
    private $jobService;

    /**
     * @var MapworksActionService
     */
    private $mapworksActionService;

    /**
     * @var NotificationChannelService
     */
    private $notificationChannelService;

    // Repositories

    /**
     * @var AppointmentsRepository
     */
    private $appointmentsRepository;

    /**
     * @var CalendarSharingRepository
     */
    private $calendarSharingRepository;

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var EmailTemplateRepository
     */
    private $emailTemplateRepository;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var OfficeHoursRepository
     */
    private $officeHoursRepository;

    /**
     * @var OfficeHoursSeriesRepository
     */
    private $officeHoursSeriesRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgCronofyCalendarRepository
     */
    private $orgCronofyCalendarRepository;

    /**
     * @var OrgCronofyHistory
     */
    private $orgCronofyHistoryRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    // Private variables

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $profileName;

    /**
     * @var string
     */
    private $redirectURI;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var string
     */
    private $scope;

    /**
     * CronofyWrapperService constructor
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "container" = @DI\Inject("service_container"),
     *            "logger" = @DI\Inject("logger"),
     *            })
     *
     * @param RepositoryResolver $repositoryResolver
     * @param Container $container
     * @param Logger $logger
     *
     */
    public function __construct($repositoryResolver, $container, $logger)
    {
        parent::__construct($repositoryResolver, $logger);

        // Scaffolding
        $this->container = $container;
        $this->logger = $logger;

        //Services
        $this->alertNotificationService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->cronofyCalendarService = $this->container->get(CronofyCalendarService::SERVICE_KEY);
        $this->cronofyFormatService = $this->container->get(CronofyFormatService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->jobService = $this->container->get(JobService::SERVICE_KEY);
        $this->mapworksActionService = $this->container->get(MapworksActionService::SERVICE_KEY);
        $this->notificationChannelService = $this->container->get(NotificationChannelService::SERVICE_KEY);

        // Repositories
        $this->appointmentsRepository = $this->repositoryResolver->getRepository(AppointmentsRepository::REPOSITORY_KEY);
        $this->calendarSharingRepository = $this->repositoryResolver->getRepository(CalendarSharingRepository::REPOSITORY_KEY);
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->emailTemplateRepository = $this->repositoryResolver->getRepository(EmailTemplateRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->officeHoursRepository = $this->repositoryResolver->getRepository(OfficeHoursRepository::REPOSITORY_KEY);
        $this->officeHoursSeriesRepository = $this->repositoryResolver->getRepository(OfficeHoursSeriesRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgCronofyCalendarRepository = $this->repositoryResolver->getRepository(OrgCronofyCalendarRepository::REPOSITORY_KEY);
        $this->orgCronofyHistoryRepository = $this->repositoryResolver->getRepository(OrgCronofyHistoryRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
    }

    /**
     * Authenticate Cronofy API
     *
     * @param string $accessToken
     * @param string $refreshToken
     * @param OrgCronofyCalendar $cronofyCalendarObject
     * @throws CronofyException
     */
    public function authenticate($accessToken = '', $refreshToken = '', $cronofyCalendarObject = null)
    {
        $this->clientId = $this->ebiConfigService->get('cronofy_client_id');
        $this->clientSecret = $this->ebiConfigService->get('cronofy_client_secret');
        $systemApiUrl = $this->ebiConfigService->get('System_API_URL');
        $redirectUrl = $this->ebiConfigService->get('cronofy_redirect_uri');
        $this->redirectURI = $systemApiUrl . $redirectUrl;
        $this->scope = $this->ebiConfigService->get('cronofy_scope');
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;

        if (!empty($accessToken)) {
            $tokenCreatedTime = $cronofyCalendarObject->getModifiedAt()->getTimestamp();
            $currentTime = new \DateTime('now');
            $currentTimeStamp = $currentTime->getTimestamp();
            $timeDifference = $currentTimeStamp - $tokenCreatedTime;
            if ($timeDifference > 3600) {
                $this->cronofyCalendarService->refreshToken = $refreshToken;
                $this->cronofyCalendarService->clientId = $this->clientId;
                $this->cronofyCalendarService->clientSecret = $this->clientSecret;
                $tokens = $this->cronofyCalendarService->refreshToken();
                $accessToken = $tokens["access_token"];
                $refreshToken = $tokens["refresh_token"];
                $cronofyCalendarObject->setCronofyCalAccessToken($accessToken);
                $cronofyCalendarObject->setCronofyCalRefreshToken($refreshToken);
                $this->orgCronofyCalendarRepository->flush();
                $this->accessToken = $accessToken;
                $this->refreshToken = $refreshToken;
            }
        }
        $this->cronofyCalendarService->enableAuthentication($this->clientId, $this->clientSecret, $this->accessToken, $this->refreshToken);
    }

    /**
     * Generate redirection URI
     *
     * @param string $accessToken
     * @param string $oneTimeToken
     * @param string $type
     * @param boolean $isProxyUser
     * @return string
     */
    public function getAuthorizationURL($accessToken, $oneTimeToken, $type, $isProxyUser)
    {
        $this->authenticate();
        $params['redirect_uri'] = $this->redirectURI;
        $params['scope'] = explode(',', $this->scope);
        $params['avoid_linking'] = true;
        $params['state'] = $accessToken . '-queryString-' . $oneTimeToken . '-queryString-' . $type . '-queryString-' . $isProxyUser;
        $authURL = $this->cronofyCalendarService->getAuthorizationURL($params);
        return $authURL;
    }

    /**
     * Get Refresh and Access Tokens
     *
     * @param string $code
     * @return Token
     */
    public function requestToken($code)
    {
        $this->authenticate();
        $params['redirect_uri'] = $this->redirectURI;
        $params['code'] = $code;
        $token = $this->cronofyCalendarService->requestToken($params);
        return $token;
    }

    /**
     * Revoke Cronofy access when the sync is disabled in mapworks
     *
     * @param int $personId
     * @param int $organizationId
     */
    public function revokeAccess($personId, $organizationId)
    {
        $cronofyCalendarObject = $this->orgCronofyCalendarRepository->findOneBy(['person' => $personId, 'organization' => $organizationId]);
        $accessToken = $cronofyCalendarObject->getCronofyCalAccessToken();
        $refreshToken = $cronofyCalendarObject->getCronofyCalRefreshToken();
        $this->authenticate($accessToken, $refreshToken, $cronofyCalendarObject);

        // Close push notification channel if it is active
        $channelId = $cronofyCalendarObject->getCronofyChannel();
        if ($channelId) {
            $calendarParameter['channel_id'] = $channelId;
            $this->cronofyCalendarService->closeChannel($calendarParameter);
        }

        // Revoke Cronofy Access
        $this->cronofyCalendarService->revokeAuthorization($accessToken);

        // Remove cronofy tokens from Mapworks
        $cronofyCalendarObject->setCronofyCalAccessToken(NULL);
        $cronofyCalendarObject->setCronofyCalRefreshToken(NULL);
        $cronofyCalendarObject->setCronofyProfile(NULL);
        $cronofyCalendarObject->setCronofyCalendar(NULL);
        $cronofyCalendarObject->setCronofyProvider(NULL);
        $cronofyCalendarObject->setCronofyChannel(NULL);
        $cronofyCalendarObject->setStatus(0);
        $facultyObject = $this->orgPersonFacultyRepository->findOneBy(['person' => $personId, 'organization' => $organizationId]);
        if ($facultyObject) {
            $facultyObject->setPcsToMafIsActive(NULL);
            $facultyObject->setMafToPcsIsActive(NULL);
        }
        $this->orgCronofyCalendarRepository->flush();
    }

    /**
     * Find the users calendar id using their profile name
     *
     * @param string $accessToken
     * @param string $refreshToken
     * @param string $profileName
     * @param OrgCronofyCalendar $cronofyCalendarObject
     * @param string $providerName - Calendar provider, this may be google, exchange, outlook, office365, apple.
     * @return string
     */
    public function getCalendarId($accessToken, $refreshToken, $profileName, $cronofyCalendarObject, $providerName)
    {
        $this->authenticate($accessToken, $refreshToken, $cronofyCalendarObject);
        $primaryCalendarId = '';
        $calendars = $this->cronofyCalendarService->listCalendars();
        $this->profileName = $profileName;

        // List of calendar for a specific profile name
        // array_values is used to make the array index from 0
        $userCalendarList = array_values(array_filter($calendars['calendars'], function ($calendar) {
            if ($calendar['profile_name'] == $this->profileName) {
                return $calendar;
            }
        }));

        if ($providerName == 'apple') {
            // iCloud is not providing calendar_primary = 1 for their primary calendar,
            // with an assumption that primary calendar will be at 0th position.
            $primaryCalendarId = $userCalendarList[0]['calendar_id'];
        } else {
            // For Google, Exchange, Office365, Outlook.com - calendar_primary value will be 1 for Primary calendars,
            // We will be syncing the events on users primary calendar.
            foreach ($userCalendarList as $userCalendar) {
                if ($userCalendar['calendar_primary'] == 1) {
                    $primaryCalendarId = $userCalendar['calendar_id'];
                    break;
                }
            }
            if (empty($primaryCalendarId)) {
                $primaryCalendarId = $userCalendarList[0]['calendar_id'];
            }
        }

        return $primaryCalendarId;
    }

    /**
     * Sync newly created or deleted office hour series slots to external calendar.
     *
     * @param int $officeHourSeriesId
     * @param int $organizationId
     * @param int $personId
     * @param string $action
     * @param DateTime $currentTime
     * @return boolean
     * @throws \Exception
     */
    public function syncOfficeHourSeries($officeHourSeriesId, $organizationId, $personId, $action, $currentTime)
    {
        $allowedEventsCountPerBatch = $this->ebiConfigRepository->findOneBy(['key' => 'cronofy_allowed_request_per_second_count'])->getValue();
        $organizationTimeZone = $this->dateUtilityService->getOrganizationISOTimeZone($organizationId);

        $cronofyCalendarObject = $this->orgCronofyCalendarRepository->findOneBy(['person' => $personId, 'organization' => $organizationId]);
        $calendarId = $this->processAuthentication($cronofyCalendarObject);
        switch ($action) {
            case 'create':
                $this->syncOfficeHourSlotForSeries($calendarId, $officeHourSeriesId, $currentTime, $personId, $organizationId, $organizationTimeZone, $allowedEventsCountPerBatch);
                break;
            case 'update':
                $this->syncDeletedOfficeHoursForSeries($calendarId, $officeHourSeriesId, $currentTime, $personId, $organizationId, $allowedEventsCountPerBatch);
                $this->syncOfficeHourSlotForSeries($calendarId, $officeHourSeriesId, $currentTime, $personId, $organizationId, $organizationTimeZone, $allowedEventsCountPerBatch);
                break;
            case 'delete':
                $this->syncDeletedOfficeHoursForSeries($calendarId, $officeHourSeriesId, $currentTime, $personId, $organizationId, $allowedEventsCountPerBatch);
                break;
            default:
                break;
        }
        return true;
    }

    /**
     * Sync office hour series slots to external calendar.
     *
     * @param string $calendarId
     * @param int $officeHourSeriesId
     * @param DateTime $currentTime
     * @param int $personId
     * @param int $organizationId
     * @param string $organizationTimeZone
     * @param int $allowedEventsCountPerBatch
     * @return boolean
     */
    private function syncOfficeHourSlotForSeries($calendarId, $officeHourSeriesId, $currentTime, $personId, $organizationId, $organizationTimeZone, $allowedEventsCountPerBatch)
    {
        $formattedTime = $currentTime->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
        $officeHourSlotsToBeSynced = $this->officeHoursSeriesRepository->getOfficeHourSlotBySeriesId($officeHourSeriesId, $formattedTime, true);

        // Process all slots to external calendar
        $this->syncEventsByBatch($officeHourSlotsToBeSynced, $calendarId, $personId, $organizationId, $allowedEventsCountPerBatch, $formattedTime, 'POST', $organizationTimeZone);
        return true;
    }

    /**
     * Returns free busy calendar events for conflict validation.
     *
     * @param string $startDateString
     * @param string $endDateString
     * @param int $loggedInPersonId
     * @param int $organizationId
     * @return CronofyPagedResultIterator
     * @throws CronofyException | SynapseValidationException
     */
    public function getFreeBusyEventsForConflictValidation($startDateString, $endDateString, $loggedInPersonId, $organizationId)
    {
        $orgCronofyCalendarObject = $this->orgCronofyCalendarRepository->findOneBy([
            "person" => $loggedInPersonId,
            "organization" => $organizationId
        ]);

        if (isset($orgCronofyCalendarObject)) {
            $accessToken = $orgCronofyCalendarObject->getCronofyCalAccessToken();
            $refreshToken = $orgCronofyCalendarObject->getCronofyCalRefreshToken();
            $this->authenticate($accessToken, $refreshToken, $orgCronofyCalendarObject);

            $calendarId = $orgCronofyCalendarObject->getCronofyCalendar();

            // Since to date should be greater than from date adding one day to the end date
            /**
             * Cronofy cant handle time and hence need to pass date itself which can create conflict
             * with the API standards if same date appointment is created.
             */
            if ($startDateString == $endDateString) {
                $endDateTime = new \DateTime($endDateString);
                $endDateTime->modify("+1 day");
                $endDateStringNew = $endDateTime->format("Y-m-d");
            } else {
                $endDateStringNew = $endDateString;
            }

            $cronofyParameters = [];
            $cronofyParameters["from"] = $startDateString;
            $cronofyParameters["to"] = $endDateStringNew;
            $cronofyParameters["tzid"] = "Etc/UTC";
            $cronofyParameters["calendar_ids"][] = $calendarId;
            $cronofyParameters["localized_times"] = true;
            return $this->cronofyCalendarService->getFreeBusyEvents($cronofyParameters);
        } else {
            throw new SynapseValidationException("Cronofy Calendar Not Found.");
        }

    }

    /** Get Cronofy busy events for the given time
     *
     * @param int $personId
     * @param int $organizationId
     * @param string $fromDate
     * @param string $toDate
     * @return array
     */
    public function getBusyEvents($personId, $organizationId, $fromDate, $toDate)
    {
        $cronofyCalendarObject = $this->orgCronofyCalendarRepository->findOneBy(['person' => $personId, 'organization' => $organizationId]);
        $accessToken = $cronofyCalendarObject->getCronofyCalAccessToken();
        $refreshToken = $cronofyCalendarObject->getCronofyCalRefreshToken();
        $calendarId = $cronofyCalendarObject->getCronofyCalendar();
        $this->authenticate($accessToken, $refreshToken, $cronofyCalendarObject);
        $params['from'] = date('Y-m-d', strtotime($fromDate));
        // for Cronofy, if from date is N then to date should be N+1,
        // In order to handle this adding one day with to date.
        $toDate = new DateTime($toDate);
        $toDate->modify('+1 day');
        $params['to'] = $toDate->format('Y-m-d');
        $params['tzid'] = 'Etc/UTC';
        $params['calendar_ids'][] = $calendarId;
        $params['localized_times'] = true;
        $events = $this->cronofyCalendarService->getFreeBusyEvents($params);

        $currentDate = new \DateTime('now');
        $externalEvents = [];
        if (!empty($events)) {
            foreach ($events->firstPage['free_busy'] as $event) {
                if ($event['free_busy_status'] == 'busy' || ($cronofyCalendarObject->getCronofyProvider() == 'apple' && $event['free_busy_status'] == 'free')) {
                    $calendarSlotDto = new CalendarTimeSlotsReponseDto();
                    $calendarSlotDto->setOfficeHoursId(0);

                    $localTimeZone = new \DateTimeZone($event['start']['tzid']);
                    $startTime = $event['start']['time'];
                    $endTime = $event['end']['time'];
                    $startDateObject = new DateTime($startTime);
                    $endDateObject = new DateTime($endTime);
                    $interval = $startDateObject->diff($endDateObject);
                    $numberOfDays = $interval->d;
                    // Update Multi day event flag
                    if ($interval->m >= 1 || $interval->y >= 1) {
                        $calendarSlotDto->setIsMultiDayEvent(true);
                    }

                    if (strpos($startTime, 'T') == false) {
                        // All day event with no start and end time selected (00 - 23:59)
                        $startDateTime = $startTime . ' 00:00:00';
                        $endDateTime = $startTime . ' 23:59:59';
                        if ($numberOfDays > 1) {
                            $calendarSlotDto->setIsMultiDayEvent(true);
                            $endDate = $endDateObject->modify('-1 day');
                            $endDateTime = $endDate->format('Y-m-d') . ' 23:59:59';
                        }
                        $calendarSlotDto->setIsAllDayEvent(true);
                        $allDaySlotStartDate = new \DateTime($startDateTime);
                        $allDaySlotEndDate = new \DateTime($endDateTime);

                        $calendarSlotDto->setAllDaySlotStart($allDaySlotStartDate);
                        $calendarSlotDto->setAllDaySlotEnd($allDaySlotEndDate);
                    } else {
                        if ($numberOfDays >= 1) {
                            // Time bounded full day event or multi day events
                            $calendarSlotDto->setIsMultiDayEvent(true);
                        }
                        $endDateTime = $endTime;
                        $startDateTime = $startTime;
                    }
                    $startDate = new \DateTime($startDateTime, $localTimeZone);
                    $startDate->setTimezone(new \DateTimeZone('UTC'));

                    $endDate = new \DateTime($endDateTime, $localTimeZone);
                    $endDate->setTimezone(new \DateTimeZone('UTC'));

                    $calendarSlotDto->setSlotStart($startDate);
                    $calendarSlotDto->setSlotEnd($endDate);
                    $calendarSlotDto->setAppointmentId(0);
                    $calendarSlotDto->setSlotType('B');
                    $calendarSlotDto->setIsConflictedFlag(false);

                    // If its time bounded event, past event and also current event, then remove that event from list
                    if (empty($calendarSlotDto->getIsAllDayEvent()) && empty($calendarSlotDto->getIsMultiDayEvent()) && ($currentDate >= $calendarSlotDto->getSlotEnd())) {
                        continue;
                    }
                    $externalEvents[] = $calendarSlotDto;
                }
            }
        }
        return $externalEvents;
    }

    /**
     * When sync disabled, if the user decided to remove the appointments which was already synced in Cronofy.
     * This will called through resque Job
     *
     * @param int $organizationId
     * @param bool $removeMafToPcs
     * @param bool $pcsRemove
     * @param string $type
     * @param string|null $event
     * @param int|null $personId
     * @param bool $syncStatus
     */
    public function removeEvent($organizationId, $removeMafToPcs, $pcsRemove, $type, $event = null, $personId = null, $syncStatus = null)
    {
        $i = 0;
        if ($type == 'admin' || $type == 'coordinator') {
            $this->removeEventByAdmin($organizationId, $pcsRemove);
            if ($type == 'coordinator') {
                $batchSize = 30;
                $facultyList = $this->orgPersonFacultyRepository->findBy([
                    'organization' => $organizationId
                ]);
                $organization = $this->organizationRepository->find($organizationId);
                if (!empty($facultyList)) {
                    foreach ($facultyList as $faculty) {
                        $faculty->setPcsToMafIsActive('n');
                        $faculty->setMafToPcsIsActive('n');
                        $faculty->setGoogleSyncStatus(NULL);
                        $person = $faculty->getPerson();
                        $this->alertNotificationService->createNotification($event, 'External Calendar', $person, null, null, null, null, null, null, $organization);
                        $i++;
                        if (($i % $batchSize) === 0) {
                            $this->orgPersonFacultyRepository->flush();
                        }
                    }
                    $this->orgPersonFacultyRepository->flush();
                }
            }
        } else {
            $this->removeEventByFaculty($organizationId, $removeMafToPcs, $personId, $syncStatus);
        }
    }

    /**
     * Remove appointments and office hours of all faculty based on organization id
     *
     * @param $organizationId
     * @param $pcsRemove
     * @return bool
     */
    private function removeEventByAdmin($organizationId, $pcsRemove)
    {
        $listOfCronofyCalendarEnabledUser = $this->orgCronofyCalendarRepository->getListOfCronofyCalendarSyncUsers($organizationId);
        if ($listOfCronofyCalendarEnabledUser) {
            foreach ($listOfCronofyCalendarEnabledUser as $person) {
                $personId = $person['person_id'];
                // Remove all events from external calendar if they desired to remove them.
                try {
                    $this->removeEventByFaculty($organizationId, $pcsRemove, $personId);
                } catch (\Exception $e) {
                    $this->removeCronofyReference($organizationId, $personId);
                }
            }
        }
        return true;
    }


    /**
     * Initial sync to external calendar - Future appointments (which is newly created, modified, deleted)
     * should be synced with external calendar.
     *
     * @param int $personId
     * @param int $organizationId
     * @param int|null $loggedInPersonId
     */
    public function initialSyncToPcs($personId, $organizationId, $loggedInPersonId = null)
    {
        $allowedEventsCountPerBatch = $this->ebiConfigRepository->findOneBy(['key' => 'cronofy_allowed_request_per_second_count'])->getValue();
        $organizationTimeZone = $this->dateUtilityService->getOrganizationISOTimeZone($organizationId);
        $cronofyCalendarObject = $this->orgCronofyCalendarRepository->findOneBy([
            'person' => $personId,
            'organization' => $organizationId
        ]);
        $calendarId = $cronofyCalendarObject->getCronofyCalendar();
        $accessToken = $cronofyCalendarObject->getCronofyCalAccessToken();
        $refreshToken = $cronofyCalendarObject->getCronofyCalRefreshToken();
        $this->authenticate($accessToken, $refreshToken, $cronofyCalendarObject);
        $currentDate = new \DateTime('now');
        $formattedTime = $currentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);

        // Sync future appointments
        $this->syncFutureAppointments($calendarId, $organizationId, $personId, $currentDate, $allowedEventsCountPerBatch, $organizationTimeZone);
        // Sync future office hours
        $this->syncFutureOfficeHours($calendarId, $organizationId, $personId, $currentDate, $allowedEventsCountPerBatch, $organizationTimeZone);
        // Sync future appointments which was deleted in mapworks
        $this->syncDeletedMapworkAppointments($calendarId, $organizationId, $personId, $formattedTime, $allowedEventsCountPerBatch);
        // Sync future office hours which was deleted in mapworks
        $this->syncDeletedMapworkOfficeHours($calendarId, $organizationId, $personId, $formattedTime, $allowedEventsCountPerBatch);
    }

    /**
     * Remove appointments and office hours of faculty based on organization id and person id
     *
     * @param int $organizationId
     * @param boolean $removeMafToPcs
     * @param int $personId
     * @param bool $syncStatus
     * @return void
     */
    public function removeEventByFaculty($organizationId, $removeMafToPcs, $personId, $syncStatus = false)
    {
        // Remove all appointments and events from external calendar
        $cronofyCalendarObject = $this->orgCronofyCalendarRepository->findOneBy(['person' => $personId, 'organization' => $organizationId]);
        if ($removeMafToPcs && $cronofyCalendarObject) {
            $calendarId = $cronofyCalendarObject->getCronofyCalendar();
            $accessToken = $cronofyCalendarObject->getCronofyCalAccessToken();
            $refreshToken = $cronofyCalendarObject->getCronofyCalRefreshToken();

            $this->authenticate($accessToken, $refreshToken, $cronofyCalendarObject);
            $calendarId = ["calendar_ids" => [$calendarId]];
            $this->cronofyCalendarService->deleteAllEvents($calendarId);
        }
        // Revoke access of that faculty
        if (!$syncStatus && $cronofyCalendarObject) {
            // This would be the case when user has selected both MAF to PCS and PCS to MAF then only we have to revoke access from cronofy,
            // Otherwise we should not revoke them.
            $this->revokeAccess($personId, $organizationId);
            $this->updateCronofyHistory($personId, $organizationId, 'Calendar disabled');
        }
    }

    /**
     * Create Channel for cronofy Push notification
     *
     * @param int $personId
     * @param string $calendarId
     * @param string $accessToken
     * @param string $refreshToken
     * @param OrgCronofyCalendar $cronofyCalendarObject
     * @return string
     */
    public function createChannel($personId, $calendarId, $accessToken, $refreshToken, $cronofyCalendarObject)
    {
        $channelId = '';
        $callBackURL = $this->ebiConfigRepository->findOneBy(['key' => 'cronofy_callback_url']);
        $systemApiUrl = $this->ebiConfigRepository->findOneBy(['key' => 'System_API_URL']);
        $cronofyCallBackUrl = $systemApiUrl->getValue() . $callBackURL->getValue() . '/' . $personId;
        $param['callback_url'] = $cronofyCallBackUrl;
        $param['filters']['calendar_ids'] = [$calendarId];
        $param['filters']['only_managed'] = false;
        $this->authenticate($accessToken, $refreshToken, $cronofyCalendarObject);
        $channel = $this->cronofyCalendarService->createChannel($param);
        if ($channel['channel']) {
            $channelId = $channel['channel']['channel_id'];
        }
        return $channelId;
    }

    /**
     * Once profile_disconnected notification received from cronofy, disable the existing calendar and send a notification email to that user
     *
     * @param int $personId
     * @param array $pushNotificationResponse
     * @throws SynapseValidationException
     * @return void
     */
    public function updatePushNotificationToUser($personId, $pushNotificationResponse)
    {
        $notificationType = $pushNotificationResponse['notification']['type'];
        $channelId = $pushNotificationResponse['channel']['channel_id'];
        $cronofyCalendarObject = $this->orgCronofyCalendarRepository->findOneBy([
            'person' => $personId,
            'cronofyChannel' => $channelId
        ]);
        if (empty($cronofyCalendarObject)) {
            throw new SynapseValidationException("Invalid channel found in push notification");
        }
        $organizationId = $cronofyCalendarObject->getOrganization()->getId();

        // When there is a change in external calendar (New event created/deleted), then we will receive push notification alert
        // and then we have to reload appointments list page
        if ($notificationType == 'change') {
            $person = $cronofyCalendarObject->getPerson();
            // Send the notification to faculty
            $this->notificationChannelService->sendNotificationToAllRegisteredChannels($person, "faculty_event_change");

            // Send event changed notification to delegated person.
            $delegatedPersons = $this->calendarSharingRepository->getSelectedProxyUsers($person->getId());
            foreach ($delegatedPersons as $delegatedPerson) {
                $delegatedPersonId = $delegatedPerson['delegated_to_person_id'];
                $delegatedPerson = $this->personRepository->find($delegatedPersonId, new SynapseValidationException('Person not found.'));
                $this->notificationChannelService->sendNotificationToAllRegisteredChannels($delegatedPerson, "delegates_event_change");
            }
            $this->updateCronofyHistory($personId, $organizationId, 'Event Changed');
        } else if ($notificationType == 'profile_disconnected') {
            // If the profile is disconnected from external calendar, we have to send the alert to the user and
            // revoke access from cronofy.

            // Send Email Notification to user
            $emailTemplateKey = 'Relink_External_calendar';
            $emailTemplateObject = $this->emailTemplateRepository->findOneBy(['emailKey' => $emailTemplateKey]);
            $emailTemplateLangObject = $this->emailTemplateLangRepository->findOneBy(['emailTemplate' => $emailTemplateObject]);
            if (!$emailTemplateLangObject) {
                throw new SynapseValidationException("Email template not found");
            }
            $providerName = $this->getCalendarProviderName($cronofyCalendarObject->getCronofyProvider());

            $tokenValues['$$first_name$$'] = $cronofyCalendarObject->getPerson()->getFirstname();
            $tokenValues['$$calendar_name$$'] = $providerName;
            $tokenValues['$$calendar_email$$'] = $cronofyCalendarObject->getCronofyProfile();

            $emailBody = $emailTemplateLangObject->getBody();
            // Convert the email string template and token values into a long HTML string
            $emailBody = strtr($emailBody, $tokenValues);
            $emailSubject = $emailTemplateLangObject->getSubject();
            $emailSubject = strtr($emailSubject, $tokenValues);

            $emailContent = [
                'from' => $emailTemplateObject->getFromEmailAddress(),
                'subject' => $emailSubject,
                'bcc' => $emailTemplateObject->getBccRecipientList(),
                'body' => $emailBody,
                'to' => $cronofyCalendarObject->getPerson()->getUsername(),
                'emailKey' => $emailTemplateKey,
                'organizationId' => $organizationId
            ];

            $emailNotificationDto = $this->emailService->sendEmailNotification($emailContent);
            $this->emailService->sendEmail($emailNotificationDto);
            $this->updateCronofyHistory($personId, $organizationId, 'Calendar disconnected from external calendar');
            // Revoke access and update history
            $this->revokeAccess($personId, $organizationId);
        }
    }

    /**
     * Update Cronofy history table when there is any changes happened in users sync settings
     *
     * @param int $personId
     * @param int $organizationId
     * @param string $reason
     * @param null|string $profileName
     * @param null|string $providerName
     * @return void
     */
    public function updateCronofyHistory($personId, $organizationId, $reason, $profileName = NULL, $providerName = NULL)
    {
        $orgCronofyHistory = new OrgCronofyHistory();
        $person = $this->personRepository->find($personId);
        $organization = $this->organizationRepository->find($organizationId);
        $orgCronofyHistory->setOrganization($organization);
        $orgCronofyHistory->setPerson($person);
        $orgCronofyHistory->setReason($reason);
        $orgCronofyHistory->setCronofyProfile($profileName);
        $orgCronofyHistory->setCronofyProvider($providerName);
        $this->orgCronofyHistoryRepository->create($orgCronofyHistory);
        $this->orgCronofyHistoryRepository->flush();
    }

    /**
     * Translate external calendar provider name
     *
     * @param string $providerName - (apple, exchange, live_connect, google)
     * @return mixed|string
     */
    public function getCalendarProviderName($providerName)
    {
        $translatedProviderName = '';
        $calendarProviders = [
            'apple' => SynapseConstant::CRONOFY_CALENDAR_ICLOUD_PROVIDER,
            'exchange' => SynapseConstant::CRONOFY_CALENDAR_MICROSOFT_PROVIDER,
            'live_connect' => SynapseConstant::CRONOFY_CALENDAR_OUTLOOK_PROVIDER,
            'google' => SynapseConstant::CRONOFY_CALENDAR_GOOGLE_PROVIDER
        ];
        if (isset($calendarProviders[$providerName])) {
            $translatedProviderName = $calendarProviders[$providerName];
        }
        return $translatedProviderName;
    }

    /**
     * Sync future appointments to external calendar.
     *
     * @param string $calendarId
     * @param int $organizationId
     * @param int $personId
     * @param DateTime $currentTime
     * @param int $allowedEventsCountPerBatch
     * @param string $organizationTimeZone
     * @return void
     */
    private function syncFutureAppointments($calendarId, $organizationId, $personId, $currentTime, $allowedEventsCountPerBatch, $organizationTimeZone)
    {
        $formattedTime = $currentTime->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
        $futureAppointments = $this->appointmentsRepository->getFutureAppointmentForFaculty($organizationId, $personId, $formattedTime);
        $futureAppointments = array_map(function ($futureAppointment) {
            return [
                'appointments_id' => $futureAppointment['appointment_id']
            ];
        }, $futureAppointments);

        // Process all slots to external calendar
        $this->syncEventsByBatch($futureAppointments, $calendarId, $personId, $organizationId, $allowedEventsCountPerBatch, $formattedTime, 'POST', $organizationTimeZone);
    }

    /**
     * Sync future office hours to external calendar.
     *
     * @param string $calendarId
     * @param int $organizationId
     * @param int $personId
     * @param DateTime $currentDate
     * @param int $allowedEventsCountPerBatch
     * @param string $organizationTimeZone
     * @return void
     */
    private function syncFutureOfficeHours($calendarId, $organizationId, $personId, $currentDate, $allowedEventsCountPerBatch, $organizationTimeZone)
    {
        $formattedTime = $currentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
        $futureOfficeHours = $this->officeHoursRepository->getFutureOfficeHoursForFaculty($organizationId, $personId, $formattedTime);

        // Process all slots to external calendar
        $this->syncEventsByBatch($futureOfficeHours, $calendarId, $personId, $organizationId, $allowedEventsCountPerBatch, $formattedTime, 'POST', $organizationTimeZone);
    }

    /**
     * Delete the future appointments in external calendar which was deleted in mapworks.
     *
     * @param string $calendarId
     * @param int $organizationId
     * @param int $personId
     * @param string $formattedTime
     * @param int $allowedEventsCountPerBatch
     * @return void
     */
    private function syncDeletedMapworkAppointments($calendarId, $organizationId, $personId, $formattedTime, $allowedEventsCountPerBatch)
    {
        $deletedAppointments = $this->appointmentsRepository->getDeletedFutureAppointments($organizationId, $personId, $formattedTime);
        $deletedAppointments = array_map(function ($deletedAppointment) {
            return [
                'appointments_id' => $deletedAppointment['id']
            ];
        }, $deletedAppointments);
        $this->syncEventsByBatch($deletedAppointments, $calendarId, $personId, $organizationId, $allowedEventsCountPerBatch, $formattedTime, 'DELETE');
    }

    /**
     * Delete the future office hours in external calendar which was deleted in mapworks.
     *
     * @param string $calendarId
     * @param int $organizationId
     * @param int $personId
     * @param string $formattedTime
     * @param int $allowedEventsCountPerBatch
     * @return void
     */
    private function syncDeletedMapworkOfficeHours($calendarId, $organizationId, $personId, $formattedTime, $allowedEventsCountPerBatch)
    {
        $deletedOfficeHours = $this->officeHoursRepository->getDeletedFutureOfficeHours($organizationId, $personId, $formattedTime);
        $this->syncEventsByBatch($deletedOfficeHours, $calendarId, $personId, $organizationId, $allowedEventsCountPerBatch, $formattedTime, 'DELETE');
    }

    /**
     * Sync Appointments/Office hours into external calendar.
     *
     * @param OrgCronofyCalendar $cronofyCalendarObject
     * @param int $mapworksEventId
     * @param string $eventType (appointment/office_hour)
     * @param string $actionType (create/update)
     * @param DateTime $currentTime
     * @return bool
     */
    public function syncOneOffEvent($cronofyCalendarObject, $mapworksEventId, $eventType, $actionType, $currentTime, $organizationTimeZone)
    {
        $calendarId = $this->processAuthentication($cronofyCalendarObject);
        $systemURL = $this->ebiConfigService->get('System_URL');
        if ($eventType == 'office_hour') {
            $officeHourObject = $this->officeHoursRepository->find($mapworksEventId);
            if ($officeHourObject) {
                $cronofyParameters = $this->cronofyFormatService->formatMAFOfficeHourDataToCronofy($officeHourObject, $calendarId, 'I', $organizationTimeZone, $systemURL);
                $this->processSyncing($actionType, $officeHourObject, $cronofyParameters, $currentTime);
            }
        }
        if ($eventType == 'appointment') {
            $appointmentObject = $this->appointmentsRepository->find($mapworksEventId);
            if ($actionType == 'create') {
                $officeHourObject = $this->officeHoursRepository->findOneBy(['appointments' => $mapworksEventId]);
                if ($officeHourObject && $officeHourObject->getGoogleAppointmentId()) {
                    $cronofyParameters = ['calendar_id' => $calendarId, 'event_id' => $officeHourObject->getGoogleAppointmentId()];
                    $this->processSyncing('delete', $officeHourObject, $cronofyParameters, $currentTime);
                }
            }
            if ($appointmentObject) {
                $cronofyParameters = $this->cronofyFormatService->formatMAFAppointmentDataToCronofy($appointmentObject, $calendarId, $organizationTimeZone, $systemURL);
                $this->processSyncing($actionType, $appointmentObject, $cronofyParameters, $currentTime);
            }
        }
        return true;
    }


    /**
     * Remove events (one-off events) from external calendar.
     *
     * @param OrgCronofyCalendar $cronofyCalendarObject
     * @param int $mapworksEventId
     * @param string $eventType
     * @param DateTime $currentTime
     * @param null|string $externalCalendarEventId
     * @param null|int $officeHourId
     * @param string $organizationTimeZone
     * @return bool
     */
    public function deleteOneOffEvent($cronofyCalendarObject, $mapworksEventId, $eventType, $currentTime, $externalCalendarEventId = NULL, $officeHourId = NULL, $organizationTimeZone)
    {
        $calendarId = $this->processAuthentication($cronofyCalendarObject);
        $personId = $cronofyCalendarObject->getPerson()->getId();
        $systemURL = $this->ebiConfigService->get('System_URL');
        if ($eventType == 'appointment') {
            if ($externalCalendarEventId) {
                $this->processSyncForDeletedMapworkEvents($calendarId, $externalCalendarEventId);
                $this->appointmentsRepository->updateSyncDetailsToAppointment([$mapworksEventId]);
            }
            if ($officeHourId) {
                $officeHourObject = $this->officeHoursRepository->find($officeHourId);
                if ($officeHourObject) {
                    $cronofyParameters = $this->cronofyFormatService->formatMAFOfficeHourDataToCronofy($officeHourObject, $calendarId, 'I', $organizationTimeZone, $systemURL);
                    $this->processSyncing('create', $officeHourObject, $cronofyParameters, $currentTime);
                }
            }
        } else if ($eventType == 'office_hour') {
            $this->processSyncForDeletedMapworkEvents($calendarId, $externalCalendarEventId);
            $formattedCurrentTime = $currentTime->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
            $this->officeHoursRepository->updateSyncDetailsToOfficeHours([$mapworksEventId], $formattedCurrentTime, $personId);
        }
        return true;
    }

    /**
     * Cronofy Authentication
     *
     * @param OrgCronofyCalendar $cronofyCalendarObject
     * @return string
     */
    private function processAuthentication($cronofyCalendarObject)
    {
        $calendarId = $cronofyCalendarObject->getCronofyCalendar();
        $accessToken = $cronofyCalendarObject->getCronofyCalAccessToken();
        $refreshToken = $cronofyCalendarObject->getCronofyCalRefreshToken();
        $this->authenticate($accessToken, $refreshToken, $cronofyCalendarObject);
        return $calendarId;
    }

    /**
     * Process delete events from external calendar.
     *
     * @param string $calendarId
     * @param string $externalCalendarEventId
     * @return bool
     */
    private function processSyncForDeletedMapworkEvents($calendarId, $externalCalendarEventId)
    {
        $cronofyParameters = ['calendar_id' => $calendarId, 'event_id' => $externalCalendarEventId];
        $this->processCronofyRequest('delete', $cronofyParameters);
        return true;
    }

    /**
     * Process cronofy syncing and update the reference to mapworks.
     *
     * @param string $actionType
     * @param Appointments|OfficeHours $mapworksEventObject
     * @param array $cronofyParameters
     * @param DateTime $currentTime
     * @return bool
     * @throws \Exception
     */
    private function processSyncing($actionType, $mapworksEventObject, $cronofyParameters, $currentTime)
    {
        try {
            $params = $this->processCronofyRequest($actionType, $cronofyParameters);
            $mapworksEventObject->setGoogleAppointmentId($params['event_id']);
            $mapworksEventObject->setLastSynced($currentTime);
            $this->officeHoursRepository->flush();
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
        return true;
    }

    /**
     * Process Cronofy Request
     *
     * @param string $action
     * @param array $cronofyParameters
     * @return array
     */
    private function processCronofyRequest($action, $cronofyParameters)
    {
        if ($action == 'delete') {
            $this->cronofyCalendarService->deleteEvent($cronofyParameters);
            $cronofyParameters['event_id'] = NULL;
        } else {
            $this->cronofyCalendarService->upsertEvent($cronofyParameters);
        }
        return $cronofyParameters;
    }

    /**
     * Sync the deleted office hour slots to external calendar.
     *
     * @param string $calendarId
     * @param int $officeHourSeriesId
     * @param DateTime $currentTime
     * @param int $personId
     * @param int $organizationId
     * @param int $allowedEventsCountPerBatch
     * @return bool
     */
    public function syncDeletedOfficeHoursForSeries($calendarId, $officeHourSeriesId, $currentTime, $personId, $organizationId, $allowedEventsCountPerBatch )
    {
        $formattedTime = $currentTime->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
        $deletedSlotsToBeSynced = $this->officeHoursSeriesRepository->getOfficeHourSlotBySeriesId($officeHourSeriesId, $formattedTime, false);

        // Delete Batch process
        $this->syncEventsByBatch($deletedSlotsToBeSynced, $calendarId, $personId, $organizationId, $allowedEventsCountPerBatch, $formattedTime, 'DELETE');
        return true;
    }

    /**
     * Remove cronofy reference from org_cronofy_calendar.
     *
     * @param int $organizationId
     * @param int $personId
     * @return bool
     */
    private function removeCronofyReference($organizationId, $personId)
    {
        $cronofyCalendarObject = $this->orgCronofyCalendarRepository->findOneBy(['person' => $personId, 'organization' => $organizationId]);
        $cronofyCalendarObject->setCronofyCalAccessToken(NULL);
        $cronofyCalendarObject->setCronofyCalRefreshToken(NULL);
        $cronofyCalendarObject->setCronofyProfile(NULL);
        $cronofyCalendarObject->setCronofyCalendar(NULL);
        $cronofyCalendarObject->setCronofyProvider(NULL);
        $cronofyCalendarObject->setCronofyChannel(NULL);
        $cronofyCalendarObject->setStatus(0);
        $facultyObject = $this->orgPersonFacultyRepository->findOneBy(['person' => $personId, 'organization' => $organizationId]);
        if ($facultyObject) {
            $facultyObject->setPcsToMafIsActive(NULL);
            $facultyObject->setMafToPcsIsActive(NULL);
        }
        return true;
    }

    /**
     * Format and process office hours slots to get sync with external calendar
     *
     * @param array $eventsToBeSynced
     * @param string $calendarId
     * @param int $personId
     * @param int $organizationId
     * @param string $organizationTimeZone
     * @param int $batchSize
     * @param string $formattedTime
     * @param string $method
     * @return bool
     */
    private function syncEventsByBatch($eventsToBeSynced, $calendarId, $personId, $organizationId, $batchSize, $formattedTime, $method = 'POST', $organizationTimeZone = NULL)
    {
        $eventsCount = 0;
        while ($eventsCount < count($eventsToBeSynced)) {
            if ($method == 'POST') {
                // Getting System URL
                $systemURL = $this->ebiConfigService->get('System_URL');
                $batchData = $this->formatBatchDataToUpsert($eventsToBeSynced, $batchSize, $eventsCount, $calendarId, $organizationTimeZone, $systemURL);
            } else {
                $batchData = $this->formatBatchDataToDelete($eventsToBeSynced, $batchSize, $eventsCount, $calendarId);
            }
            $eventIds = $batchData['event_ids'];
            $formattedMAFDataToBeSynced = $batchData['formatted_data'];
            $eventsCount += $batchSize;
            $this->processCronofyEventsByBatch($formattedMAFDataToBeSynced, $eventIds, $personId, $organizationId, $formattedTime, $method);
        }
        return true;
    }

    /**
     * Format MAF data to insert/update in cronofy through batch process.
     *
     * @param array $eventsToBeSynced
     * @param int $batchSize
     * @param int $eventsCount
     * @param string $calendarId
     * @param string $organizationTimeZone
     * @param string $systemURL
     * @return array
     */
    private function formatBatchDataToUpsert($eventsToBeSynced, $batchSize, $eventsCount, $calendarId, $organizationTimeZone, $systemURL)
    {
        $formattedMAFDataToBeSynced['batch'] = [];
        $eventIds['events'] = [];
        $batchData['event_ids'] = [];
        $batchData['formatted_data'] = [];
        for ($batchLoop = 0; $batchLoop < $batchSize; $batchLoop++) {
            if (isset($eventsToBeSynced[$eventsCount + $batchLoop])) {
                $officeHourId = NULL;
                $appointmentId = NULL;
                $cronofyCalendarParameters = [];
                $eventDetail = $eventsToBeSynced[$eventsCount + $batchLoop];
                if (isset($eventDetail['appointments_id'])) {
                    $appointmentId = $eventDetail['appointments_id'];
                    $appointmentObject = $this->appointmentsRepository->find($appointmentId);
                    if ($appointmentObject) {
                        $cronofyCalendarParameters = $this->cronofyFormatService->formatMAFAppointmentDataToCronofy($appointmentObject, $calendarId, $organizationTimeZone, $systemURL);
                        $eventIds['events'][] = $cronofyCalendarParameters['event_id'];
                    }
                } else {
                    $officeHourId = $eventDetail['id'];
                    $officeHourObject = $this->officeHoursRepository->find($officeHourId);
                    if ($officeHourObject) {
                        $cronofyCalendarParameters = $this->cronofyFormatService->formatMAFOfficeHourDataToCronofy($officeHourObject, $calendarId, 'S', $organizationTimeZone, $systemURL);
                        $eventIds['events'][] = $cronofyCalendarParameters['event_id'];
                    }
                }

                $calendarParameters = $this->cronofyFormatService->formatMAFAppointmentAndOfficeHourBatchDataToCronofy($cronofyCalendarParameters, $calendarId, 'POST');
                $formattedMAFDataToBeSynced['batch'][$batchLoop] = $calendarParameters;
            }
        }
        $batchData['event_ids'] = $eventIds;
        $batchData['formatted_data'] = $formattedMAFDataToBeSynced;
        return $batchData;
    }

    /**
     * Format MAF data to deleted from cronofy through batch process.
     *
     * @param array $eventsToBeDeleted
     * @param int $batchSize
     * @param int $eventsCount
     * @param string $calendarId
     * @return array
     */
    private function formatBatchDataToDelete($eventsToBeDeleted, $batchSize, $eventsCount, $calendarId)
    {
        $batchData = [];
        $formattedMAFDataToBeDeleted['batch'] = [];
        $eventIds['events'] = [];
        for ($batchLoop = 0; $batchLoop < $batchSize; $batchLoop++) {
            if (isset($eventsToBeDeleted[$eventsCount + $batchLoop])) {
                $eventDetail = $eventsToBeDeleted[$eventsCount + $batchLoop];
                $officeHourId = isset($eventDetail['id']) ? $eventDetail['id'] : null;
                $appointmentId = isset($eventDetail['appointments_id']) ? $eventDetail['appointments_id'] : null;
                if ($appointmentId) {
                    $eventId['event_id'] = 'A' . $appointmentId;
                    $eventIds['events'][] = $eventId['event_id'];
                } else {
                    $eventId['event_id'] = 'O' . $officeHourId;
                    $eventIds['events'][] = $eventId['event_id'];
                }
                $calendarParameters = $this->cronofyFormatService->formatMAFAppointmentAndOfficeHourBatchDataToCronofy($eventId, $calendarId, 'DELETE');
                $formattedMAFDataToBeDeleted['batch'][$batchLoop] = $calendarParameters;
            }
        }
        $batchData['event_ids'] = $eventIds;
        $batchData['formatted_data'] = $formattedMAFDataToBeDeleted;
        return $batchData;
    }

    /**
     * Sync office hour series slots to external calendar through batch processing.
     *
     * @param array $formattedBatchResultArray
     * @param array $eventIds
     * @param int $personId
     * @param int $organizationId
     * @param string $formattedTime
     * @param string $method
     * @return bool
     */
    private function processCronofyEventsByBatch($formattedBatchResultArray, $eventIds, $personId, $organizationId, $formattedTime, $method)
    {
        $flagForUpdatingGoogleAppointment = ($method == 'DELETE') ? false : true;
        $eventsStatus = [];
        if (isset($formattedBatchResultArray['batch']) && count($formattedBatchResultArray['batch']) > 0) {
            $results = $this->cronofyCalendarService->upsertBatchEvent($formattedBatchResultArray);
            $eventsResult = json_decode($results, true);
            foreach ($eventsResult['batch'] as $key => $status) {
                $eventsStatus[$eventIds['events'][$key]] = $status;
            }
        }

        $failedEventIds = [];
        $passedOfficeHourIds = [];
        $passedAppointmentIds = [];
        foreach ($eventsStatus as $eventId => $eventStatus) {
            if ($eventStatus['status'] != SynapseConstant::CRONOFY_STATUS_ACCEPTED) { // Failed events
                $failedEventIds[] = $eventId;
            } else { // Passed events
                if ($eventId[0] == 'A') {
                    $passedAppointmentIds[] = substr($eventId, 1);;
                } else {
                    $passedOfficeHourIds[] = substr($eventId, 1);;

                }

            }
        }

        // Update passed events status : office_hour table status update
        if (count($passedOfficeHourIds) > 0) {
            $this->officeHoursRepository->updateBatchEventsOfficeHourStatus($passedOfficeHourIds, $formattedTime, $flagForUpdatingGoogleAppointment);
        }

        // Update passed events status : appointments table status update
        if (count($passedAppointmentIds) > 0) {
            $this->appointmentsRepository->updateSyncDetailsToAppointment($passedAppointmentIds, $flagForUpdatingGoogleAppointment, $personId);
        }

        // Send notifications about failed events
        if (count($failedEventIds) > 0) {
            $tokenValues['$$event_id$$'] = $failedEventIds;
            $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, 'event_sync_failed', 'creator', 'calendar_sync', $personId, "A calendar sync error occurred", NULL, NULL, $tokenValues);
        }
        return true;
    }
}