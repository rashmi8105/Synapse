<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\SystemAlerts;

class SystemAlertRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:SystemAlerts';

    /**
     * Create SystemAlert
     */
    public function createSystemAlert(SystemAlerts $systemAlert)
    {
        $em = $this->getEntityManager();
        $em->persist($systemAlert);
        return $systemAlert;
    }
}