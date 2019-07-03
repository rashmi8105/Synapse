<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdate;
use Synapse\AcademicUpdateBundle\Service\AcademicUpdateCreateService;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Helper;
use Synapse\RiskBundle\EntityDto\RiskVariableDto;
use Synapse\RiskBundle\EntityDto\BucketDetailsDto;
use Synapse\RiskBundle\EntityDto\SourceIdDto;
use Synapse\RiskBundle\EntityDto\CalculatedDataDto;
use Synapse\RiskBundle\Util\Constants\RiskVariableConstants;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class CreateRiskVariable extends ContainerAwareJob
{

    const MODULE_FILE = "UPLOAD BUNDLE : RISKVARIABLE UPLOAD : CreateRiskVariable : ";

    const LOGGER = 'logger';
    
    const RISK_VALIDATOR_SERVICE = 'riskvariable_validator_service';

    public function run($args)
    {
        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $userId = $args['userId'];
        
        $errors = [];
        $validRows = 0;
        $updatedRows = 0;
        $createdRows = 0;

        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $cache = $this->getContainer()->get('synapse_redis_cache');
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $validatorObj = $this->getContainer()->get(self::RISK_VALIDATOR_SERVICE);
        $riskUploadService = $this->getContainer()->get('riskvariable_upload_service');
        $personService = $this->getContainer()->get('person_service');
        $riskVariableService = $this->getContainer()->get('riskvariable_service');
        
        $riskVariableRepository = $repositoryResolver->getRepository('SynapseRiskBundle:RiskVariable');
        
        $logger = $this->getContainer()->get(self::LOGGER);
        
        $AcademicUpdateRepository = $repositoryResolver->getRepository('SynapseAcademicUpdateBundle:AcademicUpdate');
        $AcademicUpdateReqRepository = $repositoryResolver->getRepository('SynapseAcademicUpdateBundle:AcademicUpdateRequest');
        
        foreach ($creates as $id => $data) {
            $logger->info(self::MODULE_FILE . __FUNCTION__ . " : Processing Risk variable : " . $data[strtolower(RiskVariableConstants::RISK_VAR_RISKVARNAME)]);
            $requiredMissing = false;
            
            $validatorObj->isValueEmpty($data[strtolower(RiskVariableConstants::RISK_VAR_RISKVARNAME)], RiskVariableConstants::RISK_VAR_RISKVARNAME);
            $validatorObj->isValueEmpty($data[strtolower(RiskVariableConstants::RISK_VAR_RISKVARTYPE)], RiskVariableConstants::RISK_VAR_RISKVARTYPE);
            $validatorObj->isValueEmpty($data[strtolower(RiskVariableConstants::RISK_VAR_CALCULATED)], RiskVariableConstants::RISK_VAR_CALCULATED);
            $validatorObj->isValueEmpty($data[strtolower(RiskVariableConstants::RISK_VAR_SOURCETYPE)], RiskVariableConstants::RISK_VAR_SOURCETYPE);
            $validatorObj->isValueEmpty($data[strtolower(RiskVariableConstants::RISK_VAR_SOURCEID)], RiskVariableConstants::RISK_VAR_SOURCEID);
            $tempSource = strtoupper($data[strtolower(RiskVariableConstants::RISK_VAR_SOURCETYPE)]);
            
            $isContinous = (strtolower($data[strtolower(RiskVariableConstants::RISK_VAR_RISKVARTYPE)]) == 'continuous') ? true : false;
            $this->validateCampusSpecific($data, $tempSource);
            $this->validateCalculation($data);
            $barray = [];
            $barray = $this->setBucketDetail($isContinous, $data);
            
            $this->continuousBucketValidation($isContinous, $barray);
            
            $errorsTrack = $validatorObj->getErrors();
            
            if (sizeof($errorsTrack) > 0) {
                $errors[$id] = $errorsTrack;
            } else {
                try {
                    $riskVariableDto = new RiskVariableDto();
                    
                    $sourceIdDto = new SourceIdDto();
                    
                    $sourse = strtolower($data[strtolower(RiskVariableConstants::RISK_VAR_SOURCETYPE)]);
                    $logger->info(self::MODULE_FILE . __FUNCTION__ . " : Processing Risk variable Source: " . $sourse);
                    
                    $sourceIdDto = $this->setSource($sourse, $sourceIdDto, $data);
                    $riskVariableDto->setRiskVariableName($data[strtolower(RiskVariableConstants::RISK_VAR_RISKVARNAME)]);
                    $riskVariableDto->setSourceType($data[strtolower(RiskVariableConstants::RISK_VAR_SOURCETYPE)]);
                    $riskVariableDto->setSourceId($sourceIdDto);
                    
                    $riskVariableDto->setIsContinuous($isContinous);
                    $riskVariableDto->setIsCalculated((strtolower($data[strtolower(RiskVariableConstants::RISK_VAR_CALCULATED)]) == 'yes') ? true : false);
                    $riskVariableDto = $this->setIsCalculated($riskVariableDto, $data);
                    
                    $barray = [];
                    $barray = $this->setBucketDetail($isContinous, $data);
                    $riskVariableDto->setBucketDetails($barray);
                    
                    $logger->info(self::MODULE_FILE . __FUNCTION__ . " : Creating Risk variable : " . $data[strtolower(RiskVariableConstants::RISK_VAR_RISKVARNAME)]);
                    
                    /**
                     * Need to find out Risk Variable for update
                     * check the name exist or not, if exist then it for update else insert
                     */
                    $isVariableExists = $riskVariableRepository->findOneByRiskBVariable($data[strtolower(RiskVariableConstants::RISK_VAR_RISKVARNAME)]);
                    if($isVariableExists)
                    {
                        $riskVariableDto->setId($isVariableExists->getId());
                        $riskVariableService->create($riskVariableDto, 'update');

                        $updatedRows++;
                    }else{
                        $riskVariableService->create($riskVariableDto, 'insert');
                        $createdRows++;
                    }
                   
                    $validRows ++;
                } catch (ValidationException $e) {
                    $logger->error(self::MODULE_FILE . __FUNCTION__ . " : FAILED Risk variable Creation : " . $data[strtolower(RiskVariableConstants::RISK_VAR_RISKVARNAME)] . $e->getMessage());
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
        $uploadFileLogService->updateCreatedRowCount($uploadId, $createdRows);
        $uploadFileLogService->updateUpdatedRowCount($uploadId, $updatedRows);
        $uploadFileLogService->updateErrorRowCount($uploadId, count($errors));

        $AcademicUpdateRepository->clear();
        /**
         * Fetch Job details
         */
        $jobs = $cache->fetch("riskvariable.upload.{$uploadId}.jobs");
        
        $jobs = $this->unsetJob($jobs, $jobNumber);
        $cache->save("riskvariable.upload.{$uploadId}.jobs", $jobs);
        $cache->save("riskvariable.upload:{$uploadId}:job:{$jobNumber}:errors", $errors);
        return $errors;
    }

    public function setSource($sourse, $sourceIdDto, $data)
    {
        $riskUploadService = $this->getContainer()->get('riskvariable_upload_service');
        switch ($sourse) {
            case 'profile':
                $sourceId = $riskUploadService->getEbiProfileIdByName($data[strtolower(RiskVariableConstants::RISK_VAR_SOURCEID)]);
                $sourceIdDto->setEbiProfileId($sourceId);
                break;
            case 'surveyquestion':
                $sourceData = explode("-", $data[strtolower(RiskVariableConstants::RISK_VAR_SOURCEID)]);
                $sourceIdDto->setSurveyId(@$sourceData[0]);
                $sourceIdDto->setQuestionId(@$sourceData[1]);
                break;
            case 'surveyfactor':
                $sourceData = explode("-", $data[strtolower(RiskVariableConstants::RISK_VAR_SOURCEID)]);
                $sourceIdDto->setSurveyId(@$sourceData[0]);
                $sourceIdDto->setFactorId(@$sourceData[1]);
                break;
            case 'isp':
                $sourceData = $riskUploadService->getOrgProfileIdByName($data[strtolower(RiskVariableConstants::RISK_VAR_SOURCEID)], $data[strtolower(RiskVariableConstants::RISK_VAR_CAMPUSID)]);
                $sourceIdDto->setIspId($sourceData);
                $sourceIdDto->setCampusId($data[strtolower(RiskVariableConstants::RISK_VAR_CAMPUSID)]);
                break;
            case 'isq':
                $sourceIdDto->setIsqId($data[strtolower(RiskVariableConstants::RISK_VAR_SOURCEID)]);
                $sourceIdDto->setCampusId($data[strtolower(RiskVariableConstants::RISK_VAR_CAMPUSID)]);
                break;
            case 'questionbank':
                $sourceIdDto->setQuestionBankId($data[strtolower(RiskVariableConstants::RISK_VAR_SOURCEID)]);
                break;
            default:
                $errors[$id][] = $this->getSwitchError();
        }
        return $sourceIdDto;
    }

    public function continuousBucketValidation($isContinous, $barray)
    {
        if ($isContinous) {
            $logger = $this->getContainer()->get(self::LOGGER);
            $validatorObj = $this->getContainer()->get(self::RISK_VALIDATOR_SERVICE);
            $logger->info(self::MODULE_FILE . __FUNCTION__ . " : continuous type validation");
            $validatorObj->validateContinousBucket($barray);
        }
    }

    public function validateCampusSpecific($data, $tempSource)
    {
        $logger = $this->getContainer()->get(self::LOGGER);
        $validatorObj = $this->getContainer()->get(self::RISK_VALIDATOR_SERVICE);
        if ($tempSource == 'ISP' || $tempSource == 'ISQ') {
            $logger->info(self::MODULE_FILE . __FUNCTION__ . " : ISP | ISQ Validation");
            $validatorObj->isValueEmpty($data[strtolower(UploadConstant::CAMPUSID)], 'CampusID');
        }
    }

    public function validateCalculation($data)
    {
        $logger = $this->getContainer()->get(self::LOGGER);
        $validatorObj = $this->getContainer()->get(self::RISK_VALIDATOR_SERVICE);
        
        if (ucfirst($data[strtolower(RiskVariableConstants::RISK_VAR_CALCULATED)]) == 'Yes') {
            $logger->info(self::MODULE_FILE . __FUNCTION__ . " : Is Calculated Validation");
            $validatorObj->isValueEmpty($data[strtoloweR(RiskVariableConstants::RISK_VAR_CALTYPE)], RiskVariableConstants::RISK_VAR_CALTYPE);
            $validatorObj->isValueEmpty($data[strtolower(RiskVariableConstants::RISK_VAR_CALMIN)], RiskVariableConstants::RISK_VAR_CALMIN);
            $validatorObj->isValueEmpty($data[strtolower(RiskVariableConstants::RISK_VAR_CALMAX)], RiskVariableConstants::RISK_VAR_CALMAX);
        }
    }

    public function unsetJob($jobs, $jobNumber)
    {
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        return $jobs;
    }

    public function getDateFromFormat($date)
    {
        try {
            $dateTime = new \DateTIme($date);
        } catch (\Exception $e) {
            
            throw new ValidationException([
                $e->getMessage()
            ], $e->getMessage(), 'ERR_RISK-VAR');
        }
        return $dateTime;
    }

    public function setIsCalculated($riskVariableDto, $data)
    {
        if ($riskVariableDto->getIsCalculated()) {
            $calcualtedDto = new CalculatedDataDto();
            $calcualtedDto->setCalculationType($data[strtolower(RiskVariableConstants::RISK_VAR_CALTYPE)]);
            $calcualtedDto->setCalculationStartDate($this->getDateFromFormat($data[strtolower(RiskVariableConstants::RISK_VAR_CALMIN)]));
            $calcualtedDto->setCalculationStopDate($this->getDateFromFormat($data[strtolower(RiskVariableConstants::RISK_VAR_CALMAX)]));
            
            $riskVariableDto->setCalculatedData($calcualtedDto);
        } else {
            $riskVariableDto->setCalculatedData(NULL);
        }
        return $riskVariableDto;
    }

    public function setBucketDetail($isContinous, $data)
    {
        $barray = [];
        
        for ($i = 1; $i <= 7; $i ++) 

        {
            
            $bucketDetailsDto = new BucketDetailsDto();
            $bucketDetailsDto->setBucketValue($i);
            if ($isContinous) {
                $bucketDetailsDto->setMin($data[strtolower('B' . $i . 'Min')]);
                $bucketDetailsDto->setMax($data[strtolower('B' . $i . 'Max')]);
            } else {
                $catArray = [];
                if (strpos($data[strtolower('B' . $i . 'Cat')], ";")) {
                    $catArray = explode(";", $data[strtolower('B' . $i . 'Cat')]);
                } else {
                    $catArray[] = $data[strtolower('B' . $i . 'Cat')];
                }
                $bucketDetailsDto->setOptionValue($catArray);
            }
            
            $barray[] = $bucketDetailsDto;
        }
        
        return $barray;
    }

    private function getSwitchError()
    {
        throw new ValidationException([
            "Invalid Source Type"
        ], "Invalid Source Type", 'ERRSOURCE');
    }
}