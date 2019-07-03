<?php

use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\UsersHelperService;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Entity\OrgPersonStudentYear;
use Synapse\CoreBundle\Service\Impl\ReferralService;
use Synapse\StudentViewBundle\Service\Impl\StudentAppointmentService;
use Synapse\JobBundle\Service\Impl\JobService;
use Synapse\CoreBundle\Repository\AppointmentRecepientAndStatusRepository;

class UserHelperServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;


    public function testGetServiceAccounts()
    {

        $this->specify("Get Service Accounts", function ($organizationId, $serviceAccountsArray) {
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


            //Repository Mocks
            $mockOrganizationRoleRepository = $this->getMock("OrganizationRoleRepository", ["getServiceAccountsForOrganization"]);
            $mockOrganizationRoleRepository->method('getServiceAccountsForOrganization')->willReturn($serviceAccountsArray);


            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrganizationRoleRepository::REPOSITORY_KEY,
                        $mockOrganizationRoleRepository
                    ],

                ]);

            $userHelperService = new UsersHelperService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $serviceAccounts = $userHelperService->getServiceAccounts($organizationId);

            foreach ($serviceAccounts['service_accounts'] as $serviceAccountIndex => $serviceAccount) {
                foreach ($serviceAccount as $serviceAccountKey => $serviceAccountValue) {

                    if ($serviceAccountKey == "key_creation_date") {
                        $keyCreationDateObject = new \DateTime($serviceAccountsArray[$serviceAccountIndex]['modified_at']);
                        $this->assertEquals($keyCreationDateObject, $serviceAccountValue);
                    } else {
                        $this->assertEquals($serviceAccountsArray[$serviceAccountIndex][$serviceAccountKey], $serviceAccountValue);
                    }
                }
            }

        }, [

                'examples' => [
                    // returning only one service accounts
                    [
                        1,

                        [

                            [
                                'id' => 1,
                                'lastname' => "service account",
                                'roleid' => 1,
                                'role' => "Api Coordinator",
                                'client_id' => "1_somerandomtext",
                                'client_secret' => "somerandomtext",
                                'modified_at' => "2017-03-15 10:00:00"
                            ]

                        ],
                        // returning multiple service accounts
                        [
                            1,

                            [

                                [
                                    'id' => 1,
                                    'lastname' => "service account",
                                    'roleid' => 1,
                                    'role' => "Api Coordinator",
                                    'client_id' => "1_somerandomtext",
                                    'client_secret' => "somerandomtext",
                                    'modified_at' => "2017-03-15 10:00:00"
                                ],
                                [
                                    'id' => 2,
                                    'lastname' => "service account2",
                                    'roleid' => 1,
                                    'role' => "Api Coordinator",
                                    'client_id' => "1_somerandomtext1",
                                    'client_secret' => "somerandomtext1",
                                    'modified_at' => "2017-03-14 10:00:00"
                                ]

                            ]
                        ]
                    ]
                ]
            ]
        );
    }

    public function testValidateExternalId()
    {

        $this->specify("Test Validate External Id", function ($externalId, $organizationId, $userType, $userName, $userId, $entity, $tier, $errorType, $expectedResult) {
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
            $mockPersonRepository = $this->getMock("PersonRepository", ["findOneBy", "validateEntityExtId", "findExternalIdByCampusIds", "find"]);
            $mockOrganizationRoleRepository = $this->getMock("OrganizationRoleRepository", ["findOneBy"]);
            $mockOrgPersonFacultyRepository = $this->getMock("OrgPersonFacultyRepository", ["findOneBy"]);
            $mockOrgPersonStudentRepository = $this->getMock("OrgPersonStudentRepository", ["findOneBy"]);

            $organization = $this->getOrganizationInstance($organizationId, $tier);
            $mockPerson = $this->getMock('Person', ['getId', 'getUsername', 'getExternalId']);
            $mockPerson->method('getId')->willReturn($userId);
            $mockPerson->method('getUsername')->willReturn($userName);
            $mockPerson->method('getExternalId')->willReturn($externalId);

            if ($errorType == 'email_exist') {
                $mockPersonRepository->method('findOneBy')->will($this->throwException(new SynapseValidationException(ucfirst($userType) . ' email already exist.')));
            } else if ($errorType == 'external_id_exist') {
                $mockPersonRepository->method('findOneBy')->will($this->throwException(new SynapseValidationException(ucfirst($userType) . ' ID already exist.')));
            } else if ($errorType == 'user_exist') {
                $mockPersonRepository->method('findOneBy')->will($this->throwException(new SynapseValidationException('User already exists in the system')));
            } else if ($errorType == 'external_id_exists') {
                $mockPersonRepository->method('validateEntityExtId')->will($this->throwException(new SynapseValidationException('User already exists in the system')));
            } else {
                $mockPersonRepository->method('findOneBy')->willReturn($mockPerson);
            }

            $mockPersonRepository->method('find')->willReturn($mockPerson);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
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
                    ]
                ]);

            try {
                $userHelperService = new UsersHelperService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $userHelperService->validateExternalId($externalId, $organization, $userId, $entity, $userType, $userName);

                $this->assertInternalType('object', $result);
                $this->assertEquals($result->getId(), $userId);
                $this->assertEquals($result->getUsername(), $userName);

            } catch (SynapseException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }


        }, [
                'examples' => [
                    // Test0: Throwing exception as The provided External ID is not allowed.
                    [
                        '#clear',
                        1,
                        'faculty',
                        null,
                        null,
                        false,
                        null,
                        '',
                        'The provided External ID is not allowed.'
                    ],
                    // Test1: Throwing exception as {UserType} email already exist.
                    [
                        'X10001',
                        1,
                        'faculty',
                        null,
                        null,
                        false,
                        null,
                        'email_exist',
                        'Faculty email already exist.'
                    ],
                    // Test2: Throwing exception as {UserType} ID already exist.
                    [
                        'X10001',
                        1,
                        'faculty',
                        null,
                        null,
                        false,
                        null,
                        'external_id_exist',
                        'Faculty ID already exist.'
                    ],
                    // Test3: Throwing exception as User already exists in the system.
                    [
                        'X10001',
                        1,
                        'faculty',
                        'test@mailinator.com',
                        null,
                        false,
                        null,
                        'user_exist',
                        'User already exists in the system'
                    ],
                    // Test4: Throwing exception as Faculty already exists in the system
                    [
                        'X10001',
                        1,
                        'faculty',
                        'test@mailinator.com',
                        1,
                        false,
                        null,
                        '',
                        'Faculty already exist.'
                    ],
                    // Test5: Throwing exception as Student ID already exists in the system
                    [
                        'X10001',
                        1,
                        'student',
                        'test@mailinator.com',
                        1,
                        false,
                        null,
                        'external_id_exist',
                        'Student ID already exist.'
                    ],
                    // Test6: Throwing exception as Student email already exists in the system
                    [
                        'X10001',
                        1,
                        'student',
                        'test@mailinator.com',
                        1,
                        false,
                        null,
                        'email_exist',
                        'Student email already exist.'
                    ],
                    // Test7: When entity is null, throwing exception as User already exists in the system.
                    [
                        'X10001',
                        1,
                        'faculty',
                        'test@mailinator.com',
                        1,
                        true,
                        null,
                        'external_id_exists',
                        'User already exists in the system'
                    ]
                ]
            ]
        );
    }


    private function getOrganizationInstance($campusId = 1, $tier = '0')
    {
        $organization = new Organization();
        $organization->setCampusId($campusId);
        $organization->setTier($tier);

        return $organization;
    }


    public function testUpdateStudentAsNonParticipating()
    {

        $this->specify("Test update students as non participant", function ($isParticipating, $currentParticipantStatus) {

            $studentId = 1;
            $organizationId = 1;
            $loggedInUserId = 3;

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

            $orgPersonStudentYearEntity = new OrgPersonStudentYear();

            $mockOrgPersonStudentYearRepositoryMock = $this->getMock('OrgPersonStudentYearRepository', ['findOneBy']);
            if ($currentParticipantStatus) {
                $mockOrgPersonStudentYearRepositoryMock->method('findOneBy')->willReturn($orgPersonStudentYearEntity);
            } else {
                $mockOrgPersonStudentYearRepositoryMock->method('findOneBy')->willReturn(null);
            }
            $mockAcademicYearService = $this->getMock("AcademicYearService", ['getCurrentOrgAcademicYearId']);
            $mockAcademicYearService->method('getCurrentOrgAcademicYearId')->willReturn(1);

            $mockJobService = $this->getMock('JobService', ['addJobToQueue']);
            $mockJobService->method('addJobToQueue')->willReturn(1);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgPersonStudentYearRepository::REPOSITORY_KEY,
                        $mockOrgPersonStudentYearRepositoryMock
                    ]
                ]);

            $mockContainer->method('get')->willReturnMap(
                [
                    [
                        AcademicYearService::SERVICE_KEY,
                        $mockAcademicYearService
                    ],
                    [
                        JobService::SERVICE_KEY,
                        $mockJobService
                    ]
                ]);

            $userHelperService = new UsersHelperService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $userHelperService->updateStudentAsNonParticipating($studentId, $organizationId, $isParticipating, $loggedInUserId);
            if ($currentParticipantStatus) {
                $this->assertInstanceOf("Synapse\CoreBundle\Entity\OrgPersonStudentYear", $result);
                if (!$isParticipating) {
                    $this->assertNotNull($result->getDeletedAt());
                } else {
                    $this->assertNull($result->getDeletedAt());
                }
            } else {
                $this->assertNull($result);
            }
        }, [
                'examples' => [
                    // isParticipating = 1 , $currentParticipationStatus = true , when participating status is to be set 1 , no changes made
                    [
                        1, true
                    ],
                    // isParticipating = 0 , $currentParticipationStatus = true ,when participating status is to be set 0 , deleted_at should not be null
                    [
                        0, true
                    ],
                    // isParticipating = 0 , $currentParticipationStatus = false  ,result will be asserted as null ( method only  marks participant student non participant)
                    [
                        0, false
                    ],
                    // isParticipating = 1 , $currentParticipationStatus = false  ,result will be asserted as null ( method only  marks participant student non participant)
                    [
                        1, false
                    ]
                ]
            ]
        );

    }
}