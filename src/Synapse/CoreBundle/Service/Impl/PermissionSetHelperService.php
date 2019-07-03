<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Service\PermissionSetServiceInterface;
use Synapse\RestBundle\Entity\PermissionSetDto;
use Synapse\CoreBundle\Entity\EbiPermissionsetFeatures;
use Synapse\CoreBundle\Entity\EbiPermissionsetDatablock;
use Synapse\CoreBundle\Entity\PermissionSet;
use Synapse\CoreBundle\Entity\PermissionSetLang;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\RestBundle\Entity\AccessLevelDto;
use Synapse\RestBundle\Entity\FeatureBlockDto;
use Synapse\RestBundle\Entity\BlockDto;
use Synapse\RestBundle\Entity\PermissionValueDto;
use Synapse\RestBundle\Entity\PermissionSetStatusDto;
use \DateTime;

class PermissionSetHelperService extends AbstractService
{

    const PERMSET_REPO = "SynapseCoreBundle:PermissionSet";

    const PERMSET_LANG_REPO = "SynapseCoreBundle:PermissionSetLang";

    const PERMSET_FEATURE_MASTER_REPO = "SynapseCoreBundle:FeatureMaster";

    const PERMSET_FEATURE_MASTERLANG_REPO = "SynapseCoreBundle:FeatureMasterLang";

    const LNG_MASTER_REPO = "SynapseCoreBundle:LanguageMaster";

    const DATABLOCK_MASTER_LANG_REPO = "SynapseCoreBundle:DatablockMasterLang";

    const PERMSET_DATABLOCK_REPO = "SynapseCoreBundle:EbiPermissionsetDatablock";

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

    protected function isPermissionsetFound($permissionSet)
    {
        if (! $permissionSet) {
            throw new ValidationException([
                self::ERROR_PERMISSIONSET_NOT_FOUND
            ], self::ERROR_PERMISSIONSET_NOT_FOUND, self::ERROR_KEY);
        }
    }

    protected function getFeatureDataBlock($activeFeatures, $permissionSet, $featureDataBlockResponse)
    {
        $this->ebiPermissionsetFeaturesRepository = $this->repositoryResolver->getRepository(self::PERMSET_FEATURE_REPO);
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
                }
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
                
                $featureDataBlockResponse[] = $featureBlockDto;
            }
        }
        return $featureDataBlockResponse;
    }

    protected function getStatusCode($status)
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

    protected function getPermissionSets($status)
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

    protected function getLastUpdated($dataBlocks, $lastupdated)
    {
        foreach ($dataBlocks as $dataBlock) {
            if ($lastupdated < $dataBlock->getLastUpdated()) {
                $lastupdated = $dataBlock->getLastUpdated();
            }
        }
        return $lastupdated;
    }

    protected function catchDatablockException($dataBlock, $blockId)
    {
        if (! $dataBlock) {
            throw new ValidationException([
                self::ERROR_DATABLOCK_NOT_FOUND . $blockId
            ], self::ERROR_DATABLOCK_NOT_FOUND, self::ERROR_KEY);
        } else {
            return $dataBlock;
        }
    }

    protected function catchLanguageException($lang)
    {
        if (! isset($lang)) {
            throw new ValidationException([
                self::ERROR_LANGUAGE_NOT_FOUND
            ], self::ERROR_LANGUAGE_NOT_FOUND, self::ERROR_LANGUAGE_NOT_FOUND_KEY);
        } else {
            return $lang;
        }
    }

    protected function catchError($errors)
    {
        if (count($errors) > 0) {
            $errorsString = $errors[0]->getMessage();
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'permissionsetname_duplicate_error');
        }
    }

    protected function setPermissionValDto($featureBlockDto, $permissionFeature)
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

    protected function insertUpdateBlocks($block, $ebiPermissionSet, $type)
    {
        $this->ebiPermissionsetDatablockRepository = $this->repositoryResolver->getRepository(self::PERMSET_DATABLOCK_REPO);
        $this->dataBlockMasterRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:DatablockMaster");
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

    protected function getEbiDataBlocks($blocks, $permissionSet)
    {
        $this->ebiPermissionsetDatablockRepository = $this->repositoryResolver->getRepository(self::PERMSET_DATABLOCK_REPO);
        $profileDataBlocksResponse = array();
        if (count($blocks) > 0) {
            foreach ($blocks as $block) {
                $blockDto = new BlockDto();
                $blocksPermission = $this->ebiPermissionsetDatablockRepository->findOneBy([
                    self::FIELD_EBIPERMISSION_SET => $permissionSet,
                    self::DATA_BLOCK => $block[self::FIELD_DATABLOCKID]
                ]);
                $blockDto->setBlockName($block['datablock_name']);
                $blockDto->setBlockId($block[self::FIELD_DATABLOCKID]);
                
                if ($blocksPermission) {
                    
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

    protected function getActiveFeatureBlocks($activeFeatures, $permissionSet, $featureDataBlockResponse)
    {
        $this->ebiPermissionsetFeaturesRepository = $this->repositoryResolver->getRepository(self::PERMSET_FEATURE_REPO);
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
                }
                $pValueDto = $this->setPermissionValDto($featureBlockDto, $permissionFeature);
                $featureBlockDto->setTeamsShare($pValueDto);
                $featureDataBlockResponse[] = $featureBlockDto;
            }
        }
        return $featureDataBlockResponse;
    }
}
