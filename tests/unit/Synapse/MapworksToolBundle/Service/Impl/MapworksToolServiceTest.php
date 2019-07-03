<?php

namespace Synapse\MapworksToolBundle\Service\Impl;

use Codeception\Specify;
use Codeception\Test\Unit;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;
use Synapse\CoreBundle\Service\Utility\URLUtilityService;
use Synapse\MapworksToolBundle\Entity\MapworksTool;
use Synapse\MapworksToolBundle\Entity\MapworksToolLastRun;
use Synapse\MapworksToolBundle\Entity\OrgPermissionsetTool;
use Synapse\MapworksToolBundle\EntityDto\IssuePaginationDTO;
use Synapse\MapworksToolBundle\EntityDto\IssuesInputDTO;
use Synapse\MapworksToolBundle\EntityDto\ToolAnalysisDTO;
use Synapse\MapworksToolBundle\EntityDto\TopIssuesDTO;
use Synapse\MapworksToolBundle\Repository\MapworksToolLastRunRepository;
use Synapse\MapworksToolBundle\Repository\MapworksToolRepository;
use Synapse\MapworksToolBundle\Repository\OrgPermissionsetToolRepository;
use Synapse\PdfBundle\Service\Impl\PdfDetailsService;
use Synapse\SurveyBundle\Repository\WessLinkRepository;
use Synapse\SurveyBundle\Service\Impl\IssueService;


class MapworksToolServiceTest extends Unit
{
    use Specify;

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
     * @var \DateTime
     */
    private $dateTime;

    /**
     * @var int
     */
    private $facultyId = 5049081;

    /**
     * @var int
     */
    private $fetchCount = 10;

    /**
     * @var MapworksToolLastRun
     */
    private $mapWorksLastRunObject;

    /**
     * @var MapworksTool
     */
    private $mapworksToolObject;

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
    private $totalStudentIssuesCount = 1431;

    public function _before()
    {
        $this->container = $this->getMock('container', ['get']);
        $this->logger = $this->getMock('logger', ['debug', 'error']);
        $this->repositoryResolver = $this->getMock('repositoryResolver', ['getRepository']);

        $this->dateTime = new \DateTime('2017-09-25 00:00:00');
        // create mapworks tool object
        $this->mapworksToolObject = new MapworksTool();
        $this->mapworksToolObject->setId(1);
        $this->mapworksToolObject->setToolName("Top Issues");
        // create person object
        $this->personObject = new Person();
        $this->personObject->setId($this->facultyId);
        // create mapworks tool last run object
        $this->mapWorksLastRunObject = new MapworksToolLastRun();
        $this->mapWorksLastRunObject->setPersonId($this->personObject);
        $this->mapWorksLastRunObject->setToolId($this->mapworksToolObject);
        $this->mapWorksLastRunObject->setLastRun($this->dateTime);
    }

    public function testCreateTopIssuesCSVJob()
    {
        $this->specify("Test createTopIssuesCSVJob", function ($expectedResult) {
            $mockResque = $this->getMock("resque", ["enqueue"]);
            $this->container->method("get")->willReturnMap([
                [
                    "bcc_resque.resque",
                    $mockResque
                ]
            ]);
            $mapWorksToolService = new MapworksToolService($this->repositoryResolver, $this->logger, $this->container);
            $issuesInputDTO = new IssuesInputDTO();
            $result = $mapWorksToolService->createTopIssuesCSVJob($this->organizationId, $this->facultyId, $issuesInputDTO);
            $this->assertEquals($expectedResult, $result);
        }, [
            "examples" => [
                // example matches the notification when csv job scheduled
                [
                    ["You may continue to use Mapworks while your download completes. We will notify you when it is available."] //expected notifications
                ]
            ]
        ]);

    }

