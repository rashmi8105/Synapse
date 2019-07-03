<?php
namespace Synapse\RiskBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\RiskBundle\Service\RiskModelListServiceInterface;
use Synapse\RiskBundle\EntityDto\RiskModelListDto;
use Synapse\RiskBundle\EntityDto\RiskModelDto;
use Synapse\RiskBundle\EntityDto\RiskIndicatorsDto;
use Synapse\RiskBundle\Repository\RiskModelMasterRepository;
use Synapse\RiskBundle\EntityDto\RiskModelAssignmentsDto;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\RiskBundle\Util\Constants\RiskErrorConstants;
use Synapse\RiskBundle\Util\Constants\RiskModelConstants;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * @DI\Service("riskmodellist_service")
 */
class RiskModelListService extends AbstractService implements RiskModelListServiceInterface
{

    const SERVICE_KEY = 'riskmodellist_service';

    const TOTAL_COUNT = 'total_count';

    const TOTAL_ARCHIVE_COUNT = 'total_archived_count';

    const ARCHIVED = 'Archived';

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

    public function getModelList($status)
    {
        $this->logger->debug(" Get Model List by Status  " . $status);
        $modelsData = [];
        $this->riskModelMasterRepository = $this->repositoryResolver->getRepository(RiskModelConstants::RISK_MODEL_MASTER);
        $statusCount = $this->getStatusCount();
        $modelsData[self::TOTAL_COUNT] = $statusCount[self::TOTAL_COUNT];
        $modelsData[self::TOTAL_ARCHIVE_COUNT] = $statusCount[self::TOTAL_ARCHIVE_COUNT];
        $models = $this->riskModelMasterRepository->getRiskModels($status);
        $modelList = [];
        foreach ($models as $model) {
            $modelListDto = new RiskModelListDto();
            $modelListDto->setId($model['id']);
            $modelListDto->setModelName($model['model_name']);
            $modelListDto->setVariablesCount($model['variables_count']);
            $modelListDto->setCampusesCount($model['campuses_count']);
            $modelList[] = $modelListDto;
        }
        $modelsData['risk_models'] = $modelList;
        $this->logger->info(" Get Model List By Status");
        return $modelsData;
    }

    private function getStatusCount()
    {
        $modalState[self::TOTAL_COUNT] = 0;
        $modalState[self::TOTAL_ARCHIVE_COUNT] = 0;
        $this->riskModelMasterRepository = $this->repositoryResolver->getRepository(RiskModelConstants::RISK_MODEL_MASTER);
        $statusCounts = $this->riskModelMasterRepository->getCountByStatus();
        if ($statusCounts) {
            $status = array_column($statusCounts, 'status');
            $statusCount = array_column($statusCounts, 'status_count');
            $allStatus = array_combine($status, $statusCount);
            $modalState[self::TOTAL_ARCHIVE_COUNT] = (isset($allStatus[self::ARCHIVED])) ? $allStatus[self::ARCHIVED] : 0;
            $modalState[self::TOTAL_COUNT] = array_sum(array_values($allStatus)) - $modalState[self::TOTAL_ARCHIVE_COUNT];
        }
        return $modalState;
    }

    /**
     * Function is being use by job
     */
    public function ArchiveRiskModel($date)
    {
        $logger = $this->container->get('logger');
        $riskModelMasterRepo = $this->repositoryResolver->getRepository(RiskModelConstants::RISK_MODEL_MASTER);
        $markArchiveData = $riskModelMasterRepo->getPassedModel($date);
        
        if ($markArchiveData) {
            $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> Model found to be marked as Archived.");
            foreach ($markArchiveData as $markArchive) {
                $modelData = $riskModelMasterRepo->find($markArchive['id']);
                $modelData->setModelState('Archived');
                $riskModelMasterRepo->flush();
                $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>> Model Id - " . $markArchive['id'] . " marked as Archived. \n");
            }
        } else {
            $logger->info("No Model found to be marked as Archived");
        }
    }

    public function exportByModelId($id)
    {
        $this->logger->debug(" exportByModelId  " . $id);
        $riskModelWeightRepository = $this->repositoryResolver->getRepository('SynapseRiskBundle:RiskModelWeights');
        $riskModels = $riskModelWeightRepository->getRiskModelWeightByModel($id);
        
        $currentDate = time();
        $fh = @fopen(UploadConstant::DATA_SLASH . RiskModelConstants::RISK_DIR . "{$currentDate}-view-risk-model-{$id}.csv", 'w');
        
        $rows = [];
        $headers = [
            'ModelID',
            'RiskVarName',
            'Weight'
        ];
        fputcsv($fh, $headers);
        if (count($riskModels) > 0) {
            foreach ($riskModels as $riskModel) {
                fputcsv($fh, $riskModel);
            }
        }
        fclose($fh);
        $this->logger->info(" export By Model Id ");
		return RiskModelConstants::RISK_DIR . "{$currentDate}-view-risk-model-{$id}.csv";
    }

