<?php
namespace Synapse\SearchBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\SearchBundle\Entity\OrgSearchSharedWith;

class OrgSearchSharedWithRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSearchBundle:OrgSearchSharedWith';

    public function create(OrgSearchSharedWith $orgSearchSharedWith)
    {
        $em = $this->getEntityManager();
        $em->persist($orgSearchSharedWith);
        return $orgSearchSharedWith;
    }

    public function deleteSharedSearchWith(OrgSearchSharedWith $orgSearchSharedWith)
    {
        $this->getEntityManager()->remove($orgSearchSharedWith);
    }

    public function getSharedSearchWith()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('IDENTITY(osw.orgSearch) as orgSearch, IDENTITY(osw.personSharedwith) as personIdSharedwith,IDENTITY(osw.orgSearchDest) as shared_search_id,osw.sharedOn as dateShared,p.firstname, p.lastname')
            ->from('SynapseSearchBundle:OrgSearchSharedWith', 'osw')
            ->join('SynapseCoreBundle:Person', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = osw.personSharedwith')
            ->orderBy('osw.sharedOn', 'DESC')
            ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }
}