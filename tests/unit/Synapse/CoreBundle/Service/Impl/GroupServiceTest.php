<?php

namespace Synapse\CoreBundle\Service\Impl;

use Codeception\Specify;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestGroupRepository;
use Synapse\CoreBundle\Entity\OrgGroup;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrgGroupRepository;
use Synapse\CoreBundle\Repository\OrgGroupTreeRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\SearchBundle\EntityDto\GroupsDto;

class GroupServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    private $descendantGroups = [
        0 => [
            "group_id" => 50,
            "external_id" => "grp50"
        ],
        1 => [
            "group_id" => 51,
            "external_id" => "grp51"
        ],
        2 => [
            "group_id" => 52,
            "external_id" => "grp52"
        ]
    ];

    private $listOfAU = [
        0 => [
            "id" => 20,
            "org_group_id" => 35,
            "org_id" => 2,
            "academic_update_request_id" => 45
        ],
        1 => [
            "id" => 25,
            "org_group_id" => 36,
            "org_id" => 2,
            "academic_update_request_id" => 48
        ]
    ];



    public function testDeleteGroup()
    {
        $this->specify("Test Delete Group", function ($organizationId, $groupId, $isInternal, $isAUExist, $hasSubgroups, $expectedResult) {
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

            // Service Mocks
            $mockRbacManager = $this->getMock('Manager', array('checkAccessToStudent', 'checkAccessToOrganization'));
            $mockResque = $this->getMock('resque', array(
                'enqueue'
            ));

            //Repository Mocks
            $mockAcademicUpdateRequestGroupRepository = $this->getMock("AcademicUpdateRequestGroupRepository", ["isAUExistsForGroup"]);
            $mockOrgGroupTreeRepository = $this->getMock("OrgGroupTreeRepository", ["findAllDescendantGroups"]);
            $mockOrgGroupRepository = $this->getMock("OrgGroupRepository", ["find"]);

            if($groupId == "ALLSTUDENTS"){
                $orgGroup = new OrgGroup();
                $orgGroup->setExternalId("ALLSTUDENTS");
                $mockOrgGroupRepository->method('find')->willReturn($orgGroup);
            }

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        AcademicUpdateRequestGroupRepository::REPOSITORY_KEY,
                        $mockAcademicUpdateRequestGroupRepository
                    ],
                    [
                        OrgGroupTreeRepository::REPOSITORY_KEY,
                        $mockOrgGroupTreeRepository
                    ],
                    [
                        OrgGroupRepository::REPOSITORY_KEY,
                        $mockOrgGroupRepository
                    ]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        Manager::SERVICE_KEY,
                        $mockRbacManager
                    ],
                    [
                        SynapseConstant::RESQUE_CLASS_KEY,
                        $mockResque
                    ],
                ]);

            if ($isAUExist) {
                $mockAcademicUpdateRequestGroupRepository->method('isAUExistsForGroup')->willReturn($this->listOfAU);
            }

            if ($hasSubgroups) {
                $mockOrgGroupTreeRepository->method('findAllDescendantGroups')->willReturn($this->descendantGroups);
            }else {
                $mockOrgGroupTreeRepository->method('findAllDescendantGroups')->willReturn([$this->descendantGroups[0]]);
            }


            $groupService = new GroupService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $result = $groupService->deleteGroup($organizationId, $groupId, $isInternal);
                $this->assertEquals($result, $expectedResult);

            } catch (SynapseValidationException $e) {
                $errorMessage = $e->getMessage();
                $this->assertEquals($errorMessage, $expectedResult);
            }
        }, [
                'examples' => [
                    //Case 1 : External valid group with Descendants and deleted successfully
                    [
                        2,
                        45,
                        false,
                        false,
                        false,
                        45
                    ],
                    //Case 2 : External valid group having Academic Update attached with this group, throws exception
                    [
                        2,
                        46,
                        false,
                        true,
                        false,
                        'Academic Update attached with this group.'
                    ],
                    //Case 3 : Internal valid group having sub-groups under it , throws exception
                    [
                        2,
                        47,
                        true,
                        false,
                        true,
                        'Group can’t be deleted because it has subgroups below it.'
                    ],

                    //Case 3 : Internal valid group having sub-groups under it , throws exception
                    [
                        2,
                        47,
                        true,
                        false,
                        true,
                        'Group can’t be deleted because it has subgroups below it.'
                    ],

                    //Case 4 : AllStudents groups should not be deleted
                    [
                        2,
                        "ALLSTUDENTS",
                        false,
                        false,
                        false,
                        'ALL Student Group can not be deleted.'
                    ]

                ]
            ]
        );
    }

    public function testGetListGroups()
    {
        $this->specify("Test getList Group", function ($loggedUserId, $groupsArray, $expectedResult) {
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

            // Service Mocks
            $mockRbacManager = $this->getMock('Manager', array('refreshPermissionCache', 'getAccessMap'));

            $mockRbacManager->expects($this->any())
                ->method('getAccessMap')
                ->will($this->returnValue($groupsArray));

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        Manager::SERVICE_KEY,
                        $mockRbacManager
                    ]
                ]);

            $groupService = new GroupService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $groupService->getListGroups($loggedUserId);
            $this->assertEquals($result->getGroups(), $expectedResult);
        }, [
            'examples' =>
                [
                    // Example 1: For valid userId
                    [   5048809,
                        ['groups' => [377604 => 'g160']],
                        $this->getGroupsDto(
                            [
                                0 =>
                                    ['groupId' => 377604,
                                        'groupName' => 'g160'
                                    ]
                            ]
                        )],
                    // Example 2: Test with null data
                    [   null,
                        ['groups' => []],
                        null],
                    // Example 3: For return multiple groups
                    [   5048809,
                        ['groups' => [377604 => 'g160',377605 => 'g161']],
                        $this->getGroupsDto(
                        [
                            0 =>
                                ['groupId' => 377604,
                                    'groupName' => 'g160'
                                ],
                            1 =>
                                ['groupId' => 377605,
                                    'groupName' => 'g161'
                                ],
                        ]
                    )]
                ]
        ]);
    }

    private function getGroupsDto($groupsData)
    {
        $groupsDtoArray = [];
        foreach ($groupsData as $group) {
            $groupsDto = new GroupsDto();
            $groupsDto->setGroupId($group['groupId']);
            $groupsDto->setGroupName($group['groupName']);
            $groupsDtoArray[] = $groupsDto;
        }
        return $groupsDtoArray;
    }
}