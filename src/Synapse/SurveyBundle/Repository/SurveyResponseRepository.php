<?php
namespace Synapse\SurveyBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\SearchConstant;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SurveyBundle\SurveyBundleConstant;


class SurveyResponseRepository extends SynapseRepository
{

	const REPOSITORY_KEY = 'SynapseSurveyBundle:SurveyResponse';
    
    /**
     * This function will return the students response for the given survey based on the academic year      
     *
     * @param int $surveyId
     * @param int $personId
     * @param int $orgId
     * @param int $academicYearId
     * @return array
     */
    public function getSurveyResponse($surveyId, $personId, $orgId, $academicYearId = null)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('sr.id,IDENTITY(sr.surveyQuestions) as survey_question_id ,
            sr.responseType,sr.decimalValue,sr.charValue,sr.charmaxValue,IDENTITY(sq.ebiQuestion) as ebi_question_id,IDENTITY(sq.indQuestion) as  ind_question_id,sq.type', 'sq.qnbr as survey_ques_no');
        $qb->from('SynapseSurveyBundle:SurveyResponse', 'sr');
        $qb->LEFTJoin('SynapseSurveyBundle:SurveyQuestions', 'sq', \Doctrine\ORM\Query\Expr\Join::WITH, 'sr.surveyQuestions = sq.id');
        $qb->where('sr.person = :person');
        $qb->andWhere('sr.organization = :orgId');
        $qb->andWhere('sr.survey = :survey');
        /* 
         * If academic year id is passed then this has to be included 
         * in the condition check.
         */
        if(empty($academicYearId))
        {
            $qb->setParameters(array(
                'person' => $personId,
                'orgId' => $orgId,
                    'survey' => $surveyId                
            ));
        } else {
            $qb->andWhere('sr.orgAcademicYear = :yearId');
            $qb->setParameters(array(
                'person' => $personId,
                'orgId' => $orgId,
                    'survey' => $surveyId,
                    'yearId' => $academicYearId
            ));
        }
		$qb->orderBy('sq.qnbr+0', 'ASC');
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }


