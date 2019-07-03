<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\UploadBundle\Command\CourseStudentUploadCommand;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class ProcessCourseStudentUpload extends ContainerAwareJob
{

    public function run($args)
    {
        $command = new CourseStudentUploadCommand();
        $command->setContainer($this->getContainer());
        $input = new ArrayInput(array(
            'file' => 'data://course_student_uploads/' . $args['key'],
            UploadConstant::ORGID => $args[UploadConstant::ORGN],
            UploadConstant::UPLOADID => $args[UploadConstant::UPLOADID]
        ));

        $output = new StreamOutput(fopen('php://stdout', 'w'));
        $resultCode = $command->run($input, $output);

        $statusOfUpload = '';

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

        // $this->getContainer()
        //     ->get(UploadConstant::UPLOAD_FILE_LOG_SERVICE)
        //     ->sendEmail($args[UploadConstant::USERID], $args['organization'], 'Course_Student_Upload_Notification', $upload);

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
