<?php

namespace Synapse\CalendarBundle\Service\Impl;

use Synapse\CalendarBundle\Repository\OrgCronofyCalendarRepository;
use Synapse\CalendarBundle\EntityDto\SyncFacultySettingsDto;
use Synapse\CoreBundle\Repository\AccessTokenRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\MapworksActionService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\JobBundle\Service\Impl\JobService;

class CalendarIntegrationServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;


    public function testUpdateFacultySyncStatus()
    {
        $this->specify("Verify the functionality of the method updateFacultySyncStatus", function ($orgId, $personId) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', array('findOneBy', 'setGoogleSyncDisabledTime', 'flush'));
            $mockResque = $this->getMock('resque', array('enqueue'));

            $mockCalendarFactoryService = $this->getMock('CalendarFactoryService', array('updateSyncStatus'));
            $mockJobService = $this->getMock('JobService', array('addJobToQueue'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrgPersonFacultyRepository::REPOSITORY_KEY, $mockOrgPersonFacultyRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [SynapseConstant::RESQUE_CLASS_KEY, $mockResque],
                    [CalendarFactoryService::SERVICE_KEY, $mockCalendarFactoryService],
                    [JobService::SERVICE_KEY, $mockJobService]
                ]);

            $mockOrgPersonFaculty = $this->getMock('OrgPersonFaculty', array('getId', 'setPcsToMafIsActive', 'setMafToPcsIsActive', 'setGoogleSyncStatus'));
            $mockOrgPersonFaculty->method('setPcsToMafIsActive')->willReturn(1);
            $mockOrgPersonFaculty->method('setMafToPcsIsActive')->willReturn(1);
            $mockOrgPersonFaculty->method('setGoogleSyncStatus')->willReturn(1);

            $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonFaculty);

            $facultySettingsDto = $this->createSyncFacultySettings($orgId, $personId);

            $calendarIntegrationService = new CalendarIntegrationService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $result = $calendarIntegrationService->updateFacultySyncStatus($facultySettingsDto);
            $this->assertNotEmpty($result);
            $this->assertEquals('object', gettype($result));

        }, ["examples" =>
            [
                [203, 4891668],
                [203, 4901835]
            ]
        ]);
    }


    public function testGetGoogleSyncStatus()
    {
        $this->specify("Verify the functionality of the method getGoogleSyncStatus", function ($orgId, $personId) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', array('findOneBy', 'getGoogleSyncStatus'));
			
			$mockOrgCronofyCalendarRepository = $this->getMock('orgCronofyCalendarRepository', array('findOneBy'));
			
            $mockPersonRepository = $this->getMock('PersonRepository', array('findOneBy'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCoreBundle:OrgPersonFaculty', $mockOrgPersonFacultyRepository],
                    ['SynapseCoreBundle:Person', $mockPersonRepository],
					['SynapseCalendarBundle:OrgCronofyCalendar', $mockOrgCronofyCalendarRepository]
                ]);

            $mockOrganization = $this->getMock('Organization', array('getId'));
            $mockOrgPersonFaculty = $this->getMock('OrgPersonFaculty', array('getId', 'getGoogleSyncStatus'));
            $mockPerson = $this->getMock('Person', array('getId'));

            $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonFaculty);
            $mockOrgPersonFacultyRepository->method('getGoogleSyncStatus')->willReturn(1);
			
			$mockOrgCronofyCalendar = $this->getMock('OrgCronofyCalendar', array('getStatus'));
			$mockOrgCronofyCalendarRepository->method('findOneBy')->willReturn($mockOrgCronofyCalendar);
			$mockOrgCronofyCalendar->method('getStatus')->willReturn(0);

            $calendarIntegrationService = new CalendarIntegrationService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $result = $calendarIntegrationService->getGoogleSyncStatus($orgId, $personId);

        }, ["examples" =>
            [
                [203, 4891668],
                [203, 4901835]
            ]
        ]);
    }



    public function testFacultyCalendarSettings()
    {
        $this->specify("Verify the functionality of the method facultyCalendarSettings", function ($orgId, $personId) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockOrganizationRepository = $this->getMock('OrganizationRepository', array('find', 'getPcs'));
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', array('findOneBy', 'flush', 'getGoogleEmailId', 'getGoogleSyncStatus', 'getMafToPcsIsActive', 'getPcsToMafIsActive'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCoreBundle:Organization', $mockOrganizationRepository],
                    ['SynapseCoreBundle:OrgPersonFaculty', $mockOrgPersonFacultyRepository]
                ]);

            $mockOrganizationRepository->method('getPcs')->willReturn('test');

            $mockOrgPersonFaculty = $this->getMock('OrgPersonFaculty', array('getId', 'getGoogleEmailId', 'getGoogleSyncStatus', 'getMafToPcsIsActive', 'getPcsToMafIsActive'));
            $mockOrgPersonFaculty->method('getGoogleEmailId')->willReturn('test');
            $mockOrgPersonFaculty->method('getGoogleSyncStatus')->willReturn('test');
            $mockOrgPersonFaculty->method('getMafToPcsIsActive')->willReturn('test');
            $mockOrgPersonFaculty->method('getPcsToMafIsActive')->willReturn('test');

            $calendarIntegrationService = new CalendarIntegrationService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $facultySetting = $calendarIntegrationService->facultyCalendarSettings($orgId, $personId);
            $this->assertNotEmpty($facultySetting);
            $this->assertEquals('array', gettype($facultySetting));

        }, ["examples" =>
            [
                [203, 4891668],
                [203, 4901835]
            ]
        ]);
    }



    public function testEnableOAuth()
    {
        //$this->markTestSkipped('Failing due to some data required');
        $this->specify("Verify the functionality of the method enableOAuth", function ($personId, $orgId, $type) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockOrgCorporateGoogleAccessRepository = $this->getMock('OrgCorporateGoogleAccessRepository', array('findOneBy', 'create', 'flush'));
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', array('find', 'getGoogleAppointmentId', 'getSyncedMafAppointments'));
            $mockPersonRepository = $this->getMock('PersonRepository', array('find'));
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', array('findOneBy', 'flush', 'setOauthOneTimeToken'));
            $mockAccessTokenRepository = $this->getMock('AccessTokenRepository', array('findOneBy', 'getAccessTokenExpireTime'));

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockClientManager = $this->getMock('ClientManager', array('createClient'));
            $mockClient = $this->getMock('Client', array('getRandomId'));
            $mockGoogleClient = $this->getMock('\Google_Client', array('setClientId', 'setClientSecret', 'setAccessType', 'setApprovalPrompt', 'addScope', 'setApplicationName'));


            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCoreBundle:Organization', $mockOrganizationRepository],
                    ['SynapseCalendarBundle:OrgCorporateGoogleAccess', $mockOrgCorporateGoogleAccessRepository],
                    ['SynapseCoreBundle:Person', $mockPersonRepository],
                    ['SynapseCoreBundle:OrgPersonFaculty', $mockOrgPersonFacultyRepository],
                    ['SynapseCoreBundle:AccessToken', $mockAccessTokenRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    ['ebi_config_service', $mockEbiConfigService],
                    ['fos_oauth_server.client_manager.default', $mockClientManager],
                    ['\Google_Client', $mockGoogleClient]
                ]);

            $mockPerson = $this->getMock('Person', array('getId'));
            $mockOrganization = $this->getMock('Organization', array('getId'));

            $mockOrgCorporateGoogleAccess = $this->getMock('OrgCorporateGoogleAccess', array('setOrganization', 'setPerson', 'setOauthOneTimeToken'));
            $mockOrgCorporateGoogleAccess->method('setOrganization')->willReturn($mockOrganization);
            $mockOrgCorporateGoogleAccess->method('setPerson')->willReturn($mockPerson);
            $mockOrgCorporateGoogleAccess->method('setOauthOneTimeToken')->willReturn('test');

            $mockOrgPersonFaculty = $this->getMock('OrgPersonFaculty', array('getId', 'setOauthOneTimeToken'));
            $mockOrgPersonFaculty->method('setOauthOneTimeToken')->willReturn('test');

            $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonFaculty);

            $mockAccessToken = $this->getMock('AccessToken', array('getId', 'token'));
            $mockAccessToken->token = 'test';
            $mockAccessTokenRepository->method('findOneBy')->willReturn($mockAccessToken);

            $mockClientManager->method('createClient')->willReturn($mockClient);

            $mockGoogleClient->method('setClientId')->willReturn('test');
            $mockGoogleClient->method('setClientSecret')->willReturn('test');
            $mockGoogleClient->method('setAccessType')->willReturn('test');
            $mockGoogleClient->method('setApprovalPrompt')->willReturn('test');
            $mockGoogleClient->method('addScope')->willReturn('test');
            $mockGoogleClient->method('setApplicationName')->willReturn('test');

            $calendarIntegrationService = new CalendarIntegrationService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $authUrl = $calendarIntegrationService->enableOAuth($personId, $orgId, $mockAccessToken->token, $type);
            $this->assertNotEmpty($authUrl);

        }, ["examples" =>
            [
                [4891668, 203, 'organization'],
                [4901835, 203, '']
            ]
        ]);
    }


    public function testSendSyncNotificationToFaculties()
    {
        $this->specify("Verify the functionality of the method sendSyncNotificationToFaculties", function ($orgId, $event, $calendarType) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', array('findBy', 'flush'));
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', array('find', 'getGoogleAppointmentId', 'getSyncedMafAppointments'));

            $mockAlertNotificationsService = $this->getMock('AlertNotificationsService', array('createNotification'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCoreBundle:Organization', $mockOrganizationRepository],
                    ['SynapseCoreBundle:OrgPersonFaculty', $mockOrgPersonFacultyRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    ['alertNotifications_service', $mockAlertNotificationsService]
                ]);

            $mockOrgPersonFaculty = $this->getMock('OrgPersonFaculty', array('setGoogleEmailId', 'setOauthOneTimeToken', 'setOauthCalAccessToken', 'setOauthCalRefreshToken'));
            $mockOrgPersonFaculty->method('setGoogleEmailId')->willReturn('test');
            $mockOrgPersonFaculty->method('setOauthOneTimeToken')->willReturn('test');
            $mockOrgPersonFaculty->method('setOauthCalAccessToken')->willReturn('test');
            $mockOrgPersonFaculty->method('setOauthCalRefreshToken')->willReturn('test');

            $calendarIntegrationService = new CalendarIntegrationService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarIntegrationService->sendSyncNotificationToFaculties($orgId, $event, $calendarType);

        }, ["examples" =>
            [
                [203, 'Calendar_Enabled', 'google'],
                [203, 'Calendar_Disabled', 'google']
            ]
        ]);
    }


    public function testGetGoogleCorporateAccess()
    {
        $this->specify("Verify the functionality of the method getGoogleCorporateAccess", function ($orgId) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockOrgCorporateGoogleAccessRepository = $this->getMock('OrgCorporateGoogleAccessRepository', array('findOneBy', 'getStatus'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCalendarBundle:OrgCorporateGoogleAccess', $mockOrgCorporateGoogleAccessRepository]
                ]);

            $mockOrgCorporateGoogleAccess = $this->getMock('OrgCorporateGoogleAccess', array('getId', 'getStatus'));
            $mockOrgCorporateGoogleAccessRepository->method('findOneBy')->willReturn($mockOrgCorporateGoogleAccess);
            
            $calendarIntegrationService = new CalendarIntegrationService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $status = $calendarIntegrationService->getGoogleCorporateAccess($orgId);
            $this->assertEquals($status, $mockOrgCorporateGoogleAccess->getStatus());

        }, ["examples" =>
            [
                [203],
                [87]
            ]
        ]);
    }

    public function testProcessOauthForOrganization()
    {
        $this->specify("Verifying Organization level OAuth Process", function ($accessToken, $oneTimeToken, $token, $refreshToken) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockGoogleRepository = $this->getMock('orgCorporateGoogleAccessRepository', array('findOneBy', 'getPerson', 'getId', 'flush'));
            $mockAccessTokenRepository = $this->getMock('accessTokenRepository', array('findOneBy', 'getAccessTokenExpireTime'));
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCalendarBundle:OrgCorporateGoogleAccess', $mockGoogleRepository],
                    ['SynapseCoreBundle:AccessToken', $mockAccessTokenRepository]
                ]);
            $mockOrgCorporateGoogleAccessObj = $this->getMock('OrgCorporateGoogleAccess', array('findOneBy',
                'getPerson',
                'getId',
                'getOrganization',
                'setOauthCalAccessToken',
                'setStatus',
                'setOauthCalRefreshToken',
                'setOauthOneTimeToken',
                'flush'
            ));
            $mockGoogleRepository->method('findOneBy')->willReturn($mockOrgCorporateGoogleAccessObj);
            $mockOrgCorporateGoogleAccessObj->method('getPerson')->willReturn($mockOrgCorporateGoogleAccessObj);
            $mockOrgCorporateGoogleAccessObj->method('getOrganization')->willReturn($mockOrgCorporateGoogleAccessObj);
            $mockOrgCorporateGoogleAccessObj->method('getId')->willReturn($mockOrgCorporateGoogleAccessObj);
            $mockEbiConfigService = $this->getMock('EbiConfigService', array('getSystemUrl'));
            $mockContainer->method('get')
                ->willReturnMap([
                    ['ebi_config_service', $mockEbiConfigService]
                ]);            
			$mockAccessToken = $this->getMock('AccessToken', array('findOneBy'));
			$mockAccessTokenRepository->method('findOneBy')->willReturn($mockAccessToken);
			$mockOrgCorporateGoogleAccessObj->method('setOauthCalAccessToken')->willReturn($mockOrgCorporateGoogleAccessObj);
			$mockOrgCorporateGoogleAccessObj->method('setStatus')->willReturn($mockOrgCorporateGoogleAccessObj);
			$mockOrgCorporateGoogleAccessObj->method('setOauthCalRefreshToken')->willReturn($mockOrgCorporateGoogleAccessObj);
			$mockOrgCorporateGoogleAccessObj->method('setOauthOneTimeToken')->willReturn($mockOrgCorporateGoogleAccessObj);
			$calendarIntegrationService = new CalendarIntegrationService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarIntegrationService->processOauthForOrganization($accessToken, $oneTimeToken, $token, $refreshToken);
        }, ["examples" =>
            [
                ['nfjtVFuOjxi6FAcUl', 'uOjxi6FAcUlO8f', 'AcUlO8fO4dfFjjDF4eh', '9VCyVxsvf0yR3Ka'],
                ['uOjxi6FAcUlO8f', 'nfjtVFuOjxi6FAcUl', 'AcUlO8fO4dfFjjDF4eh', '9VCyVxsvf0yR3Ka']
            ]
        ]);
    }

    public function testProcessOauthForFaculty()
    {
        $this->specify("Verifying Faculty level OAuth Process", function ($accessToken, $oneTimeToken, $token, $refreshToken, $client, $type) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));
            $mockAccessTokenRepository = $this->getMock('accessTokenRepository', array('findOneBy', 'getAccessTokenExpireTime'));
            $mockPersonFacultyRepository = $this->getMock('orgPersonFacultyRepository', array('findOneBy', 'getPerson', 'getId', 'flush'));
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCoreBundle:OrgPersonFaculty', $mockPersonFacultyRepository],
                    ['SynapseCoreBundle:AccessToken', $mockAccessTokenRepository]
                ]);
            $mockOrgPersonFaculty = $this->getMock('OrgPersonFaculty', array('findOneBy',
                'getPerson',
                'getId',
                'getOrganization',
                'setOauthCalAccessToken',
                'setGoogleEmailId',
                'setPcsToMafIsActive',
                'setMafToPcsIsActive',
                'setGoogleSyncStatus',
                'setOauthCalRefreshToken',
                'setOauthOneTimeToken',
                'flush'
            ));
            $mockPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonFaculty);
            $mockOrgPersonFaculty->method('getPerson')->willReturn($mockOrgPersonFaculty);
            $mockOrgPersonFaculty->method('getOrganization')->willReturn($mockOrgPersonFaculty);
            $mockOrgPersonFaculty->method('getId')->willReturn($mockOrgPersonFaculty);

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('getSystemUrl'));
            $mockContainer->method('get')
                ->willReturnMap([
                    ['ebi_config_service', $mockEbiConfigService]
                ]);            
			$mockAccessToken = $this->getMock('AccessToken', array('findOneBy'));
			$mockAccessTokenRepository->method('findOneBy')->willReturn($mockAccessToken);
			$mockOrgPersonFaculty->method('setOauthCalAccessToken')->willReturn($mockOrgPersonFaculty);
			$mockOrgPersonFaculty->method('setGoogleEmailId')->willReturn($mockOrgPersonFaculty);
			$mockOrgPersonFaculty->method('setPcsToMafIsActive')->willReturn($mockOrgPersonFaculty);
			$mockOrgPersonFaculty->method('setMafToPcsIsActive')->willReturn($mockOrgPersonFaculty);
			$mockOrgPersonFaculty->method('setGoogleSyncStatus')->willReturn($mockOrgPersonFaculty);
			$mockOrgPersonFaculty->method('setOauthCalRefreshToken')->willReturn($mockOrgPersonFaculty);
			$mockOrgPersonFaculty->method('setOauthOneTimeToken')->willReturn($mockOrgPersonFaculty);            
            $calendarIntegrationService = new CalendarIntegrationService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarIntegrationService->processOauthForFaculty($accessToken, $oneTimeToken, $token, $refreshToken, $client, $type);
        }, ["examples" =>
            [
                ['nfjtVFuOjxi6FAcUl', 'uOjxi6FAcUlO8f', 'AcUlO8fO4dfFjjDF4eh', '9VCyVxsvf0yR3Ka', new \Google_Client, 'pcstomaf'],
                ['nfjtVFuOjxi6FAcUl', 'uOjxi6FAcUlO8f', 'AcUlO8fO4dfFjjDF4eh', '9VCyVxsvf0yR3Ka', new \Google_Client, 'pcstomaf']
            ]
        ]);
    }

    public function testGetFaultyGoogleEmail()
    {
        $this->specify("Verifying Faculty Google Email id", function ($organizationId, $facultyId, $expectedResults) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockOrgPersonFacultyRepository = $this->getMock('personFaculty', array('findOneBy'));
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCoreBundle:OrgPersonFaculty', $mockOrgPersonFacultyRepository]
                ]);

            $mockOrgPersonFaculty = $this->getMock('OrgPersonFaculty', array('getId', 'getGoogleEmailId'));
            $mockOrgPersonFacultyRepository->expects($this->any())->method('findOneBy')->willReturn($mockOrgPersonFaculty);
            $mockOrgPersonFaculty->expects($this->any())->method('getGoogleEmailId')->willReturn($expectedResults);

            $calendarIntegrationService = new CalendarIntegrationService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $results = $calendarIntegrationService->getFaultyGoogleEmail($organizationId, $facultyId);
            $this->assertEquals($expectedResults, $results);

        }, ["examples" =>
            [
                [203, 4879301, 'test@skyfactor.com'],
                [203, 4879305, 'admin@skyfactor.com']
            ]
        ]);
    }


    public function testGetAuthorizationURL()
    {
        $this->specify("Verifying Authorization URL", function ($facultyId, $organizationId, $accessToken, $type) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockOrgCronofyCalendarRepository = $this->getMock('OrgCronofyCalendar', array('findOneBy', 'create', 'flush'));
            $mockOrganizationRepository = $this->getMock('Organization', array('find'));
            $mockPersonRepository = $this->getMock('Person', array('find'));

            $mockClientManager = $this->getMock('ClientManager', array('createClient'));
            $mockCronofyWrapperService = $this->getMock('CronofyWrapperService', array('getAuthorizationURL'));
            $mockClient = $this->getMock('Client', array('getRandomId'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCalendarBundle:OrgCronofyCalendar', $mockOrgCronofyCalendarRepository],
                    ['SynapseCoreBundle:Organization', $mockOrganizationRepository],
                    ['SynapseCoreBundle:Person', $mockPersonRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    ['fos_oauth_server.client_manager.default', $mockClientManager],
                    ['cronofy_wrapper_service', $mockCronofyWrapperService]
                ]);

            $mockOrganization = $this->getMock('Organization', array('getId'));
            $mockOrganizationRepository->expects($this->any())->method('find')->willReturn($mockOrganization);

            $mockPerson = $this->getMock('Person', array('getId'));
            $mockPersonRepository->expects($this->any())->method('find')->willReturn($mockPerson);

            $mockOrgCronofyCalendar = $this->getMock('OrgCronofyCalendar', array('getId', 'setCronofyOneTimeToken', 'setPerson', 'setOrganization'));
            $mockOrgCronofyCalendar->method('setCronofyOneTimeToken')->willReturn('test');
            $mockOrgCronofyCalendar->method('setPerson')->willReturn('test');
            $mockOrgCronofyCalendar->method('setOrganization')->willReturn('test');
            $mockOrgCronofyCalendarRepository->expects($this->any())->method('findOneBy')->willReturn($mockOrgCronofyCalendar);

            $mockClientManager->method('createClient')->willReturn($mockClient);

            $calendarIntegrationService = new CalendarIntegrationService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarIntegrationService->getAuthorizationURL($facultyId, $organizationId, $accessToken, $type, true);

        }, ["examples" =>
            [
                [4879301, 203, 'nfjtVFuOjxi6FAcUl', 'exchange'],
                [4879305, 203, 'nfjtVFuOjxi6FAcUlhttyuu12', 'google']
            ]
        ]);
    }


    public function testProcessCronofyToken()
    {
        $this->specify("Verifying Process Cronofy Token", function ($state, $accessCode, $cronofyError, $expectedResults) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockOrgCronofyCalendarRepository = $this->getMock('OrgCronofyCalendar', array('findOneBy', 'create', 'flush', 'getCronofyProfile'));
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFaculty', array('findOneBy', 'flush'));
            $mockAccessTokenRepository = $this->getMock('accessTokenRepository', array('findOneBy', 'getAccessTokenExpireTime'));
            $mockEbiConfigService = $this->getMock('EbiConfigService', array('getSystemUrl'));
            $mockCronofyWrapperService = $this->getMock('CronofyWrapperService', array('getCalendarId', 'requestToken', 'createChannel', 'updateCronofyHistory'));
            $mockResque = $this->getMock('resque', array('enqueue'));
            $mockCalendarIntegrationService = $this->getMock('CalendarIntegrationService', array('processCronofyToken'));
            $mockPerson = $this->getMock('Person', array('getId'));
            $mockOrganization = $this->getMock('Organization', array('getId'));
            $mockJobService = $this->getMock('JobService', ['addJobToQueue']);
            $mockOrgCronofyCalendar = $this->getMock('OrgCronofyCalendar', array(

                    'getId',
                    'getPerson',
                    'getOrganization',
                    'setStatus',
                    'setCronofyOneTimeToken',
                    'setCronofyCalAccessToken',
                    'setCronofyCalRefreshToken',
                    'setCronofyProvider',
                    'setCronofyProfile',
                    'setCronofyCalendar',
                    'getCronofyProfile',
                    'getCronofyProvider',
                    'setCronofyChannel')
            );
            $mockOrgPersonFaculty = $this->getMock('OrgPersonFaculty', array(
                'getId',
                'setMafToPcsIsActive',
                'setPcsToMafIsActive',
                'setGoogleSyncStatus',
                'setOauthCalRefreshToken',
                'setOauthOneTimeToken'
            ));


            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrgCronofyCalendarRepository::REPOSITORY_KEY, $mockOrgCronofyCalendarRepository],
                    [OrgPersonFacultyRepository::REPOSITORY_KEY, $mockOrgPersonFacultyRepository],
                    [AccessTokenRepository::REPOSITORY_KEY, $mockAccessTokenRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [SynapseConstant::RESQUE_CLASS_KEY, $mockResque],
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [CronofyWrapperService::SERVICE_KEY, $mockCronofyWrapperService],
                    [CalendarIntegrationService::SERVICE_KEY, $mockCalendarIntegrationService],
                    [JobService::SERVICE_KEY, $mockJobService]
                ]);


            $mockOrgCronofyCalendar->expects($this->any())->method('getPerson')->willReturn($mockPerson);
            $mockOrgCronofyCalendar->expects($this->any())->method('getOrganization')->willReturn($mockOrganization);
            $mockOrgCronofyCalendarRepository->expects($this->any())->method('findOneBy')->willReturn($mockOrgCronofyCalendar);

            $mockOrgPersonFacultyRepository->expects($this->any())->method('findOneBy')->willReturn($mockOrgPersonFaculty);
            $mockAccessToken = $this->getMock('AccessToken', array('findOneBy'));
            $mockAccessTokenRepository->method('findOneBy')->willReturn($mockAccessToken);

            $mockCalendarIntegrationService->method('processCronofyToken')->willReturn(true);

            $calendarIntegrationService = new CalendarIntegrationService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $result = $calendarIntegrationService->processCronofyToken($state, $accessCode, $cronofyError);
            $this->assertEquals($result, $expectedResults);
        }, ["examples" =>
            [
                // Proccess Cronofy Token
                [
                    'nfjtVFuOjxi6FAcUl-queryString-cxnbv123nbcxvnxcb-queryString-maftopcs-queryString-false',
                    20456,
                    false,
                    '#/configDone'
                ],
                // Process cronofy token for proxy
                [
                    'nfjtVFuOjxi6FAcUl-queryString-cxnbv123nbcxvnxcb-queryString-maftopcs-queryString-false',
                    20456,
                    false,
                    '#/configDone'
                ],
                // Invalid access code
                [
                    'nfjtVFuOjxi6FAcUl-queryString-cxnbv123nbcxvnxcb-queryString-maftopcs-queryString-false',
                    -9089,
                    true,
                    '#/configDone?error=true'
                ],
                // Enable only PCS to MAF
                [
                    'nfjtVFuOjxi6FAcUl-queryString-cxnbv123nbcxvnxcb-queryString-pcstomaf-queryString-false',
                    20456,
                    false,
                    '#/configDone'
                ],
            ]
        ]);
    }


    public function testRedirectToOauth()
    {
        $this->specify("Verifying redirect to Oauth", function ($loggedUserId, $organizationId, $accessToken, $type) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockAccessTokenRepository = $this->getMock('AccessTokenRepository', array('findOneBy', 'getAccessTokenExpireTime'));
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', array('findOneBy', 'flush'));

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockClientManager = $this->getMock('ClientManager', array('createClient'));
            $mockClient = $this->getMock('Client', array('getRandomId'));
            $mockJobService = $this->getMock('JobService', array('getJobStatus'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [AccessTokenRepository::REPOSITORY_KEY, $mockAccessTokenRepository],
                    [OrgPersonFacultyRepository::REPOSITORY_KEY, $mockOrgPersonFacultyRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [SynapseConstant::CLIENT_MANAGER_CLASS_KEY, $mockClientManager],
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [JobService::SERVICE_KEY, $mockJobService]
                ]);

            $mockAccessToken = $this->getMock('AccessToken', array('findOneBy'));
            $mockAccessTokenRepository->method('findOneBy')->willReturn($mockAccessToken);

            $mockOrgPersonFaculty = $this->getMock('OrgPersonFaculty', array('getId', 'setOauthOneTimeToken'));
            $mockOrgPersonFaculty->method('setOauthOneTimeToken')->willReturn('test');
            $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonFaculty);

            $mockClientManager->method('createClient')->willReturn($mockClient);

            $calendarIntegrationService = new CalendarIntegrationService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarIntegrationService->redirectToOauth($loggedUserId, $organizationId, $accessToken, $type);

        }, ["examples" =>
            [
                [4879301, 203, 'nfjtVFuOjxi6FAcUl', 'exchange'],
                [4879305, 203, 'nfjtVFuOjxi6FAcUlhttyuu12', 'google']
            ]
        ]);
    }


    private function createSyncFacultySettings($orgId, $personId)
    {
        $facultySettingsDto = new SyncFacultySettingsDto();
        $facultySettingsDto->setPersonId($personId);
        $facultySettingsDto->setOrganizationId($orgId);
        $facultySettingsDto->setCalendarType('google');
        $facultySettingsDto->setMafToPcs(true);
        $facultySettingsDto->setSyncOption(true);
        $facultySettingsDto->setPcsToMaf(true);

        return $facultySettingsDto;
    }

    public function testSyncOneOffEvent()
    {
        $this->specify("Test Sync One Off Event", function ($mapworksEventId, $eventType, $actionType, $externalCalendarEventId, $convertedToId) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);
            $mockContainer = $this->getMock('Container', [
                'get'
            ]);
            $mockLogger = $this->getMock('Logger', [
                'debug',
                'error',
                'info'
            ]);

            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['find', 'getPcs']);
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', ['findOneBy', 'flush', 'getGoogleEmailId', 'getGoogleSyncStatus', 'getMafToPcsIsActive', 'getPcsToMafIsActive']);
            $mockOrgCronofyCalendarRepository = $this->getMock('orgCronofyCalendarRepository', ['findOneBy', 'getStatus']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrganizationRepository::REPOSITORY_KEY,
                        $mockOrganizationRepository
                    ],
                    [
                        OrgPersonFacultyRepository::REPOSITORY_KEY,
                        $mockOrgPersonFacultyRepository
                    ],
                    [
                        OrgCronofyCalendarRepository::REPOSITORY_KEY,
                        $mockOrgCronofyCalendarRepository
                    ]
                ]);

            $mockCronofyWrapperService = $this->getMock('CronofyWrapperService', ['syncOneOffEvent', 'deleteOneOffEvent']);
            $mockDateUtilityService = $this->getMock('DateUtilityService', ['getOrganizationISOTimeZone']);
            $mockMapworksActionService = $this->getMock('MapworksActionService', ['getTokenVariablesFromPerson', 'sendCommunicationBasedOnMapworksAction']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        CronofyWrapperService::SERVICE_KEY,
                        $mockCronofyWrapperService
                    ],
                    [
                        MapworksActionService::SERVICE_KEY,
                        $mockMapworksActionService
                    ],
                    [
                        DateUtilityService::SERVICE_KEY,
                        $mockDateUtilityService
                    ]
                ]);

            $mockOrganization = $this->getMock('Organization', [
                'getId', 'getPcs'
            ]);
            $mockOrganizationRepository->method('find')->willReturn($mockOrganization);
            $mockOrganization->method('getPcs')->willReturn('G');
            $mockOrgPersonFaculty = $this->getMock('OrgPersonFaculty', [
                'getGoogleEmailId',
                'getGoogleSyncStatus',
                'getPcsToMafIsActive',
                'getMafToPcsIsActive']);
            $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonFaculty);
            $mockOrgPersonFaculty->method('getMafToPcsIsActive')->willReturn('y');
            $mockOrgPersonFaculty->method('getPcsToMafIsActive')->willReturn('y');

            $mockOrgCronofyCalendar = $this->getMock('OrgCronofyCalendar', ['getStatus']);
            $mockOrgCronofyCalendarRepository->method('findOneBy')->willReturn($mockOrgCronofyCalendar);
            $mockOrgCronofyCalendar->method('getStatus')->willReturn(1);


            $sendCommunicationBasedOnMapworksAction = 0;
            $syncOneOffEvent = 0;
            $deleteOneOffEvent = 0;

            if (empty($expectedResult)) {
                $sendCommunicationBasedOnMapworksAction = 1;
            } else if ($actionType == 'create') {
                $syncOneOffEvent = 1;
            } else {
                $deleteOneOffEvent = 1;
            }
            $mockMapworksActionService->method('sendCommunicationBasedOnMapworksAction')->willReturn($sendCommunicationBasedOnMapworksAction);
            $mockCronofyWrapperService->method('syncOneOffEvent')->willReturn($syncOneOffEvent);
            $mockCronofyWrapperService->method('deleteOneOffEvent')->willReturn($deleteOneOffEvent);


            $calendarIntegrationService = new CalendarIntegrationService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $results = $calendarIntegrationService->syncOneOffEvent(204, 425876, $mapworksEventId, $eventType, $actionType, $externalCalendarEventId, $convertedToId);
            $this->assertEquals(true, $results);
            $this->assertEquals($deleteOneOffEvent, $mockCronofyWrapperService->deleteOneOffEvent());
            $this->assertEquals($syncOneOffEvent, $mockCronofyWrapperService->syncOneOffEvent());
            $this->assertEquals($sendCommunicationBasedOnMapworksAction, $mockMapworksActionService->sendCommunicationBasedOnMapworksAction());
        }, [
            'examples' => [
                [
                    // Sync newly created appointment to external calendar.
                    548755,
                    'appointment',
                    'create',
                    NULL,
                    NULL

                ],
                [
                    // Sync newly created office hour to external calendar.
                    412365,
                    'office_hour',
                    'create',
                    NULL,
                    NULL

                ],
                [
                    // Passing empty event id to create event in external calendar which will fail and send alert/email notification to faculty
                    '',
                    'appointment',
                    'create',
                    NULL,
                    NULL
                ],
                [
                    // Sync updated appointment to external calendar.
                    548755,
                    'appointment',
                    'update',
                    NULL,
                    NULL

                ],
                [
                    // delete appointment from external calendar.
                    548755,
                    'appointment',
                    'delete',
                    'A548755',
                    NULL

                ],
                [
                    // delete appointment from external calendar which become office hour
                    548755,
                    'appointment',
                    'delete',
                    'A548755',
                    451285,

                ],
                [
                    // delete office hour from external calendar
                    548755,
                    'appointment',
                    'delete',
                    'A548755',
                    NULL,
                ]
            ]
        ]);
    }


    public function testsyncOfficeHourSeries()
    {
        $this->specify("Test Sync Office hour series", function ($officeHourSeriesId, $organizationId, $personId, $action) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);
            $mockContainer = $this->getMock('Container', [
                'get'
            ]);
            $mockLogger = $this->getMock('Logger', [
                'debug',
                'error',
                'info'
            ]);

            $mockOrganizationRepository = $this->getMock('OrganizationRepository', array('find', 'getPcs'));
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', array('findOneBy', 'flush', 'getGoogleEmailId', 'getGoogleSyncStatus', 'getMafToPcsIsActive', 'getPcsToMafIsActive'));
            $mockOrgCronofyCalendarRepository = $this->getMock('orgCronofyCalendarRepository', array('findOneBy', 'getStatus'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrganizationRepository::REPOSITORY_KEY,
                        $mockOrganizationRepository
                    ],
                    [
                        OrgPersonFacultyRepository::REPOSITORY_KEY,
                        $mockOrgPersonFacultyRepository
                    ],
                    [
                        OrgCronofyCalendarRepository::REPOSITORY_KEY,
                        $mockOrgCronofyCalendarRepository
                    ]
                ]);

            $mockCronofyWrapperService = $this->getMock('CronofyWrapperService', ['syncOneOffEvent']);
            $mockJobService = $this->getMock('JobService', ['addJobToQueue']);
            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        CronofyWrapperService::SERVICE_KEY,
                        $mockCronofyWrapperService
                    ],
                    [
                        JobService::SERVICE_KEY,
                        $mockJobService
                    ]
                ]);

            $mockOrganization = $this->getMock('Organization', [
                'getId', 'getPcs'
            ]);
            $mockOrganizationRepository->method('find')->willReturn($mockOrganization);
            $mockOrganization->method('getPcs')->willReturn('G');

            $mockOrgPersonFaculty = $this->getMock('OrgPersonFaculty', [
                'getGoogleEmailId',
                'getGoogleSyncStatus',
                'getPcsToMafIsActive',
                'getMafToPcsIsActive']);
            $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonFaculty);
            $mockOrgPersonFaculty->method('getMafToPcsIsActive')->willReturn('y');
            $mockOrgPersonFaculty->method('getPcsToMafIsActive')->willReturn('y');

            $mockOrgCronofyCalendar = $this->getMock('OrgCronofyCalendar', ['getStatus']);
            $mockOrgCronofyCalendarRepository->method('findOneBy')->willReturn($mockOrgCronofyCalendar);
            $mockOrgCronofyCalendar->method('getStatus')->willReturn(1);
            $calendarIntegrationService = new CalendarIntegrationService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $syncOneOffEvent = $calendarIntegrationService->syncOfficeHourSeries($officeHourSeriesId, $organizationId, $personId, $action);
            $this->assertEquals($syncOneOffEvent, TRUE);
        }, [
            'examples' => [
                // Sync newly created series to external calendar.
                [456778, 203, 4878751, 'create'],
                // Passing empty person if
                [67567, 214, NULL, 'update'],
                // Sync updated series slots to external calendar.
                [67567, 214, 5055885, 'update'],
                // Sync the deleted series slots to external calendar.
                [34567, 203, 4878823, 'delete'],
                // Pass invalid organization id
                [34567, NULL, 4878823, 'delete'],
                // Passing empty action
                [34567, NULL, 4878823, NULL],
            ]
        ]);
    }
}