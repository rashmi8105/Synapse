<?php

namespace Synapse\SurveyBundle\Service\Impl;

use Codeception\Specify;
use Codeception\Test\Unit;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Entity\OrgAcademicYear;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\LanguageMaster;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\Survey;
use Synapse\CoreBundle\Entity\SurveyLang;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Repository\SurveyLangRepository;
use Synapse\CoreBundle\Repository\SurveyRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\MapworksToolBundle\DAO\IssueDAO;
use Synapse\MapworksToolBundle\EntityDto\IssuePaginationDTO;
use Synapse\MapworksToolBundle\EntityDto\TopIssuesDTO;
use Synapse\MapworksToolBundle\EntityDto\IssuesInputDTO;
use Synapse\SearchBundle\Service\Impl\StudentListService;
use Synapse\SurveyBundle\Entity\Factor;
use Synapse\SurveyBundle\Entity\Issue;
use Synapse\SurveyBundle\Entity\IssueLang;
use Synapse\SurveyBundle\Entity\SurveyQuestions;
use Synapse\SurveyBundle\EntityDto\FactorsArrayDto;
use Synapse\SurveyBundle\EntityDto\IssueCreateDto;
use Synapse\SurveyBundle\EntityDto\IssueCreateQuesOptionsDto;
use Synapse\SurveyBundle\EntityDto\IssueCreateQuestionsDto;

class IssueServiceTest extends Unit
{
    use \Codeception\Specify;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var int
     */
    private $academicYearId = 192;

    /**
     * @var int
     */
    private $cohort = 1;

    /**
     * @var int
     */
    private $facultyId = 5049081;

    /**
     * @var int
     */
    private $organizationId = 62;

    /**
     * @var Person
     */
    private $personObject;

    /**
     * @var int
     */
    private $surveyId = 15;

    /**
     * @var int
     */
    private $topIssuesCount = 5;


    public function _before()
    {
        $this->container = $this->getMock('container', ['get', 'getParameter']);
        $this->logger = $this->getMock('logger', ['debug', 'error', 'addCritical']);
        $this->repositoryResolver = $this->getMock('repositoryResolver', ['getRepository']);

        $this->personObject = new Person();
        $this->personObject->setId($this->facultyId);

    }


    public function testGetTopIssuesWithStudentList()
    {
        $this->specify("Test getTopIssuesWithStudentList", function ($rootCall, $studentPopulation, $issueArray, $expectedResult) {

            $mockIssueDao = $this->getMock("IssueDAO", ["generateStudentIssuesTemporaryTable", "getTopIssuesFromStudentIssues", "getDistinctStudentPopulationCount", "getStudentListFromStudentIssues", "getDistinctParticipantStudentPopulationCount"]);
            $mockStudentListService = $this->getMock("StudentListService", ["getStudentListWithMetadata"]);
            $mockAcademicYearService = $this->getMock("AcademicYearService", ["getCurrentOrgAcademicYearId"]);


            $issuesInputDTOData = $this->getIssuesInputDTO($rootCall, $studentPopulation);
            $topIssues = $this->generateFakeTopIssue($issueArray);

            //This takes care of the goofiness of EXPECTS being on Objects and not methods
            $indexForIssueDAO = 0;

            $mockIssueDao->expects($this->at($indexForIssueDAO))->method("generateStudentIssuesTemporaryTable")->willReturn(true);
            $indexForIssueDAO++;
            $mockIssueDao->expects($this->at($indexForIssueDAO))->method("getTopIssuesFromStudentIssues")->willReturn($topIssues);
            $indexForIssueDAO++;


            for ($issueCount = 0; $issueCount < count($topIssues); $issueCount++) {
                $numberOfStudentsWithIssue = $topIssues[$issueCount]['numerator'];
                $mockIssueDao->expects($this->at($indexForIssueDAO))->method("getDistinctParticipantStudentPopulationCount")->willReturn($numberOfStudentsWithIssue);
                $indexForIssueDAO++;
            }

            if (!empty($topIssues)){
                $mockIssueDao->expects($this->at($indexForIssueDAO))->method("getDistinctStudentPopulationCount")->willReturn($studentPopulation);
                $indexForIssueDAO++;
                $mockIssueDao->expects($this->at($indexForIssueDAO))->method("getDistinctParticipantStudentPopulationCount")->willReturn($studentPopulation);
                $indexForIssueDAO++;
            }


            $mockPersonRepository = $this->getMock("PersonRepository", ["find"]);
            $mockPersonRepository->method("find")->willReturn($this->personObject);

            $orgAcademicYearObject = new OrgAcademicYear();
            $mockOrgAcademicYearRepository = $this->getMock("OrgAcademicYearRepository", ["find"]);
            $mockOrgAcademicYearRepository->method("find")->willReturn($orgAcademicYearObject);

            $mockAcademicYearService->method("getCurrentOrgAcademicYearId")->willReturn(5);

            $orgSurveyObject = new SurveyLang();
            $mockSurveyLangRepository = $this->getMock("SurveyLangRepository", ["findOneBy"]);
            $mockSurveyLangRepository->method("findOneBy")->willReturn($orgSurveyObject);


            for ($issueCounter = 0; $issueCounter < count($topIssues); $issueCounter++) {

                $recordsPerPage = 25;
                $numberOfStudentsWithIssue = $topIssues[$issueCounter]['numerator'];


                $studentIds = $this->generateFakeStudentIds($numberOfStudentsWithIssue);
                $studentRecords = $this->generateFakeStudentList($recordsPerPage, $numberOfStudentsWithIssue);
                $mockIssueDao->expects($this->at($indexForIssueDAO))->method("getStudentListFromStudentIssues")->willReturn($studentIds);
                $mockStudentListService->expects($this->at($issueCounter))->method("getStudentListWithMetadata")->willReturn($studentRecords);
                $indexForIssueDAO++;
            }


            $this->container->method('get')->willReturnMap([
                [
                    IssueDAO::DAO_KEY,
                    $mockIssueDao
                ],
                [
                    StudentListService::SERVICE_KEY,
                    $mockStudentListService
                ],
                [
                    AcademicYearService::SERVICE_KEY,
                    $mockAcademicYearService
                ]
            ]);

            $this->repositoryResolver->method('getRepository')->willReturnMap([
                [
                    PersonRepository::REPOSITORY_KEY,
                    $mockPersonRepository
                ],
                [
                    OrgAcademicYearRepository::REPOSITORY_KEY,
                    $mockOrgAcademicYearRepository
                ],
                [
                    SurveyLangRepository::REPOSITORY_KEY,
                    $mockSurveyLangRepository
                ],
                [
                    SurveyRepository::REPOSITORY_KEY,
                    $mockSurveyLangRepository
                ]
            ]);

            $issueServiceObject = new IssueService($this->repositoryResolver, $this->logger, $this->container);

            $topIssuesWithStudentList = $issueServiceObject->getTopIssuesWithStudentList($this->organizationId, $this->facultyId, $issuesInputDTOData);
            $this->assertInstanceOf(TopIssuesDTO::class, $topIssuesWithStudentList);
            $this->assertEquals($expectedResult, $topIssuesWithStudentList->getTopIssues());

        }, [
            "examples" => [
                //Comparing the results of getTopIssuesWithStudentList with expected output with provided parameters
                [
                    // IssuesInputDTO
                    true,
                    300,
                    [
                        ['issue_id' => 125,
                            'issue_name' => 'Homesick (separation)',
                            'numerator' => 1984,
                            'denominator' => 1984
                        ],
                        ['issue_id' => 100,
                            'issue_name' => 'Struggling in at least 2 courses',
                            'numerator' => 795,
                            'denominator' => 1976
                        ],
                        ['issue_id' => 134,
                            'issue_name' => 'Test Anxiety',
                            'numerator' => 790,
                            'denominator' => 1978
                        ],
                        ['issue_id' => 130,
                            'issue_name' => 'Low social aspects (on-campus living)',
                            'numerator' => 414,
                            'denominator' => 1900
                        ],
                        ['issue_id' => 126,
                            'issue_name' => 'Homesick (distressed)',
                            'numerator' => 421,
                            'denominator' => 1982
                        ],

                    ],
                    [
                        [
                            'issue_id' => '125',
                            'top_issue' => 1,
                            'name' => 'Homesick (separation)',
                            'non_participant_count_with_issue' => 0,
                            'participant_count_with_issue' => 1984,
                            'total_student_population_available_for_issue' => 1984,
                            'percentage' => '100.0',
                            'image' => 'anyGivenIcon.png',
                            'current_page' => 1,
                            'records_per_page' => 25,
                            'total_pages' => 80,
                            'sort_by' => '+student_last_name',
                            'students_with_issue_paginated_list' =>
                                $this->generateFakeStudentRecords(25)
                        ],

                        [
                            'issue_id' => '100',
                            'top_issue' => 2,
                            'name' => 'Struggling in at least 2 courses',
                            'non_participant_count_with_issue' => 0,
                            'participant_count_with_issue' => 795,
                            'total_student_population_available_for_issue' => 1976,
                            'percentage' => '40.2',
                            'image' => 'anyGivenIcon.png',
                            'current_page' => 1,
                            'records_per_page' => 25,
                            'total_pages' => 32,
                            'sort_by' => '+student_last_name',
                            'students_with_issue_paginated_list' =>
                                $this->generateFakeStudentRecords(25)
                        ],

                        [
                            'issue_id' => '134',
                            'top_issue' => 3,
                            'name' => 'Test Anxiety',
                            'non_participant_count_with_issue' => 0,
                            'participant_count_with_issue' => 790,
                            'total_student_population_available_for_issue' => 1978,
                            'percentage' => '39.9',
                            'image' => 'anyGivenIcon.png',
                            'current_page' => 1,
                            'records_per_page' => 25,
                            'total_pages' => 32,
                            'sort_by' => '+student_last_name',
                            'students_with_issue_paginated_list' =>
                                $this->generateFakeStudentRecords(25)
                        ],
                        [
                            'issue_id' => '130',
                            'top_issue' => 4,
                            'name' => 'Low social aspects (on-campus living)',
                            'non_participant_count_with_issue' => 0,
                            'participant_count_with_issue' => 414,
                            'total_student_population_available_for_issue' => 1900,
                            'percentage' => '21.8',
                            'image' => 'anyGivenIcon.png',
                            'current_page' => 1,
                            'records_per_page' => 25,
                            'total_pages' => 17,
                            'sort_by' => '+student_first_name',
                            'students_with_issue_paginated_list' =>
                                $this->generateFakeStudentRecords(25)
                        ],
                        [
                            'issue_id' => '126',
                            'top_issue' => 5,
                            'name' => 'Homesick (distressed)',
                            'non_participant_count_with_issue' => 0,
                            'participant_count_with_issue' => 421,
                            'total_student_population_available_for_issue' => 1982,
                            'percentage' => '21.2',
                            'image' => 'anyGivenIcon.png',
                            'current_page' => 1,
                            'records_per_page' => 25,
                            'total_pages' => 17,
                            'sort_by' => '+student_last_name',
                            'students_with_issue_paginated_list' =>
                                $this->generateFakeStudentRecords(25)
                        ]
                    ] //Expected output
                ],
                //Comparing the results of getTopIssuesWithStudentList with expected output with provided parameters (passing only one topIssue)
                [
                    // IssuesInputDTO
                    true,
                    300,
                    [
                        ['issue_id' => 125,
                            'issue_name' => 'Homesick (separation)',
                            'numerator' => 1984,
                            'denominator' => 1984
                        ]
                    ],
                    [
                        [
                            'issue_id' => "125",
                            'top_issue' => 1,
                            'name' => 'Homesick (separation)',
                            'non_participant_count_with_issue' => 0,
                            'participant_count_with_issue' => 1984,
                            'total_student_population_available_for_issue' => 1984,
                            'percentage' => '100.0',
                            'image' => 'anyGivenIcon.png',
                            'current_page' => 1,
                            'records_per_page' => 25,
                            'total_pages' => 80,
                            'sort_by' => '+student_last_name',
                            'students_with_issue_paginated_list' =>
                                $this->generateFakeStudentRecords(25)
                        ]
                    ]
                ],
                [
                    // No Top 5 Issues For Students
                    true,
                    300,
                    [],
                    []
                ],


            ]]);

    }


