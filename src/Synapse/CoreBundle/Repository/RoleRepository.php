<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\Role;
use Doctrine\ORM\Mapping\ClassMetadata as ORM_Mapping;

class RoleRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:Role';

    public function getCoordinatorRoleID()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $queryBuilder = $qb->select('rolelang.id')
            ->from('SynapseCoreBundle:RoleLang', 'rolelang')
            ->where('rolelang.roleName LIKE :roleType')
            ->setParameters(array(
            'roleType' => '%coordinator'
        ))
            ->getQuery();
        $resultSet = $queryBuilder->getResult();
        return $resultSet;
    }
}
