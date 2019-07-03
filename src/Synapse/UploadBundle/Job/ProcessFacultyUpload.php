<?php

namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\UploadBundle\Command\FacultyUploadCommand;

class ProcessFacultyUpload extends ContainerAwareJob
{
    public function run($args)
    {
        $command = new FacultyUploadCommand();
        $command->setContainer($this->getContainer());
        $input = new ArrayInput(array(
            'file' => 'data://faculty_uploads/'.$args['key'],
            'orgId' => $args['organization'],
            'uploadId' => $args['uploadId']
        ));
        $output = new StreamOutput(fopen('php://stdout', 'w'));
        $resultCode = $command->run($input, $output);

        if ($resultCode === 0) {
            $this->getContainer()->get('upload_file_log_service')->updateJobStatus($args['jobNumber'], 'S');
        } else {
            $this->getContainer()->get('upload_file_log_service')->updateJobStatus($args['jobNumber'], 'F');
        }
    }
}
