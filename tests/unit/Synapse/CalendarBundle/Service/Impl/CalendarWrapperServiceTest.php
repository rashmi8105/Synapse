<?php

namespace Synapse\CalendarBundle\Service\Impl;

use Symfony\Bridge\Monolog\Logger;
use Synapse\CalendarBundle\Service\Impl\CalendarWrapperService;
use Synapse\CalendarBundle\Service\Impl\GoogleCalendarService;
use Synapse\RestBundle\Entity\CalendarTimeSlotsReponseDto;

class CalendarWrapperServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testSyncPCS()
    {
        $this->specify("Verify the functionality of the method syncPCS", function ($personId, $orgId, $calendarType, $crudType) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockCalendarWrapperService = $this->getMock('CalendarWrapperService', array('getGoogleCredentials'));
            $mockOrgCorporateGoogleAccessRepository = $this->getMock('OrgCorporateGoogleAccessRepository', array('findOneBy'));
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', array('findOneBy','getOauthCalAccessToken','getGoogleSyncDisabledTime'));

            $mockGoogleCalendarService = $this->getMock('GoogleCalendarService', array('authenticateGoogleAPI', 'createAppointment', 'updateAppointment', 'getInstance', 'cancelAppointment'));
            $mockCalendarIntegrationService = $this->getMock('CalendarIntegrationService', array('facultyCalendarSettings'));
            $mockGoogleFormatService = $this->getMock('GoogleFormatService', array('getGoogleSyncData', 'formatMAFAppointmentDataToGoogle', 'formatMAFOfficeHourDataToGoogle', 'formatMAFOfficeHourSeriesDataToGoogle'));

            $mockAppointmentRepository = $this->getMock('AppointmentsRepository', array('getId', 'getGoogleAppointmentId', 'getFutureAppointmentForFaculty', 'getFutureAppointmentsModified', 'getDeletedFutureAppointments', 'find', 'flush'));
            $mockOfficeHoursRepository = $this->getMock('OfficeHoursRepository', array('getId', 'getGoogleAppointmentId', 'getFutureOfficeHoursForFaculty', 'getDeletedFutureOfficeHours', 'getAllDeletedFutureOfficeHours', 'getFutureOfficeHoursModified', 'flush', 'find', 'setGoogleAppointmentId'));
            $mockOfficeHoursSeriesRepository = $this->getMock('OfficeHoursSeriesRepository', array('getId', 'getGoogleMasterAppointmentId', 'getFutureOfficeHourSeriesForFaculty', 'flush', 'getFutureOfficeHoursSeriesModified', 'find'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCalendarBundle:OrgCorporateGoogleAccess', $mockOrgCorporateGoogleAccessRepository],
                    ['SynapseCoreBundle:OrgPersonFaculty', $mockOrgPersonFacultyRepository],
                    ['SynapseCoreBundle:Appointments', $mockAppointmentRepository],
                    ['SynapseCoreBundle:OfficeHours', $mockOfficeHoursRepository],
                    ['SynapseCoreBundle:OfficeHoursSeries', $mockOfficeHoursSeriesRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    ['calendarwrapper_service', $mockCalendarWrapperService],
                    ['google_calendar_service', $mockGoogleCalendarService],
                    ['google_format_service', $mockGoogleFormatService],
                    ['calendarintegration_service', $mockCalendarIntegrationService]
                ]);

            $mockOrgPersonObj = $this->getMock('Person',array('getId','getOauthCalAccessToken', 'getGoogleSyncDisabledTime'));
            $mockOrgPersonObj->method('getOauthCalAccessToken')->willReturn(1);
            $mockOrgPersonObj->method('getGoogleSyncDisabledTime')->willReturn(new \DateTime('now'));

            $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonObj);

            $mockAppointmentsObj = $this->getMock('Appointments', array('getId', 'setGoogleAppointmentId', 'setLastSynced'));
            $mockAppointmentRepository->method('find')->willReturn($mockAppointmentsObj);
            $mockAppointmentsObj->method('setGoogleAppointmentId')->willReturn(0);
            $mockAppointmentsObj->method('setLastSynced')->willReturn('test');

            $mockOfficeHoursObj = $this->getMock('OfficeHours', array('getId', 'setGoogleAppointmentId', 'setLastSynced'));
            $mockOfficeHoursRepository->method('find')->willReturn($mockOfficeHoursObj);
            $mockOfficeHoursObj->method('setGoogleAppointmentId')->willReturn(0);
            $mockOfficeHoursObj->method('setLastSynced')->willReturn('test');

            $mockGoogleCalendar = $this->getMock('\Google_Service_Calendar_Event', array('getId'));
            $mockGoogleCalendar->id = 1;
            $mockGoogleCalendar->method('getId')->willReturn(1);

            $mockGoogleCalendarService->method('createAppointment')->willReturn($mockGoogleCalendar);

            $mockOfficeHoursSeriesObj = $this->getMock('OfficeHoursSeries',array('getId'));
            $mockOfficeHoursSeriesRepository->method('getId')->willReturn($mockOfficeHoursSeriesObj);

            $calendarSettings = array();
            $mockCalendarIntegrationService->method('facultyCalendarSettings')->willReturn($calendarSettings);

            $pcsSyncData = $this->getSyncPCSData($personId, $orgId, $calendarSettings, $mockOfficeHoursSeriesObj, $calendarType, $crudType);

            $calendarWrapperService = new CalendarWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarWrapperObj = $calendarWrapperService->syncPCS($pcsSyncData);
            //$this->assertEquals($calendarWrapperObj, null);

        }, [
            'examples' => [
                [4891668, 203, 'appointment', 'create'],
                [4891668, 203, 'officehour', 'create'],
                [4901835, 203, 'officehourseries', 'create']
            ]
        ]);
    }

    public function testGetGoogleCredentials()
    {
        $this->specify("Verify the functionality of the method getGoogleCredentials", function ($personId, $orgId) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockOrgCorporateGoogleAccessRepository = $this->getMock('OrgCorporateGoogleAccessRepository', array('findOneBy', 'getStatus', 'getOauthCalAccessToken', 'getOauthCalRefreshToken'));
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', array('findOneBy', 'setOauthCalAccessToken', 'setOauthCalRefreshToken', 'getOauthCalAccessToken', 'getOauthCalRefreshToken'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCalendarBundle:OrgCorporateGoogleAccess', $mockOrgCorporateGoogleAccessRepository],
                    ['SynapseCoreBundle:OrgPersonFaculty', $mockOrgPersonFacultyRepository]
                ]);

            $mockOrgCorporateGoogleAccessObj = $this->getMock('OrgCorporateGoogleAccess',array('getId', 'getStatus', 'getOauthCalAccessToken', 'getOauthCalRefreshToken'));
            $mockOrgCorporateGoogleAccessObj->method('getStatus')->willReturn(1);
            $mockOrgCorporateGoogleAccessObj->method('getOauthCalAccessToken')->willReturn('test');
            $mockOrgCorporateGoogleAccessObj->method('getOauthCalRefreshToken')->willReturn('test');

            $mockOrgCorporateGoogleAccessRepository->method('findOneBy')->willReturn($mockOrgCorporateGoogleAccessObj);

            $mockOrgPersonFacultyObj = $this->getMock('OrgPersonFaculty',array('getId', 'setOauthCalAccessToken', 'setOauthCalRefreshToken', 'getOauthCalAccessToken'));
            $mockOrgPersonFacultyObj->method('setOauthCalAccessToken')->willReturn(1);
            $mockOrgPersonFacultyObj->method('setOauthCalRefreshToken')->willReturn(1);
            $mockOrgPersonFacultyObj->method('getOauthCalAccessToken')->willReturn(1);

            $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonFacultyObj);

            $calendarWrapperService = new CalendarWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarWrapperObj = $calendarWrapperService->getGoogleCredentials($personId, $orgId);
            $this->assertNotEmpty($calendarWrapperObj);
            $this->assertEquals('object', gettype($calendarWrapperObj));

        }, [
            'examples' => [
                [4891668, 203],
                [4901835, 203]
            ]
        ]);
    }


    public function testUpdatePCSReference()
    {
        $this->specify("Verify the functionality of the method updatePCSReference", function ($calendarType, $crudType) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockCalendarWrapperService = $this->getMock('CalendarWrapperService', array('updatePCSReferrence'));
            $mockGoogleCalendarService = $this->getMock('GoogleCalendarService', array('authenticateGoogleAPI', 'createAppointment', 'updateAppointment', 'getInstance', 'cancelAppointment'));

            $mockAppointmentRepository = $this->getMock('AppointmentsRepository', array('getId', 'setGoogleAppointmentId', 'find', 'setLastSynced', 'flush'));
            $mockOfficeHoursRepository = $this->getMock('OfficeHoursRepository', array('getId', 'setGoogleAppointmentId', 'find', 'setLastSynced', 'flush'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCoreBundle:Appointments', $mockAppointmentRepository],
                    ['SynapseCoreBundle:OfficeHours', $mockOfficeHoursRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    ['calendarwrapper_service', $mockCalendarWrapperService],
                    ['google_calendar_service', $mockGoogleCalendarService]
                ]);

            $mockAppointmentObj = $this->getMock('Appointments', array('setGoogleAppointmentId', 'setLastSynced'));
            $mockAppointmentObj->method('setGoogleAppointmentId')->willReturn(0);
            $mockAppointmentObj->method('setLastSynced')->willReturn('test');

            $mockAppointmentRepository->method('find')->willReturn($mockAppointmentObj);

            $mockOfficeHoursObj = $this->getMock('OfficeHours', array('setGoogleAppointmentId', 'getId', 'setLastSynced'));
            $mockOfficeHoursObj->method('setGoogleAppointmentId')->willReturn(0);
            $mockOfficeHoursObj->method('getId')->willReturn(1123);
            $mockOfficeHoursObj->method('setLastSynced')->willReturn(1123);

            $mockOfficeHoursRepository->method('find')->willReturn($mockOfficeHoursObj);

            $mockGoogleCalendar = $this->getMock('\Google_Service_Calendar_Event', array('getId'));
            $mockGoogleCalendar->id = 1;
            $mockGoogleCalendar->method('getId')->willReturn(1);

            $calendarWrapperService = new CalendarWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarWrapperObj = $calendarWrapperService->updatePCSReference($mockGoogleCalendar, $calendarType, $crudType, $mockOfficeHoursObj->getId());
            $this->assertEquals($calendarWrapperObj, null);

        }, [
            'examples' => [
                ['appointment', 'create'],
                ['officehour', 'create']
            ]
        ]);
    }


    public function testInitialSyncToPcs()
    {
        $this->specify("Verify the functionality of the method initialSyncToPcs", function ($personId, $orgId)
        {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockCalendarWrapperService = $this->getMock('CalendarWrapperService', array('getGoogleCredentials'));
            $mockOrgCorporateGoogleAccessRepository = $this->getMock('OrgCorporateGoogleAccessRepository', array('findOneBy'));
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', array('findOneBy','getOauthCalAccessToken','getGoogleSyncDisabledTime'));

            $mockGoogleCalendarService = $this->getMock('GoogleCalendarService', array('authenticateGoogleAPI', 'createAppointment', 'updateAppointment', 'getInstance', 'cancelAppointment'));
            $mockGoogleFormatService = $this->getMock('GoogleFormatService', array('getGoogleSyncData', 'formatMAFAppointmentDataToGoogle', 'formatMAFOfficeHourDataToGoogle', 'formatMAFOfficeHourSeriesDataToGoogle'));

            $mockAppointmentRepository = $this->getMock('AppointmentsRepository', array('getId', 'getGoogleAppointmentId', 'getFutureAppointmentForFaculty', 'getFutureAppointmentsModified', 'getDeletedFutureAppointments'));
            $mockOfficeHoursRepository = $this->getMock('OfficeHoursRepository', array('getId', 'getGoogleAppointmentId', 'getFutureOfficeHoursForFaculty', 'getDeletedFutureOfficeHours', 'getAllDeletedFutureOfficeHours', 'getFutureOfficeHoursModified', 'flush'));
            $mockOfficeHoursSeriesRepository = $this->getMock('OfficeHoursSeriesRepository', array('getId', 'getGoogleMasterAppointmentId', 'getFutureOfficeHourSeriesForFaculty', 'flush', 'getFutureOfficeHoursSeriesModified'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCalendarBundle:OrgCorporateGoogleAccess', $mockOrgCorporateGoogleAccessRepository],
                    ['SynapseCoreBundle:OrgPersonFaculty', $mockOrgPersonFacultyRepository],
                    ['SynapseCoreBundle:Appointments', $mockAppointmentRepository],
                    ['SynapseCoreBundle:OfficeHours', $mockOfficeHoursRepository],
                    ['SynapseCoreBundle:OfficeHoursSeries', $mockOfficeHoursSeriesRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    ['calendarwrapper_service', $mockCalendarWrapperService],
                    ['google_calendar_service', $mockGoogleCalendarService],
                    ['google_format_service', $mockGoogleFormatService]
                ]);

            $mockOrgPersonObj = $this->getMock('Person',array('getId','getOauthCalAccessToken', 'getGoogleSyncDisabledTime'));
            $mockOrgPersonObj->method('getOauthCalAccessToken')->willReturn(1);
            $mockOrgPersonObj->method('getGoogleSyncDisabledTime')->willReturn(new \DateTime('now'));

            $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonObj);

            $mockOfficeHoursRepository->method('getFutureOfficeHoursModified')->willReturn(array());
            $mockOfficeHoursSeriesRepository->method('getFutureOfficeHoursSeriesModified')->willReturn(array());

            $calendarWrapperService = new CalendarWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarWrapperObj = $calendarWrapperService->initialSyncToPcs($personId, $orgId);
            $this->assertEquals($calendarWrapperObj, null);

        }, [
            'examples' => [
                [4891668, 203],
                [4901835, 203]
            ]
        ]);
    }


    public function testUpdatePCSReferenceOnDeletedSeries()
    {
        $this->specify("Verify the functionality of the method updatePCSReferenceOnDeletedSeries", function ($personId, $orgId) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockCalendarWrapperService = $this->getMock('CalendarWrapperService', array('updatePCSReferenceOnDeletedSeries'));

            $mockOfficeHoursSeriesRepository = $this->getMock('OfficeHoursSeriesRepository', array('getId', 'setGoogleAppointmentId', 'find', 'setLastSynced', 'flush'));
            $mockOfficeHoursRepository = $this->getMock('OfficeHoursRepository', array('getId', 'setGoogleAppointmentId', 'findBy', 'setLastSynced', 'flush'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCoreBundle:OfficeHoursSeries', $mockOfficeHoursSeriesRepository],
                    ['SynapseCoreBundle:OfficeHours', $mockOfficeHoursRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    ['calendarwrapper_service', $mockCalendarWrapperService]
                ]);

            $mockOfficeHoursSeriesObj = $this->getMock('OfficeHoursSeries', array('setGoogleMasterAppointmentId', 'getId', 'setLastSynced'));
            $mockOfficeHoursSeriesObj->method('setGoogleMasterAppointmentId')->willReturn(0);
            $mockOfficeHoursSeriesObj->method('getId')->willReturn(1123);
            $mockOfficeHoursSeriesObj->method('setLastSynced')->willReturn('test');

            $mockOfficeHoursSeriesRepository->method('find')->willReturn($mockOfficeHoursSeriesObj);

            $mockOfficeHoursObj = $this->getMock('OfficeHours', array('setGoogleAppointmentId', 'getId', 'setLastSynced', 'findBy'));
            $mockOfficeHoursObj->method('setGoogleAppointmentId')->willReturn(0);
            $mockOfficeHoursObj->method('getId')->willReturn(1123);
            $mockOfficeHoursObj->method('setLastSynced')->willReturn('test');
            $mockOfficeHoursObj->method('setLastSynced')->willReturn('test');

            $mockOfficeHoursRepository->method('findBy')->willReturn($mockOfficeHoursObj);

            $calendarWrapperService = new CalendarWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarWrapperObj = $calendarWrapperService->updatePCSReferenceOnDeletedSeries($mockOfficeHoursSeriesRepository->getId());
            $this->assertEquals($calendarWrapperObj, null);

        }, [
            'examples' => [
                ['appointment', 'create'],
                ['officehour', 'create']
            ]
        ]);
    }




    public function testUpdateOfficeHourSeriesWithPCSData()
    {
        $this->specify("Verify the functionality of the method updateOfficeHourSeriesWithPCSData", function ($personId, $orgId) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockCalendarWrapperService = $this->getMock('CalendarWrapperService', array('updateOfficeHourSeriesWithPCSData'));
            $mockGoogleCalendarService = $this->getMock('GoogleCalendarService', array('authenticateGoogleAPI', 'createAppointment', 'updateAppointment', 'getInstance', 'cancelAppointment'));

            $mockOfficeHoursSeriesRepository = $this->getMock('OfficeHoursSeriesRepository', array('getId', 'setGoogleMasterAppointmentId', 'find', 'setLastSynced', 'flush'));
            $mockOfficeHoursRepository = $this->getMock('OfficeHoursRepository', array('getId', 'setGoogleAppointmentId', 'findOneBy', 'setLastSynced', 'flush'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCoreBundle:OfficeHoursSeries', $mockOfficeHoursSeriesRepository],
                    ['SynapseCoreBundle:OfficeHours', $mockOfficeHoursRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    ['calendarwrapper_service', $mockCalendarWrapperService],
                    ['google_calendar_service', $mockGoogleCalendarService]
                ]);

            $mockOfficeHoursSeriesObj = $this->getMock('OfficeHoursSeries', array('setGoogleMasterAppointmentId', 'getId', 'setLastSynced'));
            $mockOfficeHoursSeriesObj->method('setGoogleMasterAppointmentId')->willReturn(0);
            $mockOfficeHoursSeriesObj->method('getId')->willReturn(1123);
            $mockOfficeHoursSeriesObj->method('setLastSynced')->willReturn('test');

            $mockOfficeHoursSeriesRepository->method('find')->willReturn($mockOfficeHoursSeriesObj);

            $mockOfficeHoursObj = $this->getMock('OfficeHours', array('setGoogleAppointmentId', 'getId', 'setLastSynced', 'findBy'));
            $mockOfficeHoursObj->method('setGoogleAppointmentId')->willReturn(0);
            $mockOfficeHoursObj->method('getId')->willReturn(1123);
            $mockOfficeHoursObj->method('setLastSynced')->willReturn('test');

            $mockOfficeHoursRepository->method('findOneBy')->willReturn($mockOfficeHoursObj);

            $mockGoogleCalendar = $this->getMock('\Google_Service_Calendar_Event', array('getId'));
            $mockGoogleCalendar->id = 1;
            $mockGoogleCalendar->method('getId')->willReturn(1);

            $reflection = new \ReflectionClass(get_class($mockGoogleCalendar));
            $property = $reflection->getProperty('modelData');
            $property->setAccessible(true);
            $property->getValue($mockGoogleCalendar);

            $calendarWrapperService = new CalendarWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarWrapperObj = $calendarWrapperService->updateOfficeHourSeriesWithPCSData($mockGoogleCalendar, $mockOfficeHoursSeriesRepository->getId(), $personId, $orgId, $mockGoogleCalendar->getId());
            $this->assertEquals($calendarWrapperObj, null);

        }, [
            'examples' => [
                [4891668, 203],
                [4901835, 203]
            ]
        ]);
    }


    public function testRemoveEvent()
    {
        $this->specify("Verify the functionality of the method removeEvent", function ($personId, $orgId, $removeMafToPcs, $syncStatus) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockCalendarWrapperService = $this->getMock('CalendarWrapperService', array('getGoogleCredentials'));
            $mockOrgCorporateGoogleAccessRepository = $this->getMock('OrgCorporateGoogleAccessRepository', array('findOneBy', 'getStatus'));
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', array('findOneBy', 'getOauthCalAccessToken', 'getGoogleSyncDisabledTime', 'flush'));

            $mockGoogleCalendarService = $this->getMock('GoogleCalendarService', array('authenticateGoogleAPI', 'createAppointment', 'updateAppointment', 'getInstance', 'cancelAppointment'));

            $mockAppointmentRepository = $this->getMock('AppointmentsRepository', array('getId', 'getGoogleAppointmentId', 'getSyncedMafAppointments'));
            $mockOfficeHoursRepository = $this->getMock('OfficeHoursRepository', array('getId', 'getGoogleAppointmentId', 'getSyncedMafOfficeHours'));
            $mockOfficeHoursSeriesRepository = $this->getMock('OfficeHoursSeriesRepository', array('getId', 'getGoogleMasterAppointmentId', 'getSyncedOfficeHourSeries', 'flush'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCalendarBundle:OrgCorporateGoogleAccess', $mockOrgCorporateGoogleAccessRepository],
                    ['SynapseCoreBundle:OrgPersonFaculty', $mockOrgPersonFacultyRepository],
                    ['SynapseCoreBundle:Appointments', $mockAppointmentRepository],
                    ['SynapseCoreBundle:OfficeHours', $mockOfficeHoursRepository],
                    ['SynapseCoreBundle:OfficeHoursSeries', $mockOfficeHoursSeriesRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    ['calendarwrapper_service', $mockCalendarWrapperService],
                    ['google_calendar_service', $mockGoogleCalendarService]
                ]);

            $mockOrgPersonObj = $this->getMock('Person', array('getId', 'getOauthCalAccessToken', 'getGoogleSyncDisabledTime', 'setGoogleEmailId', 'setOauthOneTimeToken', 'setOauthCalAccessToken', 'setOauthCalRefreshToken'));
            $mockOrgPersonObj->method('getOauthCalAccessToken')->willReturn(1);
            $mockOrgPersonObj->method('getGoogleSyncDisabledTime')->willReturn(new \DateTime('now'));
            $mockOrgPersonObj->method('setGoogleEmailId')->willReturn(1);
            $mockOrgPersonObj->method('setOauthOneTimeToken')->willReturn(1);
            $mockOrgPersonObj->method('setOauthCalAccessToken')->willReturn(1);
            $mockOrgPersonObj->method('setOauthCalRefreshToken')->willReturn(1);

            $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonObj);

            $mockAppointmentRepository->method('getSyncedMafAppointments')->willReturn(array());
            $mockOfficeHoursRepository->method('getSyncedMafOfficeHours')->willReturn(array());
            $mockOfficeHoursSeriesRepository->method('getSyncedOfficeHourSeries')->willReturn(array());

            $mockOrgCorporateGoogleAccessObj = $this->getMock('OrgCorporateGoogleAccess', array('getStatus'));
            $mockOrgCorporateGoogleAccessRepository->method('findOneBy')->willReturn($mockOrgCorporateGoogleAccessObj);
            $mockOrgCorporateGoogleAccessRepository->method('getStatus')->willReturn($mockOrgCorporateGoogleAccessObj);

            $calendarWrapperService = new CalendarWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarWrapperObj = $calendarWrapperService->removeEvent($personId, $orgId, $removeMafToPcs, $syncStatus);
            $this->assertEquals($calendarWrapperObj, null);

        }, [
            'examples' => [
                [4891668, 203, true, 0],
                [4901835, 203, true, 0]
            ]
        ]);
    }


    public function testListBusyTentativeEventsFromGoogle()
    {
        $this->specify("Verify the functionality of the method listBusyTentativeEventsFromGoogle", function ($personId, $orgId, $pcsCalendarId) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockCalendarWrapperService = $this->getMock('CalendarWrapperService', array('getGoogleCredentials'));
            $mockOrgCorporateGoogleAccessRepository = $this->getMock('OrgCorporateGoogleAccessRepository', array('findOneBy'));
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', array('findOneBy','getOauthCalAccessToken','getGoogleSyncDisabledTime'));

            $mockGoogleCalendarService = $this->getMock('GoogleCalendarService', array('authenticateGoogleAPI', 'createAppointment', 'updateAppointment', 'getInstance', 'cancelAppointment', 'listEvents'));
            $mockCalendarIntegrationService = $this->getMock('CalendarIntegrationService', array('facultyCalendarSettings'));
            $mockGoogleFormatService = $this->getMock('GoogleFormatService', array('getGoogleSyncData', 'formatMAFAppointmentDataToGoogle', 'formatMAFOfficeHourDataToGoogle', 'formatMAFOfficeHourSeriesDataToGoogle'));

            $mockAppointmentRepository = $this->getMock('AppointmentsRepository', array('getId', 'getGoogleAppointmentId', 'getFutureAppointmentForFaculty', 'getFutureAppointmentsModified', 'getDeletedFutureAppointments', 'find', 'flush'));
            $mockOfficeHoursRepository = $this->getMock('OfficeHoursRepository', array('getId', 'getGoogleAppointmentId', 'getFutureOfficeHoursForFaculty', 'getDeletedFutureOfficeHours', 'getAllDeletedFutureOfficeHours', 'getFutureOfficeHoursModified', 'flush', 'find', 'setGoogleAppointmentId'));
            $mockOfficeHoursSeriesRepository = $this->getMock('OfficeHoursSeriesRepository', array('getId', 'getGoogleMasterAppointmentId', 'getFutureOfficeHourSeriesForFaculty', 'flush', 'getFutureOfficeHoursSeriesModified', 'find'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCalendarBundle:OrgCorporateGoogleAccess', $mockOrgCorporateGoogleAccessRepository],
                    ['SynapseCoreBundle:OrgPersonFaculty', $mockOrgPersonFacultyRepository],
                    ['SynapseCoreBundle:Appointments', $mockAppointmentRepository],
                    ['SynapseCoreBundle:OfficeHours', $mockOfficeHoursRepository],
                    ['SynapseCoreBundle:OfficeHoursSeries', $mockOfficeHoursSeriesRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    ['calendarwrapper_service', $mockCalendarWrapperService],
                    ['google_calendar_service', $mockGoogleCalendarService],
                    ['google_format_service', $mockGoogleFormatService],
                    ['calendarintegration_service', $mockCalendarIntegrationService]
                ]);

            $mockOrgPersonObj = $this->getMock('Person',array('getId','getOauthCalAccessToken', 'getGoogleSyncDisabledTime', 'getGoogleEmailId'));
            $mockOrgPersonObj->method('getOauthCalAccessToken')->willReturn(1);
            $mockOrgPersonObj->method('getGoogleSyncDisabledTime')->willReturn(new \DateTime('now'));
            $mockOrgPersonObj->method('getGoogleEmailId')->willReturn('test');

            $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonObj);

            $mockGoogleCalendarService->method('listEvents')->willReturn(array());

            $slotStartDate = new \DateTime('now');
            $slotEndDate = new \DateTime($slotStartDate->format('Y-m-d H:i:s'));
            $slotEndDate = $slotEndDate->add(new \DateInterval("P1D"));

            $calendarWrapperService = new CalendarWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $eventList = $calendarWrapperService->listBusyTentativeEventsFromGoogle($personId, $orgId, $pcsCalendarId, $slotStartDate->format('Y-m-d H:i:s'), $slotEndDate->format('Y-m-d H:i:s'));


        }, [
            'examples' => [
                [4891668, 203, 101],
                [4901835, 203, 102]
            ]
        ]);
    }


    private function getSyncPCSData($personId, $orgId, $calendarSettings, $officeHourSeriesEntity, $eventType, $crudType)
    {
        $calendarSettings['campusSettings'] = 'G';
        $personName = 'Tech Mahindra';
        $appointmentSyncData['primaryAttendee']['email'] = 'rasmi.techm@gmail.com';
        $appointmentSyncData['primaryAttendee']['displayName'] = $personName;
        $appointmentSyncData['mafEventId'] = 1123;

        if($crudType != 'create') {
            $appointmentSyncData['googleAppointmentId'] = '1234567890abcdefghijklmnopqrstuvwxyz';
        }

        $appointmentSyncData['mafInputObject'] = $officeHourSeriesEntity;
        $appointmentSyncData['personId'] = $personId;
        $appointmentSyncData['orgId'] = $orgId;
        $appointmentSyncData['eventType'] = $eventType;
        $appointmentSyncData['crudType'] = $crudType;
        $appointmentSyncData['calendarSettings'] = $calendarSettings;

        return $appointmentSyncData;
    }
}
