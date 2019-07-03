<?php
namespace Synapse\RiskBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\RiskBundle\Service\RiskVariableServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\Helper;
use Synapse\RiskBundle\EntityDto\RiskVariablesResponseDto;
use Synapse\RiskBundle\EntityDto\RiskVariableResponseDto;
use Synapse\RiskBundle\EntityDto\RiskVariablesListDto;
use Synapse\RiskBundle\EntityDto\BucketDetailsListDto;
use Synapse\RiskBundle\Entity\RiskVariable;
use Synapse\RiskBundle\Repository\RiskVariableRepository;
use Synapse\RiskBundle\Repository\RiskVariableRangeRepository;
use Synapse\RiskBundle\EntityDto\RiskVariableDto;
use Synapse\RiskBundle\EntityDto\RiskSourceIdsDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\RiskBundle\Util\RiskVariableHelper;
use JMS\Serializer\Tests\Fixtures\Publisher;
use Synapse\CoreBundle\Util\Constants\ProfileConstant;
use Synapse\CoreBundle\Util\Constants\OrgProfileConstant;
use Synapse\RiskBundle\Entity\RiskVariableRange;
use Synapse\RiskBundle\Entity\RiskVariableCategory;
use Synapse\RiskBundle\Util\Constants\RiskVariableConstants;
use Synapse\RiskBundle\EntityDto\BucketDetailsDto;
use Synapse\RiskBundle\EntityDto\SourceIdDto;
use Synapse\RiskBundle\EntityDto\CalculatedDataDto;
use Synapse\UploadBundle\Job\CreateRiskVariableListJob;
use JMS\Serializer\Serializer;
use Synapse\SurveyBundle\Repository\FactorRepository;
use Synapse\CoreBundle\Repository\SurveyRepository;

/**
 * @DI\Service("riskvariable_service")
 */
class RiskVariableService extends AbstractService implements RiskVariableServiceInterface
{

    const SERVICE_KEY = 'riskvariable_service';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBI_CONFIG_REPO = "SynapseCoreBundle:EbiConfig";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const RISK_VARIABLE_REPO = "SynapseRiskBundle:RiskVariable";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBI_METADATA_REPO = "SynapseCoreBundle:EbiMetadata";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const SURVEY_REPO = "SynapseCoreBundle:Survey";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const FACTOR_REPO = "SynapseSurveyBundle:Factor";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORGANIZATION_REPO = "SynapseCoreBundle:Organization";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBI_QUESTION = "SynapseCoreBundle:EbiQuestion";

    const CACHE_TTL = 1800;

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const RISK_VARIABLE_RANGE_REPO = "SynapseRiskBundle:RiskVariableRange";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const RISK_VARIABLE_CAT_REPO = "SynapseRiskBundle:RiskVariableCategory";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const RISK_MODEL_WEIGHTS_REPO = "SynapseRiskBundle:RiskModelWeights";

    /**
     * @var FactorRepository
     */
    private $factorRepository;

