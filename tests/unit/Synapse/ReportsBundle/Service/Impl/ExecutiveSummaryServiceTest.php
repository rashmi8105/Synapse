<?php
namespace Synapse\ReportsBundle\Service\Impl;

use Codeception\Specify;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Repository\EbiMetadataRepository;
use Synapse\CoreBundle\Repository\OrgCohortNameRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\SearchRepository;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionRepository;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;
use Synapse\RiskBundle\Repository\PersonRiskLevelHistoryRepository;
use Synapse\RiskBundle\Repository\RiskLevelsRepository;
use Synapse\SearchBundle\Repository\IntentToLeaveRepository;
use Synapse\SurveyBundle\Repository\PersonFactorCalculatedRepository;
use Synapse\SurveyBundle\Repository\WessLinkRepository;

class ExecutiveSummaryServiceTest extends \PHPUnit_Framework_TestCase
{
    //ReUsed Constructor Mocks
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRepositoryResolver;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockResque;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLogger;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContainer;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockSerializer;


    use Specify;

    public $sections = array(
        "reportId" => "16",
        "reportDisable" => false,
        "report_name" => "Executive Summary Report",
        "short_code" => "EXEC",
        "reportDesc" => "Provide reporting of actionable data for Skyfactor admins, campus coordinators and faculty/staff.",
        "setupPage" => "reports/executive-summary-report",
        "reportFilterPages" =>
            [
                [
                    "reportPage" => "filterAttributes",
                    "title" => "Filter options"
                ]
            ],
        "reportSetupPages" =>
            [
                [
                    "reportPage" => "ExecutivePage1",
                    "title" => "Scren 1"
                ],
                [
                    "reportPage" => "ExecutivePage2",
                    "title" => "scren 2"
                ]
            ],
        "reportFilter" =>
            [
                "participating" => false,
                "risk" => true,
                "active" => true,
                "retentionCompletion" => false,
                "activities" => false,
                "group" => true,
                "course" => false,
                "ebi" => true,
                "isp" => true,
                "static" => true,
                "factor" => false,
                "survey" => false,
                "isq" => false,
                "surveyMetadata" => false,
                "academicTerm" => false,
                "cohort" => false,
                "team" => false,
                "academicYears" => false
            ],
        "RequestParam" => "",
        "pageurl" => "/reports/executive-summary-report/",
        "id" => "16",
        "report_description" => "See key statistics on effectiveness: persistence/retention, GPA, activity, and more. Print to pdf.",
        "is_batch_job" => true,
        "is_coordinator_report" => "y",
        "report_id" => "16",
        "cohorts" =>
            [
                [
                    "year_id" => 201516,
                    "org_academic_year_id" => 36,
                    "cohort" => 1,
                    "cohort_name" => "Survey Cohort 1",
                    "surveys" =>
                        [
                            11,
                            14
                        ],
                    "is_selected" => true
                ]
            ],
        "sections" =>
            [
                [
                    "section_id" => "8",
                    "section_name" => "What is Mapworks?",
                    "sequence" => "1",
                    "retention_tracking_type" => "none",
                    "survey_contingent" => false,
                    "academic_term_contingent" => false,
                    "risk_contingent" => false,
                    "elements" =>
                        [
                            [
                                "element_id" => 112,
                                "element_name" => "Purpose",
                                "is_included" => true,
                                "element_text" => "Mapworks is a holistic approach to student success and retention, providing a platform of information that faculty and staff use to identify at-risk students early in the term. It also allows faculty and staff the ability to coordinate interventions with at-risk students by providing the power of real-time analytics, strategic communications, and differentiated user interfacing, with integrated statistical testing and outcomes reporting."
                            ],
                            [
                                "element_id" => 113,
                                "element_name" => "Rationale",
                                "is_included" => true,
                                "element_text" => ""
                            ],
                            [
                                "element_id" => 114,
                                "element_name" => "Process",
                                "is_included" => true,
                                "element_text" => "The Mapworks process includes combining data from the institution with information from the students. Using that information, Mapworks uses real-time analytics to provide information directly to the students as well as to the faculty and staff working with the students."
                            ],
                            [
                                "element_id" => 115,
                                "element_name" => "Graphic",
                                "is_included" => true
                            ]
                        ],
                    "is_included" => true
                ],
                [
                    "section_id" => "9",
                    "section_name" => "Risk Profile",
                    "sequence" => "2",
                    "retention_tracking_type" => "optional",
                    "survey_contingent" => false,
                    "academic_term_contingent" => false,
                    "risk_contingent" => true,
                    "is_included" => true,
                    "apply_retention_track" => true
                ],
                [
                    "section_id" => "10",
                    "section_name" => "GPA by Risk",
                    "sequence" => "3",
                    "retention_tracking_type" => "optional",
                    "survey_contingent" => false,
                    "academic_term_contingent" => true,
                    "risk_contingent" => true,
                    "is_included" => true,
                    "apply_retention_track" => true
                ],
                [
                    "section_id" => "11",
                    "section_name" => "Intent to Leave and Persistence",
                    "sequence" => "4",
                    "retention_tracking_type" => "required",
                    "survey_contingent" => true,
                    "academic_term_contingent" => false,
                    "risk_contingent" => false,
                    "is_included" => true,
                    "apply_retention_track" => false
                ],
                [
                    "section_id" => "12",
                    "section_name" => "Persistence and Retention by Risk",
                    "sequence" => "5",
                    "retention_tracking_type" => "required",
                    "survey_contingent" => false,
                    "academic_term_contingent" => false,
                    "risk_contingent" => true,
                    "is_included" => true,
                    "apply_retention_track" => false
                ],
                [
                    "section_id" => "13",
                    "section_name" => "Top Factors with Correlation to Persistence and Retention",
                    "sequence" => "6",
                    "retention_tracking_type" => "required",
                    "survey_contingent" => true,
                    "academic_term_contingent" => false,
                    "risk_contingent" => false,
                    "is_included" => true,
                    "apply_retention_track" => false
                ],
                [
                    "section_id" => "14",
                    "section_name" => "Top Factors with Correlation to GPA",
                    "sequence" => "7",
                    "retention_tracking_type" => "optional",
                    "survey_contingent" => true,
                    "academic_term_contingent" => true,
                    "risk_contingent" => false,
                    "is_included" => true,
                    "apply_retention_track" => true
                ],
                [
                    "section_id" => "15",
                    "section_name" => "Activity Overview",
                    "sequence" => "8",
                    "retention_tracking_type" => "optional",
                    "survey_contingent" => false,
                    "academic_term_contingent" => false,
                    "risk_contingent" => false,
                    "elements" =>
                        [
                            [
                                "element_id" => 116,
                                "element_name" => "Referrals",
                                "is_included" => true
                            ],
                            [
                                "element_id" => 117,
                                "element_name" => "Appointments",
                                "is_included" => true
                            ],
                            [
                                "element_id" => 118,
                                "element_name" => "Contacts",
                                "is_included" => true
                            ],
                            [
                                "element_id" => 119,
                                "element_name" => "Interaction Contacts",
                                "is_included" => true
                            ],
                            [
                                "element_id" => 120,
                                "element_name" => "Notes",
                                "is_included" => true
                            ],
                            [
                                "element_id" => 121,
                                "element_name" => "Academic Updates",
                                "is_included" => true
                            ]
                        ],
                    "is_included" => true,
                    "apply_retention_track" => true
                ]
            ],
        "campus_info" =>
            [
                "organization_id" => 14,
                "primary_color" => "#f2de29",
                "secondary_color" => "#3d4147",
                "inactivity_timeout" => "60",
                "refer_for_academic_assistance" => true,
                "send_to_student" => true,
                "can_view_in_progress_grade" => false,
                "can_view_absences" => false,
                "can_view_comments" => false,
                "calendar_type" => "google",
                "calendar_sync" => true,
                "calendar_sync_users" => 3,
                "campus_name" => "niteshnegitest",
                "campus_logo" => "images/default-mw-header-logo.png"
            ]
    );

    public $filter = array(
        "risk" => false,
        "active" => false,
        "activities" => false,
        "group" => false,
        "course" => false,
        "ebi" => false,
        "isp" => false,
        "static" => false,
        "factor" => false,
        "survey" => false,
        "isq" => false,
        "surveyMetadata" => false,
        "academicTerm" => false,
        "cohort" => false,
        "team" => false,
        "academicYears" => false,
    );

