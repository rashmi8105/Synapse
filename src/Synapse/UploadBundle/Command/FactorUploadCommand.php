<?php
namespace Synapse\UploadBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVFile;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdate;
use Resque_Job_Status;
use Synapse\CoreBundle\Util\UploadHelper;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class FactorUploadCommand extends ContainerAwareCommand
{

    private $uploadFileLogService;

    private $ioEmitter;

    private $output;

    protected function configure()
    {
        $this->setName('factorUpload:process')
            ->setDescription('Process a Survey Block Upload CSV')
            ->addArgument('file', InputArgument::REQUIRED, 'What file do you want to process?')
            ->addArgument('orgId', InputArgument::OPTIONAL, 'Organization Id to process this upload as?')
            ->addArgument(UploadConstant::UPLOADTYPE, InputArgument::OPTIONAL, 'Upload Type to process this upload as?')
            ->addArgument(UploadConstant::UPLOADID, InputArgument::OPTIONAL, 'Upload Id to process this upload as?');
        
        $this->uploadHelper = new UploadHelper();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        
        $file = $input->getArgument('file');
        
        $orgId = ! is_null($input->getArgument(UploadConstant::ORGID)) ? $input->getArgument(UploadConstant::ORGID) : 1;
        $userId =  -1;
        $uploadId = ! is_null($input->getArgument(UploadConstant::UPLOADID)) ? $input->getArgument(UploadConstant::UPLOADID) : 1;
        $cacheKey = 'upload.' . $uploadId . '.status';
        
        $errors = [];
        
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $factorUploadService = $this->getContainer()->get('factor_upload_service');
        $cache = $this->getContainer()->get('synapse_redis_cache');
        
        $fileData = $factorUploadService->load($file, $orgId, $userId);
        $cache->save($cacheKey, [
            UploadConstant::JOBSTATUS => 'loaded',
            UploadConstant::DATA => $fileData
        ]);
        
        $jobData = $factorUploadService->process($uploadId);
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
                    
                    $jobErrors = $cache->fetch("organization:{$orgId}:upload:{$uploadId}:job:{$jobNumber}:errors");
                    if ($jobErrors) {
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

        if ($errorCount) {
            $this->uploadHelper->generateErrorCSV($errors, $factorUploadService->getFileObject(), "data://factor_uploads/errors/-1-{$uploadId}-upload-errors.csv");
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
        
        $factorUploadService->generateDumpCSV($uploadId);
    }
}