    public function getModelJson($id)
    {
        $this->logger->debug(" Get Model JSON " . $id);
        $riskIndicators = [];
        $this->riskModelMasterRepository = $this->repositoryResolver->getRepository(RiskModelConstants::RISK_MODEL_MASTER);
        $model = $this->riskModelMasterRepository->getModel($id);
        if ($model) {
            
            $modelListDto = new RiskModelDto();
            $modelListDto->setId($model[0]['id']);
            $modelListDto->setRiskModelName($model[0]['name']);
            $modelListDto->setCalculationStartDate($model[0]['calculationStartDate']);
            $modelListDto->setCalculationStopDate($model[0]['calculationEndDate']);
            $modelListDto->setEnrollmentEndDate($model[0]['enrollmentDate']);
            $modelListDto->setModelState($model[0]['modelState']);
            foreach ($model as $mData) {
                $riskIndicator = new RiskIndicatorsDto();
                $riskIndicator->setName($mData['riskText']);
                $riskIndicator->setMin($mData['min']);
                $riskIndicator->setMax($mData['max']);
                $riskIndicators[] = $riskIndicator;
            }
            $modelListDto->setRiskIndicators($riskIndicators);
        } else {
            $this->logger->error("Risk Bundle - Get Model JSON - ".RiskErrorConstants::RISK_M_005);
            throw new ValidationException([
                RiskErrorConstants::RISK_M_005
            ], RiskErrorConstants::RISK_M_005, 'RISK_M_005');
        }
        $this->logger->info(" Get Model JSON ");
        return $modelListDto;
    }

    public function getModel($id, $viewmode = 'json')
    {
        $this->logger->debug(" Get Model By Id  " . $id . "View Mode" . $viewmode);
        $viewmode = strtolower($viewmode);
        
        if ($viewmode == 'csv') {
            $response = $this->exportByModelId($id);
        } else {
            $response = $this->getModelJson($id);
        }
        $this->logger->info(" Get Model By Id and JSON  ");
        return $response;
    }

    public function getModelAssignments($filter, $viewmode = 'json')
    {
        $this->logger->debug(" Get Model Assignments By filter " . $filter . "View Mode" . $viewmode);
        $viewmode = strtolower($viewmode);
        
        if (strtolower($viewmode) == 'csv') {
            $response = $this->getModelAssignmentsCsv();
        } else {
            $response = $this->getModelAssignmentsJson($filter);
        }
        $this->logger->debug(" Get Model Assignments");
        return $response;
    }

    private function getModelAssignmentsCsv()
    {
        $riskModelMasterRepo = $this->repositoryResolver->getRepository(RiskModelConstants::RISK_MODEL_MASTER);
        $modelData = $riskModelMasterRepo->getModelAssignments('all');
        $currentDate = date("Y-m-d");
        $fileName = "RiskAdministrationExport.csv";
        $fh = @fopen(UploadConstant::DATA_SLASH . RiskModelConstants::RISK_DIR . $fileName, 'w');
        $headers = [
            'CampusID',
            'CampusName',
            'RiskGroupID',
            'RiskGroupName',
            'ModelID',
            'ModelName',
            'EnrollmentEndDate',
            'EffectiveStartDate',
            'EffectiveEndDate'
        ];
        fputcsv($fh, $headers);
        if (count($modelData) > 0) {
            foreach ($modelData as $riskModel) {
                fputcsv($fh, $riskModel);
            }
        }
        fclose($fh);
        return RiskModelConstants::RISK_AMAZON_URL . $fileName;
    }

    public function getModelAssignmentsJson($filter)
    {
        $this->logger->debug(" Get Model Assignments JSON " . $filter);
        $modelAssignmentData = [];
        $riskModelMasterRepo = $this->repositoryResolver->getRepository(RiskModelConstants::RISK_MODEL_MASTER);
        $modelData = $riskModelMasterRepo->getModelAssignments($filter);
        if ($modelData) {
            $modelAssignmentData['total_assigned_models_count'] = count($modelData);
            $assignmentData = $this->getAssignedData($modelData);
            $modelAssignmentData['risk_model_assignments'] = $assignmentData;
        }
        $this->logger->info(" Get Model Assignments JSON ");
        return $modelAssignmentData;
    }

    private function getAssignedData($modelData)
    {
        $assignmentData = [];
        foreach ($modelData as $data) {
            $modelAssignments = new RiskModelAssignmentsDto();
            $modelAssignments->setCampusId($data['id']);
            $modelAssignments->setCampusName($data['campus_name']);
            $modelAssignments->setRiskGroupId($data['risk_group_id']);
            $modelAssignments->setRiskGroupName($data['risk_group_name']);
            $modelAssignments->setRiskModelId($data['risk_model_id']);
            $modelAssignments->setRiskModelName($data['risk_model_name']);
            $sDate = ($data['calculation_start_date']) ? new \DateTime($data['calculation_start_date']) : null;
            $modelAssignments->setCalculationStartDate($sDate);
            $eDate = ($data['calculation_end_date']) ? new \DateTime($data['calculation_end_date']) : null;
            $modelAssignments->setCalculationStopDate($eDate);
            $enDate = ($data['enrollment_date']) ? new \DateTime($data['enrollment_date']) : null;
            $modelAssignments->setEnrollmentEndDate($enDate);
            $assignmentData[] = $modelAssignments;
        }
        
        return $assignmentData;
    }
}