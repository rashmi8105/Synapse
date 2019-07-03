<?php
namespace Synapse\SearchBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\SearchBundle\Entity\OrgSearchShared;

class OrgSearchSharedRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSearchBundle:OrgSearchShared';

    public function create(OrgSearchShared $orgSearchShared)
    {
        $em = $this->getEntityManager();
        $em->persist($orgSearchShared);
        return $orgSearchShared;
    }

    public function getSharedByUsers($loggedInUser, $orgId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('IDENTITY(oss.personIdSharedwith) as personIdSharedwith,IDENTITY(oss.personIdSharedby) as personIdSharedby,IDENTITY(oss.orgSearchIdSource) as sourceId,os.sharedOn as dateShared,os.id as id,IDENTITY(oss.orgSearchIdDest) as destId, os.name as name')
            ->from('SynapseSearchBundle:OrgSearchShared', 'oss')
            ->join('SynapseSearchBundle:OrgSearch', 'os', \Doctrine\ORM\Query\Expr\Join::WITH, 'oss.orgSearchIdDest = os.id')
            ->where('oss.personIdSharedwith = :person')
            ->orWhere('oss.personIdSharedby = :person')
            ->andWhere('os.organization = :orgId')
            ->andWhere('os.sharedOn IS NOT NULL')
            ->andWhere('os.deletedAt IS NULL and oss.deletedAt IS NULL')
            ->setParameters(array(
            'person' => $loggedInUser,
            'orgId' => $orgId
        ))
            ->orderBy('os.sharedOn', 'DESC')
            ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    public function deleteSharedSearch(OrgSearchShared $orgSearchShared)
    {
        $this->getEntityManager()->remove($orgSearchShared);
    }
}
