<?php
namespace Synapse\SurveyBundle\Service\Impl;

use Codeception\Specify;
use Codeception\Test\Unit;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Repository\SurveyLangRepository;
use Synapse\SurveyBundle\Repository\SurveyQuestionsRepository;

class SurveyQuestionServiceTest extends Unit
{
    use Specify;

    /**
     * @var mockRepositoryResolver
     */
    private $mockRepositoryResolver;

    /**
     * @var mockLogger
     */
    private $mockLogger;

    /**
     * @var mockContainer
     */
    private $mockContainer;


    protected function _before()
    {
        $this->mockContainer = $this->getMock('Container', ['get']);
        $this->mockLogger = $this->getMock('Logger', ['debug', 'error']);
        $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
    }

    public function testGetSurveyCohortISQ()
    {
        $this->specify("Get Survey ISQ with cohort", function ($orgId, $surveyId, $cohortId) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockSurveyLangRepository = $this->getMock('SurveyLang', array(
                'findOneBySurvey'
            ));

            $mockSurveyQuestionsRepository = $this->getMock('SurveyQuestions', array(
                'getSurveyCohortISQ'
            ));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                        [
                            'SynapseCoreBundle:SurveyLang',
                            $mockSurveyLangRepository
                        ],
                        [
                            'SynapseSurveyBundle:SurveyQuestions',
                            $mockSurveyQuestionsRepository
                        ]
                    ]
                );

            $mockSurveyLang = $this->getMock('SurveyLang', array(
                'getName'
            ));

            $mockSurveyLangRepository->expects($this->any())
                ->method('findOneBySurvey')
                ->will($this->returnValue($mockSurveyLang));

            $mockSurveyQuestions = $this->getMock('SurveyQuestions', array(
                'getSurveyCohortISQ'
            ));

            $mockSurveyQuestionsRepository->expects($this->any())
                ->method('getSurveyCohortISQ')
                ->will($this->returnValue($mockSurveyQuestions));

