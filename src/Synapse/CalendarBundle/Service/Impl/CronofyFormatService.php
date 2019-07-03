<?php
namespace Synapse\CalendarBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\Appointments;
use Synapse\CoreBundle\Entity\OfficeHours;
use Synapse\CoreBundle\Repository\AppointmentRecepientAndStatusRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\SynapseConstant;

/**
 * @DI\Service("cronofy_format_service")
 */
class CronofyFormatService extends AbstractService
{
    const SERVICE_KEY = 'cronofy_format_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    // Repositories

    /**
     * @var AppointmentRecepientAndStatusRepository
     */
    private $appointmentRecipientAndStatusRepository;

    // Services

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * CronofyFormatService constructor
     *
     *      @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *      })
     *
     * @param RepositoryResolver $repositoryResolver
     * @param Container $container
     * @param Logger $logger
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->logger = $logger;

        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);

        //Repository Initialization
        $this->appointmentRecipientAndStatusRepository = $this->repositoryResolver->getRepository(AppointmentRecepientAndStatusRepository::REPOSITORY_KEY);
    }

    /**
     * Format Mapwork data to pass Google API in order to create/update office hour
     *
     * @param Appointments $appointments
     * @param string $calendarId
     * @param string $organizationTimeZone
     * @param string $systemUrl
     * @return array
     */
    public function formatMAFAppointmentDataToCronofy($appointments, $calendarId, $organizationTimeZone, $systemUrl)
    {
        $appointmentId = $appointments->getId();

        $externalCalendarEventId = $appointments->getGoogleAppointmentId();
        if (empty($externalCalendarEventId)) {
            $externalCalendarEventId = 'A' . $appointmentId;
        }

        // Attendees detail should be included in the description
        $attendees = $this->appointmentRecipientAndStatusRepository->findBy(['appointments' => $appointmentId]);
        $bodyContent = "";
        $summary = "";
        if (!empty($attendees)) {
            $subject = $attendees[0]->getPersonIdStudent()->getFirstname() . " " . $attendees[0]->getPersonIdStudent()->getLastname();
            $totalAttendees = count($attendees);
            if ($totalAttendees > 1) {
                $otherAttendeesCount = ($totalAttendees - 1);
                if ($otherAttendeesCount > 1) {
                    $otherText = " others";
                } else {
                    $otherText = " other";
                }
                $bodyContent = $subject . " and " . $otherAttendeesCount . $otherText;
                $subject .= " + " . $otherAttendeesCount . $otherText;
            } else {
                $bodyContent = $subject;
            }
            $summary = "Mapworks appt: {$subject}";
        }
        $viewLinkInPCS = $this->generateRedirectionURL($systemUrl, $appointmentId, 'appointment');
        $description = "You have a Mapworks appointment with {$bodyContent}. To modify it or see details, please go to $viewLinkInPCS";
        $appointments->getStartDateTime()->setTimezone(new \DateTimeZone('UTC'));
        $appointments->getEndDateTime()->setTimezone(new \DateTimeZone('UTC'));
        $startDate = $appointments->getStartDateTime()->format(SynapseConstant::DATE_TIME_ZONE_FORMAT);
        $endDate = $appointments->getEndDateTime()->format(SynapseConstant::DATE_TIME_ZONE_FORMAT);
        $calendarParameters = array(
            'calendar_id' => $calendarId,
            'event_id' => $externalCalendarEventId,
            'summary' => $summary,
            'start' => array('time' => $startDate, 'tzid' => $organizationTimeZone),
            'end' => array('time' => $endDate, 'tzid' => $organizationTimeZone),
            'description' => $description,
            'location' => array('description' => $appointments->getLocation()),
            'transparency' => 'opaque'
        );
        return $calendarParameters;
    }

    /**
     * Generate Redirection URL
     *
     * @param string $systemUrl
     * @param int $appointmentId
     * @param string $type
     * @param string $slotType - For office hour this will be 'I' and for series this will be 'S'
     * @return string
     */
    public function generateRedirectionURL($systemUrl, $appointmentId, $type, $slotType = NULL)
    {
        $redirectionURL = $systemUrl . '#/schedule/' . $type . '/' . $appointmentId;
        if (!empty($slotType)) {
            $redirectionURL .= '/' . $slotType;
        }
        return $redirectionURL;
    }

    /**
     * Format Mapwork data to pass Google API in order to create/update an appointment
     *
     * @param OfficeHours $officeHours
     * @param string $calendarId
     * @param string $type I for individual slot and S for series
     * @param string $organizationTimeZone
     * @param string $systemUrl
     * @return array
     */
    public function formatMAFOfficeHourDataToCronofy($officeHours, $calendarId, $type, $organizationTimeZone, $systemUrl)
    {
        $id = $officeHours->getId();
        $externalCalendarEventId = $officeHours->getGoogleAppointmentId();
        if (empty($externalCalendarEventId)) {
            $externalCalendarEventId = 'O' . $id;
        }
        $viewLinkInPCS = $this->generateRedirectionURL($systemUrl, $id, 'officehour', $type);
        $calendarParameters = array(
            'calendar_id' => $calendarId,
            'event_id' => $externalCalendarEventId,
            'summary' => "Mapworks Office Hours",
            'start' => array('time' => $officeHours->getSlotStart()->format(SynapseConstant::DATE_TIME_ZONE_FORMAT), 'tzid' => $organizationTimeZone),
            'end' => array('time' => $officeHours->getSlotEnd()->format(SynapseConstant::DATE_TIME_ZONE_FORMAT), 'tzid' => $organizationTimeZone),
            'description' => "To modify this office hour or see details, please go to {$viewLinkInPCS} ",
            'location' => array('description' => $officeHours->getLocation()),
            'transparency' => 'transparent'
        );
        return $calendarParameters;
    }

    /**
     * Format Mapwork data to pass cronofy in order to create an appointment or office hour
     *
     * @param array $cronofyCalendarParameters
     * @param int $calendarId
     * @param string $method
     * @return array
     */
    public function formatMAFAppointmentAndOfficeHourBatchDataToCronofy($cronofyCalendarParameters, $calendarId, $method = 'POST')
    {
        $cronofyEventsArray = [];
        $cronofyEventsArray['method'] = $method;
        $cronofyEventsArray['relative_url'] = '/v1/calendars/' . $calendarId . '/events';
        $cronofyEventsArray['data'] = $cronofyCalendarParameters;

        return $cronofyEventsArray;
    }
}