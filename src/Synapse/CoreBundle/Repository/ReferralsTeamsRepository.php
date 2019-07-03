<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\ReferralsTeams;

class ReferralsTeamsRepository extends SynapseRepository
{
    const REPOSITORY_KEY = 'SynapseCoreBundle:ReferralsTeams';

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return ReferralsTeams[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }



    public function createReferralsTeams($referralsTeams)
    {

        $em = $this->getEntityManager();
        $em->persist($referralsTeams);
        return $referralsTeams;

    }

    public function removeReferralsTeam($team)
    {

        $em = $this->getEntityManager();
        $em->remove($team);
        return $team;

    }
}
