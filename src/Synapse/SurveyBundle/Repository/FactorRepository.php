<?php
namespace Synapse\SurveyBundle\Repository;

use Doctrine\DBAL\Connection;
use Guzzle\Service\Exception\ValidationException;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\SurveyBundle\Entity\Factor;
use Synapse\CoreBundle\Repository\SynapseRepository;

/**
 * FactorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FactorRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSurveyBundle:Factor';

    public function getAllFactors($surveyId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('F.id as id', 'FL.name as text', 'IDENTITY(FQ.ebiQuestion) as fq_eqid', 'IDENTITY(FQ.surveyQuestions) as fq_sqid', 'IDENTITY(SQ.survey) as sq_surveyid', 'SQ.id as sq_sqid')
            ->from('SynapseSurveyBundle:Factor', 'F')
            ->LEFTJoin('SynapseSurveyBundle:FactorLang', 'FL', \Doctrine\ORM\Query\Expr\Join::WITH, 'F.id = FL.factor')
            ->INNERJoin('SynapseSurveyBundle:FactorQuestions', 'FQ', \Doctrine\ORM\Query\Expr\Join::WITH, 'F.id = FQ.factor')
            ->INNERJoin('SynapseSurveyBundle:SurveyQuestions', 'SQ', \Doctrine\ORM\Query\Expr\Join::WITH, 'SQ.ebiQuestion = FQ.ebiQuestion OR SQ.id = FQ.surveyQuestions')
            ->where('SQ.survey = :survey')
            ->setParameters(array(
                'survey' => $surveyId
            ))
            ->groupBy('F.id')
            ->getQuery();
        $result = $qb->getArrayResult();
        return $result;
    }

    public function getSequenceOrder()
    {
        $results = '';
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('max(factor.sequence) as sequence_order')
            ->from('SynapseSurveyBundle:Factor', 'factor')
            ->getQuery();
        $result = $qb->getArrayResult();
        if (!empty($result)) {
            $results = $result[0]['sequence_order'];
        }
        return (int)$results;
    }

    public function getFactorReport($totalStudents, $surveyId, $orgId, $personId, $factorId)
    {
        $em = $this->getEntityManager();
        try {
            $sql = "select f.id as factor_id,
			fl.name as factor_name,
			pfc.mean_value as response,
			count(distinct pfc.person_id) as responded,
			round((count(pfc.person_id) / $totalStudents * 100),2) as responded_percentage,
			round(avg( pfc.mean_value),2) as responded_mean,
			round(stddev_pop( pfc.mean_value),2) as responded_std,
			MIN(CASE WHEN (pfc.mean_value >= 1 AND pfc.mean_value < 3) THEN pfc.mean_value END) as red_minimum,
			MAX(CASE WHEN (pfc.mean_value >= 1 AND pfc.mean_value < 3) THEN pfc.mean_value END) as red_maximum,
			MIN(CASE WHEN (pfc.mean_value >= 3 AND pfc.mean_value < 6)  THEN pfc.mean_value END) as yellow_minimum,
			MAX(CASE WHEN (pfc.mean_value >= 3 AND pfc.mean_value < 6)  THEN pfc.mean_value END) as yellow_maximum,
			MIN(CASE WHEN (pfc.mean_value >= 6 AND pfc.mean_value < 7.02) THEN pfc.mean_value END) as green_minimum,
			MAX(CASE WHEN (pfc.mean_value >= 6 AND pfc.mean_value < 7.02) THEN pfc.mean_value END) as green_maximum,
			count(CASE WHEN (pfc.mean_value >= 1 AND pfc.mean_value < 3) THEN pfc.person_id END) as responded_red,
			count(CASE WHEN (pfc.mean_value >= 3 AND pfc.mean_value < 6) THEN pfc.person_id END) as responded_yellow,
			count(CASE WHEN (pfc.mean_value >= 6 AND pfc.mean_value < 7.02) THEN pfc.person_id END) as responded_green,
			count(CASE WHEN (pfc.mean_value >= 7.02) THEN pfc.person_id END) as responded_unknown,
			TRUNCATE(((count(CASE WHEN (pfc.mean_value >= 1 AND pfc.mean_value < 3) THEN pfc.person_id END) / count(pfc.person_id)) * 100),2) as responded_red_percentage,
			TRUNCATE(((count(CASE WHEN (pfc.mean_value >= 3 AND pfc.mean_value < 6) THEN pfc.person_id END) / count(pfc.person_id)) * 100),2) as responded_yellow_percentage,
			TRUNCATE(((count(CASE WHEN (pfc.mean_value >= 6 AND pfc.mean_value < 7.02) THEN pfc.person_id END) / count(pfc.person_id)) * 100),2) as responded_green_percentage,
			TRUNCATE(((count(CASE WHEN (pfc.mean_value >= 7.02) THEN pfc.person_id END) / count(pfc.person_id) / count(pfc.person_id)) * 100),2) as responded_unknown_percentage
			from factor f
			inner join factor_lang fl on fl.factor_id = f.id
			inner join person_factor_calculated pfc on pfc.factor_id = fl.factor_id
			where pfc.survey_id=$surveyId and

			pfc.modified_at = (select modified_at from person_factor_calculated pf
			where pf.organization_id = pfc.organization_id

			AND pf.person_id = pfc.person_id AND pf.survey_id = pfc.survey_id AND  pf.factor_id = pfc.factor_id ORDER BY pf.modified_at DESC LIMIT 1)
			and
			pfc.organization_id=$orgId and
			pfc.person_id in ( $personId ) and pfc.factor_id = $factorId
			group by factor_id
			order by factor_id";
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute();
        } catch (\Exception $e) {
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
        return $stmt->fetchAll();
    }

    /**
     * Gets the survey questions associated with the specified factor and survey IDs.
     *
     * @param int $factorId
     * @param int $surveyId
     * @return array
     */
    public function getFactorQuestions($factorId, $surveyId)
    {
        $parameters = [
            'surveyId' => $surveyId,
            'factorId' => $factorId
        ];

        $sql = "SELECT
					CONCAT(eql.question_text, ' ', eql.question_rpt) AS question,
					eql.ebi_question_id AS question_id
				FROM
					factor_questions fq
						INNER JOIN
					survey_questions sq ON fq.ebi_question_id = sq.ebi_question_id
						  AND sq.survey_id = :surveyId
						  AND sq.deleted_at IS NULL
						INNER JOIN
					ebi_questions_lang eql ON sq.ebi_question_id = eql.ebi_question_id
				WHERE
					fq.factor_id = :factorId
					AND fq.deleted_at IS NULL
					AND eql.deleted_at IS NULL
				ORDER BY question_id";

        $records = $this->executeQueryFetchAll($sql, $parameters);

        return $records;
    }


    /**
     * Gets the data for a drilldown from the Survey Factors Report, for the specified factor and factor value range.
     *
     * @param string $studentIds - String of student ids to which the report runner has access
     * @param string $limitOffset - Offset(first number) part of LIMIT section of SQL query
     * @param string $numberOfRecords - Number of records to be returned after the offset in the MySQL query
     * @param int $orgId - ID of the organization in which the logged in user resides
     * @param int $factorId - ID of the factor that the drilldown is being run on
     * @param int $surveyId - ID of the survey that the drilldown is being run on
     * @param string $minimumFactorValue - If the person's calculated factors are to be limited, this is the inclusive minimum that the query will use
     * @param string $maximumFactorValue - If the person's calculated factors are to be limited, this is the exclusive maximum that the query will use
     * @param string $outputType - Denotes whether or not the output type is a CSV or a drilldown
     * @param int $ebiMetadataIdOfClassLevel - ID of the metadata item for class level the drilldown is being run on
     * @param string $sortBy - determines the sort order of the query
     * @param int|null $orgAcademicYearId - organization academic year id
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getSurveyFactorReportDrilldownStudents($studentIds, $limitOffset, $numberOfRecords, $orgId, $factorId, $surveyId, $minimumFactorValue, $maximumFactorValue, $ebiMetadataIdOfClassLevel, $outputType = '', $sortBy = '', $orgAcademicYearId = null)
    {
        $em = $this->getEntityManager();
        $parameters = [];

        if (!empty($minimumFactorValue) && !empty($maximumFactorValue)) {
            $factorRangeString = " AND pfc.mean_value >= :minFactorValue AND pfc.mean_value < :maxFactorValue";
            $parameters['minFactorValue'] = $minimumFactorValue;
            $parameters['maxFactorValue'] = $maximumFactorValue;
        } else {
            $factorRangeString = "";
        }

        if (empty($outputType)) {
            $limitString = "LIMIT :limitOffset,:numberOfRecords ";
            $parameters['limitOffset'] = (int)$limitOffset;
            $parameters['numberOfRecords'] = (int)$numberOfRecords;
        } else {
            $limitString = "";
        }

        // This is done this tedious way to prevent SQL injection.  Unfortunately, "ORDER BY :sortBy" doesn't work.
        switch ($sortBy) {
            case 'student_last_name':
            case '+student_last_name':
                $orderBy = ' p.lastname ASC, p.firstname ASC, p.id DESC ';
                break;
            case '-student_last_name':
                $orderBy = ' p.lastname DESC, p.firstname ASC, p.id DESC ';
                break;
            case 'student_risk_status':
            case '+student_risk_status':
                //The + and - here sort the columns ascending / descending while placing nulls at the end of the list.
                //Since null is how gray risk values are stored in p.risk_level, this will accomplish always putting grays last. 
                $orderBy = ' +p.risk_level DESC, p.lastname ASC, p.firstname ASC, p.id DESC ';
                break;
            case '-student_risk_status':
                $orderBy = ' -p.risk_level DESC, p.lastname ASC, p.firstname ASC, p.id DESC ';
                break;
            case 'student_classlevel':
            case '+student_classlevel':
                $orderBy = ' class_level ASC, p.lastname ASC, p.firstname ASC, p.id DESC ';
                break;
            case '-student_classlevel':
                $orderBy = ' class_level DESC, p.lastname ASC, p.firstname ASC, p.id DESC ';
                break;
            case 'response':
            case '+response':
                $orderBy = ' pfc.mean_value ASC, p.lastname ASC, p.firstname ASC, p.id DESC ';
                break;
            case '-response':
                $orderBy = ' pfc.mean_value DESC, p.lastname ASC, p.firstname ASC, p.id DESC ';
                break;
            default:
                $orderBy = ' p.risk_level DESC, p.lastname ASC, p.firstname ASC, p.id DESC ';
                break;
        }

        if (!empty($orgAcademicYearId)) {
            $isActiveColumn = 'opsy.is_active, ';
            $joinWithOPSY = ' INNER JOIN org_person_student_year opsy ON opsy.person_id = p.id
                        AND opsy.org_academic_year_id = :currentAcademicYearId 
                        AND opsy.deleted_at IS NULL';
            $parameters['currentAcademicYearId'] = $orgAcademicYearId;
        } else {
            $isActiveColumn = '';
            $joinWithOPSY = '';
        }

        $sql = "
                SELECT
					p.id AS student,
					p.firstname,
					p.lastname,
					p.risk_level,
					$isActiveColumn
					rl.risk_text,
					rl.image_name AS risk_imagename,
					pfc.mean_value AS response,
					pem.metadata_value AS class_level
				FROM
					person p
					    $joinWithOPSY
					    INNER JOIN
					person_factor_calculated pfc ON p.id = pfc.person_id
						LEFT JOIN
					risk_level AS rl ON p.risk_level = rl.id AND rl.deleted_at IS NULL
						LEFT JOIN
					person_ebi_metadata AS pem ON pem.person_id = p.id AND pem.ebi_metadata_id = :ebiMetadataId AND pem.deleted_at IS NULL
				WHERE
                    p.organization_id = :orgId
                    AND pfc.survey_id= :surveyId
					AND p.id IN (:studentIds)
                    AND pfc.organization_id = :orgId
                    AND pfc.factor_id = :factorId
                    AND p.deleted_at IS NULL
                    AND pfc.deleted_at IS NULL
                    $factorRangeString
                GROUP BY p.id
                ORDER BY $orderBy
                $limitString ;
                ";


        $parameters['ebiMetadataId'] = $ebiMetadataIdOfClassLevel;
        $parameters['orgId'] = $orgId;
        $parameters['surveyId'] = $surveyId;
        $parameters['studentIds'] = explode(",", $studentIds);
        $parameters['factorId'] = $factorId;

        $parameterTypes =
            [
                'studentIds' => Connection::PARAM_INT_ARRAY,
                'numberOfRecords' => \PDO::PARAM_INT,
                'limitOffset' => \PDO::PARAM_INT
            ];

        try {
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $stmt->fetchAll();
    }

    /**
     * Gets the question ID, datablock ID, and factor ID that the faculty has access to based on either individual or aggregate permissions.
     *
     * @param int $organizationId
     * @param int $personId
     * @param int $surveyId
     * @param bool $aggregatePermissionsFlag - true for applying aggregate permissions, false for applying individual and aggregate permissions
     * @param int $factorId - (optional) allows a filter by a single factor ID.
     * @return array
     * @throws ValidationException
     */
    public function getDatablockIdsWithFactorIdsAccessibleToFaculty($organizationId, $personId, $surveyId, $aggregatePermissionsFlag, $factorId = null)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'surveyId' => $surveyId,
            'facultyId' => $personId
        ];

        if ($aggregatePermissionsFlag) {
            $aggregatePermissionsCondition = ' AND (op.accesslevel_ind_agg = 1 OR op.accesslevel_agg = 1) ';
        } else {
            $aggregatePermissionsCondition = ' AND op.accesslevel_ind_agg = 1 ';
        }

        if ($factorId) {
            $factorCondition = ' AND fq.factor_id = :factorId ';
            $parameters['factorId'] = $factorId;
        } else {
            $factorCondition = '';
        }

        $sql = "
                SELECT
                    fq.ebi_question_id,
                    dq.datablock_id,
                    fq.factor_id
                FROM
                    (
                        SELECT
                            ogf.org_permissionset_id AS permissionset_id
                        FROM
                            org_group_faculty ogf
                        WHERE
                            ogf.person_id = :facultyId
                            AND ogf.organization_id = :organizationId
                            AND ogf.deleted_at IS NULL

                        UNION

                        SELECT
                            ocf.org_permissionset_id AS permissionset_id
                        FROM
                            org_course_faculty ocf
                                JOIN
                            org_courses oc
                                    ON oc.id = ocf.org_courses_id
                                JOIN
                            org_academic_terms AS oat
                                    ON oat.id = oc.org_academic_terms_id
                                    AND DATE(NOW()) BETWEEN oat.start_date AND oat.end_date
                        WHERE
                            ocf.organization_id = :organizationId
                            AND ocf.person_id = :facultyId
                            AND ocf.deleted_at IS NULL
                            AND oc.deleted_at IS NULL
                            AND oat.deleted_at IS NULL
                    ) ofspm
                        JOIN
                    org_permissionset op ON op.id = ofspm.permissionset_id
                        JOIN
                    org_permissionset_datablock opd ON opd.org_permissionset_id = op.id
                        JOIN
                    datablock_questions dq ON opd.datablock_id = dq.datablock_id
                        JOIN
                    survey_questions sq ON dq.ebi_question_id = sq.ebi_question_id
                            AND (dq.survey_id = sq.survey_id OR dq.survey_id IS NULL)
                        JOIN
                    factor_questions fq ON fq.ebi_question_id = sq.ebi_question_id
                WHERE
                    sq.survey_id = :surveyId
                    AND op.organization_id = :organizationId
                    AND opd.block_type = 'survey'
                    AND dq.ebi_question_id IS NOT NULL
                    $aggregatePermissionsCondition
                    $factorCondition
                    AND fq.deleted_at IS NULL
                    AND sq.deleted_at IS NULL
                    AND dq.deleted_at IS NULL
                    AND opd.deleted_at IS NULL
                    AND op.deleted_at IS NULL;
            ";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $stmt->fetchAll();
    }

    public function listFactorsByPermission($ebiQuestion)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(fq.factor) as factor_id');
        $qb->from('SynapseSurveyBundle:FactorQuestions', 'fq');
        $qb->where('fq.ebiQuestion IN (:ebiQuestion)');
        $qb->setParameters(array(
            'ebiQuestion' => $ebiQuestion
        ));
        $qb->orderBy('fq.factor');
        $qb->groupBy('fq.factor');
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }


    /**
     * Returns the given student's mean_value for each factor on each of the given surveys,
     * along with the name of the factor.
     * Note: The person_factor_calculated table now has a unique index on (organization_id, person_id, survey_id, factor_id)
     * so there is no longer any need to query for the most recent value.
     *
     * @param int $studentId
     * @param array $surveyIds
     * @return array
     */
    public function getStudentFactorValues($studentId, $surveyIds)
    {
        $parameters = [
            'studentId' => $studentId,
            'surveyIds' => $surveyIds
        ];
        $parameterTypes = ['surveyIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    pfc.survey_id,
                    pfc.factor_id,
                    fl.name AS factor_text,
                    pfc.mean_value
                FROM
                    person_factor_calculated pfc
                        INNER JOIN
                    factor_lang fl
                        ON pfc.factor_id = fl.factor_id
                WHERE
                    pfc.person_id = :studentId
                    AND pfc.survey_id IN (:surveyIds)
                    AND pfc.deleted_at IS NULL
                    AND fl.deleted_at IS NULL;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();
        return $results;
    }
}