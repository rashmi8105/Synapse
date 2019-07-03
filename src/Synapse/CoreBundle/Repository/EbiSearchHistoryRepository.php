<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\EbiSearchHistory;

class EbiSearchHistoryRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:EbiSearchHistory';

    public function updateHistory(EbiSearchHistory $ebiHistory)
    {
        $em = $this->getEntityManager();
        $em->merge($ebiHistory);
        $em->flush();
    }
}