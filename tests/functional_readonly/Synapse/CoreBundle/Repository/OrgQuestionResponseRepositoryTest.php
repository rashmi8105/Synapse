<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\OrgQuestionResponseRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;


class OrgQuestionResponseRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var OrgQuestionResponseRepository
     */
    private $orgQuestionResponseRepository;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    public function _before()
    {
        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->orgQuestionResponseRepository = $this->repositoryResolver->getRepository(OrgQuestionResponseRepository::REPOSITORY_KEY);
    }

    public function testGetStudentISQResponses()
    {
        $this->specify('Verify the functionality of the method getStudentISQResponses', function ($surveyId, $studentId, $organizationId, $orgQuestionIds, $expectedResult) {


                $functionResult = $this->orgQuestionResponseRepository->getStudentISQResponses($surveyId, $studentId, $organizationId, $orgQuestionIds);
                verify($expectedResult)->equals($functionResult);

        }, ['examples' => [
            // test case for student id 494605
            [11, 494605, 87, [616, 617, 618, 619, 620], [

                ["survey_que_id" => 1874, "question_text" => "Organization: 087 Question ID: 00616", "response_type" => "decimal", "option_name" => "Not Applicable", "decimal_value" => 99.00, "char_value" => NULL, "charmax_value" => NULL],
                ["survey_que_id" => 1875, "question_text" => "Organization: 087 Question ID: 00617", "response_type" => "decimal", "option_name" => "Not Applicable", "decimal_value" => 99.00, "char_value" => NULL, "charmax_value" => NULL],
                ["survey_que_id" => 1876, "question_text" => "Organization: 087 Question ID: 00618", "response_type" => "decimal", "option_name" => "Infrequently", "decimal_value" => 2.00, "char_value" => NULL, "charmax_value" => NULL],
                ["survey_que_id" => 1877, "question_text" => "Organization: 087 Question ID: 00619", "response_type" => "decimal", "option_name" => "(4) Moderately", "decimal_value" => 4.00, "char_value" => NULL, "charmax_value" => NULL],
                ["survey_que_id" => 1878, "question_text" => "Organization: 087 Question ID: 00620", "response_type" => "decimal", "option_name" => "Not Applicable", "decimal_value" => 99.00, "char_value" => NULL, "charmax_value" => NULL]
            ]],

            // test case for student id 489322
            [11, 489322, 87, [616], [

                ["survey_que_id" => 1874, "question_text" => "Organization: 087 Question ID: 00616", "response_type" => "decimal", "option_name" => "(5) ", "decimal_value" => 5.00, "char_value" => NULL, "charmax_value" => NULL]
            ]],

            // test case with wrong data type
            ['test', 'test', 'test', ['test'], []],

            // test case with null/empty value
            ['', null, '', [], []]
        ]]);
    }

    public function testGetScaledCategoryISQCalculatedResponses()
    {
        $this->specify('Verify the functionality of the method getScaledCategoryISQCalculatedResponses', function ($surveyId, $reportStudentId, $organizationId, $orgQuestionIds, $expectedResult) {
            $functionResult = $this->orgQuestionResponseRepository->getScaledCategoryISQCalculatedResponses($surveyId, $reportStudentId, $organizationId, $orgQuestionIds);
            verify($expectedResult)->equals($functionResult);
        }, ['examples' => [
            // test case for student id 4709664 and organization 87
            [11, [4709664], 87, [557,558,559,576,574,539,540,541,542,543,544,545,546,547,561,562,563,565], [
                [
                    'org_question_id' => 539,
                    'survey_question_id' => 1826,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00539',
                    'question_type' => 'Q',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 6.00
                ],
                [
                    'org_question_id' => 540,
                    'survey_question_id' => 1827,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00540',
                    'question_type' => 'Q',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 5.00
                ],
                [
                    'org_question_id' => 541,
                    'survey_question_id' => 1828,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00541',
                    'question_type' => 'D',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 5.00
                ],
                [
                    'org_question_id' => 542,
                    'survey_question_id' => 1829,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00542',
                    'question_type' => 'Q',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 4.00
                ],
                [
                    'org_question_id' => 543,
                    'survey_question_id' => 1830,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00543',
                    'question_type' => 'Q',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 5.00
                ],
                [
                    'org_question_id' => 544,
                    'survey_question_id' => 1831,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00544',
                    'question_type' => 'Q',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 3.00
                ],
                [
                    'org_question_id' => 545,
                    'survey_question_id' => 1832,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00545',
                    'question_type' => 'Q',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 4.00
                ],
                [
                    'org_question_id' => 546,
                    'survey_question_id' => 1833,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00546',
                    'question_type' => 'Q',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 4.00
                ],
                [
                    'org_question_id' => 547,
                    'survey_question_id' => 1834,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00547',
                    'question_type' => 'Q',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 7.00
                ],
                [
                    'org_question_id' => 557,
                    'survey_question_id' => 1835,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00557',
                    'question_type' => 'D',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 4.00
                ],
                [
                    'org_question_id' => 558,
                    'survey_question_id' => 1836,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00558',
                    'question_type' => 'D',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 2.00
                ],
                [
                    'org_question_id' => 559,
                    'survey_question_id' => 1837,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00559',
                    'question_type' => 'D',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 2.00
                ],
                [
                    'org_question_id' => 561,
                    'survey_question_id' => 1839,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00561',
                    'question_type' => 'Q',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 5.00
                ],
                [
                    'org_question_id' => 562,
                    'survey_question_id' => 1840,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00562',
                    'question_type' => 'Q',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 5.00
                ],
                [
                    'org_question_id' => 563,
                    'survey_question_id' => 1841,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00563',
                    'question_type' => 'D',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 2.00
                ],
                [
                    'org_question_id' => 565,
                    'survey_question_id' => 1842,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00565',
                    'question_type' => 'Q',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 5.00
                ],
                [
                    'org_question_id' => 574,
                    'survey_question_id' => 1843,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00574',
                    'question_type' => 'Q',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 5.00
                ],
                [
                    'org_question_id' => 576,
                    'survey_question_id' => 1838,
                    'question_number' => NULL,
                    'question_text' => 'Organization: 087 Question ID: 00576',
                    'question_type' => 'Q',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 1.00
                ]
            ]],

            // test case for student id 4628420 and organization 2
            [11, [4628420], 2, [11, 15], [
                [
                    'org_question_id' => 11,
                    'survey_question_id' => 1091,
                    'question_number' => NULL,
                    'question_text' => 'How satisfied were you with your Orientation leader?',
                    'question_type' => 'Q',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 3.00
                ],
                [
                    'org_question_id' => 15,
                    'survey_question_id' => 3560,
                    'question_number' => NULL,
                    'question_text' => 'Pick a number',
                    'question_type' => 'D',
                    'student_count' => 1,
                    'standard_deviation' => 0.00,
                    'mean' => 2.00
                ]
            ]],

            // test case with wrong data type
            ['test', ['test'], 'test', ['test', 'test'], []],

            // test case with null/empty value
            [null, [], null, [], []]
        ]]);
    }

    public function testGetScaledTypeISQResponses()
    {
        $this->specify('Verify the functionality of the method getScaledTypeISQResponses', function ($surveyId, $organizationId, $personIds, $orgQuestionId, $expectedResult) {

            $functionResult = $this->orgQuestionResponseRepository->getScaledTypeISQResponses($surveyId, $organizationId, $personIds, $orgQuestionId);
            verify($expectedResult)->equals($functionResult);
        },
            [
                'examples' =>
                    [
                        // test case for student id 4709664 and organization 87
                        [
                            11, 87, [4709664], 539,
                            [
                                [
                                    "survey_questions_id" => 1826,
                                    "student_count" => 1,
                                    "question_number" => NULL,
                                    "survey_id" => 11,
                                    "org_id" => 87,
                                    "response_type" => 'decimal',
                                    "decimal_value" => 6.00,
                                    "char_value" => NULL,
                                    "charmax_value" => NULL,
                                    "option_text" => '(6) ',
                                    "option_value" => 6
                                ]
                            ]
                        ],
                        // test case for student id 4628421, 4628424 and organization 2
                        [
                            11, 2, [4628421, 4628424], 15,
                            [
                                [
                                    "survey_questions_id" => 3560,
                                    "student_count" => 1,
                                    "question_number" => NULL,
                                    "survey_id" => 11,
                                    "org_id" => 2,
                                    "response_type" => 'decimal',
                                    "decimal_value" => 3.00,
                                    "char_value" => NULL,
                                    "charmax_value" => NULL,
                                    "option_text" => 'Three',
                                    "option_value" => 3
                                ],
                                [
                                    "survey_questions_id" => 3560,
                                    "student_count" => 1,
                                    "question_number" => NULL,
                                    "survey_id" => 11,
                                    "org_id" => 2,
                                    "response_type" => 'decimal',
                                    "decimal_value" => 4.00,
                                    "char_value" => NULL,
                                    "charmax_value" => NULL,
                                    "option_text" => 'Four',
                                    "option_value" => 4
                                ]
                            ]
                        ],
                        // test case with wrong data type
                        [
                            'test', 'test', ['test'], 'test', []
                        ],
                        // test case with null/empty value
                        [
                            null, null, [], null, []
                        ]
                    ]]);
    }

    public function testGetDescriptiveISQResponses()
    {
        $this->specify('Verify the functionality of the method getDescriptiveISQResponses', function ($surveyId, $organizationId, $personIds, $type, $selectedField, $permittedQuestionIds, $expectedResult) {

            $functionResult = $this->orgQuestionResponseRepository->getDescriptiveISQResponses($surveyId, $organizationId, $personIds, $type, $selectedField, $permittedQuestionIds);

            verify($expectedResult)->equals($functionResult);
        },
            [
                'examples' =>
                    [
                        // test case for student id 4709664 and organization 87
                        [
                            11, 87, [4709664], "D", "char_value",
                            [
                                557, 558, 559, 576
                            ],
                            [
                                [
                                    "survey_questions_id" => 1835,
                                    "question_number" => NULL,
                                    "question_text" => "Organization: 087 Question ID: 00557",
                                    "student_count" => 1,
                                    "question_type" => "D",
                                    "survey_id" => 11,
                                    "org_question_id" => 557,
                                    "org_id" => 87,
                                    "response_type" => "decimal",
                                    "char_value" => NULL
                                ],
                                [
                                    "survey_questions_id" => 1836,
                                    "question_number" => NULL,
                                    "question_text" => "Organization: 087 Question ID: 00558",
                                    "student_count" => 1,
                                    "question_type" => "D",
                                    "survey_id" => 11,
                                    "org_question_id" => 558,
                                    "org_id" => 87,
                                    "response_type" => "decimal",
                                    "char_value" => NULL
                                ],
                                [
                                    "survey_questions_id" => 1837,
                                    "question_number" => NULL,
                                    "question_text" => "Organization: 087 Question ID: 00559",
                                    "student_count" => 1,
                                    "question_type" => "D",
                                    "survey_id" => 11,
                                    "org_question_id" => 559,
                                    "org_id" => 87,
                                    "response_type" => "decimal",
                                    "char_value" => NULL
                                ]
                            ]
                        ],
                        // test case for student id 1053819 and organization 17
                        [
                            12, 17, [1053819], "Q", "char_value", [3536],
                            [
                                [
                                    "survey_questions_id" => 1235,
                                    "question_number" => NULL,
                                    "question_text" => "Organization: 017 Question ID: 03536",
                                    "student_count" => 1,
                                    "question_type" => "Q",
                                    "survey_id" => 12,
                                    "org_question_id" => 3536,
                                    "org_id" => 17,
                                    "response_type" => "decimal",
                                    "char_value" => NULL
                                ]
                            ]
                        ],
                        // test case with wrong data type
                        [
                            'test', 'test', ['test'], 123, "char_value", ['test'], []
                        ],
                        // test case with null/empty value
                        [
                            null, null, [], null, "char_value", [], []
                        ]
                    ]
            ]
        );
    }

    public function testGetNumericISQResponseCounts()
    {
        $this->specify('Verify the functionality of the method getNumericISQResponseCounts', function ($surveyId, $reportStudentIds, $organizationId, $permittedQuestions, $expectedResult) {

            $functionResult = $this->orgQuestionResponseRepository->getNumericISQResponseCounts($surveyId, $reportStudentIds, $organizationId, $permittedQuestions);

            verify($expectedResult)->equals($functionResult);
        },
            [
                'examples' =>
                    [
                        // test case for student id 4702050 and organization 44
                        [
                            11, [4702050], 44, [677, 678, 679],
                            [
                                [
                                    "survey_question_id" => 1421,
                                    "question_number" => NULL,
                                    "question_text" => "Organization: 044 Question ID: 00677",
                                    "question_type" => "NA",
                                    "org_question_id" => 677,
                                    "student_count" => 1
                                ],
                                [
                                    "survey_question_id" => 1422,
                                    "question_number" => NULL,
                                    "question_text" => "Organization: 044 Question ID: 00678",
                                    "question_type" => "NA",
                                    "org_question_id" => 678,
                                    "student_count" => 1
                                ],
                                [
                                    "survey_question_id" => 1423,
                                    "question_number" => NULL,
                                    "question_text" => "Organization: 044 Question ID: 00679",
                                    "question_type" => "NA",
                                    "org_question_id" => 679,
                                    "student_count" => 1
                                ]
                            ]
                        ],
                        // test case for student ids 254975, 254993, 677579 and organization 120
                        [
                            11, [254975, 254993, 677579], 120, [1081, 1089, 1099],
                            [
                                [
                                    "survey_question_id" => 2580,
                                    "question_number" => NULL,
                                    "question_text" => "Organization: 120 Question ID: 01081",
                                    "question_type" => "NA",
                                    "org_question_id" => 1081,
                                    "student_count" => 1
                                ],
                                [
                                    "survey_question_id" => 2588,
                                    "question_number" => NULL,
                                    "question_text" => "Organization: 120 Question ID: 01089",
                                    "question_type" => "NA",
                                    "org_question_id" => 1089,
                                    "student_count" => 1
                                ],
                                [
                                    "survey_question_id" => 2566,
                                    "question_number" => NULL,
                                    "question_text" => "Organization: 120 Question ID: 01099",
                                    "question_type" => "NA",
                                    "org_question_id" => 1099,
                                    "student_count" => 1
                                ]
                            ]
                        ],
                        //  test case with wrong data type
                        [
                            'test', ['test', 'test', 'test'], 120, [1081, 1089, 1099], []
                        ],
                        //  test case with null/empty value
                        [
                            null, [], null, [], []
                        ]
                    ]
            ]
        );
    }

    public function testGetNumericISQCalculatedResponses()
    {
        $this->specify('Verify the functionality of the method getNumericISQCalculatedResponses', function ($surveyId, $organizationId, $personIds, $orgQuestionsId, $expectedResult) {

            $functionResult = $this->orgQuestionResponseRepository->getNumericISQCalculatedResponses($surveyId, $organizationId, $personIds, $orgQuestionsId);

            verify($expectedResult)->equals($functionResult);
        },
            [
                'examples' =>
                    [
                        // test case for student ids 4702050 , 4702073, 4702105, 4702106 and organization 44
                        [
                            11, 44, [4702050, 4702073, 4702105, 4702106], 676,
                            [
                                [
                                    "survey_questions_id" => 1420,
                                    "responded_count" => 4,
                                    "minimum_value" => "1.00",
                                    "maximum_value" => "1.00",
                                    "standard_deviation" => 0,
                                    "mean" => "1.00",
                                    "option_value" => "1"
                                ]
                            ]
                        ],
                        // test case for student id 4671244 and organization 2
                        [
                            14, 2, [4671244], 941,
                            [
                                [
                                    "survey_questions_id" => 3562,
                                    "responded_count" => 1,
                                    "minimum_value" => "1.00",
                                    "maximum_value" => "1.00",
                                    "standard_deviation" => 0,
                                    "mean" => "1.00",
                                    "option_value" => "1"
                                ]
                            ]
                        ],
                        //  test case with wrong data type
                        [
                            'test', 'test', ['test'], 'test',
                            [
                                [
                                    "survey_questions_id" => null,
                                    "responded_count" => 0,
                                    "minimum_value" => null,
                                    "maximum_value" => null,
                                    "standard_deviation" => null,
                                    "mean" => null,
                                    "option_value" => null
                                ]
                            ]
                        ],
                        // test case with null/empty value
                        [
                            null, null, [], null,
                            [
                                [
                                    "survey_questions_id" => null,
                                    "responded_count" => 0,
                                    "minimum_value" => null,
                                    "maximum_value" => null,
                                    "standard_deviation" => null,
                                    "mean" => null,
                                    "option_value" => null
                                ]
                            ]
                        ],
                    ]
            ]
        );
    }

    public function testGetNumericISQResponses()
    {
        $this->specify('Verify the functionality of the method getNumericISQResponses', function ($surveyId, $organizationId, $personIds, $orgQuestionsId, $expectedResult) {

            $functionResult = $this->orgQuestionResponseRepository->getNumericISQResponses($surveyId, $organizationId, $personIds, $orgQuestionsId);
            verify($expectedResult)->equals($functionResult);
        },
            [
                'examples' =>
                    [
                        // test case for student ids 4702050, 4702106, 4702848, 4702861 and organization 44
                        [
                            11, 44, [4702050, 4702106, 4702848, 4702861], 676,
                            [
                                [
                                    "org_question_id" => 676,
                                    "decimal_value" => 1.00
                                ],
                                [
                                    "org_question_id" => 676,
                                    "decimal_value" => 1.00
                                ],
                                [
                                    "org_question_id" => 676,
                                    "decimal_value" => 2.00
                                ],
                                [
                                    "org_question_id" => 676,
                                    "decimal_value" => 2.00
                                ]
                            ]
                        ],
                        // test case for student id 4704138 and organization 14
                        [
                            14, 14, [4704138], 4511,
                            [
                                [
                                    "org_question_id" => 4511,
                                    "decimal_value" => 1.00
                                ],
                                [
                                    "org_question_id" => 4511,
                                    "decimal_value" => 1.00
                                ],
                                [
                                    "org_question_id" => 4511,
                                    "decimal_value" => 1.00
                                ],
                                [
                                    "org_question_id" => 4511,
                                    "decimal_value" => 1.00
                                ],
                                [
                                    "org_question_id" => 4511,
                                    "decimal_value" => 1.00
                                ],
                                [
                                    "org_question_id" => 4511,
                                    "decimal_value" => 1.00
                                ]
                            ]
                        ],
                        // test case with wrong data type
                        [
                            'test', 'test', ['test'], 'test', []
                        ],
                        // test case with null/empty value
                        [
                            null, null, [], null, []
                        ]
                    ]
            ]
        );
    }

    public function testGetMultiResponseISQCalculatedResponses()
    {
        $this->specify('Verify the functionality of the method getMultiResponseISQCalculatedResponses', function ($surveyId, $reportStudentIds, $organizationId, $orgQuestionIds, $expectedResult) {

            $functionResult = $this->orgQuestionResponseRepository->getMultiResponseISQCalculatedResponses($surveyId, $reportStudentIds, $organizationId, $orgQuestionIds);
            
            verify($expectedResult)->equals($functionResult);

        }, ['examples' => [
            // test case for student ids 766884, 397280 and organization 46
            [11, [766884, 397280], 46, [254, 265, 267], [

                ["org_question_id" => 254, "survey_question_id" => 1498, "question_number" => NULL, "question_text" => "Organization: 046 Question ID: 00254", "question_type" => "MR", "student_count" => 1, "standard_deviation" => "0.00", "mean" => "1.00"],
                ["org_question_id" => 265, "survey_question_id" => 1508, "question_number" => NULL, "question_text" => "Organization: 046 Question ID: 00265", "question_type" => "MR", "student_count" => 1, "standard_deviation" => "0.00", "mean" => "1.00"],
                ["org_question_id" => 267, "survey_question_id" => 1510, "question_number" => NULL, "question_text" => "Organization: 046 Question ID: 00267", "question_type" => "MR", "student_count" => 1, "standard_deviation" => "0.00", "mean" => "1.00"]
            ]],
            // test case for student ids 4696863, 397280 and organization 14
            [11, [4696863], 14, [1005, 1554, 1556, 1007], [

                ["org_question_id" => 1005, "survey_question_id" => 1128, "question_number" => NULL, "question_text" => "Organization: 014 Question ID: 01005", "question_type" => "MR", "student_count" => 1, "standard_deviation" => "0.00", "mean" => "1.00"],
                ["org_question_id" => 1007, "survey_question_id" => 1130, "question_number" => NULL, "question_text" => "Organization: 014 Question ID: 01007", "question_type" => "MR", "student_count" => 1, "standard_deviation" => "0.00", "mean" => "1.00"],
                ["org_question_id" => 1554, "survey_question_id" => 1125, "question_number" => NULL, "question_text" => "Organization: 014 Question ID: 01554", "question_type" => "MR", "student_count" => 1, "standard_deviation" => "0.00", "mean" => "1.00"],
                ["org_question_id" => 1556, "survey_question_id" => 1127, "question_number" => NULL, "question_text" => "Organization: 014 Question ID: 01556", "question_type" => "MR", "student_count" => 1, "standard_deviation" => "0.00", "mean" => "1.00"]
            ]],
            // test case with wrong data type
            ['test', ['test'], 'test', ['test'], []],
            // test case with null/empty value
            [null, [], null, [], []]

        ]]);
    }

    public function testGetMultiResponseISQResponses()
    {
        $this->specify('Verify the functionality of the method getMultiResponseISQResponses', function ($surveyId, $organizationId, $personIds, $orgQuestionId, $expectedResult) {

            $functionResult = $this->orgQuestionResponseRepository->getMultiResponseISQResponses($surveyId, $organizationId, $personIds, $orgQuestionId);

            verify($expectedResult)->equals($functionResult);

        }, ['examples' => [
            // test case for student id 4709664 and organization 87
            [11, 87, [4709664], 576, [

                ["survey_questions_id" => 1838, "student_count" => 1, "question_number" => NULL, "survey_id" => 11, "org_id" => 87, "response_type" => "decimal", "decimal_value" => "1.00", "char_value" => NULL, "charmax_value" => NULL, "option_text" => "(1) Not At All", "option_value" => 1, "option_id" => 19754]
            ]],
            // test case for student id 4708303 and organization 190
            [12, 190, [4708303], 4963, [

                ["survey_questions_id" => 4582, "student_count" => 1, "question_number" => NULL, "survey_id" => 12, "org_id" => 190, "response_type" => "decimal", "decimal_value" => "1.00", "char_value" => NULL, "charmax_value" => NULL, "option_text" => "With parents/family", "option_value" => 1, "option_id" => 40461]
            ]],
            // test case with wrong data type
            ['test', 'test', ['test'], 'test', []],
            // test case with null/empty value
            [null, null, [], null, []]

        ]]);
    }

    public function testGetMultiResponseISQResponseCount()
    {
        $this->specify('Verify the functionality of the method getMultiResponseISQResponseCount', function ($surveyId, $organizationId, $personIds, $orgQuestionId, $expectedResult) {

            $functionResult = $this->orgQuestionResponseRepository->getMultiResponseISQResponseCount($surveyId, $organizationId, $personIds, $orgQuestionId);

            verify($expectedResult)->equals($functionResult);

        }, ['examples' => [
            // test case for student id 4709664 and organization 87
            [11, 87, [4709664], 576, [["student_count" => 1]]],
            // test case for student id 269355 and organization 134
            [12, 134, [269355], 3791, [["student_count" => 1]]],
            // test case with wrong data type
            ['test', 'test', ['test'], 'test', []],
            // test case with null/empty value
            [null, null, [], null, []]

        ]]);
    }

    public function testGetGPAdataForISQ()
    {
        
        $this->specify("Verify the functionality of the method getGPAdataForISQ", function ($organizationId, $loggedInUserId, $yearId, $questionId, $caseDetails, $studentIdsToInclude, $expectedResults, $expectedCount) {
            $results = $this->orgQuestionResponseRepository->getGPAdataForISQ($organizationId, $loggedInUserId, $yearId, $questionId, $caseDetails, $studentIdsToInclude);
            $testArray = [];
            foreach ($results as $key => $resultData) {
                unset($resultData['participant_id']); // can’t test RAND()
                $testArray[$key] = $resultData;
            }
            $testArray = array_slice($testArray, 0, 11);
            verify($testArray)->equals($expectedResults);
            verify(count($results))->equals($expectedCount);
        }, ["examples" =>
            [
                [ // Example 1 Test with all parameters which are valid and not empty
                    59, // organization id
                    181819, // user id
                    201516, // year id
                    1564, // question id
                    [
                        'sql' => "CASE WHEN oqr.decimal_value IN (:decimal_value_1) THEN 1 WHEN oqr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        'parameters' => [
                            'decimal_value_1' => [4],
                            'decimal_value_2' => [5]
                        ]
                    ],
                    [],
                    [
                        [
                            "org_academic_terms_id" =>  "212",
                            "subpopulation_id" =>  "1",
                            "gpa_value" =>  "2.42",
                            "term_name" =>  "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" =>  "212",
                            "subpopulation_id" =>  "2",
                            "gpa_value" =>  "3.40",
                            "term_name" =>  "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" =>  "213",
                            "subpopulation_id" =>  "1",
                            "gpa_value" =>  "3.51",
                            "term_name" =>  "Spring Semester 2016"
                        ],
                        [
                            "org_academic_terms_id" =>  "213",
                            "subpopulation_id" =>  "2",
                            "gpa_value" =>  "3.50",
                            "term_name" =>  "Spring Semester 2016"
                        ]
                    ],
                    4
                ],
                [ // Example 2 Test with empty data
                    59, // organization id
                    181819, // user id
                    201516, // year id
                    1567, // question id
                    [
                        'sql' => "CASE WHEN oqr.decimal_value IN (:decimal_value_1) THEN 1 WHEN oqr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        'parameters' => [
                            'decimal_value_1' => [4],
                            'decimal_value_2' => [5]
                        ]
                    ],
                    [],
                    [],
                    0
                ],
                [ // Example 3 Test with all parameters which are valid and not empty with filtered students
                    59, // organization id
                    181819, // user id
                    201516, // year id
                    1564, // question id
                    [
                        'sql' => "CASE WHEN oqr.decimal_value IN (:decimal_value_1) THEN 1 WHEN oqr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        'parameters' => [
                            'decimal_value_1' => [0,1,2,3,4],
                            'decimal_value_2' => [5]
                        ]
                    ],
                    [
                        4670047,4670044,4670043,4670042,4670029,4670026,4670024,4668818,4668807,4668802
                    ],
                    [
                        [
                            "org_academic_terms_id" =>  "212",
                            "subpopulation_id" =>  "1",
                            "gpa_value" =>  "1.58",
                            "term_name" =>  "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" =>  "212",
                            "subpopulation_id" =>  "1",
                            "gpa_value" =>  "1.98",
                            "term_name" =>  "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" =>  "212",
                            "subpopulation_id" =>  "1",
                            "gpa_value" =>  "2.43",
                            "term_name" =>  "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" =>  "212",
                            "subpopulation_id" =>  "1",
                            "gpa_value" =>  "1.15",
                            "term_name" =>  "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" =>  "212",
                            "subpopulation_id" =>  "1",
                            "gpa_value" =>  "2.30",
                            "term_name" =>  "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" =>  "212",
                            "subpopulation_id" =>  "1",
                            "gpa_value" =>  "2.92",
                            "term_name" =>  "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" =>  "212",
                            "subpopulation_id" =>  "1",
                            "gpa_value" =>  "1.98",
                            "term_name" =>  "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" =>  "212",
                            "subpopulation_id" =>  "1",
                            "gpa_value" =>  "1.86",
                            "term_name" =>  "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" =>  "212",
                            "subpopulation_id" =>  "1",
                            "gpa_value" =>  "1.50",
                            "term_name" =>  "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" =>  "212",
                            "subpopulation_id" =>  "1",
                            "gpa_value" =>  "3.20",
                            "term_name" =>  "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" =>  "213",
                            "subpopulation_id" =>  "1",
                            "gpa_value" =>  "2.76",
                            "term_name" =>  "Spring Semester 2016"
                        ]
                    ],
                    20
                ]
            ]
        ]);
    }

    public function testGetFactorDataForISQ()
    {
        $this->specify("Verify the functionality of the method getFactorDataForISQ", function ($organizationId, $surveyId, $cohortId, $questionId, $loggedInUserId, $surveyYearId, $caseDetails, $studentPopulationSurveyId, $studentIdsToInclude, $expectedResults) {
            $results = $this->orgQuestionResponseRepository->getFactorDataForISQ($organizationId, $surveyId, $cohortId, $questionId, $loggedInUserId, $surveyYearId, $caseDetails, $studentPopulationSurveyId, $studentIdsToInclude);
            $testArray = [];
            foreach ($results as $key => $gpaAndTermData) {
                unset($gpaAndTermData['participant_id']); // can’t test RAND()
                $testArray[$key] = $gpaAndTermData;
            }
            verify($testArray)->equals($expectedResults);
        }, ["examples" =>
            [
                [ // Example 1 Test with all parameters which are valid and not empty
                    45, // organization id
                    13, // survey id
                    3, // cohort id
                    4635, // question id
                    223639, // user id
                    201516, // survey year id
                    [  // Case details
                        "sql" => "CASE WHEN oqr.decimal_value IN (:decimal_value_1) THEN 'xxxx' WHEN oqr.decimal_value IN (:decimal_value_2) THEN 'yyyy'  END",
                        "parameters" => [
                            'decimal_value_1' => [1],
                            'decimal_value_2' => [2]
                        ]
                    ],
                    13, // studentPopulationSurveyId
                    [],
                    [
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "4",
                            "factor_name" => "Self-Discipline",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "5",
                            "factor_name" => "Time Management",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "6.33"
                        ],
                        [
                            "factor_id" => "6",
                            "factor_name" => "Financial Means",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "4.00"
                        ],
                        [
                            "factor_id" => "7",
                            "factor_name" => "Basic Academic Behaviors",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "6.00"
                        ],
                        [
                            "factor_id" => "9",
                            "factor_name" => "Academic Self-Efficacy",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "4.00"
                        ],
                        [
                            "factor_id" => "10",
                            "factor_name" => "Academic Resiliency",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "6.50"
                        ],
                        [
                            "factor_id" => "11",
                            "factor_name" => "Peer Connections",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "4.00"
                        ],
                        [
                            "factor_id" => "14",
                            "factor_name" => "Academic Integration",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "4.00"
                        ],
                        [
                            "factor_id" => "15",
                            "factor_name" => "Social Integration",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "4.00"
                        ],
                        [
                            "factor_id" => "16",
                            "factor_name" => "Satisfaction with Institution",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "1.00"
                        ],
                        [
                            "factor_id" => "17",
                            "factor_name" => "On-Campus: Social Aspects",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "18",
                            "factor_name" => "On-Campus: Environment",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "5.00"
                        ],
                        [
                            "factor_id" => "21",
                            "factor_name" => "Test Anxiety",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "1.00"
                        ]
                    ]
                ],
                [ // Example 2 Test with empty data
                    45, // organization id
                    11, // survey id
                    1, // cohort id
                    0, // question id
                    0, // user id
                    0, // survey year id
                    [
                        'sql' => "CASE WHEN oqr.decimal_value IN (:decimal_value_1) THEN 1 WHEN oqr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        'parameters' => [
                            'decimal_value_1' => [4],
                            'decimal_value_2' => [5]
                        ]
                    ],
                    15, // studentPopulationSurveyId
                    [],
                    []
                ],
                [ // Example 3 Test with all parameters which are valid and not empty with filtered students
                    45, // organization id
                    13, // survey id
                    3, // cohort id
                    4635, // question id
                    223639, // user id
                    201516, // survey year id
                    [  // Case details
                        "sql" => "CASE WHEN oqr.decimal_value IN (:decimal_value_1) THEN 'xxxx' WHEN oqr.decimal_value IN (:decimal_value_2) THEN 'yyyy'  END",
                        "parameters" => [
                            'decimal_value_1' => [1, 3, 4, 5],
                            'decimal_value_2' => [2]
                        ]
                    ],
                    13, // studentPopulationSurveyId
                    [
                        223639
                    ],
                    [
                        [
                            "factor_id" => "1",
                            "factor_name" => "Commitment to the Institution",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "4",
                            "factor_name" => "Self-Discipline",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "5",
                            "factor_name" => "Time Management",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "6.33"
                        ],
                        [
                            "factor_id" => "6",
                            "factor_name" => "Financial Means",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "4.00"
                        ],
                        [
                            "factor_id" => "7",
                            "factor_name" => "Basic Academic Behaviors",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "6.00"
                        ],
                        [
                            "factor_id" => "9",
                            "factor_name" => "Academic Self-Efficacy",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "4.00"
                        ],
                        [
                            "factor_id" => "10",
                            "factor_name" => "Academic Resiliency",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "6.50"
                        ],
                        [
                            "factor_id" => "11",
                            "factor_name" => "Peer Connections",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "4.00"
                        ],
                        [
                            "factor_id" => "14",
                            "factor_name" => "Academic Integration",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "4.00"
                        ],
                        [
                            "factor_id" => "15",
                            "factor_name" => "Social Integration",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "4.00"
                        ],
                        [
                            "factor_id" => "16",
                            "factor_name" => "Satisfaction with Institution",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "1.00"
                        ],
                        [
                            "factor_id" => "17",
                            "factor_name" => "On-Campus: Social Aspects",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "7.00"
                        ],
                        [
                            "factor_id" => "18",
                            "factor_name" => "On-Campus: Environment",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "5.00"
                        ],
                        [
                            "factor_id" => "21",
                            "factor_name" => "Test Anxiety",
                            "subpopulation_id" => "yyyy",
                            "factor_value" => "1.00"
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function testGetRetentionDataForISQ()
    {
        $this->specify("Verify the functionality of the method getRetentionDataForISQ", function ($organizationId, $personId, $questionId, $surveyId, $surveyYearId, $caseDetails, $studentIdsToInclude, $expectedCount, $expectedResults) {
            $results = $this->orgQuestionResponseRepository->getRetentionDataForISQ($organizationId, $personId, $questionId, $surveyId, $surveyYearId, $caseDetails, $studentIdsToInclude);
            $testArray = [];
            foreach ($results as $key => $gpaAndTermData) {
                unset($gpaAndTermData['participant_id']); // can’t test RAND()
                $testArray[$key] = $gpaAndTermData;
            }
            verify($expectedCount)->equals(count($results));
            $testArray = array_slice($testArray, 0, 11);
            verify($testArray)->equals($expectedResults);
        }, ["examples" =>
            [
                [ // Example 1 : Test with empty array
                    62, // organization id
                    5048809, // person id
                    5321, // question id
                    15, // survey id
                    201617, // survey year id
                    [
                        'sql' => "CASE WHEN oqr.decimal_value IN (:decimal_value_1) THEN 1 WHEN oqr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        'parameters' => [
                            'decimal_value_1' => [4],
                            'decimal_value_2' => [5]
                        ]
                    ],
                    [],
                    0,
                    []
                ],
                [ // Example 2 : Test with data
                    59, // organization id
                    53573, // person id
                    934, // question id
                    11, // survey id
                    201516, // survey year id
                    [
                        'sql' => "CASE WHEN oqr.decimal_value IN (:decimal_value_1) THEN 1 WHEN oqr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        'parameters' => [
                            'decimal_value_1' => [0],
                            'decimal_value_2' => [1]
                        ]
                    ],
                    [],
                    32,
                    [
                        [
                            "subpopulation_id"  =>  "2",
                            "organization_id"  =>  "59",
                            "person_id"  =>  "4614937",
                            "retention_tracking_year"  =>  "201516",
                            "year_id"  =>  "201516",
                            "year_name"  =>  "2015-16 Academic Year",
                            "retention_completion_variable_name"  =>  "Completed Degree in 1 Year or Less",
                            "years_from_retention_track"  =>  "0",
                            "retention_completion_value"  =>  "0",
                            "retention_completion_variable_order"  =>  null
                        ],
                        [
                            "subpopulation_id"  =>  "2",
                            "organization_id"  =>  "59",
                            "person_id"  =>  "4614937",
                            "retention_tracking_year"  =>  "201516",
                            "year_id"  =>  "201617",
                            "year_name"  =>  "2016-17 Academic Year",
                            "retention_completion_variable_name"  =>  "Completed Degree in 2 Years or Less",
                            "years_from_retention_track"  =>  "1",
                            "retention_completion_value"  =>  "0",
                            "retention_completion_variable_order"  =>  null
                        ],
                        [
                            "subpopulation_id"  =>  "2",
                            "organization_id"  =>  "59",
                            "person_id"  =>  "4614937",
                            "retention_tracking_year"  =>  "201516",
                            "year_id"  =>  "201718",
                            "year_name"  =>  "2017-18 Academic Year",
                            "retention_completion_variable_name"  =>  "Completed Degree in 3 Years or Less",
                            "years_from_retention_track"  =>  "2",
                            "retention_completion_value"  =>  "0",
                            "retention_completion_variable_order"  =>  null
                        ],
                        [
                            "subpopulation_id"  =>  "2",
                            "organization_id"  =>  "59",
                            "person_id"  =>  "4614937",
                            "retention_tracking_year"  =>  "201516",
                            "year_id"  =>  "201516",
                            "year_name"  =>  "2015-16 Academic Year",
                            "retention_completion_variable_name"  =>  "Retained to Midyear Year 1",
                            "years_from_retention_track"  =>  "0",
                            "retention_completion_value"  =>  "1",
                            "retention_completion_variable_order"  =>  "2"
                        ],
                        [
                            "subpopulation_id"  =>  "2",
                            "organization_id"  =>  "59",
                            "person_id"  =>  "4614937",
                            "retention_tracking_year"  =>  "201516",
                            "year_id"  =>  "201617",
                            "year_name"  =>  "2016-17 Academic Year",
                            "retention_completion_variable_name"  =>  "Retained to Midyear Year 2",
                            "years_from_retention_track"  =>  "1",
                            "retention_completion_value"  =>  "1",
                            "retention_completion_variable_order"  =>  "2"
                        ],
                        [
                            "subpopulation_id"  =>  "2",
                            "organization_id"  =>  "59",
                            "person_id"  =>  "4614937",
                            "retention_tracking_year"  =>  "201516",
                            "year_id"  =>  "201718",
                            "year_name"  =>  "2017-18 Academic Year",
                            "retention_completion_variable_name"  =>  "Retained to Midyear Year 3",
                            "years_from_retention_track"  =>  "2",
                            "retention_completion_value"  =>  "1",
                            "retention_completion_variable_order"  =>  "2"
                        ],
                        [
                            "subpopulation_id"  =>  "2",
                            "organization_id"  =>  "59",
                            "person_id"  =>  "4614937",
                            "retention_tracking_year"  =>  "201516",
                            "year_id"  =>  "201617",
                            "year_name"  =>  "2016-17 Academic Year",
                            "retention_completion_variable_name"  =>  "Retained to Start of Year 2",
                            "years_from_retention_track"  =>  "1",
                            "retention_completion_value"  =>  "1",
                            "retention_completion_variable_order"  =>  "1"
                        ],
                        [
                            "subpopulation_id"  =>  "2",
                            "organization_id"  =>  "59",
                            "person_id"  =>  "4614937",
                            "retention_tracking_year"  =>  "201516",
                            "year_id"  =>  "201718",
                            "year_name"  =>  "2017-18 Academic Year",
                            "retention_completion_variable_name"  =>  "Retained to Start of Year 3",
                            "years_from_retention_track"  =>  "2",
                            "retention_completion_value"  =>  "1",
                            "retention_completion_variable_order"  =>  "1"
                        ],
                        [
                            "subpopulation_id"  =>  "2",
                            "organization_id"  =>  "59",
                            "person_id"  =>  "4615338",
                            "retention_tracking_year"  =>  "201516",
                            "year_id"  =>  "201516",
                            "year_name"  =>  "2015-16 Academic Year",
                            "retention_completion_variable_name"  =>  "Completed Degree in 1 Year or Less",
                            "years_from_retention_track"  =>  "0",
                            "retention_completion_value"  =>  "0",
                            "retention_completion_variable_order"  =>  null
                        ],
                        [
                            "subpopulation_id"  =>  "2",
                            "organization_id"  =>  "59",
                            "person_id"  =>  "4615338",
                            "retention_tracking_year"  =>  "201516",
                            "year_id"  =>  "201617",
                            "year_name"  =>  "2016-17 Academic Year",
                            "retention_completion_variable_name"  =>  "Completed Degree in 2 Years or Less",
                            "years_from_retention_track"  =>  "1",
                            "retention_completion_value"  =>  "0",
                            "retention_completion_variable_order"  =>  null
                        ],
                        [
                            "subpopulation_id"  =>  "2",
                            "organization_id"  =>  "59",
                            "person_id"  =>  "4615338",
                            "retention_tracking_year"  =>  "201516",
                            "year_id"  =>  "201718",
                            "year_name"  =>  "2017-18 Academic Year",
                            "retention_completion_variable_name"  =>  "Completed Degree in 3 Years or Less",
                            "years_from_retention_track"  =>  "2",
                            "retention_completion_value"  =>  "0",
                            "retention_completion_variable_order"  =>  null
                        ]
                    ]
                ],
                [ // Example 3 : Test with data with filtered students
                    59, // organization id
                    53573, // person id
                    934, // question id
                    11, // survey id
                    201516, // survey year id
                    [
                        'sql' => "CASE WHEN oqr.decimal_value IN (:decimal_value_1) THEN 1 WHEN oqr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        'parameters' => [
                            'decimal_value_1' => [0],
                            'decimal_value_2' => [1]
                        ]
                    ],
                    [
                        4652149,4617739,4615338
                    ],
                    24,
                    [
                        [
                            "subpopulation_id" =>  "2",
                            "organization_id" =>  "59",
                            "person_id" =>  "4615338",
                            "retention_tracking_year" =>  "201516",
                            "year_id" =>  "201516",
                            "year_name" =>  "2015-16 Academic Year",
                            "retention_completion_variable_name" =>  "Completed Degree in 1 Year or Less",
                            "years_from_retention_track" =>  "0",
                            "retention_completion_value" =>  "0",
                            "retention_completion_variable_order" =>  null
                        ],
                        [
                            "subpopulation_id" =>  "2",
                            "organization_id" =>  "59",
                            "person_id" =>  "4615338",
                            "retention_tracking_year" =>  "201516",
                            "year_id" =>  "201617",
                            "year_name" =>  "2016-17 Academic Year",
                            "retention_completion_variable_name" =>  "Completed Degree in 2 Years or Less",
                            "years_from_retention_track" =>  "1",
                            "retention_completion_value" =>  "0",
                            "retention_completion_variable_order" =>  null
                        ],
                        [
                            "subpopulation_id" =>  "2",
                            "organization_id" =>  "59",
                            "person_id" =>  "4615338",
                            "retention_tracking_year" =>  "201516",
                            "year_id" =>  "201718",
                            "year_name" =>  "2017-18 Academic Year",
                            "retention_completion_variable_name" =>  "Completed Degree in 3 Years or Less",
                            "years_from_retention_track" =>  "2",
                            "retention_completion_value" =>  "0",
                            "retention_completion_variable_order" =>  null
                        ],
                        [
                            "subpopulation_id" =>  "2",
                            "organization_id" =>  "59",
                            "person_id" =>  "4615338",
                            "retention_tracking_year" =>  "201516",
                            "year_id" =>  "201516",
                            "year_name" =>  "2015-16 Academic Year",
                            "retention_completion_variable_name" =>  "Retained to Midyear Year 1",
                            "years_from_retention_track" =>  "0",
                            "retention_completion_value" =>  "1",
                            "retention_completion_variable_order" =>  "2"
                        ],
                        [
                            "subpopulation_id" =>  "2",
                            "organization_id" =>  "59",
                            "person_id" =>  "4615338",
                            "retention_tracking_year" =>  "201516",
                            "year_id" =>  "201617",
                            "year_name" =>  "2016-17 Academic Year",
                            "retention_completion_variable_name" =>  "Retained to Midyear Year 2",
                            "years_from_retention_track" =>  "1",
                            "retention_completion_value" =>  "1",
                            "retention_completion_variable_order" =>  "2"
                        ],
                        [
                            "subpopulation_id" =>  "2",
                            "organization_id" =>  "59",
                            "person_id" =>  "4615338",
                            "retention_tracking_year" =>  "201516",
                            "year_id" =>  "201718",
                            "year_name" =>  "2017-18 Academic Year",
                            "retention_completion_variable_name" =>  "Retained to Midyear Year 3",
                            "years_from_retention_track" =>  "2",
                            "retention_completion_value" =>  "1",
                            "retention_completion_variable_order" =>  "2"
                        ],
                        [
                            "subpopulation_id" =>  "2",
                            "organization_id" =>  "59",
                            "person_id" =>  "4615338",
                            "retention_tracking_year" =>  "201516",
                            "year_id" =>  "201617",
                            "year_name" =>  "2016-17 Academic Year",
                            "retention_completion_variable_name" =>  "Retained to Start of Year 2",
                            "years_from_retention_track" =>  "1",
                            "retention_completion_value" =>  "1",
                            "retention_completion_variable_order" =>  "1"
                        ],
                        [
                            "subpopulation_id" =>  "2",
                            "organization_id" =>  "59",
                            "person_id" =>  "4615338",
                            "retention_tracking_year" =>  "201516",
                            "year_id" =>  "201718",
                            "year_name" =>  "2017-18 Academic Year",
                            "retention_completion_variable_name" =>  "Retained to Start of Year 3",
                            "years_from_retention_track" =>  "2",
                            "retention_completion_value" =>  "1",
                            "retention_completion_variable_order" =>  "1"
                        ],
                        [
                            "subpopulation_id" =>  "2",
                            "organization_id" =>  "59",
                            "person_id" =>  "4617739",
                            "retention_tracking_year" =>  "201516",
                            "year_id" =>  "201516",
                            "year_name" =>  "2015-16 Academic Year",
                            "retention_completion_variable_name" =>  "Completed Degree in 1 Year or Less",
                            "years_from_retention_track" =>  "0",
                            "retention_completion_value" =>  "0",
                            "retention_completion_variable_order" =>  null
                        ],
                        [
                            "subpopulation_id" =>  "2",
                            "organization_id" =>  "59",
                            "person_id" =>  "4617739",
                            "retention_tracking_year" =>  "201516",
                            "year_id" =>  "201617",
                            "year_name" =>  "2016-17 Academic Year",
                            "retention_completion_variable_name" =>  "Completed Degree in 2 Years or Less",
                            "years_from_retention_track" =>  "1",
                            "retention_completion_value" =>  "0",
                            "retention_completion_variable_order" =>  null
                        ],
                        [
                            "subpopulation_id" =>  "2",
                            "organization_id" =>  "59",
                            "person_id" =>  "4617739",
                            "retention_tracking_year" =>  "201516",
                            "year_id" =>  "201718",
                            "year_name" =>  "2017-18 Academic Year",
                            "retention_completion_variable_name" =>  "Completed Degree in 3 Years or Less",
                            "years_from_retention_track" =>  "2",
                            "retention_completion_value" =>  "0",
                            "retention_completion_variable_order" =>  null
                        ]
                    ]
                ]
            ]

        ]);
    }

    public function testGetStudentIdsListBasedOnISQSelection()
    {
        $this->specify("Verify the functionality of the method getStudentIdsListBasedOnISQSelection", function ($organizationId, $orgQuestionId, $whereClause, $expectedResults, $expectedCount) {
            $results = $this->orgQuestionResponseRepository->getStudentIdsListBasedOnISQSelection($organizationId, $orgQuestionId, $whereClause);
            verify($expectedCount)->equals(count($results));
            $testArray = array_slice($results, 0, 11);
            verify($testArray)->equals($expectedResults);
        }, ["examples" =>
            [
                [ // Example 1 Test with category type decimal values
                    45, // organization id
                    4635, // question id
                    [
                        "where_query" => "oqr.decimal_value IN (:category_values)",
                        "parameters" => [
                            "category_values" => [
                                "0", "1", "2", "3", "4", "5", "6", "7", "99"
                            ]
                        ]
                    ],
                    [
                        "186982",
                        "187004",
                        "189238",
                        "223633",
                        "223636",
                        "223638",
                        "223639",
                        "223644",
                        "223654",
                        "223660",
                        "223663"
                    ],
                    133
                ],
                [ // Example 2 Test with number type decimal values
                    45, // organization id
                    4635, // question id
                    [
                        "where_query" =>  "((oqr.decimal_value BETWEEN :subpopulation1_min_digits AND :subpopulation1_max_digits) OR oqr.decimal_value IN (:subpopulation2_single_values))",
                        "parameters" =>  [
                            "subpopulation1_min_digits" =>  "0",
                            "subpopulation1_max_digits" =>  "1",
                            "subpopulation2_single_values" =>  "2"
                        ]
                    ],
                    [
                          "186982",
                          "187004",
                          "189238",
                          "223633",
                          "223636",
                          "223638",
                          "223639",
                          "223644",
                          "223660",
                          "223663",
                          "223668"
                    ],
                    127
                ],
                [ // Example 3 Test with number type decimal values but no data
                    45, // organization id
                    463545, // question id
                    [
                        "where_query" =>  "((oqr.decimal_value BETWEEN :subpopulation1_min_digits AND :subpopulation1_max_digits) OR oqr.decimal_value IN (:subpopulation2_single_values))",
                        "parameters" =>  [
                            "subpopulation1_min_digits" =>  "5",
                            "subpopulation1_max_digits" =>  "6",
                            "subpopulation2_single_values" =>  "7"
                        ]
                    ],
                    [
                    ],
                    0
                ],
                [ // Example 4 Test with empty where clause
                    45, // organization id
                    4635, // question id
                    [

                    ],
                    [
                        "186982",
                        "187004",
                        "189238",
                        "223633",
                        "223636",
                        "223638",
                        "223639",
                        "223644",
                        "223654",
                        "223660",
                        "223663"
                    ],
                    133
                ]
            ]
        ]);
    }
}