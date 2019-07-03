<?php
namespace Synapse\RiskBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\LoggerHelperService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\RiskBundle\Entity\OrgRiskvalCalcInputs;
use Synapse\RiskBundle\EntityDto\CalculatedRiskVariableDto;
use Synapse\RiskBundle\EntityDto\CalculatedSourceDto;
use Synapse\RiskBundle\EntityDto\PersonRiskScoresDto;
use Synapse\RiskBundle\EntityDto\RiskCalculationInputDto;
use Synapse\RiskBundle\EntityDto\RiskScoreDto;
use Synapse\RiskBundle\Job\RiskCalculationJob;
use Synapse\RiskBundle\Repository\OrgRiskvalCalcInputsRepository;
use Synapse\RiskBundle\Service\RiskCalculationServiceInterface;

/**
 * @DI\Service("riskcalculation_service")
 */
class RiskCalculationService extends AbstractService implements RiskCalculationServiceInterface
{

    const SERVICE_KEY = 'riskcalculation_service';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_RISK_CAL_INPUTS = 'SynapseRiskBundle:OrgRiskvalCalcInputs';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_CAL_RISSK_VAR = 'SynapseRiskBundle:OrgCalculatedRiskVariables';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_RISK_LEVEL_HISTORY = 'SynapseRiskBundle:PersonRiskLevelHistory';

    const ERR_RISKCAL_INPUT_001 = 'Risk value calculation type not vaild.';
    
    const ERR_TALKINGPOINT_INPUT_001 = 'Talking point calculation type not vaild.';
    
    const ERR_SUCCESSMARKER_INPUT_001 = 'Success marker calculation type not vaild.';
    
    const ERR_FACTORCALU_INPUT_001 = 'Factor calculation type not vaild.';

    /**
     * @var LoggerHelperService
     */
    private $loggerHelperService;

    /**
     * @var OrgRiskvalCalcInputsRepository
     */
    private $orgRiskValCalcInputsRepository;

    /**
     * @var OrganizationService
     */
    private $orgService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var Container
     */
    private $container;

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

        //Repositories
        $this->orgRiskValCalcInputsRepository = $this->repositoryResolver->getRepository(OrgRiskvalCalcInputsRepository::REPOSITORY_KEY);

