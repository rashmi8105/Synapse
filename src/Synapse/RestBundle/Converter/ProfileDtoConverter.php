<?php
namespace Synapse\RestBundle\Converter;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Validator\Constraints\Null;
use Synapse\CoreBundle\Entity\MetadataListValues;
use Synapse\CoreBundle\Entity\MetadataMaster;
use Synapse\CoreBundle\Entity\Metadatamasterlang;
use Synapse\RestBundle\Entity\ProfileDto;

/**
 * DTO converter
 *
 * Helper class to convert entities to data transfer objects
 * This class only contain converter that could be shared across different REST end points
 * Some end points could have different implementation accordingly with its needs
 *
 * @DI\Service("profiledto_converter")
 */
class ProfileDtoConverter
{

    public function createProfileResponse($profile)
    {
        
        // var_dump($profile);exit;
        $profileDto = new ProfileDto();
        
        $metadataMaster = $profile['meta_data_master'];
        $profileDto->setId($metadataMaster->getId());
        $profileDto->setDefinitionType($metadataMaster->getDefinitionType());
        $profileDto->setItemDataType($metadataMaster->getMetadataType());
        $profileDto->setSequenceNo($metadataMaster->getSequence());
        if ($metadataMaster->getMetadataType() == 'N') {
            $numberArray = array();
        if(!is_null($metadataMaster->getMinRange()) && trim($metadataMaster->getMaxRange()) != "" ){
                $numberArray['min_digits'] = (double) $metadataMaster->getMinRange();
            }else{
                $numberArray['min_digits'] = "";
            }
            if(!is_null($metadataMaster->getMaxRange()) && trim($metadataMaster->getMaxRange()) != "" ){
            $numberArray['max_digits'] = (double) $metadataMaster->getMaxRange();
            }else{
                $numberArray['max_digits'] = "";
            }
            if(!is_null($metadataMaster->getNoOfDecimals() && trim($metadataMaster->getNoOfDecimals()) != "" )){
                $numberArray['decimal_points'] = (int) $metadataMaster->getNoOfDecimals();
            }else{
                $numberArray['decimal_points'] = "";
            }
            $profileDto->setNumberType($numberArray);
        }
        
        /* Metadata master lang */
        
        $metadataMasterLang = $profile['meta_data_master_lang'];
        
        $profileDto->setItemLabel($metadataMasterLang->getMetaName());
        $profileDto->setItemSubtext($metadataMasterLang->getMetaDescription());
        
        if ($metadataMaster->getMetadataType() == 'S') {
            $metaListValues = $profile['meta_data_list'];
            
            $listValues = array();
            if (count($metaListValues) > 0) {
                foreach ($metaListValues as $metaListValue) {
                   if($metaListValue)
                   {
                       $listval = array();
                       
                       $listval['answer'] = $metaListValue->getListName();
                       $listval['value'] = $metaListValue->getListValue();
                       $listval['sequence_no'] = $metaListValue->getSequence();
                       $listValues[] = $listval;
                   }
                   
                    
                }
                    //print_r($listValues);
                $profileDto->setCategoryType($listValues);
            }
        }
        
        return $profileDto;
    }

    public function reorderResponse ($profile)
    {
        $reorderResponse = array();
        $reorderResponse['id']= $profile->getId();
        $reorderResponse['sequence_no']= $profile->getSequence();
        return $reorderResponse;
        
    }
}