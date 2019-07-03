<?php
namespace Synapse\CoreBundle\Service\Impl;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\ProxyLogRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\ProxyDto;
use Synapse\RestBundle\Exception\ValidationException;

class ProxyServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    public function testCreateProxy()
    {
        $this->specify("Test createProxy", function ($proxyDtoArray, $isEbiUser, $isCoordinator, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error']);
            $mockContainer = $this->getMock('Container', ['get', 'getParameter']);

            // Mock OrganizationRepository
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['find']);
            $mockOrganization = $this->getMock('Synapse\CoreBundle\Entity\Organization', ['getId']);
            if (isset($proxyDtoArray['campus_id']) && $proxyDtoArray['campus_id'] != -1) {
                $mockOrganizationRepository->method('find')->willReturn($mockOrganization);
            } else {
                $mockOrganizationRepository->method('find')->willThrowException(new ValidationException([], $expectedResult));
            }

            // Mock OrganizationRoleRepository
            $mockOrganizationRoleRepository = $this->getMock('OrganizationRoleRepository', ['findOneBy']);
            $mockOrganizationRole = $this->getMock('OrganizationRole', ['getId']);
            if ($isCoordinator) {
                $mockOrganizationRoleRepository->method('findOneBy')->willReturn($mockOrganizationRole);
            } else {
                $mockOrganizationRoleRepository->method('findOneBy')->willReturn(false);
            }

            // Mock PersonRepository
            $mockPersonRepository = $this->getMock('PersonRepository', ['findOneBy', 'find']);
            $mockPerson = $this->getMock('Synapse\CoreBundle\Entity\Person', ['getId']);

            if ($isEbiUser) {
                $mockPersonRepository->method('findOneBy')->willReturn($mockPerson);
            } else {
                $mockPersonRepository->method('findOneBy')->willReturn(false);
            }

            if (isset($proxyDtoArray['user_id']) && $proxyDtoArray['user_id'] != -1) {
                $mockPersonRepository->method('find')->willReturn($mockPerson);
            } else {
                $mockPersonRepository->method('find')->willThrowException(new ValidationException([], $expectedResult));
            }

            // Mock validator
            $mockValidator = $this->getMock('validator', ['validate']);
            $mockValidator->method('validate')->willReturn([]);

            // Mock ProxyLogRepository
            $mockProxyLog = $this->getMock('ProxyLog', ['getId']);
            $mockProxyLog->method('getId')->willReturn(4);
            $mockProxyLogRepository = $this->getMock('ProxyLogRepository', ['create', 'flush']);


            $mockContainer->method('get')->willReturnMap([
                [SynapseConstant::VALIDATOR,$mockValidator],
            ]);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                [OrganizationRoleRepository::REPOSITORY_KEY, $mockOrganizationRoleRepository],
                [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                [ProxyLogRepository::REPOSITORY_KEY, $mockProxyLogRepository],
            ]);
            try {
                $proxyService = new ProxyService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $proxyService->createProxy($this->getProxyDetailsDto($proxyDtoArray));
                $this->assertEquals($results, $expectedResult);
            } catch (ValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }
        }, [
            'examples' => [
                // Test01 - Passing invalid campus_id will throw an exception
                [
                    [
                        'campus_id' => -1,
                        'user_id' => 2,
                        'proxy_user_id' => 3,
                    ],
                    '',
                    '',
                    'Organization ID Not Found'
                ],
                // Test02 - If the person is not a coordinator and not an ebi user will throw an exception
                [
                    [
                        'campus_id' => 1,
                        'user_id' => 2,
                        'proxy_user_id' => 3,
                    ],
                    false,
                    false,
                    'You do not have EBI Admin/Coordinator Access'
                ],
                // Test03 - Invalid user id will throw an exception
                [
                    [
                        'campus_id' => 1,
                        'user_id' => -1,
                        'proxy_user_id' => 3,
                    ],
                    true,
                    true,
                    'Person not found'
                ],
                // Test04 - Passing all valid values will create proxy
                [
                    [
                        'campus_id' => 1,
                        'user_id' => 2,
                        'proxy_user_id' => 3,
                    ],
                    true,
                    true,
                    $this->getProxyDetailsDto(['campus_id' => 1, 'user_id' => 2, 'proxy_user_id' => 3])
                ],
                // Test05 - If all values are null|empty will throw an exception
                [
                    [],
                    null,
                    null,
                    'Organization ID Not Found'
                ],
            ]
        ]);
    }

    private function getProxyDetailsDto($proxyDtoArray)
    {
        $proxyDto = new ProxyDto();
        if (!empty($proxyDtoArray)) {
            $proxyDto->setCampusId($proxyDtoArray['campus_id']);
            $proxyDto->setUserId($proxyDtoArray['user_id']);
            $proxyDto->setProxyUserId($proxyDtoArray['proxy_user_id']);
        }
        return $proxyDto;
    }

    public function testDeleteProxy()
    {
        $this->specify("Test deleteProxy", function ($userId, $proxyUserId, $isCoordinator, $isEbiUser, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error']);
            $mockContainer = $this->getMock('Container', ['get', 'getParameter']);

            // Mock OrganizationRoleRepository
            $mockOrganizationRoleRepository = $this->getMock('OrganizationRoleRepository', ['findOneBy']);
            $mockOrganizationRole = $this->getMock('OrganizationRole', ['getId']);
            if ($isCoordinator) {
                $mockOrganizationRoleRepository->method('findOneBy')->willReturn($mockOrganizationRole);
            } else {
                $mockOrganizationRoleRepository->method('findOneBy')->willReturn(false);
            }

            // Mock PersonRepository
            $mockPersonRepository = $this->getMock('PersonRepository', ['findOneBy', 'find']);
            $mockPerson = $this->getMock('Synapse\CoreBundle\Entity\Person', ['getId']);

            if ($isEbiUser) {
                $mockPersonRepository->method('findOneBy')->willReturn($mockPerson);
            } else {
                $mockPersonRepository->method('findOneBy')->willReturn(false);
            }

            if ($userId != -1) {
                $mockPersonRepository->method('find')->willReturn($mockPerson);
            } else {
                $mockPersonRepository->method('find')->willThrowException(new ValidationException([], $expectedResult));
            }

            if ($proxyUserId != -1) {
                $mockPersonRepository->method('find')->willReturn($mockPerson);
            } else {
                $mockPersonRepository->method('find')->willThrowException(new ValidationException([], $expectedResult));
            }

            // Mock ProxyLogRepository
            $mockProxyLog = $this->getMock('ProxyLog', ['getId']);
            $mockProxyLog->method('getId')->willReturn(1);
            $mockProxyLogRepository = $this->getMock('ProxyLogRepository', ['findOneBy', 'remove', 'flush']);
            $mockProxyLogRepository->method('findOneBy')->willReturn($mockProxyLog);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                [OrganizationRoleRepository::REPOSITORY_KEY, $mockOrganizationRoleRepository],
                [ProxyLogRepository::REPOSITORY_KEY, $mockProxyLogRepository],
            ]);
            try {
                $proxyService = new ProxyService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $proxyService->deleteProxy($userId, $proxyUserId);
                $this->assertEquals($results, $expectedResult);
            } catch (ValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }
        }, [
            'examples' => [
                // Test01 - Passing all valid values delete proxy and return null
                [
                    1,
                    2,
                    true,
                    true,
                    null
                ],
                // Test02 - Passing invalid user id will throw an exception
                [
                    -1,
                    2,
                    true,
                    true,
                    'Person not found'
                ],
                // Test03 - Passing invalid proxy user id will throw an exception
                [
                    1,
                    -1,
                    true,
                    true,
                    'Person not found'
                ],
                // Test04 - If the person is not a coordinator and not an ebi user will throw an exception
                [
                    1,
                    1,
                    false,
                    false,
                    'You do not have EBI Admin/Coordinator Access'
                    ],
                // Test05 - If all values are null will throw an exception
                [
                    null,
                    null,
                    null,
                    null,
                    'You do not have EBI Admin/Coordinator Access'
                ]
            ]
        ]);
    }
}