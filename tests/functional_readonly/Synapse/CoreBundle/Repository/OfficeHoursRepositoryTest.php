<?php

use Synapse\CoreBundle\Repository\OfficeHoursRepository;
use Synapse\CoreBundle\SynapseConstant;

class OfficeHoursRepositoryTest extends \Codeception\TestCase\Test
{
    use Codeception\Specify;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\CoreBundle\Repository\OfficeHoursRepository
     */
    private $officeHoursRepository;


    public function _before()
    {
        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->officeHoursRepository = $this->repositoryResolver->getRepository(OfficeHoursRepository::REPOSITORY_KEY);
    }

    public function testIsOverlappingOfficeHours()
    {
        $this->specify('test is overlapping office hours', function ($expectedResult, $organizationId, $personId, $startDate, $endDate, $exclude) {
            $results = $this->officeHoursRepository->isOverlappingOfficeHours($personId, $organizationId, $startDate, $endDate, $exclude);
            verify($expectedResult)->equals($results);

        }, ['examples' => [
            [
                //Non-overlapping time frame
                false,
                99,
                113802,
                '2016-05-02 12:00:00',
                '2016-05-03 13:00:00',
                null
            ],
            [
                //Overlapping an existing series time frame
                true,
                99,
                113802,
                '2016-05-03 14:00:00',
                '2016-05-09 15:30:00',
                null
            ],
            [
                //Butting up to, but not overlapping, first member of an existing series
                false,
                99,
                113802,
                '2016-05-02 13:00:00',
                '2016-05-03 14:00:00',
                null
            ],
            [
                //Overlapping an existing series' first member only
                true,
                99,
                113802,
                '2016-05-02 13:00:00',
                '2016-05-03 14:30:00',
                null
            ],
            [
                //Butting up to, but not overlapping, last member of an existing series
                false,
                99,
                113802,
                '2016-05-09 16:00:00',
                '2016-05-10 16:30:00',
                null

            ],
            [
                //Overlapping an existing series' last member only
                true,
                99,
                113802,
                '2016-05-09 15:30:00',
                '2016-05-10 16:30:00',
                null
            ],
            [
                //Completely within an existing series dates and times
                true,
                99,
                113802,
                '2016-05-03 15:00:00',
                '2016-05-09 15:15:00',
                null
            ],
            [
                //Midnight problem
                false,
                99,
                113802,
                '2016-05-03 23:55:00',
                '2016-05-04 00:05:00',
                null
            ],
            [
                //Excluding an office hour ID
                false,
                99,
                113802,
                '2016-05-03 14:00:00',
                '2016-05-03 14:30:00',
                [
                    109545
                ]
            ]
        ]]);
    }

