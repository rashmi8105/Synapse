<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Dump\Container;
use Synapse\CoreBundle\Entity\DatablockMetadata;
use Synapse\CoreBundle\Entity\EbiMetadata;
use Synapse\CoreBundle\Entity\EbiMetadataLang;
use Synapse\CoreBundle\Entity\EbiMetadataListValues;
use Synapse\CoreBundle\Entity\MetadataListValues;
use Synapse\CoreBundle\Entity\MetadataMaster;
use Synapse\CoreBundle\Entity\MetadataMasterLang;
use Synapse\CoreBundle\Repository\PersonEbiMetaDataRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\ProfileServiceInterface;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\OrgProfileConstant;
use Synapse\CoreBundle\Util\Constants\ProfileConstant;
use Synapse\DataBundle\Util\Constants\ProfileBlocksConstants;
use Synapse\RestBundle\Entity\ProfileDto;
use Synapse\RestBundle\Entity\ReOrderProfileDto;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("profile_service")
 */
class ProfileService extends AbstractService implements ProfileServiceInterface
{

    const SERVICE_KEY = 'profile_service';

    // Scaffolding
    /**
     *
     * @var Container
     */
    private $container;


    /**
     * @var Manager
     */
    private $rbacManager;

    // Services
    /**
     *
     * @var LanguageMasterService
     */
    private $languageMasterService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    // Repositories
    /**
     *
     * @var DatablockMasterRepository
     */
    private $datablockMasterRepository;

    /**
     *
     * @var DatablockMetadataRepository
     */
    private $datablockMetadataRepository;

    /**
     *
     * @var EbiMetadataLangRepository
     */
    private $ebiMetadataLangRepository;

    /**
     *
     * @var EbimetadataRepository
     */
    private $ebiMetadataRepository;


    /**
     * @var PersonEbiMetaDataRepository
     */
    private $personEbiMetadataRepository;

    /**
     *
     * @DI\InjectParams({
     *          "repositoryResolver" = @DI\Inject("repository_resolver"),
     *          "logger" = @DI\Inject("logger"),
     *          "container" = @DI\Inject("service_container")
     *          })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        // Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->rbacManager = $this->container->get(SynapseConstant::TINYRBAC_MANAGER);

        //Services
        $this->languageMasterService = $this->container->get(LanguageMasterService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);

        //Repositories
        $this->personEbiMetadataRepository = $this->repositoryResolver->getRepository(PersonEbiMetaDataRepository::REPOSITORY_KEY);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Synapse\CoreBundle\Service\ProfileServiceInterface::createProfile()
     */
    public function createProfile(ProfileDto $profileDto)
    {
        //$this->rbacManager->checkAccessToOrganization($profileDto->getOrganizationId());

        $this->container->get('orgprofile_service')->checkExistingColumnNames($profileDto->getOrganizationId(),$profileDto->getItemLabel(),'ISP');

        $logContent = $this->container->get('loggerhelper_service')->getLog($profileDto);
        $this->logger->debug(" Creating Profile -  " . $logContent);

        $response = "";
        $defType = array(
            
            'E'
        );
        $validCalenderAssignment = array(
            'N',
            'Y',
            'T'
        );
        
        if (! in_array($profileDto->getDefinitionType(), $defType)) {
            $this->logger->info("The definition type is invalid. " . $profileDto->getDefinitionType());
            throw new ValidationException([
                'The definition type is invalid.'
            ]);
        } elseif ($profileDto->getCalenderAssignment() != null && ! in_array($profileDto->getCalenderAssignment(), $validCalenderAssignment)) {
            $this->logger->info("The calender assignment is invalid.");
            throw new ValidationException([
                'The calender assignment is invalid.'
            ]);
        } else {
            $response = $this->createProfileItem($profileDto);
        }
        $this->logger->info("Created Profile ");
        return $response;
    }

