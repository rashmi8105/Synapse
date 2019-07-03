<?php
namespace Synapse\CoreBundle\Repository;


use Synapse\CoreBundle\Entity\ActivityReferenceUnassigned;

class ActivityReferenceUnassignedRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:ActivityReferenceUnassigned';

    public function getReasonRoutingList($organizationId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
        ->select('aru')
        ->from('SynapseCoreBundle:ActivityReferenceUnassigned', 'aru')
        ->Join('aru.activityReference', 'ar')
        ->where('aru.activityReference = ar.id')
        ->where('aru.organization = :organizationid')
        ->setParameters(array(
                'organizationid' => $organizationId
        ))
        ->getQuery();
        return  $qb->getResult();


    }
}