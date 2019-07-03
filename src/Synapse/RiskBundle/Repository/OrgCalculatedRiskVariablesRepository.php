<?php
namespace Synapse\RiskBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;

class OrgCalculatedRiskVariablesRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseRiskBundle:OrgCalculatedRiskVariables';

    public function getCalculatedRiskVariables($personId, $start, $end, $riskmodel, $org_id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(ocrv.riskVariable) as risk_variable_id', 'ocrv.calcBucketValue as calc_bucket_value', 'ocrv.calcWeight as calc_weight', 'ocrv.riskSourceValue as risk_source_value');
        $qb->addSelect('rmm.name as risk_model_name');
        $qb->addSelect('ocrv.createdAt as created_at');
        
        $qb->from('SynapseRiskBundle:OrgCalculatedRiskVariables', 'ocrv');
        $qb->join('SynapseRiskBundle:RiskModelMaster', 'rmm', \Doctrine\ORM\Query\Expr\Join::WITH, 'rmm.id = ocrv.riskModel');
        $qb->where('ocrv.person =:person  AND ocrv.riskModel = :model AND ocrv.org =:org AND ocrv.createdAt >= :start AND ocrv.createdAt <= :end');
        
        $qb->setParameters([
            'person' => $personId,
            'model' => $riskmodel,
            'org' => $org_id,
            'start' => $start,
            'end' => $end
        ]);
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        
        return $resultSet;
        
    }
}