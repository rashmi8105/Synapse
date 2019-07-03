<?php
namespace Synapse\RiskBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;

class RiskGroupRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseRiskBundle:RiskGroup';

    public function startTransaction()
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();
    }

    public function completeTransaction()
    {
        $em = $this->getEntityManager();
        $em->getConnection()->commit();
    }

    public function rollbackTransaction()
    {
        $em = $this->getEntityManager();
        $em->getConnection()->rollback();
    }
}