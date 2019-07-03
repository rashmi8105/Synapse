<?php
namespace Synapse\RiskBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\RiskBundle\Util\Constants\RiskGroupConstants;

class RiskGroupLangRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseRiskBundle:RiskGroupLang';

    public function getRiskGroups($lang)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        
        $qb->select('rgl.name as groupName', 'rgl.description as groupDescription');
        $qb->addselect('rg.id');
        $qb->from(RiskGroupConstants::RISK_GROUP_LANG, 'rgl');
        $qb->Join(RiskGroupConstants::RISK_GROUP, 'rg', \Doctrine\ORM\Query\Expr\Join::WITH, 'rg.id = rgl.riskGroup');
        $qb->where('rgl.lang = :lang');
        $qb->setParameters([
            'lang' => $lang
        ]);
        
        
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }
    
    public function getRiskGroupById($lang,$id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
    
        $qb->select('rgl.name as groupName', 'rgl.description as groupDescription');
        $qb->addselect('rg.id');
        $qb->from(RiskGroupConstants::RISK_GROUP_LANG, 'rgl');
        $qb->Join(RiskGroupConstants::RISK_GROUP, 'rg', \Doctrine\ORM\Query\Expr\Join::WITH, 'rg.id = rgl.riskGroup');
        $qb->where('rgl.lang = :lang AND rg.id = :id');
        $qb->setParameters([
            'lang' => $lang,
            "id"=> $id
            ]);
    
    
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }
}