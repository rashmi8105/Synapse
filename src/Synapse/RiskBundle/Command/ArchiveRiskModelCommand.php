<?php

namespace Synapse\RiskBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Synapse\AcademicBundle\Service\RiskModelListServiceInterface;
/*
 * Time being not in use handling through job
 */
class ArchiveRiskModelCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('ArchiveRiskModel:process')
            ->setDescription('Archive Risk Model');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $riskModelService = $this->getContainer()->get('riskmodellist_service');
        $currentDate = new \DateTime('now');
        echo "********Job Started******** \n";        
        $r = $riskModelService->ArchiveRiskModel($currentDate);
        echo "********Job Ended******** \n";
    }
}