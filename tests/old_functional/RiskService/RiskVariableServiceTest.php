<?php
use Codeception\Util\Stub;
use Synapse\RiskBundle\EntityDto\RiskVariableDto;
use Synapse\RiskBundle\EntityDto\SourceIdDto;
use Synapse\RiskBundle\EntityDto\CalculatedDataDto;
use Synapse\RiskBundle\EntityDto\BucketDetailsDto;
use Synapse\RiskBundle\EntityDto\RiskVariablesResponseDto;
use Synapse\RiskBundle\EntityDto\RiskVariableResponseDto;
use Synapse\RiskBundle\EntityDto\RiskVariablesListDto;
use Synapse\RiskBundle\EntityDto\BucketDetailsListDto;
class RiskVariableServiceTest extends \Codeception\TestCase\Test
{

    private $riskModelCreateService;
    
    private $variableId = 1;

    public function _before()
    {
        $container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->riskVariableService = $container->get('riskvariable_service');
    }

  
    public function testCreateVariable()
    {
        $variable = $this->createVariableDto('insert', 0, 0);        
        $variableResponse = $this->riskVariableService->create($variable, 'insert');        
        $this->assertGreaterThan(0, $variableResponse->getId());
    }
    
    public function testGetResourceType()
    {
        $variableResponse = $this->riskVariableService->getResourceTypes();        
        $this->assertArrayHasKey('source_type', $variableResponse);
        $this->assertEquals('profile, surveyquestion, surveyfactor, ISP, ISQ, questionbank', $variableResponse['source_type']);
    }
    
    public function testGetRiskVariable()
    {
        $variable = $this->riskVariableService->getRiskVariable($this->variableId);
        $this->assertAttributeEquals($this->variableId, 'id', $variable);
        $this->assertAttributeEquals('C_HSGradYear', 'riskVariableName', $variable);
        $this->assertAttributeEquals('profile', 'sourceType', $variable);
    }
    
    public function testGetRiskVariables()
    {
        $variables = $this->riskVariableService->getRiskVariables('active');        
        $this->assertAttributeEquals(1, 'id', $variables->getRiskVariables()[0]);
        $this->assertAttributeEquals('C_HSGradYear', 'riskVariableName', $variables->getRiskVariables()[0]);
        $this->assertAttributeEquals('profile', 'sourceType', $variables->getRiskVariables()[0]);
    }
    
    public function testDeleteRiskVariable()
    {
        $variables = $this->riskVariableService->changeStatus($this->variableId);        
        $this->assertEquals(null, $variables);        
    }
    public function testGetRiskSourceIds()
    {
        $sourceType = array('profile','questionbank','surveyquestion','surveyfactor','ISP');
        foreach ($sourceType as $type) {    
            $variables = $this->riskVariableService->getRiskSourceIds($type); 
            $this->assertArrayHasKey('source_ids', $variables);
            $this->assertEquals(strtoupper($type), strtoupper($variables['source_ids'][0]->getSourceType()));
        }
    }
    
    public function createVariableDto($type = 'insert', $id, $name) {       
        $sourceIdDto = new SourceIdDto();
        $sourceIdDto->setEbiProfileId(1);
        
        $variable = new RiskVariableDto();
        if($type == 'insert'){
            $variable->setRiskVariableName("RiskVariable_" . uniqid());
            $variable->setIsCalculated(true);
        }
        else {
            $variable->setId($id);
            $variable->setRiskVariableName($name);
            $variable->setIsCalculated(false);
        }
        $variable->setSourceType('profile');
        $variable->setSourceId($sourceIdDto);
        $variable->setIsContinuous(true);
        $variable->setIsCalculated(true);
        $bucket = $this->getBucketDetailDto();
        $variable->setBucketDetails($bucket);
        
        $calculatedDataDto = new CalculatedDataDto();
        $startDate = new \DateTime();
        $calculatedDataDto->setCalculationStartDate($startDate);
        $endDate = clone $startDate;
		$endDate->add(new \DateInterval('P1D'));
        $calculatedDataDto->setCalculationStopDate($endDate);
        $calculatedDataDto->setCalculationType('Sum');
        $variable->setCalculatedData($calculatedDataDto);
        
        return $variable;
    }
    
    private function getBucketDetailDto(){
        $continuousBucket = [];
        $j = 1;
        for($i = 1; $i<=7; $i++) {            
            $rvContinuousBucket = new BucketDetailsDto();
            $rvContinuousBucket->setBucketValue($i);
            $rvContinuousBucket->setMin($j = $j + 0.2);
            $rvContinuousBucket->setMax($j = $j + 0.5);            
            $continuousBucket[] = $rvContinuousBucket; 
            $j = $j + 0.1;
        }        
        return $continuousBucket;       
    }

}