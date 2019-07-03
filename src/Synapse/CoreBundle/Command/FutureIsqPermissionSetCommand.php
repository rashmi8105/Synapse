<?php
namespace Synapse\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use JMS\DiExtraBundle\Annotation as DI;

class FutureIsqPermissionSetCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('futureisqpermissionset:isq')
            ->setDescription('Populate newly created ISQs to permissionset that are set with future flag true')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("\nProcess to copy newly created ISQs to existing permissionset that are set with future flag true - Starting now...");
 
        $this->getContainer()->get('orgpermissionset_service')->FutureIsqPermissionSet();		

        $output->writeln("\nProcess to copy newly created ISQs to existing permissionset that are set with future flag true - Completed.");

    }

}