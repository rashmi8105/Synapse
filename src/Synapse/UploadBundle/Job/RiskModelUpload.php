<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\RiskBundle\Entity\RiskModelWeights;
use Synapse\RiskBundle\Entity\RiskModelMaster;
use Synapse\RiskBundle\Entity\RiskVariable;
use Synapse\UploadBundle\Util\Constants\UploadConstant;



class RiskModelUpload extends ContainerAwareJob
{    
    const MODELID = 'ModelID';
    
    const RISKVARNAME = 'RiskVarName';
    
    const WEIGHT = 'Weight';
    
    const COMMANDS = 'Commands';
    
    const VALUE = 'value';

    const ERRORS = 'errors';
    
    const CLEAR = '#clear';

    public function run($args)
    {   
        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        
        $logger = $this->getContainer()->get('logger');
        
        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> RiskModelUpload-Job-");
          
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $cache = $this->getContainer()->get('synapse_redis_cache');
        $organizationRepository = $repositoryResolver->getRepository('SynapseCoreBundle:Organization');
        $riskModelWeightRepository = $repositoryResolver->getRepository('SynapseRiskBundle:RiskModelWeights');
        $riskModelRepository = $repositoryResolver->getRepository('SynapseRiskBundle:RiskModelMaster');
        $riskVariableRepository = $repositoryResolver->getRepository('SynapseRiskBundle:RiskVariable');
                
        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> RiskModelUpload-Job-initiated repository");
        
        $validatorObj = $this->getContainer()->get('risk_model_validator_service');
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $resque = $this->getContainer()->get('bcc_resque.resque');
        $errors = [];

        $validRows = 0;
        
        $requiredItems = [
            self::MODELID,
            self::RISKVARNAME,
            self::WEIGHT
        ];
        $removeItem = 'Remove';
        $removeGroupModel = "#clear";
        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> RiskModelUpload-Job-create-loop- started- count:".count($creates));
        foreach ($creates as $id => $data) {            
            $requiredMissing = false;            
   
            $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> checking empty of values");
            $validatorObj->isValueEmpty($data[strtolower(self::MODELID)], self::MODELID);
            $validatorObj->isValueEmpty($data[strtolower(self::RISKVARNAME)], self::RISKVARNAME);
            $validatorObj->isValueEmpty($data[strtolower(self::WEIGHT)], self::WEIGHT);
            
            $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> empty check done");
            $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> validating data");
            //if (sizeof($errorsTrack) == 0) {
            $validatorObj->validateModelData($data);
            //}
            $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> data validated");
            
            $errorsTrack = $validatorObj->getErrors();
            $errcount = count($errorsTrack);
            $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> no. of error found - ".$errcount);
            if (sizeof($errorsTrack) > 0) {
                $errors[$id] = $errorsTrack;
            } else {
             try {   
            $riskVariable = $riskVariableRepository->findOneByRiskBVariable($data[strtolower(self::RISKVARNAME)]);
            $variableId = $riskVariable->getId();            
            $riskModel = $riskModelRepository->findOneById($data[strtolower(self::MODELID)]);
            
            
            $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> RiskModelUpload--Validated-".$data[strtolower(self::COMMANDS)]);
          
            
            if ($data[strtolower(self::COMMANDS)] == self::CLEAR) {
                $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> RiskModelUpload--Deleting data");
                
                $riskModelWeight = $riskModelWeightRepository->findOneBy([
                    'riskModel' => $data[strtolower(self::MODELID)],
                    'riskVariable' => $variableId,
                    'weight' => $data[strtolower(self::WEIGHT)]
                    ]);
                if($riskModelWeight) {
                    $logger->info(">>>>>>>>>>>>> checking if calculation started.");
                    $currentDate = new \DateTime();
                    $calculationStartDate = $riskModel->getCalculationStartDate();                
                    if(($calculationStartDate) && ($calculationStartDate < $currentDate)){
                        $key= 'RISK_M_009';
                        $errorMsg = RiskErrorConstants::RISK_M_009;
                        $this->setError($key, $errorMsg);
                    }                
                    else { 
                        $logger->info(">>>>>>>>>>>>> deleting record.");
                        $riskModelWeightRepository->delete($riskModelWeight);
                    }
                }
                else { $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> RiskModelUpload--exception-record does not found ");
                    throw new ValidationException([
                                    UploadConstant::DATA_NOT_VALID
                                ], UploadConstant::DATA_NOT_VALID, UploadConstant::DATA_NOT_VALID);
                }
            } else {              
                $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> RiskModelUpload--Inserting data");
                        $riskModelWeight = new RiskModelWeights();
                        $riskModelWeight->setRiskVariable($riskVariable);
                        $riskModelWeight->setWeight($data[strtolower(self::WEIGHT)]);
                        $riskModelWeight->setRiskModel($riskModel);
                        $riskModelWeightRepository->createModelVars($riskModelWeight);
                        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> RiskModelUpload--Persisted");
                        
                        $currentDate = new \DateTime();
                        $calEndDate = $riskModel->getCalculationEndDate();
                        if (($calEndDate) && ($currentDate < $calEndDate)) {
                            $riskCalcArray = [];
                            $riskCalcArray['modelid'] = $data[strtolower(self::MODELID)];
                            
                            $createObject = 'Synapse\UploadBundle\Job\ProcessOrgCalcFlagRisk';
                            $job = new $createObject();
                            
                            $job->args = $riskCalcArray;
                            $resque->enqueue($job, true);
                        }
                
                
            }
                $validRows ++;
             } catch (ValidationException $e) {
                 $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> RiskModelUpload-catch-exception");
                    $logger->error(">>>>>>>>>>>>>>>>>>>> " . $e->getMessage());
                    $errors[$id][] = [
                        'name' => '',
                        'value' => '',
                        'errors' => [
                            $e->getMessage()
                        ]
                    ];
                }
            }
            $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> updating valid row count.".$validRows);
        }
        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> updating valid row count.");
        $uploadFileLogService->updateValidRowCount($uploadId, $validRows);
        
        $riskModelWeightRepository->flush();
        $riskModelWeightRepository->clear();
        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> RiskModelUpload--Flushed");
        $jobs = $cache->fetch("riskmodel.upload.{$uploadId}.jobs");
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $cache->save("riskmodel.upload.{$uploadId}.jobs", $jobs);
        
        $cache->save("riskmodel:upload:{$uploadId}:job:{$jobNumber}:errors", $errors);
        
        return $errors;
    }
}
