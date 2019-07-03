<?php
use Codeception\Util\Stub;
use Synapse\RiskBundle\EntityDto\RiskGroupDto;

class RiskModelListServiceTest extends \Codeception\TestCase\Test
{

    private $riskModelService;

    public function _before()
    {
        $container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->riskModelService = $container->get('riskmodellist_service');
    }

    public function testGetModelList()
    {
        $response = $this->riskModelService->getModelList('Active');
        $this->assertArrayHasKey('total_count', $response);
        $this->assertArrayHasKey('total_archived_count', $response);
    }

    public function testGetModel()
    {
        $response = $this->riskModelService->getModel(2);
        
        $this->assertAttributeEquals('RiskModel_TestCase_A', 'riskModelName', $response);
        $this->assertAttributeEquals(2, 'id', $response);
    }
    
    public function testGetModelAssignments(){
        $response = $this->riskModelService->getModelAssignments('all');
        $this->assertArrayHasKey('total_assigned_models_count', $response);
        $this->assertArrayHasKey('risk_model_assignments', $response);
    }
}