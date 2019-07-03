<?php
namespace Synapse\RiskBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Util\Helper;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\RiskBundle\Entity\RiskGroup;
use Synapse\RiskBundle\Entity\RiskGroupLang;
use Synapse\RiskBundle\EntityDto\RiskGroupDto;
use Synapse\RiskBundle\EntityDto\RiskGroupModelAssignmentsDto;
use Synapse\RiskBundle\EntityDto\RiskGroupModelDto;
use Synapse\RiskBundle\Repository\OrgRiskGroupModelRepository;
use Synapse\RiskBundle\Repository\RiskGroupRepository;
use Synapse\RiskBundle\Service\RiskGroupServiceInterface;
use Synapse\RiskBundle\Util\Constants\RiskErrorConstants;
use Synapse\RiskBundle\Util\Constants\RiskGroupConstants;
use Synapse\RiskBundle\Util\RiskVariableHelper;

/**
 * @DI\Service("riskgroup_service")
 */
class RiskGroupService extends AbstractService implements RiskGroupServiceInterface
{

    const SERVICE_KEY = 'riskgroup_service';

    const TOTAL_COUNT = 'total_count';

    const RISK_GROUPS = 'risk_groups';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    // Repositories

    /**
     * @var OrgRiskGroupModelRepository
     */
    private $orgRiskGroupModelRepository;

    /**
     * @var RiskGroupRepository
     */
    private $riskGroupRepository;

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

