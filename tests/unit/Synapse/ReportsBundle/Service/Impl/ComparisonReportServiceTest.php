<?php

namespace Synapse\ReportsBundle\Service\Impl;

use Codeception\Specify;
use Codeception\Test\Unit;
use DateTime;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\AcademicBundle\Entity\OrgAcademicYear;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrgQuestionResponseRepository;
use Synapse\CoreBundle\Repository\PersonEbiMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonOrgMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\NotificationChannelService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\DAO\ComparisonReportDAO;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\ReportsBundle\Repository\ReportsRepository;
use Synapse\ReportsBundle\Repository\ReportsRunningJsonRepository;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\SurveyBundle\Repository\SurveyResponseRepository;

class ComparisonReportServiceTest extends Unit
{
    use Specify;

    private $standardSearchAttributes = [ // searchAttribute data for isqs
        "org_academic_year_id" => [
            186
        ],
        "org_academic_year" => [
            "year" => [
                "id" => 186,
                "name" => "2016-17 Academic Year",
                "year_id" => "201617"
            ],
        ],
        "retention_org_academic_year" => [
            "year" => [
                "year_id" => "201617"
            ],
        ],
        "gpa_org_academic_year" => [
            "year" => [
                "id" => 186,
                "name" => "2016-17 Academic Year",
                "year_id" => "201617"
            ],
        ],
        "latest_survey" => [
            "name" => "Transition One",
            "id" => 15
        ],
        "latest_cohort" => [
            "name" => "Survey Cohort 1",
            "id" => 1
        ],
        "group_names" => [
            "xxxx",
            "yyyy"
        ],
        "datablocks" => [

        ],
        "isps" => [

        ],
        "isqs" => [
            "survey_id" => 11,
            "year_id" => "201516",
            "question_id" => 1564,
            "type" => "category",
            "cohort" => "1",
            "subpopulation1" => [
                "category_type" => [
                    [
                        "id" => 32652,
                        "answer" => "Yes, it conflicts between 5 and 10 hours per week.",
                        "value" => "4",
                        "subpopulation1selected" => true
                    ]
                ]
            ],
            "subpopulation2" => [
                "category_type" => [
                    [
                        "id" => 32653,
                        "answer" => "Yes, it conflicts between 11 and 19 hours per week.",
                        "value" => "5",
                        "subpopulation2selected" => true
                    ],
                ],
            ],
        ],
        "survey" => [

        ],
        "filterText" => [
            "isq",
            "Organization: 059 Question ID: 01564",
            [
                "xxxx",
                "yyyy"
            ],
        ],
    ];

