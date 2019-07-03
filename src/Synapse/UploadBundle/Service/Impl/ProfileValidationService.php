<?php
namespace Synapse\UploadBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\SynapseConstant;

/**
 * Handle Profile Items custom validation
 *
 * @DI\Service("profile_validation_service")
 */
class ProfileValidationService
{

    const SERVICE_KEY = 'profile_validation_service';
    
    /*
     * Custom validations for profile items got here e.g 'campusstate' => [ 'validateTextLength' => 5 ] campusstate => profile item , should always be in lower case validateTextLength = > method for validatiob 5 => validation param for the method
     */
    private $profileCustomValidationArray = [
        
        'campusstate' => [
            'validateTextLength' => SynapseConstant::CAMPUS_PROFILE_ITEM_CHARACTER_LENGTH
        ],
        'campuszip' => [
            'validateTextLength' => SynapseConstant::CAMPUS_PROFILE_ITEM_CHARACTER_LENGTH
        ],
        'campuscountry' => [
            'validateTextLength' => SynapseConstant::CAMPUS_PROFILE_ITEM_CHARACTER_LENGTH
        ],
        'campusaddress' => [
            'validateTextLength' => SynapseConstant::CAMPUS_PROFILE_ITEM_CHARACTER_LENGTH
        ],
        'campuscity' => [
            'validateTextLength' => SynapseConstant::CAMPUS_PROFILE_ITEM_CHARACTER_LENGTH
        ]
    ];

    /**
     * Customized validation of profile items. The type of validation needed is determined by the $profileCustomValidationArray
     * returns a message if there is a validation error message or returns an empty string
     *
     * @param string $profileItem
     * @param string $profileItemValue
     * @return string
     */
    public function profileItemCustomValidations($profileItem, $profileItemValue)
    {
        $profileItemsArr = $this->profileCustomValidationArray;
        if (trim($profileItemValue) != "") {
            if (isset($profileItemsArr[strtolower($profileItem)])) {
                $valiadtionData = $profileItemsArr[$profileItem]; // gets the validation methods for the validations defined for the profile item above in a private variable above
                foreach ($valiadtionData as $validateMethod => $validateValue) {
                    $errorMsg = $this->$validateMethod($profileItemValue, $validateValue);
                    if (trim($errorMsg) != "") {
                        return $errorMsg;
                    }
                }
            }
        }
        return "";
    }

    /**
     * Validates length  of the profile item ( *This method would seem to be unused but is being called dynamically  profileItemCustomValidations . //$errorMsg = $this->$validateMethod($profileItemValue, $validateValue);)
     *
     * @param $profileItemValue
     * @param $validateValue
     * @return string
     */
    private function validateTextLength($profileItemValue, $validateValue)
    {
        if (strlen($profileItemValue) > $validateValue) {
            return "should not be more than  $validateValue characters";
        }
        return "";
    }
}