    public function testGetAllOfficeHourSlots()
    {
        $this->specify('test to get all office hour slots', function ($personId, $organizationId, $startDate, $endDate, $excludeOfficeHoursSeriesId, $expectedResult) {
            $startDate = new \DateTime($startDate);
            $endDate = new \DateTime($endDate);
            $results = $this->officeHoursRepository->getAllOfficeHourSlots($personId, $organizationId, $startDate, $endDate, $excludeOfficeHoursSeriesId);
            $testArray = array_slice($results, 0, 4);
            verify($testArray)->equals($expectedResult);

        }, ['examples' => [
            // Example 1 : Get all future slots for a single day with start_date and end_date based on person_id and organization_id
            [
                113802,
                99,
                '2016-05-03 14:00:00',
                '2016-05-03 15:30:00',
                null,
                [
                    [   "slot_start" => "2016-05-03 14:00:00",
                        "slot_end" => "2016-05-03 14:30:00"
                    ],
                    [   "slot_start" => "2016-05-03 14:30:00",
                        "slot_end" => "2016-05-03 15:00:00"
                    ],
                    [   "slot_start" => "2016-05-03 15:00:00",
                        "slot_end" => "2016-05-03 15:30:00"
                    ],
                    [   "slot_start" => "2016-05-03 15:30:00",
                        "slot_end" => "2016-05-03 16:00:00"
                    ]
                ]
            ],
            // Example 2 : Get all future slots for multiple days with start_date and end_date based on person_id and organization_id
            [
                113802,
                99,
                '2016-05-03 14:00:00',
                '2016-05-30 15:30:00',
                null,
                [
                    [   "slot_start" => "2016-05-03 14:00:00",
                        "slot_end" => "2016-05-03 14:30:00"
                    ],
                    [   "slot_start" => "2016-05-03 14:30:00",
                        "slot_end" => "2016-05-03 15:00:00"
                    ],
                    [   "slot_start" => "2016-05-03 15:00:00",
                        "slot_end" => "2016-05-03 15:30:00"
                    ],
                    [   "slot_start" => "2016-05-03 15:30:00",
                        "slot_end" => "2016-05-03 16:00:00"
                    ]
                ]
            ],
            // Example 3 : Get all future slots for a person with start_date and end_date, excluding an office_hour_series_id = 1
            [
                220,
                2,
                '2015-05-27 13:30:00',
                '2015-05-27 17:00:00',
                1,
                [
                    [   "slot_start" => "2015-05-27 13:30:00",
                        "slot_end" => "2015-05-27 14:00:00"
                    ],
                    [   "slot_start" => "2015-05-27 14:00:00",
                        "slot_end" => "2015-05-27 14:30:00"
                    ],
                    [   "slot_start" => "2015-05-27 14:30:00",
                        "slot_end" => "2015-05-27 15:00:00"
                    ],
                    [   "slot_start" => "2015-05-27 15:00:00",
                        "slot_end" => "2015-05-27 15:30:00"
                    ]
                ]
            ],
            // Example 4 : Get all future slots for a person with start_date and end_date, excluding an office_hour_series_id = 712 and office_hour_series_id IS NULL
            [
                113802,
                99,
                '2016-05-06 15:00:00',
                '2016-05-09 17:00:00',
                712,
                [
                    [   "slot_start" => "2016-05-06 16:00:00",
                        "slot_end" => "2016-05-06 16:30:00"
                    ],
                    [   "slot_start" => "2016-05-06 16:30:00",
                        "slot_end" => "2016-05-06 17:00:00"
                    ],
                    [   "slot_start" => "2016-05-06 17:00:00",
                        "slot_end" => "2016-05-06 17:30:00"
                    ]
                ]
            ],
            // Example 5 : Test with null data
            [
                null,
                null,
                null,
                null,
                null,
                []
            ]
        ]]);
    }

    public function testGetFreeStandingAppointments()
    {
        $this->specify('test to get the list of free standing appointments', function ($officeHourSeriesId, $currentTime, $expectedResult) {
            $results = $this->officeHoursRepository->getFreeStandingAppointments($officeHourSeriesId, $currentTime);
            $testArray = array_slice($results, 0, 4);
            verify($testArray)->equals($expectedResult);

        }, ['examples' => [
            // Example 1 : If office_hour_series_id is null and current_time, returns blank array
            [
                null,
                '2016-01-10 12:00:00',
                []
            ],
            // Example 2 : If office_hour_series_id and current_time is null, returns blank array
            [
                530,
                null,
                []
            ],
            // Example 3 : Get the list of free standing appointments based on office_hour_series_id and current_time
            [
                530,
                '2016-01-10 12:00:00',
                [
                    "5552",
                    "5553"
                ]
            ],
            // Example 4 : No data found based on office_hour_series_id and current_time
            [
                531,
                '2016-01-10 12:00:00',
                []
            ],
            // Example 5: Test with null data
            [
                null,
                null,
                []
            ]
        ]]);
    }

