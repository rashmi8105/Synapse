<?php
namespace Synapse\RiskBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\RiskBundle\Service\RiskModelCreateServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\RiskBundle\Entity\RiskModelMaster;
use Synapse\RiskBundle\Util\Constants\RiskModelConstants;
use Synapse\RiskBundle\Entity\RiskLevels;
use Synapse\RiskBundle\Entity\RiskModelLevels;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\RiskBundle\Util\Constants\RiskErrorConstants;
use Synapse\RiskBundle\Job\ArchiveRiskModelJob;
use Synapse\CoreBundle\Util\Helper;
use JMS\Serializer\Serializer;

/**
 * @DI\Service("riskmodelcreate_service")
 */
class RiskModelCreateService extends AbstractService implements RiskModelCreateServiceInterface
{

    const SERVICE_KEY = 'riskmodelcreate_service';

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
    }

    public function createModel($riskModelDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($riskModelDto);
        $this->logger->debug(" Creating Risk Model  -  " . $logContent);
        
        $modelRepo = $this->repositoryResolver->getRepository(RiskModelConstants::RISK_MODEL_MASTER);
        $currentDate = new \DateTime('now');
        $currentDate->setTime(0, 0, 0);
        $calculationStartDate = $riskModelDto->getCalculationStartDate();
        $calculationStartDate->setTime(0, 0, 0);
        $calculationStopDate = $riskModelDto->getCalculationStopDate();
        $calculationStopDate->setTime(0, 0, 0);
        $enrollmentEndDate = $riskModelDto->getEnrollmentEndDate();
        $enrollmentEndDate->setTime(0, 0, 0);
        
        $riskModelName = $riskModelDto->getRiskModelName();
        
        /**
         * Date Validation
         */
        $this->currentDateValidation($currentDate, $calculationStartDate);
        $this->currentDateValidation($currentDate, $calculationStopDate);
        $this->currentDateValidation($currentDate, $enrollmentEndDate);
        $this->startEndDateValidation($calculationStartDate, $calculationStopDate);
        try {
            $modelRepo->startTransaction();
            $riskModelMaster = new RiskModelMaster();
            $riskModelMaster->setName($riskModelName);
            $riskModelMaster->setModelState($riskModelDto->getModelState());
            $riskModelMaster->setCalculationStartDate($calculationStartDate);
            $riskModelMaster->setCalculationEndDate($calculationStopDate);
            $riskModelMaster->setEnrollmentDate($enrollmentEndDate);
            $this->validateEntity($riskModelMaster);
            $modelRepo->persist($riskModelMaster);
            $indicators = $riskModelDto->getRiskIndicators();
            foreach ($indicators as $indicator) {
                
                /*
                 * $riskLevel = new RiskLevels(); $riskLevel->setRiskText($indicator->getName()); $modelRepo->persist($riskLevel);
                 */
                $riskLevel = $this->getRiskLevelByName($indicator->getName());
                
                $riskModelLevel = new RiskModelLevels();
                $riskModelLevel->setRiskLevel($riskLevel);
                $riskModelLevel->setRiskModel($riskModelMaster);
                $riskModelLevel->setMax($indicator->getMax());
                $riskModelLevel->setMin($indicator->getMin());
                $modelRepo->persist($riskModelLevel);
            }
        } catch (ValidationException $e) {
            $modelRepo->rollbackTransaction();
            throw $e;
        } catch (\Exception $e) {
            $modelRepo->rollbackTransaction();
            throw $e;
        }
        /**
         * Setting job
         */
        $this->setArchiveModelJob($calculationStopDate);
        
        $modelRepo->completeTransaction();
        $riskModelDto->setId($riskModelMaster->getId());
        return $riskModelDto;
    }

    private function setArchiveModelJob($calcEndDate)
    {
        $resque = $this->container->get('bcc_resque.resque');
        $job = new ArchiveRiskModelJob();
        $job->args = array(
            'calculationEndDate' => $calcEndDate
        );
        $resque->enqueueAt($calcEndDate, $job);
    }

    public function updateModel($riskModelDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($riskModelDto);
        $this->logger->debug(" Updating Risk Model  -  " . $logContent);
        
        $riskModelRepo = $this->repositoryResolver->getRepository(RiskModelConstants::RISK_MODEL_MASTER);
        $riskModelLevelRepo = $this->repositoryResolver->getRepository(RiskModelConstants::RISK_MODEL_LEVELS);
        $currentDate = new \DateTime('now');
        $currentDate->setTime(0, 0, 0);
        $calculationStartDate = $riskModelDto->getCalculationStartDate();
        $calculationStartDate->setTime(0, 0, 0);
        
        $calculationStopDate = $riskModelDto->getCalculationStopDate();
        $calculationStopDate->setTime(0, 0, 0);
        
        $enrollmentEndDate = $riskModelDto->getEnrollmentEndDate();
        $enrollmentEndDate->setTime(0, 0, 0);
        
        $this->startEndDateValidation($calculationStartDate, $calculationStopDate);
        
        $modelId = $riskModelDto->getId();
        $riskModelMaster = $riskModelRepo->find($modelId);
        if (! $riskModelMaster) {
            $this->throwException(RiskErrorConstants::RISK_M_005, 'RISK_M_005');
        }
        $isEditable = false;
        try {
            $riskModelRepo->startTransaction();
            $dbCaluculationStartDate = $riskModelMaster->getCalculationStartDate();
            $dbCaluculationEndDate = $riskModelMaster->getCalculationEndDate();
            $isEditable = $this->isEditableRiskModel($currentDate, $dbCaluculationStartDate);
            
            $riskModelName = $riskModelDto->getRiskModelName();
            
            $riskModelMaster->setName($riskModelName);
            
            $riskModelMaster->setCalculationEndDate($calculationStopDate);
            $riskModelMaster->setEnrollmentDate($enrollmentEndDate);
            
            $this->currentDateValidation($currentDate, $calculationStopDate);
            $this->currentDateValidation($currentDate, $enrollmentEndDate);
            
            $riskModelMaster->setCalculationEndDate($calculationStopDate);
            
            if ($calculationStopDate->format(RiskModelConstants::YMD) != $dbCaluculationStartDate->format(RiskModelConstants::YMD) && $riskModelMaster->getModelState() == 'Archived') {
                
                $riskModelMaster->setModelState('Assigned');
                $resque = $this->container->get('bcc_resque.resque');
                $riskCalcArray = [];
                $riskCalcArray['modelid'] = $modelId;
                
                $createObject = 'Synapse\UploadBundle\Job\ProcessOrgCalcFlagRisk';
                $job = new $createObject();
                
                $job->args = $riskCalcArray;
                $resque->enqueue($job, true);
                
                
            }
            if ($isEditable) {
                $this->currentDateValidation($currentDate, $calculationStartDate);
                
                $riskModelMaster->setCalculationStartDate($calculationStartDate);
                
                $indicators = $riskModelDto->getRiskIndicators();
                
                $riskModelLevels = $riskModelLevelRepo->findBy([
                    'riskModel' => $riskModelMaster
                ]);
                foreach ($riskModelLevels as $key => $riskModelLevel) {
                    $riskModelLevel->setMax($indicators[$key]->getMax());
                    $riskModelLevel->setMin($indicators[$key]->getMin());
                }
            } else {
                
                if ($calculationStartDate->format(RiskModelConstants::YMD) != $dbCaluculationStartDate->format(RiskModelConstants::YMD)) {
                    $this->throwException(RiskErrorConstants::RISK_M_006, 'RISK_M_006');
                }
                
                $this->changeCutPoints($riskModelDto, $riskModelMaster);
            }
            
            $this->validateEntity($riskModelMaster);
            $riskModelRepo->flush();
            $riskModelRepo->completeTransaction();
            /**
             * Setting job
             */
            $this->setArchiveModelJob($calculationStopDate);

        } catch (\Exception $e) {
            $riskModelRepo->rollbackTransaction();
            throw $e;
        }
        
        return $riskModelDto;
    }

    private function changeCutPoints($riskModelDto, $riskModelMaster)
    {
        $riskModelLevelRepo = $this->repositoryResolver->getRepository(RiskModelConstants::RISK_MODEL_LEVELS);
        $indicators = $riskModelDto->getRiskIndicators();
        
        $riskModelLevels = $riskModelLevelRepo->findBy([
            'riskModel' => $riskModelMaster
        ]);
        foreach ($riskModelLevels as $key => $riskModelLevel) {
            $max = $indicators[$key]->getMax();
            $min = $indicators[$key]->getMin();
            
            $dbMin = $riskModelLevel->getMin();
            $dbMax = $riskModelLevel->getMax();
            
            if ($min == $dbMin && $max == $dbMax) {
                continue;
            } else {
                $this->throwException(RiskErrorConstants::RISK_M_007, 'RISK_M_007');
            }
        }
    }

    private function isEditableRiskModel($currentDate, $dbCaluculationStartDate)
    {
        $return = false;
        if ($currentDate > $dbCaluculationStartDate) {
            $return = false;
        } else {
            $return = true;
        }
        return $return;
    }

    private function currentDateValidation($current, $date)
    {
        if ($current > $date) {
            $this->throwException(RiskErrorConstants::RISK_M_002, 'RISK_M_002');
        } else {
            return true;
        }
    }

    private function startEndDateValidation($start, $end)
    {
        if ($end <= $start) {
            $this->throwException(RiskErrorConstants::RISK_M_003, 'RISK_M_003');
        } else {
            return true;
        }
    }

    private function validateEntity($entity)
    {
        $validator = $this->container->get('validator');
        $errors = $validator->validate($entity);
        
        if (count($errors) > 0) {
            $errorsString = "";
            foreach ($errors as $error) {
                
                $errorsString = $error->getMessage();
            }
            $this->throwException($errorsString, 'RISK_M_004');
        }
    }

    private function throwException($message, $code)
    {
        throw new ValidationException([
            $message
        ], $message, $code);
    }

    public function getRiskLevelByName($name)
    {
        $riskLevelRepo = $this->repositoryResolver->getRepository(RiskModelConstants::RISK_LEVELS);
        $riskLevel = $riskLevelRepo->findOneBy([
            'riskText' => $name
        ]);
        if (! $riskLevel) {
            $this->throwException($name . " " . RiskErrorConstants::RISK_M_016, 'RISK_M_016');
        }
        return $riskLevel;
    }
}