    public $rawData = [
        "organization_id" => "14",
        "person_id" => "250474",
        "search_attributes" =>
            [
                "filterCount" => 3,
                "org_academic_year_id" => 36,
                "org_academic_year" =>
                    [
                        "year" =>
                            [
                                [
                                    "id" => 36,
                                    "name" => "2015-2016",
                                    "year_id" => "201516",
                                    "start_date" => "2015-08-12",
                                    "end_date" => "2017-05-12",
                                    "can_delete" => false,
                                    "is_current_year" => true,
                                    "academic_terms" =>
                                        [
                                            [
                                                "name" => "Fall 2015",
                                                "term_id" => 73,
                                                "term_code" => "201540",
                                                "start_date" => "2015-08-12",
                                                "end_date" => "2015-12-11",
                                                "can_delete" => false,
                                                "current_academic_term_flag" => false,
                                                "is_selected" => false
                                            ],
                                            [
                                                "name" => "Spring 2016",
                                                "term_id" => 74,
                                                "term_code" => "201610",
                                                "start_date" => "2016-01-11",
                                                "end_date" => "2017-05-12",
                                                "can_delete" => false,
                                                "current_academic_term_flag" => true,
                                                "is_selected" => true
                                            ]
                                        ],
                                    "is_selected" => true,
                                    "selectedTerms" => [
                                        [
                                                "name" => "Spring 2016",
                                                "term_id" => 74,
                                                "term_code" => "201610",
                                                "start_date" => "2016-01-11",
                                                "end_date" => "2017-05-12",
                                                "can_delete" => false,
                                                "current_academic_term_flag" => true,
                                                "is_selected" => true
                                        ]
                                    ]
                                ]
                            ],
                        "terms" =>
                            [
                                [
                                    "name" => "Spring 2016",
                                    "term_id" => 74,
                                    "term_code" => "201610",
                                    "start_date" => "2016-01-11",
                                    "end_date" => "2017-05-12",
                                    "can_delete" => false,
                                    "current_academic_term_flag" => true,
                                    "is_selected" => true
                                ]
                            ]
                    ],
                "risk_indicator_date" => "",
                "risk_indicator_ids" => "",
                "student_status" =>
                    [
                        "status_value" => [],
                        "org_academic_year_id" => []
                    ],
                "group_ids" => "",
                "group_names" => [],
                "datablocks" => [],
                "isps" => [],
                "static_list_ids" => "",
                "static_list_names" => [],
                "retention_date" =>
                    [
                        "academic_year_id" => 36,
                        "academic_year_name" => "2015-2016"
                    ],
                "risk_start_date" => "2016-03-26",
                "risk_end_date" => "2017-03-08",
                "org_academic_terms_id" =>
                    [
                        74
                    ],
                "cohortSurvey" =>
                    [
                        [
                            "subList" =>
                                [
                                    [
                                        "name" => "Transition One"
                                    ],
                                    [
                                        "name" => "Check-Up Two"
                                    ]
                                ],
                                "name" => "Survey Cohort 1"
                        ]
                    ],
                "filterText" => "Retention Tracking Group : 2015-2016, Risk Window : 03/26/2016 To 03/08/2017, Academic Year > 2015-2016 > Spring 2016, Survey Cohort > Survey Cohort 1 > Transition One / Check-Up Two"
            ],
        "report_sections" =>
            [
                "reportId" => "16",
                "reportDisable" => false,
                "report_name" => "Executive Summary Report",
                "short_code" => "EXEC",
                "reportDesc" => "Provide reporting of actionable data for Skyfactor admins, campus coordinators and faculty/staff.",
                "setupPage" => "reports/executive-summary-report",
                "reportFilterPages" =>
                    [
                        [
                            "reportPage" => "filterAttributes",
                            "title" => "Filter options"
                        ]
                    ],
                "reportSetupPages" =>
                    [
                        [
                            "reportPage" => "ExecutivePage1",
                            "title" => "Scren 1"
                        ],
                        [
                            "reportPage" => "ExecutivePage2",
                            "title" => "scren 2"
                        ]
                    ],
                "reportFilter" =>
                    [
                        "participating" => false,
                        "risk" => true,
                        "active" => true,
                        "retentionCompletion" => false,
                        "activities" => false,
                        "group" => true,
                        "course" => false,
                        "ebi" => true,
                        "isp" => true,
                        "static" => true,
                        "factor" => false,
                        "survey" => false,
                        "isq" => false,
                        "surveyMetadata" => false,
                        "academicTerm" => false,
                        "cohort" => false,
                        "team" => false,
                        "academicYears" => false
                    ],
                "RequestParam" => "",
                "pageurl" => "/reports/executive-summary-report/",
                "id" => "16",
                "report_description" => "See key statistics on effectiveness: persistence/retention, GPA, activity, and more. Print to pdf.",
                "is_batch_job" => true,
                "is_coordinator_report" => "y",
                "report_id" => "16",
                "cohorts" =>
                    [
                        [
                            "year_id" => 201516,
                            "org_academic_year_id" => 36,
                            "cohort" => 1,
                            "cohort_name" => "Survey Cohort 1",
                            "surveys" =>
                                [
                                    11,
                                    14
                                ],
                            "is_selected" => true
                        ]
                    ],
                "sections" =>
                    [
                        [
                            "section_id" => "8",
                            "section_name" => "What is Mapworks?",
                            "sequence" => "1",
                            "retention_tracking_type" => "none",
                            "survey_contingent" => false,
                            "academic_term_contingent" => false,
                            "risk_contingent" => false,
                            "elements" =>
                                [
                                    [
                                        "element_id" => 112,
                                        "element_name" => "Purpose",
                                        "is_included" => true,
                                        "element_text" => "Mapworks is a holistic approach to student success and retention, providing a platform of information that faculty and staff use to identify at-risk students early in the term. It also allows faculty and staff the ability to coordinate interventions with at-risk students by providing the power of real-time analytics, strategic communications, and differentiated user interfacing, with integrated statistical testing and outcomes reporting."
                                    ],
                                    [
                                        "element_id" => 113,
                                        "element_name" => "Rationale",
                                        "is_included" => true,
                                        "element_text" => ""
                                    ],
                                    [
                                        "element_id" => 114,
                                        "element_name" => "Process",
                                        "is_included" => true,
                                        "element_text" => "The Mapworks process includes combining data from the institution with information from the students. Using that information, Mapworks uses real-time analytics to provide information directly to the students as well as to the faculty and staff working with the students."
                                    ],
                                    [
                                        "element_id" => 115,
                                        "element_name" => "Graphic",
                                        "is_included" => true
                                    ]
                                ],
                            "is_included" => true
                        ],
                        [
                            "section_id" => "9",
                            "section_name" => "Risk Profile",
                            "sequence" => "2",
                            "retention_tracking_type" => "optional",
                            "survey_contingent" => false,
                            "academic_term_contingent" => false,
                            "risk_contingent" => true,
                            "is_included" => true,
                            "apply_retention_track" => true
                        ],
                        [
                            "section_id" => "10",
                            "section_name" => "GPA by Risk",
                            "sequence" => "3",
                            "retention_tracking_type" => "optional",
                            "survey_contingent" => false,
                            "academic_term_contingent" => true,
                            "risk_contingent" => true,
                            "is_included" => true,
                            "apply_retention_track" => true
                        ],
                        [
                            "section_id" => "11",
                            "section_name" => "Intent to Leave and Persistence",
                            "sequence" => "4",
                            "retention_tracking_type" => "required",
                            "survey_contingent" => true,
                            "academic_term_contingent" => false,
                            "risk_contingent" => false,
                            "is_included" => true,
                            "apply_retention_track" => false
                        ],
                        [
                            "section_id" => "12",
                            "section_name" => "Persistence and Retention by Risk",
                            "sequence" => "5",
                            "retention_tracking_type" => "required",
                            "survey_contingent" => false,
                            "academic_term_contingent" => false,
                            "risk_contingent" => true,
                            "is_included" => true,
                            "apply_retention_track" => false
                        ],
                        [
                            "section_id" => "13",
                            "section_name" => "Top Factors with Correlation to Persistence and Retention",
                            "sequence" => "6",
                            "retention_tracking_type" => "required",
                            "survey_contingent" => true,
                            "academic_term_contingent" => false,
                            "risk_contingent" => false,
                            "is_included" => true,
                            "apply_retention_track" => false
                        ],
                        [
                            "section_id" => "14",
                            "section_name" => "Top Factors with Correlation to GPA",
                            "sequence" => "7",
                            "retention_tracking_type" => "optional",
                            "survey_contingent" => true,
                            "academic_term_contingent" => true,
                            "risk_contingent" => false,
                            "is_included" => true,
                            "apply_retention_track" => true
                        ],
                        [
                            "section_id" => "15",
                            "section_name" => "Activity Overview",
                            "sequence" => "8",
                            "retention_tracking_type" => "optional",
                            "survey_contingent" => false,
                            "academic_term_contingent" => false,
                            "risk_contingent" => false,
                            "elements" =>
                                [
                                    [
                                        "element_id" => 116,
                                        "element_name" => "Referrals",
                                        "is_included" => true
                                    ],
                                    [
                                        "element_id" => 117,
                                        "element_name" => "Appointments",
                                        "is_included" => true
                                    ],
                                    [
                                        "element_id" => 118,
                                        "element_name" => "Contacts",
                                        "is_included" => true
                                    ],
                                    [
                                        "element_id" => 119,
                                        "element_name" => "Interaction Contacts",
                                        "is_included" => true
                                    ],
                                    [
                                        "element_id" => 120,
                                        "element_name" => "Notes",
                                        "is_included" => true
                                    ],
                                    [
                                        "element_id" => 121,
                                        "element_name" => "Academic Updates",
                                        "is_included" => true
                                    ]
                                ],
                            "is_included" => true,
                            "apply_retention_track" => true
                        ]
                    ],
                "campus_info" =>
                    [
                        "organization_id" => 14,
                        "primary_color" => "#f2de29",
                        "secondary_color" => "#3d4147",
                        "inactivity_timeout" => "60",
                        "refer_for_academic_assistance" => true,
                        "send_to_student" => true,
                        "can_view_in_progress_grade" => false,
                        "can_view_absences" => false,
                        "can_view_comments" => false,
                        "calendar_type" => "google",
                        "calendar_sync" => true,
                        "calendar_sync_users" => 3,
                        "campus_name" => "niteshnegitest",
                        "campus_logo" => "images/default-mw-header-logo.png"
                    ]
            ],
        "report_id" => "16"
    ];


