<?php
namespace Synapse\RiskBundle\Service\Impl;

use Synapse\RiskBundle\Repository\OrgRiskGroupModelRepository;
use Synapse\RiskBundle\Repository\RiskGroupRepository;

class RiskGroupServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testValidateRiskGroupBelongsToOrganization()
    {
        $this->specify("Test validate risk group belongs to organization", function ($organizationId, $riskGroupId, $expectedResult) {

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
            $mockRiskGroupRepository = $this->getMock('RiskGroupRepository', ['find']);
            $mockOrgRiskGroupModelRepository = $this->getMock('OrgRiskGroupModelRepository', ['findOneBy']);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    RiskGroupRepository::REPOSITORY_KEY,
                    $mockRiskGroupRepository
                ],
                [
                    OrgRiskGroupModelRepository::REPOSITORY_KEY,
                    $mockOrgRiskGroupModelRepository
                ],
            ]);

            $mockRiskGroup = $this->getMock('RiskGroup', ['getId']);
            $mockOrgRiskGroupModel = $this->getMock('OrgRiskGroupModel', ['getId']);
            if (empty($riskGroupId)) {
                $mockRiskGroupRepository->method('find')->willReturn(null);
            } else {
                $mockRiskGroupRepository->method('find')->willReturn($mockRiskGroup);
            }

            if (empty($organizationId)) {
                $mockOrgRiskGroupModelRepository->method('findOneBy')->willReturn(null);
            } else {
                $mockOrgRiskGroupModelRepository->method('findOneBy')->willReturn($mockOrgRiskGroupModel);
            }

            $riskGroupService = new RiskGroupService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $riskGroupService->validateRiskGroupBelongsToOrganization($organizationId, $riskGroupId);

            // These are the assertions
            $this->assertEquals($result, $expectedResult);

        }, [
            'examples' => [
                // Test1: If $riskGroupId is null or invalid, Returns error message
                [1, null, "Risk Group does not exist."],

                // Test2: If $organizationId is null or $riskGroupId is not mapped with organization, Returns error message
                [0, 1234, "Risk Group is not mapped to any organization."],

                // Test3: If $organizationId and $riskGroupId are valid, Returns blank error message
                [1, 1, true],
            ]
        ]
        );
    }


}