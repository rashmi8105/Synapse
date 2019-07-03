<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class UpdateCourseFacultyDataFile extends ContainerAwareJob
{
    public function __construct()
    {
        $this->queue = 'dumpfiles';
    }

    public function run($args)
    {
        $orgId = $args['orgId'];

        $courseFacultyUploadService = $this->getContainer()->get('course_faculty_upload_service');

        $courseFacultyUploadService->generateDumpCSV($orgId);
    }

}