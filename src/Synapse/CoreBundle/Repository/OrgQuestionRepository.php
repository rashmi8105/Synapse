<?php
namespace Synapse\CoreBundle\Repository;

use Facile\DoctrineMySQLComeBack\Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Entity\OrgQuestion;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Constants\SearchConstant;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;

class OrgQuestionRepository extends SynapseRepository
{

	const REPOSITORY_KEY = 'SynapseCoreBundle:OrgQuestion';

	/**
	 * Override find() to use PHP Typing
	 *
	 * @param mixed $id
	 * @param null $lockMode
	 * @param null $lockVersion
	 * @return null|OrgQuestion
	 */
	public function find($id, $lockMode = null, $lockVersion = null)
	{
		return parent::find($id, $lockMode, $lockVersion);
	}


	/**
	 * Overide FindOneBy to Use PHP Typing
	 *
	 * @param array $criteria
	 * @param array|null $orderBy
	 * @param int|null $limit
	 * @param int|null $offset
	 *
	 * @return OrgQuestion[] The objects.
	 */
	public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
	{
		return parent::findBy($criteria, $orderBy, $limit, $offset);
	}


	/**
	 * Override FindOneBy to Use PHP Typing
	 *
	 * @param array $criteria
	 * @param array|null $orderBy
	 *
	 * @return OrgQuestion|null The entity instance or NULL if the entity can not be found.
	 */
	public function findOneBy(array $criteria, array $orderBy = null)
	{
		return parent::findOneBy($criteria, $orderBy);
	}


