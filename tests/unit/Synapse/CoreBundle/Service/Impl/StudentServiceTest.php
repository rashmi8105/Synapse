<?php
namespace Synapse\CoreBundle\Service\Impl;

use Codeception\Specify;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CampusConnectionBundle\Service\Impl\CampusConnectionService;
use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgPersonStudent;
use Synapse\CoreBundle\Entity\OrgPersonStudentYear;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\DataProcessingExceptionHandler;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\ActivityLogRepository;
use Synapse\CoreBundle\Repository\AppointmentRecepientAndStatusRepository;
use Synapse\CoreBundle\Repository\EbiMetadataListValuesRepository;
use Synapse\CoreBundle\Repository\FeatureMasterLangRepository;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\OrgMetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetDatablockRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetMetadataRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonEbiMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonOrgMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\StudentDbViewLogRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Utility\EntityValidationService;
use Synapse\CoreBundle\Service\Utility\URLUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\PersonBundle\DTO\PersonDTO;
use Synapse\RestBundle\Entity\AppointmentListArrayResponseDto;
use Synapse\RestBundle\Entity\StudentDetailsResponseDto;
use Synapse\RestBundle\Entity\StudentListArrayResponseDto;
use Synapse\RestBundle\Entity\StudentListHeaderResponseDto;
use Synapse\RestBundle\Entity\StudentOpenAppResponseDto;
use Synapse\RiskBundle\Entity\RiskLevels;
use Synapse\RiskBundle\Repository\RiskGroupPersonHistoryRepository;
use Synapse\RiskBundle\Repository\RiskGroupRepository;
use Synapse\RiskBundle\Service\Impl\RiskGroupService;
use Synapse\SearchBundle\Entity\IntentToLeave;
use Synapse\SurveyBundle\Repository\OrgPersonStudentSurveyLinkRepository;

class StudentServiceTest extends \PHPUnit_Framework_TestCase
{
    use Specify;

    private $personDataForCreate = [
        "1" => [
            'external_id' => 'X10001',
            'mapworks_internal_id' => 1,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell@ns2016.mapworks.com',
            'photo_link' => "http://googole.com",
            'is_student' => 1,
            'is_faculty' => 0,
            'primary_campus_connection_id' => 4567,
            'risk_group_id' => 1
        ],
        "2" => [
            'external_id' => 'X10001',
            'mapworks_internal_id' => 1,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell@ns2016.mapworks.com',
            'photo_link' => "http://googole.com",
            'is_student' => 0,
            'is_faculty' => 0,
            'primary_campus_connection_id' => 4567,
            'risk_group_id' => 1
        ],
        "3" => [
            'external_id' => 'X10001',
            'auth_username' => 'auth',
            'mapworks_internal_id' => 1,
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell@ns2016.mapworks.com',
            'is_student' => 1,
            'is_faculty' => 0,
            'risk_group_id' => 1,
            'fields_to_clear' => ['photo_link', 'primary_campus_connection_id', 'organization']
        ]
    ];

