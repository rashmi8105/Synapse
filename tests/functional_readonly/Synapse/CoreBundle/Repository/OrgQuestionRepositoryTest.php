<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\OrgQuestionRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;

class OrgQuestionRepositoryTest extends Test
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
     * @var OrgQuestionRepository
     */
    private $orgQuestionRepository;

    public function _before()
    {
        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->orgQuestionRepository = $this->repositoryResolver->getRepository(OrgQuestionRepository::REPOSITORY_KEY);
    }

    public function testGetISQCohortsAndSurveysWithRespectToYearsForOrganization()
    {
        $this->specify("Verify the functionality of the method getISQCohortsAndSurveysWithRespectToYearsForOrganization", function ($organizationId, $userId, $orgAcademicYearId, $surveyStatus, $excludeQuestionType, $expectedResults, $expectedCount) {
            $results = $this->orgQuestionRepository->getISQCohortsAndSurveysWithRespectToYearsForOrganization($organizationId, $userId, $orgAcademicYearId, $surveyStatus, $excludeQuestionType);
            verify(count($results))->equals($expectedCount);
            $testArray = array_slice($results, 0, 4);
            verify($testArray)->equals($expectedResults);

        }, ["examples" =>
            [
                [ //Example 1 : Test without org academic yead id
                    2, // organization id
                    4799548, //user id
                    null, // org academic yead id
                    ['launched', 'closed'], // survey status
                    ['LA', 'SA', 'MR'], // excludeQuestionType
                    [ // Expected result
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "13",
                            "survey_name" => "Transition Two",
                            "status" => "closed",
                            "open_date" => "2015-08-26 06:00:00",
                            "close_date" => "2015-08-28 06:00:00",
                            "question_type" => "Q   ",
                            "question_id" => "391",
                            "question_text" => "How satisfied were you with your Orientation leader?"
                        ],
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "13",
                            "survey_name" => "Transition Two",
                            "status" => "closed",
                            "open_date" => "2015-08-26 06:00:00",
                            "close_date" => "2015-08-28 06:00:00",
                            "question_type" => "D   ",
                            "question_id" => "392",
                            "question_text" => "When did you attend Orientation?"
                        ],
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "13",
                            "survey_name" => "Transition Two",
                            "status" => "closed",
                            "open_date" => "2015-08-26 06:00:00",
                            "close_date" => "2015-08-28 06:00:00",
                            "question_type" => "NA  ",
                            "question_id" => "393",
                            "question_text" => "How many welcome week events did you attend?"
                        ],
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "14",
                            "survey_name" => "Check-Up Two",
                            "status" => "closed",
                            "open_date" => "2016-01-12 06:00:00",
                            "close_date" => "2016-02-04 06:00:00",
                            "question_type" => "D   ",
                            "question_id" => "3968",
                            "question_text" => "Pick a number"
                        ]
                    ],
                    11
                ],
                [ //Example 2 : Test Without org academic yead id, survey status, excludeQuestionType
                    2, // organization id
                    4799548, //user id
                    null, // org academic yead id
                    null, // survey status
                    null, // excludeQuestionType
                    [
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "13",
                            "survey_name" => "Transition Two",
                            "status" => "closed",
                            "open_date" => "2015-08-26 06:00:00",
                            "close_date" => "2015-08-28 06:00:00",
                            "question_type" => "Q   ",
                            "question_id" => "391",
                            "question_text" => "How satisfied were you with your Orientation leader?"
                        ],
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "13",
                            "survey_name" => "Transition Two",
                            "status" => "closed",
                            "open_date" => "2015-08-26 06:00:00",
                            "close_date" => "2015-08-28 06:00:00",
                            "question_type" => "D   ",
                            "question_id" => "392",
                            "question_text" => "When did you attend Orientation?"
                        ],
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "13",
                            "survey_name" => "Transition Two",
                            "status" => "closed",
                            "open_date" => "2015-08-26 06:00:00",
                            "close_date" => "2015-08-28 06:00:00",
                            "question_type" => "NA  ",
                            "question_id" => "393",
                            "question_text" => "How many welcome week events did you attend?"
                        ],
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "14",
                            "survey_name" => "Check-Up Two",
                            "status" => "closed",
                            "open_date" => "2016-01-12 06:00:00",
                            "close_date" => "2016-02-04 06:00:00",
                            "question_type" => "D   ",
                            "question_id" => "3968",
                            "question_text" => "Pick a number"
                        ]
                    ],
                    18
                ],
                [ //Example 3 : Test with all data available
                    2, // organization id
                    4799548, //user id
                    88, // org academic yead id
                    ['launched', 'closed'], // survey status
                    ['LA', 'SA', 'MR'], // excludeQuestionType
                    [ // Expected result
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "13",
                            "survey_name" => "Transition Two",
                            "status" => "closed",
                            "open_date" => "2015-08-26 06:00:00",
                            "close_date" => "2015-08-28 06:00:00",
                            "question_type" => "Q   ",
                            "question_id" => "391",
                            "question_text" => "How satisfied were you with your Orientation leader?"
                        ],
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "13",
                            "survey_name" => "Transition Two",
                            "status" => "closed",
                            "open_date" => "2015-08-26 06:00:00",
                            "close_date" => "2015-08-28 06:00:00",
                            "question_type" => "D   ",
                            "question_id" => "392",
                            "question_text" => "When did you attend Orientation?"
                        ],
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "13",
                            "survey_name" => "Transition Two",
                            "status" => "closed",
                            "open_date" => "2015-08-26 06:00:00",
                            "close_date" => "2015-08-28 06:00:00",
                            "question_type" => "NA  ",
                            "question_id" => "393",
                            "question_text" => "How many welcome week events did you attend?"
                        ],
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "14",
                            "survey_name" => "Check-Up Two",
                            "status" => "closed",
                            "open_date" => "2016-01-12 06:00:00",
                            "close_date" => "2016-02-04 06:00:00",
                            "question_type" => "D   ",
                            "question_id" => "3968",
                            "question_text" => "Pick a number"
                        ]
                    ],
                    11
                ],
                [ //Example 4 : Test with null data
                    null, // organization id
                    null, //user id
                    null, // org academic yead id
                    null, // survey status
                    null, // excludeQuestionType
                    [],
                    0
                ]
            ]
        ]);
    }

    public function testGetSurveysAndCohortsWithRespectToYearsForOrganization()
    {
        $this->specify("Verify the functionality of the method getSurveysAndCohortsWithRespectToYearsForOrganization", function ($organizationId, $userId, $orgAcademicYearId, $surveyStatus, $excludeQuestionType, $expectedResults, $expectedCount) {
            $results = $this->orgQuestionRepository->getSurveysAndCohortsWithRespectToYearsForOrganization($organizationId, $userId, $orgAcademicYearId, $surveyStatus, $excludeQuestionType);
            verify(count($results))->equals($expectedCount);
            $testArray = array_slice($results, 0, 11);
            verify($testArray)->equals($expectedResults);
        }, ["examples" =>
            [
                [ // Example 1: Test with empty org_acedimic_year_id and survey status
                    2, // organization id
                    4799548, // user id
                    null, // acedemic year id
                    null, // survey status
                    null, // excludeQuestionType
                    [ // Expected result
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "11",
                            "survey_name" => "Transition One",
                            "status" => "closed",
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "Q",
                            "question_id" => "8",
                            "question_text" => "To what degree are you committed to completing a: Degree, certificate, or licensure?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "Q",
                            "question_id" => "9",
                            "question_text" => "To what degree are you committed to completing a: Degree, certificate, or licensure at this institution?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "D",
                            "question_id" => "44",
                            "question_text" => "How many courses are you taking?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "D",
                            "question_id" => "45",
                            "question_text" => "Of those, how many courses are you struggling in?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "Q",
                            "question_id" => "13",
                            "question_text" => "To what degree are you the kind of person who: Attends class?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "Q",
                            "question_id" => "14",
                            "question_text" => "To what degree are you the kind of person who: Takes good notes in class?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "Q",
                            "question_id" => "15",
                            "question_text" => "To what degree are you the kind of person who: Turns in required homework assignments?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "D",
                            "question_id" => "61",
                            "question_text" => "When are you predominately on-campus?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "D",
                            "question_id" => "62",
                            "question_text" => "How far are you currently living from campus?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "D",
                            "question_id" => "63",
                            "question_text" => "What do you typically use for transportation to campus?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "D",
                            "question_id" => "64",
                            "question_text" => "On average, how long does it take you to get to campus one way?"
                        ]
                    ],
                    444
                ],
                [ // Example 2: Test with empty data
                    null, // organization id
                    null, // user id
                    null, // acedemic year id
                    null, // survey status
                    null, // excludeQuestionType
                    [], // expected output
                    0,
                ],
                [ // Example 3: Test with available data
                    2, // organization id
                    4799548, // user id
                    88, // acedemic year id
                    ['launched', 'closed'], // survey status
                    ['LA', 'SA', 'MR'], //excludeQuestionType
                    [
                        [
                            "year_id" => "201516",
                            "org_academic_year_id" => "88",
                            "year_name" => "2015-2016",
                            "cohort" => "1",
                            "cohort_name" => "Survey Cohort 1",
                            "survey_id" => "11",
                            "survey_name" => "Transition One",
                            "status" => "closed",
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "Q",
                            "question_id" => "8",
                            "question_text" => "To what degree are you committed to completing a: Degree, certificate, or licensure?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "Q",
                            "question_id" => "9",
                            "question_text" => "To what degree are you committed to completing a: Degree, certificate, or licensure at this institution?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "D",
                            "question_id" => "44",
                            "question_text" => "How many courses are you taking?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "D",
                            "question_id" => "45",
                            "question_text" => "Of those, how many courses are you struggling in?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "Q",
                            "question_id" => "13",
                            "question_text" => "To what degree are you the kind of person who: Attends class?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "Q",
                            "question_id" => "14",
                            "question_text" => "To what degree are you the kind of person who: Takes good notes in class?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "Q",
                            "question_id" => "15",
                            "question_text" => "To what degree are you the kind of person who: Turns in required homework assignments?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "D",
                            "question_id" => "61",
                            "question_text" => "When are you predominately on-campus?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "D",
                            "question_id" => "62",
                            "question_text" => "How far are you currently living from campus?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "D",
                            "question_id" => "63",
                            "question_text" => "What do you typically use for transportation to campus?"
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
                            "open_date" => "2015-08-24 06:00:00",
                            "close_date" => "2015-10-28 06:00:00",
                            "question_type" => "D",
                            "question_id" => "64",
                            "question_text" => "On average, how long does it take you to get to campus one way?"
                        ]
                    ],
                    428
                ]
            ]
        ]);
    }

}
