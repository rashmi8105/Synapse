<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Helper;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\ReportsBundle\EntityDto\ElementDto;
use Synapse\ReportsBundle\EntityDto\ElementBucketDto;


class CreateReportElements extends ContainerAwareJob
{

    const MODULE_FILE = "UPLOAD BUNDLE : REPORT ELEMENSTS UPLOAD : CreateReportElements : ";

    const LOGGER = 'logger';
    
    const REPORT_ELEMENT_VALIDATOR_SERVICE = 'report_element_validator_service';

    const SECTIONID = 'SectionID';

    const ELEMENTNAME = 'ElementName';

    const DATATYPE = 'DataType';

    const REDTEXT = 'RedText';

    const REDLOW = 'RedLow';

    const REDHIGH = 'RedHigh';

    const YELLOWTEXT = 'YellowText';

    const YELLOWHIGH = 'YellowHigh';

    const YELLOWLOW = 'YellowLow';

    const GREENTEXT = 'GreenText';

    const GREENHIGH = 'GreenHigh';

    const GREENLOW = 'GreenLow';

    const RED = 'Red';

    const YELLOW = 'Yellow';

    const GREEN = 'Green';

    const DATASOURCE = 'DataSource';

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
        $personService = $this->getContainer()->get('person_service');
        $reportSetupService = $this->getContainer()->get('reportsetup_service');        
        $sectionElementRepository = $repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_ELEMENT_REPO);
        $sectionRepository = $repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_REPO);
        $logger = $this->getContainer()->get(self::LOGGER);                
        foreach ($creates as $id => $data) {            
            $elementCount = $sectionElementRepository->findBy([ 'sectionId' => $data['sectionid'] ]);
            if(count($elementCount) >= 5)
            {
                $section = $sectionRepository->find($data['sectionid']);                
                $validatorObj->stackFullError($section->getTitle());
            }
            $logger->info(self::MODULE_FILE . __FUNCTION__ . " : Processing Report Elements upload : " . $data['elementname']);
            $requiredMissing = false;
            
            $validatorObj->isValueEmpty($data[strtolower(self::SECTIONID)], self::SECTIONID);
            $validatorObj->validateDataType($data[strtolower(self::DATATYPE)]);
            $validatorObj->isValueEmpty($data[strtolower(self::ELEMENTNAME)], self::ELEMENTNAME);
            $validatorObj->isValueEmpty($data[strtolower(self::DATATYPE)], self::DATATYPE);
            $validatorObj->isValueEmpty($data[strtolower(self::DATASOURCE)], self::DATASOURCE);            
            $validatorObj->isNumeric($data[strtolower(self::REDLOW)], self::REDLOW);
            $validatorObj->isNumeric($data[strtolower(self::REDHIGH)], self::REDHIGH);            
            $validatorObj->isValueEmpty($data[strtolower(self::REDTEXT)], self::REDTEXT);                        
            $validatorObj->isValueEmpty($data[strtolower(self::YELLOWTEXT)], self::YELLOWTEXT);
            $validatorObj->isNumeric($data[strtolower(self::YELLOWLOW)], self::YELLOWLOW);
            $validatorObj->isNumeric($data[strtolower(self::YELLOWHIGH)], self::YELLOWHIGH);                        
            $validatorObj->isValueEmpty($data[strtolower(self::GREENTEXT)], self::GREENTEXT); 
            $validatorObj->isNumeric($data[strtolower(self::GREENLOW)], self::GREENLOW);
            $validatorObj->isNumeric($data[strtolower(self::GREENHIGH)], self::GREENHIGH);            
            $isElementExists = $sectionElementRepository->findBy(['title' => $data[strtolower(self::ELEMENTNAME)], 'sectionId' => $data[strtolower(self::SECTIONID)] ]);

            if($isElementExists )
            {
                $validatorObj->checkDuplicate($data[strtolower(self::ELEMENTNAME)]);
            }

            $errorsTrack = $validatorObj->getErrors();
            

            if (sizeof($errorsTrack) > 0) {
                $errors[$id] = $errorsTrack;
            } else {
                try {    
                    $buckets = [];
                    $elementsDto = new ElementDto();									
                    $logger->info(self::MODULE_FILE . __FUNCTION__ . " : Processing report elements create");
                    $elementsDto->setSectionId($data[strtolower(self::SECTIONID)]);
					$elementsDto->setElementName($data[strtolower(self::ELEMENTNAME)]);
					$elementsDto->setSourceType($data[strtolower(self::DATATYPE)]);
					if($data[strtolower(self::DATATYPE)] == 'Factor')
					{						
						$elementsDto->setFactorId($data[strtolower(self::DATASOURCE)]);
					}
					if($data[strtolower(self::DATATYPE)] == 'QuestionBank')
					{						
						$elementsDto->setSurveyQuestionId($data[strtolower(self::DATASOURCE)]);
					}
					$redElementBucketDto = new ElementBucketDto();						
					$redElementBucketDto->setBucketName(self::RED);
					$redElementBucketDto->setBucketText($data[strtolower(self::REDTEXT)]);
					$redElementBucketDto->setRangeMin($data[strtolower(self::REDLOW)]);
					$redElementBucketDto->setRangeMax($data[strtolower(self::REDHIGH)]);
					$buckets[] = $redElementBucketDto;
					$yellowElementBucketDto = new ElementBucketDto();	
					$yellowElementBucketDto->setBucketName(self::YELLOW);
					$yellowElementBucketDto->setBucketText($data[strtolower(self::YELLOWTEXT)]);
					$yellowElementBucketDto->setRangeMin($data[strtolower(self::YELLOWLOW)]);
					$yellowElementBucketDto->setRangeMax($data[strtolower(self::YELLOWHIGH)]);
					$buckets[] = $yellowElementBucketDto;
					$greenElementBucketDto = new ElementBucketDto();	
					$greenElementBucketDto->setBucketName(self::GREEN);
					$greenElementBucketDto->setBucketText($data[strtolower(self::GREENTEXT)]);
					$greenElementBucketDto->setRangeMin($data[strtolower(self::GREENLOW)]);
					$greenElementBucketDto->setRangeMax($data[strtolower(self::GREENHIGH)]);
					$buckets[] = $greenElementBucketDto;
					$elementsDto->setBuckets($buckets);                                        
                    $logger->info(self::MODULE_FILE . __FUNCTION__ . " : Creating Report Elements : " . $data[strtolower(self::ELEMENTNAME)]);$reportSetupService->createElements($elementsDto);
                    $validRows ++;
                } catch (ValidationException $e) {                    
                    $logger->error(self::MODULE_FILE . __FUNCTION__ . " : FAILED Report Element Creation : " . $data[strtolower(self::ELEMENTNAME)] . $e->getMessage());
                    $errors[$id][] = [
                        'name' => $e->getCode(),
                        'value' => '',
                        'errors' => [
                            $e->getMessage()
                        ]
                    ];
                }
            }
        }

        // All Report Elements upload are either created or errored out
        $uploadFileLogService->updateValidRowCount($uploadId, $validRows);
        $uploadFileLogService->updateCreatedRowCount($uploadId, $validRows);
        $uploadFileLogService->updateErrorRowCount($uploadId, count($errors));
        $sectionElementRepository->clear();
        /**
         * Fetch Job details
         */
        $jobs = $cache->fetch("reportElement.upload.{$uploadId}.jobs");        
        $jobs = $this->unsetJob($jobs, $jobNumber);
        $cache->save("reportElement.upload.{$uploadId}.jobs", $jobs);        
        $cache->save("reportElement.upload:{$uploadId}:job:{$jobNumber}:errors", $errors);        
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