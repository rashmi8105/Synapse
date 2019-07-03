<?php
namespace Synapse\UploadBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Synapse Upload Validation
 *
 * @DI\Service("report_element_validator_service")
 */
class ReportElementValidatorService extends SynapseValidatorService
{

    const SERVICE_KEY = 'report_element_validator_service';

    const RISK_M_001 = "ERR-RISK_M_001";

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "profileService" = @DI\Inject("profile_service"),
     *            "orgProfileService" = @DI\Inject("orgprofile_service"),
     *            "validator" = @DI\Inject("validator")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $profileService, $orgProfileService, $validator)
    {
        parent::__construct($repositoryResolver, $logger, $profileService, $orgProfileService, $validator);
    }

    public function isValueEmpty($value, $key)    
    {
        $this->logger->info(">>>>>>>>>>>>> isValueEmpty{$value}{$key}");        
        if (empty($value)) {
            $this->errors[] = [
                'name' => $key,
                'value' => '',
                'errors' => [
                    "{$key} should not be empty. "
                ]
            ];
            return false;
        } else {
            return true;
        }
    } 
    
    public function checkDuplicate($element)
    {
        if($element)
        {
            $this->errors[] = [
                'name' => $element,
                'value' => '',
                'errors' => [
                    "{$element} already exist. "
                ]
            ];
            return false;
        } else {
            return true;
        }
    }
    
    public function stackFullError($element)
    {
        if($element)
        {
            $this->errors[] = [
                'name' => $element,
                'value' => '',
                'errors' => [
                    "{$element} - Section should have only 5 elements "
                ]
            ];
            return false;
        } else {
            return true;
        }
    }   
    
    /*
     * Validate the character lenght
     */
    public function validateCharLength($length, $data, $field)
    {        
        if (strlen($data) > $length) {
            $this->errors[] = [
                'name' => $field,
                'value' => '',
                'errors' => [
                    " Filed cannot exceed more than {$length} characters. "
                ]
            ];            
        }
        return true;
    }
    
    /*
     * Validate Data type
     * @string $value
     */
    
    public function validateDataType($value)    
    {
        $this->logger->info(">>>>>>>>>>>>> validateDataType{$value}");
        if ($value != 'Factor' && $value != 'QuestionBank') {                
            $this->errors[] = [
                'name' => 'DataType',
                'value' => '',
                'errors' => [
                    "DataType should be either Factor or QuestionBank "
                ]
            ];
            return false;
        } else {
            return true;
        }
    }
    
    /*
     * Validate isNumeric
     * @string $value
     * @string $key
     */

    public function isNumeric($value, $key)
    {
        if(!is_numeric($value) || !isset($value))
        {
            $this->errors[] = [
                'name' => $key,
                'value' => '',
                'errors' => [
                    "{$key} should have numerical value. "
                ]
            ];
            return false;
        } else {
            return true;
        }
    }    
}