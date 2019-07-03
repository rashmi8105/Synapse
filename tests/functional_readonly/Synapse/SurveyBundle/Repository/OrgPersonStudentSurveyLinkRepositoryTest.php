<?php

use Codeception\TestCase\Test;


class OrgPersonStudentSurveyLinkRepositoryTest extends Test
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
     * @var \Synapse\SurveyBundle\Repository\OrgPersonStudentSurveyLinkRepository
     */
    private $orgPersonStudentSurveyLinkRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->orgPersonStudentSurveyLinkRepository = $this->repositoryResolver->getRepository('SynapseSurveyBundle:OrgPersonStudentSurveyLink');
    }

    public function testListSurveysForStudent()
    {
        $this->specify("Verify the functionality of the method listSurveysForStudent", function($studentId, $orgId, $expectedResult){

            $result = $this->orgPersonStudentSurveyLinkRepository->listSurveysForStudent($studentId, $orgId);

            if($orgId != 214){
                verify($result)->equals($expectedResult);
            }

        }, ["examples"=>
            [
                // This is the example in ESPRJ-9431.  The student's cohort was changed mid-year, and the old function was repeating surveys.
                [4637412, 180, [
                    [
                        'survey_id' => '14',
                        'survey_name' => 'Check-Up Two',
                        'cohort' => '1',
                        'cohort_name' => 'Survey Cohort 1',
                        'year_id' => '201516',
                        'org_academic_year_id' => '35',
                        'year_name' => '2015-16',
                        'open_date' => '2016-04-01 05:00:00',
                        'close_date' => '2016-04-11 05:00:00',
                        'survey_status' => 'closed',
                        'survey_completion_status' => 'Assigned',
                        'has_responses' => 'No',
                        'survey_completion_date' => null,
                        'survey_link' => 'https://survey.skyfactor.com/WESS/language.aspx?stidx=4lJzDAJZaJBjZ1Wssqy0pA==&1'
                    ],
                    [
                        'survey_id' => '13',
                        'survey_name' => 'Transition Two',
                        'cohort' => '1',
                        'cohort_name' => 'Survey Cohort 1',
                        'year_id' => '201516',
                        'org_academic_year_id' => '35',
                        'year_name' => '2015-16',
                        'open_date' => '2016-01-26 05:00:00',
                        'close_date' => '2016-02-07 05:00:00',
                        'survey_status' => 'closed',
                        'survey_completion_status' => 'Assigned',
                        'has_responses' => 'No',
                        'survey_completion_date' => null,
                        'survey_link' => 'https://survey.skyfactor.com/WESS/language.aspx?stidx=d5GxAuhYCtjcXH2Np6Tduw==&1'
                    ],
                    [
                        'survey_id' => '12',
                        'survey_name' => 'Check-Up One',
                        'cohort' => '2',
                        'cohort_name' => 'Survey Cohort 2',
                        'year_id' => '201516',
                        'org_academic_year_id' => '35',
                        'year_name' => '2015-16',
                        'open_date' => '2015-11-21 05:00:00',
                        'close_date' => '2015-12-03 05:00:00',
                        'survey_status' => 'closed',
                        'survey_completion_status' => 'Assigned',
                        'has_responses' => 'No',
                        'survey_completion_date' => null,
                        'survey_link' => 'https://survey.skyfactor.com/WESS/language.aspx?stidx=IGmDFiDNn6hOC4I5y+jLOw==&1'
                    ],
                    [
                        'survey_id' => '11',
                        'survey_name' => 'Transition One',
                        'cohort' => '2',
                        'cohort_name' => 'Survey Cohort 2',
                        'year_id' => '201516',
                        'org_academic_year_id' => '35',
                        'year_name' => '2015-16',
                        'open_date' => '2015-09-23 05:00:00',
                        'close_date' => '2015-10-06 05:00:00',
                        'survey_status' => 'closed',
                        'survey_completion_status' => 'Assigned',
                        'has_responses' => 'No',
                        'survey_completion_date' => null,
                        'survey_link' => 'https://survey.skyfactor.com/WESS/language.aspx?stidx=YFuwQDDKoZD5jkUSv8YWRg==&1'
                    ]
                ]],
                // This is the example in ESPRJ-9779.  The student had a different cohort each year, and the old function was repeating surveys.
                // This test fails because organization 214 doesn't exist.
                [4891105, 214, [
                    [
                        'survey_id' => '15',
                        'survey_name' => 'Transition One',
                        'cohort' => '2',
                        'cohort_name' => 'Survey Cohort 2',
                        'year_id' => '201617',
                        'org_academic_year_id' => '171',
                        'year_name' => '2015-2016',
                        'open_date' => '2016-04-08 06:00:00',
                        'close_date' => '2016-04-14 06:00:00',
                        'survey_status' => 'closed',
                        'survey_completion_status' => '2016-10-01 11:56:00',
                        'has_responses' => 'Yes',
                        'survey_completion_date' => null,
                        'survey_link' => null
                    ],
                    [
                        'survey_id' => '14',
                        'survey_name' => 'Check-Up Two',
                        'cohort' => '1',
                        'cohort_name' => 'Survey Cohort 1',
                        'year_id' => '201516',
                        'org_academic_year_id' => '170',
                        'year_name' => '2015-2016',
                        'open_date' => '2016-02-20 10:00:00',
                        'close_date' => '2016-03-15 10:00:00',
                        'survey_status' => 'closed',
                        'survey_completion_status' => '2016-03-02 10:00:00',
                        'has_responses' => 'Yes',
                        'survey_completion_date' => null,
                        'survey_link' => null
                    ],
                    [
                        'survey_id' => '13',
                        'survey_name' => 'Transition Two',
                        'cohort' => '1',
                        'cohort_name' => 'Survey Cohort 1',
                        'year_id' => '201516',
                        'org_academic_year_id' => '170',
                        'year_name' => '2015-2016',
                        'open_date' => '2016-01-10 10:00:00',
                        'close_date' => '2016-02-19 10:00:00',
                        'survey_status' => 'closed',
                        'survey_completion_status' => '2016-01-20 10:00:00',
                        'has_responses' => 'Yes',
                        'survey_completion_date' => null,
                        'survey_link' => null
                    ],
                    [
                        'survey_id' => '12',
                        'survey_name' => 'Check-Up One',
                        'cohort' => '1',
                        'cohort_name' => 'Survey Cohort 1',
                        'year_id' => '201516',
                        'org_academic_year_id' => '170',
                        'year_name' => '2015-2016',
                        'open_date' => '2015-10-09 10:00:00',
                        'close_date' => '2015-10-28 10:00:00',
                        'survey_status' => 'closed',
                        'survey_completion_status' => 'CompletedAll',
                        'has_responses' => 'Yes',
                        'survey_completion_date' => '2015-10-30 11:00:00',
                        'survey_link' => null
                    ],
                    [
                        'survey_id' => '11',
                        'survey_name' => 'Transition One',
                        'cohort' => '1',
                        'cohort_name' => 'Survey Cohort 1',
                        'year_id' => '201516',
                        'org_academic_year_id' => '170',
                        'year_name' => '2015-2016',
                        'open_date' => '2015-08-09 10:00:00',
                        'close_date' => '2015-10-09 10:00:00',
                        'survey_status' => 'closed',
                        'survey_completion_status' => 'CompletedAll',
                        'has_responses' => 'Yes',
                        'survey_completion_date' => '2015-09-27 11:56:00',
                        'survey_link' => null
                    ]]
                ]
            ]
        ]);
    }

    public function testGetStudentIdsForSurveyAndCohort()
    {

        $this->specify("Verify the functionality of the method GetStudentIdsForSurveyAndCohort", function ($surveyId, $cohort, $organizationId, $excludeArchiveStudents,$expectedCount,$expectedArr) {

            $result = $this->orgPersonStudentSurveyLinkRepository->getStudentIdsForSurveyAndCohort($surveyId, $cohort, $organizationId, $excludeArchiveStudents);
            $res =  array_column($result,'person_id');
            verify(count($res))->equals($expectedCount);
            foreach($expectedArr as $expectedStudent){
                verify($res)->contains($expectedStudent);
            }
        }, [
            "examples" =>[
                    [11,1,203,0,200,[ 4878808, 4878809, 4878810, 4878811, 4878812]],
                    [11,1,203,1,200,[ 4878808, 4878809, 4878810, 4878811, 4878812]],
                    [11,2,203,1,0,[]],
                    [11,1,190,1,292,[ 4708233,4708234,4708235,4708236,4708237,4708238]],
                    [11,1,190,0,292,[ 4708242,4708243,4708244, 4708246,4708247,4708248]],
                ]

        ]);


    }


}