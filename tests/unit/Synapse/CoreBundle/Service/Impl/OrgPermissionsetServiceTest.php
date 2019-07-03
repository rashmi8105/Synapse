<?php
use Codeception\TestCase\Test;
use Synapse\CoreBundle\Entity\FeatureMaster;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgPermissionset;
use Synapse\CoreBundle\Entity\OrgPermissionsetFeatures;
use Synapse\CoreBundle\Repository\FeatureMasterRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgFeaturesRepository;
use Synapse\CoreBundle\Repository\OrgMetadataRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetFeaturesRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetMetadataRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgQuestionRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\MapworksToolBundle\Entity\MapworksTool;
use Synapse\MapworksToolBundle\Entity\OrgPermissionsetTool;
use Synapse\MapworksToolBundle\Repository\MapworksToolRepository;
use Synapse\MapworksToolBundle\Repository\OrgPermissionsetToolRepository;
use Synapse\RestBundle\Entity\AccessLevelDto;
use Synapse\RestBundle\Entity\CoursesAccessDto;
use Synapse\RestBundle\Entity\FeatureBlockDto;
use Synapse\RestBundle\Entity\OrgPermissionSetDto;
use Synapse\RestBundle\Entity\PermissionValueDto;
use Synapse\RestBundle\Entity\ToolSelectionDto;

class OrgPermissionsetServiceTest extends Test
{
    use \Codeception\Specify;

    /**
     * @var int
     */
    private $orgId = 211;

    /**
     * @var int
     */
    private $userId = 1;
    /**
     * @var int
     */
    private $featureId = 1;

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
     * @var array
     */
    private $toolsPermissionInfo;

    /**
     * @var OrgPermissionset
     */
    private $orgPermissionSet;

    protected function _before()
    {
        $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
        $this->mockLogger = $this->getMock('Logger', ['debug', 'error']);
        $this->mockContainer = $this->getMock('Container', ['get']);
        $this->toolsPermissionInfo = $this->getToolsPermissionInfo();
        $this->orgPermissionSet = new OrgPermissionset();

    }

    /**
     *
     */
    public function testCheckActivityPermission()
    {
        $this->specify("Test activity permission from allowed features ", function ($expectedResult, $allowedFeatures, $activityType) {
            $mockOrgPermissionsetRepository = $this->getMock('OrgPermissionsetRepository', ['flush']);

            $mockOrgPermissionsetMetadataRepository = $this->getMock('OrgPermissionsetMetadataRepository');
            $mockOrgOrgMetadataRepository = $this->getMock('OrgMetadataRepository');
            $mockOrgQuestionRepository = $this->getMock('OrgQuestionRepository');
            $this->mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgPermissionsetRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetRepository
                    ],

                    [
                        OrgPermissionsetMetadataRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetMetadataRepository
                    ],

                    [
                        OrgMetadataRepository::REPOSITORY_KEY,
                        $mockOrgOrgMetadataRepository
                    ],

                    [
                        OrgQuestionRepository::REPOSITORY_KEY,
                        $mockOrgQuestionRepository
                    ]
                ]);

