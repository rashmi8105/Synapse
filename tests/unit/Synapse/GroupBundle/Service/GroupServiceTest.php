<?php

namespace Synapse\GroupBundle\Service;

use Codeception\Specify;
use Synapse\CoreBundle\Entity\OrgGroup;
use Synapse\CoreBundle\Exception\DataProcessingExceptionHandler;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\OrgGroupRepository;
use Synapse\CoreBundle\Repository\OrgGroupStudentsRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\GroupService as CoreBundleGroupService;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\Service\Utility\EntityValidationService;
use Synapse\CoreBundle\Service\Utility\IDConversionService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\GroupBundle\DTO\GroupFacultyDTO;
use Synapse\GroupBundle\DTO\GroupFacultyInputDTO;
use Synapse\GroupBundle\DTO\GroupFacultyListInputDTO;
use Synapse\GroupBundle\DTO\GroupInputDTO;
use Synapse\GroupBundle\DTO\GroupPersonInputDto;
use Synapse\GroupBundle\DTO\OrgGroupDTO;
use Synapse\UploadBundle\Service\Impl\GroupUploadService;


class GroupServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    private $groupListWithSearchText = [
        [
            'group_internal_id' => 346984,
            'group_name' => "Test Group 00346984",
            'external_id' => "EXID00346984"
        ],
        [
            'group_internal_id' => 370411,
            'group_name' => "Test Group 00370411",
            'external_id' => "EXID00370411"
        ],
        [
            'group_internal_id' => 370412,
            'group_name' => "Test Group 00370412",
            'external_id' => "EXID00370412"
        ]
    ];

    private $groupListWithOutSearchText = [

        [
            'group_internal_id' => 345437,
            'group_name' => "All Students",
            'external_id' => "ALLSTUDENTS"
        ],
        [
            'group_internal_id' => 346984,
            'group_name' => "Test Group 00346984",
            'external_id' => "EXID00346984"
        ],
        [
            'group_internal_id' => 370411,
            'group_name' => "Test Group 00370411",
            'external_id' => "EXID00370411"
        ],
        [
            'group_internal_id' => 370412,
            'group_name' => "Test Group 00370412",
            'external_id' => "EXID00370412"
        ]
    ];

    private $groupStudents = [
        [
            "external_id" => "20162188N",
            "first_name" => "Katelyn",
            "last_name" => "Barnes",
            "primary_email" => "Katelyn.Barnes@mailinator.com"
        ],
        [
            "external_id" => "19696",
            "first_name" => "qqqq",
            "last_name" => "aaa",
            "primary_email" => "qqqqaaaa@mailinator.com"
        ]
    ];

    public function testGetGroupsByOrganization()
    {
        $this->specify("Test Get Groups By Organization", function ($organizationId, $searchText, $pageNumber, $recordsPerPage, $expectedTotalPages, $expectedGroupArray) {

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

            $mockOrgGroupRepository = $this->getMock('OrgGroupRepository', ['fetchOrgGroupTotalCount', 'getGroupsMetaData']);

            $mockOrgGroupRepository->method('fetchOrgGroupTotalCount')->willReturnCallback(function ($organizationId, $searchText) {

                if (is_null($searchText)) {
                    return ['total_groups' => 4];
                } else {
                    return ['total_groups' => 3];
                }

            });

            $mockOrgGroupRepository->method('getGroupsMetaData')->willReturnCallback(function ($organizationId, $searchText) {

                if (is_null($searchText)) {
                    $groupList = $this->groupListWithOutSearchText;
                } else {
                    $groupList = $this->groupListWithSearchText;
                }
                return $groupList;

            });

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    OrgGroupRepository::REPOSITORY_KEY,
                    $mockOrgGroupRepository
                ]
            ]);

            $groupService = new GroupService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $resultDto = $groupService->getGroupsByOrganization($organizationId, $searchText, $pageNumber, $recordsPerPage);

            verify($resultDto->getTotalPages())->equals($expectedTotalPages);
            verify($resultDto->getCurrentPage())->equals($pageNumber);
            verify($resultDto->getRecordsPerPage())->equals($recordsPerPage);
            verify($resultDto->getGroupList())->equals($expectedGroupArray);


        }, [
                'examples' => [
                    //Testing data with search filter passed in
                    [
                        2, "text", 1, 3, 1, $this->groupListWithSearchText
                    ],
                    //Testing data without search filter being passed in
                    [
                        2, null, 1, 4, 1, $this->groupListWithOutSearchText
                    ],
                    //Testing data with without search filter being passed in with records per page is 2 , expected total pages count now becomes 2.
                    [
                        2, null, 1, 2, 2, $this->groupListWithOutSearchText
                    ],
                    //Testing data with without search filter being passed in  with records per page is 3 , total pages count  now becomes 2.
                    [
                        2, null, 1, 3, 2, $this->groupListWithOutSearchText
                    ]
                ]
            ]
        );
    }


    public function testAddStudentsToGroup()
    {

        $this->specify('Test add Students to group', function ($groupPersonDto, $organizationId, $isValidGroup, $isValidStudent, $expectedResult) {

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


            $mockIdConversionService = $this->getMock('IDConversionService', ['convertPersonIds']);
            $mockIdConversionService->method('convertPersonIds')->willReturn(1);


            $mockOrgGroupRepository = $this->getMock('OrgGroupRepository', ['findOneBy']);
            if ($isValidGroup) {
                $mockOrgGroupRepository->method('findOneBy')->willReturn(1);
            } else {
                $invalidGroupException = new DataProcessingExceptionHandler();
                $invalidGroupException->addErrors("Not a Valid GroupId", "group_external_id");
                $mockOrgGroupRepository->method('findOneBy')->willThrowException($invalidGroupException);
            }


            $mockOrganizationEntity = $this->getMock('\Synapse\CoreBundle\Entity\Organization', ['getId']);
            $mockOrganizationEntity->method('getId')->willReturn(1);

            $mockPersonEntity = $this->getMock('\Synapse\CoreBundle\Entity\Person', ['getId', 'getOrganization']);
            $mockPersonEntity->method('getOrganization')->willReturn($mockOrganizationEntity);
            $mockPersonEntity->method('getId')->willReturn(1);

            $mockOrgPersonStudent = $this->getMock('\Synapse\CoreBundle\Entity\OrgPersonStudent', ['getPerson']);
            $mockOrgPersonStudent->method('getPerson')->willReturn($mockPersonEntity);

            $mockEntityValidationService = $this->getMock('EntityValidationService', ['validateDoctrineEntity']);
            $mockEntityValidationService->method('validateDoctrineEntity')->willReturn(null);

            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', ['findOneBy']);

            if ($isValidStudent) {
                $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($mockOrgPersonStudent);
            } else {

                $invalidStudentException = new DataProcessingExceptionHandler();
                $invalidStudentException->addErrors("Not a Valid Student", "person_external_id");
                $mockOrgPersonStudentRepository->method('findOneBy')->willThrowException($invalidStudentException);
            }

            $mockIdConversionService = $this->getMock('IdConversionService', ['convertPersonIds']);
            $mockIdConversionService->method('convertPersonIds')->willReturn([
                [1]
            ]);

            $mockApiValidationService = $this->getMock('ApiValidationService', ['updateOrganizationAPIValidationErrorCount']);
            $mockApiValidationService->method('updateOrganizationAPIValidationErrorCount')->willReturn(1);


            $mockDataProcessingUtilityService = $this->getMock('DataProcessingutilityService', ['setErrorMessageOrValueInArray']);
            $mockDataProcessingUtilityService->method('setErrorMessageOrValueInArray')->willReturnCallback(function ($records, $errorArray, $entityFieldMap) {

                foreach ($errorArray as $errorKey => $errorValue) {
                    if (array_key_exists($errorKey, $entityFieldMap)) {
                        $errorArray[$entityFieldMap[$errorKey]] = $errorValue;
                    }
                }

                $responseArray = [];
                foreach ($records as $key => $value) {
                    if (array_key_exists($key, $errorArray)) {
                        $responseArray[$key]['value'] = $value;
                        $responseArray[$key]['message'] = $errorArray[$key];
                    } else {
                        $responseArray[$key] = $value;
                    }
                }
                return $responseArray;

            });


            $mockGroupStudentRepository = $this->getMock('OrgGroupStudentRepository', ['persist']);
            $mockGroupStudentRepository->method('persist')->willReturn(1);

            $mockContainer->method('get')->willReturnMap([
                [
                    IDConversionService::SERVICE_KEY,
                    $mockIdConversionService
                ],
                [
                    EntityValidationService::SERVICE_KEY,
                    $mockEntityValidationService
                ],
                [
                    APIValidationService::SERVICE_KEY,
                    $mockApiValidationService
                ],
                [
                    DataProcessingUtilityService::SERVICE_KEY,
                    $mockDataProcessingUtilityService
                ]
            ]);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    OrgGroupRepository::REPOSITORY_KEY,
                    $mockOrgGroupRepository
                ],
                [
                    OrgPersonStudentRepository::REPOSITORY_KEY,
                    $mockOrgPersonStudentRepository
                ],
                [
                    OrgGroupStudentsRepository::REPOSITORY_KEY,
                    $mockGroupStudentRepository
                ]
            ]);

            $groupService = new GroupService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $resultArray = $groupService->addStudentsToGroup($groupPersonDto, $organizationId);
            verify($resultArray)->equals($expectedResult);

        }, [
            'examples' => [

                // Valid values for student and group, created successfully
                [
                    $this->createGroupPersonDto([
                        [
                            "group_external_id" => "ValidGroup",
                            "person_external_id" => "X10001"
                        ]
                    ]), 2, 1, 1, [

                    'data' => [
                        'created_count' => 1,
                        'created_records' => [
                            [
                                "group_external_id" => "ValidGroup",
                                "person_external_id" => "X10001"
                            ]
                        ]
                    ],
                    'error' => []

                ]
                ],
                //Testing with invalid Group
                [
                    $this->createGroupPersonDto([
                        [
                            "group_external_id" => "Invalid Group",
                            "person_external_id" => "X10001"
                        ]
                    ]), 2, 0, 1, [

                    'data' => [
                        'created_count' => 0,
                        'created_records' => [
                        ]
                    ],
                    'error' => [
                        [
                            "group_external_id" => [
                                'value' => "Invalid Group",
                                'message' => "Not a Valid GroupId"
                            ],
                            "person_external_id" => "X10001"
                        ]
                    ]
                ]
                ],
                //Testing with Invalid Student
                [
                    $this->createGroupPersonDto([
                        [
                            "group_external_id" => "ValidGroup",
                            "person_external_id" => "Invalid"
                        ]
                    ]), 2, 1, 0, [

                    'data' => [
                        'created_count' => 0,
                        'created_records' => [
                        ]
                    ],
                    'error' => [
                        [
                            "group_external_id" => "ValidGroup",
                            "person_external_id" => [
                                'value' => "Invalid",
                                'message' => "Not a Valid Student"
                            ]

                        ]
                    ]

                ]
                ]
            ]

        ]);
    }

    private function createGroupPersonDto($groupPersonArray)
    {

        $orgGroupPersonDto = new GroupPersonInputDto();
        $orgGroupPersonDto->setGroupPersonList($groupPersonArray);
        return $orgGroupPersonDto;
    }


    public function testFetchStudentsForGroup()
    {

        $this->specify("Test Get Groups By Organization", function ($groupEntity, $hasStudents, $expectedStudents) {

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


            $mockOrgGroupStudentsRepository = $this->getMock('OrgGroupStudentsRepository', ['listExternalIdsForStudentsInGroup']);

            $groupExternalId = $groupEntity->getExternalId();
            $groupName = $groupEntity->getGroupName();

            if ($hasStudents) {
                $mockOrgGroupStudentsRepository->method('listExternalIdsForStudentsInGroup')->willReturn(
                    $this->groupStudents
                );
            } else {
                $mockOrgGroupStudentsRepository->method('listExternalIdsForStudentsInGroup')->willReturn([]);
            }


            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    OrgGroupStudentsRepository::REPOSITORY_KEY,
                    $mockOrgGroupStudentsRepository
                ]
            ]);

            $groupService = new GroupService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $resultDto = $groupService->fetchStudentsForGroup($groupEntity);

            verify($resultDto)->isInstanceOf("\Synapse\GroupBundle\DTO\GroupStudentDto");
            verify($resultDto->getGroupExternalId())->equals($groupExternalId);
            verify($resultDto->getGroupName())->equals($groupName);
            verify($resultDto->getStudentList())->equals($expectedStudents);


        }, [
                'examples' => [
                    //Testing valid group with students in it
                    [
                        $this->mockGroupEntity("VALIDGROUP", "VALIDGROUPNAME", 1, 1), 1, $this->groupStudents
                    ],
                    //Testing Valid group for the organization without any students
                    [
                        $this->mockGroupEntity("VALIDGROUP", "VALIDGROUPNAME", 1, 1), 0, []
                    ],

                ]
            ]
        );

    }

    private function mockGroupEntity($groupExternalId, $groupName, $groupId, $organizationId)
    {

        $mockOrganization = $this->getmock('\Synapse\CoreBundle\Entity\Organization', ['getId']);
        $mockOrganization->method('getId')->willReturn($organizationId);


        $mockGroupObj = $this->getMock('\Synapse\CoreBundle\Entity\OrgGroup', ['getId', 'getGroupName', 'getExternalId', 'getOrganization', 'get']);
        $mockGroupObj->method('getId')->willReturn($groupId);
        $mockGroupObj->method('getGroupName')->willReturn($groupName);
        $mockGroupObj->method('getExternalId')->willReturn($groupExternalId);
        $mockGroupObj->method('getOrganization')->willReturn($mockOrganization);

        return $mockGroupObj;

    }


    public function testGetFacultyByGroup()
    {
        $this->specify("Test Get Faculty By Group", function ($organizationId, $groupId, $isInternal, $externalGroupId, $groupName, $isValidGroup, $groupFacultyList, $errorMessage) {

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
            //Repository Mocks
            $mockOrgGroupFacultyRepository = $this->getMock("OrgGroupFacultyRepository", ["getGroupStaffList"]);
            $mockOrgGroupRepository = $this->getMock("OrgGroupRepository", ["findOneBy"]);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgGroupFacultyRepository::REPOSITORY_KEY,
                        $mockOrgGroupFacultyRepository
                    ],
                    [
                        OrgGroupRepository::REPOSITORY_KEY,
                        $mockOrgGroupRepository
                    ]
                ]);
            if ($groupFacultyList) {
                $mockOrgGroupFacultyRepository->method('getGroupStaffList')->willReturn($groupFacultyList);
            } else {
                $mockOrgGroupFacultyRepository->method('getGroupStaffList')->willReturn([]);
            }
            if ($isValidGroup) {
                $mockOrgGroupObject = $this->getMock('OrgGroup', ['getId', 'getExternalId', 'getGroupName']);
                $mockOrgGroupObject->method('getId')->willReturn($groupId);
                $mockOrgGroupObject->method('getExternalId')->willReturn($externalGroupId);
                $mockOrgGroupObject->method('getGroupName')->willReturn($groupName);
                $mockOrgGroupRepository->method('findOneBy')->willReturn($mockOrgGroupObject);
            }
            $groupObject = new OrgGroup();
            $groupObject->setGroupName($groupName);
            $groupObject->setExternalId($externalGroupId);
            $groupService = new GroupService($mockRepositoryResolver, $mockContainer, $mockLogger);
            try {
                $result = $groupService->getFacultyByGroup($organizationId, $groupObject, $isInternal);
                $expectedResult = $this->getGroupFacultyDTO($groupFacultyList, $externalGroupId, $groupName);
                $this->assertEquals($result, $expectedResult);
            } catch (SynapseValidationException $e) {
                $errorResult = $e->getMessage();
                $this->assertEquals($errorResult, $errorMessage);
            }
        }, [
                'examples' =>
                    [
                        //Case 1 : Valid group with faculty list
                        [
                            2,
                            15,
                            false,
                            "grp15",
                            "Group 15",
                            true,
                            [
                                0 =>
                                    [
                                        "mapworks_internal_id" => 107,
                                        "faculty_external_id" => "20160158",
                                        "firstname" => "Mark",
                                        "lastname" => "Wissinger",
                                        "primary_email" => "Mark.WissingerTestRpt@mailinator.com",
                                        "is_invisible" => 0,
                                        "permissionset_name" => "AllAccess"
                                    ],
                                1 =>
                                    [
                                        "mapworks_internal_id" => 134,
                                        "faculty_external_id" => "20160198",
                                        "firstname" => "Tony",
                                        "lastname" => "Hall",
                                        "primary_email" => "Tony.HallTestRpt@mailinator.com",
                                        "is_invisible" => 0,
                                        "permissionset_name" => "AggregateOnly"
                                    ]
                            ],
                            null
                        ],
                        //Case 2 : Valid group with no faculty , returns empty faculty list
                        [
                            2,
                            20,
                            false,
                            true,
                            "grp20",
                            "Group20",
                            [],
                            null
                        ]
                    ]
            ]
        );
    }

    private function getGroupFacultyDTO($groupFacultyList, $externalGroupId, $groupName)
    {
        $groupFacultyDTO = new GroupFacultyDTO();
        $groupFacultyDTO->setGroupExternalId($externalGroupId);
        $groupFacultyDTO->setGroupName($groupName);
        $groupFacultyDTO->setFacultyList($groupFacultyList);
        return $groupFacultyDTO;
    }

    public function testDeleteStudentFromGroup()
    {
        $this->specify("Test Delete Student From Group", function ($groupExternalId, $studentExternalId, $organizationId, $errorMessage, $expectedResult) {


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

            //Repository mocking

            $mockOrgGroupRepository = $this->getMock('OrgGroupRepository', ['findOneBy']);
            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', ['findOneBy']);
            $mockOrgGroupStudentsRepository = $this->getMock('OrgGroupStudentsRepository', ['findOneBy', 'delete']);

            //Service mocking

            $mockIdConversionService = $this->getMock('IdConversionService', ['convertPersonIds']);
            $mockGroupUploadService = $this->getMock('GroupUploadService', ['updateDataFile']);
            $mockApiValidationService = $this->getMock('ApiValidationService', ['updateOrganizationAPIValidationErrorCount']);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    OrgGroupRepository::REPOSITORY_KEY,
                    $mockOrgGroupRepository
                ],
                [
                    OrgPersonStudentRepository::REPOSITORY_KEY,
                    $mockOrgPersonStudentRepository
                ],
                [
                    OrgGroupStudentsRepository::REPOSITORY_KEY,
                    $mockOrgGroupStudentsRepository
                ]
            ]);

            $mockContainer->method('get')->willReturnMap([
                [
                    IDConversionService::SERVICE_KEY,
                    $mockIdConversionService
                ],
                [
                    GroupUploadService::SERVICE_KEY,
                    $mockGroupUploadService
                ],
                [
                    APIValidationService::SERVICE_KEY,
                    $mockApiValidationService
                ]
            ]);

            $mockOrgGroup = $this->getMock('OrgGroup', ['getExternalId', 'getOrganization']);
            if ($errorMessage && ($errorMessage == 'invalid_group')) {
                $mockOrgGroupRepository->method('findOneBy')->willReturn(null);
            } else {
                $mockOrgGroupRepository->method('findOneBy')->willReturn($mockOrgGroup);
            }

            $mockIdConversionService->method('convertPersonIds')->willReturn([12]);

            $mockOrgPersonStudent = $this->getMock('OrgPersonStudent', ['getPerson', 'getOrganization']);
            if ($errorMessage && ($errorMessage == 'invalid_student')) {
                $mockOrgPersonStudentRepository->method('findOneBy')->willReturn(null);
            } else {
                $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($mockOrgPersonStudent);
            }

            $mockOrgGroupStudent = $this->getMock('OrgGroupStudent', ['getId']);
            if ($errorMessage && ($errorMessage == 'student_not_member')) {
                $mockOrgGroupStudentsRepository->method('findOneBy')->willReturn(null);
            } else {
                $mockOrgGroupStudentsRepository->method('findOneBy')->willReturn($mockOrgGroupStudent);
            }
            try {
                $groupService = new GroupService($mockRepositoryResolver, $mockContainer, $mockLogger);
                $result = $groupService->deleteStudentFromGroup($groupExternalId, $studentExternalId, $organizationId);
                $this->assertEquals($result, $expectedResult);
            } catch (SynapseValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        },
            [
                'examples' =>
                    [
                        //Test1: If valid group with valid student and student is member of the group, returns null
                        [
                            "G0097",
                            "P978",
                            77,
                            null,
                            null
                        ],
                        //Test2: If invalid group , throws invalid group exception
                        [
                            "G0585",
                            "P565",
                            77,
                            'invalid_group',
                            'Group is not valid for current organization'
                        ],
                        //Test3: If invalid student , throws invalid student exception
                        [
                            "G0012",
                            "P0015",
                            77,
                            'invalid_student',
                            'Student is not valid for current organization'
                        ],
                        //Test4: If valid group with valid student but student is not a member of the group, throws student not member exception
                        [
                            "G0018",
                            "P6005",
                            77,
                            'student_not_member',
                            'Student is not associated with the group'
                        ]
                    ]
            ]
        );
    }

    public function testDeleteFacultyFromGroup()
    {
        $this->specify("Test Delete Faculty From Group ", function ($personId, $groupId, $organizationId, $isMember) {
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

            //Repository Mocks
            $mockOrgGroupFacultyRepository = $this->getMock('OrgGroupFacultyRepository', ['findOneBy', 'delete']);

            //Services Mocks
            $mockCoreBundleGroupService = $this->getMock('CoreBundleGroupService', ['startUpdateSubGroupList']);


            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgGroupFacultyRepository::REPOSITORY_KEY,
                        $mockOrgGroupFacultyRepository
                    ]
                ]);
            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        CoreBundleGroupService::SERVICE_KEY,
                        $mockCoreBundleGroupService
                    ]
                ]);
            if ($isMember) {

                $mockOrgGroupFacultyObject = $this->getMock('OrgGroupFaculty', ['getId']);
                $mockOrgGroupFacultyRepository->method('findOneBy')->willReturn($mockOrgGroupFacultyObject);
            }
            $groupService = new GroupService($mockRepositoryResolver, $mockContainer, $mockLogger);

            try {
                $result = $groupService->deleteFacultyFromGroup($personId, $groupId, $organizationId);
                $this->assertEquals($result, true);

            } catch (SynapseValidationException $e) {
                $this->assertEquals($e->getMessage(), 'Faculty is not a member of the group');
            }

        }, [
                'examples' =>
                    [
                        //Case1: Faculty is not mapped to group , throws exception
                        [
                            12,
                            45,
                            2,
                            false
                        ],
                        //Case2: Faculty is successfully deleted from the group
                        [
                            12,
                            48,
                            2,
                            true
                        ]
                    ]
            ]
        );
    }

    public function testCreateGroups()
    {
        $this->specify("Test Create Groups for organization", function ($groupInputDto, $organization, $errorInEntity, $expectedResult) {


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

            $mockContainerForEntityService = $this->getMock('Container', array(
                'get'
            ));

            $mockValidator = $this->getMock('Validator', ['validate']);

            if (!empty($errorInEntity)) {
                $mockValidator->method('validate')->willReturn($this->buildArrayOFErrorObjects($errorInEntity));
            }
            $mockContainerForEntityService->method('get')->willReturnMap([
                [
                    SynapseConstant::VALIDATOR,
                    $mockValidator
                ]
            ]);

            $mockEntityValidationService = new EntityValidationService($mockRepositoryResolver, $mockLogger, $mockContainerForEntityService);
            $mockApiValidationService = $this->getMock('ApiValidationService', ['updateOrganizationAPIValidationErrorCount']);
            $mockDataProcessingUtilityService = $this->getMock('DataProcessingUtilityService', ['setErrorMessageOrValueInArray']);

            $mockDataProcessingUtilityService->method('setErrorMessageOrValueInArray')->willReturnCallback(function ($records, $errorArray, $entityFieldMap = []) {

                foreach ($errorArray as $errorKey => $errorValue) {
                    if (array_key_exists($errorKey, $entityFieldMap)) {
                        $errorArray[$entityFieldMap[$errorKey]] = $errorValue;
                    }
                }

                $responseArray = [];
                foreach ($records as $key => $value) {
                    if (array_key_exists($key, $errorArray)) {
                        $responseArray[$key]['value'] = $value;
                        $responseArray[$key]['message'] = $errorArray[$key];
                    } else {
                        $responseArray[$key] = $value;
                    }
                }
                return $responseArray;
            });


            $mockOrgGroupRepository = $this->getMock('OrgGroupRepository', ['persist']);
            $mockContainer->method('get')->willReturnMap([
                [
                    EntityValidationService::SERVICE_KEY,
                    $mockEntityValidationService
                ],
                [
                    APIValidationService::SERVICE_KEY,
                    $mockApiValidationService
                ],
                [
                    DataProcessingUtilityService::SERVICE_KEY,
                    $mockDataProcessingUtilityService
                ]
            ]);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    OrgGroupRepository::REPOSITORY_KEY,
                    $mockOrgGroupRepository
                ]

            ]);

            $groupService = new GroupService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $result = $groupService->createGroups($groupInputDto, $organization);
            verify($result)->equals($expectedResult);

        }, [
                'examples' => [
                    //Testing vit a valid group
                    [
                        $this->createGroupInputDto(
                            [
                                [
                                    'external_id' => "groupExternalId",
                                    'group_name' => "groupName"
                                ]
                            ]
                        ), $this->createOrganizationEntity(2), [],
                        [
                            'data' => [
                                'created_count' => 1,
                                'created_records' => [
                                    $this->createGroupDto("groupExternalId", "groupName")
                                ]
                            ],
                            'errors' => []
                        ]
                    ],
                    // Testing with Invalid external id for the group
                    [
                        $this->createGroupInputDto(
                            [
                                [
                                    'external_id' => "Invalid ExternalId",
                                    'group_name' => "groupName",
                                ]
                            ]
                        ), $this->createOrganizationEntity(2), ['externalId' => "Invalid ExternalId"],
                        [
                            'data' => [
                                'created_count' => 0,
                                'created_records' => []
                            ],
                            'errors' => [
                                [
                                    'external_id' => [
                                        'value' => "Invalid ExternalId",
                                        'message' => "Invalid ExternalId"
                                    ],
                                    'group_name' => "groupName"
                                ]
                            ]
                        ]
                    ],
                    // Testing with Invalid  group name
                    [
                        $this->createGroupInputDto(
                            [
                                [
                                    'external_id' => "groupExternalId",
                                    'group_name' => "",
                                ]
                            ]
                        ), $this->createOrganizationEntity(2), ['groupName' => "Invalid GroupName"],
                        [
                            'data' => [
                                'created_count' => 0,
                                'created_records' => []
                            ],
                            'errors' => [
                                [
                                    'group_name' => [
                                        'value' => "",
                                        'message' => "Invalid GroupName"
                                    ],
                                    'external_id' => "groupExternalId"
                                ]
                            ]
                        ]
                    ]]

            ]
        );
    }

    public function createGroupInputDto($groupArray)
    {

        $groupInputDto = new GroupInputDTO();
        $groupDtoArr = [];
        foreach ($groupArray as $group) {

            $orgGroupDto = $this->createGroupDto($group['external_id'], $group['group_name']);
            $groupDtoArr[] = $orgGroupDto;

        }
        $groupInputDto->setGroupList($groupDtoArr);
        return $groupInputDto;
    }


    private function createGroupDto($externalId, $groupName, $mapworksInternalId = null)
    {

        $orgGroupDto = new OrgGroupDTO();
        $orgGroupDto->setExternalId($externalId);
        $orgGroupDto->setGroupName($groupName);
        $orgGroupDto->setMapworksInternalId($mapworksInternalId);
        return $orgGroupDto;
    }

    public function createOrganizationEntity($organziationId)
    {
        $mockOrganization = $this->getMock('Synapse\CoreBundle\Entity\Organization', ['getId']);
        $mockOrganization->method('getId')->willReturn($organziationId);
        return $mockOrganization;
    }

    public function buildArrayOFErrorObjects($errorArray)
    {
        $errorObjectArray = [];
        foreach ($errorArray as $errorKey => $errorValue) {

            $mockValidatorConstraint = $this->getMock('ConstrainValidator', ['getPropertyPath', 'getMessage']);
            $mockValidatorConstraint->method('getPropertyPath')->willReturn($errorKey);
            $mockValidatorConstraint->method('getMessage')->willReturn($errorValue);
            $errorObjectArray[] = $mockValidatorConstraint;
        }
        return $errorObjectArray;
    }

    public function testAddFacultiesToGroups()
    {
        $this->specify("Test Add Faculties To Groups ", function ($organizationId, $groupFacultyListInputDTO, $case, $errorArray, $expectedResult) {


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

            //Repository Mocks
            $mockOrgGroupRepository = $this->getMock('OrgGroupRepository', ['findOneBy']);
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', ['findOneBy']);
            $mockOrgGroupFacultyRepository = $this->getMock('OrgGroupFacultyRepository', ['findOneBy', 'persist']);
            $mockOrgPermissionsetRepository = $this->getMock('OrgPermissionsetRepository', ['findOneBy']);
            $mockPersonRepository = $this->getMock('PersonRepository', ['findOneBy']);

            //Services Mocks
            $mockApiValidationService = $this->getMock('ApiValidationService', ['updateOrganizationAPIValidationErrorCount']);
            $mockEntityValidationService = $this->getMock('EntityValidationService', ['validateDoctrineEntity']);
            $mockCoreBundleGroupService = $this->getMock('CoreBundleGroupService', ['startUpdateSubGroupList']);
            $mockDataProcessingUtilityService = $this->getMock('DataProcessingUtilityService', ['setErrorMessageOrValueInArray']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgGroupRepository::REPOSITORY_KEY,
                        $mockOrgGroupRepository
                    ],
                    [
                        OrgPersonFacultyRepository::REPOSITORY_KEY,
                        $mockOrgPersonFacultyRepository
                    ],
                    [
                        OrgGroupFacultyRepository::REPOSITORY_KEY,
                        $mockOrgGroupFacultyRepository
                    ],
                    [
                        OrgPermissionsetRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetRepository
                    ],
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ]
                ]);
            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        APIValidationService::SERVICE_KEY,
                        $mockApiValidationService
                    ],
                    [
                        EntityValidationService::SERVICE_KEY,
                        $mockEntityValidationService
                    ],
                    [
                        CoreBundleGroupService::SERVICE_KEY,
                        $mockCoreBundleGroupService
                    ],
                    [
                        DataProcessingUtilityService::SERVICE_KEY,
                        $mockDataProcessingUtilityService
                    ],
                ]);

            $groupFacultyList = $groupFacultyListInputDTO->getGroupFacultyList();

            foreach ($groupFacultyList as $groupFacultyDTO) {
                $groupFaculty['group_id'] = $groupFacultyDTO->getGroupId();
                $groupFaculty['faculty_id'] = $groupFacultyDTO->getFacultyId();
                $groupFaculty['permissionset_name'] = $groupFacultyDTO->getPermissionsetName();
                $groupFaculty['is_invisible'] = $groupFacultyDTO->getIsInvisible();
            }

            $mockOrgGroupObject = $this->getMock('\Synapse\CoreBundle\Entity\OrgGroup', ['getId']);
            $mockOrgGroupObject->method('getId')->willReturn(1);

            if ($case == 'invalid-group') {
                $mockOrgGroupRepository->method('findOneBy')->willThrowException(new DataProcessingExceptionHandler("Not a Valid GroupID"));
            } else {
                $mockOrgGroupRepository->method('findOneBy')->willReturn($mockOrgGroupObject);
            }

            $mockPersonObject = $this->getMock('\Synapse\CoreBundle\Entity\Person', ['getId']);
            $mockPersonObject->method('getId')->willReturn(1);

            $mockOrganizationObject = $this->getMock('\Synapse\CoreBundle\Entity\Organization', ['getId']);
            $mockOrganizationObject->method('getId')->willReturn(2);

            $mockPersonRepository->method('findOneBy')->willReturn($mockPersonObject);

            if ($case == 'invalid-faculty') {
                $mockOrgPersonFacultyRepository->method('findOneBy')->willThrowException(new DataProcessingExceptionHandler("Not a Valid FacultyID"));
            } else {
                $mockOrgPersonFacultyObject = $this->getMock('\Synapse\CoreBundle\Entity\OrgPersonFaculty', ['getPerson', 'getOrganization', 'getStatus']);
                $mockOrgPersonFacultyObject->method('getPerson')->willReturn($mockPersonObject);
                $mockOrgPersonFacultyObject->method('getOrganization')->willReturn($mockOrganizationObject);
                if ($case == 'inactive-faculty') {
                    $mockOrgPersonFacultyObject->method('getStatus')->willReturn(0);
                } else {
                    $mockOrgPersonFacultyObject->method('getStatus')->willReturn(1);
                }

                $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonFacultyObject);
            }

            if ($case == 'faculty-already-member') {
                $mockOrgGroupFacultyObject = $this->getMock('OrgGroupFaculty', ['getId']);
                $mockOrgGroupFacultyObject->method('getId')->willReturn(1);
                $mockOrgGroupFacultyRepository->method('findOneBy')->willReturn($mockOrgGroupFacultyObject);
            } else {
                $mockOrgGroupFacultyRepository->method('findOneBy')->willReturn(null);
            }

            if ($case == 'invalid-permissionset') {
                $mockOrgPermissionsetRepository->method('findOneBy')->willReturn(null);
            } else {
                $mockOrgPermissionsetObject = $this->getMock('\Synapse\CoreBundle\Entity\OrgPermissionset', ['getName']);
                $mockOrgPermissionsetObject->method('getName')->willReturn($groupFaculty['permissionset_name']);
                $mockOrgPermissionsetRepository->method('findOneBy')->willReturn($mockOrgPermissionsetObject);
            }

            $error = $this->setErrorMessageOrValueInArray($groupFaculty, $errorArray);
            $mockDataProcessingUtilityService->method('setErrorMessageOrValueInArray')->willReturn($error);

            $groupService = new GroupService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $result = $groupService->addFacultiesToGroups($organizationId, $groupFacultyListInputDTO);
            $this->assertEquals($result, $expectedResult);
        },
            [
                'examples' =>
                    [
                        //case0 : If faculty Id is in active, error array contains error and created is empty
                        [
                            2,
                            $this->getGroupFacultyInputDTO("grp20", "fac31", "per255", false),
                            "inactive-faculty",
                            [
                                "faculty_id" => "Faculty Id is not active at the organization",
                                "type" => "required"
                            ],
                            [
                                "data" =>
                                    [
                                        "created_count" => 0,
                                        "created_records" => []
                                    ],
                                "errors" =>
                                    [
                                        "error_count" => 1,
                                        "error_records" =>
                                            [
                                                [
                                                    "group_id" => "grp20",
                                                    "faculty_id" =>
                                                        [
                                                            "value" => "fac31",
                                                            "message" => "Faculty Id is not active at the organization",
                                                        ],
                                                    "permissionset_name" => "per255",
                                                    "is_invisible" => false
                                                ]
                                            ]

                                    ]
                            ]
                        ],
                        //case1 : If faculty Id is invalid, error array contains error and created is empty
                        [
                            2,
                            $this->getGroupFacultyInputDTO("grp20", "fac31", "per255", false),
                            "invalid-faculty",
                            [
                                "faculty_id" => "Not a Valid FacultyID",
                                "type" => null
                            ],
                            [
                                "data" =>
                                    [
                                        "created_count" => 0,
                                        "created_records" => []
                                    ],
                                "errors" =>
                                    [
                                        "error_count" => 1,
                                        "error_records" =>
                                            [
                                                [
                                                    "group_id" => "grp20",
                                                    "faculty_id" =>
                                                        [
                                                            "value" => "fac31",
                                                            "message" => "Not a Valid FacultyID",
                                                        ],
                                                    "permissionset_name" => "per255",
                                                    "is_invisible" => false
                                                ]
                                            ]

                                    ]
                            ]
                        ],
                        //case2 : If group Id is invalid, error array contains error and created is empty
                        [
                            2,
                            $this->getGroupFacultyInputDTO("grp26", "fac42", "per255", false),
                            "invalid-group",
                            [
                                "faculty_id" => "Not a Valid GroupID",
                                "type" => null
                            ],
                            [
                                "data" =>
                                    [
                                        "created_count" => 0,
                                        "created_records" => []
                                    ],
                                "errors" =>
                                    [
                                        "error_count" => 1,
                                        "error_records" =>
                                            [
                                                [
                                                    "group_id" => "grp26",
                                                    "faculty_id" =>
                                                        [
                                                            "value" => "fac42",
                                                            "message" => "Not a Valid GroupID",
                                                        ],
                                                    "permissionset_name" => "per255",
                                                    "is_invisible" => false
                                                ]
                                            ]

                                    ]
                            ]
                        ],
                        //Case3 : If optional field is invalid , group faculty will be created with error array
                        [
                            2,
                            $this->getGroupFacultyInputDTO("grp75", "fac32", "per88", false),
                            "invalid-permissionset",
                            [
                                "permissionset_name" => "Permissionset Name is not valid",
                                "type" => null
                            ],
                            [
                                "data" =>
                                    [
                                        "created_count" => 1,
                                        "created_records" =>
                                            [
                                                $this->getGroupFacultyInputDTO("grp75", "fac32", "per88", false, true)
                                            ]
                                    ],
                                "errors" =>
                                    [
                                        "error_count" => 1,
                                        "error_records" =>
                                            [
                                                [
                                                    "group_id" => "grp75",
                                                    "faculty_id" => "fac32",
                                                    "permissionset_name" =>
                                                        [
                                                            "value" => "per88",
                                                            "message" => "Permissionset Name is not valid"
                                                        ],
                                                    "is_invisible" => false
                                                ]
                                            ]
                                    ]
                            ]
                        ]
                    ]
            ]
        );
    }

    private function getGroupFacultyInputDTO($groupExternalId, $facultyExternalId, $permissionsetName, $isInvisible, $returnInputDTO = false)
    {

        $groupFacultyInputDTO = new GroupFacultyInputDTO();

        $groupFacultyInputDTO->setGroupId($groupExternalId);
        $groupFacultyInputDTO->setFacultyId($facultyExternalId);
        $groupFacultyInputDTO->setPermissionsetName($permissionsetName);
        $groupFacultyInputDTO->setIsInvisible($isInvisible);

        $groupFacultyListInputDTO = new GroupFacultyListInputDTO();
        $groupFacultyListInputDTO->setGroupFacultyList([$groupFacultyInputDTO]);
        if ($returnInputDTO) {
            return $groupFacultyInputDTO;
        }
        return $groupFacultyListInputDTO;
    }

    private function setErrorMessageOrValueInArray($records, $errorArray)
    {
        $responseArray = [];
        foreach ($records as $key => $value) {
            if (array_key_exists($key, $errorArray)) {
                $responseArray[$key]['value'] = $value;
                $responseArray[$key]['message'] = $errorArray[$key];
            } else {
                $responseArray[$key] = $value;
            }
        }
        return $responseArray;
    }

    public function testUpdateGroups()
    {
        $this->specify("Test Update Groups for organization", function ($groupInputDto, $organization, $errorType, $errorInEntity, $expectedResult) {

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


            $mockOrgGroupRepository = $this->getMock('OrgGroupRepository', ['persist', 'findOneBy']);
            $mockEntityValidationService = $this->getMock('EntityValidationService', ['validateDoctrineEntity']);
            $mockApiValidationService = $this->getMock('ApiValidationService', ['updateOrganizationAPIValidationErrorCount']);
            $mockDataProcessingUtilityService = $this->getMock('DataProcessingUtilityService', ['setErrorMessageOrValueInArray']);
            $mockCoreBundleGroupService = $this->getMock('CoreBundleGroupService', ['startUpdateSubGroupList']);


            $mockContainer->method('get')->willReturnMap([
                [
                    EntityValidationService::SERVICE_KEY,
                    $mockEntityValidationService
                ],
                [
                    APIValidationService::SERVICE_KEY,
                    $mockApiValidationService
                ],
                [
                    DataProcessingUtilityService::SERVICE_KEY,
                    $mockDataProcessingUtilityService
                ],
                [
                    CoreBundleGroupService::SERVICE_KEY,
                    $mockCoreBundleGroupService
                ]
            ]);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    OrgGroupRepository::REPOSITORY_KEY,
                    $mockOrgGroupRepository
                ]
            ]);
            $groupList = $groupInputDto->getGroupList();
            foreach ($groupList as $group) {
                $groupArray['external_id'] = $group->getExternalId();
                $groupArray['group_name'] = $group->getGroupName();
            }

            $mockOrgGroup = $this->getMock('OrgGroup', ['getId', 'getExternalId', 'getGroupName', 'setExternalId', 'setGroupName']);
            $mockOrgGroup->method('getId')->willReturn(5);
            if ($errorType == "skipped_case") {
                $mockOrgGroup->method('getGroupName')->willReturn($groupArray['group_name']);
            }
            if ($errorType == "invalid_group") {
                $mockOrgGroupRepository->method('findOneBy')->willThrowException(new DataProcessingExceptionHandler());
            } else {
                $mockOrgGroupRepository->method('findOneBy')->willReturn($mockOrgGroup);
            }

            if ($errorInEntity) {
                $error = $this->setErrorMessageOrValueInArray($groupArray, $errorInEntity);
                $mockDataProcessingUtilityService->method('setErrorMessageOrValueInArray')->willReturn($error);
            }
            $groupService = new GroupService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $result = $groupService->updateGroups($groupInputDto, $organization);
            verify($result)->equals($expectedResult);
        },
            [
                'examples' =>
                    [
                        //Case1: If group data is valid, updated array gets value
                        [
                            $this->createGroupInputDto(
                                [
                                    [
                                        'external_id' => "grp202",
                                        'group_name' => "GRP202",
                                    ]
                                ]
                            ),
                            $this->createOrganizationEntity(2),
                            null,
                            [],
                            [
                                "data" =>
                                    [
                                        "updated_count" => 1,
                                        "updated_records" =>
                                            [
                                                $this->createGroupDto("grp202", "GRP202", 5)
                                            ],
                                        "skipped_count" => 0,
                                        "skipped_records" => []
                                    ],
                                "errors" =>
                                    [
                                        "error_count" => 0,
                                        "error_records" => []
                                    ]
                            ]
                        ],
                        //Case2: If group external id invalid, error array gets value
                        [
                            $this->createGroupInputDto(
                                [
                                    [
                                        'external_id' => "grp204",
                                        'group_name' => "GRP204",
                                    ]
                                ]
                            ),
                            $this->createOrganizationEntity(2),
                            "invalid_group",
                            [
                                "external_id" => "Not a Valid Id",
                                "type" => null
                            ],
                            [
                                "data" =>
                                    [
                                        "updated_count" => 0,
                                        "updated_records" => [],
                                        "skipped_count" => 0,
                                        "skipped_records" => []
                                    ],
                                "errors" =>
                                    [
                                        "error_count" => 1,
                                        "error_records" => [
                                            [
                                                "external_id" =>
                                                    [
                                                        "value" => "grp204",
                                                        "message" => "Not a Valid Id"
                                                    ],
                                                "group_name" => "GRP204"
                                            ]
                                        ]
                                    ]
                            ]
                        ],
                        //Case 3 : If groupname same as before, skipped array gets value
                        [
                            $this->createGroupInputDto(
                                [
                                    [
                                        'external_id' => "grp500",
                                        'group_name' => "GRP500",
                                    ]
                                ]
                            ),
                            $this->createOrganizationEntity(2),
                            "skipped_case",
                            [],
                            [
                                "data" =>
                                    [
                                        "updated_count" => 0,
                                        "updated_records" => [],
                                        "skipped_count" => 1,
                                        "skipped_records" => [
                                            $this->createGroupDto("grp500", "GRP500")
                                        ]
                                    ],
                                "errors" =>
                                    [
                                        "error_count" => 0,
                                        "error_records" => []
                                    ]
                            ]
                        ]
                    ]
            ]
        );
    }
}