    /**
     * This function will be remove once the upload story handle the new database changes
     *
     * @param ProfileDto $profileDto            
     * @throws ValidationException
     * @return Ambigous <unknown, multitype:unknown >
     */
    public function createProfileItemTemp(ProfileDto $profileDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($profileDto);
        $this->logger->debug(" Creating Profile Item Temp -  " . $logContent);

        $this->metadataMasterRepository = $this->repositoryResolver->getRepository(ProfileConstant::METADATA_REPO);
        $this->metadataMasterLangRepository = $this->repositoryResolver->getRepository(ProfileConstant::METADATA_MASTER_REPO);
        $response = array();
        $lang = $this->languageMasterService->getLangRepository()->find($profileDto->getLangId());
        if (! $lang) {
            throw new ValidationException([
                ProfileConstant::LANG_NOT_FOUND
            ], ProfileConstant::LANG_NOT_FOUND, 'lang_not_found');
        }
        
        if ($profileDto->getDefinitionType() == 'O') {
            $organization = $this->organizationService->find($profileDto->getOrganizationId());
            $sequence = $this->metadataMasterRepository->getOrgProfileCount($organization);
        } else {
            $sequence = $this->metadataMasterRepository->getEbiProfileCount();
        }
        
        /**
         * Assign to next values
         */
        $sequence ++;
        
        /**
         * Create Entries in MetadataMaster
         */
        
        $metadataMaster = new MetadataMaster();
        $metadataMaster->setSequence($sequence);
        $metadataMaster->setMetadataType($profileDto->getItemDataType());
        $metadataMaster->setDefinitionType($profileDto->getDefinitionType());
        if ($profileDto->getItemDataType() == 'N') {
            
            $numberDetail = $profileDto->getNumberType();
            if (count($numberDetail) > 0) {
                if (is_numeric($numberDetail[ProfileConstant::MIN_DIGITS])) {
                    $metadataMaster->setMinRange($numberDetail[ProfileConstant::MIN_DIGITS]);
                }
                if (is_numeric($numberDetail[ProfileConstant::MAX_DIGITS])) {
                    $metadataMaster->setMaxRange($numberDetail[ProfileConstant::MAX_DIGITS]);
                }
                $metadataMaster->setNoOfDecimals($numberDetail[ProfileConstant::DECIMAL_POINTS]);
            }
        }
        if ($profileDto->getDefinitionType() == 'O') {
            $metadataMaster->setOrganization($organization);
        }
        
        $metadataMaster->setKey($profileDto->getItemLabel());
        
        $validator = $this->container->get(ProfileConstant::VALIDATOR);
        $errors = $validator->validate($metadataMaster);
        $this->catchError($errors);
        
        $metadataMaster = $this->metadataMasterRepository->create($metadataMaster);
        
        /**
         * Create Entries in MetadataMasterLang
         */
        $metadataMasterLang = new MetadataMasterLang();
        
        $metadataMasterLang->setLang($lang);
        $metadataMasterLang->setMetadata($metadataMaster);
        $metadataMasterLang->setMetaName($profileDto->getItemLabel());
        $metadataMasterLang->setMetaDescription($profileDto->getItemSubtext());
        $metadataMasterLang = $this->metadataMasterLangRepository->create($metadataMasterLang);
        
        $response[ProfileConstant::META_DATA_MASTER] = $metadataMaster;
        $response[ProfileConstant::META_DATA_MASTER_LANG] = $metadataMasterLang;
        
        $metadataListValues = $profileDto->getCategoryType();
        if ($metadataMaster->getMetadatatype() == 'S' && isset($metadataListValues) && count($metadataListValues) > 0) {
            $this->metaDataListValuesRepository = $this->repositoryResolver->getRepository(ProfileConstant::METADATA_LIST_REPO);
            $valArray = array();
            foreach ($metadataListValues as $metadataListValue) {
                if (in_array($metadataListValue[ProfileConstant::VAL], $valArray)) {
                    $errorsString = "List Value already exists.";
                    throw new ValidationException([
                        $errorsString
                    ], $errorsString, ProfileBlocksConstants::METALISTVAL_DUP_ERR);
                }
                $valArray[] = $metadataListValue[ProfileConstant::VAL];
                $listVals = new MetadataListValues();
                $listVals->setLang($lang);
                $listVals->setMetadata($metadataMaster);
                $listVals->setListName($metadataListValue[ProfileConstant::ANSWER]);
                $listVals->setListValue($metadataListValue[ProfileConstant::VAL]);
                $listVals->setSequence($metadataListValue[ProfileConstant::SEQUENCE_NO]);
                $listVals = $this->metaDataListValuesRepository->create($listVals);
                $response['meta_data_list'][] = $listVals;
            }
        }
        $this->metadataMasterRepository->flush();
    }

