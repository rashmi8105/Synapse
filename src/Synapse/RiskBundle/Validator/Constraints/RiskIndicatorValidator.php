<?php
namespace Synapse\RiskBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\RiskBundle\Util\Constants\RiskErrorConstants;

class RiskIndicatorValidator extends ConstraintValidator
{
    const RISK_M_001 = "ERR-RISK_M_001";
    public function validate($riskIndicators, Constraint $constraint)
    {
        $resFlag = $this->validateContinousBucket($riskIndicators);
        if (!$resFlag) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', "Error")
                ->addViolation();
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
                if ($min > $max) {
                    throw new ValidationException([
                        RiskErrorConstants::RISK_M_008
                    ], RiskErrorConstants::RISK_M_008, "RISK_M_008");
                }else{
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
        if (! empty($start) && ! empty($start2) && ! empty($end) && ! empty($end2)) {
            $this->checkOverlap($start, $start2, $end, $end2);
        } else {
            return;
        }
    }


    /**
     * Checks if the indicator ranges overlap, but are not equivalent
     *
     * @param float $indicator1Minimum
     * @param float $indicator2Minimum
     * @param float $indicator1Maximum
     * @param float $indicator2Maximum
     * @return bool
     */
    public function checkOverlap($indicator1Minimum, $indicator2Minimum, $indicator1Maximum, $indicator2Maximum)
    {
        if (($indicator1Minimum > $indicator2Minimum && $indicator1Minimum < $indicator2Maximum) || ($indicator1Maximum > $indicator2Minimum && $indicator1Maximum < $indicator2Maximum)) {

            throw new ValidationException([
                RiskErrorConstants::RISK_M_001
            ], RiskErrorConstants::RISK_M_001, self::RISK_M_001);
        } else {
            return true;
        }
    }
}