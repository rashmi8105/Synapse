<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class CreateRiskVariableListJob extends ContainerAwareJob
{
    public function run($args)
    {
        $logger = $this->getContainer()->get('logger');
        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>>>> Update Risk Varaible List");
        $riskVariableUploadService = $this->getContainer()->get('riskvariable_upload_service');
        $riskVariableUploadService->generateDumpCSV();
    }
}