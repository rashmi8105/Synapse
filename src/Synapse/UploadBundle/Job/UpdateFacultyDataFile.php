<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class UpdateFacultyDataFile extends ContainerAwareJob
{
    public function __construct()
    {
        $this->queue = 'dumpfiles';
    }

    public function run($args)
    {
        $orgId = $args['orgId'];
        $synapseUploadService = $this->getContainer()->get('synapse_upload_service');
        $synapseUploadService->generateDumpCSV($orgId, "Faculty");
    }

}
