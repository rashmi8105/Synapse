<?php
namespace Synapse\UploadBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVFile;
use Synapse\CoreBundle\Service\TalkingPointService;
use Synapse\CoreBundle\Entity\TalkingPoints;
use Resque_Job_Status;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class TalkingPointsUploadCommand extends ContainerAwareCommand
{

    private $uploadFileLogService;

    private $ioEmitter;

    private $output;

    protected function configure()
    {
        $this->setName('talkingPointsUpload:process')
            ->setDescription('Process a Taling Points upload CSV')
            ->addArgument('file', InputArgument::REQUIRED, 'What file do you want to process?')
            ->addArgument(UploadConstant::ORGID, InputArgument::REQUIRED, 'Id to process this upload as?')
            ->addArgument(UploadConstant::UPLOADID, InputArgument::OPTIONAL, 'Id to process this upload as?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $file = $input->getArgument('file');
        $orgId = ! is_null($input->getArgument('orgId')) ? $input->getArgument('orgId') : 1;
        $uploadId = ! is_null($input->getArgument('uploadId')) ? $input->getArgument('uploadId') : 1;
        $cacheKey = 'upload.' . $uploadId . '.status';
        $errors = [];
        $completedJobs = 0;
        $errorCount = 0;

        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $talkingPointsUploadService = $this->getContainer()->get('talking_points_upload_service');
        $cache = $this->getContainer()->get('synapse_redis_cache');

        $fileData = $talkingPointsUploadService->load($file, $orgId);
        $cache->save($cacheKey, [
            UploadConstant::JOBSTATUS => 'loaded',
            UploadConstant::DATA => $fileData
        ]);

        $jobData = $talkingPointsUploadService->process($uploadId);
        $jobs = $jobData['jobs'];
        $totalJobs = count($jobs);

        $cache->save($cacheKey, [
            UploadConstant::JOBSTATUS => 'processed',
            UploadConstant::DATA => $totalJobs
        ]);

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
                    $jobErrors = $cache->fetch("organization:talkingpoints:upload:{$uploadId}:job:{$jobNumber}:errors");
                    if ($jobErrors) {
                        foreach ($jobErrors as $rowErrors) {
                            foreach ($rowErrors as $columnErrors) {
                                $errorCount += count($columnErrors['errors']);
                            }
                        }
                    }
                    $errors = $errors + $jobErrors;
                    $percentComplete = round(($completedJobs / $totalJobs) * 100);
                    $cache->save($cacheKey, [
                        UploadConstant::JOBSTATUS => 'running',
                        UploadConstant::DATA => [
                            'completedJobs' => $completedJobs,
                            'totalJobs' => $totalJobs,
                            'percentComplete' => $percentComplete
                        ]
                    ]);

                    
                    // echo 'Status: Total - ' . $totalJobs . ' Completed - ' . $completedJobs . ' Percent Complete - ' . $percentComplete . '%' . PHP_EOL;

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

        $createdRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.created');
        $createdRowCount = $createdRowCount ? $createdRowCount : 0;
        $uploadFileLogService->saveCreatedRowCount($uploadId, $createdRowCount);

        $updatedRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.updated');
        $updatedRowCount = $updatedRowCount ? $updatedRowCount : 0;
        $uploadFileLogService->saveUpdatedRowCount($uploadId, $updatedRowCount);

        $errorRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.error');
        $errorRowCount = $errorRowCount ? $errorRowCount : 0;
        $uploadFileLogService->saveErrorRowCount($uploadId, $errorRowCount);


        if ($errorCount) {
            $talkingPointsUploadService->generateErrorCSV($errors);
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

        $talkingPointsUploadService->generateDumpCSV($orgId);
    }
}