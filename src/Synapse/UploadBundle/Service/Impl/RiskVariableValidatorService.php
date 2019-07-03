<?php
namespace Synapse\UploadBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Synapse Upload Validation
 *
 * @DI\Service("riskvariable_validator_service")
 */
class RiskVariableValidatorService extends SynapseValidatorService
{

    const SERVICE_KEY = 'riskvariable_validator_service';

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

    public function validateContinousBucket($bucketValues)
    {
        $minArray = [];
        $maxArray = [];
        $returnFlag = true;
        try {
            if (is_array($bucketValues) && count($bucketValues) > 0) {
                foreach ($bucketValues as $bucketValue) {
                    $min = $bucketValue->getMin();
                    $max = $bucketValue->getMax();
                    $this->checkMinMaxEmpty($min, $max);
                    $this->checkNumberLimit($min, $max);
                    if ( is_numeric($min) &&  is_numeric($max)) {
                        $this->checkMinGreaterMx($min, $max);
                        $minArray[] = $min;
                        $maxArray[] = $max;
                    }
                }
                
                $minReponse = $this->isArrayDuplicates($minArray);
                $maxReponse = $this->isArrayDuplicates($maxArray);
                $this->value_overlap($bucketValues);
            } else {
                $returnFlag = true;
            }
        } catch (ValidationException $e) {
            $this->errors[] = [
                'name' => '',
                'value' => '',
                'errors' => [
                    $e->getMessage()
                ]
            ];
        }
        
        return $returnFlag;
    }

    public function checkNumberLimit ($minValue,$maxValue)
    {
        $minLimit = -999999999999;
        $maxLimit = 999999999999;
        if($minValue < $minLimit)
        {
            throw new ValidationException([
                "min value entered should not be less than $minLimit "
                ], "min value entered should not be less than $minLimit ", 'ERRBUCKETVAL');
        }
        if($minValue  > $maxLimit)
        {
            throw new ValidationException([
                "min value entered should not grater than $maxLimit "
                ], "min value entered should not grater than $maxLimit ", 'ERRBUCKETVAL');
        }
        
        if($maxValue < $minLimit)
        {
            throw new ValidationException([
                "max value entered should not be less than $minLimit "
                ], "max value entered should not be less than $minLimit ", 'ERRBUCKETVAL');
        }
        if($maxValue  > $maxLimit)
        {
            throw new ValidationException([
                "max value entered should not be greater than  $maxLimit "
                ], "max value entered should not be greater than $maxLimit ", 'ERRBUCKETVAL');
        }
    }
    public function checkMinGreaterMx($min, $max)
    {
        if ($min > $max) {
            
            throw new ValidationException([
                'Min is greater than Max. Please check math.'
            ], 'Min is greater than Max. Please check math.', self::RISK_M_001);
            return false;
        } else {
            return;
        }
    }

    public function checkMinMaxEmpty($min, $max)
    {
        if (is_numeric($min) && ! is_numeric($max)) {
            throw new ValidationException([
                'Missing max value.'
            ], 'Missing max value', self::RISK_M_001);
        } elseif (! is_numeric($min) && is_numeric($max)) {
            
            throw new ValidationException([
                'Missing min value.'
            ], 'Missing min value', self::RISK_M_001);
        } else {
            return;
        }
    }

    /**
     * find values repeated in single array
     * If repeated return false
     *
     * @param array $array            
     * @return boolean
     */
    public function isArrayDuplicates($array)
    
    {
        if (count($array) > 0) {
            $count = count($array);
            $countUnique = count(array_unique($array));
            if ($count === $countUnique) {
                return true;
            } else {
                throw new ValidationException([
                    UploadConstant::BUCKET_OVERLAP
                ], UploadConstant::BUCKET_OVERLAP, self::RISK_M_001);
            }
        } else {
            return true;
        }
    }


    public function value_overlap($arrayObj)
    {
        $arrayObj2 = $arrayObj;
        foreach ($arrayObj as $key1 => $time) {
            $start = $time->getMin();
            $end = $time->getMax();
            foreach ($arrayObj2 as $key2 => $check) {
                if ($key1 !== $key2) {
                    $start2 = $check->getMin();
                    $end2 = $check->getMax();
                    $this->isEmptyValues($start, $start2, $end, $end2);
                } else {
                    continue;
                }
            }
        }
        return;
    }

    public function isEmptyValues($start, $start2, $end, $end2)
    {
        if ( is_numeric($start) &&  is_numeric($start2) &&  is_numeric($end) &&  is_numeric($end2)) {
            $this->checkOverlap($start, $start2, $end, $end2);
        } else {
            return;
        }
    }


    /**
     * Checks if the buckets overlap, but are not equivalent
     *
     * @param float $bucket1Minimum
     * @param float $bucket2Minimum
     * @param float $bucket1Maximum
     * @param float $bucket2Maximum
     * @return bool
     */
    public function checkOverlap($bucket1Minimum, $bucket2Minimum, $bucket1Maximum, $bucket2Maximum)
    {
        if (($bucket1Minimum > $bucket2Minimum && $bucket1Minimum < $bucket2Maximum) || ($bucket1Maximum > $bucket2Minimum && $bucket1Maximum < $bucket2Maximum)) {

            throw new ValidationException([
                UploadConstant::BUCKET_OVERLAP
            ], UploadConstant::BUCKET_OVERLAP, self::RISK_M_001);
        } else {
            return true;
        }
    }
}