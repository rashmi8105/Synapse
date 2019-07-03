<?php
namespace Synapse\ReportsBundle\Service\Impl;

use Codeception\Specify;
use JMS\DiExtraBundle\Annotation as DI;


/*
 * Tests That Need More Cases
 * testBuildTermsForRiskGPA
 * testbuildTermsForGPA
 * testCollateByYearTermAndReplaceId
 *
 */

class GpaReportServiceTest extends \Codeception\Test\Unit
{


    use Specify;


    public function testAreMandatoryFiltersSet()
    {
        $this->specify("Test if risk level function is working is working in all cases", function ($mandatoryFilter, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockResque = $this->getMock('resque', array('enqueue'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));


            $GPAReportService = new GPAReportService($mockRepositoryResolver, $mockLogger, $mockContainer, $mockResque);

            $isMandatorySet = $GPAReportService->areMandatoryFiltersSet($mandatoryFilter);

            $this->assertEquals($expectedResult, $isMandatorySet);

        }, ['examples' => [
            [array(1, 1, 1, 1), true],
            [array("0", 1, 1, 1), false],
            [array("", 1, 1, 1), false],
            [array(array(), 1, 1, 1), false],
            [array(array(1, 2), 1, 1, 1, 1), true],
            [array("Here I am", 1, 1, 1, 1), true],
            [array(0, 1, 1, 1, 1), false],
            [array(-1, 1, 1, 1), true],
            [array(NULL, 1, 1, 1), false]
        ]]);

    }


    /*
    public function testBuildTermsJSON(){
        $this->specify("build term section of GPA JSON with year->term format", function ($gpaWithIds, $yearId, $termName,$riskWithGpaHolder, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockResque = $this->getMock('resque', array('enqueue'));
            $mockLogger = $this->getMock('Logger', array('debug','error'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockTerm = $this->getMock('orgAcademicTerm', array('getName'));

            $orgAcademicTermRepository = $this->getMock('orgAcademicTermRepository', array('findOneBy'));

            $orgAcademicTermRepository->expects($this->any())->method('findOneBy')->willReturn($mockTerm);
            $mockTerm->expects($this->any())->method('getName')->willReturn($termName);
            $GPAReportService = new GPAReportService($mockRepositoryResolver, $mockLogger, $mockContainer, $mockResque);

            $termJSON = $GPAReportService->buildTermsJSON($gpaWithIds, $yearId, $riskWithGpaHolder, $orgAcademicTermRepository);
            $this->assertEquals($expectedResult, $termJSON);


        }, ['examples'=>[
            [[
                [ 'org_academic_year_id' => 1, 'org_academic_terms_id' => 2, 'mean_gpa' => "2.25", 'percent_under_2' => "50.00", 'student_count' => "4"],
                #[ 'org_academic_year_id' => 1, 'org_academic_terms_id' => 30, 'mean_gpa' => "1.50", 'percent_under_2' => "50.00", 'student_count' => "2"]
                #[ 'org_academic_year_id' => 2, 'org_academic_terms_id' => 3, 'mean_gpa' => "1.75", 'percent_under_2' => "50.00", 'student_count' => "4"],
                #[ 'org_academic_year_id' => 10, 'org_academic_terms_id' => 30, 'mean_gpa' => "3.50", 'percent_under_2' => "0.00", 'student_count' => "1"]

            ], 1,'term',
                [1 => [2 => ['risk_color' => "yellow", 'student_count' => 4, 'mean_gpa' => "2.25"]]],
                [0 =>
                    ["term_name" => "term",
                     "student_count" => "4",
                     "mean_gpa" => "2.25",
                     "percent_under_2" => "50.00",
                        "gpa_summary_by_risk" => [
                            "risk_color" => "yellow",
                            "student_count" => "4",
                            "mean_gpa" => "2.25"]]
                    ]
            ], [[
                [ 'org_academic_year_id' => 1, 'org_academic_terms_id' => 2, 'mean_gpa' => "2.25", 'percent_under_2' => "50.00", 'student_count' => "4"],
                [ 'org_academic_year_id' => 1, 'org_academic_terms_id' => 30, 'mean_gpa' => "1.50", 'percent_under_2' => "100.00", 'student_count' => "2"],
                [ 'org_academic_year_id' => 1, 'org_academic_terms_id' => 3, 'mean_gpa' => "1.75", 'percent_under_2' => "50.00", 'student_count' => "4"]
            ], 1,'term', [1 => [2 => ['risk_color' => "yellow", 'student_count' => 4, 'mean_gpa' => "2.25"], 30 =>['risk_color' => "yellow", 'student_count' => 2, 'mean_gpa' => "2.25"], 3 =>['risk_color' => 'gray', 'student_count' => 0]]],
                [
                    ["term_name" => "term",
                        "student_count" => "4",
                        "mean_gpa" => "2.25",
                        "percent_under_2" => "50.00",
                        "gpa_summary_by_risk" => [
                            "risk_color" => "yellow",
                            "student_count" => "4",
                            "mean_gpa" => "2.25"]],

                ["term_name" => "term",
                    "student_count" => "2",
                    "mean_gpa" => "1.50",
                    "percent_under_2" => "100.00",
                    "gpa_summary_by_risk" => [
                        "risk_color" => "yellow",
                        "student_count" => "2",
                        "mean_gpa" => "2.25"]],

                ["term_name" => "term",
                    "student_count" => "4",
                    "mean_gpa" => "1.75",
                    "percent_under_2" => "50.00",
                    "gpa_summary_by_risk" => [
                        "risk_color" => "gray",
                        "student_count" => "0"]]
                ]
            ]
        ]]);

    }*/

