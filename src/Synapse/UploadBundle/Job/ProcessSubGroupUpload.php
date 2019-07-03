<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\UploadBundle\Command\SubGroupUploadCommand;
use Synapse\CoreBundle\Util\Constants\GroupUploadConstants;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class ProcessSubGroupUpload extends ContainerAwareJob
{

    public function run($args)
    {
        $this->getContainer()
            ->get('logger')
            ->info("************************************************* ProcessSubGroupUpload");
        $command = new SubGroupUploadCommand();
        $command->setContainer($this->getContainer());
        $input = new ArrayInput(array(
            'file' => 'data://' . GroupUploadConstants::GROUP_DIR . $args['key'],
            'orgId' => $args['organization'],
            UploadConstant::UPLOADID => $args[UploadConstant::UPLOADID],
            'userId' => $args['userId']
        ));
        
        $output = new StreamOutput(fopen('php://stdout', 'w'));
        $resultCode = $command->run($input, $output);
        $this->getContainer()
        ->get('logger')
        ->info("************************************************* ProcessSubGroupUpload - RESULT CODE".$resultCode);
        if ($resultCode === 0) {
            $this->getContainer()
                ->get(UploadConstant::UPLOAD_FILE_LOG_SERVICE)
                ->updateJobStatus($args['jobNumber'], 'S');
        } else {
            $this->getContainer()
                ->get(UploadConstant::UPLOAD_FILE_LOG_SERVICE)
                ->updateJobStatus($args['jobNumber'], 'F');
        }
    }
}