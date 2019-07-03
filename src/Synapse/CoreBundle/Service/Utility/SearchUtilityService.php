<?php
namespace Synapse\CoreBundle\Service\Utility;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("search_utility_service")
 */

class SearchUtilityService
{
    const SERVICE_KEY = 'search_utility_service';

    const CONTACT_TYPES = "contacttypes";

    const REFL_STATUS = "referralstatus";

    /**
     * Returns appended query
     *
     * @param string $value
     * @param string $append
     * @return string
     */
    public function makeSqlQuery($value, $append)
    {
        $valueCount = substr_count($value, ',');
        if ($value == null || strlen($value) < 1) {
            $sqlAppend = "";
        } elseif ($valueCount == 0 && strlen($value) > 0) {
            $sqlAppend = $append . " = '" . $value . "'";
        } elseif ($valueCount > 0) {
            $valueArray = explode(',', $value);
            foreach ($valueArray as $arrayData) {
                if (trim($arrayData) != "") {
                    $resultArray[] = $arrayData;
                }
                $value = "'" . implode("','", $resultArray) . "'";
            }
            $sqlAppend = $append . " in ($value)";
        } else {
            $sqlAppend = "";
        }
        return $sqlAppend;
    }
}