<?php

/*
 * Author:  Technically it is Ian Who made this
 * Note:    I copied GroupStudentBulkUploadCommand so I can actually create this class
 *          The comments I made are mostly for my understanding
 */

namespace Synapse\UploadBundle\Command;

// include these
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVFile;
use Resque_Job_Status;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/*
 * Class name:  ManageGroupStudentUploadCommand
 * Function:    The first step of the upload process, this will take
 *              the file and prep it for the service function
 */

class ManageGroupStudentBulkUploadCommand extends ContainerAwareCommand
{

    private $uploadFileLogService;

    private $output;

    const LBL_ORGID = 'orgId';

    const LBL_JOBSTATUS = 'jobStatus';

    const LBL_UPLOADEDID = 'uploadId';

    /************************************************************
     * I added these constants to attempt to clean up "Magic    *
     * Numbers". These will default all uploads to the          *
     * first organization, first user and first upload id       *
     * (mostly for testing purposes).                           *
     ************************************************************/
    //TODO if these parameters do not exist then fail the upload, then delete my "magic numbers"

    const DEFAULT_ORGANIZATION_ID = 1;
    const DEFAULT_UPLOAD_ID = 1;
    const DEFAULT_USER_ID = 1;


    // run configure, configure works with symphony to create this object.
    // You can use this to run within the command line to test this object
    protected function configure()
    {
        $this->setName('manageGroupStudentBulkUpload:process')
            ->setDescription('Process a Manage Group Student Upload CSV')
            ->addArgument('file', InputArgument::REQUIRED, 'What file do you want to process?')
            ->addArgument(self::LBL_ORGID, InputArgument::REQUIRED, 'Id to process this upload as?')
            ->addArgument(self::LBL_UPLOADEDID, InputArgument::OPTIONAL, 'Id to process this upload as?');
    }

