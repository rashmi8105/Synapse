<?php
namespace Synapse\CoreBundle\Service\Impl;

use Codeception\Specify;
use Codeception\Test\Unit;

class SurveyServiceTest extends Unit
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
        $this->mockLogger = $this->getMock('Logger', array('debug', 'error'));
        $this->mockContainer = $this->getMock('Container', array('get'));
    }

    public function testGetSurveysAndCohorts()
    {
        $this->specify('test get surveys and cohorts', function ($includeCompletionData, $hasCoordinatorAccess, $purpose, $rawSurveyAndCohortData, $expectedResult) {

            //Mock away outside functions
            $mockRoleService = $this->getMock('RoleService', ['hasCoordinatorOmniscience']);
            $mockWessLinkRepository = $this->getMock('wessLinkRepository', array(
                  'getSurveysAndCohortsForOrganizationWithoutPermissionCheck',
                  'getSurveysAndCohortsForOrganizationWithoutCompletionData',
                  'getSurveysAndCohortsForOrganizationWithCompletionData'
                ));

            $mockDateUtilityService = $this->getMock('DateUtilityService', ['convertDatabaseStringToISOString']);
            $mockISQPermissionsetService = $this->getMock('ISQPermissionsetService', array('getSurveysAndCohortsHavingAccessibleISQs'));

            //mocking away all function calls outside of the tested function
            $mockRoleService->method('hasCoordinatorOmniscience')->willReturn($hasCoordinatorAccess);

            $this->mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    ['SynapseSurveyBundle:WessLink', $mockWessLinkRepository]
                ]
            );

            $this->mockContainer->method('get')->willReturnMap(
                [
                    ['role_service', $mockRoleService],
                    ['date_utility_service', $mockDateUtilityService],
                    ['isq_permissionset_service', $mockISQPermissionsetService]
                ]
            );

            $mockDateUtilityService->method('convertDatabaseStringToISOString')->willReturnMap(
                [
                    ['2015-09-01 06:00:00', '2015-09-01T06:00:00+0000'],
                    ['2015-10-31 06:00:00', '2015-10-31T06:00:00+0000']
                ]
            );

            $mockISQPermissionsetService->method('getSurveysAndCohortsHavingAccessibleISQs')->willReturn($rawSurveyAndCohortData);

            if ($purpose != 'isq_access') {

                if (!$includeCompletionData && $hasCoordinatorAccess) {
                    $mockWessLinkRepository->expects($this->atLeastOnce())->method('getSurveysAndCohortsForOrganizationWithoutPermissionCheck')->willReturn($rawSurveyAndCohortData);

                } else if (!$includeCompletionData) {
                    $mockWessLinkRepository->expects($this->atLeastOnce())->method('getSurveysAndCohortsForOrganizationWithoutCompletionData')->willReturn($rawSurveyAndCohortData);

                } else {
                    $mockWessLinkRepository->expects($this->atLeastOnce())->method('getSurveysAndCohortsForOrganizationWithCompletionData')->willReturn($rawSurveyAndCohortData);

                }
            }

            // Data that's irrelevant to the unit test:
            $orgId = 1;
            $personId = 12345;
            $orgAcademicYearId = null;
            $surveyStatus = null;
            $surveyId = null;
            $isAggregateReporting = null;

            //Creating class
            $surveyService = new SurveyService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);

            //Calling function
            $functionResults = $surveyService->getSurveysAndCohorts($orgId, $personId, $orgAcademicYearId, $surveyStatus, $surveyId, $includeCompletionData, $purpose, $isAggregateReporting);

            $this->assertEquals($expectedResult, $functionResults);

        }, ['examples' =>
            [
                // Example 1:  One survey and one cohort without completion data.
                [false, true, null,
                    // raw database record
                    [
                        [
                            "year_id" => "201516",
                            "open_date" => "2015-09-01 06:00:00",
                            "close_date" => "2015-09-01 06:00:00",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "status" => "closed",
                            "survey_name" => "Transition One",
                            "org_academic_year_id" => 23,
                            "survey_id" => 11,
                            "year_name" => "YEARNAME"
                        ]
                    ],
                    // reformatted result
                    [
                        "organization_id" => 1,
                        "surveys" => [
                            [
                                "year_id" => 201516,
                                "org_academic_year_id" => 23,
                                "survey_id" => 11,
                                "survey_name" => "Transition One",
                                "year_name" => "YEARNAME",
                                "cohorts" => [
                                    [
                                        'cohort' => 1,
                                        'cohort_name' => "Survey Cohort 1",
                                        'status' => "closed",
                                        'open_date' => "2015-09-01T06:00:00+0000",
                                        'close_date' => "2015-09-01T06:00:00+0000"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                // Example 2:  Multiple surveys and cohorts, without completion data.
                [false, false, null,
                    // raw database records
                    [
                        [
                            "year_id" => "201516",
                            "open_date" => "2015-09-01 06:00:00",
                            "close_date" => "2015-09-01 06:00:00",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "status" => "closed",
                            "survey_name" => "Transition Two",
                            "org_academic_year_id" => 23,
                            "year_name" => "YEARNAME",
                            "survey_id" => 13
                        ],
                        [
                            "year_id" => "201516",
                            "open_date" => "2015-09-01 06:00:00",
                            "close_date" => "2015-09-01 06:00:00",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "status" => "closed",
                            "survey_name" => "Transition One",
                            "org_academic_year_id" => 23,
                            "year_name" => "YEARNAME",
                            "survey_id" => 11
                        ],
                        [
                            "year_id" => "201516",
                            "open_date" => "2015-09-01 06:00:00",
                            "close_date" => "2015-09-01 06:00:00",
                            "cohort" => "2",
                            "cohort_name" => "Survey Cohort 2",
                            "status" => "closed",
                            "survey_name" => "Transition One",
                            "org_academic_year_id" => 23,
                            "year_name" => "YEARNAME",
                            "survey_id" => 11
                        ],
                        [
                            "year_id" => "201516",
                            "open_date" => "2015-09-01 06:00:00",
                            "close_date" => "2015-09-01 06:00:00",
                            "cohort" => "3",
                            "cohort_name" => "Survey Cohort 3",
                            "status" => "closed",
                            "survey_name" => "Transition One",
                            "org_academic_year_id" => 23,
                            "year_name" => "YEARNAME",
                            "survey_id" => 11
                        ]
                    ],
                    // reformatted result
                    [
                        "organization_id" => 1,
                        "surveys" => [
                            [
                                "year_id" => 201516,
                                "org_academic_year_id" => 23,
                                "survey_id" => 13,
                                "survey_name" => "Transition Two",
                                "year_name" => "YEARNAME",
                                "cohorts" => [
                                    [
                                        'cohort' => 1,
                                        'cohort_name' => "Survey Cohort 1",
                                        'status' => "closed",
                                        'open_date' => "2015-09-01T06:00:00+0000",
                                        'close_date' => "2015-09-01T06:00:00+0000"
                                    ]
                                ]
                            ],
                            [
                                "year_id" => 201516,
                                "org_academic_year_id" => 23,
                                "survey_id" => 11,
                                "survey_name" => "Transition One",
                                "year_name" => "YEARNAME",
                                "cohorts" => [
                                    [
                                        "cohort" => 1,
                                        "cohort_name" => "Survey Cohort 1",
                                        "status" => "closed",
                                        "open_date" => "2015-09-01T06:00:00+0000",
                                        "close_date" => "2015-09-01T06:00:00+0000"
                                    ],
                                    [
                                        "cohort" => 2,
                                        "cohort_name" => "Survey Cohort 2",
                                        "status" => "closed",
                                        "open_date" => "2015-09-01T06:00:00+0000",
                                        "close_date" => "2015-09-01T06:00:00+0000"
                                    ],
                                    [
                                        "cohort" => 3,
                                        "cohort_name" => "Survey Cohort 3",
                                        "status" => "closed",
                                        "open_date" => "2015-09-01T06:00:00+0000",
                                        "close_date" => "2015-09-01T06:00:00+0000"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                // Example 3:  One survey and one cohort, with completion data.
                [true, true, null,
                    // raw database record
                    [
                        [
                            "year_id" => "201516",
                            "open_date" => "2015-09-01 06:00:00",
                            "close_date" => "2015-09-01 06:00:00",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "status" => "closed",
                            "survey_name" => "Transition One",
                            "org_academic_year_id" => 23,
                            "year_name" => "YEARNAME",
                            "survey_id" => 11,
                            "student_count" => 100,
                            "students_responded_count" => 75
                        ]
                    ],
                    // reformatted result
                    [
                        "organization_id" => 1,
                        "surveys" => [
                            [
                                "year_id" => "201516",
                                "org_academic_year_id" => 23,
                                "survey_id" => 11,
                                "survey_name" => "Transition One",
                                "year_name" => "YEARNAME",
                                "cohorts" => [
                                    [
                                        'cohort' => 1,
                                        'cohort_name' => "Survey Cohort 1",
                                        'status' => "closed",
                                        'open_date' => "2015-09-01T06:00:00+0000",
                                        'close_date' => "2015-09-01T06:00:00+0000",
                                        "student_count" => 100,
                                        "students_responded_count" => 75,
                                        'percentage_responded' => 75
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                // Example 4:  Multiple surveys and cohorts, with completion data.
                [true, false, null,
                    // raw database records
                    [
                        [
                            "year_id" => "201516",
                            "open_date" => "2015-09-01 06:00:00",
                            "close_date" => "2015-09-01 06:00:00",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "status" => "closed",
                            "survey_name" => "Transition One",
                            "org_academic_year_id" => 23,
                            "year_name" => "YEARNAME",
                            "survey_id" => 11,
                            "student_count" => 100,
                            "students_responded_count" => 75
                        ],
                        [
                            "year_id" => "201516",
                            "open_date" => "2015-09-01 06:00:00",
                            "close_date" => "2015-09-01 06:00:00",
                            "cohort" => "2",
                            "cohort_name" => "Survey Cohort 2",
                            "status" => "closed",
                            "survey_name" => "Transition One",
                            "org_academic_year_id" => 23,
                            "year_name" => "YEARNAME",
                            "survey_id" => 11,
                            "student_count" => 100,
                            "students_responded_count" => 75
                        ],
                        [
                            "year_id" => "201516",
                            "open_date" => "2015-09-01 06:00:00",
                            "close_date" => "2015-09-01 06:00:00",
                            "cohort" => "3",
                            "cohort_name" => "Survey Cohort 3",
                            "status" => "closed",
                            "survey_name" => "Transition One",
                            "org_academic_year_id" => 23,
                            "year_name" => "YEARNAME",
                            "survey_id" => 11,
                            "student_count" => 100,
                            "students_responded_count" => 75
                        ],
                        [
                            "year_id" => "201516",
                            "open_date" => "2015-09-01 06:00:00",
                            "close_date" => "2015-09-01 06:00:00",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "status" => "closed",
                            "survey_name" => "Transition Two",
                            "org_academic_year_id" => 23,
                            "year_name" => "YEARNAME",
                            "survey_id" => 13,
                            "student_count" => 100,
                            "students_responded_count" => 75
                        ]
                    ],
                    // reformatted result
                    [
                        "organization_id" => 1,
                        "surveys" => [
                            [
                                "year_id" => 201516,
                                "org_academic_year_id" => 23,
                                "survey_id" => 13,
                                "survey_name" => "Transition Two",
                                "year_name" => "YEARNAME",
                                "cohorts" => [
                                    [
                                        'cohort' => 1,
                                        'cohort_name' => "Survey Cohort 1",
                                        'status' => "closed",
                                        'open_date' => "2015-09-01T06:00:00+0000",
                                        'close_date' => "2015-09-01T06:00:00+0000",
                                        "student_count" => 100,
                                        "students_responded_count" => 75,
                                        "percentage_responded" => 75
                                    ]
                                ]
                            ],
                            [
                                "year_id" => 201516,
                                "org_academic_year_id" => 23,
                                "survey_id" => 11,
                                "survey_name" => "Transition One",
                                "year_name" => "YEARNAME",
                                "cohorts" => [
                                    [
                                        "cohort" => 1,
                                        "cohort_name" => "Survey Cohort 1",
                                        "status" => "closed",
                                        "open_date" => "2015-09-01T06:00:00+0000",
                                        "close_date" => "2015-09-01T06:00:00+0000",
                                        "student_count" => 100,
                                        "students_responded_count" => 75,
                                        "percentage_responded" => 75
                                    ],
                                    [
                                        "cohort" => 2,
                                        "cohort_name" => "Survey Cohort 2",
                                        "status" => "closed",
                                        "open_date" => "2015-09-01T06:00:00+0000",
                                        "close_date" => "2015-09-01T06:00:00+0000",
                                        "student_count" => 100,
                                        "students_responded_count" => 75,
                                        "percentage_responded" => 75
                                    ],
                                    [
                                        "cohort" => 3,
                                        "cohort_name" => "Survey Cohort 3",
                                        "status" => "closed",
                                        "open_date" => "2015-09-01T06:00:00+0000",
                                        "close_date" => "2015-09-01T06:00:00+0000",
                                        "student_count" => 100,
                                        "students_responded_count" => 75,
                                        "percentage_responded" => 75
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                // Example 5:  When the purpose is isq_access, the records coming in may be ordered by cohort then survey; this function being tested still should be able to group them correctly.
                [false, true, 'isq_access',
                    // raw database records, output of ISQPermissionsetServiceTest::testGetSurveysAndCohortsHavingAccessibleISQs (with some date changes to simplify things)
                    [
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '57',
                            'year_name' => '2015-2016',
                            'cohort' => '1',
                            'cohort_name' => 'Survey Cohort 1',
                            'survey_id' => '12',
                            'survey_name' => 'Check-Up One',
                            'status' => 'closed',
                            'open_date' => '2015-09-01 06:00:00',
                            'close_date' => '2015-10-31 06:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '57',
                            'year_name' => '2015-2016',
                            'cohort' => '1',
                            'cohort_name' => 'Survey Cohort 1',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'status' => 'closed',
                            'open_date' => '2015-09-01 06:00:00',
                            'close_date' => '2015-10-31 06:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '57',
                            'year_name' => '2015-2016',
                            'cohort' => '2',
                            'cohort_name' => 'Survey Cohort 2',
                            'survey_id' => '12',
                            'survey_name' => 'Check-Up One',
                            'status' => 'closed',
                            'open_date' => '2015-09-01 06:00:00',
                            'close_date' => '2015-10-31 06:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '57',
                            'year_name' => '2015-2016',
                            'cohort' => '2',
                            'cohort_name' => 'Survey Cohort 2',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'status' => 'closed',
                            'open_date' => '2015-09-01 06:00:00',
                            'close_date' => '2015-10-31 06:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '57',
                            'year_name' => '2015-2016',
                            'cohort' => '3',
                            'cohort_name' => 'Survey Cohort 3',
                            'survey_id' => '12',
                            'survey_name' => 'Check-Up One',
                            'status' => 'closed',
                            'open_date' => '2015-09-01 06:00:00',
                            'close_date' => '2015-10-31 06:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '57',
                            'year_name' => '2015-2016',
                            'cohort' => '3',
                            'cohort_name' => 'Survey Cohort 3',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'status' => 'closed',
                            'open_date' => '2015-09-01 06:00:00',
                            'close_date' => '2015-10-31 06:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '57',
                            'year_name' => '2015-2016',
                            'cohort' => '4',
                            'cohort_name' => 'Survey Cohort 4',
                            'survey_id' => '12',
                            'survey_name' => 'Check-Up One',
                            'status' => 'closed',
                            'open_date' => '2015-09-01 06:00:00',
                            'close_date' => '2015-10-31 06:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '57',
                            'year_name' => '2015-2016',
                            'cohort' => '4',
                            'cohort_name' => 'Survey Cohort 4',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'status' => 'closed',
                            'open_date' => '2015-09-01 06:00:00',
                            'close_date' => '2015-10-31 06:00:00',
                            'wess_admin_link' => null
                        ]
                    ],
                    // reformatted result
                    [
                        'organization_id' => 1,
                        'surveys' => [
                            [
                                'year_id' => 201516,
                                'org_academic_year_id' => 57,
                                'year_name' => '2015-2016',
                                'survey_id' => 12,
                                'survey_name' => 'Check-Up One',
                                'cohorts' => [
                                    [
                                        'cohort' => 1,
                                        'cohort_name' => 'Survey Cohort 1',
                                        'status' => 'closed',
                                        'open_date' => '2015-09-01T06:00:00+0000',
                                        'close_date' => '2015-10-31T06:00:00+0000'
                                    ],
                                    [
                                        'cohort' => 2,
                                        'cohort_name' => 'Survey Cohort 2',
                                        'status' => 'closed',
                                        'open_date' => '2015-09-01T06:00:00+0000',
                                        'close_date' => '2015-10-31T06:00:00+0000'
                                    ],
                                    [
                                        'cohort' => 3,
                                        'cohort_name' => 'Survey Cohort 3',
                                        'status' => 'closed',
                                        'open_date' => '2015-09-01T06:00:00+0000',
                                        'close_date' => '2015-10-31T06:00:00+0000'
                                    ],
                                    [
                                        'cohort' => 4,
                                        'cohort_name' => 'Survey Cohort 4',
                                        'status' => 'closed',
                                        'open_date' => '2015-09-01T06:00:00+0000',
                                        'close_date' => '2015-10-31T06:00:00+0000'
                                    ]
                                ]
                            ],
                            [
                                'year_id' => 201516,
                                'org_academic_year_id' => 57,
                                'year_name' => '2015-2016',
                                'survey_id' => 11,
                                'survey_name' => 'Transition One',
                                'cohorts' => [
                                    [
                                        'cohort' => 1,
                                        'cohort_name' => 'Survey Cohort 1',
                                        'status' => 'closed',
                                        'open_date' => '2015-09-01T06:00:00+0000',
                                        'close_date' => '2015-10-31T06:00:00+0000'
                                    ],
                                    [
                                        'cohort' => 2,
                                        'cohort_name' => 'Survey Cohort 2',
                                        'status' => 'closed',
                                        'open_date' => '2015-09-01T06:00:00+0000',
                                        'close_date' => '2015-10-31T06:00:00+0000'
                                    ],
                                    [
                                        'cohort' => 3,
                                        'cohort_name' => 'Survey Cohort 3',
                                        'status' => 'closed',
                                        'open_date' => '2015-09-01T06:00:00+0000',
                                        'close_date' => '2015-10-31T06:00:00+0000'
                                    ],
                                    [
                                        'cohort' => 4,
                                        'cohort_name' => 'Survey Cohort 4',
                                        'status' => 'closed',
                                        'open_date' => '2015-09-01T06:00:00+0000',
                                        'close_date' => '2015-10-31T06:00:00+0000'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                // Example 6:  The order of the survey_ids in the output should always be descending, even if different cohorts were assigned different surveys.
                [false, true, 'isq_access',
                    // raw database records, output of ISQPermissionsetServiceTest::testGetSurveysAndCohortsHavingAccessibleISQs (with some date changes to simplify things)
                    [
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '66',
                            'year_name' => '201516',
                            'cohort' => '1',
                            'cohort_name' => 'Survey Cohort 1',
                            'survey_id' => '14',
                            'survey_name' => 'Check-Up Two',
                            'status' => 'closed',
                            'open_date' => '2015-09-01 06:00:00',
                            'close_date' => '2015-10-31 06:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '66',
                            'year_name' => '201516',
                            'cohort' => '1',
                            'cohort_name' => 'Survey Cohort 1',
                            'survey_id' => '12',
                            'survey_name' => 'Check-Up One',
                            'status' => 'closed',
                            'open_date' => '2015-09-01 06:00:00',
                            'close_date' => '2015-10-31 06:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '66',
                            'year_name' => '201516',
                            'cohort' => '1',
                            'cohort_name' => 'Survey Cohort 1',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'status' => 'closed',
                            'open_date' => '2015-09-01 06:00:00',
                            'close_date' => '2015-10-31 06:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '66',
                            'year_name' => '201516',
                            'cohort' => '2',
                            'cohort_name' => 'Survey Cohort 2',
                            'survey_id' => '13',
                            'survey_name' => 'Transition Two',
                            'status' => 'closed',
                            'open_date' => '2015-09-01 06:00:00',
                            'close_date' => '2015-10-31 06:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '66',
                            'year_name' => '201516',
                            'cohort' => '2',
                            'cohort_name' => 'Survey Cohort 2',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'status' => 'closed',
                            'open_date' => '2015-09-01 06:00:00',
                            'close_date' => '2015-10-31 06:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '66',
                            'year_name' => '201516',
                            'cohort' => '3',
                            'cohort_name' => 'Survey Cohort 3',
                            'survey_id' => '13',
                            'survey_name' => 'Transition Two',
                            'status' => 'closed',
                            'open_date' => '2015-09-01 06:00:00',
                            'close_date' => '2015-10-31 06:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '66',
                            'year_name' => '201516',
                            'cohort' => '3',
                            'cohort_name' => 'Survey Cohort 3',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'status' => 'closed',
                            'open_date' => '2015-09-01 06:00:00',
                            'close_date' => '2015-10-31 06:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '66',
                            'year_name' => '201516',
                            'cohort' => '4',
                            'cohort_name' => 'Survey Cohort 4',
                            'survey_id' => '13',
                            'survey_name' => 'Transition Two',
                            'status' => 'closed',
                            'open_date' => '2015-09-01 06:00:00',
                            'close_date' => '2015-10-31 06:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '66',
                            'year_name' => '201516',
                            'cohort' => '4',
                            'cohort_name' => 'Survey Cohort 4',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'status' => 'closed',
                            'open_date' => '2015-09-01 06:00:00',
                            'close_date' => '2015-10-31 06:00:00',
                            'wess_admin_link' => null
                        ]
                    ],
                    // reformatted result
                    [
                        'organization_id' => 1,
                        'surveys' => [
                            [
                                'year_id' => 201516,
                                'org_academic_year_id' => '66',
                                'year_name' => '201516',
                                'survey_id' => 14,
                                'survey_name' => 'Check-Up Two',
                                'cohorts' => [
                                    [
                                        'cohort' => 1,
                                        'cohort_name' => 'Survey Cohort 1',
                                        'status' => 'closed',
                                        'open_date' => '2015-09-01T06:00:00+0000',
                                        'close_date' => '2015-10-31T06:00:00+0000'
                                    ]
                                ]
                            ],
                            [
                                'year_id' => 201516,
                                'org_academic_year_id' => '66',
                                'year_name' => '201516',
                                'survey_id' => 13,
                                'survey_name' => 'Transition Two',
                                'cohorts' => [
                                    [
                                        'cohort' => 2,
                                        'cohort_name' => 'Survey Cohort 2',
                                        'status' => 'closed',
                                        'open_date' => '2015-09-01T06:00:00+0000',
                                        'close_date' => '2015-10-31T06:00:00+0000'
                                    ],
                                    [
                                        'cohort' => 3,
                                        'cohort_name' => 'Survey Cohort 3',
                                        'status' => 'closed',
                                        'open_date' => '2015-09-01T06:00:00+0000',
                                        'close_date' => '2015-10-31T06:00:00+0000'
                                    ],
                                    [
                                        'cohort' => 4,
                                        'cohort_name' => 'Survey Cohort 4',
                                        'status' => 'closed',
                                        'open_date' => '2015-09-01T06:00:00+0000',
                                        'close_date' => '2015-10-31T06:00:00+0000'
                                    ]
                                ]
                            ],
                            [
                                'year_id' => 201516,
                                'org_academic_year_id' => '66',
                                'year_name' => '201516',
                                'survey_id' => 12,
                                'survey_name' => 'Check-Up One',
                                'cohorts' => [
                                    [
                                        'cohort' => 1,
                                        'cohort_name' => 'Survey Cohort 1',
                                        'status' => 'closed',
                                        'open_date' => '2015-09-01T06:00:00+0000',
                                        'close_date' => '2015-10-31T06:00:00+0000'
                                    ]
                                ]
                            ],
                            [
                                'year_id' => 201516,
                                'org_academic_year_id' => '66',
                                'year_name' => '201516',
                                'survey_id' => 11,
                                'survey_name' => 'Transition One',
                                'cohorts' => [
                                    [
                                        'cohort' => 1,
                                        'cohort_name' => 'Survey Cohort 1',
                                        'status' => 'closed',
                                        'open_date' => '2015-09-01T06:00:00+0000',
                                        'close_date' => '2015-10-31T06:00:00+0000'
                                    ],
                                    [
                                        'cohort' => 2,
                                        'cohort_name' => 'Survey Cohort 2',
                                        'status' => 'closed',
                                        'open_date' => '2015-09-01T06:00:00+0000',
                                        'close_date' => '2015-10-31T06:00:00+0000'
                                    ],
                                    [
                                        'cohort' => 3,
                                        'cohort_name' => 'Survey Cohort 3',
                                        'status' => 'closed',
                                        'open_date' => '2015-09-01T06:00:00+0000',
                                        'close_date' => '2015-10-31T06:00:00+0000'
                                    ],
                                    [
                                        'cohort' => 4,
                                        'cohort_name' => 'Survey Cohort 4',
                                        'status' => 'closed',
                                        'open_date' => '2015-09-01T06:00:00+0000',
                                        'close_date' => '2015-10-31T06:00:00+0000'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }
}