<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\UploadBundle\Command\SurveyUploadCommand;

class ProcessSurveyUpload extends ContainerAwareJob
{

    public function run($args)
    {
        $command = new SurveyUploadCommand();
        $command->setContainer($this->getContainer());
        $input = new ArrayInput(array(
            'file' => 'data://survey_uploads/' . $args['key'],
            'type' => $args['type'],
            'uploadId' => $args['uploadId']            
        ));
        $output = new StreamOutput(fopen('php://stdout', 'w'));
        $resultCode = $command->run($input, $output);
        if ($resultCode === 0) {
            $this->getContainer()
                ->get('upload_file_log_service')
                ->updateJobStatus($args['jobNumber'], 'S');
            $this->getContainer()
            ->get('talkingpoint_service')
            ->cacheTalkingPoints();
        } else {
            $this->getContainer()
                ->get('upload_file_log_service')
                ->updateJobStatus($args['jobNumber'], 'F');
        }
    }
}