<?php
namespace Synapse\CoreBundle\Service\Utility;

use Synapse\CoreBundle\SynapseConstant;

/**
 * @DI\Service("math_utility_service")
 *
 * This class is intended for utility functions which process mathematical operations without retrieving data from the database.
 * They are not specific to our application, and are the type of functions that could be in a generic PHP library
 * (but aren't in existing libraries, as far as we could find).
 */
class MathUtilityService
{
    const SERVICE_KEY = 'math_utility_service';



    /**
     * Gets the division result
     *
     * @param int $numerator
     * @param int $denominator
     * @return float
     */
    public function divide($numerator, $denominator)
    {
        try {
            $result = $numerator / $denominator;
        } catch (\Exception $e) {
            $result = 0;
        }

        return $result;
    }

    /**
     * Gets the rounded division result
     *
     * @param $numerator
     * @param $denominator
     * @param int $precision - Number of decimal places included in the result
     * @param int $rounding - PHP Constant for rounding settings.
     * @return float
     */
    public function divideAndRound($numerator, $denominator, $precision = 2, $rounding = PHP_ROUND_HALF_UP)
    {
        $divisionResult = $this->divide($numerator, $denominator);

        try {
            $result = round($divisionResult, $precision, $rounding);
        } catch (\Exception $e) {
            $result = $divisionResult;
        }

        return $result;
    }

    /**
     * Gets the percentage from the result of the numerator divided by the denominator.
     *
     * @param int $numerator
     * @param int $denominator
     * @param int $precision - Number of decimal points that should be in the division result.
     * @param int $rounding - PHP Constant for rounding settings.
     * @return float|int
     */
    public function getPercentage($numerator, $denominator, $precision = 8, $rounding = PHP_ROUND_HALF_UP)
    {
        $divisionResult = $this->divideAndRound($numerator, $denominator, $precision, $rounding);

        return SynapseConstant::ONE_HUNDRED_PERCENT * $divisionResult;
    }

    /**
     * Returns the ceiling of the division result
     *
     * @param int $numerator
     * @param int $denominator
     * @return float
     */
    public function divideAndCeiling($numerator, $denominator)
    {
        $divisionResult = $this->divide($numerator, $denominator);

        return ceil($divisionResult);
    }

}