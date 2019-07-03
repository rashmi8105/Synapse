<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\ProxyLog;
use Synapse\CoreBundle\Repository\SynapseRepository;

class ProxyLogRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:ProxyLog';

    /**
     *
     * @param ProxyLog $proxyLog            
     * @return ProxyLog
     */
    public function create(ProxyLog $proxyLog)
    {
        $em = $this->getEntityManager();
        $em->persist($proxyLog);
        return $proxyLog;
    }

    /**
     *
     * @param ProxyLog $proxyLog            
     */
    public function remove(ProxyLog $proxyLog)
    {
        $em = $this->getEntityManager();
        $em->remove($proxyLog);
    }
}