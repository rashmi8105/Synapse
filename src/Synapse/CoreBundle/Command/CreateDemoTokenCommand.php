<?php

namespace Synapse\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDemoTokenCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('oauth-server:token:create')
            ->setDescription('Creates a new token')
            ->setHelp(
                <<<EOT
                    The <info>%command.name%</info>command creates a new token.


EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setRedirectUris(array('http://localhost:9000/'));
        $client->setAllowedGrantTypes(array(
            'authorization_code', 'password', 'refresh_token', 'token', 'client_credentials'
        ));
        $clientManager->updateClient($client);
        $url = 'http://127.0.0.1/oauth/v2/token?client_id='.$client->getPublicId().'&client_secret='.$client->getSecret().'&grant_type=password&username=user&password=userpass';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
        ));
        $result = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($result);

        $output->writeln(
            sprintf(
                'New access token created <info>%s</info>',
                $data->access_token
            )
        );
    }
}