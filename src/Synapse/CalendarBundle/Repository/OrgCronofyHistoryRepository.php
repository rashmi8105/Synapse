<?php
namespace Synapse\CalendarBundle\Repository;

use Synapse\CalendarBundle\Entity\OrgCronofyHistory;
use Synapse\CoreBundle\Repository\SynapseRepository;

class OrgCronofyHistoryRepository extends SynapseRepository
{
    const REPOSITORY_KEY = 'SynapseCalendarBundle:OrgCronofyHistory';

    public function create(OrgCronofyHistory $orgCronofyHistory)
    {
        $em = $this->getEntityManager();
        $em->persist($orgCronofyHistory);
        return $orgCronofyHistory;
    }

}