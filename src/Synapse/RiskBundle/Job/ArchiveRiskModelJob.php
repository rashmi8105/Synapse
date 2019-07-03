<?php
namespace Synapse\RiskBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class ArchiveRiskModelJob extends ContainerAwareJob
{

    public function run($args)
    {
        $riskModelService = $this->getContainer()->get('riskmodellist_service');
        $currentDate = new \DateTime('now');
        
        $logger = $this->getContainer()->get('logger');        
        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> ArchiveRiskModel-Job-Started");      
        $r = $riskModelService->ArchiveRiskModel($currentDate);
        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> ArchiveRiskModel-Job-Ended");
    }
}