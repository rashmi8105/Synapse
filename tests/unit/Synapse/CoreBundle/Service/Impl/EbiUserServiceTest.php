<?php
namespace Synapse\CoreBundle\Service\Impl;

use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RoleLangRepository;

class EbiUserServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testIsEbIUser()
    {
        $this->specify("Test isEbIUser function", function ($userId, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error'
            ));

            // Mock PersonRepository
            $mockPersonRepository = $this->getMock('PersonRepository', ['findBy']);
            $mockPerson = $this->getMock('Person', ['getId']);
            if ($userId > 0) {
                $mockPersonRepository->method('findBy')->willReturn($mockPerson);
            } else {
                $mockPersonRepository->method('findBy')->willReturn(null);
            }

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                ]);

            $ebiUserService = new EbiUserService($mockRepositoryResolver, $mockLogger);
            $response = $ebiUserService->isEbIUser($userId);
            $this->assertEquals($expectedResult, $response);
        }, [
            'examples' => [
                [
                    // Passing valid user id will return true
                    1,
                    true
                ],
                [
                    // Passing invalid user id will return false
                    -1,
                    false
                ],
                [
                    // Passing user id as null will return false
                    null,
                    false
                ],
            ]
        ]);
    }

    public function testIsARTUser()
    {
        $this->specify("Test isARTUser function", function ($userId, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error'
            ));

            // Mock PersonRepository
            $mockPersonRepository = $this->getMock('PersonRepository', ['findBy']);
            $mockPerson = $this->getMock('Person', ['getId']);
            if ($userId > 0) {
                $mockPersonRepository->method('findBy')->willReturn($mockPerson);
            } else {
                $mockPersonRepository->method('findBy')->willReturn(null);
            }

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                ]);

            $ebiUserService = new EbiUserService($mockRepositoryResolver, $mockLogger);
            $response = $ebiUserService->isARTUser($userId);
            $this->assertEquals($expectedResult, $response);
        }, [
            'examples' => [
                [
                    // Passing valid user id will return true
                    1,
                    true
                ],
                [
                    // Passing invalid user id will return false
                    -1,
                    false
                ],
                [
                    // Passing user id as null will return false
                    null,
                    false
                ],
            ]
        ]);
    }

    public function testIsSkyfactorUser()
    {
        $this->specify("Test isSkyfactorUser function", function ($userId, $roleName, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error'
            ));

            // Mock PersonRepository
            $mockPersonRepository = $this->getMock('PersonRepository', ['findOneBy']);
            $mockPerson = $this->getMock('Person', ['getId']);
            $mockPerson->method('getId')->willReturn(123);
            if ($userId > 0) {
                $mockPersonRepository->method('findOneBy')->willReturn($mockPerson);
            } else {
                $mockPersonRepository->method('findBy')->willReturn(null);
            }
            // Mock organizationRoleRepository
            $mockOrganizationRoleRepository = $this->getMock('OrganizationRoleRepository',['findOneBy']);
            $mockOrganizationRole = $this->getMock('OrganizationRole',['getRole','getId']);
            $mockRole = $this->getMock('Role',['getId']);
            $mockOrganizationRole->method('getRole')->willReturn($mockRole);
            $mockOrganizationRoleRepository->method('findOneBy')->willReturn($mockOrganizationRole);

            // Mock RoleLangRepository
            $mockRoleLangRepository = $this->getMock('RoleLangRepository',['findOneBy']);
            $mockRoleLang = $this->getMock('RoleLang',['getId','getRoleName']);
            $mockRoleLang->method('getRoleName')->willReturn($roleName);
            $mockRoleLangRepository->method('findOneBy')->willReturn($mockRoleLang);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [OrganizationRoleRepository::REPOSITORY_KEY, $mockOrganizationRoleRepository],
                    [RoleLangRepository::REPOSITORY_KEY, $mockRoleLangRepository],
                ]);

                $ebiUserService = new EbiUserService($mockRepositoryResolver, $mockLogger);
                $response = $ebiUserService->isSkyfactorUser($userId);
                $this->assertEquals($expectedResult, $response);

        }, [
            'examples' => [
                [
                    // Passing valid Skyfactor Admin id will return true
                    1,
                    'Skyfactor Admin',
                    true
                ],
                [
                    // Passing invalid Skyfactor Admin id will return false
                    -1,
                    null,
                    false
                ],
                [
                    // Passing Skyfactor Admin id and role name as null will return false
                    null,
                    null,
                    false
                ],
            ]
        ]);
    }
}