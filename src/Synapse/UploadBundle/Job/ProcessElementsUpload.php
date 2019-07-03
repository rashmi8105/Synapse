<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\UploadBundle\Command\ElementsUploadCommand;

class ProcessElementsUpload extends ContainerAwareJob
{

    public function run($args)
    {
        $command = new ElementsUploadCommand();
        $command->setContainer($this->getContainer());
        $this->getContainer()
            ->get('logger')
            ->info(">>>>>>>>>>I am in ProcessReportElements upload");
        
        $input = new ArrayInput(array(
            'file' => 'data://reports_master/' . $args['key'],
            
            'userId' => $args['userId'],
            'uploadId' => $args['uploadId']
        ));
        
        $this->getContainer()
        ->get('logger')
        ->info(">>>>>>>>>>I am in ProcessReportElementsUpload-2");
        
        $output = new StreamOutput(fopen('php://stdout', 'w'));
        $resultCode = $command->run($input, $output);
        if ($resultCode === 0) {
            $this->getContainer()
                ->get('upload_file_log_service')
                ->updateJobStatus($args['jobNumber'], 'S');
            $statusOfUpload = 'S';
        } else {
            $this->getContainer()
                ->get('upload_file_log_service')
                ->updateJobStatus($args['jobNumber'], 'F');
            $statusOfUpload = 'F';
        }
        
       
    }
}