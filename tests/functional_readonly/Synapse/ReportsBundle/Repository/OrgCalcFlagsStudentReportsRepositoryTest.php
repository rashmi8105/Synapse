<?php

use Codeception\TestCase\Test;

class OrgCalcFlagsStudentReportsRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\ReportsBundle\Repository\OrgCalcFlagsStudentReportsRepository
     */
    private $orgCalcFlagsStudentReportsRepository;


    public function _before(){
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->orgCalcFlagsStudentReportsRepository = $this->repositoryResolver->getRepository('SynapseReportsBundle:OrgCalcFlagsStudentReports');
    }

    public function testGetStudentsWithCalculatedReportAndNoPDF()
    {
        $this->specify("Verify the functionality of the method GetStudentsWithCalculatedReportAndNoPDF", function ($expectedCount, $expectedResults) {
            $results = $this->orgCalcFlagsStudentReportsRepository->getStudentsWithCalculatedReportAndNoPDF();
            verify(count($results))->equals($expectedCount);
            verify($results)->contains($expectedResults);

        }, ["examples" =>
            [
                [7, ['student_id' => 4834226, 'survey_id' => 13]]
            ]
        ]);
    }


    public function testGetStudentsNeedingPartiallyCompleteStudentReportEmail()
    {
        $this->specify("Verify the functionality of the method GetStudentsNeedingPartiallyCompleteStudentReportEmail", function ($expectedCount, $expectedResults) {
            $results = $this->orgCalcFlagsStudentReportsRepository->getStudentsNeedingPartiallyCompleteStudentReportEmail();
            verify(count($results))->equals($expectedCount);
            verify($results)->contains($expectedResults);

        }, ["examples" =>
            [
                [538, ['ocfsr_id' => 6448, 'student_id' => 4876278]]
            ]
        ]);
    }


    public function testGetStudentsNeedingCompletedStudentReportEmail()
    {
        $this->specify("Verify the functionality of the method StudentsNeedingCompletedStudentReportEmail", function ($expectedCount, $expectedResults) {
            $results = $this->orgCalcFlagsStudentReportsRepository->getStudentsNeedingCompletedStudentReportEmail();
            verify(count($results))->equals($expectedCount);
            foreach ($expectedResults as $expectedResult) {
                verify($results)->contains($expectedResult);
            }

        }, ["examples" =>
            [
                [433, [
                    ['ocfsr_id' => 196430, 'student_id' => 4772213],
                    ['ocfsr_id' => 20609, 'student_id' => 1031864],
                    ['ocfsr_id' => 20610, 'student_id' => 1032229],
                    ['ocfsr_id' => 21456, 'student_id' => 887708],
                    ['ocfsr_id' => 26849, 'student_id' => 4846981]
                ]
                ]
            ]]);
    }

    public function testGetStudentSurveyDetailsUsingStudentReportID()
    {
        $this->specify("Get student survey details from report id ", function($studentReportID, $expectedResult){

            $result = $this->orgCalcFlagsStudentReportsRepository->getStudentSurveyDetailsUsingStudentReportID($studentReportID);
            verify($result)->equals($expectedResult);

        }, ['examples'=>
            [
                [
                    1,
                    [
                        'cohort'=>'3',
                        'survey_id'=>'11',
                        'year_id'=>'201516',
                        'person_id'=>'4628420'

                    ]
                ],
                [
                    2,
                    [
                        'cohort'=>'3',
                        'survey_id'=>'11',
                        'year_id'=>'201516',
                        'person_id'=>'4628421'
                    ]
                ]
            ]
        ]);
    }

    public function testGetStudentReportId()
    {
        $this->specify("Verify the functionality of the method GetStudentReportId", function ($studentId, $surveyId, $expectedResult) {

            $results = $this->orgCalcFlagsStudentReportsRepository->getStudentReportId($studentId, $surveyId);
            verify($results)->equals($expectedResult);

        }, ["examples" =>
            [
                // Example 1: Expected calculated_student_report_id should be 21457.
                [4834226, 13, [['calculated_student_report_id' => 21457]]],

                // Example 2: Expected calculated_student_report_id should be 202722.
                [4763562, 13, [['calculated_student_report_id' => 202722]]],

                // Example 3: Expected calculated_student_report_id should be 130249.
                [4877142, 14, [['calculated_student_report_id' => 130249]]],

                // Example 4: studentId is null, expected result should be empty.
                [null, 14, []],

                // Example 5: surveyId is null, expected result should be empty.
                [4877142, null, []],

                // Example 6: studentId and surveyId are null, expected result should be empty
                [null, null, []]

            ]
        ]);
    }


}