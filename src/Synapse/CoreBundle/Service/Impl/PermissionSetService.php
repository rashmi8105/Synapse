<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\EbiPermissionsetDatablock;
use Synapse\CoreBundle\Entity\EbiPermissionsetFeatures;
use Synapse\CoreBundle\Entity\PermissionSet;
use Synapse\CoreBundle\Entity\PermissionSetLang;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\DatablockMasterLangRepository;
use Synapse\CoreBundle\Repository\DatablockMasterRepository;
use Synapse\CoreBundle\Repository\FeatureMasterRepository;
use Synapse\CoreBundle\Repository\LanguageMasterRepository;
use Synapse\CoreBundle\Repository\PermissionSetLangRepository;
use Synapse\CoreBundle\Repository\PermissionSetRepository;
use Synapse\RestBundle\Entity\AccessLevelDto;
use Synapse\RestBundle\Entity\BlockDto;
use Synapse\RestBundle\Entity\CoursesAccessDto;
use Synapse\RestBundle\Entity\FeatureBlockDto;
use Synapse\RestBundle\Entity\PermissionSetDto;
use Synapse\RestBundle\Entity\PermissionSetStatusDto;
use Synapse\RestBundle\Entity\PermissionValueDto;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("permissionset_service")
 */
class PermissionSetService extends AbstractService
{

    const SERVICE_KEY = 'permissionset_service';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERMSET_REPO = "SynapseCoreBundle:PermissionSet";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERMSET_LANG_REPO = "SynapseCoreBundle:PermissionSetLang";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERMSET_FEATURE_MASTER_REPO = "SynapseCoreBundle:FeatureMaster";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERMSET_FEATURE_MASTERLANG_REPO = "SynapseCoreBundle:FeatureMasterLang";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const LNG_MASTER_REPO = "SynapseCoreBundle:LanguageMaster";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const DATABLOCK_MASTER_LANG_REPO = "SynapseCoreBundle:DatablockMasterLang";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERMSET_DATABLOCK_REPO = "SynapseCoreBundle:EbiPermissionsetDatablock";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERMSET_FEATURE_REPO = "SynapseCoreBundle:EbiPermissionsetFeatures";

    const ERROR_LANGUAGE_NOT_FOUND = 'Language Not Found.';

    const ERROR_LANGUAGE_NOT_FOUND_KEY = 'lang_not_found';

    const ERROR_FEATURE_NOT_FOUND = 'Feature Not Found';

    const ERROR_KEY = 'EFEA001';

    const ERROR_PERMISSIONSET_NOT_FOUND = 'Permissionset Not Found';

    const ERROR_DATABLOCK_NOT_FOUND = "DataBlock Not Found";

    const FIELD_EBIPERMISSIONSET = "ebiPermissionSet";

    const FIELD_ACTIVE = "active";

    const FIELD_FEATURE = "feature";

    const FIELD_EBIPERMISSION_SET = "ebiPermissionset";

    const FIELD_DATABLOCKID = "datablock_id";

    const FIELD_ARCHIVE = "archive";

    const FIELD_FEATUREID = "feature_id";

    const LANG_ID = 'lang_id';

    const INSERT = 'insert';

    const DATA_BLOCK = 'dataBlock';

    // Scaffolding
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Logger
     */
    protected $logger;


    // Services

    /**
     *
     * @var LanguageMasterService
     */
    private $languageService;

    // Repositories

    /**
     * @var dataBlockMasterLangRepository
     */
    private $dataBlockMasterLangRepository;

    /**
     * @var DatablockMasterRepository
     */
    private $dataBlockMasterRepository;

    /**
     * @var FeatureMasterRepository
     */
    private $featureMasterRepository;

    /**
     * @var LanguageMasterRepository
     */
    private $languageMasterRepository;

    /**
     * @var PermissionSetLangRepository
     */
    private $permissionSetLangRepository;

    /**
     * @var PermissionSetRepository
     */
    private $permissionSetRepository;

    /**
     * PermissionSetService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container"),
     *            })
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     *
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        // Scaffolding
        $this->container = $container;

        // Services
        $this->languageService = $this->container->get(LanguageMasterService::SERVICE_KEY);

        // Repositories
        $this->dataBlockMasterLangRepository = $this->repositoryResolver->getRepository(DataBlockMasterLangRepository::REPOSITORY_KEY);
        $this->languageMasterRepository = $this->repositoryResolver->getRepository(languageMasterRepository::REPOSITORY_KEY);
    }

    public function find($id)
    {
        $this->permissionSetRepository = $this->repositoryResolver->getRepository(self::PERMSET_REPO);
        $permissionset = $this->permissionSetRepository()->find($id);
        if (! $permissionset) {
            throw new ValidationException([
                self::ERROR_PERMISSIONSET_NOT_FOUND
            ], self::ERROR_PERMISSIONSET_NOT_FOUND, 'Permissionset_not_found');
        }
        return $permissionset;
    }

    public function create(PermissionSetDto $permissionSetDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($permissionSetDto);
        $this->logger->debug(" Creating Permissionset -  " . $logContent);
        
        $this->permissionSetRepository = $this->repositoryResolver->getRepository(self::PERMSET_REPO);
        $this->permissionSetLangRepository = $this->repositoryResolver->getRepository(self::PERMSET_LANG_REPO);
        $this->ebiPermissionsetFeaturesRepository = $this->repositoryResolver->getRepository(self::PERMSET_FEATURE_REPO);
        $this->featureMasterRepository = $this->repositoryResolver->getRepository(self::PERMSET_FEATURE_MASTER_REPO);
        /**
         * Creating Permission Set
         */
        $lang = $this->languageService->getLanguageById($permissionSetDto->getLangId());
        $lang = $this->catchLanguageException($lang);
        $permissionSet = new PermissionSet();
        $permissionSet->setIsActive(true);
        $permissionSet->setIntentToLeave($permissionSetDto->getIntentToleave());
        $permissionSet->setRiskIndicator($permissionSetDto->getRiskindicator());
        
