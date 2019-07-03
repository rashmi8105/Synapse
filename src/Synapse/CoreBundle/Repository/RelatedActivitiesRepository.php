<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\RelatedActivities;

class RelatedActivitiesRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:RelatedActivities';

    public function createRelatedActivities($relatedActivities)
    {
        $em = $this->getEntityManager();
        $em->persist($relatedActivities);
        return $relatedActivities;
    }

    public function remove($relatedActivities)
    {
        $em = $this->getEntityManager();
        $em->remove($relatedActivities);
    }
}