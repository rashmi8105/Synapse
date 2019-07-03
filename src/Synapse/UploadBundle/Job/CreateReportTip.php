<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Helper;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\ReportsBundle\EntityDto\TipsDto;

class CreateReportTip extends ContainerAwareJob
{

    const MODULE_FILE = "UPLOAD BUNDLE : REPORT TIP UPLOAD : CreateReportTip : ";

    const LOGGER = 'logger';
    
    const REPORT_ELEMENT_VALIDATOR_SERVICE = 'report_element_validator_service';

    public function run($args)
    {
        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $userId = $args['userId'];
        
        $errors = [];
        $validRows = 0;

        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $cache = $this->getContainer()->get('synapse_redis_cache');
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $validatorObj = $this->getContainer()->get(self::REPORT_ELEMENT_VALIDATOR_SERVICE);
        $reportElementsUploadService = $this->getContainer()->get('report_elements_upload_service');        
        $reportSetupService = $this->getContainer()->get('reportsetup_service');        
        $reportTipsRepository = $repositoryResolver->getRepository(ReportsConstants::REPORT_TIPS_REPO);		
        $logger = $this->getContainer()->get(self::LOGGER);        
        
        foreach ($creates as $id => $data) {            
            $logger->info(self::MODULE_FILE . __FUNCTION__ . " : Processing Report Tip upload ");            
            $validatorObj->isValueEmpty($data['SectionID'], 'SectionID');
            $validatorObj->isValueEmpty($data['TipName'], 'TipName');
            $validatorObj->isValueEmpty($data['TipText'], 'TipText');             
            $validatorObj->isNumeric($data['DisplayOrder'], 'DisplayOrder'); 
            $validatorObj->validateCharLength(250, $data['TipText'], 'TipText');  
            $validatorObj->validateCharLength(45, $data['TipName'], 'TipName');
            $isTipExists = $reportTipsRepository->findBy(['title' => $data['TipName']]);
            if($isTipExists )
            {
                $validatorObj->checkDuplicate($data['TipName']);
            }
            $errorsTrack = $validatorObj->getErrors();            
            if (sizeof($errorsTrack) > 0) {
                $errors[$id] = $errorsTrack;
            } else {
                try {
                        $tipDto = new TipsDto();									
                        $logger->info(self::MODULE_FILE . __FUNCTION__ . " : Processing report tip create");
                        $tipDto->setSectionId($data['SectionID']);
                        $tipDto->setTipName($data['TipName']);
                        $tipDto->setTipText($data['TipText']);
                        $tipDto->setTipOrder($data['DisplayOrder']);
                        $logger->info(self::MODULE_FILE . __FUNCTION__ . " : Creating Report tip : " . $data['TipText']);    $reportSetupService->createTip($tipDto);
                        $validRows ++;          
                } catch (ValidationException $e) {
                    $logger->error(self::MODULE_FILE . __FUNCTION__ . " : FAILED Report Tip Creation : " . $data['TipText'] . $e->getMessage());
                    $errors[$id][] = [
                        'name' => '',
                        'value' => '',
                        'errors' => [
                            $e->getMessage()
                        ]
                    ];
                }
            }
        }
        $uploadFileLogService->updateValidRowCount($uploadId, $validRows);
        $uploadFileLogService->updateCreatedRowCount($uploadId, $validRows);
        $uploadFileLogService->updateErrorRowCount($uploadId, count($errors));
        $reportTipsRepository->clear();
        /**
         * Fetch Job details
         */
        $jobs = $cache->fetch("reportTip.upload.{$uploadId}.jobs");
        
        $jobs = $this->unsetJob($jobs, $jobNumber);
        $cache->save("reportTip.upload.{$uploadId}.jobs", $jobs);
        $cache->save("reportTip.upload:{$uploadId}:job:{$jobNumber}:errors", $errors);
        return $errors;
    }   

    public function unsetJob($jobs, $jobNumber)
    {
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        return $jobs;
    }

}