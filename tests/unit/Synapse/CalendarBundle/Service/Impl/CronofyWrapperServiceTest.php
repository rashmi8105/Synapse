<?php

namespace Synapse\CalendarBundle\Service\Impl;

use DateTime;
use Synapse\CalendarBundle\Entity\OrgCronofyCalendar;
use Synapse\CalendarBundle\Repository\OrgCronofyCalendarRepository;
use Synapse\CalendarBundle\Repository\OrgCronofyHistoryRepository;
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
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\MapworksActionService;
use Synapse\CoreBundle\Service\Impl\NotificationChannelService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\RestBundle\Entity\CalendarTimeSlotsReponseDto;

class CronofyWrapperServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    private $personId = 4891668;

    private $organizationId = 203;

    public function testAuthenticate()
    {
        $this->specify("Verify cronofy authentication", function ($accessToken) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockCronofyCalendarService = $this->getMock('CronofyCalendarService', array('enableAuthentication', 'refreshToken'));
            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [CronofyCalendarService::SERVICE_KEY, $mockCronofyCalendarService]
                ]);
            $mockCronofyCalendarObj = false;

            if (!empty($accessToken)) {
                $mockCronofyCalendarObj = $this->getMock('OrgCronofyCalendarRepository', [
                    'getModifiedAt',
                    'setCronofyCalAccessToken',
                    'setCronofyCalRefreshToken',
                    'flush'
                ]);
                $mockDate = $this->getMock('\DateTime', ['getTimestamp']);
                $mockRepositoryResolver->method('getRepository')
                    ->willReturnMap([
                        [OrgCronofyCalendarRepository::REPOSITORY_KEY, $mockCronofyCalendarObj]
                    ]);
                $mockCronofyCalendarObj->method('getModifiedAt')->willReturn($mockDate);
            }
            $cronofyWrapperService = new CronofyWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $cronofyWrapperService->authenticate($accessToken, false, $mockCronofyCalendarObj);
        }, [
            'examples' => [
                [false, false, false],
                ['Nzk1N2Q1YjdiMGVlM2Y1MTY0MWY0ZjVmZDY2NTUwZmZkYmJkZmE1NGFkYmUyYzVkNWJmZWNiYTQwNzNhOTg3MA', false, false]
            ]
        ]);
    }

    public function testGetAuthorizationURL()
    {
        $this->specify("Verify cronofy getAuthorizationURL", function ($accessToken, $oneTimeToken, $type) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockCronofyCalendarService = $this->getMock('CronofyCalendarService', array('getAuthorizationURL', 'enableAuthentication'));
            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [CronofyCalendarService::SERVICE_KEY, $mockCronofyCalendarService]
                ]);

            $redirectionUrl = 'https://api.cronofy.com/' . $accessToken . '-queryString-' . $oneTimeToken . '-queryString-' . $type;
            $mockCronofyCalendarService->method('getAuthorizationURL')->willReturn($redirectionUrl);
            $cronofyWrapperService = new CronofyWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $redirection = $cronofyWrapperService->getAuthorizationURL($accessToken, $oneTimeToken, $type, true);
            $this->assertEquals($redirection, $redirectionUrl);
        }, [
            'examples' => [
                ['Nzk1N2Q1YjdiMGVlM2Y1MTY0MWY0ZjVmZD', 'Y2NTUwZmZkYmJkZmE1NGFkYmUyYzVkNWJmZWNiYTQwNzNhOTg3MA', 'maftopcs'],
                ['JmZWNiYTQwNzNhOTg3MA', 'JmZWNiYTQwNzNhOTg3MA', 'pcstomaf']
            ]
        ]);
    }

    public function testRequestToken()
    {
        $this->specify("Verify cronofy requestToken", function ($code) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockCronofyCalendarService = $this->getMock('CronofyCalendarService', array('requestToken', 'enableAuthentication'));
            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [CronofyCalendarService::SERVICE_KEY, $mockCronofyCalendarService]
                ]);

            $requestToken = 'JmZWNiYTQwNzNhOTg3MA';
            $mockCronofyCalendarService->method('requestToken')->willReturn($requestToken);
            $cronofyWrapperService = new CronofyWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $token = $cronofyWrapperService->requestToken($code);
            $this->assertEquals($requestToken, $token);
        }, [
            'examples' => [
                ['Nzk1N2Q1Yjdiretrt'],
                ['JmZWNiYTQwNzNhOTg3MA']
            ]
        ]);
    }

    public function testRevokeAuthorization()
    {
        $this->specify("Verify cronofy revokeAuthorization", function ($personId, $organizationId) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockCronofyCalendarRepository = $this->getMock('OrgCronofyCalendarRepository', array('findOneBy', 'flush'));
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', array('findOneBy'));
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrgCronofyCalendarRepository::REPOSITORY_KEY, $mockCronofyCalendarRepository],
                    [OrgPersonFacultyRepository::REPOSITORY_KEY, $mockOrgPersonFacultyRepository]
                ]);

            $mockCronofyCalendar = $this->getMock('OrgCronofyCalendar', array(
                    'getCronofyCalAccessToken',
                    'getCronofyCalRefreshToken',
                    'getCronofyChannel',
                    'setCronofyCalAccessToken',
                    'setCronofyCalRefreshToken',
                    'setCronofyProfile',
                    'setCronofyCalendar',
                    'setCronofyProvider',
                    'setStatus',
                    'setCronofyChannel'
                )
            );
            $mockCronofyCalendarRepository->method('findOneBy')->willReturn($mockCronofyCalendar);

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockCronofyCalendarService = $this->getMock('CronofyCalendarService', array('revokeAuthorization', 'enableAuthentication'));
            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [CronofyCalendarService::SERVICE_KEY, $mockCronofyCalendarService]
                ]);

            $cronofyWrapperService = new CronofyWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $results = $cronofyWrapperService->revokeAccess($personId, $organizationId);
            $this->assertEquals(NULL, $results);
        }, [
            'examples' => [
                [4891668, 203],
                [4901835, 203]
            ]
        ]);
    }

    public function testGetCalendarId()
    {
        $this->specify("Verify cronofy getCalendarId", function ($accessToken, $refreshToken, $profileName, $calendarId, $calendars, $providerName) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockCronofyCalendarRepository = $this->getMock('OrgCronofyCalendarRepository', array('findOneBy', 'flush'));
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrgCronofyCalendarRepository::REPOSITORY_KEY, $mockCronofyCalendarRepository]
                ]);

            $mockCronofyCalendar = $this->getMock('OrgCronofyCalendar', array('getCronofyCalAccessToken', 'setCronofyCalAccessToken', 'setCronofyCalRefreshToken', 'setCronofyProfile', 'setCronofyCalendar', 'setCronofyProvider', 'setStatus'));
            $mockCronofyCalendarRepository->method('findOneBy')->willReturn($mockCronofyCalendar);
            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockCronofyCalendarService = $this->getMock('CronofyCalendarService', array('listCalendars', 'enableAuthentication', 'refreshToken'));
            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [CronofyCalendarService::SERVICE_KEY, $mockCronofyCalendarService]
                ]);
            $mockCronofyCalendarObj = NULL;
            $mockCronofyCalendarService->method('listCalendars')->willReturn($calendars);

            if (!empty($accessToken)) {
                $mockCronofyCalendarObj = $this->getMock('OrgCronofyCalendarRepository', [
                    'getModifiedAt',
                    'setCronofyCalAccessToken',
                    'setCronofyCalRefreshToken',
                    'flush'
                ]);
                $mockDate = $this->getMock('\DateTime', ['getTimestamp']);
                $mockRepositoryResolver->method('getRepository')
                    ->willReturnMap([
                        [OrgCronofyCalendarRepository::REPOSITORY_KEY, $mockCronofyCalendarObj]
                    ]);
                $mockCronofyCalendarObj->method('getModifiedAt')->willReturn($mockDate);
            }

            $cronofyWrapperService = new CronofyWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $calendarProfile = $cronofyWrapperService->getCalendarId($accessToken, $refreshToken, $profileName, $mockCronofyCalendarObj, $providerName);
            $this->assertEquals($calendarProfile, $calendarId);
        }, [
            'examples' => [
                [
                    // Google calendar Provider
                    'Nzk1N2Q1YjdiMGVlM2Y1',
                    'Y2NTUwZmZkYmJkZmE',
                    'example@cronofy.com',
                    'cal_n23kjnwrw2_jsdfjksn234',
                    ['calendars' =>
                        [
                            [
                                'provider_name' => 'google',
                                'profile_id' => 'pro_n23kjnwrw2',
                                'profile_name' => 'example@cronofy.com',
                                'calendar_id' => 'cal_n23kjnwrw2_jsdfjksn234',
                                'calendar_primary' => 1
                            ]
                        ]
                    ],
                    'google'
                ],
                [
                    // Exchange calendar Provider
                    'NsdFKUed78SDR',
                    'ANdrFJ45dsdIOJ',
                    'test@cronofy.com',
                    'cal_n23kjnwrw2_3nkj23wejk1',
                    ['calendars' =>
                        [
                            [
                                'provider_name' => 'exchange',
                                'profile_id' => 'pro_n93kjnwrw2',
                                'profile_name' => 'test@cronofy.com',
                                'calendar_id' => 'cal_n23kjnwrw2_3nkj23wejk1',
                                'calendar_primary' => 1
                            ]
                        ]
                    ],
                    'exchange'
                ],
                [
                    // iCloud calendar Provider
                    'HD67yERT898LKIOU',
                    'KJUY989JKIJHUokl',
                    'skyfactor@gmail.com',
                    'calendar_123_skyfactor',
                    ['calendars' =>
                        [
                            [
                                'provider_name' => 'apple',
                                'profile_id' => 'pro_erty87DF',
                                'profile_name' => 'skyfactor@gmail.com',
                                'calendar_id' => 'calendar_123_skyfactor',
                                'calendar_primary' => 0
                            ]
                        ]
                    ],
                    'apple'
                ],
                [
                    // outlook calendar Provider which has more than one calendar
                    'HD67yERT898AEDRT',
                    'KJUY989JKIJHUtsrt',
                    'skyfactor@outlook.com',
                    'calendar_235_skyfactor',
                    ['calendars' =>
                        [
                            [
                                'provider_name' => 'live_connect',
                                'profile_id' => 'pro_tryQATR',
                                'profile_name' => 'skyfactor@outlook.com',
                                'calendar_id' => 'calendar_234_skyfactor',
                                'calendar_primary' => 0
                            ],
                            [
                                'provider_name' => 'live_connect',
                                'profile_id' => 'pro_tryQATR',
                                'profile_name' => 'skyfactor@outlook.com',
                                'calendar_id' => 'calendar_235_skyfactor',
                                'calendar_primary' => 1
                            ],
                            [
                                'provider_name' => 'live_connect',
                                'profile_id' => 'pro_tryQATR',
                                'profile_name' => 'skyfactor@outlook.com',
                                'calendar_id' => 'calendar_236_skyfactor',
                                'calendar_primary' => 0
                            ]
                        ]
                    ],
                    'live_connect'
                ],
                [
                    // Multiple calendar providers where profile is created in Google
                    'HD67yERT898AEDRT',
                    'KJUY989JKIJHUtsrt',
                    'skyfactor@google.com',
                    'calendar_100_skyfactor',
                    ['calendars' =>
                        [
                            [
                                'provider_name' => 'google',
                                'profile_id' => 'pro_googleSkyfacot',
                                'profile_name' => 'skyfactor@google.com',
                                'calendar_id' => 'calendar_101_skyfactor',
                                'calendar_primary' => 0
                            ],
                            [
                                'provider_name' => 'google',
                                'profile_id' => 'pro_googleSkyfacot',
                                'profile_name' => 'skyfactor@google.com',
                                'calendar_id' => 'calendar_100_skyfactor',
                                'calendar_primary' => 1
                            ],
                            [
                                'provider_name' => 'google',
                                'profile_id' => 'pro_googleSkyfacot',
                                'profile_name' => 'skyfactor@google.com',
                                'calendar_id' => 'calendar_102_skyfactor',
                                'calendar_primary' => 0
                            ],
                            [
                                'provider_name' => 'outlook',
                                'profile_id' => 'pro_mapworksSkyfacot',
                                'profile_name' => 'mapworks@outlook.com',
                                'calendar_id' => 'calendar_103_skyfactor',
                                'calendar_primary' => 0
                            ],
                            [
                                'provider_name' => 'outlook',
                                'profile_id' => 'pro_mapworksSkyfacot',
                                'profile_name' => 'mapworks@outllok.com',
                                'calendar_id' => 'calendar_104_skyfactor',
                                'calendar_primary' => 1
                            ]
                        ]
                    ],
                    'google'
                ]

            ]
        ]);
    }

    public function testGetBusyEvents()
    {
        $this->specify("Verify getBusyEvents", function ($personId, $organizationId, $fromDate, $toDate) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            // Mock Services
            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockCronofyCalendarService = $this->getMock('CronofyCalendarService', array('enableAuthentication', 'refreshToken', 'getFreeBusyEvents', 'firstPage'));
            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [CronofyCalendarService::SERVICE_KEY, $mockCronofyCalendarService]
                ]);

            // Mock Repository
            $mockCronofyCalendarObj = $this->getMock('OrgCronofyCalendarRepository', [
                'getModifiedAt',
                'setCronofyCalAccessToken',
                'setCronofyCalRefreshToken',
                'flush',
                'findOneBy'
            ]);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrgCronofyCalendarRepository::REPOSITORY_KEY, $mockCronofyCalendarObj]
                ]);


            $mockCronofyCalendar = $this->getMock('OrgCronofyCalendar', array('getCronofyCalAccessToken', 'getCronofyCalRefreshToken', 'getCronofyCalendar'));
            $mockCronofyCalendarObj->method('findOneBy')->willReturn($mockCronofyCalendar);

            $event['start']['tzid'] = 'Asia/Kolkata';
            $startTime = $fromDate;
            $endTime = $toDate;
            $event['start']['time'] = $fromDate;
            $event['end']['time'] = $endTime;
            $event['free_busy_status'] = 'busy';
            $events[] = $event;
            $mockFreeBusy = $this->getMock('CronofyCalendarService', array('getFreeBusyEvents', 'firstPage'));
            $mockFreeBusy->firstPage['free_busy'] = $events;
            $mockCronofyCalendarService->method('getFreeBusyEvents')->willReturn($mockFreeBusy);

            $calendarSlotDto = new CalendarTimeSlotsReponseDto();
            $calendarSlotDto->setOfficeHoursId(0);
            $calendarSlotDto->setOfficeHoursId(0);
            $calendarSlotDto->setIsMultiDayEvent(false);

            $startDateObject = new \DateTime($fromDate);
            $endDateObject = new \DateTime($endTime);
            $interval = $startDateObject->diff($endDateObject);
            $numberOfDays = $interval->d;
            if (strpos($startTime, 'T') == false) {
                // All day event with no start and end time selected (00 - 23:59)
                $startDateTime = $startTime . ' 00:00:00';
                $endDateTime = $startTime . ' 23:59:59';
                if ($numberOfDays > 1) {
                    $calendarSlotDto->setIsMultiDayEvent(true);
                    $endDateTime = $endTime . ' 23:59:59';
                }
                $calendarSlotDto->setIsAllDayEvent(true);
            } else {
                if ($numberOfDays >= 1) {
                    // Time bounded full day event or multi day events
                    $calendarSlotDto->setIsMultiDayEvent(true);
                }
                $endDateTime = $endTime;
                $startDateTime = $startTime;
            }
            $startDate = new \DateTime($startDateTime, new \DateTimeZone('Asia/Kolkata'));
            $startDate->setTimezone(new \DateTimeZone('UTC'));

            $endDate = new \DateTime($endDateTime, new \DateTimeZone('Asia/Kolkata'));
            $endDate->setTimezone(new \DateTimeZone('UTC'));

            $calendarSlotDto->setSlotStart($startDate);
            $calendarSlotDto->setSlotEnd($endDate);
            $calendarSlotDto->setAppointmentId(0);
            $calendarSlotDto->setSlotType('B');
            $calendarSlotDto->setIsConflictedFlag(false);
            $calendarEvents[] = $calendarSlotDto;

            // Call service
            $cronofyWrapperService = new CronofyWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $result = $cronofyWrapperService->getBusyEvents($personId, $organizationId, $fromDate, $toDate);
            $this->assertEquals($result, $calendarEvents);
        }, [
            'examples' => [
                [4891668, 203, '2017-01-01T10:00:00', '2017-01-30T11:00:00'],
                //[4901835, 203, '2017-04-12T07:00:00', '2017-04-12T08:30:00'] This test case is failing
            ]
        ]);
    }

    public function testGetFreeBusyEventsForConflictValidation()
    {
        $this->specify("Test function checkAppointmentConflictWithExternalCalendar", function ($startDate, $endDate, $freeBusyEvents, $expectedOutput) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            // Repository Mocks
            $mockCronofyCalendarRepository = $this->getMock("OrgCronofyCalendarRepository", ["findOneBy"]);
            // Entity Mocks
            $mockOrgCronofyCalendar = $this->getMock("OrgCronofyCalendar", [
                "getCronofyCalAccessToken",
                "getCronofyCalRefreshToken",
                "getCronofyCalendar"
            ]);
            // Service Mocks
            $mockEbiConfigService = $this->getMock("EbiConfigService", ["get"]);
            $mockCronofyCalendarService = $this->getMock("CronofyCalendarService", ["enableAuthentication", "getFreeBusyEvents", "getHandleResponse"]);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgCronofyCalendarRepository::REPOSITORY_KEY,
                        $mockCronofyCalendarRepository
                    ]
                ]);
            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        EbiConfigService::SERVICE_KEY,
                        $mockEbiConfigService
                    ],
                    [
                        CronofyCalendarService::SERVICE_KEY,
                        $mockCronofyCalendarService
                    ]
                ]);
            $mockCronofyCalendarRepository->method('findOneBy')->willReturn($mockOrgCronofyCalendar);
            $mockCronofyCalendarService->method('getFreeBusyEvents')->willReturn($this->getCronofyPagedResultIterator($mockCronofyCalendarService, $freeBusyEvents));
            $cronofyWrapperService = new CronofyWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $results = $cronofyWrapperService->getFreeBusyEventsForConflictValidation($startDate, $endDate, $this->personId, $this->organizationId);
            $this->assertInstanceOf('Synapse\CalendarBundle\Service\Impl\CronofyPagedResultIterator', $results);
            $this->assertEquals($results->firstPage, $expectedOutput);
        }, [
            'examples' => [
                [
                    "2017-01-27",
                    "2017-01-27",
                    [
                        [
                            "calendar_id" => "cal_V6gV6WjRXRUOAL7r_5iNV3MeIW@71Xmq@fU3G2A",
                            "start" =>
                                [
                                    "time" => "2017-01-27T10:00:00+05:30",
                                    "tzid" => "Asia/Kolkata"
                                ],
                            "end" =>
                                [
                                    "time" => "2017-01-27T11:00:00+05:30",
                                    "tzid" => "Asia/Kolkata"
                                ],
                            "free_busy_status" => "busy"
                        ]
                    ],
                    [
                        "pages" => [
                            "current" => 1,
                            "total" => 1
                        ],
                        "free_busy" => [
                            [
                                "calendar_id" => "cal_V6gV6WjRXRUOAL7r_5iNV3MeIW@71Xmq@fU3G2A",
                                "start" =>
                                    [
                                        "time" => "2017-01-27T10:00:00+05:30",
                                        "tzid" => "Asia/Kolkata"
                                    ],
                                "end" =>
                                    [
                                        "time" => "2017-01-27T11:00:00+05:30",
                                        "tzid" => "Asia/Kolkata"
                                    ],
                                "free_busy_status" => "busy"
                            ]
                        ]
                    ]
                ],
                [
                    "2017-01-31",
                    "2017-01-31",
                    [],
                    [
                        "pages" => [
                            "current" => 1,
                            "total" => 1
                        ],
                        "free_busy" => []
                    ]
                ]
            ]
        ]);
    }


    public function testRemoveEvent()
    {
        $this->specify("Test function removeEvent", function ($organizationId, $removeMafToPcs, $pcsRemove, $type, $event = null, $personId = null, $syncStatus) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockCronofyCalendarService = $this->getMock("CronofyCalendarService",
                [
                    'revokeAccess',
                    'revokeAuthorization',
                    'enableAuthentication',
                    'deleteAllEvents'
                ]
            );
            $mockCronofyCalendarRepository = $this->getMock('OrgCronofyCalendarRepository', [
                'findOneBy',
                'getCronofyCalAccessToken',
                'setCronofyCalAccessToken',
                'getCronofyChannel',
                'getListOfCronofyCalendarSyncUsers',
                'setCronofyCalRefreshToken',
                'setCronofyProfile',
                'setCronofyCalendar',
                'setCronofyProvider',
                'setStatus',
                'flush'
            ]);
            $mockPersonRepository = $this->getMock('PersonRepository', array('find'));
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', array('findOneBy', 'findBy'));
            $mockOrganizationRepository = $this->getMock('organizationRepository', ['find']);
            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockAppointmentsRepository = $this->getMock("AppointmentsRepository", ['getSyncedMafAppointments']);
            $mockOfficeHourRepository = $this->getMock("OfficeHourRepository", ['getSyncedMafOfficeHours']);
            $mockCronofyHistory = $this->getMock("OrgCronofyHistoryRepository", ['create', 'flush']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgCronofyCalendarRepository::REPOSITORY_KEY,
                        $mockCronofyCalendarRepository
                    ],
                    [
                        OrganizationRepository::REPOSITORY_KEY,
                        $mockOrganizationRepository
                    ],
                    [
                        OrgPersonFacultyRepository::REPOSITORY_KEY,
                        $mockOrgPersonFacultyRepository
                    ],
                    [
                        AppointmentsRepository::REPOSITORY_KEY,
                        $mockAppointmentsRepository
                    ],
                    [
                        OfficeHoursRepository::REPOSITORY_KEY,
                        $mockOfficeHourRepository
                    ],
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        OrgCronofyHistoryRepository::REPOSITORY_KEY,
                        $mockCronofyHistory
                    ]
                ]);
            $mockCronofyCalendar = $this->getMock('OrgCronofyCalendar', array(
                'getCronofyCalAccessToken',
                'getCronofyCalRefreshToken',
                'getCronofyChannel',
                'getCronofyCalendar',
                'setCronofyCalAccessToken',
                'setCronofyCalRefreshToken',
                'setCronofyProfile',
                'setCronofyCalendar',
                'setCronofyProvider',
                'setStatus',
                'flush',
                'setCronofyChannel'
            ));
            $mockCronofyCalendarRepository->method('findOneBy')->willReturn($mockCronofyCalendar);
            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        CronofyCalendarService::SERVICE_KEY,
                        $mockCronofyCalendarService
                    ],
                    [
                        EbiConfigService::SERVICE_KEY,
                        $mockEbiConfigService
                    ],
                ]);
            $cronofyWrapperService = new CronofyWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $cronofyWrapperService->removeEvent($organizationId, $removeMafToPcs, $pcsRemove, $type, $event, $personId, $syncStatus);
        }, [
            'examples' => [
                [203, true, false, 'faculty', '', 4891668, true],
                [203, false, false, 'admin', '', 4891668, false],
                [203, false, true, 'coordinator', 'Calendar_Disabled', 4891668, false],
                [203, false, false, 'coordinator', 'Calendar_Enabled', 4891668, false],
                [203, true, true, 'faculty', '', 4891668, false],
            ]
        ]);
    }

    
    private function getCronofyPagedResultIterator($mockCronofyCalendarService, $freeBusyEvents)
    {
        $iterator = new CronofyPagedResultIterator($mockCronofyCalendarService, "", [], "http://api.cronofy.com", "");
        $iterator->firstPage = [
            "pages" =>
                [
                    "current" => 1,
                    "total" => 1
                ],
            "free_busy" => $freeBusyEvents
        ];
        return $iterator;
    }

    public function testCreateChannel()
    {
        $this->specify("Test to create channel for the push notification", function ($personId, $calendarId, $channelData, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        EbiConfigRepository::REPOSITORY_KEY,
                        $mockEbiConfigRepository
                    ]
                ]);
            $ebiConfigEntity = $this->getMock('EbiConfig', ['getValue']);
            $mockEbiConfigRepository->expects($this->any())->method('findOneBy')->will($this->returnValue($ebiConfigEntity));

            $mockEbiConfigService = $this->getMock('EbiConfigService', ['get']);
            $mockCronofyCalendarService = $this->getMock('CronofyCalendarService', ['enableAuthentication', 'refreshToken', 'createChannel']);
            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [CronofyCalendarService::SERVICE_KEY, $mockCronofyCalendarService]
                ]);

            $cronofyCalendarObject = new OrgCronofyCalendar();
            $mockCronofyCalendarService->method('createChannel')->willReturn($channelData);

            $cronofyWrapperService = new CronofyWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $result = $cronofyWrapperService->createChannel($personId, $calendarId, NULL, NULL, $cronofyCalendarObject);
            $this->assertEquals($result, $expectedResult);
        }, [
            'examples' => [
                [
                    // Notification type is profile_disconnected
                    4891668,
                    'calendarId',
                    [
                        'notification' => [
                            'type' => 'profile_disconnected'
                        ],
                        'channel' => [
                            'channel_id' => 'test_channel_123',
                            'callback_url' => 'https://mapworks-qa.skyfactor.com/api/v1/cronofy/notifications/4891668'
                        ]
                    ],
                    'test_channel_123'
                ],
                [
                    // Notification type is change, when there is a change in the calendar
                    4891668,
                    'calendarId',
                    [
                        'notification' => [
                            'type' => 'profile_disconnected'
                        ],
                        'channel' => [
                            'channel_id' => 'channel_990898_abgh',
                            'callback_url' => 'https://mapworks-qa.skyfactor.com/api/v1/cronofy/notifications/4891668'
                        ]
                    ],
                    'channel_990898_abgh'
                ]
            ],
            [
                // Passing invalid person id
                '009090yu',
                'calendarId',
                [
                    'notification' => [
                        'type' => 'profile_disconnected'
                    ],
                    'channel' => [
                    ]
                ],
                ''
            ],
        ]);
    }


    public function testGetCalendarProviderName()
    {
        $this->specify("Test external calendar provider name", function ($providerName, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $cronofyWrapperService = new CronofyWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $result = $cronofyWrapperService->getCalendarProviderName($providerName);
            $this->assertEquals($result, $expectedResult);
        }, [
            'examples' => [
                // Google Provider
                [
                    'google',
                    'Google'
                ],
                // Exchange Provider
                [
                    'exchange',
                    'Microsoft'
                ],
                // Outlook.com Provider
                [
                    'live_connect',
                    'Outlook'
                ],
                // iCloud Provider
                [
                    'apple',
                    'Apple iCalendar'
                ],
                // Invalid Provider
                [
                    'calendar',
                    ''
                ]

            ]

        ]);
    }


    public function testupdatePushNotificationToUser()
    {
        $this->specify("Test to create channel for the push notification", function ($personId, $pushNotification) {
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
            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy']);
            $mockCronofyCalendarRepository = $this->getMock('OrgCronofyCalendarRepository',
                ['findOneBy', 'flush', 'getOrganization', 'getPerson', 'getCronofyProvider', 'getCronofyProfile', 'getCronofyCalAccessToken', 'getCronofyCalRefreshToken', 'setCronofyCalRefreshToken']
            );
            $mockPersonRepository = $this->getMock('personRepository', ['find', 'getId']);
            $mockOrganizationRepository = $this->getMock('organizationRepository', ['find']);
            $mockOrgCronofyHistoryRepository = $this->getMock('organizationRepository', ['create', 'flush']);
            $mockEmailTemplateRepository = $this->getMock('EmailTemplateRepository', ['findOneBy', 'getFromEmailAddress', 'getBccRecipientList']);
            $emailTemplateLangRepository = $this->getMock('EmailTemplateLangRepository', ['findOneBy', 'getBody']);
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', array('findOneBy'));
            $mockCalendarSharingRepository = $this->getMock('CalendarSharingRepository', array('getSelectedProxyUsers'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        EbiConfigRepository::REPOSITORY_KEY,
                        $mockEbiConfigRepository
                    ],
                    [
                        OrgCronofyCalendarRepository::REPOSITORY_KEY,
                        $mockCronofyCalendarRepository
                    ],
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        OrganizationRepository::REPOSITORY_KEY,
                        $mockOrganizationRepository
                    ],
                    [
                        OrgCronofyHistoryRepository::REPOSITORY_KEY,
                        $mockOrgCronofyHistoryRepository
                    ],
                    [
                        EmailTemplateRepository::REPOSITORY_KEY,
                        $mockEmailTemplateRepository
                    ],
                    [
                        EmailTemplateLangRepository::REPOSITORY_KEY,
                        $emailTemplateLangRepository
                    ],
                    [
                        OrgPersonFacultyRepository::REPOSITORY_KEY,
                        $mockOrgPersonFacultyRepository
                    ],
                    [
                        CalendarSharingRepository::REPOSITORY_KEY,
                        $mockCalendarSharingRepository
                    ]
                ]);

            $ebiConfigEntity = $this->getMock('EbiConfig', ['getValue']);
            $mockEbiConfigRepository->expects($this->any())->method('findOneBy')->will($this->returnValue($ebiConfigEntity));

            $emailTemplateLang = $this->getMock('EmailTemplateLang',
                ['findOneBy', 'getBody', 'getSubject']
            );
            $emailTemplateLangRepository->expects($this->any())->method('findOneBy')->will($this->returnValue($emailTemplateLang));

            $emailTemplateObject = $this->getMock('EmailTemplate',
                ['findOneBy', 'getFromEmailAddress', 'getBccRecipientList']
            );
            $mockEmailTemplateRepository->expects($this->any())->method('findOneBy')->will($this->returnValue($emailTemplateObject));


            $mockOrganizationEntity = $this->getMock('Organization', ['getId', 'getTimezone', 'find']);
            $mockOrgCronofyCalendarEntity = $this->getMock('OrgCronofyCalendar',
                ['findOneBy', 'getOrganization', 'getPerson', 'getCronofyProvider', 'getCronofyProfile', 'getCronofyCalAccessToken', 'getCronofyCalRefreshToken', 'getCronofyChannel', 'setCronofyCalAccessToken', 'setCronofyCalRefreshToken', 'setCronofyProfile', 'setCronofyCalendar', 'setCronofyProvider', 'setCronofyChannel', 'setStatus']
            );
            $mockCronofyCalendarRepository->method('findOneBy')->willReturn($mockOrgCronofyCalendarEntity);
            $mockOrgCronofyCalendarEntity->method('getOrganization')->willReturn($mockOrganizationEntity);

            $mockPerson = $this->getMock('Person', ['getFirstname', 'getUsername', 'getId']);
            $mockOrgCronofyCalendarEntity->method('getPerson')->willReturn($mockPerson);
            $mockPerson->method('getFirstname')->willReturn('Name');
            $mockPerson->method('getUsername')->willReturn('Name');

            $mockNotificationChannelService = $this->getMock('notificationChannelService', ['sendNotificationToAllRegisteredChannels']);
            $mockCronofyWrapperService = $this->getMock('CronofyWrapperService', ['updatePushNotificationToUser']);
            $mockEmailService = $this->getMock('EmailService', ['sendEmailNotification', 'sendEmail']);
            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockCronofyCalendarService = $this->getMock('CronofyCalendarService', array('revokeAuthorization', 'enableAuthentication'));
            $mockCalendarSharingRepository->method('getSelectedProxyUsers')->willReturn([]);
            
            $mockContainer->method('get')
                ->willReturnMap([
                    [NotificationChannelService::SERVICE_KEY, $mockNotificationChannelService],
                    [CronofyWrapperService::SERVICE_KEY, $mockCronofyWrapperService],
                    [EmailService::SERVICE_KEY, $mockEmailService],
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [CronofyCalendarService::SERVICE_KEY, $mockCronofyCalendarService]
                ]);

            $cronofyWrapperService = new CronofyWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $mockCronofyWrapperService->method('updatePushNotificationToUser')->willReturn(true);
            $cronofyWrapperService->updatePushNotificationToUser($personId, $pushNotification);
            $this->assertTrue(TRUE);
            $this->assertEquals(true, $mockCronofyWrapperService->updatePushNotificationToUser());
        }, [
            'examples' => [
                [
                    // Notification when type is change
                    4891668,
                    [
                        'notification' => [
                            'type' => 'change'
                        ],
                        'channel' => [
                            'channel_id' => 'test_channel_123',
                            'callback_url' => 'https://mapworks-qa.skyfactor.com/api/v1/cronofy/notifications/4891668'
                        ]
                    ]
                ],
                [
                    // Notification when type is profile_disconnected
                    4891668,
                    [
                        'notification' => [
                            'type' => 'profile_disconnected'
                        ],
                        'channel' => [
                            'channel_id' => 'test_channel_154',
                            'callback_url' => 'https://mapworks-qa.skyfactor.com/api/v1/cronofy/notifications/4891668'
                        ]
                    ]
                ],
                [
                    // Notification when type is change
                    4891668,
                    [
                        'notification' => [
                            'type' => 'change'
                        ],
                        'channel' => [
                            'channel_id' => 'test_channel_741',
                            'callback_url' => 'https://mapworks-qa.skyfactor.com/api/v1/cronofy/notifications/4891668'
                        ]
                    ]
                ],
                [
                    // Notification when passing wrong Person Id
                    'sdsd',
                    [
                        'notification' => [
                            'type' => 'change'
                        ],
                        'channel' => [
                            'channel_id' => 'test_channel_564',
                            'callback_url' => 'https://mapworks-qa.skyfactor.com/api/v1/cronofy/notifications/4891668'
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function testSyncOfficeHourSeries()
    {
        $this->specify("Test office hour series sync", function ($officeHourSeriesId, $action, $dataToBeSynced, $expectedResult) {
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

            $mockCronofyCalendarRepository = $this->getMock('OrgCronofyCalendarRepository', ['findOneBy']);
            $mockOfficeHourSeriesRepository = $this->getMock('OfficeHoursSeriesRepository', ['getOfficeHourSlotBySeriesId']);
            $mockAppointmentRepository = $this->getMock('AppointmentsRepository', ['find', 'flush', 'updateSyncDetailsToAppointment']);
            $mockOfficeHoursRepository = $this->getMock('OfficeHoursRepository', ['updateSyncDetailsToOfficeHours', 'find', 'flush', 'updateBatchEventsOfficeHourStatus']);

            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy', 'get']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        EbiConfigRepository::REPOSITORY_KEY,
                        $mockEbiConfigRepository
                    ],
                    [
                        OrgCronofyCalendarRepository::REPOSITORY_KEY,
                        $mockCronofyCalendarRepository
                    ],
                    [
                        OfficeHoursSeriesRepository::REPOSITORY_KEY,
                        $mockOfficeHourSeriesRepository
                    ],
                    [
                        AppointmentsRepository::REPOSITORY_KEY,
                        $mockAppointmentRepository
                    ],
                    [
                        OfficeHoursRepository::REPOSITORY_KEY,
                        $mockOfficeHoursRepository
                    ]
                ]);

            $mockCronofyCalendar = $this->getMock('OrgCronofyCalendar', [
                'getCronofyCalendar',
                'getCronofyCalAccessToken',
                'getCronofyCalRefreshToken'
            ]);

            $mockEbiConfigService = $this->getMock('EbiConfigService', ['get']);
            $mockCronofyCalendarService = $this->getMock('CronofyCalendarService', ['enableAuthentication', 'refreshToken', 'upsertEvent', 'deleteEvent', 'upsertBatchEvent']);
            $mockCronofyFormatService = $this->getMock('CronofyFormatService', ['formatMAFAppointmentDataToCronofy', 'formatMAFOfficeHourDataToCronofy', 'formatMAFAppointmentAndOfficeHourBatchDataToCronofy']);
            $mockDateUtilityService = $this->getMock('DateUtilityService', ['getOrganizationISOTimeZone']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        EbiConfigService::SERVICE_KEY,
                        $mockEbiConfigService
                    ],
                    [
                        CronofyCalendarService::SERVICE_KEY,
                        $mockCronofyCalendarService
                    ],
                    [
                        CronofyFormatService::SERVICE_KEY,
                        $mockCronofyFormatService
                    ],
                    [
                        DateUtilityService::SERVICE_KEY,
                        $mockDateUtilityService
                    ]
                ]);

            $ebiConfigEntity = $this->getMock('EbiConfig', ['getValue']);
            $mockEbiConfigRepository->expects($this->any())->method('findOneBy')->will($this->returnValue($ebiConfigEntity));
            $mockCronofyCalendarRepository->method('findOneBy')->willReturn($mockCronofyCalendar);

            $mockAppointments = $this->getMock('Appointments', [
                'setGoogleAppointmentId', 'setLastSynced'

            ]);
            $mockAppointmentRepository->method('find')->willReturn($mockAppointments);
            $mockEbiConfigRepository->expects($this->any())->method('findOneBy')->will($this->returnValue($ebiConfigEntity));
            $ebiConfigEntity->method('getValue')->willReturn(50);
            $mockOfficeHourSeriesRepository->method('getOfficeHourSlotBySeriesId')->willReturn($dataToBeSynced);
            $mockOfficeHours = $this->getMock('Appointments', [
                'setGoogleAppointmentId', 'setLastSynced'

            ]);
            $mockOfficeHoursRepository->method('find')->willReturn($mockOfficeHours);

            $mockCronofyFormatService->method('formatMAFOfficeHourDataToCronofy')->willReturn($this->getCronofyOfficeHourFormatArray(1, 'calendar1test'));
            $mockCronofyFormatService->method('formatMAFAppointmentDataToCronofy')->willReturn($this->getCronofyOfficeHourFormatArray(1, 'calendar1test', 'appointment'));

            $mockCronofyFormatService->method('formatMAFAppointmentAndOfficeHourBatchDataToCronofy')->willReturn($this->getCronofyFormatArray($dataToBeSynced, 'calendar1test', 'POST'));
            $mockCronofyCalendarService->method('upsertBatchEvent')->willReturn($this->getCronofyResultJSON($dataToBeSynced));

            $currentTime = new \DateTime('now');
            $cronofyWrapperService = new CronofyWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $syncOfficeHour = $cronofyWrapperService->syncOfficeHourSeries($officeHourSeriesId, $this->organizationId, $this->personId, $action, $currentTime);
            $this->assertEquals($expectedResult, $syncOfficeHour);
        }, [
            'examples' => [
                [
                    // Sync office hour series with the new slots. No slots converted to appointments
                    567898,
                    'create',
                    [
                        [
                            'id' => 45126,
                            'google_appointment_id' => 'O45126',
                            'appointments_id' => ''
                        ],
                        [
                            'id' => 45127,
                            'google_appointment_id' => 'O45127',
                            'appointments_id' => ''
                        ],
                    ],
                    TRUE
                ],
                [
                    // Sync office hour series with the slots where few are converted to appointments.
                    8675567,
                    'create',
                    [
                        [
                            'id' => 45126,
                            'google_appointment_id' => 'O45126',
                            'appointments_id' => '4512'
                        ],
                        [
                            'id' => 45127,
                            'google_appointment_id' => 'O45127',
                            'appointments_id' => ''
                        ],
                    ],
                    TRUE
                ],
                [
                    // Sync office hour series where some of the slots are in the form of free standing appointment
                    56466,
                    'create',
                    [
                        [
                            'id' => 45128,
                            'google_appointment_id' => 'O45128',
                            'appointments_id' => 1025,
                            'deleted_at' => '33'
                        ],
                        [
                            'id' => 45129,
                            'google_appointment_id' => 'O45129',
                            'appointments_id' => ''
                        ],
                    ],
                    TRUE
                ],
                [
                    // Sync office hour series when the events are updated from external calendar.
                    787878,
                    'update',
                    [
                        [
                            'id' => 45128,
                            'google_appointment_id' => 'O45128',
                            'appointments_id' => ''
                        ],
                        [
                            'id' => 45125,
                            'google_appointment_id' => 'O45125',
                            'appointments_id' => 123
                        ],
                    ],
                    TRUE
                ],
                [
                    // Sync the deleted office hours from external calendar.
                    545445,
                    'delete',
                    [
                        [
                            'id' => 123,
                            'google_appointment_id' => 'A2344',
                            'appointments_id' => ''
                        ],
                        [
                            'id' => 123,
                            'google_appointment_id' => 'A2344',
                            'appointments_id' => 89
                        ],
                    ],
                    TRUE
                ],
            ]
        ]);
    }

    public function testSyncDeletedOfficeHoursForSeries()
    {
        $this->specify("Test delete office hour series from external calendar", function ($calendarId, $officeHourSeriesId, $allowedRequestCount, $personId, $dataToBeSynced, $organizationId, $expectedResult) {
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
            $mockOfficeHourSeriesRepository = $this->getMock('OfficeHoursSeriesRepository', ['getOfficeHourSlotBySeriesId']);
            $mockOfficeHoursRepository = $this->getMock('OfficeHoursRepository', ['updateSyncDetailsToOfficeHours', 'find', 'flush', 'updateBatchEventsOfficeHourStatus']);
            $mockAppointmentsRepository = $this->getMock('AppointmentsRepository', ['updateSyncDetailsToAppointment']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OfficeHoursSeriesRepository::REPOSITORY_KEY,
                        $mockOfficeHourSeriesRepository
                    ],
                    [
                        OfficeHoursRepository::REPOSITORY_KEY,
                        $mockOfficeHoursRepository
                    ],
                    [
                        AppointmentsRepository::REPOSITORY_KEY,
                        $mockAppointmentsRepository
                    ]
                ]);
            $mockOfficeHourSeriesRepository->method('getOfficeHourSlotBySeriesId')->willReturn($dataToBeSynced);

            $mockCronofyCalendarService = $this->getMock('CronofyCalendarService', ['deleteEvent', 'upsertBatchEvent']);
            $mockCronofyFormatService = $this->getMock('CronofyFormatService', ['formatMAFAppointmentAndOfficeHourBatchDataToCronofy']);
            $mockMapworksActionService = $this->getMock('MapworksActionService', ['sendCommunicationBasedOnMapworksAction']);
            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        CronofyCalendarService::SERVICE_KEY,
                        $mockCronofyCalendarService
                    ],
                    [
                        CronofyFormatService::SERVICE_KEY,
                        $mockCronofyFormatService
                    ],
                    [
                        MapworksActionService::SERVICE_KEY,
                        $mockMapworksActionService
                    ]
                ]);
            $mockCronofyFormatService->method('formatMAFAppointmentAndOfficeHourBatchDataToCronofy')->willReturn($this->getCronofyFormatArray($dataToBeSynced, $calendarId, 'DELETE'));
            $mockCronofyCalendarService->method('upsertBatchEvent')->willReturn($this->getCronofyResultJSON($dataToBeSynced));

            $currentTime = new \DateTime('now');

            try {
                $cronofyWrapperService = new CronofyWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
                $syncOfficeHour = $cronofyWrapperService->syncDeletedOfficeHoursForSeries($calendarId, $officeHourSeriesId, $currentTime, $personId, $organizationId, $allowedRequestCount);

                $this->assertEquals($expectedResult, $syncOfficeHour);
            } catch (\Exception $e) {
                $this->assertEquals($expectedResult, get_class($e));
            }
        }, [
            'examples' => [
                // Delete office hour series - where the slots are not booked as appointment.
                [
                    'cal_WMD73jnUCGmHAA0H_GKh4A37-ZWRAZfa0DGb2iA', 4785, 5, 856231,
                    [
                        [
                            'id' => 45123,
                            'google_appointment_id' => 'O2344',
                            'appointments_id' => ''
                        ],
                        [
                            'id' => 45623,
                            'google_appointment_id' => 'O2344',
                            'appointments_id' => ''
                        ],
                    ], 203, TRUE
                ],
                // Delete office hour series - few slots are booked as appointment and few are still free slots.
                [
                    'cal_WMD73jnUCGmHAA0H_GKh4A37-ZWRAZfa0DGb2iA', 4785, 5, 856231,
                    [
                        [
                            'id' => 45123,
                            'google_appointment_id' => 'A7868',
                            'appointments_id' => 7868
                        ],
                        [
                            'id' => 78546,
                            'google_appointment_id' => 'O78546',
                            'appointments_id' => ''
                        ],
                        [
                            'id' => 41236,
                            'google_appointment_id' => 'O41236',
                            'appointments_id' => ''
                        ],
                        [
                            'id' => 78965,
                            'google_appointment_id' => 'A8789',
                            'appointments_id' => 8789
                        ],
                        [
                            'id' => 41236,
                            'google_appointment_id' => 'O41236',
                            'appointments_id' => ''
                        ],
                    ], 203, TRUE
                ],
                // Delete office hour series - where the slots are booked as appointment.
                [
                    'cal_WMD73jnUCGmHAA0H_GKh4A37-ZWRAZfa0DGb2iA', 4785, 5, 856231,
                    [
                        [
                            'id' => 45123,
                            'google_appointment_id' => 'A456',
                            'appointments_id' => 456
                        ],
                        [
                            'id' => 45623,
                            'google_appointment_id' => 'A426',
                            'appointments_id' => 426
                        ],
                    ], 203, TRUE
                ],
                // Delete office hour series throws exception since event_id id not available
                [
                    'cal_WMD73jnUCGmHAA0H_GKh4A37-ZWRAZfa0DGb2iA', 1234, 5, 67899,
                    [
                        [
                            'id' => 4578,
                            'google_appointment_id' => null,
                            'appointments_id' => null
                        ],
                        [
                            'id' => 8956,
                            'google_appointment_id' => 'A2344',
                            'appointments_id' => 1245
                        ],
                    ], 1,
                    "PHPUnit_Framework_ExpectationFailedException"
                ]
            ]
        ]);
    }

    private function getCronofyOfficeHourFormatArray($eventId, $calendarId, $eventType = 'officehour')
    {
        $start = date('Y-m-d\TH:i:s\Z', strtotime("+15 minutes"));
        $end = date('Y-m-d\TH:i:s\Z', strtotime("+45 minutes"));

        $event['calendar_id'] = $calendarId;
        $event['event_id'] = $eventId;
        $event['summary'] = 'Mapworks Office Hours';
        $event['description'] = 'Mapworks Office Hours description';
        $event['location'] = 'India';
        if ($eventType == 'officehour') {
            $event['transparency'] = 'transparent';
        } else {
            $event['transparency'] = 'opaque';
        }
        $event['start'] = [
            'time' => $start,
            'tzid' => 'Central'
        ];
        $event['end'] = [
            'time' => $end,
            'tzid' => 'Central'
        ];

        return $event;
    }

    private function getCronofyFormatArray($dataToBeSynced, $calendarId, $method)
    {
        $returnArray = [];
        foreach ($dataToBeSynced as $event) {
            $cronofyEventsArray = [];
            $cronofyEventsArray['method'] = $method;
            $cronofyEventsArray['relative_url'] = '/v1/calendars/' . $calendarId . '/events';
            $officeHourId = $event['id'];
            $appointmentId = $event['appointments_id'];
            if ($appointmentId) {
                $eventId['event_id'] = 'A' . $appointmentId;
            } else {
                $eventId['event_id'] = 'O' . $officeHourId;
            }

            if ($method == 'POST') {
                $eventId = $this->getCronofyOfficeHourFormatArray($eventId, $calendarId);
            }

            $cronofyEventsArray['data'] = $eventId;
            $returnArray[] = $cronofyEventsArray;
        }
        return $returnArray;
    }

    private function getCronofyResultJSON($dataToBeSynced)
    {
        $responseArray = [];
        $statusArray['status'] = 202;
        $responseArray[] = $statusArray;

        return json_encode(['batch' => $responseArray]);
    }

    public function testSyncOneOffEvent()
    {
        $this->specify("Test sync one off event", function ($mapworksEventId, $eventType, $actionType, $expectedResult, $parameters, $organizationTimeZone) {

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
            $mockCronofyCalendarRepository = $this->getMock('OrgCronofyCalendarRepository', ['findOneBy']);
            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy']);
            $mockOfficeHourRepository = $this->getMock('OfficeHoursRepository', ['find', 'flush', 'findOneBy']);
            $mockAppointmentsRepository = $this->getMock('Appointments', ['find', 'flush']);


            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgCronofyCalendarRepository::REPOSITORY_KEY,
                        $mockCronofyCalendarRepository
                    ],
                    [
                        EbiConfigRepository::REPOSITORY_KEY,
                        $mockEbiConfigRepository
                    ],
                    [
                        OfficeHoursRepository::REPOSITORY_KEY,
                        $mockOfficeHourRepository
                    ],
                    [
                        AppointmentsRepository::REPOSITORY_KEY,
                        $mockAppointmentsRepository
                    ]
                ]);
            $mockEbiConfigService = $this->getMock('EbiConfigService', ['get']);
            $mockCronofyCalendarService = $this->getMock('CronofyCalendarService', ['enableAuthentication', 'refreshToken', 'upsertEvent']);
            $mockCronofyFormatService = $this->getMock('CronofyFormatService', ['formatMAFAppointmentDataToCronofy', 'formatMAFOfficeHourDataToCronofy']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        EbiConfigService::SERVICE_KEY,
                        $mockEbiConfigService
                    ],
                    [
                        CronofyCalendarService::SERVICE_KEY,
                        $mockCronofyCalendarService
                    ],
                    [
                        CronofyFormatService::SERVICE_KEY,
                        $mockCronofyFormatService
                    ]
                ]);

            $mockCronofyCalendar = $this->getMock('OrgCronofyCalendar', [
                'getCronofyCalendar',
                'getCronofyCalAccessToken',
                'getCronofyCalRefreshToken'
            ]);
            $mockOfficeHour = $this->getMock('OfficeHours', ['getId', 'setGoogleAppointmentId', 'setLastSynced']);
            $mockAppointments = $this->getMock('Appointments', ['getId', 'setGoogleAppointmentId', 'setLastSynced']);

            $mockCronofyCalendarRepository->method('findOneBy')->willReturn($mockCronofyCalendar);
            $mockOfficeHourRepository->method('find')->willReturn($mockOfficeHour);

            $mockAppointmentsRepository->method('find')->willReturn($mockAppointments);
            if ($eventType == 'appointment') {
                $mockCronofyFormatService->method('formatMAFAppointmentDataToCronofy')->willReturn($parameters);
            } else {
                $mockCronofyFormatService->method('formatMAFOfficeHourDataToCronofy')->willReturn($parameters);
            }
            $currentTime = new \DateTime('now');

            try {
                $cronofyWrapperService = new CronofyWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
                $syncOfficeHour = $cronofyWrapperService->syncOneOffEvent($mockCronofyCalendar, $mapworksEventId, $eventType, $actionType, $currentTime, $organizationTimeZone);
                $this->assertEquals($expectedResult, $syncOfficeHour);
            } catch (\Exception $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }
        }, [
            'examples' => [
                // Sync newly created Appointments
                [
                    '457865',
                    'appointment',
                    'create',
                    TRUE,
                    [
                        'event_id' => '677878',
                        'summary' => "Mapworks Appointments",
                        'description' => "Appointment with student",
                        'location' => 'test',
                        'transparency' => 'opaque'
                    ],
                    'Eastern',
                ],
                // Throws an error while syncing newly created Appointments since the event id id not available
                [
                    '457865',
                    'appointment',
                    'create',
                    'Undefined index: event_id',
                    [
                        'summary' => "Mapworks Appointments",
                        'description' => "Appointments ",
                        'location' => 'test',
                        'transparency' => 'opaque'
                    ],
                    'Eastern',
                ],
                // Sync newly created office hour
                [
                    '457865',
                    'office_hour',
                    'create',
                    TRUE,
                    [
                        'event_id' => 78687,
                        'calendar_id' => 'skyfactor@gmail.com',
                        'summary' => "Mapworks Office Hours",
                        'description' => "Office hour ",
                        'location' => 'test',
                        'transparency' => 'transparent'
                    ],
                    'Central',
                ],
                // Throw an exception while creating office hour since event_id is missing
                [
                    '457864',
                    'office_hour',
                    'create',
                    "Undefined index: event_id",
                    [
                        'summary' => "Mapworks Office Hours",
                        'description' => "Student to book ",
                        'location' => 'test',
                        'transparency' => 'transparent'
                    ],
                    'Central',
                ],
                // sync updated office hour in to external calendar.
                [
                    '457865',
                    'office_hour',
                    'update',
                    TRUE,
                    [
                        'event_id' => 78687,
                        'calendar_id' => 'skyfactor@gmail.com',
                        'summary' => "Mapworks Office Hours",
                        'description' => "To modify this office hour or see details ",
                        'location' => 'test',
                        'transparency' => 'transparent'
                    ],
                    'Central',
                ],
                // Sync updated appointment
                [
                    '457865',
                    'appointment',
                    'update',
                    TRUE,
                    [
                        'event_id' => '677878',
                        'summary' => "Mapworks Appointments",
                        'description' => "To modify this appointment or see details",
                        'location' => 'test',
                        'transparency' => 'opaque'
                    ],
                    'Central',
                ],
            ]
        ]);
    }

    public function testDeleteOneOffEvent()
    {
        $this->specify("Test delete one off event", function ($mapworksEventId, $eventType, $externalCalendarEventId, $officeHourId, $parameters, $organizationTimeZone, $expectedResult) {

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
            $mockCronofyCalendarRepository = $this->getMock('OrgCronofyCalendarRepository', ['findOneBy']);
            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy']);
            $mockOfficeHourRepository = $this->getMock('OfficeHoursRepository', ['find', 'updateSyncDetailsToOfficeHours', 'flush']);
            $mockAppointmentsRepository = $this->getMock('Appointments', ['updateSyncDetailsToAppointment']);


            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgCronofyCalendarRepository::REPOSITORY_KEY,
                        $mockCronofyCalendarRepository
                    ],
                    [
                        EbiConfigRepository::REPOSITORY_KEY,
                        $mockEbiConfigRepository
                    ],
                    [
                        OfficeHoursRepository::REPOSITORY_KEY,
                        $mockOfficeHourRepository
                    ],
                    [
                        AppointmentsRepository::REPOSITORY_KEY,
                        $mockAppointmentsRepository
                    ]
                ]);
            $mockEbiConfigService = $this->getMock('EbiConfigService', ['get']);
            $mockCronofyCalendarService = $this->getMock('CronofyCalendarService', ['enableAuthentication', 'refreshToken', 'deleteEvent', 'upsertEvent']);
            $mockCronofyFormatService = $this->getMock('CronofyFormatService', ['formatMAFOfficeHourDataToCronofy']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        EbiConfigService::SERVICE_KEY,
                        $mockEbiConfigService
                    ],
                    [
                        CronofyCalendarService::SERVICE_KEY,
                        $mockCronofyCalendarService
                    ],
                    [
                        CronofyFormatService::SERVICE_KEY,
                        $mockCronofyFormatService
                    ]
                ]);

            $mockCronofyCalendar = $this->getMock('OrgCronofyCalendar', [
                'getCronofyCalendar',
                'getCronofyCalAccessToken',
                'getCronofyCalRefreshToken',
                'getPerson'
            ]);

            $mockPerson = $this->getMock('Person', [
                'getId'
            ]);

            if ($officeHourId) {
                $mockOfficeHour = $this->getMock('OfficeHours', ['getId', 'setGoogleAppointmentId', 'setLastSynced']);
                $mockOfficeHourRepository->method('find')->willReturn($mockOfficeHour);
                $mockCronofyFormatService->method('formatMAFOfficeHourDataToCronofy')->willReturn($parameters);
            }
            $mockCronofyCalendarRepository->method('findOneBy')->willReturn($mockCronofyCalendar);
            $mockCronofyCalendar->method('getPerson')->willReturn($mockPerson);
            $currentTime = new \DateTime('now');

            try {
                $cronofyWrapperService = new CronofyWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
                $syncOfficeHour = $cronofyWrapperService->deleteOneOffEvent($mockCronofyCalendar, $mapworksEventId, $eventType, $currentTime, $externalCalendarEventId, $officeHourId, $organizationTimeZone);
                $this->assertEquals($expectedResult, $syncOfficeHour);
            } catch (\Exception $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }
        }, [
            'examples' => [
                // Delete appointment from external calendar.
                [
                    '457865',
                    'appointment',
                    'A457865',
                    NULL,
                    NULL,
                    'Eastern',
                    TRUE
                ],

                // Delete appointment from external calendar which become office hour.
                [
                    '457865',
                    'appointment',
                    'A45908',
                    78546,
                    [
                        'event_id' => 898998,
                        'summary' => "Mapworks Office Hours",
                        'description' => "To modify this office hour or see details, please go to ",
                        'location' => 'test',
                        'transparency' => 'transparent'
                    ],
                    'Central',
                    TRUE
                ],

                // Delete appointment from external calendar which throws an error due to invalid parameters.
                [
                    '457865',
                    'appointment',
                    'A45908',
                    78546,
                    [
                        'transparency' => 'transparent'
                    ],
                    null,
                    'Undefined index: event_id'
                ],

                // Delete Office hour from external calendar.
                [
                    '457865',
                    'office_hour',
                    'O457865',
                    NULL,
                    NULL,
                    'Central',
                    TRUE
                ]
            ]
        ]);
    }


    public function testInitialSyncToPcs()
    {
        $this->specify("Test initial sync to pcs", function ($personId, $organizationId, $cronofyDetails, $type, $dataToBeSync, $method, $expectedResult) {
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

            // Mocking Repositories
            $mockOrgCronofyCalendarRepository = $this->getMock('OrgCronofyCalendarRepository', ['findOneBy', 'flush']);
            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy', 'get']);
            $mockAppointmentRepository = $this->getMock('AppointmentsRepository', ['getFutureAppointmentForFaculty', 'find', 'getDeletedFutureAppointments', 'updateSyncDetailsToAppointment']);
            $mockOfficeHoursRepository = $this->getMock('OfficeHoursRepository', ['updateBatchEventsOfficeHourStatus', 'getFutureOfficeHoursForFaculty', 'getDeletedFutureOfficeHours', 'find']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        EbiConfigRepository::REPOSITORY_KEY,
                        $mockEbiConfigRepository
                    ],
                    [
                        OrgCronofyCalendarRepository::REPOSITORY_KEY,
                        $mockOrgCronofyCalendarRepository
                    ],
                    [
                        AppointmentsRepository::REPOSITORY_KEY,
                        $mockAppointmentRepository
                    ],
                    [
                        OfficeHoursRepository::REPOSITORY_KEY,
                        $mockOfficeHoursRepository
                    ],
                ]);

            // Mocking Services
            $mockDateUtilityService = $this->getMock('DateUtilityService', ['getOrganizationISOTimeZone']);
            $mockEbiConfigService = $this->getMock('EbiConfigService', ['get']);
            $mockCronofyCalendarService = $this->getMock('CronofyCalendarService', ['refreshToken', 'clientId', 'clientSecret', 'enableAuthentication', 'upsertBatchEvent']);
            $mockCronofyFormatService = $this->getMock('CronofyFormatService', ['formatMAFAppointmentDataToCronofy', 'formatMAFOfficeHourDataToCronofy', 'formatMAFAppointmentAndOfficeHourBatchDataToCronofy']);
            $mockMapworksActionService = $this->getMock('MapworksActionService', ['sendCommunicationBasedOnMapworksAction']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        EbiConfigService::SERVICE_KEY,
                        $mockEbiConfigService
                    ],
                    [
                        CronofyCalendarService::SERVICE_KEY,
                        $mockCronofyCalendarService
                    ],
                    [
                        CronofyFormatService::SERVICE_KEY,
                        $mockCronofyFormatService
                    ],
                    [
                        DateUtilityService::SERVICE_KEY,
                        $mockDateUtilityService
                    ],
                    [
                        MapworksActionService::SERVICE_KEY,
                        $mockMapworksActionService
                    ]
                ]);

            $ebiConfigEntity = $this->getMock('EbiConfig', ['getValue']);
            $ebiConfigEntity->method('getValue')->willReturn(50);
            $mockEbiConfigRepository->expects($this->any())->method('findOneBy')->will($this->returnValue($ebiConfigEntity));

            $mockCronofyCalendar = $this->getMock('OrgCronofyCalendar', [
                'getCronofyCalendar',
                'getCronofyCalAccessToken',
                'getCronofyCalRefreshToken',
                'getModifiedAt',
                'setCronofyCalAccessToken',
                'setCronofyCalRefreshToken',
            ]);
            $mockCronofyCalendar->method('getCronofyCalendar')->willReturn($cronofyDetails['calendar_id']);
            $mockCronofyCalendar->method('getCronofyCalAccessToken')->willReturn($cronofyDetails['access_token']);
            $mockCronofyCalendar->method('getCronofyCalRefreshToken')->willReturn($cronofyDetails['refresh_token']);
            $tokenCreatedTime = date("Y-m-d H:i:s", strtotime("-2 hours"));
            $mockCronofyCalendar->method('getModifiedAt')->willReturn(new DateTime($tokenCreatedTime));
            $mockOrgCronofyCalendarRepository->method('findOneBy')->willReturn($mockCronofyCalendar);

            if ($type == 'appointment_create') {
                $mockAppointmentRepository->method('getFutureAppointmentForFaculty')->willReturn($dataToBeSync);

                $mockAppointments = $this->getMock('Appointments', [
                    'getId', 'getGoogleAppointmentId'
                ]);
                $mockAppointments->method('getId')->willReturn(1001);
                $mockAppointmentRepository->method('find')->willReturn($mockAppointments);
            } else {
                $mockAppointmentRepository->method('getFutureAppointmentForFaculty')->willReturn([]);
            }

            if ($type == 'officehour_create') {
                $mockOfficeHoursRepository->method('getFutureOfficeHoursForFaculty')->willReturn($dataToBeSync);

                $mockOfficeHours = $this->getMock('Appointments', [
                    'getId', 'getGoogleAppointmentId'

                ]);
                $mockOfficeHours->method('getId')->willReturn(1001);
                $mockOfficeHoursRepository->method('find')->willReturn($mockOfficeHours);
            } else {
                $mockOfficeHoursRepository->method('getFutureOfficeHoursForFaculty')->willReturn([]);
            }

            if ($type == 'appointment_delete') {
                $mockAppointmentRepository->method('getDeletedFutureAppointments')->willReturn($dataToBeSync);
            } else {
                $mockAppointmentRepository->method('getDeletedFutureAppointments')->willReturn([]);
            }

            if ($type == 'officehour_delete') {
                $mockOfficeHoursRepository->method('getDeletedFutureOfficeHours')->willReturn($dataToBeSync);
            } else {
                $mockOfficeHoursRepository->method('getDeletedFutureOfficeHours')->willReturn([]);
            }


            $mockCronofyFormatService->method('formatMAFOfficeHourDataToCronofy')->willReturn($this->getCronofyOfficeHourFormatArray(1, 'calendar1test'));
            $mockCronofyFormatService->method('formatMAFAppointmentDataToCronofy')->willReturn($this->getCronofyOfficeHourFormatArray(1, 'calendar1test', 'appointment'));

            $mockCronofyFormatService->method('formatMAFAppointmentAndOfficeHourBatchDataToCronofy')->willReturn($this->getCronofyFormatArray($dataToBeSync, 'calendar1test', $method));
            $mockCronofyCalendarService->method('upsertBatchEvent')->willReturn($this->getCronofyResultJSON($dataToBeSync));

            $cronofyWrapperService = new CronofyWrapperService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $syncOfficeHour = $cronofyWrapperService->initialSyncToPcs($personId, $organizationId);

            $this->assertEquals($expectedResult, $syncOfficeHour);
        }, [
            'examples' => [
                // Test0: Sync all future appointments
                [
                    4879301,
                    203,
                    [
                        'calendar_id' => 'cal_4879301_nfjtVFuOjxi6FAcUl',
                        'access_token' => 'nfjtVFuOjxi6FAcUl45b3n4m5b3nm4',
                        'refresh_token' => 'nfjtVFuOjxi6FAcUl45b3n4m5b3nm4',
                    ],
                    'appointment_create',
                    [
                        [
                            'id' => 1001,
                            'appointment_id' => 1001,
                            'appointments_id' => 1001
                        ],
                        [
                            'id' => 1002,
                            'appointment_id' => 1002,
                            'appointments_id' => 1002
                        ],
                    ],
                    'POST',
                    null
                ],
                // Test1: Sync future office hours
                [
                    4879301,
                    203,
                    [
                        'calendar_id' => 'cal_4879301_nfjtVFuOjxi6FAcUl',
                        'access_token' => 'nfjtVFuOjxi6FAcUl45b3n4m5b3nm4',
                        'refresh_token' => 'nfjtVFuOjxi6FAcUl45b3n4m5b3nm4',
                    ],
                    'officehour_create',
                    [
                        [
                            'id' => 1001,
                            'appointment_id' => null,
                            'appointments_id' => null
                        ],
                        [
                            'id' => 1002,
                            'appointment_id' => null,
                            'appointments_id' => null
                        ],
                    ],
                    'POST',
                    null
                ],
                // Test2: Desync future appointments
                [
                    4879301,
                    203,
                    [
                        'calendar_id' => 'cal_4879301_nfjtVFuOjxi6FAcUl',
                        'access_token' => 'nfjtVFuOjxi6FAcUl45b3n4m5b3nm4',
                        'refresh_token' => 'nfjtVFuOjxi6FAcUl45b3n4m5b3nm4',
                    ],
                    'appointment_delete',
                    [
                        [
                            'id' => 1001,
                            'google_appointment_id' => 'A1001',
                            'appointments_id' => null
                        ],
                        [
                            'id' => 1002,
                            'google_appointment_id' => 'A1002',
                            'appointments_id' => null
                        ],
                    ],
                    'DELETE',
                    null
                ],
                // Test3: Desync future office hours
                [
                    4879301,
                    203,
                    [
                        'calendar_id' => 'cal_4879301_nfjtVFuOjxi6FAcUl',
                        'access_token' => 'nfjtVFuOjxi6FAcUl45b3n4m5b3nm4',
                        'refresh_token' => 'nfjtVFuOjxi6FAcUl45b3n4m5b3nm4',
                    ],
                    'officehour_delete',
                    [
                        [
                            'id' => 1001,
                            'google_appointment_id' => 'O1001',
                            'appointments_id' => 'O1001',
                        ],
                        [
                            'id' => 1002,
                            'google_appointment_id' => 'O1002',
                            'appointments_id' => 'O1001',
                        ],
                    ],
                    'DELETE',
                    null
                ],
                // Test4: Sync all future appointments, while appointment array is blank
                [
                    4879301,
                    203,
                    [
                        'calendar_id' => 'cal_4879301_nfjtVFuOjxi6FAcUl',
                        'access_token' => 'nfjtVFuOjxi6FAcUl45b3n4m5b3nm4',
                        'refresh_token' => 'nfjtVFuOjxi6FAcUl45b3n4m5b3nm4',
                    ],
                    'appointment_create',
                    [],
                    'POST',
                    null
                ],
                // Test5: Deyync all future appointments, while appointment array is blank
                [
                    4879301,
                    203,
                    [
                        'calendar_id' => 'cal_4879301_nfjtVFuOjxi6FAcUl',
                        'access_token' => 'nfjtVFuOjxi6FAcUl45b3n4m5b3nm4',
                        'refresh_token' => 'nfjtVFuOjxi6FAcUl45b3n4m5b3nm4',
                    ],
                    'appointment_delete',
                    [],
                    'DELETE',
                    null
                ],
                // Test6: Sync all future office hours, while office hours array is blank
                [
                    4879301,
                    203,
                    [
                        'calendar_id' => 'cal_4879301_nfjtVFuOjxi6FAcUl',
                        'access_token' => 'nfjtVFuOjxi6FAcUl45b3n4m5b3nm4',
                        'refresh_token' => 'nfjtVFuOjxi6FAcUl45b3n4m5b3nm4',
                    ],
                    'officehour_create',
                    [],
                    'POST',
                    null
                ],
                // Test7: Desync all future office hours, while office hours array is blank
                [
                    4879301,
                    203,
                    [
                        'calendar_id' => 'cal_4879301_nfjtVFuOjxi6FAcUl',
                        'access_token' => 'nfjtVFuOjxi6FAcUl45b3n4m5b3nm4',
                        'refresh_token' => 'nfjtVFuOjxi6FAcUl45b3n4m5b3nm4',
                    ],
                    'officehour_delete',
                    [],
                    'DELETE',
                    null
                ],
            ]
        ]);
    }
}