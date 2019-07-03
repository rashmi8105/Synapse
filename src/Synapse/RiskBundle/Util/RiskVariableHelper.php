<?php
namespace Synapse\RiskBundle\Util;

use Synapse\RestBundle\Exception\ValidationException;
use Synapse\RiskBundle\Util\Constants\RiskVariableConstants;

class RiskVariableHelper
{

    public static function getVaiableType($riskVariableDto)
    {
        $variable = NULL;
        if ($riskVariableDto->getIsContinuous()) {
            $variable = RiskVariableConstants::RISK_VARIABLE_TYPE_CONTINUOUS;
        } else {
            $variable = RiskVariableConstants::RISK_VARIABLE_TYPE_CATEGORICAL;
        }
        return $variable;
    }

    public static function getNullIfEmpty($value)
    {
        $rtValue = '';
        if (empty($value)  && strlen($value) == 0) {
            $rtValue = NULL;
        } else {
            $rtValue = $value;
        }
        return $rtValue;
    }

    public static function validateEntity($errors)
    {
        if (count($errors) > 0) {
            $errorsString = "";
            foreach ($errors as $error) {
                
                $errorsString .= $error->getMessage();
            }
            
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'duplicate_error');
        }
    }

    public static function isCalculatedFiledsEmpty($calData)
    {
        if (empty($calData->getCalculationType()) || empty($calData->getCalculationStartDate()) || empty($calData->getCalculationStopDate())) {
            throw new ValidationException([
                '"Missing required calculated field.'
            ], 'Missing required calculated field.', RiskVariableConstants::ERR_RISK_001);
        } elseif (! in_array($calData->getCalculationType(), [
            'Sum',
            'Most Recent',
            'Average',
            'Count',
            'Academic Update'
        ])) {
            throw new ValidationException([
                RiskVariableConstants::INVALID_CALC_TYPE
            ], RiskVariableConstants::INVALID_CALC_TYPE, RiskVariableConstants::ERR_RISK_001);
        } else {
            return true;
        }
    }

    public static function CalcStartEndDateValidation($start, $end)
    {
        if ($end <= $start) {
            
            throw new ValidationException([
                "Calculation Dates are invalid"
            ], "Calculation Dates are invalid", 'RISK_M_003');
        } else {
            return true;
        }
    }

    public static function validateCalType($riskVariableDto)
    {
        $calData = $riskVariableDto->getCalculatedData();
        if (! $riskVariableDto->getIsContinuous() && $riskVariableDto->getIsCalculated()) {
            if (! in_array($calData->getCalculationType(), [
                'Most Recent',
                'Count'
            ])) {
                throw new ValidationException([
                    RiskVariableConstants::INVALID_CALC_TYPE
                ], RiskVariableConstants::INVALID_CALC_TYPE, RiskVariableConstants::ERR_RISK_001);
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    public static function getEmptyIfNull($val)
    {
        return is_null($val) ? "" : $val;
    }
}