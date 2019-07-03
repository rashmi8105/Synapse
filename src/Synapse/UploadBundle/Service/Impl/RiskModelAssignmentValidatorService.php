<?php
namespace Synapse\UploadBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\RiskBundle\Repository\OrgRiskGroupModelRepository;
use Synapse\RiskBundle\Repository\RiskGroupRepository;
use Synapse\RiskBundle\Repository\RiskModelMasterRepository;
use Synapse\RiskBundle\Util\Constants\RiskErrorConstants;

/**
 * Handle course upload validation
 *
 * @DI\Service("risk_model_assignment_validator_service")
 */
class RiskModelAssignmentValidatorService extends SynapseValidatorService
{

    const SERVICE_KEY = 'risk_model_assignment_validator_service';

    const CAMPUSID = 'CampusID';

    const RISKGROUPID = 'RiskGroupID';

    const MODELID = 'ModelID';

    const COMMAND = 'Commands';

    const COMMANDCLEAR = '#clear';

    const COMMANDCLEARMODEL = '#model';

    const ORGID = 'OrgID';

    /**
     * @var RepositoryResolver
     */
    public $repositoryResolver;

    /**
     * @var OrganizationRepository
     */
    protected $organizationRepository;

    /**
     * @var RiskGroupRepository
     */
    protected $riskGroupRepository;

    /**
     * @var RiskModelMasterRepository
     */
    protected $riskModelMasterRepository;

    /**
     * @var OrgRiskGroupModelRepository
     */
    protected $orgRiskGroupModelRepository;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            })
     */
    public function __construct($repositoryResolver, $logger)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->errors = [];
        $this->repositoryResolver = $repositoryResolver;
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->riskGroupRepository = $this->repositoryResolver->getRepository(RiskGroupRepository::REPOSITORY_KEY);
        $this->riskModelMasterRepository = $this->repositoryResolver->getRepository(RiskModelMasterRepository::REPOSITORY_KEY);
        $this->orgRiskGroupModelRepository = $this->repositoryResolver->getRepository(OrgRiskGroupModelRepository::REPOSITORY_KEY);
    }

    public function isValueEmpty($value, $key)
    {
        $this->logger->info(">>>>>>>>>>>>> isValueEmpty{$value}{$key}");
        if (empty($value)) {
            $this->errors[] = [
                'name' => $key,
                'value' => '',
                'errors' => [
                    "Missing a required field."
                ]
            ];
            return false;
        } else {
            return true;
        }
    }

    /*
     * Validate column name
     * @param string $name
     * @param array $data
     */
    public function validateColumn($name, $data)
    {
        $items = [
            self::ORGID,
            self::RISKGROUPID,
            self::MODELID
        ];

        if (! in_array($name, $items)) {
            $this->errors[] = 'is not a valid column';
        }

        if (count($this->errors)) {
            return false;
        }

        return true;
    }

    /**
     * Validate uploaded data for organization risk group model
     *
     * @param array $organizationRiskGroupModelData
     * @return bool
     */
    public function validateModelData($organizationRiskGroupModelData)
    {
        $organization = $this->organizationRepository->find($organizationRiskGroupModelData['orgid']);
        $this->checkData($organization, 'OrgID');
        $riskGroup = $this->riskGroupRepository->find($organizationRiskGroupModelData['riskgroupid']);
        $this->checkData($riskGroup, 'RiskGroupID');

        if (isset($organizationRiskGroupModelData['modelid']) && $organizationRiskGroupModelData['commands'] != '#clear' && $organizationRiskGroupModelData['commands'] != '#model') {
            $orgRiskModel = $this->orgRiskGroupModelRepository->findBy([
                'org' => $organization,
                'riskModel' => $organizationRiskGroupModelData['modelid'],
                'riskGroup' => $organizationRiskGroupModelData['riskgroupid']
            ]);
            if ($orgRiskModel) {
                $key = 'RISK_M_011';
                $errorMsg = RiskErrorConstants::RISK_M_011;
                $this->setError($key, $errorMsg);
            }
            $riskModel = $this->riskModelMasterRepository->find($organizationRiskGroupModelData['modelid']);

            if (!empty($organizationRiskGroupModelData['modelid'])) {
                $this->checkData($riskModel, 'ModelID');
            }
            if ($riskModel) {
                $currentDate = new \DateTime();
                $calculationEndDate = $riskModel->getCalculationEndDate();

                if (($calculationEndDate) && ($calculationEndDate < $currentDate)) {
                    $key = 'RISK_M_009';
                    $errorMsg = RiskErrorConstants::RISK_M_009;
                    $this->setError($key, $errorMsg);
                }

                $modelState = $riskModel->getModelState();
                if ($modelState == 'Archived') {
                    $key = 'RISK_M_010';
                    $errorMsg = RiskErrorConstants::RISK_M_010;
                    $this->setError($key, $errorMsg);
                }
            }
        }

        if (count($this->errors)) {
            return false;
        }
        return true;
    }

    public function validateModelDate($riskModel)
    {
        $currentDate = new \DateTime();
        $currentDate->setTime(0, 0, 0);
        $calculationstartDate = $riskModel->getCalculationStartDate();

        if (($calculationstartDate) && ($calculationstartDate < $currentDate)) {
            $this->logger->debug(">>>>>>>>>>>>> Date Validation Failed for Model " . $riskModel->getId());
            throw new ValidationException([
                RiskErrorConstants::RISK_M_015
            ], RiskErrorConstants::RISK_M_015, 'ERRRM015');
        } else {
            $this->logger->debug(">>>>>>>>>>>>> Date Validation Success for Model " . $riskModel->getId());
            $this->logger->debug($calculationstartDate->format('Y-m-d H:i:s'));
            $this->logger->debug($currentDate->format('Y-m-d H:i:s'));
            return true;
        }
    }

    private function checkData($data, $key)
    {
        if (! $data) {
            $errorMsg = $key . " does not match existing values";
            $this->setError($key, $errorMsg);
        }
    }

    private function setError($key, $errorMsg)
    {
        $this->errors[] = [
            'name' => $key,
            'value' => '',
            'errors' => [
                $errorMsg
            ]
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
