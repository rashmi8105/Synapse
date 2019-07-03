<?php
namespace Synapse\RiskBundle\Service\Impl;

use Synapse\CampusConnectionBundle\Service\Impl\CampusConnectionService;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;

class CampusConnectionServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testValidatePrimaryCampusConnectionId()
    {
        $this->specify("Test validate primary campus connection id", function ($primaryCampusConnectionId, $organizationId, $personId, $errorType, $expectedResult) {

            // Initializing repository to be mocked
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

            // Mocking Repositories
            $mockPersonRepository = $this->getMock('PersonRepository', ['findOneBy']);

            // Mocking Repositories
            $mockRbacManager = $this->getMock('Manager', array('checkAccessToStudent'));

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    PersonRepository::REPOSITORY_KEY,
                    $mockPersonRepository
                ]
            ]);

            $mockContainer->method('get')->willReturnMap([
                [
                    Manager::SERVICE_KEY,
                    $mockRbacManager
                ],
            ]);

            $mockPerson = $this->getMock('Person', ['getId']);
            if (empty($primaryCampusConnectionId)) {
                $mockPersonRepository->method('findOneBy')->willReturn(null);
            } else {
                $mockPersonRepository->method('findOneBy')->willReturn($mockPerson);
            }

            if ($errorType == 'no_access') {
                $mockRbacManager->method('checkAccessToStudent')->willReturn(false);
            } else {
                $mockRbacManager->method('checkAccessToStudent')->willReturn(true);
            }

            $organization = $this->getOrganizationInstance($organizationId);

            $campusConnectionService = new CampusConnectionService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $campusConnectionService->validatePrimaryCampusConnectionId($primaryCampusConnectionId, $organization, $personId);

            // These are the assertions
            $this->assertEquals($result, $expectedResult);

        }, [
            'examples' => [
                // Test1: If Primary Campus Connection Id is null or invalid, Returns error message
                [null, 1, 1, '', 'Invalid Primary Campus Connection Id'],

                // Test2: If person student has no access, Returns error message
                [1, 1, 1234, 'no_access', 'Faculty does not have access to this student: 1234'],

                // Test3: If Primary Campus Connection Id is valid, Returns blank error message
                [1, 1, 1234, '', true],
            ]
        ]
        );
    }

    private function getOrganizationInstance($campusId = 1)
    {
        $organization = new Organization();
        $organization->setExternalId('ABC123');
        $organization->setCampusId($campusId);
        return $organization;
    }

}