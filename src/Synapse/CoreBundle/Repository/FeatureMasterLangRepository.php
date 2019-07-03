<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Doctrine\ORM\Query\Expr\Expr;
use Doctrine\ORM\Query\Expr\Join;
use SebastianBergmann\Exporter\Exception;
use Synapse\CoreBundle\Entity\FeatureMasterLang;
use Synapse\RestBundle\Entity\Error;

class FeatureMasterLangRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:FeatureMasterLang';

    /**
     * This gives doctrine a hint about the kind of object findOneBy returns (in the return statement)
     *
     * @param array $criteria
     * @param array|null|null $orderBy
     * @return FeatureMasterLang|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }


    public function listFeaturesAll($langid)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('m.id as feature_id', 's.featureName as feature_name')
            ->from('SynapseCoreBundle:FeatureMasterLang', 's')
            ->LEFTJoin('SynapseCoreBundle:FeatureMaster', 'm', \Doctrine\ORM\Query\Expr\Join::WITH, 's.featureMaster = m.id')
            ->where('s.lang = :langid')
            ->setParameters(array(
            'langid' => $langid
        ))
            ->getQuery();
        $resultSet = $qb->getResult();

        return $resultSet;
    }

}