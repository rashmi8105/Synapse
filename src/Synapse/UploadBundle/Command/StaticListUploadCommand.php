<?php
namespace Synapse\UploadBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Synapse\CoreBundle\Util\UploadHelper;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVFile;
use Resque_Job_Status;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class StaticListUploadCommand extends ContainerAwareCommand
{

    private $uploadFileLogService;

    private $ioEmitter;

    private $output;

    private $uploadHelper;

    private $errorCount = 0;

    private $errors;

    protected function configure()
    {
        $this->setName('staticListUpload:process')
            ->setDescription('Process a Static List upload CSV')
            ->addArgument('file', InputArgument::REQUIRED, 'What file do you want to process?')
            ->addArgument(UploadConstant::ORGID, InputArgument::REQUIRED, 'Id to process this upload as?')
            ->addArgument(UploadConstant::USERID, InputArgument::REQUIRED, 'User Id to process this upload as?')
            ->addArgument(UploadConstant::STATICLISTID, InputArgument::REQUIRED, 'User Id to process this upload as?')
            ->addArgument(UploadConstant::UPLOADID, InputArgument::OPTIONAL, 'Id to process this upload as?');

        $this->uploadHelper = new UploadHelper();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->output = $output;

        $file = $input->getArgument('file');
        $organizationId = ! is_null($input->getArgument(UploadConstant::ORGID)) ? $input->getArgument(UploadConstant::ORGID) : 1;
        $userId = ! is_null($input->getArgument(UploadConstant::USERID)) ? $input->getArgument(UploadConstant::USERID) : 1;
        $staticListId = ! is_null($input->getArgument(UploadConstant::STATICLISTID)) ? $input->getArgument(UploadConstant::STATICLISTID) : 1;
        $uploadId = ! is_null($input->getArgument(UploadConstant::UPLOADID)) ? $input->getArgument(UploadConstant::UPLOADID) : 1;
        $cacheKey = 'upload.' . $uploadId . '.status';

        $this->errors = [];

        $this->uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $staticListUploadService = $this->getContainer()->get('staticlist_upload_service');
        $cache = $this->getContainer()->get('synapse_redis_cache');

        $fileData = $staticListUploadService->load($file, $organizationId, $userId, $staticListId);
        $cache->save($cacheKey, [
            UploadConstant::JOBSTATUS => 'loaded',
            UploadConstant::DATA => $fileData
        ]);

        $jobData = $staticListUploadService->process($uploadId);
        $jobs = $jobData['jobs'];
        $totalJobs = count($jobs);

        $cache->save($cacheKey, [
            UploadConstant::JOBSTATUS => 'processed',
            UploadConstant::DATA => $totalJobs
        ]);

        $this->jobs($jobs, $cache, $cacheKey,  $organizationId, $uploadId);

        $this->uploadFileLogService->updateErrorCount($uploadId, $this->errorCount);

        $validRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.valid');
        $validRowCount = $validRowCount ? $validRowCount : 0;
        $ErrorRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.error');
        $ErrorRowCount = $ErrorRowCount ? $ErrorRowCount : 0;
        $createdRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.created');
        $createdRowCount = $createdRowCount ? $createdRowCount : 0;
        $updatedRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.updated');
        $updatedRowCount = $updatedRowCount ? $updatedRowCount : 0;
        $this->uploadFileLogService->saveValidRowCount($uploadId, $validRowCount);
        $this->uploadFileLogService->saveErrorRowCount($uploadId, $ErrorRowCount);
        $this->uploadFileLogService->saveCreatedRowCount($uploadId, $createdRowCount);
        $this->uploadFileLogService->saveUpdatedRowCount($uploadId, $updatedRowCount);

        $downloadFailedLogFile = "";
        if ($this->errorCount) {
            $downloadFailedLogFile = $this->uploadHelper->generateErrorCSV($this->errors, $staticListUploadService->getFileObject(), "data://staticlist_uploads/errors/{$organizationId}-{$uploadId}-upload-errors.csv");
            sleep(2);
            $cache->save($cacheKey, [
                UploadConstant::JOBSTATUS => UploadConstant::FINISHED
            ]);
        } else {
            sleep(2);
            $cache->save($cacheKey, [
                UploadConstant::JOBSTATUS => UploadConstant::FINISHED
            ]);
        }

        $staticListUploadService->sendFTPNotification($organizationId, $this->errorCount, $downloadFailedLogFile, $userId);
    }

    private function jobs($jobs, $cache, $cacheKey, $orgId, $uploadId)
    {

        $totalJobs = count($jobs);
        $completedJobs = 0;

        while (count($jobs) > 0)
        {
            sleep(3);
            $percentComplete = round(($completedJobs / $totalJobs) * 100);
            $cache->save($cacheKey, [
                UploadConstant::JOBSTATUS => 'running',
                UploadConstant::DATA => [
                    'completedJobs' => $completedJobs,
                    'totalJobs' => $totalJobs,
                    'percentComplete' => $percentComplete
                ]
            ]);

            foreach ($jobs as $jobNumber => $job)
            {

                $status = $job->get();

                if ($status === Resque_Job_Status::STATUS_COMPLETE) {
                    $completedJobs ++;
                    $jobErrors = $cache->fetch("organization:".$orgId.":upload:".$uploadId.":job:{$jobNumber}:errors");

                    ($jobErrors) ? $this->errorCount( $jobErrors ) : "";

                    (! empty($jobErrors)) ? $this->errors = $this->errors + $jobErrors : "";

                    $percentComplete = round(($completedJobs / $totalJobs) * 100);
                    $cache->save($cacheKey, [
                        UploadConstant::JOBSTATUS => 'running',
                        UploadConstant::DATA => [
                            'completedJobs' => $completedJobs,
                            'totalJobs' => $totalJobs,
                            'percentComplete' => $percentComplete
                        ]
                    ]);
                    unset($jobs[$jobNumber]);
                }

                if ($status === Resque_Job_Status::STATUS_FAILED) {
                    $this->uploadFileLogService->updateJobStatusById($uploadId, 'F');
                    sleep(2);
                    $cache->save($cacheKey, [
                        UploadConstant::JOBSTATUS => UploadConstant::FINISHED
                    ]);
                    exit();
                }
            }
        }
    }

    private function errorCount( $jobErrors ) {

        foreach ($jobErrors as $rowErrors) {
            foreach ($rowErrors as $columnErrors) {
                $this->errorCount += count($columnErrors['errors']);
            }
        }

    }

}