<?php

use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Service\Impl\EmailPasswordService;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\DTO\StudentParticipationDTO;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\AccessTokenRepository;
use Synapse\CoreBundle\Repository\AuthCodeRepository;
use Synapse\CoreBundle\Repository\ClientRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgGroupStudentsRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RoleLangRepository;
use Synapse\CoreBundle\Repository\RoleRepository;
use Synapse\CoreBundle\Service\Impl\GroupService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Impl\ReferralService;
use Synapse\CoreBundle\Service\Impl\UsersHelperService;
use Synapse\CoreBundle\Service\Impl\UsersService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\DataBundle\DAO\InformationSchemaDAO;
use Synapse\RestBundle\Entity\UserDTO;
use Synapse\RestBundle\Entity\UserListDto;
use Synapse\PersonBundle\Repository\ContactInfoRepository;


class UsersServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;


    /**
     * @expectedException \Synapse\CoreBundle\Exception\SynapseValidationException
     */
    public function testCreateServiceAccount()
    {

        $this->specify("Test Creation of Service Accounts", function ($userDTO, $serviceAccountExist = false) {


            $mockOrganizationObject = $this->getMock("\Synapse\CoreBundle\Entity\Organization");


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


            $mockPersonRepository = $this->getMock('PersonRepository', ['findOneBy', 'persist']);

            $mockRole = new \Synapse\CoreBundle\Entity\Role();

            $mockRoleRepository = $this->getMock('RoleRepository', ['find']);
            $mockRoleRepository->method('find')->willReturn($mockRole);

            $mockAuthCodeRepository = $this->getMock('AuthCodeRepository', ['persist']);

            $mockAuthCodeRepository->method('persist')->willReturn(1);

            $mockOrganizationRoleRepository = $this->getMock('Synapse\CoreBundle\Entity\OrganizationRole', ['persist']);

            $mockPersonObject = $this->getMock('Synapse\CoreBundle\Entity\Person', []);
            $mockPersonRepository->method('persist')->willReturn($mockPersonObject);

            if ($serviceAccountExist) {
                $mockPersonRepository->method('findOneBy')->willReturn($mockPersonObject);
            } else {
                $mockPersonRepository->method('findOneBy')->willReturn(null);
            }

            $test = $mockPersonRepository->findOneBy();

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        RoleRepository::REPOSITORY_KEY,
                        $mockRoleRepository
                    ],
                    [
                        OrganizationRoleRepository::REPOSITORY_KEY,
                        $mockOrganizationRoleRepository

                    ],
                    [
                        AuthCodeRepository::REPOSITORY_KEY,
                        $mockAuthCodeRepository
                    ]
                ]
            );

            $mockContainer->method('get')->willReturnCallback(function ($inputData) {
                if ($inputData == SynapseConstant::CLIENT_MANAGER_CLASS_KEY) {
                    $mockClientManager = $this->getMock("ClientManager", ["createClient",
                        "updateClient"
                    ]);

                    $mockClientObject = $this->getMock('client', [
                            'getRandomId',
                            'getAllowedGrantTypes',
                            'getRedirectUris',
                            'getSecret'
                        ]
                    );

                    $clientReturnValue = "randomtext";
                    $mockClientObject->method('getRandomId')->willReturn($clientReturnValue);
                    $mockClientObject->method('getAllowedGrantTypes')->willReturn([$clientReturnValue]);
                    $mockClientObject->method('getRedirectUris')->willReturn([$clientReturnValue]);
                    $mockClientObject->method('getSecret')->willReturn($clientReturnValue);
                    $mockClientManager->method('createClient')->willReturn($mockClientObject);
                    return $mockClientManager;
                } else if ($inputData == InformationSchemaDAO::DAO_KEY) {

                    $mockInformatioSchemaDao = $this->getMock("InformationSchema", ["getCharacterLengthForColumnsInTable"]);
                    $mockInformatioSchemaDao->method('getCharacterLengthForColumnsInTable')->willReturn([
                        ['length' => 45]
                    ]);
                    return $mockInformatioSchemaDao;
                } else {
                    return 1;
                }
            });
            // Entities Mock
            $mockRoleObj = $this->getMock('Synapse\CoreBundle\Entity\Role', []);

            //Repository Mocks
            $mockRoleRepository = $this->getMock("RoleRepository", ["find", "persist"]);
            $mockPersonRepository = $this->getMock("PersonRepository", ["persist"]);
            $mockOrganizationRoleRepository = $this->getMock("OrganizationRoleRepository", ["persist"]);
            $mockAuthCodeRepository = $this->getMock("AuthCodeRepository", ['persist']);

            $mockRoleRepository->method('find')->willReturn($mockRoleObj);

            $mockPersonRepository->method('persist')->willReturnCallback(function ($inputData) {
                $inputData->setId(1);
                return $inputData;
            });
            $mockOrganizationRoleRepository->method('persist')->willReturn(1);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrganizationRoleRepository::REPOSITORY_KEY,
                        $mockOrganizationRoleRepository
                    ],
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        AuthCodeRepository::REPOSITORY_KEY,
                        $mockAuthCodeRepository
                    ],
                    [
                        RoleRepository::REPOSITORY_KEY,
                        $mockRoleRepository
                    ]
                ]);

            $this->assertNull($userDTO->getAuthCode()); // Should be null
            $this->assertNull($userDTO->getClientSecret()); // Should be null
            $this->assertNull($userDTO->getClientId()); // Should be null
            $lastName = $userDTO->getLastname(); // capturing last name before the dto is updated by the createServiceAccount method

            $userService = new UsersService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $userDTO = $userService->createServiceAccount($userDTO, $mockOrganizationObject);

            $this->assertEquals($userDTO->getLastname(), $lastName);
            $this->assertNotNull($userDTO->getAuthCode()); // should not be null
            $this->assertEquals($userDTO->getClientSecret(), "randomtext");
            $this->assertEquals($userDTO->getClientId(), "_randomtext"); //  the client id should be prefixed by  the auto incremented  like "1_randomtext" , but as it is handled by the vendor code which does not allows us to set it's value.
        }, [
                'examples' => [
                    //creating service account with name "Service Account"
                    [
                        $this->createUserDto(1, "Service Account")
                    ],
                    //creating service account with name "Service Account2"
                    [
                        $this->createUserDto(1, "Service Account2")
                    ],
                    // Throws exception, that the service account already exists for the organization
                    [
                        $this->createUserDto(1, "Service Account2"), true
                    ],
                    // Throws exception, that the service account  cannot be greater than 45 characters
                    [
                        $this->createUserDto(1, "Service Account2 Testing more than forty five characters")
                    ],
                ]
            ]
        );
    }


    /**
     * @expectedException Synapse\CoreBundle\Exception\SynapseValidationException
     */
    public function testDeleteServiceAccount()
    {

        $this->specify("Test deleting of Service Accounts", function ($serviceAccountId, $organizationId, $requestedServiceAccountRoleId, $serviceAccountRoleId, $serviceAccountNotPresent = false, $missingServiceAccountRole = false) {


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

            $mockContainer->method('get')->willReturn(1);
            // Entities Mock

            $mockPerson = $this->getMock('Person', []);

            $mockRoleServiceAccount = $this->getMock('Role', ['getId']);
            $mockRoleServiceAccount->method('getId')->willReturn($requestedServiceAccountRoleId); // this is the requested users role id

            $mockRole = $this->getMock('Role', ['getId']);
            $mockRole->method('getId')->willReturn($serviceAccountRoleId); // this is treated as the role id from db

            $mockOrganizationRole = $this->getMock('OrganizationRole', ['getRole', 'getPerson']);
            $mockOrganizationRole->method('getRole')->willReturn($mockRoleServiceAccount);
            $mockOrganizationRole->method('getPerson')->willReturn($mockPerson);

            $mockRoleLang = $this->getMock('RoleLang', ['getRole']);
            $mockRoleLang->method('getRole')->willReturn($mockRole);


            //Repository Mocks
            $mockRoleRepository = $this->getMock("RoleRepository", ['findOneBy', 'delete']);
            $mockPersonRepository = $this->getMock("PersonRepository", ['findOneBy', 'delete']);
            $mockOrganizationRoleRepository = $this->getMock("OrganizationRoleRepository", ['findOneBy', 'delete']);
            $mockAuthCodeRepository = $this->getMock("AuthCodeRepository", ['findOneBy', 'delete']);
            $mockClientRepository = $this->getMock("ClientRepository", ['findOneBy', 'delete']);
            $mockRoleLangRepository = $this->getMock("RoleLang", ['findOneBy', 'delete']);

            if ($serviceAccountNotPresent) {
                $mockOrganizationRoleRepository->method('findOneBy')->willReturn(null);
            } else {
                $mockOrganizationRoleRepository->method('findOneBy')->willReturn($mockOrganizationRole);
            }
            if ($missingServiceAccountRole) {
                $mockRoleLangRepository->method('findOneBy')->willReturn(null);
            } else {
                $mockRoleLangRepository->method('findOneBy')->willReturn($mockRoleLang);
            }


            $mockOrganizationRoleRepository->method('persist')->willReturn(1);

            $mockAccessTokenRepository = $this->getMock("AccessTokenRepository", ['invalidateAccessTokensForUser']);
            $mockAccessTokenRepository->method('invalidateAccessTokensForUser')->willReturn(1);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrganizationRoleRepository::REPOSITORY_KEY,
                        $mockOrganizationRoleRepository
                    ],
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        AuthCodeRepository::REPOSITORY_KEY,
                        $mockAuthCodeRepository
                    ],
                    [
                        RoleRepository::REPOSITORY_KEY,
                        $mockRoleRepository
                    ],
                    [
                        ClientRepository::REPOSITORY_KEY,
                        $mockClientRepository
                    ],
                    [
                        RoleLangRepository::REPOSITORY_KEY,
                        $mockRoleLangRepository
                    ],
                    [
                        AccessTokenRepository::REPOSITORY_KEY,
                        $mockAccessTokenRepository
                    ]
                ]);

            $userService = new UsersService($mockRepositoryResolver, $mockLogger, $mockContainer);
            // this method will not have any assertions as it does not return any value and only throws exception, it would only throw exceptions
            $userService->deleteServiceAccount($serviceAccountId, $organizationId);
        }, [
                'examples' => [
                    //deleting the service account
                    [
                        1, 1, 1, 1
                    ],
                    // Will throw exception since the person id passed is not a service account , Not a valid service account
                    [
                        1, 1, 2, 1
                    ],
                    // Will throw exception , Service account id requested for deletion not found in db
                    [
                        1, 1, 2, 1, true
                    ],
                    // Will throw exception , Service account role is not present in db
                    [
                        1, 1, 1, 1, false, true
                    ],
                ]
            ]
        );
    }

    public function createUserDto($roleId, $serviceAccountName)
    {

        $userDto = new UserDTO();
        $userDto->setRoleid($roleId);
        $userDto->setLastname($serviceAccountName);
        return $userDto;
    }

    public function testListOrganizationUsersByType()
    {
        $this->specify("Test List users by organization", function ($organizationId, $userType, $exclude, $searchText, $participantFilter, $sortBy, $pageNumber, $limit, $checkAccessToOrg, $userData, $expectedExceptionClass, $expectedExceptionMessage) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockOrganizationRepository = $this->getMock('OrganizationRepository', array('find'));
            $mockPersonRepository = $this->getMock('PersonRepository', array(
                'getOrganizationFacultiesBySearchText',
                'getOrganizationFacultyCountBySearchText',
                'getOrganizationStudentsBySearchText',
                'getOrganizationStudentCountBySearchText'
            ));
            $mockAcademicYearService = $this->getMock('AcademicYearService', array('getCurrentOrgAcademicYearId'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [AcademicYearService::SERVICE_KEY, $mockAcademicYearService]
                ]);

            if (is_int($organizationId)) {
                $mockOrganization = new Organization();
                $mockOrganizationRepository->method('find')->willReturn($mockOrganization);
            } else {
                $mockOrganizationRepository->method('find')->willReturn('');
            }

            $expectedResults = $this->getUserDetails($userData, $userType);
            if ($userType == 'faculty') {
                $mockPersonRepository->method('getOrganizationFacultiesBySearchText')->willReturn($userData);
            } else {
                $mockPersonRepository->method('getOrganizationStudentsBySearchText')->willReturn($userData);
            }

            $userService = new UsersService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $results = $userService->listOrganizationUsersByType($organizationId, $userType, $exclude, $searchText, $participantFilter, $sortBy, $pageNumber, $limit, $checkAccessToOrg);
                $this->assertEquals($expectedResults, $results);
            } catch (SynapseException $exception) {

                verify($exception)->isInstanceOf($expectedExceptionClass);
                verify($exception->getMessage())->equals($expectedExceptionMessage);
            }

        }, [
            'examples' => [
                [
                    // List participant students
                    213, 'student', '', '', 'participant', '+name', 1, 10, false,
                    [
                        [
                            'modified_at' => '2017-01-08',
                            'welcome_email_sent_date' => '2017-01-08',
                            'primary_mobile' => 0,
                            'id' => 897878,
                            'firstname' => 'Robert',
                            'lastname' => 'Joseph',
                            'title' => 'student',
                            'primary_email' => 'robert@skyfactor.com',
                            'external_id' => 'TES456',
                            'home_phone' => 8978787,
                            'status' => '1',
                            'participant' => 1,
                        ]
                    ], '', ''
                ],
                // List non - participant students.
                [
                    213, 'student', '', '', 'non-participant', '+name', 1, 10, false,
                    [
                        [
                            'modified_at' => '2015-11-23',
                            'welcome_email_sent_date' => '2015-10-25',
                            'primary_mobile' => 0,
                            'id' => 452121,
                            'firstname' => 'Jose',
                            'lastname' => 'Robin',
                            'title' => 'student',
                            'primary_email' => 'robin@skyfactor.com',
                            'external_id' => 'ROB124',
                            'home_phone' => 4457878,
                            'status' => '0',
                            'participant' => 0,
                        ]
                    ], '', ''
                ],

                // List participant students where search text is test
                [
                    213, 'student', '', 'test', 'non-participant', '+name', 1, 10, false,
                    [
                        [
                            'modified_at' => '2016-11-15',
                            'welcome_email_sent_date' => '2016-10-14',
                            'primary_mobile' => 0,
                            'id' => 4512785,
                            'firstname' => 'Johnson',
                            'lastname' => 'Gruz',
                            'title' => 'student',
                            'primary_email' => 'testjohnson@skyfactor.com',
                            'external_id' => 'TEST456',
                            'home_phone' => 1254856,
                            'status' => '0',
                            'participant' => 0,
                        ]
                    ], '', ''
                ],
                // List faculties
                [
                    213, 'faculty', '', '', '', '', 1, 10, false,
                    [
                        [
                            'modified_at' => '2017-01-02',
                            'welcome_email_sent_date' => '2017-01-02',
                            'primary_mobile' => 0,
                            'id' => 1214545,
                            'firstname' => 'Anish',
                            'lastname' => 'Gruz',
                            'title' => 'faculty',
                            'primary_email' => 'testjohnson@skyfactor.com',
                            'external_id' => 'ERD1212',
                            'home_phone' => 454545,
                            'status' => '1'
                        ]
                    ], '', ''
                ],
                // List faculties where search text is test
                [
                    213, 'faculty', '', '', '', '', 1, 10, false,
                    [
                        [
                            'modified_at' => '2016-01-02',
                            'welcome_email_sent_date' => '2016-01-02',
                            'primary_mobile' => 0,
                            'id' => 1214545,
                            'firstname' => 'Test',
                            'lastname' => 'Coocrdinator',
                            'title' => 'faculty',
                            'primary_email' => 'testcoordinator@skyfactor.com',
                            'external_id' => 'DFF898',
                            'home_phone' => 124578,
                            'status' => '1'
                        ]
                    ], '', ''
                ],
                // Empty organization id wll throw SynapseValidationException
                ['', 'student', '', '', '', '', 1, 10, false, [], '\Synapse\CoreBundle\Exception\SynapseValidationException', 'Organization Not Found.'],
                // in valid organization id wll throw SynapseValidationException
                ['invalidOrg', 'student', '', '', '', '', 1, 10, false, [], '\Synapse\CoreBundle\Exception\SynapseValidationException', 'Organization Not Found.'],
            ]
        ]);
    }

    /**
     * @param array $users
     * @param string $userType
     * @return UserListDto
     */
    private function getUserDetails($users, $userType)
    {
        $userListDto = new UserListDto();
        foreach ($users as $user) {
            $userListDto->setLastUpdated($user['modified_at']);
            $userListDto->setTotalPages(0);
            $userListDto->setTotalRecords(0);
            $userListDto->setRecordsPerPage(10);
            $userListDto->setCurrentPage(1);
            $userDto = new UserDTO();
            $userDto->setId($user['id']);
            $userDto->setFirstname($user['firstname']);
            $userDto->setLastname($user['lastname']);
            $userDto->setTitle($user['title']);
            $userDto->setEmail($user['primary_email']);
            $userDto->setExternalid($user['external_id']);
            $isActive = $user['status'] === '0' ? 0 : 1;
            $userDto->setIsActive($isActive);
            $userDto->setPhone($user['home_phone']);
            $isMobile = $user['primary_mobile'] ? true : false;
            $userDto->setIsmobile($isMobile);
            $userDto->setWelcomeEmailSentDate($user['welcome_email_sent_date']);
            if ($userType == 'student') {
                $userDto->setParticipantStatus($user['participant']);
            }
            $usersArray[] = $userDto;
            if ($userType == 'student') {
                $userListDto->setStudent($usersArray);
            } else {
                $userListDto->setFaculty($usersArray);
            }
        }
        return $userListDto;
    }


    public function testGetUsers()
    {
        $this->specify("Test list users by its type", function ($organizationId, $userType, $exclude, $searchText, $participantFilter, $sortBy, $pageNumber, $limit, $checkAccessToOrganization, $userData, $expectedExceptionClass, $expectedExceptionMessage) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockOrganizationRepository = $this->getMock('OrganizationRepository', array('find'));
            $mockPersonRepository = $this->getMock('PersonRepository', array(
                'getOrganizationFacultiesBySearchText',
                'getOrganizationFacultyCountBySearchText',
                'getOrganizationStudentsBySearchText',
                'getOrganizationStudentCountBySearchText'
            ));
            $mockAcademicYearService = $this->getMock('AcademicYearService', array('getCurrentOrgAcademicYearId'));
            $mockUserHelperService = $this->getMock('UsersHelperService', array('getCoordinator'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [AcademicYearService::SERVICE_KEY, $mockAcademicYearService],
                    [UsersHelperService::SERVICE_KEY, $mockUserHelperService]
                ]);

            if (is_int($organizationId)) {
                $mockOrganization = new Organization();
                $mockOrganizationRepository->method('find')->willReturn($mockOrganization);
            }

            $expectedResults = $this->getUserDetails($userData, $userType);
            if ($userType == 'faculty') {
                $mockPersonRepository->method('getOrganizationFacultiesBySearchText')->willReturn($userData);
            } else {
                $mockPersonRepository->method('getOrganizationStudentsBySearchText')->willReturn($userData);
            }

            $userService = new UsersService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $results = $userService->getUsers($organizationId, $userType, $exclude, $searchText, $participantFilter, $sortBy, $pageNumber, $limit, $checkAccessToOrganization);
                $this->assertEquals($expectedResults, $results);
            } catch (SynapseException $exception) {
                verify($exception)->isInstanceOf($expectedExceptionClass);
                verify($exception->getMessage())->equals($expectedExceptionMessage);
            }

        }, [
            'examples' => [
                // Invalid user type will throw exception
                [123, 'invalid', '', '', '', '', 1, 10, NULL, [], '\Synapse\CoreBundle\Exception\SynapseValidationException', 'Invalid Type'],

                // List faculty users
                [
                    213, 'faculty', '', '', '', '', 1, 10, NULL,
                    [
                        [
                            'modified_at' => '2017-01-08',
                            'welcome_email_sent_date' => '2017-01-08',
                            'primary_mobile' => 0,
                            'id' => 897878,
                            'firstname' => 'Robert',
                            'lastname' => 'Joseph',
                            'title' => 'student',
                            'primary_email' => 'robert@skyfactor.com',
                            'external_id' => 'TES456',
                            'home_phone' => 8978787,
                            'status' => '1'
                        ]
                    ], '', ''
                ],

                // empty- invalid user type will throw exception
                ['123', '', '', '', '', '', 1, 10, NULL, [], '\Synapse\CoreBundle\Exception\SynapseValidationException', 'Invalid Type'],

                // List students
                [
                    213, 'student', '', '', 'non-participant', '+name', 1, 10, NULL,
                    [
                        [
                            'modified_at' => '2015-11-23',
                            'welcome_email_sent_date' => '2015-10-25',
                            'primary_mobile' => 0,
                            'id' => 452121,
                            'firstname' => 'Jose',
                            'lastname' => 'Robin',
                            'title' => 'student',
                            'primary_email' => 'robin@skyfactor.com',
                            'external_id' => 'ROB124',
                            'home_phone' => 4457878,
                            'status' => '0',
                            'participant' => 0,
                        ]
                    ], '', ''
                ],
                // Empty organization id wll throw SynapseValidationException
                ['', 'student', '', '', 'participant', '', 1, 10, NULL, [], '\Synapse\CoreBundle\Exception\SynapseValidationException', 'Organization Not Found.'],

                // in valid organization id wll throw SynapseValidationException
                ['test', 'faculty', '', '', '', '', 1, 10, NULL, [], '\Synapse\CoreBundle\Exception\SynapseValidationException', 'Organization Not Found.'],
            ]
        ]);
    }

    private $coordinatorContact = [
        "id" => 95,
        "firstname" => "Chris",
        "lastname" => "Coordinator",
        "title" => "Campus Coordinator",
        "email" => "Chris.Coordinator@mailinator.com",
        "phone" => "7675686867",
        "ismobile" => 1,
        "role" => "Mapworks Admin",
        "roleid" => 1
    ];


    public function testUpdateStudentParticipation()
    {
        $this->specify("Test Updates Student Participation status", function ($loggedInUserId, $organizationId, $studentParticipationDTO, $existingActiveStatus, $isParticipant, $expectedResult) {
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
            $mockPersonRepository = $this->getMock('PersonRepository', ['find', 'findOneBy', 'delete']);
            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', ['find']);
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['find']);
            $mockOrgPersonStudentYearRepository = $this->getMock('OrgPersonStudentYearRepository', ['findOneBy', 'persist']);
            // Service Mocks
            $mockAcademicYearService = $this->getMock('AcademicYearService', ['getCurrentOrgAcademicYearId']);
            $mockPersonService = $this->getMock('PersonService', ['getCoordinatorById']);
            $mockUsersHelperService = $this->getMock('UsersHelperService', ['updateStudentAsNonParticipating']);
            $mockReferralService = $this->getMock('ReferralService', ['sendCommunicationsRelatedToReferralsUponStudentParticipationStatusUpdate']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        OrgAcademicYearRepository::REPOSITORY_KEY,
                        $mockOrgAcademicYearRepository
                    ],
                    [
                        OrganizationRepository::REPOSITORY_KEY,
                        $mockOrganizationRepository
                    ],
                    [
                        OrgPersonStudentYearRepository::REPOSITORY_KEY,
                        $mockOrgPersonStudentYearRepository
                    ]
                ]);
            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        AcademicYearService::SERVICE_KEY,
                        $mockAcademicYearService
                    ],
                    [
                        PersonService::SERVICE_KEY,
                        $mockPersonService
                    ],
                    [
                        UsersHelperService::SERVICE_KEY,
                        $mockUsersHelperService
                    ],
                    [
                        ReferralService::SERVICE_KEY,
                        $mockReferralService
                    ],
                ]);
            // Entities Mock
            $mockPerson = $this->getMock('Person', ['getId']);
            $mockOrganization = $this->getMock('Organization', ['getId']);
            $mockOrgAcademicYear = $this->getMock('OrgAcademicYear', ['getId',]);
            $currentAcademicYear = 5;
            if ($isParticipant) {
                $mockOrgPersonStudentYear = $this->getMock('OrgPersonStudentYear', ['getId', 'getIsActive', 'setDeletedAt', 'setDeletedBy', 'setIsActive', 'setModifiedAt', 'setModifiedBy']);
                $mockOrgPersonStudentYear->method('getIsActive')->willReturn($existingActiveStatus);
            } else {
                $mockOrgPersonStudentYear = NULL;
            }
            $mockPerson->method('getId')->willReturn($loggedInUserId);
            $mockPersonRepository->method('find')->willReturn($mockPerson);
            $mockAcademicYearService->method('getCurrentOrgAcademicYearId')->willReturn($currentAcademicYear);
            $mockPersonService->method('getCoordinatorById')->willReturn([$this->coordinatorContact]);
            $mockOrgAcademicYear->method('getId')->willReturn($currentAcademicYear);
            $mockOrgAcademicYearRepository->method('find')->willReturn($mockOrgAcademicYear);
            $mockOrganization->method('getId')->willReturn($organizationId);
            $mockOrganizationRepository->method('find')->willReturn($mockOrganization);
            $mockOrgPersonStudentYearRepository->method('findOneBy')->willReturn($mockOrgPersonStudentYear);
            $userService = new UsersService($mockRepositoryResolver, $mockLogger, $mockContainer);
            // this method will not have any assertions as it does not return any value and only throws exception, it would only throw exceptions
            try {
                $userService->updateStudentParticipation($loggedInUserId, $organizationId, $studentParticipationDTO);
            } catch (\Synapse\CoreBundle\Exception\SynapseValidationException $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }

        }, [
                'examples' => [
                    //Inactive participant to inactive participant
                    [
                        95,
                        2,
                        $this->createStudentParticipationDTO(6, 0, 1),
                        0,
                        true,
                        'The student is already an inactive participant in the current year.'
                    ],
                    //Active participant to inactive participant
                    [
                        95,
                        2,
                        $this->createStudentParticipationDTO(6, 0, 1),
                        1,
                        true,
                        null
                    ],
                    //Inactive participant to active participant
                    [
                        95,
                        2,
                        $this->createStudentParticipationDTO(6, 1, 1),
                        0,
                        true,
                        null
                    ],
                    //Active participant to active participant
                    [
                        95,
                        2,
                        $this->createStudentParticipationDTO(6, 1, 1),
                        1,
                        true,
                        'The student is already an active participant in the current year.'
                    ],
                    //Inactive participant to active nonparticipant
                    [
                        95,
                        2,
                        $this->createStudentParticipationDTO(6, 1, 0),
                        0,
                        true,
                        'Active non-participant is not a valid state for a student'
                    ],
                    //Active participant to active nonparticipant
                    [
                        95,
                        2,
                        $this->createStudentParticipationDTO(6, 1, 0),
                        1,
                        true,
                        'Active non-participant is not a valid state for a student'
                    ],
                    //Inactive participant to inactive nonparticipant
                    [
                        95,
                        2,
                        $this->createStudentParticipationDTO(6, 0, 0),
                        0,
                        true,
                        null
                    ],
                    //Active participant to inactive nonparticipant
                    [
                        95,
                        2,
                        $this->createStudentParticipationDTO(6, 0, 0),
                        1,
                        true,
                        null
                    ],
                    //Inactive nonparticipant to inactive participant
                    [
                        95,
                        2,
                        $this->createStudentParticipationDTO(6, 0, 1),
                        null,
                        false,
                        null
                    ],
                    //Inactive nonparticipant to active participant
                    [
                        95,
                        2,
                        $this->createStudentParticipationDTO(6, 1, 1),
                        null,
                        false,
                        null
                    ],
                    //Inactive nonparticipant to active nonparticipant
                    [
                        95,
                        2,
                        $this->createStudentParticipationDTO(6, 1, 0),
                        null,
                        false,
                        'Active non-participant is not a valid state for a student.'
                    ],
                    //Inactive nonparticipant to inactive nonparticipant
                    [
                        95,
                        2,
                        $this->createStudentParticipationDTO(6, 0, 0),
                        null,
                        false,
                        'The student is already a non-participant in the current year.'
                    ]
                ]
            ]
        );
    }

    private function createStudentParticipationDTO($studentId, $activeStatus, $participatingStatus)
    {
        $studentParticipationDTO = new StudentParticipationDTO();
        $studentParticipationDTO->setStudentId($studentId);
        $studentParticipationDTO->setIsActive($activeStatus);
        $studentParticipationDTO->setIsParticipant($participatingStatus);
        return $studentParticipationDTO;
    }

    public function testDeleteUser()
    {
        $this->specify("Test delete User", function ($userId, $campusId, $userType, $errorType, $expectedResult) {
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
            $mockCache = $this->getMock('cache', array(
                'fetch',
                'save'
            ));

            //Repository Mocks
            $mockPersonRepository = $this->getMock('PersonRepository', ['find', 'flush', 'update', 'remove']);
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['find']);
            $mockOrganizationRoleRepository = $this->getMock('OrganizationRoleRepository', ['findOneBy', 'remove']);
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', ['findOneBy', 'remove']);
            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', ['findOneBy', 'remove']);
            $mockOrgGroupStudentsRepository = $this->getMock('OrgGroupStudentsRepository', ['deleteBulkStudentEnrolledGroups']);
            $mockContactInfoRepository = $this->getMock('ContactInfoRepository', ['getCurrentOrgAcademicYearId']);

            // Service Mocks
            $mockPersonService = $this->getMock('PersonService', ['getPerson']);
            $mockGroupService = $this->getMock('GroupService', ['validateOrganization', 'isPersonLocked']);
            $mockSecurityContext = $this->getMock('SecurityContext', ['getToken']);


            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        OrganizationRepository::REPOSITORY_KEY,
                        $mockOrganizationRepository
                    ],
                    [
                        OrganizationRoleRepository::REPOSITORY_KEY,
                        $mockOrganizationRoleRepository
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
                        OrgGroupStudentsRepository::REPOSITORY_KEY,
                        $mockOrgGroupStudentsRepository
                    ],
                    [
                        ContactInfoRepository::REPOSITORY_KEY,
                        $mockContactInfoRepository
                    ],
                ]);
            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        PersonService::SERVICE_KEY,
                        $mockPersonService
                    ],
                    [
                        GroupService::SERVICE_KEY,
                        $mockGroupService
                    ],
                    [
                        SynapseConstant::SECURITY_CONTEXT_CLASS_KEY,
                        $mockSecurityContext
                    ],
                    [
                        SynapseConstant::REDIS_CLASS_KEY,
                        $mockCache
                    ]
                ]);

            //Entity mocks
            $mockPerson = $this->getMock('Person', ['getId', 'getContacts', 'setExternalId', 'setUsername', 'getExternalId', 'getUsername', 'getIsLocked']);
            $mockPerson->method('getId')->willReturn($userId);
            $mockPerson->method('getContacts')->willReturn([]);
            $mockPerson->method('getExternalId')->willReturn('3435');
            $mockPerson->method('getUsername')->willReturn('test@ggg.com');
            if ($errorType == 'is_locked') {
                $mockPerson->method('getIsLocked')->willReturn('y');
            } else {
                $mockPerson->method('getIsLocked')->willReturn('n');
            }
            $mockOrganization = $this->getMock('Organization', ['getId']);
            $mockOrganization->method('getId')->willReturn($campusId);
            $mockOrganizationRole = $this->getMock('OrganizationRole', ['getId']);
            $mockOrganizationRole->method('getId')->willReturn(1);
            $mockOrgPersonFaculty = $this->getMock('OrgPersonFaculty', ['getId']);
            $mockOrgPersonFaculty->method('getPerson')->willReturn(1);
            $mockOrgPersonStudent = $this->getMock('OrgPersonStudent', ['getId']);
            $mockOrgPersonStudent->method('getPerson')->willReturn(1);


            if ($errorType == 'invalid_person') {
                $mockPersonRepository->method('find')->willThrowException(new SynapseValidationException('Person Not Found.'));
            } else {
                $mockPersonRepository->method('find')->willReturn($mockPerson);
            }

            if ($errorType == 'invalid_organization') {
                $mockOrganizationRepository->method('find')->willThrowException(new SynapseValidationException('Organization ID Not Found'));
            } else {
                $mockOrganizationRepository->method('find')->willReturn($mockOrganization);
            }

            if ($userType == 'coordinator') {
                if ($errorType == 'invalid_coordinator') {
                    $mockOrganizationRoleRepository->method('findOneBy')->willThrowException(new SynapseValidationException('Coordinator Role not found'));
                } else {
                    $mockOrganizationRoleRepository->method('findOneBy')->willReturn($mockOrganizationRole);
                }
            } elseif ($userType == 'faculty') {
                if ($errorType == 'invalid_faculty') {
                    $mockOrgPersonFacultyRepository->method('findOneBy')->willThrowException(new SynapseValidationException('Faculty Role not found'));
                } else {
                    $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonFaculty);
                }
            } elseif ($userType == 'student') {
                if ($errorType == 'invalid_student') {
                    $mockOrgPersonStudentRepository->method('findOneBy')->willThrowException(new SynapseValidationException('Student Role not found'));
                } else {
                    $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($mockOrgPersonStudent);
                }
            }
            if ($errorType == 'empty_person_role') {
                $mockPersonService->method('getPerson')->willReturn([]);

                $mockToken = $this->getMock('AccessToken', ['getUser']);
                $mockToken->method('getUser')->willReturn($mockPerson);
                $mockSecurityContext->method('getToken')->willReturn($mockToken);

                $mockCache->method('fetch')->willReturn([$userId]);

            } else {
                $mockPersonService->method('getPerson')->willReturn(['person_type' => 'coordinator']);
            }

            $userService = new UsersService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $result = $userService->deleteUser($userId, $campusId, $userType);
                $this->assertEquals($result, $expectedResult);
            } catch (SynapseValidationException $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }

        },
            [
                'examples' => [
                    //case 1 : Invalid user id, throws SynapseValidationException Person Not Found.
                    [
                        4555,
                        62,
                        '',
                        'invalid_person',
                        'Person Not Found.'
                    ],
                    //case 2 : Invalid organization id, throws SynapseValidationException Organization ID Not Found.
                    [
                        4850,
                        11,
                        '',
                        'invalid_organization',
                        'Organization ID Not Found'
                    ],
                    //case 3: Invalid coordinator, throws SynapseValidationException Coordinator Role not found
                    [
                        2122,
                        62,
                        'coordinator',
                        'invalid_coordinator',
                        'Coordinator Role not found'
                    ],
                    //case 4: Locked Faculty, throws SynapseValidationException
                    [
                        2117,
                        62,
                        'faculty',
                        'is_locked',
                        'We are unable to delete users who have activity or academic data associated with their Mapworks account.'
                    ],
                    //case 5: Invalid faculty, throws SynapseValidationException Faculty Role not found
                    [
                        2118,
                        62,
                        'faculty',
                        'invalid_faculty',
                        'Faculty Role not found'
                    ],
                    //case 6: Invalid student, throws SynapseValidationException Student Role not found
                    [
                        2120,
                        62,
                        'student',
                        'invalid_student',
                        'Student Role not found'
                    ],
                    //case 7: Invalid user Type, throws SynapseValidationException 'Invalid Type'
                    [
                        2128,
                        62,
                        '',
                        '',
                        'Invalid Type'
                    ],
                    //case 8: Valid Coordinator with no error, gets deleted and returns true
                    [
                        2050,
                        62,
                        'coordinator',
                        '',
                        true
                    ],
                    //case 9: Valid Coordinator with empty person role,  gets deleted and returns true
                    [
                        2055,
                        62,
                        'coordinator',
                        'empty_person_role',
                        true
                    ]
                ]
            ]
        );
    }


    public function testSendInvitation()
    {
        $this->specify("testSendInvitation", function ($expectedResults, $mockUserData = null, $mockEmailSendResult = null, $organizationId = null, $personId = null) {
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

            $mockPersonRepository = $this->getMock('personRepository', ['getUsersByUserIds']);

            $mockUserHelperService = $this->getMock('usersHelperService', ['validateOrganization']);
            $mockEmailPasswordService = $this->getMock('emailPasswordService', ['sendEmailWithInvitationLink']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [UsersHelperService::SERVICE_KEY, $mockUserHelperService],
                    [EmailPasswordService::SERVICE_KEY, $mockEmailPasswordService]
                ]);


            $mockUserHelperService->method('validateOrganization')->willReturn('');
            $mockPersonRepository->method('getUsersByUserIds')->willReturn($mockUserData);
            $mockEmailPasswordService->method('sendEmailWithInvitationLink')->willReturn($mockEmailSendResult);


            $userService = new UsersService($mockRepositoryResolver, $mockLogger, $mockContainer);

            try {
                $results = $userService->sendInvitation($organizationId, $personId);
                $this->assertEquals($expectedResults, $results);
            } catch (SynapseException $e) {
                $this->assertEquals($e->getMessage(), $expectedResults);
            }


        }, [
            'examples' =>
                [
                    //Email successfully sent
                    [
                        [
                            'email_sent_status' => true,
                            'welcome_email_sentDate' => '2017-01-01 00:00:00',
                            'message' => "Mail sent successfully to testemail@mailinator.com",
                            'email_detail' => [
                                'from' => 'noreply@mapworks.com',
                                'subject' => 'Welcome to Mapworks',
                                'bcc' => null,
                                'body' => 'body of email',
                                'to' => 'testemail@mailinator.com',
                                'emailKey' => 'welcome_to_mapworks',
                                'organizationId' => 1
                            ]
                        ],
                        [
                            0 => [
                                'username' => 'testemail@mailinator.com',
                                'person_id' => 1
                            ]
                        ],
                        [
                            'email_sent_status' => true,
                            'welcome_email_sentDate' => '2017-01-01 00:00:00',
                            'message' => "Mail sent successfully to testemail@mailinator.com",
                            'email_detail' => [
                                'from' => 'noreply@mapworks.com',
                                'subject' => 'Welcome to Mapworks',
                                'bcc' => null,
                                'body' => 'body of email',
                                'to' => 'testemail@mailinator.com',
                                'emailKey' => 'welcome_to_mapworks',
                                'organizationId' => 1
                            ]
                        ],
                        1,
                        1
                    ],
                    //No user found for invalid person ID & invalid org ID
                    [
                        "Person requested was not found."
                    ],
                ]
        ]);
    }
}