<?php
namespace Synapse\CoreBundle\Repository;
use Synapse\CoreBundle\Entity\NoteTeams;



class NoteTeamsRepository extends SynapseRepository {

    const REPOSITORY_KEY = 'SynapseCoreBundle:NoteTeams';

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return NoteTeams[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    public function createNoteTeams($notesTeams){
        $em = $this->getEntityManager();
        $em->persist($notesTeams);

        return $notesTeams;
    }

    public function remove(NoteTeams $noteTeam)
    {
        $em = $this->getEntityManager();
        $em->remove($noteTeam);
    }
}