<?php
namespace tests\unit\Synapse\CoreBundle\Service\Util;

use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgGroupRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\Service\Utility\IDConversionService;


class IDConversionServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    use \Codeception\Specify;


    // tests
    public function testConvertPersonIds()
    {
        $this->specify("Test to convert person id from internal to external or opposite", function ($personIds, $organizationId, $isInternal, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockPersonRepository = $this->getMock('PersonRepository', array('find', 'findOneBy'));
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', array('find'));

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository]
            ]);

            $organizationObject = $this->getMock('PersonObject', array('getOrganization', 'getId'));
            $personObject = $this->getMock('Person', array('findOneBy', 'getId', 'getOrganization', 'getExternalId'));

            if ($isInternal) {
                $mockPersonRepository->expects($this->at(0))->method('find')->willReturn($personObject);
                $personObject->expects($this->at(1))->method('getExternalId')->willReturn($personIds);
            } else {
                $mockPersonRepository->expects($this->at(0))->method('findOneBy')->willReturn($personObject);
                $personObject->expects($this->at(1))->method('getId')->willReturn($personIds);
            }

            $personObject->expects($this->at(0))->method('getOrganization')->willReturn($organizationObject);
            $organizationObject->expects($this->at(0))->method('getId')->willReturn($organizationId);

            $idConversionService = new IDConversionService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $idConversionService->convertPersonIds($personIds, $organizationId, $isInternal);
            $this->assertEquals($expectedResult, $result);

        }, ['examples' => [
            [
                // Convert single internal person id to external person id
                '4878750',
                203,
                true,
                ['4878750']
            ],
            [
                // Convert list of internal person id to external id
                '4878751, 4878808, 4878809',
                203,
                true,
                [
                    '4878751, 4878808, 4878809'
                ]
            ],
            [
                //Convert external person id to internal person id
                '4878751, 4878808, 4878809',
                203,
                false,
                [
                    '4878751, 4878808, 4878809'
                ]
            ],
            [
                // convert external to internal for a single person
                '4878750',
                203,
                true,
                [
                    '4878750'
                ]
            ]
        ]]);
    }

    public function testConvertCourseIDs()
    {
        $this->specify("Test to convert course id from internal to external or opposite", function ($courseIds, $organizationId, $isInternal, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockCourseRepository = $this->getMock('OrgCoursesRepository', array('find', 'findOneBy'));
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', array('find'));

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [OrgCoursesRepository::REPOSITORY_KEY, $mockCourseRepository],
                [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository]
            ]);

            $organizationObject = $this->getMock('CourseObject', array('getOrganization', 'getId'));
            $courseObject = $this->getMock('OrgCourses', array('findOneBy', 'getId', 'getOrganization', 'getExternalId'));

            if ($isInternal) {
                $mockCourseRepository->expects($this->at(0))->method('find')->willReturn($courseObject);
                $courseObject->expects($this->at(1))->method('getExternalId')->willReturn($courseIds);
            } else {
                $mockCourseRepository->expects($this->at(0))->method('findOneBy')->willReturn($courseObject);
                $courseObject->expects($this->at(1))->method('getId')->willReturn($courseIds);
            }

            $courseObject->expects($this->at(0))->method('getOrganization')->willReturn($organizationObject);
            $organizationObject->expects($this->at(0))->method('getId')->willReturn($organizationId);

            $idConversionService = new IDConversionService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $idConversionService->convertCourseIDs($courseIds, $organizationId, $isInternal);
            $this->assertEquals($expectedResult, $result);

        }, ['examples' => [
            [
                // Convert external course id to internal for list of course
                '100018,100019',
                203,
                false,
                [
                    '100018,100019'
                ]
            ],
            [
                // Convert internal course id to external for single course
                '100001',
                203,
                true,
                [
                    '100001'
                ]
            ],
            [
                // Convert external course id to internal for single course
                '100035',
                203,
                false,
                [
                    '100035'
                ]
            ],
            [
                // Convert internal course id to external for multiple course
                '100130,100042,100043',
                203,
                true,
                [
                    '100130,100042,100043'
                ]
            ]
        ]]);
    }

    public function testGetConvertedGroupObjects()
    {
        $this->specify("Test to Get Converted Group Objects", function ($groupIdArray, $organizationId, $isInternalIds, $convertedGroupId, $isGroupExist, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            //Mocking Services
            $mockApiValidationService = $this->getMock('ApiValidationService', ['addErrorsToOrganizationAPIErrorCount']);

            //Mocking Repositories
            $mockOrganizationGroupRepository = $this->getMock('OrgGroup',['findOneBy','find']);

            $mockContainer->method('get')->willReturnMap([
                [
                    APIValidationService::SERVICE_KEY,
                    $mockApiValidationService
                ]
            ]);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    OrgGroupRepository::REPOSITORY_KEY,
                    $mockOrganizationGroupRepository
                ]
            ]);

            $mockOrganization = $this->getMock('Organization', ['getId']);
            $mockOrganization->method('getId')->willReturn($organizationId);
            $mockOrgGroup = $this->getMock('OrgGroup', ['getId', 'getOrganization']);
            $mockOrgGroup->method('getOrganization')->willReturn($mockOrganization);
            $mockOrgGroup->method('getId')->willReturn($convertedGroupId);

            if(!$isGroupExist) {
                $validationErrorString = 'Group ID '.$groupIdArray[0].' is not valid at the organization.';
                $mockApiValidationService->method('addErrorsToOrganizationAPIErrorCount')->willThrowException(new SynapseValidationException($validationErrorString));
            }

            if ($isInternalIds) {
                if ($isGroupExist) {
                    $mockOrganizationGroupRepository->method('find')->willReturn($mockOrgGroup);
                } else {
                    $mockOrganizationGroupRepository->method('find')->willReturn(null);
                }
            } else {
                if ($isGroupExist) {
                    $mockOrganizationGroupRepository->method('findOneBy')->willReturn($mockOrgGroup);
                } else {
                    $mockOrganizationGroupRepository->method('findOneBy')->willReturn(null);
                }
            }


            try {

                $idConversionService = new IDConversionService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $idConversionService->getConvertedGroupObjects($groupIdArray, $organizationId, $isInternalIds);
                $this->assertEquals($expectedResult, $result);

            } catch (SynapseValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        },
            ['examples' =>
                [
                    //Test 1 : If $groupIdArray having group external_id, returns array of internal ids for all
                    [
                        [
                            "grp22"
                        ],
                        2,
                        false,
                        22,
                        true,
                        [
                            22
                        ]
                    ],
                    //Test 2 : If $groupIdArray having group internal_id, returns array external ids for all
                    [
                        [
                            24
                        ],
                        2,
                        true,
                        "grp24",
                        true,
                        [
                            "grp24"
                        ]
                    ],
                    //Test 3 : If $groupId does not exists, throws exception
                    [
                        [
                            "grp26"
                        ],
                        2,
                        false,
                        null,
                        false,
                        'Group ID grp26 is not valid at the organization.'
                    ]
                ]
            ]);
    }


    public function testGetConvertedGroupObject(){

        $this->specify("Test to Get Converted Group Object", function ($groupId, $organizationId, $isInternalIds, $convertedGroupId, $isGroupExist, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            //Mocking Services
            $mockApiValidationService = $this->getMock('ApiValidationService', ['addErrorsToOrganizationAPIErrorCount']);

            //Mocking Repositories
            $mockOrganizationGroupRepository = $this->getMock('OrgGroup',['findOneBy','find']);

            $mockContainer->method('get')->willReturnMap([
                [
                    APIValidationService::SERVICE_KEY,
                    $mockApiValidationService
                ]
            ]);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    OrgGroupRepository::REPOSITORY_KEY,
                    $mockOrganizationGroupRepository
                ]
            ]);

            $mockOrganization = $this->getMock('Organization', ['getId']);
            $mockOrganization->method('getId')->willReturn($organizationId);
            $mockOrgGroup = $this->getMock('OrgGroup', ['getId', 'getOrganization']);
            $mockOrgGroup->method('getOrganization')->willReturn($mockOrganization);
            $mockOrgGroup->method('getId')->willReturn($convertedGroupId);

            if(!$isGroupExist) {
                $validationErrorString = 'Group ID '.$groupId.' is not valid at the organization.';
                $mockApiValidationService->method('addErrorsToOrganizationAPIErrorCount')->willThrowException(new SynapseValidationException($validationErrorString));
            }

            if ($isInternalIds) {
                if ($isGroupExist) {
                    $mockOrganizationGroupRepository->method('find')->willReturn($mockOrgGroup);
                } else {
                    $mockOrganizationGroupRepository->method('find')->willReturn(null);
                }
            } else {
                if ($isGroupExist) {
                    $mockOrganizationGroupRepository->method('findOneBy')->willReturn($mockOrgGroup);
                } else {
                    $mockOrganizationGroupRepository->method('findOneBy')->willReturn(null);
                }
            }

            try {

                $idConversionService = new IDConversionService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $idConversionService->getConvertedGroupObject($groupId, $organizationId, $isInternalIds);
                $this->assertEquals($expectedResult, $result->getId());

            } catch (SynapseValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        },
            ['examples' =>
                [
                    //Test1 : If valid group External Id passed, returns orgGroup Object
                    [
                        "200011",
                        2,
                        false,
                        201,
                        true,
                        201
                    ],
                    //Test2 : If valid group internal Id passed, returns orgGroup Object
                    [
                        205,
                        2,
                        true,
                        "200055",
                        true,
                        "200055"
                    ],
                    //Test3 : If invalid group Id is passed , returns exception
                    [
                        "200020",
                        2,
                        false,
                        null,
                        false,
                        'Group ID 200020 is not valid at the organization.'
                    ]
                ]
            ]
        );
    }


    public function testGetConvertedCourseObjects()
    {
        $this->specify("Test to Get Converted Course Objects", function ($courseIdArray, $organizationId, $isInternalIds, $convertedCourseId, $isCourseExist, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            //Mocking Services
            $mockApiValidationService = $this->getMock('ApiValidationService', ['addErrorsToOrganizationAPIErrorCount']);

            //Mocking Repositories
            $mockOrganizationCourseRepository = $this->getMock('OrganizationCourseRepository', ['findOneBy', 'find']);

            $mockContainer->method('get')->willReturnMap([
                [
                    APIValidationService::SERVICE_KEY,
                    $mockApiValidationService
                ]
            ]);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    OrgCoursesRepository::REPOSITORY_KEY,
                    $mockOrganizationCourseRepository
                ]
            ]);

            $mockOrganization = $this->getMock('Organization', ['getId']);
            $mockOrganization->method('getId')->willReturn($organizationId);
            $mockOrgCourses = $this->getMock('OrgCourses', ['getId', 'getOrganization']);
            $mockOrgCourses->method('getOrganization')->willReturn($mockOrganization);
            $mockOrgCourses->method('getId')->willReturn($convertedCourseId);

            if(!$isCourseExist) {
                $validationErrorString = 'Course ID '.$courseIdArray[0].' is not valid at the organization.';
                $mockApiValidationService->method('addErrorsToOrganizationAPIErrorCount')->willThrowException(new SynapseValidationException($validationErrorString));
            }

            if ($isInternalIds) {
                if ($isCourseExist) {
                    $mockOrganizationCourseRepository->method('find')->willReturn($mockOrgCourses);
                } else {
                    $mockOrganizationCourseRepository->method('find')->willReturn(null);
                }
            } else {
                if ($isCourseExist) {
                    $mockOrganizationCourseRepository->method('findOneBy')->willReturn($mockOrgCourses);
                } else {
                    $mockOrganizationCourseRepository->method('findOneBy')->willReturn(null);
                }
            }

            try {
                $idConversionService = new IDConversionService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $idConversionService->getConvertedCourseObjects($courseIdArray, $organizationId, $isInternalIds);
                $this->assertEquals($expectedResult, $result);
            } catch (SynapseValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }
        },
            ['examples' =>
                [
                    // Test0: If $courseIdArray having course external_id, returns array of internal ids for all
                    [
                        [
                            "100001"
                        ],
                        2,
                        false,
                        10,
                        true,
                        [
                            10
                        ]
                    ],
                    // Test1: If $courseIdArray having course internal_id, returns array external ids for all
                    [
                        [
                            10
                        ],
                        2,
                        true,
                        "100001",
                        true,
                        [
                            "100001"
                        ]
                    ],
                    // Test2: If $courseId does not exists, throws exception
                    [
                        [
                            "100001"
                        ],
                        2,
                        false,
                        null,
                        false,
                        'Course ID 100001 is not valid at the organization.'
                    ]
                ]
            ]
        );
    }


    public function testGetConvertedCourseObject(){

        $this->specify("Test to Get Converted Course Object", function ($courseId, $organizationId, $isInternalIds, $convertedCourseId, $isCourseExist, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            //Mocking Services
            $mockApiValidationService = $this->getMock('ApiValidationService', ['addErrorsToOrganizationAPIErrorCount']);

            //Mocking Repositories
            $mockOrganizationCourseRepository = $this->getMock('OrganizationCourseRepository', ['findOneBy', 'find']);

            $mockContainer->method('get')->willReturnMap([
                [
                    APIValidationService::SERVICE_KEY,
                    $mockApiValidationService
                ]
            ]);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    OrgCoursesRepository::REPOSITORY_KEY,
                    $mockOrganizationCourseRepository
                ]
            ]);

            $mockOrganization = $this->getMock('Organization', ['getId']);
            $mockOrganization->method('getId')->willReturn($organizationId);
            $mockOrgCourses = $this->getMock('OrgCourses', ['getId', 'getOrganization', 'getExternalId']);
            $mockOrgCourses->method('getOrganization')->willReturn($mockOrganization);
            if ($isInternalIds) {
                $mockOrgCourses->method('getId')->willReturn($convertedCourseId);
            } else {
                $mockOrgCourses->method('getExternalId')->willReturn($convertedCourseId);
            }

            if(!$isCourseExist) {
                $validationErrorString = 'Course ID ' . $courseId . ' is not valid at the organization.';
                $mockApiValidationService->method('addErrorsToOrganizationAPIErrorCount')->willThrowException(new SynapseValidationException($validationErrorString));
            }

            if ($isInternalIds) {
                if ($isCourseExist) {
                    $mockOrganizationCourseRepository->method('find')->willReturn($mockOrgCourses);
                } else {
                    $mockOrganizationCourseRepository->method('find')->willReturn(null);
                }
            } else {
                if ($isCourseExist) {
                    $mockOrganizationCourseRepository->method('findOneBy')->willReturn($mockOrgCourses);
                } else {
                    $mockOrganizationCourseRepository->method('findOneBy')->willReturn(null);
                }
            }

            try {
                $idConversionService = new IDConversionService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $idConversionService->getConvertedCourseObject($courseId, $organizationId, $isInternalIds);
                if ($isInternalIds) {
                    $this->assertEquals($expectedResult, $result->getId());
                } else {
                    $this->assertEquals($expectedResult, $result->getExternalId());
                }
            } catch (SynapseValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }
        },
            ['examples' =>
                [
                    // Test0: If valid Course External Id passed, returns OrgCourses Object
                    [
                        "100001",
                        2,
                        false,
                        10,
                        true,
                        10
                    ],
                    // Test1: If valid course internal Id passed, returns OrgCourses Object
                    [
                        10,
                        2,
                        true,
                        "100001",
                        true,
                        "100001"
                    ],
                    // Test2: If invalid course Id is passed, throws exception
                    [
                        "100001",
                        2,
                        false,
                        null,
                        false,
                        'Course ID 100001 is not valid at the organization.'
                    ]
                ]
            ]
        );
    }
}
