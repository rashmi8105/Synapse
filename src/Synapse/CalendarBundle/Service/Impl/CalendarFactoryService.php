<?php
namespace Synapse\CalendarBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CalendarBundle\Repository\OrgCronofyCalendarRepository;
use Synapse\CoreBundle\Entity\OrgPersonFaculty;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\AbstractService;


/**
 * @DI\Service("calendar_factory_service")
 */
class CalendarFactoryService extends AbstractService
{
    const SERVICE_KEY = 'calendar_factory_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Logger
     */
    protected $logger;

    // Services

    /**
     * @var CalendarIntegrationService
     */
    private $calendarIntegrationService;

    /**
     * @var CalendarWrapperService
     */
    private $calendarWrapperService;

    /**
     * @var CronofyWrapperService
     */
    private $cronofyWrapperService;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    // Repositories

    /**
     * @var OrgCronofyCalendarRepository
     */
    private $orgCronofyCalendarRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;


    /**
     * CalendarFactoryService constructor.
     *
     * @param RepositoryResolver $repositoryResolver
     * @param Container $container
     * @param Logger $logger
     *
     *      @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "container" = @DI\Inject("service_container"),
     *            "logger" = @DI\Inject("logger"),
     *
     *      })
     */
    public function __construct($repositoryResolver, $container, $logger)
    {
        parent::__construct($repositoryResolver, $logger);

        // Scaffolding
        $this->container = $container;
        $this->logger = $logger;

        // Services
        $this->calendarWrapperService = $this->container->get(CalendarWrapperService::SERVICE_KEY);
        $this->cronofyWrapperService = $this->container->get(CronofyWrapperService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);

        // Repositories
        $this->orgCronofyCalendarRepository = $this->repositoryResolver->getRepository(OrgCronofyCalendarRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
    }

    /**
     * This function will identify which calendar tool is enabled by the organization, based on the calendar type control will be re directed to
     * appropriate services
     *
     * @param array $appointmentData
     * @param string $roleType
     */
    public function syncPCS($appointmentData, $roleType = NULL)
    {
        $calendarType = $this->ebiConfigService->get('calendar_type');
        $personId = $appointmentData['personId'];
        $organizationId = $appointmentData['orgId'];
        switch ($calendarType) {
            case 'cronofy':
                $cronofyCalendarObject = $this->orgCronofyCalendarRepository->findOneBy(['person' => $personId, 'organization' => $organizationId]);
                if ($cronofyCalendarObject->getStatus()) {
                    $this->cronofyWrapperService->syncPCS($appointmentData, $cronofyCalendarObject, $roleType);
                }
                break;
            case 'google':
                if ($appointmentData['calendarSettings']['google_sync_status']) {
                    $this->calendarWrapperService->syncPCS($appointmentData);
                }
                break;
            default:
                break;
        }
    }

    /**
     * Once the user has granted the access then it should update the details back to mapworks
     *
     * @param string $syncStatus
     * @param OrgPersonFaculty $personObject
     */
    public function updateSyncStatus($syncStatus, $personObject = NULL)
    {
        $calendarType = $this->ebiConfigService->get('calendar_type');
        switch ($calendarType) {
            case 'cronofy':
                $cronofyCalendarObject = $this->orgCronofyCalendarRepository->findOneBy(['person' => $personObject->getPerson()->getId(), 'organization' => $personObject->getOrganization()->getId()]);
                if (!$syncStatus && $cronofyCalendarObject) {
                    $cronofyCalendarObject->setStatus(0);
                    $cronofyCalendarObject->setCronofyProfile(NULL);
                    $cronofyCalendarObject->setCronofyProvider(NULL);
                }
                break;
            case 'google':
                $personObject->setGoogleSyncStatus($syncStatus);
                break;
            default:
                break;
        }
    }

    /**
     * Get busy events from Google/Cronofy
     *
     * @param int $personId
     * @param int $organizationId
     * @param array $pcsCalendarIds
     * @param string $fromDate
     * @param string $toDate
     * @param boolean|null $googleSyncStatus
     * @return array
     */
    public function getBusyEvents($personId, $organizationId, $pcsCalendarIds, $fromDate, $toDate, $googleSyncStatus = NULL)
    {
        $calendarType = $this->ebiConfigService->get('calendar_type');
        $events = [];
        switch ($calendarType) {
            case 'cronofy':
                $cronofyCalendarObj = $this->orgCronofyCalendarRepository->findOneBy(['person' => $personId, 'organization' => $organizationId]);
                if ($cronofyCalendarObj->getStatus()) {
                    $events = $this->cronofyWrapperService->getBusyEvents($personId, $organizationId, $fromDate, $toDate);
                }
                break;
            case 'google':
                if ($googleSyncStatus) {
                    $events = $this->calendarWrapperService->listBusyTentativeEventsFromGoogle($personId, $organizationId, $pcsCalendarIds, $fromDate, $toDate);
                }
                break;
        }
        return $events;
    }


    /**
     * Gets the count of users per organization that sync their calendars
     *
     * @param int $organizationId
     * @return int
     */
    public function getCountOfCalendarSyncUsers($organizationId)
    {
        $calendarType = $this->ebiConfigService->get('calendar_type');
        switch ($calendarType) {
            case 'cronofy':
                $listOfCronofyCalendarEnabledUser = $this->orgCronofyCalendarRepository->getListOfCronofyCalendarSyncUsers($organizationId);
                $calendarEnabledUserCount = count($listOfCronofyCalendarEnabledUser);
                break;
            case 'google':
                $listOfGoogleCalendarEnabledUser = $this->orgPersonFacultyRepository->getListOfGoogleCalendarSyncUsers($organizationId);
                $calendarEnabledUserCount = count($listOfGoogleCalendarEnabledUser);
                break;
            default:
                $calendarEnabledUserCount = 0;
                break;
        }
        return $calendarEnabledUserCount;
    }


    /**
     * This function will identify which calendar tool is enabled by the organization, based on the calendar type control will be remove events
     *
     * @param int $personId
     * @param int $organizationId
     * @param boolean $removeMafToPcs
     * @param boolean $syncStatus
     * @param string $type
     * @param boolean|null $pcsRemove
     */
    public function removeEvent($personId, $organizationId, $removeMafToPcs, $syncStatus, $type, $pcsRemove)
    {
        $calendarType = $this->ebiConfigService->get('calendar_type');
        switch ($calendarType) {
            case 'cronofy':
                $this->cronofyWrapperService->removeEvent($organizationId, $removeMafToPcs, $pcsRemove, $type, null, $personId, $syncStatus);
                break;
            case 'google':
                $this->calendarWrapperService->removeEvent($personId, $organizationId, $removeMafToPcs, $syncStatus);
                break;
            default:
                break;
        }
    }


    /**
     * Initial Sync
     *
     * @param int $personId
     * @param int $organizationId
     * @param int $loggedInPersonId
     */
    public function initialSync($personId, $organizationId, $loggedInPersonId = null)
    {
        $calendarType = $this->ebiConfigService->get('calendar_type');
        switch ($calendarType) {
            case 'cronofy':
                $this->cronofyWrapperService->initialSyncToPcs($personId, $organizationId, $loggedInPersonId);
                break;
            case 'google':
                $this->calendarWrapperService->initialSyncToPcs($personId, $organizationId);
                break;
            default:
                break;
        }
    }


    /**
     * For Cronofy: Remove Event with disable calendar
     * For Google: Disconnect calendar sync for faculty and send notification
     *
     * @param int $organizationId
     * @param string $event
     * @param string $type
     * @param boolean $pcsRemove
     */
    public function removeEventWithDisableCalendar($organizationId, $event, $type, $pcsRemove)
    {
        $this->calendarIntegrationService = $this->container->get(CalendarIntegrationService::SERVICE_KEY);
        $calendarType = $this->ebiConfigService->get('calendar_type');
        switch ($calendarType) {
            case 'cronofy':
                $this->cronofyWrapperService->removeEvent($organizationId, true, $pcsRemove, $type, $event);
                break;
            case 'google':
                $this->calendarIntegrationService->sendSyncNotificationToFaculties($organizationId, $event, $calendarType);
                break;
            default:
                break;
        }
    }
}