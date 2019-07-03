<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\ORM\Query\Expr\Expr;
use Doctrine\ORM\Query\Expr\Join;
use Synapse\CoreBundle\Entity\EbiPermissionsetFeatures;


class EbiPermissionsetFeaturesRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:EbiPermissionsetFeatures';

    public function getPermissionSetFeatures($Id)
    {
        $em = $this->getEntityManager();
        
        $qb = $em->createQueryBuilder()
            ->select('m.featureName as name, f.id as feature_id,e.publicCreate,e.publicView, e.privateCreate,e.teamCreate, e.teamView, e.receiveReferral')
            ->from('SynapseCoreBundle:EbiPermissionsetFeatures', 'e')
            ->LEFTJoin('SynapseCoreBundle:FeatureMaster', 'f', \Doctrine\ORM\Query\Expr\Join::WITH, 'e.feature = f.id')
            ->LEFTJoin('SynapseCoreBundle:FeatureMasterLang', 'm', \Doctrine\ORM\Query\Expr\Join::WITH, 'm.featureMaster = e.feature')
            ->where('e.ebiPermissionset = :id')
            ->setParameters(array(
            'id' => $Id
        ))
            ->getQuery();
        
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    public function createEbiPermissionsetFeatures(EbiPermissionsetFeatures $ebiPermissionsetFeatures){

        $em = $this->getEntityManager();
        $em->persist($ebiPermissionsetFeatures);
        return $ebiPermissionsetFeatures;

    }

}