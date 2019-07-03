<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\ReportsBundle\Job\ReportsJob;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\UploadBundle\Util\Constants\UploadConstant;
use Synapse\UploadBundle\Command\OurStudentReportUploadCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class ProcessOurStudentReport extends ReportsJob
{
    public function run($args)
    {
        $logger = $this->getContainer()->get('logger');
        
        $logger->info("************************************************* Process Our Student Report : Start");
        $command = new OurStudentReportUploadCommand();
        $command->setContainer($this->getContainer());
        $input = new ArrayInput(array(
            'file' => 'data://' . UploadConstant::OUR_STUD_REPORT_UPLOAD_DIR . "/" . $args['key'],
            // 'orgId' => $args['organization'],
            UploadConstant::UPLOADID => $args[UploadConstant::UPLOADID]
        // 'userId' => $args['userId']
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
        $logger->info("************************************************* Process Our Student Report : END" . $resultCode);
    }
}