    public function testGetTopIssues()
    {
        $this->specify("Test getTopIssues", function ($orgAcademicYearId, $cohort, $surveyId, $daoReturn, $expectedResult) {
            $mockIssueDao = $this->getMock('IssueDAO', ["generateStudentIssuesTemporaryTable", "getTopIssuesFromStudentIssues"]);
            $mockIssueDao->method('getTopIssuesFromStudentIssues')->willReturn($daoReturn);
            $this->container->method('get')->willReturnMap([
                [
                    IssueDAO::DAO_KEY,
                    $mockIssueDao
                ]
            ]);
            $issueServiceObject = new IssueService($this->repositoryResolver, $this->logger, $this->container);
            $topIssues = $issueServiceObject->getTopIssues($this->topIssuesCount, $this->organizationId, $this->facultyId, $orgAcademicYearId, $cohort, $surveyId);
            $this->assertEquals($expectedResult, $topIssues);
        }, [
            "examples" => [
                //DAO returns something
                [
                    192,
                    15,
                    1,
                    [
                        [
                            'issue_id' => '125',
                            'issue_name' => 'Homesick (separation)',
                            'numerator' => '1984',
                            'denominator' => '4006',
                            'percent' => '49.5257',
                            'icon' => 'large-report-icon-homesick.png',
                        ]

                    ],
                    [
                        [
                            'issue_id' => '125',
                            'issue_name' => 'Homesick (separation)',
                            'numerator' => '1984',
                            'denominator' => '4006',
                            'percent' => '49.5257',
                            'icon' => 'large-report-icon-homesick.png',
                        ]
                    ]
                ],
                //DAO returns Nothing
                [
                    192,
                    15,
                    1,
                    [],
                    []
                ]

            ]
        ]);
    }

            public function testHasStudentPopulationOrIssuesChanged()
            {
                $this->specify("test hasStudentPopulationOrIssuesChanged", function ($rootCall, $totalStudentPopulation, $totalStudents, $studentsByIssueIds, $topIssuesIds, $expectedResult) {
                    $issueService = new IssueService($this->repositoryResolver, $this->logger, $this->container);
                    $hasChanged = $issueService->hasStudentPopulationOrIssuesChanged($this->getIssuesInputDTO($rootCall, $totalStudentPopulation), $totalStudents, $studentsByIssueIds, $topIssuesIds);
                    $this->assertEquals($expectedResult, $hasChanged);
                }, [
                    "examples" => [
                        // example 1: if root call hasChange is false
                        [
                            true, // rootCall
                            10878, // totalStudentPopulation
                            534, // totalStudents
                            [], // studentsByIssueIds
                            [], // topIssuesIds
                            false // expectedResult
                        ],
                        // example 2: if not root and  getTotalStudentPopulation and getTotalStudents return different value output is true
                        [
                            false, // rootCall
                            10878, // totalStudentPopulation
                            534, // totalStudents
                            [], // studentsByIssueIds
                            [], // topIssuesIds
                            true // expectedResult
                        ],
                        // example 3: if not root and  getTotalStudentPopulation and getTotalStudents are equal value output is true
                        [
                            false, // rootCall
                            534, // totalStudentPopulation
                            534, // totalStudents
                            [
                                3 => 299,
                                20 => 100,
                                12 => 50,
                                7 => 10,
                                10 => 5
                            ], // studentsByIssueIds
                            [20, 12, 7, 10], // topIssuesIds
                            false // expectedResult
                        ],
                        // example 3: if not root and  getTotalStudentPopulation and getTotalStudents are equal and provided all topIssuesIds have keys of totalStudents  output is false
                        [
                            false, // rootCall
                            534, // totalStudentPopulation
                            534, // totalStudents
                            [
                                3 => 299,
                                20 => 100,
                                12 => 50,
                                7 => 10,
                                10 => 5
                            ], // studentsByIssueIds
                            [3, 20, 12, 7, 10], // topIssuesIds
                            false // expectedResult
                        ]
                    ]
                ]);
            }