    public function testGetMapworksToolTopIssues()
    {
        $this->specify("Test method getMapworksToolTopIssues", function ($orgAcademicYearId, $surveyId, $cohort, $topIssuesPagination, $expectedResults) {

            $mockMapworksToolRepository = $this->getMock("MapworksToolRepository", ["find", "findOneBy"]);
            $mockMapworksToolRepository->method("findOneBy")->willReturn($this->mapworksToolObject);
            $mockMapworksToolRepository->method("find")->willReturn($this->mapworksToolObject);

            $mockPersonRepository = $this->getMock("PersonRepository", ["find"]);
            $mockPersonRepository->method("find")->willReturn($this->personObject);

            $mockMapworksToolLastRunRepository = $this->getMock("MapworksToolLastRunRepository", ["findOneBy", "persist"]);

            $mockMapworksToolLastRunRepository->method("findOneBy")->willReturn($this->mapWorksLastRunObject);
            $mockMapworksToolLastRunRepository->method("persist")->willReturn(true);

            $mockIssueService = $this->getMock("IssueService", ["getTopIssuesWithStudentList"]);
            $mockIssueService->method("getTopIssuesWithStudentList")->willReturn($this->getTopIssuesWithStudentList($cohort));

            $this->repositoryResolver->method('getRepository')->willReturnMap([
                [
                    MapworksToolRepository::REPOSITORY_KEY,
                    $mockMapworksToolRepository
                ],
                [
                    PersonRepository::REPOSITORY_KEY,
                    $mockPersonRepository
                ],
                [
                    MapworksToolLastRunRepository::REPOSITORY_KEY,
                    $mockMapworksToolLastRunRepository
                ]
            ]);
            $this->container->method('get')->willReturnMap([
                [
                    IssueService::SERVICE_KEY,
                    $mockIssueService,
                ]
            ]);
            $mapWorksToolService = new MapworksToolService($this->repositoryResolver, $this->logger, $this->container);
            $issuesInputDTO = new IssuesInputDTO();
            $issuesInputDTO->setOrgAcademicYearId($orgAcademicYearId);
            $issuesInputDTO->setCohort($cohort);
            $issuesInputDTO->setSurveyId($surveyId);
            $issuesInputDTO->setNumberOfTopIssues($this->fetchCount);
            $issuesInputDTO->setTopIssuesPagination($topIssuesPagination);
            $result = $mapWorksToolService->getMapworksToolTopIssues($this->organizationId, $this->facultyId, $issuesInputDTO);
            $this->assertInstanceOf(TopIssuesDTO::class, $result);
            $this->assertEquals($result->getTopIssues(), $expectedResults);
        }, [
            "examples" => [
                // compare the results of GetMapworksToolTopIssues service method with provided parameters
                [
                    192, // orgAcademicYearId
                    15, // surveyId
                    1, // cohort
                    $this->getTopIssuesPaginationArray(), // top issues pagination array
                    $this->getTopIssuesStudentsListData() //expected Student Issues List
                ]
            ]
        ]);
    }

    private function getTopIssuesPaginationArray()
    {
        $dataArray = [
            [
                "top_issue" => 1,
                "current_page" => 1,
                "records_per_page" => 25,
                "sort_by" => "+student_last_name",
                "display_students" => true
            ],
            [
                "top_issue" => 2,
                "current_page" => 2,
                "records_per_page" => 50,
                "sort_by" => "+student_last_name",
                "display_students" => true
            ],
            [
                "top_issue" => 3,
                "current_page" => 5,
                "records_per_page" => 25,
                "sort_by" => "+student_last_name",
                "display_students" => true
            ],
            [
                "top_issue" => 4,
                "current_page" => 1,
                "records_per_page" => 25,
                "sort_by" => "+student_first_name",
                "display_students" => true
            ],
            [
                "top_issue" => 5,
                "current_page" => 1,
                "records_per_page" => 25,
                "sort_by" => "+student_last_name",
                "display_students" => true
            ]
        ];
        $issuePaginationDTOArray = [];
        foreach ($dataArray as $item) {
            $issuePaginationDTO = new IssuePaginationDTO();
            $issuePaginationDTO->setTopIssue($item['top_issue']);
            $issuePaginationDTO->setCurrentPage($item['current_page']);
            $issuePaginationDTO->setSortBy($item['sort_by']);
            $issuePaginationDTO->setDisplayStudents($item['display_students']);
            $issuePaginationDTOArray[] = $issuePaginationDTO;
        }
        return $issuePaginationDTOArray;
    }

