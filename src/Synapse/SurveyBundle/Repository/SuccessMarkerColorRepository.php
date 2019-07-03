<?php

namespace Synapse\SurveyBundle\Repository;

use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;


class SuccessMarkerColorRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSurveyBundle:SuccessMarkerColor';

    /**
     * Given a value for a success marker, assigns it a color using the ranges in the success_marker_color table.
     *
     * @param float $value
     * @return array
     */
    public function getSuccessMarkerColor($value)
    {
        $parameters = ['value' => $value];

        $sql = "SELECT color
                FROM success_marker_color
                WHERE :value BETWEEN min_value AND max_value
                AND deleted_at IS NULL;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $result = $stmt->fetchAll();
        $result = $result[0]['color'];

        return $result;
    }

}