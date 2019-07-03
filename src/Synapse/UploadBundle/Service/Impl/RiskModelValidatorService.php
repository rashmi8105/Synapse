<?php
namespace Synapse\UploadBundle\Service\Impl;

//use Synapse\UploadBundle\Service\UploadValidatorServiceInterface;
use Synapse\RiskBundle\Util\Constants\RiskErrorConstants;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\RepositoryResolver;

/**
 * Handle course upload validation
 *
 * @DI\Service("risk_model_validator_service")
 */
class RiskModelValidatorService extends SynapseValidatorService
{

    const SERVICE_KEY = 'risk_model_validator_service';

    const MODELID = 'ModelID';

    const RISKVARNAME = 'RiskVarName';

    const WEIGHT = 'Weight';

    const COMMANDS = 'Commands';

    const CLEAR = '#clear';

    public $repositoryResolver;
    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            })
     */
    public function __construct($repositoryResolver, $logger, $profileService, $validator)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->errors = [];
        $this->repositoryResolver = $repositoryResolver;

    }

    public function isValueEmpty($value, $key)
    {
        $this->logger->info(">>>>>>>>>>>>> isValueEmpty{$value}{$key}");
        if (empty($value)) {
            $this->errors[] = [
            'name' => $key,
            'value' => '',
            'errors' => [RiskErrorConstants::RISK_M_012]
            ];
            return false;
        } else {
        return true;
        }
    }

    public function validateColumn($name, $data)
    {
        $items = [
            self::MODELID,
            self::RISKVARNAME,
            self::WEIGHT
        ];

        if (!in_array($name, $items)) {
            $this->errors[] = 'is not a valid column';
        }

        if (count($this->errors)) {
            return false;
        }

        return true;
    }

    public function validateModelData($data) {
        $this->logger->info(">>>>>>>>>>>>> data validation started");

        $riskVariableRepository = $this->repositoryResolver->getRepository('SynapseRiskBundle:RiskVariable');
        $riskModelRepository = $this->repositoryResolver->getRepository('SynapseRiskBundle:RiskModelMaster');
        $riskModelWeightRepository = $this->repositoryResolver->getRepository('SynapseRiskBundle:RiskModelWeights');


        $this->logger->info(">>>>>>>>>>>>> checking if model id exist in db");
        $riskModel = $riskModelRepository->findOneById($data[strtolower(self::MODELID)]);
        $this->checkData($riskModel, self::MODELID);
        $this->logger->info(">>>>>>>>>>>>> checking if variable exist in db");
        $riskVariable = $riskVariableRepository->findOneByRiskBVariable($data[strtolower(self::RISKVARNAME)]);
        $this->checkData($riskVariable, self::RISKVARNAME);
        /*
         * If all value exist
         */
        if($riskModel && $riskVariable && (trim($data[strtolower(self::COMMANDS)]) != self::CLEAR))
        {
        $this->logger->info(">>>>>>>>>>>>> checking exist in risk model_variable_weight");
        $variableId = $riskVariable->getId();

        $riskModelWeight = $riskModelWeightRepository->findBy([
                'riskModel' => $data[strtolower(self::MODELID)],
                'riskVariable' => $variableId
            ]);

        if($riskModelWeight){
                $key= 'RISK_M_013';
                $errorMsg = RiskErrorConstants::RISK_M_013;
                $this->setError($key, $errorMsg);
            }
        }
        if (count($this->errors)) {
            return false;
        }
        return true;
    }


    private function checkData($data, $key) {
        if(!$data){
            $errorMsg = $key." does not exist.";
            $this->setError($key, $errorMsg);
        }
    }

    private function setError($key, $errorMsg){
        $this->errors[] = [
            'name' => $key,
            'value' => '',
            'errors' => [$errorMsg]
            ];

        return $this->errors;
    }


    public function getErrors()
    {
        $errors = $this->errors;
        $this->errors = [];

        return $errors;
    }
}
