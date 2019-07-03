<?php
namespace Synapse\RiskBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;

class RiskModelLevelsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseRiskBundle:RiskModelLevels';

    public function getRiskIndicators()
    {
        $em = $this->getEntityManager();
        
        $qb = $em->createQueryBuilder();
        $qb->select('risklevels.riskLevel as risk_level', 'risklevels.riskText as risk_text','risklevels.imageName')
            ->from('SynapseRiskBundle:RiskModelLevels', 'risklevels')
            ->LEFTJoin('SynapseRiskBundle:RiskModelMaster', 'master', \Doctrine\ORM\Query\Expr\Join::WITH, 'master.id = risklevels.riskModel')
            ->where('master.riskKey= :key')
            ->setParameters(array(
            'key' => 'O'
        ));
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        if (count($result) <= 0) {
            $result = null;
        }
        return $result;
    }
}