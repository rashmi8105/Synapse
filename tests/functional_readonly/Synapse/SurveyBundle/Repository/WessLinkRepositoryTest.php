<?php

use Codeception\TestCase\Test;


class WessLinkRepositoryTest extends Test
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
     * @var \Synapse\SurveyBundle\Repository\WessLinkRepository
     */
    private $wessLinkRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->wessLinkRepository = $this->repositoryResolver->getRepository('SynapseSurveyBundle:WessLink');
    }


    public function testGetSurveysForCohortAndYear()
    {
        $this->specify("Verify the functionality of the method getSurveysForCohortAndYear", function ($orgId, $cohortId, $yearId, $expectedResult) {

            $results = $this->wessLinkRepository->getSurveysForCohortAndYear($orgId, $cohortId, $yearId);

            for ($i = 0; $i < count($expectedResult); $i++) {
                verify($results[$i]['survey_id'])->notEmpty();
                verify($results[$i]['survey_id'])->equals($expectedResult[$i]['survey_id']);
            }

        }, ["examples" => [[203, 1, 201516, [
            [
                'survey_id' => 11
            ],
            [
                'survey_id' => 12
            ]
        ]]]]);
    }

    public function testGetSurveysAndCohortsForOrganizationWithoutPermissionCheck()
    {
        $this->specify("Verify the functionality of the method getSurveysAndCohortsForOrganizationWithoutPermissionCheck", function ($orgId, $loggedInUserId, $orgAcademicYearId, $surveyStatus, $surveyId, $includeCompletionDataFlag, $hasCoordinatorAccess, $expectedResult) {

            $result = $this->wessLinkRepository->getSurveysAndCohortsForOrganizationWithoutPermissionCheck($orgId, $orgAcademicYearId, $surveyStatus, $surveyId);

            verify($result)->equals($expectedResult);

        }, ["examples" =>
        // Example 1:  Simplest case at org 203 -- no completion data and none of the optional parameters set
            [[203, 4883173, null, null, null, false, false,
                [
                    [
                        'org_academic_year_id' => '157',
                        'year_id' => '201516',
                        'year_name' => '2015-2016',
                        'survey_id' => '12',
                        'survey_name' => 'Check-Up One',
                        'cohort' => '1',
                        'cohort_name' => 'Survey Cohort 1',
                        'status' => 'closed',
                        'open_date' => '2015-10-09 10:00:00',
                        'close_date' => '2015-10-28 10:00:00'
                    ],
                    [
                        'org_academic_year_id' => '157',
                        'year_id' => '201516',
                        'year_name' => '2015-2016',
                        'survey_id' => '11',
                        'survey_name' => 'Transition One',
                        'cohort' => '1',
                        'cohort_name' => 'Survey Cohort 1',
                        'status' => 'closed',
                        'open_date' => '2015-08-09 10:00:00',
                        'close_date' => '2015-10-09 10:00:00'
                    ]
                ]
            ],
                // Example 2:  Restricting to a single survey
                [203, 4883173, null, null, 12, false, false,
                    [
                        [
                            'org_academic_year_id' => '157',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'survey_id' => '12',
                            'survey_name' => 'Check-Up One',
                            'cohort' => '1',
                            'cohort_name' => 'Survey Cohort 1',
                            'status' => 'closed',
                            'open_date' => '2015-10-09 10:00:00',
                            'close_date' => '2015-10-28 10:00:00'
                        ]
                    ]
                ], // Example 3:  Only launched and closed surveys
                [100, 212767, null, ['launched', 'closed'], null, false, true, [
                    [
                        'org_academic_year_id' => '21',
                        'year_id' => '201516',
                        'year_name' => '2015-16',
                        'survey_id' => '11',
                        'survey_name' => 'Transition One',
                        'cohort' => '1',
                        'cohort_name' => 'Survey Cohort 1',
                        'status' => 'closed',
                        'open_date' => '2015-09-28 06:00:00',
                        'close_date' => '2015-10-10 06:00:00'
                    ],
                    [
                        'org_academic_year_id' => '21',
                        'year_id' => '201516',
                        'year_name' => '2015-16',
                        'survey_id' => '11',
                        'survey_name' => 'Transition One',
                        'cohort' => '2',
                        'cohort_name' => 'Survey Cohort 2',
                        'status' => 'closed',
                        'open_date' => '2015-09-28 06:00:00',
                        'close_date' => '2015-10-10 06:00:00'
                    ],
                    [
                        'org_academic_year_id' => '21',
                        'year_id' => '201516',
                        'year_name' => '2015-16',
                        'survey_id' => '11',
                        'survey_name' => 'Transition One',
                        'cohort' => '3',
                        'cohort_name' => 'Survey Cohort 3',
                        'status' => 'closed',
                        'open_date' => '2015-09-28 06:00:00',
                        'close_date' => '2015-10-10 06:00:00'
                    ],
                    [
                        'org_academic_year_id' => '21',
                        'year_id' => '201516',
                        'year_name' => '2015-16',
                        'survey_id' => '11',
                        'survey_name' => 'Transition One',
                        'cohort' => '4',
                        'cohort_name' => 'Survey Cohort 4',
                        'status' => 'closed',
                        'open_date' => '2015-09-28 06:00:00',
                        'close_date' => '2015-10-10 06:00:00'
                    ]
                ]
                ],// Example 4:  Using another organization to show some aspects not available in org 203.
                // This organization has multiple cohorts and "open" surveys.
                [100, 212767, null, null, null, false, true,
                    [
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '14',
                            'survey_name' => 'Check-Up Two',
                            'cohort' => '1',
                            'cohort_name' => 'Survey Cohort 1',
                            'status' => 'open',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-29 06:00:00'
                        ],
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '14',
                            'survey_name' => 'Check-Up Two',
                            'cohort' => '2',
                            'cohort_name' => 'Survey Cohort 2',
                            'status' => 'open',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-29 06:00:00'
                        ],
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '14',
                            'survey_name' => 'Check-Up Two',
                            'cohort' => '3',
                            'cohort_name' => 'Survey Cohort 3',
                            'status' => 'open',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-29 06:00:00'
                        ],
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '14',
                            'survey_name' => 'Check-Up Two',
                            'cohort' => '4',
                            'cohort_name' => 'Survey Cohort 4',
                            'status' => 'open',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-29 06:00:00'
                        ],
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '13',
                            'survey_name' => 'Transition Two',
                            'cohort' => '1',
                            'cohort_name' => 'Survey Cohort 1',
                            'status' => 'open',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-10 06:00:00'
                        ],
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '13',
                            'survey_name' => 'Transition Two',
                            'cohort' => '2',
                            'cohort_name' => 'Survey Cohort 2',
                            'status' => 'open',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-29 06:00:00'
                        ],
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '13',
                            'survey_name' => 'Transition Two',
                            'cohort' => '3',
                            'cohort_name' => 'Survey Cohort 3',
                            'status' => 'open',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-29 06:00:00'
                        ],
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '13',
                            'survey_name' => 'Transition Two',
                            'cohort' => '4',
                            'cohort_name' => 'Survey Cohort 4',
                            'status' => 'open',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-29 06:00:00'
                        ],
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '12',
                            'survey_name' => 'Check-Up One',
                            'cohort' => '1',
                            'cohort_name' => 'Survey Cohort 1',
                            'status' => 'open',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-29 06:00:00'
                        ],
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '12',
                            'survey_name' => 'Check-Up One',
                            'cohort' => '2',
                            'cohort_name' => 'Survey Cohort 2',
                            'status' => 'open',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-29 06:00:00'
                        ],
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '12',
                            'survey_name' => 'Check-Up One',
                            'cohort' => '3',
                            'cohort_name' => 'Survey Cohort 3',
                            'status' => 'open',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-29 06:00:00'
                        ],
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '12',
                            'survey_name' => 'Check-Up One',
                            'cohort' => '4',
                            'cohort_name' => 'Survey Cohort 4',
                            'status' => 'open',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-29 06:00:00'
                        ],
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'cohort' => '1',
                            'cohort_name' => 'Survey Cohort 1',
                            'status' => 'closed',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-10 06:00:00'
                        ],
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'cohort' => '2',
                            'cohort_name' => 'Survey Cohort 2',
                            'status' => 'closed',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-10 06:00:00'
                        ],
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'cohort' => '3',
                            'cohort_name' => 'Survey Cohort 3',
                            'status' => 'closed',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-10 06:00:00'
                        ],
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'cohort' => '4',
                            'cohort_name' => 'Survey Cohort 4',
                            'status' => 'closed',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-10 06:00:00'
                        ]
                    ]],

            ]]);
    }

    public function testGetSurveysAndCohortsForOrganizationWithoutCompletionData()
    {
        $this->specify("Verify the functionality of the method getSurveysAndCohortsForOrganizationWithoutCompletionData", function ($orgId, $loggedInUserId, $orgAcademicYearId, $surveyStatus, $surveyId, $includeCompletionDataFlag, $hasCoordinatorAccess, $expectedResult) {
            $result = $this->wessLinkRepository->getSurveysAndCohortsForOrganizationWithoutCompletionData($orgId, $loggedInUserId, $orgAcademicYearId, $surveyStatus, $surveyId);
            verify($result)->equals($expectedResult);
        }, ["examples" =>
        // Example 1:  Simplest case at org 203 -- no completion data and none of the optional parameters set
            [[203, 4883173, null, null, null, false, false,
                [
                    [
                        'org_academic_year_id' => '157',
                        'year_id' => '201516',
                        'year_name' => '2015-2016',
                        'survey_id' => '12',
                        'survey_name' => 'Check-Up One',
                        'cohort' => '1',
                        'cohort_name' => 'Survey Cohort 1',
                        'status' => 'closed',
                        'open_date' => '2015-10-09 10:00:00',
                        'close_date' => '2015-10-28 10:00:00'
                    ],
                    [
                        'org_academic_year_id' => '157',
                        'year_id' => '201516',
                        'year_name' => '2015-2016',
                        'survey_id' => '11',
                        'survey_name' => 'Transition One',
                        'cohort' => '1',
                        'cohort_name' => 'Survey Cohort 1',
                        'status' => 'closed',
                        'open_date' => '2015-08-09 10:00:00',
                        'close_date' => '2015-10-09 10:00:00'
                    ]
                ]
            ],
                // Example 2:  Restricting to a single survey
                [203, 4883173, null, null, 12, false, false,
                    [
                        [
                            'org_academic_year_id' => '157',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'survey_id' => '12',
                            'survey_name' => 'Check-Up One',
                            'cohort' => '1',
                            'cohort_name' => 'Survey Cohort 1',
                            'status' => 'closed',
                            'open_date' => '2015-10-09 10:00:00',
                            'close_date' => '2015-10-28 10:00:00'
                        ]
                    ]
                ],

                // Example 3:  When $hasCoordinatorAccess is false, this user's permissions
                // restrict which surveys and cohorts are included.
                [100, 212767, null, null, null, false, false,
                    [
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'cohort' => '3',
                            'cohort_name' => 'Survey Cohort 3',
                            'status' => 'closed',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-10 06:00:00'
                        ],
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'cohort' => '4',
                            'cohort_name' => 'Survey Cohort 4',
                            'status' => 'closed',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-10 06:00:00'
                        ]
                    ]
                ],
                // Example 4:  When $hasCoordinatorAccess is false, this user's permissions
                // restrict which surveys and cohorts are included.
                [100, 212767, null, null, null, false, false,
                    [
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'cohort' => '3',
                            'cohort_name' => 'Survey Cohort 3',
                            'status' => 'closed',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-10 06:00:00'
                        ],
                        [
                            'org_academic_year_id' => '21',
                            'year_id' => '201516',
                            'year_name' => '2015-16',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'cohort' => '4',
                            'cohort_name' => 'Survey Cohort 4',
                            'status' => 'closed',
                            'open_date' => '2015-09-28 06:00:00',
                            'close_date' => '2015-10-10 06:00:00'
                        ]
                    ]
                ],


            ]]);
    }


    public function testGetSurveysAndCohortsForOrganizationWithCompletionData()
    {
        $this->specify("Verify the functionality of the method getSurveysAndCohortsForOrganizationWithCompletionData", function ($orgId, $loggedInUserId, $orgAcademicYearId, $surveyStatus, $surveyId, $includeCompletionDataFlag, $hasCoordinatorAccess, $expectedResult) {

            $result = $this->wessLinkRepository->getSurveysAndCohortsForOrganizationWithCompletionData($orgId, $loggedInUserId, $orgAcademicYearId, $surveyStatus, $surveyId, $hasCoordinatorAccess);

            verify($result)->equals($expectedResult);

        }, ["examples" =>
                [
                    // Example 1:  Including completion data and restricted to students the user has access to
                    [203, 4883173, null, null, null, true, false,
                        [
                            [
                                'org_academic_year_id' => '157',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '12',
                                'survey_name' => 'Check-Up One',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'status' => 'closed',
                                'open_date' => '2015-10-09 10:00:00',
                                'close_date' => '2015-10-28 10:00:00',
                                'students_responded_count' => '10',
                                'student_count' => '10'
                            ],
                            [
                                'org_academic_year_id' => '157',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'status' => 'closed',
                                'open_date' => '2015-08-09 10:00:00',
                                'close_date' => '2015-10-09 10:00:00',
                                'students_responded_count' => '10',
                                'student_count' => '10'
                            ]
                        ]
                    ],
                    // Example 2:  Including completion data and restricted to students the user has access to.
                    // This example demonstrates that the group hierarchy is respected, as there are no students directly in this user's group.
                    [203, 4883175, null, null, null, true, false,
                        [
                            [
                                'org_academic_year_id' => '157',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '12',
                                'survey_name' => 'Check-Up One',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'status' => 'closed',
                                'open_date' => '2015-10-09 10:00:00',
                                'close_date' => '2015-10-28 10:00:00',
                                'students_responded_count' => '21',
                                'student_count' => '21'
                            ],
                            [
                                'org_academic_year_id' => '157',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'status' => 'closed',
                                'open_date' => '2015-08-09 10:00:00',
                                'close_date' => '2015-10-09 10:00:00',
                                'students_responded_count' => '21',
                                'student_count' => '21'
                            ]
                        ]
                    ],

                    // Example 3:  Including completion data for the whole organization (by using the $hasCoordinatorAccess flag).
                    // (This user is not a coordinator, but this flag is meant to be set in the service that calls this function;
                    // here, we're demonstrating that the flag has the desired effect on the results.)
                    [203, 4883173, null, null, null, true, true,
                        [
                            [
                                'org_academic_year_id' => '157',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '12',
                                'survey_name' => 'Check-Up One',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'status' => 'closed',
                                'open_date' => '2015-10-09 10:00:00',
                                'close_date' => '2015-10-28 10:00:00',
                                'students_responded_count' => '200',
                                'student_count' => '200'
                            ],
                            [
                                'org_academic_year_id' => '157',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'status' => 'closed',
                                'open_date' => '2015-08-09 10:00:00',
                                'close_date' => '2015-10-09 10:00:00',
                                'students_responded_count' => '200',
                                'student_count' => '200'
                            ]
                        ]
                    ],
                    // Example 4:  Completion data for the whole organization
                    [100, 212767, null, null, null, true, true,
                        [
                            [
                                'org_academic_year_id' => '21',
                                'year_id' => '201516',
                                'year_name' => '2015-16',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'status' => 'closed',
                                'open_date' => '2015-09-28 06:00:00',
                                'close_date' => '2015-10-10 06:00:00',
                                'students_responded_count' => '206',
                                'student_count' => '454'
                            ],
                            [
                                'org_academic_year_id' => '21',
                                'year_id' => '201516',
                                'year_name' => '2015-16',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'cohort' => '2',
                                'cohort_name' => 'Survey Cohort 2',
                                'status' => 'closed',
                                'open_date' => '2015-09-28 06:00:00',
                                'close_date' => '2015-10-10 06:00:00',
                                'students_responded_count' => '24',
                                'student_count' => '143'
                            ],
                            [
                                'org_academic_year_id' => '21',
                                'year_id' => '201516',
                                'year_name' => '2015-16',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'cohort' => '3',
                                'cohort_name' => 'Survey Cohort 3',
                                'status' => 'closed',
                                'open_date' => '2015-09-28 06:00:00',
                                'close_date' => '2015-10-10 06:00:00',
                                'students_responded_count' => '25',
                                'student_count' => '240'
                            ],
                            [
                                'org_academic_year_id' => '21',
                                'year_id' => '201516',
                                'year_name' => '2015-16',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'cohort' => '4',
                                'cohort_name' => 'Survey Cohort 4',
                                'status' => 'closed',
                                'open_date' => '2015-09-28 06:00:00',
                                'close_date' => '2015-10-10 06:00:00',
                                'students_responded_count' => '55',
                                'student_count' => '332'
                            ]
                        ]
                    ],
                    // Example 5:  Completion data for only accessible students.
                    // This example also ensures we aren't double-counting students when they are connected to the faculty member via multiple groups/courses.  (ESPRJ-11832)
                    // For example, the student 904802 was being counted 3 times because the faculty member and student were both in a parent group (269202) and a subgroup (309927).
                    [100, 212767, null, null, null, true, false,
                        [
                            [
                                'org_academic_year_id' => '21',
                                'year_id' => '201516',
                                'year_name' => '2015-16',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'cohort' => '3',
                                'cohort_name' => 'Survey Cohort 3',
                                'status' => 'closed',
                                'open_date' => '2015-09-28 06:00:00',
                                'close_date' => '2015-10-10 06:00:00',
                                'students_responded_count' => '1',      // was 2 when we were double-counting students
                                'student_count' => '3'                  // was 6 when we were double-counting students
                            ],
                            [
                                'org_academic_year_id' => '21',
                                'year_id' => '201516',
                                'year_name' => '2015-16',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'cohort' => '4',
                                'cohort_name' => 'Survey Cohort 4',
                                'status' => 'closed',
                                'open_date' => '2015-09-28 06:00:00',
                                'close_date' => '2015-10-10 06:00:00',
                                'students_responded_count' => '0',
                                'student_count' => '7'                  // was 17 when we were double-counting students
                            ]
                        ]
                    ],
                    // Example 6: An organization with multiple surveys and multiple cohorts.
                    [163, 1144934, null, null, null, true, true,
                        [
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '14',
                                'survey_name' => 'Check-Up Two',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'status' => 'closed',
                                'open_date' => '2016-04-05 06:00:00',
                                'close_date' => '2016-04-20 06:00:00',
                                'students_responded_count' => '260',
                                'student_count' => '1746'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '14',
                                'survey_name' => 'Check-Up Two',
                                'cohort' => '2',
                                'cohort_name' => 'Survey Cohort 2',
                                'status' => 'closed',
                                'open_date' => '2016-04-05 06:00:00',
                                'close_date' => '2016-04-20 06:00:00',
                                'students_responded_count' => '288',
                                'student_count' => '2049'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '14',
                                'survey_name' => 'Check-Up Two',
                                'cohort' => '3',
                                'cohort_name' => 'Survey Cohort 3',
                                'status' => 'closed',
                                'open_date' => '2016-04-05 06:00:00',
                                'close_date' => '2016-04-20 06:00:00',
                                'students_responded_count' => '483',
                                'student_count' => '3649'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '14',
                                'survey_name' => 'Check-Up Two',
                                'cohort' => '4',
                                'cohort_name' => 'Survey Cohort 4',
                                'status' => 'closed',
                                'open_date' => '2016-04-05 06:00:00',
                                'close_date' => '2016-04-20 06:00:00',
                                'students_responded_count' => '567',
                                'student_count' => '1425'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '13',
                                'survey_name' => 'Transition Two',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'status' => 'closed',
                                'open_date' => '2016-02-08 06:00:00',
                                'close_date' => '2016-03-10 06:00:00',
                                'students_responded_count' => '1849',
                                'student_count' => '3845'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '13',
                                'survey_name' => 'Transition Two',
                                'cohort' => '2',
                                'cohort_name' => 'Survey Cohort 2',
                                'status' => 'closed',
                                'open_date' => '2016-02-08 06:00:00',
                                'close_date' => '2016-03-10 06:00:00',
                                'students_responded_count' => '361',
                                'student_count' => '2411'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '13',
                                'survey_name' => 'Transition Two',
                                'cohort' => '3',
                                'cohort_name' => 'Survey Cohort 3',
                                'status' => 'closed',
                                'open_date' => '2016-02-08 06:00:00',
                                'close_date' => '2016-03-10 06:00:00',
                                'students_responded_count' => '917',
                                'student_count' => '4203'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '13',
                                'survey_name' => 'Transition Two',
                                'cohort' => '4',
                                'cohort_name' => 'Survey Cohort 4',
                                'status' => 'closed',
                                'open_date' => '2016-02-08 06:00:00',
                                'close_date' => '2016-03-10 06:00:00',
                                'students_responded_count' => '671',
                                'student_count' => '1834'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '12',
                                'survey_name' => 'Check-Up One',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'status' => 'closed',
                                'open_date' => '2015-11-10 06:00:00',
                                'close_date' => '2015-11-24 06:00:00',
                                'students_responded_count' => '337',
                                'student_count' => '1250'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '12',
                                'survey_name' => 'Check-Up One',
                                'cohort' => '2',
                                'cohort_name' => 'Survey Cohort 2',
                                'status' => 'closed',
                                'open_date' => '2015-11-10 06:00:00',
                                'close_date' => '2015-11-24 06:00:00',
                                'students_responded_count' => '199',
                                'student_count' => '1587'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '12',
                                'survey_name' => 'Check-Up One',
                                'cohort' => '3',
                                'cohort_name' => 'Survey Cohort 3',
                                'status' => 'closed',
                                'open_date' => '2015-11-10 06:00:00',
                                'close_date' => '2015-11-24 06:00:00',
                                'students_responded_count' => '369',
                                'student_count' => '3796'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '12',
                                'survey_name' => 'Check-Up One',
                                'cohort' => '4',
                                'cohort_name' => 'Survey Cohort 4',
                                'status' => 'closed',
                                'open_date' => '2015-11-10 06:00:00',
                                'close_date' => '2015-11-24 06:00:00',
                                'students_responded_count' => '629',
                                'student_count' => '1280'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'status' => 'closed',
                                'open_date' => '2015-09-20 06:00:00',
                                'close_date' => '2015-10-25 06:00:00',
                                'students_responded_count' => '2915',
                                'student_count' => '3848'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'cohort' => '2',
                                'cohort_name' => 'Survey Cohort 2',
                                'status' => 'closed',
                                'open_date' => '2015-09-21 06:00:00',
                                'close_date' => '2015-10-25 06:00:00',
                                'students_responded_count' => '728',
                                'student_count' => '1885'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'cohort' => '3',
                                'cohort_name' => 'Survey Cohort 3',
                                'status' => 'closed',
                                'open_date' => '2015-09-21 06:00:00',
                                'close_date' => '2015-10-25 06:00:00',
                                'students_responded_count' => '937',
                                'student_count' => '4404'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'cohort' => '4',
                                'cohort_name' => 'Survey Cohort 4',
                                'status' => 'closed',
                                'open_date' => '2015-09-21 06:00:00',
                                'close_date' => '2015-10-25 06:00:00',
                                'students_responded_count' => '956',
                                'student_count' => '1707'
                            ]
                        ]
                    ],
                    // Example 7:  Completion data for only accessible students.
                    [163, 1144934, null, null, null, true, false,
                        [
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '14',
                                'survey_name' => 'Check-Up Two',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'status' => 'closed',
                                'open_date' => '2016-04-05 06:00:00',
                                'close_date' => '2016-04-20 06:00:00',
                                'students_responded_count' => '0',
                                'student_count' => '3'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '14',
                                'survey_name' => 'Check-Up Two',
                                'cohort' => '3',
                                'cohort_name' => 'Survey Cohort 3',
                                'status' => 'closed',
                                'open_date' => '2016-04-05 06:00:00',
                                'close_date' => '2016-04-20 06:00:00',
                                'students_responded_count' => '0',
                                'student_count' => '1'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '13',
                                'survey_name' => 'Transition Two',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'status' => 'closed',
                                'open_date' => '2016-02-08 06:00:00',
                                'close_date' => '2016-03-10 06:00:00',
                                'students_responded_count' => '23',
                                'student_count' => '29'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '13',
                                'survey_name' => 'Transition Two',
                                'cohort' => '2',
                                'cohort_name' => 'Survey Cohort 2',
                                'status' => 'closed',
                                'open_date' => '2016-02-08 06:00:00',
                                'close_date' => '2016-03-10 06:00:00',
                                'students_responded_count' => '0',
                                'student_count' => '1'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '13',
                                'survey_name' => 'Transition Two',
                                'cohort' => '3',
                                'cohort_name' => 'Survey Cohort 3',
                                'status' => 'closed',
                                'open_date' => '2016-02-08 06:00:00',
                                'close_date' => '2016-03-10 06:00:00',
                                'students_responded_count' => '1',
                                'student_count' => '2'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '12',
                                'survey_name' => 'Check-Up One',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'status' => 'closed',
                                'open_date' => '2015-11-10 06:00:00',
                                'close_date' => '2015-11-24 06:00:00',
                                'students_responded_count' => '0',
                                'student_count' => '1'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'status' => 'closed',
                                'open_date' => '2015-09-20 06:00:00',
                                'close_date' => '2015-10-25 06:00:00',
                                'students_responded_count' => '30',
                                'student_count' => '31'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'cohort' => '2',
                                'cohort_name' => 'Survey Cohort 2',
                                'status' => 'closed',
                                'open_date' => '2015-09-21 06:00:00',
                                'close_date' => '2015-10-25 06:00:00',
                                'students_responded_count' => '1',
                                'student_count' => '1'
                            ],
                            [
                                'org_academic_year_id' => '57',
                                'year_id' => '201516',
                                'year_name' => '2015-2016',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'cohort' => '3',
                                'cohort_name' => 'Survey Cohort 3',
                                'status' => 'closed',
                                'open_date' => '2015-09-21 06:00:00',
                                'close_date' => '2015-10-25 06:00:00',
                                'students_responded_count' => '3',
                                'student_count' => '3'
                            ]
                        ]
                    ],
                ]]
        );
    }


    public function testGetCohortsAndSurveysForOrganizationForSetup()
    {
        $this->specify("Verify the functionality of the method getCohortsAndSurveysForOrganizationForSetup", function ($orgId, $purpose, $surveyStatus, $expectedResult) {

            $result = $this->wessLinkRepository->getCohortsAndSurveysForOrganizationForSetup($orgId, $purpose, null, $surveyStatus);

            verify($result)->equals($expectedResult);

        }, ["examples" =>
                [
                    // Example 1:  Using "isq" parameter to only get cohort and survey combinations which have ISQs associated with them.
                    [118, 'isq', null,
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
                                'close_date' => '2015-10-08 06:00:00',
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
                                'open_date' => '2015-12-01 06:00:00',
                                'close_date' => '2015-12-03 06:00:00',
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
                                'open_date' => '2015-09-11 06:00:00',
                                'close_date' => '2015-10-08 06:00:00',
                                'wess_admin_link' => null
                            ]
                        ]
                    ],
                    // Example 2:  Using "survey_setup" parameter to get all cohort and survey combinations for the organization,
                    // including ones which have been set up and ones which are available to be set up.
                    [118, 'survey_setup', null,
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
                                'open_date' => '2015-09-28 06:00:00',
                                'close_date' => '2015-10-29 06:00:00',
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
                                'open_date' => '2015-09-28 06:00:00',
                                'close_date' => '2015-10-29 06:00:00',
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
                                'open_date' => '2015-09-28 06:00:00',
                                'close_date' => '2015-10-29 06:00:00',
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
                                'close_date' => '2015-10-08 06:00:00',
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
                                'open_date' => '2015-12-01 06:00:00',
                                'close_date' => '2015-12-03 06:00:00',
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
                                'open_date' => '2015-09-28 06:00:00',
                                'close_date' => '2015-10-29 06:00:00',
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
                                'open_date' => '2015-09-28 06:00:00',
                                'close_date' => '2015-10-29 06:00:00',
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
                                'open_date' => '2015-09-11 06:00:00',
                                'close_date' => '2015-10-08 06:00:00',
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
                                'open_date' => '2015-09-28 06:00:00',
                                'close_date' => '2015-10-29 06:00:00',
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
                                'open_date' => '2015-09-28 06:00:00',
                                'close_date' => '2015-10-29 06:00:00',
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
                                'open_date' => '2015-09-28 06:00:00',
                                'close_date' => '2015-10-29 06:00:00',
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
                                'open_date' => '2015-09-11 06:00:00',
                                'close_date' => '2015-09-16 06:00:00',
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
                                'open_date' => '2015-09-28 06:00:00',
                                'close_date' => '2015-10-29 06:00:00',
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
                                'open_date' => '2015-09-28 06:00:00',
                                'close_date' => '2015-10-29 06:00:00',
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
                                'open_date' => '2015-09-28 06:00:00',
                                'close_date' => '2015-10-29 06:00:00',
                                'wess_admin_link' => null
                            ],
                            [
                                'year_id' => '201617',
                                'org_academic_year_id' => '148',
                                'year_name' => '2016-17',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'survey_id' => '15',
                                'survey_name' => 'Transition One',
                                'status' => 'open',
                                'open_date' => '2016-09-30 06:00:00',
                                'close_date' => '2016-10-29 06:00:00',
                                'wess_admin_link' => null
                            ],
                            [
                                'year_id' => '201617',
                                'org_academic_year_id' => '148',
                                'year_name' => '2016-17',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'survey_id' => '16',
                                'survey_name' => 'Check-Up One',
                                'status' => 'open',
                                'open_date' => '2016-09-30 06:00:00',
                                'close_date' => '2016-10-29 06:00:00',
                                'wess_admin_link' => null
                            ],
                            [
                                'year_id' => '201617',
                                'org_academic_year_id' => '148',
                                'year_name' => '2016-17',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'survey_id' => '17',
                                'survey_name' => 'Transition Two',
                                'status' => 'open',
                                'open_date' => '2016-09-30 06:00:00',
                                'close_date' => '2016-10-29 06:00:00',
                                'wess_admin_link' => null
                            ],
                            [
                                'year_id' => '201617',
                                'org_academic_year_id' => '148',
                                'year_name' => '2016-17',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'survey_id' => '18',
                                'survey_name' => 'Check-Up Two',
                                'status' => 'open',
                                'open_date' => '2016-09-30 06:00:00',
                                'close_date' => '2016-10-29 06:00:00',
                                'wess_admin_link' => null
                            ],
                            [
                                'year_id' => '201617',
                                'org_academic_year_id' => '148',
                                'year_name' => '2016-17',
                                'cohort' => '2',
                                'cohort_name' => 'Survey Cohort 2',
                                'survey_id' => '15',
                                'survey_name' => 'Transition One',
                                'status' => 'open',
                                'open_date' => '2016-09-30 06:00:00',
                                'close_date' => '2016-10-29 06:00:00',
                                'wess_admin_link' => null
                            ],
                            [
                                'year_id' => '201617',
                                'org_academic_year_id' => '148',
                                'year_name' => '2016-17',
                                'cohort' => '2',
                                'cohort_name' => 'Survey Cohort 2',
                                'survey_id' => '16',
                                'survey_name' => 'Check-Up One',
                                'status' => 'open',
                                'open_date' => '2016-09-30 06:00:00',
                                'close_date' => '2016-10-29 06:00:00',
                                'wess_admin_link' => null
                            ],
                            [
                                'year_id' => '201617',
                                'org_academic_year_id' => '148',
                                'year_name' => '2016-17',
                                'cohort' => '2',
                                'cohort_name' => 'Survey Cohort 2',
                                'survey_id' => '17',
                                'survey_name' => 'Transition Two',
                                'status' => 'open',
                                'open_date' => '2016-09-30 06:00:00',
                                'close_date' => '2016-10-29 06:00:00',
                                'wess_admin_link' => null
                            ],
                            [
                                'year_id' => '201617',
                                'org_academic_year_id' => '148',
                                'year_name' => '2016-17',
                                'cohort' => '2',
                                'cohort_name' => 'Survey Cohort 2',
                                'survey_id' => '18',
                                'survey_name' => 'Check-Up Two',
                                'status' => 'open',
                                'open_date' => '2016-09-30 06:00:00',
                                'close_date' => '2016-10-29 06:00:00',
                                'wess_admin_link' => null
                            ],
                            [
                                'year_id' => '201617',
                                'org_academic_year_id' => '148',
                                'year_name' => '2016-17',
                                'cohort' => '3',
                                'cohort_name' => 'Survey Cohort 3',
                                'survey_id' => '15',
                                'survey_name' => 'Transition One',
                                'status' => 'open',
                                'open_date' => '2016-09-30 06:00:00',
                                'close_date' => '2016-10-29 06:00:00',
                                'wess_admin_link' => null
                            ],
                            [
                                'year_id' => '201617',
                                'org_academic_year_id' => '148',
                                'year_name' => '2016-17',
                                'cohort' => '3',
                                'cohort_name' => 'Survey Cohort 3',
                                'survey_id' => '16',
                                'survey_name' => 'Check-Up One',
                                'status' => 'open',
                                'open_date' => '2016-09-30 06:00:00',
                                'close_date' => '2016-10-29 06:00:00',
                                'wess_admin_link' => null
                            ],
                            [
                                'year_id' => '201617',
                                'org_academic_year_id' => '148',
                                'year_name' => '2016-17',
                                'cohort' => '3',
                                'cohort_name' => 'Survey Cohort 3',
                                'survey_id' => '17',
                                'survey_name' => 'Transition Two',
                                'status' => 'open',
                                'open_date' => '2016-09-30 06:00:00',
                                'close_date' => '2016-10-29 06:00:00',
                                'wess_admin_link' => null
                            ],
                            [
                                'year_id' => '201617',
                                'org_academic_year_id' => '148',
                                'year_name' => '2016-17',
                                'cohort' => '3',
                                'cohort_name' => 'Survey Cohort 3',
                                'survey_id' => '18',
                                'survey_name' => 'Check-Up Two',
                                'status' => 'open',
                                'open_date' => '2016-09-30 06:00:00',
                                'close_date' => '2016-10-29 06:00:00',
                                'wess_admin_link' => null
                            ],
                            [
                                'year_id' => '201617',
                                'org_academic_year_id' => '148',
                                'year_name' => '2016-17',
                                'cohort' => '4',
                                'cohort_name' => 'Survey Cohort 4',
                                'survey_id' => '15',
                                'survey_name' => 'Transition One',
                                'status' => 'open',
                                'open_date' => '2016-09-30 06:00:00',
                                'close_date' => '2016-10-29 06:00:00',
                                'wess_admin_link' => null
                            ],
                            [
                                'year_id' => '201617',
                                'org_academic_year_id' => '148',
                                'year_name' => '2016-17',
                                'cohort' => '4',
                                'cohort_name' => 'Survey Cohort 4',
                                'survey_id' => '16',
                                'survey_name' => 'Check-Up One',
                                'status' => 'open',
                                'open_date' => '2016-09-30 06:00:00',
                                'close_date' => '2016-10-29 06:00:00',
                                'wess_admin_link' => null
                            ],
                            [
                                'year_id' => '201617',
                                'org_academic_year_id' => '148',
                                'year_name' => '2016-17',
                                'cohort' => '4',
                                'cohort_name' => 'Survey Cohort 4',
                                'survey_id' => '17',
                                'survey_name' => 'Transition Two',
                                'status' => 'open',
                                'open_date' => '2016-09-30 06:00:00',
                                'close_date' => '2016-10-29 06:00:00',
                                'wess_admin_link' => null
                            ],
                            [
                                'year_id' => '201617',
                                'org_academic_year_id' => '148',
                                'year_name' => '2016-17',
                                'cohort' => '4',
                                'cohort_name' => 'Survey Cohort 4',
                                'survey_id' => '18',
                                'survey_name' => 'Check-Up Two',
                                'status' => 'open',
                                'open_date' => '2016-09-30 06:00:00',
                                'close_date' => '2016-10-29 06:00:00',
                                'wess_admin_link' => null
                            ]
                        ]
                    ],
                    // Example 3a:  All survey and cohort combinations with ISQs for org 19.
                    [19, 'isq', null,
                        [
                            [
                                'year_id' => '201516',
                                'org_academic_year_id' => '59',
                                'year_name' => '2015-2016',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'status' => 'closed',
                                'open_date' => '2015-08-31 05:00:00',
                                'close_date' => '2015-09-22 05:00:00',
                                'wess_admin_link' => null
                            ],
                            [
                                'year_id' => '201516',
                                'org_academic_year_id' => '59',
                                'year_name' => '2015-2016',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'survey_id' => '13',
                                'survey_name' => 'Transition Two',
                                'status' => 'open',
                                'open_date' => '2015-09-28 05:00:00',
                                'close_date' => '2015-10-29 05:00:00',
                                'wess_admin_link' => null
                            ]
                        ]
                    ],
                    // Example 3b:  Only launched/closed surveys and cohort combinations with ISQs for org 19.
                    [19, 'isq', ['launched', 'closed'],
                        [
                            [
                                'year_id' => '201516',
                                'org_academic_year_id' => '59',
                                'year_name' => '2015-2016',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'status' => 'closed',
                                'open_date' => '2015-08-31 05:00:00',
                                'close_date' => '2015-09-22 05:00:00',
                                'wess_admin_link' => null
                            ]
                        ]
                    ]
                ]
            ]
        );
    }


    public function testGetSurveysAndCohortsHavingAccessibleISQs()
    {
        $this->specify("Verify the functionality of the method getSurveysAndCohortsHavingAccessibleISQs", function ($orgId, $permissionSetIds, $isAggregateReporting, $surveyStatus, $expectedResult) {

            $result = $this->wessLinkRepository->getSurveysAndCohortsHavingAccessibleISQs($orgId, $permissionSetIds, $isAggregateReporting, null, $surveyStatus);

            verify($result)->equals($expectedResult);

        }, ["examples" =>
                [
                    // Example 1a:  This is an aggregate-only permission set, so when $isAggregateReporting is true,
                    // the surveys and cohorts with ISQs in this permission set should be returned.
                    [51, [299], true, null,
                        [
                            [
                                'year_id' => '201516',
                                'org_academic_year_id' => '104',
                                'year_name' => 'Academic Year 2015-16',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'status' => 'closed',
                                'open_date' => '2015-09-18 05:00:00',
                                'close_date' => '2015-10-13 05:00:00'
                            ],
                            [
                                'year_id' => '201516',
                                'org_academic_year_id' => '104',
                                'year_name' => 'Academic Year 2015-16',
                                'cohort' => '2',
                                'cohort_name' => 'Survey Cohort 2',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'status' => 'closed',
                                'open_date' => '2015-09-18 05:00:00',
                                'close_date' => '2015-10-13 05:00:00'
                            ],
                            [
                                'year_id' => '201516',
                                'org_academic_year_id' => '104',
                                'year_name' => 'Academic Year 2015-16',
                                'cohort' => '3',
                                'cohort_name' => 'Survey Cohort 3',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'status' => 'closed',
                                'open_date' => '2015-08-31 05:00:00',
                                'close_date' => '2015-10-13 05:00:00'
                            ]
                        ]
                    ],
                    // Example 1b:  This is an aggregate-only permission set, so when $isAggregateReporting is false,
                    // no surveys and cohorts should be returned.
                    [51, [299], false, null, []],
                    // Example 1c:  These two permission sets have access to the same ISQs.
                    // Since permission set 295 grants individual access, the surveys and cohorts should be listed.
                    [51, [295, 299], false, null,
                        [
                            [
                                'year_id' => '201516',
                                'org_academic_year_id' => '104',
                                'year_name' => 'Academic Year 2015-16',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'status' => 'closed',
                                'open_date' => '2015-09-18 05:00:00',
                                'close_date' => '2015-10-13 05:00:00'
                            ],
                            [
                                'year_id' => '201516',
                                'org_academic_year_id' => '104',
                                'year_name' => 'Academic Year 2015-16',
                                'cohort' => '2',
                                'cohort_name' => 'Survey Cohort 2',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'status' => 'closed',
                                'open_date' => '2015-09-18 05:00:00',
                                'close_date' => '2015-10-13 05:00:00'
                            ],
                            [
                                'year_id' => '201516',
                                'org_academic_year_id' => '104',
                                'year_name' => 'Academic Year 2015-16',
                                'cohort' => '3',
                                'cohort_name' => 'Survey Cohort 3',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'status' => 'closed',
                                'open_date' => '2015-08-31 05:00:00',
                                'close_date' => '2015-10-13 05:00:00'
                            ]
                        ]
                    ],
                    // Example 1d:  Permission set 298 grants individual access but has no ISQs.
                    // Permission set 299 has ISQs but is aggregate only.
                    // When $isAggregateReporting is false, no surveys and cohorts should be returned.
                    [51, [298, 299], false, null, []],
                    // Example 2a:  All survey and cohort combinations with accessible ISQs for permission set 618.
                    [104, [618], true, null,
                        [
                            [
                                'year_id' => '201516',
                                'org_academic_year_id' => '122',
                                'year_name' => '2015-2016',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'survey_id' => '12',
                                'survey_name' => 'Check-Up One',
                                'status' => 'open',
                                'open_date' => '2015-09-28 05:00:00',
                                'close_date' => '2015-10-29 05:00:00'
                            ],
                            [
                                'year_id' => '201516',
                                'org_academic_year_id' => '122',
                                'year_name' => '2015-2016',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'status' => 'closed',
                                'open_date' => '2015-09-25 05:00:00',
                                'close_date' => '2015-10-25 05:00:00'
                            ],
                            [
                                'year_id' => '201516',
                                'org_academic_year_id' => '122',
                                'year_name' => '2015-2016',
                                'cohort' => '2',
                                'cohort_name' => 'Survey Cohort 2',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'status' => 'closed',
                                'open_date' => '2015-09-25 05:00:00',
                                'close_date' => '2015-10-25 05:00:00'
                            ]
                        ]
                    ],
                    // Example 2b:  Only launched/closed survey and cohort combinations with accessible ISQs for permission set 618.
                    [104, [618], true, ['launched', 'closed'],
                        [
                            [
                                'year_id' => '201516',
                                'org_academic_year_id' => '122',
                                'year_name' => '2015-2016',
                                'cohort' => '1',
                                'cohort_name' => 'Survey Cohort 1',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'status' => 'closed',
                                'open_date' => '2015-09-25 05:00:00',
                                'close_date' => '2015-10-25 05:00:00'
                            ],
                            [
                                'year_id' => '201516',
                                'org_academic_year_id' => '122',
                                'year_name' => '2015-2016',
                                'cohort' => '2',
                                'cohort_name' => 'Survey Cohort 2',
                                'survey_id' => '11',
                                'survey_name' => 'Transition One',
                                'status' => 'closed',
                                'open_date' => '2015-09-25 05:00:00',
                                'close_date' => '2015-10-25 05:00:00'
                            ]
                        ]
                    ]
                ]
            ]
        );
    }

    public function testGetSurveyClosedDateForFaculty()
    {
        $this->specify("Verify the functionality of the method getSurveyClosedDateForFaculty", function ($facultyId, $organizationId, $expectedResult) {
            $result = $this->wessLinkRepository->getSurveyClosedDateForFaculty($facultyId, $organizationId);
            verify($result)->equals($expectedResult);
        }, ["examples" =>
                [
                    [ //Example 1 Test with invalid person id & empty data
                        123,
                        1,
                        []
                    ],
                    [ //Example 2 Test with valid person id & non empty data
                        4799548, //faculty id
                        2, // org id
                        [ // Expected output
                            'close_date' => '2016-03-02 06:00:00'
                        ]
                    ],

                ]
            ]
        );
    }

}