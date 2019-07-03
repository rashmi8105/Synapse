<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\ReferralsInterestedParties;

class ReferralsInterestedPartiesRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:ReferralsInterestedParties';

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return ReferralsInterestedParties[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    public function createReferralsInterestedParties($referralsInterestedParties)
    {

        $em = $this->getEntityManager();
        $em->persist($referralsInterestedParties);
        return $referralsInterestedParties;

    }

    public function removeReferralsInterestedParties($referralsInterestedParties)
    {

        $em = $this->getEntityManager();
        $em->remove($referralsInterestedParties);
        return $referralsInterestedParties;

    }
}
