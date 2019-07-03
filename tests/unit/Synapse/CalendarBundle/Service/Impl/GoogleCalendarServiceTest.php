<?php

namespace Synapse\CalendarBundle\Service\Impl;

use Symfony\Bridge\Monolog\Logger;
use Synapse\CalendarBundle\Service\Impl\GoogleCalendarService;
use Synapse\CoreBundle\Entity\OrgPersonFaculty;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;

class GoogleCalendarServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testCreateAppointment()
    {
        $this->specify("Verify the functionality of the method createAppointment", function ($personId, $orgId)
        {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $calendarEvent = $this->createEvent();
            $mockGoogleCalendarService = $this->getMock('GoogleCalendarService', array('authenticateGoogleAPI', 'createAppointment', 'updateAppointment', 'getInstance', 'cancelAppointment'));

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockGoogleClient = $this->getMock('Google_Client', array('setClientId', 'setClientSecret', 'setApprovalPrompt', 'getClientId'));

            $mockContainer->method('get')
                ->willReturnMap([
                    ['google_calendar_service', $mockGoogleCalendarService],
                    ['ebi_config_service', $mockEbiConfigService],
                    ['Google_Client', $mockGoogleClient]
                ]);

            $mockEbiConfigService->method('get')->willReturn('test');

            $mockPerson = $this->getMock('OrgPersonFaculty', array('getOauthCalAccessToken', 'getOauthCalRefreshToken'));
            $mockPerson->method('getOauthCalAccessToken')->willReturn('test');
            $mockPerson->method('getOauthCalRefreshToken')->willReturn('test');

            $mockGoogleClient->method('setClientId')->willReturn('test');
            $mockGoogleClient->method('setClientSecret')->willReturn('test');
            $mockGoogleClient->method('setApprovalPrompt')->willReturn('test');

            $googleCalendarService = new GoogleCalendarService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $googleCalendarService->authenticateGoogleAPI($mockPerson);
            $googleCalendarObj = $googleCalendarService->createAppointment($calendarEvent);

            $this->assertNotEmpty($googleCalendarObj);

        }, [
            'examples' => [
                [4891668, 203],
                [4901835, 203]
            ]
        ]);
    }


    public function testUpdateAppointment()
    {
        $this->specify("Verify the functionality of the method updateAppointment", function ($personId, $orgId)
        {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $calendarEvent = $this->createEvent();
            $mockGoogleCalendarService = $this->getMock('GoogleCalendarService', array('authenticateGoogleAPI', 'createAppointment', 'updateAppointment', 'getInstance', 'cancelAppointment'));

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));

            $mockContainer->method('get')
                ->willReturnMap([
                    ['google_calendar_service', $mockGoogleCalendarService],
                    ['ebi_config_service', $mockEbiConfigService]
                ]);

            $mockEbiConfigService->method('get')->willReturn('test');

            $mockPerson = $this->getMock('OrgPersonFaculty', array('getOauthCalAccessToken', 'getOauthCalRefreshToken'));
            $mockPerson->method('getOauthCalAccessToken')->willReturn('test');
            $mockPerson->method('getOauthCalRefreshToken')->willReturn('test');

            $mockGoogleCalendar = $this->getMock('Google_Service_Calendar_Event', array('getId'));
            $mockGoogleCalendar->id = 1;
            $mockGoogleCalendar->method('getId')->willReturn(1);

            $googleCalendarService = new GoogleCalendarService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $googleCalendarService->authenticateGoogleAPI($mockPerson);

            $googleCalendarObj = $googleCalendarService->updateAppointment($calendarEvent, $mockGoogleCalendar->id);
            $this->assertNotEmpty($googleCalendarObj);


        }, [
            'examples' => [
                [4891668, 203],
                [4901835, 203]
            ]
        ]);
    }


    public function testCancelAppointment()
    {
        $this->specify("Verify the functionality of the method cancelAppointment", function ($personId, $orgId)
        {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockGoogleCalendarService = $this->getMock('GoogleCalendarService', array('authenticateGoogleAPI', 'createAppointment', 'updateAppointment', 'getInstance', 'cancelAppointment'));

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));

            $mockContainer->method('get')
                ->willReturnMap([
                    ['google_calendar_service', $mockGoogleCalendarService],
                    ['ebi_config_service', $mockEbiConfigService]
                ]);

            $mockEbiConfigService->method('get')->willReturn('test');

            $mockPerson = $this->getMock('OrgPersonFaculty', array('getOauthCalAccessToken', 'getOauthCalRefreshToken'));
            $mockPerson->method('getOauthCalAccessToken')->willReturn('test');
            $mockPerson->method('getOauthCalRefreshToken')->willReturn('test');

            $mockGoogleCalendar = $this->getMock('Google_Service_Calendar_Event', array('getId'));
            $mockGoogleCalendar->id = 1;
            $mockGoogleCalendar->method('getId')->willReturn(1);

            $googleCalendarService = new GoogleCalendarService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $googleCalendarService->authenticateGoogleAPI($mockPerson);

            $googleCalendarObj = $googleCalendarService->cancelAppointment($mockGoogleCalendar->id);
            $this->assertNotEmpty($googleCalendarObj);

        }, [
            'examples' => [
                [4891668, 203],
                [4901835, 203]
            ]
        ]);
    }


    public function testGetInstance()
    {
        $this->specify("Verify the functionality of the method getInstance", function ($personId, $orgId)
        {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $calendarEvent = $this->createEvent();
            $mockGoogleCalendarService = $this->getMock('GoogleCalendarService', array('authenticateGoogleAPI', 'createAppointment', 'updateAppointment', 'getInstance', 'cancelAppointment'));

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));

            $mockContainer->method('get')
                ->willReturnMap([
                    ['google_calendar_service', $mockGoogleCalendarService],
                    ['ebi_config_service', $mockEbiConfigService]
                ]);

            $mockEbiConfigService->method('get')->willReturn('test');

            $mockPerson = $this->getMock('OrgPersonFaculty', array('getOauthCalAccessToken', 'getOauthCalRefreshToken'));
            $mockPerson->method('getOauthCalAccessToken')->willReturn('test');
            $mockPerson->method('getOauthCalRefreshToken')->willReturn('test');

            $mockGoogleCalendar = $this->getMock('Google_Service_Calendar_Event', array('getId'));
            $mockGoogleCalendar->id = 1;
            $mockGoogleCalendar->method('getId')->willReturn(1);

            $googleCalendarService = new GoogleCalendarService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $googleCalendarService->authenticateGoogleAPI($mockPerson);

            $googleCalendarObj = $googleCalendarService->getInstance($mockGoogleCalendar->id);
            $this->assertNotEmpty($googleCalendarObj);

        }, [
            'examples' => [
                [4891668, 203],
                [4901835, 203]
            ]
        ]);
    }


    private function createEvent()
    {
        $event = array();
        $attendee = array();

        $attendee[0]['email'] = 'rasmi.techm@gmail.com';
        $attendee[0]['displayName'] = 'Negi Nitesh';
        $attendee[0]['responseStatus'] = 'tentative';

        $reminders = array(
            'useDefault' => FALSE,
            'overrides' => array(
                array('method' => 'email', 'minutes' => 24 * 60),
                array('method' => 'popup', 'minutes' => 10),
            ),
        );

        $slotStartDate = new \DateTime('now');
        $slotStartDate->add(new \DateInterval("P1D"));

        $startDate = array();
        $startDate['dateTime'] = $slotStartDate->format('Y-m-d\TH:i:s') . '+00:00';
        $startDate['timeZone'] = 'UTC';

        $slotEndDate = new \DateTime($slotStartDate->format('Y-m-d H:i:s'));
        $slotEndDate = $slotEndDate->add(new \DateInterval("PT30M"));

        $endDate = array();
        $endDate['dateTime'] = $slotEndDate->format('Y-m-d\TH:i:s') . '+00:00';
        $endDate['timeZone'] = 'UTC';

        $event['location'] = 'Banglore, India';
        $event['description'] = '<a href=https://mapworks-qa.skyfactor.com/>View this appointment in Mapworks</a>';
        $event['attendees'] = $attendee;
        $event['summary'] = 'Mapworks Office Hours';
        $event['reminders'] = $reminders;
        $event['transparency'] = 'transparent';
        $event['start'] = $startDate;
        $event['end'] = $endDate;

        return $event;
    }
}