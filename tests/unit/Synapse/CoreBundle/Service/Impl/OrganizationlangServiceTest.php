<?php

use Synapse\CalendarBundle\Service\Impl\CalendarFactoryService;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Service\Impl\EmailPasswordService;
use Synapse\CoreBundle\Service\Impl\OrganizationlangService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\JobBundle\Service\Impl\JobService;
use Synapse\RestBundle\Entity\OrganizationDTO;

class OrganizationlangServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    private $organizationId = 1;

    private $coordinatorArray = [
        'coordinators' => [
            'id' => 1
        ]
    ];

    public function testUpdateOrganization()
    {
        $this->specify("Update Organization", function ($organizationId, $isCalendarSync, $errorType, $expectedResult) {
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

            // Initializing DTO
            $organizationDto = $this->getOrganizationDto($organizationId, $isCalendarSync);

            // Mock Repositories
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['find']);
            $mockOrganizationlangRepository = $this->getMock('OrganizationlangRepository', ['findOneBy', 'flush']);

            // Mock Services
            $mockValidator = $this->getMock('validator', ['validate']);
            $mockCalendarFactoryService = $this->getMock('CalendarFactoryService', ['getCountOfCalendarSyncUsers']);
            $mockJobService = $this->getMock('JobService', ['addJobToQueue']);
            $mockPersonService = $this->getMock('PersonService', ['getCoordinator']);
            $mockEmailPasswordService = $this->getMock('EmailPasswordService', ['sendEmailWithCoordinatorInvitationLink']);

            // Scaffolding for Repositories
            $mockRepositoryResolver->expects($this->any())
                ->method('getRepository')
                ->willReturnMap([
                    [
                        OrganizationRepository::REPOSITORY_KEY,
                        $mockOrganizationRepository
                    ],
                    [
                        OrganizationlangRepository::REPOSITORY_KEY,
                        $mockOrganizationlangRepository
                    ],
                ]);

            // Scaffolding for Services
            $mockContainer->method('get')->willReturnMap(
                [
                    [
                        SynapseConstant::VALIDATOR,
                        $mockValidator
                    ],
                    [
                        CalendarFactoryService::SERVICE_KEY,
                        $mockCalendarFactoryService
                    ],
                    [
                        JobService::SERVICE_KEY,
                        $mockJobService
                    ],
                    [
                        PersonService::SERVICE_KEY,
                        $mockPersonService
                    ],
                    [
                        EmailPasswordService::SERVICE_KEY,
                        $mockEmailPasswordService
                    ]
                ]);

            $mockOrganization = $this->getMock('Organization', ['getId', 'setSubdomain', 'setTimeZone', 'setCampusId', 'setStatus', 'setIsLdapSamlEnabled', 'setCalendarSync', 'setPcs', 'getSubdomain', 'getTimeZone', 'getCampusId', 'getIsLdapSamlEnabled', 'getStatus', 'getCalendarSync']);
            $mockOrganization->method('getSubdomain')->willReturn($organizationDto->getSubdomain());
            $mockOrganization->method('getTimeZone')->willReturn($organizationDto->getTimezone());
            $mockOrganization->method('getCampusId')->willReturn($organizationDto->getCampusId());
            $mockOrganization->method('getIsLdapSamlEnabled')->willReturn($organizationDto->getIsLdapSamlEnabled());
            $mockOrganization->method('getStatus')->willReturn($organizationDto->getStatus());
            $mockOrganization->method('getCalendarSync')->willReturn($organizationDto->getCalendarSync());

            if ($errorType == 'organization_not_found') {
                $mockOrganizationRepository->method('find')->willReturn(null);
            } else {
                $mockOrganizationRepository->method('find')->willReturn($mockOrganization);
            }

            $mockOrganizationLang = $this->getMock('OrganizationLang', ['setOrganizationName', 'setNickName', 'getOrganizationName', 'getNickName', 'getOrganization']);
            $mockOrganizationLang->method('setOrganizationName')->willReturn($organizationDto->getName());
            $mockOrganizationLang->method('setNickName')->willReturn($organizationDto->getNickName());
            $mockOrganizationLang->method('getOrganizationName')->willReturn($organizationDto->getName());
            $mockOrganizationLang->method('getNickName')->willReturn($organizationDto->getNickName());
            $mockOrganizationLang->method('getOrganization')->willReturn($mockOrganization);

            $mockOrganizationlangRepository->method('findOneBy')->willReturn($mockOrganizationLang);

            if ($errorType == 'organization_validation_error' || $errorType == 'organizationlang_validation_error') {
                $organizationValidationErrors = ['error' => $expectedResult];
                $errors = $this->arrayOfErrorObjects($organizationValidationErrors);
                $mockValidator->method('validate')->willReturn($errors);
            } else {
                $mockValidator->method('validate')->willReturn([]);
            }

            $mockPersonService->method('getCoordinator')->willReturn($this->coordinatorArray);

            try {
                $organizationLangService = new OrganizationlangService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $organizationLangService->updateOrganization($organizationDto);

                $this->assertEquals($result, $expectedResult);
            } catch (SynapseValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                // Test0: Organization not found exception
                [
                    $this->organizationId,
                    true,
                    'organization_not_found',
                    'The organization could not be found.'
                ],
                // Test1: Organization validation error
                [
                    $this->organizationId,
                    true,
                    'organization_validation_error',
                    'No special characters or spaces allowed'
                ],
                // Test2: OrganizationLang validation error
                [
                    $this->organizationId,
                    true,
                    'organizationlang_validation_error',
                    'Name cannot contain special characters'
                ],
                // Test3: Update organization with calendar_sync = true without error
                [
                    $this->organizationId,
                    true,
                    '',
                    [
                        'id' => $this->organizationId,
                        'name' => 'Test Organization ' . $this->organizationId,
                        'nick_name' => 'Beta Organization ' . $this->organizationId,
                        'subdomain' => 'synapsetesting000' . $this->organizationId,
                        'timezone' => 'Central',
                        'campus_id' => null,
                        'is_ldap_saml_enabled' => '1',
                        'status' => 'Active',
                        'calendar_sync' => true,
                        'calendar_sync_users' => null,
                    ]
                ],
                // Test4: Update organization with calendar_sync = false without error
                [
                    $this->organizationId,
                    false,
                    '',
                    [
                        'id' => $this->organizationId,
                        'name' => 'Test Organization ' . $this->organizationId,
                        'nick_name' => 'Beta Organization ' . $this->organizationId,
                        'subdomain' => 'synapsetesting000' . $this->organizationId,
                        'timezone' => 'Central',
                        'campus_id' => null,
                        'is_ldap_saml_enabled' => '1',
                        'status' => 'Active',
                        'calendar_sync' => false,
                        'calendar_sync_users' => null,
                    ]
                ],
            ]
        ]);
    }

    private function getOrganizationDto($organizationId, $calendarSync = true, $pcsRemove = true)
    {
        $organizationDto = new OrganizationDTO();
        $organizationDto->setId($organizationId);
        $organizationDto->setName("Test Organization " . $organizationId);
        $organizationDto->setNickName("Beta Organization " . $organizationId);
        $organizationDto->setSubdomain("synapsetesting000" . $organizationId);
        $organizationDto->setCampusId($organizationId);
        $organizationDto->setTimezone('Central');
        $organizationDto->setStatus('Active');
        $organizationDto->setLangid(1);
        $organizationDto->setIsLdapSamlEnabled('1');
        $organizationDto->setIsSendLink(1);
        $organizationDto->setCalendarSync($calendarSync);
        if (!$calendarSync) {
            $organizationDto->setPcsRemove($pcsRemove);
        }

        return $organizationDto;
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

    public function testGetOrganizations()
    {
        $this->specify("Test to get organizations", function ($organizationHasRecords, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', ['debug', 'error','info']);
            $mockContainer = $this->getMock('Container', array('get'));

            // Mock OrganizationLangRepository
            $mockOrganizationLangRepository = $this->getMock('OrganizationlangRepository', ['findAll']);
            $mockOrganization = $this->getMock('Organization', ['getId', 'getSubdomain', 'getTimeZone']);
            $mockOrganization->method('getId')->willReturn(62);
            $mockOrganization->method('getSubdomain')->willReturn('Test synapse');
            $mockOrganization->method('getTimeZone')->willReturn('Asia/Kolkata');
            $mockOrganizationLang = $this->getMock('OrganizationLang', ['getOrganization', 'getModifiedAt', 'getOrganizationName', 'getNickName']);
            $mockOrganizationLang->method('getOrganization')->willReturn($mockOrganization);
            $mockOrganizationLang->method('getModifiedAt')->willReturn(new \DateTime('2017-12-08 05:55:26'));
            $mockOrganizationLang->method('getOrganizationName')->willReturn('Synapse');
            $mockOrganizationLang->method('getNickName')->willReturn('Skyfactor');
            if($organizationHasRecords) {
                $mockOrganizationLangRepository->method('findAll')->willReturn([$mockOrganizationLang]);
            }else{
                $mockOrganizationLangRepository->method('findAll')->willReturn([]);
            }

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrganizationlangRepository::REPOSITORY_KEY, $mockOrganizationLangRepository],
                ]);
            $mockContainer->method('get')
                ->willReturnMap([
                    []
                ]);

            $organizationLangService = new OrganizationlangService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $organizationLangService->getOrganizations();
            $this->assertEquals($results, $expectedResult);

        },
            [
                'examples' => [

                    // Test 01 - If organization has records will return records array.
                    [
                        true,
                      [
                          'institutions'=> [
                              0 =>[
                                  'id' => 62,
                                  'name' => 'Synapse',
                                  'nick_name' => 'Skyfactor',
                                  'subdomain' => 'Test synapse',
                                  'timezone' => 'Asia/Kolkata',
                              ]
                          ],
                          'institutions_total_count' => 1,
                          'institutions_last_updated' => new \DateTime('2017-12-08 05:55:26')
                      ]
                    ],
                    // Test 02 - If organization has not record will return empty result array.
                    [
                        false,
                        [
                            'institutions'=> []
                        ]
                    ],
                ]
            ]
        );
    }

    public function testGetOrganization()
    {
        $this->specify("Test to get getOrganization", function ($organizationId, $organizationStatus, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', array('get'));

            // Mock OrganizationLangRepository
            $mockOrganizationLangRepository = $this->getMock('OrganizationlangRepository', ['findOneBy']);

            $mockOrganization = $this->getMock('Organization', ['getId', 'getSubdomain', 'getTimeZone', 'getCampusId', 'getIsLdapSamlEnabled', 'getStatus', 'getCalendarSync']);
            $mockOrganization->method('getId')->willReturn(62);
            $mockOrganization->method('getCampusId')->willReturn(192);
            $mockOrganization->method('getSubdomain')->willReturn('Test synapse');
            $mockOrganization->method('getTimeZone')->willReturn('Asia/Kolkata');
            $mockOrganization->method('getIsLdapSamlEnabled')->willReturn(true);
            $mockOrganization->method('getStatus')->willReturn($organizationStatus);
            $mockOrganization->method('getCalendarSync')->willReturn(true);

            $mockOrganizationLang = $this->getMock('OrganizationLang', ['getOrganization', 'getModifiedAt', 'getOrganizationName', 'getNickName']);
            $mockOrganizationLang->method('getOrganization')->willReturn($mockOrganization);
            $mockOrganizationLang->method('getOrganizationName')->willReturn('Oxford');
            $mockOrganizationLang->method('getNickName')->willReturn('Skyfactor');
            if ($organizationId > 0) {
                $mockOrganizationLangRepository->method('findOneBy')->willReturn($mockOrganizationLang);
            } else {
                $mockOrganizationLangRepository->method('findOneBy')->willThrowException(new SynapseValidationException('Organization Not Found.'));
            }

            // Mock calendarFactoryService
            $mockCalendarFactoryService = $this->getMock('CalendarFactoryService', ['getCountOfCalendarSyncUsers']);
            $mockCalendarFactoryService->method('getCountOfCalendarSyncUsers')->willReturn(900);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrganizationlangRepository::REPOSITORY_KEY, $mockOrganizationLangRepository],
                ]);
            $mockContainer->method('get')
                ->willReturnMap([
                    [CalendarFactoryService::SERVICE_KEY,$mockCalendarFactoryService]
                ]);
            try {
                $organizationLangService = new OrganizationlangService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $organizationLangService->getOrganization($organizationId);
                $this->assertEquals($results, $expectedResult);
            } catch (SynapseValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        },
            [
                'examples' => [

                    // Test 01 - Passing valid organization id and organization status as I will return organization details array
                    [
                        1,
                        'I',
                        [
                            'id' => 1,
                            'name' => 'Oxford',
                            'nick_name' => 'Skyfactor',
                            'subdomain' => 'Test synapse',
                            'timezone' => 'Asia/Kolkata',
                            'campus_id' => 192,
                            'is_ldap_saml_enabled' => true,
                            'status' => 'Inactive',
                            'calendar_sync' => true,
                            'calendar_sync_users' => 900
                        ]
                    ],
                // Test 02 - Passing valid organization id and organization status as A will return organization details array
                [
                    1,
                    'A',
                    [
                        'id' => 1,
                        'name' => 'Oxford',
                        'nick_name' => 'Skyfactor',
                        'subdomain' => 'Test synapse',
                        'timezone' => 'Asia/Kolkata',
                        'campus_id' => 192,
                        'is_ldap_saml_enabled' => true,
                        'status' => 'Active',
                        'calendar_sync' => true,
                        'calendar_sync_users' => 900
                ]
            ],
                    // Test 03 - Passing invalid organization id will return empty result array
                    [
                        -1,
                        'I',
                        'Organization Not Found.'
                    ],
                ]
            ]
        );
    }

    public function testGetLdapLoginDetails()
    {
        $this->specify("Test to getLdapLoginDetails method", function ($subDomain, $isLdapSamlEnabled, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock OrganizationRepository
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['findOneBy']);
            $mockOrganization = $this->getMock('Organization', ['getIsLdapSamlEnabled', 'getSubdomain', 'getId', 'getName']);
            if($isLdapSamlEnabled) {
                $mockOrganization->method('getIsLdapSamlEnabled')->willReturn('saml');
            }
            else{
                $mockOrganization->method('getIsLdapSamlEnabled')->willReturn($isLdapSamlEnabled);
            }
            $mockOrganization->method('getSubdomain')->willReturn('synapsetesting0001');
            $mockOrganization->method('getId')->willReturn(1);
            $mockOrganization->method('getName')->willReturn('Test Organization 1');
            if ($subDomain == -1 || $subDomain == null) {
                $mockOrganizationRepository->method('findOneBy')->willThrowException(new SynapseValidationException($expectedResult));
            } else {
                $mockOrganizationRepository->method('findOneBy')->willReturn($mockOrganization);
            }

            // Mock AuthConfigRepository
            $mockAuthConfigRepository = $this->getMock('OrgAuthConfigRepository', ['findOneByOrganization']);
            $mockAuthConfig = $this->getMock('OrgAuthConfig', ['getSamlStudentEnabled', 'getSamlStaffEnabled']);
            $mockAuthConfig->method('getSamlStudentEnabled')->willReturn(true);
            $mockAuthConfig->method('getSamlStaffEnabled')->willReturn(true);
            $mockAuthConfigRepository->method('findOneByOrganization')->willReturn($mockAuthConfig);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                    [\Synapse\AuthenticationBundle\Repository\OrgAuthConfigRepository::REPOSITORY_KEY, $mockAuthConfigRepository],
                ]);
            $mockContainer->method('get')
                ->willReturnMap([
                    []
                ]);
            try {
                $organizationLangService = new OrganizationlangService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $organizationLangService->getLdapLoginDetails($subDomain);
                verify($results)->equals($expectedResult);
            } catch (SynapseValidationException $e) {
                verify($expectedResult)->equals($e->getMessage());
            }

        },
            [
                'examples' => [

                    // Test 01 - Passing valid sub domain and isLdapSamlEnabled as true wil return ldap login details
                    [
                        'test',
                        true,
                        $this->getOrganizationDtoResponse(1, true)
                    ],
                    // Test 02 - Passing valid sub domain and isLdapSamlEnabled as false wil return ldap login details
                    [
                        'test',
                        false,
                        $this->getOrganizationDtoResponse(1, false)
                    ],
                    // Test 03 - Passing invalid sub domain and isLdapSamlEnabled as false wil throw exception
                    [
                        -1,
                        false,
                       'Subdomain Not Found.'
                    ],
                    // Test 04 - Passing sub domain as null and isLdapSamlEnabled as false wil throw exception
                    [
                        null,
                        false,
                        'Subdomain Not Found.'
                    ],
                ]
            ]
        );
    }

    private function getOrganizationDtoResponse($organizationId, $isLdapSamlEnabled)
    {
        $organizationDto = new OrganizationDTO();
        $organizationDto->setId($organizationId);
        if ($isLdapSamlEnabled) {
            $organizationDto->setIsLdapSamlEnabled('saml');
        } else {
            $organizationDto->setIsLdapSamlEnabled($isLdapSamlEnabled);
        }
        $organizationDto->setSubdomain("synapsetesting000" . $organizationId);
        return $organizationDto;
    }

}