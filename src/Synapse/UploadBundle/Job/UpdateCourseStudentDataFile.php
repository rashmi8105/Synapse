<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class UpdateCourseStudentDataFile extends ContainerAwareJob
{
    public function __construct()
    {
        $this->queue = 'dumpfiles';
    }

    public function run($args)
    {
        $orgId = $args['orgId'];

        $courseStudentUploadService = $this->getContainer()->get('course_student_upload_service');

        $courseStudentUploadService->generateDumpCSV($orgId);
    }

}