    protected function _before(){

        $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
        $this->mockResque = $this->getMock('resque', array('enqueue'));
        $this->mockLogger = $this->getMock('Logger', array('debug','error'));
        $this->mockContainer = $this->getMock('Container', array('get'));
        $this->mockSerializer = $this->getMock('serializer', array('serialize'));
    }

    public function executePrivateMethod(&$object, $methodName, $parameters)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    private function buildReportRunningDto($orgIdStub, $filter, $sections = ['reportId' => 16])
    {
        $reportRunningStatus = new ReportRunningStatusDto();
        $reportRunningStatus->setId(1);
        $reportRunningStatus->setOrganizationId($orgIdStub);
        $reportRunningStatus->setReportId(16);
        $reportRunningStatus->setPersonId(113802);
        $reportRunningStatus->setSearchAttributes($filter);
        $reportRunningStatus->setReportSections($sections);
        $reportRunningStatus->setCreatedAt("2016-02-01 00:00:00");

        return $reportRunningStatus;
    }

    // tests


    public function testGenerateReport()
    {
        $this->specify('test generate report', function($reportRunningStatusDto, $mockFilteredStudentList, $mockCurrentYearData, $expectedResult){

            $mockOrganizationId = 99;
            $mockOrgAcademicYearId = 36;
            $mockCohortName = "Survey Cohort 1";
            $mockRetentionTrackStudentIds = [12345679, 12345680, 12345681, 12345682];
            $mockRiskLevelSet = [
                ['total_students' => 1, 'risk_level' => 1],
                ['total_students' => 1, 'risk_level' => 2],
                ['total_students' => 1, 'risk_level' => 3],
                ['total_students' => 1, 'risk_level' => 4],
                ['total_students' => 1, 'risk_level' => 6]
            ];
            $mockFacultySet = [
                ['faculty_id' => 112233],
                ['faculty_id' => 112234],
                ['faculty_id' => 112235]
            ];
            $mockGPAId = 83;
            $mockGPAReport = [
                'total_students' => 10,
                'org_id' => 99,
                'gpa_term_summaries_by_year' => [0 => ['gpa_summary_by_term' => ['dummyValue' => 1]]],
            ];

            $mockMeaningfulRetentionVariables =
                [

                    0 =>
                        [
                            1 => "Retained to Midyear Year 1",
                        ],
                    1 =>
                        [
                            0 => "Retained to Start of Year 2",
                            1 => "Retained to Midyear Year 2",
                        ],
                    2 =>
                        [
                            0 => "Retained to Start of Year 3",
                            1 => "Retained to Midyear Year 3",
                        ],
                    3 =>
                        [
                            0 => "Retained to Start of Year 4",
                            1 => "Retained to Midyear Year 4",
                        ],
                ];

            $mockSurveyData =
                [
                    11 =>
                        [
                            "survey_name" => "Transition One",
                            "included_in_persist_midyear_reporting" => 1,
                        ],
                    14 =>
                        [
                            "survey_name" => "Checkup Two",
                            "included_in_persist_midyear_reporting" => 1,
                        ],
                ];

            $mockStudentsIntendingToLeave =
                [
                    12345679
                ];

            $mockRetentionData =
                [
                    [
                        "years_from_retention_track" => 0,
                        "beginning_year_numerator_count" => 0,
                        "midyear_numerator_count" => 1,
                        "denominator_count" => 1,
                    ],
                    [
                        "years_from_retention_track" => 1,
                        "beginning_year_numerator_count" => 1,
                        "midyear_numerator_count" => 1,
                        "denominator_count" => 1,
                    ],
                ];

            if (empty($mockCurrentYearData)) {
                $mockFormatRetentionDataset = null;
            } else {
                $mockFormatRetentionDataset = [
                    0 => [
                        "column_title" => "Retained to Midyear Year 1",
                        "total_students_retained" => 11,
                        "total_student_count" => 11,
                        "percent" => 100,
                        "persistence_retention_by_risk" =>
                            [
                                0 => [
                                    "risk_color" => "green",
                                    "students_retained" => 2,
                                    "student_count" => 2,
                                    "percent" => 100,
                                ],
                                1 => [
                                    "risk_color" => "yellow",
                                    "students_retained" => 2,
                                    "student_count" => 2,
                                    "percent" => 100,
                                ],
                                2 => [
                                    "risk_color" => "red",
                                    "students_retained" => 5,
                                    "student_count" => 5,
                                    "percent" => 100,
                                ],
                                3 => [
                                    "risk_color" => "red2",
                                    "students_retained" => 2,
                                    "student_count" => 2,
                                    "percent" => 100,
                                ],
                            ],
                    ],
                    1 => [
                        "column_title" => "Retained to Start of Year 2",
                        "total_students_retained" => 11,
                        "total_student_count" => 11,
                        "percent" => 100,
                        "persistence_retention_by_risk" =>
                            [
                                0 => [
                                    "risk_color" => "green",
                                    "students_retained" => 2,
                                    "student_count" => 2,
                                    "percent" => 100,
                                ],
                                1 => [
                                    "risk_color" => "yellow",
                                    "students_retained" => 2,
                                    "student_count" => 2,
                                    "percent" => 100,
                                ],
                                2 => [
                                    "risk_color" => "red",
                                    "students_retained" => 5,
                                    "student_count" => 5,
                                    "percent" => 100,
                                ],
                                3 => [
                                    "risk_color" => "red2",
                                    "students_retained" => 2,
                                    "student_count" => 2,
                                    "percent" => 100,
                                ],
                            ],
                    ],
                    2 => [
                        "column_title" => "Retained to Midyear Year 2",
                        "total_students_retained" => 11,
                        "total_student_count" => 11,
                        "percent" => 100,
                        "persistence_retention_by_risk" =>
                            [
                                0 => [
                                    "risk_color" => "green",
                                    "students_retained" => 2,
                                    "student_count" => 2,
                                    "percent" => 100,
                                ],
                                1 => [
                                    "risk_color" => "yellow",
                                    "students_retained" => 2,
                                    "student_count" => 2,
                                    "percent" => 100,
                                ],
                                2 => [
                                    "risk_color" => "red",
                                    "students_retained" => 5,
                                    "student_count" => 5,
                                    "percent" => 100,
                                ],
                                3 => [
                                    "risk_color" => "red2",
                                    "students_retained" => 2,
                                    "student_count" => 2,
                                    "percent" => 100,
                                ],

                            ],
                    ],
                ];
            }
            $mockTopFactorsWithCorrelations = [
                [
                    'factor_id' => 2,
                    'name' => 'Self-Assessment: Communication Skills',
                    'correlation' => 0.07069478195130362,
                ],
                [

                    'factor_id' => 13,
                    'name' => 'Homesickness: Distressed',
                    'correlation' => 0.06950673589461748,
                ],

                [
                    'factor_id' => 12,
                    'name' => 'Homesickness: Separation',
                    'correlation' => 0.0675769425463927,
                ],
                [
                    'factor_id' => 20,
                    'name' => 'Off-Campus Living: Environment ',
                    'correlation' => 0.05857183766704982,
                ],
                [
                    'factor_id' => 7,
                    'name' => 'Basic Academic Behaviors',
                    'correlation' => 0.05309303150458174,
                ],
            ];

            $mockEbiMetadataRecords =
                [
                    0 =>
                        [
                            'name' => 'entry1'
                        ],
                    1 =>
                        [
                            'name' => 'entry2'
                        ],
                    2 =>
                        [
                            'name' => 'entry3'
                        ]
                ];

            //Create all mocks necessary for Service class creation
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            //Repositories that will be mocked away
            $mockReportsRunningStatusRepository = $this->getMock('ReportsRunningStatusRepository', array('find','flush'));
            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', array('find'));
            $mockOrgAcademicTermsRepository = $this->getMock('OrgAcademicTermsRepository', array('find'));
            $mockOrgPersonStudentRetentionTrackingGroupRepository = $this->getMock('OrgPersonStudentRetentionTrackingGroupRepository', array('find','areStudentsAssignedToThisRetentionTrackingYear','getRetentionTrackingGroupStudents'));
            $mockPersonRepository = $this->getMock('PersonRepository', array('find'));
            $mockReportsRepository = $this->getMock('ReportsRepository', array('find'));
            $mockWessLinkRepository = $this->getMock('WessLinkRepository', array('getSurveysAndNamesForOrganization'));
            $mockPersonRiskLevelHistoryRepository = $this->getMock('PersonRiskLevelHistoryRepository', array('getRiskLevelHistoryByDateRange'));
            $mockRiskLevelsRepository = $this->getMock('RiskLevelsRepository', array('findOneBy','find'));
            $mockEbiMetadataRepository = $this->getMock('ebiMetadataRepository', array('findOneBy'));
            $mockOrgCohortNameRepository = $this->getMock('OrgCohortNameRepository', array('findOneBy'));
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', array('getAllFacultiesForOrg'));
            $mockIntentToLeaveRepository = $this->getMock('IntentToLeaveRepository', array('getStudentsWhoIntendToLeave'));
            $mockOrgPersonStudentRetentionRepository = $this->getMock('OrgPersonStudentRetentionRepository', array('getAggregatedCountsOfRetentionDataByStudentList'));
            $mockPersonFactorCalculatedRepository = $this->getMock('PersonFactorCalculatedRepository', array('getTopFactorsCorrelatedToPersistence','getTopFactorsCorrelatedWithEbiMetadata'));
            $mockSearchRepository = $this->getMock('SearchRepository', array('getQueryResult'));


            //Services that will be mocked away
            $mockAcademicYearService = $this->getMock('AcademicYearService', array('determinePastCurrentOrFutureYearForOrganization', 'findCurrentAcademicYearForOrganization'));
            $mockActivityReportService = $this->getMock('activityReportService', ['getActivityReportSectionQueries', 'replacePlaceHoldersInQuery']);
            $mockDateUtilityService = $this->getMock('DateUtilityService', array('convertToUtcDatetime'));
            $mockPersistenceRetentionService = $this->getMock('PersistenceRetentionService', array('getMeaningfulRetentionVariables','formatRetentionDataset'));
            $mockAlertNotificationsService = $this->getMock('AlertNotificationsService', array('createReportNotification'));
            $mockGPAReportService = $this->getMock('GPAReportService', array('getEndTermGpaId','buildReportItems'));
            $mockDataProcessingUtilityService = $this->getMock('DataProcessingUtilityService', array('recursiveRemovalByArrayKey'));
            $mockSerializer = $this->getMock('Serializer', array('serialize'));


            //Objects that will be mocked away
            $mockReportRunningStatusObj = $this->getMock('ReportRunningStatusObj',array('getFilteredStudentIds', 'setStatus', 'setResponseJson', 'getCreatedAt'));
            $mockDateObj = $this->getMock('date', array('getTimestamp', 'format'));

            //mocking away all function calls outside of the tested function
            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [OrgAcademicYearRepository::REPOSITORY_KEY, $mockOrgAcademicYearRepository],
                    [OrgAcademicTermRepository::REPOSITORY_KEY, $mockOrgAcademicTermsRepository],
                    [OrgPersonStudentRetentionTrackingGroupRepository::REPOSITORY_KEY, $mockOrgPersonStudentRetentionTrackingGroupRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [ReportsRunningStatusRepository::REPOSITORY_KEY, $mockReportsRunningStatusRepository],
                    [ReportsRepository::REPOSITORY_KEY, $mockReportsRepository],
                    [WessLinkRepository::REPOSITORY_KEY, $mockWessLinkRepository],
                    [PersonRiskLevelHistoryRepository::REPOSITORY_KEY, $mockPersonRiskLevelHistoryRepository],
                    [EbiMetadataRepository::REPOSITORY_KEY, $mockEbiMetadataRepository],
                    [OrgCohortNameRepository::REPOSITORY_KEY, $mockOrgCohortNameRepository],
                    [OrgPersonFacultyRepository::REPOSITORY_KEY, $mockOrgPersonFacultyRepository],
                    [SearchRepository::REPOSITORY_KEY, $mockSearchRepository],
                    [IntentToLeaveRepository::REPOSITORY_KEY, $mockIntentToLeaveRepository],
                    [OrgPersonStudentRetentionRepository::REPOSITORY_KEY, $mockOrgPersonStudentRetentionRepository],
                    [PersonFactorCalculatedRepository::REPOSITORY_KEY, $mockPersonFactorCalculatedRepository],
                    [RiskLevelsRepository::REPOSITORY_KEY, $mockRiskLevelsRepository]
                ]
            );

            $mockContainer->method('get')->willReturnMap(
                [
                    [AcademicYearService::SERVICE_KEY, $mockAcademicYearService],
                    [ActivityReportService::SERVICE_KEY, $mockActivityReportService],
                    [AlertNotificationsService::SERVICE_KEY, $mockAlertNotificationsService],
                    [DateUtilityService::SERVICE_KEY, $mockDateUtilityService],
                    [PersistenceRetentionService::SERVICE_KEY, $mockPersistenceRetentionService],
                    [GPAReportService::SERVICE_KEY, $mockGPAReportService],
                    [DataProcessingUtilityService::SERVICE_KEY, $mockDataProcessingUtilityService],
                    [SynapseConstant::JMS_SERIALIZER_CLASS_KEY, $mockSerializer]
                ]
            );

            $mockReportsRunningStatusRepository->method('find')->willReturn($mockReportRunningStatusObj);
            $mockReportRunningStatusObj->method('getFilteredStudentIds')->willReturn($mockFilteredStudentList);
            $mockReportRunningStatusObj->method('setStatus')->willReturn('IP');

            $mockOrgAcademicYear = $this->getMock('OrgAcademicYear',array('getYearId', 'getOrganization','getId', 'getStartDate', 'getEndDate'));
            $mockYear = $this->getMock('Year',array('getId'));
            $mockYear->method('getId')->willReturn($mockOrgAcademicYearId);
            $mockOrgAcademicYear->method('getYearId')->willReturn($mockYear);
            $mockOrgAcademicYearRepository->method('find')->willReturn($mockOrgAcademicYear);

            $mockOrganization = $this->getMock('Organization',array('getId'));
            $mockOrganization->method('getId')->willReturn($mockOrganizationId);
            $mockOrgAcademicYear->method('getOrganization')->willReturn($mockOrganization);

            $mockAcademicYearService->method('findCurrentAcademicYearForOrganization')->willReturn($mockCurrentYearData);

            $mockPersistenceRetentionService->method('getMeaningfulRetentionVariables')->willReturn($mockMeaningfulRetentionVariables);

            $mockWessLinkRepository->method('getSurveysAndNamesForOrganization')->willReturn($mockSurveyData);

            $mockIntentToLeaveRepository->method('getStudentsWhoIntendToLeave')->willReturn($mockStudentsIntendingToLeave);

            $mockOrgPersonStudentRetentionRepository->method('getAggregatedCountsOfRetentionDataByStudentList')->willReturn($mockRetentionData);

            $mockPersistenceRetentionService->method('formatRetentionDataset')->willReturn($mockFormatRetentionDataset);

            $mockPersonFactorCalculatedRepository->method('getTopFactorsCorrelatedToPersistence')->willReturn($mockTopFactorsWithCorrelations);

            $mockPersonFactorCalculatedRepository->method('getTopFactorsCorrelatedWithEbiMetadata')->willReturn($mockEbiMetadataRecords);

            $mockActivityReportService->method('getActivityReportSectionQueries')->willReturn('');
            

            $mockOrgAcademicTerm = $this->getMock('OrgAcademicTerms',array('getOrgAcademicYear','getName'));
            $mockOrgAcademicTerm->method('getOrgAcademicYear')->willReturn($mockOrgAcademicYear);
            $mockOrgAcademicTerm->method('getName')->willReturn("Term1");
            $mockOrgAcademicYear->method('getId')->willReturn($mockOrgAcademicYearId);
            $mockOrgAcademicTermsRepository->method('find')->willReturn($mockOrgAcademicTerm);

            $mockOrgPersonStudentRetentionTrackingGroupRepository->method('areStudentsAssignedToThisRetentionTrackingYear')->willReturn(true);
            $mockReportRunningStatusObj->method('getCreatedAt')->willReturn($mockDateObj);
            $mockDateObj->method('getTimestamp')->willReturn(123456789);
            $mockOrgPersonStudentRetentionTrackingGroupRepository->method('getRetentionTrackingGroupStudents')->willReturn($mockRetentionTrackStudentIds);
            $mockPersonRiskLevelHistoryRepository->method('getRiskLevelHistoryByDateRange')->willReturn($mockRiskLevelSet);

            $mockRiskLevelsRepository->method('find')->willReturnCallback(function($inputData){

                $mockRiskLevelObject = $this->getMock('RiskLevels',array('getId', 'getRiskText', 'getColorHex'));

                switch($inputData){
                    case 1:
                        $mockRiskLevelObject->method('getRiskText')->willReturn('red2');
                        $mockRiskLevelObject->method('getColorHex')->willReturn('#c70009');
                        break;
                    case 2:
                        $mockRiskLevelObject->method('getRiskText')->willReturn('red');
                        $mockRiskLevelObject->method('getColorHex')->willReturn('#f72d35');
                        break;
                    case 3:
                        $mockRiskLevelObject->method('getRiskText')->willReturn('yellow');
                        $mockRiskLevelObject->method('getColorHex')->willReturn('#fec82a');
                        break;
                    case 4:
                        $mockRiskLevelObject->method('getRiskText')->willReturn('green');
                        $mockRiskLevelObject->method('getColorHex')->willReturn('#95cd3c');
                        break;
                    case 6:
                        $mockRiskLevelObject->method('getRiskText')->willReturn('gray');
                        $mockRiskLevelObject->method('getColorHex')->willReturn('#cccccc');
                        break;
                }

                return $mockRiskLevelObject;

            });

            $mockRiskLevelsRepository->method('findOneBy')->willReturnCallback(function($inputData){
                $mockRiskLevelObject = $this->getMock('RiskLevels',array('getId', 'getRiskText', 'getColorHex'));
                $mockRiskLevelObject->method('getRiskText')->willReturn('gray');
                $mockRiskLevelObject->method('getColorHex')->willReturn('#cccccc');
                return $mockRiskLevelObject;
            });

            $mockGPAReportService->method('getEndTermGpaId')->willReturn($mockGPAId);
            $mockGPAReportService->method('buildReportItems')->willReturn($mockGPAReport);

            $mockDataProcessingUtilityService->method('recursiveRemovalByArrayKey')->willReturn($mockGPAReport);

            $mockEbiMetadataObject = $this->getMock('EbiMetadata',array('getId'));
            $mockEbiMetadataRepository->method('findOneBy')->willReturn($mockEbiMetadataObject);
            
            $mockCohortObject = $this->getMock('OrgCohortName',array('getCohortName'));
            $mockCohortObject->method('getCohortName')->willReturn($mockCohortName);
            $mockOrgCohortNameRepository->method('findOneBy')->willReturn($mockCohortObject);

            $mockOrgPersonFacultyRepository->method('getAllFacultiesForOrg')->willReturn($mockFacultySet);

            $mockOrgAcademicYear->method('getStartDate')->willReturn($mockDateObj);
            $mockOrgAcademicYear->method('getEndDate')->willReturn($mockDateObj);

            $mockSearchRepository->method('getQueryResult')->willReturn([]);

            $mockReportObject = $this->getMock('Reports',array('getId','getName', 'getShortCode'));
            $mockReportObject->method('getName')->willReturn('Executive Summary Report');
            $mockReportObject->method('getShortCode')->willReturn('EXEC');
            $mockReportsRepository->method('find')->willReturn($mockReportObject);

            $mockPersonObject = $this->getMock('Person',array('getId','getFirstName','getLastName'));
            $mockPersonObject->method('getFirstName')->willReturn('test123');
            $mockPersonObject->method('getLastName')->willReturn('test456');
            $mockPersonRepository->method('find')->willReturn($mockPersonObject);

            $executiveSummaryService = new ExecutiveSummaryService($mockRepositoryResolver, $mockLogger, $mockContainer);

            $functionResults = $executiveSummaryService->generateReport($reportRunningStatusDto->getId(), $reportRunningStatusDto);
            $this->assertEquals($expectedResult, $functionResults['report_items']);

        },['examples'=>[
            //Example 1: Generate report
            [
                $this->buildReportRunningDto(99, $this->rawData['search_attributes'], $this->sections),
                "12345,123456,1234567,12345678,12345679,12345680,12345681,12345682",
                [
                    "year_id" => "201718"
                ],
                [
                    0 =>
                        [
                            "section_name" => "What is Mapworks?",
                            "section_text" =>
                            [
                                0 =>
                                    [
                                        "element_name" => "Purpose",
                                        "element_text" => "Mapworks is a holistic approach to student success and retention, providing a platform of information that faculty and staff use to identify at-risk students early in the term. It also allows faculty and staff the ability to coordinate interventions with at-risk students by providing the power of real-time analytics, strategic communications, and differentiated user interfacing, with integrated statistical testing and outcomes reporting.",
                                    ],
                                1 =>
                                    [
                                        "element_name" => "Process",
                                        "element_text" => "The Mapworks process includes combining data from the institution with information from the students. Using that information, Mapworks uses real-time analytics to provide information directly to the students as well as to the faculty and staff working with the students.",
                                    ],

                            ]
                        ],
                    1 =>
                        [
                            "section_name" => "Risk Profile",
                            "total_students" => 4,
                            "risk_levels" =>
                                [
                                    0 =>
                                        [
                                            "risk_level" => "red2",
                                            "total_students" => 1,
                                            "risk_percentage" => 25,
                                            "color_value" => "#c70009"
                                        ],
                                    1 =>
                                        [
                                            "risk_level" => "red",
                                            "total_students" => 1,
                                            "risk_percentage" => 25,
                                            "color_value" => "#f72d35"
                                        ],
                                    2 =>
                                        [
                                            "risk_level" => "yellow",
                                            "total_students" => 1,
                                            "risk_percentage" => 25,
                                            "color_value" => "#fec82a"
                                        ],
                                    3 =>
                                        [
                                            "risk_level" => "green",
                                            "total_students" => 1,
                                            "risk_percentage" => 25,
                                            "color_value" => "#95cd3c"
                                        ],
                                    4 =>
                                        [
                                            "risk_level" => "gray",
                                            "total_students" => 1,
                                            "risk_percentage" => 25,
                                            "color_value" => "#cccccc"
                                        ]
                                ]
                        ],
                    2 =>
                        [
                            "section_name" => "GPA by Risk",
                            "gpa_summary_by_term" =>
                                [
                                    "dummyValue" => 1
                                ]
                        ],
                    3 =>
                        [
                            "section_name" => "Intent to Leave and Persistence",
                            "cohort_specific_data" =>
                                [
                                    0 =>
                                        [
                                            "cohort" => 1,
                                            "cohort_name" => "Survey Cohort 1",
                                            "survey_specific_data" =>
                                                [
                                                    0 =>
                                                        [
                                                            "survey_id" => 11,
                                                            "survey_name" => "Transition One",
                                                            "persistence_specific_data" =>
                                                            [

                                                                0 =>
                                                                   [
                                                                       "title" => "N = Responded Intent to Leave",
                                                                       "count" => 1
                                                                   ],
                                                                1 =>
                                                                   [
                                                                       "title" => "Retained to Midyear Year 1",
                                                                       "students_retained" => 1,
                                                                       "student_count" => 1,
                                                                       "percent" => 100
                                                                   ],
                                                               2 =>
                                                                   [
                                                                       "title" => "Retained to Start of Year 2",
                                                                       "students_retained" => 1,
                                                                       "student_count" => 1,
                                                                       "percent" => 100
                                                                   ],
                                                               3 =>
                                                                   [
                                                                       "title" => "Retained to Midyear Year 2",
                                                                       "students_retained" => 1,
                                                                       "student_count" => 1,
                                                                       "percent" => 100
                                                                   ],
                                                               4 =>
                                                                   [
                                                                       "title" => "Retained to Start of Year 3",
                                                                       "students_retained" => 1,
                                                                       "student_count" => 1,
                                                                       "percent" => 100
                                                                   ],
                                                               5 =>
                                                                   [
                                                                       "title" => "Retained to Midyear Year 3",
                                                                       "students_retained" => 1,
                                                                       "student_count" => 1,
                                                                       "percent" => 100
                                                                   ],
                                                               6 =>
                                                                   [
                                                                       "title" => "Retained to Start of Year 4",
                                                                       "students_retained" => 1,
                                                                       "student_count" => 1,
                                                                       "percent" => 100
                                                                   ],
                                                               7 =>
                                                                   [
                                                                       "title" => "Retained to Midyear Year 4",
                                                                       "students_retained" => 1,
                                                                       "student_count" => 1,
                                                                       "percent" => 100
                                                                   ],
                                                            ]
                                                        ],
                                                    1 =>
                                                        [
                                                            "survey_id" => 14,
                                                            "survey_name" => "Checkup Two",
                                                            "persistence_specific_data" =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        "title" => "N = Responded Intent to Leave",
                                                                        "count" => 1
                                                                    ],
                                                                1 =>
                                                                    [
                                                                        "title" => "Retained to Midyear Year 1",
                                                                        "students_retained" => 1,
                                                                        "student_count" => 1,
                                                                        "percent" => 100
                                                                    ],
                                                                2 =>
                                                                    [
                                                                        "title" => "Retained to Start of Year 2",
                                                                        "students_retained" => 1,
                                                                        "student_count" => 1,
                                                                        "percent" => 100
                                                                    ],
                                                                3 =>
                                                                    [
                                                                        "title" => "Retained to Midyear Year 2",
                                                                        "students_retained" => 1,
                                                                        "student_count" => 1,
                                                                        "percent" => 100
                                                                    ],
                                                                4 =>
                                                                    [
                                                                        "title" => "Retained to Start of Year 3",
                                                                        "students_retained" => 1,
                                                                        "student_count" => 1,
                                                                        "percent" => 100
                                                                    ],
                                                                5 =>
                                                                    [
                                                                        "title" => "Retained to Midyear Year 3",
                                                                        "students_retained" => 1,
                                                                        "student_count" => 1,
                                                                        "percent" => 100
                                                                    ],
                                                                6 =>
                                                                    [
                                                                        "title" => "Retained to Start of Year 4",
                                                                        "students_retained" => 1,
                                                                        "student_count" => 1,
                                                                        "percent" => 100
                                                                    ],
                                                                7 =>
                                                                    [
                                                                        "title" => "Retained to Midyear Year 4",
                                                                        "students_retained" => 1,
                                                                        "student_count" => 1,
                                                                        "percent" => 100
                                                                    ]
                                                            ]
                                                        ]
                                                    ]
                                        ]
                                ]
                        ],
                    4 =>
                        [
                            "section_name" => "Persistence and Retention by Risk",
                            "section_data" => 
                                [
                                0 =>
                                    [
                                        "column_title" => "Retained to Midyear Year 1",
                                        "total_students_retained" => 11,
                                        "total_student_count" => 11,
                                        "percent" => 100,
                                        "persistence_retention_by_risk" =>
                                            [
                                                0 =>
                                                    [
                                                        "risk_color" => "green",
                                                        "students_retained" => 2,
                                                        "student_count" => 2,
                                                        "percent" => 100
                                                    ],
                                                1 =>
                                                    [
                                                        "risk_color" => "yellow",
                                                        "students_retained" => 2,
                                                        "student_count" => 2,
                                                        "percent" => 100
                                                    ],
                                                2 =>
                                                    [
                                                        "risk_color" => "red",
                                                        "students_retained" => 5,
                                                        "student_count" => 5,
                                                        "percent" => 100
                                                    ],
                                                3 =>
                                                    [
                                                        "risk_color" => "red2",
                                                        "students_retained" => 2,
                                                        "student_count" => 2,
                                                        "percent" => 100
                                                    ]
                                            ]
                                    ],
                                1 =>
                                    [
                                        "column_title" => "Retained to Start of Year 2",
                                        "total_students_retained" => 11,
                                        "total_student_count" => 11,
                                        "percent" => 100,
                                        "persistence_retention_by_risk" =>
                                            [
                                                0 =>
                                                    [
                                                        "risk_color" => "green",
                                                        "students_retained" => 2,
                                                        "student_count" => 2,
                                                        "percent" => 100
                                                    ],
                                                1 =>
                                                    [
                                                        "risk_color" => "yellow",
                                                        "students_retained" => 2,
                                                        "student_count" => 2,
                                                        "percent" => 100
                                                    ],
                                                2 =>
                                                    [
                                                        "risk_color" => "red",
                                                        "students_retained" => 5,
                                                        "student_count" => 5,
                                                        "percent" => 100
                                                    ],
                                                3 =>
                                                    [
                                                        "risk_color" => "red2",
                                                        "students_retained" => 2,
                                                        "student_count" => 2,
                                                        "percent" => 100
                                                    ]
                                            ]
                                    ],
                                2 =>
                                    [
                                        "column_title" => "Retained to Midyear Year 2",
                                        "total_students_retained" => 11,
                                        "total_student_count" => 11,
                                        "percent" => 100,
                                        "persistence_retention_by_risk" =>
                                            [
                                                0 =>
                                                    [
                                                        "risk_color" => "green",
                                                        "students_retained" => 2,
                                                        "student_count" => 2,
                                                        "percent" => 100
                                                    ],
                                                1 =>
                                                    [
                                                        "risk_color" => "yellow",
                                                        "students_retained" => 2,
                                                        "student_count" => 2,
                                                        "percent" => 100
                                                    ],
                                                2 =>
                                                    [
                                                        "risk_color" => "red",
                                                        "students_retained" => 5,
                                                        "student_count" => 5,
                                                        "percent" => 100
                                                    ],
                                                3 =>
                                                    [
                                                        "risk_color" => "red2",
                                                        "students_retained" => 2,
                                                        "student_count" => 2,
                                                        "percent" => 100
                                                    ]
                                            ]
                                    ]
                                ]
                        ],
                    5 =>
                        [
                            "section_name" => "Top Factors with Correlation to Persistence and Retention",
                            "cohort_specific_data" =>
                                [
                                    0 =>
                                        [
                                            "cohort" => 1,
                                            "cohort_name" => "Survey Cohort 1",
                                            "survey_specific_data" =>
                                                [
                                                    0 =>
                                                        [
                                                            "survey_id" => 11,
                                                            "survey_name" => "Transition One",
                                                            "persistence_specific_data" =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        "title" => "Retained to Midyear Year 1",
                                                                        "factors" =>
                                                                            [
                                                                                0 => "Self-Assessment: Communication Skills",
                                                                                1 => "Homesickness: Distressed",
                                                                                2 => "Homesickness: Separation",
                                                                                3 => "Off-Campus Living: Environment ",
                                                                                4 => "Basic Academic Behaviors"
                                                                            ]
                                                                    ],
                                                                1 =>
                                                                    [
                                                                        "title" => "Retained to Start of Year 2",
                                                                        "factors" =>
                                                                            [
                                                                                0 => "Self-Assessment: Communication Skills",
                                                                                1 => "Homesickness: Distressed",
                                                                                2 => "Homesickness: Separation",
                                                                                3 => "Off-Campus Living: Environment ",
                                                                                4 => "Basic Academic Behaviors"
                                                                            ]
                                                                    ],
                                                                2 =>
                                                                    [
                                                                        "title" => "Retained to Midyear Year 2",
                                                                        "factors" =>
                                                                            [
                                                                                0 => "Self-Assessment: Communication Skills",
                                                                                1 => "Homesickness: Distressed",
                                                                                2 => "Homesickness: Separation",
                                                                                3 => "Off-Campus Living: Environment ",
                                                                                4 => "Basic Academic Behaviors"
                                                                            ]
                                                                    ],
                                                                3 =>
                                                                    [
                                                                        "title" => "Retained to Start of Year 3",
                                                                        "factors" =>
                                                                            [
                                                                                0 => "Self-Assessment: Communication Skills",
                                                                                1 => "Homesickness: Distressed",
                                                                                2 => "Homesickness: Separation",
                                                                                3 => "Off-Campus Living: Environment ",
                                                                                4 => "Basic Academic Behaviors"
                                                                            ]
                                                                    ],
                                                                4 =>
                                                                    [
                                                                        "title" => "Retained to Midyear Year 3",
                                                                        "factors" =>
                                                                            [
                                                                                0 => "Self-Assessment: Communication Skills",
                                                                                1 => "Homesickness: Distressed",
                                                                                2 => "Homesickness: Separation",
                                                                                3 => "Off-Campus Living: Environment ",
                                                                                4 => "Basic Academic Behaviors"
                                                                            ]
                                                                    ],
                                                                5 =>
                                                                    [
                                                                        "title" => "Retained to Start of Year 4",
                                                                        "factors" =>
                                                                            [
                                                                                0 => "Self-Assessment: Communication Skills",
                                                                                1 => "Homesickness: Distressed",
                                                                                2 => "Homesickness: Separation",
                                                                                3 => "Off-Campus Living: Environment ",
                                                                                4 => "Basic Academic Behaviors"
                                                                            ]
                                                                    ],
                                                                6 =>
                                                                    [
                                                                        "title" => "Retained to Midyear Year 4",
                                                                        "factors" =>
                                                                            [
                                                                                0 => "Self-Assessment: Communication Skills",
                                                                                1 => "Homesickness: Distressed",
                                                                                2 => "Homesickness: Separation",
                                                                                3 => "Off-Campus Living: Environment ",
                                                                                4 => "Basic Academic Behaviors"
                                                                            ]
                                                                    ]
                                                            ]
                                                        ],
                                                    1 =>
                                                        [
                                                            "survey_id" => 14,
                                                            "survey_name" => "Checkup Two",
                                                            "persistence_specific_data" =>
                                                                [
                                                                    0 =>
                                                                        [
                                                                            "title" => "Retained to Midyear Year 1",
                                                                            "factors" =>
                                                                                [
                                                                                    0 => "Self-Assessment: Communication Skills",
                                                                                    1 => "Homesickness: Distressed",
                                                                                    2 => "Homesickness: Separation",
                                                                                    3 => "Off-Campus Living: Environment ",
                                                                                    4 => "Basic Academic Behaviors"
                                                                                ]
                                                                        ],
                                                                    1 =>
                                                                        [
                                                                            "title" => "Retained to Start of Year 2",
                                                                            "factors" =>
                                                                                [
                                                                                    0 => "Self-Assessment: Communication Skills",
                                                                                    1 => "Homesickness: Distressed",
                                                                                    2 => "Homesickness: Separation",
                                                                                    3 => "Off-Campus Living: Environment ",
                                                                                    4 => "Basic Academic Behaviors"
                                                                                ]
                                                                        ],
                                                                    2 =>
                                                                        [
                                                                            "title" => "Retained to Midyear Year 2",
                                                                            "factors" =>
                                                                                [
                                                                                    0 => "Self-Assessment: Communication Skills",
                                                                                    1 => "Homesickness: Distressed",
                                                                                    2 => "Homesickness: Separation",
                                                                                    3 => "Off-Campus Living: Environment ",
                                                                                    4 => "Basic Academic Behaviors"
                                                                                ]
                                                                        ],
                                                                    3 =>
                                                                        [
                                                                            "title" => "Retained to Start of Year 3",
                                                                            "factors" =>
                                                                                [
                                                                                    0 => "Self-Assessment: Communication Skills",
                                                                                    1 => "Homesickness: Distressed",
                                                                                    2 => "Homesickness: Separation",
                                                                                    3 => "Off-Campus Living: Environment ",
                                                                                    4 => "Basic Academic Behaviors"
                                                                                ]
                                                                        ],
                                                                    4 =>
                                                                        [
                                                                            "title" => "Retained to Midyear Year 3",
                                                                            "factors" =>
                                                                                [
                                                                                    0 => "Self-Assessment: Communication Skills",
                                                                                    1 => "Homesickness: Distressed",
                                                                                    2 => "Homesickness: Separation",
                                                                                    3 => "Off-Campus Living: Environment ",
                                                                                    4 => "Basic Academic Behaviors"
                                                                                ]
                                                                        ],
                                                                    5 =>
                                                                        [
                                                                            "title" => "Retained to Start of Year 4",
                                                                            "factors" =>
                                                                                [
                                                                                    0 => "Self-Assessment: Communication Skills",
                                                                                    1 => "Homesickness: Distressed",
                                                                                    2 => "Homesickness: Separation",
                                                                                    3 => "Off-Campus Living: Environment ",
                                                                                    4 => "Basic Academic Behaviors"
                                                                                ]
                                                                        ],
                                                                    6 =>
                                                                        [
                                                                            "title" => "Retained to Midyear Year 4",
                                                                            "factors" =>
                                                                                [
                                                                                    0 => "Self-Assessment: Communication Skills",
                                                                                    1 => "Homesickness: Distressed",
                                                                                    2 => "Homesickness: Separation",
                                                                                    3 => "Off-Campus Living: Environment ",
                                                                                    4 => "Basic Academic Behaviors"
                                                                                ]
                                                                        ]
                                                                ]
                                                        ]
                                               ]
                                        ]
                                ]
                        ],
                    6 =>
                        [
                            "section_name" => "Top Factors with Correlation to GPA",
                            "cohort_specific_data" =>
                                [
                                    0 =>
                                        [
                                            "cohort" => 1,
                                            "cohort_name" => "Survey Cohort 1",
                                            "survey_specific_data" =>
                                                [
                                                    0 =>
                                                        [
                                                            "survey_id" => 11,
                                                            "survey_name" => "Transition One",
                                                            "term_specific_data" =>
                                                                [
                                                                    0 =>
                                                                        [
                                                                            "title" => "Term1",
                                                                            "factors" =>
                                                                                [
                                                                                    0 => "entry1",
                                                                                    1 => "entry2",
                                                                                    2 => "entry3"
                                                                                ]
                                                                        ]
                                                                ]
                                                        ],
                                                    1 =>
                                                        [
                                                            "survey_id" => 14,
                                                            "survey_name" => "Checkup Two",
                                                            "term_specific_data" =>
                                                                [
                                                                    0 =>
                                                                        [
                                                                            "title" => "Term1",
                                                                            "factors" =>
                                                                                [
                                                                                    0 => "entry1",
                                                                                    1 => "entry2",
                                                                                    2 => "entry3"
                                                                                ]
                                                                        ]

                                                                ]

                                                        ]
                                                ]
                                        ]
                                ]
                        ],
                    7 =>
                        [
                            "section_name" => "Activity Overview",
                            "section_data" =>
                            [
                                0 =>
                                    [
                                        "label" => "# of Activities Logged",
                                        "activities" =>
                                            [
                                                0 =>
                                                    [
                                                        "activity_type" => "Referrals",
                                                        "value" => 0
                                                    ],
                                                1 =>
                                                    [
                                                        "activity_type" => "Appointments",
                                                        "value" => 0
                                                    ],
                                                2 =>
                                                    [
                                                        "activity_type" => "Contacts",
                                                        "value" => 0
                                                    ],
                                                3 =>
                                                    [
                                                        "activity_type" => "Interaction Contacts",
                                                        "value" => 0
                                                    ],
                                                4 =>
                                                    [
                                                        "activity_type" => "Notes",
                                                        "value" => 0
                                                    ],
                                                5 =>
                                                    [
                                                        "activity_type" => "Academic Updates",
                                                        "value" => 0
                                                    ]
                                            ]
                                    ],
                                1 =>
                                    [
                                        "label" => "# of Students Involved",
                                        "activities" =>
                                            [
                                                0 =>
                                                    [
                                                        "activity_type" => "Referrals",
                                                        "value" => 0
                                                    ],
                                                1 =>
                                                    [
                                                        "activity_type" => "Appointments",
                                                        "value" => 0
                                                    ],
                                                2 =>
                                                    [
                                                        "activity_type" => "Contacts",
                                                        "value" => 0
                                                    ],
                                                3 =>
                                                    [
                                                        "activity_type" => "Interaction Contacts",
                                                         "value" => 0
                                                    ],
                                                4 =>
                                                    [
                                                        "activity_type" => "Notes",
                                                        "value" => 0
                                                    ],
                                                5 =>
                                                    [
                                                        "activity_type" => "Academic Updates",
                                                        "value" => 0
                                                    ]
                                            ]

                                    ],
                                2 =>
                                    [
                                        "label" => "% of Students",
                                        "activities" =>
                                            [
                                                0 =>
                                                    [
                                                        "activity_type" => "Referrals",
                                                        "value" => 0
                                                    ],
                                                1 =>
                                                    [
                                                        "activity_type" => "Appointments",
                                                        "value" => 0
                                                    ],
                                                2 =>
                                                    [
                                                        "activity_type" => "Contacts",
                                                        "value" => 0
                                                    ],
                                                3 =>
                                                    [
                                                        "activity_type" => "Interaction Contacts",
                                                        "value" => 0
                                                    ],
                                                4 =>
                                                    [
                                                        "activity_type" => "Notes",
                                                        "value" => 0
                                                    ],
                                                5 =>
                                                    [
                                                        "activity_type" => "Academic Updates",
                                                        "value" => 0
                                                    ]
                                            ]
                                    ],
                                3 =>
                                    [
                                        "label" => "# of Faculty/Staff Logged",
                                        "activities" =>
                                            [
                                                0 =>
                                                    [
                                                        "activity_type" => "Referrals",
                                                        "value" => 0
                                                    ],
                                                1 =>
                                                    [
                                                        "activity_type" => "Appointments",
                                                        "value" => 0
                                                    ],
                                                2 =>
                                                    [
                                                        "activity_type" => "Contacts",
                                                        "value" => 0
                                                    ],
                                                3 =>
                                                    [
                                                        "activity_type" => "Interaction Contacts",
                                                        "value" => 0
                                                    ],
                                                4 =>
                                                    [
                                                        "activity_type" => "Notes",
                                                        "value" => 0
                                                    ],
                                                5 =>
                                                    [
                                                        "activity_type" => "Academic Updates",
                                                        "value" => 0
                                                    ]
                                            ]
                                    ],
                                4 =>
                                    [
                                        "label" => "# of Faculty/Staff Received",
                                        "activities" =>
                                            [
                                                0 =>
                                                    [
                                                        "activity_type" => "Referrals",
                                                        "value" => 0
                                                    ],
                                                1 =>
                                                    [
                                                        "activity_type" => "Appointments",
                                                        "value" => "-"
                                                    ],
                                                2 =>
                                                    [
                                                        "activity_type" => "Contacts",
                                                        "value" => "-"
                                                    ],
                                                3 =>
                                                    [
                                                        "activity_type" => "Interaction Contacts",
                                                        "value" => "-"
                                                    ],
                                                4 =>
                                                    [
                                                        "activity_type" => "Notes",
                                                        "value" => "-"
                                                    ],
                                                5 =>
                                                    [
                                                        "activity_type" => "Academic Updates",
                                                        "value" => "-"
                                                    ]
                                            ]
                                    ]
                            ]
                        ]
                ]
            ],
            //Example 2: Generate report in between academic years
            [
                $this->buildReportRunningDto(99, $this->rawData['search_attributes'], $this->sections),
                "12345,123456,1234567,12345678,12345679,12345680,12345681,12345682",
                [],
                [
                    0 =>
                        [
                            "section_name" => "What is Mapworks?",
                            "section_text" =>
                                [
                                    0 =>
                                        [
                                            "element_name" => "Purpose",
                                            "element_text" => "Mapworks is a holistic approach to student success and retention, providing a platform of information that faculty and staff use to identify at-risk students early in the term. It also allows faculty and staff the ability to coordinate interventions with at-risk students by providing the power of real-time analytics, strategic communications, and differentiated user interfacing, with integrated statistical testing and outcomes reporting.",
                                        ],
                                    1 =>
                                        [
                                            "element_name" => "Process",
                                            "element_text" => "The Mapworks process includes combining data from the institution with information from the students. Using that information, Mapworks uses real-time analytics to provide information directly to the students as well as to the faculty and staff working with the students.",
                                        ],

                                ]
                        ],
                    1 =>
                        [
                            "section_name" => "Risk Profile",
                            "total_students" => 4,
                            "risk_levels" =>
                                [
                                    0 =>
                                        [
                                            "risk_level" => "red2",
                                            "total_students" => 1,
                                            "risk_percentage" => 25,
                                            "color_value" => "#c70009"
                                        ],
                                    1 =>
                                        [
                                            "risk_level" => "red",
                                            "total_students" => 1,
                                            "risk_percentage" => 25,
                                            "color_value" => "#f72d35"
                                        ],
                                    2 =>
                                        [
                                            "risk_level" => "yellow",
                                            "total_students" => 1,
                                            "risk_percentage" => 25,
                                            "color_value" => "#fec82a"
                                        ],
                                    3 =>
                                        [
                                            "risk_level" => "green",
                                            "total_students" => 1,
                                            "risk_percentage" => 25,
                                            "color_value" => "#95cd3c"
                                        ],
                                    4 =>
                                        [
                                            "risk_level" => "gray",
                                            "total_students" => 1,
                                            "risk_percentage" => 25,
                                            "color_value" => "#cccccc"
                                        ]
                                ]
                        ],
                    2 =>
                        [
                            "section_name" => "GPA by Risk",
                            "gpa_summary_by_term" =>
                                [
                                    "dummyValue" => 1
                                ]
                        ],
                    3 =>
                        [
                            "section_name" => "Intent to Leave and Persistence",
                            "message" => "No retention variables are available."
                        ],
                    4 =>
                        [
                            "section_name" => "Persistence and Retention by Risk",
                            "section_data" => null
                        ],
                    5 =>
                        [
                            "section_name" => "Top Factors with Correlation to Persistence and Retention",
                            "message" => "No retention variables are available."
                        ],
                    6 =>
                        [
                            "section_name" => "Top Factors with Correlation to GPA",
                            "cohort_specific_data" =>
                                [
                                    0 =>
                                        [
                                            "cohort" => 1,
                                            "cohort_name" => "Survey Cohort 1",
                                            "survey_specific_data" =>
                                                [
                                                    0 =>
                                                        [
                                                            "survey_id" => 11,
                                                            "survey_name" => "Transition One",
                                                            "term_specific_data" =>
                                                                [
                                                                    0 =>
                                                                        [
                                                                            "title" => "Term1",
                                                                            "factors" =>
                                                                                [
                                                                                    0 => "entry1",
                                                                                    1 => "entry2",
                                                                                    2 => "entry3"
                                                                                ]
                                                                        ]
                                                                ]
                                                        ],
                                                    1 =>
                                                        [
                                                            "survey_id" => 14,
                                                            "survey_name" => "Checkup Two",
                                                            "term_specific_data" =>
                                                                [
                                                                    0 =>
                                                                        [
                                                                            "title" => "Term1",
                                                                            "factors" =>
                                                                                [
                                                                                    0 => "entry1",
                                                                                    1 => "entry2",
                                                                                    2 => "entry3"
                                                                                ]
                                                                        ]

                                                                ]

                                                        ]
                                                ]
                                        ]
                                ]
                        ],
                    7 =>
                        [
                            "section_name" => "Activity Overview",
                            "section_data" =>
                                [
                                    0 =>
                                        [
                                            "label" => "# of Activities Logged",
                                            "activities" =>
                                                [
                                                    0 =>
                                                        [
                                                            "activity_type" => "Referrals",
                                                            "value" => 0
                                                        ],
                                                    1 =>
                                                        [
                                                            "activity_type" => "Appointments",
                                                            "value" => 0
                                                        ],
                                                    2 =>
                                                        [
                                                            "activity_type" => "Contacts",
                                                            "value" => 0
                                                        ],
                                                    3 =>
                                                        [
                                                            "activity_type" => "Interaction Contacts",
                                                            "value" => 0
                                                        ],
                                                    4 =>
                                                        [
                                                            "activity_type" => "Notes",
                                                            "value" => 0
                                                        ],
                                                    5 =>
                                                        [
                                                            "activity_type" => "Academic Updates",
                                                            "value" => 0
                                                        ]
                                                ]
                                        ],
                                    1 =>
                                        [
                                            "label" => "# of Students Involved",
                                            "activities" =>
                                                [
                                                    0 =>
                                                        [
                                                            "activity_type" => "Referrals",
                                                            "value" => 0
                                                        ],
                                                    1 =>
                                                        [
                                                            "activity_type" => "Appointments",
                                                            "value" => 0
                                                        ],
                                                    2 =>
                                                        [
                                                            "activity_type" => "Contacts",
                                                            "value" => 0
                                                        ],
                                                    3 =>
                                                        [
                                                            "activity_type" => "Interaction Contacts",
                                                            "value" => 0
                                                        ],
                                                    4 =>
                                                        [
                                                            "activity_type" => "Notes",
                                                            "value" => 0
                                                        ],
                                                    5 =>
                                                        [
                                                            "activity_type" => "Academic Updates",
                                                            "value" => 0
                                                        ]
                                                ]

                                        ],
                                    2 =>
                                        [
                                            "label" => "% of Students",
                                            "activities" =>
                                                [
                                                    0 =>
                                                        [
                                                            "activity_type" => "Referrals",
                                                            "value" => 0
                                                        ],
                                                    1 =>
                                                        [
                                                            "activity_type" => "Appointments",
                                                            "value" => 0
                                                        ],
                                                    2 =>
                                                        [
                                                            "activity_type" => "Contacts",
                                                            "value" => 0
                                                        ],
                                                    3 =>
                                                        [
                                                            "activity_type" => "Interaction Contacts",
                                                            "value" => 0
                                                        ],
                                                    4 =>
                                                        [
                                                            "activity_type" => "Notes",
                                                            "value" => 0
                                                        ],
                                                    5 =>
                                                        [
                                                            "activity_type" => "Academic Updates",
                                                            "value" => 0
                                                        ]
                                                ]
                                        ],
                                    3 =>
                                        [
                                            "label" => "# of Faculty/Staff Logged",
                                            "activities" =>
                                                [
                                                    0 =>
                                                        [
                                                            "activity_type" => "Referrals",
                                                            "value" => 0
                                                        ],
                                                    1 =>
                                                        [
                                                            "activity_type" => "Appointments",
                                                            "value" => 0
                                                        ],
                                                    2 =>
                                                        [
                                                            "activity_type" => "Contacts",
                                                            "value" => 0
                                                        ],
                                                    3 =>
                                                        [
                                                            "activity_type" => "Interaction Contacts",
                                                            "value" => 0
                                                        ],
                                                    4 =>
                                                        [
                                                            "activity_type" => "Notes",
                                                            "value" => 0
                                                        ],
                                                    5 =>
                                                        [
                                                            "activity_type" => "Academic Updates",
                                                            "value" => 0
                                                        ]
                                                ]
                                        ],
                                    4 =>
                                        [
                                            "label" => "# of Faculty/Staff Received",
                                            "activities" =>
                                                [
                                                    0 =>
                                                        [
                                                            "activity_type" => "Referrals",
                                                            "value" => 0
                                                        ],
                                                    1 =>
                                                        [
                                                            "activity_type" => "Appointments",
                                                            "value" => "-"
                                                        ],
                                                    2 =>
                                                        [
                                                            "activity_type" => "Contacts",
                                                            "value" => "-"
                                                        ],
                                                    3 =>
                                                        [
                                                            "activity_type" => "Interaction Contacts",
                                                            "value" => "-"
                                                        ],
                                                    4 =>
                                                        [
                                                            "activity_type" => "Notes",
                                                            "value" => "-"
                                                        ],
                                                    5 =>
                                                        [
                                                            "activity_type" => "Academic Updates",
                                                            "value" => "-"
                                                        ]
                                                ]
                                        ]
                                ]
                        ]
                ]
            ]
        ]]);
    }
}