    public function testDetermineStudentUpdateType()
    {
        $this->specify("Test determine student update type", function ($personDTO, $personId, $organizationId, $actionType, $errorArray, $expectedArray) {
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

            $personObject = $this->getPersonInstance($personId);
            $personObject->setTitle('title');
            $personObject->setAuthUsername('auth_username');
            $organization = $this->getOrganizationInstance($organizationId);
            $loggedInUser = $this->getPersonInstance(2);
            $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();

            // Mock Repositories
            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', ['findOneBy', 'persist']);
            $mockPersonRepository = $this->getMock('PersonRepository', ['findOneBy']);
            $mockRiskGroupRepository = $this->getMock('RiskGroupRepository', ['find']);
            $mockRiskGroupPersonHistoryRepository = $this->getMock('RiskGroupPersonHistoryRepository', ['findOneBy', 'persist']);

            // Mock Services
            $mockCampusConnectionService = $this->getMock('CampusConnectionService', ['validatePrimaryCampusConnectionId']);
            $mockRiskGroupService = $this->getMock('RiskGroupService', ['validateRiskGroupBelongsToOrganization']);
            $mockURLUtilityService = $this->getMock('URLUtilityService', ['validatePhotoURL']);
            $mockPersonService = $this->getMock('PersonService', ['generateAuthKey']);
            $mockGroupService = $this->getMock('GroupService', ['addStudentSystemGroup']);
            $mockEntityValidationService = $this->getMock('EnitityValidationService', ['nullifyFieldsToBeCleared']);
            $mockEntityValidationService->method('nullifyFieldsToBeCleared')->willReturnCallback(function ($doctrineEntity, $clearFields) {


                $clearAttributesMappedToDBFields = [
                    'photo_link' => 'photo_url',
                    'primary_campus_connection_id' => 'person_id_primary_connect'
                ];
                $entity = (array)$doctrineEntity;
                $attributeArray = array_keys($entity);
                $entityAttributesArray = [];


                foreach ($attributeArray as $attribute) {
                    $attrName = explode("\000", $attribute);
                    $entityAttributesArray[] = $attrName[count($attrName) - 1];
                }

                foreach ($clearFields as $field) {

                    if (array_key_exists($field, $clearAttributesMappedToDBFields)) {
                        $field = $clearAttributesMappedToDBFields[$field];
                    }
                    $convertedAttribute = str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
                    $convertedAttribute = lcfirst($convertedAttribute);
                    if (in_array($convertedAttribute, $entityAttributesArray)) {
                        $setFunction = 'set' . ucfirst($convertedAttribute);
                        $doctrineEntity->$setFunction(null);
                    }
                }
                return $doctrineEntity;
            });


            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgPersonStudentRepository::REPOSITORY_KEY,
                        $mockOrgPersonStudentRepository
                    ],
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        RiskGroupRepository::REPOSITORY_KEY,
                        $mockRiskGroupRepository
                    ],
                    [
                        RiskGroupPersonHistoryRepository::REPOSITORY_KEY,
                        $mockRiskGroupPersonHistoryRepository
                    ]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        CampusConnectionService::SERVICE_KEY,
                        $mockCampusConnectionService
                    ],
                    [
                        RiskGroupService::SERVICE_KEY,
                        $mockRiskGroupService
                    ],
                    [
                        URLUtilityService::SERVICE_KEY,
                        $mockURLUtilityService
                    ],
                    [
                        PersonService::SERVICE_KEY,
                        $mockPersonService
                    ],
                    [
                        GroupService::SERVICE_KEY,
                        $mockGroupService
                    ],
                    [
                        EntityValidationService::SERVICE_KEY,
                        $mockEntityValidationService
                    ]
                ]);

            if ($actionType == 'update_student' || $actionType == 'remove_student') {


                $mockPerson = $this->getMock('Synapse\CoreBundle\Entity\Person', ['getId']);
                $mockPerson->method('getId')->willReturn($personId);
                $mockPerson->setTitle('title');
                $mockPerson->setAuthUserName('auth_uername');


                $mockOrganization = $this->getMock('Synapse\CoreBundle\Entity\Organization', ['getId']);
                $mockOrganization->method('getId')->willReturn($organizationId);


                $mockOrgPersonStudent = $this->getMock('Synapse\CoreBundle\Entity\OrgPersonStudent', ['getId', 'getPerson']);
                $mockOrgPersonStudent->method('getId')->willReturn(1);
                $mockOrgPersonStudent->method('getPerson')->willReturn($mockPerson);
                $mockOrgPersonStudent->setOrganization($mockOrganization);
                $mockOrgPersonStudent->setPersonIdPrimaryConnect($mockPerson);
                $mockOrgPersonStudent->setPhotoUrl('http://google.com');

                $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($mockOrgPersonStudent);

                $mockPersonRepository->method('findOneBy')->willReturn($mockPerson);

                $mockRiskGroup = $this->getMock('RiskGroup', ['getId']);
                $mockRiskGroup->method('getId')->willReturn(1);
                $mockRiskGroupRepository->method('find')->willReturn($mockRiskGroup);

                $mockRiskGroupPersonHistoryRepository->method('findOneBy')->willReturn(null);
            } else {
                $mockOrgPersonStudentRepository->method('findOneBy')->willReturn(null);

                $mockPersonService->method('generateAuthKey')->willReturn('test');
                $mockGroupService->method('addStudentSystemGroup')->willReturn(true);
            }

            if (array_key_exists('invalid_photo_link', $errorArray)) {
                $mockURLUtilityService->method('validatePhotoURL')->willReturn(false);
            } else {
                $mockURLUtilityService->method('validatePhotoURL')->willReturn(true);
            }

            if (array_key_exists('invalid_primary_campus', $errorArray)) {
                $mockCampusConnectionService->method('validatePrimaryCampusConnectionId')->willReturn($errorArray['invalid_primary_campus']);
            } else {
                $mockCampusConnectionService->method('validatePrimaryCampusConnectionId')->willReturn(true);
            }

            if (array_key_exists('invalid_risk_group_id', $errorArray)) {
                $mockRiskGroupService->method('validateRiskGroupBelongsToOrganization')->willReturn($errorArray['invalid_risk_group_id']);
            } else {
                $mockRiskGroupService->method('validateRiskGroupBelongsToOrganization')->willReturn(true);
            }

            $studentService = new StudentService($mockRepositoryResolver, $mockLogger, $mockContainer);


            $checkForFieldsToClear = !empty($personDTO->getFieldsToClear()) && $actionType == "update_student";

            if ($checkForFieldsToClear) {
                // Ensure that the entities have values in them before the method is called
                $this->assertNotNull($mockOrgPersonStudent->getPhotoUrl());
                $this->assertNotNull($mockOrgPersonStudent->getPersonIdPrimaryConnect());
                $this->assertNotNull($mockOrgPersonStudent->getOrganization()); // organization Object should not get reset
            }
            $actualResult = $studentService->determineStudentUpdateType($personObject, $personDTO, $organization, $loggedInUser, $dataProcessingExceptionHandler);
            if ($checkForFieldsToClear) {
                // After the method call the entity attributes are set to null as they would be cleared
                $this->assertNull($mockOrgPersonStudent->getPhotoUrl());
                $this->assertNull($mockOrgPersonStudent->getPersonIdPrimaryConnect());
                $this->assertNotNull($mockOrgPersonStudent->getOrganization()); // organization Object should not get reset
            }

            // Reset all errors before getting $expectedResults
            $dataProcessingExceptionHandler->resetAllErrors();
            $dataProcessingExceptionHandler->enqueueErrorsOntoExceptionObject($expectedArray, null);
            $expectedResult = $dataProcessingExceptionHandler->getAllErrors();

            $this->assertInstanceOf('Synapse\CoreBundle\Exception\DataProcessingExceptionHandler', $actualResult);
            $this->assertEquals($actualResult->getAllErrors(), $expectedResult);


        }, [
                'examples' => [
                    // Test0: Update student with invalid errors, returns DataProcessingExceptionHandler
                    [
                        $this->getPersonDTO($this->personDataForCreate[1]),
                        95,
                        203,
                        'update_student',
                        [
                            'invalid_primary_campus' => "Invalid Primary Campus Connection Id",
                            'invalid_risk_group_id' => "Risk Group does not exist.",
                            'invalid_photo_link' => "Invalid Photo URL. Please try another URL."
                        ],
                        [
                            'primary_campus_connection_id' => "Invalid Primary Campus Connection Id",
                            'risk_group_id' => "Risk Group does not exist.",
                            'photo_link' => "Invalid Photo URL. Please try another URL."
                        ]
                    ],
                    // Test1: Update student with other errors, returns DataProcessingExceptionHandler
                    [
                        $this->getPersonDTO($this->personDataForCreate[1]),
                        95,
                        203,
                        'update_student',
                        [
                            'invalid_primary_campus' => "Faculty does not have access to this student: 95",
                            'invalid_risk_group_id' => "Risk Group is not mapped to any organization."
                        ],
                        [
                            'primary_campus_connection_id' => "Faculty does not have access to this student: 95",
                            'risk_group_id' => "Risk Group is not mapped to any organization."
                        ]
                    ],
                    // Test2: Update student without errors, returns DataProcessingExceptionHandler
                    [
                        $this->getPersonDTO($this->personDataForCreate[1]),
                        95,
                        203,
                        'update_student',
                        [],
                        []
                    ],
                    // Test3: Remove student, returns DataProcessingExceptionHandler
                    [
                        $this->getPersonDTO($this->personDataForCreate[2]),
                        95,
                        203,
                        'remove_student',
                        [],
                        []
                    ],
                    // Test4: Create student with invalid errors, returns DataProcessingExceptionHandler
                    [
                        $this->getPersonDTO($this->personDataForCreate[1]),
                        95,
                        203,
                        'create_student',
                        [
                            'invalid_primary_campus' => "Invalid Primary Campus Connection Id",
                            'invalid_risk_group_id' => "Risk Group does not exist.",
                            'invalid_photo_link' => "Invalid Photo URL. Please try another URL."
                        ],
                        [
                            'primary_campus_connection_id' => "Invalid Primary Campus Connection Id",
                            'risk_group_id' => "Risk Group does not exist.",
                            'photo_link' => "Invalid Photo URL. Please try another URL."
                        ]
                    ],
                    // Test5: Create student with invalid errors, returns DataProcessingExceptionHandler
                    [
                        $this->getPersonDTO($this->personDataForCreate[1]),
                        95,
                        203,
                        'create_student',
                        [
                            'invalid_primary_campus' => "Faculty does not have access to this student: 95",
                            'invalid_risk_group_id' => "Risk Group is not mapped to any organization."
                        ],
                        [
                            'primary_campus_connection_id' => "Faculty does not have access to this student: 95",
                            'risk_group_id' => "Risk Group is not mapped to any organization."
                        ]
                    ],
                    // Test6: Create student without errors, returns DataProcessingExceptionHandler
                    [
                        $this->getPersonDTO($this->personDataForCreate[1]),
                        95,
                        203,
                        'create_student',
                        [],
                        []
                    ],
                    // Test7:  clearing up the values  for the entities
                    [
                        $this->getPersonDTO($this->personDataForCreate[3]),
                        95,
                        203,
                        'update_student',
                        [],
                        []
                    ],


                ]
            ]
        );
    }

    public function testModifyStudentPrimaryCampusConnection()
    {
        $this->specify("Test Modify Student Primary Campus Connection", function ($primaryCampusConnectionId, $personId, $errorType, $expectedResult) {
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

            // Mock Repositories
            $mockPersonRepository = $this->getMock('PersonRepository', ['findOneBy']);

            // Mock Services
            $mockCampusConnectionService = $this->getMock('CampusConnectionService', ['validatePrimaryCampusConnectionId']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        CampusConnectionService::SERVICE_KEY,
                        $mockCampusConnectionService
                    ]
                ]);

            $orgPersonStudent = $this->setOrgPersonStudent($personId);
            $errorText = '';
            if (!empty($errorType)) {
                if ($errorType == 'invalid_primary_connection') {
                    $errorText = "Invalid Primary Campus Connection Id";
                } else {
                    $errorText = "Faculty does not have access to this student: " . $personId;
                }
            }
            $validateResponse = !empty($errorType) ? $errorText : true;

            $mockCampusConnectionService->method('validatePrimaryCampusConnectionId')->willReturn($validateResponse);

            $studentService = new StudentService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $actualResult = $studentService->modifyStudentPrimaryCampusConnection($primaryCampusConnectionId, $orgPersonStudent);

            $this->assertEquals($expectedResult, $actualResult);
        }, [
                'examples' => [
                    // case 1 : validates invalid Campus Connection Id, returns invalid_primary_connection error
                    [
                        "1234",
                        95,
                        'invalid_primary_connection',
                        ['primary_campus_connection_id' => "Invalid Primary Campus Connection Id"]
                    ],
                    // case 2 : validates faculty does not have student access, returns does not have access error
                    [
                        "256",
                        95,
                        'no_student_access',
                        ['primary_campus_connection_id' => "Faculty does not have access to this student: 95"]
                    ],
                    // case 3 :  validates valid primary connection with student access, returns OrgPersonStudent
                    [
                        "256",
                        95,
                        '',
                        $this->setOrgPersonStudent(95)
                    ]
                ]
            ]
        );
    }

    public function testModifyStudentRiskGroup()
    {
        $this->specify("Test Modify Student Risk Group ", function ($riskGroupId, $personId, $errorType, $expectedResult) {
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

            // Mock Repositories
            $mockRiskGroupRepository = $this->getMock('RiskGroupRepository', ['find']);
            $mockRiskGroupPersonHistoryRepository = $this->getMock('RiskGroupPersonHistoryRepository', ['findOneBy', 'persist']);

            // Mock Services
            $mockRiskGroupService = $this->getMock('RiskGroupService', ['validateRiskGroupBelongsToOrganization']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        RiskGroupRepository::REPOSITORY_KEY,
                        $mockRiskGroupRepository
                    ],
                    [
                        RiskGroupPersonHistoryRepository::REPOSITORY_KEY,
                        $mockRiskGroupPersonHistoryRepository
                    ]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        RiskGroupService::SERVICE_KEY,
                        $mockRiskGroupService
                    ]
                ]);

            $orgPersonStudent = $this->setOrgPersonStudent($personId);
            $errorText = '';
            if (!empty($errorType)) {
                if ($errorType == 'invalid_risk_group') {
                    $errorText = "Risk Group does not exist.";
                } else {
                    $errorText = "Risk Group is not mapped to any organization.";
                }
            }
            $validateResponse = !empty($errorType) ? $errorText : true;

            $mockRiskGroupService->method('validateRiskGroupBelongsToOrganization')->willReturn($validateResponse);

            $mockRiskGroupPersonHistoryRepository->method('findOneBy')->willReturn(null);

            $studentService = new StudentService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $actualResult = $studentService->modifyStudentRiskGroup($riskGroupId, $orgPersonStudent);

            $this->assertEquals($expectedResult, $actualResult);
        }, [
                'examples' => [
                    // case 1 : validates invalid risk group id, returns invalid risk group error
                    [
                        "1234",
                        95,
                        'invalid_risk_group',
                        ['risk_group_id' => "Risk Group does not exist."]
                    ],
                    // case 2 : validates valid risk but not mapped to any organization, returns risk not mapped error
                    [
                        "256",
                        95,
                        'not_mapped',
                        ['risk_group_id' => "Risk Group is not mapped to any organization."]
                    ],
                    // case 3 :  validates valid risk mapped to organization, returns OrgPersonStudent
                    [
                        "256",
                        95,
                        '',
                        $this->setOrgPersonStudent(95)
                    ]
                ]
            ]
        );
    }

    public function testModifyStudentPhotoLink()
    {
        $this->specify("Test Modify Student Risk Group ", function ($photoLink, $personId, $validPhotoLink, $expectedResult) {
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

            // Mock Service
            $mockUrlUtilityService = $this->getMock('UrlUtilityService', ['validatePhotoURL']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        URLUtilityService::SERVICE_KEY,
                        $mockUrlUtilityService
                    ]
                ]);

            $orgPersonStudent = $this->setOrgPersonStudent($personId);

            $validateResponse = $validPhotoLink ? true : false;

            $mockUrlUtilityService->method('validatePhotoURL')->willReturn($validateResponse);

            $studentService = new StudentService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $actualResult = $studentService->modifyStudentPhotoLink($photoLink, $orgPersonStudent);

            $this->assertEquals($expectedResult, $actualResult);
        }, [
                'examples' => [
                    // case 1 : valid photo url , returns OrgPersonStudent Object
                    [
                        'http://php.net/manual/en/images/c0d23d2d6769e53e24a1b3136c064577-php_logo.png',
                        95,
                        true,
                        $this->setOrgPersonStudent(95, 'http://php.net/manual/en/images/c0d23d2d6769e53e24a1b3136c064577-php_logo.png')
                    ],
                    // case 2 : invalid photo url, returns error
                    [
                        'invalid-photo.png',
                        95,
                        false,
                        ['photo_link' => "Invalid Photo URL. Please try another URL."]
                    ]
                ]]
        );
    }

    private function setOrgPersonStudent($personId, $photoUrl = null)
    {
        $personObject = new Person();
        $personObject->setId($personId);

        $organizationObject = new Organization();
        $organizationObject->setExternalId('org111');

        $orgPersonStudentObject = new OrgPersonStudent();
        $orgPersonStudentObject->setPerson($personObject);
        $orgPersonStudentObject->setOrganization($organizationObject);
        if ($photoUrl) {
            $orgPersonStudentObject->setPhotoUrl($photoUrl);
        }
        return $orgPersonStudentObject;
    }

    private function getPersonInstance($externalId = '123')
    {
        $person = new Person();
        $person->setExternalId($externalId);
        return $person;
    }


    private function getOrganizationInstance($campusId = 1)
    {
        $organization = new Organization();
        $organization->setExternalId('ABC123');
        $organization->setCampusId($campusId);
        return $organization;
    }

    private function getPersonDTO($personData)
    {
        $personDTO = new PersonDTO();

        $personDTO->setAuthUsername($personData['auth_username']);
        $personDTO->setPrimaryEmail($personData['primary_email']);
        $personDTO->setExternalId($personData['external_id']);
        $personDTO->setMapworksInternalId($personData['mapworks_internal_id']);
        $personDTO->setFirstname($personData['firstname']);
        $personDTO->setLastname($personData['lastname']);
        if (isset($personData['photo_url'])) {
            $personDTO->setPhotoLink($personData['photo_link']);
        }
        if (isset($personData['primary_campus_connection_id'])) {
            $personDTO->setPrimaryCampusConnectionId($personData['primary_campus_connection_id']);
        }
        $personDTO->setIsStudent($personData['is_student']);
        $personDTO->setIsFaculty($personData['is_faculty']);
        $personDTO->setTitle("Mr.");
        if (isset($personData['risk_group_id'])) {
            $personDTO->setRiskGroupId($personData['risk_group_id']);
        }
        if (!empty($personData['fields_to_clear'])) {
            $personDTO->setFieldsToClear($personData['fields_to_clear']);
        } else {
            $personDTO->setFieldsToClear([]);
        }
        return $personDTO;
    }

    public function testIsPersonAStudent()
    {
        $this->specify("Test is person a student", function ($studentId, $errorType, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            // Mock Repository
            $mockOrgPersonStudentRepository = $this->getMock('orgPersonStudentRepository', ['findOneBy']);
            if ($errorType == 'valid') {
                $mockPerson = $this->getPersonInstance('1');
            } else {
                $mockPerson = null;
            }
            $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($mockPerson);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgPersonStudentRepository::REPOSITORY_KEY,
                        $mockOrgPersonStudentRepository
                    ],
                ]);

            $studentService = new StudentService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $studentService->isPersonAStudent($studentId);

            $this->assertEquals($results, $expectedResult);

        }, [
            'examples' => [
                // Test0: Case when person is not a student
                [
                    1,
                    "invalid",
                    false
                ],
                // Test1: Case when person is a student
                [
                    1,
                    "valid",
                    true
                ]
            ]
        ]);
    }

    public function testGetActivityObjectArray()
    {
        $this->specify("Converting array of activities to array of StudentListArrayResponseDto", function ($activityArray) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));


            $studentService = new StudentService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $studentService->getActivityObjectArray($activityArray);
            $this->assertEquals(count($activityArray), count($results));
            for ($i = 0; $i < count($activityArray); $i++) {
                $this->assertInstanceOf('Synapse\RestBundle\Entity\StudentListArrayResponseDto', $results[$i]);
                $this->assertEquals($results[$i]->getActivityId(), $activityArray[$i]['activity_id']);
                $this->assertEquals($results[$i]->getActivityLogId(), $activityArray[$i]['activity_log_id']);
                $this->assertEquals($results[$i]->getActivityType(), $activityArray[$i]['activity_type']);
                $this->assertEquals($results[$i]->getActivityCreatedById(), $activityArray[$i]['activity_created_by_id']);
                $this->assertEquals($results[$i]->getActivityCreatedByFirstName(), $activityArray[$i]['activity_created_by_first_name']);
                $this->assertEquals($results[$i]->getActivityCreatedByLastName(), $activityArray[$i]['activity_created_by_last_name']);
                $this->assertEquals($results[$i]->getActivityReasonId(), $activityArray[$i]['activity_reason_id']);
                $this->assertEquals($results[$i]->getActivityReasonText(), $activityArray[$i]['activity_reason_text']);
                $this->assertEquals($results[$i]->getActivityDescription(), $activityArray[$i]['activity_description']);
                $this->assertEquals($results[$i]->getActivityReferralStatus(), $activityArray[$i]['activity_referral_status']);
            }

        }, [
            'examples' => [
                // testing with an empty array
                [
                  []
                ],
                // testing with an activity array with activity type note
                [
                    [
                        $this->createActivityArray(1, 1, "10-10-2017", "firstname", "lastname", "note", null)
                    ]
                ],
                // testing with an activity array with activity type referral
                [
                    [
                        $this->createActivityArray(2, 3, "10-10-2017", "secondname", "secondlastname", "referral", true)
                    ]
                ],
                // testing with an activity array with two activities of activity type email and appointment
                [
                    [
                        $this->createActivityArray(4, 5, "10-10-2017", "thirdname", "thirdname", "email", null),
                        $this->createActivityArray(6, 7, "10-10-2017", "fourthname", "fourthlastname", "appointment", null)
                    ]
                ],
                // testing with an activity array with 3 activities of activity type email and appointment and note
                [
                    [
                        $this->createActivityArray(1, 1, "10-10-2017", "firstname", "lastname", "note", null),
                        $this->createActivityArray(4, 5, "10-10-2017", "thirdname", "thirdname", "email", null),
                        $this->createActivityArray(6, 7, "10-10-2017", "fourthname", "fourthlastname", "appointment", null)
                    ]
                ],
            ]
        ]);
    }


    private function createActivityArray($activityId, $activityLogId, $activityDate, $firstName, $lastName, $activityType, $referralStatus)
    {

        $activityDate = new \DateTime($activityDate);
        return [
            'activity_id' => $activityId,
            'activity_log_id' => $activityLogId,
            'activity_date' => $activityDate,
            'activity_type' => $activityType,
            'activity_created_by_id' => 1,
            'activity_created_by_first_name' => $firstName,
            'activity_created_by_last_name' => $lastName,
            'activity_reason_id' => 1,
            'activity_reason_text' => "some Reason",
            'activity_description' => "some desc",
            'activity_referral_status' => $referralStatus
        ];

    }


    public function testGetStudentDetails()
    {
        $this->specify("Test is person a student", function ($studentId, $loggedInPerson, $accessToStudentId, $studentIsParticipant, $hasDataBlocks, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockRbacManager = $this->getMock('Manager', ['checkAccessToOrganizationUsingPersonId', 'assertPermissionToEngageWithStudents']);

            if ($accessToStudentId) {
                $mockRbacManager->method('checkAccessToOrganizationUsingPersonId')->willReturn(1);
            } else {
                $mockRbacManager->method('checkAccessToOrganizationUsingPersonId')->willThrowException(new AccessDeniedException("Unauthorized access"));
            }

            if ($studentIsParticipant) {
                $mockRbacManager->method('assertPermissionToEngageWithStudents')->willReturn(1);
            } else {
                $mockRbacManager->method('assertPermissionToEngageWithStudents')->willThrowException(new AccessDeniedException("Non Participant Student"));
            }

            $mockOrgGroupFacultyRepository = $this->getMock('OrgGroupFacultyRepository', ['getPermissionsByFacultyStudent']);

            if ($hasDataBlocks) {
                $mockOrgGroupFacultyRepository->method('getPermissionsByFacultyStudent')->willReturn([1]);
            } else {
                $mockOrgGroupFacultyRepository->method('getPermissionsByFacultyStudent')->willReturn([]);
            }


            $mockOrgPermissionsetDatablockRepository = $this->getMock('orgPermissionsetDatablockRepository', ['getAllblockIdByPermissions']);

            $mockOrgPermissionsetDatablockRepository->method('getAllblockIdByPermissions')->willReturn([
                'block_id' => 1
            ]);

            $mockOrgPermissionsetMetadataRepository = $this->getMock('orgPermissionsetMetadataRepository', ['getAllMetadataIdByPermissions']);

            $mockPersonEbiMetadataRepository = $this->getMock('personEbiMetadataRepository', ['getStudentProfileInformation']);

            $mockPersonEbiMetadataRepository->method('getStudentProfileInformation')->willReturn([[
                'metadata_type' => "type",
                'year_name' => "2017",
                'term_name' => "FALL",
                'meta_name' => "Test",
                'datablock_desc' => "Testing",
                'metadata_value' => "Test"
            ]]);
            $mockPersonOrgMetadataRepository = $this->getMock('personOrgMetadataRepository', []);

            $mockOrgMetadataListValuesRepository = $this->getMock('orgMetadataListValuesRepository', []);
            $mockEbiMetadataListValuesRepository = $this->getMock('ebiMetadataListValuesRepository', ['getListValues']);


            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [
                        OrgGroupFacultyRepository::REPOSITORY_KEY,
                        $mockOrgGroupFacultyRepository
                    ],
                    [
                        OrgPermissionsetDatablockRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetDatablockRepository
                    ],
                    [
                        OrgPermissionsetMetadataRepository::REPOSITORY_KEY,
                        $mockOrgPermissionsetMetadataRepository
                    ],
                    [
                        PersonEbiMetaDataRepository::REPOSITORY_KEY,
                        $mockPersonEbiMetadataRepository
                    ],
                    [
                        PersonOrgMetaDataRepository::REPOSITORY_KEY,
                        $mockPersonOrgMetadataRepository

                    ],
                    [
                        OrgMetadataListValuesRepository::REPOSITORY_KEY,
                        $mockOrgMetadataListValuesRepository
                    ],
                    [
                        EbiMetadataListValuesRepository::REPOSITORY_KEY,
                        $mockEbiMetadataListValuesRepository
                    ]
                ]
            );

            $mockContainer->method('get')->willReturnMap([
                    [
                        Manager::SERVICE_KEY,
                        $mockRbacManager
                    ]
                ]
            );

            $studentService = new StudentService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $results = $studentService->getStudentDetails($studentId, $loggedInPerson);
                $this->assertInstanceOf('Synapse\RestBundle\Entity\StudentProfileResponseDto', $results);
                $this->assertEquals($results->getPersonStaffId(), $loggedInPerson->getId());
                $this->assertEquals($results->getPersonStudentId(), $studentId);
                $this->assertEquals($results->getProfile(), $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                // testing with student having datablocks
                [
                    1, $this->createPersonObject(2, "faculty", "lastname"), $accessToStudentId = true, $studentIsParticipant = true, true, [[
                    'block_name' => 'Testing',
                    'items' => [
                        '0' => [
                            'year_name' => '2017',
                            'term_name' => 'FALL',
                            'name' => 'Test',
                            'value' => 'Test',
                        ]
                    ]

                ]]
                ],
                // testing with student not having datablock
                [
                    1, $this->createPersonObject(2, "faculty", "lastname"), $accessToStudentId = true, $studentIsParticipant = true, false, []
                ],
                // testing with student  for which the  faculty does not have access to , throws exception
                [
                    1, $this->createPersonObject(2, "faculty", "lastname"), $accessToStudentId = false, $studentIsParticipant = true, false, "Unauthorized access"
                ],
                // testing with a non participant student , throws exception
                [
                    1, $this->createPersonObject(2, "faculty", "lastname"), $accessToStudentId = true, $studentIsParticipant = false, false, "Non Participant Student"
                ],

            ]
        ]);
    }


    public function testGetStudentsOpenAppointments()
    {
        $this->specify("Test students open appointments", function ($studentId, $loggedInPerson, $organizationId, $appointments, $isValidPerson, $expectedResult, $expectedException) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockActivityService = $this->getMock('ActivityService', ['getSharingAccess']);
            $mockManagerService = $this->getMock('Manager', ['assertPermissionToEngageWithStudents']);


            $mockContainer->method('get')
                ->willReturnMap([
                    [ActivityService::SERVICE_KEY, $mockActivityService],
                    [Manager::SERVICE_KEY, $mockManagerService],
                ]);

            $mockAppointmentsRecipientAndStatusRepository = $this->getMock('AppointmentRecepientAndStatusRepository', ['getTotalUpcomingAppointments', 'getTotalAppointments', 'findOneBy']);
            $mockPersonRepository = $this->getMock('PersonRepository', ['findOneBy']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [AppointmentRecepientAndStatusRepository::REPOSITORY_KEY, $mockAppointmentsRecipientAndStatusRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                ]);

            if ($isValidPerson) {
                $mockPerson = $this->getMock('Person', ['getId', 'getOrganization']);
                $mockPersonRepository->method('findOneBy')->willReturn($mockPerson);
            } else {
                $invalidStudentException = new SynapseValidationException($expectedException);
                $mockPersonRepository->method('findOneBy')->willThrowException($invalidStudentException);
            }

            $mockAppointmentsRecipientAndStatusRepository->method('getTotalUpcomingAppointments')->willReturn(count($appointments));
            $mockAppointmentsRecipientAndStatusRepository->method('getTotalAppointments')->willReturn($appointments);

            $mockAppointmentRecipient = $this->getMock('AppointmentRecepientAndStatus', ['getOrganization', 'getPersonIdFaculty', 'getPersonIdStudent']);
            $mockAppointmentsRecipientAndStatusRepository->method('findOneBy')->willReturn($mockAppointmentRecipient);
            $mockPerson = $this->getMock('Person', ['getId', 'getOrganization']);
            $mockAppointmentRecipient->method('getPersonIdFaculty')->willReturn($mockPerson);
            $mockPerson->method('getId')->willReturn($loggedInPerson);
            $studentService = new StudentService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $results = $studentService->getStudentsOpenAppointments($studentId, $loggedInPerson, $organizationId);
                $this->assertEquals($results, $expectedResult);
            } catch (\Exception $exception) {
                $this->assertEquals($exception->getMessage(), $expectedException);
            }

        }, [
            'examples' => [
                // Passing empty student id should throw an exception
                [
                    '',
                    5050960,
                    203,
                    [],
                    false,
                    [],
                    'Not a Valid Student.'
                ],
                // Open appointments for the same day.
                [
                    5050962,
                    5050960,
                    203,
                    [
                        [
                            'id' => 2853,
                            'startDate' => new \DateTime('+1 hour'),
                            'endDate' => new \DateTime('+2 hour')
                        ],
                    ],
                    true,
                    $this->createStudentOpenAppointmentDto([
                        'appointments' => [
                            [
                                'id' => 2853,
                                'startDate' => new \DateTime('+1 hour'),
                                'endDate' => new \DateTime('+2 hour')
                            ]
                        ],
                        'person_id_student' => 5050962,
                        'person_id_faculty' => 5050960,
                        'total_appointments' => 1,
                        'missed_appointments' => '',
                        'appointments_by_me' => 1,
                        'today_appointments' => 1
                    ]),
                    NULL,
                ],
                // Passing invalid student id should throw an exception
                [
                    'invalid',
                    5050960,
                    203,
                    [],
                    false,
                    [],
                    'Not a Valid Student.'
                ],
                // Passing empty organization id should throw an exception
                [
                    5050962,
                    5050960,
                    '',
                    [],
                    false,
                    [],
                    'Not a Valid Student.'
                ],
                // Open appointments for the same day and past time.
                [
                    5050962,
                    5050960,
                    203,
                    [
                        [
                            'id' => '54879',
                            'startDate' => '2017-11-21 10:00:00',
                            'endDate' => '2017-11-21 11:00:00',
                        ],
                        [
                            'id' => 2853,
                            'startDate' => new \DateTime('+1 hour'),
                            'endDate' => new \DateTime('+2 hour')
                        ],
                        [
                            'id' => '5412',
                            'startDate' => '2017-11-25 18:00:00',
                            'endDate' => '2017-11-25 19:00:00',
                        ]
                    ],
                    true,
                    $this->createStudentOpenAppointmentDto([
                        'appointments' => [
                            [
                                'id' => 2853,
                                'startDate' => new \DateTime('+1 hour'),
                                'endDate' => new \DateTime('+2 hour')
                            ],
                            [
                                'id' => '54879',
                                'startDate' => '2017-11-21 10:00:00',
                                'endDate' => '2017-11-21 11:00:00',
                            ],
                            [
                                'id' => '5412',
                                'startDate' => '2017-11-25 18:00:00',
                                'endDate' => '2017-11-25 19:00:00',
                            ]
                        ],
                        'person_id_student' => 5050962,
                        'person_id_faculty' => 5050960,
                        'total_appointments' => 3,
                        'missed_appointments' => '',
                        'appointments_by_me' => 3,
                        'today_appointments' => 1
                    ]),
                    NULL
                ]
            ]
        ]);
    }

    public function testGetStudentActivityList()
    {
        $this->specify("Test getStudentActivityList", function ($studentId, $category, $isInteraction, $organizationId, $facultyId, $accessToOrganization, $studentIsParticipant, $isPersonAvailable, $expectedErrorMessage, $activityCount, $activityArray, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockRbacManager = $this->getMock('Manager', ['checkAccessToOrganizationUsingPersonId', 'assertPermissionToEngageWithStudents']);
            $mockActivityService = $this->getMock('ActivityService', ['getSharingAccess', 'getAllActivities', 'getPermission', 'getStudentAppointmentList', 'getStudentNotes', 'getStudentReferralList', 'getStudentEmailList', 'getStudentContacts']);
            $mockAcademicYearService = $this->getMock('AcademicYearService', ['findCurrentAcademicYearForOrganization']);
            $mockContainer->method('get')->willReturnMap([
                    [AcademicYearService::SERVICE_KEY, $mockAcademicYearService],
                    [ActivityService::SERVICE_KEY, $mockActivityService],
                    [Manager::SERVICE_KEY, $mockRbacManager]
                ]
            );
            if ($accessToOrganization) {
                $mockRbacManager->method('checkAccessToOrganizationUsingPersonId')->willReturn(1);
            } else {
                $mockRbacManager->method('checkAccessToOrganizationUsingPersonId')->willThrowException(new AccessDeniedException($expectedErrorMessage));
            }
            if ($studentIsParticipant) {
                $mockRbacManager->method('assertPermissionToEngageWithStudents')->willReturn(1);
            } else {
                $mockRbacManager->method('assertPermissionToEngageWithStudents')->willThrowException(new AccessDeniedException($expectedErrorMessage));
            }
            $mockActivityLogRepository = $this->getMock('ActivityLogRepository', ['getContactActivityCount', 'getNoteActivityCount', 'getAppointmentsActivityCount', 'getEmailActivityCount', 'getReferralsActivityCount']);
            $mockFeatureMasterLangRepository = $this->getMock('FeatureMasterLangRepository', ['findOneBy']);
            $mockPersonRepository = $this->getMock('PersonRepository', ['findOneBy']);
            $mockFeatureMasterLang = $this->getMock('FeatureMasterLang', ['getId']);
            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [ActivityLogRepository::REPOSITORY_KEY, $mockActivityLogRepository],
                    [FeatureMasterLangRepository::REPOSITORY_KEY, $mockFeatureMasterLangRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository]
                ]
            );
            if ($isPersonAvailable) {
                $mockPerson = $this->getMock('Person', ['getId', 'getOrganization']);
                $mockPersonRepository->method('findOneBy')->willReturn($mockPerson);
            } else {
                $mockPersonRepository->method('findOneBy')->willThrowException(new SynapseValidationException($expectedErrorMessage));
            }
            $mockFeatureMasterLangRepository->method('findOneBy')->willReturn($mockFeatureMasterLang);

            $mockActivityLogRepository->method('getContactActivityCount')->willReturn($activityCount['total_contacts']);
            $mockActivityLogRepository->method('getNoteActivityCount')->willReturn($activityCount['total_notes']);
            $mockActivityLogRepository->method('getAppointmentsActivityCount')->willReturn($activityCount['total_appointments']);
            $mockActivityLogRepository->method('getEmailActivityCount')->willReturn($activityCount['total_email']);
            $mockActivityLogRepository->method('getReferralsActivityCount')->willReturn($activityCount['total_referrals']);

            $mockActivityService->method('getAllActivities')->willReturn([]);
            $mockActivityService->method('getStudentAppointmentList')->willReturn($activityArray);
            $mockActivityService->method('getStudentNotes')->willReturn($activityArray);
            $mockActivityService->method('getStudentReferralList')->willReturn($activityArray);
            $mockActivityService->method('getStudentEmailList')->willReturn($activityArray);
            $mockActivityService->method('getStudentContacts')->willReturn($activityArray);

            $studentService = new StudentService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $results = $studentService->getStudentActivityList($studentId, $category, $isInteraction, $organizationId, $facultyId);
                $this->assertEquals($results, $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($e->getMessage(), $expectedErrorMessage);
            }
        }, [
            'examples' => [
                // Note activity list.
                [
                    4545845, 'note', false, 203, 454545, true, true, true, '',
                    [
                        'total_activities' => 7,
                        'total_notes' => 2,
                        'total_contacts' => 0,
                        'total_referrals' => 2,
                        'total_appointments' => 1,
                        'total_email' => 2
                    ],
                    [
                        [
                            "activity_id" => 282804,
                            "activity_type" => "Note",
                            "activity_date" => new \DateTime("2018-10-10 12:00:00"),
                            "activity_created_by_id" => 5050960,
                            "activity_created_by_first_name" => "First",
                            "activity_created_by_last_name" => "Name",
                            "activity_reason_id" => 20,
                            "activity_reason_text" => "Class attendance positive",
                            "activity_description" => "Notes",
                            "activity_referral_status" => "",
                            "activity_log_id" => 6284900
                        ]
                    ],
                    $this->createStudentListHeaderResponseDto(4545845,
                        [
                            'total_activities' => 7,
                            'total_notes' => 2,
                            'total_contacts' => 0,
                            'total_referrals' => 2,
                            'total_appointments' => 1,
                            'total_email' => 2,
                            'interaction' => false,
                            'activities' => [
                                [
                                    "activity_id" => 282804,
                                    "activity_date" => new \DateTime("2018-10-10 12:00:00"),
                                    "activity_type" => "Note",
                                    "activity_created_by_id" => 5050960,
                                    "activity_created_by_first_name" => "First",
                                    "activity_created_by_last_name" => "Name",
                                    "activity_reason_id" => 20,
                                    "activity_reason_text" => "Class attendance positive",
                                    "activity_description" => "Notes",
                                    "activity_referral_status" => "",
                                    "activity_log_id" => 6284900
                                ]
                            ]
                        ])
                ],
                // Student don't have access to the organization should throw an exception.
                [4545845, 'all', true, 203, 454545, false, true, true, 'You do not have permission to access this resource', NULL, NULL, []],
                // Not a participant for the current academic year should throw an exception.
                [4545845, 'all', true, 203, 454545, true, false, true, 'You do not have permission to access this resource', NULL, NULL, []],
                // Appointments activity List
                [
                    4545845, 'appointment', false, 203, 454545, true, true, true, '',
                    [
                        'total_activities' => 7,
                        'total_notes' => 2,
                        'total_contacts' => 0,
                        'total_referrals' => 2,
                        'total_appointments' => 1,
                        'total_email' => 2
                    ],
                    [
                        [
                            "activity_id" => 282804,
                            "activity_type" => "Appointment",
                            "activity_date" => new \DateTime("2018-10-10 12:00:00"),
                            "activity_created_by_id" => 5050960,
                            "activity_created_by_first_name" => "First",
                            "activity_created_by_last_name" => "Name",
                            "activity_reason_id" => 20,
                            "activity_reason_text" => "Class attendance positive",
                            "activity_description" => "Appointment",
                            "activity_referral_status" => "",
                            "activity_log_id" => 6284900
                        ]
                    ],
                    $this->createStudentListHeaderResponseDto(4545845,
                        [
                            'total_activities' => 7,
                            'total_notes' => 2,
                            'total_contacts' => 0,
                            'total_referrals' => 2,
                            'total_appointments' => 1,
                            'total_email' => 2,
                            'interaction' => false,
                            'activities' => [
                                [
                                    "activity_id" => 282804,
                                    "activity_date" => new \DateTime("2018-10-10 12:00:00"),
                                    "activity_type" => "Appointment",
                                    "activity_created_by_id" => 5050960,
                                    "activity_created_by_first_name" => "First",
                                    "activity_created_by_last_name" => "Name",
                                    "activity_reason_id" => 20,
                                    "activity_reason_text" => "Class attendance positive",
                                    "activity_description" => "Appointment",
                                    "activity_referral_status" => "",
                                    "activity_log_id" => 6284900
                                ]
                            ]
                        ])
                ],
                // Referral activity list
                [
                    4545845, 'referral', false, 203, 454545, true, true, true, '',
                    [
                        'total_activities' => 7,
                        'total_notes' => 2,
                        'total_contacts' => 0,
                        'total_referrals' => 2,
                        'total_appointments' => 1,
                        'total_email' => 2
                    ],
                    [
                        [
                            "activity_id" => 282804,
                            "activity_type" => "referral",
                            "activity_date" => new \DateTime("2018-10-10 12:00:00"),
                            "activity_created_by_id" => 5050960,
                            "activity_created_by_first_name" => "First",
                            "activity_created_by_last_name" => "Name",
                            "activity_reason_id" => 20,
                            "activity_reason_text" => "Class attendance positive",
                            "activity_description" => "referral",
                            "activity_referral_status" => "",
                            "activity_log_id" => 6284900
                        ]
                    ],
                    $this->createStudentListHeaderResponseDto(4545845,
                        [
                            'total_activities' => 7,
                            'total_notes' => 2,
                            'total_contacts' => 0,
                            'total_referrals' => 2,
                            'total_appointments' => 1,
                            'total_email' => 2,
                            'interaction' => false,
                            'activities' => [
                                [
                                    "activity_id" => 282804,
                                    "activity_date" => new \DateTime("2018-10-10 12:00:00"),
                                    "activity_type" => "referral",
                                    "activity_created_by_id" => 5050960,
                                    "activity_created_by_first_name" => "First",
                                    "activity_created_by_last_name" => "Name",
                                    "activity_reason_id" => 20,
                                    "activity_reason_text" => "Class attendance positive",
                                    "activity_description" => "referral",
                                    "activity_referral_status" => "",
                                    "activity_log_id" => 6284900
                                ]
                            ]
                        ])
                ],
                // Email activity list
                [
                    4545845, 'email', false, 203, 454545, true, true, true, '',
                    [
                        'total_activities' => 7,
                        'total_notes' => 2,
                        'total_contacts' => 0,
                        'total_referrals' => 2,
                        'total_appointments' => 1,
                        'total_email' => 2
                    ],
                    [
                        [
                            "activity_id" => 282804,
                            "activity_type" => "email",
                            "activity_date" => new \DateTime("2018-10-10 12:00:00"),
                            "activity_created_by_id" => 5050960,
                            "activity_created_by_first_name" => "First",
                            "activity_created_by_last_name" => "Name",
                            "activity_reason_id" => 20,
                            "activity_reason_text" => "Class attendance positive",
                            "activity_description" => "email",
                            "activity_referral_status" => "",
                            "activity_log_id" => 6284900
                        ]
                    ],
                    $this->createStudentListHeaderResponseDto(4545845,
                        [
                            'total_activities' => 7,
                            'total_notes' => 2,
                            'total_contacts' => 0,
                            'total_referrals' => 2,
                            'total_appointments' => 1,
                            'total_email' => 2,
                            'interaction' => false,
                            'activities' => [
                                [
                                    "activity_id" => 282804,
                                    "activity_date" => new \DateTime("2018-10-10 12:00:00"),
                                    "activity_type" => "email",
                                    "activity_created_by_id" => 5050960,
                                    "activity_created_by_first_name" => "First",
                                    "activity_created_by_last_name" => "Name",
                                    "activity_reason_id" => 20,
                                    "activity_reason_text" => "Class attendance positive",
                                    "activity_description" => "email",
                                    "activity_referral_status" => "",
                                    "activity_log_id" => 6284900
                                ]
                            ]
                        ])
                ],
                // Contact activity list
                [
                    4545845, 'Contact', false, 203, 454545, true, true, true, '',
                    [
                        'total_activities' => 7,
                        'total_notes' => 2,
                        'total_contacts' => 0,
                        'total_referrals' => 2,
                        'total_appointments' => 1,
                        'total_email' => 2
                    ],
                    [
                        [
                            "activity_id" => 282804,
                            "activity_type" => "Contact",
                            "activity_date" => new \DateTime("2018-10-10 12:00:00"),
                            "activity_created_by_id" => 5050960,
                            "activity_created_by_first_name" => "First",
                            "activity_created_by_last_name" => "Name",
                            "activity_reason_id" => 20,
                            "activity_reason_text" => "Class attendance positive",
                            "activity_description" => "Contact",
                            "activity_referral_status" => "",
                            "activity_log_id" => 6284900,
                            'activity_contact_type_id' => 12,
                            'activity_contact_type_text' => "contacts"
                        ]
                    ],
                    $this->createStudentListHeaderResponseDto(4545845,
                        [
                            'total_activities' => 7,
                            'total_notes' => 2,
                            'total_contacts' => 0,
                            'total_referrals' => 2,
                            'total_appointments' => 1,
                            'total_email' => 2,
                            'interaction' => false,
                            'activities' => [
                                [
                                    "activity_id" => 282804,
                                    "activity_date" => new \DateTime("2018-10-10 12:00:00"),
                                    "activity_type" => "Contact",
                                    "activity_created_by_id" => 5050960,
                                    "activity_created_by_first_name" => "First",
                                    "activity_created_by_last_name" => "Name",
                                    "activity_reason_id" => 20,
                                    "activity_reason_text" => "Class attendance positive",
                                    "activity_description" => "Contact",
                                    "activity_referral_status" => "",
                                    "activity_log_id" => 6284900,
                                    'activity_contact_type_id' => 12,
                                    'activity_contact_type_text' => "contacts"
                                ]
                            ]
                        ])
                ],
                // Passing invalid student id should throw an exception
                ['invalid-id', 'all', true, 203, 454545, true, true, false, 'Not a valid Student Id.', NULL, NULL, []],
                // Passing empty student id should throw an exception.
                ['', 'all', true, 203, 454545, true, true, false, 'Not a valid Student Id.', NULL, NULL, []],
                // List of all activities.
                [
                    4545845, 'all', false, 203, 454545, true, true, true, '',
                    [
                        'total_activities' => 4,
                        'total_notes' => 1,
                        'total_contacts' => 0,
                        'total_referrals' => 1,
                        'total_appointments' => 1,
                        'total_email' => 1,
                        'interaction' => false
                    ],
                    NULL,
                    $this->createStudentListHeaderResponseDto(4545845,
                        [
                            'total_activities' => 4,
                            'total_notes' => 1,
                            'total_contacts' => 0,
                            'total_referrals' => 1,
                            'total_appointments' => 1,
                            'total_email' => 1,
                            'interaction' => false,
                            'activities' => [
                            ]
                        ])
                ]
            ]
        ]);
    }


    public function testGetStudentProfile()
    {
        $this->specify("Testing Student Details Response DTO", function ($studentId, $organizationId, $loggedUserId, $validCode, $loadingArray, $expectedResult, $expectedException = null, $riskArray = []) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));


            $loggedInPerson = new Person();
            $loggedInPerson->setId($loggedUserId);

            $mockDateTimeString = '2015-01-01 00:00:00';


            $mockOrgPersonStudentObject = $this->getMock('OrgPersonStudent', ['getIsActive', 'getPhotoUrl']);
            $mockOPSSLArray = [];


            $mockManagerService = $this->getMock('Manager', ['checkAccessToStudent']);
            $mockOrgPermissionService = $this->getMock('orgPermissionService', ['getRiskIndicatorForStudent']);
            $mockAcademicYearService = $this->getMock('academicYearService', ['getCurrentOrgAcademicYearId']);



            $mockContainer->method('get')
                ->willReturnMap([
                    [Manager::SERVICE_KEY, $mockManagerService],
                    [OrgPermissionsetService::SERVICE_KEY, $mockOrgPermissionService],
                    [AcademicYearService::SERVICE_KEY, $mockAcademicYearService]
                ]);

            $mockPersonRepository = $this->getMock('PersonRepository', ['find']);
            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', ['findOneBy']);
            $mockOrgPersonStudentYearRepository = $this->getMock('OrgPersonStudentYearRepository', ['findOneBy']);
            $mockOrgPersonStudentSurveyLinkRepository = $this->getMock('OrgPersonStudentSurveyLinkRepository', ['listSurveysForStudent']);
            $mockStudentDbViewLogRepository = $this->getMock('studentDbViewLogRepository', ['findOneBy', 'createStudentDbViewLog', 'flush']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [OrgPersonStudentRepository::REPOSITORY_KEY, $mockOrgPersonStudentRepository],
                    [OrgPersonStudentYearRepository::REPOSITORY_KEY, $mockOrgPersonStudentYearRepository],
                    [OrgPersonStudentSurveyLinkRepository::REPOSITORY_KEY, $mockOrgPersonStudentSurveyLinkRepository],
                    [StudentDbViewLogRepository::REPOSITORY_KEY, $mockStudentDbViewLogRepository]
                ]);

            switch ($validCode) {
                //Standard Cases with Varying Risk Indicator Permission
                case 1:
                    $mockOrgPermissionService->method('getRiskIndicatorForStudent')->willReturn($riskArray);
                    $mockManagerService->method('checkAccessToStudent')->willReturn(1);


                    $studentObject = new Person();
                    $studentObject->setExternalId($loadingArray['external_id']);
                    $studentObject->setFirstName($loadingArray['firstname']);
                    $studentObject->setLastName($loadingArray['lastname']);
                    $studentObject->setUsername($loadingArray['primary_email']);
                    $contactInfoObject = new ContactInfo();
                    $contactInfoObject->setHomePhone($loadingArray['phone_number']);
                    $contactInfoObject->setPrimaryMobile($loadingArray['mobile_number']);
                    $studentObject->addContact($contactInfoObject);
                    $riskLevel = new RiskLevels();
                    $riskLevel->setRiskText($loadingArray['risk_level']);
                    $studentObject->setRiskLevel($riskLevel);
                    $intentToLeave = new IntentToLeave();
                    $intentToLeave->setText($loadingArray['intent_to_leave']);
                    $studentObject->setIntentToLeave($intentToLeave);
                    $studentObject->setRiskUpdateDate($loadingArray['risk_update_date']);
                    $studentObject->setAuthUsername($loadingArray['auth_username']);

                    $orgPersonStudentYear = new OrgPersonStudentYear();
                    $orgPersonStudentYear->setIsActive($loadingArray['student_status']);

                    $mockOrgPersonStudentYearRepository->method('findOneBy')->willReturn($orgPersonStudentYear);
                    $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($mockOrgPersonStudentObject);
                    $mockOrgPersonStudentObject->method('getPhotoUrl')->willReturn($loadingArray['photo_url']);
                    $mockPersonRepository->method('find')->willReturn($studentObject);

                    $mockOrgPersonStudentSurveyLinkRepository->method('listSurveysForStudent')->willReturn($mockOPSSLArray);
                    break;
                //Invalid Student
                case 2:
                    $invalidStudentException = new SynapseValidationException($expectedException);
                    $mockPersonRepository->method('find')->willThrowException($invalidStudentException);
                    break;
                //No Student Access
                case 3:
                    $mockManagerService->method('checkAccessToStudent')->willReturn(0);
                    break;
                //Person is not student
                case 4:
                    $mockOrgPermissionService->method('getRiskIndicatorForStudent')->willReturn($riskArray);
                    $mockManagerService->method('checkAccessToStudent')->willReturn(1);


                    $studentObject = new Person();
                    $studentObject->setExternalId($loadingArray['external_id']);
                    $studentObject->setFirstName($loadingArray['firstname']);
                    $studentObject->setLastName($loadingArray['lastname']);
                    $studentObject->setUsername($loadingArray['primary_email']);
                    $contactInfoObject = new ContactInfo();
                    $contactInfoObject->setHomePhone($loadingArray['phone_number']);
                    $contactInfoObject->setPrimaryMobile($loadingArray['mobile_number']);
                    $studentObject->addContact($contactInfoObject);
                    $riskLevel = new RiskLevels();
                    $riskLevel->setRiskText($loadingArray['risk_level']);
                    $studentObject->setRiskLevel($riskLevel);
                    $intentToLeave = new IntentToLeave();
                    $intentToLeave->setText($loadingArray['intent_to_leave']);
                    $studentObject->setIntentToLeave($intentToLeave);
                    $studentObject->setRiskUpdateDate($loadingArray['risk_update_date']);
                    $studentObject->setAuthUsername($loadingArray['auth_username']);

                    $orgPersonStudentYear = new OrgPersonStudentYear();
                    $orgPersonStudentYear->setIsActive($loadingArray['student_status']);

                    $mockOrgPersonStudentYearRepository->method('findOneBy')->willReturn($orgPersonStudentYear);
                    $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($mockOrgPersonStudentObject);
                    $mockOrgPersonStudentObject->method('getPhotoUrl')->willReturn($loadingArray['photo_url']);
                    $mockPersonRepository->method('find')->willReturn($studentObject);
                    $mockOrgPersonStudentRepository->method('findOneBy')->willThrowException(new SynapseValidationException($expectedException));
                    break;

            }


            $mockStudent = $this->getMock('Person', [
                'getOrganization',
                'getExternalId',
                'getContacts',
                'getFirstname',
                'getLastname',
                'getAuthUsername',
                'getUsername',
                'getRiskLevel',
                'getRiskUpdateDate',
                'getIntentToLeave',
                ]);
            $mockStudent->method('getId')->willReturn($studentId);

            $studentService = new StudentService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $results = $studentService->getStudentProfile($organizationId, $studentId, $loggedInPerson, $loggedUserId, $mockDateTimeString);
                $this->assertEquals($expectedResult, $results);
            } catch (\Exception $exception) {
                $this->assertEquals($expectedException, $exception->getMessage());
            }


        }, [
            'examples' =>
                [
                    //Student doesn't exist
                    [
                        1,
                        1,
                        2,
                        2,
                        [],
                        [],
                        null,
                        'The student ID is not valid'
                    ],
                    //Access Denied
                    [
                        1,
                        1,
                        2,
                        3,
                        [],
                        null,
                        'You do not have permission to view this student'

                    ],
                    //Person is not a student
                    [
                        1,
                        1,
                        2,
                        4,
                        [
                            'external_id' => 1,
                            'firstname' => 'bob',
                            'lastname' => 'bob',
                            'primary_email' => 'bob@bob',
                            'phone_number' => '1234',
                            'mobile_number' => '1235',
                            'risk_level' => 'green',
                            'intent_to_leave' => 'green',
                            'risk_update_date' => new \DateTime('2015-01-01 00:00:00'),
                            'last_viewed' => new \DateTime('2015-01-01 00:00:00'),
                            'student_status' => true,
                            'photo_url' => 'bobby',
                            'auth_username' => 'whataboutbob'
                        ],
                        null,
                        'Person is not a Student'
                    ],

                    //Normal Case - All Permissions
                    [
                        1,
                        1,
                        2,
                        1,
                        [
                            'external_id' => 1,
                            'firstname' => 'bob',
                            'lastname' => 'bob',
                            'primary_email' => 'bob@bob',
                            'phone_number' => '1234',
                            'mobile_number' => '1235',
                            'risk_level' => 'green',
                            'intent_to_leave' => 'green',
                            'risk_update_date' => new \DateTime('2015-01-01 00:00:00'),
                            'last_viewed' => new \DateTime('2015-01-01 00:00:00'),
                            'student_status' => true,
                            'photo_url' => 'bobby',
                            'auth_username' => 'whataboutbob'
                        ],
                        $this->createStudentDetailsResponseDto([
                            'external_id' => 1,
                            'firstname' => 'bob',
                            'lastname' => 'bob',
                            'primary_email' => 'bob@bob',
                            'phone_number' => '1234',
                            'mobile_number' => '1235',
                            'risk_level' => 'green',
                            'intent_to_leave' => 'green',
                            'risk_update_date' => new \DateTime('2015-01-01 00:00:00'),
                            'last_viewed' => new \DateTime('2015-01-01 00:00:00'),
                            'student_status' => true,
                            'photo_url' => 'bobby',
                            'auth_username' => 'whataboutbob'
                        ]),
                        null,
                        [
                            'risk_indicator' => 1,
                            'intent_to_leave' => 1
                        ],
                    ],
                    // Normal - No Risk Permission
                    [
                        1,
                        1,
                        2,
                        1,
                        [
                            'external_id' => 1,
                            'firstname' => 'bob',
                            'lastname' => 'bob',
                            'primary_email' => 'bob@bob',
                            'phone_number' => '1234',
                            'mobile_number' => '1235',
                            'risk_level' => 'green',
                            'intent_to_leave' => 'green',
                            'risk_update_date' => new \DateTime('2015-01-01 00:00:00'),
                            'last_viewed' => new \DateTime('2015-01-01 00:00:00'),
                            'student_status' => true,
                            'photo_url' => 'bobby',
                            'auth_username' => 'whataboutbob'
                        ],
                        $this->createStudentDetailsResponseDto([
                            'external_id' => 1,
                            'firstname' => 'bob',
                            'lastname' => 'bob',
                            'primary_email' => 'bob@bob',
                            'phone_number' => '1234',
                            'mobile_number' => '1235',
                            'risk_level' => null,
                            'intent_to_leave' => 'green',
                            'risk_update_date' => null,
                            'last_viewed' => new \DateTime('2015-01-01 00:00:00'),
                            'student_status' => true,
                            'photo_url' => 'bobby',
                            'auth_username' => 'whataboutbob'
                        ]),
                        null,
                        [
                            'risk_indicator' => 0,
                            'intent_to_leave' => 1
                        ],
                    ],
                    // Normal - No Intent Permission
                    [
                        1,
                        1,
                        2,
                        1,
                        [
                            'external_id' => 1,
                            'firstname' => 'bob',
                            'lastname' => 'bob',
                            'primary_email' => 'bob@bob',
                            'phone_number' => '1234',
                            'mobile_number' => '1235',
                            'risk_level' => 'green',
                            'intent_to_leave' => 'green',
                            'risk_update_date' => new \DateTime('2015-01-01 00:00:00'),
                            'last_viewed' => new \DateTime('2015-01-01 00:00:00'),
                            'student_status' => true,
                            'photo_url' => 'bobby',
                            'auth_username' => 'whataboutbob'
                        ],
                        $this->createStudentDetailsResponseDto([
                            'external_id' => 1,
                            'firstname' => 'bob',
                            'lastname' => 'bob',
                            'primary_email' => 'bob@bob',
                            'phone_number' => '1234',
                            'mobile_number' => '1235',
                            'risk_level' => 'green',
                            'intent_to_leave' => null,
                            'risk_update_date' => new \DateTime('2015-01-01 00:00:00'),
                            'last_viewed' => new \DateTime('2015-01-01 00:00:00'),
                            'student_status' => true,
                            'photo_url' => 'bobby',
                            'auth_username' => 'whataboutbob'
                        ]),
                        null,
                        [
                            'risk_indicator' => 1,
                            'intent_to_leave' => 0
                        ],
                    ],
                    // NO Permission for Either Risk or Intent
                    [
                        1,
                        1,
                        2,
                        1,
                        [
                            'external_id' => 1,
                            'firstname' => 'bob',
                            'lastname' => 'bob',
                            'primary_email' => 'bob@bob',
                            'phone_number' => '1234',
                            'mobile_number' => '1235',
                            'risk_level' => 'green',
                            'intent_to_leave' => 'green',
                            'risk_update_date' => new \DateTime('2015-01-01 00:00:00'),
                            'last_viewed' => new \DateTime('2015-01-01 00:00:00'),
                            'student_status' => true,
                            'photo_url' => 'bobby',
                            'auth_username' => 'whataboutbob'
                        ],
                        $this->createStudentDetailsResponseDto([
                            'external_id' => 1,
                            'firstname' => 'bob',
                            'lastname' => 'bob',
                            'primary_email' => 'bob@bob',
                            'phone_number' => '1234',
                            'mobile_number' => '1235',
                            'risk_level' => null,
                            'intent_to_leave' => null,
                            'risk_update_date' => null,
                            'last_viewed' => new \DateTime('2015-01-01 00:00:00'),
                            'student_status' => true,
                            'photo_url' => 'bobby',
                            'auth_username' => 'whataboutbob'
                        ]),
                        null,
                        [
                            'risk_indicator' => 0,
                            'intent_to_leave' => 0
                        ],
                    ],
                ]
            ]
        );
    }

    /**
     * Create StudentListHeaderResponseDto
     *
     * @param int $personId
     * @param array $activityList
     * @return StudentListHeaderResponseDto
     */
    private function createStudentListHeaderResponseDto($personId, $activityList)
    {
        $studentListDto = new StudentListHeaderResponseDto();
        $studentListDto->setPersonId($personId);
        $studentListDto->setTotalActivities($activityList['total_activities']);
        $studentListDto->setTotalNotes($activityList['total_notes']);
        $studentListDto->setTotalContacts($activityList['total_contacts']);
        $studentListDto->setTotalReferrals($activityList['total_referrals']);
        $studentListDto->setTotalAppointments($activityList['total_appointments']);
        $studentListDto->setTotalEmail($activityList['total_email']);
        $studentListDto->setShowInteractionContactType($activityList['interaction']);
        $activities = $activityList['activities'];
        $studentActivity = [];
        foreach ($activities as $activity) {
            $studentResponseDto = new StudentListArrayResponseDto();
            $studentResponseDto->setActivityId($activity['activity_id']);
            $studentResponseDto->setActivityLogId($activity['activity_log_id']);
            $activityDate = $activity['activity_date']->format(SynapseConstant::DATE_FORMAT_WITH_TIMEZONE);
            $studentResponseDto->setActivityDate($activityDate);
            $studentResponseDto->setActivityType($activity['activity_type']);
            $studentResponseDto->setActivityCreatedById($activity['activity_created_by_id']);
            $studentResponseDto->setActivityCreatedByFirstName($activity['activity_created_by_first_name']);
            $studentResponseDto->setActivityCreatedByLastName($activity['activity_created_by_last_name']);
            $studentResponseDto->setActivityReasonId($activity['activity_reason_id']);
            $studentResponseDto->setActivityReasonText($activity['activity_reason_text']);
            $studentResponseDto->setActivityDescription($activity['activity_description']);
            $studentResponseDto->setActivityReferralStatus($activity['activity_referral_status']);
            if (isset($activity['activity_contact_type_id'])) {
                $studentResponseDto->setActivityContactTypeId($activity['activity_contact_type_id']);
                $studentResponseDto->setActivityContactTypeText($activity['activity_contact_type_text']);
            }
            $studentActivity[] = $studentResponseDto;
        }
        $studentListDto->setActivities($studentActivity);
        return $studentListDto;
    }

    /**
     * Create StudentOpenAppResponseDto
     *
     * @param array $openAppointments
     * @return StudentOpenAppResponseDto
     */
    private function createStudentOpenAppointmentDto($openAppointments)
    {
        $studentOpenAppointmentDto = new StudentOpenAppResponseDto();
        $studentOpenAppointmentDto->setPersonStudentId($openAppointments['person_id_student']);
        $studentOpenAppointmentDto->setPersonStaffId($openAppointments['person_id_faculty']);
        $studentOpenAppointmentDto->setTotalAppointments($openAppointments['total_appointments']);
        $studentOpenAppointmentDto->setTotalMissedAppointments($openAppointments['missed_appointments']);
        $studentOpenAppointmentDto->setTotalAppointmentsByMe($openAppointments['appointments_by_me']);
        $studentOpenAppointmentDto->setTotalSameDayAppointmentsByMe($openAppointments['today_appointments']);
        $appointmentList = $openAppointments['appointments'];
        foreach ($appointmentList as $appointment) {
            $currentDate = new \DateTime('now');
            $appointmentStartDate = $appointment['startDate'];
            if (($appointmentStartDate >= $currentDate)) {
                $appointmentListDto = new AppointmentListArrayResponseDto();
                $appointmentListDto->setAppointmentId($appointment['id']);
                $appointmentListDto->setStartDate($appointment['startDate']);
                $appointmentListDto->setEndDate($appointment['endDate']);
                $appointments[] = $appointmentListDto;
            }
        }
        $studentOpenAppointmentDto->setAppointments($appointments);
        return $studentOpenAppointmentDto;
    }

    private function createPersonObject($personId, $personFirstName, $personLastName)
    {

        $personEntity = new Person();
        $personEntity->setId($personId);
        $personEntity->setFirstname($personFirstName);
        $personEntity->setLastname($personLastName);

        return $personEntity;

    }

    private function createStudentDetailsResponseDto($loadingArray, $assessmentBanner = [])
    {
        $studentDetailsResponseDto = new StudentDetailsResponseDto();
        $studentDetailsResponseDto->setId(1);
        $studentDetailsResponseDto->setStudentExternalId($loadingArray['external_id']);
        $studentDetailsResponseDto->setStudentFirstName($loadingArray['firstname']);
        $studentDetailsResponseDto->setStudentLastName($loadingArray['lastname']);
        $studentDetailsResponseDto->setPrimaryEmail($loadingArray['primary_email']);
        $studentDetailsResponseDto->setPhoneNumber($loadingArray['phone_number']);
        $studentDetailsResponseDto->setMobileNumber($loadingArray['mobile_number']);
        $studentDetailsResponseDto->setStudentRiskStatus($loadingArray['risk_level']);
        $studentDetailsResponseDto->setStudentIntentToLeave($loadingArray['intent_to_leave']);
        $studentDetailsResponseDto->setRiskUpdatedDate($loadingArray['risk_update_date']);
        $studentDetailsResponseDto->setLastViewedDate($loadingArray['last_viewed']);
        $studentDetailsResponseDto->setStudentStatus($loadingArray['student_status']);
        $studentDetailsResponseDto->setPhotoUrl($loadingArray['photo_url']);
        $studentDetailsResponseDto->setAssessmentBanner($assessmentBanner);
        $studentDetailsResponseDto->setAuthUsername($loadingArray['auth_username']);

        return $studentDetailsResponseDto;

    }

}