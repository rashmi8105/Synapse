<?php
namespace Synapse\CoreBundle\Repository;
use Synapse\CoreBundle\Entity\EmailTeams;

class EmailTeamsRepository extends SynapseRepository {

    const REPOSITORY_KEY = 'SynapseCoreBundle:EmailTeams';

    public function createEmailTeams($emailTeams){
        
        $em = $this->getEntityManager();
        $em->persist($emailTeams);

        return $emailTeams;
    }
    public function deleteEmailTeam($emailTeams){
        
        $em = $this->getEntityManager();
        $em->remove($emailTeams);
        
        return $emailTeams;
    }
}