	public function getOrgQuestion($organizationid)
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('om.id', 'om.modifiedAt as modified_at', 'om.questionKey as question_label')
            ->
        from('SynapseCoreBundle:OrgQuestion', 'om')
            ->where('om.organization = :organizationid ')
            ->setParameters(array(
            'organizationid' => $organizationid
        ))
            ->getQuery();
        return $qb->getResult();
    }
	
	public function getDrilldownRespondedStudents($personId, $orgQuestionId, $responseValue, $field, $optionId)
	{			
		$em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(orgResp.person) as person_id', $field.' as response');
        $qb->from('SynapseCoreBundle:OrgQuestionResponse', 'orgResp');
        $qb->where('orgResp.person IN (:personId)');
        $qb->andWhere('orgResp.orgQuestion =:orgQuestionId');
		if(!empty($responseValue))
		{
			$qb->andWhere('orgResp.decimalValue IN (:responseValue)');
			$qb->setParameters(array(
				'personId' => $personId,
				'orgQuestionId' => $orgQuestionId,
				'responseValue' => $responseValue
			));		
		} else if (!empty($optionId)) {
			$qb->andWhere('orgResp.orgQuestionOptions IN (:optionId)');
			$qb->andWhere('orgResp.decimalValue = :decimal');
			$qb->setParameters(array(
				'personId' => $personId,
				'orgQuestionId' => $orgQuestionId,
				'optionId' => $optionId,
				'decimal' => 1
			));
		} else {
			$qb->setParameters(array(
				'personId' => $personId,
				'orgQuestionId' => $orgQuestionId				
			));	
		}
        $query = $qb->getQuery();		
        $resultSet = $query->getArrayResult();				
		return $resultSet;
	}

	/**
	 * List students in drilldown with the stated response and their intent to leave and risk. This differs from the listDrilldownStudents function because this function looks at responses with question option values (type MR, D, Q, etc.)
	 *
	 * @param int $studentId
	 * @param int $startPoint
	 * @param int $limit
	 * @param int $sortBy
	 * @param int $orgQuestionId
	 * @param int $field
	 * @param int $orgId
	 * @param int $surveyId
	 * @param int ebiMetadataId
	 * @param string $viewmode
	 * @return array
	 */
	public function listDrilldownStudentsByCategory($studentId, $startPoint, $limit, $sortBy, $orgQuestionId, $field, $orgId, $surveyId, $ebiMetadataId, $viewmode='')
	{

		if(!empty($orgQuestionId)){
			$parameters['orgQuestionId'] = $orgQuestionId;
		} else {
			return array();
		}

		if(!empty($ebiMetadataId)){
			$parameters['ebiMetadataId'] = $ebiMetadataId;
		} else {
			return array();
		}

		if(!empty($studentId)){
			if(!is_array($studentId)){
				$studentId = explode(',', $studentId);
			}
			$parameters['studentIds'] = $studentId;
			$parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];
		} else {
			return array();
		}

		if(!empty($orgId)){
			$parameters['orgId'] = $orgId;
		} else {
			return array();
		}

		if(!empty($surveyId)){
			$parameters['surveyId'] = $surveyId;
		} else {
			return array();
		}

		if($field == 'orgResp.decimal_value'){
			$decimalValueExcludeCondition = " and orgResp.decimal_value != 99";
		} else {
			$decimalValueExcludeCondition = "";
		}

		if(empty($viewmode) && $viewmode != 'student-list') {
			$parameters['limit'] = (int) $limit;
			$parameters['startPoint'] = (int) $startPoint;
			$limitStatement = " LIMIT :startPoint, :limit";
			$parameterTypes['limit'] = \PDO::PARAM_INT;
			$parameterTypes['startPoint'] = \PDO::PARAM_INT;
		} else {
			$limitStatement = "";
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS
				P.id AS student,
				P.firstname,
				P.lastname,
				P.risk_level,
				RL.risk_text,
				RL.image_name AS risk_imagename,
				IL.text AS intent_to_leave_text,
				IL.image_name AS intent_imagename,
				QO.option_name AS response,
				pem.metadata_value AS class_level
			FROM
				person AS P
					JOIN
				org_question_response orgResp ON P.id = orgResp.person_id
						AND orgResp.deleted_at IS NULL
						AND orgResp.org_question_id = :orgQuestionId
						AND orgResp.org_id = :orgId
						AND orgResp.survey_id = :surveyId
					LEFT JOIN
				risk_level AS RL ON P.risk_level = RL.id AND RL.deleted_at IS NULL
					LEFT JOIN
				intent_to_leave AS IL ON P.intent_to_leave = IL.id AND IL.deleted_at IS NULL
					LEFT JOIN
				person_ebi_metadata as pem ON pem.person_id = P.id
						AND pem.ebi_metadata_id = :ebiMetadataId
						AND pem.deleted_at IS NULL
					JOIN
				org_question OQ ON orgResp.org_question_id = OQ.id
					JOIN
				org_question_options QO ON QO.org_question_id = OQ.id
						AND $field = QO.option_value
						AND QO.deleted_at IS NULL
			WHERE
			    P.deleted_at IS NULL
				AND P.id IN (:studentIds)
				$decimalValueExcludeCondition
				GROUP BY P.id ORDER BY $sortBy
				$limitStatement
				";

		try {
			$em = $this->getEntityManager();
			$stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

		} catch (\Exception $e) {
			throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
		}

		$records = $stmt->fetchAll();

		return $records;
	}

	/**
	 * List students in drilldown with the stated response and their intent to leave and risk. This differs from the listDrilldownStudentsByCategory function because this function looks at responses without question option values (type LA, SA, etc.)
	 * @param $studentId
	 * @param $startPoint
	 * @param $limit
	 * @param $sortBy
	 * @param $orgQuestionId
	 * @param $field
	 * @param $orgId
	 * @param $surveyId
	 * @param $ebiMetadataId
	 * @param string $viewmode
	 * @return array
	 */
	public function listDrilldownStudents($studentId, $startPoint, $limit, $sortBy, $orgQuestionId, $field, $orgId, $surveyId, $ebiMetadataId, $viewmode='')
	{

        if(!empty($orgQuestionId)){
            $parameters['orgQuestionId'] = $orgQuestionId;
        } else {
            return array();
        }

        if(!empty($studentId)){
            if(!is_array($studentId)){
                $studentId = explode(',', $studentId);
            }
            $parameters['studentIds'] = $studentId;
            $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];
        } else {
            return array();
        }

		if(!empty($ebiMetadataId)){
			$parameters['ebiMetadataId'] = $ebiMetadataId;
		} else {
			return array();
		}

        if(!empty($orgId)){
            $parameters['orgId'] = $orgId;
        } else {
            return array();
        }

        if(!empty($surveyId)){
            $parameters['surveyId'] = $surveyId;
        } else {
            return array();
        }

		if(empty($viewmode) && $viewmode != 'student-list') {
			$parameters['limit'] = (int) $limit;
			$parameters['startPoint'] = (int) $startPoint;
			$limitStatement = " LIMIT :startPoint, :limit";
			$parameterTypes['limit'] = \PDO::PARAM_INT;
			$parameterTypes['startPoint'] = \PDO::PARAM_INT;
		} else {
			$limitStatement = "";
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS
				P.id,
				P.id AS student,
				P.firstname,
				P.lastname,
				P.risk_level,
				RL.risk_text,
				RL.image_name AS risk_imagename,
				IL.text AS intent_to_leave_text,
				IL.image_name AS intent_imagename,
				$field AS response,
				pem.metadata_value AS class_level
			FROM
				person AS P
					JOIN
				org_question_response AS orgResp ON P.id = orgResp.person_id
						AND orgResp.deleted_at IS NULL
						AND orgResp.org_question_id = :orgQuestionId
						AND orgResp.org_id = :orgId
						AND orgResp.survey_id = :surveyId
					LEFT JOIN
				risk_level AS RL ON P.risk_level = RL.id AND RL.deleted_at IS NULL
					LEFT JOIN
				intent_to_leave AS IL ON P.intent_to_leave = IL.id AND IL.deleted_at IS NULL
					LEFT JOIN
				person_ebi_metadata AS pem ON pem.person_id = P.id
						AND pem.ebi_metadata_id = :ebiMetadataId
					  	AND pem.deleted_at IS NULL
			WHERE
			    P.deleted_at IS NULL
				AND P.id IN (:studentIds)
			GROUP BY P.id ORDER BY $sortBy
			$limitStatement";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $stmt->fetchAll();
	}

	/**
	 * List drilldown of ISQs of MR type for each student.
	 *
	 * @param string $studentIds
	 * @param int $startPoint
	 * @param int $limit
	 * @param string $sortBy
	 * @param int $orgQuestionId
	 * @param int $optionId
	 * @param int $orgId
	 * @param int $surveyId
	 * @param int $ebiMetadataId
	 * @param string $viewmode
	 * @return array
	 */
	public function listOrgMRStudentList($studentIds, $startPoint, $limit, $sortBy, $orgQuestionId, $optionId, $orgId, $surveyId, $ebiMetadataId, $viewmode='')
	{		
		$response = ($optionId) ? 'QO.option_name as response' : 'GROUP_CONCAT(QO.option_name SEPARATOR \', \') as response';

		if(!empty($studentIds)){
			$parameters['studentIds'] = explode(",",$studentIds);
			$parameterTypes['studentIds'] = Connection::PARAM_INT_ARRAY;
		}else{
			return array();
		}

		if(!empty($orgQuestionId)){
			$parameters['orgQuestionId'] = $orgQuestionId;
		}else{
			return array();
		}

		if(!empty($orgId)){
			$parameters['orgId'] = $orgId;
		}else{
			return array();
		}

		if(!empty($ebiMetadataId)){
			$parameters['ebiMetadataId'] = $ebiMetadataId;
		}else{
			return array();
		}

		if(!empty($surveyId)){
			$parameters['surveyId'] = $surveyId;
		}else{
			return array();
		}

		if(!empty($optionId)) {
			$optionIdCondition = " and orgResp.org_question_options_id = :optionId ";
			$parameters['optionId'] = $optionId;
		}else{
			$optionIdCondition = "";
		}

		if(empty($viewmode) && $viewmode != 'student-list') {
			$parameters['limit'] = (int) $limit;
			$parameters['startPoint'] = (int) $startPoint;
			$limitStatement = " LIMIT :startPoint, :limit";
			$parameterTypes['limit'] = \PDO::PARAM_INT;
			$parameterTypes['startPoint'] = \PDO::PARAM_INT;
		} else {
			$limitStatement = "";
		}


		$sql = "SELECT SQL_CALC_FOUND_ROWS
				P.id AS student,
				P.firstname,
				P.lastname,
				P.risk_level,
				RL.image_name AS risk_imagename,
				IL.text AS intent_to_leave_text,
				IL.image_name AS intent_imagename,
				$response,
				pem.metadata_value AS class_level
			FROM
				person AS P
					JOIN
				org_question_response orgResp ON P.id = orgResp.person_id
						AND orgResp.org_question_id = :orgQuestionId
						AND orgResp.org_id = :orgId
						AND orgResp.survey_id = :surveyId
						AND orgResp.decimal_value=1
						$optionIdCondition
					LEFT JOIN
				risk_level AS RL ON P.risk_level = RL.id AND RL.deleted_at IS NULL
					LEFT JOIN
				intent_to_leave AS IL ON P.intent_to_leave = IL.id
					LEFT JOIN
				person_ebi_metadata AS pem ON pem.person_id = P.id
						AND pem.ebi_metadata_id = :ebiMetadataId
						AND pem.deleted_at IS NULL
					JOIN
				org_question_options QO ON (QO.org_question_id = orgResp.org_question_id AND QO.id = orgResp.org_question_options_id)
			WHERE
				P.id IN ( :studentIds )
				AND P.deleted_at IS NULL
			GROUP BY P.id ORDER BY $sortBy
			$limitStatement";

		try{
			$em = $this->getEntityManager();
			$stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
	    } catch (\Exception $e) {
			throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
		}
        return $stmt->fetchAll();
	}	
    
    /**
     * Return org_question_response for a student in the particular survey.
     *
     * @param int $surveyId
     * @param int $personId
     * @param int $orgId
     * @param int $academicYearId
     * @return array
     */
    public function getOrgQuestionSurveyResponse($surveyId, $personId, $orgId, $academicYearId, $cohortId)
    {           
        $sql = "select 
                    qr.org_question_id as org_question_id,
                    qr.response_type as response_type,
                    qr.decimal_value as decimal_value,
                    qr.char_value as char_value,
                    qr.charmax_value as char_max_value,
                    oq.question_type_id as question_type,                    
                    group_concat(oqo.sequence SEPARATOR ', ') as sequence
                from
                    org_question_response qr
                        left join
                    org_question oq ON qr.org_question_id = oq.id
                        left join
                    org_question_options oqo ON qr.org_question_options_id = oqo.id
                        join 
                    survey_questions sq on sq.org_question_id = qr.org_question_id                    
                where
                    qr.person_id = ?
                        and qr.org_id = ?
                        and qr.survey_id = ?
                        and qr.org_academic_year_id = ?
                        and sq.cohort_code = ?
                group by qr.org_question_id
                order by org_question_id";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $parameters = array($personId, $orgId, $surveyId, $academicYearId, $cohortId);
            $stmt->execute($parameters);     
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }             
        return $stmt->fetchAll();
    }

    /**
     * Gets ISQ data about each survey cohort combination with responses for the organization.
     *
     * @param int $organizationId
     * @param int $userId
     * @param int|null $orgAcademicYearId
     * @param array|null $surveyStatus - array containing any combination of the following: "launched", "closed", "open", "ready"
	 * @param string|null $excludeQuestionType
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getISQcohortsAndSurveysWithRespectToYearsForOrganization($organizationId, $userId, $orgAcademicYearId = null, $surveyStatus = null, $excludeQuestionType = '')
    {
        $parameters = [
			'organizationId' => $organizationId,
            'facultyId' => $userId
        ];

        $parameterTypes = [];

        if ($orgAcademicYearId) {
            $yearString = 'AND oay.id = :orgAcademicYearId';
            $parameters['orgAcademicYearId'] = $orgAcademicYearId;
        } else {
            $yearString = '';
        }

        if ($surveyStatus) {
            $statusString = 'AND wl.status IN (:surveyStatus)';
            $parameters['surveyStatus'] = $surveyStatus;
            $parameterTypes['surveyStatus'] = Connection::PARAM_STR_ARRAY;
        } else {
            $statusString = '';
        }

		if (!empty($excludeQuestionType)) {
			$excludeQuestionTypeStringWhereCondition = "AND sq.type NOT IN (:excludeQuestionType)";
			$parameters['excludeQuestionType'] = $excludeQuestionType;
			$parameterTypes['excludeQuestionType'] = Connection::PARAM_STR_ARRAY;
		} else {
			$excludeQuestionTypeStringWhereCondition = '';
		}

        $sql = "
			SELECT
				oay.year_id AS year_id,
				oay.id AS org_academic_year_id,
				oay.name AS year_name,
				wl.cohort_code AS cohort,
				ocn.cohort_name,
				s.id AS survey_id,
				sl.name AS survey_name,
				wl.status,
				wl.open_date,
				wl.close_date,
				questions.type AS question_type,
				questions.org_question_id AS question_id,
				oq.question_text
			FROM
				survey s
					INNER JOIN
				survey_lang sl ON sl.survey_id = s.id
					INNER JOIN
				org_academic_year oay ON oay.year_id = s.year_id
					INNER JOIN
				wess_link wl ON oay.organization_id = wl.org_id AND wl.survey_id = s.id
					INNER JOIN
				org_cohort_name ocn ON ocn.cohort = wl.cohort_code
					AND ocn.org_academic_year_id = oay.id
					INNER JOIN
				(
					SELECT
						DISTINCT sq.org_question_id,
						opsc.cohort,
						opsc.org_academic_year_id,
						oqr.survey_id,
						ofspm.permissionset_id,
						ofspm.org_id,
						sq.type,
						sq.sequence
					FROM
						org_question_response oqr
							INNER JOIN
						org_faculty_student_permission_map ofspm ON ofspm.faculty_id = :facultyId
							AND ofspm.student_id = oqr.person_id
							INNER JOIN
						survey_questions sq ON sq.org_question_id = oqr.org_question_id AND sq.survey_id = oqr.survey_id
							INNER JOIN
						org_person_student_cohort opsc ON opsc.person_id = oqr.person_id
					WHERE
						oqr.deleted_at IS NULL
						AND opsc.deleted_at IS NULL
						AND sq.deleted_at IS  NULL
						$excludeQuestionTypeStringWhereCondition
				) AS questions ON questions.survey_id = s.id AND questions.cohort = wl.cohort_code AND questions.org_academic_year_id = oay.id
					INNER JOIN
				org_permissionset op ON op.id = questions.permissionset_id
					LEFT JOIN
				org_permissionset_question opq ON opq.org_question_id = questions.org_question_id
					AND questions.permissionset_id = opq.org_permissionset_id
					AND questions.org_id = opq.organization_id
					AND opq.deleted_at IS NULL
					INNER JOIN
				org_question oq ON oq.id = questions.org_question_id
			WHERE
				wl.org_id = :organizationId
				AND (op.current_future_isq = 1 OR opq.id IS NOT NULL)
				$statusString
				$yearString
				AND oay.deleted_at IS NULL
				AND ocn.deleted_at IS NULL
				AND wl.deleted_at IS NULL
				AND op.deleted_at IS NULL
				AND oq.deleted_at IS NULL
			ORDER BY wl.year_id, ocn.cohort, wl.survey_id, wl.id DESC, questions.sequence ASC";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $records = $stmt->fetchAll();
        return $records;
    }

    /**
     * Gets survey data about each cohort-survey combination with survey responses for the organization.
     *
     * @param int $organizationId
     * @param int $userId
     * @param int|null $orgAcademicYearId
     * @param array|null $surveyStatus - array containing any combination of the following: "launched", "closed", "open", "ready"
     * @param array|null $excludeQuestionType
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getSurveysAndCohortsWithRespectToYearsForOrganization($organizationId, $userId, $orgAcademicYearId = null, $surveyStatus = null, $excludeQuestionType = null)
    {
        $parameters = [
			'organizationId' => $organizationId,
            'facultyId' => $userId
        ];
        $parameterTypes = [];

        if ($orgAcademicYearId) {
            $yearString = 'AND oay.id = :orgAcademicYearId';
            $parameters['orgAcademicYearId'] = $orgAcademicYearId;
        } else {
            $yearString = '';
        }

        if ($surveyStatus) {
            $statusString = 'AND wl.status IN (:surveyStatus)';
            $parameters['surveyStatus'] = $surveyStatus;
            $parameterTypes['surveyStatus'] = Connection::PARAM_STR_ARRAY;
        } else {
            $statusString = '';
        }

		if (!empty($excludeQuestionType)) {
			$excludeQuestionTypeStringWhereCondition = "AND sq.type NOT IN (:excludeQuestionType)";
			$parameters['excludeQuestionType'] = $excludeQuestionType;
			$parameterTypes['excludeQuestionType'] = Connection::PARAM_STR_ARRAY;
		} else {
			$excludeQuestionTypeStringWhereCondition = '';
		}

        $sql = "
			SELECT DISTINCT
				oay.year_id AS year_id,
				oay.id AS org_academic_year_id,
				oay.name AS year_name,
				wl.cohort_code AS cohort,
				ocn.cohort_name,
				s.id AS survey_id,
				sl.name AS survey_name,
				wl.status,
				wl.open_date,
				wl.close_date,
				sq.type AS question_type,
				sq.ebi_question_id AS question_id,
				 IF(eql.question_text > '',
                    CONCAT(eql.question_text, ' ', eql.question_rpt),
                    eql.question_rpt) AS question_text
			FROM
				survey s
					INNER JOIN
				survey_lang sl ON sl.survey_id = s.id
					INNER JOIN
				org_academic_year oay ON oay.year_id = s.year_id
					INNER JOIN
				wess_link wl ON oay.organization_id = wl.org_id 
				    AND wl.survey_id = s.id
					INNER JOIN
				org_cohort_name ocn ON ocn.cohort = wl.cohort_code
					AND ocn.org_academic_year_id = oay.id
					INNER JOIN
				survey_questions sq ON s.id = sq.survey_id 
					$excludeQuestionTypeStringWhereCondition
					AND sq.deleted_at IS NULL
					INNER JOIN
				datablock_questions dq ON dq.ebi_question_id = sq.ebi_question_id
					INNER JOIN
				org_permissionset_datablock opd ON opd.organization_id = :organizationId
					AND dq.datablock_id = opd.datablock_id
					INNER JOIN
				ebi_questions_lang eql ON eql.ebi_question_id = sq.ebi_question_id
					INNER JOIN
					(SELECT DISTINCT
                        sr.survey_questions_id,
                        opsc.cohort,
                        opsc.org_academic_year_id,
                        sr.survey_id,
                        ofspm.permissionset_id
                    FROM
                        survey_response sr
                        INNER JOIN
                        (SELECT DISTINCT
                            student_id,
                            permissionset_id
                        FROM 
                            org_faculty_student_permission_map
                        WHERE
                            faculty_id = :facultyId) ofspm ON ofspm.student_id = sr.person_id
                        INNER JOIN
                        org_person_student_cohort opsc ON opsc.person_id = sr.person_id
				            AND opsc.organization_id = :organizationId
				    WHERE
				        sr.org_id = :organizationId
                        AND sr.deleted_at IS NULL
                        AND opsc.deleted_at IS NULL) AS squ ON squ.survey_id = s.id
                    AND squ.cohort = wl.cohort_code
                    AND squ.org_academic_year_id = oay.id
                    AND squ.survey_questions_id = sq.id
                    AND squ.permissionset_id = opd.org_permissionset_id
			WHERE
				wl.org_id = :organizationId
				$statusString
				$yearString
				AND oay.deleted_at IS NULL
				AND ocn.deleted_at IS NULL
				AND wl.deleted_at IS NULL
				AND dq.deleted_at IS NULL
				AND opd.deleted_at IS NULL
				AND eql.deleted_at IS NULL
			ORDER BY wl.year_id, ocn.cohort, wl.survey_id, wl.id DESC, CAST(sq.qnbr AS UNSIGNED) ASC";

        $records = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $records;
    }

}