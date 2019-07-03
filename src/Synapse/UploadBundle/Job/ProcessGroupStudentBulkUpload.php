<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\UploadBundle\Command\GroupStudentBulkUploadCommand;
use Synapse\CoreBundle\Util\Constants\GroupUploadConstants;

class ProcessGroupStudentBulkUpload extends ContainerAwareJob
{

    public function run($args)
    {
        $this->getContainer()
            ->get('logger')
            ->info("************************************************* Process Group Student Bulk Upload");
        $command = new GroupStudentBulkUploadCommand();
        $command->setContainer($this->getContainer());
        $input = new ArrayInput(array(
            'file' => 'data://' . GroupUploadConstants::GROUP_DIR . $args['key'],
            'orgId' => $args['organization'],
            'uploadId' => $args['uploadId'],
            'userId' => $args['userId']
        ));
        
        $output = new StreamOutput(fopen('php://stdout', 'w'));
        $resultCode = $command->run($input, $output);
        
        if ($resultCode === 0) {
            $this->getContainer()
                ->get('upload_file_log_service')
                ->updateJobStatus($args['jobNumber'], 'S');
        } else {
            $this->getContainer()
                ->get('upload_file_log_service')
                ->updateJobStatus($args['jobNumber'], 'F');
        }
        
        
    }
}