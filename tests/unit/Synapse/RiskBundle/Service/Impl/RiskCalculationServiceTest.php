<?php
namespace Synapse\RiskBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\LoggerHelperService;
use Synapse\RiskBundle\EntityDto\RiskCalculationInputDto;

class RiskCalculationServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testCreateRiskCalculationInput()
    {
        $this->specify("Test creating a risk calculation input", function ($riskCalcRequired, $factorCalcRequired, $successMarkerCalcRequired, $talkingPointCalcRequired) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockRiskCalculationRepository = $this->getMock('OrgRiskvalCalcInputsRepository', array('persist', 'flush'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));
            $jmsSerializer = $this->getMock('JMSSerializer', array('serialize'));
            $jmsSerializer->expects($this->once())->method('serialize')->willReturn('');
            $orgService = $this->getMock('OrganizationService', array('find'));
            $personService = $this->getMock('PersonService', array('find'));
            $loggerService = new LoggerHelperService($mockRepositoryResolver, $mockLogger, $mockContainer);


            $mockRepositoryResolver->method('getRepository')->willReturnMap([[RiskCalculationService::ORG_RISK_CAL_INPUTS, $mockRiskCalculationRepository]]);
            $mockContainer->method('get')->willReturnMap([['loggerhelper_service', $loggerService], ['jms_serializer', $jmsSerializer], ['org_service', $orgService], ['person_service', $personService]]);

            $riskCalculationService = new RiskCalculationService($mockRepositoryResolver, $mockLogger, $mockContainer);

            // This is what we are testing
            $returnedRiskCalculationInputDto = $riskCalculationService->createRiskCalculationInput($this->getMockRiskCalculationInputDto($riskCalcRequired, $factorCalcRequired, $successMarkerCalcRequired, $talkingPointCalcRequired));

            // These are the assertions
            $this->assertEquals(1, $returnedRiskCalculationInputDto->getId());

        }, ['examples' => [
            ["y", "y", "y", "y"],
            ["y", "y", "y", "n"],
            ["y", "y", "n", "y"],
            ["y", "n", "y", "y"],
            ["y", "n", "n", "y"],
            ["y", "y", "n", "n"],
            ["y", "n", "y", "n"],
            ["y", "n", "n", "n"],
            ["n", "n", "n", "n"],
            ["n", "n", "n", "y"],
            ["n", "n", "y", "n"],
            ["n", "y", "n", "y"],
            ["n", "y", "y", "n"],
            ["n", "n", "y", "y"],
            ["n", "y", "n", "y"],
            ["n", "y", "y", "y"]
        ]]);
        $this->specify("Test creating a risk calculation input with some bad DTO values", function ($riskCalcRequired, $factorCalcRequired, $successMarkerCalcRequired, $talkingPointCalcRequired) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockRiskCalculationRepository = $this->getMock('OrgRiskvalCalcInputsRepository', array('persist', 'flush'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));
            $jmsSerializer = $this->getMock('JMSSerializer', array('serialize'));
            $jmsSerializer->expects($this->once())->method('serialize')->willReturn('');
            $loggerService = new LoggerHelperService($mockRepositoryResolver, $mockLogger, $mockContainer);


            $mockRepositoryResolver->expects($this->once())->method('getRepository')->with($this->equalTo(RiskCalculationService::ORG_RISK_CAL_INPUTS))->willReturn($mockRiskCalculationRepository);
            $mockContainer->method('get')->willReturnMap([['loggerhelper_service', $loggerService], ['jms_serializer', $jmsSerializer]]);

            $this->setExpectedException('Synapse\RestBundle\Exception\ValidationException', 'Risk value calculation type not vaild.');
            $riskCalculationService = new RiskCalculationService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $riskCalculationService->createRiskCalculationInput($this->getMockRiskCalculationInputDto($riskCalcRequired, $factorCalcRequired, $successMarkerCalcRequired, $talkingPointCalcRequired));
        }, ['examples' => [["maybe so", "maybe so", "maybe so", "maybe so"]]]);
    }

    private function getMockRiskCalculationInputDto($isRiskCalcRequired, $isFactorCalcRequired, $isSuccessMarkerCalcRequired, $isTalkingPointCalcRequired)
    {
        $riskCalculationInputDto = new RiskCalculationInputDto();
        $riskCalculationInputDto->setId("1");
        $riskCalculationInputDto->setOrganizationId("1");
        $riskCalculationInputDto->setPersonId("1");
        $riskCalculationInputDto->setIsFactorCalcReqd($isFactorCalcRequired);
        $riskCalculationInputDto->setIsRiskvalCalcRequired($isRiskCalcRequired);
        $riskCalculationInputDto->setIsSuccessMarkerCalcReqd($isSuccessMarkerCalcRequired);
        $riskCalculationInputDto->setIsTalkingPointCalcReqd($isTalkingPointCalcRequired);
        return $riskCalculationInputDto;
    }

}