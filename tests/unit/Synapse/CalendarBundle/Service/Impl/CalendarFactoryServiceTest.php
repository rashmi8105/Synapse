<?php

namespace Synapse\CalendarBundle\Service\Impl;

use Symfony\Bridge\Monolog\Logger;
use Synapse\CalendarBundle\Repository\OrgCronofyCalendarRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\RestBundle\Entity\CalendarTimeSlotsReponseDto;

class CalendarFactoryServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testsyncPCS()
    {
        $this->specify("Verify cronofy authentication", function ($data, $calendarTool, $syncStatus) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockCronofyWrapperService = $this->getMock('CronofyWrapperService', array('syncPCS'));
            $mockCalendarWrapperService = $this->getMock('CalendarWrapperService', array('syncPCS'));
            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [CronofyWrapperService::SERVICE_KEY, $mockCronofyWrapperService],
                    [CalendarWrapperService::SERVICE_KEY, $mockCalendarWrapperService]
                ]);
            $mockEbiConfigService->method('get')->willReturn($calendarTool);

            if ($calendarTool == 'cronofy') {
                $mockCronofyCalendarRepository = $this->getMock('OrgCronofyCalendarRepository', [
                    'findOneBy'
                ]);
                $mockRepositoryResolver->method('getRepository')
                    ->willReturnMap([
                        ['SynapseCalendarBundle:OrgCronofyCalendar', $mockCronofyCalendarRepository]
                    ]);
                $mockCronofyCalendar = $this->getMock('OrgCronofyCalendar', array('getStatus'));
                $mockCronofyCalendarRepository->method('findOneBy')->willReturn($mockCronofyCalendar);
                $mockCronofyCalendar->method('getStatus')->willReturn($syncStatus);
            }
            $calendarFactoryService = new CalendarFactoryService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarFactoryService->syncPCS($data);
        }, [
            'examples' => [
                [
                    [
                        'personId' => 4891668,
                        'orgId' => 203
                    ],
                    'cronofy',
                    false
                ],
                [
                    [
                        'personId' => 4891668,
                        'orgId' => 203
                    ],
                    'cronofy',
                    true
                ],
                [
                    [
                        'personId' => 4891668,
                        'orgId' => 203,
                        'calendarSettings' => ['google_sync_status' => true]
                    ],
                    'google',
                    true
                ],
                [
                    [
                        'personId' => 4891668,
                        'orgId' => 203,
                        'calendarSettings' => ['google_sync_status' => false]
                    ],
                    'google',
                    false
                ]

            ]
        ]);
    }

    public function testUpdateSyncStatus()
    {
        $this->specify("Verify Update Sync status", function ($syncStatus, $calendarType) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockCronofyWrapperService = $this->getMock('CronofyWrapperService', array('revokeAccess', 'updateSyncStatus'));
            $mockCalendarWrapperService = $this->getMock('CalendarWrapperService', array('syncPCS'));

            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [CronofyWrapperService::SERVICE_KEY, $mockCronofyWrapperService],
                    [CalendarWrapperService::SERVICE_KEY, $mockCalendarWrapperService]
                ]);
            $mockEbiConfigService->method('get')->willReturn($calendarType);
            $mockPersonFacultyRepository = NULL;
            if ($calendarType == 'cronofy') {
                $mockCronofyCalendarRepository = $this->getMock('OrgCronofyCalendarRepository', [
                    'findOneBy'
                ]);
                $mockPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', [
                    'findOneBy', 'getPerson', 'getOrganization', 'getId'
                ]);

                // Mock Repository
                $mockRepositoryResolver->method('getRepository')
                    ->willReturnMap([
                        [OrgCronofyCalendarRepository::REPOSITORY_KEY, $mockCronofyCalendarRepository],
                        [OrgPersonFacultyRepository::REPOSITORY_KEY, $mockPersonFacultyRepository]
                    ]);

                $mockPersonEntity = $this->getMock('Person', [
                    'getId',
                    'getOrganization'
                ]);
                $mockOrganizationEntity = $this->getMock('Organization', [
                    'getId',
                    'getTimezone',
                    'find'
                ]);
                $mockPersonFacultyRepository->method('getPerson')->willReturn($mockPersonEntity);
                $mockPersonFacultyRepository->method('getOrganization')->willReturn($mockOrganizationEntity);
                $mockPersonFacultyRepository->method('getId')->willReturn(1);
            }
            if ($calendarType == 'google') {
                $mockPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', [
                    'setGoogleSyncStatus'
                ]);
            }

            $cronofy = $google = 0;
            if ($calendarType == 'cronofy') {
                $cronofy = 1;
            } else if ($calendarType == 'google') {
                $google = 1;
            }
            $calendarFactoryService = new CalendarFactoryService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarFactoryService->updateSyncStatus($syncStatus, $mockPersonFacultyRepository);
            $this->assertInternalType("integer", $cronofy);
            $this->assertInternalType("integer", $google);
        }, [
            'examples' => [
                [false, 'cronofy'],
                [true, 'cronofy'],
                [true, 'google'],
                [false, 'google'],
                [true, ''],
                [false, '']
            ]
        ]);
    }

    public function testGetBusyEvents()
    {
        $this->specify("Verify getBusyEvents", function ($personId, $organizationId, $pcsCalendarIds, $fromDate, $toDate, $calendarTool, $syncStatus) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            //Mock Services
            $mockCronofyCalendarService = $this->getMock('CronofyCalendarService', array('enableAuthentication', 'refreshToken', 'freeBusy', 'firstPage'));
            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockCronofyWrapperService = $this->getMock('CronofyWrapperService', array('getBusyEvents'));
            $mockCalendarWrapperService = $this->getMock('CalendarWrapperService', array('listBusyTentativeEventsFromGoogle'));
            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [CronofyWrapperService::SERVICE_KEY, $mockCronofyWrapperService],
                    [CalendarWrapperService::SERVICE_KEY, $mockCalendarWrapperService],
                    [CronofyCalendarService::SERVICE_KEY, $mockCronofyCalendarService]
                ]);
            $mockEbiConfigService->method('get')->willReturn($calendarTool);

            if ($calendarTool == 'cronofy') {
                $mockCronofyCalendarRepository = $this->getMock('OrgCronofyCalendarRepository', [
                    'findOneBy'
                ]);

                // Mock Repository
                $mockRepositoryResolver->method('getRepository')
                    ->willReturnMap([
                        [OrgCronofyCalendarRepository::REPOSITORY_KEY, $mockCronofyCalendarRepository]
                    ]);
                $mockCronofyCalendar = $this->getMock('OrgCronofyCalendar', array('getStatus'));
                $mockCronofyCalendarRepository->method('findOneBy')->willReturn($mockCronofyCalendar);
                $mockCronofyCalendar->method('getStatus')->willReturn($syncStatus);
            }

            $event['start']['tzid'] = 'Asia/Kolkata';
            $startTime = $fromDate;
            $endTime = $toDate;
            $event['start']['time'] = $fromDate;
            $event['end']['time'] = $endTime;
            $events[] = $event;

            $calendarSlotDto = new CalendarTimeSlotsReponseDto();
            $calendarSlotDto->setOfficeHoursId(0);
            $calendarSlotDto->setOfficeHoursId(0);
            $timeZone = new \DateTimeZone('Asia/Kolkata');
            $startTime = new \DateTime($startTime, $timeZone);
            $startTime->setTimezone(new \DateTimeZone('UTC'));
            $endTime = new \DateTime($endTime, $timeZone);
            $endTime->setTimezone(new \DateTimeZone('UTC'));
            $calendarSlotDto->setSlotStart($startTime);
            $calendarSlotDto->setSlotEnd($endTime);
            $calendarSlotDto->setAppointmentId(0);
            $calendarSlotDto->setSlotType('B');
            $calendarEvents[] = $calendarSlotDto;

            $mockCronofyWrapperService->method('getBusyEvents')->willReturn($calendarEvents);
            $calendarFactoryService = new CalendarFactoryService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $results = $calendarFactoryService->getBusyEvents($personId, $organizationId, '', $fromDate, $toDate);
            $this->assertEquals($results, $calendarEvents);
        }, [
            'examples' => [
                [4891668, 203, '', '2017-02-01T10:00:00', '2017-02-01T11:00:00', 'cronofy', true],
                [4901835, 203, '', '2017-04-21T06:00:00', '2017-04-21T06:30:00', 'cronofy', true],
            ]
        ]);
    }


    public function testGetCountOfCalendarSyncUsers()
    {
        $this->specify("Verify cronofy getCountOfCalendarSyncUsers", function ($organizationId, $calendarType, $expectedCalendarEnabledUserCount) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));

            $mockOrgCronofyCalendarRepository = $this->getMock('orgCronofyCalendarRepository', array('getListOfCronofyCalendarSyncUsers'));
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', array('getListOfGoogleCalendarSyncUsers'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrgCronofyCalendarRepository::REPOSITORY_KEY, $mockOrgCronofyCalendarRepository],
                    [OrgPersonFacultyRepository::REPOSITORY_KEY, $mockOrgPersonFacultyRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService]
                ]);

            $mockOrgCronofyCalendarRepository->method('getListOfCronofyCalendarSyncUsers')->willReturn(1);
            $mockOrgPersonFacultyRepository->method('getListOfGoogleCalendarSyncUsers')->willReturn(1);

            $mockEbiConfigService->method('get')->willReturn($calendarType);

            $calendarFactoryService = new CalendarFactoryService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $result = $calendarFactoryService->getCountOfCalendarSyncUsers($organizationId);

            $this->assertInternalType("integer", $result);
            $this->assertEquals($expectedCalendarEnabledUserCount, $result);
        }, [
            'examples' => [
                [214, 'cronofy', 1],
                [203, 'google', 1],
                [214, '', 0]
            ]
        ]);
    }


    public function testRemoveEvent()
    {
        $this->specify("Verify cronofy removeEvent", function ($personId, $organizationId, $removeMafToPcs, $syncStatus, $type, $pcsRemove, $calendarType) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockCronofyWrapperService = $this->getMock('CronofyWrapperService', array('removeEvent'));
            $mockCalendarWrapperService = $this->getMock('CalendarWrapperService', array('removeEvent'));

            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [CronofyWrapperService::SERVICE_KEY, $mockCronofyWrapperService],
                    [CalendarWrapperService::SERVICE_KEY, $mockCalendarWrapperService]
                ]);

            $mockEbiConfigService->method('get')->willReturn($calendarType);

            $cronofy = $google = 0;
            if ($calendarType == 'cronofy') {
                $cronofy = 1;
            } else if($calendarType == 'google') {
                $google = 1;
            }
            $mockCronofyWrapperService->method('removeEvent')->willReturn($cronofy);
            $mockCalendarWrapperService->method('removeEvent')->willReturn($google);

            $calendarFactoryService = new CalendarFactoryService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarFactoryService->removeEvent($personId, $organizationId, $removeMafToPcs, $syncStatus, $type, $pcsRemove);

            $this->assertInternalType("integer", $cronofy);
            $this->assertEquals($cronofy, $mockCronofyWrapperService->removeEvent());
            $this->assertEquals($google, $mockCalendarWrapperService->removeEvent());

        }, [
            'examples' => [
                [4891668, 203, true, true, 'admin', true, 'cronofy'],
                [4901835, 203, false, false, 'admin', false, 'google'],
                [4901835, 203, true, true, 'faculty', true, 'cronofy'],
                [4901835, 203, false, false, 'faculty', false, 'google'],
                [4901835, 203, false, false, 'faculty', false, '']
            ]
        ]);
    }


    public function testInitialSync()
    {
        $this->specify("Verify cronofy initialSync", function ($personId, $organizationId, $calendarType) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockCronofyWrapperService = $this->getMock('CronofyWrapperService', array('initialSyncToPcs'));
            $mockCalendarWrapperService = $this->getMock('CalendarWrapperService', array('initialSyncToPcs'));

            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [CronofyWrapperService::SERVICE_KEY, $mockCronofyWrapperService],
                    [CalendarWrapperService::SERVICE_KEY, $mockCalendarWrapperService]
                ]);

            $mockEbiConfigService->method('get')->willReturn($calendarType);

            $cronofy = $google = 0;
            if ($calendarType == 'cronofy') {
                $cronofy = 1;
            } else if($calendarType == 'google') {
                $google = 1;
            }
            $mockCronofyWrapperService->method('initialSyncToPcs')->willReturn($cronofy);
            $mockCalendarWrapperService->method('initialSyncToPcs')->willReturn($google);

            $calendarFactoryService = new CalendarFactoryService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarFactoryService->initialSync($personId, $organizationId);

            $this->assertInternalType("integer", $cronofy);
            $this->assertEquals($cronofy, $mockCronofyWrapperService->initialSyncToPcs());
            $this->assertEquals($google, $mockCalendarWrapperService->initialSyncToPcs());

        }, [
            'examples' => [
                [4891668, 203, 'cronofy'],
                [4901835, 203, 'google'],
                [4901835, 203, '']
            ]
        ]);
    }


    public function testRemoveEventWithDisableCalendar()
    {
        $this->specify("Verify cronofy removeEventWithDisableCalendar", function ($organizationId, $event, $type, $pcsRemove, $calendarType) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockCronofyWrapperService = $this->getMock('CronofyWrapperService', array('removeEvent'));
            $mockCalendarIntegrationService = $this->getMock('CalendarIntegrationService', array('sendSyncNotificationToFaculties'));

            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [CronofyWrapperService::SERVICE_KEY, $mockCronofyWrapperService],
                    [CalendarIntegrationService::SERVICE_KEY, $mockCalendarIntegrationService]
                ]);

            $mockEbiConfigService->method('get')->willReturn($calendarType);

            $cronofy = $google = 0;
            if ($calendarType == 'cronofy') {
                $cronofy = 1;
            } else if($calendarType == 'google') {
                $google = 1;
            }
            $mockCronofyWrapperService->method('removeEvent')->willReturn($cronofy);
            $mockCalendarIntegrationService->method('sendSyncNotificationToFaculties')->willReturn($google);

            $calendarFactoryService = new CalendarFactoryService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarFactoryService->removeEventWithDisableCalendar($organizationId, $event, $type, $pcsRemove);
            
            $this->assertInternalType("integer", $cronofy);
            $this->assertEquals($cronofy, $mockCronofyWrapperService->removeEvent());
            $this->assertEquals($google, $mockCalendarIntegrationService->sendSyncNotificationToFaculties());

        }, [
            'examples' => [
                [203, 'Calendar_Disabled', 'coordinator', true, 'cronofy'],
                [203, 'Calendar_Disabled', 'coordinator', true, 'google'],
                [203, '', '', true, '']
            ]
        ]);
    }
}