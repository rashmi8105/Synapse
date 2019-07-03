<?php
use Codeception\Util\Stub;
use Synapse\RiskBundle\EntityDto\RiskGroupDto;

class RiskGroupServiceTest extends \Codeception\TestCase\Test
{

    private $riskGroupService;

    public function _before()
    {
        $container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->riskGroupService = $container->get('riskgroup_service');
    }

    public function testCreateRiskGroup()
    {
        $groupDto = $this->getRiskGroupDto();
        $groupDto = $this->riskGroupService->createGroup($groupDto);
        $this->assertGreaterThan(0, $groupDto->getId());
    }

    public function testEditRiskGroup()
    {
        $groupDto = $this->getRiskGroupDto();
        $name = "RiskGroup_";
        $groupDto->setGroupName($name);
        $groupDto = $this->riskGroupService->createGroup($groupDto);
        $name = "RiskGroup_1";
        $groupDto->setGroupName($name);
        $groupDto = $this->riskGroupService->editGroup($groupDto);
        $this->assertGreaterThan(0, $groupDto->getId());
    }

    public function testgetRiskGroupById()
    {
        $groupDto = $this->getRiskGroupDto();
        $name = "RiskGroup_";
        $groupDto->setGroupName($name);
        $groupDto = $this->riskGroupService->createGroup($groupDto);
        $id = $groupDto->getId();
        $group = $this->riskGroupService->getRiskGroupById($id);
        $this->assertEquals($id, $group->getId());
        $this->assertEquals($name, $group->getGroupName());
    }
    
    public function testgetRiskGroups()
    {
        $groupDto = $this->getRiskGroupDto();
        $name = "RiskGroup_";
        $groupDto->setGroupName($name);
        $groupDto = $this->riskGroupService->createGroup($groupDto);
        $id = $groupDto->getId();
        $group = $this->riskGroupService->getRiskGroups();
        $this->assertArrayHasKey('total_count', $group);
        $this->assertGreaterThan(0, $group['total_count']);
        $this->assertArrayHasKey('risk_groups', $group);
       
    }


    public function getRiskGroupDto()
    {
        $riskGroup = new RiskGroupDto();
        $riskGroup->setGroupName("RiskGroup_" . uniqid());
        $riskGroup->setGroupDescription(" Group Description");
        return $riskGroup;
    }
}