            $orgPermissionsetService = new OrgPermissionsetService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $response = $orgPermissionsetService->checkActivityPermission($allowedFeatures, $activityType);
            $this->assertEquals($expectedResult, $response);

        }, [
            'examples' => [
                [
                    true,
                    $this->getAllowedFeaturesArray(),
                    'appointment'
                ],
                [
                    false,
                    $this->getAllowedFeaturesArray(false),
                    'appointment'
                ]
            ]
        ]);
    }

    /**
     * This method test for flow with mock objects only. there is no real database transaction.
     * Test create organization permission set
     */
    public function testCreateOrgPermissionset()
    {
        $this->specify("Test create organization permission set ", function ($expectedResult, $orgPermissionSetDto, $copyFlag) {

            $mockValidator = $this->getMock('validator', ['validate']);

            $mockValidator->method('validate')->willReturn([]);
            $mockRbacManager = $this->getMock('RbacManager', ['checkAccessToOrganization']);
            $mockRbacManager->method('checkAccessToOrganization')->willReturn(true);
            $this->mockContainer->method('get')->willReturnMap(
                [
                    [
                        SynapseConstant::VALIDATOR,
                        $mockValidator,
                    ],
                    [
                        Manager::SERVICE_KEY,
                        $mockRbacManager
                    ]
                ]
            );

            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['find']);

            $mockOrg = $this->getMock(Organization::class, []);
            $mockOrganizationRepository->method('find')->willReturn($mockOrg);

            $mockOrgPermissionsetRepository = $this->getMock('OrgPermissionsetRepository', ['persist', 'flush']);

            $mockOrgPermissionsetRepository->method('persist')->willReturn(true);
            $mockOrgPermissionsetRepository->method('flush')->willReturn(true);

            $mockOrgPermissionsetFeaturesRepository = $this->getMock('OrgPermissionsetFeaturesRepository', ['persist']);
            $mockOrgPermissionsetFeaturesRepository->method('persist')->willReturn(true);

            $mockOrgFeaturesRepository = $this->getMock('OrgFeaturesRepository', ['findOneBy']);

            $orgFeature = $this->getMock('OrgFeatures', ['getConnected']);
            $orgFeature->method('getConnected')->willReturn(false);
            $mockOrgFeaturesRepository->method('findOneBy')->willReturn($orgFeature);

            $mockOrgPermissionsetMetadataRepository = $this->getMock('OrgPermissionsetMetadataRepository');
            $mockOrgOrgMetadataRepository = $this->getMock('OrgMetadataRepository');
            $mockOrgQuestionRepository = $this->getMock('OrgQuestionRepository');
            $mockFeatureMasterRepository = $this->getMock('OrgQuestionRepository', ['find']);

            $feature_master = $this->getMock(FeatureMaster::class, null);
            $feature_master->setId($this->featureId);
            $mockFeatureMasterRepository->method('find')->willReturn($feature_master);

            $this->mockRepositoryResolver->method('getRepository')
                ->willReturnMap([

                    [
                        OrgPermissionsetFeaturesRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetFeaturesRepository
                    ],
                    [
                        OrgFeaturesRepository::REPOSITORY_KEY,
                        $mockOrgFeaturesRepository
                    ],
                    [
                        FeatureMasterRepository::REPOSITORY_KEY,
                        $mockFeatureMasterRepository
                    ],
                    [
                        OrganizationRepository::REPOSITORY_KEY,
                        $mockOrganizationRepository
                    ],
                    [
                        OrgPermissionsetRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetRepository
                    ],

                    [
                        OrgPermissionsetMetadataRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetMetadataRepository
                    ],

                    [
                        OrgMetadataRepository::REPOSITORY_KEY,
                        $mockOrgOrgMetadataRepository
                    ],

                    [
                        OrgQuestionRepository::REPOSITORY_KEY,
                        $mockOrgQuestionRepository
                    ]
                ]);
            $orgPermissionsetService = new OrgPermissionsetService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $response = $orgPermissionsetService->createOrgPermissionset($orgPermissionSetDto, $copyFlag);
            // check all properties matches
            $this->assertEquals($orgPermissionSetDto, $response);

        }, [
            'examples' => [
                [   // test create permissionset with copy flag true
                    true,  // expected result
                    $this->getPermissionSetDto(), // sample Request Dto that is sent through api call
                    true // denotes copy flag
                ],
                [
                    // test create permissionset with copy flag false
                    true,
                    $this->getPermissionSetDto(),
                    false
                ]
            ]
        ]);

    }

    /**
     * Test update organization permission set
     */

    public function testUpdateOrgPermissionset()
    {
        $this->specify("Test update organization permission set ", function ($expectedResult, $orgPermissionSetDto, $copyFlag) {

            $mockValidator = $this->getMock('validator', ['validate']);

            $mockValidator->method('validate')->willReturn([]);
            $mockRbacManager = $this->getMock('RbacManager', ['checkAccessToOrganization']);
            $mockRbacManager->method('checkAccessToOrganization')->willReturn(true);
            $this->mockContainer->method('get')->willReturnMap(
                [
                    [
                        SynapseConstant::VALIDATOR,
                        $mockValidator,
                    ],
                    [
                        Manager::SERVICE_KEY,
                        $mockRbacManager
                    ]
                ]
            );

            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['find']);

            $mockOrg = $this->getMock(Organization::class, []);
            $mockOrganizationRepository->method('find')->willReturn($mockOrg);

            $mockOrgPermissionsetRepository = $this->getMock('OrgPermissionsetRepository', ['find', 'persist', 'flush']);

            $mockOrgPermissionsetRepository->method('find')->willReturn($this->orgPermissionSet);
            $mockOrgPermissionsetRepository->method('persist')->willReturn(true);
            $mockOrgPermissionsetRepository->method('flush')->willReturn(true);

            $mockOrgPermissionsetFeaturesRepository = $this->getMock('OrgPermissionsetFeaturesRepository', ['findOneBy', 'persist']);
            $orgFeaturePermission = new OrgPermissionsetFeatures();
            $mockOrgPermissionsetFeaturesRepository->method('findOneBy')->willReturn($orgFeaturePermission);
            $mockOrgPermissionsetFeaturesRepository->method('persist')->willReturn(true);

            $mockOrgFeaturesRepository = $this->getMock('OrgFeaturesRepository', ['findOneBy']);

            $orgFeature = $this->getMock('OrgFeatures', ['getConnected']);
            $orgFeature->method('getConnected')->willReturn(false);
            $mockOrgFeaturesRepository->method('findOneBy')->willReturn($orgFeature);

            $mockOrgPermissionsetMetadataRepository = $this->getMock('OrgPermissionsetMetadataRepository');
            $mockOrgOrgMetadataRepository = $this->getMock('OrgMetadataRepository');
            $mockOrgQuestionRepository = $this->getMock('OrgQuestionRepository');
            $mockFeatureMasterRepository = $this->getMock('OrgQuestionRepository', ['find']);

            $feature_master = $this->getMock(FeatureMaster::class, null);
            $feature_master->setId($this->featureId);
            $mockFeatureMasterRepository->method('find')->willReturn($feature_master);

            $this->mockRepositoryResolver->method('getRepository')
                ->willReturnMap([

                    [
                        OrgPermissionsetFeaturesRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetFeaturesRepository
                    ],
                    [
                        OrgFeaturesRepository::REPOSITORY_KEY,
                        $mockOrgFeaturesRepository
                    ],
                    [
                        FeatureMasterRepository::REPOSITORY_KEY,
                        $mockFeatureMasterRepository
                    ],
                    [
                        OrganizationRepository::REPOSITORY_KEY,
                        $mockOrganizationRepository
                    ],
                    [
                        OrgPermissionsetRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetRepository
                    ],

                    [
                        OrgPermissionsetMetadataRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetMetadataRepository
                    ],

                    [
                        OrgMetadataRepository::REPOSITORY_KEY,
                        $mockOrgOrgMetadataRepository
                    ],

                    [
                        OrgQuestionRepository::REPOSITORY_KEY,
                        $mockOrgQuestionRepository
                    ]
                ]);
            $orgPermissionsetService = new OrgPermissionsetService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $response = $orgPermissionsetService->updateOrgPermissionset($orgPermissionSetDto);
            // check all properties matches
            $this->assertEquals($orgPermissionSetDto, $response);

        }, [
            'examples' => [
                [   // test create permissionset with copy flag true
                    true,  // expected result
                    $this->getPermissionSetDto(true), // sample Request Dto that is sent through api call
                    true // denotes copy flag
                ],
                [
                    // test create permissionset with copy flag false
                    true,
                    $this->getPermissionSetDto(true),
                    false
                ]
            ]
        ]);
    }

    /**
     * Test insert tools into permissions
     */
    public function testInsertToolsIntoToolPermissions()
    {
        $this->specify("Test insert tools into permissions ", function ($expectedResult, $mapworksTools, $orgPermissionSet) {
            $mockMapworksToolRepository = $this->getMock('MapworksToolRepository', ['find']);
            foreach ($mapworksTools as $mapworksTool) {
                $mockMapworksToolRepository->method('find')->with($mapworksTool->getToolId())->willReturn($mapworksTool);
            }
            $mockOrgPermissionsetToolRepository = $this->getMock('OrgPermissionsetToolRepository', ['persist']);
            $mockOrgPermissionsetToolRepository->method('persist')->willReturn(true);
            $this->mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgPermissionsetToolRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetToolRepository
                    ],
                    [
                        MapworksToolRepository::REPOSITORY_KEY,
                        $mockMapworksToolRepository
                    ]
                ]);

            $orgPermissionsetService = new OrgPermissionsetService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $insertedToolsPermissions = $orgPermissionsetService->insertToolsIntoToolPermissions($mapworksTools, $orgPermissionSet);
            $this->assertTrue(is_array($insertedToolsPermissions));
            if (count($insertedToolsPermissions) > 0) {
                foreach ($insertedToolsPermissions as $insertedToolsPermission) {
                    $this->assertInstanceOf(OrgPermissionsetTool::class, $insertedToolsPermission);
                }
            }
            $this->assertEquals($expectedResult, count($insertedToolsPermissions));

        }, [
            'examples' => [
                // this example verifies count of tool created if selection is true and false respectively in both array items
                [
                    1, // expected count of created tools
                    [  // array mapworks tools
                        $this->getToolSelectionDto(1, "Top Issues", true, true),
                        $this->getToolSelectionDto(1, "Top Issues", false, true)
                    ],
                    $this->orgPermissionSet,
                ],
                // this example verifies count of tool created if selection is true in both array items
                [
                    2,
                    [
                        $this->getToolSelectionDto(1, "Top Issues", true, true),
                        $this->getToolSelectionDto(1, "Top Issues", true, true)
                    ],
                    $this->orgPermissionSet,
                ],
                // this example verifies count of tool updated if selection is false in both array items ie. no tools created,also "no aggregate access" false and true respectively
                [
                    0,
                    [
                        $this->getToolSelectionDto(1, "Top Issues", false, false),
                        $this->getToolSelectionDto(1, "Top Issues", false, true)
                    ],
                    $this->orgPermissionSet,
                ],

            ]
        ]);
    }

    /**
     * test organization permissionset tools retrieval
     */
    public function testRetrieveOrgPermissionsetTools()
    {
        $this->specify("Test retrieve organization permission set ", function ($expectedResult, $permissionsetId, $organizationId) {
            $mockOrgPermissionsetToolRepository = $this->getMock('OrgPermissionsetToolRepository', ['getToolsWithPermissionsetSelection']);
            $toolPermissionInfo = $this->toolsPermissionInfo[$organizationId][$permissionsetId];
            $mockOrgPermissionsetToolRepository->method('getToolsWithPermissionsetSelection')->willReturn($toolPermissionInfo);
            $this->mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgPermissionsetToolRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetToolRepository
                    ],
                ]);

            $orgPermissionsetService = new OrgPermissionsetService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $tools = $orgPermissionsetService->retrieveOrgPermissionsetTools($permissionsetId, $organizationId);
            $this->assertEquals(count($expectedResult), count($tools));
            $this->assertTrue(is_array($tools));
            if (count($tools) > 0) {
                // verify expected result entry from resulted tools
                foreach ($tools as $key => $tool) {
                    $toolSelectionDto = $expectedResult[$key];
                    $this->assertEquals($toolSelectionDto->getToolId(), $tool->getToolId());
                    $this->assertEquals($toolSelectionDto->getToolName(), $tool->getToolName());
                    $this->assertEquals($toolSelectionDto->getSelection(), $tool->getSelection());
                    $this->assertEquals($toolSelectionDto->getCanAccessWithAggregateOnlyPermission(), $tool->getCanAccessWithAggregateOnlyPermission());
                }
            }

        }, [
            'examples' => [
                // verifies retrieval of mapwork tools belongs to different org_permissionset_id also
                // validates the attributes of each tool retrieved
                // number of retrieved tools here 1
                [
                    [
                        $this->getToolSelectionDto(1, "Top Issues", true, true) // toolSelectionDto array
                    ],
                    1652,  // org_permissionset_id
                    211, // organization_id
                ],
                // verifies retrieval of mapwork tools belongs to different org_permissionset_id also
                // validates the attributes of each tool retrieved
                // number of retrieved tools here 0
                [
                    [],
                    1623,
                    211,
                ],

            ]
        ]);
    }


    /**
     * Test update tools into permissions
     */
    public function testUpdatePermissionsetTools()
    {
        $this->specify("Test update tools into permissions ", function ($expectedResult, $mapworksTools, $orgPermissionSet) {
            $mockMapworksToolRepository = $this->getMock('MapworksToolRepository', ['find']);
            foreach ($mapworksTools as $mapworksTool) {
                $mockMapworksToolRepository->method('find')->with($mapworksTool->getToolId())->willReturn($mapworksTool);
            }
            $mockOrgPermissionsetToolRepository = $this->getMock('OrgPermissionsetToolRepository', ['findOneBy', 'persist']);
            foreach ($mapworksTools as $mapworksTool) {
                $mockMapworksToolRepository->method('findOneBy')
                    ->with(['orgPermissionset' => $orgPermissionSet->getId(), 'mapworksToolId' => $mapworksTool->getToolId()])
                    ->willReturn($mapworksTool);
            }
            $mockOrgPermissionsetToolRepository->method('persist')->willReturn(true);

            $this->mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgPermissionsetToolRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetToolRepository
                    ],
                    [
                        MapworksToolRepository::REPOSITORY_KEY,
                        $mockMapworksToolRepository
                    ]
                ]);

            $orgPermissionsetService = new OrgPermissionsetService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $updatedToolsPermissions = $orgPermissionsetService->updatePermissionsetTools($mapworksTools, $orgPermissionSet);
            $this->assertTrue(is_array($updatedToolsPermissions));
            if (count($updatedToolsPermissions) > 0) {
                foreach ($updatedToolsPermissions as $updatedToolsPermission) {
                    $this->assertInstanceOf(OrgPermissionsetTool::class, $updatedToolsPermission);
                }
            }
            $this->assertEquals($expectedResult, count($updatedToolsPermissions));

        }, [
            'examples' => [
                // this example verifies count of tool updated if selection is true and false respectively in both array items
                [
                    1, // expected count of updated tools
                    [
                        $this->getToolSelectionDto(1, "Top Issues", true, true),
                        $this->getToolSelectionDto(1, "Top Issues", false, true)
                    ],
                    $this->orgPermissionSet,
                ],
                // this example verifies count of tool updated if selection is true in both array items
                [
                    2,
                    [
                        $this->getToolSelectionDto(1, "Top Issues", true, true),
                        $this->getToolSelectionDto(1, "Top Issues", true, true)
                    ],
                    $this->orgPermissionSet,
                ],
                // this example verifies count of tool updated if selection is false in both array items ie. no tools updated,also "no aggregate access" false and true respectively
                [
                    0,
                    [
                        $this->getToolSelectionDto(1, "Top Issues", false, false),
                        $this->getToolSelectionDto(1, "Top Issues", false, true)
                    ],
                    $this->orgPermissionSet,
                ]

            ]
        ]);
    }

    /**
     * Test list all the mapworks tools
     */
    public function testGetMapworksTools(){
        $this->specify("Test insert tools into permissions ", function ($expectedResult,$mapworkToolsInfo) {
            $mockMapworksToolRepository = $this->getMock('MapworksToolRepository', ['findAll']);
            $mapworksTools=[];
            if(count($mapworkToolsInfo)>0){
                foreach($mapworkToolsInfo as $mapworkToolInfo){
                    $mapworksTools[]=$this->getMapWorkTool($mapworkToolInfo["tool_id"], $mapworkToolInfo["name"], $mapworkToolInfo["can_access_with_aggregate_only_permission"]);
                }
            }
            $mockMapworksToolRepository->method('findAll')->willReturn($mapworksTools);
            $this->mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        MapworksToolRepository::REPOSITORY_KEY,
                        $mockMapworksToolRepository
                    ]
                ]);
            $orgPermissionsetService = new OrgPermissionsetService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $output=$orgPermissionsetService->getMapworksTools();
            $this->assertEquals($expectedResult,$output['total_count']);
            if(count($mapworksTools)>0){
                foreach ($mapworksTools as $key=>$mapworksTool){
                    $this->assertInstanceOf(MapworksTool::class,$mapworksTool);
                    $this->assertEquals($mapworksTool->getId(),$mapworkToolsInfo[$key]["tool_id"]);
                    $this->assertEquals($mapworksTool->getToolName(),$mapworkToolsInfo[$key]["name"]);
                    $this->assertEquals($mapworksTool->getCanAccessWithAggregateOnlyPermission(),$mapworkToolsInfo[$key]["can_access_with_aggregate_only_permission"]);
                }
            }
        }, [
            'examples' => [
                // verifies the total count of returned mapworks tool in this case zero tools are expected to return with mapwork tools info provided as input
                [
                    0, // expected count of retrieved tools
                    [] // mapwork tools info
                ],
                // verifies the total count of returned mapworks tool in this case 2 tools are expected to return with mapwork tools info provided as input
                [
                    2, // expected count of retrieved tools
                    [  // mapwork tools info
                        [
                            "tool_id"=>1,
                            "name"=>"Top Issues",
                            "can_access_with_aggregate_only_permission"=>true
                        ],
                        [
                            "tool_id"=>2,
                            "name"=>"Top Issues 2",
                            "can_access_with_aggregate_only_permission"=>false
                        ],
                    ]
                ]
            ]
        ]);
    }

    public function testGetToolSelection()
    {
        $this->specify("Test get tool selection tools", function ($expectedResult,$permissionSetId,$organizationId) {

            $mockOrgPermissionsetToolRepository = $this->getMock('OrgPermissionsetToolRepository', ['getToolsWithPermissionsetSelection']);
            $toolsinfo=$this->toolsPermissionInfo[$organizationId][$permissionSetId];
            $mockOrgPermissionsetToolRepository->method('getToolsWithPermissionsetSelection')->willReturn($toolsinfo);
            $this->mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgPermissionsetToolRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetToolRepository
                    ]
                ]);
            $orgPermissionsetService = new OrgPermissionsetService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $permissionSetTools = $orgPermissionsetService->getToolSelection($permissionSetId, $organizationId);
            $this->assertEquals($expectedResult,$permissionSetTools);
            $this->assertEquals(count($expectedResult),count($permissionSetTools));
        }, [
            'examples' => [
                // verifies retrieval of mapwork tools selection belongs to different org_permissionset_id also
                // number of retrieved tool selections here 1
                [
                    [
                        $this->getToolSelectionDto(1,"Top Issues",true,true,"T1")
                    ],
                    1652,  // org_permissionset_id
                    211, // organization_id
                ],
                // verifies retrieval of mapwork tools  selection belongs to different org_permissionset_id also
                // number of retrieved tools selections here 0
                [
                    [

                    ],
                    1623,
                    211,
                ],
            ]
        ]);
    }



    /**
     * @param $tool_id
     * @param $name
     * @param $can_access_with_aggregate_only_permission
     * @return MapworksTool
     */
    private function getMapWorkTool($tool_id, $name, $can_access_with_aggregate_only_permission)
    {
        $mapWorkTool = new MapworksTool();
        $mapWorkTool->setId($tool_id);
        $mapWorkTool->setToolName($name);
        $mapWorkTool->setCanAccessWithAggregateOnlyPermission($can_access_with_aggregate_only_permission);
        return $mapWorkTool;
    }
    /**
     *
     */
    private function getToolsPermissionInfo()
    {
        $toolPermissionInfo = [];
        $toolPermissionInfo[211][1623]=[];
        $toolPermissionInfo[211][1652][] = [
            'tool_id' => 1,
            'tool_name' => 'Top Issues',
            'short_code' =>'T1',
            'selection' => true,
            'can_access_with_aggregate_only_permission' => true,
        ];
        $toolPermissionInfo[211][1654][] = [
            'tool_id' => 1,
            'tool_name' => 'Top Issues',
            'short_code' =>'T1',
            'selection' => true,
            'can_access_with_aggregate_only_permission' => true,
        ];
        $toolPermissionInfo[211][1656][] = [
            'tool_id' => 1,
            'tool_name' => 'Top Issues',
            'short_code' =>'T1',
            'selection' => true,
            'can_access_with_aggregate_only_permission' => true,
        ];
        $toolPermissionInfo[211][1657][] = [
            'tool_id' => 1,
            'tool_name' => 'Top Issues',
            'short_code' =>'T1',
            'selection' => true,
            'can_access_with_aggregate_only_permission' => true,
        ];
        $toolPermissionInfo[211][1660][] = [
            'tool_id' => 1,
            'tool_name' => 'Top Issues',
            'short_code' =>'T1',
            'selection' => true,
            'can_access_with_aggregate_only_permission' => true,
        ];
        $toolPermissionInfo[211][1661][] = [
            'tool_id' => 1,
            'tool_name' => 'Top Issues',
            'short_code' =>'T1',
            'selection' => true,
            'can_access_with_aggregate_only_permission' => true,
        ];
        $toolPermissionInfo[211][1663][] = [
            'tool_id' => 1,
            'tool_name' => 'Top Issues',
            'short_code' =>'T1',
            'selection' => true,
            'can_access_with_aggregate_only_permission' => true,
        ];
        return $toolPermissionInfo;
    }

    private function getToolSelectionDto($tool_id, $name, $selection, $can_access_with_aggregate_only_permission,$shortcode=null)
    {
        $toolSelectionDto = new ToolSelectionDto();
        $toolSelectionDto->setToolId($tool_id);
        $toolSelectionDto->setToolName($name);
        $toolSelectionDto->setSelection($selection);
        $toolSelectionDto->setShortCode($shortcode);
        $toolSelectionDto->setCanAccessWithAggregateOnlyPermission($can_access_with_aggregate_only_permission);
        return $toolSelectionDto;
    }

    /**
     * @param bool $update
     * @return OrgPermissionSetDto
     */
    private function getPermissionSetDto($update = false)
    {
        $orgPermissionsetDto = new OrgPermissionSetDto();
        if ($update) {
            $orgPermissionsetDto->setOrganizationId($this->orgId);
        }
        $orgPermissionsetDto->setPermissionTemplateName("random_name" . time());
        $accessLevel = new AccessLevelDto();
        $accessLevel->setAggregateOnly(false);
        $accessLevel->setIndividualAndAggregate(true);
        $orgPermissionsetDto->setAccessLevel($accessLevel);
        $coursesAccess = new CoursesAccessDto();
        $coursesAccess->setViewCourses(true);
        $coursesAccess->setViewAllFinalGrades(false);
        $coursesAccess->setViewAllAcademicUpdateCourses(true);
        $coursesAccess->setCreateViewAcademicUpdate(true);
        $orgPermissionsetDto->setCoursesAccess($coursesAccess);
        $orgPermissionsetDto->setRiskIndicator(true);
        $orgPermissionsetDto->setIntentToLeave(false);
        $orgPermissionsetDto->setCurrentFutureIsq(false);
        $orgPermissionsetDto->setFeatures($this->createFeatures());
        $orgPermissionsetDto->setOrganizationId($this->orgId);
        $orgPermissionsetDto->setTools([]);
        return $orgPermissionsetDto;
    }


    public function createFeatures()
    {
        $return = [];
        for ($i = 1; $i < 3; $i++) {
            if ($i == 1) {
                $blockDto = new FeatureBlockDto();
                $blockDto->setId($i);
                $blockDto->setReceiveReferrals(true);
                $shareOPTDto = new FeatureBlockDto();
                $shareOPTDto->setId($i);
                $per = new PermissionValueDto();
                $per->setCreate(true);
                $per->setView(true);
                $shareOPTDto->setPrivateShare($per);
                $per = new PermissionValueDto();
                $per->setCreate(true);
                $per->setView(true);
                $shareOPTDto->setPublicShare($per);
                $per = new PermissionValueDto();
                $per->setCreate(true);
                $per->setView(true);
                $shareOPTDto->setTeamsShare($per);
                $blockDto->setDirectReferral($shareOPTDto);
                $blockDto->setReasonRoutedReferral($shareOPTDto);
            } else {
                $blockDto = new FeatureBlockDto();
                $blockDto->setId($i);
                $per = new PermissionValueDto();
                $per->setCreate(true);
                $per->setView(true);
                $blockDto->setPrivateShare($per);

                $per = new PermissionValueDto();
                $per->setCreate(true);
                $per->setView(true);
                $blockDto->setPublicShare($per);

                $per = new PermissionValueDto();
                $per->setCreate(true);
                $per->setView(true);
                $blockDto->setTeamsShare($per);
            }
            $return[] = $blockDto;
        }
        return $return;
    }

    private function getAllowedFeaturesArray($allowAppointment = true)
    {

        $userFeaturePermissionsArray =
            [
                "booking" => $allowAppointment,
                "booking_share" => array
                (
                    "public_share" => array
                    (
                        "create" => $allowAppointment,
                        "view" => $allowAppointment
                    ),

                    "private_share" => array
                    (
                        "create" => $allowAppointment,
                        "view" => $allowAppointment
                    ),
                    "teams_share" => array
                    (
                        "create" => $allowAppointment,
                        "view" => $allowAppointment
                    )

                ),

            ];

        return $userFeaturePermissionsArray;

    }
}