    /**
     * Create EBI Specific Profiles
     *
     * @param ProfileDto $profileDto            
     * @return void|\Synapse\RestBundle\Entity\Error
     */
    public function createProfileItem(ProfileDto $profileDto)
    {
        //$this->rbacManager->checkAccessToOrganization($profileDto->getOrganizationId());
        $logContent = $this->container->get('loggerhelper_service')->getLog($profileDto);
        $this->logger->debug(" Creating Profile Item-  " . $logContent);
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_REPO);
        $this->ebiMetadataLangRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_LANG_REPO);
        $this->datablockMasterRepository = $this->repositoryResolver->getRepository(ProfileConstant::DATABLOCK_MASTER_REPO);
        $this->datablockMetadataRepository = $this->repositoryResolver->getRepository(ProfileConstant::DATABLOCK_METADATA_REPO);
        
        $response = array();
        $lang = $this->languageMasterService->getLangRepository()->find($profileDto->getLangId());
        $this->isValidLang($lang);
        
        $sequence = $this->ebiMetadataRepository->getEbiProfileCount();
        
        /**
         * Assign to next values
         */
        $sequence ++;
        
        /**
         * Create Entries in EbiMetadata
         */
        
        $ebiMetadata = new EbiMetadata();
        $ebiMetadata->setSequence($sequence);
        $ebiMetadata->setMetadataType($profileDto->getItemDataType());
        $ebiMetadata->setDefinitionType($profileDto->getDefinitionType());
        $this->setMinMax($profileDto, $ebiMetadata);
        if(empty($profileDto->getItemLabel())){
            $itemLabel = preg_replace('/\s+/', '', $profileDto->getDisplayName());
            $ebiMetadata->setKey($itemLabel);
        }else{
            $ebiMetadata->setKey($profileDto->getItemLabel());
        }
        $ebiMetadata->setScope($profileDto->getCalenderAssignment());
        $ebiMetadata->setStatus(ProfileConstant::ACTIVE);
        $validator = $this->container->get(ProfileConstant::VALIDATOR);
        $errors = $validator->validate($ebiMetadata);
        $this->catchError($errors);
        $this->isPredefinedProfile($profileDto->getItemLabel());
        $ebiMetadata = $this->ebiMetadataRepository->create($ebiMetadata);
        
        /**
         * Create Entries in EbiMetadataLang
         */
        $ebiMetadataLang = new EbiMetadataLang();
        
        $ebiMetadataLang->setLang($lang);
        $ebiMetadataLang->setEbiMetadata($ebiMetadata);
        $ebiMetadataLang->setMetaName($profileDto->getDisplayName());
        $ebiMetadataLang->setMetaDescription($profileDto->getItemSubtext());
        $errorsMetaLang = $validator->validate($ebiMetadataLang);
        $this->catchError($errorsMetaLang);
        $ebiMetadataLang = $this->ebiMetadataLangRepository->persist($ebiMetadataLang);
        
        /*
         * Check for Profile Blocks in exists in DatablockMaster
         */
        $profileBlockId = $profileDto->getProfileBlockId();
        if ($profileBlockId != "") {
            $profileBlocks = $this->datablockMasterRepository->find($profileBlockId);
            if (! isset($profileBlocks)) {
                throw new ValidationException([
                    ProfileConstant::PROFILE_BLOCKS_NOT_FOUND
                ], ProfileConstant::PROFILE_BLOCKS_NOT_FOUND, ProfileConstant::PROFILE_BLOCKS_NOT_FOUND_KEY);
            }
            
            /**
             * Create Entries in DatablockMetadata
             */
            
            $datablockMetadata = new DatablockMetadata();
            $datablockMetadata->setDatablock($profileBlocks);
            $datablockMetadata->setEbiMetadata($ebiMetadata);
            $datablockMetadata = $this->datablockMetadataRepository->persist($datablockMetadata, null);
        }
        
        $ebiMdataListValues = $profileDto->getCategoryType();
        if ($ebiMetadata->getMetadatatype() == 'S' && isset($ebiMdataListValues) && count($ebiMdataListValues) > 0) {
            $this->createMetaDataList($ebiMdataListValues, $lang, $ebiMetadata, $response);
        }
        $this->ebiMetadataRepository->flush();
        
        $profileDto->setId($ebiMetadata->getId());
        $profileDto->setSequenceNo($sequence);
        $profileDto->setStatus($ebiMetadata->getStatus());
        return $profileDto;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Synapse\CoreBundle\Service\ProfileServiceInterface::updateProfile()
     */
    public function updateProfile(ProfileDto $profileDto)
    {
        //$this->rbacManager->checkAccessToOrganization($profileDto->getOrganizationId());
        $logContent = $this->container->get('loggerhelper_service')->getLog($profileDto);
        $this->logger->debug(" Updating Profile -  " . $logContent);

        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_REPO);
        $this->ebiMetadataLangRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_LANG_REPO);
        $this->datablockMasterRepository = $this->repositoryResolver->getRepository(ProfileConstant::DATABLOCK_MASTER_REPO);
        $this->datablockMetadataRepository = $this->repositoryResolver->getRepository(ProfileConstant::DATABLOCK_METADATA_REPO);
        $ebiMetadataId = $profileDto->getId();
        $langId = $profileDto->getLangId();
        $lang = $this->languageMasterService->getLangRepository()->find($langId);
        $this->isValidLang($lang);
        
        $ebiMetadata = $this->getValidProfile($ebiMetadataId);
        $ebiMetadata = $this->ebiMetadataRepository->find($ebiMetadataId);
        if ($this->repositoryResolver->getRepository(ProfileConstant::PERSON_EBI_META)->isDataAttched($ebiMetadataId)) {
            throw new ValidationException([
                ProfileConstant::DATA_ATT_TO_PROFILE
            ], ProfileConstant::DATA_ATT_TO_PROFILE, 'data_attached');
        }
        $this->manageDatablockMapping($profileDto, $ebiMetadata);
        $ebiMetadataLang = $this->ebiMetadataLangRepository->findOneBy(array(
            ProfileConstant::METADATA => $ebiMetadata,
            'lang' => $lang
        ));
        
        if (! isset($ebiMetadataLang)) {
            throw new ValidationException([
                'Profile Lang not found .'
            ], 'Profile Lang not found .', 'profile_lang_not_found');
        }
        
        /**
         * Setting All values In EbiMetadata
         */
        
        $ebiMetadata->setMetadataType($profileDto->getItemDataType());
        $this->setMinMax($profileDto, $ebiMetadata);
        $ebiMetadata->setScope($profileDto->getCalenderAssignment());
        $ebiMetadata->setKey($profileDto->getItemLabel());
        
        $validator = $this->container->get(ProfileConstant::VALIDATOR);
        $errors = $validator->validate($ebiMetadata);
        
        $this->catchError($errors);
        $this->isPredefinedProfile($profileDto->getItemLabel());
        /**
         * Settiing values For EbiMatadataLang
         */
        
        $ebiMetadataLang->setMetaName($profileDto->getDisplayName());
        $ebiMetadataLang->setMetaDescription($profileDto->getItemSubtext());
        $errorsMetaLang = $validator->validate($ebiMetadataLang);
        $this->catchError($errorsMetaLang);
        
        $this->removeMetaListValues($ebiMetadata, $lang);
        
        $ebiMdataListValues = $profileDto->getCategoryType();
        if ($ebiMetadata->getMetadatatype() == 'S' && isset($ebiMdataListValues) && count($ebiMdataListValues) > 0) {
            $this->createMetaDataList($ebiMdataListValues, $lang, $ebiMetadata, []);
        }
        $this->ebiMetadataRepository->flush();
        $status = (!empty($ebiMetadata->getStatus())) ? $ebiMetadata->getStatus() : '';
        $profileDto->setStatus($status);
        return $profileDto;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Synapse\CoreBundle\Service\ProfileServiceInterface::reorderProfile()
     */
    public function reorderProfile(ReOrderProfileDto $reOrderProfileDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($reOrderProfileDto);
        $this->logger->debug(" ReOrder Profile -  " . $logContent);
        
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_REPO);
        $ebiMetadataId = $reOrderProfileDto->getId();
        $newSequenceId = $reOrderProfileDto->getSequenceNo();
        $ebiMetadata = $this->ebiMetadataRepository->find($ebiMetadataId);
        $this->getValidProfile($ebiMetadataId);
        
        $oldSequenceId = $ebiMetadata->getSequence();
        
        $maxSequenceId = $this->getMaxSequenceId();
        
        if ($newSequenceId > $maxSequenceId) {
            $newSequenceId = $maxSequenceId;
        }
        
        if ($oldSequenceId < $newSequenceId) {
            for ($i = $oldSequenceId + 1; $i <= $newSequenceId; $i ++) {
                $ebiMetadataSequence = $this->getMasterSequence($i);
                if ($ebiMetadataSequence) {
                    $sequence = $i - 1;
                    $ebiMetadataSequence->setSequence($sequence);
                    $this->ebiMetadataRepository->merge($ebiMetadataSequence);
                }
            }
            $ebiMetadata->setSequence($newSequenceId);
            $this->ebiMetadataRepository->merge($ebiMetadata);
        }
        
        if ($oldSequenceId > $newSequenceId) {
            for ($i = $oldSequenceId - 1; $i >= $newSequenceId; $i --) {
                $ebiMetadataSequence = $this->getMasterSequence($i);
                if ($ebiMetadataSequence) {
                    $sequence = $i + 1;
                    $ebiMetadataSequence->setSequence($sequence);
                    $this->ebiMetadataRepository->merge($ebiMetadataSequence);
                }
            }
            $ebiMetadata->setSequence($newSequenceId);
            $this->ebiMetadataRepository->merge($ebiMetadata);
        }
        $this->ebiMetadataRepository->flush();
        return $ebiMetadata;
    }

    public function deleteProfile($ebiMetadataId)
    {
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_REPO);
        $this->ebiMetadataLangRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_LANG_REPO);
        $this->ebiMetaDataListValuesRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_LIST_REPO);
        $this->getValidProfile($ebiMetadataId);
        $auMetadataRepo = $this->repositoryResolver->getRepository('SynapseAcademicUpdateBundle:AcademicUpdateRequestMetadata');
        $isIspExists = $auMetadataRepo->isEbiExists($ebiMetadataId);
        if (count($isIspExists) > 0) {
            throw new ValidationException([
                'Academic Update attached this profile'
            ], 'Academic Update attached this profile', 'au_profile_reference_error');
        }
        $ebiMetadata = $this->ebiMetadataRepository->find($ebiMetadataId);
        if ($this->repositoryResolver->getRepository(ProfileConstant::PERSON_EBI_META)->isDataAttched($ebiMetadataId)) {
            throw new ValidationException([
                ProfileConstant::DATA_ATT_TO_PROFILE
            ], ProfileConstant::DATA_ATT_TO_PROFILE, 'data_attached');
        }
        $definitionType = $ebiMetadata->getDefinitionType();
        $oldSeq = $ebiMetadata->getSequence();
        $ebiMasterLang = $this->ebiMetadataLangRepository->findBy(array(
            ProfileConstant::METADATA => $ebiMetadata
        ));
        if ($ebiMasterLang) {
            foreach ($ebiMasterLang as $mLang) {
                $this->ebiMetadataLangRepository->remove($mLang);
            }
        }
        $masterValues = $this->ebiMetaDataListValuesRepository->findBy(array(
            ProfileConstant::METADATA => $ebiMetadata
        ));
        if ($masterValues) {
            foreach ($masterValues as $mValue) {
                $this->ebiMetaDataListValuesRepository->remove($mValue);
            }
        }
        $ebiMetadata->setSequence(NULL);
        $this->ebiMetadataRepository->flush();
        $this->ebiMetadataRepository->remove($ebiMetadata);
        $this->ebiMetadataRepository->flush();
        $this->ebiMetadataRepository->clear();
        $ebiMetadataSequence = NULL;
        
        $ebiMetadataSequence = $this->ebiMetadataRepository->findOneBy(array(
            ProfileConstant::SEQUENCE => ($oldSeq + 1),
            ProfileConstant::DEFINITION_TYPE => $definitionType
        ));
        
        /* Reseting Sequence */
        
        while ($ebiMetadataSequence) {
            $ebiMetadataSequence->setSequence(($ebiMetadataSequence->getSequence() - 1));
            $oldSeq ++;
            $ebiMetadataSequence = $this->ebiMetadataRepository->findOneBy(array(
                ProfileConstant::SEQUENCE => ($oldSeq + 1),
                ProfileConstant::DEFINITION_TYPE => $definitionType
            ));
        }
        $this->ebiMetadataRepository->flush();
        
        return $ebiMetadata;
    }

    public function getProfiles($status = 'all')
    {
        $this->ebiMetadataLangRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_LANG_REPO);
        $ebiItems = $this->ebiMetadataLangRepository->getProfiles($status);
        $archivedItemCount = $this->ebiMetadataLangRepository->getProfiles('archive');
        $profileItemsRes = array();
        $profileItemsArr = array();
        $responseArr = array();
        $profileItemsRes['total_archive_count'] = count($archivedItemCount);
        foreach ($ebiItems as $ebiItem) {
            $profileItemsArr['id'] = $ebiItem['id'];
            $profileItemsArr['modified_at'] = $ebiItem['modified_at'];
			$profileItemsArr['item_label'] = $ebiItem['item_label'];
            $profileItemsArr['display_name'] = $ebiItem['display_name'];
            $profileItemsArr['item_subtext'] = $ebiItem['item_subtext'];
            $profileItemsArr['item_data_type'] = $ebiItem['item_data_type'];
            $profileItemsArr['definition_type'] = $ebiItem['definition_type'];
            $profileItemsArr['decimal_points'] = $ebiItem['decimal_points'];
            $profileItemsArr['min_range'] = $ebiItem['min_range'];
            $profileItemsArr['max_range'] = $ebiItem['max_range'];
            $profileItemsArr['sequence_no'] = $ebiItem['sequence_no'];
            $profileItemsArr[ProfileConstant::PROFILE_BLOCK_ID] = $ebiItem[ProfileConstant::PROFILE_BLOCK_ID];
            $profileItemsArr[ProfileConstant::PROFILE_BLOCK_NAME] = $ebiItem[ProfileConstant::PROFILE_BLOCK_NAME];
            if($ebiItem[ProfileConstant::STATUS] == ProfileConstant::ACTIVE || $ebiItem[ProfileConstant::STATUS] == null){
                $status = ProfileConstant::ACTIVE;
            }else{
                $status = 'archive';
            }
            $profileItemsArr[ProfileConstant::STATUS] = $status;
            
            if ($ebiItem['pom_id'] or $ebiItem['au_id']) {
                $profileItemsArr['item_used'] = true;
            } else {
                $profileItemsArr['item_used'] = false;
            }
            $responseArr[] = $profileItemsArr;
            unset($profileItemsArr);
        }
        $profileItemsRes['profile_items'] = $responseArr;
        return $profileItemsRes;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Synapse\CoreBundle\Service\ProfileServiceInterface::getProfile()
     */
    public function getProfile($ebiMetadataId)
    {
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_REPO);
        $this->ebiMetadataLangRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_LANG_REPO);
        $this->ebiMetaDataListValuesRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_LIST_REPO);
        
        $profileItems = $this->ebiMetadataRepository->getProfileWithProfileBlock($ebiMetadataId);
        $ebiMetadataMaster = $this->getValidProfile($ebiMetadataId);
        if (! $ebiMetadataMaster) {
            return false;
        }
        $profileDto = new ProfileDto();
        $profileDto->setId($ebiMetadataMaster->getId());
        $profileDto->setDefinitionType($ebiMetadataMaster->getDefinitionType());
        $profileDto->setItemDataType($ebiMetadataMaster->getMetadataType());
        $profileDto->setSequenceNo($ebiMetadataMaster->getSequence());
        $profileDto->setCalenderAssignment(is_null($ebiMetadataMaster->getScope()) ? "" : $ebiMetadataMaster->getScope());
        
        if ($ebiMetadataMaster->getMetadataType() == 'N') {
            $numberArray = array();
            $numberArray[ProfileConstant::MIN_DIGITS] = is_null($ebiMetadataMaster->getMinRange()) ? "" : (double) $ebiMetadataMaster->getMinRange();
            $numberArray[ProfileConstant::MAX_DIGITS] = is_null($ebiMetadataMaster->getMaxRange()) ? "" : (double) $ebiMetadataMaster->getMaxRange();
            $numberArray[ProfileConstant::DECIMAL_POINTS] = is_null($ebiMetadataMaster->getNoOfDecimals()) ? "" : (double) $ebiMetadataMaster->getNoOfDecimals();
            $profileDto->setNumberType($numberArray);
        }
        $ebiMetadataLang = $this->ebiMetadataLangRepository->findOneBy(array(
            ProfileConstant::METADATA => $ebiMetadataMaster
        ));
        
        if ($profileItems) {
            $profileDto->setProfileBlockId($profileItems[0][ProfileConstant::PROFILE_BLOCK_ID]);
            $profileDto->setProfileBlockName($profileItems[0][ProfileConstant::PROFILE_BLOCK_NAME]);
        } else {
            $profileDto->setProfileBlockId(0);
            $profileDto->setProfileBlockName('');
        }
        
        $profileDto->setItemLabel($ebiMetadataMaster->getKey());
        $profileDto->setDisplayName($ebiMetadataLang->getMetaName());
        $profileDto->setItemSubtext(empty($ebiMetadataLang->getMetaDescription()) ? "" : $ebiMetadataLang->getMetaDescription());
        
        if ($ebiMetadataMaster->getMetadataType() == 'S') {
            $metaListValues = $this->ebiMetaDataListValuesRepository->findBy(array(
                ProfileConstant::METADATA => $ebiMetadataMaster
            ));
            
            $listValues = array();
            if (count($metaListValues) > 0) {
                foreach ($metaListValues as $metaListValue) {
                    if ($metaListValue) {
                        $listval = array();
                        
                        $listval[ProfileConstant::ANSWER] = $metaListValue->getListName();
                        $listval[ProfileConstant::VAL] = $metaListValue->getListValue();
                        $listval[ProfileConstant::SEQUENCE_NO] = $metaListValue->getSequence();
                        $listValues[] = $listval;
                    }
                }
                $profileDto->setCategoryType($listValues);
            }
        }
        $status = ($ebiMetadataMaster->getStatus() == 'active' || empty($ebiMetadataMaster->getStatus())) ? 'active' : 'archive';
        $profileDto->setStatus($status);
        return $profileDto;
    }

    public function createMetaDataList($ebiMdataListValues, $lang, $ebiMetadata, $response)
    {
        $this->ebiMetaDataListValuesRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_LIST_REPO);
        $valArray = array();
        $ansArray = [];
        foreach ($ebiMdataListValues as $ebiMetadataListValue) {
            if (in_array($ebiMetadataListValue[ProfileConstant::VAL], $valArray)) {
                $errorsString = "List Value already exists.";
                throw new ValidationException([
                    $errorsString
                ], $errorsString, ProfileBlocksConstants::METALISTVAL_DUP_ERR);
            }
            if (in_array($ebiMetadataListValue[ProfileConstant::ANSWER], $ansArray)) {
                $errorsString = "List Answer already exists.";
                throw new ValidationException([
                    $errorsString
                ], $errorsString, ProfileBlocksConstants::METALISTVAL_DUP_ERR);
            }
            $valArray[] = $ebiMetadataListValue[ProfileConstant::VAL];
            $ansArray[] = $ebiMetadataListValue[ProfileConstant::ANSWER];
            $listVals = new EbiMetadataListValues();
            $listVals->setLang($lang);
            $listVals->setEbiMetadata($ebiMetadata);
            $listVals->setListName($ebiMetadataListValue[ProfileConstant::ANSWER]);
            $listVals->setListValue($ebiMetadataListValue[ProfileConstant::VAL]);
            $listVals->setSequence($ebiMetadataListValue[ProfileConstant::SEQUENCE_NO]);
            $listVals = $this->ebiMetaDataListValuesRepository->create($listVals);
            $response['meta_data_list'][] = $listVals;
        }
        return $response;
    }

    public function removeMetaListValues($ebiMetadata, $lang)
    {
        $this->ebiMetaDataListValuesRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_LIST_REPO);
        $ebiMetaListValues = $this->ebiMetaDataListValuesRepository->findBy(array(
            ProfileConstant::METADATA => $ebiMetadata,
            'lang' => $lang
        ));
        
        if (isset($ebiMetaListValues)) {
            foreach ($ebiMetaListValues as $ebiMetaListValue) {
                $this->ebiMetaDataListValuesRepository->remove($ebiMetaListValue);
            }
        }
    }

    public function catchError($errors)
    {
        if (count($errors) > 0) {
            $errorsString = "";
            foreach ($errors as $error) {
                
                $errorsString = $error->getMessage();
            }
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'metakey_duplicate_Error');
        }
    }

    public function getMaxSequenceId()
    {
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_REPO);
        $maxSequenceId = $this->ebiMetadataRepository->getEbiProfileCount();
        return $maxSequenceId;
    }

    public function getMasterSequence($sequence)
    {
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_REPO);
        
        $ebiMetadataSequence = $this->ebiMetadataRepository->findOneBy(array(
            ProfileConstant::SEQUENCE => $sequence,
            ProfileConstant::DEFINITION_TYPE => "E"
        ));
        
        return $ebiMetadataSequence;
    }

    public function getValidProfile($ebiMetadataId)
    {
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_REPO);
        $ebiMetadata = $this->ebiMetadataRepository->find($ebiMetadataId);
        if (! isset($ebiMetadata)) {
            return false;
            // throw new ValidationException([
            // ProfileConstant::PROFILE_NOT_FOUND
            // ], ProfileConstant::PROFILE_NOT_FOUND, ProfileConstant::PROFILE_NOT_FOUND_KEY);
        }
        return $ebiMetadata;
    }

    public function setMinMax($profileDto, $ebiMetadata)
    {
        if ($profileDto->getItemDataType() == 'N') {
            $numberDetail = $profileDto->getNumberType();
            if (count($numberDetail) > 0) {
                
                $ebiMetadata->setNoOfDecimals($numberDetail[ProfileConstant::DECIMAL_POINTS]);
                
                if (is_numeric($numberDetail[ProfileConstant::MIN_DIGITS])) {
                    $ebiMetadata->setMinRange($numberDetail[ProfileConstant::MIN_DIGITS]);
                }
                if (is_numeric($numberDetail[ProfileConstant::MAX_DIGITS])) {
                    $ebiMetadata->setMaxRange($numberDetail[ProfileConstant::MAX_DIGITS]);
                }
            }
        } else {
            $ebiMetadata->setMaxRange(NULL);
            $ebiMetadata->setMinRange(NULL);
            $ebiMetadata->setNoOfDecimals(NULL);
        }
        
        return $ebiMetadata;
    }

    public function isValidLang($lang)
    {
        if (! $lang) {
            throw new ValidationException([
                ProfileConstant::LANG_NOT_FOUND
            ], ProfileConstant::LANG_NOT_FOUND, 'lang_not_found');
        }
        return true;
    }

    private function manageDatablockMapping($profileDto, $ebiMetadata)
    {
        $dataBlockMetadataRepo = $this->repositoryResolver->getRepository(ProfileBlocksConstants::DATABLOCK_METADATA_ENT);
        $profileBlockId = $profileDto->getProfileBlockId();
        if ($profileBlockId != "") {
            
            $profileBlocks = $this->datablockMasterRepository->find($profileBlockId);
            if (! isset($profileBlocks)) {
                throw new ValidationException([
                    ProfileConstant::PROFILE_BLOCKS_NOT_FOUND
                ], ProfileConstant::PROFILE_BLOCKS_NOT_FOUND, ProfileConstant::PROFILE_BLOCKS_NOT_FOUND_KEY);
            }
            
            $datablock = $dataBlockMetadataRepo->findOneBy([
                'ebiMetadata' => $ebiMetadata
            ]);
            if ($datablock) {
                /**
                 * If already there.just update block id
                 */
                $datablock->setDatablock($profileBlocks);
            } else {
                /**
                 * Create Entries in DatablockMetadata
                 */
                
                $datablockMetadata = new DatablockMetadata();
                $datablockMetadata->setDatablock($profileBlocks);
                $datablockMetadata->setEbiMetadata($ebiMetadata);
                $datablockMetadata = $dataBlockMetadataRepo->persist($datablockMetadata, null);
            }
        } else {
            /**
             * If Unselect the block then removing it
             */
            $datablock = $dataBlockMetadataRepo->findOneBy([
                'ebiMetadata' => $ebiMetadata
            ]);
            if ($datablock) {
                $datablock = $dataBlockMetadataRepo->removeDatablockMap($datablock);
            }
        }
    }
    
    public function updateProfileStatus(ProfileDto $profileDto)
    {
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_REPO);
        $ebiMetadata = $this->ebiMetadataRepository->findOneById($profileDto->getId());
        
        if(strtolower($profileDto->getStatus()) == 'archive'){
            $ebiMetadata->setStatus('archived');
        }else{
            $ebiMetadata->setStatus('active');
        }
        $this->ebiMetadataRepository->flush();
        return $profileDto;
        
    }
    
    public function isPredefinedProfile($metakey)
    {
        $personRepo = $this->repositoryResolver->getRepository('SynapseCoreBundle:Person');
        $profile = $personRepo->getPredefinedProfile();
        $metakey = strtolower($metakey);
        if(in_array($metakey, $profile))
        {
            throw new ValidationException([
                'Profile item already exists as Person Record Information fields'
                ], 'Profile item already exists as Person Record Information fields', 'metakey_duplicate_Error');
        
        }else{
            $ebiMetadataRepository = $this->repositoryResolver->getRepository(OrgProfileConstant::ORGMETADATA_REPO);
            $isExists = $ebiMetadataRepository->IsOrgProfileExists($metakey);
            if(!$isExists)
            {
                throw new ValidationException([
                    'ISP already exists'
                    ], 'ISP already exists', 'metakey_duplicate_Error');
            }
            return true;
        }
    }

    /**
     * Get all the profile block with profile items associated with each profile blockes for a user.
     *
     * @param int $userId
     * @param int $organizationId
     * @param array $ebiMetaKeysArray
     * @return array
     */
    public function getDatablocksAndBlockitemsWithYearAndTermInformation($userId , $organizationId, $ebiMetaKeysArray = null)
    {

        $profileItems = $this->personEbiMetadataRepository->getProfileBlockWithBlockItemAndYearTermInformation($userId, $organizationId, $ebiMetaKeysArray);

        $blockInfo = array();
        $profileItemsArray = array();
        $profileBlockCount = -1;
        $profileItemCount = -1;
        $profileBlockId = '';
        $profileItemId = '';
        $responseArray = array();
        foreach ($profileItems as $profileItem) {

            if($profileBlockId == '' || $profileBlockId != $profileItem['datablock_id']) {
                $profileBlockCount++;
                $blockInfo['id'] = $profileItem['datablock_id'];
                $blockInfo['display_name'] = $profileItem['datablock_name'];
                $responseArray['profile_blocks'][$profileBlockCount] = $blockInfo;
                $profileItemCount = -1;
            }

            if($profileItemId == '' || $profileItemId != $profileItem['ebi_metadata_id']) {
                $profileBlockId = $profileItem['datablock_id'];
                $profileItemId = $profileItem['ebi_metadata_id'];
                $profileItemsArray['id'] = $profileItem['ebi_metadata_id'];
                $profileItemsArray['item_data_type'] = $profileItem['item_data_type'];
                $profileItemsArray['display_name'] = $profileItem['display_name'];
                $profileItemsArray['calendar_assignment'] = is_null($profileItem['calendar_assignment']) ? "" : $profileItem['calendar_assignment'];
                $responseArray['profile_blocks'][$profileBlockCount]['profile_items'][] = $profileItemsArray;
                $profileItemCount++;
            }


            if ($profileItem['calendar_assignment'] == "Y" || $profileItem['calendar_assignment'] == 'T') {
                $yearAndTerm['year'] = $profileItem['year_id'];
                $yearAndTerm['year_id'] = $profileItem['org_academic_year_id'];
                $yearAndTerm['year_name'] = $profileItem['year_name'];
                $yearAndTerm['term_id'] = $profileItem['org_academic_terms_id'];
                $yearAndTerm['term_name'] = $profileItem['term_name'];
                $yearAndTerm['is_current_academic_year'] = $profileItem['is_current_academic_year'];
                $responseArray['profile_blocks'][$profileBlockCount]['profile_items'][$profileItemCount]['year_term'][] = $yearAndTerm;

            }



        }
        return $responseArray;

    }
}