    public function testCollateByYearTermAndReplaceId()
    {
        $this->specify("re-organizing the structure where year is the uppermost level and collating all data accordingly", function ($yearName, $gpaWithIds, $gpaWithRisk, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockResque = $this->getMock('resque', array('enqueue'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockReportHelper = $this->getMock('reportsHelper', array('replaceYearidWithName', 'replaceTermIdWithName'));
            $mockYear = $this->getMock('orgAcademicYear', array('getName'));
            $mockTerm = $this->getMock('orgAcademicTerm', array('getName'));
            $orgAcademicYearRepository = $this->getMock('orgAcademicYearRepository', array('findOneBy'));
            $orgAcademicTermRepository = $this->getMock('orgAcademicTermRepository', array('findOneBy'));

            $gpaTerms = "TermStub";
            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                ['SynapseAcademicBundle:OrgAcademicYear', $orgAcademicYearRepository],
                ['SynapseAcademicBundle:OrgAcademicTerms', $orgAcademicTermRepository]
            ]);

            $orgAcademicYearRepository->expects($this->any())->method('findOneBy')->willReturn($mockYear);
            $mockYear->expects($this->any())->method('getName')->willReturn($yearName);
            $orgAcademicTermRepository->expects($this->any())->method('findOneBy')->willReturn($mockTerm);
            $mockTerm->expects($this->any())->method('getName')->willReturn($gpaTerms);

            $mockReportHelper->expects($this->any())->method('replaceYearIdWithName')->willReturn($yearName);
            $mockReportHelper->expects($this->any())->method('replaceTermIdWithName')->willReturn($gpaTerms);

            $GPAReportService = new GPAReportService($mockRepositoryResolver, $mockLogger, $mockContainer, $mockResque);

            $reportJSON = $GPAReportService->collateByYearTermAndReplaceId($gpaWithIds, $gpaWithRisk, $mockReportHelper);
            $this->assertEquals($reportJSON, $expectedResult);

        }, ['examples' => [
            ['2015-2016 Academic Year', [
                ['org_academic_year_id' => 1, 'org_academic_terms_id' => 2, 'mean_gpa' => "2.25", 'percent_under_2' => "50.00", 'student_count' => "4"]
                #[ 'org_academic_year_id' => 1, 'org_academic_terms_id' => 30, 'mean_gpa' => "1.50", 'percent_under_2' => "50.00", 'student_count' => "2"],
                #[ 'org_academic_year_id' => 2, 'org_academic_terms_id' => 3, 'mean_gpa' => "1.75", 'percent_under_2' => "50.00", 'student_count' => "4"],
                #[ 'org_academic_year_id' => 10, 'org_academic_terms_id' => 30, 'mean_gpa' => "3.50", 'percent_under_2' => "0.00", 'student_count' => "1"]

            ], [
                ['org_id' => "99", 'risk_level' => null, 'org_academic_year_id' => "1", 'org_academic_terms_id' => "2", 'mean_value' => "2.25", 'student_count' => "4"]
                #[ 'org_academic_year_id' => 1, 'org_academic_terms_id' => 30, 'mean_gpa' => "1.50", 'percent_under_2' => "50.00", 'student_count' => "2"],
                #[ 'org_academic_year_id' => 2, 'org_academic_terms_id' => 3, 'mean_gpa' => "1.75", 'percent_under_2' => "50.00", 'student_count' => "4"],
                #[ 'org_academic_year_id' => 10, 'org_academic_terms_id' => 30, 'mean_gpa' => "3.50", 'percent_under_2' => "0.00", 'student_count' => "1"]

            ],
                [[
                    'year_name' => '2015-2016 Academic Year',
                    'gpa_summary_by_term' => [0 => [
                        'term_name' => "TermStub",
                        'student_count' => "4",
                        'mean_gpa' => "2.25",
                        'percent_under_2' => "50.00",
                        'gpa_summary_by_risk' => [['risk_color' => null, 'student_count' => "4", 'mean_gpa' => "2.25"]]
                    ]]
                ]
                ]]]]);


    }

