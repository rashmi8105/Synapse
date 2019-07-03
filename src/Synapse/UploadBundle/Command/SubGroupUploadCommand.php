<?php
namespace Synapse\UploadBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVFile;
use Resque_Job_Status;
use Synapse\UploadBundle\Service\Impl\GroupFacultyBulkUploadService;
use Synapse\UploadBundle\Service\Impl\ManageGroupStudentBulkUploadService;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class SubGroupUploadCommand extends ContainerAwareCommand
{

    private $uploadFileLogService;

    private $output;

    protected function configure()
    {
        $this->setName('subgroupUpload:process')
            ->setDescription('Process a Sub Groupupload CSV')
            ->addArgument('file', InputArgument::REQUIRED, 'What file do you want to process?')
            ->addArgument(UploadConstant::ORGID, InputArgument::REQUIRED, 'Id to process this upload as?')
            ->addArgument('userId', InputArgument::OPTIONAL, 'User Id to process this upload as?')
            ->addArgument(UploadConstant::UPLOADID, InputArgument::OPTIONAL, 'Id to process this upload as?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $file = $input->getArgument('file');

        $orgId = ! is_null($input->getArgument(UploadConstant::ORGID)) ? $input->getArgument(UploadConstant::ORGID) : 1;
        $userId = ! is_null($input->getArgument(UploadConstant::USERID)) ? $input->getArgument(UploadConstant::USERID) : 1;
        $uploadId = ! is_null($input->getArgument(UploadConstant::UPLOADID)) ? $input->getArgument(UploadConstant::UPLOADID) : 1;
        $cacheKey = 'upload.' . $uploadId . '.status';

        $errors = [];

        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $logger = $this->getContainer()->get('logger');
        $groupUploadService = $this->getContainer()->get('group_upload_service');
        $cache = $this->getContainer()->get('synapse_redis_cache');

        $fileData = $groupUploadService->load($file, $orgId, $userId);

        $cache->save($cacheKey, [
            UploadConstant::JOBSTATUS => 'loaded',
            UploadConstant::DATA => $fileData
        ]);

        $jobData = $groupUploadService->process($uploadId);

        $jobs = $jobData['jobs'];
        $totalJobs = count($jobs);

        $cache->save($cacheKey, [
            UploadConstant::JOBSTATUS => 'processed',
            UploadConstant::DATA => $totalJobs
        ]);

        $completedJobs = 0;
        $errorCount = 0;

        while (count($jobs) > 0) {
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

            foreach ($jobs as $jobNumber => $job) {

                $status = $job->get();

                if ($status === Resque_Job_Status::STATUS_COMPLETE) {
                    $completedJobs ++;

                    $jobErrors = $cache->fetch("subgroup.upload.{$uploadId}.job.{$jobNumber}.errors");
                    $logger->info("############## ");
                    if ($jobErrors) {
                        $logger->info("############## FOUND ERROR");
                        foreach ($jobErrors as $rowErrors) {

                            foreach ($rowErrors as $columnErrors) {

                                $errorCount += count($columnErrors['errors']);
                            }
                        }
                    }

                    if (! empty($jobErrors)) {
                        $errors = $errors + $jobErrors;
                    }

                    $percentComplete = round(($completedJobs / $totalJobs) * 100);

                    $cache->save($cacheKey, [
                        UploadConstant::JOBSTATUS => 'running',
                        UploadConstant::DATA => [
                            'completedJobs' => $completedJobs,
                            'totalJobs' => $totalJobs,
                            'percentComplete' => $percentComplete
                        ]
                    ]);

                    $logger->info('Sub Group Upload Status: Total - ' . $totalJobs . ' Completed - ' . $completedJobs . ' Percent Complete - ' . $percentComplete . '%');

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

        $validRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.valid');
        $validRowCount = $validRowCount ? $validRowCount : 0;
        $uploadFileLogService->saveValidRowCount($uploadId, $validRowCount);

        $errorRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.error');
        $errorRowCount = $errorRowCount ? $errorRowCount : 0;
        $uploadFileLogService->saveErrorRowCount($uploadId, $errorRowCount);

        $createdRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.created');
        $createdRowCount = $createdRowCount ? $createdRowCount : 0;
        $uploadFileLogService->saveCreatedRowCount($uploadId, $createdRowCount);

        $updatedRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.updated');
        $updatedRowCount = $updatedRowCount ? $updatedRowCount : 0;
        $uploadFileLogService->saveUpdatedRowCount($uploadId, $updatedRowCount);
        $downloadFailedLogFile = "";

        if ($errorCount) {
            $downloadFailedLogFile = $groupUploadService->generateErrorCSV($orgId, $errors);
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

        $groupUploadService->sendFTPNotification($orgId, 'group', $errorCount, $downloadFailedLogFile);

        /**
         * Generating existing Data
         */
        $groupUploadService->createExisting($orgId);

        $groupFacultyUploadService = $this->getContainer()->get(GroupFacultyBulkUploadService::SERVICE_KEY);
        $groupFacultyUploadService->createExisting($orgId);
        $groupStudentUploadService = $this->getContainer()->get(ManageGroupStudentBulkUploadService::SERVICE_KEY);
        $groupStudentUploadService->createExisting($orgId);

    }
}