    public function testGetUsersAppointments()
    {
        $this->specify('test to get the list of appointments for user', function ($personId, $fromDate, $toDate, $frequency, $currentDateTime, $orgAcademicYearId, $expectedResult) {
            $results = $this->officeHoursRepository->getUsersAppointments($personId, $fromDate, $toDate, $frequency, $currentDateTime, $orgAcademicYearId);
            verify($results)->equals($expectedResult);

        }, ['examples' => [
            [
                // List faculty appointment and office hours
                4893111, '2016-06-01', '2016-10-01', 'past', '2016-10-01', '182',
                [
                    [
                        'appointments_id' => NULL,
                        'office_hours_id' => '109619',
                        'person_id_proxy' => NULL,
                        'person_id' => '4893111',
                        'slot_type' => 'I',
                        'location' => 'California442',
                        'app_loc' => NULL,
                        'slot_start' => '2016-06-17 06:59:00',
                        'slot_end' => '2016-06-17 07:29:00',
                        'meeting_length' => '30',
                        'type' => NULL,
                        'start_date_time' => NULL,
                        'end_date_time' => NULL,
                        'is_free_standing' => NULL,
                        'title' => NULL,
                        'activity_category_id' => NULL,
                        'is_cancelled' => '0',
                        'office_hour_google_appointment_id' => NULL,
                        'appointment_google_appointment_id' => NULL,

                    ],
                    [
                        'appointments_id' => '6852',
                        'office_hours_id' => '109625',
                        'person_id_proxy' => NULL,
                        'person_id' => '4893111',
                        'slot_type' => 'I',
                        'location' => 'Bangalore TM862',
                        'app_loc' => 'Bangalore TM862',
                        'slot_start' => '2016-06-17 10:00:00',
                        'slot_end' => '2016-06-17 10:30:00',
                        'meeting_length' => '30',
                        'type' => 'I',
                        'start_date_time' => '2016-06-17 10:00:00',
                        'end_date_time' => '2016-06-17 10:30:00',
                        'is_free_standing' => NULL,
                        'title' => 'Academic performance concern ',
                        'activity_category_id' => '21',
                        'is_cancelled' => '0',
                        'office_hour_google_appointment_id' => NULL,
                        'appointment_google_appointment_id' => NULL,

                    ]
                ]
            ],
            // Get the list of next week appointments for a faculty
            [4610691, '', '', 'next', '2016-02-28', '162', []],
            // Faculty books appointment directly with students
            [4610691, '', '', 'past', '2016-02-28', '162',
                [
                    ['appointments_id' => 5688,
                        'office_hours_id' => 0,
                        'person_id_proxy' => 4610691,
                        'person_id' => NULL,
                        'slot_type' => NULL,
                        'location' => 'Desk',
                        'app_loc' => NULL,
                        'slot_start' => '2016-02-17 15:15:00',
                        'slot_end' => '2016-02-17 16:00:00',
                        'meeting_length' => NULL,
                        'type' => 'F',
                        'start_date_time' => '2016-02-17 15:15:00',
                        'end_date_time' => '2016-02-17 16:00:00',
                        'is_free_standing' => '1',
                        'title' => 'Class attendance positive',
                        'activity_category_id' => '20',
                        'is_cancelled' => NULL,
                        'office_hour_google_appointment_id' => NULL,
                        'appointment_google_appointment_id' => NULL
                    ],
                    [
                        'appointments_id' => 5689,
                        'office_hours_id' => 0,
                        'person_id_proxy' => 4610691,
                        'person_id' => NULL,
                        'slot_type' => NULL,
                        'location' => 'Desk',
                        'app_loc' => NULL,
                        'slot_start' => '2016-02-17 17:00:00',
                        'slot_end' => '2016-02-17 17:15:00',
                        'meeting_length' => NULL,
                        'type' => 'F',
                        'start_date_time' => '2016-02-17 17:00:00',
                        'end_date_time' => '2016-02-17 17:15:00',
                        'is_free_standing' => '1',
                        'title' => 'Class attendance positive',
                        'activity_category_id' => '20',
                        'is_cancelled' => NULL,
                        'office_hour_google_appointment_id' => NULL,
                        'appointment_google_appointment_id' => NULL

                    ]

                ]
            ],
            // student books faculty created appointments
            [132897, '', '', 'past', '2015-08-30 ', '162',
                [
                    [
                        'appointments_id' => NULL,
                        'office_hours_id' => '9120',
                        'person_id_proxy' => NULL,
                        'person_id' => '132897',
                        'slot_type' => 'I',
                        'location' => 'B05B',
                        'app_loc' => NULL,
                        'slot_start' => '2015-08-20 16:00:00',
                        'slot_end' => '2015-08-20 18:00:00',
                        'meeting_length' => NULL,
                        'type' => NULL,
                        'start_date_time' => NULL,
                        'end_date_time' => NULL,
                        'is_free_standing' => NULL,
                        'title' => NULL,
                        'activity_category_id' => NULL,
                        'is_cancelled' => '0',
                        'office_hour_google_appointment_id' => NULL,
                        'appointment_google_appointment_id' => NULL

                    ]
                ]
            ]
        ]
        ]);
    }
}