<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class UpdateGroupStudentDataFile extends ContainerAwareJob
{
    public function __construct()
    {
        $this->queue = 'dumpfiles';
    }

    public function run($args)
    {
        $orgId = $args['orgId'];

        $groupStudentUploadService = $this->getContainer()->get('manage_group_student_upload_service');

        $groupStudentUploadService->createExisting($orgId);
    }


}
