<?php
use Codeception\TestCase\Test;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Service\Impl\RetentionCompletionService;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository;

class RetentionCompletionServiceTest extends test
{

    use \Codeception\Specify;
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
    /**
     * @var int
     */
    private $orgId = 62;
    /**
     * @var int
     */
    private $loggedInUserId = 5048809;
    /**
     * @var array
     */
    private $retentionTrackingVariablesData = [
        [
            "external_id" => "4952854",
            "firstname" => "Jett",
            "lastname" => "Best",
            "primary_email" => "MapworksBetaUser04952854@mailinator.com",
            "retention_tracking_year" => "201617",
            "Retained to Midyear Year 1" => "1",
            "Retained to Start of Year 2" => "0",
            "Retained to Midyear Year 2" => "0",
            "Completed Degree in 1 Year or Less" => "1",
            "Completed Degree in 2 Years or Less" => "1"
        ]
    ];

    private $trackingGroupArray = [
        [
            'year_id' => 201415,
            'year_name' => 201415
        ],
        [
            'year_id' => 201516,
            'year_name' => 201415
        ]
    ];

    private $retentionVariables = [
        [
            'year_id' => 201415,
            'year_name' => 201415,
            'retention_completion_name_text' => "Completed Degree in 1 Year"
        ],
        [
            'year_id' => 201415,
            'year_name' => 201415,
            'retention_completion_name_text' => "Retained to Midyear Year 1"
        ],
        [
            'year_id' => 201516,
            'year_name' => 201516,
            'retention_completion_name_text' => "Completed Degree in 2 Years"
        ],
        [
            'year_id' => 201516,
            'year_name' => 201516,
            'retention_completion_name_text' => "Retained to Midyear Year 2"
        ],
        [
            'year_id' => 201516,
            'year_name' => 201516,
            'retention_completion_name_text' => "Retained to Start of Year 2"
        ]

    ];


    /**
     * @var string
     */
    private $accessDeniedExceptionMessage = "You do not have access to retention / completion information.";

    protected function _before()
    {
        $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
        $this->mockLogger = $this->getMock('Logger', ['debug', 'error']);
        $this->mockContainer = $this->getMock('Container', ['get']);
    }

