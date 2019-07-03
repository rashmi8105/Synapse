<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\ORM\Query\Expr\Expr;
use Doctrine\ORM\Query\Expr\Join;
use Synapse\CoreBundle\Entity\PermissionSet;
use Synapse\RestBundle\Entity\Error;

class PermissionSetRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:PermissionSet';

    public function createPermissionSet(PermissionSet $permissionSet){

        $em = $this->getEntityManager();
        $em->persist($permissionSet);
        return $permissionSet;
    }

    public function listPermissionsetCount(){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
        ->select('SUM(CASE WHEN s.isActive = 1 then 1 ELSE 0 END) as count_active', 'SUM(CASE WHEN s.isActive = 0 then 1 ELSE 0 END) as count_archive')
        ->from('SynapseCoreBundle:PermissionSet', 's')
        ->getQuery ();
        $resultSet = $qb->getArrayResult();
        return $resultSet;
    }
}