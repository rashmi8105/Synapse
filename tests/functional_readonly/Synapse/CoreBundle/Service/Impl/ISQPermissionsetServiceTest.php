<?php

use Codeception\TestCase\Test;

class ISQPermissionsetServiceTest extends Test
{
    use Codeception\Specify;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Service\Impl\ISQPermissionsetService
     */
    private $isqPermissionsetService;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->isqPermissionsetService = $this->container->get('isq_permissionset_service');
    }


    public function testGetSurveysAndCohortsHavingAccessibleISQs()
    {
        $this->specify("Verify the functionality of the method getSurveysAndCohortsHavingAccessibleISQs", function($loggedInUserId, $orgId, $isAggregateReporting, $userHasCoordinatorAccess, $expectedResult) {

            $result = $this->isqPermissionsetService->getSurveysAndCohortsHavingAccessibleISQs($loggedInUserId, $orgId, $isAggregateReporting, $userHasCoordinatorAccess);

            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                // Example 1:  Coordinator access -- list includes all surveys and cohorts which have ISQs set up for them.
                [138082, 163, false, true,
                    [
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '57',
                            'year_name' => '2015-2016',
                            'cohort' => '1',
                            'cohort_name' => 'Survey Cohort 1',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'status' => 'closed',
                            'open_date' => '2015-09-20 06:00:00',
                            'close_date' => '2015-10-25 06:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '57',
                            'year_name' => '2015-2016',
                            'cohort' => '1',
                            'cohort_name' => 'Survey Cohort 1',
                            'survey_id' => '12',
                            'survey_name' => 'Check-Up One',
                            'status' => 'closed',
                            'open_date' => '2015-11-10 06:00:00',
                            'close_date' => '2015-11-24 06:00:00',
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
                            'open_date' => '2015-09-21 06:00:00',
                            'close_date' => '2015-10-25 06:00:00',
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
                            'open_date' => '2015-11-10 06:00:00',
                            'close_date' => '2015-11-24 06:00:00',
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
                            'open_date' => '2015-09-21 06:00:00',
                            'close_date' => '2015-10-25 06:00:00',
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
                            'open_date' => '2015-11-10 06:00:00',
                            'close_date' => '2015-11-24 06:00:00',
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
                            'open_date' => '2015-09-21 06:00:00',
                            'close_date' => '2015-10-25 06:00:00',
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
                            'open_date' => '2015-11-10 06:00:00',
                            'close_date' => '2015-11-24 06:00:00',
                            'wess_admin_link' => null
                        ]
                    ]
                ],
                // Example 2:  This person has current_future_isq permission,
                // even though he doesn't individually have access to the ISQs.
                [4869471, 110, false, false,
                    [
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '149',
                            'year_name' => 'AY1516',
                            'cohort' => '1',
                            'cohort_name' => 'Survey Cohort 1',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'status' => 'closed',
                            'open_date' => '2015-12-09 08:00:00',
                            'close_date' => '2016-01-20 08:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '149',
                            'year_name' => 'AY1516',
                            'cohort' => '1',
                            'cohort_name' => 'Survey Cohort 1',
                            'survey_id' => '13',
                            'survey_name' => 'Transition Two',
                            'status' => 'closed',
                            'open_date' => '2016-04-06 08:00:00',
                            'close_date' => '2016-05-19 08:00:00',
                            'wess_admin_link' => null
                        ]
                    ]
                ],
                // Example 3:  Coordinator access; different cohorts have different surveys
                [223822, 179, false, true,
                    [
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '66',
                            'year_name' => '201516',
                            'cohort' => '1',
                            'cohort_name' => 'Survey Cohort 1',
                            'survey_id' => '11',
                            'survey_name' => 'Transition One',
                            'status' => 'closed',
                            'open_date' => '2015-09-27 06:00:00',
                            'close_date' => '2015-10-29 06:00:00',
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
                            'open_date' => '2015-11-01 06:00:00',
                            'close_date' => '2015-12-01 06:00:00',
                            'wess_admin_link' => null
                        ],
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '66',
                            'year_name' => '201516',
                            'cohort' => '1',
                            'cohort_name' => 'Survey Cohort 1',
                            'survey_id' => '14',
                            'survey_name' => 'Check-Up Two',
                            'status' => 'closed',
                            'open_date' => '2016-03-28 06:00:00',
                            'close_date' => '2016-04-05 06:00:00',
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
                            'open_date' => '2015-09-27 06:00:00',
                            'close_date' => '2015-10-29 06:00:00',
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
                            'open_date' => '2016-02-21 06:00:00',
                            'close_date' => '2016-03-22 06:00:00',
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
                            'open_date' => '2015-09-27 06:00:00',
                            'close_date' => '2015-10-29 06:00:00',
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
                            'open_date' => '2016-02-21 06:00:00',
                            'close_date' => '2016-03-22 06:00:00',
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
                            'open_date' => '2015-09-27 06:00:00',
                            'close_date' => '2015-10-29 06:00:00',
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
                            'open_date' => '2016-02-21 06:00:00',
                            'close_date' => '2016-03-22 06:00:00',
                            'wess_admin_link' => null
                        ]
                    ]
                ],
                // Example 4:  This is the same organization as the previous example,
                // but this person's permission set only includes ISQs for one survey and cohort combination.
                [51650, 179, false, false,
                    [
                        [
                            'year_id' => '201516',
                            'org_academic_year_id' => '66',
                            'year_name' => '201516',
                            'cohort' => '4',
                            'cohort_name' => 'Survey Cohort 4',
                            'survey_id' => '13',
                            'survey_name' => 'Transition Two',
                            'status' => 'closed',
                            'open_date' => '2016-02-21 06:00:00',
                            'close_date' => '2016-03-22 06:00:00',
                        ]
                    ]
                ]
            ]
        ]);
    }

}