        // Repositories
        $this->orgRiskGroupModelRepository = $this->repositoryResolver->getRepository(OrgRiskGroupModelRepository::REPOSITORY_KEY);
        $this->riskGroupRepository = $this->repositoryResolver->getRepository(RiskGroupRepository::REPOSITORY_KEY);
    }

    public function createGroup($riskGroupDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($riskGroupDto);
        $this->logger->debug(" Creating Risk Group  -  " . $logContent);
        
        $riskGroupRepo = $this->repositoryResolver->getRepository(RiskGroupConstants::RISK_GROUP);
        $lang = $this->getEbiLangRef();
        try {
            $riskGroupRepo->startTransaction();
            $riskGroup = new RiskGroup();
            $riskGroupRepo->persist($riskGroup);
            $riskGroupLang = new RiskGroupLang();
            $riskGroupLang->setName($riskGroupDto->getGroupName());
            $riskGroupLang->setDescription($riskGroupDto->getGroupDescription());
            $riskGroupLang->setLang($lang);
            $riskGroupLang->setRiskGroup($riskGroup);
            $this->validateEntity($riskGroupLang);
            $riskGroupRepo->persist($riskGroupLang);
            $riskGroupRepo->completeTransaction();
        } catch (ValidationException $e) {
            $this->logger->error("Risk Bundle : Crate Risk Group : ERR-RISK-RG-001 :" . $e->getMessage());
            throw new ValidationException([
                $e->getMessage()
            ], $e->getMessage(), 'ERR-RISK-RG-001');
        } catch (\Exception $e) {
            $riskGroupRepo->rollbackTransaction();
            $this->logger->error("Risk Bundle : Create Risk Group : ERR-RISK-RG-999 :" . $e->getMessage());
            throw new ValidationException([
                RiskErrorConstants::ERR_RISK_RG_999
            ], RiskErrorConstants::ERR_RISK_RG_999, 'ERR-RISK-RG-999');
        }
        $riskGroupDto->setId($riskGroup->getId());
        $this->logger->info(" Created Risk Group ");
        return $riskGroupDto;
    }

    public function editGroup($riskGroupDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($riskGroupDto);
        $this->logger->debug(" Editing Risk Group  -  " . $logContent);
        
        $riskGroupRepo = $this->repositoryResolver->getRepository(RiskGroupConstants::RISK_GROUP);
        $riskGroupLangRepo = $this->repositoryResolver->getRepository(RiskGroupConstants::RISK_GROUP_LANG);
        $lang = $this->getEbiLangRef();
        try {
            $riskGroupRepo->startTransaction();
            $groupId = $riskGroupDto->getId();
            $lang = $this->getEbiLangRef();
            $riskGroupLang = $riskGroupLangRepo->findOneBy([
                'lang' => $lang,
                'riskGroup' => $groupId
            ]);
            if (! $riskGroupLang) {
                $this->logger->error("Risk Bundle - Edit Group - ".RiskErrorConstants::ERR_RISK_RG_002);
                throw new ValidationException([
                    RiskErrorConstants::ERR_RISK_RG_002
                ], RiskErrorConstants::ERR_RISK_RG_002, 'ERR-RISK-RG-002');
            } else {
                $riskGroupLang->setName($riskGroupDto->getGroupName());
                $riskGroupLang->setDescription($riskGroupDto->getGroupDescription());
                
                $this->validateEntity($riskGroupLang);
                $riskGroupRepo->persist($riskGroupLang);
                $riskGroupRepo->completeTransaction();
            }
        } catch (ValidationException $e) {
            
            $riskGroupRepo->rollbackTransaction();
            $this->logger->error("Risk Bundle : edit Risk Group : " . $e->getMessage());
            throw new ValidationException([
                $e->getMessage()
            ], $e->getMessage(), str_replace("-", "_", $e->getExceptionCode()));
        } catch (\Exception $e) {
            $riskGroupRepo->rollbackTransaction();
            $this->logger->error("Risk Bundle : Edit Risk Group : ERR-RISK-RG-999 :" . $e->getMessage());
            throw new ValidationException([
                RiskErrorConstants::ERR_RISK_RG_999
            ], RiskErrorConstants::ERR_RISK_RG_999, 'ERR-RISK-RG-999');
        }
        $this->logger->info(" Risk Group Edited ");
        return $riskGroupDto;
    }

    public function getRiskGroups()
    {
        $this->logger->info(" Get Risk Groups");
        $riskGroupLangRepo = $this->repositoryResolver->getRepository(RiskGroupConstants::RISK_GROUP_LANG);
        $lang = $this->getEbiLangRef();
        $groups = $riskGroupLangRepo->getRiskGroups($lang);
        $response = [];
        $response[self::TOTAL_COUNT] = count($groups);
        $response[self::RISK_GROUPS] = [];
        if (count($groups) > 0) {
            foreach ($groups as $group) {
                $riskGroup = new RiskGroupDto();
                $riskGroup->setId($group['id']);
                $riskGroup->setGroupName($group['groupName']);
                $riskGroup->setGroupDescription($group['groupDescription']);
                $response[self::RISK_GROUPS][] = $riskGroup;
            }
        }
        return $response;
    }

    public function getRiskGroupById($id)
    {
        $this->logger->debug("Get Risk Group By Id " . $id);
        $riskGroupLangRepo = $this->repositoryResolver->getRepository(RiskGroupConstants::RISK_GROUP_LANG);
        $lang = $this->getEbiLangRef();
        $groups = $riskGroupLangRepo->getRiskGroupById($lang, $id);
        $riskGroup = new RiskGroupDto();
        if (count($groups) > 0) {
            $group = $groups[0];
            
            $riskGroup->setId($group['id']);
            $riskGroup->setGroupName($group['groupName']);
            $riskGroup->setGroupDescription($group['groupDescription']);
            $response[self::RISK_GROUPS][] = $riskGroup;
        } else {
            $this->logger->error("Risk Bundle - Get Risk Group by Id - ".RiskErrorConstants::ERR_RISK_RG_002);
            throw new ValidationException([
                RiskErrorConstants::ERR_RISK_RG_002
            ], RiskErrorConstants::ERR_RISK_RG_002, 'ERR-RISK-RG-002');
        }
        $this->logger->info("Get Risk Group By Id ");
        return $riskGroup;
    }

    public function getRiskModelAssingment($id)
    {
        $this->logger->debug(" Get Risk Model Assignment by Id " . $id);
        $riskGroupModelRepo = $this->repositoryResolver->getRepository('SynapseRiskBundle:OrgRiskGroupModel');
        $orgStudentRepo = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgPersonStudent');
        $riskModels = $riskGroupModelRepo->getRiskGroupsForOrganization($id);
        $groupModelDto = new RiskGroupModelDto();
        $totalStudent = $orgStudentRepo->getStudentCount($id);
        $groupModelDto->setTotalAssignedModelsCount(count($riskModels));
        $groupModelDto->setStudentsWithNoRisk(0);
        $assingedStudentCount = 0;
        $assignmentArray = [];
        if (count($riskModels) > 0) {
            /*
             * find organization time zone
             */
            $organization = $this->container->get('org_service')->find($id);
            $orgTimeZone = $organization->getTimeZone();                        
            foreach ($riskModels as $riskModel) {
            
                $assignement = new RiskGroupModelAssignmentsDto();
                
                $assignement->setRiskGroupName($riskModel['risk_group_name']);
                $assignement->setRiskGroupId($riskModel['risk_group_id']);
                $assignement->setRiskGroupDescription(RiskVariableHelper::getEmptyIfNull($riskModel['risk_group_description']));
                
                $assignement->setRiskModelName(RiskVariableHelper::getEmptyIfNull($riskModel['risk_model_name']));
                $assignement->setRiskModelId(RiskVariableHelper::getEmptyIfNull($riskModel['risk_model_id']));
                $assignement->setModelState(RiskVariableHelper::getEmptyIfNull($riskModel['model_state']));
                /*
                 * convert calculation_start_date as org time zone
                 */
                if(!empty($riskModel['calculation_start_date']))
                {
                    $calculationStartDate = new \DateTime($riskModel['calculation_start_date']);                
                    Helper::getOrganizationDate($calculationStartDate, $orgTimeZone);
                    $assignement->setCalculationStartDate($calculationStartDate);
                }
                /*
                 * convert calculation_stop_date as org time zone
                 */
                if(!empty($riskModel['calculation_stop_date']))
                {
                    $calculationStopDate = new \DateTime($riskModel['calculation_stop_date']);                
                    Helper::getOrganizationDate($calculationStopDate, $orgTimeZone);
                    $assignement->setCalculationStopDate($calculationStopDate);
                }
                /*
                 * convert enrollment_end_date as org time zone
                 */
                if(!empty($riskModel['enrollment_end_date']))
                {
                    $enrollmentEndDate = new \DateTime($riskModel['enrollment_end_date']);                
                    Helper::getOrganizationDate($enrollmentEndDate, $orgTimeZone);
                    $assignement->setEnrollmentEndDate($enrollmentEndDate);
                }
                $assignement->setStudentsCount($riskModel['student_count']);
                $assingedStudentCount += $riskModel['student_count'];
                $assignmentArray[] = $assignement;
            }
        }
        $groupModelDto->setStudentsWithNoRisk($totalStudent - $assingedStudentCount);
        $groupModelDto->setRiskModelAssignments($assignmentArray);      
        $this->logger->info(" Get Risk Model Assignment by Id ");
        return $groupModelDto;
    }

    private function getEbiLangRef()
    {
        $ebiConfigRepository = $this->repositoryResolver->getRepository(RiskGroupConstants::EBI_CONFIG);
        $ebiLang = $ebiConfigRepository->findOneByKey('Ebi_Lang');
        $langId = $ebiLang->getValue();
        $langService = $this->container->get('lang_service');
        return $langService->getLanguageById($langId);
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
            
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'ERR-RISK-RG-001');
        }
    }

    /**
     * Validates risk group ID.
     *
     * @param int $organizationId
     * @param int $riskGroupId
     * @return bool|string
     */
    public function validateRiskGroupBelongsToOrganization($organizationId, $riskGroupId)
    {
        $errorMessage = '';
        $riskGroup = $this->riskGroupRepository->find($riskGroupId);
        if ($riskGroup) {
            //Check Risk Group Mapped to Organization or not
            $orgRiskGroupModel = $this->orgRiskGroupModelRepository->findOneBy(['org' => $organizationId, 'riskGroup' => $riskGroup]);
            if (!$orgRiskGroupModel) {
                $errorMessage = "Risk Group is not mapped to any organization.";
            }
        } else {
            $errorMessage = "Risk Group does not exist.";
        }
        if (!empty($errorMessage)) {
            return $errorMessage;
        }
        return true;
    }
}