<?php
namespace Synapse\StaticListBundle\Service\Impl;

use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\StaticListBundle\Repository\OrgStaticListRepository;
use Synapse\StaticListBundle\Repository\OrgStaticListStudentsRepository;

class StaticListStudentsServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    private $organizationId = 1;

    private $personId = 1;

    /**
     * @var Person
     */
    private $personMock;

    /**
     * @expectedException \Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testViewStaticListDetails()
    {
        $this->specify("Test students static list service data for CSV", function ($staticListId, $isCSV = false, $isJob = false) {
            // Inititializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockOrgSearchRepositoryy = $this->getMock('OrgSearchRepository', array('find'));

            $mockStaticListRepository = $this->getMock('StaticListRepository', array('findOneBy'));

            $mockPersonRepository = $this->getMock('PersonRepository', array(
                'find'
            ));
            $mockMetadataListValuesRepository = $this->getMock('MetadataListValuesRepository');
            // Inititializing service to be mocked
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockDateUtilityService = $this->getMock('DateUtilityService', array(
                'adjustDateTimeToOrganizationTimezone'
            ));

            $mockDateUtilityService->method('adjustDateTimeToOrganizationTimezone')->willReturn(new \DateTime('now'));


            $mockResque = $this->getMock('resque', array(
                'enqueue'
            ));

            $orgService = $this->getMock('OrganizationService', array(
                'find',
                'getOrgTimeZone'
            ));
            $personService = $this->getMock('PersonService', array(
                'find',
                'getPrimryCoordinatorSortedByName'
            ));
            $utilServiceHelper = $this->getMock('UtilServiceHelper', array(
                'getDateByTimezone'
            ));
            $mockOrg = $this->getMock('Organization', array(
                'getId'
            ));

            // Mocking to organization
            $organizationMock = $this->getOrganizationMock();

            // Mocking to person
            $this->personMock = $this->getPersonMock();

            // Mocking manager service will be used in constroctor
            $managerService = $this->getMock('Manager');

            // Scaffolding for Repository Resolver is using mockBuilder to allow more direct correlation to the Repository Resolver
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        'SynapseCoreBundle:MetadataListValues',
                        $mockMetadataListValuesRepository
                    ],
                    [
                        'SynapseCoreBundle:Person',
                        $mockPersonRepository
                    ],
                    [
                        'SynapseStaticListBundle:OrgStaticList',
                        $mockStaticListRepository
                    ]
                ]);

            // Scaffolding for service is using mockBuilder
            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        'org_service',
                        $orgService
                    ],
                    [
                        'person_service',
                        $personService
                    ],
                    [
                        'bcc_resque.resque',
                        $mockResque
                    ],
                    [
                        'util_service',
                        $utilServiceHelper
                    ],
                    [
                        'date_utility_service',
                        $mockDateUtilityService
                    ]
                ]);

            $staticListServiceService = new StaticListStudentsService($mockRepositoryResolver, $mockLogger, $mockContainer, $managerService);
            // Fetching data for csv
            $staticListStudentsServiceData = $staticListServiceService->viewStaticListDetails($staticListId, $this->personMock, '', '', '', $isCSV, $isJob);

            // Asserting values
            $this->assertEquals('Access Denied', $staticListStudentsServiceData);
        }, [
            'examples' => [
                [
                    1, true, false
                ]
            ]
        ]);
    }


    public function testRemoveStudentFromStaticList()
    {
        $this->specify("Test remove a student from static list", function ($organizationId, $personId, $staticListId, $studentId, $orgStaticListRepositoryReturnValues, $orgPersonStudentReturnValue, $expectedResult) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockPersonRepository = $this->getMock('PersonRepository', array(
                'findOneBy'
            ));

            // Initializing service to be mocked
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockOrgStaticListRepository = $this->getMock('orgStaticListRepository', ['findOneBy']);
            $mockOrgStaticListRepository->method('FindOneBy')->willReturn($orgStaticListRepositoryReturnValues);
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', array('find'));
            $mockOrganizationRepository->method('find')->willReturn(true);
            $mockOrgPersonFacultyRepository = $this->getMock("OrgPersonFacultyRepository", ['findOneBy']);
            $mockOrgPersonStudentRepository = $this->getMock("OrgPersonStudentRepository", ['findOneBy', 'getPerson']);
            $mockOrgPersonStudent = $this->getMock("OrgPersonStudent", ['getId', 'getPerson']);
            $mockPerson = $this->getMock('Person', array('getId', 'getExternalId'));
            $mockPerson->method('getExternalId')->willReturn('test');
            $mockOrgStaticListStudentsRepository = $this->getMock('OrgStaticListStudentsRepository', ['findOneBy', 'delete']);

            $mockRbacManager = $this->getMock('rbacManager', [
                'checkAccessToOrganization'
            ]);

            // Scaffolding for Repository Resolver is using mockBuilder to allow more direct correlation to the Repository Resolver
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        OrgStaticListRepository::REPOSITORY_KEY,
                        $mockOrgStaticListRepository
                    ],
                    [
                        OrganizationRepository::REPOSITORY_KEY,
                        $mockOrganizationRepository
                    ],
                    [
                        OrgPersonFacultyRepository::REPOSITORY_KEY,
                        $mockOrgPersonFacultyRepository
                    ],
                    [
                        OrgPersonStudentRepository::REPOSITORY_KEY,
                        $mockOrgPersonStudentRepository
                    ],
                    [
                        OrgStaticListStudentsRepository::REPOSITORY_KEY,
                        $mockOrgStaticListStudentsRepository
                    ]
                ]);

            // Scaffolding for service is using mockBuilder
            $mockContainer->method('get')
                ->willReturnMap([
                    [Manager::SERVICE_KEY,
                        $mockRbacManager
                    ]
                ]);

            $mockRbacManager->method('checkAccessToOrganization')->willReturn(true);
            $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn(true);
            $mockOrgPersonStudent->method('getPerson')->willReturn($mockPerson);
            if ($orgPersonStudentReturnValue) {
                $mockOrgPersonStudent->method('getPerson')->willReturn($mockPerson);
            } else {
                $mockOrgPersonStudent = null;
            }
            $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($mockOrgPersonStudent);
            $mockOrgStaticListStudentsRepository->method('findOneBy')->willReturn(true);
            $mockOrgStaticListStudentsRepository->method('delete')->willReturn(true);

            try {
                $staticListServiceService = new StaticListStudentsService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $staticListServiceService->removeStudentFromStaticList($organizationId, $personId, $staticListId, $studentId);
                $this->assertEquals($result[0], $expectedResult);
            } catch (SynapseException $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }

        }, [
            'examples' => [
                // Example1: Static list not found, throw 'Access Denied' exception
                [
                    203,
                    4891668,
                    6295,
                    5048832,
                    null,
                    true,
                    'Access Denied'
                ],
                // Example2: Student not found in static list,  throw 'Student Not Found' exception
                [
                    203,
                    4891668,
                    6295,
                    5048832,
                    true,
                    false,
                    'Student Not Found'
                ],
                // Example3: Removes a student id this 5048832 from static list
                [
                    203,
                    4891668,
                    6295,
                    5048832,
                    [1],
                    true,
                    true,
                ]
            ]
        ]);
    }

    public function testRemoveStudentsFromStaticList()
    {
        $this->specify("Test remove students from static list", function ($organizationId, $personId, $staticListId, $studentIds, $orgStaticListRepositoryReturnValues, $orgPersonStudentReturnValue, $expectedResult) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockPersonRepository = $this->getMock('PersonRepository', array(
                'findOneBy'
            ));

            // Initializing service to be mocked
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockOrgStaticList = $this->getMock('OrgStaticList', ['getName']);
            $mockOrgStaticList->method('getName')->willReturn('test');
            $mockOrgStaticListRepository = $this->getMock('orgStaticListRepository', ['findOneBy']);
            if (!$orgStaticListRepositoryReturnValues) {
                $mockOrgStaticList = null;
            }
            $mockOrgStaticListRepository->method('FindOneBy')->willReturn($mockOrgStaticList);
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', array('find'));
            $mockOrganizationRepository->method('find')->willReturn(true);
            $mockOrgPersonFacultyRepository = $this->getMock("OrgPersonFacultyRepository", ['findOneBy']);
            $mockOrgPersonStudentRepository = $this->getMock("OrgPersonStudentRepository", ['findOneBy', 'getPerson']);
            $mockOrgPersonStudent = $this->getMock("OrgPersonStudent", ['getId', 'getPerson']);
            $mockPerson = $this->getMock('Person', array('getId', 'getExternalId'));
            $mockPerson->method('getExternalId')->willReturn('test');
            $mockOrgStaticListStudentsRepository = $this->getMock('OrgStaticListStudentsRepository', ['findOneBy', 'delete', 'flush']);

            $mockRbacManager = $this->getMock('rbacManager', [
                'checkAccessToOrganization'
            ]);
            $mockAlertNotificationsService = $this->getMock('AlertNotificationsService', array(
                'createNotification'
            ));


            // Scaffolding for Repository Resolver is using mockBuilder to allow more direct correlation to the Repository Resolver
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        OrgStaticListRepository::REPOSITORY_KEY,
                        $mockOrgStaticListRepository
                    ],
                    [
                        OrganizationRepository::REPOSITORY_KEY,
                        $mockOrganizationRepository
                    ],
                    [
                        OrgPersonFacultyRepository::REPOSITORY_KEY,
                        $mockOrgPersonFacultyRepository
                    ],
                    [
                        OrgPersonStudentRepository::REPOSITORY_KEY,
                        $mockOrgPersonStudentRepository
                    ],
                    [
                        OrgStaticListStudentsRepository::REPOSITORY_KEY,
                        $mockOrgStaticListStudentsRepository
                    ]
                ]);

            // Scaffolding for service is using mockBuilder
            $mockContainer->method('get')
                ->willReturnMap([
                    [Manager::SERVICE_KEY,
                        $mockRbacManager
                    ],
                    [
                        AlertNotificationsService::SERVICE_KEY,
                        $mockAlertNotificationsService
                    ]
                ]);

            $mockRbacManager->method('checkAccessToOrganization')->willReturn(true);
            $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn(true);
            if ($orgPersonStudentReturnValue) {
                $mockOrgPersonStudent->method('getPerson')->willReturn($mockPerson);
            } else {
                $mockOrgPersonStudent = null;
            }
            $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($mockOrgPersonStudent);
            $mockOrgStaticListStudentsRepository->method('delete')->willReturn(true);

            $staticListServiceService = new StaticListStudentsService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $staticListServiceService->removeStudentsFromStaticList($organizationId, $personId, $staticListId, $studentIds);
            $this->assertEquals($results, $expectedResult);

        }, [
            'examples' => [
                // Example1: Static list id '6295' not found, skips '6295 is not found' exception
                [
                    203,
                    4891668,
                    6295,
                    "5048832,5040351,5048801",
                    false,
                    true,
                    true
                ],
                // Example2: Student id '5048832' not found,skips '5048832 - Student Not Found' exception
                [
                    203,
                    4891668,
                    6295,
                    5048832,
                    true,
                    false,
                    true
                ],
                // Example3: Removes student ids static list from static list id 6295
                [
                    203,
                    4891668,
                    6295,
                    "5048832,5040351,5048801",
                    [1],
                    true,
                    true,
                ]
            ]
        ]);
    }


    public function testCreateBulkJobToRemoveStudentsFromStaticList()
    {
        $this->specify("Test createBulkJobToRemoveStudentsFromStaticList", function ($organizationId, $personFaculty, $staticListId, $studentIds, $orgStaticListRepositoryReturnValues, $orgPersonFacultyRepositoryReturnValues, $expectedResult) {

            // Initializing RepositoryResolver to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);

            // Initializing Logger to be mocked
            $mockLogger = $this->getMock('Logger', [
                'debug',
                'error',
                'info'
            ]);

            // Initializing Container to be mocked
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            // Mock OrgStaticList
            $mockOrgStaticList = $this->getMock('OrgStaticList', ['getId']);
            $mockOrgStaticListRepository = $this->getMock('orgStaticListRepository', ['findOneBy']);
            if (!$orgStaticListRepositoryReturnValues) {
                $mockOrgStaticList = null;
            }
            $mockOrgStaticListRepository->method('FindOneBy')->willReturn($mockOrgStaticList);

            // Mock Organization
            $mockOrganization = $this->getMock('Organization', ['getId']);
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', array('find'));
            $mockOrganizationRepository->method('find')->willReturn($mockOrganization);

            // Mock OrgPersonFaculty
            $mockOrgPersonFaculty = $this->getMock("OrgPersonFaculty", ['getId']);
            $mockOrgPersonFacultyRepository = $this->getMock("OrgPersonFacultyRepository", ['findOneBy']);
            if (!$orgPersonFacultyRepositoryReturnValues) {
                $mockOrgPersonFaculty = null;
            }
            $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonFaculty);

            $mockResque = $this->getMock('resque', array(
                'enqueue'
            ));

            // Scaffolding for Repository Resolver is using mockBuilder to allow more direct correlation to the Repository Resolver
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgStaticListRepository::REPOSITORY_KEY,
                        $mockOrgStaticListRepository
                    ],
                    [
                        OrganizationRepository::REPOSITORY_KEY,
                        $mockOrganizationRepository
                    ],
                    [
                        OrgPersonFacultyRepository::REPOSITORY_KEY,
                        $mockOrgPersonFacultyRepository
                    ]
                ]);

            // Scaffolding for service is using mockBuilder
            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        SynapseConstant::RESQUE_CLASS_KEY,
                        $mockResque
                    ]
                ]);

            try {
                $staticListServiceService = new StaticListStudentsService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $staticListServiceService->createBulkJobToRemoveStudentsFromStaticList($organizationId, $personFaculty, $staticListId, $studentIds);
                $this->assertEquals($result, $expectedResult);
            } catch (SynapseException $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }


        }, [
            'examples' => [
                // Example1: Static list not found, throw 'Access Denied' exception
                [
                    203,
                    $this->getPersonMock(),
                    6295,
                    [5048832, 5040351, 5048801],
                    false,
                    true,
                    'Static list id not found.'
                ],
                // Example2: Faculty not found, throw 'You are not authorized person' exception
                [
                    203,
                    $this->getPersonMock(),
                    6295,
                    [5048832, 5040351, 5048801],
                    true,
                    false,
                    'You are not authorized person!.'
                ],
                // Example3: Removes multiple students from Static List
                [
                    203,
                    $this->getPersonMock(),
                    6295,
                    [5048832, 5040351, 5048801],
                    true,
                    true,
                    true
                ]
            ]
        ]);
    }

    private function getOrganizationMock()
    {
        // Mocking to organization
        $organizationMock = $this->getMockBuilder('Synapse\CoreBundle\Entity\Organization', array(
            'getId'
        ))
            ->disableOriginalConstructor()
            ->getMock();
        $organizationMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($this->organizationId));

        return $organizationMock;
    }

    private function getPersonMock()
    {
        // Mocking to person
        $this->personMock = $this->getMockBuilder('Synapse\CoreBundle\Entity\Person', array(
            'getId',
            'getOrganization'
        ))
            ->disableOriginalConstructor()
            ->getMock();
        $this->personMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($this->personId));
        $this->personMock->expects($this->any())
            ->method('getOrganization')
            ->will($this->returnValue($this->getOrganizationMock()));

        return $this->personMock;
    }

}