    private function getTopIssuesWithStudentList($cohort)
    {
        $topIssuesDTO = new TopIssuesDTO();
        $topIssuesDTO->setAcademicYearName("2016-2017");
        $topIssuesDTO->setCohort($cohort);
        $topIssuesDTO->setCurrentDatetime($this->dateTime);
        $topIssuesDTO->setTotalStudents($this->totalStudentIssuesCount);
        $topIssuesDTO->setTopIssues($this->getTopIssuesStudentsListData());
        return $topIssuesDTO;
    }

    private function getTopIssuesStudentsListData()
    {
        // there are many more results but lets keep it smaller set
        return [
            [
                'student_id' => '4958587',
                'student_first_name' => 'Jace',
                'student_last_name' => 'Abbott',
                'external_id' => '4958587',
                'student_primary_email' => 'MapworksBetaUser04958587@mailinator.com',
                'student_status' => '1',
                'student_risk_status' => 'green',
                'student_risk_image_name' => 'risk-level-icon-g.png',
                'student_intent_to_leave' => 'green',
                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                'student_classlevel' => '1st Year/Freshman',
                'student_logins' => '6',
                'last_activity_date' => '2017/08/31',
                'last_activity' => 'Email',
            ],
            [
                'student_id' => '4957587',
                'student_first_name' => 'Maya',
                'student_last_name' => 'Abbott',
                'external_id' => '4957587',
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
            ],
            [
                'student_id' => '4954823',
                'student_first_name' => 'Alexis',
                'student_last_name' => 'Acevedo',
                'external_id' => '4954823',
                'student_primary_email' => 'MapworksBetaUser04954823@mailinator.com',
                'student_status' => '1',
                'student_risk_status' => 'red',
                'student_risk_image_name' => 'risk-level-icon-r1.png',
                'student_intent_to_leave' => 'green',
                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                'student_classlevel' => '1st Year/Freshman',
                'student_logins' => '1',
                'last_activity_date' => '2017/08/09',
                'last_activity' => 'Contact',
            ],
            [
                'student_id' => '4952823',
                'student_first_name' => 'Johnathan',
                'student_last_name' => 'Acevedo',
                'external_id' => '4952823',
                'student_primary_email' => 'MapworksBetaUser04952823@mailinator.com',
                'student_status' => '1',
                'student_risk_status' => 'green',
                'student_risk_image_name' => 'risk-level-icon-g.png',
                'student_intent_to_leave' => 'green',
                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                'student_classlevel' => '1st Year/Freshman',
                'student_logins' => '1',
                'last_activity_date' => '2017/08/29',
                'last_activity' => 'Appointment',
            ],
            [
                'student_id' => '4957038',
                'student_first_name' => 'Arturo',
                'student_last_name' => 'Adams',
                'external_id' => '4957038',
                'student_primary_email' => 'MapworksBetaUser04957038@mailinator.com',
                'student_status' => '1',
                'student_risk_status' => 'green',
                'student_risk_image_name' => 'risk-level-icon-g.png',
                'student_intent_to_leave' => 'green',
                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                'student_classlevel' => '1st Year/Freshman',
                'student_logins' => '0',
                'last_activity_date' => NULL,
                'last_activity' => NULL,
            ],
            [
                'student_id' => '4953038',
                'student_first_name' => 'Killian',
                'student_last_name' => 'Adams',
                'external_id' => '4953038',
                'student_primary_email' => 'MapworksBetaUser04953038@mailinator.com',
                'student_status' => '1',
                'student_risk_status' => 'green',
                'student_risk_image_name' => 'risk-level-icon-g.png',
                'student_intent_to_leave' => 'green',
                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                'student_classlevel' => 'Sophomore',
                'student_logins' => '1',
                'last_activity_date' => '2016/09/15',
                'last_activity' => 'Note',
            ],
            [
                'student_id' => '4956038',
                'student_first_name' => 'Madalyn',
                'student_last_name' => 'Adams',
                'external_id' => '4956038',
                'student_primary_email' => 'MapworksBetaUser04956038@mailinator.com',
                'student_status' => '1',
                'student_risk_status' => 'red',
                'student_risk_image_name' => 'risk-level-icon-r1.png',
                'student_intent_to_leave' => 'green',
                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                'student_classlevel' => '1st Year/Freshman',
                'student_logins' => '1',
                'last_activity_date' => '2017/08/09',
                'last_activity' => 'Contact',
            ],
            [
                'student_id' => '4957407',
                'student_first_name' => 'Santana',
                'student_last_name' => 'Adkins',
                'external_id' => '4957407',
                'student_primary_email' => 'MapworksBetaUser04957407@mailinator.com',
                'student_status' => '1',
                'student_risk_status' => 'green',
                'student_risk_image_name' => 'risk-level-icon-g.png',
                'student_intent_to_leave' => 'green',
                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                'student_classlevel' => '1st Year/Freshman',
                'student_logins' => '0',
                'last_activity_date' => NULL,
                'last_activity' => NULL,
            ],
            [
                'student_id' => '4956212',
                'student_first_name' => 'Chaya',
                'student_last_name' => 'Aguilar',
                'external_id' => '4956212',
                'student_primary_email' => 'MapworksBetaUser04956212@mailinator.com',
                'student_status' => '1',
                'student_risk_status' => 'yellow',
                'student_risk_image_name' => 'risk-level-icon-y.png',
                'student_intent_to_leave' => 'green',
                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                'student_classlevel' => '1st Year/Freshman',
                'student_logins' => '0',
                'last_activity_date' => NULL,
                'last_activity' => NULL,
            ],
            [
                'student_id' => '4954212',
                'student_first_name' => 'Kora',
                'student_last_name' => 'Aguilar',
                'external_id' => '4954212',
                'student_primary_email' => 'MapworksBetaUser04954212@mailinator.com',
                'student_status' => '1',
                'student_risk_status' => 'green',
                'student_risk_image_name' => 'risk-level-icon-g.png',
                'student_intent_to_leave' => 'green',
                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                'student_classlevel' => '1st Year/Freshman',
                'student_logins' => '0',
                'last_activity_date' => NULL,
                'last_activity' => NULL,
            ],
            [
                'student_id' => '4955212',
                'student_first_name' => 'Lennon',
                'student_last_name' => 'Aguilar',
                'external_id' => '4955212',
                'student_primary_email' => 'MapworksBetaUser04955212@mailinator.com',
                'student_status' => '1',
                'student_risk_status' => 'red',
                'student_risk_image_name' => 'risk-level-icon-r1.png',
                'student_intent_to_leave' => 'green',
                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                'student_classlevel' => '1st Year/Freshman',
                'student_logins' => '1',
                'last_activity_date' => '2017/08/09',
                'last_activity' => 'Contact',
            ],
            [
                'student_id' => '4956507',
                'student_first_name' => 'Annalee',
                'student_last_name' => 'Aguirre',
                'external_id' => '4956507',
                'student_primary_email' => 'MapworksBetaUser04956507@mailinator.com',
                'student_status' => '1',
                'student_risk_status' => 'yellow',
                'student_risk_image_name' => 'risk-level-icon-y.png',
                'student_intent_to_leave' => 'green',
                'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                'student_classlevel' => '1st Year/Freshman',
                'student_logins' => '0',
                'last_activity_date' => NULL,
                'last_activity' => NULL,
            ]
        ];
    }


