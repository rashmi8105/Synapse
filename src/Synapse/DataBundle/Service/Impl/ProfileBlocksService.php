<?php
namespace Synapse\DataBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\DataBundle\Service\ProfileBlocksServiceInterface;
use Synapse\DataBundle\EntityDto\ProfileBlocksDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Entity\DatablockMaster;
use Synapse\CoreBundle\Entity\DatablockMasterLang;
use Synapse\DataBundle\Util\Constants\ProfileBlocksConstants;
use Synapse\CoreBundle\Entity\DatablockMetadata;
use Synapse\CoreBundle\Util\Constants\OrgPermissionsetConstant;
use Synapse\CoreBundle\Util\Helper;
use JMS\Serializer\Serializer;

/**
 * @DI\Service("profileblocks_service")
 */
class ProfileBlocksService extends AbstractService implements ProfileBlocksServiceInterface
{

    const SERVICE_KEY = 'profileblocks_service';

    /**
     *
     * @var container
     */
    private $container;

    /**
     *
     * @var langService
     */
    private $langService;

    private $dataBlockMasterRepo;

    private $dataBlockMasterLangRepo;

    private $dataBlockMetadataRepo;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *
     *
     *            "container" = @DI\Inject("service_container")
     *
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        $this->container = $container;
    }

    /**
     * Create EBI Specific Profiles
     *
     * @param ProfileDto $profileDto
     * @return void|\Synapse\RestBundle\Entity\Error
     */
    public function createProfileBlocks(ProfileBlocksDto $profileBlocksDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($profileBlocksDto);
        $this->logger->debug("Creating Profile Blocks  -  " . $logContent);

        $this->langService = $this->container->get('lang_service');
        $this->dataBlockMasterRepo = $this->repositoryResolver->getRepository(ProfileBlocksConstants::DATABLOCKMASTER_ENT);
        $this->dataBlockMetadataRepo = $this->repositoryResolver->getRepository(ProfileBlocksConstants::DATABLOCK_METADATA_ENT);
        $lang = $this->getEbiLang();
        $langRef = $this->langService->getLangReferance($lang);
        try {

            $datablock = new DatablockMaster();
            $datablock->setBlockType('profile');
            $this->dataBlockMasterRepo->persist($datablock, false);
            $datablockLang = new DatablockMasterLang();
            $datablockLang->setDatablock($datablock);
            $datablockLang->setLang($langRef);
            $datablockLang->setDatablockDesc($profileBlocksDto->getProfileBlockName());
            $this->validateEntity($datablockLang);
            $this->dataBlockMasterRepo->persist($datablockLang, false);
            $profileItems = $profileBlocksDto->getProfileItems();
            $this->assignProfileItems($profileItems, $datablock);
            $this->dataBlockMasterRepo->flush();
            $profileBlocksDto->setProfileBlockId($datablock->getId());
            return $profileBlocksDto;
        } catch (ValidationException $valExp) {
            throw $valExp;
        } catch (\Exception $e) {
            $this->logger->error( " DataBundle - Profile Blocks Service - createProfileBlocks " . ProfileBlocksConstants::DATABLOCK_DB_EXCEPTION . $e->getMessage());
            throw new ValidationException([
                ProfileBlocksConstants::DATABLOCK_DB_EXCEPTION . $e->getMessage()
            ], ProfileBlocksConstants::DATABLOCK_DB_EXCEPTION, ProfileBlocksConstants::DATABLOCK_DB_EXCEPTION_CODE);
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Synapse\CoreBundle\Service\ProfileServiceInterface::updateProfile()
     */
    public function updateProfileBlocks(ProfileBlocksDto $profileBlocksDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($profileBlocksDto);
        $this->logger->debug("Updating Profile Blocks  -  " . $logContent);

        $profileBlockId = $profileBlocksDto->getProfileBlockId();
        $this->dataBlockMasterLangRepo = $this->repositoryResolver->getRepository(ProfileBlocksConstants::DATABLOCKMASTER_LANG_ENT);
        $this->dataBlockMetadataRepo = $this->repositoryResolver->getRepository(ProfileBlocksConstants::DATABLOCK_METADATA_ENT);
        $this->dataBlockMasterRepo = $this->repositoryResolver->getRepository(ProfileBlocksConstants::DATABLOCKMASTER_ENT);
        $lang = $this->getEbiLang();
        $datablockLang = $this->dataBlockMasterLangRepo->findOneBy([
            ProfileBlocksConstants::DATABLOCK => $profileBlockId,
            'lang' => $lang
        ]);
        if (! $datablockLang) {
            $this->logger->error( "DataBundle - Profile Blocks Service - updateProfileBlocks " . ProfileBlocksConstants::DATABLOCK_MASTER_NOT_FOUND . $datablockLang);
            throw new ValidationException([
                ProfileBlocksConstants::DATABLOCK_MASTER_NOT_FOUND
            ], ProfileBlocksConstants::DATABLOCK_MASTER_NOT_FOUND, ProfileBlocksConstants::DATABLOCK_MASTER_NOT_FOUND_CODE);
        }

        try {
            $dataBlock = $this->dataBlockMasterRepo->find($profileBlockId);
            $datablockLang->setDatablockDesc($profileBlocksDto->getProfileBlockName());
            $this->validateEntity($datablockLang);
            /**
             * datablock map
             */
            $existingMaps = $this->dataBlockMetadataRepo->getMappedBlocks($profileBlockId, $lang, false);

            $exisingProfileIds = [];
            if ($existingMaps && count($existingMaps) > 0) {
                foreach ($existingMaps as $existingMap) {
                    $exisingProfileIds[] = $existingMap['id'];
                }
            }
            $profileItems = $profileBlocksDto->getProfileItems();
            $this->removeProfileItems($exisingProfileIds, $profileItems, $profileBlockId);

            $this->assignProfileItems($profileItems, $dataBlock, $exisingProfileIds);

            $this->dataBlockMasterLangRepo->flush();
        } catch (ValidationException $valExp) {
            throw $valExp;
        } catch (\Exception $e) {
            $this->logger->error("DataBundle - Profile Blocks Service - updateProfileBlocks " . ProfileBlocksConstants::DATABLOCK_DB_EXCEPTION . $e->getMessage());
            throw new ValidationException([
                ProfileBlocksConstants::DATABLOCK_DB_EXCEPTION . $e->getMessage()
            ], ProfileBlocksConstants::DATABLOCK_DB_EXCEPTION, ProfileBlocksConstants::DATABLOCK_DB_EXCEPTION_CODE);
        }
        $profileBlocksDto->setProfileBlockName($datablockLang->getDatablockDesc());
        $this->logger->info(" Update Profile Blocks ");
        return $profileBlocksDto;
    }

    public function deleteProfileBlocks($profileBlockId)
    {
        $this->logger->debug(" Delete Profile Blocks by Profile Block Id " . $profileBlockId);
        $this->dataBlockMasterLangRepo = $this->repositoryResolver->getRepository(ProfileBlocksConstants::DATABLOCKMASTER_LANG_ENT);
        $this->dataBlockMetadataRepo = $this->repositoryResolver->getRepository(ProfileBlocksConstants::DATABLOCK_METADATA_ENT);
        $this->dataBlockMasterRepo = $this->repositoryResolver->getRepository(ProfileBlocksConstants::DATABLOCKMASTER_ENT);
        $dataBlock = $this->getDataBlockMaster($profileBlockId);
        $this->removeAssignProfileItems($dataBlock);
        $lang = $this->getEbiLang();
        $datablockLang = $this->dataBlockMasterLangRepo->findOneBy([
            ProfileBlocksConstants::DATABLOCK => $profileBlockId,
            'lang' => $lang
        ]);
        $this->dataBlockMasterLangRepo->removeDatablockLang($datablockLang);
        /**
         * Remove associated Permissions
         */
        $this->removeOrgPermissionset($dataBlock);
        $this->removeEbiPermissionset($dataBlock);
        $this->dataBlockMasterRepo->removeDataBlockMaster($dataBlock);
        $this->dataBlockMasterRepo->flush();
        $this->logger->info(" Delete Profile Blocks by Profile Block Id ");
    }

    public function getBlockById($blockId, $exclude = false ,$excludeType = false)
    {
        $excludeTypeArr = array(
            "T" => 'term',
            "Y" => 'year',
            "N" => 'none'
        );

        $this->logger->debug(" Get Block By Id " . $blockId . "Exclude" . $exclude);
        $this->dataBlockMasterLangRepo = $this->repositoryResolver->getRepository(ProfileBlocksConstants::DATABLOCKMASTER_LANG_ENT);
        $this->dataBlockMetadataRepo = $this->repositoryResolver->getRepository(ProfileBlocksConstants::DATABLOCK_METADATA_ENT);
        $this->ebiMetadataListValueRepo = $this->repositoryResolver->getRepository(ProfileBlocksConstants::EBI_METADATA_LIST_VALUES);
        $lang = $this->getEbiLang();
        $dataBlock = $this->dataBlockMasterLangRepo->getDataBlockById($blockId, $lang);
        if (! $dataBlock) {
            $this->logger->error( " DataBundle - Profile Blocks Service - getBlockById " . ProfileBlocksConstants::DATABLOCK_MASTER_NOT_FOUND);
            throw new ValidationException([
                ProfileBlocksConstants::DATABLOCK_MASTER_NOT_FOUND
            ], ProfileBlocksConstants::DATABLOCK_MASTER_NOT_FOUND, ProfileBlocksConstants::DATABLOCK_MASTER_NOT_FOUND_CODE);
        }

        $profileBlockDto = new ProfileBlocksDto();
        $profileBlockDto->setProfileBlockId($dataBlock[0]['dmid']);
        $profileBlockDto->setProfileBlockName(($dataBlock[0]['datablockDesc']) ? $dataBlock[0]['datablockDesc'] : "");

        if($excludeType){

            if(!in_array($excludeType,$excludeTypeArr)){
                throw new ValidationException([
                    "Invalid Exclude Type"
                ], "Invalid Exclude Type", "Invalid Exclude Type");
            }
            $excludeType =  array_search($excludeType, $excludeTypeArr);
        }

        $profileItems = $this->dataBlockMetadataRepo->getMappedBlocks($blockId, $lang, $exclude, $excludeType);

        $responseArr = array();
        $profileItemsArr = array();
        $categoryType = array();

        foreach ($profileItems as $profileItem) {

            $profileItemsArr['id'] = $profileItem['id'];
            $profileItemsArr['modified_at'] = $profileItem['modified_at'];
            $profileItemsArr[ProfileBlocksConstants::ITEM_DATA_TYPE] = $profileItem[ProfileBlocksConstants::ITEM_DATA_TYPE];
            $profileItemsArr['item_label'] = $profileItem['item_label'];
            $profileItemsArr['display_name'] = $profileItem['display_name'];
            $profileItemsArr['item_subtext'] = $profileItem['item_subtext'];
            $profileItemsArr[ProfileBlocksConstants::SEQUENCE_NO] = $profileItem[ProfileBlocksConstants::SEQUENCE_NO];
            $profileItemsArr['calendar_assignment'] = is_null($profileItem['calendar_assignment']) ? "" : $profileItem['calendar_assignment'];

            if ($profileItem[ProfileBlocksConstants::ITEM_DATA_TYPE] == "N") {
                $profileItemsArr[ProfileBlocksConstants::NUMBER_TYPE]['decimal_points'] = $profileItem['decimal_points'];
                $profileItemsArr[ProfileBlocksConstants::NUMBER_TYPE]['min_digits'] = $profileItem['min_digits'];
                $profileItemsArr[ProfileBlocksConstants::NUMBER_TYPE]['max_digits'] = $profileItem['max_digits'];
            }

            if ($profileItem[ProfileBlocksConstants::ITEM_DATA_TYPE] == "S") {

                $listValuses = $this->ebiMetadataListValueRepo->findBy([
                    'ebiMetadata' => $profileItem['id']
                ]);
                /*
                 * To fetch the ebi meta data list values
                 */
                foreach ($listValuses as $listValuse) {

                    $categoryType['answer'] = $listValuse->getListName();
                    $categoryType['value'] = $listValuse->getListValue();
                    $categoryType[ProfileBlocksConstants::SEQUENCE_NO] = $listValuse->getSequence();
                    $profileItemsArr['category_type'][] = $categoryType;
                }
            }
            $responseArr[] = $profileItemsArr;
            unset($profileItemsArr);
        }

        $profileBlockDto->setProfileItems($responseArr);
        $this->logger->info(" Get Block By Id ");
        return $profileBlockDto;
    }

    public function getDatablocks($user, $type = null)
    {
        $this->logger->debug("Get Datablocks by User " . $user . "Type" . $type);
        $orgPermission = $this->container->get('orgpermissionset_service');
        $profilePermissionBlocks = $orgPermission->getProfileblockPermission($user);
        $blockId = array();
        foreach ($profilePermissionBlocks as $blocks) {
            foreach ($blocks as $block) {
                $blockId[] = $block['block_id'];
            }
        }
        $this->dataBlockMasterLangRepo = $this->repositoryResolver->getRepository(ProfileBlocksConstants::DATABLOCKMASTER_LANG_ENT);
        $lang = $this->getEbiLang();
        $dataBlock = $this->dataBlockMasterLangRepo->getProfileDataBlocks($lang, $blockId, $type);
        $this->logger->info(" Get Datablocks ");
        return $dataBlock;
    }

    private function getDataBlockMaster($datablockId)
    {
        $dataBlock = $this->dataBlockMasterRepo->find($datablockId);
        if (! $dataBlock) {
            throw new ValidationException([
                ProfileBlocksConstants::DATABLOCK_MASTER_NOT_FOUND
            ], ProfileBlocksConstants::DATABLOCK_MASTER_NOT_FOUND, ProfileBlocksConstants::DATABLOCK_MASTER_NOT_FOUND_CODE);
        }
        return $dataBlock;
    }

    private function validateEntity($entity)
    {
        $validator = $this->container->get('validator');
        $errors = $validator->validate($entity);
        if (count($errors) > 0) {
            $errorsString = "";
            $errorsString = $errors[0]->getMessage();

            throw new ValidationException([
                $errorsString
            ], $errorsString, 'entity_validation');
        }
    }

    private function assignProfileItems($profiles, $datablock, $existingIds = [])
    {
        $this->dataBlockMetadataRepo = $this->repositoryResolver->getRepository(ProfileBlocksConstants::DATABLOCK_METADATA_ENT);
        if (is_array($profiles) && count($profiles) > 0) {
            $ebiProfileRepo = $this->repositoryResolver->getRepository(ProfileBlocksConstants::EBI_METADATA_ENT);
            foreach ($profiles as $profile) {
                if (! in_array($profile['id'], $existingIds)) {
                    $ebiProfile = $ebiProfileRepo->find($profile['id']);
                    if (! $ebiProfile) {
                        throw new ValidationException([
                            'Profile Not Found'
                        ], 'Profile Not Found', 'profile_not_found');
                    }
                    $datablockMetadata = new DatablockMetadata();
                    $datablockMetadata->setEbiMetadata($ebiProfile);
                    $datablockMetadata->setDatablock($datablock);
                    $this->validateEntity($datablockMetadata);
                    $this->dataBlockMetadataRepo->persist($datablockMetadata, false);
                }
            }
        }
    }

    private function removeAssignProfileItems($datablockId)
    {
        $this->dataBlockMetadataRepo = $this->repositoryResolver->getRepository(ProfileBlocksConstants::DATABLOCK_METADATA_ENT);
        $datablocks = $this->dataBlockMetadataRepo->findBy([
            ProfileBlocksConstants::DATABLOCK => $datablockId
        ]);
        if ($datablocks) {
            foreach ($datablocks as $datablock) {
                $this->dataBlockMetadataRepo->removeDatablockMap($datablock);
            }
        }
    }

    private function removeProfileItems($exitingIds, $profileItems, $datablockId)
    {
        $requestItems = [];
        if (is_array($profileItems) && count($profileItems) > 0) {
            foreach ($profileItems as $profileItem) {
                $requestItems[] = $profileItem['id'];
            }
        }
        if ($exitingIds) {
            foreach ($exitingIds as $exitingId) {
                if (! in_array($exitingId, $requestItems)) {
                    // mark the item as deleted
                    $datablockMeta = $this->dataBlockMetadataRepo->findOneBy([
                        ProfileBlocksConstants::DATABLOCK => $datablockId,
                        'ebiMetadata' => $exitingId
                    ]);
                    if ($datablockMeta) {
                        $this->dataBlockMetadataRepo->removeDatablockMap($datablockMeta);
                    }
                }
            }
        }
    }

    private function getEbiLang()
    {
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:EbiConfig');
        $ebiLang = $this->ebiConfigRepository->findOneByKey('Ebi_Lang');
        if ($ebiLang) {
            return $ebiLang->getValue();
        } else {
            throw new ValidationException([
                'No Ebi Lang Found.'
            ], 'No Ebi Lang Found', 'ebilang_not_found');
        }
    }

    private function removeOrgPermissionset($datablock)
    {
        $dataBlockMasterRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_PERMISSIONSET_DATABLOCK_REPO);
        $orgDatablocks = $dataBlockMasterRepository->findBy([
            'datablock' => $datablock
        ]);
        if ($orgDatablocks) {

            foreach ($orgDatablocks as $orgDatablock) {
                $dataBlockMasterRepository->remove($orgDatablock);
            }
        }
    }

    private function removeEbiPermissionset($datablock)
    {
        $dataBlockMasterRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:EbiPermissionsetDatablock');
        $ebiDatablocks = $dataBlockMasterRepository->findBy([
            'dataBlock' => $datablock
        ]);
        if ($ebiDatablocks) {
            foreach ($ebiDatablocks as $ebiDatablock) {
                $dataBlockMasterRepository->remove($ebiDatablock);
            }
        }
    }
}