        // Services
        $this->loggerHelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);
        $this->orgService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
    }

    /**
     * Create Risk Calculation Input.
     *
     * @param RiskCalculationInputDto $riskCalculationInputDto
     * @return mixed
     */
    public function createRiskCalculationInput($riskCalculationInputDto)
    {
        $logContent = $this->loggerHelperService->getLog($riskCalculationInputDto);
        $this->logger->debug(" Creating Risk Calculation Input  -  " . $logContent);

        $riskVal = $riskCalculationInputDto->getIsRiskvalCalcRequired();
        $talkingPoint = $riskCalculationInputDto->getIsTalkingPointCalcReqd();
        $successMarker = $riskCalculationInputDto->getIsSuccessMarkerCalcReqd();
        $factorCalc = $riskCalculationInputDto->getIsFactorCalcReqd();
        $this->validateInput($riskVal, self::ERR_RISKCAL_INPUT_001, 'ERR_RISKCAL_INPUT_001');
        $this->validateInput($talkingPoint, self::ERR_TALKINGPOINT_INPUT_001, 'ERR_TALKINGPOINT_INPUT_001');
        $this->validateInput($successMarker, self::ERR_SUCCESSMARKER_INPUT_001, 'ERR_SUCCESSMARKER_INPUT_001');
        $this->validateInput($factorCalc, self::ERR_FACTORCALU_INPUT_001, 'ERR_FACTORCALU_INPUT_001');        
        $orgRiskValueCalculationsInputs = new OrgRiskvalCalcInputs();
        $organization = $this->orgService->find($riskCalculationInputDto->getOrganizationId());
        $person = $this->personService->find($riskCalculationInputDto->getPersonId());
        $orgRiskValueCalculationsInputs->setOrg($organization);
        $orgRiskValueCalculationsInputs->setPerson($person);
        $orgRiskValueCalculationsInputs->setIsRiskvalCalcRequired($riskCalculationInputDto->getIsRiskvalCalcRequired());
        if(isset($talkingPoint))
        {
            $orgRiskValueCalculationsInputs->setIsTalkingPointCalcReqd($riskCalculationInputDto->getIsTalkingPointCalcReqd());
        }
        if(isset($successMarker))
        {
            $orgRiskValueCalculationsInputs->setIsSuccessMarkerCalcReqd($riskCalculationInputDto->getIsSuccessMarkerCalcReqd());
        }
        if(isset($factorCalc))
        {
            $orgRiskValueCalculationsInputs->setIsFactorCalcReqd($riskCalculationInputDto->getIsFactorCalcReqd());
        }
        $this->orgRiskValCalcInputsRepository->persist($orgRiskValueCalculationsInputs, false);
        $this->orgRiskValCalcInputsRepository->flush();
        return $riskCalculationInputDto;       
    }

    public function getCalculatedRiskVariables($personId, $start, $end, $riskmodel, $org_id)
    {
        $riskCalculationRepo = $this->repositoryResolver->getRepository(self::ORG_CAL_RISSK_VAR);
        $start = $this->getDateTimeFrom($start);
        $end = $this->getDateTimeFrom($end);
        $this->startEndDateValidation($start,$end);
        $riskVariables = $riskCalculationRepo->getCalculatedRiskVariables($personId, $start, $end, $riskmodel, $org_id);
        $calcRiskVarDto = new CalculatedRiskVariableDto();
        
        $calcRiskVarDto->setPersonId($personId);
        $CalculatedDetails = [];
        $calcRiskVarDto->setOrgId($org_id);
        if (count($riskVariables) > 0) {
            foreach ($riskVariables as $riskVariable) {
                $calcRiskVarDto->setRiskModelName($riskVariable['risk_model_name']);
                
                $detailsDto = new CalculatedSourceDto();
                $detailsDto->setRiskVariableId($riskVariable['risk_variable_id']);
                $detailsDto->setCalcBucketValue($riskVariable['calc_bucket_value']);
                $detailsDto->setCalcWeight($riskVariable['calc_weight']);
                $detailsDto->setRiskSourceValue($riskVariable['risk_source_value']);
                $detailsDto->setCreatedAt($riskVariable['created_at']);
                $CalculatedDetails[] = $detailsDto;
            }
        }
        $calcRiskVarDto->setRiskSource($CalculatedDetails);
        return $calcRiskVarDto;
    }

    public function getRiskScores($personId, $start, $end, $riskmodel)
    {
        $riskCalculationRepo = $this->repositoryResolver->getRepository(self::PERSON_RISK_LEVEL_HISTORY);
        $start = $this->getDateTimeFrom($start);
        $end = $this->getDateTimeFrom($end);
        $this->startEndDateValidation($start,$end);
        $riskScores = $riskCalculationRepo->getRiskScores($personId, $start, $end, $riskmodel);
        $CalculatedDetails = [];
        $personRisk = new PersonRiskScoresDto();
        $personRisk->setPersonId($personId);
        if (count($riskScores) > 0) {
            foreach ($riskScores as $riskScore) {
                $scoreDto = new RiskScoreDto();
                $personRisk->setRiskModelName($riskScore['risk_model_name']);
                $scoreDto->setCreatedAt($riskScore['created_at']);
                $scoreDto->setRiskScoreValue($riskScore['risk_score_value']);
                $scoreDto->setRiskLevel($riskScore['risk_level']);
                $CalculatedDetails[] = $scoreDto;
            }
        }
        $personRisk->setRiskScore($CalculatedDetails);
        return $personRisk;
    }

    public function scheduleRiskJob($riskScheduleDto)
    {
      
        $jobNumber = uniqid();
        $job = new RiskCalculationJob();
        $resque = $this->container->get('bcc_resque.resque');
        $job->args = array(
        
            'jobNumber' => $jobNumber
        );
        
        $resque->enqueue($job, true);
      
    }
    
    public function invokeRiskCalculation()
    {
        $riskCalculationRepo = $this->repositoryResolver->getRepository(self::ORG_RISK_CAL_INPUTS);
        $riskCalculationRepo->startRiskCalculation();
    }
    private function getDateTimeFrom($date)
    {
        $date = new \DateTime($date);
        return $date;
    }

    private function startEndDateValidation($start, $end)
    {
        if ($end <= $start) {
            throw new ValidationException([
                "End date cannot be earlier than start date"
            ], "End date cannot be earlier than start date", "DATE_VALIDATION");
        } else {
            return true;
        }
    }
    
    private function validateInput($riskInput, $error, $errorKey)
    {
        if(isset($riskInput))
        {
            if (! in_array($riskInput, [
                'y',
                'n'
            ])) {
                $this->logger->error("Risk Calculation - Create Risk Calculation Input - ".$error);
                throw new ValidationException([
                    $error
                ], $error, $errorKey);
            }
        }
    }
}