    public function testGetToolAnalysisData()
    {
        $this->specify("Test getToolAnalysisData", function ($facultyId, $organizationId, $surveyClosedDate, $mapworksToolEntityData, $orgPermissionSetToolEntityData, $permissionIds, $mapworksToolLastRunEntityData, $expectedResult) {
            // Declaring mock repositories
            $mockWessLinkRepository = $this->getMock('wessLinkRepository', ['getSurveyClosedDateForFaculty']);
            $mockMapworksToolRepository = $this->getMock('mapworksToolRepository', ['getToolName', 'getShortCode', 'getToolOrder', 'findAll']);
            $mockOrgPermissionSetToolRepository = $this->getMock('orgPermissionsetToolRepository', ['findOneBy']);
            $mockMapworksToolLastRunRepository = $this->getMock('mapworksToolLastRunRepository', ['findOneBy', 'getLastRun']);

            // Mocking service
            $mockOrgPermissionSetService = $this->getMock('orgPermissionSetService', ['getPermissionSetIdsOfUser']);

            $this->container->method('get')
                ->willReturnMap([
                    [OrgPermissionSetService::SERVICE_KEY, $mockOrgPermissionSetService]
                ]);
            $mockOrgPermissionSetService->method('getPermissionSetIdsOfUser')->willReturn($permissionIds);

            // Mocking repository
            $this->repositoryResolver->method('getRepository')->willReturnMap([
                    [wessLinkRepository::REPOSITORY_KEY, $mockWessLinkRepository],
                    [mapworksToolLastRunRepository::REPOSITORY_KEY, $mockMapworksToolLastRunRepository],
                    [mapworksToolRepository::REPOSITORY_KEY, $mockMapworksToolRepository],
                    [orgPermissionsetToolRepository::REPOSITORY_KEY, $mockOrgPermissionSetToolRepository]
                ]
            );

            // Mocking findAll in MapworksToolRepository with MapworksToolObject
            $mockMapworksToolRepository->method('findAll')->willReturn($mapworksToolEntityData);

            // Mocking findOneBy in orgPermissionsetToolRepository with OrgPermissionSetTool Object
            $mockOrgPermissionSetToolRepository->method('findOneBy')->willReturn($orgPermissionSetToolEntityData);

            //Mocking getSurveyClosedDateForFaculty in wessLinkRepository
            $mockWessLinkRepository->method('getSurveyClosedDateForFaculty')->willReturn($surveyClosedDate);

            foreach ($mapworksToolLastRunEntityData as $mapworksToolLastRunEntityDataItem) {
                $mockMapworksToolLastRunRepository->method('findOneBy')->willReturn($mapworksToolLastRunEntityDataItem);
                $mockMapworksToolLastRunRepository->method('getLastRun')->willReturn($mapworksToolLastRunEntityDataItem->getLastrun());
            }

            $mapworksToolService = new MapworksToolService($this->repositoryResolver, $this->logger, $this->container);
            $result = $mapworksToolService->getToolAnalysisData($facultyId, $organizationId);

            $this->assertEquals($result, $expectedResult);

        }, [
            'examples' => [
                [ // Example 1 : Test case returning multiple tools
                    5048809,
                    62,
                    [
                        'close_date' => '2017-08-25 17:16:18', //Survey Close Date
                    ],
                    [
                        $this->createMapworksToolEntity(1, 1, 'Top Issues', 'T-I', 1),
                        $this->createMapworksToolEntity(2, 2, 'Top Issues 2', 'T-II', 1),
                        $this->createMapworksToolEntity(3, 3, 'Top Issues 3', 'T-III', 1)
                    ],
                    [
                        $this->createOrgPermissionSetToolEntity(1, 2, 1676)
                    ],
                    [ // PermissionSetIdsOfUser
                        1640,
                        1676,
                        1322
                    ],
                    [
                        $this->createMapworksToolLastRunEntity(271, 5048809, new \DateTime('2017-08-01 11:11:11'))
                    ],
                    [
                        $this->createToolAnalysisDTO('Top Issues', 'T-I', 1, true, new \DateTime('2017-08-01 11:11:11')),
                        $this->createToolAnalysisDTO('Top Issues 2', 'T-II', 2, true, new \DateTime('2017-08-01 11:11:11')),
                        $this->createToolAnalysisDTO('Top Issues 3', 'T-III', 3, true, new \DateTime('2017-08-01 11:11:11'))
                    ]
                ],
                [ // Example 2 : Test case when last run date > close date
                    5048809,    // facultyId
                    62,         // organizationId
                    [
                        'close_date' => '2017-08-03 17:16:18', //Survey Close Date
                    ],
                    [           // mapworksToolEntityData
                        $this->createMapworksToolEntity(2, 2, 'otherbox', 't2', false)
                    ],
                    [
                        $this->createOrgPermissionSetToolEntity(2, 62, 1640)
                    ],
                    [ // PermissionSetIdsOfUser
                        1640,
                        1676
                    ],
                    [
                        $this->createMapworksToolLastRunEntity(271, 5048809, new \DateTime('2017-08-05 17:16:18'))
                    ],
                    [   // Expected Output
                        $this->createToolAnalysisDTO('otherbox', 't2', 2, false, new \DateTime('2017-08-05 17:16:18'))
                    ]
                ],
                [ // Example 3 : Test with no permission for tool
                    5048809,
                    62,
                    [
                        'close_date' => '2017-08-03 17:16:18', //Survey Close Date
                    ],
                    [   // mapworksToolEntityData
                        $this->createMapworksToolEntity(2, 2, 'otherbox', 't2', false)
                    ],
                    [ // OrgPermissionSetToolEntity

                    ],
                    [ // PermissionSetIdsOfUser
                        Null,
                        Null
                    ],
                    [
                        $this->createMapworksToolLastRunEntity(271, 5048809, new \DateTime('2017-08-01 11:11:11'))
                    ],
                    [   // Expected Output

                    ]
                ],
                [ // Example 4 : Test case when last run date is null
                    5048809,    // facultyId
                    62,         // organizationId
                    [
                        'close_date' => '2017-08-03 17:16:18', //Survey Close Date
                    ],
                    [           // mapworksToolEntityData
                        $this->createMapworksToolEntity(2, 2, 'otherbox', 't2', false)
                    ],
                    [
                        $this->createOrgPermissionSetToolEntity(2, 62, 1640)
                    ],
                    [ // PermissionSetIdsOfUser
                        1640,
                        1676
                    ],
                    [],
                    [   // Expected Output
                        $this->createToolAnalysisDTO('otherbox', 't2', 2, true, null)
                    ]
                ]

            ]
        ]);
    }

