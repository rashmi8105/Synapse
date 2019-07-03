<?php
namespace Synapse\CoreBundle\Service\Impl;

use Codeception\Specify;
use Codeception\Test\Unit;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Repository\OrgQuestionRepository;
use Synapse\CoreBundle\Repository\SurveyLangRepository;
use Synapse\SurveyBundle\EntityDto\SurveyQuestionsArrayDto;
use Synapse\SurveyBundle\Repository\SurveyQuestionsRepository;

class OrgQuestionServiceTest extends Unit
{
    use Specify;

    /**
     * @var questionTypes
     */
    private $questionTypes = [
        'D',
        'Q'
    ];

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRepositoryResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLogger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContainer;


    protected function _before()
    {
        $this->mockContainer = $this->getMock('Container', ['get']);
        $this->mockLogger = $this->getMock('Logger', ['debug', 'error']);
        $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
    }

    private function setISQdata($orgQuestions)
    {
        $isqQuestions = array();
        foreach ($orgQuestions as $isqQuestionData) {
            $options = '';
            $orgQuestion = new SurveyQuestionsArrayDto();
            $orgQuestion->setId($isqQuestionData['org_question_id']);
            $orgQuestion->setQuestionText($isqQuestionData['question_text']);
            $type = '';
            if (in_array(trim($isqQuestionData['question_type']), $this->questionTypes)) {
                $type = 'category';
            } elseif (strtoupper($isqQuestionData['question_type']) == 'NA') {
                $type = 'number';
            } elseif (strtoupper($isqQuestionData['question_type']) == 'MR') {
                $type = 'multiresponse';
            } else {
                $type = '';
            }
            $orgQuestion->setType($type);
            $orgQuestion->setOptions($options);
            $isqQuestions[] = $orgQuestion;
        }
        return $isqQuestions;
    }


