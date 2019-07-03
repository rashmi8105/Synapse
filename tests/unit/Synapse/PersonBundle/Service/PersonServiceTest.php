<?php

namespace Synapse\PersonBumdle\Service;

use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Validator\LegacyValidator;
use Synapse\CampusConnectionBundle\Service\Impl\CampusConnectionService;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgPersonStudent;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\DataProcessingExceptionHandler;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\FacultyService;
use Synapse\CoreBundle\Service\Impl\GroupService;
use Synapse\CoreBundle\Service\Impl\PersonService as CoreBundlePersonService;
use Synapse\CoreBundle\Service\Impl\StudentService;
use Synapse\CoreBundle\Service\Utility\EmailUtilityService;
use Synapse\CoreBundle\Service\Utility\EntityValidationService;
use Synapse\CoreBundle\Service\Utility\IDConversionService;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\Service\Utility\URLUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\PersonBundle\DTO\PersonDTO;
use Synapse\PersonBundle\DTO\PersonListDTO;
use Synapse\PersonBundle\Service\PersonService;
use Synapse\RiskBundle\Repository\OrgRiskGroupModelRepository;
use Synapse\RiskBundle\Repository\RiskGroupPersonHistoryRepository;
use Synapse\RiskBundle\Repository\RiskGroupRepository;
use Synapse\RiskBundle\Service\Impl\RiskGroupService;


class PersonServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    private $studentIds = [1, 2, 3];
    private $facultyIds = [3, 4];
    private $orphanUsers = [6];

    private $errorArray;

    private $personMetaDataDetails = [


        "1" => [
            'external_id' => "X10001",
            'mapworks_internal_id' => 1,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell@ns2016.mapworks.com',
            'photo_url' => 'http://someimage.com/someimahge.jpg',
            'is_student' => 1,
            'is_faculty' => 0,
            'primary_connection_person_id' => 4567,
            'risk_group_id' => 1,
            'risk_group_description' => "desc"
        ],

        "2" => [
            'external_id' => "X10002",
            'mapworks_internal_id' => 2,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell2@ns2016.mapworks.com',
            'photo_url' => 'http://someimage.com/someimahge.jpg',
            'is_student' => 1,
            'is_faculty' => 0,
            'primary_connection_person_id' => 4567,
            'risk_group_id' => 1,
            'risk_group_description' => "desc"
        ],

        "3" => [
            'external_id' => "X10003",
            'mapworks_internal_id' => 3,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell3@ns2016.mapworks.com',
            'photo_url' => 'http://someimage.com/someimahge.jpg',
            'is_student' => 1,
            'is_faculty' => 1,
            'primary_connection_person_id' => 4567,
            'risk_group_id' => 1,
            'risk_group_description' => "desc"
        ],

        "4" => [
            'external_id' => "X10004",
            'mapworks_internal_id' => 4,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell4@ns2016.mapworks.com',
            'is_student' => 0,
            'is_faculty' => 1
        ],

        "5" => [
            'external_id' => "X10005",
            'mapworks_internal_id' => 5,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell5@ns2016.mapworks.com',
            'is_student' => 0,
            'is_faculty' => 0

        ],
        "6" => [
            'external_id' => "X10006",
            'mapworks_internal_id' => 6,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell6@ns2016.mapworks.com',
            'is_student' => 0,
            'is_faculty' => 0,
        ]
    ];

    private $riskIntendToLeavePersonData = [

        "1" => [
            'external_id' => "X10001",
            'mapworks_internal_id' => "X10001",
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'organization_id' => 1,
            'primary_email' => 'Jasmine.Russell@ns2016.mapworks.com',
            'risk_group_id' => 1234,
            'risk_group_name' => "Group of risk groupings.",
            'risk_level' => 3,
            'risk_updated_date' => "2015-08-01 23:14:02",
            'risk_color_text' => "yellow",
            'risk_color_hex' => "#fec82a",
            'current_cohort' => 1,
            'intent_to_leave' => 5,
            'intent_to_leave_updated_date' => "2015-09-29 11:11:53",
            'intent_to_leave_color_text' => "dark gray",
            'intent_to_leave_color_hex' => '#626161'
        ],

        "2" => [
            'external_id' => "X10002",
            'mapworks_internal_id' => "X10002",
            'firstname' => 'Jasmine',
            'lastname' => 'Kelsey',
            'organization_id' => 1,
            'primary_email' => 'JanKelsey@testingnorthstate.edu',
            'risk_group_id' => 12,
            'risk_group_name' => "Group of risk groupings.",
            'risk_level' => 3,
            'risk_updated_date' => "2015-08-01 23:14:02",
            'risk_color_text' => "yellow",
            'risk_color_hex' => "#fec82a",
            'current_cohort' => 1,
            'intent_to_leave' => 5,
            'intent_to_leave_updated_date' => "2015-09-29 11:11:53",
            'intent_to_leave_color_text' => "dark gray",
            'intent_to_leave_color_hex' => '#626161'
        ],

        "3" => [
            'external_id' => "X10003",
            'mapworks_internal_id' => "X10003",
            'firstname' => 'Jasmine',
            'lastname' => 'Kelsey',
            'organization_id' => 1,
            'primary_email' => 'JanKelsey@testingnorthstate.edu',
            'risk_group_id' => 1234,
            'risk_group_name' => "Group of risk groupings.",
            'risk_level' => 3,
            'risk_updated_date' => "2015-08-01 23:14:02",
            'risk_color_text' => "yellow",
            'risk_color_hex' => "#fec82a",
            'current_cohort' => 2,
            'intent_to_leave' => 5,
            'intent_to_leave_updated_date' => "2015-09-29 11:11:53",
            'intent_to_leave_color_text' => "dark gray",
            'intent_to_leave_color_hex' => '#626161'
        ],

        "4" => [
            'external_id' => "X10004",
            'mapworks_internal_id' => "X10004",
            'firstname' => 'herry',
            'lastname' => 'Kelsey4',
            'organization_id' => 1,
            'primary_email' => 'JanKelsey4@testingnorthstate.edu',
            'risk_group_id' => 12345,
            'risk_group_name' => "Group of risk groupings.",
            'risk_level' => 3,
            'risk_updated_date' => "2015-08-01 23:14:02",
            'risk_color_text' => "yellow",
            'risk_color_hex' => "#fec82a",
            'current_cohort' => 4,
            'intent_to_leave' => 5,
            'intent_to_leave_updated_date' => "2015-09-29 11:11:53",
            'intent_to_leave_color_text' => "dark gray",
            'intent_to_leave_color_hex' => '#626161'
        ],

        "5" => [
            'external_id' => "X10005",
            'mapworks_internal_id' => "X10005",
            'firstname' => 'glorry',
            'lastname' => 'Kelsey5',
            'organization_id' => 1,
            'primary_email' => 'JanKelsey@testingnorthstate.edu',
            'risk_group_id' => 12345,
            'risk_group_name' => "Group of risk groupings.",
            'risk_level' => 3,
            'risk_updated_date' => "2015-08-01 23:14:02",
            'risk_color_text' => "yellow",
            'risk_color_hex' => "#fec82a",
            'current_cohort' => 3,
            'intent_to_leave' => 5,
            'intent_to_leave_updated_date' => "2015-09-29 11:11:53",
            'intent_to_leave_color_text' => "dark gray",
            'intent_to_leave_color_hex' => '#626161'
        ],
        "6" => [
            'external_id' => "X10006",
            'mapworks_internal_id' => "X10006",
            'firstname' => 'messy',
            'lastname' => 'Kelsey6',
            'organization_id' => 1,
            'primary_email' => 'JanKelsey6@testingnorthstate.edu',
            'risk_group_id' => 12346,
            'risk_group_name' => "Group of risk groupings.",
            'risk_level' => 3,
            'risk_updated_date' => "2015-08-01 23:14:02",
            'risk_color_text' => "yellow",
            'risk_color_hex' => "#fec82a",
            'current_cohort' => 3,
            'intent_to_leave' => 5,
            'intent_to_leave_updated_date' => "2015-09-29 11:11:53",
            'intent_to_leave_color_text' => "dark gray",
            'intent_to_leave_color_hex' => '#626161'
        ]
    ];
    public function testGetMapworksPersons()
    {

        $this->specify("Test get mapworks person", function ($organizationId, $searchText, $userType, $pageNumber, $recordsPerPage, $expectedResult) {
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


            $mockPersonRepository = $this->getMock('PersonRepository', ['getMapworksStudents', 'getMapworksFaculty', 'getMapworksOrphanUsers', 'getMapworksPersons', 'getMapworksPersonData']);

            $mockPersonRepository->method('getMapworksStudents')->willReturn($this->studentIds);
            $mockPersonRepository->method('getMapworksFaculty')->willReturn($this->facultyIds);
            $mockPersonRepository->method('getMapworksOrphanUsers')->willReturn($this->orphanUsers);
            $allUsers = array_merge($this->studentIds, $this->facultyIds, $this->orphanUsers);
            $allUsers =  array_unique($allUsers);
            $mockPersonRepository->method('getMapworksPersons')->willReturn($allUsers);


            $mockPersonRepository->method('getMapworksPersonData')->willReturnCallback(function ($organizationId, $personIdArray, $offset, $recordsPerPage) {
                $detailsArray = [];
                foreach ($personIdArray as $personId) {
                    $detailsArray[] = $this->personMetaDataDetails[$personId];
                }
                return $detailsArray;
            });


            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    PersonRepository::REPOSITORY_KEY,
                    $mockPersonRepository
                ]
            ]);

            $personService = new PersonService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $resultDto = $personService->getMapworksPersons($organizationId, $searchText, $userType, $pageNumber, $recordsPerPage);


            $this->assertEquals($resultDto->getPersonList(), $expectedResult);
            $this->assertEquals($resultDto->getCurrentPage(), 1);
            $this->assertEquals($resultDto->getTotalPages(), 1);
            $this->assertEquals($resultDto->getTotalRecords(), count($expectedResult));

        }, [
                'examples' => [
                    // Returns student list
                    [
                        1, "text", "student", null, null, $this->getPersonDetails('student')
                    ],
                    // Returns faculty list
                    [
                        1, "text", "faculty", null, null, $this->getPersonDetails('faculty')
                    ],
                    // Orphan User list
                    [
                        1, "text", "orphan", null, null, $this->getPersonDetails('orphan')
                    ],
                    // Dual role user list
                    [
                        1, "text", 'dual_role', null, null, $this->getPersonDetails('dual_role')
                    ],
                    // Invalid Role , by default would return all students
                    [
                        1, "text", 'some role', null, null, $this->getPersonDetails('all')
                    ],
                    // No role, by default should return all students
                    [
                        1, "text", 'some role', null, null, $this->getPersonDetails('all')
                    ]
                ]
            ]
        );
    }

    public function getPersonDetails($userType)
    {

        $personIdArr = [];
        switch ($userType) {
            case "student":
                $personIdArr = $this->studentIds;
                break;
            case "faculty":
                $personIdArr = $this->facultyIds;
                break;
            case "dual_role":
                $personIdArr = array_intersect($this->studentIds, $this->facultyIds);
                break;
            case "orphan":
                $personIdArr = $this->orphanUsers;
                break;
            case "all":
                $personIdArr = array_merge($this->studentIds,$this->facultyIds,$this->orphanUsers);
                $personIdArr =  array_unique($personIdArr);
                break;

        }
        $resultArray = [];
        foreach ($personIdArr as $personId) {
            $resultArray[] = $this->personMetaDataDetails[$personId];
        }
        return $resultArray;
    }



    private $personDataForCreate = [
        "1" => [
            'external_id' => 'X10001',
            'mapworks_internal_id' => 1,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell@ns2016.mapworks.com',
            'photo_url' => 'http://someimage.com/someimage.jpg',
            'is_student' => 1,
            'is_faculty' => 0,
            'primary_connection_person_id' => 4567,
            'risk_group_id' => 1
        ],

        "2" => [
            'external_id' => null,
            'mapworks_internal_id' => 2,
            'auth_username' => 'auth',
            'firstname' => null,
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell2@ns2016.mapworks.com',
            'photo_url' => 'http://someimage.com/someimage.jpg',
            'is_student' => 1,
            'is_faculty' => 0,
            'primary_connection_person_id' => 4567,
            'risk_group_id' => 1
        ],

        "3" => [
            'external_id' => "X10003",
            'mapworks_internal_id' => 3,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell3ns2016.mapworks.com',
            'photo_url' => 'http://someimage.com/someimage.jpg',
            'is_student' => 1,
            'is_faculty' => 1,
            'primary_connection_person_id' => 4567,
            'risk_group_id' => 1,
        ],

        "4" => [
            'external_id' => "X10004",
            'mapworks_internal_id' => 4,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell4@ns2016.mapworks.com',
            'is_student' => 0,
            'is_faculty' => 1
        ],

        "5" => [
            'external_id' => "X10005",
            'mapworks_internal_id' => 5,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine55555555555555555555555555555555555555555555555555555555555555544444444444',
            'lastname' => 'Russell4546666666666666666666666666666666666666666666666666666666666666666666666',
            'primary_email' => 'Jasmine.Russell5@ns2016.mapworks.com',
            'is_student' => 0,
            'is_faculty' => 0

        ],
        "6" => [
            'external_id' => "X10006",
            'mapworks_internal_id' => 6,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell6@ns2016.mapworks.com',
            'is_student' => 0,
            'is_faculty' => 1,
        ],
        "7" => [
            'external_id' => 'X10007',
            'mapworks_internal_id' => 7,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell@ns2016.mapworks.com',
            'photo_url' => 'http://someimage.com/someimage.jpg',
            'is_student' => 1,
            'is_faculty' => 0,
            'primary_connection_person_id' => 4567,
            'risk_group_id' => 1
        ],
        "8" => [
            'external_id' => 'X10008',
            'mapworks_internal_id' => 8,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell@ns2016.mapworks.com',
            'photo_url' => 'http://someimage.com/someimage.jpg',
            'is_student' => 1,
            'is_faculty' => 0,
            'primary_connection_person_id' => 1234,
            'risk_group_id' => 1
        ],
        "9" => [
            'external_id' => 'X10009',
            'mapworks_internal_id' => 9,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell@ns2016.mapworks.com',
            'photo_url' => 'http://someimage.com/someimage.jpg',
            'is_student' => 1,
            'is_faculty' => 0,
            'primary_connection_person_id' => 2345,
            'risk_group_id' => 1
        ],
        "10" => [
            'external_id' => 'X100010',
            'mapworks_internal_id' => 10,
            'auth_username' => 'auth123',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'title' => 'Mr.',
            'primary_email' => 'Jasmine.Russell@ns2016.mapworks.com',
            'photo_url' => 'http://someimage.com/someimage.jpg',
            'is_student' => 1,
            'is_faculty' => 0,
            'primary_connection_person_id' => 2345,
            'risk_group_id' => 1
        ]
    ];

    public function testCreatePersons()
    {
        $this->specify("Test Create mapworks person", function ($personListDTO, $organization, $loggedInUser, $personValidationErrors, $validPhotoLink, $validPrimaryConnection, $validRiskGroup, $expectedResult) {
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

            $mockPersonRepository = $this->getMock('PersonRepository', ['find', 'findBy', 'findOneBy', 'persist', 'flush']);
            $mockRiskGroupRepository = $this->getMock('RiskGroupRepository', ['find']);
            $mockOrgRiskGroupModelRepository = $this->getMock('OrgRiskGroupModelRepository', ['findOneBy']);
            $mockRiskGroupPersonHistoryRepository = $this->getMock('RiskGroupPersonHistoryRepository', ['persist']);
            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', ['persist']);
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', ['persist']);

            //mocking services
            $mockApiValidationService = $this->getMock('APIValidationService', ['updateOrganizationAPIValidationErrorCount']);
            $mockUrlUtilityService = $this->getMock('UrlUtilityService', ['validatePhotoURL']);
            $mockValidatorService = $this->getMock('ValidatorService', ['validate']);
            $mockRbacManager = $this->getMock('Manager', array('checkAccessToStudent'));
            $mockCoreBundlePersonService = $this->getMock('CoreBundlePersonService', array('generateAuthKey'));
            $mockGroupService = $this->getMock('GroupService', array('addStudentSystemGroup'));
            $mockRiskGroupService = $this->getMock('RiskGroupService', array('validateRiskGroupBelongsToOrganization'));
            $mockCampusConnectionService = $this->getMock('CampusConnectionService', array('validatePrimaryCampusConnectionId'));


            $mockDataProcessingUtilityService = $this->getMock('dataProcessingUtilityService',['setErrorMessageOrValueInArray']);
            $mockDataProcessingUtilityService->method('setErrorMessageOrValueInArray')->willReturnCallback(function($records, $errorArray) {
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

            $mockRepositoryResolver->method('getRepository')->willReturnMap([

                [
                    PersonRepository::REPOSITORY_KEY,
                    $mockPersonRepository
                ],
                [
                    RiskGroupRepository::REPOSITORY_KEY,
                    $mockRiskGroupRepository
                ],
                [
                    OrgRiskGroupModelRepository::REPOSITORY_KEY,
                    $mockOrgRiskGroupModelRepository
                ],
                [
                    RiskGroupPersonHistoryRepository::REPOSITORY_KEY,
                    $mockRiskGroupPersonHistoryRepository
                ],
                [
                    OrgPersonStudentRepository::REPOSITORY_KEY,
                    $mockOrgPersonStudentRepository
                ],
                [
                    OrgPersonFacultyRepository::REPOSITORY_KEY,
                    $mockOrgPersonFacultyRepository
                ]
            ]);

            $mockContainer->method('get')->willReturnMap([
                [
                    APIValidationService::SERVICE_KEY,
                    $mockApiValidationService
                ],
                [
                    URLUtilityService::SERVICE_KEY,
                    $mockUrlUtilityService
                ],
                [
                    SynapseConstant::VALIDATOR,
                    $mockValidatorService
                ],
                [
                    Manager::SERVICE_KEY,
                    $mockRbacManager
                ],
                [
                    CoreBundlePersonService::SERVICE_KEY,
                    $mockCoreBundlePersonService
                ],
                [
                    GroupService::SERVICE_KEY,
                    $mockGroupService
                ],
                [
                    DataProcessingUtilityService::SERVICE_KEY,
                    $mockDataProcessingUtilityService
                ],
                [
                    RiskGroupService::SERVICE_KEY,
                    $mockRiskGroupService
                ],
                [
                    CampusConnectionService::SERVICE_KEY,
                    $mockCampusConnectionService
                ]
            ]);

            $personObject = $this->setPersonObject($personListDTO);

            $mockPersonRepository->method('persist')->willReturn($personObject);

            $mockUrlUtilityService->method('validatePhotoURL')->willReturn($validPhotoLink);

            if (!empty($personValidationErrors)) {
                $errors = $this->arrayOfErrorObjects($personValidationErrors);
                $mockValidatorService->method('validate')->willReturn($errors);
            }

            if (!$validPrimaryConnection['valid_primary_connection_id']) {
                $mockCampusConnectionService->method('validatePrimaryCampusConnectionId')->willReturn($validPrimaryConnection['error_message_connection_id']);
            } else {
                $mockCampusConnectionService->method('validatePrimaryCampusConnectionId')->willReturn(true);
            }


            if (!$validPrimaryConnection['has_student_access']) {
                $mockCampusConnectionService->method('validatePrimaryCampusConnectionId')->willReturn($validPrimaryConnection['error_message_connection_id']);
            } else {
                $mockCampusConnectionService->method('validatePrimaryCampusConnectionId')->willReturn(true);
            }

            if (!$validRiskGroup['valid_risk_group']) {
                $mockRiskGroupService->method('validateRiskGroupBelongsToOrganization')->willReturn($validRiskGroup['error_message_risk_group']);
            }else if (!$validRiskGroup['is_mapped_to_organization']) {
                $mockRiskGroupService->method('validateRiskGroupBelongsToOrganization')->willReturn($validRiskGroup['error_message_mapped_to']);
            } else {
                $mockRiskGroupService->method('validateRiskGroupBelongsToOrganization')->willReturn(true);
            }

            $personService = new PersonService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $result = $personService->createPersons($personListDTO, $organization, $loggedInUser);
            $this->assertEquals($result, $expectedResult);
        }, [
                'examples' => [
                    // Returns created student user and error array
                    [
                        $this->setPersonListDTO($this->personDataForCreate[1]),
                        $this->setOrganization(),
                        $this->setUser(),
                        [],
                        false,
                        [
                            'valid_primary_connection_id' => false,
                            'has_student_access' => false,
                            'error_message_connection_id' => "Invalid Primary Campus Connection Id",
                            'error_message_access' => "Invalid Primary Campus Connection Id"
                        ],
                        [
                            'valid_risk_group' => false,
                            'is_mapped_to_organization' => false,
                            'error_message_risk_group' => "Risk Group does not exist.",
                            'error_message_mapped_to' => "Risk Group does not exist."
                        ],
                        $this->generatePersonResponse($this->personDataForCreate[1],
                            [
                                'photo_link' => 'Invalid Photo URL. Please try another URL.',
                                'primary_campus_connection_id' => 'Invalid Primary Campus Connection Id',
                                'risk_group_id' => 'Risk Group does not exist.',
                            ]
                        ),

                    ],
                    // Validates missing required fields and returns only error array
                    [
                        $this->setPersonListDTO($this->personDataForCreate[2]),
                        $this->setOrganization(),
                        $this->setUser(),
                        [
                            'external_id' => 'External ID cannot be empty',
                            'firstname' => 'Firstname cannot be empty'
                        ],
                        false,
                        [
                            'valid_primary_connection_id' => false,
                            'has_student_access' => false,
                            'error_message_connection_id' => "Invalid Primary Campus Connection Id",
                            'error_message_access' => "Invalid Primary Campus Connection Id"
                        ],
                        [
                            'valid_risk_group' => false,
                            'is_mapped_to_organization' => false,
                            'error_message_risk_group' => "Risk Group does not exist.",
                            'error_message_mapped_to' => "Risk Group does not exist."
                        ],
                        $this->generatePersonResponse($this->personDataForCreate[2],
                            [
                                'external_id' => 'External ID cannot be empty',
                                'firstname' => 'Firstname cannot be empty'
                            ],
                            true
                        ),
                    ],
                    // Validates invalid email and returns only error array
                    [
                        $this->setPersonListDTO($this->personDataForCreate[3]),
                        $this->setOrganization(),
                        $this->setUser(),
                        [
                            'username' => 'Invalid email ID.'
                        ],
                        false,
                        [
                            'valid_primary_connection_id' => false,
                            'has_student_access' => false,
                            'error_message_connection_id' => "Invalid Primary Campus Connection Id",
                            'error_message_access' => "Invalid Primary Campus Connection Id"
                        ],
                        [
                            'valid_risk_group' => false,
                            'is_mapped_to_organization' => false,
                            'error_message_risk_group' => "Risk Group does not exist.",
                            'error_message_mapped_to' => "Risk Group does not exist."
                        ],
                        $this->generatePersonResponse($this->personDataForCreate[3],
                            [
                                'primary_email' => 'Invalid email ID.'
                            ],
                            true
                        ),
                    ],
                    // verifying existing email and externalId and returns only error array
                    [
                        $this->setPersonListDTO($this->personDataForCreate[4]),
                        $this->setOrganization(),
                        $this->setUser(),
                        [
                            'username' => 'Email already in use.',
                            'externalId' => 'External Id already in use.',
                        ],
                        false,
                        [
                            'valid_primary_connection_id' => false,
                            'has_student_access' => false,
                            'error_message_connection_id' => "Invalid Primary Campus Connection Id",
                            'error_message_access' => "Invalid Primary Campus Connection Id"
                        ],
                        [
                            'valid_risk_group' => false,
                            'is_mapped_to_organization' => false,
                            'error_message_risk_group' => "Risk Group does not exist.",
                            'error_message_mapped_to' => "Risk Group does not exist."
                        ],
                        $this->generatePersonResponse($this->personDataForCreate[4],
                            [
                                'primary_email' => 'Email already in use.',
                                'external_id' => 'External Id already in use.',
                            ],
                            true
                        ),
                    ],
                    // validates person fields and returns error array
                    [
                        $this->setPersonListDTO($this->personDataForCreate[5]),
                        $this->setOrganization(),
                        $this->setUser(),
                        [
                            'firstname' => 'Firstname cannot be longer than 45 characters',
                            'lastname' => 'Lastname cannot be longer than 45 characters'
                        ],
                        false,
                        [
                            'valid_primary_connection_id' => false,
                            'has_student_access' => false,
                            'error_message_connection_id' => "Invalid Primary Campus Connection Id",
                            'error_message_access' => "Invalid Primary Campus Connection Id"
                        ],
                        [
                            'valid_risk_group' => false,
                            'is_mapped_to_organization' => false,
                            'error_message_risk_group' => "Risk Group does not exist.",
                            'error_message_mapped_to' => "Risk Group does not exist."
                        ],
                        $this->generatePersonResponse($this->personDataForCreate[5],
                            [
                                'firstname' => 'Firstname cannot be longer than 45 characters',
                                'lastname' => 'Lastname cannot be longer than 45 characters'
                            ],
                            true
                        ),
                    ],
                    //Returns created faculty user without error
                    [
                        $this->setPersonListDTO($this->personDataForCreate[6]),
                        $this->setOrganization(),
                        $this->setUser(),
                        [],
                        false,
                        [
                            'valid_primary_connection_id' => false,
                            'has_student_access' => false,
                            'error_message_connection_id' => "Invalid Primary Campus Connection Id",
                            'error_message_access' => "Invalid Primary Campus Connection Id"
                        ],
                        [
                            'valid_risk_group' => false,
                            'is_mapped_to_organization' => false,
                            'error_message_risk_group' => "Risk Group does not exist.",
                            'error_message_mapped_to' => "Risk Group does not exist."
                        ],
                        $this->generatePersonResponse($this->personDataForCreate[6], []),
                    ],
                    // Returns created student user with valid photo url and returns error for invalid Risk Group Id and invalid Primary Campus Connection Id
                    [
                        $this->setPersonListDTO($this->personDataForCreate[7]),
                        $this->setOrganization(),
                        $this->setUser(),
                        [],
                        true,
                        [
                            'valid_primary_connection_id' => false,
                            'has_student_access' => false,
                            'error_message_connection_id' => "Invalid Primary Campus Connection Id",
                            'error_message_access' => "Invalid Primary Campus Connection Id"
                        ],
                        [
                            'valid_risk_group' => false,
                            'is_mapped_to_organization' => false,
                            'error_message_risk_group' => "Risk Group does not exist.",
                            'error_message_mapped_to' => "Risk Group does not exist."
                        ],
                        $this->generatePersonResponse($this->personDataForCreate[7],
                            [
                                'primary_campus_connection_id' => 'Invalid Primary Campus Connection Id',
                                'risk_group_id' => 'Risk Group does not exist.',
                            ]
                        ),
                    ],

                    // Returns created student user with valid photo url and valid Primary Campus Connection with access to student and returns error for invalid Risk Group Id
                    [
                        $this->setPersonListDTO($this->personDataForCreate[8]),
                        $this->setOrganization(),
                        $this->setUser(),
                        [],
                        true,
                        [
                            'valid_primary_connection_id' => true,
                            'has_student_access' => true
                        ],
                        [
                            'valid_risk_group' => false,
                            'is_mapped_to_organization' => false,
                            'error_message_risk_group' => "Risk Group does not exist.",
                            'error_message_mapped_to' => "Risk Group does not exist."
                        ],
                        $this->generatePersonResponse($this->personDataForCreate[8],
                            [
                                'risk_group_id' => 'Risk Group does not exist.',
                            ]
                        ),
                    ],
                    // Returns created student user with valid photo url and valid Primary Campus Connection with access to student and valid Risk Group Id with no error
                    [
                        $this->setPersonListDTO($this->personDataForCreate[8]),
                        $this->setOrganization(),
                        $this->setUser(),
                        [],
                        true,
                        [
                            'valid_primary_connection_id' => true,
                            'has_student_access' => true
                        ],
                        [
                            'valid_risk_group' => true,
                            'is_mapped_to_organization' => true
                        ],
                        $this->generatePersonResponse($this->personDataForCreate[8], [])
                    ],
                    // Returns created student user with valid photo url and valid Primary Campus Connection with access to student with error for risk group Id not mapped to organization
                    [
                        $this->setPersonListDTO($this->personDataForCreate[8]),
                        $this->setOrganization(),
                        $this->setUser(),
                        [],
                        true,
                        [
                            'valid_primary_connection_id' => true,
                            'has_student_access' => true
                        ],
                        [
                            'valid_risk_group' => true,
                            'is_mapped_to_organization' => false,
                            'error_message_risk_group' => "Risk Group does not exist.",
                            'error_message_mapped_to' => "Risk Group is not mapped to any organization."
                        ],
                        $this->generatePersonResponse($this->personDataForCreate[8],
                            [
                                'risk_group_id' => 'Risk Group is not mapped to any organization.'
                            ]
                        )
                    ],
                ]
            ]
        );
    }

    private function arrayOfErrorObjects($errorArray)
    {

        $returnArray = [];

        foreach ($errorArray as $errorKey => $error) {
            $mockErrorObject = $this->getMock('ErrorObject', ['getPropertyPath', 'getMessage']);
            $mockErrorObject->method('getPropertyPath')->willReturn($errorKey);
            $mockErrorObject->method('getMessage')->willReturn($error);
            $returnArray[] = $mockErrorObject;
        }
        return $returnArray;
    }

    private function setPersonListDTO($personData, $returnPersonDTO = false)
    {
        $personDTO = new PersonDTO();

        $personDTO->setAuthUsername($personData['auth_username']);
        $personDTO->setPrimaryEmail($personData['primary_email']);
        $personDTO->setExternalId($personData['external_id']);
        $personDTO->setMapworksInternalId($personData['mapworks_internal_id']);
        $personDTO->setFirstname($personData['firstname']);
        $personDTO->setLastname($personData['lastname']);
        if (isset($personData['photo_url'])) {
            $personDTO->setPhotoLink($personData['photo_url']);
        }
        if (isset($personData['primary_connection_person_id'])) {
            $personDTO->setPrimaryCampusConnectionId($personData['primary_connection_person_id']);
        }
        $personDTO->setIsStudent($personData['is_student']);
        $personDTO->setIsFaculty($personData['is_faculty']);
        $personDTO->setTitle("Mr.");
        if (isset($personData['risk_group_id'])) {
            $personDTO->setRiskGroupId($personData['risk_group_id']);
        }
        $personDTO->setFieldsToClear([]);

        if ($returnPersonDTO) {
            return $personDTO;
        }

        $personListDTO = new PersonListDTO();
        $personListDTO->setPersonList([$personDTO]);
        return $personListDTO;
    }

    private function setPersonObject($personListDTO)
    {

        $personList = $personListDTO->getPersonList();
        $personDTO = $personList[0];
        $currentDate = new \DateTime('now');
        $organizationObject = new Organization();

        //creating new person
        $personObject = new Person();
        $personObject->setExternalId($personDTO->getExternalId());
        $personObject->setUsername($personDTO->getPrimaryEmail());
        $personObject->setFirstname($personDTO->getFirstname());
        $personObject->setLastname($personDTO->getLastname());
        $personObject->setOrganization($organizationObject);
        $personObject->setAuthUsername($personDTO->getAuthUsername());
        $personObject->setTitle($personDTO->getTitle());
        $personObject->setIsLocked("y");
        $personObject->setCreatedAt($currentDate);
        $personObject->setModifiedAt($currentDate);
        $personObject->setId($personDTO->getMapworksInternalId());
        return $personObject;
    }

    private function setOrganization($campusId = 1)
    {
        $organization = new Organization();
        $organization->setExternalId('ABC123');
        $organization->setCampusId($campusId);
        return $organization;
    }

    private function setUser($externalId = '123')
    {
        $person = new Person();
        $person->setExternalId($externalId);
        return $person;
    }


    private function generatePersonResponse($personArray, $errorArray, $skipped = false, $isUpdate = false, $isSkipped = false)
    {

        $responseArray = [];
        $createdRowsArray = [];
        $errorRowsArray = [];
        $personListDTO = $this->setPersonListDTO($personArray);

        foreach ($personListDTO->getPersonList() as $personDTO) {
            if (count($errorArray) > 0) {
                $errorRowsArray[] = $this->setPersonResponse($personDTO, $errorArray);
            }
            if (!$skipped) {
                $createdRowsArray[] = $this->setPersonResponse($personDTO);
            }
        }

        $responseArray['errors']['error_count'] = count($errorRowsArray);
        $responseArray['errors']['error_records'] = $errorRowsArray;
        if ($isUpdate) {
            if ($isSkipped) {
                $responseArray['data']['updated_count'] = 0;
                $responseArray['data']['updated_records'] = [];
                $responseArray['data']['skipped_count'] = count($createdRowsArray);
                $responseArray['data']['skipped_records'] = $createdRowsArray;
            } else {
                $responseArray['data']['updated_count'] = count($createdRowsArray);
                $responseArray['data']['updated_records'] = $createdRowsArray;
                $responseArray['data']['skipped_count'] = 0;
                $responseArray['data']['skipped_records'] = [];
            }
        } else {
            $responseArray['data']['created_count'] = count($createdRowsArray);
            $responseArray['data']['created_records'] = $createdRowsArray;
        }

        return $responseArray;
    }

    private function setPersonResponse(PersonDTO $personDTO, $errorMessage = [])
    {
        $personArray = $responseArray = [];
        $personArray['external_id'] = $personDTO->getExternalId();
        $personArray['mapworks_internal_id'] = $personDTO->getMapworksInternalId();
        if (array_key_exists('authUsername', $errorMessage)) {
            $errorMessage['auth_username'] = $errorMessage['authUsername'];
        }
        $personArray['auth_username'] = $personDTO->getAuthUsername();
        $personArray['firstname'] = $personDTO->getFirstname();
        $personArray['lastname'] = $personDTO->getLastname();
        $personArray['title'] = $personDTO->getTitle();
        $personArray['primary_email'] = $personDTO->getPrimaryEmail();
        if(!empty($personDTO->getPhotoLink())) {
            $personArray['photo_link'] = $personDTO->getPhotoLink();
        }
        if(!empty($personDTO->getPrimaryCampusConnectionId())) {
            $personArray['primary_campus_connection_id'] = $personDTO->getPrimaryCampusConnectionId();
        }
        if(!empty($personDTO->getRiskGroupId())) {
            $personArray['risk_group_id'] = $personDTO->getRiskGroupId();
        }
        $personArray['is_student'] = $personDTO->getIsStudent();
        $personArray['is_faculty'] = $personDTO->getIsFaculty();

        foreach ($personArray as $key => $value) {
            if (array_key_exists($key, $errorMessage)) {
                $responseArray[$key]['value'] = $value;
                $responseArray[$key]['message'] = $errorMessage[$key];
            } else {
                $responseArray[$key] = $value;
            }
        }
        return $responseArray;
    }

     public function testGetCurrentRiskAndIntentToLeaveForOrganization()
    {
        $this->specify("Test Get Current Risk And Intent To Leave For Organization", function ($organizationId, $searchText, $cohort, $riskGroupId, $recordsPerPage, $queryDataIds, $expectedResult) {
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

            $mockOrgAcademicYearRepository = $this->getMock('orgAcademicYearRepository', ['getCurrentOrPreviousAcademicYearUsingCurrentDate']);

            $mockPersonRepository = $this->getMock('PersonRepository', ['getMapworksStudents','getPersonsCurrentRiskAndIntentToLeaveFilteredByStudentCriteria']);

            $academicYearId = [
                "0" => ['org_academic_year_id' => 1]
            ];
            $mockOrgAcademicYearRepository->method('getCurrentOrPreviousAcademicYearUsingCurrentDate')->willReturn($academicYearId);

            if($recordsPerPage) {
                $totalRecords = array_column($this->riskIntendToLeavePersonData, 'person_id');
                $mockPersonRepository->method('getMapworksStudents')->willReturn(count($totalRecords));
            }
            $queryReturnedData = $this->getRiskAndIntentToLeavePersonDetails($queryDataIds);

            $mockPersonRepository->method('getPersonsCurrentRiskAndIntentToLeaveFilteredByStudentCriteria')->willReturn($queryReturnedData);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    OrgAcademicYearRepository::REPOSITORY_KEY,
                    $mockOrgAcademicYearRepository
                ],
                [
                    PersonRepository::REPOSITORY_KEY,
                    $mockPersonRepository
                ]
            ]);

            $personService = new PersonService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $resultDto = $personService->getCurrentRiskAndIntentToLeaveForOrganization($organizationId, $searchText, $cohort, $riskGroupId, 1, $recordsPerPage);

            $this->assertEquals($resultDto->getPersonList(), $expectedResult);
            $this->assertEquals($resultDto->getCurrentPage(), 1);
            $this->assertEquals($resultDto->getTotalPages(), 1);
            $this->assertEquals($resultDto->getTotalRecords(), count($expectedResult));

        }, [
            'examples' => [
                //No search filter, no current cohort parameter, no risk group parameter, no pagination (0, 0, 0, 0)
                [
                    1, null, null, null, null, [], $this->getRiskAndIntentToLeavePersonDetails([])
                ],
                //Search filter, no current cohort parameter, no risk group parameter, no pagination (1, 0, 0, 0)
                [
                    1, 'Jasmine', null, null, null, [1,2,3], $this->getRiskAndIntentToLeavePersonDetails([1,2,3])
                ],
                //Search filter, current cohort parameter, no risk group parameter, no pagination (1, 1, 0, 0)
                [
                    1, 'Jasmine', 1, null, null, [1,2], $this->getRiskAndIntentToLeavePersonDetails([1,2])
                ],
                //Search filter, current cohort parameter, risk group parameter, no pagination (1, 1, 1, 0)
                [
                    1, 'Jasmine', 1, 1234, null, [1,3], $this->getRiskAndIntentToLeavePersonDetails([1,3])
                ],
                //Search filter, current cohort parameter, risk group parameter, pagination (1, 1, 1, 1)
                [
                    1, 'Jasmine', 1, 1234, 1, [1], $this->getRiskAndIntentToLeavePersonDetails([1])
                ],
                //No search filter, current cohort parameter, no risk group parameter, no pagination (0, 1, 0, 0)
                [
                    1, null, 3, null, null, [5,6], $this->getRiskAndIntentToLeavePersonDetails([5,6])
                ],
                //No search filter, current cohort parameter, risk group parameter, no pagination (0, 1, 1, 0)
                [
                    1, null, 3, 12345, null, [5], $this->getRiskAndIntentToLeavePersonDetails([5])
                ],
                //No search filter, current cohort parameter, risk group parameter, pagination (0, 1, 1, 1)
                [
                    1, null, 3, 12345, 1, [5], $this->getRiskAndIntentToLeavePersonDetails([5])
                ],
                //No search filter, no current cohort parameter, risk group parameter, no pagination(0, 0 , 1, 0)
                [
                    1, null, null, 12345, null, [4,5], $this->getRiskAndIntentToLeavePersonDetails([4,5])
                ],
                //No search filter, no current cohort parameter, risk group parameter, pagination (0, 0, 1, 1)
                [
                    1, null, null, 12345, 1, [4], $this->getRiskAndIntentToLeavePersonDetails([4])
                ],
                //Search filter, no current cohort parameter, no risk group parameter, pagination (0, 0, 0, 1)
                [
                    1, null, null, null, 1, [1], $this->getRiskAndIntentToLeavePersonDetails([1])
                ],
            ]
            ]
        );
    }




    public function testUpdatePersons()
    {
        $this->specify("Test get mapworks person", function ($organizationId, $personArray, $loggedInUserId, $errorType, $errorArray) {
            $this->errorArray = $errorArray;
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

            //this container is for entity validation service
            $mockContainerForEntityValidationService = $this->getMock('Container', array(
                'get'
            ));

            // Initializing parameters
            $personListDto = $this->setPersonListDTO($personArray);
            $organization = $this->setOrganization($organizationId);
            $loggedInUser = $this->setUser($loggedInUserId);

            // Mock Repositories
            $mockPersonRepository = $this->getMock('PersonRepository', ['findOneBy', 'persist']);
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', ['findOneBy']);
            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', ['findOneBy']);
            $mockRiskGroupRepository = $this->getMock('RiskGroupRepository', ['find']);
            $mockOrgRiskGroupModelRepository = $this->getMock('OrgRiskGroupModelRepository', ['findOneBy']);
            $mockRiskGroupPersonHistoryRepository = $this->getMock('RiskGroupPersonHistoryRepository', ['persist', 'findOneBy']);

            // Mock Services
            $mockValidator =  $this->getMock('Validator',['validate']);
            if($errorType == "optional"){
                $mockValidator->method('validate')->willReturnCallback(function($doctrineEntity, $test = null , $validationGroup){
                    if($validationGroup == "required"){
                        return [];
                    }else{
                        return $this->arrayOfErrorObjects($this->errorArray);
                    }
                });
            }

            if($errorType == "required"){
                $mockValidator->method('validate')->willReturnCallback(function($doctrineEntity, $test = null , $validationGroup){
                    if($validationGroup == "required"){
                        return $this->arrayOfErrorObjects($this->errorArray);
                    }else{
                        return [];
                    }
                });
            }

            $mockDataProcessingUtilityService = $this->getMock('DataProcessingUtilityService', ['setErrorMessageOrValueInArray']);
            $mockStudentService = $this->getMock('StudentService', ['determineStudentUpdateType']);
            $mockFacultyService = $this->getMock('StudentService', ['determineFacultyUpdateType']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        OrgPersonStudentRepository::REPOSITORY_KEY,
                        $mockOrgPersonStudentRepository
                    ],
                    [
                        RiskGroupPersonHistoryRepository::REPOSITORY_KEY,
                        $mockRiskGroupPersonHistoryRepository
                    ],
                    [
                        OrgPersonFacultyRepository::REPOSITORY_KEY,
                        $mockOrgPersonFacultyRepository
                    ],
                    [
                        RiskGroupRepository::REPOSITORY_KEY,
                        $mockRiskGroupRepository
                    ],
                    [
                        OrgRiskGroupModelRepository::REPOSITORY_KEY,
                        $mockOrgRiskGroupModelRepository
                    ],
                ]);

            $mockContainerForEntityValidationService->method('get')->willReturnMap([
                [
                    SynapseConstant::VALIDATOR,
                    $mockValidator

                ],
                [
                    DataProcessingUtilityService::SERVICE_KEY,
                    $mockDataProcessingUtilityService
                ],
                [
                    StudentService::SERVICE_KEY,
                    $mockStudentService
                ],
                [
                    FacultyService::SERVICE_KEY,
                    $mockFacultyService
                ],
            ]);


            // we are not mocking the entity validation service here as all the error processing logic is in here which we want to execute and not mock, and this does not use any database calls
            $entityValidationService =  new EntityValidationService($mockRepositoryResolver,$mockLogger, $mockContainerForEntityValidationService);

            $mockContainer->method('get')->willReturnMap([
                [
                    SynapseConstant::VALIDATOR,
                    $mockValidator

                ],
                [
                    EntityValidationService::SERVICE_KEY,
                    $entityValidationService
                ],
                [
                    DataProcessingUtilityService::SERVICE_KEY,
                    $mockDataProcessingUtilityService
                ],
                [
                    StudentService::SERVICE_KEY,
                    $mockStudentService
                ],
                [
                    FacultyService::SERVICE_KEY,
                    $mockFacultyService
                ],
            ]);

            // Initializing DataProcessingExceptionHandler Object
            $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();

            $mockDataProcessingUtilityService->method('setErrorMessageOrValueInArray')->willReturnCallback(function($records, $errorArray) {
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

            // Mock Organization
            $mockOrganization = $this->getMock('Organization', ['getId']);

            if ($errorType == 'invalid_external_id') {
                $mockPersonRepository->method('findOneBy')->willReturn(null);
            } else {
                $mockPerson = $this->getMock('Person', ['getId', 'setFirstname', 'setLastname', 'setUsername', 'setTitle', 'setModifiedAt', 'setModifiedBy', 'setOrganization', 'setAuthUsername', 'getOrganization', 'getExternalId', 'getFirstname', 'getLastname', 'getAuthUsername', 'getTitle', 'getUsername']);
                $mockPerson->method('getId')->willReturn($personArray['mapworks_internal_id']);
                $mockPerson->method('setFirstname')->willReturn($personArray['firstname']);
                $mockPerson->method('setLastname')->willReturn($personArray['lastname']);
                $mockPerson->method('setUsername')->willReturn($personArray['primary_email']);
                $mockPerson->method('setTitle')->willReturn('Mr.');
                $mockPerson->method('setAuthUsername')->willReturn($personArray['auth_username']);
                $mockPerson->method('setOrganization')->willReturn($organization);
                $mockPerson->method('setModifiedBy')->willReturn($loggedInUser);
                $mockPerson->method('setModifiedAt')->willReturn(new DateTime());
                $mockPerson->method('getOrganization')->willReturn($mockOrganization);
                $mockPerson->method('getExternalId')->willReturn($personArray['external_id']);
                $mockPerson->method('getFirstname')->willReturn($personArray['firstname']);
                $mockPerson->method('getLastname')->willReturn($personArray['lastname']);
                $mockPerson->method('getAuthUsername')->willReturn($personArray['auth_username']);
                $mockPerson->method('getUsername')->willReturn($personArray['primary_email']);
                $mockPersonRepository->method('findOneBy')->willReturn($mockPerson);
            }

            // Mock OrgPersonStudent
            $personObject = $this->setUser();
            $mockOrgPersonStudent = $this->getMock('OrgPersonStudent', ['setPerson', 'setPhotoUrl', 'setPersonIdPrimaryConnect', 'getOrganization', 'getPerson']);
            $mockOrgPersonStudent->method('setPerson')->willReturn($personObject);
            $mockOrgPersonStudent->method('setPersonIdPrimaryConnect')->willReturn($personObject);
            $photoUrl = isset($personArray['photo_url']) ? $personArray['photo_url'] : null;
            $mockOrgPersonStudent->method('setPhotoUrl')->willReturn($photoUrl);
            $mockOrgPersonStudent->method('getOrganization')->willReturn($mockOrganization);

            if (!empty($personArray['is_student'])) {
                $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($mockOrgPersonStudent);
            } else if ($errorType == 'optional' || $errorType == 'existing' || $errorType == '') {
                $mockOrgPersonStudentRepository->method('findOneBy')->willReturn(false);
            }

            if ($errorType == 'required') {
                $dataProcessingExceptionHandler->enqueueErrorsOntoExceptionObject($errorArray, "required");
            } else if ($errorType == 'invalid_risk_group_id') {
                $dataProcessingExceptionHandler->addErrors('Risk Group does not exist.', 'risk_group_id');
            } else {
                $dataProcessingExceptionHandler->enqueueErrorsOntoExceptionObject($errorArray, null);
                $mockStudentService->method('determineStudentUpdateType')->willReturn($dataProcessingExceptionHandler);
                $mockFacultyService->method('determineFacultyUpdateType')->willReturn(true);
            }

            $personService = new PersonService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $actualResult = $personService->updatePersons($personListDto, $organization, $loggedInUser);

            $skipped = false;
            if (!empty($errorType) && $errorType != 'skip' && $errorType != 'optional' && $errorType != 'invalid_risk_group_id') {
                $skipped = true;
            }

            $isSkipped = false;
            if ($errorType == 'skip') {
                $isSkipped = true;
            }

            $expectedResult = $this->generatePersonResponse($personArray, $errorArray, $skipped, true, $isSkipped);

            $this->assertEquals($expectedResult, $actualResult);

        }, [
                'examples' => [
                    // Test0: Test case for Validating external_id mapped to the organization
                    [
                        1,
                        $this->personDataForCreate[1],
                        1,
                        'invalid_external_id',
                        [
                            'external_id' => 'Person ID ' . $this->personDataForCreate[1]['external_id'] . ' is not valid at the organization.'
                        ]
                    ],
                    // Test1: Skipping a person array
                    [
                        1,
                        $this->personDataForCreate[5],
                        1,
                        'skip',
                        [],
                    ],
                    // Test2: Test case for Validating required fields
                    [
                        1,
                        $this->personDataForCreate[2],
                        1,
                        'required',
                        [
                            'external_id' => 'External ID cannot be empty',
                            'firstname' => 'Firstname cannot be empty'
                        ],
                    ],
                    // Test3: Validates invalid email and returns only error array
                    [
                        1,
                        $this->personDataForCreate[3],
                        1,
                        'required',
                        [
                            'primary_email' => 'Invalid email ID.'
                        ],
                    ],
                    // Test4: Validates existing email, external_id and returns error
                    [
                        1,
                        $this->personDataForCreate[4],
                        1,
                        'required',
                        [
                            'primary_email' => 'Email already in use.',
                            'external_id' => 'External Id already in use.',
                        ],
                    ],
                    // Test5: Validates person fields and returns error array
                    [
                        1,
                        $this->personDataForCreate[5],
                        1,
                        'optional',
                        [
                            'title' => 'Title cannot be longer than 100 characters',
                        ],
                    ],
                    // Test6: Updating person fields and returns updated array
                    [
                        1,
                        $this->personDataForCreate[1],
                        1,
                        '',
                        [],
                    ],
                    // Test7: Updating student with error fields fields and returns updated array
                    [
                        1,
                        $this->personDataForCreate[1],
                        1,
                        'invalid_risk_group_id',
                        [],
                    ],
                ]
        ]);
    }


    public function getRiskAndIntentToLeavePersonDetails($studentFilterKeys)
    {
        if(empty($studentFilterKeys)){
            return $this->riskIntendToLeavePersonData;
        }
        else {
            $resultArray = [];
            foreach ($studentFilterKeys as $studentFilterKey) {
                $resultArray[] = $this->riskIntendToLeavePersonData[$studentFilterKey];
            }
            return $resultArray;
        }
    }

}