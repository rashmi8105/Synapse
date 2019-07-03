<?php
use Codeception\Util\Stub;
use Synapse\RiskBundle\EntityDto\RiskModelDto;
use Synapse\RiskBundle\EntityDto\RiskIndicatorsDto;

class RiskModelCreateServiceTest extends \Codeception\TestCase\Test
{

    private $riskModelCreateService;

    public function _before()
    {
        $container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->riskModelCreateService = $container->get('riskmodelcreate_service');
    }

  
    public function testCreateModel()
    {
        $model = $this->createRiskModelDto();
        $modelResponse = $this->riskModelCreateService->createModel($model);
        $this->assertGreaterThan(0, $modelResponse->getId());
    }
    

    public function createRiskModelDto()
    {
        $riskArray = [];
        $riskIndicator = new RiskIndicatorsDto();
        $riskIndicator->setName('red2');
        $riskIndicator->setMin(1.0);
        $riskIndicator->setMax(1.5);
        $riskArray[] = $riskIndicator;
        
        $riskIndicator = new RiskIndicatorsDto();
        $riskIndicator->setName('red');
        $riskIndicator->setMin(1.6);
        $riskIndicator->setMax(2.0);
        $riskArray[] = $riskIndicator;
        
        $riskIndicator = new RiskIndicatorsDto();
        $riskIndicator->setName('yellow');
        $riskIndicator->setMin(2.1);
        $riskIndicator->setMax(3.0);
        $riskArray[] = $riskIndicator;
        
        $riskIndicator = new RiskIndicatorsDto();
        $riskIndicator->setName('green');
        $riskIndicator->setMin(3.1);
        $riskIndicator->setMax(4.0);
        $riskArray[] = $riskIndicator;
        
        $model = new RiskModelDto();
        $model->setRiskModelName("RiskModel_" . uniqid());
        $model->setRiskIndicators($riskArray);
        $model->setModelState('Unassigned');
        
        $startDate = new \DateTime();
        $startDate->add(new DateInterval('P2D'));
        
        $endDate = clone $startDate;
        $endDate->add(new DateInterval('P7D'));
        $enrollDate = clone $startDate;
        $enrollDate->add(new DateInterval('P6D'));
        
        $model->setEnrollmentEndDate($enrollDate);
        $model->setCalculationStopDate($endDate);
        
        $model->setCalculationStartDate($startDate);
        
        return $model;
    }
}