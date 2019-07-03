<?php
namespace Synapse\SearchBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\SearchBundle\Entity\OrgSearchSharedBy;

class OrgSearchSharedByRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSearchBundle:OrgSearchSharedBy';

    /**
     * Finds a single entity by a set of criteria.
     * Override added to inform PhpStorm about the return type.
     *
     * @param array $criteria
     * @param array|null $orderBy
     *
     * @return OrgSearchSharedBy
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }


    public function create(OrgSearchSharedBy $orgSearchSharedBy)
    {
        $em = $this->getEntityManager();
        $em->persist($orgSearchSharedBy);
        return $orgSearchSharedBy;
    }
    
    public function deleteSharedSearchBy(OrgSearchSharedBy $orgSearchSharedBy) {
        $this->getEntityManager()->remove($orgSearchSharedBy);
    }
}