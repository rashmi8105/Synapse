<?php
namespace Synapse\RiskBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\RiskBundle\Util\Constants\RiskErrorConstants;

class BucketDetailValidator extends ConstraintValidator
{

    const RISK_M_001 = "ERR-RISK_M_001";

    public function validate($bucket, Constraint $constraint)
    {
        $isContinuous = $bucket->getIsContinuous();
        $bucketValues = $bucket->getBucketDetails();
        if ($isContinuous) {
            $isRight = $this->validateContinousBucket($bucketValues);
            if (!$isRight) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%string%', "Error")
                    ->addViolation();
            } else {
                return;
            }
        } else {
            return;
        }
    }

    public function validateContinousBucket($bucketValues)
    {
        $minArray = [];
        $maxArray = [];
        $returnFlag = true;
        if (is_array($bucketValues) && count($bucketValues) > 0) {
            foreach ($bucketValues as $bucketValue) {
                $min = $bucketValue->getMin();
                $max = $bucketValue->getMax();
                $this->checkMinMaxEmpty($min, $max);
                if (! is_null($min) && ! is_null($max)) {
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
        return $returnFlag;
    }

    public function checkMinGreaterMx($min, $max)
    {
        if ($min > $max) {
            throw new ValidationException([
                RiskErrorConstants::RISK_M_008
            ], RiskErrorConstants::RISK_M_008, "RISK_M_008");
        } else {
            return;
        }
    }

    public function checkMinMaxEmpty($min, $max)
    {
        if (is_numeric($min) && ! is_numeric($max) || ! is_numeric($min) && is_numeric($max)) {
            
            throw new ValidationException([
                "Min & Max values are required"
            ], "Min & Max values are required", "ERR");
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
                    RiskErrorConstants::RISK_M_001
                ], RiskErrorConstants::RISK_M_001, self::RISK_M_001);
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
        if (! is_null($start) && ! is_null($start2) && ! is_null($end) && ! is_null($end2)) {
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
                RiskErrorConstants::RISK_M_001
            ], RiskErrorConstants::RISK_M_001, self::RISK_M_001);
        } else {
            return true;
        }
    }
}