<?php
namespace Synapse\RiskBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;

class RiskCalculationJob extends ContainerAwareJob
{

    public function run($args)
    {
        $riskCalcService = $this->getContainer()->get('riskcalculation_service');
        
        $logger = $this->getContainer()->get('logger');
        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> Risk Calculation-Job-Started");
        $riskCalcService->invokeRiskCalculation();
        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> Risk Calculation-Job-Ended");
    }
}