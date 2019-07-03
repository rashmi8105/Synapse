<?php
namespace Synapse\SurveyBundle\Service\Impl;

use Codeception\Specify;
use Codeception\Test\Unit;
use JMS\DiExtraBundle\Annotation as DI;

class CohortsServiceTest extends Unit
{
    use Specify;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRepositoryResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLogger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContainer;


    protected function _before()
    {
        $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
        $this->mockLogger = $this->getMock('Logger', array('debug','error'));
        $this->mockContainer = $this->getMock('Container', array('get'));
    }

    public function testGetCohortsAndSurveysForOrganizationForSetup()
    {
        $this->specify("Validate the method getCohortsAndSurveysForOrganizationForSetup", function($purpose, $rawDatabaseRecords, $expectedResult) {

            // Declaring mock repositories and services
            $mockWessLinkRepository = $this->getMock('WessLinkRepository', ['getCohortsAndSurveysForOrganizationForSetup']);
            $mockDateUtilityService = $this->getMock('DateUtilityService', ['convertDatabaseStringToISOString']);

            // Mocking method calls
            $this->mockRepositoryResolver->method('getRepository')->willReturn($mockWessLinkRepository);

            $this->mockContainer->method('get')->willReturn($mockDateUtilityService);

            $mockWessLinkRepository->method('getCohortsAndSurveysForOrganizationForSetup')->willReturn($rawDatabaseRecords);

            $mockDateUtilityService->method('convertDatabaseStringToISOString')->willReturnMap(
                [
                    ['2015-09-10 06:00:00', '2015-09-10T06:00:00+0000'],
                    ['2015-10-31 06:00:00', '2015-10-31T06:00:00+0000']
                ]
            );

            $orgId = 118;     // irrelevant for the unit test

            // Call the function to be tested and verify results.
            $cohortsService = new CohortsService($this->mockRepositoryResolver, $this->mockContainer, $this->mockLogger);
            $output = $cohortsService->getCohortsAndSurveysForOrganizationForSetup($orgId, $purpose);

            verify($output)->equals($expectedResult);

        }, ['examples' => [
            // Example 1: $purpose = 'isq'
            ['isq',
                // raw database records, output of WessLinkRepositoryTest::testGetCohortsAndSurveysForOrganizationForSetup (with some date changes to simplify things)
                [
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '1',
                        'cohort_name' => 'Survey Cohort 1',
                        'survey_id' => '11',
                        'survey_name' => 'Transition One',
                        'status' => 'closed',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '2',
                        'cohort_name' => 'Survey Cohort 2',
                        'survey_id' => '11',
                        'survey_name' => 'Transition One',
                        'status' => 'closed',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '2',
                        'cohort_name' => 'Survey Cohort 2',
                        'survey_id' => '12',
                        'survey_name' => 'Check-Up One',
                        'status' => 'open',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '3',
                        'cohort_name' => 'Survey Cohort 3',
                        'survey_id' => '11',
                        'survey_name' => 'Transition One',
                        'status' => 'closed',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ]
                ],
                // reformatted result
                [
                    'org_id' => 118,
                    'cohorts' => [
                        [
                            'year_id'=> 201516,
                            'org_academic_year_id' => '23',
                            'year_name' => '2015-16',
                            'cohort' => 1,
                            'cohort_name' => 'Survey Cohort 1',
                            'surveys' => [
                                [
                                    'survey_id' => 11,
                                    'survey_name' => 'Transition One',
                                    'status' => 'closed',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000'
                                ]
                            ]
                        ],
                        [
                            'year_id'=> 201516,
                            'org_academic_year_id' => '23',
                            'year_name' => '2015-16',
                            'cohort' => 2,
                            'cohort_name' => 'Survey Cohort 2',
                            'surveys' => [
                                [
                                    'survey_id' => 11,
                                    'survey_name' => 'Transition One',
                                    'status' => 'closed',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000'
                                ],
                                [
                                    'survey_id' => 12,
                                    'survey_name' => 'Check-Up One',
                                    'status' => 'open',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000'
                                ]
                            ]
                        ],
                        [
                            'year_id'=> 201516,
                            'org_academic_year_id' => '23',
                            'year_name' => '2015-16',
                            'cohort' => 3,
                            'cohort_name' => 'Survey Cohort 3',
                            'surveys' => [
                                [
                                    'survey_id' => 11,
                                    'survey_name' => 'Transition One',
                                    'status' => 'closed',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // Example 2: $purpose = 'survey_setup'
            ['survey_setup',
                // raw database records, output of WessLinkRepositoryTest::testGetCohortsAndSurveysForOrganizationForSetup (with some date changes to simplify things)
                [
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '1',
                        'cohort_name' => 'Survey Cohort 1',
                        'survey_id' => '11',
                        'survey_name' => 'Transition One',
                        'status' => 'closed',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '1',
                        'cohort_name' => 'Survey Cohort 1',
                        'survey_id' => '12',
                        'survey_name' => 'Check-Up One',
                        'status' => 'open',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '1',
                        'cohort_name' => 'Survey Cohort 1',
                        'survey_id' => '13',
                        'survey_name' => 'Transition Two',
                        'status' => 'open',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '1',
                        'cohort_name' => 'Survey Cohort 1',
                        'survey_id' => '14',
                        'survey_name' => 'Check-Up Two',
                        'status' => 'open',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '2',
                        'cohort_name' => 'Survey Cohort 2',
                        'survey_id' => '11',
                        'survey_name' => 'Transition One',
                        'status' => 'closed',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '2',
                        'cohort_name' => 'Survey Cohort 2',
                        'survey_id' => '12',
                        'survey_name' => 'Check-Up One',
                        'status' => 'open',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '2',
                        'cohort_name' => 'Survey Cohort 2',
                        'survey_id' => '13',
                        'survey_name' => 'Transition Two',
                        'status' => 'open',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '2',
                        'cohort_name' => 'Survey Cohort 2',
                        'survey_id' => '14',
                        'survey_name' => 'Check-Up Two',
                        'status' => 'open',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '3',
                        'cohort_name' => 'Survey Cohort 3',
                        'survey_id' => '11',
                        'survey_name' => 'Transition One',
                        'status' => 'closed',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '3',
                        'cohort_name' => 'Survey Cohort 3',
                        'survey_id' => '12',
                        'survey_name' => 'Check-Up One',
                        'status' => 'open',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '3',
                        'cohort_name' => 'Survey Cohort 3',
                        'survey_id' => '13',
                        'survey_name' => 'Transition Two',
                        'status' => 'open',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '3',
                        'cohort_name' => 'Survey Cohort 3',
                        'survey_id' => '14',
                        'survey_name' => 'Check-Up Two',
                        'status' => 'open',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '4',
                        'cohort_name' => 'Survey Cohort 4',
                        'survey_id' => '11',
                        'survey_name' => 'Transition One',
                        'status' => 'closed',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '4',
                        'cohort_name' => 'Survey Cohort 4',
                        'survey_id' => '12',
                        'survey_name' => 'Check-Up One',
                        'status' => 'open',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '4',
                        'cohort_name' => 'Survey Cohort 4',
                        'survey_id' => '13',
                        'survey_name' => 'Transition Two',
                        'status' => 'open',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ],
                    [
                        'year_id' => '201516',
                        'org_academic_year_id' => '23',
                        'year_name' => '2015-16',
                        'cohort' => '4',
                        'cohort_name' => 'Survey Cohort 4',
                        'survey_id' => '14',
                        'survey_name' => 'Check-Up Two',
                        'status' => 'open',
                        'open_date' => '2015-09-10 06:00:00',
                        'close_date' => '2015-10-31 06:00:00',
                        'wess_admin_link' => null
                    ]
                ],
                // reformatted result
                [
                    'org_id' => 118,
                    'cohorts' => [
                        [
                            'year_id'=> 201516,
                            'org_academic_year_id' => '23',
                            'year_name' => '2015-16',
                            'cohort' => 1,
                            'cohort_name' => 'Survey Cohort 1',
                            'surveys' => [
                                [
                                    'survey_id' => 11,
                                    'survey_name' => 'Transition One',
                                    'status' => 'closed',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000',
                                    'wess_admin_link' => null
                                ],
                                [
                                    'survey_id' => 12,
                                    'survey_name' => 'Check-Up One',
                                    'status' => 'open',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000',
                                    'wess_admin_link' => null
                                ],
                                [
                                    'survey_id' => 13,
                                    'survey_name' => 'Transition Two',
                                    'status' => 'open',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000',
                                    'wess_admin_link' => null
                                ],
                                [
                                    'survey_id' => 14,
                                    'survey_name' => 'Check-Up Two',
                                    'status' => 'open',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000',
                                    'wess_admin_link' => null
                                ]
                            ]
                        ],
                        [
                            'year_id'=> 201516,
                            'org_academic_year_id' => '23',
                            'year_name' => '2015-16',
                            'cohort' => 2,
                            'cohort_name' => 'Survey Cohort 2',
                            'surveys' => [
                                [
                                    'survey_id' => 11,
                                    'survey_name' => 'Transition One',
                                    'status' => 'closed',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000',
                                    'wess_admin_link' => null
                                ],
                                [
                                    'survey_id' => 12,
                                    'survey_name' => 'Check-Up One',
                                    'status' => 'open',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000',
                                    'wess_admin_link' => null
                                ],
                                [
                                    'survey_id' => 13,
                                    'survey_name' => 'Transition Two',
                                    'status' => 'open',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000',
                                    'wess_admin_link' => null
                                ],
                                [
                                    'survey_id' => 14,
                                    'survey_name' => 'Check-Up Two',
                                    'status' => 'open',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000',
                                    'wess_admin_link' => null
                                ]
                            ]
                        ],
                        [
                            'year_id'=> 201516,
                            'org_academic_year_id' => '23',
                            'year_name' => '2015-16',
                            'cohort' => 3,
                            'cohort_name' => 'Survey Cohort 3',
                            'surveys' => [
                                [
                                    'survey_id' => 11,
                                    'survey_name' => 'Transition One',
                                    'status' => 'closed',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000',
                                    'wess_admin_link' => null
                                ],
                                [
                                    'survey_id' => 12,
                                    'survey_name' => 'Check-Up One',
                                    'status' => 'open',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000',
                                    'wess_admin_link' => null
                                ],
                                [
                                    'survey_id' => 13,
                                    'survey_name' => 'Transition Two',
                                    'status' => 'open',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000',
                                    'wess_admin_link' => null
                                ],
                                [
                                    'survey_id' => 14,
                                    'survey_name' => 'Check-Up Two',
                                    'status' => 'open',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000',
                                    'wess_admin_link' => null
                                ]
                            ]
                        ],
                        [
                            'year_id'=> 201516,
                            'org_academic_year_id' => '23',
                            'year_name' => '2015-16',
                            'cohort' => 4,
                            'cohort_name' => 'Survey Cohort 4',
                            'surveys' => [
                                [
                                    'survey_id' => 11,
                                    'survey_name' => 'Transition One',
                                    'status' => 'closed',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000',
                                    'wess_admin_link' => null
                                ],
                                [
                                    'survey_id' => 12,
                                    'survey_name' => 'Check-Up One',
                                    'status' => 'open',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000',
                                    'wess_admin_link' => null
                                ],
                                [
                                    'survey_id' => 13,
                                    'survey_name' => 'Transition Two',
                                    'status' => 'open',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000',
                                    'wess_admin_link' => null
                                ],
                                [
                                    'survey_id' => 14,
                                    'survey_name' => 'Check-Up Two',
                                    'status' => 'open',
                                    'open_date' => '2015-09-10T06:00:00+0000',
                                    'close_date' => '2015-10-31T06:00:00+0000',
                                    'wess_admin_link' => null
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]]);
    }
}