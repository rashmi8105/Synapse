<?php
namespace Synapse\CoreBundle\Repository;



class AccessLogRepository extends SynapseRepository {

    const REPOSITORY_KEY = 'SynapseCoreBundle:AccessLog';

    public function createAccessLog($accesslog){

        $em = $this->getEntityManager();
        $em->persist($accesslog);
        return $accesslog;

    }

}