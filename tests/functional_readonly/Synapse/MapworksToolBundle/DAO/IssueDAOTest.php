<?php
use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Dump\Container;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\MapworksToolBundle\DAO\IssueDAO;

class IssueDAOTest extends Test
{
    use Codeception\Specify;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var IssueDAO
     */
    private $issueDAO;

    private $organizationId = 189;

    private $facultyId = 256048;

    private $fetchCount = 10;

    public function _before()
    {
        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->issueDAO = $this->container->get(IssueDAO::DAO_KEY);
    }


    public function testGetTopIssuesFromStudentIssues()
    {
        $this->specify('test testGenerateStudentIssuesTemporaryTable method', function ($orgAcedmicYearId, $surveyId, $cohort, $expectedCount, $studentIds, $expectedResults) {
            $this->callProcedure($orgAcedmicYearId, $surveyId, $cohort);
            $result = $this->issueDAO->getTopIssuesFromStudentIssues($this->fetchCount, $studentIds);
            $this->assertLessThanOrEqual(count($result), $expectedCount);
            $this->assertEquals($result, $expectedResults);
        }, [
            "examples" => [
                // Note: all example fetches first 10 results but actual number of results may vary with provided input to procedure
                //       call so below are the examples that tests these cases.

                // 1. example that results into no student issues
                [
                    154, // orgAcedmicYearId
                    11,  // surveyId
                    1, // cohort
                    0, // expectedCount
                    [], // studentIds
                    []  // expectedResults
                ],
                // 2. example that have student issues , no student id(s) provided
                [
                    33,
                    11,
                    1,
                    10,
                    [],
                    [
                        [
                            'issue_id' => '16',
                            'issue_name' => 'Homesick (separation)',
                            'numerator' => '29',
                            'denominator' => '57',
                            'percent' => '50.9',
                            'icon' => 'large-report-icon-homesick.png',
                        ],
                        [
                            'issue_id' => '26',
                            'issue_name' => 'Test Anxiety',
                            'numerator' => '30',
                            'denominator' => '67',
                            'percent' => '44.8',
                            'icon' => 'large-report-icon-academic.png',
                        ],
                        [
                            'issue_id' => '5',
                            'issue_name' => 'Struggling in at least 2 courses',
                            'numerator' => '30',
                            'denominator' => '70',
                            'percent' => '42.9',
                            'icon' => 'large-report-icon-courses.png',
                        ],
                        [
                            'issue_id' => '10',
                            'issue_name' => 'Not confident about finances',
                            'numerator' => '15',
                            'denominator' => '69',
                            'percent' => '21.7',
                            'icon' => 'large-report-icon-finances.png',
                        ],
                        [
                            'issue_id' => '7',
                            'issue_name' => 'Low analytical skills',
                            'numerator' => '13',
                            'denominator' => '70',
                            'percent' => '18.6',
                            'icon' => 'large-report-icon-academic.png',
                        ],
                        [
                            'issue_id' => '17',
                            'issue_name' => 'Homesick (distressed)',
                            'numerator' => '9',
                            'denominator' => '56',
                            'percent' => '16.1',
                            'icon' => 'large-report-icon-homesick.png',
                        ],
                        [
                            'issue_id' => '20',
                            'issue_name' => 'Low social integration',
                            'numerator' => '10',
                            'denominator' => '66',
                            'percent' => '15.2',
                            'icon' => 'large-report-icon-homesick.png',
                        ],
                        [
                            'issue_id' => '12',
                            'issue_name' => 'Low advanced academic behaviors',
                            'numerator' => '10',
                            'denominator' => '68',
                            'percent' => '14.7',
                            'icon' => 'large-report-icon-academic.png',
                        ],
                        [
                            'issue_id' => '6',
                            'issue_name' => 'Low communication skills',
                            'numerator' => '8',
                            'denominator' => '70',
                            'percent' => '11.4',
                            'icon' => 'large-report-icon-academic.png',
                        ],
                        [
                            'issue_id' => '27',
                            'issue_name' => 'Low advanced study skills',
                            'numerator' => '7',
                            'denominator' => '67',
                            'percent' => '10.4',
                            'icon' => 'large-report-icon-academic.png',
                        ]
                    ]
                ],
                //2 (a) example that have student issues , student id(s) provided
                [
                    33,
                    11,
                    1,
                    10,
                    [4672380, 4672385, 4672391, 4672392, 4672397, 4672398, 4672400, 4672407, 4672412, 4672435, 4672437, 4672438, 4672446, 4672450, 4672453, 4672466, 4672480, 4672490, 4672495, 4672503, 4672505, 4672506, 4672508, 4672511, 4672521, 4672526, 4672528, 4672530, 4672535, 4672542, 4672546, 4672547, 4672557, 4672564, 4672568, 4672570, 4672572, 4672577, 4672584, 4672587, 4672590, 4672606, 4672609, 4672611, 4672623, 4672638, 4672639, 4672655, 4672661, 4672662, 4672664, 4672666, 4672673, 4672674, 4672679, 4672687, 4672692, 4672695, 4672702, 4672703, 4672706, 4672710, 4672716, 4672720, 4672731, 4672735, 4672737, 4672748, 4672750, 4672755],
                    [
                        [
                            'issue_id' => '16',
                            'issue_name' => 'Homesick (separation)',
                            'numerator' => '29',
                            'denominator' => '57',
                            'percent' => '50.9',
                            'icon' => 'large-report-icon-homesick.png',
                        ],
                        [
                            'issue_id' => '26',
                            'issue_name' => 'Test Anxiety',
                            'numerator' => '30',
                            'denominator' => '67',
                            'percent' => '44.8',
                            'icon' => 'large-report-icon-academic.png',
                        ],
                        [
                            'issue_id' => '5',
                            'issue_name' => 'Struggling in at least 2 courses',
                            'numerator' => '30',
                            'denominator' => '70',
                            'percent' => '42.9',
                            'icon' => 'large-report-icon-courses.png',
                        ],
                        [
                            'issue_id' => '10',
                            'issue_name' => 'Not confident about finances',
                            'numerator' => '15',
                            'denominator' => '69',
                            'percent' => '21.7',
                            'icon' => 'large-report-icon-finances.png',
                        ],
                        [
                            'issue_id' => '7',
                            'issue_name' => 'Low analytical skills',
                            'numerator' => '13',
                            'denominator' => '70',
                            'percent' => '18.6',
                            'icon' => 'large-report-icon-academic.png',
                        ],
                        [
                            'issue_id' => '17',
                            'issue_name' => 'Homesick (distressed)',
                            'numerator' => '9',
                            'denominator' => '56',
                            'percent' => '16.1',
                            'icon' => 'large-report-icon-homesick.png',
                        ],
                        [
                            'issue_id' => '20',
                            'issue_name' => 'Low social integration',
                            'numerator' => '10',
                            'denominator' => '66',
                            'percent' => '15.2',
                            'icon' => 'large-report-icon-homesick.png',
                        ],
                        [
                            'issue_id' => '12',
                            'issue_name' => 'Low advanced academic behaviors',
                            'numerator' => '10',
                            'denominator' => '68',
                            'percent' => '14.7',
                            'icon' => 'large-report-icon-academic.png',
                        ],
                        [
                            'issue_id' => '6',
                            'issue_name' => 'Low communication skills',
                            'numerator' => '8',
                            'denominator' => '70',
                            'percent' => '11.4',
                            'icon' => 'large-report-icon-academic.png',
                        ],
                        [
                            'issue_id' => '27',
                            'issue_name' => 'Low advanced study skills',
                            'numerator' => '7',
                            'denominator' => '67',
                            'percent' => '10.4',
                            'icon' => 'large-report-icon-academic.png',
                        ]
                    ]
                ],
                //2 (b) example that have student issues , some invalid student id(s) provided , gives no result
                [
                    33,
                    11,
                    1,
                    0,
                    [564266],
                    []
                ],


            ]
        ]);
    }


