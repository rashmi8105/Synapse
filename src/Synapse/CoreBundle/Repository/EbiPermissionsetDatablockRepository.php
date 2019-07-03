<?php

namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CoreBundle\Entity\EbiPermissionsetDatablock;

class EbiPermissionsetDatablockRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:EbiPermissionsetDatablock';

    public function remove($entity)
    {
        $this->getEntityManager()->remove($entity);
    }
    public function createEbiPermissionsetDatablock(EbiPermissionsetDatablock $ebiPermissionsetDatablock){

        $em = $this->getEntityManager();
        $em->persist($ebiPermissionsetDatablock);
        return $ebiPermissionsetDatablock;


    }
    
    public function getEbiDataBlockID($permissionsetID, $type)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()
            ->select('IDENTITY(e.dataBlock)')
            ->from('SynapseCoreBundle:EbiPermissionsetDatablock', 'e')
            ->where('e.ebiPermissionset = :permissionsetID')
            ->andWhere('e.blockType = :type')
            ->setParameters(array(
                'permissionsetID' => $permissionsetID,
                'type'  => $type
            ));        
        $resultSet = $queryBuilder->getQuery()->getScalarResult();
        return $resultSet;
    }
}