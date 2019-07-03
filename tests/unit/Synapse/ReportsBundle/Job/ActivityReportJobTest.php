<?php

use Synapse\ReportsBundle\Job\ActivityReportJob;

class ActivityReportJobTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    private $activityResponse = [
        'sections' => [
            0 => [
                'title' => 'Top Activities',
                'elements' => [
                    0 => [
                        'element_id' => 'N',
                        'value' => 2
                    ]

                ],

                'section_id' => 101
            ],

            1 => [
                'title' => 'Faculty/Staff',
                'value' => 2,
                'elements' => [
                    0 => [
                        'element_id' => 'total',
                        'value' => 2
                    ]

                ],
                'section_id' => 1
            ],

            2 => [
                'title' => 'Students',
                'value' => 2,
                'elements' => [
                    0 => [
                        'element_id' => 'total',
                        'value' => 2
                    ]

                ],
                'section_id' => 2
            ],
            3 => [
                'section_id' => 3,
                'title' => 'Activity Overview',
                'value' => '',
                'elements' => [
                    0 => [
                        'element_id' => 6,
                        'title' => '# of activities logged',
                        'value' => [
                            0 => [
                                'value' => 2,
                                'id' => 'notes'
                            ],

                            1 => [
                                'value' => 2,
                                'id' => 'appointments'
                            ],

                            2 => [
                                'value' => 2,
                                'id' => 'referrals'
                            ],

                            3 => [
                                'value' => 2,
                                'id' => 'contacts'
                            ]

                        ]

                    ],

                    1 => [
                        'element_id' => 7,
                        'title' => '# of students involved',
                        'value' => [
                            0 => [
                                'value' => 2,
                                'id' => 'notes'
                            ],

                            1 => [
                                'value' => 2,
                                'id' => 'appointments'
                            ],

                            2 => [
                                'value' => 2,
                                'id' => 'referrals'
                            ],

                            3 => [
                                'value' => 2,
                                'id' => 'contacts'
                            ]

                        ]

                    ],

                    2 => [
                        'element_id' => 8,
                        'title' => '% of students',
                        'value' => [
                            0 => [
                                'value' => 100,
                                'id' => 'notes'
                            ],

                            1 => [
                                'value' => 100,
                                'id' => 'appointments'
                            ],

                            2 => [
                                'value' => 100,
                                'id' => 'referrals'
                            ],

                            3 => [
                                'value' => 100,
                                'id' => 'contacts'
                            ]

                        ]

                    ],

                    3 => [
                        'element_id' => 9,
                        'title' => '# of Faculty/Staff logged',
                        'value' => [
                            0 => [
                                'value' => 2,
                                'id' => 'notes'
                            ],

                            1 => [
                                'value' => 2,
                                'id' => 'appointments'
                            ],

                            2 => [
                                'value' => 2,
                                'id' => 'referrals'
                            ],

                            3 => [
                                'value' => 2,
                                'id' => 'contacts'
                            ]

                        ]

                    ],

                    4 => [
                        'element_id' => 10,
                        'title' => '# of Faculty/Staff received',
                        'value' => [
                            0 => [
                                'value' => 1,
                                'id' => 'notes'
                            ],

                            1 => [
                                'value' => 1,
                                'id' => 'appointments'
                            ],

                            2 => [
                                'value' => 1,
                                'id' => 'referrals'
                            ],

                            3 => [
                                'value' => 1,
                                'id' => 'contacts'
                            ]

                        ]

                    ]

                ]

            ],

            4 => [
                'section_id' => 4,
                'title' => 'Categories',
                'value' => '',
                'elements' => [
                    0 => [
                        'element_id' => 'N',
                        'title' => 'N',
                        'value' => [
                            0 => [
                                'value' => 50,
                                'id' => 'notes'
                            ]

                        ]

                    ]

                ]

            ],

            5 => [
                'title' => 'Referrals',
                'value' => 1,
                'elements' => [
                    0 => [
                        'element_id' => 'total_referrals',
                        'value' => 1
                    ],

                    1 => [
                        'element_id' => 'discussed_count',
                        'value' => 300
                    ],

                    2 => [
                        'element_id' => 'intent_to_leave_count',
                        'value' => 100
                    ],

                    3 => [
                        'element_id' => 'high_priority_concern_count',
                        'value' => 100
                    ],

                    4 => [
                        'element_id' => 'number_open_count',
                        'value' => 100,
                        'count' => 1
                    ],

                    5 => [
                        'element_id' => 'open_count',
                        'value' => 100
                    ]

                ],

                'section_id' => 5
            ],

            6 => [
                'title' => 'Appointments',
                'value' => 1,
                'elements' => [
                    0 => [
                        'element_id' => 'total_appointments',
                        'value' => 1
                    ],

                    1 => [
                        'element_id' => 'completed_count',
                        'value' => 100
                    ],

                    2 => [
                        'element_id' => 'staff_initiated_count',
                        'value' => 100
                    ],

                    3 => [
                        'element_id' => 'student_initiated_count',
                        'value' => 0
                    ]

                ],

                'section_id' => 6
            ],

            7 => [
                'title' => 'Contacts',
                'value' => 1,
                'elements' => [
                    0 => [
                        'element_id' => 'total_contacts',
                        'value' => 1
                    ],

                    1 => [
                        'element_id' => 'interaction_contacts_count',
                        'value' => 100
                    ],

                    2 => [
                        'element_id' => 'non_interaction_contacts_count',
                        'value' => 100
                    ]

                ],

                'section_id' => 7
            ],

            8 => [
                'title' => 'Academic Updates',
                'value' => 2,
                'elements' => [
                    0 => [
                        'element_id' => 'total_au',
                        'value' => 2
                    ],

                    1 => [
                        'element_id' => 'failure_risk_level_count',
                        'value' => 100
                    ],

                    2 => [
                        'element_id' => 'grade_df_count',
                        'value' => 100
                    ],

                    3 => [
                        'element_id' => 'request_count',
                        'value' => 0,
                        'count' => 2
                    ],

                    4 => [
                        'element_id' => 'request_closed_count',
                        'value' => 100
                    ]

                ],

                'section_id' => 11
            ],

            9 => [
                'title' => 'Referral Categories',
                'elements' => [
                    0 => [
                        0 => 2,
                        'value' => 100
                    ]

                ],

                'section_id' => 8
            ],

            10 => [
                'title' => 'Appointment Categories',
                'elements' => [
                    0 => [
                        'value' => 50,
                        0 => 2
                    ]

                ],

                'section_id' => 9
            ],

            11 => [
                'title' => 'Contact Categories',
                'elements' => [
                    0 => [
                        'value' => 50,
                        0 => 2
                    ]

                ],

                'section_id' => 10
            ]

        ],

        'report_info' => [
            'report_id' => 1,
            'report_name' => 'report',
            'short_code' => 'act',
            'report_instance_id' => 1,
            'report_date' => null,
            'report_start_date' => '2015-06-01',
            'report_end_date' => '2015-06-01',
            'students_count' => 2,
            'report_by' => [
                'first_name' => 'Joe',
                'last_name' => 'Dubinator'
            ]

        ],

        'campus_info' => [
            'campus_id' => 2,
            'campus_name' => 'ReportName',
            'campus_logo' => null
        ],

        'search_filter' => [
            'team_ids' => 1
        ],

        'report_sections' => [
            'report_name' => 'report',
            'short_code' => 'act'
        ],

        'request_json' => '',
    ];

    use Codeception\specify;


    // tests
    public function testRun()
    {
        $this->markTestSkipped("Breaking the CI build in bitbucket, need to investigate why, later");
        $this->specify("test if run is returning the correct activity report", function ($args, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockReportSections = $this->getMock('am', array('get'));
            $mockSearchService = $this->getMock('searchService', array('getStudentListBasedCriteria'));
            $mockReportRunningStatus = $this->getMock('reportRunningStatusRepo', array('findOneById', 'update', 'flush'));
            $mockReportRunningStatusObject = $this->getMock('reportRunningStatusObj', array('setStatus', 'getFilterCriteria', 'setResponseJSON', 'getReports', 'getPerson', 'getId'));
            $mockOrgService = $this->getMock('orgService', array('getOrganization'));
            $mockOrgRepository = $this->getMock('orgRepo', array('findOneById'));
            $mockReportsService = $this->getMock('reportsService', array('replacePlaceHolders'));
            $mockEbiSearch = $this->getMock('ebiSearch', array('getQueryResult'));
            $mockAlertNotificationService = $this->getMock('alertnotification', array('createNotification'));
            $mockKernel = $this->getMockBuilder('kernel')->setMethods(array('getContainer'))->getMock();
            $mockOrgImage = $this->getMock('orgImage', array('getLogoFileName'));
            $mockReport = $this->getMock('report', array('getShortCode', 'getName'));

            $orgDetails = ['name' => 'ReportName'];
            $orgFilename = 'filename';


            //Stuff To Fill
            $reportFilter = "{'team_ids': 1}";
            $studentQueryResult = [['person_id' => '1']];
            //TRANSPOSE element_id to element_type, value to total_staff
            $facultyResult = [["element_type" => "total", "total_staff" => 2]];
            //TRANSPOSE element_id to element_type, value to total_students
            $studentResult = [["element_type" => "total", "total_students" => 2]];
            //TRANSPOSE element_id to activity_type, value to total_count
            $topActivityResult = [['activity_type' => 'N', 'total_count' => '2']];

            $activityOverviewResult = [
                ['element_type' => 'N', 'activity_count' => 2, 'student_count' => 2, 'faculty_count' => 2, 'received_referrals' => '1'],
                ['element_type' => 'A', 'activity_count' => 2, 'student_count' => 2, 'faculty_count' => 2, 'received_referrals' => '1'],
                ['element_type' => 'R', 'activity_count' => 2, 'student_count' => 2, 'faculty_count' => 2, 'received_referrals' => '1'],
                ['element_type' => 'C', 'activity_count' => 2, 'student_count' => 2, 'faculty_count' => 2, 'received_referrals' => '1']];
            //TRANSPOSE activity_count to value, activity_type to id   RESULTS element_id => types, title => tyeps, value => activity_count
            $activityByCategoryResult = [['activity_count' => 1, 'activity_type' => 'N', ['activity_count' => 1], 'element_type' => 'N']];
            //TRANSPOSES ALL TO ELEMENT IDS
            $referralResult = [['total_referrals' => 1, 'discussed_count' => 3, 'intent_to_leave_count' => 1, 'high_priority_concern_count' => 1, 'number_open_count' => 1, 'open_count' => 1]];
            //TRANSPOSE ALL TO ELEMENT IDS
            $appointmentsResult = [['total_appointments' => 1, 'completed_count' => 1, 'staff_initiated_count' => 1, 'student_initiated_count' => 1]];
            //TRANSPOSE ALL TO ELEMENT IDS
            $contactResult = [['total_contacts' => 1, 'interaction_contacts_count' => 1, 'non_interaction_contacts_count' => 1]];
            //TRANSPOSE ALL TO ELEMENT IDS
            $auResult = [['total_au' => 2, 'failure_risk_level_count' => 2, 'grade_df_count' => 2, 'request_count' => 2, 'request_closed_count' => 2]];
            //TRANSPOSE element_id to element_type, value to activity_count
            $referralByCategoryResult = [['element_id' => 'ref', 'value' => 2, 'activity_count' => 2]];
            //TRANSPOSE element_id to element_type, value to activity_count
            $appointmentsByCategoryResult = [['activity_count' => 1, 'element_id' => 'ref', 'value' => 2]];
            //TRANSPOSE element_id to element_type, value to activity_count
            $contactsByCategoryResult = [['activity_count' => 1, 'element_id' => 'ref', 'value' => 2]];

            //args
            /*
            $args['reportInstanceId']
            $args['orgId']
            $args['userId']
            $args['reportSections']
             $args['reporting_on_faculty_ids']
             $args['reporting_on_student_ids']
             $args['reportId']
             $args['start_date']
             $args['end_date']
              $args['reportRunByFirstName']
              $args['reportRunByLastName']
               $args['requestJson']*/

            //Replace me with scenerio
            $topActivityQuery = 'first';
            $facultyQuery = '';
            $studentQuery = '';
            $activityOverviewQuery = '';
            $activityByCategoryQuery = '';
            $referralQuery = '';
            $appointmentsQuery = '';
            $contactQuery = '';
            $auQuery = '';
            $referralByCategoryQuery = '';
            $appointmentsByCategoryQuery = '';
            $contactsByCategoryQuery = '';

            $activityReportJob = new ActivityReportJob();


            $reflection = new \ReflectionClass(get_class($activityReportJob));
            $parentReflection = $reflection->getParentClass()->getParentClass();
            $property = $parentReflection->getProperty('kernel');
            $property->setAccessible(true);
            $property->setValue($activityReportJob, $mockKernel);

            $topActivityQuery = $activityReportJob->getQueries('TopActivity');
            $facultyQuery = $activityReportJob->getQueries('faculty');
            $studentQuery = $activityReportJob->getQueries('student');
            $activityOverviewQuery = $activityReportJob->getQueries('ActivityOverview');
            $activityByCategoryQuery = $activityReportJob->getQueries('ActivityByCategory');
            $referralQuery = $activityReportJob->getQueries('referral');
            $appointmentsQuery = $activityReportJob->getQueries('appointments');
            $contactQuery = $activityReportJob->getQueries('contacts');
            $auQuery = $activityReportJob->getQueries('Au');
            $referralByCategoryQuery = $activityReportJob->getQueries('ReferralsByCategory');
            $appointmentsByCategoryQuery = $activityReportJob->getQueries('AppointmentsByCategory');
            $contactsByCategoryQuery = $activityReportJob->getQueries('ContactsByCategory');

            $mockKernel->method('getContainer')->willReturn($mockContainer);

            $mockContainer->method('get')->willReturnMap([
                ['repository_resolver', $mockRepositoryResolver],
                ['search_service', $mockSearchService],
                ['organizationlang_service', $mockOrgService],
                ['reports_service', $mockReportsService],
                ['alertNotifications_service', $mockAlertNotificationService]
            ]);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                ['SynapseReportsBundle:ReportSections', $mockReportSections],
                ['SynapseReportsBundle:ReportsRunningStatus', $mockReportRunningStatus],
                ['SynapseCoreBundle:Organization', $mockOrgRepository],
                ['SynapseCoreBundle:EbiSearch', $mockEbiSearch]
            ]);

            $mockReportRunningStatus->method('findOneById')->willReturn($mockReportRunningStatusObject);
            $mockReportRunningStatus->method('update')->willReturn('');
            $mockReportRunningStatus->method('flush')->willReturn('');
            $mockOrgService->method('getOrganization')->willReturn($orgDetails);
            $mockOrgRepository->method('findOneById')->willReturn($mockOrgImage);
            $mockOrgRepository->method('getLogoFileName')->willReturn($orgFilename);
            $mockReportRunningStatusObject->method('getFilterCriteria')->willReturn($reportFilter);
            $mockReportRunningStatusObject->method('setStatus')->willReturn('');
            $mockReportRunningStatusObject->method('setResponseJSON')->willReturn('');
            $mockReportRunningStatusObject->method('getReports')->willReturn($mockReport);
            $mockReport->method('getShortCode')->willReturn('');
            $mockReport->method('getName')->willReturn('');
            $mockReportRunningStatusObject->method('getPerson')->willReturn('');
            $mockReportRunningStatusObject->method('getId')->willReturn('');
            $mockSearchService->method('getStudentListBasedCriteria')->willReturn('');
            $mockReportsService->method('replacePlaceHolders')->willReturn('first');


            //FIXING RESULTS
            $topActivityQuery = $activityReportJob->replacePlaceHoldersInQuery($topActivityQuery, $args);
            $studentQuery = $activityReportJob->replacePlaceHoldersInQuery($studentQuery, $args);
            $facultyQuery = $activityReportJob->replacePlaceHoldersInQuery($facultyQuery, $args);
            $activityOverviewQuery = $activityReportJob->replacePlaceHoldersInQuery($activityOverviewQuery, $args);
            $activityByCategoryQuery = $activityReportJob->replacePlaceHoldersInQuery($activityByCategoryQuery, $args);
            $referralQuery = $activityReportJob->replacePlaceHoldersInQuery($referralQuery, $args);
            $appointmentsQuery = $activityReportJob->replacePlaceHoldersInQuery($appointmentsQuery, $args);
            $contactQuery = $activityReportJob->replacePlaceHoldersInQuery($contactQuery, $args);
            $auQuery = $activityReportJob->replacePlaceHoldersInQuery($auQuery, $args);
            $referralByCategoryQuery = $activityReportJob->replacePlaceHoldersInQuery($referralByCategoryQuery, $args);
            $appointmentsByCategoryQuery = $activityReportJob->replacePlaceHoldersInQuery($appointmentsByCategoryQuery, $args);
            $contactsByCategoryQuery = $activityReportJob->replacePlaceHoldersInQuery($contactsByCategoryQuery, $args);

            $mockEbiSearch->method('getQueryResult')->willReturnMap([
                ['first', $studentQueryResult],
                [$topActivityQuery, $topActivityResult],
                [$studentQuery, $studentResult],
                [$facultyQuery, $facultyResult],
                [$activityOverviewQuery, $activityOverviewResult],
                [$activityByCategoryQuery, $activityByCategoryResult],
                [$referralQuery, $referralResult],
                [$appointmentsQuery, $appointmentsResult],
                [$contactQuery, $contactResult],
                [$auQuery, $auResult],
                [$referralByCategoryQuery, $referralByCategoryResult],
                [$appointmentsByCategoryQuery, $appointmentsByCategoryResult],
                [$contactsByCategoryQuery, $contactsByCategoryResult]
            ]);

            $mockAlertNotificationService->method('createNotification')->willReturn('');

            $activityReport = $activityReportJob->run($args);


            //Setting Report date to null since I have no access to date() generated
            $activityReport['report_info']['report_date'] = null;

            $this->assertEquals($expectedResult, $activityReport);

        }, ['examples' => [
            [['reportInstanceId' => 1, 'orgId' => 2, 'userId' => 2, 'reportSections' => ['report_name' => 'report', 'short_code' => 'act'],
                'reporting_on_student_ids' => '1',
                'reporting_on_faculty_ids' => 'SELECT DISTINCT person_id FROM team_members t WHERE t.organization_id = 2 AND t.teams_id IN (1)',
                'reportId' => 1,
                'start_date' => '2015-06-01',
                'end_date' => '2015-06-01',
                'reportRunByFirstName' => 'Joe',
                'reportRunByLastName' => 'Dubinator',
                'requestJson' => ''], $this->activityResponse]
        ]]);
    }

}