        $permissionSet->setAccesslevelAgg($permissionSetDto->getAccessLevel()
            ->getAggregateOnly());
        $permissionSet->setAccesslevelIndAgg($permissionSetDto->getAccessLevel()
            ->getIndividualAndAggregate());
        
        $permissionSet->setViewCourses($permissionSetDto->getCoursesAccess()
            ->getViewCourses());
        $permissionSet->setCreateViewAcademicUpdate($permissionSetDto->getCoursesAccess()
            ->getCreateViewAcademicUpdate());
        $permissionSet->setViewAllAcademicUpdateCourses($permissionSetDto->getCoursesAccess()
            ->getViewAllAcademicUpdateCourses());
        $permissionSet->setViewAllFinalGrades($permissionSetDto->getCoursesAccess()
            ->getViewAllFinalGrades());
        
        $this->permissionSetRepository->createPermissionSet($permissionSet);
        
        $permissionSetLang = new PermissionSetLang();
        $permissionSetLang->setEbiPermissionSet($permissionSet);
        $permissionSetLang->setLang($lang);
        $permissionSetLang->setPermissionsetName($permissionSetDto->getPermissionTemplateName());
        $validator = $this->container->get('validator');
        $errors = $validator->validate($permissionSetLang);
        
        $this->catchError($errors);
        $this->permissionSetLangRepository->createPermissionSetLang($permissionSetLang);
        
        $profileBlocks = $permissionSetDto->getProfileBlocks();
        $surveyBlocks = $permissionSetDto->getSurveyBlocks();
        $features = $permissionSetDto->getFeatures();
        
        /**
         * Creating Data Blocks Permission
         */
        
        $this->createEbiDataBlocks($profileBlocks, $permissionSet);
        $this->createEbiDataBlocks($surveyBlocks, $permissionSet);
        
        /**
         * Creating Permission Set Feature
         */
        
