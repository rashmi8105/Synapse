<?php
/**
 * Created by PhpStorm.
 * User: imeyers
 * Date: 12/7/15
 * Time: 3:05 PM
 */

namespace Synapse\UploadBundle\Job;


use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\UploadBundle\Command\ManageGroupStudentBulkUploadCommand;
use Synapse\CoreBundle\Util\Constants\GroupUploadConstants;


class ProcessManageGroupStudentBulkUpload extends ContainerAwareJob
{
    // What should be returned if the upload is successful
    const Successful_Upload_Flag = 0;

    /**
     * @param $args
     * @throws \Exception
     */
    public function run($args)
    {
        // create a new command object
        $command = new ManageGroupStudentBulkUploadCommand();

        // set the command with the container
        $container = $this->getContainer();
        $command->setContainer($container);


        // file name org Id uploadedId userId
        $input = new ArrayInput(array(
            'file' => 'data://' . GroupUploadConstants::GROUP_DIR . $args['key'],
            'orgId' => $args['organization'],
            'uploadId' => $args['uploadId'],
        ));

        // open the file and set it to the stream output
        $opened_file = fopen('php://stdout', 'w');
        $output = new StreamOutput($opened_file);

        // run the run command from the ManageGroupStudentBulkUploadCommand();
        $resultCode = $command->run($input, $output);

        // If the upload was successful
        if ($resultCode === self::Successful_Upload_Flag) {

            // set the job as a successful upload
            $this->getContainer()
                ->get('upload_file_log_service')
                ->updateJobStatus($args['jobNumber'], 'S');

        } else {

            // set the job as a failed upload
            $this->getContainer()
                ->get('upload_file_log_service')
                ->updateJobStatus($args['jobNumber'], 'F');
        }

    }
}
