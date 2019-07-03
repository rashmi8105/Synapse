<?php

namespace Synapse\CoreBundle\Repository;
use Synapse\CoreBundle\Entity\StudentDbViewLog;

class StudentDbViewLogRepository extends SynapseRepository 
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:StudentDbViewLog';

    public function createStudentDbViewLog($studentDbViewLog)
    {
        $em = $this->getEntityManager();
        $em->persist($studentDbViewLog);
        return $studentDbViewLog;
    }

}