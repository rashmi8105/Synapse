<?php

namespace Synapse\UploadBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVFile;
use Synapse\CoreBundle\Service\PersonServiceInterface;
use Synapse\CoreBundle\Entity\Person;
use Resque_Job_Status;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class FacultyUploadCommand extends ContainerAwareCommand
{

    private $personService;

    private $uploadFileLogService;

    private $facultyEntity;

    private $ioEmitter;

    private $output;


    protected function configure()
    {
        $this
            ->setName('facultyUpload:process')
            ->setDescription('Process a faculty upload CSV')
            ->addArgument('file', InputArgument::REQUIRED, 'What file do you want to process?')
            ->addArgument('orgId', InputArgument::REQUIRED, 'Org Id to process this upload as?')
            ->addArgument(UploadConstant::UPLOADTYPE, InputArgument::OPTIONAL, 'Upload Type to process this upload as?')
            ->addArgument(UploadConstant::UPLOADID, InputArgument::OPTIONAL, 'Upload Id to process this upload as?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {


        $this->output = $output;

        $file = $input->getArgument('file');
        $orgId = $input->getArgument('orgId');
        $uploadType = !is_null($input->getArgument(UploadConstant::UPLOADTYPE)) ? $input->getArgument(UploadConstant::UPLOADTYPE) : "";
        $uploadId = !is_null($input->getArgument(UploadConstant::UPLOADID)) ? $input->getArgument(UploadConstant::UPLOADID) : 1;
        $cacheKey = 'upload.' . $uploadId . '.status';

        $errors = [];

        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');

        $facultyUploadService = $this->getContainer()->get('faculty_upload_service');
        $synapseUploadService = $this->getContainer()->get('synapse_upload_service');
        $synapseUploadService->entityName = 'Faculty';


        $cache = $this->getContainer()->get('synapse_redis_cache');

        $fileData = $synapseUploadService->load($file, $orgId);
        $cache->save($cacheKey, [UploadConstant::JOBSTATUS => 'loaded', UploadConstant::DATA => $fileData]);

        $jobData = $synapseUploadService->process($uploadId);
        $jobs = $jobData['jobs'];
        $totalJobs = count($jobs);

        $cache->save($cacheKey, [UploadConstant::JOBSTATUS => 'processed', UploadConstant::DATA => $totalJobs]);

        $completedJobs = 0;
        $errorCount = 0;

        while (count($jobs) > 0) {
            sleep(3);

            $percentComplete = round(($completedJobs / $totalJobs) * 100);

            $cache->save($cacheKey,
                [
                    UploadConstant::JOBSTATUS => 'running',
                    UploadConstant::DATA => [
                        'completedJobs' => $completedJobs,
                        'totalJobs' => $totalJobs,
                        'percentComplete' => $percentComplete
                    ]
                ]
            );

            foreach ($jobs as $jobNumber => $job) {
                $status = $job->get();

                if ($status === Resque_Job_Status::STATUS_COMPLETE) {
                    $completedJobs++;

                    $jobErrors = $cache->fetch(
                        "organization:{$orgId}:upload:{$uploadId}:job:{$jobNumber}:errors"
                    );


                    if ($jobErrors) {
                        foreach ($jobErrors as $rowErrors) {
                            foreach ($rowErrors as $columnErrors) {
                                $errorCount += count($columnErrors['errors']);
                            }
                        }

                        $errors = $errors + $jobErrors;
                    }

                    $percentComplete = round(($completedJobs / $totalJobs) * 100);

                    $cache->save($cacheKey,
                        [
                            UploadConstant::JOBSTATUS => 'running',
                            UploadConstant::DATA => [
                                'completedJobs' => $completedJobs,
                                'totalJobs' => $totalJobs,
                                'percentComplete' => $percentComplete
                            ]
                        ]
                    );


                    // echo 'Status: Total - ' .
                    //     $totalJobs . ' Completed - ' .
                    //     $completedJobs . ' Percent Complete - ' .
                    //     $percentComplete . '%'.PHP_EOL;

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
        $validRowCount = $validRowCount ? $validRowCount : 0;
        $uploadFileLogService->saveValidRowCount($uploadId, $validRowCount);

        $errorRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.error');
        $errorRowCount = $errorRowCount ? $errorRowCount : 0;
        $uploadFileLogService->saveErrorRowCount($uploadId, $errorRowCount);


        // added created and updated counts into the database
        $createdRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.created');
        $createdRowCount = $createdRowCount ? $createdRowCount : 0;
        $updatedRowCount = $cache->getRedis()->get('upload.' . $uploadId . '.updated');
        $updatedRowCount = $updatedRowCount ? $updatedRowCount : 0;
        $uploadFileLogService->saveCreatedRowCount($uploadId, $createdRowCount);
        $uploadFileLogService->saveUpdatedRowCount($uploadId, $updatedRowCount);


        if ($errorCount) {
            $downloadFailedLogFile = $synapseUploadService->generateErrorCSV($errors);
            sleep(2);
            $cache->save($cacheKey, [UploadConstant::JOBSTATUS => UploadConstant::FINISHED]);
        } else {
            sleep(2);
            $cache->save($cacheKey, [UploadConstant::JOBSTATUS => UploadConstant::FINISHED]);
        }

        $updatedRows = $synapseUploadService->getUpdatedRows();
        $facultyUploadService->removeActivitiesForFacultyInactivatedByThisUpload($updatedRows, $orgId);
        $synapseUploadService->sendFTPNotification($orgId, 'faculty', $errorCount, $downloadFailedLogFile);
        $synapseUploadService->generateDumpCSV($orgId, "Faculty");

    }

}