    /**
     * @expectedException \Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testGetOrganizationRetentionTrackingGroups()
    {
        $this->specify("Test getOrganizationRetentionTrackingGroups", function ($facultyId, $organizationId, $retentionCompletionPermission, $retentionTrackingGroup, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('repositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);


            $mockOrgPermissionsetRepository = $this->getMock('OrgPermissionsetRepository', array(
                'getAllPermissionsetIdsByPerson',
                'hasRetentionAndCompletionAccess'
            ));

            $mockOrgPermissionsetRepository->expects($this->any())->method('getAllPermissionsetIdsByPerson')->willReturn([1]);

            $mockOrgPermissionsetRepository->expects($this->any())->method('hasRetentionAndCompletionAccess')->willReturn($retentionCompletionPermission);

            $mockOrgPersonStudentRetentionTrackingGroupRepository = $this->getMock('OrgPersonStudentRetentionTrackingGroupRepository', array(
                'getRetentionTrackingGroupsForOrganization',
                'getRetentionAndCompletionVariables'
            ));

            $mockOrgPersonStudentRetentionTrackingGroupRepository->expects($this->any())
                ->method('getRetentionTrackingGroupsForOrganization')->willReturn($retentionTrackingGroup);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        \Synapse\CoreBundle\Repository\OrgPermissionsetRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetRepository
                    ],
                    [
                        \Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository::REPOSITORY_KEY,
                        $mockOrgPersonStudentRetentionTrackingGroupRepository
                    ]
                ]);


            $retentionCompletionService = new RetentionCompletionService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $retentionCompletionService->getOrganizationRetentionTrackingGroups($facultyId, $organizationId);
            $this->assertEquals($result, $expectedResult);


        }, ['examples' => [

            [1, 1, 1, $this->trackingGroupArray,
                [
                    'organization_id' => 1,
                    'retention_tracking_groups' => $this->trackingGroupArray
                ]
            ],

            // No permission
            [1, 1, 0, $this->trackingGroupArray, -1] //expected result will be an array or -1 (This would throw SynapseValidationnException)

        ]]);
    }

    /**
     * @expectedException \Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testGetRetentionTrackGroupVariables()
    {
        $this->specify("Test getRetentionTrackGroupVariables", function ($facultyId, $organizationId, $retentionCompletionPermission, $retentionCompletionVariables, $retentionTrackingGroup, $currentYearId, $expectedResult) {
            //Core Mocks
            $mockRepositoryResolver = $this->getMock('repositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);


            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository',[
                'getCurrentOrPreviousAcademicYearUsingCurrentDate'
            ]);

            $mockOrgAcademicYearRepository->method('getCurrentOrPreviousAcademicYearUsingCurrentDate')->willReturn([
                ['year_id' => $currentYearId ]
            ]);


            $mockOrgPermissionsetRepository = $this->getMock('OrgPermissionsetRepository', array(
                'getAllPermissionsetIdsByPerson',
                'hasRetentionAndCompletionAccess'
            ));

            $mockOrgPermissionsetRepository->expects($this->any())->method('getAllPermissionsetIdsByPerson')->willReturn([1]);



            $mockOrgPermissionsetRepository->expects($this->any())->method('hasRetentionAndCompletionAccess')->willReturn($retentionCompletionPermission);

            $mockOrgPersonStudentRetentionTrackingGroupRepository = $this->getMock('OrgPersonStudentRetentionTrackingGroupRepository', array(
                'getRetentionTrackingGroupsForOrganization',
                'getRetentionAndCompletionVariables'
            ));


            $mockOrgPersonStudentRetentionTrackingGroupRepository->expects($this->any())
                ->method('getRetentionAndCompletionVariables')->willReturn($retentionCompletionVariables);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        \Synapse\CoreBundle\Repository\OrgPermissionsetRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetRepository
                    ],
                    [
                        \Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository::REPOSITORY_KEY,
                        $mockOrgPersonStudentRetentionTrackingGroupRepository
                    ],
                    [
                        \Synapse\AcademicBundle\Repository\OrgAcademicYearRepository::REPOSITORY_KEY,
                        $mockOrgAcademicYearRepository
                    ]
                ]);


            $retentionCompletionService = new RetentionCompletionService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $retentionCompletionService->getRetentionTrackGroupVariables($facultyId, $organizationId, $retentionTrackingGroup);

            $this->assertEquals($result, $expectedResult);


            //$facultyId, $organizationId, $retentionCompletionPermission, $retentionCompletionVariables , $retentionTrackingGroup,$expectedResult
        }, ['examples' => [

            [1, 1, 1, $this->retentionVariables, "201415","201516",
                [
                    'organization_id' => 1,
                    'retention_tracking_year' => 201415,
                    'retention_track_variables' =>
                        [

                            [
                                'year_id' => 201415,
                                'year_name' => 201415,
                                'variables' =>
                                    [
                                        "Completed Degree in 1 Year",
                                        "Retained to Midyear Year 1"
                                    ]

                            ],
                            [
                                'year_id' => 201516,
                                'year_name' => 201516,
                                'variables' =>
                                    [
                                        "Completed Degree in 2 Years",
                                        "Retained to Midyear Year 2",
                                        "Retained to Start of Year 2"
                                    ]

                            ]

                        ]

                ]

            ],
            [1, 1, 0, $this->retentionVariables, "201415","201516", -1] //expected result will be an array or -1 (This would throw SynapseValidationnException)


        ]]);
    }

    public function testGetOrganizationRetentionCompletionVariables()
    {
        $this->specify("Test GetOrganizationRetentionCompletionVariables method", function ($yearId, $studentIds, $haveAccessToStudent) {
            $mockOrgPersonStudentRetentionTrackingGroupRepository = $this->getMock("OrgPersonStudentRetentionTrackingGroupRepository", ["getRetentionCompletionVariablesByOrganization"]);

            $mockOrgPermissionsetRepository = $this->getMock("OrgPermissionsetRepository", ["getAllPermissionsetIdsByPerson", "hasRetentionAndCompletionAccess", "hasRetentionAccessToStudents"]);
            $mockOrgPermissionsetRepository->method("getAllPermissionsetIdsByPerson")->willReturn([]);
            $mockOrgPermissionsetRepository->method("hasRetentionAndCompletionAccess")->willReturn($haveAccessToStudent);
            $mockOrgPermissionsetRepository->method("hasRetentionAccessToStudents")->willReturn($haveAccessToStudent);
            // this is using $retentionTrackingVariablesData private property which is static return data for our specific case with student id "4952854"
            $mockOrgPersonStudentRetentionTrackingGroupRepository->method("getRetentionCompletionVariablesByOrganization")->willReturn($this->retentionTrackingVariablesData);
            $this->mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgPersonStudentRetentionTrackingGroupRepository::REPOSITORY_KEY,
                        $mockOrgPersonStudentRetentionTrackingGroupRepository
                    ],
                    [
                        OrgPermissionsetRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetRepository
                    ]
                ]);

            $retentionCompletionService = new RetentionCompletionService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            try {
                // case when have access
                $retentionCompletionVariables = $retentionCompletionService->getOrganizationRetentionCompletionVariables($this->loggedInUserId, $this->orgId, $yearId, $studentIds);
                $this->assertTrue(is_array($retentionCompletionVariables));
            } catch (AccessDeniedException $e) {
                // case when do not have access to student
                $this->assertEquals($e->getMessage(), $this->accessDeniedExceptionMessage);
            }

        }, [
            'examples' => [
                // example validates the variable retention variables for a given student when have access
                [
                    null, //year id
                    [4952854],  //student id
                    true // have access to student
                ],
                // get an access denied exception if a non belonging student details being asked
                [
                    null,
                    [5049367],
                    false
                ],

            ]
        ]);
    }
}