    /**
     * This function runs immediately after the configure
     * This will take the processed file, given within the configure function
     *
     * @param InputInterface $inputArray
     * @param OutputInterface $output
     * @return array
     */
    protected function execute(InputInterface $inputArray, OutputInterface $output)
    {
        // output file
        $this->output = $output;
        $file = $inputArray->getArgument('file');


        $orgId = $inputArray->getArgument(self::LBL_ORGID);

        if(! is_null($inputArray->getArgument(self::LBL_UPLOADEDID))){
            $uploadId =  $inputArray->getArgument(self::LBL_UPLOADEDID);
        }else{
            $uploadId =  self::DEFAULT_UPLOAD_ID;
        }
        $cacheKey = 'upload.' . $uploadId . '.status';




        // create the errors array
        $errors = [];

        // get the containers, with the container names
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');

        // This will log information for debugging
        // I currently have the debugging information commented out
        // but will leave it to be uncommented if there are errors later
        $logger = $this->getContainer()->get('logger');

        // this will service the file (which calls the actual upload).
        $manageGroupUploadService = $this->getContainer()->get('manage_group_student_upload_service');
        $cache = $this->getContainer()->get('synapse_redis_cache');

        // Load the file into memory
        $fileData = $manageGroupUploadService->load($file, $orgId);

        // first save, the file has been loaded
        $cache->save($cacheKey, [
            self::LBL_JOBSTATUS => 'loaded',
            'data' => $fileData
        ]);

        // process the file
        $jobData = $manageGroupUploadService->process($uploadId);

        // get the total number of jobs
        $jobs = $jobData['jobs'];
        $totalJobs = count($jobs);

        // The file has been processed
        $cache->save($cacheKey, [
            self::LBL_JOBSTATUS => 'processed',
            'data' => $totalJobs
        ]);

        // set the completedJobs, errorCounrt to 0
        $completedJobs = 0;
        $errorCount = 0;

        // while there are jobs that are not quite done yet
        while (count($jobs) > 0) {

            // Sleep for three seconds...
            sleep(3);

            // get the percent completed
            $percentComplete = round(($completedJobs / $totalJobs) * 100);

            // save the cache information
            $cache->save($cacheKey, [
                self::LBL_JOBSTATUS => 'running',
                'data' => [
                    'completedJobs' => $completedJobs,
                    'totalJobs' => $totalJobs,
                    'percentComplete' => $percentComplete
                ]
            ]);


            // get each jobs,
            foreach ($jobs as $jobNumber => $job) {

                // get the status of the given job
                $status = $job->get();

                // if there is a completed job
                if ($status === Resque_Job_Status::STATUS_COMPLETE) {
                    $completedJobs ++;

                    // get the errors from the job
                    $jobErrors = $cache->fetch("groupstudentbulk.upload.{$uploadId}.job.{$jobNumber}.errors");
                    // $logger->info("############## ");

                    //IF there is an error within the upload
                    if ($jobErrors) {

                        // say that there was an error and count it
                        // $logger->info("############## FOUND ERROR");
                        foreach ($jobErrors as $rowErrors) {
                            foreach ($rowErrors as $columnErrors) {
                                $errorCount += count($columnErrors['errors']);
                            }
                        }
                    }

                    // if there are job errors, count the errors into
                    if (! empty($jobErrors)) {
                        $errors = $errors + $jobErrors;
                    }

                    // get the percentage of the jobs working on
                    $percentComplete = round(($completedJobs / $totalJobs) * 100);

                    // save the cache of the information
                    // in this case the status is running,
                    // and percentage parts
                    $cache->save($cacheKey, [
                        self::LBL_JOBSTATUS => 'running',
                        'data' => [
                            'completedJobs' => $completedJobs,
                            'totalJobs' => $totalJobs,
                            'percentComplete' => $percentComplete
                        ]
                    ]);

                    // log the information into the database
                    // $logger->info(' Group Student Upload Status: Total - ' . $totalJobs . ' Completed - ' . $completedJobs . ' Percent Complete - ' . $percentComplete . '%');

                    // destroy the job within the data
                    unset($jobs[$jobNumber]);

                    // if the job has failed
                } elseif ($status === Resque_Job_Status::STATUS_FAILED) {
                    // save the file upload as fail
                    $uploadFileLogService->updateJobStatusById($uploadId, 'F');
                    sleep(2);
                    // save it to cache
                    $cache->save($cacheKey, [
                        self::LBL_JOBSTATUS => UploadConstant::FINISHED
                    ]);
                    exit();
                }
            }
        }

        // Get the errors for the upload
        $uploadFileLogService->updateErrorCount($uploadId, $errorCount);
        $validRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.valid');

        $updatedRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.updated');
        $updatedRowCount = $updatedRowCount ? $updatedRowCount : 0;
        $uploadFileLogService->saveUpdatedRowCount($uploadId, $updatedRowCount);

        $createdRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.created');
        $createdRowCount = $createdRowCount ? $createdRowCount : 0;
        $uploadFileLogService->saveCreatedRowCount($uploadId, $createdRowCount);

        $errorRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.error');
        $errorRowCount = $errorRowCount ? $errorRowCount : 0;
        $uploadFileLogService->saveErrorRowCount($uploadId, $errorRowCount);

        // if the validRow count is null then change it to 0
        if(is_null($validRowCount)){
            // not magic number null = 0 in this case
            $validRowCount = 0;
        }

        // save valid row count
        $uploadFileLogService->saveValidRowCount($uploadId, $validRowCount);
        $downloadFailedLogFile = "";

        // If there are errors,
        if ($errorCount) {

            // generate the error report
            $downloadFailedLogFile = $manageGroupUploadService->generateErrorCSV($orgId,$errors);
            sleep(2);

            // save to the cache the upload has finished
            $cache->save($cacheKey, [
                self::LBL_JOBSTATUS => UploadConstant::FINISHED
            ]);
        } else {

            // just save to the cache that the upload has finished
            sleep(2);
            $cache->save($cacheKey, [
                self::LBL_JOBSTATUS => UploadConstant::FINISHED
            ]);
        }

        $manageGroupUploadService->sendFTPNotification($orgId, 'Group_student', $errorCount, $downloadFailedLogFile);


        // clear everything up and finish the upload
        $manageGroupUploadService->createExisting($orgId);
        $rbacManager = $this->getContainer()->get('tinyrbac.manager');
        $rbacManager->refreshPermissionCache();


    }
}
