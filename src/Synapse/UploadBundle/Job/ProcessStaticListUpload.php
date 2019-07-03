<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\UploadBundle\Command\StaticListUploadCommand;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class ProcessStaticListUpload extends ContainerAwareJob
{

    public function run($args)
    {
        $command = new StaticListUploadCommand();
        
        $command->setContainer($this->getContainer());
        $input = new ArrayInput(array(
            'file' => 'data://staticlist_uploads/' . $args['key'],
            UploadConstant::ORGID => $args[UploadConstant::ORGID],
            UploadConstant::USERID => $args[UploadConstant::USERID],
            'staticListId' => $args['staticListId'],
            UploadConstant::UPLOADID => $args[UploadConstant::UPLOADID]
        ));
        
        $output = new StreamOutput(fopen('php://stdout', 'w'));
        $resultCode = $command->run($input, $output);
        if ($resultCode === 0) {
            $this->getContainer()
                ->get(UploadConstant::UPLOAD_FILE_LOG_SERVICE)
                ->updateJobStatus($args[UploadConstant::JOB_NUM], 'S');
        } else {
            $this->getContainer()
                ->get(UploadConstant::UPLOAD_FILE_LOG_SERVICE)
                ->updateJobStatus($args[UploadConstant::JOB_NUM], 'F');
        }

        $transport = $this->getContainer()
            ->get('mailer')
            ->getTransport();
        if (! $transport instanceof \Swift_Transport_SpoolTransport) {
            return;
        }
        
        $spool = $transport->getSpool();
        if (! $spool instanceof \Swift_MemorySpool) {
            return;
        }
        
        $spool->flushQueue($this->getContainer()
            ->get('swiftmailer.transport.real'));
    }
}