    /**
     * @var SurveyRepository
     */
    private $surveyRepository;

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
        $this->factorRepository = $this->repositoryResolver->getRepository('SynapseSurveyBundle:Factor');
        $this->surveyRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Survey');
    }

    public function getResourceTypes()
    {
        $cacheKey = 'resource_types';
        $cache = $this->container->get('synapse_redis_cache');
        $sourceType = $cache->fetch($cacheKey);

        if (! $sourceType) {
            $sourceType[RiskVariableConstants::SOURCE_TYPE] = [];
            $this->ebiConfigRepository = $this->repositoryResolver->getRepository(self::EBI_CONFIG_REPO);
            $ebiKey = "Risk_Source_Types";
            $resourceType = $this->ebiConfigRepository->findOneByKey($ebiKey);
            if ($resourceType) {
                $sourceType['source_type'] = $resourceType->getValue();
                $cache->save($cacheKey, $sourceType, self::CACHE_TTL);
            } else {
                $sourceType['source_type'] = [];
            }
        }
        $this->logger->info(" Get Resource types");
        return $sourceType;
    }

    public function getRiskVariables($status)
    {
        $this->logger->debug(" Get Risk Variables " . $status);
        $this->riskVariableRepository = $this->repositoryResolver->getRepository(self::RISK_VARIABLE_REPO);
        $isArchived_val = (trim($status) == 'archived') ? 1 : 0;
        $riskVariables = $this->riskVariableRepository->getAllRiskVariables($isArchived_val);
        $riskVariablesResp = new RiskVariablesResponseDto();
        if ($isArchived_val) {
            $activeCountArr = $this->riskVariableRepository->getStatusCount(0);
            $activeCount = $activeCountArr[0]['cnt'];
            $archivedCount = count($riskVariables);
        } else {
            $archivedCountArr = $this->riskVariableRepository->getStatusCount(1);
            $archivedCount = $archivedCountArr[0]['cnt'];
            $activeCount = count($riskVariables);
        }

        $totalCount = $activeCount + $archivedCount;
        $riskVariablesResp->setTotalCount($totalCount);
        $riskVariablesResp->setTotalArchivedCount($archivedCount);
        $riskVariablesArr = [];
        foreach ($riskVariables as $riskVariable) {
            $riskVariableList = new RiskVariablesListDto();
            $riskVariableList->setId($riskVariable['rvid']);
            $riskVariableList->setRiskVariableName($riskVariable['risk_b_variable']);
            $riskVariableList->setCampusId($riskVariable['campus_id']);
            $riskVariableList->setSourceType($riskVariable['source']);
            $riskVariableList->setSurveyId($riskVariable[RiskVariableConstants::SURVEY_ID]);

            $riskVariableList = $this->setSourceId($riskVariableList, $riskVariable);

            $isAssign = ($riskVariable['risk_model_id']) ? true : false;
            $riskVariableList->setIsAssigned($isAssign);

            $riskVariablesArr[] = $riskVariableList;
        }
        $riskVariablesResp->setRiskVariables($riskVariablesArr);
        $this->logger->info(" Get Risk Variables by Status");
        return $riskVariablesResp;
    }

    private function setSourceId($riskVariableList, $riskVariable)
    {
        switch (strtolower($riskVariable['source'])) {
            case RiskVariableConstants::PROFILE:
                $riskVariableList->setSourceId($riskVariable['ebi_metadata_id']);
                break;
            case RiskVariableConstants::SURVEYQUESTION:
                $riskVariableList->setSourceId($riskVariable['survey_questions_id']);
                break;
            case RiskVariableConstants::SURVEYFACTOR:
                $riskVariableList->setSourceId($riskVariable['factor_id']);
                break;

            case RiskVariableConstants::ISP:
                $riskVariableList->setSourceId($riskVariable['org_metadata_id']);
                break;
            case RiskVariableConstants::ISQ:
                $riskVariableList->setSourceId($riskVariable['org_question_id']);
                break;

            case RiskVariableConstants::QUESTIONBANK:
                $riskVariableList->setSourceId($riskVariable['ebi_question_id']);
                break;
            default:
                throw new ValidationException([
                    'Invalid Source'
                ], 'Invalid Source', 'ERR_SOURCE');
        }

        return $riskVariableList;
    }

    public function getRiskVariable($id)
    {
        $this->logger->debug(" Get Risk Variable by Id " . $id);
        $this->riskVariableRepository = $this->repositoryResolver->getRepository(self::RISK_VARIABLE_REPO);
        $riskVariableDetails = $this->riskVariableRepository->findOneById($id);

        if (! $riskVariableDetails) {
            $this->logger->error("Risk Bundle - Get Risk Variable - ".RiskVariableConstants::ERR_RISK_RV_002);
            throw new ValidationException([
                RiskVariableConstants::ERR_RISK_RV_002
            ], RiskVariableConstants::ERR_RISK_RV_002, RiskVariableConstants::ERR_RISK_RV_002);
        } else {
            $riskVariableResp = new RiskVariableResponseDto();
            $riskVariableResp->setId($riskVariableDetails->getId());
            $riskVariableResp->setRiskVariableName($riskVariableDetails->getRiskBVariable());
            $riskVariableResp->setSourceType($riskVariableDetails->getSource());
            if ($riskVariableDetails->getVariableType() == 'continuous') {
                $riskVariableResp->setIsContinuous(true);
                $this->riskVariableRangeRepository = $this->repositoryResolver->getRepository(self::RISK_VARIABLE_RANGE_REPO);
                $riskVariableRange = $this->riskVariableRangeRepository->findByRiskVariable($riskVariableDetails->getId());
                $riskVariableRangeArr = [];
                $tmpArrBucketFound = [];
                foreach ($riskVariableRange as $riskVariable) {
                    $bucketDetailsResp = new BucketDetailsListDto();
                    $bucketDetailsResp->setBucketValue($riskVariable->getBucketValue());
                    $bucketDetailsResp->setMin($riskVariable->getMin());
                    $bucketDetailsResp->setMax($riskVariable->getMax());
                    $riskVariableRangeArr[] = $bucketDetailsResp;
                    array_push($tmpArrBucketFound, $riskVariable->getBucketValue());
                }
                $toAddBuckets = array_diff([
                    1,
                    2,
                    3,
                    4,
                    5,
                    6,
                    7
                ], $tmpArrBucketFound);
                for ($i = 1; $i <= 7; $i ++) {
                    if (in_array($i, $toAddBuckets)) {
                        $bucketDetailsResp = new BucketDetailsListDto();
                        $bucketDetailsResp->setBucketValue($toAddBuckets[$i - 1]);
                        $bucketDetailsResp->setMin('');
                        $bucketDetailsResp->setMax('');
                        $riskVariableRangeArr[] = $bucketDetailsResp;
                    }
                }
            } else {
                $riskVariableResp->setIsContinuous(false);
                $this->riskVariableCatRepository = $this->repositoryResolver->getRepository(self::RISK_VARIABLE_CAT_REPO);
                $riskVariableCat = $this->riskVariableRepository->getRiskCatValues($riskVariableDetails->getId());
                $riskVariableRangeArr = [];
                $tmpArrBucketFound = [];
                foreach ($riskVariableCat as $riskVariable) {
                    $bucketDetailsResp = new BucketDetailsListDto();
                    $bucketDetailsResp->setBucketValue($riskVariable['bucket_value']);
                    $bucketDetailsResp->setOptionValue(explode(';', $riskVariable['option_value_str']));
                    $riskVariableRangeArr[] = $bucketDetailsResp;
                    array_push($tmpArrBucketFound, $riskVariable['bucket_value']);
                }
                $toAddBuckets = array_diff([
                    1,
                    2,
                    3,
                    4,
                    5,
                    6,
                    7
                ], $tmpArrBucketFound);
                for ($i = 1; $i <= 7; $i ++) {
                    if (in_array($i, $toAddBuckets)) {
                        $bucketDetailsResp = new BucketDetailsListDto();
                        $bucketDetailsResp->setBucketValue($toAddBuckets[$i - 1]);
                        $bucketDetailsResp->setOptionValue('');
                        $riskVariableRangeArr[] = $bucketDetailsResp;
                    }
                }
            }
            $riskVariableResp->setBucketDetails($riskVariableRangeArr);

            $sourceIdResp = $this->getSourceID($riskVariableDetails);

            $riskVariableResp->setSourceId($sourceIdResp);

            $riskVariableResp->setIsCalculated($riskVariableDetails->getIsCalculated());

            $calculatedDataResp = new CalculatedDataDto();
            $calculatedDataResp->setCalculationStartDate($riskVariableDetails->getCalculationStartDate());
            $calculatedDataResp->setCalculationStopDate($riskVariableDetails->getCalculationEndDate());
            $calculatedDataResp->setCalculationType($riskVariableDetails->getCalcType());

            $riskVariableResp->setCalculatedData($calculatedDataResp);
        }
        $this->logger->info(" Get Risk Variable by Id ");
        return $riskVariableResp;
    }

    private function getSourceID($riskVariableDetails)
    {
        $sourceIdResp = new SourceIdDto();
        switch (strtolower($riskVariableDetails->getSource())) {
            case RiskVariableConstants::PROFILE:
                $sourceIdResp->setEbiProfileId($riskVariableDetails->getEbiMetadata()
                    ->getId());
                break;
            case RiskVariableConstants::SURVEYQUESTION:
                $sourceIdResp->setSurveyId($riskVariableDetails->getSurvey()
                    ->getId());
                $sourceIdResp->setQuestionId($riskVariableDetails->getSurveyQuestions()
                    ->getId());
                break;
            case RiskVariableConstants::SURVEYFACTOR:
                $sourceIdResp->setSurveyId($riskVariableDetails->getSurvey()
                    ->getId());
                $sourceIdResp->setFactorId($riskVariableDetails->getFactor()
                    ->getId());
                break;

            case RiskVariableConstants::ISP:
                $sourceIdResp->setIspId($riskVariableDetails->getOrgMetadata()
                    ->getId());
                $sourceIdResp->setCampusId($riskVariableDetails->getOrg()
                    ->getcampusId());
                break;
            case RiskVariableConstants::ISQ:
                $sourceIdResp->setIsq($riskVariableDetails->getOrgQuestion()
                    ->getId());
                $sourceIdResp->setCampusId($riskVariableDetails->getOrg()
                    ->getcampusId());
                break;

            case RiskVariableConstants::QUESTIONBANK:
                $sourceIdResp->setQuestionBankId($riskVariableDetails->getEbiQuestion()
                    ->getId());
                break;
            default:
                continue;
        }

        return $sourceIdResp;
    }

    public function create(RiskVariableDto $riskVariableDto, $type)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($riskVariableDto);
        $this->logger->debug(" Creating Risk Variable  -  " . $logContent . "Type" . $type);

        $riskVariableRepository = $this->repositoryResolver->getRepository(self::RISK_VARIABLE_REPO);
        if ($type == 'insert') {
            $riskVariable = new RiskVariable();
            $riskVariable->setRiskBVariable($riskVariableDto->getRiskVariableName());

            $riskVariable->setVariableType(RiskVariableHelper::getVaiableType($riskVariableDto));

            $riskVariable->setIsCalculated($riskVariableDto->getIsCalculated());

            $riskVariable->setIsArchived(false);
            $this->setSourceType($riskVariableDto, $riskVariable);
            $this->setCalculationData($riskVariableDto, $riskVariable);
            RiskVariableHelper::validateCalType($riskVariableDto);
            $validator = $this->container->get('validator');
            $errors = $validator->validate($riskVariable);
            RiskVariableHelper::validateEntity($errors);
            $riskVariableRepository->persist($riskVariable, false);
        } else {
            $riskVariable = $riskVariableRepository->find($riskVariableDto->getId());
            if (! $riskVariable) {
                $this->logger->error("Risk Bundle - RiskVariableConstants::ERR_RISK_RV_002: " . RiskVariableConstants::ERR_RISK_RV_002);
                throw new ValidationException([
                    RiskVariableConstants::ERR_RISK_RV_002
                ], RiskVariableConstants::ERR_RISK_RV_002, RiskVariableConstants::ERR_RISK_RV_002);
            } else {
                if ($this->isVariableAssigned($riskVariableDto->getId())) {
                    $this->logger->error("Risk Bundle - RiskVariableConstants::ERR_RISK_RV_001 : ".RiskVariableConstants::ERR_RISK_RV_001);
                    throw new ValidationException([
                        'Variable is assigned to model.'
                    ], RiskVariableConstants::ERR_RISK_RV_001, 'ERR_RISK_RV_001');
                }
                $riskVariable->setVariableType(RiskVariableHelper::getVaiableType($riskVariableDto));

                $validator = $this->container->get('validator');
                $errors = $validator->validate($riskVariable);
                RiskVariableHelper::validateEntity($errors);

                $riskVariable->setIsCalculated($riskVariableDto->getIsCalculated());
                $riskVariable->setIsArchived(false);
                $this->setSourceType($riskVariableDto, $riskVariable);
                $this->setCalculationData($riskVariableDto, $riskVariable);

                $this->removeRiskVariableCategory($riskVariable);
                $this->removeRiskVariableRange($riskVariable);
            }
        }

        $riskVariableRepository->flush();
        if ($riskVariableDto->getIsContinuous()) {
            $this->setRiskVariableRange($riskVariableDto, $riskVariable);
        } else {
            $this->setRiskVariableCategory($riskVariableDto, $riskVariable);
        }
        $riskVariableRepository->flush();
        $riskVariableDto->setId($riskVariable->getId());
        $this->startUpdateRiskVariableList();
        $this->logger->info(" Created Risk Variable");
        return $riskVariableDto;
    }

    public function getRiskSourceIds($type)
    {
        $this->logger->debug(" Get Risk Source Ids by Type" . $type);
        $data = array();
        $type = strtolower($type);
        $cacheKey = 'resource_ids-' . $type;
        $cache = $this->container->get('synapse_redis_cache');
        // checking and cached data

        $data = $cache->fetch($cacheKey);

        if (! $data) {
            $sourceIds = $this->getDataSourceType($type);
            $data['source_ids'] = $sourceIds;
            if ($sourceIds) {
                $cache->save($cacheKey, $data, self::CACHE_TTL);
            } else {
                // dont cache
            }
        }
        $this->logger->info(" Get Risk Source Ids");
        return $data;
    }

    private function getDataSourceType($type)
    {
        $type = strtolower($type);
        switch ($type) {
            case RiskVariableConstants::PROFILE:
                $data = $this->getProfileIds($type);
                break;
            case RiskVariableConstants::SURVEYQUESTION:
                $data = $this->getSurveyQuestion($type);
                break;
            case RiskVariableConstants::SURVEYFACTOR:
                $data = $this->getSurveyFactor($type);
                break;
            case RiskVariableConstants::ISP:
                $data = $this->getIspIsqQuestion($type);
                break;
            case RiskVariableConstants::ISQ:
                $data = $this->getIspIsqQuestion($type);
                break;
            case RiskVariableConstants::QUESTIONBANK:
                $data = $this->getQuestionbankIds($type);
                break;
            default:
                throw new ValidationException([
                    RiskVariableConstants::ERR_RISK_RV_003
                ], RiskVariableConstants::ERR_RISK_RV_003, 'ERRRISK003');
        }
        return $data;
    }

    private function getProfileIds($type)
    {
        $profileData = array();
        $profileRepo = $this->repositoryResolver->getRepository(self::EBI_METADATA_REPO);
        $ebiData = $profileRepo->getAllProfileIds();
        $metaKeyIds = "";
        if ($ebiData) {
            foreach ($ebiData as $data){
                $metaKeyIds.= $data['meta_key'] .'('.$data['id'].')'.',';
             }
            $riskSource = new RiskSourceIdsDto();
            $riskSource->setSourceType($type);
            $riskSource->setSurveyId('');
            $riskSource->setIds(rtrim($metaKeyIds,','));
            $profileData[] = $riskSource;
        }
        return $profileData;
    }

    private function getSurveyQuestion($type)
    {
        $surveyRepo = $this->repositoryResolver->getRepository(self::SURVEY_REPO);
        $surveyQid = $surveyRepo->getSurveyQuestion();
        $sourceIds = [];
        if ($surveyQid) {
            $sourceIds = $this->getSourceIds($surveyQid, $type);
        }
        return $sourceIds;
    }

    private function getSourceIds($Quest, $type)
    {
        $sourceType = ($type == 'surveyfactor') ? 'factor' : 'questions';
        $sourceIds = [];
        foreach ($Quest as $sType) {
            $riskSource = new RiskSourceIdsDto();
            $riskSource->setSourceType($type);
            $riskSource->setSurveyId($sType[RiskVariableConstants::SURVEY_ID]);

            $ids = ($sType[$sourceType]) ? $sType[$sourceType] : '';
            $riskSource->setIds($ids);
            $sourceIds[] = $riskSource;
        }
        return $sourceIds;
    }

    private function getSurveyFactor($type)
    {
        $factorRepo = $this->repositoryResolver->getRepository(self::SURVEY_REPO);
        $surveyQid = $factorRepo->getSurveyFactor();
        $sourceIds = [];
        if ($surveyQid) {
            $sourceIds = $this->getSourceIds($surveyQid, $type);
        }
        return $sourceIds;
    }

    private function getIspIsqQuestion($type)
    {
        $factorRepo = $this->repositoryResolver->getRepository(self::ORGANIZATION_REPO);
        $qustData = $factorRepo->getSourceTypeQuestions($type);
        $sourceIds = [];
        if ($qustData) {
            foreach ($qustData as $orgQuestions) {

                $riskSource = new RiskSourceIdsDto();
                $riskSource->setSourceType($type);
                $riskSource->setOrgId($orgQuestions['org_id']);
                $riskSource->setCampusId(RiskVariableHelper::getEmptyIfNull($orgQuestions['campus_id']));
                $riskSource->setIds(RiskVariableHelper::getEmptyIfNull($orgQuestions['ids']));
                $sourceIds[] = $riskSource;
            }
        }

        return $sourceIds;
    }

    private function getQuestionbankIds($type)
    {
        $qbData = array();
        $ebiRepo = $this->repositoryResolver->getRepository(self::EBI_QUESTION);
        $ebiData = $ebiRepo->getAllEbiQuestions();
        if ($ebiData) {
            $riskSource = new RiskSourceIdsDto();
            $riskSource->setSourceType($type);
            $riskSource->setSurveyId('');
            $riskSource->setIds($ebiData[0]['ebi_ids']);
            $qbData[] = $riskSource;
        }
        return $qbData;
    }

    private function getAllSurveyQuestion($surveyQid)
    {
        $questions = [];
        foreach ($surveyQid as $sQuest) {
            $questions[$sQuest[RiskVariableConstants::SURVEY_ID]][] = $sQuest['questions'];
        }
        return $questions;
    }

    private function setSourceType($riskVariableDto, $riskVariable)
    {
        $sourse = $riskVariableDto->getSourceType();
        $sourse = strtolower($sourse);
        $riskVariable->setSource($sourse);
        switch ($sourse) {
            case RiskVariableConstants::PROFILE:
                $this->setProfile($riskVariableDto->getSourceId(), $riskVariable);
                break;
            case RiskVariableConstants::SURVEYQUESTION:
                $this->setSurveyQuestion($riskVariableDto->getSourceId(), $riskVariable);
                break;
            case RiskVariableConstants::SURVEYFACTOR:
                $this->setSurveyFactor($riskVariableDto->getSourceId(), $riskVariable);
                break;

            case RiskVariableConstants::ISP:
                $this->setIsp($riskVariableDto->getSourceId(), $riskVariable);
                break;
            case RiskVariableConstants::ISQ:
                $this->setIsq($riskVariableDto->getSourceId(), $riskVariable);
                break;

            case RiskVariableConstants::QUESTIONBANK:
                $this->setQuestionBank($riskVariableDto->getSourceId(), $riskVariable);
                break;
            default:
                throw new ValidationException([
                    RiskVariableConstants::ERR_RISK_RV_004
                ], RiskVariableConstants::ERR_RISK_RV_004, 'ERR_RISK_RV_004');
        }
    }

    private function setQuestionBank($sources, $riskVariable)
    {
        $ebiProfileRepository = $this->repositoryResolver->getRepository(RiskVariableConstants::EBI_QUESTION);
        $ebiProfile = $ebiProfileRepository->find($sources->getQuestionBankId());
        if ($ebiProfile) {
            $riskVariable->setEbiMetadata(NULL);
            $riskVariable->setOrgMetadata(NULL);
            $riskVariable->setEbiQuestion($ebiProfile);
            $riskVariable->setSurvey(NULL);
            $riskVariable->setOrgQuestion(NULL);
            $riskVariable->setSurveyQuestions(NULL);
            $riskVariable->setFactor(NULL);
            $riskVariable->setOrg(NULL);
        } else {
            throw new ValidationException([
                RiskVariableConstants::ERR_RISK_RV_005
            ], RiskVariableConstants::ERR_RISK_RV_005, 'ERR_RISK_RV_005');
        }
    }

    public function setProfile($sources, $riskVariable)
    {
        $this->logger->debug(" Set Profile by Sources and Risk Variable" );
        $ebiProfileRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_REPO);
        $ebiProfile = $ebiProfileRepository->find($sources->getEbiProfileId());
        if ($ebiProfile) {
            $riskVariable->setEbiMetadata($ebiProfile);
            $riskVariable->setOrgMetadata(NULL);
            $riskVariable->setEbiQuestion(NULL);
            $riskVariable->setSurvey(NULL);
            $riskVariable->setOrgQuestion(NULL);
            $riskVariable->setSurveyQuestions(NULL);
            $riskVariable->setFactor(NULL);
            $riskVariable->setOrg(NULL);
        } else {
            $this->logger->error("Risk Bundle - Set Profile - ".RiskVariableConstants::ERR_RISK_RV_006);
            throw new ValidationException([
                RiskVariableConstants::ERR_RISK_RV_006
            ], RiskVariableConstants::ERR_RISK_RV_006, 'ERR_RISK_RV_006');
        }
    }

    public function setSurveyQuestion($sources, $riskVariable)
    {
        $this->logger->debug("Set Survey Questions by Sources and Risk Variable " );
        $surveyQuestionRepository = $this->repositoryResolver->getRepository(RiskVariableConstants::SURVEY_QUESTION);
        $surveyQuestion = $surveyQuestionRepository->findOneBy([
            'id' => $sources->getQuestionId(),
            'survey' => $sources->getSurveyId()
        ]);
        if ($surveyQuestion) {
            $riskVariable->setEbiMetadata(NULL);
            $riskVariable->setOrgMetadata(NULL);
            $riskVariable->setEbiQuestion(NULL);
            $riskVariable->setSurvey($surveyQuestion->getSurvey());
            $riskVariable->setOrgQuestion(NULL);
            $riskVariable->setSurveyQuestions($surveyQuestion);
            $riskVariable->setFactor(NULL);
            $riskVariable->setOrg(NULL);
        } else {
            $this->logger->error("Risk Bundle - Set Survey Question - ".RiskVariableConstants::ERR_RISK_RV_007);
            throw new ValidationException([
                RiskVariableConstants::ERR_RISK_RV_007
            ], RiskVariableConstants::ERR_RISK_RV_007, 'ERR_RISK_RV_007');
        }
    }


    /**
     * If the risk source has both a valid factor ID and a valid survey ID, sets those on the risk variable.
     * Otherwise throws an error
     *
     * @param SourceIdDto $riskSource
     * @param RiskVariable $riskVariable
     * @throws ValidationException
     */
    public function setSurveyFactor($riskSource, $riskVariable)
    {
        $this->logger->info(" Set Survey Factor by Sources  and  Risk Variable ");
        $factorObject = $this->factorRepository->find($riskSource->getFactorId());
        $surveyObject = $this->surveyRepository->find($riskSource->getSurveyId());

        if ($factorObject && $surveyObject) {
            $riskVariable->setEbiMetadata(null);
            $riskVariable->setOrgMetadata(null);
            $riskVariable->setEbiQuestion(null);
            $riskVariable->setSurvey($surveyObject);
            $riskVariable->setOrgQuestion(null);
            $riskVariable->setSurveyQuestions(null);
            $riskVariable->setFactor($factorObject);
            $riskVariable->setOrg(null);
        } else {
            $this->logger->error("Risk Bundle - Set Survey Factor - " . RiskVariableConstants::ERR_RISK_RV_008);
            throw new ValidationException([
                RiskVariableConstants::ERR_RISK_RV_008
            ], RiskVariableConstants::ERR_RISK_RV_008, 'ERR_RISK_RV_008');
        }
        $this->logger->info(" Set Survey Factor ");
    }

    private function setIsp($sources, $riskVariable)
    {
        $ispRepository = $this->repositoryResolver->getRepository(OrgProfileConstant::ORGMETADATA_REPO);
        $campusId = $sources->getCampusId();
        $organization = $this->container->get('org_service')->getOrgnizationByCampusId($campusId);
        $isp = $ispRepository->findOneBy([
            'id' => $sources->getIspId(),
            'organization' => $organization
        ]);
        if ($isp) {
            $riskVariable->setEbiMetadata(NULL);
            $riskVariable->setOrgMetadata($isp);
            $riskVariable->setEbiQuestion(NULL);
            $riskVariable->setSurvey(NULL);
            $riskVariable->setOrgQuestion(NULL);
            $riskVariable->setSurveyQuestions(NULL);
            $riskVariable->setFactor(NULL);
            $riskVariable->setOrg($organization);
        } else {
            throw new ValidationException([
                RiskVariableConstants::ERR_RISK_RV_009
            ], RiskVariableConstants::ERR_RISK_RV_009, 'ERR_RISK_RV_009');
        }
    }

    private function setIsq($sources, $riskVariable)
    {
        $isqRepository = $this->repositoryResolver->getRepository(RiskVariableConstants::ORG_QUESTION);
        $campusId = $sources->getCampusId();
        $organization = $this->container->get('org_service')->getOrgnizationByCampusId($campusId);
        $isq = $isqRepository->findOneBy([
            'id' => $sources->getIsqId(),
            'organization' => $organization
        ]);
        if ($isq) {
            $riskVariable->setEbiMetadata(NULL);
            $riskVariable->setOrgMetadata(NULL);
            $riskVariable->setEbiQuestion(NULL);
            $riskVariable->setSurvey(NULL);
            $riskVariable->setOrgQuestion($isq);
            $riskVariable->setSurveyQuestions(NULL);
            $riskVariable->setFactor(NULL);
            $riskVariable->setOrg($organization);
        } else {
            throw new ValidationException([
                RiskVariableConstants::ERR_RISK_RV_010
            ], RiskVariableConstants::ERR_RISK_RV_010, 'ERR_RISK_RV_010');
        }
    }

    private function setCalculationData($riskVariableDto, $riskVariable)
    {
        $calData = $riskVariableDto->getCalculatedData();
        if ($riskVariableDto->getIsCalculated()) {
            RiskVariableHelper::isCalculatedFiledsEmpty($calData);
            RiskVariableHelper::CalcStartEndDateValidation($calData->getCalculationStartDate(), $calData->getCalculationStopDate());

            $date =  $calData->getCalculationStartDate()->format('y-m-d');
            $startDate =  new \DateTime($date,new \DateTimeZone('CST'));
            $startDate->setTimezone(new \DateTimeZone('UTC'));

            $riskVariable->setCalculationStartDate($startDate);

            $date =  $calData->getCalculationStopDate()->format('y-m-d');
            $endDate =  new \DateTime($date ,new \DateTimeZone('CST'));
            $endDate->setTimezone(new \DateTimeZone('UTC'));


            $riskVariable->setCalculationEndDate($endDate);

            $riskVariable->setCalcType($calData->getCalculationType());
        } else {
            if(is_object($calData) && ($calData->getCalculationStartDate() || $calData->getCalculationStopDate() || $calData->getCalculationType())){
                throw new ValidationException([
                		'Risk Calculation Data Invalid.'
                		], 'Risk Calculation Data Invalid.', 'Risk_Calculation_Data_Invalid');
            }
            $riskVariable->setCalculationStartDate(NULL);
            $riskVariable->setCalculationEndDate(NULL);
            $riskVariable->setCalcType(NULL);
        }
    }

    private function setRiskVariableCategory($riskVariableDto, $riskVariable)
    {
        $riskVariableRangeRepository = $this->repositoryResolver->getRepository(RiskVariableConstants::RISK_VARIABLE_CATEGORY);
        $bucketDetails = $riskVariableDto->getBucketDetails();
        foreach ($bucketDetails as $bucketDetail) {

            $optionValues = $bucketDetail->getOptionValue();
            if (count($optionValues) == 1 && empty($optionValues[0]) && strlen($optionValues[0]) == 0) {
                continue;
            } else {
                for ($indexCount = 0; $indexCount < count($optionValues); $indexCount ++) {
                    $category = new RiskVariableCategory();
                    $category->setBucketValue($bucketDetail->getBucketValue());
                    $category->setRiskVariable($riskVariable);
                    $category->setOptionValue(RiskVariableHelper::getNullIfEmpty($optionValues[$indexCount]));
                    $riskVariableRangeRepository->persist($category, false);
                }
            }
        }
    }

    private function removeRiskVariableCategory($riskVariable)
    {
        $riskVariableCatRepository = $this->repositoryResolver->getRepository(RiskVariableConstants::RISK_VARIABLE_CATEGORY);
        $values = $riskVariableCatRepository->findBy([
            'riskVariable' => $riskVariable
        ]);
        if ($values) {
            foreach ($values as $value) {
                $riskVariableCatRepository->delete($value, false);
            }
        }
    }

    private function removeRiskVariableRange($riskVariable)
    {
        $riskVariableRangeRepository = $this->repositoryResolver->getRepository(RiskVariableConstants::RISK_VARIABLE_RANGE);
        $values = $riskVariableRangeRepository->findBy([
            'riskVariable' => $riskVariable
        ]);
        if ($values) {
            foreach ($values as $value) {
                $riskVariableRangeRepository->delete($value, false);
            }
        }
    }

    private function setRiskVariableRange($riskVariableDto, $riskVariable)
    {
        $riskVariableRangeRepository = $this->repositoryResolver->getRepository(RiskVariableConstants::RISK_VARIABLE_RANGE);
        $bucketDetails = $riskVariableDto->getBucketDetails();

        foreach ($bucketDetails as $bucketDetail) {
            if (empty($bucketDetail->getMin()) && empty($bucketDetail->getMax())) {
                continue;
            } else {
                $category = new RiskVariableRange();
                $category->setBucketValue($bucketDetail->getBucketValue());
                $category->setRiskVariable($riskVariable);
                $category->setMin($bucketDetail->getMin());
                $category->setMax($bucketDetail->getMax());
                $riskVariableRangeRepository->persist($category, false);
            }
        }
    }

    public function changeStatus($id)
    {
        $this->logger->debug(" Change Status by Id " . $id);
        $riskVariableRepository = $this->repositoryResolver->getRepository(self::RISK_VARIABLE_REPO);
        $riskVariable = $riskVariableRepository->find($id);
        if (! $riskVariable) {
            $this->logger->error("Risk Bundle - Change Status - ".RiskVariableConstants::ERR_RISK_RV_002);
            throw new ValidationException([
                RiskVariableConstants::ERR_RISK_RV_002
            ], RiskVariableConstants::ERR_RISK_RV_002, RiskVariableConstants::ERR_RISK_RV_002);
        } else {
            /**
             * Mark variable as active
             */
            if ($riskVariable->getIsArchived()) {
                $status = false;
            } else {
                /**
                 * check if variable asassigned to model
                 */
                $assignedStatus = $this->isVariableAssigned($id);
                if ($assignedStatus) {
                    throw new ValidationException([
                        'Variable is assigned to model.'
                    ], RiskVariableConstants::ERR_RISK_RV_001, 'ERR_RISK_RV_001');
                } else {
                    /**
                     * Mark variable as archived
                     */
                    $status = true;
                }
            }
            $riskVariable->setIsArchived($status);
        }
        $riskVariableRepository->flush();
    }

    /**
     * Check is variable assigned to model
     *
     * @param unknown $variableId
     * @return unknown
     */
    private function isVariableAssigned($variableId)
    {
        $riskVariableRepository = $this->repositoryResolver->getRepository(self::RISK_MODEL_WEIGHTS_REPO);
        $assignedStatus = $riskVariableRepository->findOneByriskVariable($variableId);
        if ($assignedStatus) {
            $value = true;
        } else {
            $value = false;
        }
        return $value;
    }

    public function startUpdateRiskVariableList()
    {
        $this->logger->info(" Start Update Risk Variable List ");
        $jobNumber = uniqid();
        $job = new CreateRiskVariableListJob();
        $resque = $this->container->get('bcc_resque.resque');
        $job->args = array(

            'jobNumber' => $jobNumber
        );

        $resque->enqueue($job, true);
    }
}