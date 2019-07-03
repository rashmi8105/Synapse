<?php
namespace Synapse\RiskBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;

class OrgRiskGroupModelRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseRiskBundle:OrgRiskGroupModel';

    public function createAssignment($orgRiskModel)
    {
        $em = $this->getEntityManager();
        $em->persist($orgRiskModel);
        return $orgRiskModel;
    }


    /**
     * Get the risk groups for an organization
     *
     * @param $organizationId
     * @return array
     */
    public function getRiskGroupsForOrganization($organizationId)
    {
        $parameters = ['organizationId' => $organizationId];
        $sql = "
        SELECT
            rmm.id AS risk_model_id,
            rmm.name AS risk_model_name,
            rmm.model_state AS model_state,
            rmm.calculation_start_date AS calculation_start_date,
            rmm.calculation_end_date AS calculation_stop_date,
            rmm.enrollment_date AS enrollment_end_date,
            orgm.risk_group_id AS risk_group_id,
            rgl.name AS risk_group_name,
            student_count,
            rgl.description AS risk_group_description
        FROM
            risk_group_lang rgl
                INNER JOIN
            org_risk_group_model orgm ON rgl.risk_group_id = orgm.risk_group_id
                LEFT JOIN
            risk_model_master rmm ON rmm.id = orgm.risk_model_id
                LEFT JOIN
            (SELECT
                rgph.risk_group_id, COUNT(ops.person_id) student_count
            FROM
                risk_group_person_history rgph
            JOIN org_person_student ops ON ops.person_id = rgph.person_id
                AND ops.organization_id = :organizationId
                AND ops.deleted_at IS NULL
            GROUP BY rgph.risk_group_id) rgphs ON rgphs.risk_group_id = orgm.risk_group_id
        WHERE
            orgm.org_id = :organizationId
                AND orgm.deleted_at IS NULL
                AND rmm.deleted_at IS NULL
        GROUP BY rgl.risk_group_id , rmm.id
        ORDER BY rgl.risk_group_id DESC;";

        $results = $this->executeQueryFetchAll($sql, $parameters);
        return $results;
    }

    public function getRiskGroupByModel($modelid)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(orgm.riskGroup) as risk_group_id', 'IDENTITY(orgm.org) as org_id');
        $qb->from('SynapseRiskBundle:OrgRiskGroupModel', 'orgm');
        $qb->leftJoin('SynapseRiskBundle:RiskModelMaster', 'rmm', \Doctrine\ORM\Query\Expr\Join::WITH, 'rmm.id = orgm.riskModel');
        $qb->where('orgm.riskModel = :model');
        $qb->setParameters([
            'model' => $modelid
        ]);
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }
}