    public $sections = array(
        "reportId" => "18",
        "reportDisable" => false,
        "report_name" => "Compare",
        "short_code" => "SUB-COM",
        "reportDesc" => "Compare outcomes of two student subpopulations. Choose subpopulations by profile item, ISP, survey question or ISQ. Compare survey factors and GPA. Export to csv, print to pdf.",
        "reportFilterPages" => [
            [
                "reportPage" => "itemType",
                "title" => "Select Subpopulation Attribute",
                "breadCrumb" => "Select Type",
                "visited" => false,
                "showHelp" => true
            ],
            [
                "reportPage" => "treeSelector",
                "title" => "Select Profile Item",
                "breadCrumbs" => [
                    [
                        "type" => "isp",
                        "text" => "Profile Item"
                    ],
                    [
                        "type" => "isq",
                        "text" => "Survey Question"
                    ]
                ],
                "subPages" => [
                    [
                        "prefix" => "a",
                        "reportPage" => "treeSelector",
                        "title" => "Select one Cohort from one Survey",
                        "breadCrumb" => "Survey Question"
                    ],
                    [
                        "prefix" => "b",
                        "reportPage" => "surveyQuestion",
                        "title" => "Select one survey question",
                        "breadCrumb" => "Survey Question",
                        "showSearch" => true
                    ]
                ],
                "visited" => false,
                "showSearch" => true
            ],
            [
                "reportPage" => "subPopulation",
                "title" => "",
                "breadCrumb" => "Values",
                "visited" => false
            ]
        ],
        "reportFilter" => [
            "participating" => false,
            "risk" => true,
            "active" => true,
            "activities" => false,
            "group" => true,
            "course" => true,
            "ebi" => true,
            "isp" => true,
            "static" => true,
            "factor" => true,
            "survey" => true,
            "isq" => true,
            "surveyMetadata" => false,
            "academicTerm" => false,
            "cohort" => false,
            "team" => false
        ],
        "RequestParam" => "",
        "pageurl" => "/reports/outcomes-comparison-report/",
        "templateUrl" => "/partials/reports/outcomesComparisonReport/modals/report-parameters-modal.html",
        "controller" => "OutcomeComparisonController",
        "id" => "18",
        "report_description" => "Compare outcomes of two student subpopulations. Choose subpopulations by profile item, ISP, survey question or ISQ. Compare survey factors and GPA. Export to csv, print to pdf.",
        "is_batch_job" => false,
        "is_coordinator_report" => "n"
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


    protected function _before()
    {
        $this->mockContainer = $this->getMock('Container', ['get']);
        $this->mockLogger = $this->getMock('Logger', ['debug', 'error']);
        $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
        $this->mockResque = $this->getMock('resque', array('enqueue'));
        $this->mockSerializer = $this->getMock('serializer', array('serialize'));
    }


    private function buildReportRunningDto($orgIdStub, $filter, $sections = ['reportId' => 16], $filterdStudentIds = null)
    {
        $reportRunningStatus = new ReportRunningStatusDto();
        $reportRunningStatus->setId(1);
        $reportRunningStatus->setOrganizationId($orgIdStub);
        $reportRunningStatus->setReportId(18);
        $reportRunningStatus->setPersonId(113802);
        $reportRunningStatus->setSearchAttributes($filter);
        $reportRunningStatus->setMandatoryFilters($filter);
        $reportRunningStatus->setReportSections($sections);
        $reportRunningStatus->setCreatedAt("2017-03-03 00:00:00");

        return $reportRunningStatus;
    }


    public function testGetSubPopulationHeaderText()
    {
        $this->specify('test Build Compare Report CSV array sections', function ($subPopulationKey, $selectedIspOrIsqBlocks, $expectedResult) {

            //Create all mocks necessary for Service class creation
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'addWarning'));
            $mockContainer = $this->getMock('Container', array('get'));

            $comparisonReportService = new ComparisonReportService($mockRepositoryResolver, $mockLogger, $mockContainer);

            $functionResults = $comparisonReportService->getSubPopulationHeaderText($subPopulationKey, $selectedIspOrIsqBlocks, $expectedResult);

            $this->assertEquals($expectedResult, $functionResults);

        }, ['examples' => [
            [ // Example1 test with number type profile block with only single value
                'subpopulation1',
                [
                    "id" => 83,
                    "display_name" => "EndTermGPA",
                    "item_data_type" => "N",
                    "item_meta_key" => "Gender",
                    "calendar_assignment" => "T",
                    "year_term" => [
                        "year_id" => "201617",
                        "year_name" => "Newname2016-2017",
                        "term_name" => "long"
                    ],
                    "subpopulation1" => [
                        "is_single" => true,
                        "single_value" => 1
                    ],
                    "subpopulation2" => [
                        "is_single" => true,
                        "single_value" => 2
                    ]
                ],
                1
            ],
            [ // Example2 test with number type profile block with  single and range value
                'subpopulation2',
                [
                    "id" => 83,
                    "display_name" => "EndTermGPA",
                    "item_data_type" => "N",
                    "item_meta_key" => "Gender",
                    "calendar_assignment" => "T",
                    "year_term" => [
                        "year_id" => "201617",
                        "year_name" => "Newname2016-2017",
                        "term_name" => "long"
                    ],
                    "subpopulation1" => [
                        "is_single" => true,
                        "single_value" => 1
                    ],
                    "subpopulation2" => [
                        "is_single" => false,
                        "min_digits" => 2,
                        "max_digits" => 3
                    ]
                ],
                'between 2 and 3'
            ],
            [ // Example3 test with date type profile block
                'subpopulation2',
                [
                    "id" => 85,
                    "display_name" => "EndTermGPA",
                    "item_data_type" => "D",
                    "item_meta_key" => "Gender",
                    "calendar_assignment" => "T",
                    "year_term" => [
                        "year_id" => "201617",
                        "year_name" => "Newname2016-2017",
                        "term_name" => "long"
                    ],
                    "subpopulation1" => [
                        "start_date" => "06/20/2017",
                        "end_date" => "06/22/2017"
                    ],
                    "subpopulation2" => [
                        "start_date" => "06/06/2017",
                        "end_date" => "06/14/2017"
                    ]
                ],
                '06/06/2017 and 06/14/2017'
            ],
            [ // Example4 test with category type profile block
                'subpopulation2',
                [
                    "id" => 85,
                    "display_name" => "EndTermGPA",
                    "item_data_type" => "S",
                    "item_meta_key" => "Gender",
                    "calendar_assignment" => "T",
                    "year_term" => [
                        "year_id" => "201617",
                        "year_name" => "Newname2016-2017",
                        "term_name" => "long"
                    ],
                    "subpopulation1" => [
                        "category_type" => [
                            [
                                "answer" => "Male",
                                "value" => "1",
                                "sequence_no" => 0,
                                "id" => "",
                                "subpopulationOneselected" => true
                            ]
                        ]
                    ],
                    "subpopulation2" => [
                        "category_type" => [
                            [
                                "answer" => "Female",
                                "value" => "0",
                                "sequence_no" => 0,
                                "id" => "",
                                "subpopulationTwoselected" => true
                            ]
                        ]
                    ]
                ],
                'Female'
            ],
            [ // Example5 test with number type ISQ block
                'subpopulation2',
                [
                    "survey_id" => 11,
                    "year_id" => "201516",
                    "question_id" => 1564,
                    "type" => "number",
                    "cohort" => "1",
                    "subpopulation1" => [
                        "is_single" => true,
                        "single_value" => "8"
                    ],
                    "subpopulation2" => [
                        "is_single" => false,
                        "max_digits" => "20.0000",
                        "min_digits" => "10.000"
                    ],
                    "filterText" => [
                        "isp",
                        "ApplicationDate",
                        [
                            "sd",
                            "er"
                        ]
                    ]

                ],
                'between 10.000 and 20.0000'
            ],
            [  // Example6 test with categroy type with multiple category ISQ block
                'subpopulation2',
                [
                    "survey_id" => 11,
                    "year_id" => "201516",
                    "question_id" => 1564,
                    "type" => "category",
                    "cohort" => "1",
                    "subpopulation1" => [
                        "category_type" => [
                            [
                                "id" => 32652,
                                "answer" => "Yes, it conflicts between 5 and 10 hours per week.",
                                "value" => "4",
                                "subpopulation1selected" => true
                            ]
                        ]
                    ],
                    "subpopulation2" => [
                        "category_type" => [
                            [
                                "id" => 32653,
                                "answer" => "Yes, it conflicts between 11 and 19 hours per week.",
                                "value" => "5",
                                "subpopulation2selected" => true
                            ],
                            [
                                "id" => 32657,
                                "answer" => "No, it's Not conflicts between 11 and 19.",
                                "value" => "6",
                                "subpopulation2selected" => true
                            ]
                        ],
                    ]
                ],
                'Yes, it conflicts between 11 and 19 hours per week., No, it\'s Not conflicts between 11 and 19.'
            ]

        ]
        ]);
    }


    public function testBuildCompareReportCSVArraySections()
    {
        $this->specify('test Build Compare Report CSV array sections', function ($key, $reportItems, $gpaTermYearName, $csvRowCount, $expectedResult) {

            //Create all mocks necessary for Service class creation
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'addWarning'));
            $mockContainer = $this->getMock('Container', array('get'));

            $comparisonReportService = new ComparisonReportService($mockRepositoryResolver, $mockLogger, $mockContainer);

            $functionResults = $comparisonReportService->buildCompareReportCSVArraySections($key, $reportItems, $gpaTermYearName, $csvRowCount);

            $this->assertEquals($expectedResult, $functionResults);

        }, ['examples' => [
            [ // Example1 extract the factor data for csv with key as factor
                'factor',
                [
                    "organizationId" => 20,
                    "factor" => [
                        [
                            "factor_name" => "Commitment to the Institution",
                            "t_score" => "-",
                            "p_value" => "-",
                            "display" => 4,
                            "subpopulation1" => [
                                "name" => "NO",
                                "n_value" => 5,
                                "mean_value" => 6.53,
                                "standard_deviation_value" => 1.04
                            ],
                            "subpopulation2" => [
                                "name" => "YES",
                                "n_value" => 0,
                                "mean_value" => 0,
                                "standard_deviation_value" => 0
                            ]
                        ],
                        [
                            "factor_name" => "Communication Skills",
                            "t_score" => "-",
                            "p_value" => "-",
                            "display" => 4,
                            "subpopulation1" => [
                                "name" => "NO",
                                "n_value" => 5,
                                "mean_value" => 5.6,
                                "standard_deviation_value" => 0.65
                            ],
                            "subpopulation2" => [
                                "name" => "YES",
                                "n_value" => 0,
                                "mean_value" => 0,
                                "standard_deviation_value" => 0
                            ]
                        ],
                        [
                            "factor_name" => "Analytical Skills",
                            "t_score" => "-",
                            "p_value" => "-",
                            "display" => 4,
                            "subpopulation1" => [
                                "name" => "NO",
                                "n_value" => 5,
                                "mean_value" => 4.7,
                                "standard_deviation_value" => 0.91
                            ],
                            "subpopulation2" => [
                                "name" => "YES",
                                "n_value" => 0,
                                "mean_value" => 0,
                                "standard_deviation_value" => 0
                            ]
                        ]
                    ],
                    "gpa" => [
                        [
                            "term_name" => "Fall Term of 2016",
                            "t_score" => "-",
                            "p_value" => "-",
                            "display" => 4,
                            "subpopulation1" => [
                                "name" => "NO",
                                "n_value" => 9,
                                "mean_value" => 3.11,
                                "standard_deviation_value" => 1.2
                            ],
                            "subpopulation2" => [
                                "name" => "YES",
                                "n_value" => 0,
                                "mean_value" => 0,
                                "standard_deviation_value" => 0
                            ]
                        ],
                        [
                            "term_name" => "long",
                            "t_score" => "-",
                            "p_value" => "-",
                            "display" => 4,
                            "subpopulation1" => [
                                "name" => "NO",
                                "n_value" => 9,
                                "mean_value" => 1.56,
                                "standard_deviation_value" => 0.53
                            ],
                            "subpopulation2" => [
                                "name" => "YES",
                                "n_value" => 0,
                                "mean_value" => 0,
                                "standard_deviation_value" => 0
                            ]
                        ]
                    ]
                ],
                null,
                0,
                [
                    [
                        "Factor",
                        "Population",
                        "N",
                        "Mean",
                        "Standard Deviation",
                        "t-score",
                        "p-value"
                    ],
                    [
                        "Commitment to the Institution",
                        "NO",
                        5,
                        6.53,
                        1.04,
                        "",
                        ""
                    ],
                    [
                        "Commitment to the Institution",
                        "YES",
                        0,
                        0,
                        0,
                        "",
                        ""
                    ],
                    [
                        "Communication Skills",
                        "NO",
                        5,
                        5.6,
                        0.65,
                        "",
                        ""
                    ],
                    [
                        "Communication Skills",
                        "YES",
                        0,
                        0,
                        0,
                        "",
                        ""
                    ],
                    [
                        "Analytical Skills",
                        "NO",
                        5,
                        4.7,
                        0.91,
                        "",
                        ""
                    ],
                    [
                        "Analytical Skills",
                        "YES",
                        0,
                        0,
                        0,
                        "",
                        ""
                    ],
                    [
                        ""
                    ]
                ]
            ],
            [ // Example2 extract the factor data for csv with key as gpa
                'gpa',
                [
                    "organizationId" => 20,
                    "factor" => [
                        [
                            "factor_name" => "Commitment to the Institution",
                            "t_score" => "-",
                            "p_value" => "-",
                            "display" => 4,
                            "subpopulation1" => [
                                "name" => "NO",
                                "n_value" => 5,
                                "mean_value" => 6.53,
                                "standard_deviation_value" => 1.04
                            ],
                            "subpopulation2" => [
                                "name" => "YES",
                                "n_value" => 0,
                                "mean_value" => 0,
                                "standard_deviation_value" => 0
                            ]
                        ],
                        [
                            "factor_name" => "Communication Skills",
                            "t_score" => "-",
                            "p_value" => "-",
                            "display" => 4,
                            "subpopulation1" => [
                                "name" => "NO",
                                "n_value" => 5,
                                "mean_value" => 5.6,
                                "standard_deviation_value" => 0.65
                            ],
                            "subpopulation2" => [
                                "name" => "YES",
                                "n_value" => 0,
                                "mean_value" => 0,
                                "standard_deviation_value" => 0
                            ]
                        ],
                        [
                            "factor_name" => "Analytical Skills",
                            "t_score" => "-",
                            "p_value" => "-",
                            "display" => 4,
                            "subpopulation1" => [
                                "name" => "NO",
                                "n_value" => 5,
                                "mean_value" => 4.7,
                                "standard_deviation_value" => 0.91
                            ],
                            "subpopulation2" => [
                                "name" => "YES",
                                "n_value" => 0,
                                "mean_value" => 0,
                                "standard_deviation_value" => 0
                            ]
                        ]
                    ],
                    "gpa" => [
                        [
                            "term_name" => "Fall Term of 2016",
                            "t_score" => "-",
                            "p_value" => "-",
                            "display" => 4,
                            "subpopulation1" => [
                                "name" => "NO",
                                "n_value" => 9,
                                "mean_value" => 3.11,
                                "standard_deviation_value" => 1.2
                            ],
                            "subpopulation2" => [
                                "name" => "YES",
                                "n_value" => 0,
                                "mean_value" => 0,
                                "standard_deviation_value" => 0
                            ]
                        ],
                        [
                            "term_name" => "long",
                            "t_score" => "-",
                            "p_value" => "-",
                            "display" => 4,
                            "subpopulation1" => [
                                "name" => "NO",
                                "n_value" => 9,
                                "mean_value" => 1.56,
                                "standard_deviation_value" => 0.53
                            ],
                            "subpopulation2" => [
                                "name" => "YES",
                                "n_value" => 0,
                                "mean_value" => 0,
                                "standard_deviation_value" => 0
                            ]
                        ]
                    ]
                ],
                'Newname2016-2017',
                10,
                [
                    "10" => [
                        "GPA BY TERM for Newname2016-2017"
                    ],
                    "11" => [
                        "Term GPA for Academic Year: Newname2016-2017",
                        "Population",
                        "N",
                        "Mean",
                        "Standard Deviation",
                        "t-score",
                        "p-value"
                    ],
                    "12" => [
                        "Fall Term of 2016",
                        "NO",
                        9,
                        3.11,
                        1.2,
                        "",
                        ""
                    ],
                    "13" => [
                        "Fall Term of 2016",
                        "YES",
                        0,
                        0,
                        0,
                        "",
                        ""
                    ],
                    "14" => [
                        "long",
                        "NO",
                        9,
                        1.56,
                        0.53,
                        "",
                        ""
                    ],
                    "15" => [
                        "long",
                        "YES",
                        0,
                        0,
                        0,
                        "",
                        ""
                    ],
                    "16" => [
                        ""
                    ]
                ]
            ],
            [ // Example3 extract the data for csv with key as other than factor,gpa and retention
                'factorgpa',
                [
                    "organizationId" => 20,
                    "factor" => [
                        [
                            "factor_name" => "Commitment to the Institution",
                            "t_score" => "-",
                            "p_value" => "-",
                            "display" => 4,
                            "subpopulation1" => [
                                "name" => "NO",
                                "n_value" => 5,
                                "mean_value" => 6.53,
                                "standard_deviation_value" => 1.04
                            ],
                            "subpopulation2" => [
                                "name" => "YES",
                                "n_value" => 0,
                                "mean_value" => 0,
                                "standard_deviation_value" => 0
                            ]
                        ],
                        [
                            "factor_name" => "Communication Skills",
                            "t_score" => "-",
                            "p_value" => "-",
                            "display" => 4,
                            "subpopulation1" => [
                                "name" => "NO",
                                "n_value" => 5,
                                "mean_value" => 5.6,
                                "standard_deviation_value" => 0.65
                            ],
                            "subpopulation2" => [
                                "name" => "YES",
                                "n_value" => 0,
                                "mean_value" => 0,
                                "standard_deviation_value" => 0
                            ]
                        ],
                        [
                            "factor_name" => "Analytical Skills",
                            "t_score" => "-",
                            "p_value" => "-",
                            "display" => 4,
                            "subpopulation1" => [
                                "name" => "NO",
                                "n_value" => 5,
                                "mean_value" => 4.7,
                                "standard_deviation_value" => 0.91
                            ],
                            "subpopulation2" => [
                                "name" => "YES",
                                "n_value" => 0,
                                "mean_value" => 0,
                                "standard_deviation_value" => 0
                            ]
                        ]
                    ],
                    "gpa" => [
                        [
                            "term_name" => "Fall Term of 2016",
                            "t_score" => "-",
                            "p_value" => "-",
                            "display" => 4,
                            "subpopulation1" => [
                                "name" => "NO",
                                "n_value" => 9,
                                "mean_value" => 3.11,
                                "standard_deviation_value" => 1.2
                            ],
                            "subpopulation2" => [
                                "name" => "YES",
                                "n_value" => 0,
                                "mean_value" => 0,
                                "standard_deviation_value" => 0
                            ]
                        ],
                        [
                            "term_name" => "long",
                            "t_score" => "-",
                            "p_value" => "-",
                            "display" => 4,
                            "subpopulation1" => [
                                "name" => "NO",
                                "n_value" => 9,
                                "mean_value" => 1.56,
                                "standard_deviation_value" => 0.53
                            ],
                            "subpopulation2" => [
                                "name" => "YES",
                                "n_value" => 0,
                                "mean_value" => 0,
                                "standard_deviation_value" => 0
                            ]
                        ]
                    ]
                ],
                'Newname2016-2017',
                10,
                [
                    "10" => [
                        "GPA BY TERM for Newname2016-2017"
                    ],
                    "11" => [
                        "Term GPA for Academic Year: Newname2016-2017"
                    ],
                    "12" => [
                        ""
                    ]
                ]
            ],
            [ // Example4 extract the retention data for csv with key as retention_completion
                'retention_completion',
                [
                    "organizationId" => 20,
                    "retention_completion" => [
                        [
                            "retention_completion_variable_name" => "Retained to Midyear Year 1",
                            "chi_square" => 10.5,
                            "p_value" => 0,
                            "display" => 1,
                            "subpopulation1" => [
                                "name" => "Subpop1",
                                "n_value" => 2226,
                                "percentage_retained" => 5.39
                            ],
                            "subpopulation2" => [
                                "name" => "Subpop2",
                                "n_value" => 2835,
                                "percentage_retained" => 1.18
                            ]
                        ],
                        [
                            "retention_completion_variable_name" => "Retained to Start of Year 2",
                            "chi_square" => 10.5,
                            "p_value" => 0,
                            "display" => 1,
                            "subpopulation1" => [
                                "name" => "Subpop1",
                                "n_value" => 2003,
                                "percentage_retained" => 5.39
                            ],
                            "subpopulation2" => [
                                "name" => "Subpop2",
                                "n_value" => 2391,
                                "percentage_retained" => 1.18
                            ]
                        ],
                        [
                            "retention_completion_variable_name" => "Retained to Midyear Year 2",
                            "chi_square" => 10.5,
                            "p_value" => 0,
                            "display" => 2,
                            "subpopulation1" => [
                                "name" => "Subpop1",
                                "n_value" => 1013,
                                "percentage_retained" => 5.39
                            ],
                            "subpopulation2" => [
                                "name" => "Subpop2",
                                "n_value" => 2300,
                                "percentage_retained" => 1.18
                            ]
                        ],
                        [
                            "retention_completion_variable_name" => "Retained to Start of Year 3",
                            "chi_square" => 10.5,
                            "p_value" => 0,
                            "display" => 1,
                            "subpopulation1" => [
                                "name" => "Subpop1",
                                "n_value" => 1500,
                                "percentage_retained" => 5.39
                            ],
                            "subpopulation2" => [
                                "name" => "Subpop2",
                                "n_value" => 939,
                                "percentage_retained" => 1.18
                            ]
                        ],
                        [
                            "retention_completion_variable_name" => "Retained to Midyear Year 3",
                            "chi_square" => 10.5,
                            "p_value" => 0,
                            "display" => 1,
                            "subpopulation1" => [
                                "name" => "Subpop1",
                                "n_value" => 1487,
                                "percentage_retained" => 5.39
                            ],
                            "subpopulation2" => [
                                "name" => "Subpop2",
                                "n_value" => 900,
                                "percentage_retained" => 1.18
                            ]
                        ]
                    ]
                ],
                "Retention Year 2016-2017",
                60,
                [
                    "60" => [
                        "RETENTION/COMPLETION for Retention Tracking Group Retention Year 2016-2017"
                    ],
                    "61" => [
                        "Retention/Completion",
                        "Population",
                        "N",
                        "Percent Retained",
                        "Chi-square",
                        "p-value"
                    ],
                    "62" => [
                        "Retained to Midyear Year 1",
                        "Subpop1",
                        2226,
                        5.39,
                        10.5,
                        ""
                    ],
                    "63" => [
                        "Retained to Midyear Year 1",
                        "Subpop2",
                        2835,
                        1.18,
                        10.5,
                        ""
                    ],
                    "64" => [
                        "Retained to Start of Year 2",
                        "Subpop1",
                        2003,
                        5.39,
                        10.5,
                        ""
                    ],
                    "65" => [
                        "Retained to Start of Year 2",
                        "Subpop2",
                        2391,
                        1.18,
                        10.5,
                        ""
                    ],
                    "66" => [
                        "Retained to Midyear Year 2",
                        "Subpop1",
                        1013,
                        5.39,
                        10.5,
                        ""
                    ],
                    "67" => [
                        "Retained to Midyear Year 2",
                        "Subpop2",
                        2300,
                        1.18,
                        10.5,
                        ""
                    ],
                    "68" => [
                        "Retained to Start of Year 3",
                        "Subpop1",
                        1500,
                        5.39,
                        10.5,
                        ""
                    ],
                    "69" => [
                        "Retained to Start of Year 3",
                        "Subpop2",
                        939,
                        1.18,
                        10.5,
                        ""
                    ],
                    "70" => [
                        "Retained to Midyear Year 3",
                        "Subpop1",
                        1487,
                        5.39,
                        10.5,
                        ""
                    ],
                    "71" => [
                        "Retained to Midyear Year 3",
                        "Subpop2",
                        900,
                        1.18,
                        10.5,
                        ""
                    ],
                    "72" => [
                        ""
                    ]
                ]
            ],
            [ // Example 5 extract the retention data for csv with key as retention_completion but retention data is empty
                'retention_completion',
                [
                    "organizationId" => 20,
                    "retention_completion" => [

                    ]
                ],
                'Newname2016-2017',
                10,
                [
                    "10" => [
                        "RETENTION/COMPLETION for Retention Tracking Group Newname2016-2017"
                    ],
                    "12" => [
                        ""
                    ]
                ]
            ]
        ]]);
    }


    public function testGenerateCompareReportCSV()
    {
        $this->specify('test get csv data', function ($responseJson, $expectedResult) {

            //Create all mocks necessary for Service class creation
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'addWarning'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockDateUtilityService = $this->getMock('DateUtilityService', array('getCurrentFormattedDateTimeForOrganization', 'getFormattedDateTimeForOrganization'));
            $mockCsvUtilityService = $this->getMock('CSVUtilityService', array('generateCSV'));

            $mockContainer->method('get')->willReturnMap(
                [
                    [DateUtilityService::SERVICE_KEY, $mockDateUtilityService],
                    [CSVUtilityService::SERVICE_KEY, $mockCsvUtilityService]
                ]
            );
            $mockDateUtilityService->method('getCurrentFormattedDateTimeForOrganization')->willReturn('20170704_164200');
            $mockDateUtilityService->method('getFormattedDateTimeForOrganization')->willReturn('07/25/2017 at 09:16:50 AM EDT');

            $comparisonReportService = new ComparisonReportService($mockRepositoryResolver, $mockLogger, $mockContainer);

            try {
                $functionResults = $comparisonReportService->generateCompareReportCSV($responseJson);
            } catch (SynapseValidationException $e) {
                $functionResults = $e->getMessage();
            }
            $this->assertEquals($expectedResult, $functionResults);

        }, ['examples' => [
            [
                [ // Example 1 to check the exception message . report_info and search_attribute information is missing from the request json.
                    "request_json" => [
                        "id" => 27316,
                        "report_id" => 18,
                        "organization_id" => 20,
                        "person_id" => 5056928,
                        "report_sections" => [
                            "reportId" => "18",
                            "reportDisable" => false,
                            "report_name" => "Compare",
                            "short_code" => "SUB-COM",
                            "reportDesc" => "Compare outcomes of two student subpopulations. Choose subpopulations by profile item, ISP, survey question or ISQ. Compare survey factors and GPA. Export to csv, print to pdf.",
                            "reportFilterPages" => [
                                [
                                    "reportPage" => "itemType",
                                    "title" => "Select Subpopulation Attribute",
                                    "breadCrumb" => "Select Type",
                                    "visited" => false,
                                    "showHelp" => true
                                ],
                                [
                                    "reportPage" => "treeSelector",
                                    "title" => "Select Profile Item",
                                    "breadCrumbs" => [
                                        [
                                            "type" => "isp",
                                            "text" => "Profile Item"
                                        ],
                                        [
                                            "type" => "isq",
                                            "text" => "Survey Cohort"
                                        ]
                                    ],
                                    "subPages" => [
                                        [
                                            "prefix" => "a",
                                            "reportPage" => "treeSelector",
                                            "title" => "Select one Cohort from one Survey",
                                            "breadCrumb" => "Survey Cohort"
                                        ],
                                        [
                                            "prefix" => "b",
                                            "reportPage" => "surveyQuestion",
                                            "title" => "Select one survey question",
                                            "breadCrumb" => "Survey Question",
                                            "showSearch" => true
                                        ]
                                    ],
                                    "visited" => false,
                                    "showSearch" => true
                                ],
                                [
                                    "reportPage" => "subPopulation",
                                    "title" => "",
                                    "breadCrumb" => "Values",
                                    "visited" => false
                                ]
                            ],
                            "reportFilter" => [
                                "participating" => false,
                                "risk" => true,
                                "active" => true,
                                "activities" => false,
                                "group" => true,
                                "course" => true,
                                "ebi" => true,
                                "isp" => true,
                                "static" => true,
                                "factor" => true,
                                "survey" => true,
                                "isq" => true,
                                "surveyMetadata" => false,
                                "academicTerm" => false,
                                "cohort" => false,
                                "team" => false
                            ],
                            "RequestParam" => "",
                            "pageurl" => "/reports/compare/",
                            "templateUrl" => "/partials/reports/outcomesComparisonReport/modals/report-parameters-modal.html",
                            "controller" => "OutcomeComparisonController",
                            "id" => "18",
                            "report_description" => "Compare outcomes of two student subpopulations. Choose by profile item or survey question, compare survey factors and GPA.",
                            "is_batch_job" => true,
                            "is_coordinator_report" => "n"
                        ]
                    ],
                    "report_items" => [
                    ]
                ],
                "The report does not have required information to generate a CSV file. Please contact Skyfactor Client Services."
            ],
            [
                [ // Example 2 with category type profile item.
                    "request_json" => [
                        "id" => 27316,
                        "report_id" => 18,
                        "organization_id" => 20,
                        "person_id" => 5056928,
                        "mandatory_filters" => [
                            "org_academic_year_id" => [
                                190
                            ],
                            "org_academic_year" => [
                                "year" => [
                                    "id" => 190,
                                    "name" => "Newname2016-2017",
                                    "year_id" => 201617
                                ]
                            ],
                            "gpa_org_academic_year" => [
                                "year" => [
                                    "id" => 190,
                                    "name" => "Newname2016-2017",
                                    "year_id" => 201617
                                ]
                            ],
                            "retention_org_academic_year" => [
                                "year" => [
                                    "id" => 191,
                                    "name" => "Retention Year 2016-2017",
                                    "year_id" => 201617
                                ]
                            ],
                            "latest_survey" => [
                                "name" => "Transition One",
                                "id" => 15
                            ],
                            "latest_cohort" => [
                                "name" => "Survey Cohort 1",
                                "id" => 1
                            ],
                            "group_names" => [
                                "Male",
                                "Female"
                            ],
                            "datablocks" => [
                                "profile_block_id" => 13,
                                "profile_items" => [
                                    "id" => 1,
                                    "display_name" => "Gender",
                                    "item_data_type" => "S",
                                    "item_meta_key" => "Gender",
                                    "calendar_assignment" => "N",
                                    "year_term" => false,
                                    "subpopulation1" => [
                                        "category_type" => [
                                            [
                                                "answer" => "Male",
                                                "value" => "1",
                                                "sequence_no" => 0,
                                                "id" => "",
                                                "subpopulationOneselected" => true
                                            ]
                                        ]
                                    ],
                                    "subpopulation2" => [
                                        "category_type" => [
                                            [
                                                "answer" => "Female",
                                                "value" => "0",
                                                "sequence_no" => 0,
                                                "id" => "",
                                                "subpopulationTwoselected" => true
                                            ]
                                        ]
                                    ]
                                ],
                                "profile_block_name" => "Demographic"
                            ],
                            "isps" => [],
                            "isqs" => [],
                            "survey" => [],
                            "filterText" => [
                                "profileItem",
                                [
                                    "text" => "Gender",
                                    "yearTerm" => ""
                                ],
                                [
                                    "Male",
                                    "Female"
                                ]
                            ]
                        ],
                        "search_attributes" => [
                            "filterCount" => 3,
                            "org_academic_year_id" => 192,
                            "org_academic_terms_id" => [
                                648
                            ],
                            "org_academic_year" => [
                                "year" => [
                                    "id" => 192,
                                    "name" => "2016-2017",
                                    "year_id" => "201617",
                                    "start_date" => "2016-06-22",
                                    "end_date" => "2018-05-05",
                                    "can_delete" => false,
                                    "is_current_year" => true
                                ]
                            ],
                            "risk_indicator_date" => "",
                            "risk_indicator_ids" => "",
                            "student_status" => [
                                "status_value" => [],
                                "org_academic_year_id" => []
                            ],
                            "group_ids" => "",
                            "group_names" => [],
                            "courses" => [],
                            "datablocks" => [],
                            "isps" => [],
                            "static_list_ids" => "",
                            "static_list_names" => [],
                            "survey_status" => [],
                            "retention_completion" => [],
                            "participating" => [
                                "participating_value" => [
                                    1
                                ],
                                "org_academic_year_id" => [
                                    192
                                ]
                            ],
                            "cohort_ids" => "",
                            "cohort_names" => [],
                            "cohort_filter" => [
                                "cohorts" => [],
                                "org_academic_year_id" => 192,
                                "org_academic_year_name" => "2016-2017"
                            ],
                            "filterText" => "Participating Students  =>  (2016-2017) => Yes"
                        ],
                        "report_sections" => [
                            "reportId" => "18",
                            "reportDisable" => false,
                            "report_name" => "Compare",
                            "short_code" => "SUB-COM",
                            "reportDesc" => "Compare outcomes of two student subpopulations. Choose subpopulations by profile item, ISP, survey question or ISQ. Compare survey factors and GPA. Export to csv, print to pdf.",
                            "reportFilterPages" => [
                                [
                                    "reportPage" => "itemType",
                                    "title" => "Select Subpopulation Attribute",
                                    "breadCrumb" => "Select Type",
                                    "visited" => false,
                                    "showHelp" => true
                                ],
                                [
                                    "reportPage" => "treeSelector",
                                    "title" => "Select Profile Item",
                                    "breadCrumbs" => [
                                        [
                                            "type" => "isp",
                                            "text" => "Profile Item"
                                        ],
                                        [
                                            "type" => "isq",
                                            "text" => "Survey Cohort"
                                        ]
                                    ],
                                    "subPages" => [
                                        [
                                            "prefix" => "a",
                                            "reportPage" => "treeSelector",
                                            "title" => "Select one Cohort from one Survey",
                                            "breadCrumb" => "Survey Cohort"
                                        ],
                                        [
                                            "prefix" => "b",
                                            "reportPage" => "surveyQuestion",
                                            "title" => "Select one survey question",
                                            "breadCrumb" => "Survey Question",
                                            "showSearch" => true
                                        ]
                                    ],
                                    "visited" => false,
                                    "showSearch" => true
                                ],
                                [
                                    "reportPage" => "subPopulation",
                                    "title" => "",
                                    "breadCrumb" => "Values",
                                    "visited" => false
                                ]
                            ],
                            "reportFilter" => [
                                "participating" => false,
                                "risk" => true,
                                "active" => true,
                                "activities" => false,
                                "group" => true,
                                "course" => true,
                                "ebi" => true,
                                "isp" => true,
                                "static" => true,
                                "factor" => true,
                                "survey" => true,
                                "isq" => true,
                                "surveyMetadata" => false,
                                "academicTerm" => false,
                                "cohort" => false,
                                "team" => false
                            ],
                            "RequestParam" => "",
                            "pageurl" => "/reports/compare/",
                            "templateUrl" => "/partials/reports/outcomesComparisonReport/modals/report-parameters-modal.html",
                            "controller" => "OutcomeComparisonController",
                            "id" => "18",
                            "report_description" => "Compare outcomes of two student subpopulations. Choose by profile item or survey question, compare survey factors and GPA.",
                            "is_batch_job" => true,
                            "is_coordinator_report" => "n"
                        ]
                    ],
                    "report_info" => [
                        "reports_id" => 18,
                        "report_name" => "Compare",
                        "short_code" => "SUB-COM",
                        "report_instance_id" => 27316,
                        "report_date" => "2017-07-25T13:16:50+0000",
                        "report_by" => [
                            "first_name" => "retention1",
                            "last_name" => "faculty"
                        ]
                    ],
                    "report_items" => [
                        "organizationId" => 20,
                        "factor" => [
                            [
                                "factor_name" => "Commitment to the Institution",
                                "t_score" => "-1",
                                "p_value" => "0.391",
                                "display" => 3,
                                "subpopulation1" => [
                                    "name" => "Male",
                                    "n_value" => 2,
                                    "mean_value" => 5.84,
                                    "standard_deviation_value" => 1.65
                                ],
                                "subpopulation2" => [
                                    "name" => "Female",
                                    "n_value" => 3,
                                    "mean_value" => 7,
                                    "standard_deviation_value" => 0
                                ]
                            ],
                            [
                                "factor_name" => "Communication Skills",
                                "t_score" => "1.51",
                                "p_value" => "0.228",
                                "display" => 3,
                                "subpopulation1" => [
                                    "name" => "Male",
                                    "n_value" => 2,
                                    "mean_value" => 6,
                                    "standard_deviation_value" => 0
                                ],
                                "subpopulation2" => [
                                    "name" => "Female",
                                    "n_value" => 3,
                                    "mean_value" => 5.33,
                                    "standard_deviation_value" => 0.76
                                ]
                            ],
                            [
                                "factor_name" => "Analytical Skills",
                                "t_score" => "0.11",
                                "p_value" => "0.92",
                                "display" => 3,
                                "subpopulation1" => [
                                    "name" => "Male",
                                    "n_value" => 2,
                                    "mean_value" => 4.75,
                                    "standard_deviation_value" => 0.35
                                ],
                                "subpopulation2" => [
                                    "name" => "Female",
                                    "n_value" => 3,
                                    "mean_value" => 4.67,
                                    "standard_deviation_value" => 1.26
                                ]
                            ],
                            [
                                "factor_name" => "Self-Discipline",
                                "t_score" => "-1.22",
                                "p_value" => "0.309",
                                "display" => 3,
                                "subpopulation1" => [
                                    "name" => "Male",
                                    "n_value" => 2,
                                    "mean_value" => 5.17,
                                    "standard_deviation_value" => 0.23
                                ],
                                "subpopulation2" => [
                                    "name" => "Female",
                                    "n_value" => 3,
                                    "mean_value" => 6,
                                    "standard_deviation_value" => 1.15
                                ]
                            ]
                        ],
                        "gpa" => [
                            [
                                "term_name" => "Fall Term of 2016",
                                "t_score" => 0.07,
                                "p_value" => 0.947,
                                "display" => 3,
                                "subpopulation1" => [
                                    "name" => "Male",
                                    "n_value" => 2,
                                    "mean_value" => 3.13,
                                    "standard_deviation_value" => 0.09
                                ],
                                "subpopulation2" => [
                                    "name" => "Female",
                                    "n_value" => 7,
                                    "mean_value" => 3.1,
                                    "standard_deviation_value" => 1.39
                                ]
                            ],
                            [
                                "term_name" => "long",
                                "t_score" => 2.83,
                                "p_value" => 0.025,
                                "display" => 1,
                                "subpopulation1" => [
                                    "name" => "Male",
                                    "n_value" => 2,
                                    "mean_value" => 2,
                                    "standard_deviation_value" => 0
                                ],
                                "subpopulation2" => [
                                    "name" => "Female",
                                    "n_value" => 7,
                                    "mean_value" => 1.43,
                                    "standard_deviation_value" => 0.53
                                ]
                            ]
                        ]
                    ]
                ],
                "20-compare_report_20170704_164200.csv"
            ],
            [ // Example 3 with category type survey question
                [
                    "request_json" => [
                        "id" => 27437,
                        "report_id" => 18,
                        "organization_id" => 80,
                        "person_id" => 5056928,
                        "mandatory_filters" => [
                            "org_academic_year_id" => [
                                190
                            ],
                            "org_academic_year" => [
                                "year" => [
                                    "id" => 190,
                                    "name" => "Newname2016-2017",
                                    "year_id" => 201617
                                ]
                            ],
                            "gpa_org_academic_year" => [
                                "year" => [
                                    "id" => 190,
                                    "name" => "Newname2016-2017",
                                    "year_id" => 201617
                                ]
                            ],
                            "retention_org_academic_year" => [
                                "year" => [
                                    "id" => 191,
                                    "name" => "Retention Year 2016-2017",
                                    "year_id" => 201617
                                ]
                            ],
                            "latest_survey" => [
                                "name" => "Transition One",
                                "id" => 15
                            ],
                            "latest_cohort" => [
                                "name" => "Survey Cohort 1",
                                "id" => 1
                            ],
                            "group_names" => [
                                "Completely disagree",
                                "Disagree somewhat"
                            ],
                            "datablocks" => [],
                            "isps" => [],
                            "isqs" => [
                                "survey_id" => "11",
                                "year_id" => "201516",
                                "question_id" => 2397,
                                "type" => "category",
                                "cohort" => "2",
                                "subpopulation1" => [
                                    "category_type" => [
                                        [
                                            "id" => 33565,
                                            "answer" => "Completely disagree",
                                            "value" => "1",
                                            "subpopulationOneselected" => true
                                        ]
                                    ]
                                ],
                                "subpopulation2" => [
                                    "category_type" => [
                                        [
                                            "id" => 33566,
                                            "answer" => "Disagree somewhat",
                                            "value" => "2",
                                            "subpopulationTwoselected" => true
                                        ]
                                    ]
                                ]
                            ],
                            "survey" => [],
                            "filterText" => [
                                "isq",
                                [
                                    "text" => "Organization =>  020 Question ID =>  02397",
                                    "yearTerm" => ""
                                ],
                                [
                                    "Completely disagree",
                                    "Disagree somewhat"
                                ]
                            ]
                        ],
                        "report_sections" => [
                            "reportId" => "18",
                            "reportDisable" => false,
                            "report_name" => "Compare",
                            "short_code" => "SUB-COM",
                            "reportDesc" => "Compare outcomes of two student subpopulations. Choose subpopulations by profile item, ISP, survey question or ISQ. Compare survey factors and GPA. Export to csv, print to pdf.",
                            "reportFilterPages" => [
                                [
                                    "reportPage" => "itemType",
                                    "title" => "Select Subpopulation Attribute",
                                    "breadCrumb" => "Select Type",
                                    "visited" => false,
                                    "showHelp" => true
                                ],
                                [
                                    "reportPage" => "treeSelector",
                                    "title" => "Select Profile Item",
                                    "breadCrumbs" => [
                                        [
                                            "type" => "isp",
                                            "text" => "Profile Item"
                                        ],
                                        [
                                            "type" => "isq",
                                            "text" => "Survey Cohort"
                                        ]
                                    ],
                                    "subPages" => [
                                        [
                                            "prefix" => "a",
                                            "reportPage" => "treeSelector",
                                            "title" => "Select one Cohort from one Survey",
                                            "breadCrumb" => "Survey Cohort"
                                        ],
                                        [
                                            "prefix" => "b",
                                            "reportPage" => "surveyQuestion",
                                            "title" => "Select one survey question",
                                            "breadCrumb" => "Survey Question",
                                            "showSearch" => true
                                        ]
                                    ],
                                    "visited" => false,
                                    "showSearch" => true
                                ],
                                [
                                    "reportPage" => "subPopulation",
                                    "title" => "",
                                    "breadCrumb" => "Values",
                                    "visited" => false
                                ]
                            ],
                            "reportFilter" => [
                                "participating" => false,
                                "risk" => true,
                                "active" => true,
                                "activities" => false,
                                "group" => true,
                                "course" => true,
                                "ebi" => true,
                                "isp" => true,
                                "static" => true,
                                "factor" => true,
                                "survey" => true,
                                "isq" => true,
                                "surveyMetadata" => false,
                                "academicTerm" => false,
                                "cohort" => false,
                                "team" => false
                            ],
                            "RequestParam" => "",
                            "pageurl" => "/reports/compare/",
                            "templateUrl" => "/partials/reports/outcomesComparisonReport/modals/report-parameters-modal.html",
                            "controller" => "OutcomeComparisonController",
                            "id" => "18",
                            "report_description" => "Compare outcomes of two student subpopulations. Choose by profile item or survey question, compare survey factors and GPA.",
                            "is_batch_job" => true,
                            "is_coordinator_report" => "n"
                        ]
                    ],
                    "report_info" => [
                        "report_id" => 18,
                        "report_name" => "Compare",
                        "short_code" => "SUB-COM",
                        "report_instance_id" => 27437,
                        "report_date" => "2017-07-25T13:16:50+0000",
                        "report_by" => [
                            "first_name" => "retention1",
                            "last_name" => "faculty"
                        ]
                    ],
                    "status_message" => [
                        "code" => "R1002",
                        "description" => "There are no students that fit your selected criteria. Please refine your criteria and run your report again."
                    ],
                    "report_items" => [
                        "organizationId" => 20,
                        "factor" => [],
                        "gpa" => []
                    ]
                ],
                "80-compare_report_20170704_164200.csv"
            ],
            [ // Example 4 with category type ISP data
                [
                    "request_json" => [
                        "id" => 27436,
                        "report_id" => 18,
                        "organization_id" => 20,
                        "person_id" => 5056928,
                        "mandatory_filters" => [
                            "org_academic_year_id" => [
                                190
                            ],
                            "org_academic_year" => [
                                "year" => [
                                    "id" => 190,
                                    "name" => "Newname2016-2017",
                                    "year_id" => 201617
                                ]
                            ],
                            "gpa_org_academic_year" => [
                                "year" => [
                                    "id" => 190,
                                    "name" => "Newname2016-2017",
                                    "year_id" => 201617
                                ]
                            ],
                            "retention_org_academic_year" => [
                                "year" => [
                                    "id" => 191,
                                    "name" => "Retention Year 2016-2017",
                                    "year_id" => 201617
                                ]
                            ],
                            "latest_survey" => [
                                "name" => "Transition One",
                                "id" => 15
                            ],
                            "latest_cohort" => [
                                "name" => "Survey Cohort 1",
                                "id" => 1
                            ],
                            "group_names" => [
                                "NO",
                                "YES"
                            ],
                            "datablocks" => [],
                            "isps" => [
                                "id" => 5934,
                                "display_name" => "Organization =>  020 Metadata ID =>  005934",
                                "item_data_type" => "S",
                                "item_meta_key" => "Gender",
                                "calendar_assignment" => "N",
                                "year_term" => false,
                                "subpopulation1" => [
                                    "category_type" => [
                                        [
                                            "answer" => "No",
                                            "value" => "0",
                                            "id" => "",
                                            "subpopulationOneselected" => true
                                        ]
                                    ]
                                ],
                                "subpopulation2" => [
                                    "category_type" => [
                                        [
                                            "answer" => "Yes",
                                            "value" => "1",
                                            "id" => "",
                                            "subpopulationTwoselected" => true
                                        ]
                                    ]
                                ]
                            ],
                            "isqs" => [],
                            "survey" => [],
                            "filterText" => [
                                "isp",
                                [
                                    "text" => "Organization =>  020 Metadata ID =>  005934",
                                    "yearTerm" => ""
                                ],
                                [
                                    "NO",
                                    "YES"
                                ]
                            ]
                        ],
                        "report_sections" => [
                            "reportId" => "18",
                            "reportDisable" => false,
                            "report_name" => "Compare",
                            "short_code" => "SUB-COM",
                            "reportDesc" => "Compare outcomes of two student subpopulations. Choose subpopulations by profile item, ISP, survey question or ISQ. Compare survey factors and GPA. Export to csv, print to pdf.",
                            "reportFilterPages" => [
                                [
                                    "reportPage" => "itemType",
                                    "title" => "Select Subpopulation Attribute",
                                    "breadCrumb" => "Select Type",
                                    "visited" => false,
                                    "showHelp" => true
                                ],
                                [
                                    "reportPage" => "treeSelector",
                                    "title" => "Select Profile Item",
                                    "breadCrumbs" => [
                                        [
                                            "type" => "isp",
                                            "text" => "Profile Item"
                                        ],
                                        [
                                            "type" => "isq",
                                            "text" => "Survey Cohort"
                                        ]
                                    ],
                                    "subPages" => [
                                        [
                                            "prefix" => "a",
                                            "reportPage" => "treeSelector",
                                            "title" => "Select one Cohort from one Survey",
                                            "breadCrumb" => "Survey Cohort"
                                        ],
                                        [
                                            "prefix" => "b",
                                            "reportPage" => "surveyQuestion",
                                            "title" => "Select one survey question",
                                            "breadCrumb" => "Survey Question",
                                            "showSearch" => true
                                        ]
                                    ],
                                    "visited" => false,
                                    "showSearch" => true
                                ],
                                [
                                    "reportPage" => "subPopulation",
                                    "title" => "",
                                    "breadCrumb" => "Values",
                                    "visited" => false
                                ]
                            ],
                            "reportFilter" => [
                                "participating" => false,
                                "risk" => true,
                                "active" => true,
                                "activities" => false,
                                "group" => true,
                                "course" => true,
                                "ebi" => true,
                                "isp" => true,
                                "static" => true,
                                "factor" => true,
                                "survey" => true,
                                "isq" => true,
                                "surveyMetadata" => false,
                                "academicTerm" => false,
                                "cohort" => false,
                                "team" => false
                            ],
                            "RequestParam" => "",
                            "pageurl" => "/reports/compare/",
                            "templateUrl" => "/partials/reports/outcomesComparisonReport/modals/report-parameters-modal.html",
                            "controller" => "OutcomeComparisonController",
                            "id" => "18",
                            "report_description" => "Compare outcomes of two student subpopulations. Choose by profile item or survey question, compare survey factors and GPA.",
                            "is_batch_job" => true,
                            "is_coordinator_report" => "n"
                        ]
                    ],
                    "report_info" => [
                        "reports_id" => 18,
                        "report_name" => "Compare",
                        "short_code" => "SUB-COM",
                        "report_instance_id" => 27436,
                        "report_date" => "2017-06-26T13:16:50+0000",
                        "report_by" => [
                            "first_name" => "retention1",
                            "last_name" => "faculty"
                        ]
                    ],
                    "report_items" => [
                        "organizationId" => 20,
                        "factor" => [
                            [
                                "factor_name" => "Commitment to the Institution",
                                "t_score" => "-",
                                "p_value" => "-",
                                "display" => 4,
                                "subpopulation1" => [
                                    "name" => "NO",
                                    "n_value" => 5,
                                    "mean_value" => 6.53,
                                    "standard_deviation_value" => 1.04
                                ],
                                "subpopulation2" => [
                                    "name" => "YES",
                                    "n_value" => 0,
                                    "mean_value" => 0,
                                    "standard_deviation_value" => 0
                                ]
                            ],
                            [
                                "factor_name" => "Communication Skills",
                                "t_score" => "-",
                                "p_value" => "-",
                                "display" => 4,
                                "subpopulation1" => [
                                    "name" => "NO",
                                    "n_value" => 5,
                                    "mean_value" => 5.6,
                                    "standard_deviation_value" => 0.65
                                ],
                                "subpopulation2" => [
                                    "name" => "YES",
                                    "n_value" => 0,
                                    "mean_value" => 0,
                                    "standard_deviation_value" => 0
                                ]
                            ]
                        ],
                        "gpa" => [
                            [
                                "term_name" => "Fall Term of 2016",
                                "t_score" => "-",
                                "p_value" => "-",
                                "display" => 4,
                                "subpopulation1" => [
                                    "name" => "NO",
                                    "n_value" => 9,
                                    "mean_value" => 3.11,
                                    "standard_deviation_value" => 1.2
                                ],
                                "subpopulation2" => [
                                    "name" => "YES",
                                    "n_value" => 0,
                                    "mean_value" => 0,
                                    "standard_deviation_value" => 0
                                ]
                            ],
                            [
                                "term_name" => "long",
                                "t_score" => "-",
                                "p_value" => "-",
                                "display" => 4,
                                "subpopulation1" => [
                                    "name" => "NO",
                                    "n_value" => 9,
                                    "mean_value" => 1.56,
                                    "standard_deviation_value" => 0.53
                                ],
                                "subpopulation2" => [
                                    "name" => "YES",
                                    "n_value" => 0,
                                    "mean_value" => 0,
                                    "standard_deviation_value" => 0
                                ]
                            ]
                        ]
                    ]
                ],
                "20-compare_report_20170704_164200.csv"
            ]

        ]]);
    }


    public function testGetCSVPreliminaryRows()
    {
        $this->specify('test get csv preliminary data', function ($filterTextArray, $selectedIspOrIsqBlocks, $expectedResult) {

            //Create all mocks necessary for Service class creation
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'addWarning'));
            $mockContainer = $this->getMock('Container', array('get'));

            $comparisonReportService = new ComparisonReportService($mockRepositoryResolver, $mockLogger, $mockContainer);

            $functionResults = $comparisonReportService->getCSVPreliminaryRows($filterTextArray, $selectedIspOrIsqBlocks);

            $this->assertEquals($expectedResult, $functionResults);

        }, ['examples' => [
            [ // Example 1 profile item with category type data

                [
                    "profileItem",
                    [
                        "text" => "Gender",
                        "yearTerm" => ""
                    ],
                    [
                        "Male",
                        "Female"
                    ]
                ],


                [
                    "id" => 1,
                    "display_name" => "Gender",
                    "item_data_type" => "S",
                    "item_meta_key" => "Gender",
                    "calendar_assignment" => "N",
                    "year_term" => false,
                    "subpopulation1" => [
                        "category_type" => [
                            [
                                "answer" => "Male",
                                "value" => "1",
                                "sequence_no" => 0,
                                "id" => "",
                                "subpopulationOneselected" => true
                            ]
                        ]
                    ],
                    "subpopulation2" => [
                        "category_type" => [
                            [
                                "answer" => "Female",
                                "value" => "0",
                                "sequence_no" => 0,
                                "id" => "",
                                "subpopulationTwoselected" => true
                            ]
                        ]
                    ]
                ],
                [
                    "selected_text" => "Selected Profile Item : Gender",
                    "subpopulation1_text" => "Subpopulation 1: Male, defined as: Male",
                    "subpopulation2_text" => "Subpopulation 2: Female, defined as: Female"
                ]

            ],
            [ // Example 2 profile item with number type meta data
                [
                    "profileItem",
                    [
                        "text" => "EndTermGPA",
                        "yearTerm" => "Newname2016-2017,Fall Term of 2016"
                    ],
                    [
                        "1",
                        "2"
                    ]

                ],
                [
                    "id" => 83,
                    "display_name" => "EndTermGPA",
                    "item_data_type" => "N",
                    "item_meta_key" => "Gender",
                    "calendar_assignment" => "T",
                    "year_term" => [
                        "year_id" => "201617",
                        "year_name" => "Newname2016-2017",
                        "term_name" => "Fall Term of 2016"
                    ],
                    "subpopulation1" => [
                        "is_single" => true,
                        "single_value" => 1
                    ],
                    "subpopulation2" => [
                        "is_single" => false,
                        "min_digits" => 2,
                        "max_digits" => 3
                    ]
                ],
                [
                    "selected_text" => "Selected Profile Item [for Newname2016-2017,Fall Term of 2016]: EndTermGPA",
                    "subpopulation1_text" => "Subpopulation 1: 1, defined as: 1",
                    "subpopulation2_text" => "Subpopulation 2: 2, defined as: between 2 and 3"
                ]
            ],
            [ // Example 3 profile item with date type meta data
                [
                    "profileItem",
                    [
                        "text" => "ApplicationDate",
                        "yearTerm" => ""
                    ],
                    [
                        "d1",
                        "d2"
                    ]

                ],
                [
                    "id" => 48,
                    "display_name" => "ApplicationDate",
                    "item_data_type" => "D",
                    "item_meta_key" => "Gender",
                    "calendar_assignment" => "N",
                    "year_term" => false,
                    "subpopulation1" => [
                        "start_date" => "06/20/2017",
                        "end_date" => "06/22/2017"
                    ],
                    "subpopulation2" => [
                        "start_date" => "06/06/2017",
                        "end_date" => "06/14/2017"
                    ]
                ],
                [
                    "selected_text" => "Selected Profile Item : ApplicationDate",
                    "subpopulation1_text" => "Subpopulation 1: d1, defined as: 06/20/2017 and 06/22/2017",
                    "subpopulation2_text" => "Subpopulation 2: d2, defined as: 06/06/2017 and 06/14/2017"
                ]
            ],
            [ // Example 4 ISQ item with category type meta data
                [
                    "isq",
                    [
                        "text" => "Organization =>  020 Question ID =>  05804",
                        "yearTerm" => ""
                    ],
                    [
                        "Major",
                        "Minor"
                    ]
                ],
                [
                    "survey_id" => "15",
                    "year_id" => "201617",
                    "question_id" => 5804,
                    "type" => "category",
                    "cohort" => "1",
                    "subpopulation1" => [
                        "category_type" => [
                            [
                                "id" => 46763,
                                "answer" => "I have decided on a Major & Career",
                                "value" => "1",
                                "subpopulationOneselected" => true
                            ],
                            [
                                "id" => 46765,
                                "answer" => "I have decided on a Career but not a Major",
                                "value" => "3",
                                "subpopulationOneselected" => true
                            ]
                        ]
                    ],
                    "subpopulation2" => [
                        "category_type" => [
                            [
                                "id" => 46764,
                                "answer" => "I have decided on a Major but not a Career",
                                "value" => "2",
                                "subpopulationTwoselected" => true
                            ],
                            [
                                "id" => 46766,
                                "answer" => "I have not decided on a Major or a Career, but I have narrowed my choices to a few similar options in one specific field",
                                "value" => "4",
                                "subpopulationTwoselected" => true
                            ]
                        ]
                    ]
                ],
                [
                    "selected_text" => "Selected Institution-Specific Survey Question : Organization =>  020 Question ID =>  05804",
                    "subpopulation1_text" => "Subpopulation 1: Major, defined as: I have decided on a Major & Career, I have decided on a Career but not a Major",
                    "subpopulation2_text" => "Subpopulation 2: Minor, defined as: I have decided on a Major but not a Career, I have not decided on a Major or a Career, but I have narrowed my choices to a few similar options in one specific field"
                ]

            ]
        ]]);
    }


    public function testGenerateReport()
    {
        $this->specify('test generate report', function ($expectedResult, $functionResultType, $searchAttributes, $filteredStudentIds, $override = '') {

            //Create all mocks necessary for Service class creation
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'addWarning', 'addError'));
            $mockContainer = $this->getMock('Container', array('get'));

            //Repositories that will be mocked away
            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', array('find'));
            $mockPersonEbiMetaDataRepository = $this->getMock('PersonEbiMetaDataRepository', array('getFactorDataForProfileblock', 'getGpaDataForProfileblock', 'getRetentionDataForProfileBlock'));
            $mockPersonOrgMetaDataRepository = $this->getMock('PersonOrgMetaDataRepository', array('getFactorDataForIsp', 'getGpaDataForIsp', 'getRetentionDataForISP'));
            $mockPersonRepository = $this->getMock('PersonRepository', array('find'));
            $mockReportsRunningJsonRepository = $this->getMock('ReportsRunningJsonRepository', array('persist', 'flush'));
            $mockReportsRunningStatusRepository = $this->getMock('ReportsRunningStatusRepository', array('find', 'flush', 'persist'));
            $mockReportsRepository = $this->getMock('ReportsRepository', array('find'));

            // Survey Repository
            $mockOrgQuestionResponseRepository = $this->getMock('OrgQuestionResponseRepository', array('getGPAdataForISQ', 'getFactorDataForISQ', 'getRetentionDataForISQ'));
            $mockSurveyResponseRepository = $this->getMock('SurveyResponseRepository', array('getGPAdataForSurvey', 'getFactorDataForSurvey', 'createCaseQueryForISQorSurvey', 'getRetentionDataForSurvey'));

            //Services that will be mocked away
            $mockAcademicYearService = $this->getMock('AcademicYearService', array('validateAcademicYear'));
            $mockAlertNotificationsService = $this->getMock('AlertNotificationsService', array('createReportNotification'));
            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get'));
            $mockNotificationChannelService = $this->getMock('NotificationChannelService', array('sendNotificationToAllRegisteredChannels'));
            $mockSerializer = $this->getMock('Serializer', array('serialize'));

            //Objects that will be mocked away
            $mockReportRunningStatusObj = $this->getMock('ReportRunningStatus', array('getFilteredStudentIds', 'setStatus', 'setResponseJson', 'getCreatedAt'));
            $mockDateObj = $this->getMock('DateTime', array('getTimestamp', 'format'));
            $mockReportRunningStatusObj->method('getCreatedAt')->willReturn($mockDateObj);
            $mockDateObj->method('getTimestamp')->willReturn(123456789);

            $mockComparisionReportDAO = $this->getMock('ComparisonReportDAO', array('executeRscriptJob', 'createCaseQueryForIspOrProfileblock'));

            //mocking away all function calls outside of the tested function
            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [OrgAcademicYearRepository::REPOSITORY_KEY, $mockOrgAcademicYearRepository],
                    [OrgQuestionResponseRepository::REPOSITORY_KEY, $mockOrgQuestionResponseRepository],
                    [PersonEbiMetaDataRepository::REPOSITORY_KEY, $mockPersonEbiMetaDataRepository],
                    [PersonOrgMetaDataRepository::REPOSITORY_KEY, $mockPersonOrgMetaDataRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [ReportsRepository::REPOSITORY_KEY, $mockReportsRepository],
                    [ReportsRunningJsonRepository::REPOSITORY_KEY, $mockReportsRunningJsonRepository],
                    [ReportsRunningStatusRepository::REPOSITORY_KEY, $mockReportsRunningStatusRepository],
                    [SurveyResponseRepository::REPOSITORY_KEY, $mockSurveyResponseRepository]

                ]
            );

            $mockContainer->method('get')->willReturnMap(
                [
                    [AcademicYearService::SERVICE_KEY, $mockAcademicYearService],
                    [AlertNotificationsService::SERVICE_KEY, $mockAlertNotificationsService],
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [ComparisonReportDAO::DAO_KEY, $mockComparisionReportDAO],
                    [DateTime::class, $mockDateObj],
                    [NotificationChannelService::SERVICE_KEY, $mockNotificationChannelService],
                    [SynapseConstant::JMS_SERIALIZER_CLASS_KEY, $mockSerializer],
                ]
            );

            $reportRunningStatusDto = $this->buildReportRunningDto(59, $searchAttributes, $this->sections);

            $mockReportObject = $this->getMock('Reports', array('getId', 'getName', 'getShortCode'));
            $mockReportObject->method('getName')->willReturn('Compare');
            $mockReportObject->method('getShortCode')->willReturn('SUB-COM');

            if ($override == 'reportObject') {
                $mockReportsRepository->method('find')->willReturn(null);
            } else {
                $mockReportsRepository->method('find')->willReturn($mockReportObject);
            }

            $mockPersonObject = $this->getMock('Person', array('getId', 'getFirstName', 'getLastName'));
            $mockPersonObject->method('getFirstName')->willReturn('test123');
            $mockPersonObject->method('getLastName')->willReturn('test456');

            if ($override == 'personObject') {
                $mockPersonRepository->method('find')->willReturn(null);
            } else {
                $mockPersonRepository->method('find')->willReturn($mockPersonObject);
            }

            $mockReportsRunningStatusEntity = $this->getMock('Synapse\ReportsBundle\Entity\ReportsRunningStatus', array('getId', 'getFilteredStudentIds', 'setStatus', 'setResponseJson', 'getCreatedAt'));
            $mockReportsRunningStatusEntity->method('setId')->willReturn(1);
            $mockReportsRunningStatusEntity->method('setStatus')->willReturn('Q');
            $mockReportsRunningStatusEntity->method('getFilteredStudentIds')->willReturn($filteredStudentIds);
            $mockReportsRunningStatusEntity->method('setResponseJson')->willReturn([]);
            $mockReportsRunningStatusEntity->method('getCreatedAt')->willReturn($mockDateObj);

            if ($override == 'reportRuningStatusObject') {
                $mockReportsRunningStatusRepository->method('find')->willReturn(null);
            } else {
                $mockReportsRunningStatusRepository->method('find')->willReturn($mockReportsRunningStatusEntity);
            }

            $mockReportsRunningStatusRepository->method('persist')->willReturn(true);


            $mockReportsRunningJsonObject = new \Synapse\ReportsBundle\Entity\ReportsRunningJson();

            if ($override == 'queryReturn') {
                $queryReturn = [];
            } else {
                $queryReturn = ['stuff' => 'stuff'];
            }


            if (!empty($searchAttributes['datablocks'])) {
                $mockPersonEbiMetaDataRepository->method('getFactorDataForProfileblock')->willReturn($queryReturn);
                $mockPersonEbiMetaDataRepository->method('getGpaDataForProfileblock')->willReturn($queryReturn);
                $mockPersonEbiMetaDataRepository->method('getRetentionDataForProfileBlock')->willReturn($queryReturn);
            } else if (!empty($searchAttributes['isps'])) {
                $mockPersonOrgMetaDataRepository->method('getIspGpaAndFactorData')->willReturn($queryReturn);
            }

            if (!empty($searchAttributes['isqs'])) {
                $mockOrgQuestionResponseRepository->method('getGPAdataForISQ')->willReturn($queryReturn);
                $mockOrgQuestionResponseRepository->method('getFactorDataForISQ')->willReturn($queryReturn);
                $mockOrgQuestionResponseRepository->method('getRetentionDataForISQ')->willReturn($queryReturn);
            }

            if (!empty($searchAttributes['survey'])) {
                $mockSurveyResponseRepository->method('getGPAdataForSurvey')->willReturn($queryReturn);
                $mockSurveyResponseRepository->method('getFactorDataForSurvey')->willReturn($queryReturn);
                $mockSurveyResponseRepository->method('getRetentionDataForSurvey')->willReturn($queryReturn);
            }

            $mockSerializer->method('serialize')->willReturnCallback(function ($inputData) {
                return json_encode($inputData);
            });

            $mockEbiConfigService->method('get')->willReturn(1);

            if ($override == 'rScriptFailure') {
                $mockComparisionReportDAO->method('executeRscriptJob')->willReturn('NULL');
            } else {
                $mockComparisionReportDAO->method('executeRscriptJob')->willReturn(1);

            }


            $mockNotificationChannelService->method('sendNotificationToAllRegisteredChannels')->willReturn(1);

            $comparisonReportService = new ComparisonReportService($mockRepositoryResolver, $mockLogger, $mockContainer);

            $functionResults = $comparisonReportService->generateReport($reportRunningStatusDto->getId(), $reportRunningStatusDto);

            if ($functionResultType == 'error') {
                $this->assertEquals($expectedResult, $functionResults['error']);
            } else if ($functionResultType == 'status') {
                $this->assertEquals($expectedResult, $functionResults['status_message']);
            } else {
                $this->assertEquals($expectedResult, $functionResults);
            }

        }, ['examples' => [
            //Reports Running Status Object is Empty

            [
                'The reports running status object is not defined',
                'error',
                $this->standardSearchAttributes,
                '1, 2, 3, 5',
                'reportRuningStatusObject'

            ],
            // Person Object is not defined
            [
                'The person object is not defined',
                'error',
                $this->standardSearchAttributes,
                '1, 2, 3, 5',
                'personObject'
            ],
            //Reports Object is not defined
            [
                'The report object is not defined',
                'error',
                $this->standardSearchAttributes,
                '1, 2, 3, 5',
                'reportObject'
            ],
            //Mandatory Filters have not been defined
            [
                'Compare report mandatory search attributes are not defined',
                'error',
                [],
                '1, 2, 3, 5',
                ''

            ],
            //Subpopulation names are empty
            [
                'Sub population group names must not be empty',
                'error',
                ["group_names" => []],
                '1, 2, 3, 5',
                ''
            ],
            //Optional Filters are too restrictive
            [
                [
                    'code' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_CODE,
                    'description' => ReportsConstants::REPORT_OPTIONAL_FILTERS_TOO_RESTRICTIVE_MESSAGE
                ],
                'status',
                $this->standardSearchAttributes,
                '',
                ''
            ],
            //No Data Returned From Queries
            [
                [
                    'code' => ReportsConstants::REPORT_NO_DATA_CODE,
                    'description' => ReportsConstants::REPORT_NO_DATA_MESSAGE
                ],
                'status',
                $this->standardSearchAttributes,
                '1,2,3,4,5',
                'queryReturn'
            ],

            //Successful Report
            [
                [
                    'request_json' => $this->buildReportRunningDto(59, $this->standardSearchAttributes, $this->sections),
                    'report_info' => ['report_id' => 18,
                        'report_name' => 'Compare',
                        'short_code' => 'SUB-COM',
                        'report_instance_id' => 1,
                        'report_date' => '1973-11-29T21:33:09+0000',
                        'report_by' => [
                            'first_name' => 'test123',
                            'last_name' => 'test456'
                        ]
                    ]
                ],
                '',
                $this->standardSearchAttributes,
                '1,2,3,4,5',
                ''
            ],
            //R Script Error
            [
                'There has been a failure in the script that calculates the values in this report. Please contact Mapworks Client Services.',
                'error',
                $this->standardSearchAttributes,
                '1,2,3,4,5',
                'rScriptFailure'
            ]
        ]]);
    }


    /**
     *
     */
    public function testGetQueryDataForISQorSurveyWithGpaFactorAndRetention()
    {
        $this->specify('test getQueryDataForISQorSurveyWithGpaFactorAndRetention', function ($personId, $organizationId, $searchAttributes, $surveyType, $isqQueryResult, $surveyQueryResult, $caseQueryResult, $retentionTrackingYearId, $expectedResult) {
            // Declaring mock repositories
            $mockPersonRepository = $this->getMock("PersonRepository", ['getId']);
            $mockReportsRunningStatusRepository = $this->getMock("ReportsRunningStatusRepository");

            $mockOrgQuestionResponseRepository = $this->getMock("OrgQuestionResponseRepository", ['getGPAdataForISQ', 'getFactorDataForISQ', 'getRetentionDataForISQ', 'persist']);
            $mockSurveyResponseRepository = $this->getMock("SurveyResponseRepository", ['getGPAdataForSurvey', 'getFactorDataForSurvey', 'getRetentionDataForSurvey', 'persist', 'createCaseQueryForISQorSurvey']);
            $mockOrgAcademicYearRepository = $this->getMock("OrgAcademicYearRepository", ['find']);

            // Mocking repository
            $this->mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        ReportsRunningStatusRepository::REPOSITORY_KEY,
                        $mockReportsRunningStatusRepository
                    ],
                    [
                        OrgQuestionResponseRepository::REPOSITORY_KEY,
                        $mockOrgQuestionResponseRepository
                    ],
                    [
                        SurveyResponseRepository::REPOSITORY_KEY,
                        $mockSurveyResponseRepository
                    ],
                    [
                        OrgAcademicYearRepository::REPOSITORY_KEY,
                        $mockOrgAcademicYearRepository
                    ]
                ]);

            // Declaring mock objects
            $mockPersonObject = $this->getMock('Person', array('getId', 'getFirstName', 'getLastName'));
            $mockPersonObject->method('getId')->willReturn($personId);
            $mockPersonRepository->method('find')->willReturn($mockPersonObject);

            // Declaring methods

            if ($surveyType == 'isqs') {
                $mockOrgQuestionResponseRepository->method('getGPAdataForISQ')->willReturn($isqQueryResult['gpa']);
                $mockOrgQuestionResponseRepository->method('getFactorDataForISQ')->willReturn($isqQueryResult['factor']);
                $mockOrgQuestionResponseRepository->method('getRetentionDataForISQ')->willReturn($isqQueryResult['retention']);
            } elseif ($surveyType == 'survey') {
                $mockSurveyResponseRepository->method('getGPAdataForSurvey')->willReturn($surveyQueryResult['gpa']);
                $mockSurveyResponseRepository->method('getFactorDataForSurvey')->willReturn($surveyQueryResult['factor']);
                $mockSurveyResponseRepository->method('getRetentionDataForSurvey')->willReturn($surveyQueryResult['retention']);
            }

            $mockSurveyResponseRepository->method('createCaseQueryForISQorSurvey')->willReturn($caseQueryResult);

            // Mocking find with OrgAcademicYear
            $mockOrgAcademicYearObject = new OrgAcademicYear();
            $mockOrgAcademicYearRepository->method('find')->willReturn($mockOrgAcademicYearObject);
            $surveyData = empty($surveyType) ? '' : $searchAttributes[$surveyType];
            $comparisionReportService = new ComparisonReportService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $result = $comparisionReportService->getQueryDataForISQorSurveyWithGpaFactorAndRetention($organizationId, $personId, $surveyType, $searchAttributes['latest_cohort']['id'], $searchAttributes['latest_survey']['id'], $searchAttributes['gpa_org_academic_year']['year']['year_id'], $searchAttributes['org_academic_year']['year']['year_id'], $surveyData, $retentionTrackingYearId);

            $this->assertEquals($expectedResult, $result);

        }, ['examples' =>
            [
                [ // Example 1 Test with ISQ
                    181819,
                    59, // organizationId
                    [ // searchAttribute data for isqs
                        "org_academic_year_id" => [
                            186
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 186,
                                "name" => "2016-17 Academic Year",
                                "year_id" => "201617"
                            ],
                        ],
                        "retention_org_academic_year" => [
                            "year" => [
                                "year_id" => "201617"
                            ],
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 186,
                                "name" => "2016-17 Academic Year",
                                "year_id" => "201617"
                            ],
                        ],
                        "latest_survey" => [
                            "name" => "Transition One",
                            "id" => 15
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 1",
                            "id" => 1
                        ],
                        "group_names" => [
                            "xxxx",
                            "yyyy"
                        ],
                        "datablocks" => [

                        ],
                        "isps" => [

                        ],
                        "isqs" => [
                            "survey_id" => 11,
                            "year_id" => "201516",
                            "question_id" => 1564,
                            "type" => "category",
                            "cohort" => "1",
                            "subpopulation1" => [
                                "category_type" => [
                                    [
                                        "id" => 32652,
                                        "answer" => "Yes, it conflicts between 5 and 10 hours per week.",
                                        "value" => "4",
                                        "subpopulation1selected" => true
                                    ]
                                ]
                            ],
                            "subpopulation2" => [
                                "category_type" => [
                                    [
                                        "id" => 32653,
                                        "answer" => "Yes, it conflicts between 11 and 19 hours per week.",
                                        "value" => "5",
                                        "subpopulation2selected" => true
                                    ],
                                ],
                            ],
                        ],
                        "survey" => [

                        ],
                        "filterText" => [
                            "isq",
                            "Organization: 059 Question ID: 01564",
                            [
                                "xxxx",
                                "yyyy"
                            ],
                        ],
                    ],
                    'isqs',
                    [ //isqQueryResult
                        "factor" => [
                            [
                                "factor_id" => "1",
                                "factor_name" => "Commitment to the Institution",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615056",
                                "factor_value" => "7.00"
                            ],
                            [
                                "factor_id" => "2",
                                "factor_name" => "Communication Skills",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615097",
                                "factor_value" => "6.50"
                            ],
                            [
                                "factor_id" => "3",
                                "factor_name" => "Analytical Skills",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615079",
                                "factor_value" => "5.00"
                            ],
                            [
                                "factor_id" => "4",
                                "factor_name" => "Self-Discipline",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615046",
                                "factor_value" => "6.67"
                            ],
                            [
                                "factor_id" => "5",
                                "factor_name" => "Time Management",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615160",
                                "factor_value" => "6.33"
                            ],
                            [
                                "factor_id" => "6",
                                "factor_name" => "Financial Means",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615061",
                                "factor_value" => "4.67"
                            ],
                        ],
                        "gpa" => [
                            [
                                "org_academic_terms_id" => "471",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615116",
                                "gpa_value" => "3.60",
                                "term_name" => "Fall Semester 2016"
                            ],
                            [
                                "org_academic_terms_id" => "471",
                                "subpopulation_id" => "2",
                                "participant_id" => "4618087",
                                "gpa_value" => "3.18",
                                "term_name" => "Fall Semester 2016"
                            ]
                        ],
                        "retention" => [
                            [
                                "subpopulation_id" => "2",
                                "organization_id" => "20",
                                "person_id" => "4939199",
                                "retention_tracking_year" => "201617",
                                "year_id" => "201617",
                                "year_name" => "Newname2016-2017",
                                "retention_completion_variable_name" => "Completed Degree in 1 Year or Less",
                                "retention_completion_value" => "1"
                            ],
                            [
                                "subpopulation_id" => "2",
                                "organization_id" => "20",
                                "person_id" => "4939199",
                                "retention_tracking_year" => "201617",
                                "year_id" => "201718",
                                "year_name" => "201213",
                                "retention_completion_variable_name" => "Retained to Start of Year 2",
                                "retention_completion_value" => "0"
                            ]

                        ]
                    ],
                    [],//surveyQueryResult
                    [//caseQueryResult
                        "sql" => "CASE WHEN oqr.decimal_value IN (:decimal_value_1) THEN 1 WHEN oqr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        "parameters" => [
                            "decimal_value_1" => [
                                "1"
                            ],
                            "decimal_value_2" => [
                                "0"
                            ]
                        ]
                    ],
                    201516, //retentionTrackingYearId
                    [ // Expected
                        "factor" => [
                            [
                                "factor_id" => "1",
                                "factor_name" => "Commitment to the Institution",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615056",
                                "factor_value" => "7.00"
                            ],
                            [
                                "factor_id" => "2",
                                "factor_name" => "Communication Skills",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615097",
                                "factor_value" => "6.50"
                            ],
                            [
                                "factor_id" => "3",
                                "factor_name" => "Analytical Skills",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615079",
                                "factor_value" => "5.00"
                            ],
                            [
                                "factor_id" => "4",
                                "factor_name" => "Self-Discipline",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615046",
                                "factor_value" => "6.67"
                            ],
                            [
                                "factor_id" => "5",
                                "factor_name" => "Time Management",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615160",
                                "factor_value" => "6.33"
                            ],
                            [
                                "factor_id" => "6",
                                "factor_name" => "Financial Means",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615061",
                                "factor_value" => "4.67"
                            ],
                        ],
                        "gpa" => [
                            [
                                "org_academic_terms_id" => "471",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615116",
                                "gpa_value" => "3.60",
                                "term_name" => "Fall Semester 2016"
                            ],
                            [
                                "org_academic_terms_id" => "471",
                                "subpopulation_id" => "2",
                                "participant_id" => "4618087",
                                "gpa_value" => "3.18",
                                "term_name" => "Fall Semester 2016"
                            ]
                        ],
                        "retention" => [
                            [
                                "subpopulation_id" => "2",
                                "organization_id" => "20",
                                "person_id" => "4939199",
                                "retention_tracking_year" => "201617",
                                "year_id" => "201617",
                                "year_name" => "Newname2016-2017",
                                "retention_completion_variable_name" => "Completed Degree in 1 Year or Less",
                                "retention_completion_value" => "1"
                            ],
                            [
                                "subpopulation_id" => "2",
                                "organization_id" => "20",
                                "person_id" => "4939199",
                                "retention_tracking_year" => "201617",
                                "year_id" => "201718",
                                "year_name" => "201213",
                                "retention_completion_variable_name" => "Retained to Start of Year 2",
                                "retention_completion_value" => "0"
                            ]

                        ]
                    ]
                ],
                [ // Example 2 Test with survey
                    181819,
                    59, // organizationId
                    [ // searchAttribute data for survey
                        "org_academic_year_id" => [
                            187
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 187,
                                "name" => "2016-17",
                                "year_id" => "201617"
                            ],
                        ],
                        "retention_org_academic_year" => [
                            "year" => [
                                "year_id" => "201617"
                            ],
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 187,
                                "name" => "2016-17",
                                "year_id" => "201617"
                            ],
                        ],
                        "latest_survey" => [
                            "name" => "Transition Two",
                            "id" => 16
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 2",
                            "id" => 2
                        ],
                        "group_names" => [
                            "xxxx",
                            "yyyy"
                        ],
                        "datablocks" => [

                        ],
                        "isps" => [

                        ],
                        "isqs" => [

                        ],
                        "survey" => [
                            "survey_id" => 16,
                            "year_id" => "201617",
                            "question_id" => 1565,
                            "type" => "category",
                            "cohort" => "1",
                            "subpopulation1" => [
                                "category_type" => [
                                    [
                                        "id" => 32653,
                                        "answer" => "Yes, it conflicts between 5 and 10 hours per week.",
                                        "value" => "3",
                                        "subpopulation1selected" => true
                                    ]
                                ]
                            ],
                            "subpopulation2" => [
                                "category_type" => [
                                    [
                                        "id" => 32654,
                                        "answer" => "Yes, it conflicts between 11 and 19 hours per week.",
                                        "value" => "4",
                                        "subpopulation2selected" => true
                                    ],
                                ],
                            ],
                        ],
                        "filterText" => [
                            "isq",
                            "Organization: 059 Question ID: 01564",
                            [
                                "xxxx",
                                "yyyy"
                            ],
                        ],
                    ],
                    'survey',
                    [],//isqQueryResult
                    [ //surveyQueryResult
                        "factor" => [
                            [
                                "factor_id" => "1",
                                "factor_name" => "Commitment to the Institution again",
                                "subpopulation_id" => "1",
                                "participant_id" => "4612056",
                                "factor_value" => "6.00"
                            ],
                            [
                                "factor_id" => "2",
                                "factor_name" => "Skills",
                                "subpopulation_id" => "2",
                                "participant_id" => "4115097",
                                "factor_value" => "6.70"
                            ],
                            [
                                "factor_id" => "3",
                                "factor_name" => "Analytical Skills",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615079",
                                "factor_value" => "5.00"
                            ],
                            [
                                "factor_id" => "4",
                                "factor_name" => "Self-Discipline",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615046",
                                "factor_value" => "6.67"
                            ],
                            [
                                "factor_id" => "5",
                                "factor_name" => "Time Management",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615160",
                                "factor_value" => "6.33"
                            ],
                            [
                                "factor_id" => "6",
                                "factor_name" => "Financial Means",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615061",
                                "factor_value" => "4.67"
                            ],
                        ],
                        "gpa" => [
                            [
                                "org_academic_terms_id" => "471",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615116",
                                "gpa_value" => "3.60",
                                "term_name" => "Fall Semester 2016"
                            ],
                            [
                                "org_academic_terms_id" => "471",
                                "subpopulation_id" => "2",
                                "participant_id" => "4618087",
                                "gpa_value" => "3.18",
                                "term_name" => "Fall Semester 2016"
                            ]
                        ],
                        "retention" => [
                            [
                                "subpopulation_id" => "2",
                                "organization_id" => "20",
                                "person_id" => "4939199",
                                "retention_tracking_year" => "201617",
                                "year_id" => "201617",
                                "year_name" => "Newnam2016-2017",
                                "retention_completion_variable_name" => "Completed Degree in 2 Year or Less",
                                "retention_completion_value" => "1"
                            ],
                            [
                                "subpopulation_id" => "2",
                                "organization_id" => "20",
                                "person_id" => "4939199",
                                "retention_tracking_year" => "201617",
                                "year_id" => "201718",
                                "year_name" => "201213",
                                "retention_completion_variable_name" => "Retained to Start of Year 2",
                                "retention_completion_value" => "0"
                            ]

                        ]
                    ],
                    [//caseQueryResult
                        "sql" => "CASE WHEN sr.decimal_value IN (:decimal_value_1) THEN 1 WHEN sr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        "parameters" => [
                            "decimal_value_1" => [
                                "2"
                            ],
                            "decimal_value_2" => [
                                "1"
                            ]
                        ]
                    ],
                    201617, //retentionTrackingYearId
                    [ // Expected
                        "factor" => [
                            [
                                "factor_id" => "1",
                                "factor_name" => "Commitment to the Institution again",
                                "subpopulation_id" => "1",
                                "participant_id" => "4612056",
                                "factor_value" => "6.00"
                            ],
                            [
                                "factor_id" => "2",
                                "factor_name" => "Skills",
                                "subpopulation_id" => "2",
                                "participant_id" => "4115097",
                                "factor_value" => "6.70"
                            ],
                            [
                                "factor_id" => "3",
                                "factor_name" => "Analytical Skills",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615079",
                                "factor_value" => "5.00"
                            ],
                            [
                                "factor_id" => "4",
                                "factor_name" => "Self-Discipline",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615046",
                                "factor_value" => "6.67"
                            ],
                            [
                                "factor_id" => "5",
                                "factor_name" => "Time Management",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615160",
                                "factor_value" => "6.33"
                            ],
                            [
                                "factor_id" => "6",
                                "factor_name" => "Financial Means",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615061",
                                "factor_value" => "4.67"
                            ],
                        ],
                        "gpa" => [
                            [
                                "org_academic_terms_id" => "471",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615116",
                                "gpa_value" => "3.60",
                                "term_name" => "Fall Semester 2016"
                            ],
                            [
                                "org_academic_terms_id" => "471",
                                "subpopulation_id" => "2",
                                "participant_id" => "4618087",
                                "gpa_value" => "3.18",
                                "term_name" => "Fall Semester 2016"
                            ]
                        ],
                        "retention" => [
                            [
                                "subpopulation_id" => "2",
                                "organization_id" => "20",
                                "person_id" => "4939199",
                                "retention_tracking_year" => "201617",
                                "year_id" => "201617",
                                "year_name" => "Newnam2016-2017",
                                "retention_completion_variable_name" => "Completed Degree in 2 Year or Less",
                                "retention_completion_value" => "1"
                            ],
                            [
                                "subpopulation_id" => "2",
                                "organization_id" => "20",
                                "person_id" => "4939199",
                                "retention_tracking_year" => "201617",
                                "year_id" => "201718",
                                "year_name" => "201213",
                                "retention_completion_variable_name" => "Retained to Start of Year 2",
                                "retention_completion_value" => "0"
                            ]

                        ]
                    ]
                ],
                [ // Example 3 Test with empty survey
                    181819,
                    59, // organizationId
                    [ // searchAttribute data for survey
                        "org_academic_year_id" => [
                            187
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 187,
                                "name" => "2016-17",
                                "year_id" => "201617"
                            ],
                        ],
                        "retention_org_academic_year" => [
                            "year" => [
                                "year_id" => "201617"
                            ],
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 187,
                                "name" => "2016-17",
                                "year_id" => "201617"
                            ],
                        ],
                        "latest_survey" => [
                            "name" => "Transition Two",
                            "id" => 16
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 2",
                            "id" => 2
                        ],
                        "group_names" => [
                            "xxxx",
                            "yyyy"
                        ],
                        "datablocks" => [

                        ],
                        "isps" => [

                        ],
                        "isqs" => [

                        ],
                        "survey" => [
                            "survey_id" => 16,
                            "year_id" => "201617",
                            "question_id" => 1565,
                            "type" => "category",
                            "cohort" => "1",
                            "subpopulation1" => [
                                "category_type" => [
                                    [
                                        "id" => 32653,
                                        "answer" => "Yes, it conflicts between 5 and 10 hours per week.",
                                        "value" => "3",
                                        "subpopulation1selected" => true
                                    ]
                                ]
                            ],
                            "subpopulation2" => [
                                "category_type" => [
                                    [
                                        "id" => 32654,
                                        "answer" => "Yes, it conflicts between 11 and 19 hours per week.",
                                        "value" => "4",
                                        "subpopulation2selected" => true
                                    ],
                                ],
                            ],
                        ],
                        "filterText" => [
                            "isq",
                            "Organization: 059 Question ID: 01564",
                            [
                                "xxxx",
                                "yyyy"
                            ],
                        ],
                    ],
                    '',
                    [],//isqQueryResult
                    [],//surveyQueryResult
                    [],//caseQueryResult
                    201516, //retentionTrackingYearId
                    [ // Expected result
                        'factor' => '',
                        'gpa' => '',
                        'retention' => ''
                    ]
                ],
                [ // Example 4 Test with numeric
                    181819,
                    59, // organizationId
                    [ // searchAttribute data for survey
                        "org_academic_year_id" => [
                            187
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 187,
                                "name" => "2016-17",
                                "year_id" => "201617"
                            ],
                        ],
                        "retention_org_academic_year" => [
                            "year" => [
                                "year_id" => "201617"
                            ],
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 187,
                                "name" => "2016-17",
                                "year_id" => "201617"
                            ],
                        ],
                        "latest_survey" => [
                            "name" => "Transition Two",
                            "id" => 16
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 2",
                            "id" => 2
                        ],
                        "group_names" => [
                            "xxxx",
                            "yyyy"
                        ],
                        "datablocks" => [

                        ],
                        "isps" => [

                        ],
                        "isqs" => [

                        ],
                        "survey" => [
                            "survey_id" => 16,
                            "year_id" => "201617",
                            "question_id" => 1565,
                            "type" => "number",
                            "cohort" => "1",
                            "subpopulation1" => [
                                "is_single" => true,
                                "single_value" => "8"
                            ],
                            "subpopulation2" => [
                                "is_single" => false,
                                "max_digits" => "20.0000",
                                "min_digits" => "10.000"
                            ],
                        ],
                        "filterText" => [
                            "isq",
                            "Organization: 059 Question ID: 01564",
                            [
                                "xxxx",
                                "yyyy"
                            ],
                        ],
                    ],
                    '',
                    [],//isqQueryResult
                    [],//surveyQueryResult
                    [],//caseQueryResult
                    201415, //retentionTrackingYearId
                    [ // Expected result
                        'factor' => '',
                        'gpa' => '',
                        'retention' => ''
                    ]
                ],
            ]
        ]);
    }


    public function testGetQueryDataForDataBlockOrIspWithGpaFactorAndRetention()
    {
        $this->specify('test getQueryDataForDataBlockOrIspWithGpaFactorAndRetention', function ($organizationId, $personId, $searchAttributes, $cohortId, $surveyId, $gpaYearId, $surveyYearId, $retentionTrackingYearId, $queryResult, $expectedResult) {

            //Create all mocks necessary for Service class creation
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'addWarning'));
            $mockContainer = $this->getMock('Container', array('get'));

            // Declaring mock repositories
            $mockReportsRunningStatusRepository = $this->getMock("ReportsRunningStatusRepository");
            $mockPersonEbiMetaDataRepository = $this->getMock("PersonEbiMetaDataRepository", ['getFactorDataForProfileblock', 'getGpaDataForProfileblock', 'getRetentionDataForProfileBlock']);
            $mockPersonOrgMetaDataRepository = $this->getMock("PersonOrgMetaDataRepository", ['getFactorDataForISP', 'getGpaDataForISP', 'getRetentionDataForISP']);
            $mockComparisionReportDAO = $this->getMock('ComparisonReportDAO', array('createCaseQueryForIspOrProfileblock'));

            // Mocking repository
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonEbiMetaDataRepository::REPOSITORY_KEY,
                        $mockPersonEbiMetaDataRepository
                    ],
                    [
                        PersonOrgMetaDataRepository::REPOSITORY_KEY,
                        $mockPersonOrgMetaDataRepository
                    ],
                    [
                        ComparisonReportDAO::DAO_KEY,
                        $mockComparisionReportDAO
                    ],
                ]);

            $mockContainer->method('get')->willReturnMap(
                [
                    [ComparisonReportDAO::DAO_KEY, $mockComparisionReportDAO],
                ]
            );

            if (!empty($searchAttributes['datablocks'])) {
                $mockPersonEbiMetaDataRepository->method('getFactorDataForProfileblock')->willReturn($queryResult['factor']);
                $mockPersonEbiMetaDataRepository->method('getGpaDataForProfileblock')->willReturn($queryResult['gpa']);
                $mockPersonEbiMetaDataRepository->method('getRetentionDataForProfileBlock')->willReturn($queryResult['retention']);
            } else if (!empty($searchAttributes['isps'])) {
                $mockPersonOrgMetaDataRepository->method('getFactorDataForISP')->willReturn($queryResult['factor']);
                $mockPersonOrgMetaDataRepository->method('getGpaDataForISP')->willReturn($queryResult['gpa']);
                $mockPersonOrgMetaDataRepository->method('getRetentionDataForISP')->willReturn($queryResult['retention']);
            }

            $mockComparisionReportDAO->method('createCaseQueryForIspOrProfileblock')->willReturn(1);

            $comparisionReportService = new ComparisonReportService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $comparisionReportService->getQueryDataForDataBlockOrIspWithGpaFactorAndRetention($organizationId, $personId, $searchAttributes, $cohortId, $surveyId, $gpaYearId, $surveyYearId, $retentionTrackingYearId);

            $this->assertEquals($expectedResult, $result);

        }, ['examples' =>
            [
                [ // Example 1 test with isp
                    62,
                    5048809,
                    [ // json passed
                        "org_academic_year_id" => [
                            192
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "retention_org_academic_year" => [
                            "year" => [
                                "year_id" => "201617"
                            ],
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "latest_survey" => [
                            "name" => "Transition One",
                            "id" => 15
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 1",
                            "id" => 1
                        ],
                        "group_names" => [
                            "Sub popu 1",
                            "Sub popu 2"
                        ],
                        "datablocks" => [

                        ],
                        "isps" => [
                            "id" => 7112,
                            "display_name" => "Organization: 062 Metadata ID: 007112",
                            "item_data_type" => "S",
                            "item_meta_key" => "Gender",
                            "calendar_assignment" => "N",
                            "year_term" => false,
                            "subpopulation1" => [
                                "category_type" => [
                                    [
                                        "answer" => "Yes",
                                        "value" => "1",
                                        "sequence_no" => 0,
                                        "id" => "",
                                        "subpopulationOneselected" => true
                                    ]
                                ]
                            ],
                            "subpopulation2" => [
                                "category_type" => [
                                    [
                                        "answer" => "No",
                                        "value" => "0",
                                        "sequence_no" => 0,
                                        "id" => "",
                                        "subpopulationTwoselected" => true
                                    ]
                                ]
                            ]
                        ],
                        "isqs" => [

                        ],
                        "survey" => [

                        ],
                        "filterText" => [
                            "isp",
                            [
                                "text" => "Organization: 062 Metadata ID: 007112",
                                "yearTerm" => ""
                            ],
                            [
                                "Sub popu 1",
                                "Sub popu 2"
                            ]
                        ]
                    ],
                    1,//cohort id
                    15,//survey id
                    201617,
                    201617,
                    201415, //retentionTrackingYearId
                    [
                        "factor" => [
                            [
                                "factor_id" => "1",
                                "factor_name" => "Commitment to the Institution",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615056",
                                "factor_value" => "7.00"
                            ],
                            [
                                "factor_id" => "2",
                                "factor_name" => "Communication Skills",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615097",
                                "factor_value" => "6.50"
                            ],
                            [
                                "factor_id" => "3",
                                "factor_name" => "Analytical Skills",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615079",
                                "factor_value" => "5.00"
                            ],
                            [
                                "factor_id" => "4",
                                "factor_name" => "Self-Discipline",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615046",
                                "factor_value" => "6.67"
                            ],
                            [
                                "factor_id" => "5",
                                "factor_name" => "Time Management",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615160",
                                "factor_value" => "6.33"
                            ],
                            [
                                "factor_id" => "6",
                                "factor_name" => "Financial Means",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615061",
                                "factor_value" => "4.67"
                            ],
                        ],
                        "gpa" => [
                            [
                                "org_academic_terms_id" => "471",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615116",
                                "gpa_value" => "3.60",
                                "term_name" => "Fall Semester 2016"
                            ],
                            [
                                "org_academic_terms_id" => "471",
                                "subpopulation_id" => "2",
                                "participant_id" => "4618087",
                                "gpa_value" => "3.18",
                                "term_name" => "Fall Semester 2016"
                            ]
                        ],
                        "retention" => [
                            [
                                "subpopulation_id" => "2",
                                "organization_id" => "20",
                                "person_id" => "4939199",
                                "retention_tracking_year" => "201617",
                                "year_id" => "201617",
                                "year_name" => "Newname2016-2017",
                                "retention_completion_variable_name" => "Completed Degree in 1 Year or Less",
                                "retention_completion_value" => "1"
                            ],
                            [
                                "subpopulation_id" => "2",
                                "organization_id" => "20",
                                "person_id" => "4939199",
                                "retention_tracking_year" => "201617",
                                "year_id" => "201718",
                                "year_name" => "201213",
                                "retention_completion_variable_name" => "Retained to Start of Year 2",
                                "retention_completion_value" => "0"
                            ]

                        ]
                    ],
                    [
                        "factor" => [
                            [
                                "factor_id" => "1",
                                "factor_name" => "Commitment to the Institution",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615056",
                                "factor_value" => "7.00"
                            ],
                            [
                                "factor_id" => "2",
                                "factor_name" => "Communication Skills",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615097",
                                "factor_value" => "6.50"
                            ],
                            [
                                "factor_id" => "3",
                                "factor_name" => "Analytical Skills",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615079",
                                "factor_value" => "5.00"
                            ],
                            [
                                "factor_id" => "4",
                                "factor_name" => "Self-Discipline",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615046",
                                "factor_value" => "6.67"
                            ],
                            [
                                "factor_id" => "5",
                                "factor_name" => "Time Management",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615160",
                                "factor_value" => "6.33"
                            ],
                            [
                                "factor_id" => "6",
                                "factor_name" => "Financial Means",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615061",
                                "factor_value" => "4.67"
                            ]
                        ],
                        "gpa" => [
                            [
                                "org_academic_terms_id" => "471",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615116",
                                "gpa_value" => "3.60",
                                "term_name" => "Fall Semester 2016"
                            ],
                            [
                                "org_academic_terms_id" => "471",
                                "subpopulation_id" => "2",
                                "participant_id" => "4618087",
                                "gpa_value" => "3.18",
                                "term_name" => "Fall Semester 2016"
                            ]
                        ],
                        "retention" => [
                            [
                                "subpopulation_id" => "2",
                                "organization_id" => "20",
                                "person_id" => "4939199",
                                "retention_tracking_year" => "201617",
                                "year_id" => "201617",
                                "year_name" => "Newname2016-2017",
                                "retention_completion_variable_name" => "Completed Degree in 1 Year or Less",
                                "retention_completion_value" => "1"
                            ],
                            [
                                "subpopulation_id" => "2",
                                "organization_id" => "20",
                                "person_id" => "4939199",
                                "retention_tracking_year" => "201617",
                                "year_id" => "201718",
                                "year_name" => "201213",
                                "retention_completion_variable_name" => "Retained to Start of Year 2",
                                "retention_completion_value" => "0"
                            ]
                        ]
                    ]
                ],
                [ // Example 2 test with data block
                    62,
                    5048809,
                    [
                        "org_academic_year_id" => [
                            192
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "latest_survey" => [
                            "name" => "Transition One",
                            "id" => 15
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 1",
                            "id" => 1
                        ],
                        "group_names" => [
                            "Sub popu 1",
                            "Sub popu 2"
                        ],
                        "datablocks" => [
                            "profile_block_id" => 26,
                            "profile_items" => [
                                "id" => 65,
                                "display_name" => "PersistMidYear",
                                "item_data_type" => "S",
                                "item_meta_key" => "Gender",
                                "calendar_assignment" => "Y",
                                "year_term" => [
                                    "year_id" => "192",
                                    "year_name" => "2016-2017",
                                    "term_id" => false,
                                    "term_name" => false
                                ],
                                "subpopulation1" => [
                                    "category_type" => [
                                        [
                                            "answer" => "No",
                                            "value" => "0",
                                            "sequence_no" => 0,
                                            "id" => "",
                                            "subpopulationOneselected" => true
                                        ]
                                    ]
                                ],
                                "subpopulation2" => [
                                    "category_type" => [
                                        [
                                            "answer" => "Yes",
                                            "value" => "1",
                                            "sequence_no" => 0,
                                            "id" => "",
                                            "subpopulationOneselected" => false,
                                            "subpopulationTwoselected" => true
                                        ]
                                    ]
                                ]
                            ],
                            "profile_block_name" => "Retention"
                        ],
                        "isps" => [

                        ],
                        "isqs" => [

                        ],
                        "survey" => [

                        ],
                        "filterText" => [
                            "profileItem",
                            [
                                "text" => "PersistMidYear",
                                "yearTerm" => "2016-2017"
                            ],
                            [
                                "Sub popu 1",
                                "Sub popu 2"
                            ]
                        ]
                    ],
                    1,//cohort id
                    15,//survey id
                    201617,
                    201617,
                    201415, //retentionTrackingYearId
                    [
                        "factor" => [
                            [
                                "factor_id" => "1",
                                "factor_name" => "Commitment to the Institution",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615056",
                                "factor_value" => "7.00"
                            ],
                            [
                                "factor_id" => "2",
                                "factor_name" => "Communication Skills",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615097",
                                "factor_value" => "6.50"
                            ],
                            [
                                "factor_id" => "3",
                                "factor_name" => "Analytical Skills",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615079",
                                "factor_value" => "5.00"
                            ],
                            [
                                "factor_id" => "4",
                                "factor_name" => "Self-Discipline",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615046",
                                "factor_value" => "6.67"
                            ],
                            [
                                "factor_id" => "5",
                                "factor_name" => "Time Management",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615160",
                                "factor_value" => "6.33"
                            ],
                            [
                                "factor_id" => "6",
                                "factor_name" => "Financial Means",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615061",
                                "factor_value" => "4.67"
                            ],
                        ],
                        "gpa" => [
                            [
                                "org_academic_terms_id" => "471",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615116",
                                "gpa_value" => "3.60",
                                "term_name" => "Fall Semester 2016"
                            ],
                            [
                                "org_academic_terms_id" => "471",
                                "subpopulation_id" => "2",
                                "participant_id" => "4618087",
                                "gpa_value" => "3.18",
                                "term_name" => "Fall Semester 2016"
                            ]
                        ],
                        "retention" => [
                            [
                                "subpopulation_id" => "2",
                                "organization_id" => "20",
                                "person_id" => "4939199",
                                "retention_tracking_year" => "201617",
                                "year_id" => "201617",
                                "year_name" => "Newname2016-2017",
                                "retention_completion_variable_name" => "Completed Degree in 1 Year or Less",
                                "retention_completion_value" => "1"
                            ],
                            [
                                "subpopulation_id" => "2",
                                "organization_id" => "20",
                                "person_id" => "4939199",
                                "retention_tracking_year" => "201617",
                                "year_id" => "201718",
                                "year_name" => "201213",
                                "retention_completion_variable_name" => "Retained to Start of Year 2",
                                "retention_completion_value" => "0"
                            ]

                        ]
                    ],
                    [
                        "factor" => [
                            [
                                "factor_id" => "1",
                                "factor_name" => "Commitment to the Institution",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615056",
                                "factor_value" => "7.00"
                            ],
                            [
                                "factor_id" => "2",
                                "factor_name" => "Communication Skills",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615097",
                                "factor_value" => "6.50"
                            ],
                            [
                                "factor_id" => "3",
                                "factor_name" => "Analytical Skills",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615079",
                                "factor_value" => "5.00"
                            ],
                            [
                                "factor_id" => "4",
                                "factor_name" => "Self-Discipline",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615046",
                                "factor_value" => "6.67"
                            ],
                            [
                                "factor_id" => "5",
                                "factor_name" => "Time Management",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615160",
                                "factor_value" => "6.33"
                            ],
                            [
                                "factor_id" => "6",
                                "factor_name" => "Financial Means",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615061",
                                "factor_value" => "4.67"
                            ]
                        ],
                        "gpa" => [
                            [
                                "org_academic_terms_id" => "471",
                                "subpopulation_id" => "1",
                                "participant_id" => "4615116",
                                "gpa_value" => "3.60",
                                "term_name" => "Fall Semester 2016"
                            ],
                            [
                                "org_academic_terms_id" => "471",
                                "subpopulation_id" => "2",
                                "participant_id" => "4618087",
                                "gpa_value" => "3.18",
                                "term_name" => "Fall Semester 2016"
                            ]
                        ],
                        "retention" => [
                            [
                                "subpopulation_id" => "2",
                                "organization_id" => "20",
                                "person_id" => "4939199",
                                "retention_tracking_year" => "201617",
                                "year_id" => "201617",
                                "year_name" => "Newname2016-2017",
                                "retention_completion_variable_name" => "Completed Degree in 1 Year or Less",
                                "retention_completion_value" => "1"
                            ],
                            [
                                "subpopulation_id" => "2",
                                "organization_id" => "20",
                                "person_id" => "4939199",
                                "retention_tracking_year" => "201617",
                                "year_id" => "201718",
                                "year_name" => "201213",
                                "retention_completion_variable_name" => "Retained to Start of Year 2",
                                "retention_completion_value" => "0"
                            ]
                        ]
                    ]
                ],
                [ // Example 3 test with no block
                    62,
                    5048809,
                    [
                        "org_academic_year_id" => [
                            192
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "latest_survey" => [
                            "name" => "Transition One",
                            "id" => 15
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 1",
                            "id" => 1
                        ],
                        "group_names" => [
                            "Sub popu 1",
                            "Sub popu 2"
                        ],
                        "datablocks" => [
                        ],
                        "isps" => [

                        ],
                        "isqs" => [

                        ],
                        "survey" => [

                        ],
                        "filterText" => [
                            "profileItem",
                            [
                                "text" => "PersistMidYear",
                                "yearTerm" => "2016-2017"
                            ],
                            [
                                "Sub popu 1",
                                "Sub popu 2"
                            ]
                        ]
                    ],
                    1,//cohort id
                    15,//survey id
                    201617,
                    201617,
                    201415, //retentionTrackingYearId
                    [
                        "factor" => [

                        ],
                        "gpa" => [

                        ],
                        "retention" => [

                        ]
                    ],
                    [

                    ]
                ]
            ]
        ]);
    }


    public function testBuildAndExecuteISPQuery()
    {
        $this->specify('test buildAndExecuteISPQuery', function ($organizationId, $mandatoryFilters, $studentList, $expectedResult) {

            //Create all mocks necessary for Service class creation
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'addWarning'));
            $mockContainer = $this->getMock('Container', array('get'));

            // Declaring mock repositories
            $mockPersonOrgMetaDataRepository = $this->getMock("PersonOrgMetaDataRepository", ['getStudentIdsListBasedOnISPSelection']);

            // Mocking repository
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonOrgMetaDataRepository::REPOSITORY_KEY,
                        $mockPersonOrgMetaDataRepository
                    ],

                ]);


            $mockPersonOrgMetaDataRepository->method('getStudentIdsListBasedOnISPSelection')->willReturn($studentList);

            $comparisionReportService = new ComparisonReportService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $comparisionReportService->buildAndExecuteISPQuery($organizationId, $mandatoryFilters);

            $this->assertEquals($expectedResult, $result);

        }, ['examples' =>
            [
                [ // Example 1 ISP with category type metadata
                    62,
                    [ // json passed
                        "org_academic_year_id" => [
                            192
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "retention_org_academic_year" => [
                            "year" => [
                                "year_id" => "201617"
                            ],
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "latest_survey" => [
                            "name" => "Transition One",
                            "id" => 15
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 1",
                            "id" => 1
                        ],
                        "group_names" => [
                            "Sub popu 1",
                            "Sub popu 2"
                        ],
                        "datablocks" => [

                        ],
                        "isps" => [
                            "id" => 7112,
                            "display_name" => "Organization: 062 Metadata ID: 007112",
                            "item_data_type" => "S",
                            "item_meta_key" => "Gender",
                            "calendar_assignment" => "N",
                            "year_term" => false,
                            "subpopulation1" => [
                                "category_type" => [
                                    [
                                        "answer" => "Yes",
                                        "value" => "1",
                                        "sequence_no" => 0,
                                        "id" => "",
                                        "subpopulationOneselected" => true
                                    ]
                                ]
                            ],
                            "subpopulation2" => [
                                "category_type" => [
                                    [
                                        "answer" => "No",
                                        "value" => "0",
                                        "sequence_no" => 0,
                                        "id" => "",
                                        "subpopulationTwoselected" => true
                                    ]
                                ]
                            ]
                        ],
                        "isqs" => [

                        ],
                        "survey" => [

                        ],
                        "filterText" => [
                            "isp",
                            [
                                "text" => "Organization: 062 Metadata ID: 007112",
                                "yearTerm" => ""
                            ],
                            [
                                "Sub popu 1",
                                "Sub popu 2"
                            ]
                        ]
                    ],
                    [
                        [
                            "person_id" => "189697"
                        ],
                        [
                            "person_id" => "189700"
                        ],
                        [
                            "person_id" => "189702"
                        ],
                        [
                            "person_id" => "189708"
                        ]
                    ],
                    [
                        [
                            "person_id" => "189697"
                        ],
                        [
                            "person_id" => "189700"
                        ],
                        [
                            "person_id" => "189702"
                        ],
                        [
                            "person_id" => "189708"
                        ]
                    ]
                ]
            ],
            [
                [ // Example 2 ISP with numeric type metadata
                    62,
                    [ // json passed
                        "org_academic_year_id" => [
                            192
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "retention_org_academic_year" => [
                            "year" => [
                                "year_id" => "201617"
                            ],
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "latest_survey" => [
                            "name" => "Transition One",
                            "id" => 15
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 1",
                            "id" => 1
                        ],
                        "group_names" => [
                            "Sub popu 1",
                            "Sub popu 2"
                        ],
                        "datablocks" => [

                        ],
                        "isps" => [
                            "id" => 22,
                            "display_name" => "HighSchoolGPA",
                            "item_data_type" => "N",
                            "item_meta_key" => "Gender",
                            "calendar_assignment" => "N",
                            "year_term" => false,
                            "subpopulation1" => [
                                "is_single" => false,
                                "min_digits" => 0,
                                "max_digits" => 5
                            ],
                            "subpopulation2" => [
                                "is_single" => false,
                                "min_digits" => 6,
                                "max_digits" => 10
                            ]
                        ],
                        "isqs" => [

                        ],
                        "survey" => [

                        ],
                        "filterText" => [
                            "isp",
                            [
                                "text" => "Organization: 062 Metadata ID: 007112",
                                "yearTerm" => ""
                            ],
                            [
                                "Sub popu 1",
                                "Sub popu 2"
                            ]
                        ]
                    ],
                    [
                        [
                            "person_id" => "189670"
                        ],
                        [
                            "person_id" => "189780"
                        ],
                        [
                            "person_id" => "189702"
                        ],
                        [
                            "person_id" => "189756"
                        ]
                    ],
                    [
                        [
                            "person_id" => "189670"
                        ],
                        [
                            "person_id" => "189780"
                        ],
                        [
                            "person_id" => "189702"
                        ],
                        [
                            "person_id" => "189756"
                        ]
                    ]
                ]
            ],
            [
                [ // Example 3 ISP with date type metadata but no studens in the range
                    62,
                    [ // json passed
                        "org_academic_year_id" => [
                            192
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "retention_org_academic_year" => [
                            "year" => [
                                "year_id" => "201617"
                            ],
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "latest_survey" => [
                            "name" => "Transition One",
                            "id" => 15
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 1",
                            "id" => 1
                        ],
                        "group_names" => [
                            "Sub popu 1",
                            "Sub popu 2"
                        ],
                        "datablocks" => [

                        ],
                        "isps" => [
                            "id" => 22,
                            "display_name" => "HighSchoolAdmission",
                            "item_data_type" => "D",
                            "item_meta_key" => "Admission",
                            "calendar_assignment" => "N",
                            "year_term" => false,
                            "subpopulation1" => [
                                "min_date" => "01/01/2016",
                                "max_date" => "31/12/2016"
                            ],
                            "subpopulation2" => [
                                "min_date" => "01/01/2017",
                                "max_date" => "31/10/2017"
                            ]
                        ],
                        "isqs" => [

                        ],
                        "survey" => [

                        ],
                        "filterText" => [
                            "isp",
                            [
                                "text" => "Organization: 062 Metadata ID: 007112",
                                "yearTerm" => ""
                            ],
                            [
                                "Sub popu 1",
                                "Sub popu 2"
                            ]
                        ]
                    ],
                    [

                    ],
                    [

                    ]
                ]
            ]
        ]);
    }


    public function testBuildAndExecuteProfileQuery()
    {
        $this->specify('test buildAndExecuteProfileQuery', function ($organizationId, $mandatoryFilters, $studentList, $expectedResult) {

            //Create all mocks necessary for Service class creation
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'addWarning'));
            $mockContainer = $this->getMock('Container', array('get'));

            // Declaring mock repositories
            $mockPersonEbiMetaDataRepository = $this->getMock("PersonEbiMetaDataRepository", ['getStudentIdsListBasedOnProfileItemSelection']);

            // Mocking repository
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonEbiMetaDataRepository::REPOSITORY_KEY,
                        $mockPersonEbiMetaDataRepository
                    ],
                ]);

            $mockPersonEbiMetaDataRepository->method('getStudentIdsListBasedOnProfileItemSelection')->willReturn($studentList);

            $comparisionReportService = new ComparisonReportService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $comparisionReportService->buildAndExecuteProfileQuery($organizationId, $mandatoryFilters);

            $this->assertEquals($expectedResult, $result);

        }, ['examples' =>
            [
                [ // Example 1 profile block with category type metadata
                    62,
                    [ // json passed
                        "org_academic_year_id" => [
                            192
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "retention_org_academic_year" => [
                            "year" => [
                                "year_id" => "201617"
                            ],
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "latest_survey" => [
                            "name" => "Transition One",
                            "id" => 15
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 1",
                            "id" => 1
                        ],
                        "group_names" => [
                            "Sub popu 1",
                            "Sub popu 2"
                        ],
                        "datablocks" => [
                            "profile_block_id" => 22,
                            "profile_items" => [
                                "id" => 22,
                                "display_name" => "HighSchoolGPA",
                                "item_data_type" => "S",
                                "item_meta_key" => "Gender",
                                "calendar_assignment" => "N",
                                "year_term" => false,
                                "subpopulation1" => [
                                    "category_type" => [
                                        [
                                            "answer" => "Male",
                                            "value" => "1",
                                            "sequence_no" => 0,
                                            "id" => "",
                                            "subpopulationOneselected" => true
                                        ]
                                    ]
                                ],
                                "subpopulation2" => [
                                    "category_type" => [
                                        [
                                            "answer" => "Female",
                                            "value" => "0",
                                            "sequence_no" => 0,
                                            "id" => "",
                                            "subpopulationTwoselected" => true
                                        ]
                                    ]
                                ]
                            ],
                            "profile_block_name" => "Admissions-HS"
                        ],
                        "isps" => [
                        ],
                        "isqs" => [

                        ],
                        "survey" => [

                        ],
                        "filterText" => [
                            "isp",
                            [
                                "text" => "Organization: 062 Metadata ID: 007112",
                                "yearTerm" => ""
                            ],
                            [
                                "Sub popu 1",
                                "Sub popu 2"
                            ]
                        ]
                    ],
                    [
                        [
                            "person_id" => "189697"
                        ],
                        [
                            "person_id" => "189700"
                        ],
                        [
                            "person_id" => "189702"
                        ],
                        [
                            "person_id" => "189708"
                        ]
                    ],
                    [
                        [
                            "person_id" => "189697"
                        ],
                        [
                            "person_id" => "189700"
                        ],
                        [
                            "person_id" => "189702"
                        ],
                        [
                            "person_id" => "189708"
                        ]
                    ]
                ]
            ],
            [
                [ // Example 2 profile block with numeric type metadata
                    62,
                    [ // json passed
                        "org_academic_year_id" => [
                            192
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "retention_org_academic_year" => [
                            "year" => [
                                "year_id" => "201617"
                            ],
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "latest_survey" => [
                            "name" => "Transition One",
                            "id" => 15
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 1",
                            "id" => 1
                        ],
                        "group_names" => [
                            "Sub popu 1",
                            "Sub popu 2"
                        ],
                        "datablocks" => [
                            "profile_block_id" => 22,
                            "profile_items" => [
                                "id" => 22,
                                "display_name" => "HighSchoolGPA",
                                "item_data_type" => "N",
                                "item_meta_key" => "Gender",
                                "calendar_assignment" => "N",
                                "year_term" => false,
                                "subpopulation1" => [
                                    "is_single" => false,
                                    "min_digits" => 0,
                                    "max_digits" => 5
                                ],
                                "subpopulation2" => [
                                    "is_single" => false,
                                    "min_digits" => 6,
                                    "max_digits" => 10
                                ]
                            ],
                            "profile_block_name" => "Admissions-HS"
                        ],
                        "isps" => [
                        ],
                        "isqs" => [

                        ],
                        "survey" => [

                        ],
                        "filterText" => [
                            "isp",
                            [
                                "text" => "Organization: 062 Metadata ID: 007112",
                                "yearTerm" => ""
                            ],
                            [
                                "Sub popu 1",
                                "Sub popu 2"
                            ]
                        ]
                    ],
                    [
                        [
                            "person_id" => "189670"
                        ],
                        [
                            "person_id" => "189780"
                        ],
                        [
                            "person_id" => "189702"
                        ],
                        [
                            "person_id" => "189756"
                        ]
                    ],
                    [
                        [
                            "person_id" => "189670"
                        ],
                        [
                            "person_id" => "189780"
                        ],
                        [
                            "person_id" => "189702"
                        ],
                        [
                            "person_id" => "189756"
                        ]
                    ]
                ]
            ],
            [
                [ // Example 3 profile block with date type metadata but no studens in the range
                    62,
                    [ // json passed
                        "org_academic_year_id" => [
                            192
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "retention_org_academic_year" => [
                            "year" => [
                                "year_id" => "201617"
                            ],
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "latest_survey" => [
                            "name" => "Transition One",
                            "id" => 15
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 1",
                            "id" => 1
                        ],
                        "group_names" => [
                            "Sub popu 1",
                            "Sub popu 2"
                        ],
                        "datablocks" => [
                            "profile_block_id" => 22,
                            "profile_items" => [
                                "id" => 22,
                                "display_name" => "HighSchoolAdmission",
                                "item_data_type" => "D",
                                "item_meta_key" => "Admission",
                                "calendar_assignment" => "N",
                                "year_term" => false,
                                "subpopulation1" => [
                                    "min_date" => "01/01/2016",
                                    "max_date" => "31/12/2016"
                                ],
                                "subpopulation2" => [
                                    "min_date" => "01/01/2017",
                                    "max_date" => "31/10/2017"
                                ]
                            ],
                            "profile_block_name" => "Admissions-HS"
                        ],
                        "isps" => [
                        ],
                        "isqs" => [

                        ],
                        "survey" => [

                        ],
                        "filterText" => [
                            "isp",
                            [
                                "text" => "Organization: 062 Metadata ID: 007112",
                                "yearTerm" => ""
                            ],
                            [
                                "Sub popu 1",
                                "Sub popu 2"
                            ]
                        ]
                    ],
                    [

                    ],
                    [

                    ]
                ]
            ]
        ]);
    }


    public function testBuildAndExecuteISQQuery()
    {
        $this->specify('test buildAndExecuteISQQuery', function ($organizationId, $mandatoryFilters, $studentList, $expectedResult) {

            //Create all mocks necessary for Service class creation
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'addWarning'));
            $mockContainer = $this->getMock('Container', array('get'));

            // Declaring mock repositories
            $mockOrgQuestionResponseRepository = $this->getMock("OrgQuestionResponseRepository", ['getStudentIdsListBasedOnISQSelection']);

            // Mocking repository
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgQuestionResponseRepository::REPOSITORY_KEY,
                        $mockOrgQuestionResponseRepository
                    ],
                ]);

            $mockOrgQuestionResponseRepository->method('getStudentIdsListBasedOnISQSelection')->willReturn($studentList);

            $comparisionReportService = new ComparisonReportService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $comparisionReportService->buildAndExecuteISQQuery($organizationId, $mandatoryFilters);

            $this->assertEquals($expectedResult, $result);

        }, ['examples' =>
            [
                [ // Example 1 ISQ with category type metadata
                    62,
                    [ // json passed
                        "org_academic_year_id" => [
                            192
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "retention_org_academic_year" => [
                            "year" => [
                                "year_id" => "201617"
                            ],
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "latest_survey" => [
                            "name" => "Transition One",
                            "id" => 15
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 1",
                            "id" => 1
                        ],
                        "group_names" => [
                            "Sub popu 1",
                            "Sub popu 2"
                        ],
                        "datablocks" => [
                        ],
                        "isps" => [
                        ],
                        "isqs" => [
                            "survey_id" => "15",
                            "year_id" => "201617",
                            "question_id" => 5313,
                            "type" => "category",
                            "cohort" => "1",
                            "subpopulation1" => [
                                "category_type" => [
                                    [
                                        "id" => 44553,
                                        "answer" => "(1) Not At All",
                                        "value" => "1",
                                        "subpopulationOneselected" => true
                                    ],
                                ]
                            ],
                            "subpopulation2" => [
                                "category_type" => [
                                    [
                                        "id" => 44555,
                                        "answer" => "(3) ",
                                        "value" => "3",
                                        "subpopulationTwoselected" => true
                                    ],
                                ]
                            ]
                        ],
                        "survey" => [

                        ],
                        "filterText" => [
                            "isp",
                            [
                                "text" => "Organization: 062 Metadata ID: 007112",
                                "yearTerm" => ""
                            ],
                            [
                                "Sub popu 1",
                                "Sub popu 2"
                            ]
                        ]
                    ],
                    [
                        [
                            "person_id" => "189697"
                        ],
                        [
                            "person_id" => "189700"
                        ],
                        [
                            "person_id" => "189702"
                        ],
                        [
                            "person_id" => "189708"
                        ]
                    ],
                    [
                        [
                            "person_id" => "189697"
                        ],
                        [
                            "person_id" => "189700"
                        ],
                        [
                            "person_id" => "189702"
                        ],
                        [
                            "person_id" => "189708"
                        ]
                    ]
                ]
            ],
            [
                [ // Example 2 ISQ with numeric type metadata
                    62,
                    [ // json passed
                        "org_academic_year_id" => [
                            192
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "retention_org_academic_year" => [
                            "year" => [
                                "year_id" => "201617"
                            ],
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "latest_survey" => [
                            "name" => "Transition One",
                            "id" => 15
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 1",
                            "id" => 1
                        ],
                        "group_names" => [
                            "Sub popu 1",
                            "Sub popu 2"
                        ],
                        "datablocks" => [
                        ],
                        "isp" => [

                        ],
                        "isqs" => [
                            "survey_id" => "15",
                            "year_id" => "201617",
                            "question_id" => 5313,
                            "type" => "number",
                            "cohort" => "1",
                            "subpopulation1" => [
                                "is_single" => false,
                                "min_digits" => 0,
                                "max_digits" => 5
                            ],
                            "subpopulation2" => [
                                "is_single" => false,
                                "min_digits" => 6,
                                "max_digits" => 10
                            ]
                        ],
                        "survey" => [

                        ],
                        "filterText" => [
                            "isp",
                            [
                                "text" => "Organization: 062 Metadata ID: 007112",
                                "yearTerm" => ""
                            ],
                            [
                                "Sub popu 1",
                                "Sub popu 2"
                            ]
                        ]
                    ],
                    [
                        [
                            "person_id" => "189670"
                        ],
                        [
                            "person_id" => "189780"
                        ],
                        [
                            "person_id" => "189702"
                        ],
                        [
                            "person_id" => "189756"
                        ]
                    ],
                    [
                        [
                            "person_id" => "189670"
                        ],
                        [
                            "person_id" => "189780"
                        ],
                        [
                            "person_id" => "189702"
                        ],
                        [
                            "person_id" => "189756"
                        ]
                    ]
                ]
            ],
            [
                [ // Example 3 ISQ with numeric type metadata but no studens in the range
                    62,
                    [ // json passed
                        "org_academic_year_id" => [
                            192
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "retention_org_academic_year" => [
                            "year" => [
                                "year_id" => "201617"
                            ],
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "latest_survey" => [
                            "name" => "Transition One",
                            "id" => 15
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 1",
                            "id" => 1
                        ],
                        "group_names" => [
                            "Sub popu 1",
                            "Sub popu 2"
                        ],
                        "datablocks" => [
                        ],
                        "isps" => [
                        ],
                        "isqs" => [
                            "survey_id" => "15",
                            "year_id" => "201617",
                            "question_id" => 5316,
                            "type" => "number",
                            "cohort" => "1",
                            "subpopulation1" => [
                                "is_single" => false,
                                "min_digits" => 0,
                                "max_digits" => 5
                            ],
                            "subpopulation2" => [
                                "is_single" => false,
                                "min_digits" => 6,
                                "max_digits" => 10
                            ]
                        ],
                        "survey" => [

                        ],
                        "filterText" => [
                            "isp",
                            [
                                "text" => "Organization: 062 Metadata ID: 007112",
                                "yearTerm" => ""
                            ],
                            [
                                "Sub popu 1",
                                "Sub popu 2"
                            ]
                        ]
                    ],
                    [

                    ],
                    [

                    ]
                ]
            ]
        ]);
    }


    public function testBuildAndExecuteSurveyQuestionQuery()
    {
        $this->specify('test buildAndExecuteSurveyQuestionQuery', function ($organizationId, $mandatoryFilters, $studentList, $expectedResult) {

            //Create all mocks necessary for Service class creation
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'addWarning'));
            $mockContainer = $this->getMock('Container', array('get'));

            // Declaring mock repositories
            $mockSurveyResponseRepository = $this->getMock("SurveyResponseRepository", ['getStudentIdsListBasedOnSurveyQuestionSelection']);

            // Mocking repository
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        SurveyResponseRepository::REPOSITORY_KEY,
                        $mockSurveyResponseRepository
                    ],
                ]);

            $mockSurveyResponseRepository->method('getStudentIdsListBasedOnSurveyQuestionSelection')->willReturn($studentList);

            $comparisionReportService = new ComparisonReportService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $comparisionReportService->buildAndExecuteSurveyQuestionQuery($organizationId, $mandatoryFilters);

            $this->assertEquals($expectedResult, $result);

        }, ['examples' =>
            [
                [ // Example 1 Survey with category type metadata
                    62,
                    [ // json passed
                        "org_academic_year_id" => [
                            192
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "retention_org_academic_year" => [
                            "year" => [
                                "year_id" => "201617"
                            ],
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "latest_survey" => [
                            "name" => "Transition One",
                            "id" => 15
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 1",
                            "id" => 1
                        ],
                        "group_names" => [
                            "Sub popu 1",
                            "Sub popu 2"
                        ],
                        "datablocks" => [
                        ],
                        "isps" => [
                        ],
                        "isqs" => [
                        ],
                        "survey" => [
                            "survey_id" => "11",
                            "year_id" => "201516",
                            "question_id" => 50,
                            "type" => "category",
                            "cohort" => "2",
                            "subpopulation1" => [
                                "category_type" => [
                                    [
                                        "id" => 10868,
                                        "answer" => "The course is not in my major.",
                                        "value" => "0",
                                        "subpopulationOneselected" => true
                                    ]
                                ]
                            ],
                            "subpopulation2" => [
                                "category_type" => [
                                    [
                                        "id" => 10893,
                                        "answer" => "The course is in my major.",
                                        "value" => "1",
                                        "subpopulationTwoselected" => true
                                    ]
                                ]
                            ]
                        ],
                        "filterText" => [
                            "isp",
                            [
                                "text" => "Organization: 062 Metadata ID: 007112",
                                "yearTerm" => ""
                            ],
                            [
                                "Sub popu 1",
                                "Sub popu 2"
                            ]
                        ]
                    ],
                    [
                        [
                            "person_id" => "189697"
                        ],
                        [
                            "person_id" => "189700"
                        ],
                        [
                            "person_id" => "189702"
                        ],
                        [
                            "person_id" => "189708"
                        ]
                    ],
                    [
                        [
                            "person_id" => "189697"
                        ],
                        [
                            "person_id" => "189700"
                        ],
                        [
                            "person_id" => "189702"
                        ],
                        [
                            "person_id" => "189708"
                        ]
                    ]
                ]
            ],
            [
                [ // Example 2 Survey with numeric type metadata
                    62,
                    [ // json passed
                        "org_academic_year_id" => [
                            192
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "retention_org_academic_year" => [
                            "year" => [
                                "year_id" => "201617"
                            ],
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "latest_survey" => [
                            "name" => "Transition One",
                            "id" => 15
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 1",
                            "id" => 1
                        ],
                        "group_names" => [
                            "Sub popu 1",
                            "Sub popu 2"
                        ],
                        "datablocks" => [
                        ],
                        "isp" => [

                        ],
                        "isqs" => [
                        ],
                        "survey" => [
                            "survey_id" => "11",
                            "year_id" => "201617",
                            "question_id" => 90,
                            "type" => "number",
                            "cohort" => "2",
                            "subpopulation1" => [
                                "is_single" => false,
                                "min_digits" => 0,
                                "max_digits" => 5
                            ],
                            "subpopulation2" => [
                                "is_single" => false,
                                "min_digits" => 6,
                                "max_digits" => 10
                            ]
                        ],
                        "filterText" => [
                            "isp",
                            [
                                "text" => "Organization: 062 Metadata ID: 007112",
                                "yearTerm" => ""
                            ],
                            [
                                "Sub popu 1",
                                "Sub popu 2"
                            ]
                        ]
                    ],
                    [
                        [
                            "person_id" => "189670"
                        ],
                        [
                            "person_id" => "189780"
                        ],
                        [
                            "person_id" => "199705"
                        ],
                        [
                            "person_id" => "189756"
                        ]
                    ],
                    [
                        [
                            "person_id" => "189670"
                        ],
                        [
                            "person_id" => "189780"
                        ],
                        [
                            "person_id" => "199705"
                        ],
                        [
                            "person_id" => "189756"
                        ]
                    ]
                ]
            ],
            [
                [ // Example 3 Survey with numeric type metadata but no studens in the range
                    62,
                    [ // json passed
                        "org_academic_year_id" => [
                            192
                        ],
                        "org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "retention_org_academic_year" => [
                            "year" => [
                                "year_id" => "201617"
                            ],
                        ],
                        "gpa_org_academic_year" => [
                            "year" => [
                                "id" => 192,
                                "name" => "2016-2017",
                                "year_id" => 201617
                            ]
                        ],
                        "latest_survey" => [
                            "name" => "Transition One",
                            "id" => 15
                        ],
                        "latest_cohort" => [
                            "name" => "Survey Cohort 1",
                            "id" => 1
                        ],
                        "group_names" => [
                            "Sub popu 1",
                            "Sub popu 2"
                        ],
                        "datablocks" => [
                        ],
                        "isps" => [
                        ],
                        "isqs" => [
                        ],
                        "survey" => [
                            "survey_id" => "14",
                            "year_id" => "201617",
                            "question_id" => 78,
                            "type" => "number",
                            "cohort" => "2",
                            "subpopulation1" => [
                                "is_single" => false,
                                "min_digits" => 0,
                                "max_digits" => 5
                            ],
                            "subpopulation2" => [
                                "is_single" => false,
                                "min_digits" => 6,
                                "max_digits" => 10
                            ]
                        ],
                        "filterText" => [
                            "isp",
                            [
                                "text" => "Organization: 062 Metadata ID: 007112",
                                "yearTerm" => ""
                            ],
                            [
                                "Sub popu 1",
                                "Sub popu 2"
                            ]
                        ]
                    ],
                    [

                    ],
                    [

                    ]
                ]
            ]
        ]);
    }


    public function testCreateWhereClauseForIspOrIsq()
    {
        $this->specify('test createWhereClauseForIspOrIsq', function ($dataArray, $metadataType, $expectedResult) {

            //Create all mocks necessary for Service class creation
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'addWarning'));
            $mockContainer = $this->getMock('Container', array('get'));

            $comparisionReportService = new ComparisonReportService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $result = $comparisionReportService->createWhereClauseForIspOrIsq($dataArray, $metadataType);
            } catch (SynapseValidationException $e) {
                $result = $e->getMessage();
            }
            $this->assertEquals($expectedResult, $result);

        }, ['examples' =>
            [
                [ // Example 1 Survey with category type metadata
                    [
                        "survey_id" => "11",
                        "year_id" => "201516",
                        "question_id" => 50,
                        "type" => "category",
                        "cohort" => "2",
                        "subpopulation1" => [
                            "category_type" => [
                                [
                                    "id" => 10868,
                                    "answer" => "The course is not in my major.",
                                    "value" => "0",
                                    "subpopulationOneselected" => true
                                ]
                            ]
                        ],
                        "subpopulation2" => [
                            "category_type" => [
                                [
                                    "id" => 10893,
                                    "answer" => "The course is in my major.",
                                    "value" => "1",
                                    "subpopulationTwoselected" => true
                                ]
                            ]
                        ]
                    ],
                    "survey",
                    [
                        "where_query" => "sr.decimal_value IN (:category_values)",
                        "parameters" => [
                            "category_values" => [
                                "0",
                                "1"
                            ]
                        ]
                    ]
                ],
                [ // Example 2 ISQ with category type metadata
                    [
                        "survey_id" => "11",
                        "year_id" => "201516",
                        "question_id" => 50,
                        "type" => "category",
                        "cohort" => "2",
                        "subpopulation1" => [
                            "category_type" => [
                                [
                                    "id" => 10868,
                                    "answer" => "The course is not in my major.",
                                    "value" => "0",
                                    "subpopulationOneselected" => true
                                ]
                            ]
                        ],
                        "subpopulation2" => [
                            "category_type" => [
                                [
                                    "id" => 10893,
                                    "answer" => "The course is in my major.",
                                    "value" => "1",
                                    "subpopulationTwoselected" => true
                                ]
                            ]
                        ]
                    ],
                    "isqs",
                    [
                        "where_query" => "oqr.decimal_value IN (:category_values)",
                        "parameters" => [
                            "category_values" => [
                                "0",
                                "1"
                            ]
                        ]
                    ]
                ],
                [ // Example 3 ISQ with number type metadata
                    [
                        "survey_id" => "11",
                        "year_id" => "201516",
                        "question_id" => 50,
                        "type" => "number",
                        "cohort" => "2",
                        "subpopulation1" => [
                            "is_single" => false,
                            "min_digits" => 0,
                            "max_digits" => 5
                        ],
                        "subpopulation2" => [
                            "is_single" => false,
                            "min_digits" => 6,
                            "max_digits" => 10
                        ]
                    ],
                    "isqs",
                    [
                        "where_query" => "((oqr.decimal_value BETWEEN :subpopulation1_min_digits AND :subpopulation1_max_digits) OR (oqr.decimal_value BETWEEN :subpopulation2_min_digits AND :subpopulation2_max_digits))",
                        "parameters" => [
                            "subpopulation1_min_digits" => 0,
                            "subpopulation1_max_digits" => 5,
                            "subpopulation2_min_digits" => 6,
                            "subpopulation2_max_digits" => 10
                        ]
                    ]
                ],
                [ // Example 4 survey with number type metadata
                    [
                        "survey_id" => "11",
                        "year_id" => "201516",
                        "question_id" => 50,
                        "type" => "number",
                        "cohort" => "2",
                        "subpopulation1" => [
                            "is_single" => false,
                            "min_digits" => 0,
                            "max_digits" => 5
                        ],
                        "subpopulation2" => [
                            "is_single" => false,
                            "min_digits" => 6,
                            "max_digits" => 10
                        ]
                    ],
                    "survey",
                    [
                        "where_query" => "((sr.decimal_value BETWEEN :subpopulation1_min_digits AND :subpopulation1_max_digits) OR (sr.decimal_value BETWEEN :subpopulation2_min_digits AND :subpopulation2_max_digits))",
                        "parameters" => [
                            "subpopulation1_min_digits" => 0,
                            "subpopulation1_max_digits" => 5,
                            "subpopulation2_min_digits" => 6,
                            "subpopulation2_max_digits" => 10
                        ]
                    ]
                ],
                [ // Example 5 ISP with category type metadata
                    [
                        "id" => 7112,
                        "display_name" => "Organization: 062 Metadata ID: 007112",
                        "item_data_type" => "S",
                        "item_meta_key" => "Gender",
                        "calendar_assignment" => "N",
                        "year_term" => false,
                        "subpopulation1" => [
                            "category_type" => [
                                [
                                    "answer" => "Yes",
                                    "value" => "1",
                                    "sequence_no" => 0,
                                    "id" => "",
                                    "subpopulationOneselected" => true
                                ]
                            ]
                        ],
                        "subpopulation2" => [
                            "category_type" => [
                                [
                                    "answer" => "No",
                                    "value" => "0",
                                    "sequence_no" => 0,
                                    "id" => "",
                                    "subpopulationTwoselected" => true
                                ]
                            ]
                        ]
                    ],
                    "isp",
                    [
                        "where_query" => "pom.metadata_value IN (:category_values)",
                        "parameters" => [
                            "category_values" => [
                                "1",
                                "0"
                            ]
                        ]
                    ]
                ],
                [ // Example 6 profile with category type metadata
                    [
                        "id" => 7112,
                        "display_name" => "Organization: 062 Metadata ID: 007112",
                        "item_data_type" => "S",
                        "item_meta_key" => "Gender",
                        "calendar_assignment" => "N",
                        "year_term" => false,
                        "subpopulation1" => [
                            "category_type" => [
                                [
                                    "answer" => "Yes",
                                    "value" => "2",
                                    "sequence_no" => 0,
                                    "id" => "",
                                    "subpopulationOneselected" => true
                                ]
                            ]
                        ],
                        "subpopulation2" => [
                            "category_type" => [
                                [
                                    "answer" => "No",
                                    "value" => "3",
                                    "sequence_no" => 0,
                                    "id" => "",
                                    "subpopulationTwoselected" => true
                                ]
                            ]
                        ]
                    ],
                    "profile",
                    [
                        "where_query" => "pem.metadata_value IN (:category_values)",
                        "parameters" => [
                            "category_values" => [
                                "2",
                                "3"
                            ]
                        ]
                    ]
                ],
                [ // Example 7 profile with numeric type metadata
                    [
                        "id" => 7112,
                        "display_name" => "Organization: 062 Metadata ID: 007112",
                        "item_data_type" => "N",
                        "item_meta_key" => "Gender",
                        "calendar_assignment" => "N",
                        "year_term" => false,
                        "subpopulation1" => [
                            "is_single" => false,
                            "min_digits" => 0,
                            "max_digits" => 5
                        ],
                        "subpopulation2" => [
                            "is_single" => true,
                            "single_value" => 6
                        ]

                    ],
                    "profile",
                    [
                        "where_query" => "((pem.metadata_value BETWEEN :subpopulation1_min_digits AND :subpopulation1_max_digits) OR pem.metadata_value IN (:subpopulation2_single_values))",
                        "parameters" => [
                            "subpopulation1_min_digits" => 0,
                            "subpopulation1_max_digits" => 5,
                            "subpopulation2_single_values" => 6
                        ]
                    ]
                ],
                [ // Example 7 ISP with numeric type metadata
                    [
                        "id" => 7112,
                        "display_name" => "Organization: 062 Metadata ID: 007112",
                        "item_data_type" => "N",
                        "item_meta_key" => "Gender",
                        "calendar_assignment" => "N",
                        "year_term" => false,
                        "subpopulation1" => [
                            "is_single" => false,
                            "min_digits" => 0,
                            "max_digits" => 5
                        ],
                        "subpopulation2" => [
                            "is_single" => false,
                            "min_digits" => 6,
                            "max_digits" => 10
                        ]

                    ],
                    "isp",
                    [
                        "where_query" => "((pom.metadata_value BETWEEN :subpopulation1_min_digits AND :subpopulation1_max_digits) OR (pom.metadata_value BETWEEN :subpopulation2_min_digits AND :subpopulation2_max_digits))",
                        "parameters" => [
                            "subpopulation1_min_digits" => 0,
                            "subpopulation1_max_digits" => 5,
                            "subpopulation2_min_digits" => 6,
                            "subpopulation2_max_digits" => 10
                        ]
                    ]
                ],
                [ // Example 7 ISP with date type metadata
                    [
                        "id" => 48,
                        "display_name" => "ApplicationDate",
                        "item_data_type" => "D",
                        "item_meta_key" => "Gender",
                        "calendar_assignment" => "N",
                        "year_term" => false,
                        "subpopulation1" => [
                            "start_date" => "01/01/2016",
                            "end_date" => "01/30/2016"
                        ],
                        "subpopulation2" => [
                            "start_date" => "07/01/2016",
                            "end_date" => "03/31/2017"
                        ]

                    ],
                    "isp",
                    [
                        "where_query" =>  "(((STR_TO_DATE(pom.metadata_value , '%m/%d/%Y' ) BETWEEN STR_TO_DATE(:subpopulation1_start_date , '%m/%d/%Y' ) AND STR_TO_DATE(:subpopulation1_end_date , '%m/%d/%Y' )) OR (STR_TO_DATE(pom.metadata_value , '%Y-%m-%d' ) BETWEEN STR_TO_DATE(:subpopulation1_start_date , '%Y-%m-%d' ) AND STR_TO_DATE(:subpopulation1_end_date , '%Y-%m-%d' ))) OR ((STR_TO_DATE(pom.metadata_value , '%m/%d/%Y' ) BETWEEN STR_TO_DATE(:subpopulation2_start_date , '%m/%d/%Y' ) AND STR_TO_DATE(:subpopulation2_end_date , '%m/%d/%Y' )) OR (STR_TO_DATE(pom.metadata_value , '%Y-%m-%d' ) BETWEEN STR_TO_DATE(:subpopulation2_start_date , '%Y-%m-%d' ) AND STR_TO_DATE(:subpopulation2_end_date , '%Y-%m-%d' ))))",
                        "parameters" =>  [
                            "subpopulation1_start_date" =>  "01/01/2016",
                            "subpopulation1_end_date" =>  "01/30/2016",
                            "subpopulation2_start_date" =>  "07/01/2016",
                            "subpopulation2_end_date" =>  "03/31/2017"
                        ]
                    ]
                ],
                [ // Example 8 profile with date type metadata
                    [
                        "id" => 48,
                        "display_name" => "ApplicationDate",
                        "item_data_type" => "D",
                        "item_meta_key" => "Gender",
                        "calendar_assignment" => "N",
                        "year_term" => false,
                        "subpopulation1" => [
                            "start_date" => "01/01/2016",
                            "end_date" => "01/30/2016"
                        ],
                        "subpopulation2" => [
                            "start_date" => "07/01/2016",
                            "end_date" => "03/31/2017"
                        ]

                    ],
                    "profile",
                    [
                        "where_query" =>  "(((STR_TO_DATE(pem.metadata_value , '%m/%d/%Y' ) BETWEEN STR_TO_DATE(:subpopulation1_start_date , '%m/%d/%Y' ) AND STR_TO_DATE(:subpopulation1_end_date , '%m/%d/%Y' )) OR (STR_TO_DATE(pem.metadata_value , '%Y-%m-%d' ) BETWEEN STR_TO_DATE(:subpopulation1_start_date , '%Y-%m-%d' ) AND STR_TO_DATE(:subpopulation1_end_date , '%Y-%m-%d' ))) OR ((STR_TO_DATE(pem.metadata_value , '%m/%d/%Y' ) BETWEEN STR_TO_DATE(:subpopulation2_start_date , '%m/%d/%Y' ) AND STR_TO_DATE(:subpopulation2_end_date , '%m/%d/%Y' )) OR (STR_TO_DATE(pem.metadata_value , '%Y-%m-%d' ) BETWEEN STR_TO_DATE(:subpopulation2_start_date , '%Y-%m-%d' ) AND STR_TO_DATE(:subpopulation2_end_date , '%Y-%m-%d' ))))",
                        "parameters" =>  [
                            "subpopulation1_start_date" =>  "01/01/2016",
                            "subpopulation1_end_date" =>  "01/30/2016",
                            "subpopulation2_start_date" =>  "07/01/2016",
                            "subpopulation2_end_date" =>  "03/31/2017"
                        ]
                    ]
                ],
                [ // Example 9 test with invalid metadata type
                    [
                        "survey_id" => "11",
                        "year_id" => "201516",
                        "question_id" => 50,
                        "type" => "number",
                        "cohort" => "2",
                        "subpopulation1" => [
                            "is_single" => false,
                            "min_digits" => 0,
                            "max_digits" => 5
                        ],
                        "subpopulation2" => [
                            "is_single" => false,
                            "min_digits" => 6,
                            "max_digits" => 10
                        ]
                    ],
                    "myisp",
                    "Invalid metadata type"
                ],
                [ // Example 10 test with invalid item metadata type
                    [
                        "survey_id" => "11",
                        "year_id" => "201516",
                        "question_id" => 50,
                        "type" => "P",
                        "cohort" => "2",
                        "subpopulation1" => [
                            "is_single" => false,
                            "min_digits" => 0,
                            "max_digits" => 5
                        ],
                        "subpopulation2" => [
                            "is_single" => false,
                            "min_digits" => 6,
                            "max_digits" => 10
                        ]
                    ],
                    "myisp",
                    "Invalid metadata type"
                ]

            ]
        ]);
    }

}