<?php
namespace Synapse\RiskBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Constants\SearchConstant;
use JMS\DiExtraBundle\Annotation as DI;

class OrgRiskvalCalcInputsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseRiskBundle:OrgRiskvalCalcInputs';

    public function startRiskCalculation()
    {
        try {
            $rsm = new ResultSetMapping();
            $em = $this->getEntityManager();
            $query = $em->createNativeQuery("CALL RiskFactorCalculation()", $rsm);
            $query->execute();
        } catch (\Exception $e) {            
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
        return;
    }
}