    public function testGetStudentListFromStudentIssues()
    {
        $this->specify('test getTopIssuesFromStudentIssues method', function ($orgAcademicYearId, $surveyId, $cohort, $issueIds, $expectedResults) {
            $this->callProcedure($orgAcademicYearId, $surveyId, $cohort);
            $result = $this->issueDAO->getStudentListFromStudentIssues($issueIds);
            $this->assertEquals($result, $expectedResults);
        }, [
            "examples" => [
                // example fetch student id(s) with provided issue id(s)
                [
                    33, // orgAcedmicYearId
                    11,  // surveyId
                    1, // cohort
                    [5, 10, 26, 7, 14, 16, 4, 6, 12, 13, 15, 17, 19, 20, 21, 22, 23, 3, 24, 9, 27, 1], // issue ids
                    [4672380, 4672385, 4672391, 4672392, 4672397, 4672398, 4672407, 4672412, 4672435, 4672437, 4672438, 4672446, 4672450, 4672453, 4672466, 4672480, 4672490, 4672495, 4672503, 4672505, 4672506, 4672508, 4672511, 4672526, 4672528, 4672530, 4672535, 4672542, 4672546, 4672547, 4672564, 4672568, 4672570, 4672572, 4672577, 4672584, 4672590, 4672606, 4672609, 4672611, 4672623, 4672655, 4672661, 4672662, 4672664, 4672666, 4672673, 4672674, 4672679, 4672687, 4672692, 4672695, 4672702, 4672703, 4672710, 4672716, 4672720, 4672731, 4672735, 4672748, 4672750] // expectedResults
                ],
                // example with no issue id provided has no results
                [
                    33,
                    11,
                    1,
                    [],
                    []
                ],
            ]
        ]);
    }

