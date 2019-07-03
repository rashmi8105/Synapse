<?php
use Synapse\CoreBundle\SynapseConstant;

class OrgAcademicYearRepositoryTest extends \Codeception\TestCase\Test
{
    use Codeception\Specify;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     *
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     *
     * @var \Synapse\AcademicBundle\Repository\OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var DateTime
     */
    private $currentYear;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgAcademicYear');
    }

    public function testGetCurrentOrPreviousAcademicYearUsingCurrentDate()
    {
        $this->specify("Verify the functionality of the method getCurrentOrPreviousAcademicYearUsingCurrentDate", function ($currentDate, $orgId, $expectedResult) {
            $results = $this->orgAcademicYearRepository->getCurrentOrPreviousAcademicYearUsingCurrentDate($currentDate, $orgId);
            verify($results)->equals($expectedResult);
            }
        , [
                "examples" => [
                    [
                       '2017-08-04 00:00:01', 9,
                        [0 =>['org_academic_year_id' => 97,
                            'year_id' => '201516',
                            'start_date' => '2015-08-10',
                            'end_date' => '2016-08-07',
                            'year_name' => '2015-2016']]
                    ],
                    [
                        '2014-05-12 00:00:01', 9,
                        []
                    ],
                    [
                        '2015-08-09 00:00:01', 203,
                        [0 =>['org_academic_year_id' => 156,
                            'year_id' => '203031',
                            'start_date' => '2014-08-11',
                            'end_date' => '2015-07-31',
                            'year_name' => '2014-2015']]
                    ],
                ]
            ]);
    }

    public function testGetAcademicYearsWithinSpecificDateRange()
    {
        $this->specify("Verify the functionality of the method getAcademicYearsWithinSpecificDateRange", function ($organizationId, $startDate, $endDate, $expectedResult) {
            $results = $this->orgAcademicYearRepository->getAcademicYearsWithinSpecificDateRange($organizationId, $startDate, $endDate);
            verify($results)->equals($expectedResult);
        }
            , [
                "examples" => [
                    [
                        203,'2016-08-16','2017-04-13',[0 =>['org_academic_year_id' => 158]]
                    ],
                    [
                        189,'2016-08-16','2017-04-13',[0 =>['org_academic_year_id' => 154]]
                    ],
                    [
                        181,'2016-08-16','2017-04-13',[0 =>['org_academic_year_id' => 176]]
                    ],
                ]
            ]);
    }


    public function testGetAllAcademicYearsForOrganization()
    {

        $this->specify("Verify the functionality of the method getAllAcademicYearsForOrganization", function ($organizationId, $expectedResult) {
            $results = $this->orgAcademicYearRepository->getAllAcademicYearsForOrganization($organizationId);
            verify($results)->equals($expectedResult);
        }
            , [
                "examples" => [

                    // lists all academic years for organization id  - 59
                    [
                        59, [
                        201516,
                        201718,
                        201819,
                        201920,
                        202021,
                        202122,
                        202223,
                        202324,
                        201617,
                        201415
                    ]
                    ],
                    // lists all academic years for organization id  - 20
                    [
                        20, [201516]
                    ],
                    // lists all academic years for organization id  - 181
                    [
                        181, [
                        201516,
                        201617
                    ]
                    ]
                ]
            ]);
    }

    // TODO: This test will fail yearly because start_date <= NOW() has been used in query.
    public function testGetPastAndCurrentAcademicYearNames()
    {
        $this->specify("Verify the functionality of the method getPastAndCurrentAcademicYearNames", function ($organizationId, $expectedResult) {
            $results = $this->orgAcademicYearRepository->getPastAndCurrentAcademicYearNames($organizationId);
            verify($results)->equals($expectedResult);
        }
            , [
                "examples" => [
                    // Lists the name of the past and current academic year for the organization Id  = 59
                    [
                        59, [
                        '2015-16 Academic Year',
                        '2017-18 Academic Year',
                        '2016-17 Academic Year',
                        '2014-15 Academic Year'
                    ]
                    ],
                    // Lists the name of the past and current academic year for the organization Id  = 20
                    [
                        20, [
                        '2015-2016'
                    ]
                    ],

                    // Lists the name of the past and current academic year for the organization Id  = 181
                    [
                        181, [
                        '2015-16',
                        '2016-17'
                    ]
                    ]
                ]
            ]);
    }

    public function testGetAllAcademicYearsWithTerms()
    {
        $this->specify("Verify the functionality of the method getPastAndCurrentAcademicYearNames", function ($organizationId, $expectedResult) {
            $results = $this->orgAcademicYearRepository->getAllAcademicYearsWithTerms($organizationId);
            verify($results)->equals($expectedResult);
        }
            , [
                "examples" => [
                    // Lists the past academic years and terms for the organization Id  = 59
                    [
                        59, [
                            0 => [
                                'id' => '190',
                                'year_name' => '2023-24 Academic Year',
                                'year_id' => '202324',
                                'year_start_date' => '2023-08-01',
                                'year_end_date' => '2024-06-30',
                                'year_status' => 'future',
                                'term_id' => '',
                                'term_name' => '',
                                'term_start_date' => '',
                                'term_end_date' => '',
                                'term_code' => ''
                            ],
                            1 => [
                                'id' => '189',
                                'year_name' => '2022-23 Academic Year',
                                'year_id' => '202223',
                                'year_start_date' => '2022-08-01',
                                'year_end_date' => '2023-06-30',
                                'year_status' => 'future',
                                'term_id' => '',
                                'term_name' => '',
                                'term_start_date' => '',
                                'term_end_date' => '',
                                'term_code' => ''
                            ],
                            2 => [
                                'id' => '188',
                                'year_name' => '2021-22 Academic Year',
                                'year_id' => '202122',
                                'year_start_date' => '2021-08-01',
                                'year_end_date' => '2022-06-30',
                                'year_status' => 'future',
                                'term_id' => '',
                                'term_name' => '',
                                'term_start_date' => '',
                                'term_end_date' => '',
                                'term_code' => ''
                            ],
                            3 => [
                                'id' => '187',
                                'year_name' => '2020-21 Academic Year',
                                'year_id' => '202021',
                                'year_start_date' => '2020-08-01',
                                'year_end_date' => '2021-06-30',
                                'year_status' => 'future',
                                'term_id' => '',
                                'term_name' => '',
                                'term_start_date' => '',
                                'term_end_date' => '',
                                'term_code' => ''
                            ],
                            4 => [
                                'id' => '186',
                                'year_name' => '2019-20 Academic Year',
                                'year_id' => '201920',
                                'year_start_date' => '2019-08-01',
                                'year_end_date' => '2020-06-30',
                                'year_status' => 'future',
                                'term_id' => '',
                                'term_name' => '',
                                'term_start_date' => '',
                                'term_end_date' => '',
                                'term_code' => ''
                            ],
                            5 => [
                                'id' => '185',
                                'year_name' => '2018-19 Academic Year',
                                'year_id' => '201819',
                                'year_start_date' => '2018-08-01',
                                'year_end_date' => '2019-06-30',
                                'year_status' => 'future',
                                'term_id' => '',
                                'term_name' => '',
                                'term_start_date' => '',
                                'term_end_date' => '',
                                'term_code' => ''
                            ],
                            6 => [
                                'id' => '184',
                                'year_name' => '2017-18 Academic Year',
                                'year_id' => '201718',
                                'year_start_date' => '2017-08-01',
                                'year_end_date' => '2018-06-30',
                                'year_status' => 'current',
                                'term_id' => '',
                                'term_name' => '',
                                'term_start_date' => '',
                                'term_end_date' => '',
                                'term_code' => ''
                            ],
                            7 => [
                                'id' => '194',
                                'year_name' => '2016-17 Academic Year',
                                'year_id' => '201617',
                                'year_start_date' => '2016-08-01',
                                'year_end_date' => '2017-06-30',
                                'year_status' => 'past',
                                'term_id' => '',
                                'term_name' => '',
                                'term_start_date' => '',
                                'term_end_date' => '',
                                'term_code' => ''
                            ],
                            8 => [
                                'id' => '94',
                                'year_name' => '2015-16 Academic Year',
                                'year_id' => '201516',
                                'year_start_date' => '2015-08-01',
                                'year_end_date' => '2016-06-30',
                                'year_status' => 'past',
                                'term_id' => '213',
                                'term_name' => 'Spring Semester 2016',
                                'term_start_date' => '2016-01-13',
                                'term_end_date' => '2016-05-18',
                                'term_code' => '2162'

                            ],
                            9 => [
                                'id' => '94',
                                'year_name' => '2015-16 Academic Year',
                                'year_id' => '201516',
                                'year_start_date' => '2015-08-01',
                                'year_end_date' => '2016-06-30',
                                'year_status' => 'past',
                                'term_id' => '212',
                                'term_name' => 'Fall Semester 2015',
                                'term_start_date' => '2015-08-17',
                                'term_end_date' => '2015-12-22',
                                'term_code' => '2154'
                            ],
                            10 => [
                                'id' => '204',
                                'year_name' => '2014-15 Academic Year',
                                'year_id' => '201415',
                                'year_start_date' => '2014-08-01',
                                'year_end_date' => '2015-06-30',
                                'year_status' => 'past',
                                'term_id' => '',
                                'term_name' => '',
                                'term_start_date' => '',
                                'term_end_date' => '',
                                'term_code' => ''
                            ]
                        ]

                    ],
                    // Lists the past and current academic year and terms for the organization Id  = 99
                    [
                        99, [
                            0 => [
                                'id' => '168',
                                'year_name' => '2016-2017 Academic Year',
                                'year_id' => '201617',
                                'year_start_date' => '2016-08-15',
                                'year_end_date' => '2017-08-09',
                                'year_status' => 'past',
                                'term_id' => '423',
                                'term_name' => 'Summer Semester',
                                'term_start_date' => '2017-05-08',
                                'term_end_date' => '2017-08-09',
                                'term_code' => 'Summer2017'
                            ],
                            1 => [
                                'id' => '168',
                                'year_name' => '2016-2017 Academic Year',
                                'year_id' => '201617',
                                'year_start_date' => '2016-08-15',
                                'year_end_date' => '2017-08-09',
                                'year_status' => 'past',
                                'term_id' => '422',
                                'term_name' => 'Spring Semester',
                                'term_start_date' => '2017-01-09',
                                'term_end_date' => '2017-05-10',
                                'term_code' => 'Spring2017'
                            ],
                            2 => [
                                'id' => '168',
                                'year_name' => '2016-2017 Academic Year',
                                'year_id' => '201617',
                                'year_start_date' => '2016-08-15',
                                'year_end_date' => '2017-08-09',
                                'year_status' => 'past',
                                'term_id' => '421',
                                'term_name' => 'Fall Semester',
                                'term_start_date' => '2016-08-15',
                                'term_end_date' => '2016-12-14',
                                'term_code' => 'Fall2016'
                            ],
                            3 => [
                                'id' => '27',
                                'year_name' => '2015-2016 Academic Year',
                                'year_id' => '201516',
                                'year_start_date' => '2015-08-01',
                                'year_end_date' => '2016-08-09',
                                'year_status' => 'past',
                                'term_id' => '60',
                                'term_name' => 'Summer Semester',
                                'term_start_date' => '2016-05-09',
                                'term_end_date' => '2016-08-09',
                                'term_code' => 'Summer2016'
                            ],
                            4 => [
                                'id' => '27',
                                'year_name' => '2015-2016 Academic Year',
                                'year_id' => '201516',
                                'year_start_date' => '2015-08-01',
                                'year_end_date' => '2016-08-09',
                                'year_status' => 'past',
                                'term_id' => '59',
                                'term_name' => 'Spring Semester',
                                'term_start_date' => '2016-01-11',
                                'term_end_date' => '2016-05-10',
                                'term_code' => 'Spring2016'
                            ],
                            5 => [
                                'id' => '27',
                                'year_name' => '2015-2016 Academic Year',
                                'year_id' => '201516',
                                'year_start_date' => '2015-08-01',
                                'year_end_date' => '2016-08-09',
                                'year_status' => 'past',
                                'term_id' => '58',
                                'term_name' => 'Fall Semester',
                                'term_start_date' => '2015-08-17',
                                'term_end_date' => '2015-12-15',
                                'term_code' => 'Fall2015'
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function testGetOrgAcademicYearsDetails()
    {
        $this->specify("Verify the functionality of the method getOrgAcademicYearsDetails", function ($organizationId, $organizationAcademicYearId, $expectedResult) {
            $results = $this->orgAcademicYearRepository->getOrgAcademicYearsDetails($organizationId, $organizationAcademicYearId);
            verify($results)->equals($expectedResult);
        }
            , [
                "examples" => [

                    // Test 01 - valid organization and valid organization academic year id will return result array
                    [
                        99,
                        27,
                        [
                            0 =>[
                                'id' => '168',
                                'startDate' => new \DateTime('2016-08-15 00:00:00'),
                                'endDate' =>  new \DateTime('2017-08-09 00:00:00')
                            ]
                        ]
                    ],
                    // Test 02 - valid organization and valid organization academic year id will return multiple result array
                    [
                        99,
                        28,
                        [
                            0 =>[
                                'id' => '27',
                                'startDate' => new \DateTime('2015-08-01 00:00:00'),
                                'endDate' =>  new \DateTime('2016-08-09 00:00:00')
                            ],
                            1 =>[
                                'id' => '168',
                                'startDate' => new \DateTime('2016-08-15 00:00:00'),
                                'endDate' =>  new \DateTime('2017-08-09 00:00:00')
                            ]
                        ]
                    ],
                    // Test 03 - invalid organization and valid organization academic year id will return empty result array
                    [
                        null,
                        27,
                        []
                    ],
                    // Test 04 - valid organization and invalid organization academic year id will return empty result array
                    [
                        99,
                        null,
                        []
                    ],
                    // Test 05 - Invalid organization and invalid organization academic year id will return empty result array test case
                    [
                        -1,
                        -1,
                        []
                    ],
                ]
            ]);
    }

    public function testGetCurrentYearId()
    {
        $this->specify("Verify the functionality of the method getCurrentYearId", function ($currentDate, $organizationId, $organizationAcademicYearId, $expectedResult, $expectedCount) {
            $results = $this->orgAcademicYearRepository->getCurrentYearId($currentDate,$organizationId, $organizationAcademicYearId);
            verify($results)->equals($expectedResult);
            verify(count($results))->equals($expectedCount);
        }
            , [
                "examples" => [

                    // Test 01 - valid current date, organization and organization academic year id will return current year id for the organization
                    [
                        '2015-09-17 10:24:12',
                        99,
                        27,
                        [
                            0 =>[
                                'id' => '27',
                                'yearId' =>'201516'
                            ]
                        ],
                        1
                    ],
                    // Test 02 - valid current date, organization id and organization academic year id as null will return current year id for the organization
                    [
                        '2015-09-17 10:24:12',
                        99,
                        '',
                        [
                            0 =>[
                                'id' => '27',
                                'yearId' =>'201516'
                            ]
                        ],
                        1
                    ],
                    // Test 03 - Invalid Organization id and valid current date and organization academic year id as null will return empty result test case array
                    [
                        '2015-09-17 10:24:12',
                        -1,
                        null,
                        [],
                        0
                    ],
                    // Test 04 - Valid Organization id and invalid current date and organization academic year id as null will return empty result test case array
                    [
                        '-1',
                        99,
                        null,
                        [],
                        0
                    ],
                    // Test 05 - Current date, organization id and organization academic year id as null will return empty result test case array
                    [
                        null,
                        null,
                        null,
                        [],
                        0
                    ]
                ]
            ]);
    }

    public function testGetCurrentAcademicDetails()
    {
        $this->specify("Verify the functionality of the method getCurrentAcademicDetails", function ($currentDate, $organizationId, $expectedResult, $expectedCount) {
            $results = $this->orgAcademicYearRepository->getCurrentAcademicDetails($currentDate, $organizationId);
            verify($results)->equals($expectedResult);
            verify(count($results))->equals($expectedCount);
        }
            , [
                "examples" => [

                    // Test 01 - valid current date, and organization will return result array
                    [
                        '2015-09-17 10:24:12',
                        99,
                        [
                            0 =>[
                                'id' => 27,
                                'yearId' =>'201516',
                                'startDate' =>'2015-08-01',
                                'endDate' =>'2016-08-09',
                                'year_name' =>'2015-2016 Academic Year'
                            ]
                        ],
                        1
                    ],
                    // Test 02 - Invalid current date, and valid organization will return result array with null values
                    [
                        null,
                        99,
                        [
                            0 =>[
                                'id' => null,
                                'yearId' =>null,
                                'startDate' =>null,
                                'endDate' =>null,
                                'year_name' =>null
                            ]
                        ],
                        1
                    ],
                    // Test 03 - Invalid current date, and organization will return result array with null values
                    [
                        null,
                        null,
                        [
                            0 =>[
                                'id' => null,
                                'yearId' =>null,
                                'startDate' =>null,
                                'endDate' =>null,
                                'year_name' =>null
                            ]
                        ],
                        1
                    ],
                ]
            ]);
    }

    public function testGetCountCurrentAcademic()
    {
        $this->specify("Verify the functionality of the method getCountCurrentAcademic", function ($currentDate, $organizationId, $expectedResult, $expectedCount) {
            $results = $this->orgAcademicYearRepository->getCountCurrentAcademic($currentDate, $organizationId);
            verify($results)->equals($expectedResult);
            verify(count($results))->equals($expectedCount);
        }
            , [
                "examples" => [

                    // Test 01 - valid current date, and organization will return result array
                    [
                        '2015-09-17 10:24:12',
                        99,
                        [
                            0 =>[
                                'oayCount' => 1
                            ]
                        ],
                        1
                    ],
                    // Test 02 - Invalid current date, and valid organization will return result array with zero values
                    [
                        null,
                        99,
                        [
                            0 =>[
                                'oayCount' => 0
                            ]
                        ],
                        1
                    ],
                    // Test 03 - Invalid current date, and organization will return result array with zero values
                    [
                        null,
                        null,
                        [
                            0 =>[
                                'oayCount' => 0
                            ]
                        ],
                        1
                    ],
                ]
            ]);
    }

    public function testFindFutureYears()
    {
        $this->specify("Verify the functionality of the method findFutureYears", function ($organizationId, $startDate, $limit, $expectedResult, $expectedCount) {
            $results = $this->orgAcademicYearRepository->findFutureYears($organizationId, $startDate, $limit);
            verify($results)->equals($expectedResult);
            verify(count($results))->equals($expectedCount);
        }
            , [
                "examples" => [

                    // Test 01 - Valid organization and current date will return future years result array
                    [
                        99,
                        '2015-08-01 00:00:00',
                        3,
                        [
                            0 =>27,
                            1 =>168
                        ],
                        2
                    ],
                    // Test 02 - Invalid organization and valid current date will return empty result array
                    [
                        null,
                        '2015-08-01 00:00:00',
                        '',
                        [],
                        0
                    ],
                    // Test 03 - valid organization and invalid current date will return empty result array
                    [
                        99,
                        null,
                        '',
                        [],
                        0
                    ],
                    // Test 04 - Invalid both organization and current date will return empty result array
                    [
                        null,
                        null,
                        '',
                        [],
                        0
                    ],
                ]
            ]);
    }

    public function testDeterminePastCurrentOrFutureYear()
    {
        $this->specify("Verify the functionality of the method determinePastCurrentOrFutureYear", function ($organizationAcademicYearId, $date, $expectedResult) {
            $results = $this->orgAcademicYearRepository->determinePastCurrentOrFutureYear($organizationAcademicYearId, $date);
            verify($results)->equals($expectedResult);
            $this->currentYear = new \DateTime();
            $this->currentYear = $this->currentYear->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
        }
            , [
                "examples" => [

                    // Test 01 - Valid organization academic year and date will return current year
                    [
                        27,
                        '2015-09-17 10:24:12',
                        'current',
                    ],
                    // Test 02 - Valid organization academic year, null date, defaults to now(). Organization academic year occurred in the past.
                    [
                        27,
                        null,
                        'past',
                    ],
                    // Test 03 - Valid organization academic year and given date is less than(<)organization academic year start date will return future year
                    [
                        27,
                        '2015-07-01 00:00:00',
                        'future',
                    ],
                    // Test 04 - Organization academic year id and date as null will return empty string
                    [
                        null,
                        null,
                        '',
                    ],
                    // Test 05 - Invalid Organization academic year id and valid past year date will return empty string
                    [
                        -27,
                        '2000-09-17 10:24:12',
                        '',
                    ],
                    // Test 06 - Invalid Organization academic year id and valid present year date will return empty string
                    [
                        -27,
                        $this->currentYear,
                        '',
                    ],
                    // Test 07 - Invalid Organization academic year id and valid future year date will return empty string
                    [
                        -27,
                        '2020-09-17 10:24:12',
                        '',
                    ]
                ]
            ]);
    }
}