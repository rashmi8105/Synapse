<?php
namespace Synapse\CalendarBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\Appointments;
use Synapse\CoreBundle\Entity\OfficeHours;
use Synapse\CoreBundle\Entity\OfficeHoursSeries;
use Synapse\CoreBundle\Entity\OrgPersonFaculty;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\AppointmentRecepientAndStatusRepository;
use Synapse\CoreBundle\Repository\OfficeHoursRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\TokenService;
use Synapse\CoreBundle\Util\UtilServiceHelper;
use Synapse\PersonBundle\Repository\ContactInfoRepository;
use Synapse\RestBundle\Entity\AttendeesDto;

/**
 * @DI\Service("google_format_service")
 */
class GoogleFormatService extends AbstractService
{
    const SERVICE_KEY = 'google_format_service';

    /**
     * @var Container
     */
    private $container;
    /**
     * @var AcademicYearService
     */
    private $academicYearService;
    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;
    /**
     * @var TokenService
     */
    private $tokenService;
    /**
     * @var UtilServiceHelper
     */
    private $utilServiceHelper;
    /**
     * @var AppointmentRecepientAndStatusRepository
     */
    private $appointmentRecipientAndStatusRepository;
    /**
     * @var ContactInfoRepository
     */
    private $contactInfoRepository;
    /**
     * @var OfficeHoursRepository
     */
    private $officeHoursRepository;
    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     *
     * @param RepositoryResolver $repositoryResolver
     * @param Container $container
     * @param Logger $logger
     *
     *      @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *
     *      })
     * @throws \Exception
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->logger = $logger;

        //Service Initialization
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->tokenService = $this->container->get(TokenService::SERVICE_KEY);
        $this->utilServiceHelper = $this->container->get(UtilServiceHelper::SERVICE_KEY);

        //Repository Initialization
        $this->appointmentRecipientAndStatusRepository = $this->repositoryResolver->getRepository(AppointmentRecepientAndStatusRepository::REPOSITORY_KEY);
        $this->contactInfoRepository = $this->repositoryResolver->getRepository(ContactInfoRepository::REPOSITORY_KEY);
        $this->officeHoursRepository = $this->repositoryResolver->getRepository(OfficeHoursRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
    }

    /**
     * Format Mapwork data to pass Google API in order to create/update an appointment
     *
     * @param array $pcsSyncData
     * @return array
     */
    public function formatMAFAppointmentDataToGoogle($pcsSyncData)
    {
        $events = [];
        $personId = $pcsSyncData['personId'];
        $id = $pcsSyncData['mafEventId'];
        $creatorEmailAddress = $pcsSyncData['primaryAttendee']['email'];
        $creator['email'] = $creatorEmailAddress;
        $reminders = array(
            'useDefault' => FALSE,
            'overrides' => array(
                array('method' => 'email', 'minutes' => 24 * 60),
                array('method' => 'popup', 'minutes' => 10),
            ),
        );
        $attendees = $pcsSyncData['attendees'];
        $attendeeList[0]['email'] = $creatorEmailAddress;
        $attendeeList[0]['displayName'] = $pcsSyncData['primaryAttendee']['displayName'];
        $attendeeList[0]['responseStatus'] = 'accepted';
        if (!empty($attendees)) {
            foreach ($attendees as $attendee) {
                $person = $this->personRepository->find($attendee->getStudentId());
                $contactInfo = $this->contactInfoRepository->getCoalescedContactInfo($person->getContacts());
                if (!empty($contactInfo->getPrimaryEmail())) {
                    $attendeeDetails['email'] = $contactInfo->getPrimaryEmail();
                    $attendeeDetails['displayName'] = $person->getFirstname() . ' ' . $person->getLastname();
                    $attendeeDetails['responseStatus'] = 'accepted';
                    $attendeeList[] = $attendeeDetails;
                }
            }
        }
        $events['status'] = 'confirmed';
        $events['location'] = $pcsSyncData['mafInputDto']['location'];
        $viewLinkInPCS = $this->generateRedirectionURL($personId, $id, 'appointment');
        $events['description'] = "<a href={$viewLinkInPCS}>View this appointment in Mapworks</a>";
        $startDate['dateTime'] = $pcsSyncData['mafInputDto']['startTime']->format('Y-m-d\TH:i:s') . '+00:00';
        $startDate['timeZone'] = 'UTC';
        $events['start'] = $startDate;
        $endDate['dateTime'] = $pcsSyncData['mafInputDto']['endTime']->format('Y-m-d\TH:i:s') . '+00:00';
        $endDate['timeZone'] = 'UTC';
        $events['end'] = $endDate;
        $events['attendees'] = $attendeeList;
        $events['summary'] = "Mapworks appointment: " . $pcsSyncData['mafInputDto']['subject'];
        $events['reminders'] = $reminders;
        $busy['start'] = $startDate;
        $busy['end'] = $endDate;
        $busy['free_busy_status'] = 'busy';
        $events['free_busy'] = $busy;
        $events['transparency'] = 'opaque';
        return $events;
    }

    /**
     * Format Mapwork Office hour data to pass Google API
     *
     * @param array $pcsSyncData
     * @return array
     */
    public function formatMAFOfficeHourDataToGoogle($pcsSyncData)
    {
        $events = [];
        $id = $pcsSyncData['mafEventId'];
        $personId = $pcsSyncData['personId'];
        $creatorEmailAddress = $pcsSyncData['primaryAttendee']['email'];
        $creator['email'] = $creatorEmailAddress;
        $reminders = array(
            'useDefault' => FALSE,
            'overrides' => array(
                array('method' => 'email', 'minutes' => 24 * 60),
                array('method' => 'popup', 'minutes' => 10),
            ),
        );
        $attendeeList[0]['email'] = $creatorEmailAddress;
        $attendeeList[0]['displayName'] = $pcsSyncData['primaryAttendee']['displayName'];
        $attendeeList[0]['responseStatus'] = 'tentative';
        $events['location'] = $pcsSyncData['mafInputDto']['location'];
        $viewLinkInPCS = $this->generateRedirectionURL($personId, $id, 'officehour', 'I');        
        $events['description'] = "<a href={$viewLinkInPCS}>View this appointment in Mapworks</a>";
        $startDate['dateTime'] = $pcsSyncData['mafInputDto']['startTime']->format('Y-m-d\TH:i:s') . '+00:00';
        $startDate['timeZone'] = 'UTC';
        $events['start'] = $startDate;
        $endDate['dateTime'] = $pcsSyncData['mafInputDto']['endTime']->format('Y-m-d\TH:i:s') . '+00:00';
        $endDate['timeZone'] = 'UTC';
        $events['end'] = $endDate;
        $events['attendees'] = $attendeeList;
        $events['summary'] = "Mapworks Office Hours";
        $events['reminders'] = $reminders;
        $events['transparency'] = 'transparent';
        return $events;
    }

    /**
     * Format Mapwork office hour series data to Google API
     *
     * @param array $pcsSyncData
     * @return array
     */
    public function formatMAFOfficeHourSeriesDataToGoogle($pcsSyncData)
    {
        $personId = $pcsSyncData['personId'];
        $officeHourSeries = $pcsSyncData['mafInputObject'];
        $creatorEmailAddress = $pcsSyncData['primaryAttendee']['email'];
        $creator['email'] = $creatorEmailAddress;
        $reminders = array(
            'useDefault' => FALSE,
            'overrides' => array(
                array('method' => 'email', 'minutes' => 24 * 60),
                array('method' => 'popup', 'minutes' => 10),
            ),
        );
        $attendeeList[0]['email'] = $creatorEmailAddress;
        $attendeeList[0]['displayName'] = $pcsSyncData['primaryAttendee']['displayName'];
        $attendeeList[0]['responseStatus'] = 'tentative';
        $events['location'] = $officeHourSeries->getLocation();
        $events['attendees'] = $attendeeList;
        $events['summary'] = "Mapworks Office Hours";
        $events['reminders'] = $reminders;
        $events['transparency'] = 'transparent';
        $officeHourSlots = $this->officeHoursRepository->findOneBy(['officeHoursSeries' => $officeHourSeries->getId()]);
        $startDateTime = $officeHourSlots->getSlotStart();
        $slotId = $officeHourSlots->getId();
        $viewLinkInPCS = $this->generateRedirectionURL($personId, $slotId, 'officehour', 'S');
        $events['recurrence'] = $this->recurrenceByMultipleSlots($officeHourSeries);

        $startDate['dateTime'] = $startDateTime->format('Y-m-d\TH:i:s') . '+00:00';
        $startDate['timeZone'] = 'UTC';

        $events['description'] = "<a href={$viewLinkInPCS}>View this appointment in Mapworks</a>";
        $meetingLength = $officeHourSeries->getMeetingLength();
        $firstSlotEndTime = clone $startDateTime;
        $firstSlotEndTime->add(new \DateInterval('P0DT0H' . $meetingLength . 'M0S'));
        $endDate['dateTime'] = $firstSlotEndTime->format('Y-m-d\TH:i:s') . '+00:00';
        $endDate['timeZone'] = 'UTC';
        $events['start'] = $startDate;
        $events['end'] = $endDate;
        return $events;
    }

    /**
     * Create RRULE for Google calendar (iCalRule), this will trigger recurrence events in Google
     *
     * @param OfficeHoursSeries $officeHourSeries
     * @return array
     */
    private function recurrenceByMultipleSlots($officeHourSeries)
    {
        $rRule = '';
        $seriesStartDate = $officeHourSeries->getSlotStart();
        $seriesEndDate = $officeHourSeries->getSlotEnd();
        $slotEndDate = $seriesStartDate->format('Y-m-d') . ' ' . $seriesEndDate->format('H:i:s');
        $seriesEndDateFormat = $seriesEndDate->format('Ymd\THis\Z');
        $meetingLength = $officeHourSeries->getMeetingLength();
        $repeatPattern = array("D" => "DAILY", "W" => "WEEKLY", "M" => "MONTHLY", "Y" => "YEARLY", 'N' => 'DAILY', 'MWF' => 'WEEKLY', 'TT' => 'WEEKLY');
        $rrRepeatPattern = $repeatPattern[$officeHourSeries->getRepeatPattern()];
        $interval = $officeHourSeries->getRepeatEvery();
        $repeatDayPattern = NULL;

        // For Weekly Series, Start Day should be Sunday as per Mapworks
        $weekStartDay = '';
        if ($officeHourSeries->getRepeatPattern() == 'W') {
            $weekStartDay = ';WKST=SU';
        }
        if (!empty($interval)) {
            $repeatDayPattern = ';INTERVAL=' . $interval;
        }
        $repeatDayPattern .= $this->getRruleByDay($officeHourSeries);
        $slotEndDate = new \DateTime($slotEndDate);

        if ($officeHourSeries->getRepetitionRange() == 'E' && $officeHourSeries->getRepetitionOccurrence()) {
            $until = ";COUNT=" . $officeHourSeries->getRepetitionOccurrence();
        } else {
            $until = ';UNTIL=' . $seriesEndDateFormat;
        }
        while ($seriesStartDate < $slotEndDate) {
            $endDateTime = clone $seriesStartDate;
            $endDateTime->add(new \DateInterval('P0DT0H' . $meetingLength . 'M0S'));
            if ($slotEndDate < $endDateTime) {
                break;
            }
            $rRule[] = 'RRULE:FREQ=' . $rrRepeatPattern . ';BYHOUR=' . $seriesStartDate->format('H') . ';BYMINUTE=' . $seriesStartDate->format('i') . $weekStartDay . $until . $repeatDayPattern . ";";
            $seriesStartDate = clone $endDateTime;
            unset($endDateTime);
        }
        return $rRule;
    }

    /**
     * Function to get the days for the recurrence rule
     *
     * @param OfficeHoursSeries $recurrence
     * @return string
     */
    private function getRruleByDay($recurrence)
    {
        $byDay = NULL;
        switch ($recurrence->getRepeatPattern()) {
            case 'MWF':
                $byDay = ';BYDAY=MO,WE,FR';
                break;
            case 'TT':
                $byDay = ';BYDAY=TU,TH';
                break;
            case 'W':
                $seriesRepeatDays = $recurrence->getDays();
                $byDay = $this->findDays($seriesRepeatDays, 0);
                break;
            case 'D':
                if ($recurrence->getIncludeSatSun()) {
                    $byDay = ';BYDAY=SU,MO,TU,WE,TH,FR,SA';
                } else {
                    $byDay = ';BYDAY=MO,TU,WE,TH,FR';
                }
                break;
            case 'M':
                $seriesRepeatDays = $recurrence->getDays();
                $repeatMonthOn = $recurrence->getRepeatMonthlyOn();
                if ($repeatMonthOn == 0) {
                    $repeatMonthOn = 1;
                }
                $byDay = $this->findDays($seriesRepeatDays, $repeatMonthOn);
                break;
        }
        return $byDay;
    }

    /**
     * Find the calendar days for a monthly series
     *
     * @param string $seriesRepeatDays
     * @param string $repeatBy
     * @param array $daysArray
     * @return string
     */
    private function findDays($seriesRepeatDays, $repeatBy = NULL, $daysArray = NULL)
    {
        $byDay = NULL;
        if (strpos($seriesRepeatDays, "1") !== false) {
            $chunkDays = chunk_split($seriesRepeatDays, 1, ",");
            $days = array("SU", "MO", "TU", "WE", "TH", "FR", "SA");
            $splitDays = explode(",", $chunkDays);
            for ($i = 0; $i < count($splitDays); $i++) {
                if ($splitDays[$i] == 1) {
                    if ($repeatBy == NULL) {
                        $byDay[] = $days[$i];
                    } else {
                        $byDay[] = $repeatBy . $days[$i];
                    }
                }
            }
            if ($daysArray) {
                return $byDay;
            }
            if (count($byDay) > 0) {
                $byDay = implode(",", $byDay);
                $byDay = ";BYDAY=" . $byDay;
            }
        }
        return $byDay;
    }

    /**
     * Get sync data from Google
     *
     * @param OfficeHoursSeries | Appointments $appointment
     * @param OrgPersonFaculty $personData
     * @param string $type
     * @return array
     */
    public function getGoogleSyncData($appointment, $personData, $type)
    {
        $attendeeList = [];
        $appointmentId = $appointment->getId();
        $pcsSyncData['mafEventId'] = $appointmentId;
        $personId = $personData->getPerson()->getId();
        $orgId = $personData->getPerson()->getOrganization()->getId();
        $personName = $personData->getPerson()->getFirstname() . ' ' . $personData->getPerson()->getLastname();
        $pcsSyncData['primaryAttendee']['email'] = $personData->getGoogleEmailId();
        $pcsSyncData['primaryAttendee']['displayName'] = $personName;
        $pcsSyncData['mafInputDto']['location'] = $appointment->getLocation();
        if ($type == 'officehourseries') {
            $pcsSyncData['mafInputObject'] = $appointment;
        } else if ($type == 'officehour') {
            $pcsSyncData['mafInputDto']['startTime'] = $appointment->getSlotStart();
            $pcsSyncData['mafInputDto']['endTime'] = $appointment->getSlotEnd();
        } else {
            $pcsSyncData['mafInputDto']['startTime'] = $appointment->getStartDateTime();
            $pcsSyncData['mafInputDto']['endTime'] = $appointment->getEndDateTime();
            $pcsSyncData['mafInputDto']['subject'] = $appointment->getTitle();

            $orgAcademicYearId = null;
            $currentAcademicYear = $this->academicYearService->findCurrentAcademicYearForOrganization($orgId);
            if (isset($currentAcademicYear['org_academic_year_id'])) {
                $orgAcademicYearId = $currentAcademicYear['org_academic_year_id'];
            }
            $personId = $this->appointmentRecipientAndStatusRepository->getAppointmentFaculty($appointmentId);
            $attendees = $this->appointmentRecipientAndStatusRepository->getParticipantAttendeesForAppointment($orgId, $personId, $appointmentId, $orgAcademicYearId);
            if (!empty($attendees)) {
                foreach ($attendees as $attendee) {
                    $attendeeDto = new AttendeesDto();
                    $attendeeDto->setStudentId($attendee['student_id']);
                    $attendeeList[] = $attendeeDto;
                }
            }
            $pcsSyncData['attendees'] = $attendeeList;
        }
        $pcsSyncData['personId'] = $personId;
        return $pcsSyncData;
    }

    /**
     * Generate Redirection URL
     *
     * @param int $personId
     * @param int $appointmentId
     * @param string $type
     * @param string $slotType - For office hour this will be 'I' and for series this will be 'S'
     * @return string
     */
    public function generateRedirectionURL($personId, $appointmentId, $type, $slotType = NULL)
    {
        $person = $this->personRepository->find($personId);
        $redirectionURL = $this->ebiConfigService->get('System_URL') . '#/schedule/' . $type . '/' . $appointmentId;
        if (!empty($slotType)) {
            $redirectionURL .= '/' . $slotType;
        }
        $organization = $person->getOrganization();
        $organizationId = $person->getOrganization()->getId();
        if ($organization->getIsLdapSamlEnabled()) {
            $redirectionURL = $this->ebiConfigService->generateCompleteUrl('Faculty_Appointment_List_Page', $organizationId);
        }
        return $redirectionURL;
    }

    /**
     * Format Mapwork data to sync Google
     *
     * @param string $eventType - This would be appointment or officehour
     * @param string $action - This would be create/update/delete
     * @param array $calendarSettings
     * @param Appointments | OfficeHours $appointmentObject
     * @param Person $person
     * @param int $organizationId
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function formatDataToSync($eventType, $action, $calendarSettings, $appointmentObject, $person, $organizationId, $startDate = NULL, $endDate = NULL)
    {
        $facultyId = $person->getId();
        $appointmentDataToSync['eventType'] = $eventType;
        if (isset($appointmentObject) && !empty($appointmentObject->getId())) {
            $appointmentDataToSync['mafEventId'] = $appointmentObject->getId();
        }
        $appointmentDataToSync['personId'] = $facultyId;
        $appointmentDataToSync['orgId'] = $organizationId;
        $appointmentDataToSync['crudType'] = $action;
        $appointmentDataToSync['calendarSettings'] = $calendarSettings;
        if (isset($appointmentObject) && !empty($appointmentObject->getGoogleAppointmentId())) {
            $appointmentDataToSync['googleAppointmentId'] = $appointmentObject->getGoogleAppointmentId();
        }
        if ($action != 'delete') {
            $googleCalendar['location'] = $appointmentObject->getLocation();
            if ($eventType == 'officehour') {
                $googleCalendar['startTime'] = $appointmentObject->getSlotStart();
                $googleCalendar['endTime'] = $appointmentObject->getSlotEnd();
            } else {
                if ($startDate) {
                    $googleCalendar['startTime'] = $startDate;
                } else {
                    $googleCalendar['startTime'] = $appointmentObject->getStartDateTime();
                }
                if ($endDate) {
                    $googleCalendar['endTime'] = $endDate;
                } else {
                    $googleCalendar['endTime'] = $appointmentObject->getEndDateTime();
                }
                $googleCalendar['subject'] = $appointmentObject->getTitle();
            }
            $personName = $person->getLastname() . ' ' . $person->getFirstname();
            $appointmentDataToSync['primaryAttendee']['email'] = $calendarSettings['googleClientId'];
            $appointmentDataToSync['primaryAttendee']['displayName'] = $personName;
            $appointmentDataToSync['mafInputDto'] = $googleCalendar;
        }
        if ($eventType == 'appointment') {
            $orgAcademicYearId = null;
            $currentAcademicYear = $this->academicYearService->findCurrentAcademicYearForOrganization($organizationId);
            if (isset($currentAcademicYear['org_academic_year_id'])) {
                $orgAcademicYearId = $currentAcademicYear['org_academic_year_id'];
            }
            $attendees = $this->appointmentRecipientAndStatusRepository->getParticipantAttendeesForAppointment($organizationId, $facultyId, $appointmentObject->getId(), $orgAcademicYearId);
            $attendeeList = [];
            if (!empty($attendees)) {
                foreach ($attendees as $attendee) {
                    $attendeeDto = new AttendeesDto();
                    $attendeeDto->setStudentId($attendee['student_id']);
                    $attendeeList[] = $attendeeDto;
                }
            }
            $appointmentDataToSync['attendees'] = $attendeeList;
        }
        return $appointmentDataToSync;
    }
}