    public function testGetIsqCohortsAndSurveysWithRespectToYears()
    {
        $this->specify("Test getIsqCohortsAndSurveysWithRespectToYears", function ($organizationId, $userId, $orgAcademicYearId, $surveyStatus, $excludeQuestionType, $rawData, $expectedResults) {

            // Declaring mock DateUtilityService
            $mockDateUtilityService = $this->getMock('DateUtilityService', ['convertDatabaseStringToISOString']);

            // Declaring mock repositories
            $mockOrgQuestionRepository = $this->getMock('OrgQuestionRepository', ['getIsqCohortsAndSurveysWithRespectToYearsForOrganization']);

            // Mocking repository
            $this->mockRepositoryResolver->method('getRepository')->willReturnMap([
                    [OrgQuestionRepository::REPOSITORY_KEY, $mockOrgQuestionRepository],
                ]
            );

            $this->mockContainer->method('get')->willReturnMap(
                [
                    ['date_utility_service', $mockDateUtilityService],
                ]
            );

            $mockDateUtilityService->method('convertDatabaseStringToISOString')->willReturnMap(
                [
                    ['2015-08-19 06:00:00', '2015-08-19T06:00:00+0000'],
                    ['2015-08-25 06:00:00', '2015-08-25T06:00:00+0000'],
                    ['2015-08-24 06:00:00', '2015-08-24T06:00:00+0000'],
                    ['2015-08-26 06:00:00', '2015-08-26T06:00:00+0000'],
                    ['2015-08-26 06:00:00', '2015-08-26T06:00:00+0000'],
                    ['2015-08-28 06:00:00', '2015-08-28T06:00:00+0000'],
                    ['2016-01-12 06:00:00', '2016-01-12T06:00:00+0000'],
                    ['2016-02-04 06:00:00', '2016-02-04T06:00:00+0000'],
                    ['2016-01-14 06:00:00', '2016-01-14T06:00:00+0000'],
                    ['2016-03-02 06:00:00', '2016-03-02T06:00:00+0000'],
                    ['2015-08-21 06:00:00', '2015-08-21T06:00:00+0000'],
                    ['2015-08-22 06:00:00', '2015-08-22T06:00:00+0000'],
                    ['2015-08-28 06:00:00', '2015-08-28T06:00:00+0000'],
                    ['2015-08-30 06:00:00', '2015-08-30T06:00:00+0000'],
                    ['2015-08-29 06:00:00', '2015-08-29T06:00:00+0000'],
                    ['2015-08-31 06:00:00', '2015-08-31T06:00:00+0000'],
                    ['2015-08-28 06:00:00', '2015-08-28T06:00:00+0000'],
                    ['2015-08-30 06:00:00', '2015-08-30T06:00:00+0000'],
                    ['2015-08-30 06:00:00', '2015-08-30T06:00:00+0000'],
                    ['2015-09-01 06:00:00', '2015-09-01T06:00:00+0000'],
                    ['2015-08-27 06:00:00', '2015-08-27T06:00:00+0000'],
                    ['2015-08-31 06:00:00', '2015-08-31T06:00:00+0000'],
                    ['2015-08-30 06:00:00', '2015-08-30T06:00:00+0000'],
                    ['2015-09-03 06:00:00', '2015-09-03T06:00:00+0000']
                ]
            );

            // Mocking getIsqCohortsAndSurveysWithRespectToYearsForOrganization repository method
            $mockOrgQuestionRepository->method('getIsqCohortsAndSurveysWithRespectToYearsForOrganization')->willReturn($rawData);
            $orgQuestionService = new OrgQuestionService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $result = $orgQuestionService->getIsqCohortsAndSurveysWithRespectToYears($organizationId, $userId, $orgAcademicYearId, $surveyStatus, $excludeQuestionType);
            verify($result)->equals($expectedResults);
        }, [
            'examples' => [ // Example 1
                [ // Test with empty orgAcademicYearId and surveyStatus
                    2, // organization id
                    4799548, // user id
                    null, // org academic year id
                    null, // survey status
                    null, // excludeQuestionType
                    [ // Rawdata
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "11",
                            "survey_name" => "Transition One",
                            "status" => "closed",
                            "open_date" => "2015-08-19 06:00:00",
                            "close_date" => "2015-08-25 06:00:00",
                            "question_id" => "15",
                            "question_type" => "D",
                            "question_text" => "Pick a number"
                        ],
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "11",
                            "survey_name" => "Transition One",
                            "status" => "closed",
                            "open_date" => "2015-08-19 06:00:00",
                            "close_date" => "2015-08-25 06:00:00",
                            "question_id" => "11",
                            "question_type" => "Q",
                            "question_text" => "How satisfied were you with your Orientation leader?"
                        ],
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "11",
                            "survey_name" => "Transition One",
                            "status" => "closed",
                            "open_date" => "2015-08-19 06:00:00",
                            "close_date" => "2015-08-25 06:00:00",
                            "question_id" => "3969",
                            "question_type" => "LA",
                            "question_text" => "Question text for a deleted question: How loud is the quack in your floor? "
                        ]
                    ],
                    [ // Expected result
                        "organization_id" => 2,
                        "years" => [
                            [
                                "id" => 201516,
                                "name" => "2015-2016",
                                "org_academic_year_id" => 88,
                                "surveys" => [
                                    [
                                        "id" => 11,
                                        "name" => "Transition One",
                                        "cohorts" => [
                                            [
                                                "id" => 1,
                                                "name" => "Survey Cohort 1",
                                                "status" => "closed",
                                                "open_date" => null,
                                                "close_date" => null,
                                                "survey_questions" => [
                                                    [
                                                        "type" => "ISQ",
                                                        "question_id" => 15,
                                                        "question_type" => "D",
                                                        "question_text" => "Pick a number"
                                                    ],
                                                    [
                                                        "type" => "ISQ",
                                                        "question_id" => 11,
                                                        "question_type" => "Q",
                                                        "question_text" => "How satisfied were you with your Orientation leader?"
                                                    ],
                                                    [
                                                        "type" => "ISQ",
                                                        "question_id" => 3969,
                                                        "question_type" => "LA",
                                                        "question_text" => "Question text for a deleted question: How loud is the quack in your floor? "
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [ // Example 2 Test with orgAcademicYearId and empty surveyStatus
                    2, // organization id
                    4799548, // user id
                    88, // org academic year id
                    ['launched', 'closed'], // survey status
                    ['LA', 'SA', 'MR'], // excludeQuestionType
                    [ // raw data
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "11",
                            "survey_name" => "Transition One",
                            "status" => "closed",
                            "open_date" => "2015-08-19 06:00:00",
                            "close_date" => "2015-08-25 06:00:00",
                            "question_id" => "15",
                            "question_type" => "D",
                            "question_text" => "Pick a number"
                        ],
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "11",
                            "survey_name" => "Transition One",
                            "status" => "closed",
                            "open_date" => "2015-08-19 06:00:00",
                            "close_date" => "2015-08-25 06:00:00",
                            "question_id" => "11",
                            "question_type" => "Q",
                            "question_text" => "How satisfied were you with your Orientation leader?"
                        ],
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "11",
                            "survey_name" => "Transition One",
                            "status" => "closed",
                            "open_date" => "2015-08-19 06:00:00",
                            "close_date" => "2015-08-25 06:00:00",
                            "question_id" => "4210",
                            "question_type" => "`",
                            "question_text" => "Enter a number:"
                        ]
                    ],
                    [
                        "organization_id" => 2,
                        "years" => [
                            [
                                "id" => 201516,
                                "name" => "2015-2016",
                                "org_academic_year_id" => 88,
                                "surveys" => [
                                    [
                                        "id" => 11,
                                        "name" => "Transition One",
                                        "cohorts" => [
                                            [
                                                "id" => 1,
                                                "name" => "Survey Cohort 1",
                                                "status" => "closed",
                                                "open_date" => null,
                                                "close_date" => null,
                                                "survey_questions" => [
                                                    [
                                                        "type" => "ISQ",
                                                        "question_id" => 15,
                                                        "question_type" => "D",
                                                        "question_text" => "Pick a number"
                                                    ],
                                                    [
                                                        "type" => "ISQ",
                                                        "question_id" => 11,
                                                        "question_type" => "Q",
                                                        "question_text" => "How satisfied were you with your Orientation leader?"
                                                    ],
                                                    [
                                                        "type" => "ISQ",
                                                        "question_id" => 4210,
                                                        "question_type" => "`",
                                                        "question_text" => "Enter a number:"
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [ // Example 3 Test with null data
                    2, // organization id
                    null, // user id
                    null, // org academic year id
                    null, // survey status
                    null,// excludeQuestionType
                    [],
                    [ // Expected formatted output
                        "organization_id" => 2,
                        "years" => []
                    ]
                ]
            ]
        ]);
    }

    public function testGetSurveysAndCohortsWithRespectToYears()
    {
        $this->specify("Test getSurveysAndCohortsWithRespectToYears", function ($organizationId, $userId, $orgAcademicYearId, $surveyStatus, $excludeQuestionType, $rawData, $expectedResults) {
            // Declaring mock DateUtilityService
            $mockDateUtilityService = $this->getMock('DateUtilityService', ['convertDatabaseStringToISOString']);

            // Declaring mock repositories
            $mockOrgQuestionRepository = $this->getMock('OrgQuestionRepository', ['getSurveysAndCohortsWithRespectToYearsForOrganization']);

            // Mocking repository
            $this->mockRepositoryResolver->method('getRepository')->willReturnMap([
                    [OrgQuestionRepository::REPOSITORY_KEY, $mockOrgQuestionRepository],
                ]
            );

            $this->mockContainer->method('get')->willReturnMap([
                    ['date_utility_service', $mockDateUtilityService],
                ]
            );

            $mockDateUtilityService->method('convertDatabaseStringToISOString')->willReturnMap(
                [
                    ['2015-08-19 06:00:00', '2015-08-19T06:00:00+0000'],
                    ['2015-08-25 06:00:00', '2015-08-25T06:00:00+0000'],
                    ['2015-08-24 06:00:00', '2015-08-24T06:00:00+0000'],
                    ['2015-08-26 06:00:00', '2015-08-26T06:00:00+0000'],
                    ['2015-08-26 06:00:00', '2015-08-26T06:00:00+0000'],
                    ['2015-08-28 06:00:00', '2015-08-28T06:00:00+0000'],
                    ['2016-01-12 06:00:00', '2016-01-12T06:00:00+0000'],
                    ['2016-02-04 06:00:00', '2016-02-04T06:00:00+0000'],
                    ['2016-01-14 06:00:00', '2016-01-14T06:00:00+0000'],
                    ['2016-03-02 06:00:00', '2016-03-02T06:00:00+0000'],
                    ['2015-08-21 06:00:00', '2015-08-21T06:00:00+0000'],
                    ['2015-08-22 06:00:00', '2015-08-22T06:00:00+0000'],
                    ['2015-08-28 06:00:00', '2015-08-28T06:00:00+0000'],
                    ['2015-08-30 06:00:00', '2015-08-30T06:00:00+0000'],
                    ['2015-08-29 06:00:00', '2015-08-29T06:00:00+0000'],
                    ['2015-08-31 06:00:00', '2015-08-31T06:00:00+0000'],
                    ['2015-08-28 06:00:00', '2015-08-28T06:00:00+0000'],
                    ['2015-08-30 06:00:00', '2015-08-30T06:00:00+0000'],
                    ['2015-08-30 06:00:00', '2015-08-30T06:00:00+0000'],
                    ['2015-09-01 06:00:00', '2015-09-01T06:00:00+0000'],
                    ['2015-08-27 06:00:00', '2015-08-27T06:00:00+0000'],
                    ['2015-08-31 06:00:00', '2015-08-31T06:00:00+0000'],
                    ['2015-08-30 06:00:00', '2015-08-30T06:00:00+0000'],
                    ['2015-09-03 06:00:00', '2015-09-03T06:00:00+0000']
                ]
            );

            // Mocking getSurveysAndCohortsWithRespectToYearsForOrganization repository method
            $mockOrgQuestionRepository->method('getSurveysAndCohortsWithRespectToYearsForOrganization')->willReturn($rawData);
            $orgQuestionService = new OrgQuestionService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $result = $orgQuestionService->getSurveysAndCohortsWithRespectToYears($organizationId, $userId, $orgAcademicYearId, $surveyStatus, $excludeQuestionType);
            verify($result)->equals($expectedResults);
        }, [
            'examples' => [
                [ // Example 1 : Test with empty orgAcedemicYearId, surveyStatus , rawData
                    2, // organizastion Id
                    4799548, // user Id
                    null, // org academic year id
                    [], // survey status
                    ['LA', 'SA', 'MR'], // excludeQuestionType
                    [ // raw data
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "11",
                            "survey_name" => "Transition One",
                            "status" => "closed",
                            "open_date" => "2015-08-19 06:00:00",
                            "close_date" => "2015-08-25 06:00:00",
                            "question_id" => "68",
                            "question_type" => "Q",
                            "question_text" => "Overall, to what degree are you: Satisfied with your academic life on campus?"
                        ],
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "11",
                            "survey_name" => "Transition One",
                            "status" => "closed",
                            "open_date" => "2015-08-19 06:00:00",
                            "close_date" => "2015-08-25 06:00:00",
                            "question_id" => "69",
                            "question_type" => "Q",
                            "question_text" => "Overall, to what degree: Would you choose this institution again if you had it to do over?"
                        ],
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "4",
                            "cohort_name" => "Survey Cohort 4",
                            "survey_id" => "11",
                            "survey_name" => "Transition One",
                            "status" => "ready",
                            "open_date" => "2015-01-01 00:00:00",
                            "close_date" => "2016-08-08 00:00:00",
                            "question_id" => "104",
                            "question_type" => "Q",
                            "question_text" => "How likely do you think it is that you will do or experience each of the following during this term: Have difficulty balancing your time between academic commitments and fraternity/sorority events?"
                        ]
                    ],
                    [ // Expected Result
                        "organization_id" => 2,
                        "years" => [
                            [
                                "id" => 201516,
                                "name" => "2015-2016",
                                "org_academic_year_id" => 88,
                                "surveys" => [
                                    [
                                        "id" => 11,
                                        "name" => "Transition One",
                                        "cohorts" => [
                                            [
                                                "id" => 1,
                                                "name" => "Survey Cohort 1",
                                                "status" => "closed",
                                                "open_date" => null,
                                                "close_date" => null,
                                                "survey_questions" => [
                                                    [
                                                        "type" => "survey",
                                                        "question_id" => 68,
                                                        "question_type" => "Q",
                                                        "question_text" => "Overall, to what degree are you: Satisfied with your academic life on campus?"
                                                    ],
                                                    [
                                                        "type" => "survey",
                                                        "question_id" => 69,
                                                        "question_type" => "Q",
                                                        "question_text" => "Overall, to what degree: Would you choose this institution again if you had it to do over?"
                                                    ]
                                                ]
                                            ],
                                            [
                                                "id" => 4,
                                                "name" => "Survey Cohort 4",
                                                "status" => "ready",
                                                "open_date" => null,
                                                "close_date" => null,
                                                "survey_questions" => [
                                                    [
                                                        "type" => "survey",
                                                        "question_id" => 104,
                                                        "question_type" => "Q",
                                                        "question_text" => "How likely do you think it is that you will do or experience each of the following during this term: Have difficulty balancing your time between academic commitments and fraternity/sorority events?"
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [ // Example 2 : Test with  not null data
                    2, // organizastion Id
                    4799548, // user Id
                    88, // org academic year id
                    ['launched', 'closed'], // survey status
                    ['LA', 'SA', 'MR'], // excludeQuestionType
                    [ // raw data
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "11",
                            "survey_name" => "Transition One",
                            "status" => "closed",
                            "open_date" => "2015-08-19 06:00:00",
                            "close_date" => "2015-08-25 06:00:00",
                            "question_id" => "70",
                            "question_type" => "Q",
                            "question_text" => "Overall, to what degree: Would you recommend this institution to someone who wants to attend college?"
                        ]
                    ],
                    [ // Expected Result
                        "organization_id" => 2,
                        "years" => [
                            [
                                "id" => 201516,
                                "name" => "2015-2016",
                                "org_academic_year_id" => 88,
                                "surveys" => [
                                    [
                                        "id" => 11,
                                        "name" => "Transition One",
                                        "cohorts" => [
                                            [
                                                "id" => 1,
                                                "name" => "Survey Cohort 1",
                                                "status" => "closed",
                                                "open_date" => null,
                                                "close_date" => null,
                                                "survey_questions" => [
                                                    [
                                                        "type" => "survey",
                                                        "question_id" => 70,
                                                        "question_type" => "Q",
                                                        "question_text" => "Overall, to what degree: Would you recommend this institution to someone who wants to attend college?"
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [ // Example 3 Test with null data
                    2, // organization id
                    null, // user id
                    null, // org academic year id
                    null, // survey status
                    null,// excludeQuestionType
                    [],
                    [ // Expected formatted output
                        "organization_id" => 2,
                        "years" => []
                    ]
                ]
            ]
        ]);
    }

    public function testGetISQsurveyQuestionOptions()
    {
        $this->specify("Test getISQsurveyQuestionOptions ", function ($surveyId, $questionId, $rawData, $expectedResult) {

            // Declaring mock repositories
            $mockSurveyQuestionsRepository = $this->getMock('SurveyQuestionsRepository', ['getOrgQuestionOptions', 'findOneBy']);
            $mockSurveyLanguageRepository = $this->getMock('surveyLangRepository', ['findOneBy']);

            // Declaring mock objects
            $mockSurveyLanguageObject = $this->getMock('surveyLang', ['getName']);
            $mockSurveyQuestionsObject = $this->getMock('SurveyQuestions', ['getOrgQuestion']);

            // Mocking repository
            $this->mockRepositoryResolver->method('getRepository')->willReturnMap([
                    [SurveyQuestionsRepository::REPOSITORY_KEY, $mockSurveyQuestionsRepository],
                    [SurveyLangRepository::REPOSITORY_KEY, $mockSurveyLanguageRepository]
                ]
            );

            // Mocking findOneBy in SurveyLanguageRepository with SurveyLanguageObject
            $mockSurveyLanguageRepository->method('findOneBy')->willReturnMap([
                [['survey' => $surveyId], $mockSurveyLanguageObject],
            ]);

            // Mocking findOneBy in SurveyQuestionsRepository with SurveyQuestionsObject
            $mockSurveyQuestionsRepository->method('findOneBy')->willReturnMap([
                [['orgQuestion' => $questionId], $mockSurveyQuestionsObject],

            ]);

            // Mocking getOrgQuestionOptions repository method
            $mockSurveyQuestionsRepository->method('getOrgQuestionOptions')->willReturn($rawData);

            $orgQuestionService = new OrgQuestionService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $isqSurveyQuestionOptionsResult = $orgQuestionService->getISQsurveyQuestionOptions($surveyId, $questionId);

            $this->assertEquals($isqSurveyQuestionOptionsResult, $expectedResult);

        }, [
            'examples' => [
                [ // Example 1: Test with not empty survey, question id, rawData
                    11, // SurveyID
                    11, // QuestionId
                    [   // rawData
                        [
                            "org_option_id" => 29998,
                            "org_option_text" => "(1) Not At All",
                            "org_option_value" => "1",
                            "sequence" => 1
                        ],
                        [
                            "org_option_id" => 29999,
                            "org_option_text" => "(2) ",
                            "org_option_value" => "2",
                            "sequence" => 2
                        ],
                        [
                            "org_option_id" => 30000,
                            "org_option_text" => "(3) ",
                            "org_option_value" => "3",
                            "sequence" => 3
                        ],
                        [
                            "org_option_id" => 30001,
                            "org_option_text" => "(4) Moderately",
                            "org_option_value" => "4",
                            "sequence" => 4
                        ],
                        [
                            "org_option_id" => 30002,
                            "org_option_text" => "(5) ",
                            "org_option_value" => "5",
                            "sequence" => 5
                        ],
                        [
                            "org_option_id" => 30003,
                            "org_option_text" => "(6) ",
                            "org_option_value" => "6",
                            "sequence" => 6
                        ],
                        [
                            "org_option_id" => 30004,
                            "org_option_text" => "(7) Extremely",
                            "org_option_value" => "7",
                            "sequence" => 7
                        ]
                    ],
                    [ // Expected Output
                        [
                            "org_option_id" => 29998,
                            "org_option_text" => "(1) Not At All",
                            "org_option_value" => "1",
                            "sequence" => 1
                        ],
                        [
                            "org_option_id" => 29999,
                            "org_option_text" => "(2) ",
                            "org_option_value" => "2",
                            "sequence" => 2
                        ],
                        [
                            "org_option_id" => 30000,
                            "org_option_text" => "(3) ",
                            "org_option_value" => "3",
                            "sequence" => 3
                        ],
                        [
                            "org_option_id" => 30001,
                            "org_option_text" => "(4) Moderately",
                            "org_option_value" => "4",
                            "sequence" => 4
                        ],
                        [
                            "org_option_id" => 30002,
                            "org_option_text" => "(5) ",
                            "org_option_value" => "5",
                            "sequence" => 5
                        ],
                        [
                            "org_option_id" => 30003,
                            "org_option_text" => "(6) ",
                            "org_option_value" => "6",
                            "sequence" => 6
                        ],
                        [
                            "org_option_id" => 30004,
                            "org_option_text" => "(7) Extremely",
                            "org_option_value" => "7",
                            "sequence" => 7
                        ]
                    ]
                ],
                [ // Example 2: Test with empty survey, question id
                    null, // SurveyID
                    null, // QuestionId
                    [   // rawData
                        [
                            "org_option_id" => 29998,
                            "org_option_text" => "(1) Not At All",
                            "org_option_value" => "1",
                            "sequence" => 1
                        ],
                        [
                            "org_option_id" => 29999,
                            "org_option_text" => "(2) ",
                            "org_option_value" => "2",
                            "sequence" => 2
                        ],
                        [
                            "org_option_id" => 30000,
                            "org_option_text" => "(3) ",
                            "org_option_value" => "3",
                            "sequence" => 3
                        ]
                    ],
                    [ // Expected Output
                        [
                            "org_option_id" => 29998,
                            "org_option_text" => "(1) Not At All",
                            "org_option_value" => "1",
                            "sequence" => 1
                        ],
                        [
                            "org_option_id" => 29999,
                            "org_option_text" => "(2) ",
                            "org_option_value" => "2",
                            "sequence" => 2
                        ],
                        [
                            "org_option_id" => 30000,
                            "org_option_text" => "(3) ",
                            "org_option_value" => "3",
                            "sequence" => 3
                        ]
                    ]
                ],
                [ // Example 3: Test with empty survey only
                    null, // SurveyID
                    11, // QuestionId
                    [   // rawData
                        [
                            "org_option_id" => 23398,
                            "org_option_text" => "(5) Not At All",
                            "org_option_value" => "1",
                            "sequence" => 1
                        ]
                    ],
                    [ // Expected Output
                        [
                            "org_option_id" => 23398,
                            "org_option_text" => "(5) Not At All",
                            "org_option_value" => "1",
                            "sequence" => 1
                        ]
                    ]
                ]
            ]
        ]);
    }

}