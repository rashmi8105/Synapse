<?php
namespace Synapse\RiskBundle\Repository;

use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;

class PersonRiskLevelHistoryRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseRiskBundle:PersonRiskLevelHistory';

    public function getRiskScores($personId, $start, $end, $riskmodel)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(prlh.riskLevel) as risk_level', 'prlh.riskScore as risk_score_value','prlh.dateCaptured as created_at');
        $qb->addSelect('rmm.name as risk_model_name');

        $qb->from('SynapseRiskBundle:PersonRiskLevelHistory', 'prlh');
        $qb->join('SynapseRiskBundle:RiskModelMaster', 'rmm', \Doctrine\ORM\Query\Expr\Join::WITH, 'rmm.id = prlh.riskModel');
        $qb->where('prlh.person =:person  AND prlh.riskModel = :model AND prlh.dateCaptured >= :start AND prlh.dateCaptured <= :end');

        $qb->setParameters([
            'person' => $personId,
            'model' => $riskmodel,
            'start' => $start,
            'end' => $end
            ]);
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
    
        return $resultSet;
        // AND ocrv.createdAt >= :start AND ocrv.createdAt <= :end
    }

    /**
     * Gets the mean of a year/term specific profile item related to a person risk level from person risk level history by organization
     *
     * @param INT $orgId
     * @param STRING $riskCalculationStart
     * @param STRING $riskCalculationEnd
     * @param ARRAY $academic_years
     * @param ARRAY $academic_terms
     * @param INT $ebiMetadataId
     * @param ARRAY $studentIds
     *
     * @return Array
     */
    public function getRiskLevelAndMeanEbiMetadataByOrgYearTerm($orgId, $riskCalculationStart, $riskCalculationEnd, $academic_years, $academic_terms, $ebiMetadataId, $studentIds, $roundToDecimal = 2){

        if(count($academic_years) < 1 OR count($academic_terms) < 1 OR count($studentIds) < 1){
            return array();
        }

        $studentHolders = implode(',', array_fill(0, count($studentIds), '?'));
        $yearPlaceholders = implode(',', array_fill(0, count($academic_years), '?'));
        $termPlaceholders = implode(',', array_fill(0, count($academic_terms), '?'));

        $sql = "select ps.risk_level, ps.org_academic_year_id, ps.org_academic_terms_id, IF(student_count is null, 0, student_count) as student_count, mean_value from
            (select ?, oay.id as org_academic_year_id, oat.id as org_academic_terms_id, rl.id as risk_level, rl.risk_text, rl.report_sequence from org_academic_terms oat INNER JOIN org_academic_year oay On oat.org_academic_year_id = oay.id
            CROSS JOIN risk_level rl
            where oay.id in ($yearPlaceholders)
                    and oat.id in ($termPlaceholders)
                    ) as ps
            LEFT JOIN (select ops.organization_id as org_id, risk_level as risk_level, pem.org_academic_year_id, pem.org_academic_terms_id, COUNT(*) as student_count,
                    ROUND(AVG(pem.metadata_value), ?) as mean_value from person_ebi_metadata pem
                    INNER JOIN org_person_student ops On pem.person_id = ops.person_id AND ops.organization_id = ?
                    INNER JOIN org_academic_terms oat ON oat.id = pem.org_academic_terms_id
                    LEFT JOIN (
                          select ogs.organization_id as org_id, prlh.person_id, prlh.risk_model_id, prlh.risk_level, prlh.date_captured from person_risk_level_history prlh
                            INNER JOIN org_person_student ogs ON ogs.person_id = prlh.person_id
                            where organization_id = ? AND date_captured =
                            (select date_captured from person_risk_level_history perc where perc.person_id = prlh.person_id AND date_captured BETWEEN ?
                            AND ?
                            ORDER BY date_captured DESC LIMIT 1)
                    ) as prl
                    ON  pem.person_id = prl.person_id AND prl.org_id = ops.organization_id
                    where pem.deleted_at is null
                    and ops.deleted_at is null
                    and pem.ebi_metadata_id = ?
                    and pem.org_academic_year_id in ($yearPlaceholders)
                    and pem.org_academic_terms_id in ($termPlaceholders)
                    and pem.person_id in ($studentHolders)
                    GROUP BY ops.organization_id, risk_level, org_academic_year_id, org_academic_terms_id) as dc
                    ON dc.org_academic_year_id = ps.org_academic_year_id
                    AND dc.org_academic_terms_id = ps.org_academic_terms_id
                    AND (dc.risk_level = ps.risk_level OR (dc.risk_level is null and ps.risk_text = 'gray'))
                    ORDER BY ps.org_academic_year_id ASC, ps.org_academic_terms_id ASC , ps.report_sequence ASC;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $orgArray = array($orgId);
            $namedParameters = array($roundToDecimal, $orgId, $orgId, $riskCalculationStart, $riskCalculationEnd, $ebiMetadataId);
            $parameters = array_merge($orgArray, $academic_years, $academic_terms, $namedParameters, $academic_years, $academic_terms, $studentIds);

            $stmt->execute($parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $stmt->fetchAll();



    }

    /**
     * Gets organization students risk levels within given date range
     *
     * @param STRING $riskCalculationStart
     * @param STRING $riskCalculationEnd
     * @param ARRAY $studentIds  (Limiter)
     *
     * @return Array
     */
    public function getRiskLevelHistoryByDateRange($riskCalculationStart, $riskCalculationEnd, $studentIds){

        //Return empty array if mandatory fields are not set properly
        if(empty($riskCalculationStart) OR empty($riskCalculationEnd) OR count($studentIds) < 1){
                return array();
        }

        $studentHolders = implode(',', array_fill(0, count($studentIds), '?'));

            $sql = "SELECT rl.id as risk_level, COUNT(colors.person_id) as total_students
                    FROM risk_level rl
                    LEFT JOIN
                    (
                       SELECT prlh.person_id, prlh.risk_level
                       FROM person_risk_level_history prlh
                       WHERE prlh.person_id IN ($studentHolders)
                       AND date_captured =
                       (
                          SELECT date_captured
                          FROM person_risk_level_history perh
                          WHERE perh.person_id = prlh.person_id
                          AND date_captured BETWEEN ? AND ?
                          ORDER BY date_captured DESC LIMIT 1
                       )
                    ) AS colors
                    ON rl.id = colors.risk_level
                    GROUP BY rl.id";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $riskDatesArray = array($riskCalculationStart, $riskCalculationEnd);
            $parameters = array_merge($studentIds, $riskDatesArray);
            $stmt->execute($parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $stmt->fetchAll();
    }
}