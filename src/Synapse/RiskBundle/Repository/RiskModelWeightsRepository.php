<?php
namespace Synapse\RiskBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;

class RiskModelWeightsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseRiskBundle:RiskModelWeights';

    public function createModelVars($modelVars)
    {
        $em = $this->getEntityManager();
        $em->persist($modelVars);
        return $modelVars;
    }

    public function getRiskModelWeightByModel($model)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(rmw.riskModel) as ModelID', 'rv.riskBVariable as RiskVarName', 'rmw.weight as Weight');
        $qb->from('SynapseRiskBundle:RiskModelWeights', 'rmw');
        $qb->Join('SynapseRiskBundle:RiskVariable', 'rv', \Doctrine\ORM\Query\Expr\Join::WITH, 'rv.id = rmw.riskVariable');
        $qb->where('rmw.riskModel = :model');
        $qb->setParameters([
            'model' => $model
        ]);
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }

    public function getRiskModelWeights()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(rmw.riskModel) as ModelID', 'rv.riskBVariable as RiskVarName', 'rmw.weight as Weight');
        $qb->from('SynapseRiskBundle:RiskModelWeights', 'rmw');
        $qb->Join('SynapseRiskBundle:RiskVariable', 'rv', \Doctrine\ORM\Query\Expr\Join::WITH, 'rv.id = rmw.riskVariable');
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }
    
    public function getTotalWeightCount()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(rmw.riskModel) as totalCount');
        $qb->from('SynapseRiskBundle:RiskModelWeights', 'rmw');
        $query = $qb->getQuery();
        return $query->getSingleScalarResult();
    }
}