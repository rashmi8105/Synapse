<?php
namespace Synapse\RestBundle\Utility;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("rest_utility_service")
 */
class RestUtilityService
{
    const SERVICE_KEY = 'rest_utility_service';


    /**
     * The current pattern for the sort_by (or sortBy) query parameter is to precede the column name
     * with a + or - to indicate sort direction (or sometimes just using the column name if it's asc).
     * This function parses that string and returns a 2-element array with the column to sort by at index 0
     * and the sort direction ("ASC" or "DESC") at index 1.
     *
     * @param string $sortBy
     * @return array
     */
    public function getSortColumnAndDirection($sortBy)
    {
        if (!empty($sortBy)) {

            if ($sortBy[0] == '+') {
                $sortDirection = 'ASC';
                $sortBy = substr($sortBy, 1);
            } elseif ($sortBy[0] == '-') {
                $sortDirection = 'DESC';
                $sortBy = substr($sortBy, 1);
            } else {
                $sortDirection = 'ASC';
            }

        } else {
            $sortBy = null;
            $sortDirection = null;
        }

        return [$sortBy, $sortDirection];
    }
}