    public function testGetDistinctStudentPopulationCount()
    {
        $this->specify('test getDistinctStudentPopulationCount method', function ($orgAcademicYearId, $surveyId, $cohort, $issueIds, $expectedCount) {
            $this->callProcedure($orgAcademicYearId, $surveyId, $cohort);
            $totalStudents = $this->issueDAO->getDistinctStudentPopulationCount($issueIds);
            $this->assertEquals($expectedCount, $totalStudents);
        }, [
            "examples" => [
                // example with given parameters resulting non-zero count
                [
                    33, // orgAcedmicYearId
                    11,  // surveyId
                    1, // cohort
                    [1, 26, 5, 10, 7],
                    70 //expectedCount
                ],
                // example with given parameters resulting zero count
                [
                    154,
                    11,
                    1,
                    [1, 26, 5, 10, 7],
                    0
                ],
                //Returns 0 if no issues are passed
                [
                    154,
                    11,
                    1,
                    [],
                    0
                ],
            ]
        ]);
    }

    public function testGetDistinctParticipantStudentPopulationCount()
    {
        $this->specify('test getDistinctStuden`tPopulationCount method', function ($organizationId, $facultyId, $orgAcademicYearId, $surveyId, $cohort, $issueIds, $expectedCount, $hasIssue = []) {
            //Overriding the Procedure Call to Valid Year 33 so I can test this specific function
            $this->callProcedure($orgAcademicYearId, $surveyId, $cohort, $facultyId, $organizationId);
            $totalStudents = $this->issueDAO->getDistinctParticipantStudentPopulationCount($issueIds, $orgAcademicYearId, $hasIssue);
            $this->assertEquals($expectedCount, $totalStudents);
        }, [
            "examples" => [
                // example with valid year resulting non-zero count and both have and do not have issues
                [
                    45, //Overriding normal faculty and org to get special one with long academic year
                    185273,
                    110, // orgAcademicYearId
                    13,  // surveyId
                    1, // cohort
                    [35, 33, 34, 32, 64], //issues
                    518 //expectedCount
                ],
                // example with invalid academic year resulting zero count
                [
                    null, //Use Standard Org/Faculty
                    null,
                    154,// orgAcademicYearId
                    11, // surveyId
                    1, // cohort
                    [1, 26, 5, 10, 7], //issues
                    0 //Expected Count
                ],
                //Returns 0 if no issues are passed
                [
                    null, //Use Standard Org/Faculty
                    null,
                    154, // orgAcademicYearId
                    90, // surveyId
                    1, // cohort
                    [], //issues
                    0 //expectedCount
                ],
                //Valid Year with non-zero count and only having issues
                [
                    45, //Overriding normal faculty and org to get special one with long academic year
                    185273,
                    110, // orgAcademicYearId
                    13,  // surveyId
                    1, // cohort
                    [35, 33, 34, 32, 64], //issues
                    267, //expectedCount
                    1 //Only Students who have issues
                ],

            ]
        ]);
    }




    private function callProcedure($orgAcademicYearId, $surveyId, $cohort, $facultyId = null, $organizationId = null)
    {
        if (is_null($facultyId)) {
            $facultyId = $this->facultyId;
        }

        if (is_null($organizationId)) {
            $organizationId = $this->organizationId;
        }

        $this->issueDAO->generateStudentIssuesTemporaryTable($organizationId, $facultyId, $orgAcademicYearId, $surveyId, $cohort);
    }

}