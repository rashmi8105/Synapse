<?php
namespace Synapse\RiskBundle\Repository;

use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;

class RiskLevelsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseRiskBundle:RiskLevels';

    /**
     * Returns a lookup table, where the key is the risk level and the value is an array containing
     * the corresponding color and image name.
     *
     * @return array
     */
    public function getRiskLevelsAndColors()
    {
        $sql = 'select id as risk_level, risk_text, image_name
                from risk_level
                where deleted_at is null;';

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute();

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage());
        }

        $records = $stmt->fetchAll();
        $lookupTable = [];
        foreach ($records as $record) {
            $lookupTable[$record['risk_level']] = [
                'risk_color' => $record['risk_text'],
                'risk_image_name' => $record['image_name']
            ];
        }

        return $lookupTable;
    }
}