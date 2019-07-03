<?php
namespace Synapse\CoreBundle\Service\Impl;

use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgGroup;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\OrganizationDTO;
use Synapse\RestBundle\Entity\OrgStmtUpdateDto;
use Synapse\StaticListBundle\Repository\OrgStaticListRepository;

class OrganizationServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    public function testCreateOrganization()
    {
        $this->specify("Test create organization", function ($organizationData, $isValidationError, $expectedResult) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));

            $mockLoggerHelperService = $this->getMock('LoggerHelperService', ['getLog']);
            $mockLanguageMasterService = $this->getMock('LanguageMasterService', ['getLanguageById']);
            $mockValidatorService = $this->getMock('validator', ['validate']);
            $mockGroupService = $this->getMock('group_service', ['addSystemGroup']);
            if ($isValidationError) {
                $errors = $this->arrayOfErrorObjects(['subdomain_duplicate_error' => 'Validation errors found']);
                $mockValidatorService->method('validate')->willReturn($errors);
            }
            $mockResque = $this->getMock('resque', ['enqueue']);
            $mockContainer->method('get')
                ->willReturnMap([
                    [LoggerHelperService::SERVICE_KEY, $mockLoggerHelperService],
                    [LanguageMasterService::SERVICE_KEY, $mockLanguageMasterService],
                    [SynapseConstant::VALIDATOR, $mockValidatorService],
                    [SynapseConstant::RESQUE_CLASS_KEY, $mockResque],
                    [GroupService::SERVICE_KEY, $mockGroupService]
                ]);
            $mockGroupService->method('addSystemGroup')->willReturn(new OrgGroup());

            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['createOrganization', 'flush']);
            $mockOrganizationLangRepository = $this->getMock('OrganizationLangRepository', ['createOrganizationLang']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                    [OrganizationlangRepository::REPOSITORY_KEY, $mockOrganizationLangRepository]
                ]);
            $organizationDto = $this->createOrganizationDTO($organizationData);
            try {
                $organizationService = new OrganizationService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $organization = $organizationService->createOrganization($organizationDto);
                $this->assertEquals($organization, $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }

        }, [
            'examples' => [
                // Special characters in name should throw the exception
                [
                    [
                        'id' => '',
                        'name' => 'campus//',
                        'nick_name' => 'campus',
                        'subdomain' => 'campus.com',
                        'timezone' => '',
                        'campus_id' => '',
                        'status' => 'Active',
                        'is_ldap_saml_enabled' => 1,

                    ],
                    true,
                    'Validation errors found'
                ],
                // Create organization with valid data.
                [
                    [
                        'id' => '',
                        'name' => 'campus',
                        'nick_name' => 'campus',
                        'subdomain' => 'campus.com',
                        'timezone' => '',
                        'campus_id' => '',
                        'status' => 'Active',
                        'is_ldap_saml_enabled' => 1,

                    ],
                    false,
                    [
                        'id' => '',
                        'name' => 'campus',
                        'nick_name' => 'campus',
                        'subdomain' => 'campus.com',
                        'timezone' => '',
                        'campus_id' => '',
                        'status' => 'Active',
                        'is_ldap_saml_enabled ' => 1,
                    ],
                ],
                // Special character in sub domain should throw an exception.
                [
                    [
                        'id' => '',
                        'name' => 'campus',
                        'nick_name' => 'campus',
                        'subdomain' => 'campus.   com',
                        'timezone' => '',
                        'campus_id' => '',
                        'status' => 'Active',
                        'is_ldap_saml_enabled' => 1,

                    ],
                    true,
                    'Validation errors found'
                ],
                // Invalid nick name should throw an exception
                [
                    [
                        'id' => '',
                        'name' => 'campus',
                        'nick_name' => 'campus &',
                        'subdomain' => 'campus.com',
                        'timezone' => '',
                        'campus_id' => '',
                        'status' => 'Active',
                        'is_ldap_saml_enabled' => 1,

                    ],
                    true,
                    'Validation errors found'
                ],
            ]
        ]);
    }


    public function testGetOrganizationDetails()
    {
        $this->specify("Test to get the organization details", function ($organizationId, $isOrganizationAvailable, $expectedResults) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));

            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['find']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                ]);
            if ($isOrganizationAvailable) {
                $mockOrganizationRepository->method('find')->willReturn($expectedResults);
            }
            try {
                $organizationService = new OrganizationService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $organization = $organizationService->getOrganizationDetails($organizationId);
                $this->assertEquals($organization, $expectedResults);
            } catch (\Exception $e) {
                $this->assertEquals($expectedResults, $e->getMessage());
            }

        }, [
            'examples' => [
                // Get organization details for 12
                [
                    12,
                    true,
                    $this->createOrganization(
                        [
                            'campus_id' => 'CMP123',
                            'status' => 'Active',
                            'inactivity_timeout' => 30,
                            'timezone' => 'Eastern',
                        ]
                    )
                ],
                // passing empty organization id should throw an exception.
                [
                    '',
                    false,
                    'Organization Not Found.'
                ],
                // passing in valid organization id should throw an exception.
                [
                    'invalid-id',
                    false,
                    'Organization Not Found.'
                ],
                // get organization details for 229
                [
                    229,
                    true,
                    $this->createOrganization(
                        [
                            'campus_id' => 'CMP229',
                            'status' => 'Active',
                            'inactivity_timeout' => 60,
                            'timezone' => 'Eastern',
                        ]
                    )
                ],
            ]
        ]);
    }

    public function testDeleteOrganization()
    {
        $this->specify("Test to delete organization", function ($organizationId, $isOrganizationAvailable, $coordinatorList, $expectedResults) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));

            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['remove', 'flush', 'find']);
            $mockOrganizationLangRepository = $this->getMock('OrganizationLangRepository', ['findBy']);
            $mockOrganizationRoleRepository = $this->getMock('OrganizationRoleRepository', ['findBy', 'remove']);
            $mockOrganizationPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', ['findBy']);
            $mockPersonRepository = $this->getMock('PersonRepository', ['find']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                    [OrganizationlangRepository::REPOSITORY_KEY, $mockOrganizationLangRepository],
                    [OrganizationRoleRepository::REPOSITORY_KEY, $mockOrganizationRoleRepository],
                    [OrgPersonFacultyRepository::REPOSITORY_KEY, $mockOrganizationPersonFacultyRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository]
                ]);
            if ($isOrganizationAvailable) {
                $mockOrganization = $this->getMock('Organization', ['getId']);
                $mockOrganizationRepository->method('find')->willReturn($mockOrganization);
            }
            $mockOrganizationRoleRepository->method('findBy')->willReturn($coordinatorList);

            try {
                $organizationService = new OrganizationService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $organization = $organizationService->deleteOrganization($organizationId);
                $this->assertEquals($organization, $expectedResults);
            } catch (\Exception $e) {
                $this->assertEquals($expectedResults, $e->getMessage());
            }

        }, [
            'examples' => [
                // passing empty organization id should throw an exception.
                [
                    '',
                    false,
                    [],
                    'Organization Not Found.'
                ],
                // delete organization 142
                [
                    142,
                    true,
                    [],
                    142
                ],

                // coordinators associated with organization should throw an exception.
                [
                    126,
                    true,
                    [
                        298978,
                        234324,
                        458723,
                    ],
                    'Coordinator is associated with the campus. This item cannot be removed.'
                ],
                // delete invalid organization id should throw an exception.
                [
                    'invalid-id',
                    false,
                    [],
                    'Organization Not Found.'
                ],
                // delete organization 234
                [
                    234,
                    true,
                    [],
                    234
                ],
            ]
        ]);
    }

    public function testUpdateCustomConfidentStatement()
    {
        $this->specify("Test to update custom confident statement.", function ($isOrganizationAvailable, $organizationData, $expectedResult, $expectedErrorMessage) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));

            $mockLoggerHelperService = $this->getMock('LoggerHelperService', ['getLog']);
            $mockOrganizationService = $this->getMock('OrganizationService', ['getCustomConfidStmt']);
            $mockContainer->method('get')
                ->willReturnMap([
                    [LoggerHelperService::SERVICE_KEY, $mockLoggerHelperService]
                ]);

            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['find', 'flush']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                ]);
            if ($isOrganizationAvailable) {
                $mockOrganization = $this->getMock('Organization', ['getCustomConfidentialityStatement', 'getId', 'setCustomConfidentialityStatement']);
                $mockOrganizationRepository->method('find')->willReturn($mockOrganization);
                $mockOrganization->method('getId')->willReturn($organizationData['organization_id']);
                $mockOrganization->method('getCustomConfidentialityStatement')->willReturn($organizationData['confidentiality_statement']);
            }
            $organizationDto = $this->createOrganizationStatementDto($organizationData);
            $mockOrganizationService->method('updateCustomConfidStmt')->willReturn($organizationDto);
            try {
                $organizationService = new OrganizationService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $organizationService->updateCustomConfidStmt($organizationDto);
                $this->assertEquals($results, $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($expectedErrorMessage, $e->getMessage());
            }

        }, [
            'examples' => [
                // Update custom Confidentiality Statement for 12.
                [
                    true,
                    [
                        'campus_id' => 'CMP12',
                        'status' => 'Active',
                        'inactivity_timeout' => 60,
                        'timezone' => 'Eastern',
                        'organization_id' => 12,
                        'confidentiality_statement' => true,
                    ],
                    [
                        'organization_id' => 12,
                        'custom_confidentiality_statement' => 1
                    ],
                    NULL
                ],
                // Update custom Confidentiality Statement where the organization id is not found should throw an exception.
                [
                    false,
                    [
                        'campus_id' => 'CMP229',
                        'status' => 'Active',
                        'inactivity_timeout' => 60,
                        'timezone' => 'Eastern',
                        'organization_id' => '',
                        'confidentiality_statement' => true,
                    ], NULL,
                    'Organization Not Found.'
                ],
                // Update custom Confidentiality Statement for 125.
                [
                    true,
                    [
                        'campus_id' => 'CMP125',
                        'status' => 'Active',
                        'inactivity_timeout' => 30,
                        'timezone' => 'Eastern',
                        'organization_id' => 125,
                        'confidentiality_statement' => true,
                    ],
                    [
                        'organization_id' => 125,
                        'custom_confidentiality_statement' => 1
                    ], NULL
                ],
            ]
        ]);
    }

    public function testGetOverview()
    {
        $this->specify("Test to get organization overview.", function ($organizationId, $personId, $isOrganizationAvailable, $isPersonAvailable, $isOrganizationPersonRole, $isOrganizationRole, $organizationData, $expectedResults, $expectedErrorMessage) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));

            $mockManagerService = $this->getMock('Manager', ['checkAccessToOrganization']);
            $mockDateUtilityService = $this->getMock('DateUtilityService', ['getOrganizationISOTimeZone', 'adjustDateTimeToOrganizationTimezone']);
            $mockAcademicYearService = $this->getMock('AcademicYearService', ['getCurrentOrgAcademicYearId']);

            $mockManagerService->method('checkAccessToOrganization')->willReturn(false);

            $mockContainer->method('get')
                ->willReturnMap([
                    [AcademicYearService::SERVICE_KEY, $mockAcademicYearService],
                    [Manager::SERVICE_KEY, $mockManagerService],
                    [DateUtilityService::SERVICE_KEY, $mockDateUtilityService]
                ]);

            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['find', 'getCount', 'getCountAndLastUpdateOfOrgPersonFaculty', 'getCountAndLastUpdateOfOrgPersonStudent']);
            $mockOrganizationCourseRepository = $this->getMock('OrgCoursesRepository', ['getCount']);
            $mockPersonRepository = $this->getMock('PersonRepository', ['find']);
            $mockOrganizationRoleRepository = $this->getMock('OrganizationRoleRepository', ['findOneBy']);
            $mockOrganizationLangRepository = $this->getMock('OrganizationLangRepository', ['findOneBy']);
            $mockOrganizationStaticListRepository = $this->getMock('OrgStaticListRepository', ['getCount']);
            $mockOrgPersonStudentYearRepository = $this->getMock('OrgPersonStudentYearRepository', ['getParticipantAndActiveStudents']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrgCoursesRepository::REPOSITORY_KEY, $mockOrganizationCourseRepository],
                    [OrganizationlangRepository::REPOSITORY_KEY, $mockOrganizationLangRepository],
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                    [OrganizationRoleRepository::REPOSITORY_KEY, $mockOrganizationRoleRepository],
                    [OrgStaticListRepository::REPOSITORY_KEY, $mockOrganizationStaticListRepository],
                    [OrgPersonStudentYearRepository::REPOSITORY_KEY, $mockOrgPersonStudentYearRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository]
                ]);

            if ($isOrganizationAvailable) {
                $mockOrganization = $this->getMock('Organization', ['getId', 'getLang']);
                $mockOrganizationRepository->method('find')->willReturn($mockOrganization);
                if ($isPersonAvailable) {
                    $mockPerson = $this->getMock('Organization', ['getId']);
                    $mockPersonRepository->method('find')->willReturn($mockPerson);

                    if ($isOrganizationPersonRole) {
                        $mockOrganizationPersonRole = $this->getMock('organizationRole', ['getOrganization', 'getRole', 'getPerson']);
                        $mockOrganizationRoleRepository->method('findOneBy')->willReturn($mockOrganizationPersonRole);
                        $mockRole = $this->getMock('Role', ['getStatus', 'getId']);
                        $mockOrganizationPersonRole->method('getRole')->willReturn($mockRole);
                        $mockRole->method('getId')->willReturn($isOrganizationRole);

                        $mockOrganizationLang = $this->getMock('OrganizationLang', ['getOrganizationName', 'getId', 'getOrganization', 'getLang']);
                        $mockOrganizationLangRepository->method('findOneBy')->willReturn($mockOrganizationLang);
                        $mockLanguage = $this->getMock('LanguageMaster', ['getId', 'getLangcode']);
                        $mockOrganizationLang->method('getLang')->willReturn($mockLanguage);

                        if ($isOrganizationRole) {
                            $mockLanguage->method('getId')->willReturn($organizationData['lang_id']);

                            $mockOrganizationRepository->expects($this->at(1))->method('getCount')->with('OrgGroup')->willReturn($organizationData['groups_count']);
                            $mockOrganizationRepository->expects($this->at(2))->method('getCount')->with('OrgPermissionset')->willReturn($organizationData['permissions_count']);
                            $mockOrganizationRepository->expects($this->at(3))->method('getCount')->with('Teams')->willReturn($organizationData['teams_count']);

                            $mockOrganizationCourseRepository->expects($this->at(0))->method('getCount')->with('OrgCourses')->willReturn($organizationData['course_count']);
                            $mockOrganizationCourseRepository->expects($this->at(1))->method('getCount')->with('OrgAcademicYear')->willReturn($organizationData['academicyear_count']);
                            $mockOrganizationCourseRepository->expects($this->at(2))->method('getCount')->with('OrgAcademicTerms')->willReturn($organizationData['academicterm_count']);

                            $orgPersonFaculty['faculty_count'] = $organizationData['staff_count'];
                            $orgPersonFaculty['modifiedAt'] = $organizationData['staff_updated_date'];
                            $mockOrganizationRepository->method('getCountAndLastUpdateOfOrgPersonFaculty')->willReturn($orgPersonFaculty);
                            $mockDateUtilityService->method('getOrganizationISOTimeZone')->willReturn('UTC');

                            $orgPersonStudent['student_count'] = $organizationData['staff_count'];
                            $orgPersonStudent['modifiedAt'] = $organizationData['students_updated_date'];
                            $organizationTime = new \DateTime($orgPersonStudent['modifiedAt']);

                            $mockOrganizationRepository->method('getCountAndLastUpdateOfOrgPersonStudent')->willReturn($orgPersonStudent);
                            $mockDateUtilityService->method('adjustDateTimeToOrganizationTimezone')->willReturn($organizationTime);
                            $mockOrganizationStaticListRepository->method('getCount')->willReturn($organizationData['staticlist_count']);
                            $mockAcademicYearService->method('getCurrentOrgAcademicYearId')->willReturn(18);
                            $mockOrgPersonStudentYearRepository->expects($this->at(0))->method('getParticipantAndActiveStudents')->willReturn($organizationData['students_participants_count']);
                            $mockOrgPersonStudentYearRepository->expects($this->at(1))->method('getParticipantAndActiveStudents')->willReturn($organizationData['students_active_participants_count']);
                        }
                    }
                }
            }
            try {
                $organizationService = new OrganizationService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $organizationService->getOverview($organizationId, $personId);
                $this->assertEquals($results, $expectedResults);
            } catch (\Exception $e) {
                $this->assertEquals($expectedErrorMessage, $e->getMessage());
            }

        }, [
            'examples' => [
                // Passing empty organization id
                [
                    '',
                    48756,
                    false,
                    true,
                    true,
                    true,
                    [],
                    [],
                    'Organization Not Found.'
                ],
                // Passing invalid person id.
                [
                    203,
                    'invalid-person',
                    true,
                    false,
                    true,
                    true,
                    [], [],
                    'Person does not exist.'
                ],
                [
                    233,
                    458796,
                    true,
                    true,
                    true,
                    true,
                    [
                        'lang_id' => 1,
                        'groups_count' => 5,
                        'permissions_count' => 12,
                        'teams_count' => 10,
                        'staticlist_count' => 19,
                        'course_count' => 15,
                        'academicyear_count' => 16,
                        'academicterm_count' => 20,
                        'staff_count' => 3,
                        'staff_updated_date' => '2017-10-10 00:00:00',
                        'students_count' => 20,
                        'students_updated_date' => '2017-10-10 00:00:00',
                        'students_participants_count' => 24,
                        'students_active_participants_count' => 21
                    ],
                    [
                        'organization_id' => 233,
                        'lang_id' => 1,
                        'students_count' => 3,
                        'students_participants_count' => 24,
                        'students_active_participants_count' => 21,
                        'students_updated_date' => new \DateTime('2017-10-10 00:00:00'),
                        'staff_count' => 3,
                        'staff_updated_date' => new \DateTime('2017-10-10 00:00:00'),
                        'permissions_count' => 12,
                        'groups_count' => 5,
                        'teams_count' => 10,
                        'academicyear_count' => 16,
                        'academicterm_count' => 20,
                        'course_count' => 15,
                        'staticlist_count' => 19,
                        'current_academic_year' => 1
                    ],
                    NULL
                ],
                // Person is not mapped with coordinator role.
                [
                    203,
                    78456,
                    true,
                    true,
                    false,
                    true,
                    [], [],
                    'Person not mapped with role.'
                ],
                // Passing empty person id.
                [
                    203,
                    '',
                    true,
                    false,
                    true,
                    true,
                    [], [],
                    'Person does not exist.'
                ],
                // Passing invalid organization id
                [
                    'invalid-id',
                    48756,
                    false,
                    true,
                    true,
                    true,
                    [],
                    [],
                    'Organization Not Found.'
                ],
            ]
        ]);
    }

    public function testGenerateAuthKeysForAllUsersInOrganization()
    {
        $this->specify("Test to generate auth keys for all users in organization.", function ($data) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));

            $personService = $this->getMock('PersonService', ['generateAuthKey']);
            $usersService = $this->getMock('UsersService', ['getUsers']);
            $mockContainer->method('get')
                ->willReturnMap([
                    [PersonService::SERVICE_KEY, $personService],
                    [UsersService::SERVICE_KEY, $usersService]
                ]);

            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['findAll']);
            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', ['findOneBy', 'persist', 'flush']);
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', ['findOneBy', 'persist', 'flush']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                    [OrgPersonStudentRepository::REPOSITORY_KEY, $mockOrgPersonStudentRepository],
                    [OrgPersonFacultyRepository::REPOSITORY_KEY, $mockOrgPersonFacultyRepository]
                ]);
            $mockOrganization[] = $this->getMock('Organization', ['getId']);
            $mockOrganizationRepository->method('findAll')->willReturn($mockOrganization);

            $mockOrgPersonStudent = $this->getMock('OrgPersonStudent', ['setAuthKey']);
            $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($mockOrgPersonStudent);

            $mockOrgPersonFaculty = $this->getMock('OrgPersonFaculty', ['setAuthKey']);
            $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonFaculty);

            $usersService->method('getUsers')->willReturn($data);

            $organizationService = new OrganizationService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $organizationService->generateAuthKeysForAllUsersInOrganization();
            $this->assertEquals($results, true);
        }, [
            'examples' => [
                [
                    'data' => [
                        'student' =>
                            [
                                [
                                    'id' => 26876,
                                    'externalid' => 'AB9888'
                                ]
                            ],
                        'faculty' =>
                            [
                                [
                                    'id' => 58462,
                                    'externalid' => 'FAC8456'
                                ]
                            ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * Create error objects.
     *
     * @param array $errorArray
     * @return array
     */
    private function arrayOfErrorObjects($errorArray)
    {

        $returnArray = [];
        foreach ($errorArray as $errorKey => $error) {
            $mockErrorObject = $this->getMock('ErrorObject', ['getMessage']);
            $mockErrorObject->method('getMessage')->willReturn($error);
            $returnArray[] = $mockErrorObject;
        }
        return $returnArray;
    }

    /**
     * Creates organization DTO.
     *
     * @param array $organization
     * @return OrganizationDTO
     */
    private function createOrganizationDTO($organization)
    {
        $subDomain = $organization['subdomain'];
        $campusId = $organization['campus_id'];
        $status = $organization['status'];
        $ldapSamlEnabled = $organization['is_ldap_saml_enabled'];
        $nickName = $organization['nick_name'];
        $name = $organization['name'];
        $timeZone = $organization['timezone'];
        $organizationDto = new OrganizationDTO();
        $organizationDto->setSubdomain($subDomain);
        $organizationDto->setCampusId($campusId);
        $organizationDto->setStatus($status);
        $organizationDto->setIsLdapSamlEnabled($ldapSamlEnabled);
        $organizationDto->setNickName($nickName);
        $organizationDto->setName($name);
        $organizationDto->setTimezone($timeZone);
        return $organizationDto;
    }

    /**
     * Create Organization
     *
     * @param array $organization
     * @return Organization
     */
    private function createOrganization($organization)
    {
        $campusId = $organization['campus_id'];
        $status = $organization['status'];
        $inActivityTimeOut = $organization['inactivity_timeout'];
        $timeZone = $organization['timezone'];
        $organization = new Organization();
        $organization->setCampusId($campusId);
        $organization->setStatus($status);
        $organization->setTimeZone($timeZone);
        $organization->setInactivityTimeout($inActivityTimeOut);
        $organization->setCustomConfidentialityStatement(true);
        return $organization;
    }

    /**
     * Create Organization update statement DTO
     *
     * @param array $organization
     * @return OrgStmtUpdateDto
     */
    private function createOrganizationStatementDto($organization)
    {
        $organizationStatementDto = new OrgStmtUpdateDto();
        $organizationStatementDto->setOrganizationId($organization['organization_id']);
        $organizationStatementDto->setCustomConfidentialityStatement($organization['confidentiality_statement']);
        return $organizationStatementDto;
    }
}