<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\Repository\ReportsRepository;


class ReportsRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     *
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var ReportsRepository
     */
    private $reportsRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->reportsRepository = $this->repositoryResolver->getRepository(ReportsRepository::REPOSITORY_KEY);
    }


    public function testGetAllNonCoordinatorsReports()
    {
        $this->specify("Verify the functionality of the method getAllNonCoordinatorsReports", function ($expectedResults) {
            $results = $this->reportsRepository->getAllNonCoordinatorsReports();
            $this->assertEquals($expectedResults, $results);
        }, [
            "examples" => [
                // verify the expected results with returned results
                [
                    [
                        [
                            'view_name' => 'Activity',
                            'id' => '3',
                            'name' => 'All Academic Updates Report',
                            'description' => 'See all academic updates for your students.  Export to csv, perform individual or bulk actions',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'AU-R',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Outcomes',
                            'id' => '18',
                            'name' => 'Compare',
                            'description' => 'Compare outcomes of two student subpopulations. Choose by profile item or survey question, compare survey factors and GPA.',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'SUB-COM',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '6',
                            'name' => 'Group Response Report',
                            'description' => 'Compare survey response rates for different groups.  Export to csv',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => NULL,
                            'short_code' => 'SUR-GRR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '2',
                            'name' => 'Individual Response Report',
                            'description' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'SUR-IRR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '9',
                            'name' => 'Our Students Report',
                            'description' => 'See Top Five issues, high-level survey data and demographics for a single survey and cohort.. Export to pdf',
                            'is_batch_job' => 'n',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'OSR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '14',
                            'name' => 'Profile Snapshot Report',
                            'description' => 'See aggregated profile data for a given student population and academic year. Drill down to see individual students and export to csv.',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'PRO-SR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '10',
                            'name' => 'Survey Factors Report',
                            'description' => 'See aggregated values of all survey factors for a given survey and cohort.  Drill down to see individual students',
                            'is_batch_job' => 'n',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'SUR-FR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '8',
                            'name' => 'Survey Snapshot Report',
                            'description' => 'See aggregated responses to all survey questions for a given survey and cohort.  Drill down to see individual students',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'SUR-SR',
                            'is_active' => 'y',
                        ]
                    ]
                ]
            ]
        ]);
    }


    public function testGetAllCoordinatorReports()
    {
        $this->specify("Verify the functionality of the method getAllCoordinatorReports", function ($expectedResults) {
            $results = $this->reportsRepository->getAllCoordinatorReports();
            $this->assertEquals($expectedResults, $results);
        }, [
            "examples" => [
                // verify the expected results with returned results
                [
                    [
                        [
                            'view_name' => 'Outcomes',
                            'id' => '13',
                            'name' => 'Completion Report',
                            'description' => 'View completion rates of one to six years by retention tracking group and by risk.  Export to csv, print to pdf.',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'y',
                            'short_code' => 'CR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Activity',
                            'id' => '16',
                            'name' => 'Executive Summary Report',
                            'description' => 'See key statistics on effectiveness: persistence/retention, GPA, activity, and more. Print to pdf.',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'y',
                            'short_code' => 'EXEC',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Activity',
                            'id' => '11',
                            'name' => 'Faculty/Staff Usage Report',
                            'description' => 'Identify faculty/staff members and their activity. Export to csv.',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'y',
                            'short_code' => 'FUR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Outcomes',
                            'id' => '15',
                            'name' => 'GPA Report',
                            'description' => 'View average GPA over time, overall and by risk.  View percent of students with GPA < 2.0.  Export to csv, print to pdf.',
                            'is_batch_job' => 'n',
                            'is_coordinator_report' => 'y',
                            'short_code' => 'GPA',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Activity',
                            'id' => '7',
                            'name' => 'Our Mapworks Activity',
                            'description' => 'View statistics on faculty and student activity tracked in Mapworks for a given date range.  Export to pdf',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'y',
                            'short_code' => 'MAR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Outcomes',
                            'id' => '12',
                            'name' => 'Persistence and Retention Report',
                            'description' => 'View persistence and retention by retention tracking group and by risk.  Export to csv, print to pdf.',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'y',
                            'short_code' => 'PRR',
                            'is_active' => 'y',
                        ]
                    ]
                ]
            ]
        ]);
    }


    public function testGetReportsTeamLeader()
    {
        $this->specify("Verify the functionality of the method GetReportsTeamLeader", function ($reportIds, $expectedResults) {
            $results = $this->reportsRepository->getReportsTeamLeader($reportIds);
            $this->assertEquals($expectedResults, $results);
        }, [
            "examples" => [
                // verify the expected results when no report(ids) provided. only one report returned in this case
                [
                    [],
                    [
                        [
                            'view_name' => 'Activity',
                            'id' => '11',
                            'name' => 'Faculty/Staff Usage Report',
                            'description' => 'Identify faculty/staff members and their activity. Export to csv.',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'y',
                            'short_code' => 'FUR',
                            'is_active' => 'y',
                        ]
                    ]
                ],
                // verify the expected results with returned results
                [
                    [
                        3, 20, 6, 2, 9, 14, 17, 10, 8
                    ],
                    [
                        [
                            'view_name' => 'Activity',
                            'id' => '3',
                            'name' => 'All Academic Updates Report',
                            'description' => 'See all academic updates for your students.  Export to csv, perform individual or bulk actions',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'AU-R',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Activity',
                            'id' => '11',
                            'name' => 'Faculty/Staff Usage Report',
                            'description' => 'Identify faculty/staff members and their activity. Export to csv.',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'y',
                            'short_code' => 'FUR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '6',
                            'name' => 'Group Response Report',
                            'description' => 'Compare survey response rates for different groups.  Export to csv',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => NULL,
                            'short_code' => 'SUR-GRR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '2',
                            'name' => 'Individual Response Report',
                            'description' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'SUR-IRR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '9',
                            'name' => 'Our Students Report',
                            'description' => 'See Top Five issues, high-level survey data and demographics for a single survey and cohort.. Export to pdf',
                            'is_batch_job' => 'n',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'OSR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '14',
                            'name' => 'Profile Snapshot Report',
                            'description' => 'See aggregated profile data for a given student population and academic year. Drill down to see individual students and export to csv.',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'PRO-SR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '10',
                            'name' => 'Survey Factors Report',
                            'description' => 'See aggregated values of all survey factors for a given survey and cohort.  Drill down to see individual students',
                            'is_batch_job' => 'n',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'SUR-FR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '8',
                            'name' => 'Survey Snapshot Report',
                            'description' => 'See aggregated responses to all survey questions for a given survey and cohort.  Drill down to see individual students',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'SUR-SR',
                            'is_active' => 'y',
                        ]
                    ]
                ]
            ]
        ]);
    }


    public function testGetSpecificReports()
    {
        $this->specify("Verify the functionality of the method getSpecificReports", function ($reportIds, $expectedResults) {
            $results = $this->reportsRepository->getSpecificReports($reportIds);
            $this->assertEquals($expectedResults, $results);
        }, [
            "examples" => [
                // verify the expected results when no report(ids) provided. no results returned in this case
                [
                    [],
                    []
                ],
                // verify the expected results with returned results
                [
                    [
                        3, 20, 6, 2, 9, 14, 17, 10, 8, 11
                    ],
                    [
                        [
                            'view_name' => 'Activity',
                            'id' => '3',
                            'name' => 'All Academic Updates Report',
                            'description' => 'See all academic updates for your students.  Export to csv, perform individual or bulk actions',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'AU-R',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Activity',
                            'id' => '11',
                            'name' => 'Faculty/Staff Usage Report',
                            'description' => 'Identify faculty/staff members and their activity. Export to csv.',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'y',
                            'short_code' => 'FUR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '6',
                            'name' => 'Group Response Report',
                            'description' => 'Compare survey response rates for different groups.  Export to csv',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => NULL,
                            'short_code' => 'SUR-GRR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '2',
                            'name' => 'Individual Response Report',
                            'description' => 'See which students responded when, who opted out for a given survey and cohort.  Export to csv, perform individual or bulk actions',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'SUR-IRR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '9',
                            'name' => 'Our Students Report',
                            'description' => 'See Top Five issues, high-level survey data and demographics for a single survey and cohort.. Export to pdf',
                            'is_batch_job' => 'n',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'OSR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '14',
                            'name' => 'Profile Snapshot Report',
                            'description' => 'See aggregated profile data for a given student population and academic year. Drill down to see individual students and export to csv.',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'PRO-SR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '10',
                            'name' => 'Survey Factors Report',
                            'description' => 'See aggregated values of all survey factors for a given survey and cohort.  Drill down to see individual students',
                            'is_batch_job' => 'n',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'SUR-FR',
                            'is_active' => 'y',
                        ],
                        [
                            'view_name' => 'Survey and Profile',
                            'id' => '8',
                            'name' => 'Survey Snapshot Report',
                            'description' => 'See aggregated responses to all survey questions for a given survey and cohort.  Drill down to see individual students',
                            'is_batch_job' => 'y',
                            'is_coordinator_report' => 'n',
                            'short_code' => 'SUR-SR',
                            'is_active' => 'y',
                        ]
                    ]
                ]
            ]
        ]);
    }


    public function testGetFacultyIdsInAnIntersectionBetweenTeamsAndGroups()
    {

        $this->specify("Verify the functionality of the method getFacultyIdsInAnIntersectionBetweenTeamsAndGroups", function ($organizationId, $groupId, $teamId, $expectedResults) {
            $results = $this->reportsRepository->getFacultyIdsInAnIntersectionBetweenTeamsAndGroups($organizationId, $groupId, $teamId);

            for ($i = 0; $i < count($results); $i++) {
                verify($results[$i]['faculty_id'])->notEmpty();
                verify($results[$i]['faculty_id'])->equals($expectedResults[$i]);
            }

        }, ["examples" =>
            [
                [203, [369206], [4262], [4878750, 4878751]],
                [203, [370633], [4262], [4883160]]
            ]
        ]);
    }


    public function testGetFacultyUsage()
    {
        $this->specify("Verify the functionality of the method getFacultyUsage", function ($organizationId, $filteredFacultyList, $startDate, $endDate, $academicYearIds, $expectedResult) {
            $results = $this->reportsRepository->getFacultyUsage($organizationId, $filteredFacultyList, $startDate, $endDate, $academicYearIds);
            $this->assertTrue(in_array($expectedResult, $results));

        }, ["examples" =>
            [
                //Testing Individual Connections Through Group Only
                [203, [4878750], '2016-08-22', '2016-10-06', [158],
                    [
                        'person_id' => 4878750,
                        'lastname' => 'Stark',
                        'firstname' => 'Kenneth',
                        'external_id' => 4878750,
                        'username' => 'MapworksBetaUser04878750@mailinator.com',
                        'student_connected' => 998,
                        'contacts_student_count' => null,
                        'contacted_student_percentage' => null,
                        'interaction_contact_student_count' => null,
                        'interaction_contact_student_percentage' => null,
                        'reports_viewed_student_count' => null,
                        'reports_viewed_student_percentage' => null,
                        'notes_count' => null,
                        'referrals_count' => null,
                        'last_login' => null,
                        'days_login' => null
                    ]
                ],
                //General Test of Query
                [203, [4878751], '2015-08-10', '2016-07-12', [157],
                    [
                        'person_id' => 4878751,
                        'lastname' => 'Lowery',
                        'firstname' => 'Maximus',
                        'external_id' => 4878751,
                        'username' => 'MapworksBetaUser04878751@mailinator.com',
                        'student_connected' => 1000,
                        'contacts_student_count' => null,
                        'contacted_student_percentage' => null,
                        'interaction_contact_student_count' => null,
                        'interaction_contact_student_percentage' => null,
                        'reports_viewed_student_count' => null,
                        'reports_viewed_student_percentage' => null,
                        'notes_count' => null,
                        'referrals_count' => null,
                        'last_login' => '2016-01-29 16:37:24',
                        'days_login' => 2
                    ]
                ]

            ]
        ]);
    }


    public function testGetAggregatedCompletionVariablesWithRisk()
    {
        $this->specify("Verify the functionality of the method getAggregatedCompletionVariablesWithRisk", function ($riskStartDate, $riskEndDate, $organizationId, $currentYearId, $retentionTrackingYear, $studentIds, $expectedResults) {
            $results = $this->reportsRepository->getAggregatedCompletionVariablesWithRisk($riskStartDate, $riskEndDate, $organizationId, $currentYearId, $retentionTrackingYear, $studentIds);
            verify($results)->equals($expectedResults);
        }, ["examples" =>
            [
                //Testing Aggregate completion Data with risk for retention tracking year  201516 and risk level green, yellow, red, red2
                ['2015-10-10', '2016-03-05', 59, '201617', '201516', [4614765, 4614752, 4614729, 4614721, 4615031, 4614728, 4614735, 4614736, 4614748, 4614756, 4614757, 4614761],
                    [
                        [
                            'years_from_retention_track' => 0,
                            'risk_level_text' => 'green',
                            'risk_level' => 4,
                            'numerator_count' => 0,
                            'denominator_count' => 2
                        ],
                        [
                            'years_from_retention_track' => 0,
                            'risk_level_text' => 'yellow',
                            'risk_level' => 3,
                            'numerator_count' => 0,
                            'denominator_count' => 2
                        ],
                        [
                            'years_from_retention_track' => 0,
                            'risk_level_text' => 'red',
                            'risk_level' => 2,
                            'numerator_count' => 0,
                            'denominator_count' => 5
                        ],
                        [
                            'years_from_retention_track' => 0,
                            'risk_level_text' => 'red2',
                            'risk_level' => 1,
                            'numerator_count' => 0,
                            'denominator_count' => 2
                        ],
                        [
                            'years_from_retention_track' => 1,
                            'risk_level_text' => 'green',
                            'risk_level' => 4,
                            'numerator_count' => 1,
                            'denominator_count' => 2
                        ],
                        [
                            'years_from_retention_track' => 1,
                            'risk_level_text' => 'yellow',
                            'risk_level' => 3,
                            'numerator_count' => 1,
                            'denominator_count' => 2
                        ],
                        [
                            'years_from_retention_track' => 1,
                            'risk_level_text' => 'red',
                            'risk_level' => 2,
                            'numerator_count' => 1,
                            'denominator_count' => 5
                        ],
                        [
                            'years_from_retention_track' => 1,
                            'risk_level_text' => 'red2',
                            'risk_level' => 1,
                            'numerator_count' => 1,
                            'denominator_count' => 2
                        ]
                    ]
                ],
                //Testing Aggregate completion Data with risk for retention tracking year  201617 and risk level gray
                ['2016-10-10', '2017-03-05', 59, '201617', '201617', [4615031],
                    [

                        [
                            'years_from_retention_track' => 0,
                            'risk_level_text' => 'gray',
                            'risk_level' => NULL,
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ]
                    ]
                ]
            ]
        ]);
    }


    public function testGetAggregatedRetentionVariablesWithRisk()
    {
        $this->specify("Verify the functionality of the method getAggregatedRetentionVariablesWithRisk", function ($riskStartDate, $riskEndDate, $organizationId, $currentYearId, $retentionTrackingYear, $filteredStudents, $expectedResults) {
            $results = $this->reportsRepository->getAggregatedRetentionVariablesWithRisk($riskStartDate, $riskEndDate, $organizationId, $currentYearId, $retentionTrackingYear, $filteredStudents);
            verify($results)->equals($expectedResults);
        }, ["examples" =>
            [
                //Testing Aggregated Retention Data With Risk for retention Tracking Year 201516 and risk level Green, Yellow, Red, Red2.
                ['2015-10-10', '2016-03-05', 59, '201617', '201516', [4614721, 4614728, 4614729, 4614735, 4614736, 4614748, 4614752, 4614756, 4614757, 4614761, 4614765],
                    [
                        [
                            'years_from_retention_track' => 0,
                            'risk_level_text' => 'green',
                            'risk_level' => 4,
                            'midyear_numerator_count' => 2,
                            'beginning_year_numerator_count' => 0,
                            'denominator_count' => 2
                        ],
                        [
                            'years_from_retention_track' => 0,
                            'risk_level_text' => 'yellow',
                            'risk_level' => 3,
                            'midyear_numerator_count' => 2,
                            'beginning_year_numerator_count' => 0,
                            'denominator_count' => 2
                        ],
                        [
                            'years_from_retention_track' => 0,
                            'risk_level_text' => 'red',
                            'risk_level' => 2,
                            'midyear_numerator_count' => 5,
                            'beginning_year_numerator_count' => 0,
                            'denominator_count' => 5
                        ],
                        [
                            'years_from_retention_track' => 0,
                            'risk_level_text' => 'red2',
                            'risk_level' => 1,
                            'midyear_numerator_count' => 2,
                            'beginning_year_numerator_count' => 0,
                            'denominator_count' => 2
                        ],
                        [
                            'years_from_retention_track' => 1,
                            'risk_level_text' => 'green',
                            'risk_level' => 4,
                            'midyear_numerator_count' => 2,
                            'beginning_year_numerator_count' => 2,
                            'denominator_count' => 2
                        ],
                        [
                            'years_from_retention_track' => 1,
                            'risk_level_text' => 'yellow',
                            'risk_level' => 3,
                            'midyear_numerator_count' => 2,
                            'beginning_year_numerator_count' => 2,
                            'denominator_count' => 2
                        ],
                        [
                            'years_from_retention_track' => 1,
                            'risk_level_text' => 'red',
                            'risk_level' => 2,
                            'midyear_numerator_count' => 5,
                            'beginning_year_numerator_count' => 5,
                            'denominator_count' => 5
                        ],
                        [
                            'years_from_retention_track' => 1,
                            'risk_level_text' => 'red2',
                            'risk_level' => 1,
                            'midyear_numerator_count' => 2,
                            'beginning_year_numerator_count' => 2,
                            'denominator_count' => 2
                        ]
                    ]
                ],
                //Testing Aggregated Retention Data With Risk for for retention Tracking Year 201617 and risk level Grey
                ['2015-10-10', '2016-03-05', 59, '201617', '201617', [4615031],
                    [
                        [
                            'years_from_retention_track' => 0,
                            'risk_level_text' => 'gray',
                            'risk_level' => NULL,
                            'midyear_numerator_count' => 1,
                            'beginning_year_numerator_count' => 0,
                            'denominator_count' => 1
                        ]
                    ]
                ]
            ]
        ]);
    }


    public function testGetIndividualResponseReportData()
    {
        $this->specify("Verify the functionality of the method getIndividualResponseReportData", function ($surveyId, $respondedFlag, $studentListFlag, $sortByQueryString, $expectedResults) {

            $calculateLimit = ' LIMIT 1, 5 ';

            // Added student list filter query based on search attribute
            $studentListFilterQuery = " EXISTS( SELECT DISTINCT
                        merged.student_id
                    FROM
                        (SELECT 
                            ofspm.student_id, ofspm.permissionset_id
                        FROM
                            org_faculty_student_permission_map ofspm
                        WHERE
                            ofspm.org_id = 203
                                AND ofspm.faculty_id = 4878750) AS merged
                            INNER JOIN
                        org_permissionset OPS ON OPS.id = merged.permissionset_id
                            AND OPS.deleted_at IS NULL
                            AND OPS.accesslevel_ind_agg = 1
                            AND OPS.risk_indicator = 1
                            AND EXISTS( SELECT 
                                1
                            FROM
                                org_person_student_year opsy
                            WHERE
                                opsy.person_id = merged.student_id
                                    AND opsy.org_academic_year_id = 157
                                    AND opsy.deleted_at IS NULL)
                    WHERE
                        student_id = p.id)
                    AND (p.risk_level in ('1' , '2', '3', '4', '6')
                    or p.risk_level is null)
                    AND EXISTS( SELECT DISTINCT
                        opssl.person_id as person_id
                    FROM
                        org_person_student_cohort opsc
                            INNER JOIN
                        org_person_student_survey_link opssl ON opssl.person_id = opsc.person_id
                            AND opssl.org_academic_year_id = opsc.org_academic_year_id
                            AND opssl.org_id = opsc.organization_id
                            AND opssl.cohort = opsc.cohort
                            INNER JOIN
                        org_person_student_survey opss ON opss.person_id = opsc.person_id
                            AND opss.survey_id = opssl.survey_id
                    WHERE
                        opsc.organization_id = 203
                            AND (opss.receive_survey = 1
                            OR opssl.Has_Responses = 'Yes')
                            AND opssl.deleted_at IS NULL
                            AND opsc.deleted_at IS NULL
                            AND opss.deleted_at IS NULL
                            AND opsc.org_academic_year_id = 157
                            AND opssl.survey_id = 11
                            AND opsc.cohort = 1
                            AND opsc.person_id = p.id) ";

            $results = $this->reportsRepository->getIndividualResponseReportData($surveyId, $studentListFilterQuery, $calculateLimit, $respondedFlag, $studentListFlag, $sortByQueryString);
            verify($results)->equals($expectedResults);

        }, ["examples" =>
            [
                // For $respondedFlag is true, it will return count of person.
                [11, true, false, '', []],

                // For $respondedFlag is false and $studentListFlag = true, it will return student id, name with order by last_name.
                [11, false, true, 'last_name',
                    [
                        [
                            'student_id' => '4879212',
                            'first_name' => 'Carmelo',
                            'last_name' => 'Aguilar'
                        ],
                        [
                            'student_id' => '4879110',
                            'first_name' => 'Nasir',
                            'last_name' => 'Alexander'
                        ],
                        [
                            'student_id' => '4879131',
                            'first_name' => 'Jalen',
                            'last_name' => 'Alvarez'
                        ],
                        [
                            'student_id' => '4878953',
                            'first_name' => 'Dexter',
                            'last_name' => 'Andersen'
                        ],
                        [
                            'student_id' => '4879203',
                            'first_name' => 'Payton',
                            'last_name' => 'Armstrong'
                        ]
                    ]
                ],

                // For $respondedFlag is false and $studentListFlag = false, it will return person details with order by last_name.
                [11, false, false, 'last_name',
                    [
                        [
                            'person_id' => '4879212',
                            'student_id' => '4879212',
                            'first_name' => 'Carmelo',
                            'last_name' => 'Aguilar',
                            'email' => 'MapworksTestingUser01552712@mailinator.com',
                            'phone_number' => null,
                            'opted_out' => 'No',
                            'survey_status' => 'CompletedAll',
                            'responded_at' => '2015-08-30 10:00:00',
                            'survey_responded_status' => 'Yes'
                        ],
                        [
                            'person_id' => '4879110',
                            'student_id' => '4879110',
                            'first_name' => 'Nasir',
                            'last_name' => 'Alexander',
                            'email' => 'MapworksTestingUser01552610@mailinator.com',
                            'phone_number' => null,
                            'opted_out' => 'No',
                            'survey_status' => 'CompletedAll',
                            'responded_at' => '2015-08-30 10:00:00',
                            'survey_responded_status' => 'Yes'
                        ],
                        [
                            'person_id' => '4879131',
                            'student_id' => '4879131',
                            'first_name' => 'Jalen',
                            'last_name' => 'Alvarez',
                            'email' => 'MapworksTestingUser01552631@mailinator.com',
                            'phone_number' => null,
                            'opted_out' => 'No',
                            'survey_status' => 'CompletedAll',
                            'responded_at' => '2015-08-30 10:00:00',
                            'survey_responded_status' => 'Yes'
                        ],
                        [
                            'person_id' => '4878953',
                            'student_id' => '4878953',
                            'first_name' => 'Dexter',
                            'last_name' => 'Andersen',
                            'email' => 'MapworksTestingUser01552453@mailinator.com',
                            'phone_number' => null,
                            'opted_out' => 'No',
                            'survey_status' => 'CompletedAll',
                            'responded_at' => '2015-08-30 10:00:00',
                            'survey_responded_status' => 'Yes'
                        ],
                        [
                            'person_id' => '4879203',
                            'student_id' => '4879203',
                            'first_name' => 'Payton',
                            'last_name' => 'Armstrong',
                            'email' => 'MapworksTestingUser01552703@mailinator.com',
                            'phone_number' => null,
                            'opted_out' => 'No',
                            'survey_status' => 'CompletedAll',
                            'responded_at' => '2015-08-30 10:00:00',
                            'survey_responded_status' => 'Yes'
                        ]
                    ]
                ],

                // For invalid survey id, it will return blank array
                [0, false, false, '', []]
            ]
        ]);
    }


    public function testGetAllAcademicUpdateReportInformationBasedOnCriteria()
    {
        $this->specify("Verify the functionality of the method getAllAcademicUpdateReportInformationBasedOnCriteria", function ($organizationId, $currentAcademicYearId, $studentListFlag, $isCount, $participationFlag, $sortBy, $expectedResults) {

            $limit = " LIMIT 0, 5 ";

            // Added student list filter query based on search attribute
            $studentSelectSQL = " EXISTS( SELECT DISTINCT
                            merged.student_id
                        FROM
                            (SELECT 
                                DISTINCT ofspm.student_id, ofspm.permissionset_id
                            FROM
                                org_faculty_student_permission_map ofspm
                            WHERE
                                ofspm.org_id = $organizationId
                                    AND ofspm.faculty_id = 158797) AS merged
                                INNER JOIN
                            org_permissionset OPS ON OPS.id = merged.permissionset_id
                                AND OPS.deleted_at IS NULL
                                AND OPS.accesslevel_ind_agg = 1
                                AND (OPS.create_view_academic_update = 1
                                or OPS.view_all_academic_update_courses = 1
                                or OPS.view_courses = 1)
                                AND OPS.risk_indicator = 1
                                AND EXISTS( SELECT 
                                    1
                                FROM
                                    org_person_student_year opsy
                                WHERE
                                    opsy.person_id = merged.student_id
                                        AND opsy.org_academic_year_id = $currentAcademicYearId
                                        AND opsy.deleted_at IS NULL)
                        WHERE
                            student_id = p.id)";

            // Added academic update filter criteria
            $academicUpdateFilterCriteriaString = " p.risk_level in ('1' , '2', '3', '4', '6') or p.risk_level is null ";

            $results = $this->reportsRepository->getAllAcademicUpdateReportInformationBasedOnCriteria($organizationId, $currentAcademicYearId, $studentSelectSQL, $academicUpdateFilterCriteriaString, $sortBy, $limit, $studentListFlag, $isCount, $participationFlag);

            verify($results)->equals($expectedResults);

        }, ["examples" =>
            [


                // Student IDs and Names for participants only
                [87, 69, true, false, true, 'student_first_name',
                    [
                        [
                            'student_id' => '4557587',
                            'first_name' => 'Castiel',
                            'last_name' => 'Abbott'
                        ],
                        [
                            'student_id' => '491587',
                            'first_name' => 'Chaim',
                            'last_name' => 'Abbott'
                        ],
                        [
                            'student_id' => '490587',
                            'first_name' => 'Elliott',
                            'last_name' => 'Abbott'
                        ],
                        [
                            'student_id' => '906587',
                            'first_name' => 'Jack',
                            'last_name' => 'Abbott'
                        ],
                        [
                            'student_id' => '4709587',
                            'first_name' => 'Jovanni',
                            'last_name' => 'Abbott'
                        ]
                    ]
                ],


                // Student IDs and Names for participants only
                [87, 69, true, false, true, '-student_last_name',
                    [
                        [
                            'student_id' => '492803',
                            'first_name' => 'Aaron',
                            'last_name' => 'Zuniga'
                        ],
                        [
                            'student_id' => '4558803',
                            'first_name' => 'Blake',
                            'last_name' => 'Zuniga'
                        ],
                        [
                            'student_id' => '4709803',
                            'first_name' => 'Destiny',
                            'last_name' => 'Zuniga'
                        ],
                        [
                            'student_id' => '490803',
                            'first_name' => 'Eli',
                            'last_name' => 'Zuniga'
                        ],
                        [
                            'student_id' => '991803',
                            'first_name' => 'Eloise',
                            'last_name' => 'Zuniga'
                        ]
                    ]
                ],


                // Student IDs and Names for participants only
                [87, 69, true, false, true, 'student_last_name',
                    [
                        [
                            'student_id' => '4557587',
                            'first_name' => 'Castiel',
                            'last_name' => 'Abbott'
                        ],
                        [
                            'student_id' => '491587',
                            'first_name' => 'Chaim',
                            'last_name' => 'Abbott'
                        ],
                        [
                            'student_id' => '490587',
                            'first_name' => 'Elliott',
                            'last_name' => 'Abbott'
                        ],
                        [
                            'student_id' => '906587',
                            'first_name' => 'Jack',
                            'last_name' => 'Abbott'
                        ],
                        [
                            'student_id' => '4709587',
                            'first_name' => 'Jovanni',
                            'last_name' => 'Abbott'
                        ]
                    ]
                ],

                // Count of participant students' academic updates
                [87, 69, false, true, true, 'student_last_name',
                    [
                        [
                            'total_count' => '555177'
                        ]
                    ]
                ],

                // Academic update IDs for participant students
                [87, 69, false, false, true, 'student_last_name',
                    [
                        '2082695',
                        '2082967',
                        '2087524',
                        '2088232',
                        '2105802'
                    ]
                ],

                // Count of academic updates for students, regardless of participation.
                [87, 69, false, true, false, 'student_last_name',
                    [
                        [
                            'total_count' => '555177'
                        ]
                    ]
                ],

                // Students names and IDs regardless of participation status
                [87, 69, true, false, false, 'student_last_name',
                    [
                        [
                            'student_id' => '4557587',
                            'first_name' => 'Castiel',
                            'last_name' => 'Abbott'
                        ],
                        [
                            'student_id' => '491587',
                            'first_name' => 'Chaim',
                            'last_name' => 'Abbott'
                        ],
                        [
                            'student_id' => '490587',
                            'first_name' => 'Elliott',
                            'last_name' => 'Abbott'
                        ],
                        [
                            'student_id' => '906587',
                            'first_name' => 'Jack',
                            'last_name' => 'Abbott'
                        ],
                        [
                            'student_id' => '4709587',
                            'first_name' => 'Jovanni',
                            'last_name' => 'Abbott'
                        ]
                    ]
                ],

                // Academic update IDs for students regardless of participation.
                [87, 69, false, false, false, 'student_last_name',
                    [
                        '2082695',
                        '2082967',
                        '2087524',
                        '2088232',
                        '2105802'
                    ]
                ],
                // Academic update IDs sorting by faculty_last_name desc
                [87, 69, false, false, true, '-faculty_last_name',
                    [
                        2803499,
                        3101885,
                        3168651,
                        3168623,
                        3168598
                    ]
                ],
                // Academic update IDs sorting by faculty_last_name asc

                [87, 69, false, false, true, 'faculty_last_name',
                    [
                        2082695,
                        2082967,
                        2087524,
                        2088232,
                        2105802,
                    ]
                ],
                // Academic update IDs sorting by inprogress grade asc
                [87, 69, false, false, true, 'inprogress_grade',
                    [
                        2082695,
                        2082967,
                        2134559,
                        2134744,
                        2180963,
                    ]
                ],
                // Academic update IDs sorting by inprogress grade desc

                [87, 69, false, false, true, '-inprogress_grade',
                    [
                        2588649,
                        2573423,
                        2586972,
                        2585849,
                        3148578
                    ]
                ],

                // Academic update IDs sorting by failure risk desc
                [87, 69, false, false, true, '-failure_risk',
                    [
                        2082695,
                        2082967,
                        2087524,
                        2088232,
                        2105802
                    ]
                ],
                // Academic update IDs sorting by failure risk asc
                [87, 69, false, false, true, 'failure_risk',
                    [
                        3235119,
                        3168651,
                        3235121,
                        3465238,
                        3235113
                    ]
                ],
                // Academic update IDs sorting by creatd_at asc
                [87, 69, false, false, true, 'created_at',
                    [
                        2072427,
                        2072398,
                        2072354,
                        2072358,
                        2072416
                    ]
                ],
                // Academic update IDs sorting by created_at desc
                [87, 69, false, false, true, '-created_at',
                    [
                        3801918,
                        3801919,
                        3801903,
                        3801911,
                        3801904
                    ]
                ],
                // Academic update IDs sorting by risk_text desc
                [87, 69, false, false, true, '-risk_text',
                    [
                        2077540,
                        2103561,
                        2103632,
                        2111237,
                        2129394
                    ]
                ],

                // Academic update IDs sorting by risk_text asc
                [87, 69, false, false, true, 'risk_text',
                    [
                        2080076,
                        2083267,
                        2083304,
                        2083961,
                        2090887
                    ]
                ],

                // Academic update IDs sorting by absences asc
                [87, 69, false, false, true, 'absences',
                    [
                        2082695,
                        2082967,
                        2087524,
                        2088232,
                        2105802
                    ]
                ],

                // Academic update IDs sorting by absences desc
                [87, 69, false, false, true, '-absences',
                    [
                        3465238,
                        3465229,
                        3465234,
                        3442505,
                        3465222
                    ]

                ],
                // Academic update IDs sorting by course asc
                [87, 69, false, false, false, '+course_name',
                    [
                        3134211,
                        3168673,
                        3744020,
                        2072427,
                        2124183
                    ]
                ],

                // Academic update IDs sorting by course desc
                [87, 69, false, false, false, '-course_name',
                    [
                        3153103,
                        3187844,
                        3780412,
                        2098401,
                        2150135


                    ]

                ]

            ]
        ]);
    }


    public function testGetAllAcademicUpdateReportForListedAcademicUpdateIds()
    {
        $this->specify("Verify the functionality of the method getAllAcademicUpdateReportForListedAcademicUpdateIds", function ($academicUpdateIds, $currentAcademicYearId, $isTimeZoneConversionNeeded, $timeZoneFormatString, $expectedResults) {


            $results = $this->reportsRepository->getAllAcademicUpdateReportForListedAcademicUpdateIds($academicUpdateIds, $currentAcademicYearId, $isTimeZoneConversionNeeded, $timeZoneFormatString);
            verify($results)->equals($expectedResults);


        }, ["examples" =>
            [
                // For all academic update ids
                [
                    [
                        '2094454', '2192755', '2288547', '2371884', '2491247'
                    ],
                    180,
                    false,
                    null,
                    [
                        [
                            "student_id" => "676324",
                            "external_id" => "676324",
                            "student_first_name" => "Emmy",
                            "student_last_name" => "Barker",
                            "email" => "MapworksBetaUser00676324@mailinator.com",
                            "course_id" => "34270",
                            "course_name" => "RSJ403",
                            "faculty_id" => "",
                            "faculty_first_name" => "",
                            "faculty_last_name" => "",
                            "academic_update_id" => "2094454",
                            "created_at" => "2016-02-02 13:16:24",
                            "failure_risk" => "Low",
                            "inprogress_grade" => "A",
                            "absences" => "",
                            "by_request" => "0",
                            "comment" => "",
                            "class_level" => "2",
                            "student_status" => "1",
                            "risk_imagename" => "risk-level-icon-gray.png",
                            "risk_text" => "gray",
                            "term_id" => "153",
                            "term_name" => "Fall 2015",
                            "update_type" => "upload",
                        ],
                        [
                            "student_id" => "676324",
                            "external_id" => "676324",
                            "student_first_name" => "Emmy",
                            "student_last_name" => "Barker",
                            "email" => "MapworksBetaUser00676324@mailinator.com",
                            "course_id" => "34270",
                            "course_name" => "RSJ403",
                            "faculty_id" => "",
                            "faculty_first_name" => "",
                            "faculty_last_name" => "",
                            "academic_update_id" => "2192755",
                            "created_at" => "2016-02-04 06:01:19",
                            "failure_risk" => "Low",
                            "inprogress_grade" => "A",
                            "absences" => "",
                            "by_request" => "0",
                            "comment" => "",
                            "class_level" => "2",
                            "student_status" => "1",
                            "risk_imagename" => "risk-level-icon-gray.png",
                            "risk_text" => "gray",
                            "term_id" => "153",
                            "term_name" => "Fall 2015",
                            "update_type" => "upload",
                        ],
                        [
                            "student_id" => "676324",
                            "external_id" => "676324",
                            "student_first_name" => "Emmy",
                            "student_last_name" => "Barker",
                            "email" => "MapworksBetaUser00676324@mailinator.com",
                            "course_id" => "34270",
                            "course_name" => "RSJ403",
                            "faculty_id" => "",
                            "faculty_first_name" => "",
                            "faculty_last_name" => "",
                            "academic_update_id" => "2288547",
                            "created_at" => "2016-02-06 05:28:36",
                            "failure_risk" => "Low",
                            "inprogress_grade" => "A",
                            "absences" => "",
                            "by_request" => "0",
                            "comment" => "",
                            "class_level" => "2",
                            "student_status" => "1",
                            "risk_imagename" => "risk-level-icon-gray.png",
                            "risk_text" => "gray",
                            "term_id" => "153",
                            "term_name" => "Fall 2015",
                            "update_type" => "upload",
                        ],
                        [
                            "student_id" => "676324",
                            "external_id" => "676324",
                            "student_first_name" => "Emmy",
                            "student_last_name" => "Barker",
                            "email" => "MapworksBetaUser00676324@mailinator.com",
                            "course_id" => "34270",
                            "course_name" => "RSJ403",
                            "faculty_id" => "",
                            "faculty_first_name" => "",
                            "faculty_last_name" => "",
                            "academic_update_id" => "2371884",
                            "created_at" => "2016-02-08 05:33:09",
                            "failure_risk" => "Low",
                            "inprogress_grade" => "A",
                            "absences" => "",
                            "by_request" => "0",
                            "comment" => "",
                            "class_level" => "2",
                            "student_status" => "1",
                            "risk_imagename" => "risk-level-icon-gray.png",
                            "risk_text" => "gray",
                            "term_id" => "153",
                            "term_name" => "Fall 2015",
                            "update_type" => "upload",
                        ],
                        [
                            "student_id" => "676324",
                            "external_id" => "676324",
                            "student_first_name" => "Emmy",
                            "student_last_name" => "Barker",
                            "email" => "MapworksBetaUser00676324@mailinator.com",
                            "course_id" => "34270",
                            "course_name" => "RSJ403",
                            "faculty_id" => "",
                            "faculty_first_name" => "",
                            "faculty_last_name" => "",
                            "academic_update_id" => "2491247",
                            "created_at" => "2016-02-10 05:34:38",
                            "failure_risk" => "Low",
                            "inprogress_grade" => "A",
                            "absences" => "",
                            "by_request" => "0",
                            "comment" => "",
                            "class_level" => "2",
                            "student_status" => "1",
                            "risk_imagename" => "risk-level-icon-gray.png",
                            "risk_text" => "gray",
                            "term_id" => "153",
                            "term_name" => "Fall 2015",
                            "update_type" => "upload",
                        ]
                    ]
                ],
                // For all academic update ids, testing Date Formatting on Created
                [
                    [
                        '2094454', '2192755', '2288547', '2371884', '2491247'
                    ],
                    180,
                    true,
                    'US/Central',
                    [
                        [
                            "student_id" => "676324",
                            "external_id" => "676324",
                            "student_first_name" => "Emmy",
                            "student_last_name" => "Barker",
                            "email" => "MapworksBetaUser00676324@mailinator.com",
                            "course_id" => "34270",
                            "course_name" => "RSJ403",
                            "faculty_id" => "",
                            "faculty_first_name" => "",
                            "faculty_last_name" => "",
                            "academic_update_id" => "2094454",
                            "created_at" => "02/02/2016",
                            "failure_risk" => "Low",
                            "inprogress_grade" => "A",
                            "absences" => "",
                            "by_request" => "0",
                            "comment" => "",
                            "class_level" => "2",
                            "student_status" => "1",
                            "risk_imagename" => "risk-level-icon-gray.png",
                            "risk_text" => "gray",
                            "term_id" => "153",
                            "term_name" => "Fall 2015",
                            "update_type" => "upload",
                        ],
                        [
                            "student_id" => "676324",
                            "external_id" => "676324",
                            "student_first_name" => "Emmy",
                            "student_last_name" => "Barker",
                            "email" => "MapworksBetaUser00676324@mailinator.com",
                            "course_id" => "34270",
                            "course_name" => "RSJ403",
                            "faculty_id" => "",
                            "faculty_first_name" => "",
                            "faculty_last_name" => "",
                            "academic_update_id" => "2192755",
                            "created_at" => "02/04/2016",
                            "failure_risk" => "Low",
                            "inprogress_grade" => "A",
                            "absences" => "",
                            "by_request" => "0",
                            "comment" => "",
                            "class_level" => "2",
                            "student_status" => "1",
                            "risk_imagename" => "risk-level-icon-gray.png",
                            "risk_text" => "gray",
                            "term_id" => "153",
                            "term_name" => "Fall 2015",
                            "update_type" => "upload",
                        ],
                        [
                            "student_id" => "676324",
                            "external_id" => "676324",
                            "student_first_name" => "Emmy",
                            "student_last_name" => "Barker",
                            "email" => "MapworksBetaUser00676324@mailinator.com",
                            "course_id" => "34270",
                            "course_name" => "RSJ403",
                            "faculty_id" => "",
                            "faculty_first_name" => "",
                            "faculty_last_name" => "",
                            "academic_update_id" => "2288547",
                            "created_at" => "02/05/2016",
                            "failure_risk" => "Low",
                            "inprogress_grade" => "A",
                            "absences" => "",
                            "by_request" => "0",
                            "comment" => "",
                            "class_level" => "2",
                            "student_status" => "1",
                            "risk_imagename" => "risk-level-icon-gray.png",
                            "risk_text" => "gray",
                            "term_id" => "153",
                            "term_name" => "Fall 2015",
                            "update_type" => "upload",
                        ],
                        [
                            "student_id" => "676324",
                            "external_id" => "676324",
                            "student_first_name" => "Emmy",
                            "student_last_name" => "Barker",
                            "email" => "MapworksBetaUser00676324@mailinator.com",
                            "course_id" => "34270",
                            "course_name" => "RSJ403",
                            "faculty_id" => "",
                            "faculty_first_name" => "",
                            "faculty_last_name" => "",
                            "academic_update_id" => "2371884",
                            "created_at" => "02/07/2016",
                            "failure_risk" => "Low",
                            "inprogress_grade" => "A",
                            "absences" => "",
                            "by_request" => "0",
                            "comment" => "",
                            "class_level" => "2",
                            "student_status" => "1",
                            "risk_imagename" => "risk-level-icon-gray.png",
                            "risk_text" => "gray",
                            "term_id" => "153",
                            "term_name" => "Fall 2015",
                            "update_type" => "upload",
                        ],
                        [
                            "student_id" => "676324",
                            "external_id" => "676324",
                            "student_first_name" => "Emmy",
                            "student_last_name" => "Barker",
                            "email" => "MapworksBetaUser00676324@mailinator.com",
                            "course_id" => "34270",
                            "course_name" => "RSJ403",
                            "faculty_id" => "",
                            "faculty_first_name" => "",
                            "faculty_last_name" => "",
                            "academic_update_id" => "2491247",
                            "created_at" => "02/09/2016",
                            "failure_risk" => "Low",
                            "inprogress_grade" => "A",
                            "absences" => "",
                            "by_request" => "0",
                            "comment" => "",
                            "class_level" => "2",
                            "student_status" => "1",
                            "risk_imagename" => "risk-level-icon-gray.png",
                            "risk_text" => "gray",
                            "term_id" => "153",
                            "term_name" => "Fall 2015",
                            "update_type" => "upload",
                        ]
                    ]
                ],
                // Test Order MUST be as the incoming AU ids, not anything else
                [
                    [
                        '2192755','2094454','2288547', '2371884', '2491247',
                    ],
                    180,
                    false,
                    null,
                    [

                        [
                            "student_id" => "676324",
                            "external_id" => "676324",
                            "student_first_name" => "Emmy",
                            "student_last_name" => "Barker",
                            "email" => "MapworksBetaUser00676324@mailinator.com",
                            "course_id" => "34270",
                            "course_name" => "RSJ403",
                            "faculty_id" => "",
                            "faculty_first_name" => "",
                            "faculty_last_name" => "",
                            "academic_update_id" => "2192755",
                            "created_at" => "2016-02-04 06:01:19",
                            "failure_risk" => "Low",
                            "inprogress_grade" => "A",
                            "absences" => "",
                            "by_request" => "0",
                            "comment" => "",
                            "class_level" => "2",
                            "student_status" => "1",
                            "risk_imagename" => "risk-level-icon-gray.png",
                            "risk_text" => "gray",
                            "term_id" => "153",
                            "term_name" => "Fall 2015",
                            "update_type" => "upload",
                        ],
                        [
                            "student_id" => "676324",
                            "external_id" => "676324",
                            "student_first_name" => "Emmy",
                            "student_last_name" => "Barker",
                            "email" => "MapworksBetaUser00676324@mailinator.com",
                            "course_id" => "34270",
                            "course_name" => "RSJ403",
                            "faculty_id" => "",
                            "faculty_first_name" => "",
                            "faculty_last_name" => "",
                            "academic_update_id" => "2094454",
                            "created_at" => "2016-02-02 13:16:24",
                            "failure_risk" => "Low",
                            "inprogress_grade" => "A",
                            "absences" => "",
                            "by_request" => "0",
                            "comment" => "",
                            "class_level" => "2",
                            "student_status" => "1",
                            "risk_imagename" => "risk-level-icon-gray.png",
                            "risk_text" => "gray",
                            "term_id" => "153",
                            "term_name" => "Fall 2015",
                            "update_type" => "upload",
                        ],
                        [
                            "student_id" => "676324",
                            "external_id" => "676324",
                            "student_first_name" => "Emmy",
                            "student_last_name" => "Barker",
                            "email" => "MapworksBetaUser00676324@mailinator.com",
                            "course_id" => "34270",
                            "course_name" => "RSJ403",
                            "faculty_id" => "",
                            "faculty_first_name" => "",
                            "faculty_last_name" => "",
                            "academic_update_id" => "2288547",
                            "created_at" => "2016-02-06 05:28:36",
                            "failure_risk" => "Low",
                            "inprogress_grade" => "A",
                            "absences" => "",
                            "by_request" => "0",
                            "comment" => "",
                            "class_level" => "2",
                            "student_status" => "1",
                            "risk_imagename" => "risk-level-icon-gray.png",
                            "risk_text" => "gray",
                            "term_id" => "153",
                            "term_name" => "Fall 2015",
                            "update_type" => "upload",
                        ],
                        [
                            "student_id" => "676324",
                            "external_id" => "676324",
                            "student_first_name" => "Emmy",
                            "student_last_name" => "Barker",
                            "email" => "MapworksBetaUser00676324@mailinator.com",
                            "course_id" => "34270",
                            "course_name" => "RSJ403",
                            "faculty_id" => "",
                            "faculty_first_name" => "",
                            "faculty_last_name" => "",
                            "academic_update_id" => "2371884",
                            "created_at" => "2016-02-08 05:33:09",
                            "failure_risk" => "Low",
                            "inprogress_grade" => "A",
                            "absences" => "",
                            "by_request" => "0",
                            "comment" => "",
                            "class_level" => "2",
                            "student_status" => "1",
                            "risk_imagename" => "risk-level-icon-gray.png",
                            "risk_text" => "gray",
                            "term_id" => "153",
                            "term_name" => "Fall 2015",
                            "update_type" => "upload",
                        ],
                        [
                            "student_id" => "676324",
                            "external_id" => "676324",
                            "student_first_name" => "Emmy",
                            "student_last_name" => "Barker",
                            "email" => "MapworksBetaUser00676324@mailinator.com",
                            "course_id" => "34270",
                            "course_name" => "RSJ403",
                            "faculty_id" => "",
                            "faculty_first_name" => "",
                            "faculty_last_name" => "",
                            "academic_update_id" => "2491247",
                            "created_at" => "2016-02-10 05:34:38",
                            "failure_risk" => "Low",
                            "inprogress_grade" => "A",
                            "absences" => "",
                            "by_request" => "0",
                            "comment" => "",
                            "class_level" => "2",
                            "student_status" => "1",
                            "risk_imagename" => "risk-level-icon-gray.png",
                            "risk_text" => "gray",
                            "term_id" => "153",
                            "term_name" => "Fall 2015",
                            "update_type" => "upload",
                        ]
                    ]
                ]
            ]
        ]);
    }


}