        if (count($features) > 0) {
            
            foreach ($features as $feature) {
                
                $ebiFeaturePermission = new EbiPermissionsetFeatures();
                $fid = $feature->getId();
                
                $featureMaster = $this->featureMasterRepository->find($fid);
                
                if (! $featureMaster) {
                    throw new ValidationException([
                        self::ERROR_FEATURE_NOT_FOUND
                    ], self::ERROR_FEATURE_NOT_FOUND, self::ERROR_KEY);
                }
                
                $ebiFeaturePermission->setFeature($featureMaster);
                $ebiFeaturePermission->setPermissionset($permissionSet);
                
                
                if ($fid == 1) {
                                    
                    $this->setAccesslevelReferal($ebiFeaturePermission, $feature);
                } else {
                                     
                    $this->setAccesslevelFeatures($ebiFeaturePermission, $feature);                   
                    
                }
                $this->ebiPermissionsetFeaturesRepository->createEbiPermissionsetFeatures($ebiFeaturePermission);
            }
        }
        $this->permissionSetRepository->flush();
        $permissionSetDto->setPermissionTemplateId($permissionSet->getId());
        return $permissionSetDto;
    }
    private function setAccesslevelFeatures($ebiFeaturePermission, $feature)
    {
        $ebiFeaturePermission->setPrivateCreate($feature->getPrivateShare()
            ->getCreate());
    
        $ebiFeaturePermission->setPublicCreate($feature->getPublicShare()
            ->getCreate());
        $ebiFeaturePermission->setPublicView($feature->getPublicShare()
            ->getView());
    
        $ebiFeaturePermission->setTeamCreate($feature->getTeamsShare()
            ->getCreate());
        $ebiFeaturePermission->setTeamView($feature->getTeamsShare()
            ->getView());
    
        $ebiFeaturePermission->setReceiveReferral(NULL);
    }
    
    private function setAccesslevelReferal($ebiFeaturePermission, $feature)
    {
        $directFeature = $feature->getDirectReferral();
        $reasonRoutedReferral = $feature->getReasonRoutedReferral();
        // Direct
        $ebiFeaturePermission->setPrivateCreate($directFeature->getPrivateShare()
            ->getCreate());
    
        $ebiFeaturePermission->setPublicCreate($directFeature->getPublicShare()
            ->getCreate());
        $ebiFeaturePermission->setPublicView($directFeature->getPublicShare()
            ->getView());
    
        $ebiFeaturePermission->setTeamCreate($directFeature->getTeamsShare()
            ->getCreate());
        $ebiFeaturePermission->setTeamView($directFeature->getTeamsShare()
            ->getView());
        // Reason routed
        $ebiFeaturePermission->setReasonReferralPrivateCreate($reasonRoutedReferral->getPrivateShare()
            ->getCreate());
    
        $ebiFeaturePermission->setReasonReferralPublicCreate($reasonRoutedReferral->getPublicShare()
            ->getCreate());
        $ebiFeaturePermission->setReasonReferralPublicView($reasonRoutedReferral->getPublicShare()
            ->getView());
    
        $ebiFeaturePermission->setReasonReferralTeamCreate($reasonRoutedReferral->getTeamsShare()
            ->getCreate());
        $ebiFeaturePermission->setReasonReferralTeamView($reasonRoutedReferral->getTeamsShare()
            ->getView());
    
        $ebiFeaturePermission->setReceiveReferral((bool) $feature->getReceiveReferrals());
    }
    private function isPermissionsetFound($permissionSet)
    {
        if (! $permissionSet) {
            throw new ValidationException([
                self::ERROR_PERMISSIONSET_NOT_FOUND
            ], self::ERROR_PERMISSIONSET_NOT_FOUND, self::ERROR_KEY);
        }
    }

    public function edit(PermissionSetDto $permissionSetDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($permissionSetDto);
        $this->logger->debug(" Updating Permissionset -  " . $logContent);
        
        $this->permissionSetRepository = $this->repositoryResolver->getRepository(self::PERMSET_REPO);
        $this->permissionSetLangRepository = $this->repositoryResolver->getRepository(self::PERMSET_LANG_REPO);
        $this->ebiPermissionsetFeaturesRepository = $this->repositoryResolver->getRepository(self::PERMSET_FEATURE_REPO);
        $this->featureMasterRepository = $this->repositoryResolver->getRepository(self::PERMSET_FEATURE_MASTER_REPO);
        /**
         * Editing Permission Set
         */
        $permissionSet = $this->permissionSetRepository->find($permissionSetDto->getPermissionTemplateId());
        $permissionSetLang = $this->permissionSetLangRepository->findOneBy([
            self::FIELD_EBIPERMISSIONSET => $permissionSetDto->getPermissionTemplateId()
        ]);
        $this->isPermissionsetFound($permissionSet);
        $permissionSetLang->setPermissionsetName($permissionSetDto->getPermissionTemplateName());
        $status = (($permissionSetDto->getPermissionTemplateStatus()) == self::FIELD_ACTIVE) ? 1 : 0;
        $permissionSet->setIsActive($status);
        $permissionSet->setRiskIndicator($permissionSetDto->getRiskindicator());
        $permissionSet->setIntentToLeave($permissionSetDto->getIntentToleave());
        
        $permissionSet->setAccesslevelAgg($permissionSetDto->getAccessLevel()
            ->getAggregateOnly());
        $permissionSet->setAccesslevelIndAgg($permissionSetDto->getAccessLevel()
            ->getIndividualAndAggregate());
        
        $permissionSet->setViewCourses($permissionSetDto->getCoursesAccess()
            ->getViewCourses());
        $permissionSet->setCreateViewAcademicUpdate($permissionSetDto->getCoursesAccess()
            ->getCreateViewAcademicUpdate());
        $permissionSet->setViewAllAcademicUpdateCourses($permissionSetDto->getCoursesAccess()
            ->getViewAllAcademicUpdateCourses());
        $permissionSet->setViewAllFinalGrades($permissionSetDto->getCoursesAccess()
            ->getViewAllFinalGrades());
        
        $validator = $this->container->get('validator');
        $errors = $validator->validate($permissionSetLang);
        
        $this->catchError($errors);
        $profileBlocks = $permissionSetDto->getProfileBlocks();
        $surveyBlocks = $permissionSetDto->getSurveyBlocks();
        $features = $permissionSetDto->getFeatures();
        /**
         * Update Data Blocks Permission
         */
        $this->createEbiDataBlocks($profileBlocks, $permissionSet, 'update');
        $this->createEbiDataBlocks($surveyBlocks, $permissionSet, 'update');
        
        /**
         * Creating Permission Set Feature
         */
        if (count($features) > 0) {
            foreach ($features as $feature) {
                $fid = $feature->getId();
                $featureMaster = $this->featureMasterRepository->find($fid);
                if (! $featureMaster) {
                    throw new ValidationException([
                        self::ERROR_FEATURE_NOT_FOUND
                    ], self::ERROR_FEATURE_NOT_FOUND, self::ERROR_KEY);
                }
                $ebiFeaturePermission = $this->ebiPermissionsetFeaturesRepository->findOneBy([
                    self::FIELD_FEATURE => $featureMaster,
                    self::FIELD_EBIPERMISSION_SET => $permissionSet
                ]);
                if ($ebiFeaturePermission) {
                                        
                    if($fid == 1){
                        $this->setAccesslevelReferal($ebiFeaturePermission, $feature);
                    }
                    else {
                        $this->setAccesslevelFeatures($ebiFeaturePermission, $feature);
                    }
                } else {
                    $ebiFeaturePermissionNew = new EbiPermissionsetFeatures();
                    $ebiFeaturePermissionNew->setFeature($featureMaster);
                    $ebiFeaturePermissionNew->setPermissionset($permissionSet);
                                    
                    if ($fid == 1) {                    
                        $this->setAccesslevelReferal($ebiFeaturePermissionNew, $feature);
                    } else {                         
                        $this->setAccesslevelFeatures($ebiFeaturePermissionNew, $feature);                    
                    }
                    $this->ebiPermissionsetFeaturesRepository->createEbiPermissionsetFeatures($ebiFeaturePermissionNew);
                }
            }
        }
        $this->permissionSetRepository->flush();
        $permissionSetDto->setPermissionTemplateId($permissionSet->getId());
        return $permissionSetDto;
    }

    public function updateStatus(PermissionSetStatusDto $permissionSetStatusDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($permissionSetStatusDto);
        $this->logger->debug(" Updating Permissionset Status -  " . $logContent);
        
        $this->permissionSetRepository = $this->repositoryResolver->getRepository(self::PERMSET_REPO);
        /**
         * Editing Permission Set
         */
        $permissionSet = $this->permissionSetRepository->find($permissionSetStatusDto->getPermissionTemplateId());
        if (! $permissionSet) {
           $this->logger->error( "Permissionset Service - updateStatus - " . self::ERROR_PERMISSIONSET_NOT_FOUND );
            throw new ValidationException([
                self::ERROR_PERMISSIONSET_NOT_FOUND
            ], self::ERROR_PERMISSIONSET_NOT_FOUND, self::ERROR_KEY);
        }
        $status = ($permissionSetStatusDto->getPermissionTemplateStatus() == self::FIELD_ACTIVE) ? true : false;
        $permissionSet->setIsActive($status);
        if ($status != 1) {
            $permissionSet->setInactiveDate($permissionSet->getModifiedAt());
        }
        
        $this->permissionSetRepository->flush();
        
        $permissionSetStatusDto->setPermissionTemplateId($permissionSet->getId());
        
        return $permissionSetStatusDto;
    }

    /**
     * get data blocks for profile or survey
     *
     * @param int $languageId
     * @param string $blockType - blockType should be survey|profile
     * @return array
     * @throws SynapseValidationException
     */
    public function getDataBlocksByType($languageId, $blockType)
    {
        $languageObject = $this->languageMasterRepository->find($languageId);
        if (empty($languageObject)) {
            throw new SynapseValidationException('Language Not Found.');
        }
        $responseArray = [];
        $responseArray['lang_id'] = $languageObject->getId();
        $responseArray["data_block_type"] = $blockType;
        $dataBlocks = $this->dataBlockMasterLangRepository->getDatablocks($blockType, $languageObject->getId());
        if (count($dataBlocks) > 0) {
            $i = 0;
            foreach ($dataBlocks as $dataBlock) {
                if ($dataBlock['profile_item_count'] > 0 || $blockType == 'survey') {
                    $responseArray["data_blocks"][$i]["block_name"] = $dataBlock['datablock_name'];
                    $responseArray["data_blocks"][$i]["block_id"] = $dataBlock['datablock_id'];
                    $i += 1;
                }
            }
        }
        return $responseArray;
    }

    public function getPermissionSet($langid, $Id)
    {
        $this->dataBlockMasterLangRepository = $this->repositoryResolver->getRepository(self::DATABLOCK_MASTER_LANG_REPO);
        $this->languageMasterRepository = $this->repositoryResolver->getRepository(self::LNG_MASTER_REPO);
        $this->permissionSetRepository = $this->repositoryResolver->getRepository(self::PERMSET_REPO);
        $this->permissionSetLangRepository = $this->repositoryResolver->getRepository(self::PERMSET_LANG_REPO);
        $this->ebiPermissionsetFeaturesRepository = $this->repositoryResolver->getRepository(self::PERMSET_FEATURE_REPO);
        $this->featureMasterLangRepository = $this->repositoryResolver->getRepository(self::PERMSET_FEATURE_MASTERLANG_REPO);
        $lang = $this->languageMasterRepository->find($langid);
        $lang = $this->catchLanguageException($lang);
        $permissionSet = $this->permissionSetRepository->find($Id);
        if (! $permissionSet) {
            throw new ValidationException([
                self::ERROR_PERMISSIONSET_NOT_FOUND
            ], self::ERROR_PERMISSIONSET_NOT_FOUND, self::ERROR_PERMISSIONSET_NOT_FOUND);
        }
        $permissionSetLang = $this->permissionSetLangRepository->findOneBy([
            self::FIELD_EBIPERMISSIONSET => $permissionSet->getId()
        ]);
        
        $profileDataBlocks = $this->dataBlockMasterLangRepository->getDatablocks('profile');
        $surveyDataBlocks = $this->dataBlockMasterLangRepository->getDatablocks('survey');
        
        $permissionSetDto = new PermissionSetDto();
        $permissionSetDto->setLangId($permissionSetLang->getLang()
            ->getId());
        $permissionSetDto->setLastUpdated($permissionSet->getModifiedAt());
        $permissionSetDto->setPermissionTemplateId($permissionSet->getId());
        $permissionSetDto->setPermissionTemplateName($permissionSetLang->getPermissionsetName());
        $status = ($permissionSet->getIsActive() != null) ? self::FIELD_ACTIVE : self::FIELD_ARCHIVE;
        $permissionSetDto->setPermissionTemplateStatus($status);
        $accessLevel = new AccessLevelDto();
        $accessLevel->setAggregateOnly($permissionSet->getAccesslevelAgg());
        $accessLevel->setIndividualAndAggregate($permissionSet->getAccesslevelIndAgg());
        $permissionSetDto->setAccessLevel($accessLevel);
        
        $coursesAccess = new CoursesAccessDto();
        $coursesAccess->setCreateViewAcademicUpdate((bool) $permissionSet->getCreateViewAcademicUpdate());
        $coursesAccess->setViewAllAcademicUpdateCourses((bool) $permissionSet->getViewAllAcademicUpdateCourses());
        $coursesAccess->setViewAllFinalGrades((bool) $permissionSet->getViewAllFinalGrades());
        $coursesAccess->setViewCourses((bool) $permissionSet->getViewCourses());
        $permissionSetDto->setCoursesAccess($coursesAccess);
        
        $permissionSetDto->setRiskindicator($permissionSet->getRiskIndicator());
        $permissionSetDto->setIntentToleave($permissionSet->getIntentToLeave());
        
        $permissionSetDto->setProfileBlocks($this->getEbiDataBlocks($profileDataBlocks, $permissionSet));
        $permissionSetDto->setSurveyBlocks($this->getEbiDataBlocks($surveyDataBlocks, $permissionSet));
        
        /**
         * Setting Feature Permissions
         */
        $featureDataBlockResponse = array();
        $activeFeatures = $this->featureMasterLangRepository->listFeaturesAll($langid);
        
        if (count($activeFeatures) > 0) {
            $featureDataBlockResponse = $this->getFeatureDataBlock($activeFeatures, $permissionSet, $featureDataBlockResponse);
        }
        $permissionSetDto->setFeatures($featureDataBlockResponse);
        return $permissionSetDto;
    }

    private function getFeatureDataBlock($activeFeatures, $permissionSet, $featureDataBlockResponse)
    {
        foreach ($activeFeatures as $activeFeature) {
            $permissionFeature = $this->ebiPermissionsetFeaturesRepository->findOneBy([
                self::FIELD_FEATURE => $activeFeature,
                self::FIELD_EBIPERMISSION_SET => $permissionSet
            ]);
            if ($permissionFeature) {
                $featureBlockDto = new FeatureBlockDto();
                $featureBlockDto->setName($activeFeature['feature_name']);
                $featureBlockDto->setId($activeFeature[self::FIELD_FEATUREID]);
                
                /**
                 * for referal only
                 */
                if ($activeFeature[self::FIELD_FEATUREID] == 1) {
                    $featureBlockDto->setReceiveReferrals($permissionFeature->getReceiveReferral());
                    
                    $featureReferralBlockDto = new FeatureBlockDto();
                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($permissionFeature->getPrivateCreate());
                    $featureReferralBlockDto->setPrivateShare($pValueDto);
                    
                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($permissionFeature->getPublicCreate());
                    $pValueDto->setView($permissionFeature->getPublicView());
                    $featureReferralBlockDto->setPublicShare($pValueDto);
                    
                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($permissionFeature->getTeamCreate());
                    $pValueDto->setView($permissionFeature->getTeamView());
                    $featureReferralBlockDto->setTeamsShare($pValueDto);
                    $featureBlockDto->setDirectReferral($featureReferralBlockDto);
                    
                    $featureReferralBlockDto = new FeatureBlockDto();
                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($permissionFeature->getReasonReferralPrivateCreate());
                    $featureReferralBlockDto->setPrivateShare($pValueDto);
                    
                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($permissionFeature->getReasonReferralPublicCreate());
                    $pValueDto->setView($permissionFeature->getReasonReferralPublicView());
                    $featureReferralBlockDto->setPublicShare($pValueDto);
                    
                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($permissionFeature->getReasonReferralTeamCreate());
                    $pValueDto->setView($permissionFeature->getReasonReferralTeamView());
                    $featureReferralBlockDto->setTeamsShare($pValueDto);
                    $featureBlockDto->setReasonRoutedReferral($featureReferralBlockDto);
                    
                }
                else{
                $pValueDto = new PermissionValueDto();
                $pValueDto->setCreate($permissionFeature->getPrivateCreate());
                $featureBlockDto->setPrivateShare($pValueDto);
                
                $pValueDto = new PermissionValueDto();
                $pValueDto->setCreate($permissionFeature->getPublicCreate());
                $pValueDto->setView($permissionFeature->getPublicView());
                $featureBlockDto->setPublicShare($pValueDto);
                
                $pValueDto = new PermissionValueDto();
                $pValueDto->setCreate($permissionFeature->getTeamCreate());
                $pValueDto->setView($permissionFeature->getTeamView());
                $featureBlockDto->setTeamsShare($pValueDto);
               }
                $featureDataBlockResponse[] = $featureBlockDto;
            }
        }
        return $featureDataBlockResponse;
    }

    private function getStatusCode($status)
    {
        if ($status == 'active') {
            $status = 1;
        } else 
            if ($status == 'archive') {
                $status = 0;
            } else {
                $status = '';
            }
        return $status;
    }

    private function getPermissionSets($status)
    {
        $this->permissionSetRepository = $this->repositoryResolver->getRepository(self::PERMSET_REPO);
        if ($status === 0 || $status == 1) {
            $permissionSets = $this->permissionSetRepository->findBy(array(
                'isActive' => $status
            ));
        } else {
            $permissionSets = $this->permissionSetRepository->findAll();
        }
        return $permissionSets;
    }

    public function listPermissionSetByStatus($langid, $status)
    {
        $this->dataBlockMasterLangRepository = $this->repositoryResolver->getRepository(self::DATABLOCK_MASTER_LANG_REPO);
        $this->languageMasterRepository = $this->repositoryResolver->getRepository(self::LNG_MASTER_REPO);
        $this->permissionSetRepository = $this->repositoryResolver->getRepository(self::PERMSET_REPO);
        $this->permissionSetLangRepository = $this->repositoryResolver->getRepository(self::PERMSET_LANG_REPO);
        $this->ebiPermissionsetFeaturesRepository = $this->repositoryResolver->getRepository(self::PERMSET_FEATURE_REPO);
        $this->featureMasterLangRepository = $this->repositoryResolver->getRepository(self::PERMSET_FEATURE_MASTERLANG_REPO);
        $status = $this->getStatusCode($status);
        $lang = $this->languageMasterRepository->find($langid);
        $lang = $this->catchLanguageException($lang);
        $permissionSets = $this->getPermissionSets($status);
        $countStatus = $this->permissionSetRepository->listPermissionsetCount();
        $response = array();
        $response[self::LANG_ID] = $langid;
        $response['permission_template_count_active'] = ($countStatus[0]['count_active']) ? $countStatus[0]['count_active'] : 0;
        $response['permission_template_count_archive'] = ($countStatus[0]['count_archive']) ? $countStatus[0]['count_archive'] : 0;
        $response['permission_template'] = [];
        $lastupdated = null;
        if ($permissionSets && count($permissionSets) > 0) {
            $response = $this->getPermissionTemplates($permissionSets, $status, $response);
        }
        return $response;
    }

    private function getPermissionTemplates($permissionSets, $status, $response)
    {
        $profileDataBlocksArray = $this->dataBlockMasterLangRepository->getDatablocks('profile');
        $surveyDataBlocksArray = $this->dataBlockMasterLangRepository->getDatablocks('survey');

        foreach ($permissionSets as $permissionSet) {
            $permissionSetDto = new permissionSetDto();
            $permissionSetDto->setPermissionTemplateId($permissionSet->getId());
            $permissionSetLang = $this->permissionSetLangRepository->findOneBy([
                self::FIELD_EBIPERMISSIONSET => $permissionSet->getId()
            ]);
            if ($permissionSetLang) {
                $permissionSetDto->setPermissionTemplateName($permissionSetLang->getPermissionsetName());
            }
            $status = ($permissionSet->getIsActive() == 1) ? self::FIELD_ACTIVE : self::FIELD_ARCHIVE;
            $permissionSetDto->setPermissionTemplateStatus($status);
            $accessLevel = new AccessLevelDto();
            $accessLevel->setAggregateOnly((bool) $permissionSet->getAccesslevelAgg());
            $accessLevel->setIndividualAndAggregate((bool) $permissionSet->getAccesslevelIndAgg());
            $permissionSetDto->setAccessLevel($accessLevel);
            
            $coursesAccess = new CoursesAccessDto();
            $coursesAccess->setCreateViewAcademicUpdate((bool) $permissionSet->getCreateViewAcademicUpdate());
            $coursesAccess->setViewAllAcademicUpdateCourses((bool) $permissionSet->getViewAllAcademicUpdateCourses());
            $coursesAccess->setViewAllFinalGrades((bool) $permissionSet->getViewAllFinalGrades());
            $coursesAccess->setViewCourses((bool) $permissionSet->getViewCourses());
            $permissionSetDto->setCoursesAccess($coursesAccess);
            
            $permissionSetDto->setRiskindicator((bool) $permissionSet->getRiskIndicator());
            $permissionSetDto->setIntentToleave((bool) $permissionSet->getIntentToLeave());
            $lastupdated = $permissionSet->getModifiedAt();
            $permissionSetDto->setPermissionTemplateLastUpdated($permissionSet->getModifiedAt());
            $permissionSetDto->setPermissionTemplateLastArchived($permissionSet->getInactiveDate());
            
            $profileDataBlocks = $this->getEbiDataBlocks($profileDataBlocksArray, $permissionSet, 'profile');
            $surveyDataBlocks = $this->getEbiDataBlocks($surveyDataBlocksArray, $permissionSet, 'survey');
            $permissionSetDto->setProfileBlocks($profileDataBlocks);
            $permissionSetDto->setSurveyBlocks($surveyDataBlocks);
            if (count($profileDataBlocks) > 0) {
                $lastupdated = $this->getLastUpdated($profileDataBlocks, $lastupdated);
            }
            if (count($surveyDataBlocks) > 0) {
                $lastupdated = $this->getLastUpdated($surveyDataBlocks, $lastupdated);
            }
            $featureDataBlockResponse = array();
            $activeFeatures = $this->featureMasterLangRepository->listFeaturesAll($response['lang_id']);
            if (count($activeFeatures) > 0) {
                $featureDataBlockResponse = $this->getActiveFeatureBlocks($activeFeatures, $permissionSet, $featureDataBlockResponse);
            }
            $permissionSetDto->setFeatures($featureDataBlockResponse);
            $response['permission_template'][] = $permissionSetDto;
        }
        
        return $response;
    }

    private function getLastUpdated($dataBlocks, $lastupdated)
    {
        foreach ($dataBlocks as $dataBlock) {
            if ($lastupdated < $dataBlock->getLastUpdated()) {
                $lastupdated = $dataBlock->getLastUpdated();
            }
        }
        return $lastupdated;
    }

    private function getActiveFeatureBlocks($activeFeatures, $permissionSet, $featureDataBlockResponse)
    {
        foreach ($activeFeatures as $activeFeature) {
            $permissionFeature = $this->ebiPermissionsetFeaturesRepository->findOneBy([
                self::FIELD_FEATURE => $activeFeature,
                self::FIELD_EBIPERMISSION_SET => $permissionSet
            ]);
            if ($permissionFeature) {
                $featureBlockDto = new FeatureBlockDto();
                $featureBlockDto->setId($activeFeature[self::FIELD_FEATUREID]);
                $featureBlockDto->setName($activeFeature['feature_name']);
                // for referal only
                if ($activeFeature[self::FIELD_FEATUREID] == 1) {
                    $featureBlockDto->setReceiveReferrals($permissionFeature->getReceiveReferral());
                    
                    $featureReferralBlockDto = new FeatureBlockDto();
                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($permissionFeature->getPrivateCreate());
                    $featureReferralBlockDto->setPrivateShare($pValueDto);
                    
                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($permissionFeature->getPublicCreate());
                    $pValueDto->setView($permissionFeature->getPublicView());
                    $featureReferralBlockDto->setPublicShare($pValueDto);
                    
                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($permissionFeature->getTeamCreate());
                    $pValueDto->setView($permissionFeature->getTeamView());
                    $featureReferralBlockDto->setTeamsShare($pValueDto);
                    $featureBlockDto->setDirectReferral($featureReferralBlockDto);
                    
                    $featureReferralBlockDto = new FeatureBlockDto();
                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($permissionFeature->getReasonReferralPrivateCreate());
                    $featureReferralBlockDto->setPrivateShare($pValueDto);
                    
                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($permissionFeature->getReasonReferralPublicCreate());
                    $pValueDto->setView($permissionFeature->getReasonReferralPublicView());
                    $featureReferralBlockDto->setPublicShare($pValueDto);
                    
                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($permissionFeature->getReasonReferralTeamCreate());
                    $pValueDto->setView($permissionFeature->getReasonReferralTeamView());
                    $featureReferralBlockDto->setTeamsShare($pValueDto);
                    $featureBlockDto->setReasonRoutedReferral($featureReferralBlockDto);
                    
                }
                else{
                $pValueDto = $this->setPermissionValDto($featureBlockDto, $permissionFeature);
                $featureBlockDto->setTeamsShare($pValueDto);
                }
                $featureDataBlockResponse[] = $featureBlockDto;
            }
        }
        return $featureDataBlockResponse;
    }

    public function isPermissionSetExists($name)
    {
        $this->permissionSetLangRepository = $this->repositoryResolver->getRepository(self::PERMSET_LANG_REPO);
        $permissionname = $this->permissionSetLangRepository->findByPermissionsetName($name);
        $result = ($permissionname) ? true : false;
        return $result;
    }

    protected function createEbiDataBlocks($blocks, $ebiPermissionSet, $type = SELF::INSERT)
    {
        $this->dataBlockMasterRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:DatablockMaster");
        $this->ebiPermissionsetDatablockRepository = $this->repositoryResolver->getRepository(self::PERMSET_DATABLOCK_REPO);
        if (count($blocks) > 0) {
            
            foreach ($blocks as $block) {
                $this->insertUpdateBlocks($block, $ebiPermissionSet, $type);
            }
        }
        $this->ebiPermissionsetDatablockRepository->flush();
    }

    private function insertUpdateBlocks($block, $ebiPermissionSet, $type)
    {
        /**
         * checking the block selection-if true only inserts
         */
        if ($type == SELF::INSERT) {
            
            if ($block->getBlockSelection()) {
                
                $ebiDataBlock = new EbiPermissionsetDatablock();
                $blockId = $block->getBlockId();
                $dataBlock = $this->dataBlockMasterRepository->find($blockId);
                $dataBlock = $this->catchDatablockException($dataBlock, $blockId);
                $ebiDataBlock->setBlockType($dataBlock->getBlockType());
                $ebiDataBlock->setDataBlock($dataBlock);
                $ebiDataBlock->setPermissionset($ebiPermissionSet);
                $this->ebiPermissionsetDatablockRepository->createEbiPermissionsetDatablock($ebiDataBlock);
            }
        } else {
            /**
             * Update
             */
            $ebiDataBlock = $this->ebiPermissionsetDatablockRepository->findOneBy([
                self::FIELD_EBIPERMISSION_SET => $ebiPermissionSet,
                self::DATA_BLOCK => $block->getBlockId()
            ]);
            if ($ebiDataBlock && ! $block->getBlockSelection()) {
                $this->ebiPermissionsetDatablockRepository->remove($ebiDataBlock);
            } elseif (! ($ebiDataBlock) && $block->getBlockSelection()) {
                $ebiDataBlock = new EbiPermissionsetDatablock();
                $blockId = $block->getBlockId();
                $dataBlock = $this->dataBlockMasterRepository->find($blockId);
                $dataBlock = $this->catchDatablockException($dataBlock, $blockId);
                
                $ebiDataBlock->setBlockType($dataBlock->getBlockType());
                $ebiDataBlock->setDataBlock($dataBlock);
                $ebiDataBlock->setPermissionset($ebiPermissionSet);
                $this->ebiPermissionsetDatablockRepository->persist($ebiDataBlock, false);
            }
        }
    }


    protected function getEbiDataBlocks($blocks, $permissionSet, $type = null)
    {
        $this->ebiPermissionsetDatablockRepository = $this->repositoryResolver->getRepository(self::PERMSET_DATABLOCK_REPO);
        $profileDataBlocksResponse = array();
        $dataBlocks = array();
        $dataBlocks = $this->ebiPermissionsetDatablockRepository->getEbiDataBlockID($permissionSet, $type);
        if (! empty($dataBlocks)) {
            $dataBlocks = call_user_func_array('array_merge', $dataBlocks);
        }
        if ($type != '') {
            $profileDataBlocksResponse = $this->getEbiSurveyProfileDataBlocks($dataBlocks, $blocks);
        } else 
            if (count($blocks) > 0) {
                foreach ($blocks as $block) {
                    $blockDto = new BlockDto();
                    $blockDto->setBlockName($block['datablock_name']);
                    $blockDto->setBlockId($block[self::FIELD_DATABLOCKID]);
                    if (in_array($block[self::FIELD_DATABLOCKID], $dataBlocks)) {
                        $blockDto->setBlockSelection(true);
                        // $blockDto->setLastUpdated($blocksPermission->getModifiedAt());
                    } else {
                        $blockDto->setBlockSelection(false);
                    }
                    
                    $profileDataBlocksResponse[] = $blockDto;
                }
            }
        return $profileDataBlocksResponse;
    }

    private function getEbiSurveyProfileDataBlocks($dataBlocks, $blocks)
    {
        $profileDataBlocksResponse = array();
        if (! empty($dataBlocks)) {
            foreach ($blocks as $block) {
                if (in_array($block[self::FIELD_DATABLOCKID], $dataBlocks)) {
                    $blockDto = new BlockDto();
                    $blockDto->setBlockName($block['datablock_name']);
                    $blockDto->setBlockId($block[self::FIELD_DATABLOCKID]);
                    $blockDto->setBlockSelection(true);
                    $profileDataBlocksResponse[] = $blockDto;
                }
            }
        }
        return $profileDataBlocksResponse;
    }

    private function catchDatablockException($dataBlock, $blockId)
    {
        if (! $dataBlock) {
            throw new ValidationException([
                self::ERROR_DATABLOCK_NOT_FOUND . $blockId
            ], self::ERROR_DATABLOCK_NOT_FOUND, self::ERROR_KEY);
        } else {
            return $dataBlock;
        }
    }

    private function catchLanguageException($lang)
    {
        if (! isset($lang)) {
            throw new ValidationException([
                self::ERROR_LANGUAGE_NOT_FOUND
            ], self::ERROR_LANGUAGE_NOT_FOUND, self::ERROR_LANGUAGE_NOT_FOUND_KEY);
        } else {
            return $lang;
        }
    }

    private function setPermissionValDto($featureBlockDto, $permissionFeature)
    {
        $pValueDto = new PermissionValueDto();
        $pValueDto->setCreate($permissionFeature->getPrivateCreate());
        $featureBlockDto->setPrivateShare($pValueDto);
        
        $pValueDto = new PermissionValueDto();
        $pValueDto->setCreate($permissionFeature->getPublicCreate());
        $pValueDto->setView($permissionFeature->getPublicView());
        $featureBlockDto->setPublicShare($pValueDto);
        
        $pValueDto = new PermissionValueDto();
        $pValueDto->setCreate($permissionFeature->getTeamCreate());
        $pValueDto->setView($permissionFeature->getTeamView());
        
        return $pValueDto;
    }

    private function catchError($errors)
    {
        if (count($errors) > 0) {
            $errorsString = $errors[0]->getMessage();
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'permissionsetname_duplicate_error');
        }
    }
}