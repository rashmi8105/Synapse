<?php

namespace Synapse\UploadBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use JMS\DiExtraBundle\Annotation as DI;

use Synapse\CoreBundle\Util\CSVFile;
use Synapse\CoreBundle\Service\PersonServiceInterface;
use Synapse\CoreBundle\Entity\Person;

class UploadHistoryUpdateCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('uploadHistoryUpdate:command')
            ->setDescription('Updating upload_file_log table to set person id for existing record')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $personService = $this->getContainer()->get('person_service');
        $uploadFileLogRepository = $repositoryResolver->getRepository('SynapseUploadBundle:UploadFileLog');
        $organizations = $uploadFileLogRepository->getUniqueOrganization();
        $uploadHistoryList = '';
        $orgLangId = '';
        $primaryCoordinator = '';
        $primaryCoordinatorId = '';
        if(!empty($organizations)){
           foreach ($organizations as $organization) {
               $orgLangId = 1;
               /*
                * Since it is existing organization, we are trying to fix for existing data so hard coded $orgLangId = 1;
               * This is not required to run in the future
               */
               $primaryCoordinators = $personService->getAllPrimaryCoordinators($organization['organizationId'], $orgLangId, 'Primary coordinator');
               if ($primaryCoordinators) {
                   	$primaryCoordinator = $primaryCoordinators[0];
                   	$primaryCoordinatorId = $primaryCoordinator->getPerson()->getId();
               }
               $uploadHistoryList = $uploadFileLogRepository->findByOrganizationId($organization['organizationId']);
               foreach ($uploadHistoryList as $list) {
                     if(!empty($list->getPersonId())){
                         continue;
                     }
                     else{
                         $list->setPersonId($primaryCoordinatorId);
                     }
               }
           }
           $uploadFileLogRepository->flush();
       }
    } 
}