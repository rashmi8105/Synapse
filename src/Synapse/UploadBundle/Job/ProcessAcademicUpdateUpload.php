<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\UploadBundle\Command\AcademicUpdateUploadCommand;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class ProcessAcademicUpdateUpload extends ContainerAwareJob
{

    public function run($args)
    {
        $command = new AcademicUpdateUploadCommand();

        $command->setContainer($this->getContainer());
        $input = new ArrayInput(array(
            'file' => 'data://academic_update_uploads/' . $args['key'],
            UploadConstant::ORGID => $args[UploadConstant::ORGID],
            UploadConstant::USERID => $args[UploadConstant::USERID],
            UploadConstant::UPLOADID => $args[UploadConstant::UPLOADID],
            'serverArray' => $args['serverArray']
        ));
        $output = new StreamOutput(fopen('php://stdout', 'w'));
        $resultCode = $command->run($input, $output);
        if ($resultCode === 0) {
            $this->getContainer()
                ->get(UploadConstant::UPLOAD_FILE_LOG_SERVICE)
                ->updateJobStatus($args[UploadConstant::JOB_NUM], 'S');
            $statusOfUpload = 'S';
        } else {
            $this->getContainer()
                ->get(UploadConstant::UPLOAD_FILE_LOG_SERVICE)
                ->updateJobStatus($args[UploadConstant::JOB_NUM], 'F');
            $statusOfUpload = 'F';
        }

        $upload = array(
            "id" => $args[UploadConstant::UPLOADID],
            UploadConstant::STATUS => $statusOfUpload
        );

        $this->getContainer()
            ->get('upload_file_log_service')
            ->sendEmail($args['userId'], $args[UploadConstant::ORGID], 'AcademicUpdate_Upload_Notification', $upload);

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