    /**
     * Returns the given student's responses to each decimal-type question on each of the given surveys,
     * along with the text of the question and response.
     *
     * @param int $studentId
     * @param array $surveyIds
     * @return array
     */
    public function getSurveyDecimalResponses($studentId, $surveyIds)
    {
        $parameters = [
            'studentId' => $studentId,
            'surveyIds' => $surveyIds
        ];
        $parameterTypes = ['surveyIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    sq.survey_id,
                    sq.id AS survey_question_id,
                    sq.ebi_question_id,
                    sq.qnbr,
                    IF(eql.question_text > '', CONCAT(eql.question_text, ' ', eql.question_rpt), eql.question_rpt) AS question_text,
                    sr.decimal_value,
                    eqo.extended_option_text AS option_text
                FROM
                    survey_questions sq
                        INNER JOIN
                    survey_response sr
                            ON sr.survey_questions_id = sq.id
                        INNER JOIN
                    ebi_questions_lang eql
                            ON eql.ebi_question_id = sq.ebi_question_id
                        LEFT JOIN
                    ebi_question_options eqo
                            ON eqo.ebi_question_id = sq.ebi_question_id
                            AND eqo.option_value = sr.decimal_value
                WHERE
                  	sr.response_type = 'decimal'
                    AND sr.person_id = :studentId
                    AND sr.survey_id IN (:surveyIds)
                    AND sq.deleted_at IS NULL
                    AND sr.deleted_at IS NULL
                    AND eql.deleted_at IS NULL
                    AND eqo.deleted_at IS NULL;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();
        return $results;
    }

	/**
	 * get scaled type questions for the given survey based on the surveyQuestionsId
	 *
	 * @param int $surveyId
	 * @param int $organizationId
	 * @param array $personIds
	 * @param int $surveyQuestionsId
	 * @throws SynapseDatabaseException
	 * @return array
	 */
    public function getScaledTypeQuestions($surveyId, $organizationId, $personIds, $surveyQuestionsId)
	{
		$parameters = [
			'surveyId' => $surveyId,
			'personIds' => $personIds,
			'organizationId' => $organizationId,
			'surveyQuestionsId' => $surveyQuestionsId,
			'decimalValue' => SurveyBundleConstant::NO_RESPONSE_FOR_QUESTION
		];

		$parameterTypes = ['personIds' => Connection::PARAM_INT_ARRAY];

		$sql = "SELECT
					survey_questions_id,
					COUNT(DISTINCT (sr.person_id)) AS student_count,
					sq.qnbr AS question_number,
					sr.survey_id,
					sr.org_id,
					response_type,
					decimal_value,
					char_value,
					charmax_value,
					eqo.option_text,
					eqo.option_value,
					eql.question_rpt
				FROM
					survey_response sr
						INNER JOIN
					survey_questions sq ON sr.survey_questions_id = sq.id
						INNER JOIN
					ebi_question eq ON sq.ebi_question_id = eq.id
						INNER JOIN
					ebi_questions_lang eql ON eq.id = eql.ebi_question_id
						INNER JOIN
					ebi_question_options eqo ON eqo.ebi_question_id = eq.id
						AND sr.decimal_value = eqo.option_value
				WHERE
					sr.survey_id = :surveyId
						AND sr.deleted_at IS NULL
						AND eq.deleted_at IS NULL
						AND sq.deleted_at IS NULL
						AND eql.deleted_at IS NULL
						AND eqo.deleted_at IS NULL
						AND sr.org_id = :organizationId
						AND sr.survey_questions_id = :surveyQuestionsId
						AND eqo.option_value != :decimalValue
						AND sr.person_id IN (:personIds)
				GROUP BY survey_questions_id , decimal_value
				ORDER BY survey_questions_id , eqo.sequence";
		$result = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
		return $result;
	}


    /**
     * Returns a list of the scaled and categorical questions on the given survey,
     * along with overall statistics for each question about the responses from the given students.
     *
     * @param int $surveyId
     * @param int $organizationId
     * @param array $studentIds
     * @param array $ebiQuestionIds -- only these questions can be included
	 * @throws SynapseDatabaseException
     * @return array
     */
    public function getScaledCategorySurveyQuestions($surveyId, $organizationId, $studentIds, $ebiQuestionIds)
    {
        $parameters = [
            'surveyId' => $surveyId,
            'organizationId' => $organizationId,
            'studentIds' => $studentIds,
            'ebiQuestionIds' => $ebiQuestionIds,
			'decimalValue' => SurveyBundleConstant::NO_RESPONSE_FOR_QUESTION
        ];

        $parameterTypes = [
            'studentIds' => Connection::PARAM_INT_ARRAY,
            'ebiQuestionIds' => Connection::PARAM_INT_ARRAY
        ];

        $sql = "SELECT
					sq.ebi_question_id,
					sr.survey_questions_id,
					sq.qnbr,
					IF(eql.question_text > '',
						CONCAT(eql.question_text, ' ', eql.question_rpt),
						eql.question_rpt) AS question_text,
					eq.question_type_id AS question_type,
					COUNT(DISTINCT (sr.person_id)) AS student_count,
					ROUND(STD(sr.decimal_value), 2) AS standard_deviation,
					ROUND(AVG(sr.decimal_value), 2) AS mean
				FROM
					survey_questions sq
						INNER JOIN
					ebi_question eq ON sq.ebi_question_id = eq.id
						INNER JOIN
					ebi_questions_lang eql ON eq.id = eql.ebi_question_id
						INNER JOIN
					survey_response sr ON sr.survey_questions_id = sq.id
						AND sr.survey_id = sq.survey_id
				WHERE
					sq.survey_id = :surveyId
						AND sr.org_id = :organizationId
						AND sr.person_id IN (:studentIds)
						AND eq.question_type_id IN ('Q' , 'D')
						AND sr.decimal_value != :decimalValue
						AND eq.id IN (:ebiQuestionIds)
						AND sq.deleted_at IS NULL
						AND eq.deleted_at IS NULL
						AND eql.deleted_at IS NULL
						AND sr.deleted_at IS NULL
				GROUP BY qnbr
				ORDER BY qnbr;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();

        return $results;
    }


    /**
     * Returns a list of the free-response questions of the given type on the given survey,
     * along with a list of responses from the given students for each question,
     * and a count of the number of those students who gave each response.
     *
     * @param int $surveyId
     * @param int $organizationId
     * @param array $studentIds
     * @param array $ebiQuestionIds -- only these questions can be included
     * @param string $questionType -- "SA" (short answer) or "LA" (long answer)
     * @param string $responseField -- "char_value" or "charmax_value"
	 * @throws SynapseDatabaseException
     * @return array
     */
    public function getDescriptiveQuestions($surveyId, $organizationId, $studentIds, $ebiQuestionIds, $questionType, $responseField)
    {
        $parameters = [
            'surveyId' => $surveyId,
            'organizationId' => $organizationId,
            'studentIds' => $studentIds,
            'ebiQuestionIds' => $ebiQuestionIds,
            'questionType' => $questionType
        ];

        $parameterTypes = [
            'studentIds' => Connection::PARAM_INT_ARRAY,
            'ebiQuestionIds' => Connection::PARAM_INT_ARRAY
        ];

        $sql = "SELECT
					sq.ebi_question_id,
					sr.survey_questions_id,
					sq.qnbr,
					IF(eql.question_text > '',
						CONCAT(eql.question_text, ' ', eql.question_rpt),
						eql.question_rpt) AS question_text,
					eq.question_type_id AS question_type,
					COUNT(sr.person_id) AS student_count,
					sr.response_type,
					sr.$responseField
				FROM
					survey_questions sq
						INNER JOIN
					ebi_question eq ON sq.ebi_question_id = eq.id
						INNER JOIN
					ebi_questions_lang eql ON eq.id = eql.ebi_question_id
						INNER JOIN
					survey_response sr ON sr.survey_questions_id = sq.id
						AND sr.survey_id = sq.survey_id
				WHERE
					sr.survey_id = :surveyId
					 	AND sr.org_id = :organizationId
						AND sr.person_id IN (:studentIds)
						AND eq.question_type_id = :questionType
						AND eq.id IN (:ebiQuestionIds)
						AND sq.deleted_at IS NULL
						AND eq.deleted_at IS NULL
						AND eql.deleted_at IS NULL
						AND sr.deleted_at IS NULL
				GROUP BY qnbr , sr.$responseField
				ORDER BY qnbr;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();

        return $results;
    }


    /**
     * Returns a list of the numeric questions on the given survey,
     * along with a count of the responses from the given students for each question.
     * (Note: There are currently no numeric ebi questions, so this function returns an empty array.)
     *
     * @param int $surveyId
     * @param int $organizationId
     * @param array $studentIds
     * @param array $ebiQuestionIds -- only these questions can be included
	 * @throws SynapseDatabaseException
     * @return array
     */
    public function getNumericQuestions($surveyId, $organizationId, $studentIds, $ebiQuestionIds)
    {
        $parameters = [
            'surveyId' => $surveyId,
            'organizationId' => $organizationId,
            'studentIds' => $studentIds,
            'ebiQuestionIds' => $ebiQuestionIds
        ];

        $parameterTypes = [
            'studentIds' => Connection::PARAM_INT_ARRAY,
            'ebiQuestionIds' => Connection::PARAM_INT_ARRAY
        ];

        $sql = "SELECT
					sq.ebi_question_id,
					sr.survey_questions_id,
					sq.qnbr,
					IF(eql.question_text > '',
						CONCAT(eql.question_text, ' ', eql.question_rpt),
						eql.question_rpt) AS question_text,
					eq.question_type_id AS question_type,
					COUNT(sr.person_id) AS student_count
				FROM
					survey_questions sq
						INNER JOIN
					ebi_question eq ON sq.ebi_question_id = eq.id
						INNER JOIN
					ebi_questions_lang eql ON eq.id = eql.ebi_question_id
						INNER JOIN
					survey_response sr ON sr.survey_questions_id = sq.id
						AND sr.survey_id = sq.survey_id
				WHERE
					sr.survey_id = :surveyId
						AND sr.org_id = :organizationId
						AND sr.person_id IN (:studentIds)
						AND eq.question_type_id = 'NA'
						AND eq.id IN (:ebiQuestionIds)
						AND sq.deleted_at IS NULL
						AND eq.deleted_at IS NULL
						AND eql.deleted_at IS NULL
						AND sr.deleted_at IS NULL
				GROUP BY qnbr
				ORDER BY qnbr;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();

        return $results;
    }

	/**
	 * get response for numeric questions based on survey id and personId
	 *
	 * @param int $surveyId
	 * @param int $organizationId
	 * @param array $personIds
	 * @param int $surveyQuestionsId
	 * @throws SynapseDatabaseException
	 * @return array
	 */
	public function getNumericQuestionsResponse($surveyId, $organizationId, $personIds, $surveyQuestionsId)
	{
		$parameters = [
			'surveyId' => $surveyId,
			'organizationId' => $organizationId,
			'personIds' => $personIds,
			'surveyQuestionsId' => $surveyQuestionsId
		];

		$parameterTypes = [
			'personIds' => Connection::PARAM_INT_ARRAY
		];

		$sql = "SELECT
					survey_questions_id,
					COUNT(decimal_value) AS responded_count,
					MIN(decimal_value) AS minimum_value,
					MAX(decimal_value) AS maximum_value,
					STD(decimal_value) AS standard_deviation,
					ROUND(AVG(decimal_value), 2) AS mean,
					eqo.option_value
				FROM
					survey_response sr
						INNER JOIN
					survey_questions sq ON sr.survey_questions_id = sq.id
						INNER JOIN
					ebi_question_options eqo ON eqo.ebi_question_id = sq.ebi_question_id
						AND sr.decimal_value = eqo.option_value
				WHERE
					sr.survey_id = :surveyId
						AND sr.deleted_at IS NULL
						AND sr.org_id = :organizationId
						AND sr.person_id IN (:personIds)
						AND sr.survey_questions_id = :surveyQuestionsId
						AND sr.deleted_at IS NULL
						AND sq.deleted_at IS NULL
						AND eqo.deleted_at IS NULL
				ORDER BY survey_questions_id";
		try {
			$em = $this->getEntityManager();
			$stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

		} catch (\Exception $e) {
			throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
		}

		$results = $stmt->fetchAll();
		return $results;
	}
	
	public function getNumericResponse($surveyId, $orgId, $personId, $surveyQuestionsId)
	{		
		$em = $this->getEntityManager();
        $sql = "select survey_questions_id, decimal_value				 
				from  survey_response as SR
				join survey_questions as SQ ON SR.survey_questions_id = SQ.id			
				JOIN ebi_question_options as EQO ON (EQO.ebi_question_id = SQ.ebi_question_id and SR.decimal_value = EQO.option_value)
				where SR.survey_id = $surveyId and SR.deleted_at IS NULL
				and SR.org_id = $orgId
				and SR.person_id IN ($personId)
				AND SR.survey_questions_id = $surveyQuestionsId				
				order by survey_questions_id asc";		
		$sql .= ReportsConstants::MAX_SCALE_POSTFIX;					
		try{
			$stmt = $em->getConnection()->prepare($sql);
	        $stmt->execute();
	        
	    } catch (\Exception $e) {
			throw new ValidationException([
			SearchConstant::QUERY_ERROR
			], $e->getMessage(), SearchConstant::QUERY_ERROR);
	    }
        return $stmt->fetchAll();
	}
	
	
	public function getDrilldownRespondedStudents($personId, $surveyQuestionId, $responseValue, $field)
	{		
		$em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(sr.person) as person_id', $field.' as response');
        $qb->from('SynapseSurveyBundle:SurveyResponse', 'sr');
        $qb->where('sr.person IN (:personId)');
        $qb->andWhere('sr.surveyQuestions =:surveyQuestionId');
		/*
         * empty is not condering $responseValue is 0, so that adding this or condition
         */
        if(isset($responseValue))
		{
			$qb->andWhere('sr.decimalValue IN (:responseValue)');
			$qb->setParameters(array(
				'personId' => $personId,
				'surveyQuestionId' => $surveyQuestionId,
				'responseValue' => $responseValue
			));		
		} else {
			$qb->setParameters(array(
				'personId' => $personId,
				'surveyQuestionId' => $surveyQuestionId				
			));	
		}
        $query = $qb->getQuery();			
        $resultSet = $query->getArrayResult();				
		return $resultSet;
	}


	/**
	 * Gets survey responses by question and a list of student IDs
	 *
	 * @param int $surveyQuestionId
	 * @param array $studentIds
	 * @param array $optionValues
	 * @param bool $countFlag
	 * @return array
	 */
	public function getSurveyResponsesByQuestionAndStudentIds($surveyQuestionId, $studentIds, $optionValues = null, $countFlag = false)
	{
		$parameters = [
			'surveyQuestionId' => $surveyQuestionId,
			'studentIds' => $studentIds
		];

		$parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

		if (isset($optionValues)) {
			$optionCondition = "AND decimal_value IN (:optionValues)";
			$parameters['optionValues'] = $optionValues;
			$parameterTypes['optionValues'] = Connection::PARAM_INT_ARRAY;
		} else {
			$optionCondition = "";
		}

		if ($countFlag) {
			$selectClause = 'SELECT COUNT(*) AS count';
		} else {
			$selectClause =
				"SELECT
                    person_id,
                    CASE
                        WHEN response_type = 'decimal' THEN decimal_value
                        WHEN response_type = 'char' THEN char_value
                        WHEN response_type = 'charmax' THEN charmax_value
                    END AS response";
		}

		$sql = "$selectClause
                FROM
                    survey_response
                WHERE
                    deleted_at IS NULL
                    AND survey_questions_id = :surveyQuestionId
                    $optionCondition
                    AND person_id IN (:studentIds);";

		try {
			$em = $this->getEntityManager();
			$stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
			$result = $stmt->fetchAll();

			if ($countFlag) {
				$result = $result[0]['count'];
			}

		} catch (\Exception $e) {
			throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
		}

		return $result;
	}


    /**
     * Get student names, risk levels, class levels, and responses
     *
     * @param int $surveyQuestionId
     * @param array $studentIds
     * @param int $classLevelMetadataId
     * @param array|null $optionValues
     * @param string|null $sortBy
     * @param int|null $recordsPerPage
     * @param int|null $offset
     * @param int|null $orgAcademicYearId
     * @return array
     */
	public function getStudentNamesAndRiskLevelsAndClassLevelsAndResponses($surveyQuestionId, $studentIds, $classLevelMetadataId, $optionValues = null, $sortBy = null, $recordsPerPage = null, $offset = null, $orgAcademicYearId = null)
	{
		if (empty($studentIds)) {
			return [];
		}

		$parameters = [
			'surveyQuestionId' => $surveyQuestionId,
			'studentIds' => $studentIds,
			'classLevelMetadataId' => $classLevelMetadataId
		];

		$parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

		if (isset($optionValues)) {
			$optionCondition = "AND decimal_value IN (:optionValues)";
			$parameters['optionValues'] = $optionValues;
			$parameterTypes['optionValues'] = Connection::PARAM_INT_ARRAY;
		} else {
			$optionCondition = "";
		}

		switch ($sortBy) {
			case 'name':
			case '+name':
				$orderByClause = 'ORDER BY p.lastname, p.firstname, p.username, p.id';
				break;
			case '-name':
				$orderByClause = 'ORDER BY p.lastname DESC, p.firstname DESC, p.username DESC, p.id DESC';
				break;
			case 'student_risk_status':
			case '+student_risk_status':
				// "Ascending" risk should start with green, which has the highest id in the database.
				$orderByClause = 'ORDER BY p.risk_level DESC, p.lastname, p.firstname, p.username, p.id';
				break;
			case '-student_risk_status':
				// "Descending" risk should start with red2, which has the lowest id in the database.
				// -{column} DESC sorts the column in ascending order with nulls last
				$orderByClause = 'ORDER BY -p.risk_level DESC, p.lastname, p.firstname, p.username, p.id';
				break;
			case 'student_classlevel':
			case '+student_classlevel':
				// -{column} DESC sorts the column in ascending order with nulls last
				$orderByClause = 'ORDER BY -emlv.list_value DESC, p.lastname, p.firstname, p.username, p.id';
				break;
			case '-student_classlevel':
				$orderByClause = 'ORDER BY emlv.list_value DESC, p.lastname, p.firstname, p.username, p.id';
				break;
			case 'response':
			case '+response':
				// The responses should never be null, so we don't need to use a trick here.
				// The trick may not work anyway, since responses may not be numeric.
				$orderByClause = 'ORDER BY response, p.lastname, p.firstname, p.username, p.id';
				break;
			case '-response':
				$orderByClause = 'ORDER BY response DESC, p.lastname, p.firstname, p.username, p.id';
				break;
			default:
				$orderByClause = 'ORDER BY p.lastname, p.firstname, p.username, p.id';
		}

		if (isset($recordsPerPage) && isset($offset)) {
			$parameters['recordsPerPage'] = (int)$recordsPerPage;
			$parameters['offset'] = (int)$offset;
			$parameterTypes['recordsPerPage'] = 'integer';
			$parameterTypes['offset'] = 'integer';
			$limitClause = 'LIMIT :recordsPerPage OFFSET :offset';
		} else {
			$limitClause = '';
		}

        if (!empty($orgAcademicYearId)) {
            $isActiveColumn = 'opsy.is_active, ';
            $joinWithOPSY = ' INNER JOIN org_person_student_year opsy ON opsy.person_id = p.id
                        AND opsy.org_academic_year_id = :currentAcademicYearId 
                        AND opsy.deleted_at IS NULL ';
            $parameters['currentAcademicYearId'] = $orgAcademicYearId;
        } else {
            $isActiveColumn = '';
            $joinWithOPSY = '';
        }

		$sql = "SELECT
                    p.id AS student_id,
                    p.firstname,
                    p.lastname,
                    p.external_id,
                    p.username,
                    $isActiveColumn
                    rl.risk_text AS risk_color,
                    rl.image_name AS risk_image_name,
                    emlv.list_name AS class_level,
                    CASE
                        WHEN sr.response_type = 'decimal' THEN sr.decimal_value
                        WHEN sr.response_type = 'char' THEN sr.char_value
                        WHEN sr.response_type = 'charmax' THEN sr.charmax_value
                    END AS response
                FROM
                    person p
                        $joinWithOPSY
                        INNER JOIN
                    survey_response sr
                            ON sr.person_id = p.id
                            AND sr.org_id = p.organization_id
                        LEFT JOIN
                    person_ebi_metadata pem
                            ON pem.person_id = p.id
                            AND pem.ebi_metadata_id = :classLevelMetadataId
                            AND pem.deleted_at IS NULL
                        LEFT JOIN
                    ebi_metadata_list_values emlv
                            ON emlv.ebi_metadata_id = pem.ebi_metadata_id
                            AND emlv.list_value = pem.metadata_value
                            AND emlv.deleted_at IS NULL
                        LEFT JOIN
                    risk_level rl
                            ON rl.id = p.risk_level
                            AND rl.deleted_at IS NULL
                WHERE
                    p.deleted_at IS NULL
                    AND sr.deleted_at IS NULL
                    AND p.id IN (:studentIds)
                    AND sr.survey_questions_id = :surveyQuestionId
                    $optionCondition
                $orderByClause
                $limitClause;";

		try {
			$em = $this->getEntityManager();
			$stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
			$results = $stmt->fetchAll();

		} catch (\Exception $e) {
			throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
		}

		return $results;
	}


	/**
	 *
	 * List students in drilldown with the stated response and their intent to leave and risk. This differs from the listDrilldownStudentsByCategory function because this function looks at responses without question option values (type LA, SA, etc.)
	 *
	 * @param $studentId
	 * @param $startPoint
	 * @param $limit
	 * @param $sortBy
	 * @param $surveyQuestionId
	 * @param $field
	 * @param $orgId
	 * @param $surveyId
	 * @param $ebiMetadataId
	 * @param string $viewmode
	 * @return array
	 * @throws SynapseDatabaseException
	 */
	public function listDrilldownStudents($studentId, $startPoint, $limit, $sortBy, $surveyQuestionId, $field, $orgId, $surveyId, $ebiMetadataId, $viewmode='')
	{
		if(!empty($surveyQuestionId)){
			$parameters['surveyQuestionId'] = $surveyQuestionId;
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

		if(empty($viewmode) && $viewmode != 'student-list') {
			$parameters['limit'] = (int) $limit;
			$parameters['startPoint'] = (int) $startPoint;
			$limitStatement = " LIMIT :startPoint, :limit";
			$parameterTypes['limit'] = \PDO::PARAM_INT;
			$parameterTypes['startPoint'] = \PDO::PARAM_INT;
		} else {
			$limitStatement = "";
		}

		$sql = "
	    SELECT SQL_CALC_FOUND_ROWS
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
            person P
                JOIN
            survey_response sr ON P.id = sr.person_id AND sr.deleted_at IS NULL
            		AND sr.survey_questions_id = :surveyQuestionId
					AND sr.org_id = :orgId
					AND sr.survey_id = :surveyId
                LEFT JOIN
            risk_level AS RL ON P.risk_level = RL.id AND RL.deleted_at IS NULL
                LEFT JOIN
            intent_to_leave AS IL ON P.intent_to_leave = IL.id AND IL.deleted_at IS NULL
                LEFT JOIN
            person_ebi_metadata AS pem ON pem.person_id = P.id AND pem.ebi_metadata_id = :ebiMetadataId AND pem.deleted_at IS NULL
            WHERE
                P.deleted_at IS NULL
				AND P.organization_id = :orgId
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
	 * Get datablock id's w.r.t ebi questions and student ids the faculty has permission on
	 *
	 * @param $personId
	 * @param $orgId
	 * @param $ebiQuesIds
	 * @param $studentIds
	 * @throws SynapseDatabaseException
	 * @return array:
	 */
	public function getStudentsBasedQuestionPermission($personId, $orgId, $ebiQuesIds, $studentIds)
    {
        $studentIds = explode(',', $studentIds);
        if(count($studentIds) == 0){
            return null;
        }
        
        $sql = 'SELECT
                    DISTINCT opd.datablock_id, ofspm.student_id
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset_datablock opd ON opd.org_permissionset_id = ofspm.permissionset_id
                WHERE
                    opd.deleted_at IS NULL
                    AND ofspm.org_id = :orgId
                    AND ofspm.faculty_id = :facultyId
                    AND ofspm.student_id IN (:studentIds)
                    AND opd.datablock_id IN
                    (
                        SELECT
                            DISTINCT datablock_id
                        FROM
                            datablock_questions
                        WHERE
                            ebi_question_id IN (:ebiQuesIds)
                    )
            ';
        
        $parameters['orgId'] = (int) $orgId;
        $parameters['facultyId'] = (int) $personId;
        $parameters['studentIds'] = $studentIds;
        $parameterTypes['studentIds'] = Connection::PARAM_INT_ARRAY;
        $parameters['ebiQuesIds'] = $ebiQuesIds;
        $parameterTypes['ebiQuesIds'] = Connection::PARAM_INT_ARRAY;
        
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $stmt->fetchAll();
    }

    /**
	 * List students in drilldown with the stated response and their intent to leave and risk. This differs from the listDrilldownStudents function because this function looks at responses with question option values (type MR, D, Q, etc.)
	 *
	 * @param int $studentId
	 * @param int $startPoint
	 * @param int $limit
	 * @param int $sortBy
	 * @param int $surveyQuestionId
	 * @param int $field
	 * @param int $orgId
	 * @param int $surveyId
	 * @param int $ebiMetadataId
	 * @param string $viewmode
	 * @return array
	 * @throws
	 */
	public function listDrilldownStudentsByCategory($studentId, $startPoint, $limit, $sortBy, $surveyQuestionId, $field, $orgId, $surveyId, $ebiMetadataId, $viewmode='')
	{

		if(!empty($surveyQuestionId)){
			$parameters['surveyQuestionId'] = $surveyQuestionId;
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

		if($field == 'sr.decimal_value') {
			$decimalValueExcludeCondition = "  AND sr.decimal_value != 99 ";
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
                    P.id,
                    P.id AS student,
                    P.firstname,
                    P.lastname,
                    P.risk_level,
                    RL.risk_text,
                    RL.image_name AS risk_imagename,
                    IL.text AS intent_to_leave_text,
                    IL.image_name AS intent_imagename,
                    QO.option_text AS response,
                    pem.metadata_value AS class_level
                FROM
                    person P
                        JOIN
                    survey_response sr ON P.id = sr.person_id
                    		AND sr.deleted_at IS NULL
                    		AND sr.survey_questions_id = :surveyQuestionId
                    		AND sr.org_id = :orgId
                    		AND sr.survey_id = :surveyId
                        LEFT JOIN
                    risk_level AS RL ON P.risk_level = RL.id AND RL.deleted_at IS NULL
                        LEFT JOIN
                    intent_to_leave AS IL ON P.intent_to_leave = IL.id AND IL.deleted_at IS NULL
                        JOIN
                    survey_questions SQ ON sr.survey_questions_id = SQ.id AND SQ.deleted_at IS NULL
                        JOIN
                    ebi_question_options QO ON QO.ebi_question_id = SQ.ebi_question_id
                    		AND $field = QO.option_value
                    		AND QO.deleted_at IS NULL
                        LEFT JOIN
                    person_ebi_metadata AS pem ON pem.person_id = P.id
                    		AND pem.ebi_metadata_id = :ebiMetadataId
                    		AND pem.deleted_at IS NULL
                WHERE
                    P.deleted_at IS NULL
					AND P.organization_id = :orgId
					AND P.id IN (:studentIds)
					$decimalValueExcludeCondition
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
     * Returns the three free response questions and responses ("Student Comments") for the success marker page,
     * provided the student has answered them and the given datablocks give the user permission to see them.
     *
     * @param int $studentId
     * @param int $surveyId
     * @param array $datablockIds
     * @return array
     */
	public function getFreeResponsesForSuccessMarkerPage($studentId, $surveyId, $datablockIds)
	{
		$parameters = [
            'studentId' => $studentId,
            'surveyId' => $surveyId,
            'datablockIds' => $datablockIds
        ];

		$parameterTypes = ['datablockIds' => Connection::PARAM_INT_ARRAY];

		$sql = "SELECT
                    IF(eql.question_text > '', CONCAT(eql.question_text, ' ', eql.question_rpt), eql.question_rpt) AS question_text,
                    COALESCE(sr.charmax_value, sr.char_value) AS response
                FROM
                    survey_response sr
                        INNER JOIN
                    survey_questions sq
                            ON sq.id = sr.survey_questions_id
                        INNER JOIN
                    ebi_question eq
                            ON eq.id = sq.ebi_question_id
                        INNER JOIN
                    ebi_questions_lang eql
                            ON eql.ebi_question_id = eq.id
                        INNER JOIN
                    datablock_questions dq
                            ON dq.ebi_question_id = eq.id
                WHERE
                    eq.on_success_marker_page = 1
                    AND sr.person_id = :studentId
                    AND sr.survey_id = :surveyId
                    AND dq.datablock_id in (:datablockIds)
                    AND sr.deleted_at IS NULL
                    AND sq.deleted_at IS NULL
                    AND eq.deleted_at IS NULL
                    AND eql.deleted_at IS NULL
                    AND dq.deleted_at IS NULL
                ORDER BY sq.qnbr;";

		try {
			$em = $this->getEntityManager();
			$stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

		} catch (\Exception $e) {
			throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
		}

		$results = $stmt->fetchAll();

		return $results;
	}

    /**
     * Creates the CASE statement for the GPA, factor and retention sections
     * Method to be used in compare report
     *
     * @param array $questionTypeWithRangeDetails
     * @param string $surveyType
     * @return array
     */
    public function createCaseQueryForISQorSurvey($questionTypeWithRangeDetails, $surveyType)
    {
        $sql = '';
        $condition = '';
        $parameters = [];

        if ($surveyType == 'isqs') {
            $tableName = 'oqr';
        } elseif ($surveyType == 'survey') {
            $tableName = 'sr';
        }

        for ($subPopulationCount = 1; $subPopulationCount <= 2; $subPopulationCount++) {
            if (isset($questionTypeWithRangeDetails['category'])) { // When question type is categorical
                $parameters['decimal_value_' . $subPopulationCount] = $questionTypeWithRangeDetails['category']['subpopulation' . $subPopulationCount];
                $condition .= "WHEN $tableName.decimal_value IN (:decimal_value_" . $subPopulationCount . ") THEN " . $subPopulationCount . " ";
            }

            if (isset($questionTypeWithRangeDetails['number'])) { // When question type is number
                $subPopulationRangeDeciderCount = count($questionTypeWithRangeDetails['number']['subpopulation' . $subPopulationCount]);

                if ($subPopulationRangeDeciderCount == 1) {
                    $parameters['single_value_' . $subPopulationRangeDeciderCount] = $questionTypeWithRangeDetails['number']['subpopulation' . $subPopulationCount]['single_value'];
                    $condition .= "WHEN $tableName.char_value = :single_value_" . $subPopulationRangeDeciderCount . " THEN " . $subPopulationCount . " ";
                } elseif ($subPopulationRangeDeciderCount == 2) {
                    $parameters['min_digits_' . $subPopulationRangeDeciderCount] = $questionTypeWithRangeDetails['number']['subpopulation' . $subPopulationCount]['min_digits'];
                    $parameters['max_digits_' . $subPopulationRangeDeciderCount] = $questionTypeWithRangeDetails['number']['subpopulation' . $subPopulationCount]['max_digits'];
                    $condition .= "WHEN $tableName.char_value BETWEEN :min_digits_" . $subPopulationRangeDeciderCount . " AND :max_digits_" . $subPopulationRangeDeciderCount . " THEN " . $subPopulationCount . " ";
                } else {
                    $condition .= '';
                }
            }
            $sql = "CASE " . $condition . " END";
        }
        $result['sql'] = $sql;
        $result['parameters'] = $parameters;
        return $result;
    }

    /**
     * This method will return Survey GPA data for comparison report.
     *
     * @param int $organizationId
     * @param int $loggedInUserId
     * @param int $yearId
     * @param int $questionId
     * @param array $caseDetails - "Distinguish population decimal_value using 'select case statement SQL' by calling createCaseQueryForISQorSurvey
     * @param array $studentIdsToInclude
     * @return string
     * @throws SynapseDatabaseException
     */
    public function getGPAdataForSurvey($organizationId, $loggedInUserId, $yearId, $questionId, $caseDetails, $studentIdsToInclude = null)
    {
        if (empty($caseDetails)) {
            return [];
        }
        $caseSql = $caseDetails['sql'];
        $caseParameters = $caseDetails['parameters'];

        $parameters = [
            'facultyId' => $loggedInUserId,
            'questionId' => $questionId,
            'organizationId' => $organizationId,
            'yearId' => $yearId
        ];

        // survey_response decimal_value column data
        if ($caseParameters['decimal_value_1'] != null && $caseParameters['decimal_value_2'] != null) {
            $parameterTypes = [
                'decimal_value_1' => Connection::PARAM_INT_ARRAY,
                'decimal_value_2' => Connection::PARAM_INT_ARRAY
            ];
        }

        if (!empty($studentIdsToInclude)) {
            $parameters['studentIdsToInclude'] = $studentIdsToInclude;
            $parameterTypes['studentIdsToInclude'] = Connection::PARAM_INT_ARRAY;
            $studentIdsCondition = 'AND sr.person_id IN (:studentIdsToInclude)';
        } else {
            $studentIdsCondition = '';
        }

        $parameters = array_merge($parameters, $caseParameters);


        $sql = "SELECT oat.id AS org_academic_terms_id, 
                             $caseSql AS subpopulation_id,
                             pem.person_id + FLOOR(RAND() * " . SynapseConstant::RANDOM_MULTIPLIER_SALT_FOR_PARTICIPANT_ID . ") + " . SynapseConstant::ADDED_SALT_FOR_PARTICIPANT_ID . " AS participant_id,
                             ROUND(pem.metadata_value,2) AS gpa_value,
                             oat.name AS term_name
                      FROM
                             person_ebi_metadata pem
                             JOIN
                                org_faculty_student_permission_map ofspm ON ofspm.student_id = pem.person_id
                                AND ofspm.faculty_id = :facultyId
                             JOIN
                                survey_response sr ON sr.person_id = pem.person_id
                             JOIN 
                                survey_questions sq ON sq.id = sr.survey_questions_id 
                                AND sq.ebi_question_id = :questionId   
                             JOIN
                                ebi_metadata em ON em.id = pem.ebi_metadata_id
                                AND em.meta_key = 'EndTermGPA'
                             JOIN
                                org_academic_terms oat ON oat.id = pem.org_academic_terms_id
                                AND oat.organization_id = :organizationId
                             JOIN
                                org_academic_year oay ON oay.id = pem.org_academic_year_id
                                AND oay.year_id = :yearId
                      WHERE
                             pem.deleted_at IS NULL
                             AND sr.deleted_at IS NULL
                             AND em.deleted_at IS NULL
                             AND oat.deleted_at IS NULL
                             AND oay.deleted_at IS NULL
                             AND sq.deleted_at IS NULL
                             $studentIdsCondition    
                      GROUP BY oat.id DESC, subpopulation_id, pem.person_id
                      HAVING subpopulation_id IS NOT NULL
                      ORDER BY oat.start_date ASC, oat.end_date ASC, subpopulation_id, pem.person_id";
        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $resultSet;
    }

    /**
     * This method will return Survey Factor data for comparison report.
     *
     * @param int $organizationId
     * @param int $surveyId
     * @param int $cohortId
     * @param int $questionId
     * @param int $loggedInUserId
     * @param string $surveyYearId
     * @param array $caseDetails - "Distinguish population decimal_value using 'select case statement SQL' by calling createCaseQueryForISQorSurvey
     * @param int $studentPopulationSurveyId
     * @param array $studentIdsToInclude
     * @return string
     * @throws SynapseDatabaseException
     */
    public function getFactorDataForSurvey($organizationId, $surveyId, $cohortId, $questionId, $loggedInUserId, $surveyYearId, $caseDetails, $studentPopulationSurveyId, $studentIdsToInclude = null)
    {
        if (empty($caseDetails)) {
            return [];
        }
        $caseSql = $caseDetails['sql'];
        $caseParameters = $caseDetails['parameters'];

        $parameters = [
            'facultyId' => $loggedInUserId,
            'questionId' => $questionId,
            'cohortId' => $cohortId,
            'organizationId' => $organizationId,
            'surveyId' => $surveyId,
            'surveyYearId' => $surveyYearId,
            'studentPopulationSurveyId' => $studentPopulationSurveyId
        ];

        // survey_response decimal_value column data
        if ($caseParameters['decimal_value_1'] != null && $caseParameters['decimal_value_2'] != null) {
            $parameterTypes = [
                'decimal_value_1' => Connection::PARAM_INT_ARRAY,
                'decimal_value_2' => Connection::PARAM_INT_ARRAY
            ];
        }

        if (!empty($studentIdsToInclude)) {
            $parameters['studentIdsToInclude'] = $studentIdsToInclude;
            $parameterTypes['studentIdsToInclude'] = Connection::PARAM_INT_ARRAY;
            $studentIdsCondition = 'AND sr.person_id IN (:studentIdsToInclude)';
        } else {
            $studentIdsCondition = '';
        }

        $parameters = array_merge($parameters, $caseParameters);

        $sql = "SELECT pfc.factor_id,
                                fl.short_name AS factor_name,
                                $caseSql AS subpopulation_id,
                                pfc.person_id + FLOOR(RAND() * " . SynapseConstant::RANDOM_MULTIPLIER_SALT_FOR_PARTICIPANT_ID . ") + " . SynapseConstant::ADDED_SALT_FOR_PARTICIPANT_ID . " AS participant_id,
                                ROUND(pfc.mean_value, 2) AS factor_value
                         FROM
                                person_factor_calculated pfc
                                JOIN
                                   org_faculty_student_permission_map ofspm ON ofspm.student_id = pfc.person_id
                                   AND ofspm.faculty_id = :facultyId
                                JOIN
                                   survey_response sr ON sr.person_id = pfc.person_id
                                   AND sr.survey_id = :studentPopulationSurveyId 
                                JOIN 
                                   survey_questions sq ON sq.id = sr.survey_questions_id 
                                   AND sq.ebi_question_id = :questionId   
                                JOIN
                                   org_permissionset_datablock opd ON opd.org_permissionset_id = ofspm.permissionset_id
                                JOIN
                                   datablock_questions dq ON dq.datablock_id = opd.datablock_id
                                   AND dq.factor_id = pfc.factor_id
                                JOIN
                                    org_academic_year oay ON oay.organization_id = ofspm.org_id
                                    AND oay.year_id = :surveyYearId
                                JOIN
                                   org_person_student_cohort opsc ON opsc.person_id = pfc.person_id 
                                   AND opsc.cohort = :cohortId
                                   AND opsc.org_academic_year_id = oay.id
                                JOIN 
                                factor_lang fl ON fl.id = pfc.factor_id   
                         WHERE
                                pfc.organization_id = :organizationId
                                AND pfc.survey_id = :surveyId
                                AND pfc.deleted_at IS NULL
                                AND sr.deleted_at IS NULL
                                AND opd.deleted_at IS NULL
                                AND dq.deleted_at IS NULL
                                AND opsc.deleted_at IS NULL
                                $studentIdsCondition
                         GROUP BY pfc.factor_id, subpopulation_id, pfc.person_id
                         HAVING subpopulation_id IS NOT NULL";
        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $resultSet;
    }

    /**
     * This method will return Survey Retention data for comparison report.
     *
     * @param int $organizationId
     * @param int $loggedInUserId
     * @param int $ebiQuestionId
     * @param int $surveyId
     * @param int $retentionTrackingYearId
     * @param array $selectCaseQuery - "Distinguish population decimal_value using 'select case statement SQL' by calling createCaseQueryForISQorSurvey
     * @param array $studentIdsToInclude
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getRetentionDataForSurvey($organizationId, $loggedInUserId, $ebiQuestionId, $surveyId, $retentionTrackingYearId, $selectCaseQuery, $studentIdsToInclude = null)
    {
        if (empty($selectCaseQuery)) {
            return [];
        }

        $caseSql = $selectCaseQuery['sql'];
        $caseParameters = $selectCaseQuery['parameters'];

        $parameters = [
            'organizationId' => $organizationId,
            'personId' => $loggedInUserId,
            'ebiQuestionId' => $ebiQuestionId,
            'surveyId' => $surveyId,
            'retentionTrackingYearId' => $retentionTrackingYearId,
        ];

        // survey_response decimal_value column data
        $parameterTypes = [
            'decimal_value_1' => Connection::PARAM_INT_ARRAY,
            'decimal_value_2' => Connection::PARAM_INT_ARRAY
        ];

        if (!empty($studentIdsToInclude)) {
            $parameters['studentIdsToInclude'] = $studentIdsToInclude;
            $parameterTypes['studentIdsToInclude'] = Connection::PARAM_INT_ARRAY;
            $studentIdsCondition = 'AND sr.person_id IN (:studentIdsToInclude)';
        } else {
            $studentIdsCondition = '';
        }

        $parameters = array_merge($parameters, $caseParameters);

        $sql = "SELECT $caseSql AS subpopulation_id,
                           opsrwdcv.organization_id,
                           opsrwdcv.person_id,
                           opsrwdcv.retention_tracking_year,
                           opsrwdcv.year_id,
                           opsrwdcv.year_name,
                           rcvn.name_text AS retention_completion_variable_name,
                           rcvn.years_from_retention_track,
                       CASE
                           WHEN rcvn.type = 'completion' THEN opsrwdcv.is_degree_completed
                           WHEN rcvn.type = 'enrolledMidYear' THEN opsrwdcv.is_enrolled_midyear
                           WHEN rcvn.type = 'enrolledBegYear' THEN opsrwdcv.is_enrolled_beginning_year
                       END AS retention_completion_value,
                       CASE
                           WHEN rcvn.type = 'completion' THEN NULL
                           WHEN rcvn.type = 'enrolledMidYear' THEN 2
                           WHEN rcvn.type = 'enrolledBegYear' THEN 1
                       END AS retention_completion_variable_order
                FROM 
                       (
                           SELECT
                           		DISTINCT student_id, permissionset_id
                           FROM
                           		org_faculty_student_permission_map ofspm
                           WHERE
                           		faculty_id = :personId
                       ) AS ofspm
                       INNER JOIN 
                             (SELECT 
                                opsrbtgv.organization_id,
                                opsrbtgv.person_id,
                                opsrbtgv.retention_tracking_year,
                                opsrbtgv.year_id,
                                opsrbtgv.year_name,
                                opsrbtgv.is_enrolled_beginning_year,
                                opsrbtgv.is_enrolled_midyear,
                                (CASE
                                    WHEN opsrbtgv.is_degree_completed = 1 THEN 1
                                    WHEN
                                        ((SELECT 
                                                opsr1.year_id
                                            FROM
                                                org_person_student_retention_view opsr1
                                            WHERE
                                                opsr1.person_id = opsrbtgv.person_id
                                                    AND opsr1.organization_id = opsrbtgv.organization_id
                                                    AND opsr1.is_degree_completed = 1
                                                    AND opsr1.year_id >= opsrbtgv.retention_tracking_year
                                            ORDER BY opsr1.year_id
                                            LIMIT 1) <= opsrbtgv.year_id)
                                    THEN 1
                                    ELSE 0
                                END) AS is_degree_completed,
                                opsrbtgv.years_from_retention_track AS years_from_retention_track
                            FROM
                                org_person_student_retention_by_tracking_group_view opsrbtgv
                            WHERE
                                retention_tracking_year = :retentionTrackingYearId
                                    AND organization_id = :organizationId) AS opsrwdcv 
                         ON opsrwdcv.person_id = ofspm.student_id
                       INNER JOIN 
                         survey_response sr 
                         ON sr.person_id = ofspm.student_id
                       INNER JOIN 
                         survey_questions sq 
                         ON sq.id = sr.survey_questions_id
                       INNER JOIN 
                         retention_completion_variable_name rcvn 
                         ON opsrwdcv.years_from_retention_track = rcvn.years_from_retention_track
                       INNER JOIN 
                         org_permissionset op 
                         ON ofspm.permissionset_id = op.id
                WHERE   opsrwdcv.retention_tracking_year = :retentionTrackingYearId
                        AND sq.ebi_question_id = :ebiQuestionId
                        AND sq.survey_id = :surveyId
                        AND op.retention_completion = 1
                        AND opsrwdcv.organization_id = :organizationId
                        AND sr.deleted_at IS NULL
                        AND sq.deleted_at IS NULL
                        AND rcvn.deleted_at IS NULL
                        AND op.deleted_at IS NULL
                        $studentIdsCondition
                GROUP BY subpopulation_id, opsrwdcv.person_id, opsrwdcv.retention_tracking_year, rcvn.name_text
                HAVING subpopulation_id IS NOT NULL";
        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $resultSet;
    }

    /**
     * This method will build and execute SQL query for mandatory filters when use has selected a Survey Question,
     * to get list of student IDS (person_id) for comparison report.
     *
     * @param int $organizationId
     * @param int $ebiQuestionId
     * @param array $whereClause - Include both sub-population's selected values in 'WHERE clause SQL' by calling createWhereClauseForIspOrIsq
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getStudentIdsListBasedOnSurveyQuestionSelection($organizationId, $ebiQuestionId, $whereClause)
    {
        $parameters = [
            'ebiQuestionId' => $ebiQuestionId,
            'organizationId' => $organizationId,
        ];

        $parameterTypes = [
            'category_values' => Connection::PARAM_INT_ARRAY,
        ];

        $whereQuery = '';

        if(!empty($whereClause)){
            $whereQuery = " AND ".$whereClause['where_query'];
            $parameters = array_merge($parameters, $whereClause['parameters']);
        }

        $sql = "SELECT 
                      DISTINCT sr.person_id
                FROM survey_response sr
                      JOIN survey_questions sq ON sq.id = sr.survey_questions_id
                WHERE sr.org_id = :organizationId
                      AND sq.ebi_question_id = :ebiQuestionId
                      $whereQuery
                      AND sr.deleted_at IS NULL
                      AND sq.deleted_at IS NULL";
        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return array_column($resultSet, 'person_id');
    }
}
