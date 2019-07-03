<?php
namespace Synapse\RiskBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use JMS\Serializer\Tests\Fixtures\Publisher;
use Synapse\RiskBundle\Util\Constants\RiskModelConstants;

class RiskModelMasterRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseRiskBundle:RiskModelMaster';

    public function getCountByStatus()
    {
        $searchResult = array();
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('rm.modelState as status', 'count(rm.id) as status_count')
            ->from(RiskModelConstants::RISK_MODEL_MASTER, 'rm')
            ->groupBy('rm.modelState')
            ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    public function getRiskModels($status)
    {
        $em = $this->getEntityManager();
        if ($status == 'Archived') {
            $sql = "select rm.id,rm.name as model_name,rm.model_state, count(distinct rw.risk_variable_id) as variables_count,count(distinct rg.org_id) as campuses_count 
            from risk_model_master as rm 
            left join risk_model_weights as rw on rw.risk_model_id = rm.id 
            left join org_risk_group_model as rg on rg.risk_model_id = rm.id where rm.model_state = 'Archived' and rm.deleted_at is NULL group by rm.id";
        } else 
            if ($status == 'Active') {
                $sql = "select rm.id,rm.name as model_name,rm.model_state, count(distinct rw.risk_variable_id) as variables_count,count(distinct rg.org_id) as campuses_count
            from risk_model_master as rm
            left join risk_model_weights as rw on rw.risk_model_id = rm.id
            left join org_risk_group_model as rg on rg.risk_model_id = rm.id where rm.model_state != 'Archived' and rm.deleted_at is NULL group by rm.id";
            }
        $resultSet = $em->getConnection()->fetchAll($sql);
        return $resultSet;
    }

    public function getPassedModel($date = null)
    {
        $searchResult = array();
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('rm.id', 'rm.modelState as status')
            ->from(RiskModelConstants::RISK_MODEL_MASTER, 'rm')
            ->where('rm.modelState != :status')
            ->andWhere('rm.calculationEndDate < :currentDate')
            ->setParameters(array(
            'currentDate' => $date,
            'status' => 'Archived'
        ))
            ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    public function getModel($id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('rm.id, rm.name, rm.calculationStartDate, rm.calculationEndDate, rm.enrollmentDate, rm.modelState, rml.min, rml.max, rl.riskText');
        $qb->from(RiskModelConstants::RISK_MODEL_MASTER, 'rm');
        $qb->Join('SynapseRiskBundle:RiskModelLevels', 'rml', \Doctrine\ORM\Query\Expr\Join::WITH, 'rml.riskModel = rm.id');
        $qb->Join('SynapseRiskBundle:RiskLevels', 'rl', \Doctrine\ORM\Query\Expr\Join::WITH, 'rl.id = rml.riskLevel');
        $qb->where('rm.id = :modelId');
        $qb->setParameters([
            'modelId' => $id
        ]);
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }

    /*
     * Get risk model data
     * @param string $filter
     */
    public function getModelAssignments($filter)
    {
        $em = $this->getEntityManager();
        $conditionStmt = '';
        // no filter
        $baseQuery = "
    SELECT
        org.id,
        ol.organization_name,
        orgm.risk_group_id,
        rgl.name AS risk_group_name,
        orgm.risk_model_id,
        rm.name AS risk_model_name,
        rm.calculation_start_date,
        rm.calculation_end_date,
        rm.enrollment_date
    FROM
        organization AS org
            LEFT JOIN
        org_risk_group_model AS orgm ON orgm.org_id = org.id
            LEFT JOIN
        organization_lang AS ol ON ol.organization_id = org.id
            LEFT JOIN
        risk_model_master AS rm ON rm.id = orgm.risk_model_id
            LEFT JOIN
        risk_group AS rg ON rg.id = orgm.risk_group_id
            LEFT JOIN
        risk_group_lang AS rgl ON rgl.risk_group_id = orgm.risk_group_id AND rgl.lang_id = ol.lang_id
    WHERE
        org.deleted_at IS NULL
        AND orgm.deleted_at IS NULL
        AND rm.deleted_at IS NULL
        AND rg.deleted_at IS NULL
        AND ol.deleted_at IS NULL
    ";

        switch ($filter) {
            case 'no-group':
                $conditionStmt = "AND orgm.risk_group_id IS NULL";
                break;
            case 'no-model':
                $conditionStmt = "AND orgm.risk_model_id IS NULL ";
                break;
            case 'group-model':
                $conditionStmt = "AND orgm.risk_model_id IS NOT NULL AND orgm.risk_group_id IS NOT NULL";
                break;
        }
        $conditionStmt .= " AND (org.tier IS NULL OR org.tier = '0' OR org.tier = '3')";

        $orderBy = " ORDER BY organization_name, orgm.risk_group_id AND orgm.risk_model_id";

        $sql = $baseQuery . ' ' . $conditionStmt . ' ' . $orderBy;

        $stmt = $em->getConnection()->executeQuery($sql);

        $resultSet = $stmt->fetchAll();;

        return $resultSet;
    }
}