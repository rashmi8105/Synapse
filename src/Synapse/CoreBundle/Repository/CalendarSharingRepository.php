<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\CalendarSharing;

class CalendarSharingRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:CalendarSharing';

    public function createDelegateUser($calanderSharing)
    {
        $em = $this->getEntityManager();
        $em->persist($calanderSharing);
        return $calanderSharing;
    }

    public function deleteDelegateUser($calanderSharing)
    {
        $em = $this->getEntityManager();
        $em->remove($calanderSharing);
        return $calanderSharing;
    }

    public function updateDelegateUser($calanderSharing)
    {
        $em = $this->getEntityManager();
        $em->merge($calanderSharing);
        return $calanderSharing;
    }

    /**
     * Listing of all managed user
     * 
     * @param unknown $proxyUserId            
     */
    public function listManagedUser($proxyUserId)
    {
        $em = $this->getEntityManager();
        
        $qb = $em->createQueryBuilder();
        $qb->select('cs.id as calendar_sharing_id', 'IDENTITY(cs.personIdSharedby) as managed_person_id', 'p.firstname as managed_person_first_name', 'p.lastname as managed_person_last_name');
        $qb->from('SynapseCoreBundle:CalendarSharing', 'cs');
        $qb->LEFTJoin('SynapseCoreBundle:person', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = cs.personIdSharedby');
        $qb->where('cs.personIdSharedto = :proxyPersonId');
        $qb->andWhere('cs.isSelected = 1');
        $qb->setParameters(array(
            'proxyPersonId' => $proxyUserId
        ));
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        
        return $result;
    }

    /**
     * Listing of all Selected Proxy Users
     * 
     * @param unknown $proxyUserId            
     */
    public function getSelectedProxyUsers($userId)
    {
        $em = $this->getEntityManager();
        
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(cs.personIdSharedto) as delegated_to_person_id', 'p.firstname as managed_person_first_name', 'p.lastname as managed_person_last_name', 'cs.isSelected as is_selected');
        $qb->from('SynapseCoreBundle:CalendarSharing', 'cs');
        $qb->LEFTJoin('SynapseCoreBundle:person', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = cs.personIdSharedto');
        $qb->where('cs.personIdSharedby = :proxySharedBy');
        $qb->setParameters(array(
            'proxySharedBy' => $userId
        ));
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        
        return $result;
    }
}