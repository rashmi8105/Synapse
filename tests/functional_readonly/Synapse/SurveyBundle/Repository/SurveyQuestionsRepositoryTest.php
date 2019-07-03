<?php
use Codeception\TestCase\Test;

class SurveyQuestionsRepositoryTest extends Test{
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
     * @var \Synapse\SurveyBundle\Repository\SurveyQuestionsRepository
     */
    private $surveyQuestionsRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->surveyQuestionsRepository = $this->repositoryResolver->getRepository('SynapseSurveyBundle:SurveyQuestions');
    }

    public function testGetSurveyCohortISQ(){
        $this->specify("Verify the functionality of the method getSurveyCohortISQ", function($orgId, $surveyId, $cohortId, $expectedResult) {
            $surveyQuestions = $this->surveyQuestionsRepository->getSurveyCohortISQ($orgId, $surveyId, $cohortId);
            verify($surveyQuestions)->equals($expectedResult);
        },[
            'examples' =>[
                [
                    2,
                    11,
                    1,
                    [
                        [
                            'survey_id' => 11,
                            'cohort_id' => 1,
                            'question_id' => 11,
                            'type_id' => 'Q',
                            'category_id' => 1,
                            'question_key' => null,
                            'question_text' => 'How satisfied were you with your Orientation leader?'
                        ],
                        [
                            'survey_id' => 11,
                            'cohort_id' => 1,
                            'question_id' => 12,
                            'type_id' => 'D',
                            'category_id' => 1,
                            'question_key' => null,
                            'question_text' => 'When did you attend Orientation?'
                        ],
                        [
                            'survey_id' => 11,
                            'cohort_id' => 1,
                            'question_id' => 13,
                            'type_id' => 'NA',
                            'category_id' => 1,
                            'question_key' => null,
                            'question_text' => 'How many welcome week events did you attend?'
                        ]
                    ]
                ],
                [
                    190,
                    11,
                    1,
                    [
                        [
                            'survey_id' => 11,
                            'cohort_id' => 1,
                            'question_id' => 3971,
                            'type_id' => 'Q',
                            'category_id' => 1,
                            'question_key' => null,
                            'question_text' => 'Organization: 190 Question ID: 03971'
                        ],
                        [
                            'survey_id' => 11,
                            'cohort_id' => 1,
                            'question_id' => 3972,
                            'type_id' => 'D',
                            'category_id' => 1,
                            'question_key' => null,
                            'question_text' => 'Organization: 190 Question ID: 03972'
                        ],
                        [
                            'survey_id' => 11,
                            'cohort_id' => 1,
                            'question_id' => 3973,
                            'type_id' => 'D',
                            'category_id' => 1,
                            'question_key' => null,
                            'question_text' => 'Organization: 190 Question ID: 03973'
                        ],
                        [
                            'survey_id' => 11,
                            'cohort_id' => 1,
                            'question_id' => 3974,
                            'type_id' => 'D',
                            'category_id' => 1,
                            'question_key' => null,
                            'question_text' => 'Organization: 190 Question ID: 03974'
                        ]
                    ]
                ]
            ]
        ]);
    }

}