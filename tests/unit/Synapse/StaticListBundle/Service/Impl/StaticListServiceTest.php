<?php

namespace Synapse\StaticListBundle\Service\Impl;


use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\StaticListBundle\EntityDto\StaticListDetailsDto;
use Synapse\StaticListBundle\EntityDto\StaticListsResponseDto;
use Synapse\StaticListBundle\Repository\OrgStaticListRepository;

class StaticListServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    private $staticListArray = [
        [
            'id' => 8429,
            'name' => 'SUV_ name',
            'description' => 'This description',
            'created_at' => '2018-03-13T16:54:33+0000',
            'modified_at' => '2018-03-13T16:54:33+0000',
            'created_by_person_id' => 184178,
            'created_by_firstname' => 'Lyric',
            'created_by_lastname' => 'Weaver',
            'modified_by_person_id' => null,
            'modified_by_firstname' => null,
            'modified_by_lastname' => null,
            'student_count' => 5
        ],
        [
            'id' => 8439,
            'name' => 'UFRI name5',
            'description' => 'This description58',
            'created_at' => '2018-03-13T16:54:33+0000',
            'modified_at' => '2018-03-13T16:54:33+0000',
            'created_by_person_id' => 184178,
            'created_by_firstname' => 'Lyric',
            'created_by_lastname' => 'Weaver',
            'modified_by_person_id' => '184178',
            'modified_by_firstname' => 'Lyric',
            'modified_by_lastname' => 'Weaver',
            'student_count' => 1
        ]
    ];

    public function testListAllStaticLists()
    {
        $this->specify("Test Listing of all static list by organization student", function ($organizationId, $facultyId, $expectedResult, $studentId = null, $pageNumber = null, $recordsPerPage = null, $sortBy = '') {

            // Inititializing repository to be mocked
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

            //mocking repositories
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', ['findOneBy']);
            $mockOrgStaticListRepository = $this->getMock('OrgStaticListRepository', ['getStaticListsWithStudentId', 'getCountOfStaticListsWithStudentID', 'getStaticListsForFaculty', 'getCountOfStaticListsForFaculty']);

            //mocking container
            $mockRbacManager = $this->getMock('Manager', ['assertPermissionToEngageWithStudents']);
            $mockAcademicYearService = $this->getMock('AcademicYearService', array('getCurrentOrgAcademicYearId'));

            //Scaffolding for Repository Resolver is using mockBuilder to allow more direct correlation to the Repository Resolver
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgPersonFacultyRepository::REPOSITORY_KEY,
                        $mockOrgPersonFacultyRepository
                    ],
                    [
                        OrgStaticListRepository::REPOSITORY_KEY,
                        $mockOrgStaticListRepository
                    ]

                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        Manager::SERVICE_KEY,
                        $mockRbacManager
                    ],
                    [
                        AcademicYearService::SERVICE_KEY,
                        $mockAcademicYearService
                    ]
                ]);
            $mockAcademicYearService->method('getCurrentOrgAcademicYearId')->willReturn(123);

            $mockOrgPersonFacultyObject = $this->getMock('OrgPersonFaculty', ['getId']);
            $mockOrgPersonFacultyObject->method('getId')->willReturn($facultyId);
            if ($organizationId) {
                $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonFacultyObject);
            } else {
                $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn(null);
            }

            if ($studentId) {
                if ($sortBy) {
                    $mockOrgStaticListRepository->method('getStaticListsWithStudentId')->willReturn(array_reverse($this->staticListArray));
                    $mockOrgStaticListRepository->method('getCountOfStaticListsWithStudentID')->willReturn(count($this->staticListArray));
                } else {
                    $mockOrgStaticListRepository->method('getStaticListsWithStudentId')->willReturn($this->staticListArray);
                    $mockOrgStaticListRepository->method('getCountOfStaticListsWithStudentID')->willReturn(count($this->staticListArray));
                }

            } else {
                $mockOrgStaticListRepository->method('getStaticListsForFaculty')->willReturn($this->staticListArray);
                $mockOrgStaticListRepository->method('getCountOfStaticListsForFaculty')->willReturn(count($this->staticListArray));
            }

            $faculty = new Person();
            $faculty->setId($facultyId);
            $staticListService = new StaticListService($mockRepositoryResolver, $mockLogger, $mockContainer);

            try {
                $result = $staticListService->listAllStaticLists($organizationId, $faculty, $studentId, $pageNumber, $recordsPerPage, $sortBy);
                $this->assertEquals($result, $expectedResult);
            } catch (SynapseValidationException $e) {
                $this->assertEquals($expectedResult, $e->getUserMessage());
            }
        }, [
            'examples' => [
                //case 1 : for null input parameters, throws unauthorised exception
                [
                    null,
                    null,
                    'You are not authorized to view the list of static lists.'
                ],
                //case 2 : for null student id, returns static list
                [
                    62,
                    22,
                    $this->setStaticListsResponseDto($this->staticListArray)
                ],
                //case 3 : for student id not null, returns static list
                [
                    62,
                    22,
                    $this->setStaticListsResponseDto($this->staticListArray),
                    141414
                ],
                //case 4: for pagination not null
                [
                    62,
                    22,
                    $this->setStaticListsResponseDto($this->staticListArray, 1, 20),
                    141414,
                    1,
                    20
                ],
                //case 5 : for pagination and sort by field not null
                [
                    62,
                    22,
                    $this->setStaticListsResponseDto(array_reverse($this->staticListArray), 1, 20),
                    141414,
                    1,
                    20,
                    'student_count'
                ]
            ]
        ]);
    }

    private function setStaticListsResponseDto($staticListArray = null, $pageNumber = null, $recordsPerPage = null)
    {

        $staticListsResponse = new StaticListsResponseDto();
        if ($pageNumber) {
            $staticListsResponse->setCurrentPage($pageNumber);
        } else {
            $staticListsResponse->setCurrentPage(1);
        }

        if ($recordsPerPage) {
            $staticListsResponse->setRecordsPerPage($recordsPerPage);
        } else {
            $staticListsResponse->setRecordsPerPage(25);
        }

        $staticListsResponse->setTotalPages(1);
        $staticListsResponse->setStaticLists(null);
        $staticListsResponse->setTotalRecords(count($staticListArray));

        if ($staticListArray) {
            foreach ($staticListArray as $staticList) {
                $staticListDetailsDto = new StaticListDetailsDto();
                $staticListDetailsDto->setStaticlistId($staticList['id']);
                $staticListDetailsDto->setCreatedBy($staticList['created_by_person_id']);
                $staticListDetailsDto->setCreatedByUserName($staticList['created_by_firstname'] . " " . $staticList['created_by_lastname']);
                $staticListDetailsDto->setStaticlistName($staticList['name']);
                $staticListDetailsDto->setStaticlistDescription($staticList['description']);
                $staticListDetailsDto->setCreatedAt($staticList['created_at']);
                $staticListDetailsDto->setModifiedAt($staticList['modified_at']);
                $staticListDetailsDto->setStudentCount($staticList['student_count']);
                $staticListDetailsDto->setModifiedByUserName($staticList['modified_by_firstname'] . " " . $staticList['modified_by_lastname']);
                $staticDetails[] = $staticListDetailsDto;
            }
            $staticListsResponse->setStaticListDetails($staticDetails);
        } else {
            $staticListsResponse->setStaticListDetails(null);
        }
        return $staticListsResponse;
    }
}