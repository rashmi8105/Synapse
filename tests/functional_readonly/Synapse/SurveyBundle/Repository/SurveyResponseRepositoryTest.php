<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\SurveyBundle\Repository\SurveyResponseRepository;
use Synapse\CoreBundle\SynapseConstant;

class SurveyResponseRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var SurveyResponseRepository
     */
    private $surveyResponseRepository;

    public function _before()
    {
        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->surveyResponseRepository = $this->repositoryResolver->getRepository(SurveyResponseRepository::REPOSITORY_KEY);
    }


    public function testGetScaledCategorySurveyQuestions(){
        $this->specify("Verify the functionality of the method getScaledCategorySurveyQuestions", function($surveyId, $organizationId, $studentIds, $ebiQuestionIds, $expectedResult) {
            $functionResult = $this->surveyResponseRepository->getScaledCategorySurveyQuestions($surveyId, $organizationId, $studentIds, $ebiQuestionIds);
            verify($functionResult)->equals($expectedResult);
        }, [
            'examples' => [
                // test case for student id 4709664 and organization 87
                [11, 87, [4709664], [113, 121, 123, 59, 60], [

                    [
                        "ebi_question_id" => 113,
                        "survey_questions_id" => 324,
                        "qnbr" => 100,
                        "question_text" => "Thinking about your role as a college student, to what degree do you know: How to allocate the correct amount of time to meet each of your obligations (e.g., social life, work life, family, student organizations, coursework)?",
                        "question_type" => "Q",
                        "student_count" => 1,
                        "standard_deviation" => 0.00,
                        "mean" => 7.00
                    ],
                    [
                        "ebi_question_id" => 121,
                        "survey_questions_id" => 332,
                        "qnbr" => 101,
                        "question_text" => "To what degree are you experiencing stress regarding: Being responsible for yourself (e.g., getting to class, doing your homework, etc.)?",
                        "question_type" => "Q",
                        "student_count" => 1,
                        "standard_deviation" => 0.00,
                        "mean" => 7.00
                    ],
                    [
                        "ebi_question_id" => 123,
                        "survey_questions_id" => 334,
                        "qnbr" => 103,
                        "question_text" => "To what degree are you experiencing stress regarding: Having enough time during the regular school week to do everything that is expected of you?",
                        "question_type" => "Q",
                        "student_count" => 1,
                        "standard_deviation" => 0.00,
                        "mean" => 3.00
                    ],
                    [
                        "ebi_question_id" => 59,
                        "survey_questions_id" => 246,
                        "qnbr" => 104,
                        "question_text" => "During this term, to what degree do you intend to: Participate in a student organization?",
                        "question_type" => "Q",
                        "student_count" => 1,
                        "standard_deviation" => 0.00,
                        "mean" => 7.00
                    ],
                    [
                        "ebi_question_id" => 60,
                        "survey_questions_id" => 247,
                        "qnbr" => 105,
                        "question_text" => "During this term, to what degree do you intend to: Hold a leadership position in a college/university student organization?",
                        "question_type" => "Q",
                        "student_count" => 1,
                        "standard_deviation" => 0.00,
                        "mean" => 1.00
                    ]
                ]],
                // test case for student id 489322 and organization 87
                [11, 87, [489322], [16, 17, 18],[
                    [
                        "ebi_question_id" => 16,
                        "survey_questions_id" => 267,
                        "qnbr" => 116,
                        "question_text" => "On this campus, to what degree are you connecting with people: Who share common interests with you?",
                        "question_type" => "Q",
                        "student_count" => 1,
                        "standard_deviation" => 0.00,
                        "mean" => 6.00
                    ],
                    [
                        "ebi_question_id" => 17,
                        "survey_questions_id" => 266,
                        "qnbr" => 117,
                        "question_text" => "On this campus, to what degree are you connecting with people: Who include you in their activities?",
                        "question_type" => "Q",
                        "student_count" => 1,
                        "standard_deviation" => 0.00,
                        "mean" => 6.00
                    ],
                    [
                        "ebi_question_id" => 18,
                        "survey_questions_id" => 265,
                        "qnbr" => 118,
                        "question_text" => "On this campus, to what degree are you connecting with people: You like?",
                        "question_type" => "Q",
                        "student_count" => 1,
                        "standard_deviation" => 0.00,
                        "mean" => 6.00
                    ]
                ]],
                // test case with wrong data type
                ['test', 'test', ['test'], ['test'],[]],
                // test case with null/empty value
                [null, null, [], [], []]
            ]]);
    }

    public function testGetScaledTypeQuestions(){
        $this->specify("Verify the functionality of the method getScaledTypeQuestions", function($surveyId, $organizationId, $personIds, $surveyQuestionsId, $expectedResult) {
            $functionResult = $this->surveyResponseRepository->getScaledTypeQuestions($surveyId, $organizationId, $personIds, $surveyQuestionsId);
            verify($functionResult)->equals($expectedResult);
        }, [
            'examples' => [
                // test case for student ids 4869743, 4871265, 4869699, 1198984, 4652248, 671171 and organization 99
                [13, 99, [4869743, 4871265, 4869699, 1198984, 4652248, 671171], 651, [
                    [
                        "survey_questions_id" => 651,
                        "student_count" => 1,
                        "question_number" => 1,
                        "survey_id" => 13,
                        "org_id" => 99,
                        "response_type" => "decimal",
                        "decimal_value" => 1.00,
                        "char_value" => null,
                        "charmax_value" => null,
                        "option_text" => '(1) Not at all',
                        "option_value" => 1,
                        "question_rpt" => 'Degree, certificate, or licensure?'
                    ],
                    [
                        "survey_questions_id" => 651,
                        "student_count" => 1,
                        "question_number" => 1,
                        "survey_id" => 13,
                        "org_id" => 99,
                        "response_type" => "decimal",
                        "decimal_value" => 2.00,
                        "char_value" => null,
                        "charmax_value" => null,
                        "option_text" => '(2)',
                        "option_value" => 2,
                        "question_rpt" => 'Degree, certificate, or licensure?'
                    ],
                    [
                        "survey_questions_id" => 651,
                        "student_count" => 1,
                        "question_number" => 1,
                        "survey_id" => 13,
                        "org_id" => 99,
                        "response_type" => "decimal",
                        "decimal_value" => 4.00,
                        "char_value" => null,
                        "charmax_value" => null,
                        "option_text" => '(4) Moderately',
                        "option_value" => 4,
                        "question_rpt" => 'Degree, certificate, or licensure?'
                    ],
                    [
                        "survey_questions_id" => 651,
                        "student_count" => 1,
                        "question_number" => 1,
                        "survey_id" => 13,
                        "org_id" => 99,
                        "response_type" => "decimal",
                        "decimal_value" => 5.00,
                        "char_value" => null,
                        "charmax_value" => null,
                        "option_text" => '(5)',
                        "option_value" => 5,
                        "question_rpt" => 'Degree, certificate, or licensure?'
                    ],
                    [
                        "survey_questions_id" => 651,
                        "student_count" => 1,
                        "question_number" => 1,
                        "survey_id" => 13,
                        "org_id" => 99,
                        "response_type" => "decimal",
                        "decimal_value" => 6.00,
                        "char_value" => null,
                        "charmax_value" => null,
                        "option_text" => '(6)',
                        "option_value" => 6,
                        "question_rpt" => 'Degree, certificate, or licensure?'
                    ],
                    [
                        "survey_questions_id" => 651,
                        "student_count" => 1,
                        "question_number" => 1,
                        "survey_id" => 13,
                        "org_id" => 99,
                        "response_type" => "decimal",
                        "decimal_value" => 7.00,
                        "char_value" => null,
                        "charmax_value" => null,
                        "option_text" => '(7) Extremely',
                        "option_value" => 7,
                        "question_rpt" => 'Degree, certificate, or licensure?'
                    ],

                ]],
                // test case for student ids 4612100, 966153, 618035, 966331, 670852 and organization 99
                [11, 99, [4612100, 966153, 618035, 966331, 670852], 219, [
                    [
                        "survey_questions_id" => 219,
                        "student_count" => 1,
                        "question_number" => 75,
                        "survey_id" => 11,
                        "org_id" => 99,
                        "response_type" => "decimal",
                        "decimal_value" => 1.00,
                        "char_value" => null,
                        "charmax_value" => null,
                        "option_text" => '(1) Not at all',
                        "option_value" => 1,
                        "question_rpt" => 'Makes "to-do lists"?'
                    ],
                    [
                        "survey_questions_id" => 219,
                        "student_count" => 1,
                        "question_number" => 75,
                        "survey_id" => 11,
                        "org_id" => 99,
                        "response_type" => "decimal",
                        "decimal_value" => 2.00,
                        "char_value" => null,
                        "charmax_value" => null,
                        "option_text" => '(2)',
                        "option_value" => 2,
                        "question_rpt" => 'Makes "to-do lists"?'
                    ],
                    [
                        "survey_questions_id" => 219,
                        "student_count" => 1,
                        "question_number" => 75,
                        "survey_id" => 11,
                        "org_id" => 99,
                        "response_type" => "decimal",
                        "decimal_value" => 3.00,
                        "char_value" => null,
                        "charmax_value" => null,
                        "option_text" => '(3)',
                        "option_value" => 3,
                        "question_rpt" => 'Makes "to-do lists"?'
                    ],
                    [
                        "survey_questions_id" => 219,
                        "student_count" => 1,
                        "question_number" => 75,
                        "survey_id" => 11,
                        "org_id" => 99,
                        "response_type" => "decimal",
                        "decimal_value" => 4.00,
                        "char_value" => null,
                        "charmax_value" => null,
                        "option_text" => '(4) Moderately',
                        "option_value" => 4,
                        "question_rpt" => 'Makes "to-do lists"?'
                    ],
                    [
                        "survey_questions_id" => 219,
                        "student_count" => 1,
                        "question_number" => 75,
                        "survey_id" => 11,
                        "org_id" => 99,
                        "response_type" => "decimal",
                        "decimal_value" => 5.00,
                        "char_value" => null,
                        "charmax_value" => null,
                        "option_text" => '(5)',
                        "option_value" => 5,
                        "question_rpt" => 'Makes "to-do lists"?'
                    ]
                ]],
                // test case with wrong data type
                ['test', 'test', ['test'], 'test', []],
                // test case with null/empty value
                [null, null, [], null, []]
            ]]);
    }

    public function testGetDescriptiveQuestions(){
        $this->specify("Verify the functionality of the method getDescriptiveQuestions", function($surveyId, $organizationId, $studentIds, $ebiQuestionIds, $questionType, $responseField, $expectedResult) {
            $functionResult = $this->surveyResponseRepository->getDescriptiveQuestions($surveyId, $organizationId, $studentIds, $ebiQuestionIds, $questionType, $responseField);
            verify($functionResult)->equals($expectedResult);
        }, [
            'examples' => [
                // test case for student id 4612900 and organization 99
                [13, 99, [4612900], [476, 563, 549], "D", "char_value", [
                    [
                        "ebi_question_id" => 476,
                        "survey_questions_id" => 687,
                        "qnbr" => 10,
                        "question_text" => "How many courses are you taking?",
                        "question_type" => "D",
                        "student_count" => 1,
                        "response_type" => "decimal",
                        "char_value" => null
                    ],
                    [
                        "ebi_question_id" => 563,
                        "survey_questions_id" => 774,
                        "qnbr" => 40,
                        "question_text" => "How many hours, on average, do you expect to spend studying for a test this college term?",
                        "question_type" => "D",
                        "student_count" => 1,
                        "response_type" => "decimal",
                        "char_value" => null
                    ],
                    [
                        "ebi_question_id" => 549,
                        "survey_questions_id" => 760,
                        "qnbr" => 49,
                        "question_text" => "How many people are assigned to live in your bedroom (including yourself)?",
                        "question_type" => "D",
                        "student_count" => 1,
                        "response_type" => "decimal",
                        "char_value" => null
                    ]
                ]],
                // test case for student id 618035 and organization 99
                [11, 99, [618035], [121, 122, 123], "Q", "char_value", [
                    [
                        "ebi_question_id" => 121,
                        "survey_questions_id" => 332,
                        "qnbr" => 101,
                        "question_text" => "To what degree are you experiencing stress regarding: Being responsible for yourself (e.g., getting to class, doing your homework, etc.)?",
                        "question_type" => "Q",
                        "student_count" => 1,
                        "response_type" => "decimal",
                        "char_value" => null
                    ],
                    [
                        "ebi_question_id" => 122,
                        "survey_questions_id" => 333,
                        "qnbr" => 102,
                        "question_text" => "To what degree are you experiencing stress regarding: Motivating yourself to get your work done on time?",
                        "question_type" => "Q",
                        "student_count" => 1,
                        "response_type" => "decimal",
                        "char_value" => null
                    ],
                    [
                        "ebi_question_id" => 123,
                        "survey_questions_id" => 334,
                        "qnbr" => 103,
                        "question_text" => "To what degree are you experiencing stress regarding: Having enough time during the regular school week to do everything that is expected of you?",
                        "question_type" => "Q",
                        "student_count" => 1,
                        "response_type" => "decimal",
                        "char_value" => null
                    ]
                ]],
                // test case with wrong data type
                ['test', 'test', ['test'], ['test'], 1, "char_value", []],
                // test case with null/empty value
                [null, null, [], [], null, "char_value", []],
            ]]);
    }

    public function testGetNumericQuestions(){
        $this->specify("Verify the functionality of the method getNumericQuestions", function($surveyId, $organizationId, $studentIds, $ebiQuestionIds, $expectedResult) {
            $functionResult = $this->surveyResponseRepository->getNumericQuestions($surveyId, $organizationId, $studentIds, $ebiQuestionIds);
            verify($functionResult)->equals($expectedResult);
        }, [
            'examples' => [
                // test case for student id 671171 and organization 99
                [13, 99, [671171], [476, 477, 512], []],
                // test case for student id 671171 and organization 87
                [11, 87, [671171], [476, 477, 512], []],
                // test case with wrong data type
                ['test', 'test', ['test'], ['test'], []],
                // test case with null/empty value
                [null, null, [], [], []]
            ]
            ]);
    }

    public function testGetNumericQuestionsResponse(){
        $this->specify("Verify the functionality of the method getNumericQuestionsResponse", function($surveyId, $organizationId, $personIds, $surveyQuestionsId, $expectedResult) {
            $functionResult = $this->surveyResponseRepository->getNumericQuestionsResponse($surveyId, $organizationId, $personIds, $surveyQuestionsId);
            verify($functionResult)->equals($expectedResult);
        }, [
            'examples' => [
                // test case for student id 4869743 and organization 99
                [13, 99, [4869743], 651, [
                    [
                        "survey_questions_id" => 651,
                        "responded_count" => 1,
                        "minimum_value" => 1.00,
                        "maximum_value" => 1.00,
                        "standard_deviation" => 0,
                        "mean" => 1.00,
                        "option_value" => 1
                    ]
                ]],
                // test case for student id 491819 and organization 99
                [11, 87, [491819], 219, [
                    [
                        "survey_questions_id" => 219,
                        "responded_count" => 1,
                        "minimum_value" => 1.00,
                        "maximum_value" => 1.00,
                        "standard_deviation" => 0,
                        "mean" => 1.00,
                        "option_value" => 1
                    ]
                ]],
                // test case with wrong data type
                [11, 87, [491819], 219, [
                    [
                        "survey_questions_id" => 219,
                        "responded_count" => 1,
                        "minimum_value" => 1.00,
                        "maximum_value" => 1.00,
                        "standard_deviation" => 0,
                        "mean" => 1.00,
                        "option_value" => 1
                    ]
                ]],
                // test case with wrong data type
                ['test', 'test', ['test'], 'test', [
                    [
                        "survey_questions_id" => null,
                        "responded_count" => 0,
                        "minimum_value" => null,
                        "maximum_value" => null,
                        "standard_deviation" => null,
                        "mean" => null,
                        "option_value" => null
                    ]
                ]],
                // test case with null/empty value
                [null, null, [], null, [
                    [
                        "survey_questions_id" => null,
                        "responded_count" => 0,
                        "minimum_value" => null,
                        "maximum_value" => null,
                        "standard_deviation" => null,
                        "mean" => null,
                        "option_value" => null
                    ]
                ]]
            ]
        ]);
    }

    public function testCreateCaseQueryForISQorSurvey()
    {
        $this->specify("Verify the functionality of the method createCaseQueryForISQorSurvey", function ($questionTypeWithRangeDetails, $surveyType, $groupName, $expectedResults) {
            $results = $this->surveyResponseRepository->createCaseQueryForISQorSurvey($questionTypeWithRangeDetails, $surveyType, $groupName);
            verify($results)->equals($expectedResults);
        }, ["examples" =>
            [
                [ // Example 1 test with isqs (question type categorical)
                    [ // questionType With RangeDetails
                        "category" => [
                            "subpopulation1" => [
                                "4"
                            ],
                            "subpopulation2" => [
                                "5"
                            ]
                        ]
                    ],
                    'isqs', // survey Type
                    [ // group Name
                        "xxxx",
                        "yyyy"
                    ],
                    [ // Expected Results
                        "sql" => "CASE WHEN oqr.decimal_value IN (:decimal_value_1) THEN 1 WHEN oqr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        "parameters" => [
                            "decimal_value_1" => [
                                "4"
                            ],
                            "decimal_value_2" => [
                                "5"
                            ]
                        ]
                    ],
                ],
                [ // Example 2 test with survey (question type categorical)
                    [ // questionType With RangeDetails
                        "category" => [
                            "subpopulation1" => [
                                "3,4"
                            ],
                            "subpopulation2" => [
                                "5"
                            ]
                        ]
                    ],
                    'survey', // survey Type
                    [ // group Name
                        "group 1",
                        "group 2"
                    ],
                    [ // Expected Results
                        "sql" => "CASE WHEN sr.decimal_value IN (:decimal_value_1) THEN 1 WHEN sr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        "parameters" => [
                            "decimal_value_1" => [
                                "3,4"
                            ],
                            "decimal_value_2" => [
                                "5"
                            ]
                        ]
                    ],
                ],
                [ // Example 3 test with isq (question type number)
                    [ // questionType With RangeDetails
                        "number" => [
                            "subpopulation1" => [
                                "single_value" => "8"
                            ],
                            "subpopulation2" => [
                                "min_digits" => "10.000",
                                "max_digits" => "20.0000"
                            ],
                        ]
                    ],
                    'isqs', // survey Type
                    [ // group Name
                        "xxxx",
                        "yyyy"
                    ],
                    [ // Expected Result
                        "sql" => "CASE WHEN oqr.char_value = :single_value_1 THEN 1 WHEN oqr.char_value BETWEEN :min_digits_2 AND :max_digits_2 THEN 2  END",
                        "parameters" => [
                            "single_value_1" => "8",
                            "min_digits_2" => "10.000",
                            "max_digits_2" => "20.0000"
                        ]
                    ]
                ],
                [ // Example 4 test with survey (question type number)
                    [ // questionType With RangeDetails
                        "number" => [
                            "subpopulation1" => [
                                "single_value" => "4"
                            ],
                            "subpopulation2" => [
                                "min_digits" => "30.000",
                                "max_digits" => "40.0000"
                            ],
                        ]
                    ],
                    'survey', // survey Type
                    [ // group Name
                        "survey group 1",
                        "survey group 2"
                    ],
                    [ // Expected Result
                        "sql" => "CASE WHEN sr.char_value = :single_value_1 THEN 1 WHEN sr.char_value BETWEEN :min_digits_2 AND :max_digits_2 THEN 2  END",
                        "parameters" => [
                            "single_value_1" => "4",
                            "min_digits_2" => "30.000",
                            "max_digits_2" => "40.0000"
                        ]
                    ]
                ]
            ],
        ]);
    }

    public function testGetGPAdataForSurvey()
    {
        $this->specify("Verify the functionality of the method getGPAdataForSurvey", function ($organizationId, $loggedInUserId, $yearId, $questionId, $caseDetails, $studentIdsToInclude, $expectedResults) {
            $results = $this->surveyResponseRepository->getGPAdataForSurvey($organizationId, $loggedInUserId, $yearId, $questionId, $caseDetails, $studentIdsToInclude);
            $testArray = [];
            foreach ($results as $key => $gpaAndTermData) {
                unset($gpaAndTermData['participant_id']); // can’t test RAND()
                $testArray[$key] = $gpaAndTermData;
            }
            verify($testArray)->equals($expectedResults);
        }, ["examples" =>
            [
                [ // Example 1 test with category type data for survey
                    59, // organization id
                    53573, // user id
                    201516, // year id
                    8, // question id
                    [
                        'sql' => "CASE WHEN sr.decimal_value IN (:decimal_value_1) THEN 1 WHEN sr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        'parameters' => [
                            'decimal_value_1' => [1],
                            'decimal_value_2' => [7]
                        ]
                    ],
                    [

                    ],
                    [ // Expected Result
                        [
                            "org_academic_terms_id" => "212",
                            "subpopulation_id" => "2",
                            "gpa_value" => "4.00",
                            "term_name" => "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" => "212",
                            "subpopulation_id" => "2",
                            "gpa_value" => "3.32",
                            "term_name" => "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" => "212",
                            "subpopulation_id" => "2",
                            "gpa_value" => "2.76",
                            "term_name" => "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" => "212",
                            "subpopulation_id" => "2",
                            "gpa_value" => "2.80",
                            "term_name" => "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" => "212",
                            "subpopulation_id" => "2",
                            "gpa_value" => "1.91",
                            "term_name" => "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" => "213",
                            "subpopulation_id" => "2",
                            "gpa_value" => "3.76",
                            "term_name" => "Spring Semester 2016"
                        ],
                        [
                            "org_academic_terms_id" => "213",
                            "subpopulation_id" => "2",
                            "gpa_value" => "3.76",
                            "term_name" => "Spring Semester 2016"
                        ],
                        [
                            "org_academic_terms_id" => "213",
                            "subpopulation_id" => "2",
                            "gpa_value" => "1.14",
                            "term_name" => "Spring Semester 2016"
                        ],
                        [
                            "org_academic_terms_id" => "213",
                            "subpopulation_id" => "2",
                            "gpa_value" => "2.28",
                            "term_name" => "Spring Semester 2016"
                        ],
                        [
                            "org_academic_terms_id" => "213",
                            "subpopulation_id" => "2",
                            "gpa_value" => "1.20",
                            "term_name" => "Spring Semester 2016"
                        ]
                    ]
                ],
                [ // Example 2 test with category type survey metadata
                    59, // organization id
                    53573, // user id
                    201516, // year id
                    9, // question id
                    [
                        'sql' => "CASE WHEN sr.decimal_value IN (:decimal_value_1) THEN 1 WHEN sr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        'parameters' => [
                            'decimal_value_1' => [1],
                            'decimal_value_2' => [7]
                        ]
                    ],
                    [

                    ],
                    [ // Expected Result
                        [
                            "org_academic_terms_id" => "212",
                            "subpopulation_id" => "2",
                            "gpa_value" => "4.00",
                            "term_name" => "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" => "212",
                            "subpopulation_id" => "2",
                            "gpa_value" => "3.32",
                            "term_name" => "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" => "212",
                            "subpopulation_id" => "2",
                            "gpa_value" => "1.91",
                            "term_name" => "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" => "213",
                            "subpopulation_id" => "2",
                            "gpa_value" => "3.76",
                            "term_name" => "Spring Semester 2016"
                        ],
                        [
                            "org_academic_terms_id" => "213",
                            "subpopulation_id" => "2",
                            "gpa_value" => "3.76",
                            "term_name" => "Spring Semester 2016"
                        ],
                        [
                            "org_academic_terms_id" => "213",
                            "subpopulation_id" => "2",
                            "gpa_value" => "1.20",
                            "term_name" => "Spring Semester 2016"
                        ]
                    ]
                ],
                [ // Example 3 with filtered students
                    59, // organization id
                    53573, // user id
                    201516, // year id
                    8, // question id
                    [
                        'sql' => "CASE WHEN sr.decimal_value IN (:decimal_value_1) THEN 1 WHEN sr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        'parameters' => [
                            'decimal_value_1' => [1],
                            'decimal_value_2' => [7]
                        ]
                    ],
                    [
                        4652149,4618245,4617739,4615338
                    ],
                    [
                        [
                            "org_academic_terms_id" =>  "212",
                            "subpopulation_id" =>  "2",
                            "gpa_value" =>  "3.32",
                            "term_name" =>  "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" =>  "212",
                            "subpopulation_id" =>  "2",
                            "gpa_value" =>  "2.76",
                            "term_name" =>  "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" =>  "212",
                            "subpopulation_id" =>  "2",
                            "gpa_value" =>  "2.80",
                            "term_name" =>  "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" =>  "212",
                            "subpopulation_id" =>  "2",
                            "gpa_value" =>  "1.91",
                            "term_name" =>  "Fall Semester 2015"
                        ],
                        [
                            "org_academic_terms_id" =>  "213",
                            "subpopulation_id" =>  "2",
                            "gpa_value" =>  "3.76",
                            "term_name" =>  "Spring Semester 2016"
                        ],
                        [
                            "org_academic_terms_id" =>  "213",
                            "subpopulation_id" =>  "2",
                            "gpa_value" =>  "1.14",
                            "term_name" =>  "Spring Semester 2016"
                        ],
                        [
                            "org_academic_terms_id" =>  "213",
                            "subpopulation_id" =>  "2",
                            "gpa_value" =>  "2.28",
                            "term_name" =>  "Spring Semester 2016"
                        ],
                        [
                            "org_academic_terms_id" =>  "213",
                            "subpopulation_id" =>  "2",
                            "gpa_value" =>  "1.20",
                            "term_name" =>  "Spring Semester 2016"
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function testGetFactorDataForSurvey()
    {
        $this->specify("Verify the functionality of the method getFactorDataForSurvey", function ($organizationId, $surveyId, $cohortId, $questionId, $loggedInUserId, $surveyYearId, $caseDetails, $studentPopulationSurveyId, $studentIdsToInclude, $expectedResults) {
            $results = $this->surveyResponseRepository->getFactorDataForSurvey($organizationId, $surveyId, $cohortId, $questionId, $loggedInUserId, $surveyYearId, $caseDetails, $studentPopulationSurveyId, $studentIdsToInclude);
            $testArray = [];
            foreach ($results as $key => $gpaAndTermData) {
                unset($gpaAndTermData['participant_id']); // can’t test RAND()
                $testArray[$key] = $gpaAndTermData;
            }
            verify($testArray)->equals($expectedResults);
        }, ["examples" =>
            [
                [ // Example 1 test factor data with category type metadata for survey
                    2, // organization id
                    11, //survey Id
                    2, //cohort id
                    226, // question id
                    2, // user id
                    201516, // survey year id
                    [
                        'sql' => "CASE WHEN sr.decimal_value IN (:decimal_value_1) THEN 1 WHEN sr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        'parameters' => [
                            'decimal_value_1' => [1],
                            'decimal_value_2' => [7]
                        ]
                    ],
                    12, // student population survey Id
                    [

                    ],
                    [
                        [
                            "factor_id" =>  "1",
                            "factor_name" =>  "Commitment to the Institution",
                            "subpopulation_id" =>  "2",
                            "factor_value" =>  "1.00"
                        ],
                        [
                            "factor_id" =>  "7",
                            "factor_name" =>  "Basic Academic Behaviors",
                            "subpopulation_id" =>  "2",
                            "factor_value" =>  "1.00"
                        ],
                        [
                            "factor_id" =>  "14",
                            "factor_name" =>  "Academic Integration",
                            "subpopulation_id" =>  "2",
                            "factor_value" =>  "1.00"
                        ],
                        [
                            "factor_id" =>  "15",
                            "factor_name" =>  "Social Integration",
                            "subpopulation_id" =>  "2",
                            "factor_value" =>  "1.00"
                        ],
                        [
                            "factor_id" =>  "16",
                            "factor_name" =>  "Satisfaction with Institution",
                            "subpopulation_id" =>  "2",
                            "factor_value" =>  "1.00"
                        ]
                    ]
                ],
                [ // Example 2 Test with empty data
                    2, // organization id
                    11, //survey Id
                    1, //cohort id
                    227, // question id
                    2, // user id
                    201516, // survey year id
                    [
                        'sql' => "CASE WHEN sr.decimal_value IN (:decimal_value_1) THEN 1 WHEN sr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        'parameters' => [
                            'decimal_value_1' => [10],
                            'decimal_value_2' => [12]
                        ]
                    ],
                    15, // student population survey Id
                    [

                    ],
                    [ // Expected Result

                    ]
                ],
                [ // Example 3 with filtered students
                    2, // organization id
                    11, //survey Id
                    2, //cohort id
                    226, // question id
                    2, // user id
                    201516, // survey year id
                    [
                        'sql' => "CASE WHEN sr.decimal_value IN (:decimal_value_1) THEN 1 WHEN sr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        'parameters' => [
                            'decimal_value_1' => [1,2,3,4,5,6],
                            'decimal_value_2' => [7]
                        ]
                    ],
                    12, // student population survey Id
                    [
                        4644982,4644984,4644974
                    ],
                    [
                        [
                            "factor_id" =>  "1",
                            "factor_name" =>  "Commitment to the Institution",
                            "subpopulation_id" =>  "1",
                            "factor_value" =>  "5.33"
                        ],
                        [
                            "factor_id" =>  "1",
                            "factor_name" =>  "Commitment to the Institution",
                            "subpopulation_id" =>  "1",
                            "factor_value" =>  "4.00"
                        ],
                        [
                            "factor_id" =>  "1",
                            "factor_name" =>  "Commitment to the Institution",
                            "subpopulation_id" =>  "2",
                            "factor_value" =>  "1.00"
                        ],
                        [
                            "factor_id" =>  "7",
                            "factor_name" =>  "Basic Academic Behaviors",
                            "subpopulation_id" =>  "1",
                            "factor_value" =>  "4.67"
                        ],
                        [
                            "factor_id" =>  "7",
                            "factor_name" =>  "Basic Academic Behaviors",
                            "subpopulation_id" =>  "1",
                            "factor_value" =>  "4.00"
                        ],
                        [
                            "factor_id" =>  "7",
                            "factor_name" =>  "Basic Academic Behaviors",
                            "subpopulation_id" =>  "2",
                            "factor_value" =>  "1.00"
                        ],
                        [
                            "factor_id" =>  "14",
                            "factor_name" =>  "Academic Integration",
                            "subpopulation_id" =>  "1",
                            "factor_value" =>  "6.00"
                        ],
                        [
                            "factor_id" =>  "14",
                            "factor_name" =>  "Academic Integration",
                            "subpopulation_id" =>  "1",
                            "factor_value" =>  "4.00"
                        ],
                        [
                            "factor_id" =>  "14",
                            "factor_name" =>  "Academic Integration",
                            "subpopulation_id" =>  "2",
                            "factor_value" =>  "1.00"
                        ],
                        [
                            "factor_id" =>  "15",
                            "factor_name" =>  "Social Integration",
                            "subpopulation_id" =>  "1",
                            "factor_value" =>  "6.00"
                        ],
                        [
                            "factor_id" =>  "15",
                            "factor_name" =>  "Social Integration",
                            "subpopulation_id" =>  "1",
                            "factor_value" =>  "4.00"
                        ],
                        [
                            "factor_id" =>  "15",
                            "factor_name" =>  "Social Integration",
                            "subpopulation_id" =>  "2",
                            "factor_value" =>  "1.00"
                        ],
                        [
                            "factor_id" =>  "16",
                            "factor_name" =>  "Satisfaction with Institution",
                            "subpopulation_id" =>  "1",
                            "factor_value" =>  "5.67"
                        ],
                        [
                            "factor_id" =>  "16",
                            "factor_name" =>  "Satisfaction with Institution",
                            "subpopulation_id" =>  "1",
                            "factor_value" =>  "4.00"
                        ],
                        [
                            "factor_id" =>  "16",
                            "factor_name" =>  "Satisfaction with Institution",
                            "subpopulation_id" =>  "2",
                            "factor_value" =>  "1.00"
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function testGetRetentionDataForSurvey()
    {
        $this->specify("Verify the functionality of the method getRetentionDataForSurvey", function ($organizationId, $personId, $questionId, $surveyId, $surveyYearId, $caseDetails, $studentIdsToInclude, $expectedCount, $expectedResults) {
            $results = $this->surveyResponseRepository->getRetentionDataForSurvey($organizationId, $personId, $questionId, $surveyId, $surveyYearId, $caseDetails, $studentIdsToInclude);
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
                        'sql' => "CASE WHEN sr.decimal_value IN (:decimal_value_1) THEN 1 WHEN sr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        'parameters' => [
                            'decimal_value_1' => [10],
                            'decimal_value_2' => [12]
                        ]
                    ],
                    [

                    ],
                    0,
                    []
                ],
                [ // Example 2 : Test with category type metadata 
                    59, // organization id
                    53573, // person id
                    49, // question id
                    11, // survey id
                    201516, // survey year id
                    [
                        'sql' => "CASE WHEN sr.decimal_value IN (:decimal_value_1) THEN 1 WHEN sr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        'parameters' => [
                            'decimal_value_1' => [0],
                            'decimal_value_2' => [1]
                        ]
                    ],
                    [

                    ],
                    16,
                    [
                        [
                            "subpopulation_id"  =>  "2",
                            "organization_id"  =>  "59",
                            "person_id"  =>  "4617739",
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
                            "person_id"  =>  "4617739",
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
                            "person_id"  =>  "4617739",
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
                            "person_id"  =>  "4617739",
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
                            "person_id"  =>  "4617739",
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
                            "person_id"  =>  "4617739",
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
                            "person_id"  =>  "4617739",
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
                            "person_id"  =>  "4617739",
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
                            "person_id"  =>  "4618245",
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
                            "person_id"  =>  "4618245",
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
                            "person_id"  =>  "4618245",
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
                    49, // question id
                    11, // survey id
                    201516, // survey year id
                    [
                        'sql' => "CASE WHEN sr.decimal_value IN (:decimal_value_1) THEN 1 WHEN sr.decimal_value IN (:decimal_value_2) THEN 2  END",
                        'parameters' => [
                            'decimal_value_1' => [0],
                            'decimal_value_2' => [1]
                        ]
                    ],
                    [
                        4617739
                    ],
                    8,
                    [
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
                        ],
                        [
                            "subpopulation_id" =>  "2",
                            "organization_id" =>  "59",
                            "person_id" =>  "4617739",
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
                            "person_id" =>  "4617739",
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
                            "person_id" =>  "4617739",
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
                            "person_id" =>  "4617739",
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
                            "person_id" =>  "4617739",
                            "retention_tracking_year" =>  "201516",
                            "year_id" =>  "201718",
                            "year_name" =>  "2017-18 Academic Year",
                            "retention_completion_variable_name" =>  "Retained to Start of Year 3",
                            "years_from_retention_track" =>  "2",
                            "retention_completion_value" =>  "1",
                            "retention_completion_variable_order" =>  "1"
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function testGetStudentIdsListBasedOnSurveyQuestionSelection()
    {
        $this->specify("Verify the functionality of the method getStudentIdsListBasedOnSurveyQuestionSelection", function ($organizationId, $ebiQuestionId, $whereClause, $expectedResults) {
            $results = $this->surveyResponseRepository->getStudentIdsListBasedOnSurveyQuestionSelection($organizationId, $ebiQuestionId, $whereClause);
            $testArray = array_slice($results, 0, 11);
            verify($testArray)->equals($expectedResults);
        }, ["examples" =>
            [
                [ // Example 1 with category type data
                    189, // organization id
                    654, // question id
                    [
                        "where_query" =>  "sr.decimal_value IN (:category_values)",
                        "parameters" =>  [
                            "category_values" =>  [
                                "0",
                                "1",
                                "2","3","4","5"
                            ]
                        ]
                    ],
                    [
                        "4672448",
                        "4672465",
                        "4672489",
                        "4672511",
                        "4672514",
                        "4672515",
                        "4672544",
                        "4672640",
                        "4878433",
                        "4878441"
                    ]
                ],
                [
                // Example 2 with numeric type data
                189, // organization id
                654, // question id
                [
                    "where_query" =>  "((sr.decimal_value BETWEEN :subpopulation1_min_digits AND :subpopulation1_max_digits) OR sr.decimal_value IN (:subpopulation2_single_values))",
                    "parameters" =>  [
                        "subpopulation1_min_digits" =>  "0",
                        "subpopulation1_max_digits" =>  "1",
                        "subpopulation2_single_values" =>  "2"
                    ]
                ],
                    [
                        "4672489",
                        "4672640"
                    ]
            ],
                [ // Example 3 with where clause empty
                    189, // organization id
                    654, // question id
                    [

                    ],
                    [
                        "4672374",
                        "4672379",
                        "4672380",
                        "4672382",
                        "4672383",
                        "4672384",
                        "4672388",
                        "4672392",
                        "4672394",
                        "4672395",
                        "4672398"
                    ]
                ]
            ]
        ]);
    }
}
?>

