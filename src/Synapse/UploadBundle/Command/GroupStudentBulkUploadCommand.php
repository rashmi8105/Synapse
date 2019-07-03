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
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class GroupStudentBulkUploadCommand extends ContainerAwareCommand
{

    private $uploadFileLogService;

    private $output;

    const LBL_ORGID = 'orgId';

    const LBL_JOBSTATUS = 'jobStatus';

    const LBL_UPLOADEDID = 'uploadId';

    protected function configure()
    {
        $this->setName('groupFacultyBulkUpload:process')
            ->setDescription('Process a Bulk Faculty Group Upload CSV')
            ->addArgument('file', InputArgument::REQUIRED, 'What file do you want to process?')
            ->addArgument(self::LBL_ORGID, InputArgument::REQUIRED, 'Id to process this upload as?')
            ->addArgument(UploadConstant::USERID, InputArgument::OPTIONAL, 'User Id to process this upload as?')
            ->addArgument(self::LBL_UPLOADEDID, InputArgument::OPTIONAL, 'Id to process this upload as?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $file = $input->getArgument('file');

        $orgId = ! is_null($input->getArgument(self::LBL_ORGID)) ? $input->getArgument(self::LBL_ORGID) : 1;
        $userId = ! is_null($input->getArgument(UploadConstant::USERID)) ? $input->getArgument(UploadConstant::USERID) : 1;
        $uploadId = ! is_null($input->getArgument(self::LBL_UPLOADEDID)) ? $input->getArgument(self::LBL_UPLOADEDID) : 1;
        $cacheKey = 'upload.' . $uploadId . '.status';

        $errors = [];

        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $logger = $this->getContainer()->get('logger');
        $groupUploadService = $this->getContainer()->get('groupstudentbulk_upload_service');
        $manageGroupUploadBulkService = $this->getContainer()->get('manage_group_student_upload_service');
        $cache = $this->getContainer()->get('synapse_redis_cache');

        $fileData = $groupUploadService->load($file, $orgId, $userId);

        $cache->save($cacheKey, [
            self::LBL_JOBSTATUS => 'loaded',
            'data' => $fileData
        ]);

        $jobData = $groupUploadService->process($uploadId);

        $jobs = $jobData['jobs'];
        $totalJobs = count($jobs);

        $cache->save($cacheKey, [
            self::LBL_JOBSTATUS => 'processed',
            'data' => $totalJobs
        ]);

        $completedJobs = 0;
        $errorCount = 0;

        while (count($jobs) > 0) {
            sleep(3);

            $percentComplete = round(($completedJobs / $totalJobs) * 100);

            $cache->save($cacheKey, [
                self::LBL_JOBSTATUS => 'running',
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

                    $jobErrors = $cache->fetch("groupstudentbulk.upload.{$uploadId}.job.{$jobNumber}.errors");
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
                        self::LBL_JOBSTATUS => 'running',
                        'data' => [
                            'completedJobs' => $completedJobs,
                            'totalJobs' => $totalJobs,
                            'percentComplete' => $percentComplete
                        ]
                    ]);

                    $logger->info(' Group Student Upload Status: Total - ' . $totalJobs . ' Completed - ' . $completedJobs . ' Percent Complete - ' . $percentComplete . '%');

                    unset($jobs[$jobNumber]);
                } elseif ($status === Resque_Job_Status::STATUS_FAILED) {
                    $uploadFileLogService->updateJobStatusById($uploadId, 'F');
                    sleep(2);
                    $cache->save($cacheKey, [
                        self::LBL_JOBSTATUS => UploadConstant::FINISHED
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
            $downloadFailedLogFile = $groupUploadService->generateErrorCSV($orgId,$errors);
            sleep(2);
            $cache->save($cacheKey, [
                self::LBL_JOBSTATUS => UploadConstant::FINISHED
            ]);
        } else {
            sleep(2);
            $cache->save($cacheKey, [
                self::LBL_JOBSTATUS => UploadConstant::FINISHED
            ]);
        }

        $groupUploadService->sendFTPNotification($orgId, 'group_student', $errorCount, $downloadFailedLogFile);
        $manageGroupUploadBulkService->createExisting($orgId);
        $rbacManager = $this->getContainer()->get('tinyrbac.manager');
        $rbacManager->refreshPermissionCache();
    }
}