            $surveyQuestionsService = new SurveyQuestionsService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $surveyQuestionsService->getSurveyCohortISQ($orgId, $surveyId, $cohortId);
            $this->assertEquals($orgId, $result->getOrganizationId());
            $this->assertEquals($surveyId, $result->getSurveyId());
            $this->assertEquals($cohortId, $result->getCohortId());
            $this->assertObjectHasAttribute('langId', $result);
            $this->assertObjectHasAttribute('surveyName', $result);
            $this->assertObjectHasAttribute('surveyQuestions', $result);
        }, [
            'examples' => [
                [
                    2,
                    11,
                    'NULL'
                ],
                [
                    5,
                    11,
                    'NULL'
                ]
            ]
        ]);
    }

    /**
     * @expectedException \Synapse\CoreBundle\Exception\SynapseValidationException
     */
    public function testGetSurveyQuestionOptions()
    {
        $this->specify("Test getSurveyQuestionOptions Method", function ($surveyId, $questionId, $rawData, $expectedResult) {
            // Declaring mock repositories
            $mockSurveyQuestionsRepository = $this->getMock('surveyQuestions', ['getOptionsForSurveyQuestions', 'findOneBy', 'getEbiQuestion']);
            $mockSurveyLanguageRepository = $this->getMock('surveyLang', ['findOneBy', 'getSurvey']);

            $this->mockRepositoryResolver->method('getRepository')->willReturnMap([
                    [SurveyQuestionsRepository::REPOSITORY_KEY, $mockSurveyQuestionsRepository],
                    [SurveyLangRepository::REPOSITORY_KEY, $mockSurveyLanguageRepository]
                ]
            );

            $mockSurveyLanguage = $this->getMock('SurveyLang', array(
                'getSurvey'
            ));

            if (!$surveyId) {
                $mockSurveyLanguageRepository->expects($this->any())->method('findOneBy')->will($this->returnValue(null));
            } else {
                $mockSurveyLanguageRepository->expects($this->any())->method('findOneBy')->will($this->returnValue($mockSurveyLanguage));
            }

            $mockSurveyQuestions = $this->getMock('SurveyQuestions', array(
                'getEbiQuestion'
            ));

            if (!$questionId) {
                $mockSurveyQuestionsRepository->expects($this->any())->method('findOneBy')->will($this->returnValue(null));
            } else {
                $mockSurveyQuestionsRepository->expects($this->any())->method('findOneBy')->will($this->returnValue($mockSurveyQuestions));
            }


            // Mocking getOptionsForSurveyQuestions repository method
            $mockSurveyQuestionsRepository->method('getOptionsForSurveyQuestions')->willReturn($rawData);

            $surveyQuestionsService = new SurveyQuestionsService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $result = $surveyQuestionsService->getSurveyQuestionOptions($surveyId, $questionId);
            $this->assertEquals($result, $expectedResult);
        }, [
            'examples' => [
                [ // Example 1: Test with Survey id and question id are valid values
                    11, // survey id
                    32, // question id
                    [ // raw question option data
                        [
                            "ebi_option_id" => 10878,
                            "ebi_option_text" => "Other reasons (Please specify.)",
                            "ebi_option_value" => "5"
                        ],
                        [
                            "ebi_option_id" => 10879,
                            "ebi_option_text" => "Student-athlete reasons",
                            "ebi_option_value" => "4"
                        ],
                        [
                            "ebi_option_id" => 10880,
                            "ebi_option_text" => "Social reasons",
                            "ebi_option_value" => "3"
                        ]
                    ],
                    [ // Expected Result
                        [
                            "ebi_option_id" => 10878,
                            "ebi_option_text" => "Other reasons (Please specify.)",
                            "ebi_option_value" => "5"
                        ],
                        [
                            "ebi_option_id" => 10879,
                            "ebi_option_text" => "Student-athlete reasons",
                            "ebi_option_value" => "4"
                        ],
                        [
                            "ebi_option_id" => 10880,
                            "ebi_option_text" => "Social reasons",
                            "ebi_option_value" => "3"
                        ]
                    ]
                ],
                [ // Example 2: Test with Survey ID is valid, and question ID is not
                    11, // survey id
                    null, // question id
                    [ // raw question option data
                        [
                            "ebi_option_id" => 1218,
                            "ebi_option_text" => "Other reasons (Please specify.)",
                            "ebi_option_value" => "5"
                        ],
                        [
                            "ebi_option_id" => 10879,
                            "ebi_option_text" => "Student-athlete reasons happen",
                            "ebi_option_value" => "4"
                        ],
                        [
                            "ebi_option_id" => 10890,
                            "ebi_option_text" => "Social reasons",
                            "ebi_option_value" => "3"
                        ]
                    ],
                    [ // expected
                        [
                            "ebi_option_id" => 1218,
                            "ebi_option_text" => "Other reasons (Please specify.)",
                            "ebi_option_value" => "5"
                        ],
                        [
                            "ebi_option_id" => 10879,
                            "ebi_option_text" => "Student-athlete reasons happen",
                            "ebi_option_value" => "4"
                        ],
                        [
                            "ebi_option_id" => 10890,
                            "ebi_option_text" => "Social reasons",
                            "ebi_option_value" => "3"
                        ]
                    ]
                ],
                [ // Example 3: Test with Survey ID is invalid, but question ID is valid
                    null, // survey id
                    32, // question id
                    [ // raw question option data
                        [
                            "ebi_option_id" => 1218,
                            "ebi_option_text" => "Other reasons (Please specify.)",
                            "ebi_option_value" => "5"
                        ],
                        [
                            "ebi_option_id" => 10879,
                            "ebi_option_text" => "Student-athlete reasons happen",
                            "ebi_option_value" => "4"
                        ],
                        [
                            "ebi_option_id" => 10890,
                            "ebi_option_text" => "Social reasons",
                            "ebi_option_value" => "3"
                        ]
                    ],
                    [ // expected
                        [
                            "ebi_option_id" => 1218,
                            "ebi_option_text" => "Other reasons (Please specify.)",
                            "ebi_option_value" => "5"
                        ],
                        [
                            "ebi_option_id" => 10879,
                            "ebi_option_text" => "Student-athlete reasons happen",
                            "ebi_option_value" => "4"
                        ],
                        [
                            "ebi_option_id" => 10890,
                            "ebi_option_text" => "Social reasons",
                            "ebi_option_value" => "3"
                        ]
                    ]
                ],
                [ // Example 4: Test with Survey ID and question ID are both invalid
                    null, // survey id
                    null, // question id
                    null,
                    null
                ]
            ]
        ]);
    }
}