    public function testCreateMapworksToolLastRunObject()
    {
        $this->specify("Test CreateMapworksToolLastRunObject", function ($toolId, $personId, $dateTime, $expectedResult) {
            try {
                $mockMapworksToolRepository = $this->getMock("MapworksToolRepository", ["find"]);
                $mockMapworksToolRepository->method("find")->willReturn($this->getMapworksToolObject($toolId));

                $mockPersonRepository = $this->getMock("PersonRepository", ["find"]);
                $mockPersonRepository->method("find")->willReturn($this->getPersonObject($personId));

                $this->repositoryResolver->method('getRepository')->willReturnMap([
                    [
                        MapworksToolRepository::REPOSITORY_KEY,
                        $mockMapworksToolRepository
                    ],
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ]
                ]);
                $mapworksToolService = new MapworksToolService($this->repositoryResolver, $this->logger, $this->container);
                $result = $mapworksToolService->createMapworksToolLastRunObject($toolId, $personId, $dateTime);
                $this->assertEquals($result, $expectedResult);
            } catch (SynapseValidationException $exception) {
                $this->assertEquals($exception->getMessage(), $expectedResult);
            }
        }, [
            "examples" => [
                //Comparing created mapWorksLastRunObject with expected mapWorksLastRunObject with provided parameters
                [
                    1,
                    5049081,
                    $this->dateTime,
                    $this->mapWorksLastRunObject,
                ],
                //Creating mapWorksLastRunObject with provided parameters(toolId = null) and this will throw SynapseValidationException
                [
                    null,
                    5049081,
                    $this->dateTime,
                    'Mapworks tool not found.', //Expected error message
                ],
                //Creating mapWorksLastRunObject with provided parameters(personId = null) and this will throw SynapseValidationException
                [
                    1,
                    null,
                    $this->dateTime,
                    'Person not found.', //Expected error message
                ]
            ]
        ]);
    }

    public function testSaveMapworksToolLastRunObject()
    {
        $this->specify("Test SaveMapworksToolLastRunObject", function ($toolId, $personId, $dateTime, $expectedResult) {

            try {
                $mockMapworksToolLastRunRepository = $this->getMock("MapworksToolLastRunRepository", ["findOneBy"]);
                $mockMapworksToolLastRunRepository->method("findOneBy")->willReturn($this->mapWorksLastRunObject);

                $mockMapworksToolRepository = $this->getMock("MapworksToolRepository", ["find"]);
                $mockMapworksToolRepository->method("find")->willReturn($this->getMapworksToolObject($toolId));

                $mockPersonRepository = $this->getMock("PersonRepository", ["find"]);
                $mockPersonRepository->method("find")->willReturn($this->getPersonObject($personId));

                $this->repositoryResolver->method('getRepository')->willReturnMap([
                    [
                        MapworksToolLastRunRepository::REPOSITORY_KEY,
                        $mockMapworksToolLastRunRepository
                    ],
                    [
                        MapworksToolRepository::REPOSITORY_KEY,
                        $mockMapworksToolRepository
                    ],
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ]
                ]);

                $mapworksToolService = new MapworksToolService($this->repositoryResolver, $this->logger, $this->container);
                $result = $mapworksToolService->saveMapworksToolLastRunObject($toolId, $personId, $dateTime);
                $this->assertEquals($result, $expectedResult);
            } catch (SynapseValidationException $exception) {
                $this->assertEquals($exception->getMessage(), $expectedResult);
            }

        }, [
            "examples" => [
                //Comparing saved mapWorksLastRunObject with expected mapWorksLastRunObject with provided parameters
                [
                    1,
                    $this->facultyId,
                    $this->dateTime,
                    $this->mapWorksLastRunObject,
                ],
                //Saving mapWorksLastRunObject with provided parameters(toolId = null) and this will throw SynapseValidationException
                [
                    null,
                    $this->facultyId,
                    $this->dateTime,
                    'Mapworks tool not found.',
                ],
                //Saving mapWorksLastRunObject with provided parameters(personId = null) and this will throw SynapseValidationException
                [
                    1,
                    null,
                    $this->dateTime,
                    'Person not found.',
                ]
            ]
        ]);
    }

    /**
     * @param int $id
     * @param int $toolOrder
     * @param string $toolName
     * @param string $shortCode
     * @param bool $canAccessWithAggregateOnlyPermission
     * @return MapworksTool
     */
    private function createMapworksToolEntity($id, $toolOrder, $toolName, $shortCode, $canAccessWithAggregateOnlyPermission)
    {
        $mapworksToolObject = new MapworksTool();
        $mapworksToolObject->setId($id);
        $mapworksToolObject->setToolOrder($toolOrder);
        $mapworksToolObject->setToolName($toolName);
        $mapworksToolObject->setShortCode($shortCode);
        $mapworksToolObject->setCanAccessWithAggregateOnlyPermission($canAccessWithAggregateOnlyPermission);
        return $mapworksToolObject;
    }

    /**
     * @param string $toolName
     * @param string $shortCode
     * @param int $toolOrder
     * @param bool $hasNewDataSinceLastRunDate
     * @param datetime $lastRunDate
     * @return ToolAnalysisDTO
     */
    private function createToolAnalysisDTO($toolName, $shortCode, $toolOrder, $hasNewDataSinceLastRunDate, $lastRunDate)
    {
        $toolAnalysisDTO = new ToolAnalysisDTO();
        $toolAnalysisDTO->setToolName($toolName);
        $toolAnalysisDTO->setShortCode($shortCode);
        $toolAnalysisDTO->setToolOrder($toolOrder);
        $toolAnalysisDTO->setHasNewDataSinceLastRunDate($hasNewDataSinceLastRunDate);
        $toolAnalysisDTO->setLastRunDate($lastRunDate);
        return $toolAnalysisDTO;
    }

    /**
     * @param int $toolId
     * @param int $personId
     * @param datetime $lastRun
     * @return MapworksToolLastRun
     */
    private function createMapworksToolLastRunEntity($toolId, $personId, $lastRun)
    {
        $mapworksToolLastRun = new MapworksToolLastRun();
        $mapworksToolLastRun->setToolId($toolId);
        $mapworksToolLastRun->setPersonId($personId);
        $mapworksToolLastRun->setLastRun($lastRun);
        return $mapworksToolLastRun;
    }

    /**
     * @param int $mapworksToolId
     * @param int $organization
     * @param int $orgPermissionSet
     * @return OrgPermissionsetTool
     */
    private function createOrgPermissionSetToolEntity($mapworksToolId, $organization, $orgPermissionSet)
    {
        $orgPermissionSetTool = new OrgPermissionsetTool();
        $orgPermissionSetTool->setMapworksToolId($mapworksToolId);
        $orgPermissionSetTool->setOrganization($organization);
        $orgPermissionSetTool->setOrgPermissionset($orgPermissionSet);
        return $orgPermissionSetTool;
    }

    public function testGenerateToolPDF()
    {
        $this->specify("Test generateToolPDF", function ($phantomResponse, $mapworksToolEntityData, $ebiConfig, $generateURLforMapworksData, $organizationId, $personId, $toolId, $zoom, $expectedResult) {
            $mockMapworksToolRepository = $this->getMock('mapworksToolRepository', ['find', 'getToolName']);
            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy']);

            $mockMapworksToolRepository->method('find')->willReturn($mapworksToolEntityData);
            $mockMapworksToolRepository->method('getToolName')->willReturn($mapworksToolEntityData->getToolName());
            $mockEbiConfigRepository->method('findOneByKey')->willReturn($ebiConfig['value']);

            $mockUrlUtilityService = $this->getMock('UrlUtilityService', ['generateURLforMapworks']);
            $mockUrlUtilityService->method('generateURLforMapworks')->willReturn($generateURLforMapworksData);

            $mockPdfDetailsService = $this->getMock('PdfDetailsService', ['generatePDFusingPhantomJS']);
            $mockPdfDetailsService->method('generatePDFusingPhantomJS')->willReturn($phantomResponse);

            $this->repositoryResolver->method('getRepository')->willReturnMap([
                    [mapworksToolRepository::REPOSITORY_KEY, $mockMapworksToolRepository],
                    [EbiConfigRepository::REPOSITORY_KEY, $mockEbiConfigRepository]
                ]
            );

            $this->container->method("get")->willReturnMap([
                [URLUtilityService::SERVICE_KEY, $mockUrlUtilityService],
                [PdfDetailsService::SERVICE_KEY, $mockPdfDetailsService]
            ]);

            try {
                $mapWorksToolService = new MapworksToolService($this->repositoryResolver, $this->logger, $this->container);
                $result = $mapWorksToolService->generateToolPDF($organizationId, $personId, $toolId, $zoom);
                $this->assertEquals($expectedResult, $result);
            } catch (\Exception $e) {
                //because we can't test readfile method due to time() in generated url
                if (strstr($e->getMessage(), 'failed to open stream: No such file or directory')) {
                    $fileName = $organizationId . '-' . $personId . '-' . $mapworksToolEntityData->getToolName() . time();
                    $fileName = md5($fileName) . '.pdf';
                    $temporaryFileOnServer = '/tmp/' . $fileName;
                    $this->assertEquals("readfile($temporaryFileOnServer): failed to open stream: No such file or directory", $e->getMessage());
                } else {
                    $this->assertEquals($expectedResult, $e->getMessage());
                }
            }
        }, [
            "examples" => [
                [ // example 1: test with phantomResponse true
                    true,
                    $this->createMapworksToolEntity(1, 1, 'Top Issues', 'T-I', 1),
                    [
                        'key' => 'TOP_ISSUES_URL_PATH',
                        'value' => '/top-issues/webpage'
                    ],
                    'https://mapworks-qa.skyfactor.com/top-issues/webpage?access_token=NDUzZTFmZTIwNDUzYjc1ZDYxZjBlNjA1NWJhNGE4Yzg1NWUwMDJmNTA5OTZhZjEwZTVmYWFmYmQxMzJlMzI5Mg&print=pdf',
                    $this->organizationId,
                    $this->facultyId,
                    1,
                    1,
                    'File Successfully Created'////because we can't test readfile method due to time() in generated url
                ],
                [ // example 2: test with phantomResponse false
                    false,
                    $this->createMapworksToolEntity(2, 2, 'Top Issues 2', 'T-II', 1),
                    [
                        'key' => 'TOP_ISSUES_URL_PATH',
                        'value' => '/top-issues/webpage'
                    ],
                    'https://mapworks-qa.skyfactor.com/top-issues/webpage?access_token=NDUzZTFmZTIwNDUzKKc1ZDYxZjBlNjA1NWJhNGE4Yzg1NWUwMDJmNTA5OTZhZjEwZTVmYWFmYmQxMzJlMzI5Mg&print=pdf',
                    $this->organizationId,
                    $this->facultyId,
                    2,
                    1.1,
                    'File Not Created.'
                ]
            ]
        ]);
    }

    private function getMapworksToolObject($toolId)
    {
        if ($toolId != null) {
            return $this->mapworksToolObject;
        } else {
            throw new SynapseValidationException('Mapworks tool not found.');
        }
    }

    private function getPersonObject($personId)
    {
        if ($personId != null) {
            return $this->personObject;
        } else {
            throw new SynapseValidationException('Person not found.');
        }
    }


}