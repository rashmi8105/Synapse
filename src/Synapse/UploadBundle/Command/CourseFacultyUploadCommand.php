<?php
namespace Synapse\UploadBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Synapse\CoreBundle\Util\CSVFile;
use Resque_Job_Status;
use Synapse\CoreBundle\Util\UploadHelper;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class CourseFacultyUploadCommand extends ContainerAwareCommand
{

    private $ioEmitter;

    private $uploadFileLogService;

    private $output;

    protected function configure()
    {
        $this->setName('courseFacultyUpload:process')
            ->setDescription('Process a faculty upload CSV')
            ->addArgument('file', InputArgument::REQUIRED, 'What file do you want to process?')
            ->addArgument('orgId', InputArgument::REQUIRED, 'Organization to process this upload as?')
            ->addArgument(UploadConstant::UPLOADID, InputArgument::OPTIONAL, 'Id to process this upload as?');

        $this->uploadHelper = new UploadHelper();
    }

    /**
     * executes the upload command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $file = $input->getArgument('file');
        $orgId = $input->getArgument('orgId');
        $uploadId = ! is_null($input->getArgument(UploadConstant::UPLOADID)) ? $input->getArgument(UploadConstant::UPLOADID) : 1;
        $cacheKey = 'upload.' . $uploadId . '.status';

        $errors = [];

        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');

        $courseFacultyUploadService = $this->getContainer()->get('course_faculty_upload_service');
        $cache = $this->getContainer()->get('synapse_redis_cache');

        $fileData = $courseFacultyUploadService->load($file, $orgId);
        $cache->save($cacheKey, [
            UploadConstant::JOBSTATUS => 'loaded',
            'data' => $fileData
        ]);

        $jobData = $courseFacultyUploadService->process($uploadId);
        $jobs = $jobData['jobs'];
        $totalJobs = count($jobs);

        $cache->save($cacheKey, [
            UploadConstant::JOBSTATUS => 'processed',
            'data' => $totalJobs
        ]);

        $completedJobs = 0;
        $errorCount = 0;

        while (count($jobs) > 0) {
            sleep(3);

            $percentComplete = round(($completedJobs / $totalJobs) * 100);

            $cache->save($cacheKey, [
                UploadConstant::JOBSTATUS => 'running',
                'data' => [
                    'completedJobs' => $completedJobs,
                    'totalJobs' => $totalJobs,
                    'percentComplete' => $percentComplete
                ]
            ]);

            foreach ($jobs as $jobNumber => $job) {
                $status = $job->get();

                if ($status === Resque_Job_Status::STATUS_COMPLETE) {
                    $completedJobs ++;

                    $jobErrors = $cache->fetch("organization:{$orgId}:upload:{$uploadId}:job:{$jobNumber}:errors");

                    if ($jobErrors) {
                        foreach ($jobErrors as $rowErrors) {
                            foreach ($rowErrors as $columnErrors) {
                                $errorCount += count($columnErrors['errors']);
                            }
                        }

                        $errors = $errors + $jobErrors;
                    }

                    $percentComplete = round(($completedJobs / $totalJobs) * 100);

                    $cache->save($cacheKey, [
                        UploadConstant::JOBSTATUS => 'running',
                        'data' => [
                            'completedJobs' => $completedJobs,
                            'totalJobs' => $totalJobs,
                            'percentComplete' => $percentComplete
                        ]
                    ]);

                    
                    echo 'Status: Total - ' . $totalJobs . ' Completed - ' . $completedJobs . ' Percent Complete - ' . $percentComplete . '%' . PHP_EOL;

                    unset($jobs[$jobNumber]);
                } elseif ($status === Resque_Job_Status::STATUS_FAILED) {
                    $uploadFileLogService->updateJobStatusById($uploadId, 'F');
                    sleep(2);
                    $cache->save($cacheKey, [
                        UploadConstant::JOBSTATUS => UploadConstant::FINISHED
                    ]);
                    exit();
                }
            }
        }

        $uploadFileLogService->updateErrorCount($uploadId, $errorCount);

        $validRowCount   = $cache->getRedis()->get('upload.' . $uploadId . '.valid');
        $errorRowCount   = $cache->getRedis()->get('upload.' . $uploadId . '.error');
        $createdRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.created');
        $updatedRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.updated');
        $validRowCount = $validRowCount ? $validRowCount : 0;
        $errorRowCount = $errorRowCount ? $errorRowCount : 0;
        $createdRowCount = $createdRowCount ? $createdRowCount : 0;
        $updatedRowCount = $updatedRowCount ? $updatedRowCount : 0;
        $uploadFileLogService->saveValidRowCount($uploadId, $validRowCount);
        $uploadFileLogService->saveErrorRowCount($uploadId, $errorRowCount);
        $uploadFileLogService->saveCreatedRowCount($uploadId, $createdRowCount);
        $uploadFileLogService->saveUpdatedRowCount($uploadId, $updatedRowCount);


        $downloadFailedLogFile = "";

        if ($errorCount) {
            $this->uploadHelper->generateErrorCSV($errors, $courseFacultyUploadService->getFileObject(), "data://course_faculty_uploads/errors/{$orgId}-{$uploadId}-upload-errors.csv");
            $downloadFailedLogFile = "errors/{$orgId}-{$uploadId}-upload-errors.csv";
        }


        $courseFacultyUploadService->sendFTPNotification($orgId, 'course_faculty', $errorCount, $downloadFailedLogFile);
        $courseFacultyUploadService->generateDumpCSV($orgId);

        sleep(2);
        $cache->save($cacheKey, [
            UploadConstant::JOBSTATUS => UploadConstant::FINISHED
        ]);

    }
}
