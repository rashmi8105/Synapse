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

class ElementsUploadCommand extends ContainerAwareCommand
{

    private $uploadFileLogService;

    private $ioEmitter;

    private $output;

    protected function configure()
    {	
        $this->setName('elementsUpload:process')
            ->setDescription('Process a Elements upload CSV')
            ->addArgument('file', InputArgument::REQUIRED, 'What file do you want to process?')
            ->addArgument(UploadConstant::USERID, InputArgument::OPTIONAL, 'User Id to process this upload as?')
            ->addArgument(UploadConstant::UPLOADID, InputArgument::OPTIONAL, 'Id to process this upload as?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {		
        $this->getContainer()
            ->get(UploadConstant::LOGGER)
            ->info(">>>>>>>>>>I am in ElementsUploadCommand");
        
        $this->output = $output;
        
        $file = $input->getArgument('file');
        
        $userId = ! is_null($input->getArgument(UploadConstant::USERID)) ? $input->getArgument(UploadConstant::USERID) : 1;
        $uploadId = ! is_null($input->getArgument(UploadConstant::UPLOADID)) ? $input->getArgument(UploadConstant::UPLOADID) : 1;
        $cacheKey = 'upload.' . $uploadId . '.status';
        $this->getContainer()
            ->get(UploadConstant::LOGGER)
            ->info(">>>>>>>>>>I am in ElementsUploadCommand -1");
        $errors = [];
        
        $uploadFileLogService = $this->getContainer()->get(UploadConstant::UPLOAD_FILE_LOG_SERVICE);
        $logger = $this->getContainer()->get(UploadConstant::LOGGER);
        $reportElementsUploadService = $this->getContainer()->get('report_elements_upload_service');
        $cache = $this->getContainer()->get('synapse_redis_cache');        
		$this->getContainer()
            ->get(UploadConstant::LOGGER)
            ->info(">>>>>>>>>>I am in ElementsUploadCommand -2-" . $file);
        $fileData = $reportElementsUploadService->load($file, $userId);
        
        $this->getContainer()
            ->get(UploadConstant::LOGGER)
            ->info(">>>>>>>>>>I am in ElementsUploadCommand -3");
        $cache->save($cacheKey, [
            UploadConstant::JOBSTATUS => 'loaded',
            UploadConstant::DATA => $fileData
        ]);
        
        $jobData = $reportElementsUploadService->process($uploadId);
        $this->getContainer()
            ->get(UploadConstant::LOGGER)
            ->info(">>>>>>>>>>I am in ElementsUploadCommand -4");
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
                    
                    $jobErrors = $cache->fetch("reportElement.upload:{$uploadId}:job:{$jobNumber}:errors");
                    
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
                    
                    $logger->info('Report Elements upload status: Total - ' . $totalJobs . ' Completed - ' . $completedJobs . ' Percent Complete - ' . $percentComplete . '%');
                    
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
            $reportElementsUploadService->generateErrorCSV($errors);
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
        $reportElementsUploadService->generateDumpCSV();
    }
}