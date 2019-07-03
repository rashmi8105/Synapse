<?php

namespace Synapse\UploadBundle\Command;

use JMS\DiExtraBundle\Annotation as DI;
use Resque_Job_Status;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\UploadBundle\Service\Impl\StudentUploadService;
use Synapse\UploadBundle\Service\Impl\SynapseUploadService;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class StudentUploadCommand extends ContainerAwareCommand
{

    private $output;

    protected function configure()
    {
        $this
            ->setName('studentUpload:process')
            ->setDescription('Process a student upload CSV')
            ->addArgument('file', InputArgument::REQUIRED, 'What file do you want to process?')
            ->addArgument('orgId', InputArgument::REQUIRED, 'Organization Id to process this upload as?')
            ->addArgument(UploadConstant::UPLOADTYPE, InputArgument::OPTIONAL, 'Upload Type to process this upload as?')
            ->addArgument(UploadConstant::UPLOADID, InputArgument::OPTIONAL, 'Upload Id to process this upload as?')
        ;
    }

    /**
     * Executes the student upload command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cache = $this->getContainer()->get(SynapseConstant::REDIS_CLASS_KEY);
        $logger = $this->getContainer()->get(UploadConstant::LOGGER);
        $studentUploadService = $this->getContainer()->get(StudentUploadService::SERVICE_KEY);
        $synapseUploadService = $this->getContainer()->get(SynapseUploadService::SERVICE_KEY);
        $uploadFileLogService = $this->getContainer()->get(UploadFileLogService::SERVICE_KEY);
        $synapseUploadService->entityName = 'Student';

        $logger->info("=====================================================================");
        $logger->info("Inside execute command Student Upload Command");
        $logger->info("=====================================================================");

        $this->output = $output;

        $file = $input->getArgument('file');
        $organizationId = $input->getArgument('orgId');

        $uploadId = !is_null($input->getArgument(UploadConstant::UPLOADID)) ? $input->getArgument(UploadConstant::UPLOADID) : 1;
        $cacheKey = 'upload.' . $uploadId . '.status';

        $this->getContainer()
            ->get(UploadConstant::LOGGER)
            ->info("=====================================================================");
        $logger->info("file :" . $file);
        $logger->info("=====================================================================");


        $logger->info("=====================================================================");
        $logger->info("Organization ID :" . $organizationId);
        $logger->info("=====================================================================");

        $fileData = $synapseUploadService->load($file, $organizationId);
        $cache->save($cacheKey, [UploadConstant::JOBSTATUS => 'loaded', 'data' => $fileData]);

        $logger->info("=====================================================================");
        $logger->info("Loaded CSV data :" . json_encode($fileData));
        $logger->info("=====================================================================");

        $jobData = $synapseUploadService->process($uploadId);
        $jobs = $jobData['jobs'];
        $totalJobs = count($jobs);

        $logger->info("=====================================================================");
        $logger->info("Total Jobs : " . $totalJobs);
        $logger->info("=====================================================================");

        $cache->save($cacheKey, [UploadConstant::JOBSTATUS => 'processed', 'data' => $totalJobs]);

        $completedJobs = 0;
        $errorCount = 0;

        while (count($jobs) > 0) {
            sleep(3);

            $percentComplete = round(($completedJobs / $totalJobs) * 100);


            $cache->save($cacheKey,
                [
                    UploadConstant::JOBSTATUS => 'running',
                    'data' => [
                        'completedJobs' => $completedJobs,
                        'totalJobs' => $totalJobs,
                        'percentComplete' => $percentComplete
                    ]
                ]
            );

            $logger->info("=====================================================================");
            $logger->info("Jobs Found:");
            $logger->info(json_encode($jobs));
            $logger->info("=====================================================================");

            foreach ($jobs as $jobNumber => $job) {
                $status = $job->get();

                $logger->info("=====================================================================");
                $logger->info(json_encode($jobNumber));
                $logger->info("Status:" . $status);
                $logger->info("=====================================================================");

                if ($status === Resque_Job_Status::STATUS_COMPLETE) {
                    $completedJobs++;

                    $jobErrors = $cache->fetch(
                        "organization:{$organizationId}:upload:{$uploadId}:job:{$jobNumber}:errors"
                    );

                    if (!is_array($jobErrors)) {
                        $jobErrors = [];
                    }

                    foreach ($jobErrors as $rowErrors) {
                        foreach ($rowErrors as $columnErrors) {
                            $errorCount += count($columnErrors['errors']);
                        }
                    }

                    $cacheUploadError = ($cache->fetch("organization:{$organizationId}:upload:{$uploadId}:errors")) ? $cache->fetch("organization:{$organizationId}:upload:{$uploadId}:errors") : [];

                    $errorUnion = $cacheUploadError + $jobErrors;

                    $cache->save(
                        "organization:{$organizationId}:upload:{$uploadId}:errors", $errorUnion
                    );

                    $percentComplete = round(($completedJobs / $totalJobs) * 100);

                    $logger->info("=====================================================================");
                    $logger->info("Percent Complete : " . $percentComplete);
                    $logger->info("=====================================================================");

                    $cache->save($cacheKey,
                        [
                            UploadConstant::JOBSTATUS => 'running',
                            'data' => [
                                'completedJobs' => $completedJobs,
                                'totalJobs' => $totalJobs,
                                'percentComplete' => $percentComplete
                            ]
                        ]
                    );

                    $logger->info("=====================================================================");
                    $logger->info("Saved in cache : " . $cacheKey);
                    $logger->info("=====================================================================");

                    unset($jobs[$jobNumber]);
                } elseif ($status === Resque_Job_Status::STATUS_FAILED) {
                    $uploadFileLogService->updateJobStatusById($uploadId, 'F');
                    sleep(2);
                    $cache->save($cacheKey, [UploadConstant::JOBSTATUS => UploadConstant::FINISHED]);
                    exit();
                }

            }


        }

        $uploadFileLogService->updateErrorCount($uploadId, $errorCount);


        $downloadFailedLogFile = "";
        $validRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.valid');
        $updatedRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.updated');
        $createdRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.created');
        $validRowCount = $validRowCount ? $validRowCount : 0;
        $updatedRowCount = $updatedRowCount ? $updatedRowCount : 0;
        $createdRowCount = $createdRowCount ? $createdRowCount : 0;

        $logger->info("=====================================================================");
        $logger->info("validRowCount :  " . $validRowCount);
        $logger->info("=====================================================================");

        $uploadFileLogService->saveValidRowCount($uploadId, $validRowCount);
        $uploadFileLogService->saveCreatedRowCount($uploadId, $createdRowCount);
        $uploadFileLogService->saveUpdatedRowCount($uploadId, $updatedRowCount);


        $errorRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.error');
        $errorRowCount = $errorRowCount ? $errorRowCount : 0;
        $uploadFileLogService->saveErrorRowCount($uploadId, $errorRowCount);


        if ($errorCount) {
            $downloadFailedLogFile = $synapseUploadService->generateErrorCSV($cache->fetch("organization:{$organizationId}:upload:{$uploadId}:errors"));
            sleep(2);
            $cache->save($cacheKey, [UploadConstant::JOBSTATUS => UploadConstant::FINISHED]);
        } else {
            sleep(2);
            $cache->save($cacheKey, [UploadConstant::JOBSTATUS => UploadConstant::FINISHED]);
        }

        $logger->info("=====================================================================");
        $logger->info("==========================EMAIL NOTIFICATION ==================");

        $logger->info("==========================  I AM INSIDE SENDING EMAIL" . $downloadFailedLogFile);
        $logger->info("========================== ERROR COUNT " . $errorCount);
        $synapseUploadService->sendFTPNotification($organizationId, 'student', $errorCount, $downloadFailedLogFile);

        $logger->info("=====================================================================");
        $logger->info("generateRiskUploadCSV for organizationId " . $organizationId);
        $logger->info("=====================================================================");

        //Once the student CSV is generated then generate the dump file  for the Risk Uploads
        $studentUploadService->generateRiskUploadCSV($organizationId);
    }

}
