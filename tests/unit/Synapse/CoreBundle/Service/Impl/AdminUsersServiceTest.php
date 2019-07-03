<?php
namespace Synapse\CoreBundle\Service\Impl;

use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\Role;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RoleLangRepository;
use Synapse\CoreBundle\Repository\RoleRepository;
use Synapse\RestBundle\Entity\UserDTO;
use Synapse\RestBundle\Exception\ValidationException;


class AdminUsersServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    public function testCreateAdminUser()
    {
        $this->specify("Test create admin users", function ($userData, $errorType, $expectedErrorMessage, $expectedResult) {

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

            $mockPersonRepository = $this->getMock('PersonRepository', ['createPerson', 'checkExistingEmail', 'flush', 'getUsersByUserIds', 'find']);
            $mockEmailTemplateLangRepository = $this->getMock('EmailTemplateLangRepository', ['getEmailTemplateByKey']);
            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy']);
            $mockOrgRoleRepository = $this->getMock('OrganizationRoleRepository', ['createCoordinator']);
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['findOneById']);
            $mockRoleLangRepository = $this->getMock('RoleLangRepository', ['findOneByRoleName']);
            $mockRoleRepository = $this->getMock('RoleRepository', ['findOneById']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [EbiConfigRepository::REPOSITORY_KEY, $mockEbiConfigRepository],
                    [EmailTemplateLangRepository::REPOSITORY_KEY, $mockEmailTemplateLangRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [OrganizationRoleRepository::REPOSITORY_KEY, $mockOrgRoleRepository],
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                    [RoleLangRepository::REPOSITORY_KEY, $mockRoleLangRepository],
                    [RoleRepository::REPOSITORY_KEY, $mockRoleRepository]
                ]);

            $mockPasswordService = $this->getMock('PasswordService', ['generateTemporaryActivationToken']);
            $mockOrganizationService = $this->getMock('OrganizationService', ['getOrganizationDetailsLang']);
            $mockEmailService = $this->getMock('EmailService', ['generateEmailMessage', 'sendEmailNotification', 'sendEmail']);
            $mockEbiConfigService = $this->getMock('EbiConfigService', ['getSystemUrl']);
            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [EmailService::SERVICE_KEY, $mockEmailService],
                    [PasswordService::SERVICE_KEY, $mockPasswordService],
                    [OrganizationService::SERVICE_KEY, $mockOrganizationService]
                ]);


            if ($errorType == 'role_lang') {
                $mockRoleLangRepository->method('findOneByRoleName')->willThrowException(new ValidationException('', 'Role Not Found.'));
            } else if ($errorType == 'role') {
                $mockRoleRepository->method('findOneById')->willThrowException(new ValidationException('', 'Role Not Found.'));
            } else if ($errorType == 'organization') {
                $mockOrganizationRepository->method('findOneById')->willThrowException(new ValidationException('', 'Organization not found!'));
            }

            $mockRoleLang = $this->getMock('RoleLang', ['getRoleId', 'getRoleName', 'getRole']);
            $mockRoleLangRepository->method('findOneByRoleName')->willReturn($mockRoleLang);
            $mockRole = $this->getMock('Role', ['getId', 'getStatus']);
            $mockRoleRepository->method('findOneById')->willReturn(new Role());
            $mockRoleLang->method('getRole')->willReturn($mockRole);
            $mockOrganizationRepository->method('findOneById')->willReturn(new Organization());
            $usersDetails[] = $userData;
            $mockPersonRepository->method('getUsersByUserIds')->willReturn($usersDetails);
            $userDto = $this->createUserDto($userData);
            $mockPerson = $this->getMock('Person', ['getId', 'getOrganization', 'setActivationToken', 'getFirstname', 'setWelcomeEmailSentDate']);
            $mockPersonRepository->method('find')->willReturn($mockPerson);
            $mockPerson->method('getOrganization')->willReturn(new Organization());
            $mockOrganizationLang = $this->getMock('OrganizationLang', ['getLang']);
            $mockOrganizationService->method('getOrganizationDetailsLang')->willReturn($mockOrganizationLang);
            $mockLanguage = $this->getMock('Language', ['getId']);
            $mockOrganizationLang->method('getLang')->willReturn($mockLanguage);
            $adminUsersService = new AdminUsersService($mockRepositoryResolver, $mockLogger, $mockContainer);

            $mockEmailTemplateLang = $this->getMock('EmailTemplateLang', ['getBody', 'getEmailTemplate', 'getSubject']);
            $mockEmailTemplateLangRepository->method('getEmailTemplateByKey')->willReturn($mockEmailTemplateLang);
            $mockEmailTemplate = $this->getMock('EmailTemplate', ['getBccRecipientList', 'getFromEmailAddress']);
            $mockEmailTemplateLang->method('getEmailTemplate')->willReturn($mockEmailTemplate);
            $mockEbiConfig = $this->getMock('EbiConfig', ['getValue']);
            $mockEbiConfigRepository->method('findOneBy')->willReturn($mockEbiConfig);
            try {
                $response = $adminUsersService->createAdminUser($userDto, null);
                $this->assertEquals($response, $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($expectedErrorMessage, $e->getMessage());
            }
        }, [
            'examples' => [
                // Create admin user where the first name is empty should throw an exception.
                [
                    [
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example.com',
                        'first_name' => '',
                        'last_name' => 'Admin',
                        'send_invite' => false
                    ],
                    NULL,
                    'First name can not be an empty!',
                    NULL
                ],
                // Last name is empty should throw an exception.
                [
                    [
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example.com',
                        'first_name' => 'Skyfactor',
                        'last_name' => '',
                        'send_invite' => false
                    ],
                    NULL,
                    'Last name can not be an empty!',
                    NULL
                ],
                // Invalid email address should throw an exception.
                [
                    [
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example',
                        'first_name' => 'Skyfactor',
                        'last_name' => 'Admin',
                        'send_invite' => false
                    ],
                    NULL,
                    'Invalid email-Id!',
                    NULL
                ],
                // Create admin user with send_invite true.
                [
                    [
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example.com',
                        'first_name' => 'Skyfactor',
                        'last_name' => 'Admin',
                        'send_invite' => true
                    ],
                    NULL,
                    NULL,
                    $this->createUserDto([
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example.com',
                        'first_name' => 'Skyfactor',
                        'last_name' => 'Admin',
                        'send_invite' => true
                    ])
                ],
                // Create user where the organization not found.
                [
                    [
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example.com',
                        'first_name' => 'Skyfactor',
                        'last_name' => 'Admin',
                        'send_invite' => false
                    ],
                    'organization',
                    'Organization not found!',
                    NULL
                ],
                // Create admin user where role lang not found.
                [
                    [
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example.com',
                        'first_name' => 'Skyfactor',
                        'last_name' => 'Admin',
                        'send_invite' => false
                    ],
                    'role_lang',
                    'Role Not Found.',
                    NULL
                ],
                // Create admin user where role is not found.
                [
                    [
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example.com',
                        'first_name' => 'Skyfactor',
                        'last_name' => 'Admin',
                        'send_invite' => false
                    ],
                    'role',
                    'Role Not Found.',
                    NULL
                ],
                // Create admin user
                [
                    [
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example.com',
                        'first_name' => 'Skyfactor',
                        'last_name' => 'Admin',
                        'send_invite' => false
                    ],
                    NULL,
                    NULL,
                    $this->createUserDto([
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example.com',
                        'first_name' => 'Skyfactor',
                        'last_name' => 'Admin',
                        'send_invite' => false
                    ])
                ],
            ]
        ]);
    }

    public function testEditAdminUser()
    {
        $this->specify("Test edit admin user", function ($userData, $userId, $errorType, $expectedErrorMessage, $expectedResult) {

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

            $mockPersonRepository = $this->getMock('PersonRepository', ['find', 'checkExistingEmail', 'flush']);
            $mockOrgRoleRepository = $this->getMock('OrganizationRoleRepository', ['findOneBy', 'createCoordinator']);
            $mockRoleLangRepository = $this->getMock('RoleLangRepository', ['findOneByRoleName']);
            $mockRoleRepository = $this->getMock('RoleRepository', ['findOneById']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [OrganizationRoleRepository::REPOSITORY_KEY, $mockOrgRoleRepository],
                    [RoleRepository::REPOSITORY_KEY, $mockRoleRepository],
                    [RoleLangRepository::REPOSITORY_KEY, $mockRoleLangRepository]
                ]);

            if ($errorType == 'user') {
                $mockPersonRepository->method('find')->willThrowException(new ValidationException('', 'Invalid email-Id!'));
            } else if ($errorType == 'duplicate-email') {
                $mockPersonRepository->method('checkExistingEmail')->willThrowException(new ValidationException('', 'Email Address already exists!'));
            } else if ($errorType == 'role_lang') {
                $mockRoleLangRepository->method('findOneByRoleName')->willThrowException(new ValidationException('', 'Role Not Found.'));
            } else if ($errorType == 'role') {
                $mockRoleRepository->method('findOneById')->willThrowException(new ValidationException('', 'Role Not Found.'));
            }
            $personObject = $this->createPerson(4526387514);
            $mockPersonRepository->method('find')->willReturn($personObject);
            $mockRoleLang = $this->getMock('RoleLang', ['getRoleId', 'getRoleName', 'getRole']);
            $mockRoleLangRepository->method('findOneByRoleName')->willReturn($mockRoleLang);
            $mockRole = $this->getMock('Role', ['getId', 'getStatus']);
            $mockRoleRepository->method('findOneById')->willReturn(new Role());
            $mockRoleLang->method('getRole')->willReturn($mockRole);

            $userDto = $this->createUserDto($userData);
            $adminUsersService = new AdminUsersService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $response = $adminUsersService->editAdminUser($userDto, $userId);
                $this->assertEquals($response, $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($expectedErrorMessage, $e->getMessage());
            }
        }, [
            'examples' => [

                // Create admin user where the first name is empty should throw an exception.
                [
                    [
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example.com',
                        'first_name' => '',
                        'last_name' => 'Admin',
                        'send_invite' => false
                    ],
                    45133,
                    NULL,
                    'First name can not be an empty!',
                    NULL
                ],
                // Last name is empty should throw an exception.
                [
                    [
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example.com',
                        'first_name' => 'Skyfactor',
                        'last_name' => '',
                        'send_invite' => false
                    ],
                    45133,
                    NULL,
                    'Last name can not be an empty!',
                    NULL
                ],

                [
                    [
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example.com',
                        'first_name' => 'Skyfactor',
                        'last_name' => 'Admin',
                        'send_invite' => true
                    ],
                    45875,
                    NULL,
                    NULL,
                    $this->createUserDto([
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example.com',
                        'first_name' => 'Skyfactor',
                        'last_name' => 'Admin',
                        'send_invite' => true
                    ])
                ],

                // Create admin user where the first name is empty should throw an exception.
                [
                    [
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example.com',
                        'first_name' => 'Skyfactor',
                        'last_name' => 'Admin',
                        'send_invite' => false
                    ],
                    45875,
                    'user',
                    'Invalid email-Id!',
                    NULL
                ],

                [
                    [
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example.com',
                        'first_name' => 'Skyfactor',
                        'last_name' => 'Admin',
                        'send_invite' => false
                    ],
                    45875,
                    'duplicate-email',
                    'Email Address already exists!',
                    NULL
                ],
                // Create admin user where role lang not found.
                [
                    [
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example.com',
                        'first_name' => 'Skyfactor',
                        'last_name' => 'Admin',
                        'send_invite' => false
                    ],
                    45875,
                    'role_lang',
                    'Role Not Found.',
                    NULL
                ],
                // Create admin user where role is not found.
                [
                    [
                        'user_type' => 'Skyfactor Admin',
                        'user_email' => 'test@example.com',
                        'first_name' => 'Skyfactor',
                        'last_name' => 'Admin',
                        'send_invite' => false
                    ],
                    45875,
                    'role',
                    'Role Not Found.',
                    NULL
                ],
            ]
        ]);
    }

    public function testDeleteAdminUser()
    {
        $this->specify("Test delete admin user", function ($userId, $loggedInPersonId, $isPersonAvailable, $isPersonRole, $expectedErrorMessage) {

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

            $mockPersonRepository = $this->getMock('PersonRepository', ['find', 'flush', 'remove']);
            $mockOrgRoleRepository = $this->getMock('OrganizationRoleRepository', ['findOneByPerson', 'remove']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [OrganizationRoleRepository::REPOSITORY_KEY, $mockOrgRoleRepository],
                ]);
            $mockPerson = $this->getMock('Person', ['getId', 'getOrganization', 'getFirstname']);
            if ($isPersonAvailable) {
                $mockPersonRepository->method('find')->willReturn($mockPerson);
            } else {
                $mockPersonRepository->method('find')->willThrowException(new ValidationException('', 'Person not found!'));
            }
            $mockPersonRole = $this->getMock('OrganizationRole', ['getPerson', 'getOrganization']);
            if ($isPersonRole) {
                $mockOrgRoleRepository->method('findOneByPerson')->willReturn($mockPersonRole);
            } else {
                $mockOrgRoleRepository->method('findOneByPerson')->willThrowException(new ValidationException('', 'Person not found!'));
            }

            $adminUsersService = new AdminUsersService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $adminUsersService->deleteAdminUser($userId, $loggedInPersonId);
            } catch (\Exception $e) {
                $this->assertEquals($expectedErrorMessage, $e->getMessage());
            }
        }, [
            'examples' => [

                // Admin user can not be deleted by themselves should throw an exception.
                [412563, 412563, true, true, 'Users themselves can not be deleted!'],
                // Passing invalid id should throw exception
                ['invalid-id', 8546312, false, false, 'Person not found!'],
                // Empty person id should throw exception
                ['', 8546312, false, false, 'Person not found!'],
                // Passing invalid logged in user id should throw exception.
                [8546312, 'invalid-id', false, false, 'Person not found!'],
                // Delete admin user
                [8546312, 412563, true, true, NULL]
            ]
        ]);
    }

    /**
     * Create UserDTO
     *
     * @param array $users
     * @return UserDTO
     */
    private function createUserDto($users)
    {
        $userDto = new UserDTO();
        $userDto->setUserType($users['user_type']);
        $userDto->setFirstname($users['first_name']);
        $userDto->setLastname($users['last_name']);
        $userDto->setEmail($users['user_email']);
        $userDto->setSendinvite($users['send_invite']);
        return $userDto;
    }

    /**
     * Create Person Object
     *
     * @param int $homePhone
     * @return Person
     */
    private function createPerson($homePhone)
    {
        $person = new Person();
        $contacts = new ContactInfo();
        $contacts->setHomePhone($homePhone);
        $person->addContact($contacts);
        return $person;
    }

}