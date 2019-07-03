<?php

use Synapse\ReportsBundle\Service\Impl\ActivityReportService;
use Synapse\CoreBundle\Repository\SearchRepository;
use Codeception\TestCase\Test;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;


class ActivityReportServiceTest extends Test
{
    use Codeception\Specify;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var ActivityReportService
     */
    private $activityReportService;

    /**
     * @var SearchRepository
     */
    private $searchRepository;


    public function _before()
    {
        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->activityReportService = $this->container->get(ActivityReportService::SERVICE_KEY);
        $this->searchRepository = $this->repositoryResolver->getRepository(SearchRepository::REPOSITORY_KEY);
    }


    public function testGetActivityReportSectionQueries()
    {
        $this->specify('getActivityReportSectionQueries test', function ($expectedResults, $queryKey, $queryArgs = null) {
            $query = $this->activityReportService->getActivityReportSectionQueries($queryKey);
            $replacedQuery = $this->activityReportService->replacePlaceHoldersInQuery($query, $queryArgs);
            $functionResults = $this->searchRepository->getQueryResult($replacedQuery);

            verify($functionResults)->equals($expectedResults);
        },
            [
                'examples' =>
                    [
                        // Academic updates query test, with data
                        [
                            [
                                0 =>
                                    [
                                        'total_au' => '29155',
                                        'failure_risk_level_count' => '2010',
                                        'grade_df_count' => '2162',
                                        'student_involved' => '4049',
                                        'faculty_logged' => '343'
                                    ]
                            ],
                            'Au',
                            [
                                'orgId' => 118,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],
                        // Academic updates query test, without data
                        [
                            [
                                0 =>
                                    [
                                        'total_au' => '0',
                                        'failure_risk_level_count' => null,
                                        'grade_df_count' => null,
                                        'student_involved' => '0',
                                        'faculty_logged' => '0'
                                    ]
                            ],
                            'Au',
                            [
                                'orgId' => 999,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],

                        // TopActivity query test, with data
                        [
                            [
                                0 =>
                                    [
                                        'activity_type' => 'referral',
                                        'total_count' => '1237'
                                    ],
                                1 =>
                                    [
                                        'activity_type' => 'appointment',
                                        'total_count' => 0
                                    ],
                                2 =>
                                    [
                                        'activity_type' => 'contact',
                                        'total_count' => '20547'
                                    ],
                                3 =>
                                    [
                                        'activity_type' => 'academic_update',
                                        'total_count' => '29155'
                                    ]
                            ],
                            'TopActivity',
                            [
                                'orgId' => 118,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],
                        // TopActivity query test, without data
                        [
                            [
                                0 =>
                                    [
                                        'activity_type' => 'referral',
                                        'total_count' => 0
                                    ],
                                1 =>
                                    [
                                        'activity_type' => 'appointment',
                                        'total_count' => 0
                                    ],
                                2 =>
                                    [
                                        'activity_type' => 'contact',
                                        'total_count' => 0
                                    ],
                                3 =>
                                    [
                                        'activity_type' => 'academic_update',
                                        'total_count' => 0
                                    ]
                            ],
                            'TopActivity',
                            [
                                'orgId' => 999,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],

                        // Faculty query test, with data
                        [
                            [
                                0 =>
                                    [
                                        'element_type' => 'campus_connection',
                                        'total_staff' => '250'
                                    ],
                                1 =>
                                    [
                                        'element_type' => 'total',
                                        'total_staff' => '931'
                                    ],
                                2 =>
                                    [
                                        'element_type' => 'who_accessed_mapworks',
                                        'total_staff' => '482'
                                    ]
                            ],
                            'faculty',
                            [
                                'orgId' => 118,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],
                        // Faculty query test, without data
                        [
                            [
                                0 =>
                                    [
                                        'element_type' => 'campus_connection',
                                        'total_staff' => '0'
                                    ],
                                1 =>
                                    [
                                        'element_type' => 'total',
                                        'total_staff' => '0'
                                    ],
                                2 =>
                                    [
                                        'element_type' => 'who_accessed_mapworks',
                                        'total_staff' => '0'
                                    ]
                            ],
                            'faculty',
                            [
                                'orgId' => 999,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],

                        // Student query test, with data
                        // Note that the total_students count on students_with_activity is incorrect, but looks to be
                        //      unused in the report.
                        [
                            [
                                0 =>
                                    [
                                        'element_type' => 'student_viewed',
                                        'total_students' => '158'
                                    ],
                                1 =>
                                    [
                                        'element_type' => 'student_with_activity',
                                        'total_students' => '483945'
                                    ],
                                2 =>
                                    [
                                        'element_type' => 'total',
                                        'total_students' => '5830'
                                    ]
                            ],
                            'student',
                            [
                                'orgId' => 118,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],
                        // Student query test, without data
                        // Note that the total_students count on students_with_activity is incorrect, but looks to be
                        //      unused in the report.
                        [
                            [
                                0 =>
                                    [
                                        'element_type' => 'student_viewed',
                                        'total_students' => '0'
                                    ],
                                1 =>
                                    [
                                        'element_type' => 'student_with_activity',
                                        'total_students' => '483945'
                                    ],
                                2 =>
                                    [
                                        'element_type' => 'total',
                                        'total_students' => '0'
                                    ]
                            ],
                            'student',
                            [
                                'orgId' => 999,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],

                        // ActivityOverview query test, with data
                        [
                            [
                                0 =>
                                    [
                                        'element_type' => 'E',
                                        'activity_count' => '34',
                                        'student_count' => '33',
                                        'student_percentage' => '0',
                                        'faculty_count' => '8',
                                        'received_referrals' => '0'
                                    ],
                                1 =>
                                    [
                                        'element_type' => 'IC',
                                        'activity_count' => '1357',
                                        'student_count' => '506',
                                        'student_percentage' => '0',
                                        'faculty_count' => '28',
                                        'received_referrals' => '0'
                                    ],
                                2 =>
                                    [
                                        'element_type' => 'N',
                                        'activity_count' => '2828',
                                        'student_count' => '1234',
                                        'student_percentage' => '0',
                                        'faculty_count' => '66',
                                        'received_referrals' => '0'
                                    ],
                                3 =>
                                    [
                                        'element_type' => 'NIC',
                                        'activity_count' => '19190',
                                        'student_count' => '2655',
                                        'student_percentage' => '0',
                                        'faculty_count' => '67',
                                        'received_referrals' => '0'
                                    ],
                                4 =>
                                    [
                                        'element_type' => 'R',
                                        'activity_count' => '1236',
                                        'student_count' => '796',
                                        'student_percentage' => '0',
                                        'faculty_count' => '206',
                                        'received_referrals' => '129'
                                    ],
                                5 =>
                                    [
                                        'element_type' => 'C',
                                        'activity_count' => '20547',
                                        'student_count' => '2996',
                                        'student_percentage' => '0',
                                        'faculty_count' => '69',
                                        'received_referrals' => '0'
                                    ],
                                6 =>
                                    [
                                        'element_type' => 'AU',
                                        'activity_count' => '29155',
                                        'student_count' => '4049',
                                        'student_percentage' => '0',
                                        'faculty_count' => '343',
                                        'received_referrals' => '0'
                                    ]
                            ],
                            'ActivityOverview',
                            [
                                'orgId' => 118,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],
                        // ActivityOverview query test, without data
                        [
                            [
                                0 =>
                                    [
                                        'element_type' => 'C',
                                        'activity_count' => '0',
                                        'student_count' => '0',
                                        'student_percentage' => '0',
                                        'faculty_count' => '0',
                                        'received_referrals' => '0'
                                    ],
                                1 =>
                                    [
                                        'element_type' => 'AU',
                                        'activity_count' => '0',
                                        'student_count' => '0',
                                        'student_percentage' => '0',
                                        'faculty_count' => '0',
                                        'received_referrals' => '0'
                                    ]
                            ],
                            'ActivityOverview',
                            [
                                'orgId' => 999,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],

                        // ActivityByCategory query test, with data
                        [
                            [
                                0 =>
                                    [
                                        'activity_type' => 'N',
                                        'element_type' => 'Academic Issues',
                                        'activity_count' => '2043'
                                    ],
                                1 =>
                                    [
                                        'activity_type' => 'N',
                                        'element_type' => 'Personal Issues',
                                        'activity_count' => '167'
                                    ],
                                2 =>
                                    [
                                        'activity_type' => 'N',
                                        'element_type' => 'Financial Issues',
                                        'activity_count' => '257'
                                    ],
                                3 =>
                                    [
                                        'activity_type' => 'N',
                                        'element_type' => 'MAP-Works Issues',
                                        'activity_count' => '6'
                                    ],
                                4 =>
                                    [
                                        'activity_type' => 'C',
                                        'element_type' => 'Academic Issues',
                                        'activity_count' => '17103'
                                    ],
                                5 =>
                                    [
                                        'activity_type' => 'C',
                                        'element_type' => 'Personal Issues',
                                        'activity_count' => '696'
                                    ],
                                6 =>
                                    [
                                        'activity_type' => 'C',
                                        'element_type' => 'Financial Issues',
                                        'activity_count' => '237'
                                    ],
                                7 =>
                                    [
                                        'activity_type' => 'C',
                                        'element_type' => 'MAP-Works Issues',
                                        'activity_count' => '15'
                                    ],
                                8 =>
                                    [
                                        'activity_type' => 'IC',
                                        'element_type' => 'Academic Issues',
                                        'activity_count' => '1323'
                                    ],
                                9 =>
                                    [
                                        'activity_type' => 'IC',
                                        'element_type' => 'Personal Issues',
                                        'activity_count' => '21'
                                    ],
                                10 =>
                                    [
                                        'activity_type' => 'IC',
                                        'element_type' => 'Financial Issues',
                                        'activity_count' => '8'
                                    ],
                                11 =>
                                    [
                                        'activity_type' => 'IC',
                                        'element_type' => 'MAP-Works Issues',
                                        'activity_count' => '5'
                                    ],
                                12 =>
                                    [
                                        'activity_type' => 'R',
                                        'element_type' => 'Academic Issues',
                                        'activity_count' => '1054'
                                    ],
                                13 =>
                                    [
                                        'activity_type' => 'R',
                                        'element_type' => 'Personal Issues',
                                        'activity_count' => '98'
                                    ],
                                14 =>
                                    [
                                        'activity_type' => 'R',
                                        'element_type' => 'Financial Issues',
                                        'activity_count' => '84'
                                    ],
                                15 =>
                                    [
                                        'activity_type' => 'R',
                                        'element_type' => 'MAP-Works Issues',
                                        'activity_count' => '1'
                                    ]

                            ],
                            'ActivityByCategory',
                            [
                                'orgId' => 118,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],
                        // ActivityByCategory query test, without data
                        [
                            [],
                            'ActivityByCategory',
                            [
                                'orgId' => 999,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],

                        // referral query test, with data
                        [
                            [
                                0 =>
                                    [
                                        'total_referrals' => '1237',
                                        'discussed_count' => '303',
                                        'intent_to_leave_count' => '80',
                                        'high_priority_concern_count' => '323',
                                        'number_open_count' => '133'
                                    ]
                            ],
                            'referral',
                            [
                                'orgId' => 118,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],
                        // referral query test, without data
                        [
                            [
                                0 =>
                                    [
                                        'total_referrals' => '0',
                                        'discussed_count' => null,
                                        'intent_to_leave_count' => null,
                                        'high_priority_concern_count' => null,
                                        'number_open_count' => null
                                    ]
                            ],
                            'referral',
                            [
                                'orgId' => 999,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],

                        // contacts query test, with data
                        [
                            [
                                0 =>
                                    [
                                        'total_contacts' => '20547',
                                        'interaction_contacts_count' => '1357',
                                        'non_interaction_contacts_count' => '268'
                                    ]
                            ],
                            'contacts',
                            [
                                'orgId' => 118,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],
                        // contacts query test, without data
                        [
                            [
                                0 =>
                                    [
                                        'total_contacts' => '0',
                                        'interaction_contacts_count' => null,
                                        'non_interaction_contacts_count' => null
                                    ]
                            ],
                            'contacts',
                            [
                                'orgId' => 999,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],

                        // NotesByCategory query test, with data
                        [
                            [
                                0 =>
                                    [
                                        'activity_type' => 'N',
                                        'element_type' => 'Academic Issues',
                                        'activity_count' => '2043'
                                    ],
                                1 =>
                                    [
                                        'activity_type' => 'N',
                                        'element_type' => 'Personal Issues',
                                        'activity_count' => '167'
                                    ],
                                2 =>
                                    [
                                        'activity_type' => 'N',
                                        'element_type' => 'Financial Issues',
                                        'activity_count' => '257'
                                    ],
                                3 =>
                                    [
                                        'activity_type' => 'N',
                                        'element_type' => 'MAP-Works Issues',
                                        'activity_count' => '6'
                                    ]
                            ],
                            'NotesByCategory',
                            [
                                'orgId' => 118,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],
                        // NotesByCategory query test, without data
                        [
                            [],
                            'NotesByCategory',
                            [
                                'orgId' => 999,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],

                        // ContactsByCategory query test, with data
                        [
                            [
                                0 =>
                                    [
                                        'activity_type' => 'C',
                                        'element_type' => 'Academic Issues',
                                        'activity_count' => '17103'
                                    ],
                                1 =>
                                    [
                                        'activity_type' => 'C',
                                        'element_type' => 'Personal Issues',
                                        'activity_count' => '696'
                                    ],
                                2 =>
                                    [
                                        'activity_type' => 'C',
                                        'element_type' => 'Financial Issues',
                                        'activity_count' => '237'
                                    ],
                                3 =>
                                    [
                                        'activity_type' => 'C',
                                        'element_type' => 'MAP-Works Issues',
                                        'activity_count' => '15'
                                    ]
                            ],
                            'ContactsByCategory',
                            [
                                'orgId' => 118,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],
                        // ContactsByCategory query test, without data
                        [
                            [],
                            'ContactsByCategory',
                            [
                                'orgId' => 999,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],

                        // ReferralsByCategory query test, with data
                        [
                            [
                                0 =>
                                    [
                                        'activity_type' => 'R',
                                        'element_type' => 'Academic Issues',
                                        'activity_count' => '1054'
                                    ],
                                1 =>
                                    [
                                        'activity_type' => 'R',
                                        'element_type' => 'Personal Issues',
                                        'activity_count' => '98'
                                    ],
                                2 =>
                                    [
                                        'activity_type' => 'R',
                                        'element_type' => 'Financial Issues',
                                        'activity_count' => '84'
                                    ],
                                3 =>
                                    [
                                        'activity_type' => 'R',
                                        'element_type' => 'MAP-Works Issues',
                                        'activity_count' => '1'
                                    ]
                            ],
                            'ReferralsByCategory',
                            [
                                'orgId' => 118,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],
                        // ReferralsByCategory query test, without data
                        [
                            [],
                            'ReferralsByCategory',
                            [
                                'orgId' => 999,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],

                        // AppointmentsByCategory query test, with data
                        [
                            [],
                            'AppointmentsByCategory',
                            [
                                'orgId' => 118,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],
                        // AppointmentsByCategory query test, without data
                        [
                            [],
                            'AppointmentsByCategory',
                            [
                                'orgId' => 999,
                                'start_date' => '2014-01-01 00:00:00',
                                'end_date' => '2018-01-01 00:00:00',
                                'reporting_on_student_ids' => '',
                                'reporting_on_faculty_ids' => ''
                            ]
                        ],
                    ]
            ]);
    }


}
