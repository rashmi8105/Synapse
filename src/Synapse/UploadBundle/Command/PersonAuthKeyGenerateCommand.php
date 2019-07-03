<?php

namespace Synapse\UploadBundle\Command;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\PersonServiceInterface;

class PersonAuthKeyGenerateCommand extends ContainerAwareCommand
{

    /**
     * Because this is a container aware command, sets this up in the container with
     * the name personAuthKey:generate
     */
    protected function configure()
    {
        $this
            ->setName('personAuthKey:generate')
            ->setDescription('generates auth keys for all users')
        ;
    }

    /**
     * NOTE: Intended for single use when generating auth keys after merging the new
     * pattern for this code. This generates auth keys for all users in all organizations.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $organizationService = $this->getContainer()->get(OrganizationService::SERVICE_KEY);
        $organizationService->generateAuthKeysForAllUsersInOrganization();

        // Parent execute expects return of null or 0 if everything went OK.
        return null;
    }

}