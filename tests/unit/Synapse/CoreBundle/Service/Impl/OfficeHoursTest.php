<?php
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CalendarBundle\Service\Impl\CalendarFactoryService;
use Synapse\CalendarBundle\Service\Impl\CalendarIntegrationService;
use Synapse\CalendarBundle\Service\Impl\CalendarWrapperService;
use Synapse\CoreBundle\Entity\OfficeHoursSeries;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Repository\AppointmentsRepository;
use Synapse\CoreBundle\Repository\OfficeHoursRepository;
use Synapse\CoreBundle\Repository\OfficeHoursSeriesRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\OfficeHoursService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\RestBundle\Entity\OfficeHoursDto;
use Synapse\RestBundle\Entity\OfficeHoursSeriesDto;

class OfficeHoursTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    private $organizationId = 1;

    private $officeHourId = 1;

    private $calendarSettings = [
        'facultyMAFTOPCS' => 'y',
        'googleClientId' => null
    ];

    public function testDeleteOfficeHour()
    {
        $this->specify("Delete office hour", function ($officeHourId, $organizationId)
        {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockManager = $this->getMock('Manager', array(
                'validateUserAsAuthorizedAppointmentUser'
            ));

            $mockCalendarIntegrationService = $this->getMock('CalendarIntegrationService', array('facultyCalendarSettings', 'syncOneOffEvent'));
            $mockCalendarWrapperService = $this->getMock('CalendarWrapperService', array('syncPCS'));
            
            $mockOfficeHoursRepository = $this->getMock('OfficehoursRepository', array(
                'findOneBy',
                'remove',
                'flush'
            ));
            $mockAppointmentsRepository = $this->getMock('AppointmentsRepository', array(
                'find'
            ));
            
            $mockRepositoryResolver->expects($this->any())
                ->method('getRepository')
                ->willReturnMap([
                [
                    AppointmentsRepository::REPOSITORY_KEY,
                    $mockAppointmentsRepository
                ],
                [
                    OfficeHoursRepository::REPOSITORY_KEY,
                    $mockOfficeHoursRepository
                ]
            ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [CalendarIntegrationService::SERVICE_KEY, $mockCalendarIntegrationService],
                    [CalendarWrapperService::SERVICE_KEY, $mockCalendarWrapperService],
                    [Manager::SERVICE_KEY, $mockManager]
                ]);

            $calendarSettings = [];
            $calendarSettings['facultyMAFTOPCS'] = 'y';
            $calendarSettings['google_sync_status'] = true;
            $mockCalendarIntegrationService->method('facultyCalendarSettings')->willReturn($calendarSettings);

            $mockCalendarWrapperService->method('syncPCS')->willReturn(1);

            $mockPersonObject = $this->getMock('Person',array('getId'));
            $mockPersonObject->method('getId')->willReturn(1);

            // Office Hours Mock Object
            $mockOfficeHourObject = $this->getMock('OfficeHours', array(
                'getAppointments',
                'getPerson',
                'getGoogleAppointmentId',
                'getId'
            ));

            $mockOfficeHourObject->method('getPerson')->willReturn($mockPersonObject);
            $mockOfficeHourObject->method('getGoogleAppointmentId')->willReturn('test');
            $mockOfficeHourObject->method('getId')->willReturn(1);

            $mockOfficeHoursRepository->expects($this->once())
                ->method('findOneBy')
                ->will($this->returnValue($mockOfficeHourObject));
            
            // Appointment Mock Object
            $mockAppointmentObject = $this->getMock('Appointments', array(
                'setIsFreeStanding'
            ));
            
            $mockOfficeHourObject->expects($this->any())
                ->method('getAppointments')
                ->will($this->returnValue($mockAppointmentObject));
            
            $officeHoursService = new OfficeHoursService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $officeHoursService->deleteOfficeHour($officeHourId, $organizationId);
        }, [
            'examples' => [
                [
                    $this->officeHourId,
                    $this->organizationId
                ]
            ]
        ]);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testValidateData()
    {
        $this->specify("Validate Office hour data", function ($personId, $startDate, $endDate, $repeatRange) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            //Mock a Logger
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            // Mock a Container
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $personService = $this->getMock('personService', array(
                'findPerson'
            ));
            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        'person_service',
                        $personService
                    ]
                ]);
            $mockPersonObject = $this->getMock('person', array('getOrganization', 'findPerson'));
            $mockOrganization = $this->getMock('Organization', array('getOrganization', 'getTimeZone'));
            $personService->expects($this->any())->method('findPerson')->willReturn($mockPersonObject);
            $mockPersonObject->expects($this->any())->method('getOrganization')->willReturn($mockOrganization);
            $mockOrganization->expects($this->any())->method('getTimeZone')->willReturn('UTC');
			
            $mockMetadataListRepository = $this->getMock('metadataListRepository', array('findByListName'));
            $mockRepositoryResolver->method('getRepository')->willReturnMap([['SynapseCoreBundle:MetadataListValues', $mockMetadataListRepository]]);
            $mockTimeZone = array($this->getMock('timezone', array('getListValue')));
            $mockMetadataListRepository->expects($this->any())->method('findByListName')->willReturn($mockTimeZone);
            $mockTimeZone[0]->expects($this->any())->method('getListValue')->willReturn('UTC');

            $officeHoursDto = $this->createOfficeHour($personId, $startDate, $endDate, $repeatRange);
            $officeHoursService = new OfficeHoursService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $officeHoursService->validateData($officeHoursDto);
        }, [
            'examples' => [
                [4878808, '2016-11-25T21:00:00+0530', '2017-04-30T21:30:00+0530', 'D'],
                [4878809, '2016-11-25T21:00:00+0530', '2017-04-30T21:30:00+0530', 'N'],
                [4878810, '2016-11-25T21:00:00+0530', '2017-04-30T21:30:00+0530', 'W'],
                [4878810, '2016-11-25T21:00:00+0530', '2015-04-30T21:30:00+0530', 'W'],
            ]
        ]);
    }

    private function createOfficeHour($personId, $startDate, $endDate, $repeatRange, $seriesDetails = [], $officeHourId = null)
    {
        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);

        $officeHoursDto = new OfficeHoursDto();
        if ($officeHourId) {
            $officeHoursDto->setOfficeHoursId($officeHourId);
        }
        $officeHoursDto->setPersonId($personId);
        $officeHoursDto->setOrganizationId($this->organizationId);
        $officeHoursDto->setPersonIdProxy(null);
        $officeHoursDto->setSlotType('S');
        $officeHoursDto->setLocation('Bengaluru, India');
        $officeHoursDto->setSlotStart($startDate);
        $officeHoursDto->setSlotEnd($endDate);

        $seriesInfo = new OfficeHoursSeriesDto();
        $seriesInfo->setRepeatRange($repeatRange);
        if (!empty($seriesDetails)) {
            $seriesInfo->setMeetingLength($seriesDetails['meeting_length']);
            $seriesInfo->setRepeatPattern($seriesDetails['repeat_pattern']);
            $seriesInfo->setRepeatEvery($seriesDetails['repeat_every']);
            $seriesInfo->setRepeatDays($seriesDetails['repeat_days']);
            $seriesInfo->setRepeatMonthlyOn($seriesDetails['repeat_monthly_on']);
            $seriesInfo->setRepeatOccurence($seriesDetails['repeat_occurrence']);
            $seriesInfo->setIncludeSatSun($seriesDetails['include_sat_sun']);
        }

        $officeHoursDto->setSeriesInfo($seriesInfo);
        $oneToSeries = false;
        if (array_key_exists('one_to_series', $seriesDetails)) {
            $oneToSeries = true;
        }
        $officeHoursDto->setOneToSeries($oneToSeries);

        return $officeHoursDto;
    }

    private function getPersonInstance($id)
    {
        $person = new \Synapse\CoreBundle\Entity\Person();
        $person->setId($id);
        $person->setOrganization($this->getOrganizationInstance());
        $person->setFirstname('Test-Firstname');
        $person->setLastname('Test-Lastname');
        return $person;
    }

    private function getOrganizationInstance()
    {
        $organization = new \Synapse\CoreBundle\Entity\Organization();
        $organization->setCampusId(2);
        return $organization;
    }

    private function getOfficeHoursSeriesInstance($officeHoursSeriesId)
    {
        $officeHoursSeries = new \Synapse\CoreBundle\Entity\OfficeHoursSeries();
        $officeHoursSeries->setId($officeHoursSeriesId);
        $officeHoursSeries->setPerson($this->getPersonInstance(1));
        $officeHoursSeries->setOrganization($this->getOrganizationInstance());

        return $officeHoursSeries;
    }


    public function testIsOfficeHoursSeriesOverlap()
    {
        $this->specify("Test Office Hours Series Overlap", function ($startDate, $endDate, $allFutureSlotDates, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            //Mock a Logger
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            // Mock a Container
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $startTime = new DateTime($startDate);
            $endTime = new DateTime($endDate);

            $officeHoursService = new OfficeHoursService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $isOverlap = $officeHoursService->isOfficeHoursSeriesOverlap($startTime, $endTime, $allFutureSlotDates);

            $this->assertEquals($isOverlap, $expectedResult);

        }, [
            'examples' =>
                [
                    // Test0: start_date = slot_start and end_date > slot_end, returns true as overlap scenario
                    [
                        '2017-09-28 14:00:00',
                        '2017-09-28 14:45:00',
                        [
                            [
                                'slot_start' => '2017-09-28 14:00:00',
                                'slot_end' => '2017-09-28 14:30:00',
                            ]
                        ],
                        true
                    ],
                    // Test1: start_date > slot_start and end_date = slot_end, returns true as overlap scenario
                    [
                        '2017-09-28 14:15:00',
                        '2017-09-28 14:30:00',
                        [
                            [
                                'slot_start' => '2017-09-28 14:00:00',
                                'slot_end' => '2017-09-28 14:30:00',
                            ]
                        ],
                        true
                    ],
                    // Test2: start_date > slot_start and start_date < slot_end and end_date > slot_end, returns true as overlap scenario
                    [
                        '2017-09-28 14:15:00',
                        '2017-09-28 14:45:00',
                        [
                            [
                                'slot_start' => '2017-09-28 14:00:00',
                                'slot_end' => '2017-09-28 14:30:00',
                            ]
                        ],
                        true
                    ],
                    // Test3: start_date < slot_start and end_date > slot_end, returns true as overlap scenario
                    [
                        '2017-09-28 14:00:00',
                        '2017-09-28 15:00:00',
                        [
                            [
                                'slot_start' => '2017-09-28 14:15:00',
                                'slot_end' => '2017-09-28 14:30:00',
                            ]
                        ],
                        true
                    ],
                    // Test4: start_date < slot_start and end_date < slot_start, returns false as overlap scenario
                    [
                        '2017-09-28 13:30:00',
                        '2017-09-28 14:00:00',
                        [
                            [
                                'slot_start' => '2017-09-28 14:00:00',
                                'slot_end' => '2017-09-28 14:30:00',
                            ]
                        ],
                        false
                    ],
                    // Test5: start_date > slot_end and end_date > slot_end, returns false as overlap scenario
                    [
                        '2017-09-28 14:30:00',
                        '2017-09-28 15:00:00',
                        [
                            [
                                'slot_start' => '2017-09-28 14:00:00',
                                'slot_end' => '2017-09-28 14:30:00',
                            ]
                        ],
                        false
                    ],
                ]
            ]
        );
    }


    public function testCreateOfficeHourSeries()
    {
        $this->specify("Test Create Office Hours Series", function ($personId, $startDate, $endDate, $seriesDetails,
                                                                    $currentAcademicYearEndDate, $allFutureSlotDates,
                                                                    $errorType, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            //Mock a Logger
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            // Mock a Container
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            // Mocking Repositories
            $mockPersonRepository = $this->getMock('PersonRepository', ['find']);
            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', ['getCurrentAcademicDetails']);
            $mockOfficeHoursSeriesRepository = $this->getMock('OfficeHoursSeriesRepository', ['persist', 'flush']);
            $mockOfficeHoursRepository = $this->getMock('OfficeHoursRepository', ['getAllOfficeHourSlots', 'createOfficeHours']);

            // Mocking Services
            $mockDateUtilityService = $this->getMock('DateUtilityService', ['getOrganizationISOTimeZone', 'getDaylightSavingsTimeOffsetAdjustment', 'getFirstDayOfWeekForDatetime']);
            $mockPersonService = $this->getMock('PersonService', ['findPerson']);
            $mockCalendarIntegrationService = $this->getMock('CalendarIntegrationService', ['facultyCalendarSettings', 'syncOfficeHourSeries']);
            $mockCalendarFactoryService = $this->getMock('CalendarFactoryService', ['syncPCS']);

            // Scaffolding for all Repositories
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [OrgAcademicYearRepository::REPOSITORY_KEY, $mockOrgAcademicYearRepository],
                    [OfficeHoursSeriesRepository::REPOSITORY_KEY, $mockOfficeHoursSeriesRepository],
                    [OfficeHoursRepository::REPOSITORY_KEY, $mockOfficeHoursRepository],
                ]);

            // Scaffolding for service
            $mockContainer->method('get')->willReturnMap(
                [
                    [PersonService::SERVICE_KEY, $mockPersonService],
                    [DateUtilityService::SERVICE_KEY, $mockDateUtilityService],
                    [CalendarIntegrationService::SERVICE_KEY, $mockCalendarIntegrationService],
                    [CalendarFactoryService::SERVICE_KEY, $mockCalendarFactoryService],
                ]);

            $mockPerson = $this->getPersonInstance($personId);
            if ($errorType == 'person_not_found') {
                $mockPersonRepository->method('find')->willReturn(null);
            } else {
                $mockPersonRepository->method('find')->willReturn($mockPerson);
            }

            $mockOrgAcademicYearRepository->method('getCurrentAcademicDetails')->willReturn([$currentAcademicYearEndDate]);

            $mockPersonService->method('findPerson')->willReturn(null);
            $mockDateUtilityService->method('getOrganizationISOTimeZone')->willReturn('Asia/Calcutta');
            $mockDateUtilityService->method('getDaylightSavingsTimeOffsetAdjustment')->willReturn(0);

            // Getting start date of the week
            $currentDate = new DateTime($startDate);
            $currentDate->sub(new \DateInterval('P' . $currentDate->format('w') . 'D'));
            $mockDateUtilityService->method('getFirstDayOfWeekForDatetime')->willReturn($currentDate);

            $mockOfficeHoursRepository->method('getAllOfficeHourSlots')->willReturn($allFutureSlotDates);

            $mockCalendarIntegrationService->method('facultyCalendarSettings')->willReturn($this->calendarSettings);

            $repeatRange = $seriesDetails['repeat_range'];
            $officeHoursDTO = $this->createOfficeHour($personId, $startDate, $endDate, $repeatRange, $seriesDetails);

            try {
                $officeHoursService = new OfficeHoursService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $officeHoursService->createOfficeHourSeries($officeHoursDTO);

                $this->assertEquals($expectedResult, $result);

            } catch (SynapseException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
                'examples' =>
                    [
                        // Test0: Person not found and throws exception - dates hardcoded
                        [
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 30,
                                'repeat_pattern' => "D",
                                'repeat_every' => 1,
                                'repeat_days' => "0000000",
                                'repeat_range' => "D",
                                'repeat_monthly_on' => 0,
                                'repeat_occurrence' => 0,
                                'include_sat_sun' => 0,
                            ],
                            [],
                            [],
                            'person_not_found',
                            'This person does not exist'
                        ],
                        // Test1: Invalid End Slot Date and throws exception - dates hardcoded
                        [
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-07-01 11:30:00',
                            [
                                'meeting_length' => 30,
                                'repeat_pattern' => "D",
                                'repeat_every' => 1,
                                'repeat_days' => "0000000",
                                'repeat_range' => "D",
                                'repeat_monthly_on' => 0,
                                'repeat_occurrence' => 0,
                                'include_sat_sun' => 0,
                            ],
                            [
                                'endDate' => date('Y-12-31')
                            ],
                            [],
                            '',
                            'Invalid End Slot Date'
                        ],
                        // Test2: Invalid Start Slot Date and throws exception - dates hardcoded
                        [
                            4878808,
                            '1985-12-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 30,
                                'repeat_pattern' => "D",
                                'repeat_every' => 1,
                                'repeat_days' => "0000000",
                                'repeat_range' => "D",
                                'repeat_monthly_on' => 0,
                                'repeat_occurrence' => 0,
                                'include_sat_sun' => 0,
                            ],
                            [
                                'endDate' => date('Y-12-31')
                            ],
                            [],
                            '',
                            'Invalid Start Slot Date'
                        ],
                        // Test3: Series for Daily - Single slot - End date and Test for overlap scenario and throws exception
                        [
                            4878808,
                            '2100-08-01 11:45:00',
                            '2100-10-01 12:45:00',
                            [
                                'meeting_length' => 30,
                                'repeat_pattern' => "D",
                                'repeat_every' => 1,
                                'repeat_days' => "0000000",
                                'repeat_range' => "D",
                                'repeat_monthly_on' => 0,
                                'repeat_occurrence' => 0,
                                'include_sat_sun' => 1,
                            ],
                            [
                                'endDate' => '2100-12-01 00:00:00'
                            ],
                            [
                                [
                                    "slot_start" => '2100-08-01 12:15:00',
                                    "slot_end" => '2100-08-01 12:30:00',
                                ],
                                [
                                    "slot_start" => '2100-10-01 12:00:00',
                                    "slot_end" => '2100-10-01 12:30:00',
                                ],
                            ],
                            '',
                            'Office hours cannot overlap'
                        ],
                        // Test4: Series for Daily - Multiple slot - End date
                        [
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 15,
                                'repeat_pattern' => "D",
                                'repeat_every' => 1,
                                'repeat_days' => "0000000",
                                'repeat_range' => "D",
                                'repeat_monthly_on' => 0,
                                'repeat_occurrence' => 0,
                                'include_sat_sun' => 0,
                            ],
                            [
                                'endDate' => '2100-12-01 00:00:00'
                            ],
                            [
                                [
                                    "slot_start" => '2100-08-01 10:15:00',
                                    "slot_end" => '2100-08-01 11:30:00',
                                ],
                                [
                                    "slot_start" => '2100-10-01 10:00:00',
                                    "slot_end" => '2100-10-01 11:30:00',
                                ],
                            ],
                            '',
                            $this->createOfficeHour(
                                4878808,
                                '2100-08-01 12:00:00',
                                '2100-10-01 12:30:00',
                                "D",
                                [
                                    'meeting_length' => 15,
                                    'repeat_pattern' => "D",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0000000",
                                    'repeat_range' => "D",
                                    'repeat_monthly_on' => 0,
                                    'repeat_occurrence' => 0,
                                    'include_sat_sun' => 0,
                                ]
                            )
                        ],
                        // Test5: Series for Daily - Multiple slot - End after X occurrence
                        [
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 15,
                                'repeat_pattern' => "D",
                                'repeat_every' => 1,
                                'repeat_days' => "0000000",
                                'repeat_range' => "E",
                                'repeat_monthly_on' => 0,
                                'repeat_occurrence' => "50",
                                'include_sat_sun' => 0,
                            ],
                            [
                                'endDate' => '2100-12-01 00:00:00'
                            ],
                            [
                                [
                                    "slot_start" => '2100-08-01 10:15:00',
                                    "slot_end" => '2100-08-01 11:30:00',
                                ],
                                [
                                    "slot_start" => '2100-10-01 10:00:00',
                                    "slot_end" => '2100-10-01 11:30:00',
                                ],
                            ],
                            '',
                            $this->createOfficeHour(
                                4878808,
                                '2100-08-01 12:00:00',
                                '2100-10-01 12:30:00',
                                "E",
                                [
                                    'meeting_length' => 15,
                                    'repeat_pattern' => "D",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0000000",
                                    'repeat_range' => "E",
                                    'repeat_monthly_on' => 0,
                                    'repeat_occurrence' => "50",
                                    'include_sat_sun' => 0,
                                ]
                            )
                        ],
                        // Test6: Series for Weekly - Multiple slot - End after X occurrence
                        [
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 15,
                                'repeat_pattern' => "W",
                                'repeat_every' => 1,
                                'repeat_days' => "0111110",
                                'repeat_range' => "E",
                                'repeat_monthly_on' => 0,
                                'repeat_occurrence' => "50",
                                'include_sat_sun' => 0,
                            ],
                            [
                                'endDate' => '2100-12-01 00:00:00'
                            ],
                            [
                                [
                                    "slot_start" => '2100-08-01 10:15:00',
                                    "slot_end" => '2100-08-01 11:30:00',
                                ],
                                [
                                    "slot_start" => '2100-10-01 10:00:00',
                                    "slot_end" => '2100-10-01 11:30:00',
                                ],
                            ],
                            '',
                            $this->createOfficeHour(
                                4878808,
                                '2100-08-01 12:00:00',
                                '2100-10-01 12:30:00',
                                "E",
                                [
                                    'meeting_length' => 15,
                                    'repeat_pattern' => "W",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0111110",
                                    'repeat_range' => "E",
                                    'repeat_monthly_on' => 0,
                                    'repeat_occurrence' => "50",
                                    'include_sat_sun' => 0,
                                ]
                            )
                        ],
                        // Test7: Series for Weekly - Multiple slot - End date
                        [
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 15,
                                'repeat_pattern' => "W",
                                'repeat_every' => 1,
                                'repeat_days' => "0111110",
                                'repeat_range' => "D",
                                'repeat_monthly_on' => 0,
                                'repeat_occurrence' => "0",
                                'include_sat_sun' => 0,
                            ],
                            [
                                'endDate' => '2100-12-01 00:00:00'
                            ],
                            [
                                ["slot_start" => '2100-08-19 13:30:00',
                                    "slot_end" => '2100-08-19 14:00:00'
                                ],
                                ["slot_start" => '2100-10-19 13:30:00',
                                    "slot_end" => '2100-10-19 14:00:00'
                                ],
                            ],
                            '',
                            $this->createOfficeHour(
                                4878808,
                                '2100-08-01 12:00:00',
                                '2100-10-01 12:30:00',
                                "D",
                                [
                                    'meeting_length' => 15,
                                    'repeat_pattern' => "W",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0111110",
                                    'repeat_range' => "D",
                                    'repeat_monthly_on' => 0,
                                    'repeat_occurrence' => "0",
                                    'include_sat_sun' => 0,
                                ]
                            )
                        ],
                        // Test8: Series for Monthly - Multiple slot - End date
                        [
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 15,
                                'repeat_pattern' => "M",
                                'repeat_every' => 1,
                                'repeat_days' => "0111110",
                                'repeat_range' => "D",
                                'repeat_monthly_on' => 1,
                                'repeat_occurrence' => "0",
                                'include_sat_sun' => 0,
                            ],
                            [
                                'endDate' => '2100-12-01 00:00:00'
                            ],
                            [
                                [
                                    "slot_start" => '2100-08-01 10:15:00',
                                    "slot_end" => '2100-08-01 11:30:00',
                                ],
                                [
                                    "slot_start" => '2100-10-01 10:00:00',
                                    "slot_end" => '2100-10-01 11:30:00',
                                ],
                            ],
                            '',
                            $this->createOfficeHour(
                                4878808,
                                '2100-08-01 12:00:00',
                                '2100-10-01 12:30:00',
                                "D",
                                [
                                    'meeting_length' => 15,
                                    'repeat_pattern' => "M",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0111110",
                                    'repeat_range' => "D",
                                    'repeat_monthly_on' => 1,
                                    'repeat_occurrence' => "0",
                                    'include_sat_sun' => 0,
                                ]
                            )
                        ],
                        // Test9: Series for Monthly - Multiple slot - End X occurrence
                        [
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 15,
                                'repeat_pattern' => "M",
                                'repeat_every' => 1,
                                'repeat_days' => "0111110",
                                'repeat_range' => "E",
                                'repeat_monthly_on' => 1,
                                'repeat_occurrence' => "50",
                                'include_sat_sun' => 0,
                            ],
                            [
                                'endDate' => '2100-12-01 00:00:00'
                            ],
                            [
                                [
                                    "slot_start" => '2100-08-01 10:15:00',
                                    "slot_end" => '2100-08-01 11:30:00',
                                ],
                                [
                                    "slot_start" => '2100-10-01 10:00:00',
                                    "slot_end" => '2100-10-01 11:30:00',
                                ],
                            ],
                            '',
                            $this->createOfficeHour(
                                4878808,
                                '2100-08-01 12:00:00',
                                '2100-10-01 12:30:00',
                                "E",
                                [
                                    'meeting_length' => 15,
                                    'repeat_pattern' => "M",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0111110",
                                    'repeat_range' => "E",
                                    'repeat_monthly_on' => 1,
                                    'repeat_occurrence' => "50",
                                    'include_sat_sun' => 0,
                                ]
                            )
                        ],
                        // Create monthly series for december - if there is no events for december then the event start date should be next year January.
                        [
                            4878808,
                            '2100-12-16 12:00:00',
                            '2101-12-16 12:30:00',
                            [
                                'meeting_length' => 30,
                                'repeat_pattern' => "M",
                                'repeat_every' => 1,
                                'repeat_days' => "0000011",
                                'repeat_range' => "D",
                                'repeat_monthly_on' => 0,
                                'include_sat_sun' => 0,
                                'repeat_occurrence' => '',
                            ],
                            [
                                'endDate' => '2101-12-16 12:30:00'
                            ],
                            [
                                [
                                    "slot_start" => '2100-12-16 12:00:00',
                                    "slot_end" => '2100-12-16 12:30:00',
                                ],
                                [
                                    "slot_start" => '2101-12-16 12:00:00',
                                    "slot_end" => '2101-12-16 12:30:00',
                                ],
                            ],
                            '',
                            $this->createOfficeHour(
                                4878808,
                                '2100-12-16 12:00:00',
                                '2101-12-16 12:30:00',
                                "D",
                                [
                                    'meeting_length' => 30,
                                    'repeat_pattern' => "M",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0000011",
                                    'repeat_range' => "D",
                                    'repeat_monthly_on' => 0,
                                    'repeat_occurrence' => '',
                                    'include_sat_sun' => 0,
                                ]
                            )
                        ],
                    ]
            ]
        );
    }


    public function testEditOfficeHourSeries()
    {
        $this->specify("Test Edit Office Hour Series", function ($officeHourId, $personId, $startDate, $endDate, $seriesDetails, $currentAcademicYearEndDate, $allFutureSlotDates, $errorType, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            //Mock a Logger
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            // Mock a Container
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            // Mocking Repositories
            $mockPersonRepository = $this->getMock('PersonRepository', ['find']);
            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', ['getCurrentAcademicDetails']);
            $mockAppointmentsRepository = $this->getMock('AppointmentsRepository', ['find']);
            $mockOfficeHoursSeriesRepository = $this->getMock('OfficeHoursSeriesRepository', ['persist', 'flush', 'find']);
            $mockOfficeHoursRepository = $this->getMock('OfficeHoursRepository', ['getAllOfficeHourSlots', 'createOfficeHours', 'find', 'findBy', 'removeExistingSlots', 'updateAppointmentForEditedSlots', 'updateAppointmentAsFreeStanding', 'getFreeStandingAppointments', 'remove', 'flush']);

            // Mocking Services
            $mockDateUtilityService = $this->getMock('DateUtilityService', ['getOrganizationISOTimeZone', 'getDaylightSavingsTimeOffsetAdjustment', 'getFirstDayOfWeekForDatetime']);
            $mockPersonService = $this->getMock('PersonService', ['findPerson']);
            $mockCalendarIntegrationService = $this->getMock('CalendarIntegrationService', ['facultyCalendarSettings', 'syncOfficeHourSeries', 'syncOneOffEvent']);
            $mockCalendarFactoryService = $this->getMock('CalendarFactoryService', ['syncPCS']);

            // Scaffolding for all Repositories
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [OrgAcademicYearRepository::REPOSITORY_KEY, $mockOrgAcademicYearRepository],
                    [OfficeHoursSeriesRepository::REPOSITORY_KEY, $mockOfficeHoursSeriesRepository],
                    [OfficeHoursRepository::REPOSITORY_KEY, $mockOfficeHoursRepository],
                    [AppointmentsRepository::REPOSITORY_KEY, $mockAppointmentsRepository]
                ]);

            // Scaffolding for service
            $mockContainer->method('get')->willReturnMap(
                [
                    [PersonService::SERVICE_KEY, $mockPersonService],
                    [DateUtilityService::SERVICE_KEY, $mockDateUtilityService],
                    [CalendarIntegrationService::SERVICE_KEY, $mockCalendarIntegrationService],
                    [CalendarFactoryService::SERVICE_KEY, $mockCalendarFactoryService],
                ]);

            $mockPerson = $this->getPersonInstance($personId);
            if ($errorType == 'person_not_found') {
                $mockPersonRepository->method('find')->willReturn(null);
            } else {
                $mockPersonRepository->method('find')->willReturn($mockPerson);
            }

            $mockOfficeHoursSeries = $this->getOfficeHoursSeriesInstance($officeHourId);
            if ($errorType == 'office_hour_not_found') {
                $mockOfficeHoursSeriesRepository->method('find')->willReturn(null);
            } else {
                $mockOfficeHoursSeriesRepository->method('find')->willReturn($mockOfficeHoursSeries);
            }

            $mockOrgAcademicYearRepository->method('getCurrentAcademicDetails')->willReturn([$currentAcademicYearEndDate]);

            $mockOfficeHoursRepository->method('findBy')->willReturn($this->getAllOfficeHoursForSeries($personId, $startDate, $endDate));

            $mockOfficeHours = $this->getAllOfficeHoursForSeries($personId, $startDate, $endDate);
            $mockOfficeHoursRepository->method('find')->willReturn($mockOfficeHours[0]);

            $mockPersonService->method('findPerson')->willReturn(null);
            $mockDateUtilityService->method('getOrganizationISOTimeZone')->willReturn('Asia/Calcutta');
            $mockDateUtilityService->method('getDaylightSavingsTimeOffsetAdjustment')->willReturn(0);

            // Getting start date of the week
            $currentDate = new DateTime($startDate);
            $currentDate->sub(new \DateInterval('P' . $currentDate->format('w') . 'D'));
            $mockDateUtilityService->method('getFirstDayOfWeekForDatetime')->willReturn($currentDate);

            $mockOfficeHoursRepository->method('getAllOfficeHourSlots')->willReturn($allFutureSlotDates);
            $mockOfficeHoursRepository->method('getFreeStandingAppointments')->willReturn([]);

            $mockCalendarIntegrationService->method('facultyCalendarSettings')->willReturn($this->calendarSettings);

            $repeatRange = $seriesDetails['repeat_range'];
            $officeHoursDTO = $this->createOfficeHour($personId, $startDate, $endDate, $repeatRange, $seriesDetails, $officeHourId);

            try {
                $officeHoursService = new OfficeHoursService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $officeHoursService->editOfficeHourSeries($officeHoursDTO);

                $this->assertEquals($result, $expectedResult);

            } catch (SynapseException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
                'examples' =>
                    [
                        // Test0: Person not found and throws exception
                        [
                            $this->officeHourId,
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 30,
                                'repeat_pattern' => "D",
                                'repeat_every' => 1,
                                'repeat_days' => "0000000",
                                'repeat_range' => "D",
                                'repeat_monthly_on' => 0,
                                'repeat_occurrence' => 0,
                                'include_sat_sun' => 0,
                            ],
                            [],
                            [],
                            'person_not_found',
                            'This person does not exist'
                        ],
                        // Test1: Office Hour Series Not Found and throws exception
                        [
                            $this->officeHourId,
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 30,
                                'repeat_pattern' => "D",
                                'repeat_every' => 1,
                                'repeat_days' => "0000000",
                                'repeat_range' => "D",
                                'repeat_monthly_on' => 0,
                                'repeat_occurrence' => 0,
                                'include_sat_sun' => 0,
                            ],
                            [],
                            [],
                            'office_hour_not_found',
                            'Office Hour Series Not Found.'
                        ],
                        // Test2: Edit Office Hour Series and check overlap scenario and throws exception
                        [
                            $this->officeHourId,
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 30,
                                'repeat_pattern' => "D",
                                'repeat_every' => 1,
                                'repeat_days' => "0000000",
                                'repeat_range' => "D",
                                'repeat_monthly_on' => 0,
                                'repeat_occurrence' => 0,
                                'include_sat_sun' => 1,
                            ],
                            [
                                'endDate' => '2100-12-01 00:00:00'
                            ],
                            [
                                [
                                    "slot_start" => '2100-08-01 12:15:00',
                                    "slot_end" => '2100-08-01 12:30:00',
                                ],
                                [
                                    "slot_start" => '2100-10-01 10:00:00',
                                    "slot_end" => '2100-10-01 11:30:00',
                                ],
                            ],
                            '',
                            'Office hours cannot overlap'
                        ],
                        // Test3: Edit Office Hour Series with Daily - Single slot - End date
                        [
                            $this->officeHourId,
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 30,
                                'repeat_pattern' => "D",
                                'repeat_every' => 1,
                                'repeat_days' => "0000000",
                                'repeat_range' => "D",
                                'repeat_monthly_on' => 0,
                                'repeat_occurrence' => 0,
                                'include_sat_sun' => 1,
                            ],
                            [
                                'endDate' => date('Y-12-31')
                            ],
                            [
                                [
                                    "slot_start" => '2100-08-01 10:15:00',
                                    "slot_end" => '2100-08-01 11:30:00',
                                ],
                                [
                                    "slot_start" => '2100-10-01 10:00:00',
                                    "slot_end" => '2100-10-01 11:30:00',
                                ],
                            ],
                            '',
                            $this->createOfficeHour(
                                4878808,
                                '2100-08-01 12:00:00',
                                '2100-10-01 12:30:00',
                                "D",
                                [
                                    'meeting_length' => 30,
                                    'repeat_pattern' => "D",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0000000",
                                    'repeat_range' => "D",
                                    'repeat_monthly_on' => 0,
                                    'repeat_occurrence' => 0,
                                    'include_sat_sun' => 1,
                                ],
                                $this->officeHourId
                            )
                        ],
                        // Test4: Edit Office Hour Series with Daily - Single slot - End after X occurrence
                        [
                            $this->officeHourId,
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 30,
                                'repeat_pattern' => "D",
                                'repeat_every' => 1,
                                'repeat_days' => "0000000",
                                'repeat_range' => "E",
                                'repeat_monthly_on' => 0,
                                'repeat_occurrence' => "50",
                                'include_sat_sun' => 1,
                            ],
                            [
                                'endDate' => '2100-12-01 00:00:00'
                            ],
                            [
                                [
                                    "slot_start" => '2100-08-01 10:15:00',
                                    "slot_end" => '2100-08-01 11:30:00',
                                ],
                                [
                                    "slot_start" => '2100-10-01 10:00:00',
                                    "slot_end" => '2100-10-01 11:30:00',
                                ],
                            ],
                            '',
                            $this->createOfficeHour(
                                4878808,
                                '2100-08-01 12:00:00',
                                '2100-10-01 12:30:00',
                                "E",
                                [
                                    'meeting_length' => 30,
                                    'repeat_pattern' => "D",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0000000",
                                    'repeat_range' => "E",
                                    'repeat_monthly_on' => 0,
                                    'repeat_occurrence' => "50",
                                    'include_sat_sun' => 1,
                                ],
                                $this->officeHourId
                            )
                        ],
                        // Test5: Edit Office Hour Series with Weekly - Multiple slot - End date
                        [
                            $this->officeHourId,
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 15,
                                'repeat_pattern' => "W",
                                'repeat_every' => 1,
                                'repeat_days' => "0111110",
                                'repeat_range' => "D",
                                'repeat_monthly_on' => 0,
                                'repeat_occurrence' => 0,
                                'include_sat_sun' => 0,
                            ],
                            [
                                'endDate' => '2100-12-01 00:00:00'
                            ],
                            [
                                [
                                    "slot_start" => '2100-08-01 10:15:00',
                                    "slot_end" => '2100-08-01 11:30:00',
                                ],
                                [
                                    "slot_start" => '2100-10-01 10:00:00',
                                    "slot_end" => '2100-10-01 11:30:00',
                                ],
                            ],
                            '',
                            $this->createOfficeHour(
                                4878808,
                                '2100-08-01 12:00:00',
                                '2100-10-01 12:30:00',
                                "D",
                                [
                                    'meeting_length' => 15,
                                    'repeat_pattern' => "W",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0111110",
                                    'repeat_range' => "D",
                                    'repeat_monthly_on' => 0,
                                    'repeat_occurrence' => 0,
                                    'include_sat_sun' => 0,
                                ],
                                $this->officeHourId
                            )
                        ],
                        // Test6: Edit Office Hour Series with Weekly - Multiple slot - End after X occurrence
                        [
                            $this->officeHourId,
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 15,
                                'repeat_pattern' => "W",
                                'repeat_every' => 1,
                                'repeat_days' => "0111110",
                                'repeat_range' => "E",
                                'repeat_monthly_on' => 0,
                                'repeat_occurrence' => "50",
                                'include_sat_sun' => 0,
                            ],
                            [
                                'endDate' => '2100-12-01 00:00:00'
                            ],
                            [
                                [
                                    "slot_start" => '2100-08-01 10:15:00',
                                    "slot_end" => '2100-08-01 11:30:00',
                                ],
                                [
                                    "slot_start" => '2100-10-01 10:00:00',
                                    "slot_end" => '2100-10-01 11:30:00',
                                ],
                            ],
                            '',
                            $this->createOfficeHour(
                                4878808,
                                '2100-08-01 12:00:00',
                                '2100-10-01 12:30:00',
                                "E",
                                [
                                    'meeting_length' => 15,
                                    'repeat_pattern' => "W",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0111110",
                                    'repeat_range' => "E",
                                    'repeat_monthly_on' => 0,
                                    'repeat_occurrence' => "50",
                                    'include_sat_sun' => 0,
                                ],
                                $this->officeHourId
                            )
                        ],
                        // Test7: Edit Office Hour Series with Monthly - Multiple slot - End date
                        [
                            $this->officeHourId,
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 15,
                                'repeat_pattern' => "M",
                                'repeat_every' => 1,
                                'repeat_days' => "0111110",
                                'repeat_range' => "D",
                                'repeat_monthly_on' => 1,
                                'repeat_occurrence' => 0,
                                'include_sat_sun' => 0,
                            ],
                            [
                                'endDate' => '2100-12-01 00:00:00'
                            ],
                            [
                                [
                                    "slot_start" => '2100-08-01 10:15:00',
                                    "slot_end" => '2100-08-01 11:30:00',
                                ],
                                [
                                    "slot_start" => '2100-10-01 10:00:00',
                                    "slot_end" => '2100-10-01 11:30:00',
                                ],
                            ],
                            '',
                            $this->createOfficeHour(
                                4878808,
                                '2100-08-01 12:00:00',
                                '2100-10-01 12:30:00',
                                "D",
                                [
                                    'meeting_length' => 15,
                                    'repeat_pattern' => "M",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0111110",
                                    'repeat_range' => "D",
                                    'repeat_monthly_on' => 1,
                                    'repeat_occurrence' => 0,
                                    'include_sat_sun' => 0,
                                ],
                                $this->officeHourId
                            )
                        ],
                        // Test8: Edit Office Hour Series with Monthly - Multiple slot - End after X occurrence
                        [
                            $this->officeHourId,
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 15,
                                'repeat_pattern' => "M",
                                'repeat_every' => 1,
                                'repeat_days' => "0111110",
                                'repeat_range' => "E",
                                'repeat_monthly_on' => 1,
                                'repeat_occurrence' => "50",
                                'include_sat_sun' => 0,
                            ],
                            [
                                'endDate' => '2100-12-01 00:00:00'
                            ],
                            [
                                [
                                    "slot_start" => '2100-08-01 10:15:00',
                                    "slot_end" => '2100-08-01 11:30:00',
                                ],
                                [
                                    "slot_start" => '2100-10-01 10:00:00',
                                    "slot_end" => '2100-10-01 11:30:00',
                                ],
                            ],
                            '',
                            $this->createOfficeHour(
                                4878808,
                                '2100-08-01 12:00:00',
                                '2100-10-01 12:30:00',
                                "E",
                                [
                                    'meeting_length' => 15,
                                    'repeat_pattern' => "M",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0111110",
                                    'repeat_range' => "E",
                                    'repeat_monthly_on' => 1,
                                    'repeat_occurrence' => "50",
                                    'include_sat_sun' => 0,
                                ],
                                $this->officeHourId
                            )
                        ],
                        // Test9: Edit Office Hour Series from One time event to series event
                        [
                            $this->officeHourId,
                            4878808,
                            '2100-08-01 12:00:00',
                            '2100-10-01 12:30:00',
                            [
                                'meeting_length' => 30,
                                'repeat_pattern' => "D",
                                'repeat_every' => 1,
                                'repeat_days' => "0000000",
                                'repeat_range' => "D",
                                'repeat_monthly_on' => 0,
                                'repeat_occurrence' => "0",
                                'include_sat_sun' => 0,
                                'one_to_series' => true
                            ],
                            [
                                'endDate' => date('Y-12-31')
                            ],
                            [
                                [
                                    "slot_start" => '2100-08-01 10:15:00',
                                    "slot_end" => '2100-08-01 11:30:00',
                                ],
                                [
                                    "slot_start" => '2100-10-01 10:00:00',
                                    "slot_end" => '2100-10-01 11:30:00',
                                ],
                            ],
                            '',
                            $this->createOfficeHour(
                                4878808,
                                '2100-08-01 12:00:00',
                                '2100-10-01 12:30:00',
                                "D",
                                [
                                    'meeting_length' => 30,
                                    'repeat_pattern' => "D",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0000000",
                                    'repeat_range' => "D",
                                    'repeat_monthly_on' => 0,
                                    'repeat_occurrence' => "0",
                                    'include_sat_sun' => 0,
                                    'one_to_series' => true
                                ],
                                null
                            )
                        ],
                        // edit monthly series for december - if there is no events for december then the event start date should be next year January.
                        [
                            $this->officeHourId,
                            4878808,
                            '2100-12-16 12:00:00',
                            '2101-12-16 12:30:00',
                            [
                                'meeting_length' => 30,
                                'repeat_pattern' => "M",
                                'repeat_every' => 1,
                                'repeat_days' => "0000011",
                                'repeat_range' => "D",
                                'repeat_monthly_on' => 0,
                                'include_sat_sun' => 0,
                                'repeat_occurrence' => '',
                            ],
                            [
                                'endDate' => '2101-12-16 12:30:00',
                            ],
                            [
                                [
                                    "slot_start" => '2100-12-16 12:00:00',
                                    "slot_end" => '2100-12-16 12:30:00',
                                ],
                                [
                                    "slot_start" => '2101-12-16 12:00:00',
                                    "slot_end" => '2101-12-16 12:30:00'
                                ],
                            ],
                            '',
                            $this->createOfficeHour(
                                4878808,
                                '2100-12-16 12:00:00',
                                '2101-12-16 12:30:00',
                                "D",
                                [
                                    'meeting_length' => 30,
                                    'repeat_pattern' => "M",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0000011",
                                    'repeat_range' => "D",
                                    'repeat_monthly_on' => 0,
                                    'repeat_occurrence' => '',
                                    'include_sat_sun' => 0,
                                ],
                                $this->officeHourId
                            )
                        ],
                    ]
            ]
        );
    }

    private function getAllOfficeHoursForSeries($personId, $startDate, $endDate)
    {
        $officeHour = new \Synapse\CoreBundle\Entity\OfficeHours();
        $officeHour->setId(1);
        $personInstance = $this->getPersonInstance($personId);
        $officeHour->setPerson($personInstance);
        $officeHour->setOrganization($personInstance->getOrganization());
        $officeHour->setOfficeHoursSeries($this->getOfficeHoursSeriesInstance($this->officeHourId));
        $officeHour->setAppointments($this->getAppointmentInstance($personId));
        $officeHour->setLocation('Bengaluru, India');
        $officeHour->setSlotType('S');
        $officeHour->setSlotStart($startDate);
        $officeHour->setSlotEnd($endDate);

        return [$officeHour];
    }

    private function getAppointmentInstance($personId)
    {
        $appointment = new \Synapse\CoreBundle\Entity\Appointments();
        $personInstance = $this->getPersonInstance($personId);
        $appointment->setPerson($personInstance);

        return $appointment;

    }


    public function testDeleteOfficeHourSeries()
    {
        $this->specify("Test delete office hour series", function ($officeHourSeriesId, $organizationId, $personId, $errorType, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            //Mock a Logger
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            // Mock a Container
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            // Mocking Repositories
            $mockOfficeHoursSeriesRepository = $this->getMock('OfficeHoursSeriesRepository', ['findOneBy']);
            $mockOfficeHoursRepository = $this->getMock('OfficeHoursRepository', ['removeExistingSlots', 'updateAppointmentAsFreeStanding', 'getFreeStandingAppointments']);

            // Mocking Services
            $mockCalendarIntegrationService = $this->getMock('CalendarIntegrationService', ['facultyCalendarSettings', 'syncOfficeHourSeries']);
            $mockCalendarFactoryService = $this->getMock('CalendarFactoryService', ['syncPCS']);

            // Scaffolding for all Repositories
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [\Synapse\CoreBundle\Repository\OfficeHoursSeriesRepository::REPOSITORY_KEY, $mockOfficeHoursSeriesRepository],
                    [OfficeHoursRepository::REPOSITORY_KEY, $mockOfficeHoursRepository],
                ]);

            // Scaffolding for service
            $mockContainer->method('get')->willReturnMap(
                [
                    [CalendarIntegrationService::SERVICE_KEY, $mockCalendarIntegrationService],
                    [\Synapse\CalendarBundle\Service\Impl\CalendarFactoryService::SERVICE_KEY, $mockCalendarFactoryService],
                ]);

            $mockOfficeHoursSeries = $this->getOfficeHoursSeriesInstance($officeHourSeriesId);
            if ($errorType == 'office_hour_not_found') {
                $mockOfficeHoursSeriesRepository->method('findOneBy')->willReturn(null);
            } else {
                $mockOfficeHoursSeriesRepository->method('findOneBy')->willReturn($mockOfficeHoursSeries);
            }

            if ($errorType == 'free_standing') {
                $mockOfficeHoursRepository->method('getFreeStandingAppointments')->willReturn([1,2]);
            } else {
                $mockOfficeHoursRepository->method('getFreeStandingAppointments')->willReturn([]);
            }

            $mockCalendarIntegrationService->method('facultyCalendarSettings')->willReturn($this->calendarSettings);

            try {
                $officeHoursService = new OfficeHoursService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $officeHoursService->deleteOfficeHourSeries($officeHourSeriesId, $organizationId, $personId);

                $this->assertEquals($result, $expectedResult);

            } catch (\Synapse\CoreBundle\Exception\SynapseException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
                'examples' =>
                    [
                        // Test0: Office Hour Series Not Found and throws exception
                        [
                            $this->officeHourId,
                            $this->organizationId,
                            4878808,
                            'office_hour_not_found',
                            'Office Hour Series Not Found.'
                        ],
                        // Test1: Delete Office Hour Series with appointment as free standing, returns void
                        [
                            $this->officeHourId,
                            $this->organizationId,
                            4878808,
                            'free_standing',
                            null
                        ],
                        // Test2: Delete Office Hour Series without appointment as free standing and returns void
                        [
                            $this->officeHourId,
                            $this->organizationId,
                            4878808,
                            '',
                            null
                        ],
                    ]
            ]
        );
    }

    public function testGetSeriesSlotStartDate()
    {
        $this->specify("Test get series slot start date for monthly series", function ($startSlot, $officeHoursSeries, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            //Mock a Logger
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            // Mock a Container
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $officeHoursService = new OfficeHoursService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $officeHoursService->getSeriesSlotStartDate($startSlot, $officeHoursSeries);
            $this->assertEquals($results, $expectedResult);
        }, [
                'examples' =>
                    [
                        // Create series every month first sunday and monday
                        [
                            new DateTime('2100-03-01 16:00:00'),
                            $this->createOfficeHourSeries(
                                45856,
                                4878808,
                                new DateTime('2100-03-01 16:00:00'),
                                new DateTime('2100-03-15 16:30:00'),
                                [
                                    'meeting_length' => 30,
                                    'repeat_pattern' => "M",
                                    'repeat_every' => 1,
                                    'repeat_days' => "1100000",
                                    'repeat_range' => "D",
                                    'include_sat_sun' => 0,
                                    'repeat_monthly_on' => 1
                                ]),
                            new DateTime('2100-03-01 16:00:00')
                        ],
                        // Create series every month second monday, tuesday for the month of december
                        [
                            new DateTime('2100-12-01 10:00:00'),
                            $this->createOfficeHourSeries(
                                45856,
                                4878808,
                                new DateTime('2100-12-01 10:00:00'),
                                new DateTime('2101-03-15 10:30:00'),
                                [
                                    'meeting_length' => 30,
                                    'repeat_pattern' => "M",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0110000",
                                    'repeat_range' => "D",
                                    'include_sat_sun' => 0,
                                    'repeat_monthly_on' => 1
                                ]),
                            new DateTime('2100-12-06 10:00:00')
                        ],
                        // Create series for every month first friday, saturday starts from december where the start date should shifted to next year since the first saturday, sunday is past from the actual start date.
                        [
                            new DateTime('2100-12-16 16:30:00'),
                            $this->createOfficeHourSeries(
                                45856,
                                4878808,
                                new DateTime('2100-12-06 16:30:00'),
                                new DateTime('2101-06-25 17:00:00'),
                                [
                                    'meeting_length' => 30,
                                    'repeat_pattern' => "M",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0000011",
                                    'repeat_range' => "D",
                                    'include_sat_sun' => 0,
                                    'repeat_monthly_on' => 1
                                ]),
                            new DateTime('2101-01-01 16:30:00')
                        ],
                        // Create series for every month second monday, wednesday and friday that starts from december where the events are not available for december since the current month date is past.
                        [
                            new DateTime('2100-12-22 17:30:00'),
                            $this->createOfficeHourSeries(
                                45856,
                                4878808,
                                new DateTime('2100-12-22 17:30:00'),
                                new DateTime('2101-06-25 18:00:00'),
                                [
                                    'meeting_length' => 30,
                                    'repeat_pattern' => "M",
                                    'repeat_every' => 1,
                                    'repeat_days' => "0101010",
                                    'repeat_range' => "D",
                                    'include_sat_sun' => 0,
                                    'repeat_monthly_on' => 1
                                ]),
                            new DateTime('2101-01-03 17:30:00')
                        ]
                    ]
            ]
        );
    }

    /**
     * @param int $officeHourSeriesId
     * @param int $personId
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param array $seriesData
     * @return OfficeHoursSeries
     */
    private function createOfficeHourSeries($officeHourSeriesId, $personId, $startDate, $endDate, $seriesData)
    {
        $officeHoursSeries = new OfficeHoursSeries();
        $officeHoursSeries->setId($officeHourSeriesId);
        $person = new Person();
        $person->setId($personId);
        $officeHoursSeries->setPerson($person);
        $officeHoursSeries->setIncludeSatSun($seriesData['include_sat_sun']);
        $officeHoursSeries->setLocation("United States");
        $officeHoursSeries->setRepeatPattern($seriesData['repeat_pattern']);
        $officeHoursSeries->setRepetitionRange($seriesData['repeat_range']);
        $officeHoursSeries->setRepeatEvery($seriesData['repeat_every']);
        $officeHoursSeries->setRepeatMonthlyOn($seriesData['repeat_monthly_on']);
        $officeHoursSeries->setMeetingLength($seriesData['meeting_length']);
        $officeHoursSeries->setDays($seriesData['repeat_days']);
        $officeHoursSeries->setSlotStart($startDate);
        $officeHoursSeries->setSlotEnd($endDate);
        return $officeHoursSeries;
    }
}