            public function testCreateIssue()
            {
                $this->specify("Test Create new issue", function ($issueCreateDto, $expectedResult) {

                    $mockLogger = $this->getMock('Logger', array(
                        'debug',
                        'error',
                        'info'
                    ));

                    $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                        'getRepository'
                    ));

                    $mockSurveyRepository = $this->getMock('SurveyRepository', array(
                        'find'
                    ));

                    $mockSurveyRepository->expects($this->any())
                        ->method('find')
                        ->will($this->returnValue($this->getSurvey()));

                    $mockSurveyQuestionsRepository = $this->getMock('SurveyQuestions', array(
                        'find'
                    ));

                    $mockSurveyQuestionsRepository->expects($this->any())
                        ->method('find')
                        ->will($this->returnValue($this->getSurveyQuestions()));

                    $mockIssueRepository = $this->getMock('Issue', array(
                        'persist',
                        'flush'
                    ));


                    $mockIssueRepository->expects($this->any())
                        ->method('persist')
                        ->will($this->returnValue($this->getIssues()));

                    $mockLanguageMasterRepository = $this->getMock('LanguageMaster', array(
                        'find',
                        'persist'
                    ));

                    $mockLanguageMaster = $this->getMock('LanguageMaster', array(
                        'find',
                        'persist'
                    ));

                    $mockLanguageMasterRepository->expects($this->any())
                        ->method('find')
                        ->will($this->returnValue($this->getLanguage()));

                    $mockLanguageMasterRepository->expects($this->any())
                        ->method('persist')
                        ->will($this->returnValue($mockLanguageMaster));

                    $mockIssueLangRepository = $this->getMock('IssueLang', array(
                        'persist'
                    ));

                    $mockIssueLang = $this->getMock('IssueLang', array(
                        'persist'
                    ));

                    $mockIssueLangRepository->expects($this->any())
                        ->method('persist')
                        ->will($this->returnValue($mockIssueLang));

                    $mockFactorRepository = $this->getMock('Factor', array(
                        'find'
                    ));

                    $mockFactorRepository->expects($this->any())
                        ->method('find')
                        ->will($this->returnValue($this->getFactor()));

                    $mockRepositoryResolver->method('getRepository')
                        ->willReturnMap([
                            [
                                'SynapseCoreBundle:Survey',
                                $mockSurveyRepository
                            ],
                            [
                                'SynapseSurveyBundle:SurveyQuestions',
                                $mockSurveyQuestionsRepository
                            ],
                            [
                                'SynapseSurveyBundle:Issue',
                                $mockIssueRepository
                            ],
                            [
                                'SynapseCoreBundle:LanguageMaster',
                                $mockLanguageMasterRepository
                            ],
                            [
                                'SynapseSurveyBundle:IssueLang',
                                $mockIssueLangRepository
                            ],
                            [
                                'SynapseSurveyBundle:Factor',
                                $mockFactorRepository
                            ]
                        ]);

                    $mockContainer = $this->getMock('Container', array(
                        'get'
                    ));

                    $issueService = new IssueService($mockRepositoryResolver, $mockLogger, $mockContainer);
                    $result = $issueService->createIssue($issueCreateDto);
                    $this->assertEquals($expectedResult['lang_id'], $result->getLangId());
                    $this->assertEquals($expectedResult['issue_name'], $result->getIssueName());
                    $this->assertEquals($expectedResult['survey_id'], $result->getSurveyId());
                }, [
                    'examples' => [
        //                Example 1: Create issue having question
                        [
                            $this->getIssueCreateDto(
                                array(
                                    'lang_id' => 1,
                                    'issue_name' => 'Test Anxiety',
                                    'survey_id' => 17,
                                    'issue_image' => 'large-report-icon-academic.png',
                                    'questions' => array(
                                        'id' => 5349,
                                        'type' => 'category',
                                        'text' => 'Degree, certificate, or licensure?',
                                        'options' => array(
                                            array(
                                                'id' => 21416,
                                                'text' => '(4) Moderately',
                                                'value' => 4
                                            )
                                        )
                                    )
                                )
                            ),
                            array(
                                'lang_id' => 1,
                                'issue_name' => 'Test Anxiety',
                                'survey_id' => 17,
                                'issue_image' => 'large-report-icon-academic.png',
                                'questions' => array(
                                    'id' => 5349,
                                    'type' => 'category',
                                    'text' => 'Degree, certificate, or licensure?',
                                    'options' => array(
                                        array(
                                            'id' => 21416,
                                            'text' => '(4) Moderately',
                                            'value' => 4
                                        )
                                    )
                                )
                            )
                        ],
        //                Example 2: Create issue having factors
                        [
                            $this->getIssueCreateDto(
                                array(
                                    'lang_id' => 1,
                                    'issue_name' => 'Low living environment',
                                    'survey_id' => 15,
                                    'issue_image' => 'large-report-icon-homesick.png',
                                    'factors' => array(
                                        'id' => 1,
                                        'text' => 'Commitment to the Institution',
                                        'range_min' => 0,
                                        'range_max' => 100
                                    )
                                )
                            ),
                            array(
                                'lang_id' => 1,
                                'issue_name' => 'Low living environment',
                                'survey_id' => 15,
                                'issue_image' => 'large-report-icon-homesick.png',
                                'factors' => array(
                                    'id' => 1,
                                    'text' => 'Commitment to the Institution',
                                    'range_min' => 0,
                                    'range_max' => 100
                                )
                            )
                        ]
                    ]
                ]);
            }



            public function testListIssues()
            {
                $this->specify("Test to list issue", function ($surveyId, $expectedResult) {
                    $mockLogger = $this->getMock('Logger', array(
                        'debug',
                        'error',
                        'info'
                    ));

                    $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                        'getRepository'
                    ));

                    $mockSurveyRepository = $this->getMock('SurveyRepository', array(
                        'find'
                    ));

                    $mockSurvey = $this->getMock('Survey');

                    $mockSurveyRepository->expects($this->any())
                        ->method('find')
                        ->will($this->returnValue($mockSurvey));

                    $mockIssueRepository = $this->getMock('Issue', array(
                        'getIssuesList'
                    ));

                    $mockIssueRepository->expects($this->any())
                        ->method('getIssuesList')
                        ->will($this->returnValue($this->getIssueList($surveyId)));

                    $mockRepositoryResolver->method('getRepository')
                        ->willReturnMap([
                            [
                                'SynapseCoreBundle:Survey',
                                $mockSurveyRepository
                            ],
                            [
                                'SynapseSurveyBundle:Issue',
                                $mockIssueRepository
                            ]
                        ]);

                    $mockContainer = $this->getMock('Container', array(
                        'get'
                    ));

                    $issueService = new IssueService($mockRepositoryResolver, $mockLogger, $mockContainer);
                    $result = $issueService->listIssues($surveyId);
                    $this->assertEquals($expectedResult['id'], $result[0]->getId());
                    $this->assertEquals($expectedResult['top_issue_name'], $result[0]->getTopIssueName());
                    $this->assertEquals($expectedResult['top_issue_image'], $result[0]->getTopIssueImage());
                }, [
                    'examples' => [
        //                Example 1: Test to list issues when non numeric value is given
                        [
                            'test',
                            array(
                                'id' => 10,
                                'top_issue_name' => 'Plan to study 5 hours or fewer a week',
                                'top_issue_image' => 'large-report-icon-courses2.png'
                            )
                        ],
        //                Example 2: Test to list issues when numeric value is given
                        [
                            10,
                            array(
                                'id' => 10,
                                'top_issue_name' => 'Plan to study 5 hours or fewer a week',
                                'top_issue_image' => 'large-report-icon-courses2.png'
                            )
                        ]
                    ]
                ]);
            }


            private function getIssueList()
            {
                $issueListArray = [
                    array(
                        'id' => 10,
                        'issue_name' => 'Plan to study 5 hours or fewer a week',
                        'issue_icon' => 'large-report-icon-courses2.png'
                    )
                ];
                return $issueListArray;
            }


            public function testEditIssue()
            {
                $this->specify("Test to edit issue", function ($issueCreateDto, $expectedResult) {
                    $mockLogger = $this->getMock('Logger', array(
                        'debug',
                        'error',
                        'info'
                    ));

                    $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                        'getRepository'
                    ));

                    $mockIssueRepository = $this->getMock('Issue', array(
                        'find',
                        'flush'
                    ));

                    $mockIssue = $this->getMock('Issue', array(
                        'find'
                    ));

                    $mockIssueRepository->expects($this->any())
                        ->method('find')
                        ->will($this->returnValue($this->getIssues()));

                    $mockSurveyRepository = $this->getMock('SurveyRepository', array(
                        'find'
                    ));

                    $mockSurveyRepository->expects($this->any())
                        ->method('find')
                        ->will($this->returnValue($this->getSurvey()));

                    $mockFactorRepository = $this->getMock('Factor', array(
                        'find'
                    ));

                    $mockFactor = $this->getMock('Factor', array(
                        'find'
                    ));

                    $mockFactorRepository->expects($this->any())
                        ->method('find')
                        ->will($this->returnValue($this->getFactor()));

                    $mockSurveyQuestionsRepository = $this->getMock('SurveyQuestions', array(
                        'find'
                    ));

                    $mockSurveyQuestionsRepository->expects($this->any())
                        ->method('find')
                        ->will($this->returnValue($this->getSurveyQuestions()));

                    $ebiQuestionOptionsRepository = $this->getMock('EbiQuestionOptions', array(
                        'find'
                    ));

                    $ebiQuestionOptions = $this->getMock('EbiQuestionOptions', array(
                        'find'
                    ));

                    $ebiQuestionOptionsRepository->expects($this->any())
                        ->method('find')
                        ->will($this->returnValue($ebiQuestionOptions));

                    $issueOptionsRepository = $this->getMock('IssueOptions', array(
                        'findOneBy',
                        'persist',
                        'flush'
                    ));

                    $issueOptions = $this->getMock('IssueOptions', array(
                        'findOneBy'
                    ));

                    $issueOptionsRepository->expects($this->any())
                        ->method('findOneBy')
                        ->will($this->returnValue($issueOptions));

                    $mockIssueLangRepository = $this->getMock('IssueLang', array(
                        'findOneBy',
                        'flush'
                    ));

                    $mockIssueLang = $this->getMock('IssueLang', array(
                        'findOneBy',
                        'setName',
                        'setLang'
                    ));

                    $mockIssueLangRepository->expects($this->any())
                        ->method('findOneBy')
                        ->will($this->returnValue($mockIssueLang));

                    $mockLanguageMasterRepository = $this->getMock('LanguageMaster', array(
                        'find'
                    ));

                    $mockLanguageMaster = $this->getMock('LanguageMaster', array(
                        'find'
                    ));

                    $mockLanguageMasterRepository->expects($this->any())
                        ->method('find')
                        ->will($this->returnValue($mockLanguageMaster));

                    $mockRepositoryResolver->method('getRepository')
                        ->willReturnMap([
                            [
                                'SynapseCoreBundle:Survey',
                                $mockSurveyRepository
                            ],
                            [
                                'SynapseSurveyBundle:SurveyQuestions',
                                $mockSurveyQuestionsRepository
                            ],
                            [
                                'SynapseSurveyBundle:Issue',
                                $mockIssueRepository
                            ],
                            [
                                'SynapseCoreBundle:LanguageMaster',
                                $mockLanguageMasterRepository
                            ],
                            [
                                'SynapseSurveyBundle:IssueLang',
                                $mockIssueLangRepository
                            ],
                            [
                                'SynapseSurveyBundle:Factor',
                                $mockFactorRepository
                            ],
                            [
                                'SynapseCoreBundle:EbiQuestionOptions',
                                $ebiQuestionOptionsRepository
                            ],
                            [
                                'SynapseSurveyBundle:IssueOptions',
                                $issueOptionsRepository
                            ]
                        ]);

                    $mockContainer = $this->getMock('Container', array(
                        'get'
                    ));

                    $issueService = new IssueService($mockRepositoryResolver, $mockLogger, $mockContainer);
                    $result = $issueService->editIssue($issueCreateDto);
                    $this->assertEquals($result->getLangId(), $expectedResult['lang_id']);
                    $this->assertEquals($result->getIssueName(), $expectedResult['issue_name']);
                    $this->assertEquals($result->getSurveyId(), $expectedResult['survey_id']);
                }, [
                    'examples' => [
        //              Example 1: Edit issue having questions
                        [
                            $this->getEditIssueDto(
                                array(
                                    'id' => 106,
                                    'lang_id' => 1,
                                    'issue_name' => 'Test Anxiety',
                                    'survey_id' => 17,
                                    'issue_image' => 'large-report-icon-academic.png',
                                    'questions' => array(
                                        'id' => 5349,
                                        'type' => 'category',
                                        'text' => 'Degree, certificate, or licensure?',
                                        'options' => array(
                                            array(
                                                'id' => 21416,
                                                'text' => '(4) Moderately',
                                                'value' => 4
                                            )
                                        )
                                    )
                                )
                            ),
                            array(
                                'id' => 106,
                                'lang_id' => 1,
                                'issue_name' => 'Test Anxiety',
                                'survey_id' => 17,
                                'issue_image' => 'large-report-icon-academic.png',
                                'questions' => array(
                                    'id' => 5349,
                                    'type' => 'category',
                                    'text' => 'Degree, certificate, or licensure?',
                                    'options' => array(
                                        array(
                                            'id' => 21416,
                                            'text' => '(4) Moderately',
                                            'value' => 4
                                        )
                                    )
                                )
                            )
                        ],
        //              Example 2: Edit issue having factors
                        [
                            $this->getEditIssueDto(
                                array(
                                    'id' => 100,
                                    'lang_id' => 1,
                                    'issue_name' => 'Low living environment',
                                    'survey_id' => 15,
                                    'issue_image' => 'large-report-icon-homesick.png',
                                    'factors' => array(
                                        'id' => 1,
                                        'text' => 'Commitment to the Institution',
                                        'range_min' => 0,
                                        'range_max' => 100
                                    )
                                )
                            ),
                            array(
                                'id' => 100,
                                'lang_id' => 1,
                                'issue_name' => 'Low living environment',
                                'survey_id' => 15,
                                'issue_image' => 'large-report-icon-homesick.png',
                                'factors' => array(
                                    'id' => 1,
                                    'text' => 'Commitment to the Institution',
                                    'range_min' => 0,
                                    'range_max' => 100
                                )
                            )
                        ]
                    ]
                ]);
            }




            public function testUploadIssueIcon()
            {
                $this->specify("Test upload issue icon", function ($issueId, $fileName, $expectedResult) {
                    $mockLogger = $this->getMock('Logger', array(
                        'debug',
                        'error',
                        'info'
                    ));

                    $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                        'getRepository'
                    ));

                    $mockIssueRepository = $this->getMock('Issue', array(
                        'find',
                        'update',
                        'setIcon'
                    ));

                    $mockIssue = $this->getMock('Issue', array(
                        'find',
                        'update',
                        'setIcon'
                    ));

                    if (is_numeric($issueId)) {
                        $mockIssueRepository->expects($this->any())
                            ->method('find')
                            ->will($this->returnValue($mockIssue));
                    } else {
                        $mockIssueRepository->expects($this->any())
                            ->method('find')
                            ->will($this->returnValue(null));
                    }

                    if ($issueId === -1) {
                        $mockIssueRepository->expects($this->any())
                            ->method('update')
                            ->will($this->returnValue(false));
                    } else {
                        $mockIssueRepository->expects($this->any())
                            ->method('update')
                            ->will($this->returnValue(true));
                    }


                    $mockRepositoryResolver->method('getRepository')
                        ->willReturnMap([
                            [
                                'SynapseSurveyBundle:Issue',
                                $mockIssueRepository
                            ]
                        ]);

                    $mockContainer = $this->getMock('Container', array(
                        'get'
                    ));

                    $issueService = new IssueService($mockRepositoryResolver, $mockLogger, $mockContainer);
                    try {
                        $result = $issueService->uploadIssueIcon($issueId, $fileName);
                        $this->assertEquals($expectedResult, $result);
                    } catch(\Exception $e) {
                        $this->assertEquals($expectedResult, $e->getMessage());
                    }

                }, [
                    'examples' => [
                        //Example 1: Test upload icon with icon filename
                        [
                            11,
                            'large-report-icon-homesick.png',
                            1
                        ],
                        //Example 2: Test upload icon
                        [
                            11,
                            '',
                            1
                        ],
                        //Example 3: Test upload icon with invalid issueId
                        [
                            null,
                            '',
                            'Invalid Issue Id'
                        ],
                            //Example 4: Test upload icon when update not successful
                        [
                            -1,
                            '',
                            0
                        ]
                    ]
                ]);
            }


            public function testGetIssue()
            {
                $this->specify("Test to get isue details", function ($issueId, $expectedResult) {
                    $mockLogger = $this->getMock('Logger', array(
                        'debug',
                        'error',
                        'info'
                    ));

                    $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                        'getRepository'
                    ));

                    $mockIssueLangRepository = $this->getMock('IssueLang', array(
                        'findOneBy'
                    ));


                    $mockIssueLangRepository->expects($this->any())
                        ->method('findOneBy')
                        ->will($this->returnValue($this->getIssueLang()));

                    $mockEbiQuestionsLangRepository = $this->getMock('EbiQuestionsLang', array(
                        'findOneBy'
                    ));

                    $mockEbiQuestionsLang = $this->getMock('EbiQuestionsLang', array(
                        'findOneBy'
                    ));

                    $mockEbiQuestionsLangRepository->expects($this->any())
                        ->method('findOneBy')
                        ->will($this->returnValue($mockEbiQuestionsLang));

                    $mockIssueOptionsRepository = $this->getMock('IssueOptions', array(
                        'findOneBy'
                    ));

                    $mockIssueOptions = $this->getMock('IssueOptions', array(
                        'findOneBy'
                    ));

                    $mockIssueOptionsRepository->expects($this->any())
                        ->method('findOneBy')
                        ->will($this->returnValue($mockIssueOptions));

                    $mockFactorLangRepository = $this->getMock('FactorLang', array(
                        'findOneBy'
                    ));

                    $mockFactorLang = $this->getMock('FactorLang', array(
                        'findOneBy'
                    ));

                    $mockFactorLangRepository->expects($this->any())
                        ->method('findOneBy')
                        ->will($this->returnValue($mockFactorLang));

                    $mockRepositoryResolver->method('getRepository')
                        ->willReturnMap([
                            [
                                'SynapseSurveyBundle:IssueLang',
                                $mockIssueLangRepository
                            ],
                            [
                                'SynapseCoreBundle:EbiQuestionsLang',
                                $mockEbiQuestionsLangRepository
                            ],
                            [
                                'SynapseSurveyBundle:IssueOptions',
                                $mockIssueOptionsRepository
                            ],
                            [
                                'SynapseSurveyBundle:FactorLang',
                                $mockFactorLangRepository
                            ]
                        ]);

                    $mockContainer = $this->getMock('Container', array(
                        'get'
                    ));

                    $issueService = new IssueService($mockRepositoryResolver, $mockLogger, $mockContainer);
                    $result = $issueService->getIssue($issueId);
                    $this->assertEquals($expectedResult['lang_id'], $result[0]->getLangId());
                    $this->assertEquals($expectedResult['issue_name'], $result[0]->getIssueName());
                }, [
                    'examples' => [
        //              Example 1: Test to get issue data
                        [
                            11,
                            array(
                                'id' => 30,
                                'lang_id' => 1,
                                'issue_name' => 'Missed 2 or more classes',
                                'survey_id' => 12,
                                'questions' => array(
                                    'id' => 505,
                                    'type' => 'category',
                                    'options' => array(),
                                    'text' => "Next term's tuition and fees?"
                                )
                            )
                        ],
        //              Example 1: Test to get issue data
                        [
                            12,
                            array(
                                'id' => 31,
                                'lang_id' => 1,
                                'issue_name' => 'Missed 2 or more classes',
                                'survey_id' => 13,
                                'questions' => array(
                                    'id' => 506,
                                    'type' => 'category',
                                    'options' => array(),
                                    'text' => "Next term's tuition and fees?"
                                )
                            )
                        ]
                    ]
                ]);
            }





            public function testSetIssueDetails()
            {
                $this->specify("Test SetIssueDetails", function ($issueId, $topIssue, $issueName, $participantCount,
                                                                 $percent, $icon, $currentPage, $recordsPerPage, $totalPages,
                                                                 $sortBy, $nonParticipantCount, $students, $expectedResult) {
                    $studentIssuesWithDetails = [];
                    $studentIssuesWithDetails['total_records'] = $participantCount;
                    $studentIssuesWithDetails['current_page'] = $currentPage;
                    $studentIssuesWithDetails['records_per_page'] = $recordsPerPage;
                    $studentIssuesWithDetails['total_pages'] = $totalPages;
                    $studentIssuesWithDetails['search_result'] = $students;

                    $totalStudents =  $participantCount + $nonParticipantCount;


                    $issueService = new IssueService($this->repositoryResolver, $this->logger, $this->container);
                    $result = $issueService->setIssueDetails($issueId, $topIssue, $issueName, $percent, $icon, $studentIssuesWithDetails, $sortBy, $nonParticipantCount, $totalStudents, $participantCount);
                    $this->assertEquals($expectedResult, $result);

                }, [
                    "examples" => [
                        // Example with empty student list
                        [
                            130,
                            1,
                            "Low social aspects (on-campus living)",
                            101,
                            50.2488,
                            "large-report-icon-homesick.png",
                            1,
                            25,
                            5,
                            "+student_last_name",
                            0,
                            [],
                            [
                                "issue_id" => 130,
                                "top_issue" => 1,
                                "name" => "Low social aspects (on-campus living)",
                                'non_participant_count_with_issue' => 0,
                                'participant_count_with_issue' => 101,
                                "total_student_population_available_for_issue" => 101,
                                "percentage" => 50.2488,
                                "image" => "large-report-icon-homesick.png",
                                "current_page" => 1,
                                "records_per_page" => 25,
                                "total_pages" => 5,
                                "sort_by" => "+student_last_name",
                                "students_with_issue_paginated_list" => []
                            ]
                        ],
                        // Example with student list
                        [
                            125,
                            2,
                            "Homesick (separation)",
                            194,
                            49.2386,
                            "large-report-icon-homesick.png",
                            1,
                            25,
                            8,
                            "+student_last_name",
                            0,
                            [
                                [
                                    "student_id" => 4962376,
                                    "student_first_name" => "Kailee",
                                    "student_last_name" => "Acosta",
                                    "external_id" => 4962376,
                                    "student_primary_email" => "MapworksBetaUser04962376@mailinator.com",
                                    "student_status" => 1,
                                    "student_risk_status" => "green",
                                    "student_risk_image_name" => "risk-level-icon-g.png",
                                    "student_intent_to_leave" => "green",
                                    "student_intent_to_leave_image_name" => "leave-intent-stay-stated.png",
                                    "student_classlevel" => "1st Year/Freshman",
                                    "student_logins" => "1",
                                    "last_activity_date" => "2017/08/29",
                                    "last_activity" => "Appointment"
                                ],
                                [
                                    "student_id" => 4962407,
                                    "student_first_name" => "Diamond",
                                    "student_last_name" => "Adkins",
                                    "external_id" => 4962407,
                                    "student_primary_email" => "MapworksBetaUser04962407@mailinator.com",
                                    "student_status" => 1,
                                    "student_risk_status" => "green",
                                    "student_risk_image_name" => "risk-level-icon-g.png",
                                    "student_intent_to_leave" => "green",
                                    "student_intent_to_leave_image_name" => "leave-intent-stay-stated.png",
                                    "student_classlevel" => "Sophomore",
                                    "student_logins" => "1",
                                    "last_activity_date" => "2016/09/15",
                                    "last_activity" => "Note"
                                ]
                            ],
                            [
                                "issue_id" => 125,
                                "top_issue" => 2,
                                "name" => "Homesick (separation)",
                                "non_participant_count_with_issue" => 0,
                                'participant_count_with_issue' => 194,
                                "total_student_population_available_for_issue" => 194,
                                "percentage" => 49.2386,
                                "image" => "large-report-icon-homesick.png",
                                "current_page" => 1,
                                "records_per_page" => 25,
                                "total_pages" => 8,
                                "sort_by" => "+student_last_name",
                                "students_with_issue_paginated_list" => [
                                    [
                                        "student_id" => 4962376,
                                        "student_first_name" => "Kailee",
                                        "student_last_name" => "Acosta",
                                        "external_id" => 4962376,
                                        "student_primary_email" => "MapworksBetaUser04962376@mailinator.com",
                                        "student_status" => 1,
                                        "student_risk_status" => "green",
                                        "student_risk_image_name" => "risk-level-icon-g.png",
                                        "student_intent_to_leave" => "green",
                                        "student_intent_to_leave_image_name" => "leave-intent-stay-stated.png",
                                        "student_classlevel" => "1st Year/Freshman",
                                        "student_logins" => "1",
                                        "last_activity_date" => "2017/08/29",
                                        "last_activity" => "Appointment"
                                    ],
                                    [
                                        "student_id" => 4962407,
                                        "student_first_name" => "Diamond",
                                        "student_last_name" => "Adkins",
                                        "external_id" => 4962407,
                                        "student_primary_email" => "MapworksBetaUser04962407@mailinator.com",
                                        "student_status" => 1,
                                        "student_risk_status" => "green",
                                        "student_risk_image_name" => "risk-level-icon-g.png",
                                        "student_intent_to_leave" => "green",
                                        "student_intent_to_leave_image_name" => "leave-intent-stay-stated.png",
                                        "student_classlevel" => "Sophomore",
                                        "student_logins" => "1",
                                        "last_activity_date" => "2016/09/15",
                                        "last_activity" => "Note"
                                    ]
                                ]
                            ]
                        ]
                    ],
                ]);
            }


            public function testSetTopIssuesDTOProperties()
            {
                $this->specify("Test SetTopIssuesDTOProperties", function ($topIssuesDTO, $totalStudentsFromTopIssue, $firstName, $lastName, $yearName, $surveyId, $surveyName, $cohort, $hasStudentPopulationOrIssuesChanged, $totalIssuesCount, $totalNonParticipantCount, $topIssuesArray, $expectedResult) {



                    $issueService = new IssueService($this->repositoryResolver, $this->logger, $this->container);
                    $result = $issueService->setTopIssuesDTOProperties($topIssuesDTO, $totalStudentsFromTopIssue, $this->facultyId, $firstName, $lastName, $yearName, $surveyId, $surveyName, $cohort, $hasStudentPopulationOrIssuesChanged, $totalIssuesCount, $topIssuesArray, $totalNonParticipantCount);
                    $this->assertEquals($expectedResult, $result);

                }, [
                    "examples" => [
                        //Comparing the returned DTO with expected result
                        [
                            new TopIssuesDTO(), //Empty TopIssuesDTO
                            9,
                            "Pulkit",
                            "Faculty",
                            "201617",
                            15,
                            "Transition One",
                            2,
                            1,
                            3,
                            0,
                            ['FAKE' => 'fake'],
                            $this->getTopIssuesDTO()
                    ]
                ]]);
            }


    public function testGetStudentsList()
    {
        $this->specify("Test GetStudentsList", function ($issueArray, $studentPopulation, $expectedResult) {
            $mockIssueDao = $this->getMock("IssueDAO", ["getStudentListFromStudentIssues", "getDistinctParticipantStudentPopulationCount"]);
            $mockStudentListService = $this->getMock("StudentListService", ["getStudentListWithMetadata"]);
            $issuesInputDTOData = $this->getIssuesInputDTO(true, $studentPopulation);
            $topIssueList = $this->generateFakeTopIssue($issueArray);

            $participantStudentsWithIssue = [];

            for ($issueCount = 0; $issueCount < count($topIssueList); $issueCount++) {
                $numberOfStudentsWithIssue = $topIssueList[$issueCount]['numerator'];
                $recordsPerPage = 25;
                $studentIds = $this->generateFakeStudentIds($numberOfStudentsWithIssue);
                $studentRecords = $this->generateFakeStudentList($recordsPerPage, $numberOfStudentsWithIssue);
                $mockIssueDao->expects($this->at($issueCount))->method("getStudentListFromStudentIssues")->willReturn($studentIds);
                $mockStudentListService->expects($this->at($issueCount))->method("getStudentListWithMetadata")->willReturn($studentRecords);
                $participantStudentsWithIssue[$topIssueList[$issueCount]['issue_id']] = $numberOfStudentsWithIssue;
            }

            $this->container->method('get')->willReturnMap([
                [
                    IssueDAO::DAO_KEY,
                    $mockIssueDao
                ],
                [
                    StudentListService::SERVICE_KEY,
                    $mockStudentListService
                ]
            ]);

            $issueService = new IssueService($this->repositoryResolver, $this->logger, $this->container);
            $result = $issueService->getStudentsList($issuesInputDTOData, $topIssueList, $this->facultyId, $this->organizationId, $participantStudentsWithIssue);
            $this->assertEquals($expectedResult, $result);

        }, [
            "examples" =>
                [
                    //Normal Set of 5
                    [
                        [
                            ['issue_id' => 125,
                                'issue_name' => 'Homesick (separation)',
                                'numerator' => 1984,
                                'denominator' => 1984
                            ],
                            ['issue_id' => 100,
                                'issue_name' => 'Struggling in at least 2 courses',
                                'numerator' => 795,
                                'denominator' => 1976
                            ],
                            ['issue_id' => 134,
                                'issue_name' => 'Test Anxiety',
                                'numerator' => 790,
                                'denominator' => 1978
                            ],
                            ['issue_id' => 130,
                                'issue_name' => 'Low social aspects (on-campus living)',
                                'numerator' => 414,
                                'denominator' => 1900
                            ],
                            ['issue_id' => 126,
                                'issue_name' => 'Homesick (distressed)',
                                'numerator' => 421,
                                'denominator' => 1982
                            ]

                        ],
                        2000,
                        [
                            [
                                'issue_id' => '125',
                                'top_issue' => 1,
                                'name' => 'Homesick (separation)',
                                'non_participant_count_with_issue' => 0,
                                'participant_count_with_issue' => 1984,
                                'total_student_population_available_for_issue' => 1984,
                                'percentage' => '100.0',
                                'image' => 'anyGivenIcon.png',
                                'current_page' => 1,
                                'records_per_page' => 25,
                                'total_pages' => 80,
                                'sort_by' => '+student_last_name',
                                'students_with_issue_paginated_list' =>
                                    $this->generateFakeStudentRecords(25)
                            ],

                            [
                                'issue_id' => '100',
                                'top_issue' => 2,
                                'name' => 'Struggling in at least 2 courses',
                                'non_participant_count_with_issue' => 0,
                                'participant_count_with_issue' => 795,
                                'total_student_population_available_for_issue' => 1976,
                                'percentage' => '40.2',
                                'image' => 'anyGivenIcon.png',
                                'current_page' => 1,
                                'records_per_page' => 25,
                                'total_pages' => 32,
                                'sort_by' => '+student_last_name',
                                'students_with_issue_paginated_list' =>
                                    $this->generateFakeStudentRecords(25)
                            ],

                            [
                                'issue_id' => '134',
                                'top_issue' => 3,
                                'name' => 'Test Anxiety',
                                'non_participant_count_with_issue' => 0,
                                'participant_count_with_issue' => 790,
                                'total_student_population_available_for_issue' => 1978,
                                'percentage' => '39.9',
                                'image' => 'anyGivenIcon.png',
                                'current_page' => 1,
                                'records_per_page' => 25,
                                'total_pages' => 32,
                                'sort_by' => '+student_last_name',
                                'students_with_issue_paginated_list' =>
                                    $this->generateFakeStudentRecords(25)
                            ],
                            [
                                'issue_id' => '130',
                                'top_issue' => 4,
                                'name' => 'Low social aspects (on-campus living)',
                                'non_participant_count_with_issue' => 0,
                                'participant_count_with_issue' => 414,
                                'total_student_population_available_for_issue' => 1900,
                                'percentage' => '21.8',
                                'image' => 'anyGivenIcon.png',
                                'current_page' => 1,
                                'records_per_page' => 25,
                                'total_pages' => 17,
                                'sort_by' => '+student_first_name',
                                'students_with_issue_paginated_list' =>
                                    $this->generateFakeStudentRecords(25)
                            ],
                            [
                                'issue_id' => '126',
                                'top_issue' => 5,
                                'name' => 'Homesick (distressed)',
                                'non_participant_count_with_issue' => 0,
                                'participant_count_with_issue' => 421,
                                'total_student_population_available_for_issue' => 1982,
                                'percentage' => '21.2',
                                'image' => 'anyGivenIcon.png',
                                'current_page' => 1,
                                'records_per_page' => 25,
                                'total_pages' => 17,
                                'sort_by' => '+student_last_name',
                                'students_with_issue_paginated_list' =>
                                    $this->generateFakeStudentRecords(25)
                            ]
                        ] //Expected output
                    ],
                    //Only One
                    [
                        // IssuesInputDTO
                        [
                            ['issue_id' => 125,
                                'issue_name' => 'Homesick (separation)',
                                'numerator' => 1984,
                                'denominator' => 1984
                            ]
                        ],
                        2000,
                        [
                            [
                                'issue_id' => "125",
                                'top_issue' => 1,
                                'name' => 'Homesick (separation)',
                                'non_participant_count_with_issue' => 0,
                                'participant_count_with_issue' => 1984,
                                'total_student_population_available_for_issue' => 1984,
                                'percentage' => '100.0',
                                'image' => 'anyGivenIcon.png',
                                'current_page' => 1,
                                'records_per_page' => 25,
                                'total_pages' => 80,
                                'sort_by' => '+student_last_name',
                                'students_with_issue_paginated_list' =>
                                    $this->generateFakeStudentRecords(25)
                            ]
                        ]
                    ],
                    [
                        // No Top 5 Issues For Students
                        [],
                        2000,
                        []
                    ],
                ]
        ]);

    }

    private function getIssueLang()
    {
        $issueLang = new IssueLang();
        $issueLang->setIssue($this->getIssues());
        $issueLang->setLang($this->getLanguage());
        $issueLang->setName('Missed 2 or more classes');
        return $issueLang;
    }



    private function getIssuesInputDTO($rootCall, $totalStudentPopulation, $numberOfTopIssues = 5)
    {
        $issuesInputDTO = new IssuesInputDTO();
        $issuesInputDTO->setRootCall($rootCall);
        $issuesInputDTO->setTotalStudentPopulation($totalStudentPopulation);
        $issuesInputDTO->setOrgAcademicYearId($this->academicYearId);
        $issuesInputDTO->setCohort($this->cohort);
        $issuesInputDTO->setSurveyId($this->surveyId);
        $issuesInputDTO->setNumberOfTopIssues($numberOfTopIssues);
        $dataArray = $this->getTopIssuesPaginationData();
        if ($numberOfTopIssues < 5) {
            $dataArray = array_slice($dataArray, 0, $numberOfTopIssues);
        }
        $issuePaginationDTOArray = [];
        foreach ($dataArray as $item) {
            $issuePaginationDTO = new IssuePaginationDTO();
            $issuePaginationDTO->setTopIssue($item['top_issue']);
            $issuePaginationDTO->setCurrentPage($item['current_page']);
            $issuePaginationDTO->setRecordsPerPage($item['records_per_page']);
            $issuePaginationDTO->setSortBy($item['sort_by']);
            $issuePaginationDTO->setParticipantCountWithIssue($item['participant_count_with_issue']);
            $issuePaginationDTO->setIssueId($item['issue_id']);
            $issuePaginationDTO->setDisplayStudents($item['display_students']);
            $issuePaginationDTOArray[] = $issuePaginationDTO;
        }
        $issuesInputDTO->setTopIssuesPagination($issuePaginationDTOArray);
        return $issuesInputDTO;
    }


    private function getTopIssuesDTO()
    {
        $topIssueArray = ['FAKE' => 'fake'];

        $currentDateTime = new \DateTime('now'); //Not setting in __before(), so we can use 'now' as a parameter
        $dateTime = new \DateTime($currentDateTime->format(SynapseConstant::DATE_FORMAT_WITH_TIMEZONE));
        $topIssuesDTO = new TopIssuesDTO();
        $topIssuesDTO->setTotalStudents(9);
        $topIssuesDTO->setFacultyId($this->facultyId);
        $topIssuesDTO->setFacultyFirstname("Pulkit");
        $topIssuesDTO->setFacultyLastname("Faculty");
        $topIssuesDTO->setCurrentDatetime($dateTime);
        $topIssuesDTO->setYear($currentDateTime->format('Y'));
        $topIssuesDTO->setAcademicYearName("201617");
        $topIssuesDTO->setSurveyId(15);
        $topIssuesDTO->setSurveyName("Transition One");
        $topIssuesDTO->setCohort(2);
        $topIssuesDTO->setStudentPopulationChange(1);
        $topIssuesDTO->setIssueCount(3);
        $topIssuesDTO->setTopIssues($topIssueArray);
        $topIssuesDTO->setTotalNonParticipantCount(0);
        return $topIssuesDTO;
    }


    private function generateFakeStudentIds($countOfStudents)
    {
        $fakeStudentIds = range(1, $countOfStudents);
        return $fakeStudentIds;
    }


    private function generateFakeStudentRecords($numberOfStudentRecords)
    {
        $studentRecord = [];
        for ($i = 0; $i < $numberOfStudentRecords; $i++) {
            $studentRecord[] =
                [
                    'student_id' => $i,
                    'student_first_name' => 'Maya' . $i,
                    'student_last_name' => 'Abbott' . $i,
                    'external_id' => $i,
                    'student_primary_email' => 'MapworksBetaUser04957587@mailinator.com',
                    'student_status' => '1',
                    'student_risk_status' => 'green',
                    'student_risk_image_name' => 'risk-level-icon-g.png',
                    'student_intent_to_leave' => 'green',
                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                    'student_classlevel' => '1st Year/Freshman',
                    'student_logins' => '0',
                    'last_activity_date' => NULL,
                    'last_activity' => NULL,
                ];
        }
        return $studentRecord;
    }


    private function generateFakeStudentList($numberOfStudentRecords, $totalStudentIds)
    {
        $studentRecords = [];
        $studentRecords['person_id'] = $this->personObject->getId();
        $studentRecords['total_records'] = $totalStudentIds;
        $studentRecords['records_per_page'] = $numberOfStudentRecords;
        $studentRecords['total_pages'] = ceil($totalStudentIds / $numberOfStudentRecords);
        $studentRecords['current_page'] = 1;
        $studentRecords['search_result'] = $this->generateFakeStudentRecords($numberOfStudentRecords);
        return $studentRecords;
    }


    private function generateFakeTopIssue($issueArray)
    {
        $topIssue = [];
        foreach ($issueArray as $issue) {
            $issueId = $issue['issue_id'];
            $issueName = $issue['issue_name'];
            $numerator = $issue['numerator'];
            $denominator = $issue['denominator'];

            $percent = round(($numerator / $denominator) * 100, 1);
            $topIssue[] = [
                'issue_id' => $issueId,
                'issue_name' => $issueName,
                'numerator' => $numerator,
                'denominator' => $denominator,
                'percent' => $percent,
                'icon' => 'anyGivenIcon.png',
            ];
        }

        return $topIssue;

    }


    private function getEditIssueDto($editIssueData)
    {
        $editIssueCreateDto = new IssueCreateDto();
        $editIssueCreateDto->setId($editIssueData['id']);
        $editIssueCreateDto->setLangId($editIssueData['lang_id']);
        $editIssueCreateDto->setIssueName($editIssueData['issue_name']);
        $editIssueCreateDto->setSurveyId($editIssueData['survey_id']);
        $editIssueCreateDto->setIssueImage($editIssueData['issue_image']);
        if (isset($editIssueData['questions'])) {
            $editIssueCreateDto->setQuestions($this->getIssueCreateQuestion($editIssueData['questions']));
        } else if ($editIssueData['factors']) {
            $editIssueCreateDto->setFactors($this->getIssueCreateFactor($editIssueData['factors']));
        }
        return $editIssueCreateDto;
    }


    private function getIssueCreateFactor($factorData)
    {
        $factorDto = new FactorsArrayDto();
        $factorDto->setId($factorData['id']);
        $factorDto->setFactorName($factorData['text']);
        $factorDto->setRangeMin($factorData['range_min']);
        $factorDto->setRangeMax($factorData['range_max']);
        return $factorDto;
    }


    private function getFactor()
    {
        $factor = new  Factor();
        return $factor;
    }


    private function getTopIssuesPaginationData()
    {
        return [
            [
                "top_issue" => 1,
                "current_page" => 1,
                "records_per_page" => 25,
                "sort_by" => "+student_last_name",
                "participant_count_with_issue" => 299,
                "issue_id" => 3,
                "display_students" => true
            ],
            [
                "top_issue" => 2,
                "current_page" => 2,
                "records_per_page" => 50,
                "sort_by" => "+student_last_name",
                "participant_count_with_issue" => 100,
                "issue_id" => 20,
                "display_students" => true
            ],
            [
                "top_issue" => 3,
                "current_page" => 5,
                "records_per_page" => 25,
                "sort_by" => "+student_last_name",
                "participant_count_with_issue" => 50,
                "issue_id" => 12,
                "display_students" => true
            ],
            [
                "top_issue" => 4,
                "current_page" => 1,
                "records_per_page" => 25,
                "sort_by" => "+student_first_name",
                "participant_count_with_issue" => 10,
                "issue_id" => 7,
                "display_students" => true
            ],
            [
                "top_issue" => 5,
                "current_page" => 1,
                "records_per_page" => 25,
                "sort_by" => "+student_last_name",
                "participant_count_with_issue" => 5,
                "issue_id" => 10,
                "display_students" => true
            ]
        ];
    }



    private function getSurvey()
    {
        $survey = new Survey();
        return $survey;
    }


    private function getSurveyQuestions()
    {
        $surveyQuestion = new SurveyQuestions();
        return $surveyQuestion;
    }


    private function getIssues()
    {
        $issue = new Issue();
        $issue->setSurvey($this->getSurvey());
        return $issue;
    }


    private function getLanguage()
    {
        $languageMaster = new LanguageMaster();
        $languageMaster->setId(1);
        return $languageMaster;
    }


    private function getIssueCreateDto($issueCreateData)
    {
        $issueCreateDto = new IssueCreateDto();
        $issueCreateDto->setLangId($issueCreateData['lang_id']);
        $issueCreateDto->setIssueName($issueCreateData['issue_name']);
        $issueCreateDto->setSurveyId($issueCreateData['survey_id']);
        $issueCreateDto->setIssueImage($issueCreateData['issue_image']);
        if (isset($issueCreateData['questions'])) {
            $issueCreateDto->setQuestions($this->getIssueCreateQuestion($issueCreateData['questions']));
        } elseif (isset($issueCreateData['factors'])) {
            $issueCreateDto->setFactors($this->getIssueCreateFactor($issueCreateData['factors']));
        }
        return $issueCreateDto;
    }


    private function getIssueCreateQuestion($issueCreateQuestionData)
    {
        $issueCreateQuestionsDto = new IssueCreateQuestionsDto();
        $issueCreateQuestionsDto->setId($issueCreateQuestionData['id']);
        $issueCreateQuestionsDto->setType($issueCreateQuestionData['type']);
        $issueCreateQuestionsDto->setText($issueCreateQuestionData['text']);
        $issueCreateQuestionsDto->setOptions($this->getIssueCreateQuestionOptions($issueCreateQuestionData['options'][0]));
        return $issueCreateQuestionsDto;
    }


    private function getIssueCreateQuestionOptions($isueCreateQuestionOptionData)
    {
        $issueCreateQuestionOptions = new IssueCreateQuesOptionsDto();
        $issueCreateQuestionOptions->setId($isueCreateQuestionOptionData['id']);
        $issueCreateQuestionOptions->setText($isueCreateQuestionOptionData['text']);
        $issueCreateQuestionOptions->setValue($isueCreateQuestionOptionData['text']);
        return $issueCreateQuestionOptions;
    }
}