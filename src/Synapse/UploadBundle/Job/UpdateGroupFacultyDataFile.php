<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class UpdateGroupFacultyDataFile extends ContainerAwareJob
{
    public function __construct()
    {
        $this->queue = 'dumpfiles';
    }

    public function run($args)
    {
        $orgId = $args['orgId'];

        $groupFacultyUploadService = $this->getContainer()->get('groupfacultybulk_upload_service');

        $groupFacultyUploadService->createExisting($orgId);
    }



}

