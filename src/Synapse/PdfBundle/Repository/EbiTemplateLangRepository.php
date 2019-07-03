<?php
namespace Synapse\PdfBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;

class EbiTemplateLangRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapsePdfBundle:EbiTemplateLang';

    public function getTemplateByKey($key)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('etl.body as body');
        $qb->from('SynapsePdfBundle:EbiTemplateLang', 'etl');
        $qb->leftJoin('SynapsePdfBundle:EbiTemplate', 'et', \Doctrine\ORM\Query\Expr\Join::WITH, 'et.key = etl.ebiTemplateKey');
        $qb->where('et.key = :key');
        $qb->setParameters(array(
            'key' => $key
        ));
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }
}