    /*
    * Examines String to Array Conversion
    * Only One Test Case Needed
    */

    public function testGetFilteredStudents()
    {
        $this->specify("get Filtered Students and process into array", function ($studentString, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockResque = $this->getMock('resque', array('enqueue'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockReportRunningStatusDto = $this->getMock('reportRunningStatus', array('getFilteredStudentIds'));

            $mockReportRunningStatusDto->expects($this->at(0))->method('getFilteredStudentIds')->willReturn($studentString);


            $GPAReportService = new GPAReportService($mockRepositoryResolver, $mockLogger, $mockContainer, $mockResque);

            $studentListAsArray = $GPAReportService->getFilteredStudentIds($mockReportRunningStatusDto);

            $this->assertEquals($expectedResult, $studentListAsArray);

        }, ['examples' => [
            ['1, 2, 3, 40', array(1, 2, 3, 40)],
            ['', array()], //Checking for rare condition if the studentList is an empty string
            ['1,0,2,,3', array(0 => 1, 2 => 2, 4 => 3)]//Eliminating Bad Values from Array
        ]]);


    }


    public function testGetMeanGPAandPercentUnder2()
    {
        $this->specify("Test Mean GPA and Percent Under 2 function with variations of year and term", function ($studentsWithGPA, $expectedResult, $badFilter = false, $ac_bad_years = array(), $ac_bad_terms = array()) {
            $academic_years = array();
            $academic_terms = array();

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockResque = $this->getMock('resque', array('enqueue'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));
            $GPAReportService = new GPAReportService($mockRepositoryResolver, $mockLogger, $mockContainer, $mockResque);
            if ($badFilter) {
                $academic_years[] = $ac_bad_years;
                $academic_terms[] = $ac_bad_terms;
            } else {

                $academic_years[] = array_unique(array_column($studentsWithGPA, 'org_academic_year_id'));
                $academic_terms[] = array_unique(array_column($studentsWithGPA, 'org_academic_terms_id'));
            }

            $gpaTestArray = $GPAReportService->getMeanGPAandPercentUnder2($studentsWithGPA, $academic_years[0], $academic_terms[0]);

            /*
             * Test 1: get Mean and % all the same
             * Test 2: Mixed, get Mean and % with no year : records with year
             * Test 3: Mixed, get Mean and % with year, no term : year with term
             * Test 4: Mixed, get Mean and % with year records: year with term
             * Test 5: Mixed(4). get Mean and % with year with term: different year with term
             * Test 6: get Mean and % for single record
             * Test 7: No Records Passed
             * Test 8: NonMatching Year Filter
             * Test 9: NonMatching Term Filter
             * Test 10: Doesn't Pick up extraneous filter data (never gets bigger)
             */

            $this->assertEquals($gpaTestArray, $expectedResult);
        }, ['examples' =>
                [
                    [
                        [
                            ['person_id' => 123, 'metadata_value' => 2.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 1],
                            ['person_id' => 4, 'metadata_value' => 3.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 1],
                            ['person_id' => 5, 'metadata_value' => 2.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 1],
                            ['person_id' => 6, 'metadata_value' => 2.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 1],
                            ['person_id' => 7, 'metadata_value' => 2.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 1],
                            ['person_id' => 9, 'metadata_value' => 1.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 1]
                        ],
                        [['org_academic_year_id' => 1, 'org_academic_terms_id' => 1, 'mean_gpa' => "2.50", 'percent_under_2' => "16.67", 'student_count' => "6"]]

                    ],
                    [
                        [
                            ['person_id' => 123, 'metadata_value' => 3.00, 'org_academic_year_id' => '', 'org_academic_terms_id' => ''],
                            ['person_id' => 4, 'metadata_value' => 4.00, 'org_academic_year_id' => '', 'org_academic_terms_id' => ''],
                            ['person_id' => 5, 'metadata_value' => 2.50, 'org_academic_year_id' => '', 'org_academic_terms_id' => ''],
                            ['person_id' => 6, 'metadata_value' => 2.50, 'org_academic_year_id' => '', 'org_academic_terms_id' => ''],
                            ['person_id' => 7, 'metadata_value' => 2.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => ''],
                            ['person_id' => 9, 'metadata_value' => 1.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => '']
                        ],
                        [
                            ['org_academic_year_id' => '', 'org_academic_terms_id' => '', 'mean_gpa' => "3.00", 'percent_under_2' => "0.00", 'student_count' => "4"],
                            ['org_academic_year_id' => 1, 'org_academic_terms_id' => '', 'mean_gpa' => "2.00", 'percent_under_2' => "50.00", 'student_count' => "2"]
                        ]

                    ],
                    [
                        [
                            ['person_id' => 123, 'metadata_value' => 2.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => ''],
                            ['person_id' => 4, 'metadata_value' => 3.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => ''],
                            ['person_id' => 5, 'metadata_value' => 2.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => ''],
                            ['person_id' => 6, 'metadata_value' => 0.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => ''],
                            ['person_id' => 7, 'metadata_value' => 1.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 2],
                            ['person_id' => 9, 'metadata_value' => 1.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 2]
                        ],
                        [
                            ['org_academic_year_id' => 1, 'org_academic_terms_id' => '', 'mean_gpa' => "2.25", 'percent_under_2' => "25.00", 'student_count' => "4"],
                            ['org_academic_year_id' => 1, 'org_academic_terms_id' => 2, 'mean_gpa' => "1.50", 'percent_under_2' => "100.00", 'student_count' => "2"]
                        ]
                    ],
                    [
                        [
                            ['person_id' => 123, 'metadata_value' => 2.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 3],
                            ['person_id' => 4, 'metadata_value' => 3.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 3],
                            ['person_id' => 5, 'metadata_value' => 2.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 3],
                            ['person_id' => 6, 'metadata_value' => 0.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 3],
                            ['person_id' => 7, 'metadata_value' => 1.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 2],
                            ['person_id' => 9, 'metadata_value' => 1.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 2]
                        ],
                        [
                            ['org_academic_year_id' => 1, 'org_academic_terms_id' => 3, 'mean_gpa' => "2.25", 'percent_under_2' => "25.00", 'student_count' => "4"],
                            ['org_academic_year_id' => 1, 'org_academic_terms_id' => 2, 'mean_gpa' => "1.50", 'percent_under_2' => "100.00", 'student_count' => "2"]
                        ]
                    ],
                    [
                        [
                            ['person_id' => 123, 'metadata_value' => 2.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 2],
                            ['person_id' => 4, 'metadata_value' => 3.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 2],
                            ['person_id' => 5, 'metadata_value' => 2.50, 'org_academic_year_id' => 2, 'org_academic_terms_id' => 3],
                            ['person_id' => 6, 'metadata_value' => 0.50, 'org_academic_year_id' => 2, 'org_academic_terms_id' => 3],
                            ['person_id' => 7, 'metadata_value' => 1.50, 'org_academic_year_id' => 2, 'org_academic_terms_id' => 3],
                            ['person_id' => 123, 'metadata_value' => 2.50, 'org_academic_year_id' => 2, 'org_academic_terms_id' => 3],
                            ['person_id' => 4, 'metadata_value' => 3.50, 'org_academic_year_id' => 10, 'org_academic_terms_id' => 30],
                            ['person_id' => 5, 'metadata_value' => 2.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 30],
                            ['person_id' => 6, 'metadata_value' => 0.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 30],
                            ['person_id' => 7, 'metadata_value' => 1.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 2],
                            ['person_id' => 9, 'metadata_value' => 1.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 2]
                        ],
                        [
                            ['org_academic_year_id' => 1, 'org_academic_terms_id' => 2, 'mean_gpa' => "2.25", 'percent_under_2' => "50.00", 'student_count' => "4"],
                            ['org_academic_year_id' => 1, 'org_academic_terms_id' => 30, 'mean_gpa' => "1.50", 'percent_under_2' => "50.00", 'student_count' => "2"],
                            ['org_academic_year_id' => 2, 'org_academic_terms_id' => 3, 'mean_gpa' => "1.75", 'percent_under_2' => "50.00", 'student_count' => "4"],
                            ['org_academic_year_id' => 10, 'org_academic_terms_id' => 30, 'mean_gpa' => "3.50", 'percent_under_2' => "0.00", 'student_count' => "1"]

                        ]
                    ],
                    [
                        [
                            ['person_id' => 123, 'metadata_value' => 2.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 2]

                        ],
                        [
                            ['org_academic_year_id' => 1, 'org_academic_terms_id' => 2, 'mean_gpa' => "2.50", 'percent_under_2' => "0.00", 'student_count' => "1"]

                        ]
                    ],
                    [
                        [
                            /*No Records Passed*/

                        ],
                        [
                            /*Expect Nothing*/

                        ]
                    ],
                    [
                        [
                            ['person_id' => 123, 'metadata_value' => 2.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 2]

                        ],
                        [
                            /*Expect Nothing*/
                        ],
                        true,/* Passing MisMatched Filter */
                        array(2), /*year*/
                        array(2)/*term*/
                    ],
                    [
                        [
                            ['person_id' => 123, 'metadata_value' => 2.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 2]

                        ],
                        [
                            /*Expect Nothing*/
                        ],
                        true,/* Passing MisMatched Filter */
                        array(1), /*year*/
                        array(3)/*term*/
                    ],
                    [
                        [
                            ['person_id' => 123, 'metadata_value' => 2.50, 'org_academic_year_id' => 1, 'org_academic_terms_id' => 2]

                        ],
                        [
                            ['org_academic_year_id' => 1, 'org_academic_terms_id' => 2, 'mean_gpa' => "2.50", 'percent_under_2' => "0.00", 'student_count' => "1"]
                        ],
                        true,/* Passing MisMatched Filter */
                        array(1, 2, 3, 4, 5), /*year*/
                        array(1, 2, 3, 4, 5)/*term*/
                    ]

                ]
            ]
        );
    }

}