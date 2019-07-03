<?php
namespace Synapse\CalendarBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Constants\CalendarConstant;

/**
 * @DI\Service("google_calendar_service")
 */
class GoogleCalendarService extends AbstractService
{
    const SERVICE_KEY = 'google_calendar_service';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var $clientId
     */
    private $clientId;

    /**
     * @var $clientSecret
     */
    private $clientSecret;

    /**
     * @var \Google_Client
     */
    private $client;

    /**
     *
     * @param RepositoryResolver $repositoryResolver
     * @param Logger $logger
     * @param Container $container
     *
     *      @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "container" = @DI\Inject("service_container"),
     *            "logger" = @DI\Inject("logger"),
     *
     *      })
     * @throws \Exception
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->logger = $logger;
        $this->ebiConfigService = $this->container->get('ebi_config_service');
    }

    /**
     * Authenticate Google Service
     * @param object $person
     * @return int
     * @throws \Exception
     */
    public function authenticateGoogleAPI($person)
    {
        $googleAccessToken = $person->getOauthCalAccessToken();
        $this->clientId = $redirectURL = $this->ebiConfigService->get('Google_Client_Id');
        $this->clientSecret = $this->ebiConfigService->get('Google_Client_Secret');
        if (empty($googleAccessToken)) {
            $this->logger->error("Google Access Token not found");
            throw new \Exception("Google OAuth is not enabled.");
        }
        try {
            $this->client = new \Google_Client();
            $this->client->setClientId($this->clientId);
            $this->client->setClientSecret($this->clientSecret);
            $this->client->setApprovalPrompt('force');
            $this->client->addScope(CalendarConstant::GOOGLE_SCOPE_CALENDAR);
            $this->client->addScope(CalendarConstant::GOOGLE_SCOPE_EMAIL);
            $this->client->setAccessToken($googleAccessToken);
            if ($this->client->isAccessTokenExpired()) {
                $googleRefreshToken = $person->getOauthCalRefreshToken();
                $this->client->refreshToken($googleRefreshToken);
                $googleAccessToken = $this->client->getAccessToken();
                $this->client->setAccessToken($googleAccessToken);
            }
        } catch (\Exception $e) {
            $this->logger->error("Failed to connect Google API" . $e->getMessage());
            return $e->getCode();
        }
    }

    /**
     * Create Events in Google
     *
     * @param array $calenderEvent
     * @return \Google_Service_Calendar_Event
     */
    public function createAppointment($calenderEvent)
    {
        $this->isAuthenticated();
        try {
            $service = new \Google_Service_Calendar($this->client);
            $event = new \Google_Service_Calendar_Event($calenderEvent);
            $event = $service->events->insert('primary', $event);
            return $event;
        } catch (\Exception $e) {
            $this->logger->error("Failed to connect Google API" . $e->getMessage());
            return $e->getCode();
        }
    }

    /**
     * Update events in Google
     * @param array $calenderEvent
     * @param string $eventId
     * @return \Google_Service_Calendar_Event
     */
    public function updateAppointment($calenderEvent, $eventId)
    {
        $this->isAuthenticated();
        try {
            $service = new \Google_Service_Calendar($this->client);
            $event = new \Google_Service_Calendar_Event($calenderEvent);
            $event = $service->events->update('primary', $eventId, $event);
            return $event;
        } catch (\Exception $e) {
            $this->logger->error("Failed to connect Google API" . $e->getMessage());
            return $e->getCode();
        }
    }

    /**
     * Cancel events in Google
     *
     * @param string $eventId
     * @return \Google_Http_Request
     */
    public function cancelAppointment($eventId)
    {
        $this->isAuthenticated();
        try {
            $service = new \Google_Service_Calendar($this->client);
            $event = $service->events->delete('primary', $eventId);
            return $event;
        } catch (\Exception $e) {
            $this->logger->error("Failed to connect Google API" . $e->getMessage());
            return $e->getCode();
        }
    }

    /**
     * Validate Google service for authentication
     */
    private function isAuthenticated()
    {
        if (empty($this->client)) {
            $this->logger->error("Failed to generate Authentication");
            throw new ValidationException([
                'Failed to generate Authentication'
            ], 'Failed to generate Authentication', 'authentication_failed');
        }
    }


    /**
     * This function is used to get the details of a particular events from the recurrence slot.
     *
     * @param string $eventId
     * @return \Google_Service_Calendar_Events
     */

    public function getInstance($eventId)
    {
        $this->isAuthenticated();
        try {
            $service = new \Google_Service_Calendar($this->client);
            $events = $service->events->instances("primary", $eventId);
            return $events;
        } catch (\Exception $e) {
            $this->logger->error("Failed to connect Google API" . $e->getMessage());
            return $e->getCode();
        }
    }

    /**
     * Get the list of Google events between the time period
     *
     * @param string $email
     * @param string $fromDate
     * @param string $toDate
     * @return array
     */
    public function listEvents($email, $fromDate, $toDate)
    {
        $this->isAuthenticated();
        try {
            $service = new \Google_Service_Calendar($this->client);
            $optParams = array('showDeleted' => false, 'timeMin' => $fromDate, 'timeMax' => $toDate, 'singleEvents' => true, 'orderBy' => 'startTime', 'timeZone' => 'UTC');
            $events = $service->events->listEvents($email, $optParams);
            $eventDetails = array();
            while (true) {
                foreach ($events->getItems() as $event) {
                    $eventDetails[$email][] = $event;
                }
                $pageToken = $events->getNextPageToken();
                if ($pageToken) {
                    $optParams['pageToken'] = $pageToken;
                    $events = $service->events->listEvents($email, $optParams);
                } else {
                    break;
                }
            }
            return $eventDetails;
        } catch (\Exception $e) {
            $this->logger->error("Failed to connect Google API" . $e->getMessage());
            return $e->getCode();
        }
    }

    /**
     * Function to return users calendar Timezone
     *
     * @return \Google_Service_Calendar_CalendarListEntry
     */
    public function getCalendarTimeZone()
    {
        $this->isAuthenticated();
        try {
            $service = new \Google_Service_Calendar($this->client);
            $timeZone = $service->calendarList->get('primary')->timeZone;
            return $timeZone;
        } catch (\Exception $e) {
            $this->logger->error("Failed to connect Google API" . $e->getMessage());
            return $e->getCode();
        }
    }
}