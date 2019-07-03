<?php
namespace Synapse\CoreBundle\Repository;
use Synapse\CoreBundle\Entity\ContactsTeams;

class ContactsTeamsRepository extends SynapseRepository {

    const REPOSITORY_KEY = 'SynapseCoreBundle:ContactsTeams';

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return ContactsTeams[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    public function createContactsTeams($contactsTeams){
        $em = $this->getEntityManager();
        $em->persist($contactsTeams);

        return $contactsTeams;
    }
    public function deleteContactsTeam($contactsTeams){

        $em = $this->getEntityManager();
        $em->remove($contactsTeams);
        return $contactsTeams;
    }
}