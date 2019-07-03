<?php
namespace Synapse\MultiCampusBundle\Repository;

use Synapse\MultiCampusBundle\Entity\OrgChangeRequest;
use Synapse\CoreBundle\Repository\SynapseRepository;

class OrgChangeRequestRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseMultiCampusBundle:OrgChangeRequest';

    public function create($changeRequest)
    {
        $em = $this->getEntityManager();
        $em->persist($changeRequest);
        return $changeRequest;
    }

    public function remove(OrgChangeRequest $changeRequest)
    {
        $em = $this->getEntityManager();
        $em->remove($changeRequest);
    }   
}