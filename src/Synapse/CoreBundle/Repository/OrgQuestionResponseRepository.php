<?php

namespace Synapse\CoreBundle\Repository;


use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Entity\OrgQuestionResponse;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\SurveyBundle\SurveyBundleConstant;
use Synapse\CoreBundle\SynapseConstant;

class OrgQuestionResponseRepository extends SynapseRepository
{
    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgQuestionResponse';

    /**
     * Override find() for PHP typing
     *
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return null|OrgQuestionResponse
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    /**
     * Override findBy for PHP Typing
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return OrgQuestionResponse[] The objects.
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }


    /**
     * Override findBy for PHP Typing
     *
     * @param array $criteria
     * @param array|null $orderBy
     *
     * @return OrgQuestionResponse|null The entity instance or NULL if the entity can not be found.
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }


    /**
     * gets Org Question Responses for a given question and a list of students
     *
     * @param int $orgQuestionId
     * @param array $studentIds
     * @param array|null $optionValues
     * @param bool|false $countFlag
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getOrgQuestionResponsesByQuestionAndStudentIds($orgQuestionId, $studentIds, $optionValues = null, $countFlag = false)
    {
        $parameters = [
            'surveyQuestionId' => $orgQuestionId,
            'studentIds' => $studentIds
        ];


        $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

        if (is_array($optionValues) && count($optionValues)>0) {
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
                    org_question_response
                WHERE
                    deleted_at IS NULL
                    AND org_question_id = :surveyQuestionId
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
     * Gets Students Names, Risk Levels, and Response for a given set of student ids and an org_question_id
     *
     * @param int $orgQuestionId
     * @param array $studentIds
     * @param int $classLevelMetadataId
     * @param array|null $optionValues
     * @param string|null $sortBy
     * @param int|null $recordsPerPage
     * @param int|null $offset
     * @param int|null $orgAcademicYearId
     * @return array
     */
    public function getStudentNamesAndRiskLevelsAndClassLevelsAndResponses($orgQuestionId, $studentIds, $classLevelMetadataId, $optionValues = null, $sortBy = null, $recordsPerPage = null, $offset = null, $orgAcademicYearId = null)
    {
        if (empty($studentIds)) {
            return [];
        }

        $parameters = [
            'surveyQuestionId' => $orgQuestionId,
            'studentIds' => $studentIds,
            'classLevelMetadataId' => $classLevelMetadataId
        ];

        $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

        if (is_array($optionValues) && count($optionValues)>0) {
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
                        WHEN oqr.response_type = 'decimal' THEN oqr.decimal_value
                        WHEN oqr.response_type = 'char' THEN oqr.char_value
                        WHEN oqr.response_type = 'charmax' THEN oqr.charmax_value
                    END AS response
                FROM
                    person p
                        $joinWithOPSY
                        INNER JOIN
                    org_question_response oqr
                            ON oqr.person_id = p.id
                            AND oqr.org_id = p.organization_id
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
                    AND oqr.deleted_at IS NULL
                    AND p.id IN (:studentIds)
                    AND oqr.org_question_id = :surveyQuestionId
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
     * Gets ISQ response for the given survey based on permitted organization question ids
     *
     * @param int $surveyId
     * @param int $studentId
     * @param int $organizationId
     * @param array $organizationQuestionIds
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getStudentISQResponses($surveyId, $studentId, $organizationId, $organizationQuestionIds)
    {
        $parameters = [
            'surveyId' => $surveyId,
            'studentId' => $studentId,
            'organizationId' => $organizationId,
            'organizationQuestionIds' => $organizationQuestionIds
        ];

        $parameterTypes = ['organizationQuestionIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    sq.id AS survey_que_id,
                    oq.question_text,
                    oqr.response_type,
                    oqo.option_name,
                    oqr.decimal_value,
                    oqr.char_value,
                    oqr.charmax_value
                FROM
                    survey_questions sq
                        INNER JOIN
                    org_question_response oqr ON oqr.org_question_id = sq.org_question_id
                        INNER JOIN
                    org_question oq ON oq.id = oqr.org_question_id
                        LEFT JOIN
                    org_question_options oqo ON oqo.org_question_id = oq.id
                        AND oqo.option_value = oqr.decimal_value
                        AND oqr.org_question_options_id = oqo.id
                        AND oqo.deleted_at IS NULL
                WHERE
                    oqr.person_id = :studentId
                        AND oqr.org_id = :organizationId
                        AND sq.org_question_id IN (:organizationQuestionIds)
                        AND oqr.survey_id = :surveyId
                        AND sq.deleted_at IS NULL
                        AND oqr.deleted_at IS NULL
                        AND oq.deleted_at IS NULL";
        $result = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $result;
    }

     /**
     * Gets Scaled Category ISQ calculated responses for the given survey based on permitted org question ids
     *
     * @param int $surveyId
     * @param array $personIds
     * @param int $organizationId
     * @param array $orgQuestionIds
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getScaledCategoryISQCalculatedResponses($surveyId, $personIds, $organizationId, $orgQuestionIds)
    {
        $parameters = [
            'surveyId' => $surveyId,
            'personIds' => $personIds,
            'organizationId' => $organizationId,
            'orgQuestionIds' => $orgQuestionIds,
            'decimalValue' => SurveyBundleConstant::NO_RESPONSE_FOR_QUESTION
        ];

        $parameterTypes = ['orgQuestionIds' => Connection::PARAM_INT_ARRAY, 'personIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    oq.id AS org_question_id,
                    sq.id AS survey_question_id,
                    sq.qnbr AS question_number,
                    oq.question_text,
                    oq.question_type_id AS question_type,
                    COUNT(DISTINCT(oqr.person_id)) AS student_count,
                    ROUND(STD(oqr.decimal_value), 2) AS standard_deviation,
                    ROUND(AVG(oqr.decimal_value), 2) AS mean
                FROM
                    survey_questions sq
                        INNER JOIN
                    org_question oq ON sq.org_question_id = oq.id
                        LEFT JOIN
                    org_question_response oqr ON oqr.org_question_id = oq.id
                        AND oqr.org_id = :organizationId
                        AND oqr.survey_id = :surveyId
                        AND oqr.person_id IN (:personIds)
                        AND oqr.deleted_at IS NULL
                        AND oqr.decimal_value != :decimalValue
                WHERE
                    oqr.survey_id = :surveyId
                        AND sq.deleted_at IS NULL
                        AND oq.deleted_at IS NULL
                        AND oq.question_type_id IN ('Q' , 'D')
                        AND oq.id IN (:orgQuestionIds)
                GROUP BY question_text
                ORDER BY oq.id";
        $result = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $result;
    }

    /**`
     * Gets Scaled Type responses for the given survey based on the permitted org question id
     *
     * @param int $surveyId
     * @param int $organizationId
     * @param array $personIds
     * @param int $orgQuestionId
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getScaledTypeISQResponses($surveyId, $organizationId, $personIds, $orgQuestionId)
    {
        $parameters = [
            'surveyId' => $surveyId,
            'personIds' => $personIds,
            'organizationId' => $organizationId,
            'orgQuestionId' => $orgQuestionId,
            'decimalValue' => SurveyBundleConstant::NO_RESPONSE_FOR_QUESTION
        ];

        $parameterTypes = ['personIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    sq.id AS survey_questions_id,
                    COUNT(DISTINCT(oqr.person_id)) AS student_count,
                    sq.qnbr AS question_number,
                    oqr.survey_id,
                    oqr.org_id,
                    oqr.response_type,
                    oqr.decimal_value,
                    oqr.char_value,
                    oqr.charmax_value,
                    oqo.option_name AS option_text,
                    oqo.option_value
                FROM
                    org_question_response oqr
                        INNER JOIN
                    survey_questions sq ON sq.org_question_id = oqr.org_question_id
                        INNER JOIN
                    org_question oq ON oqr.org_question_id = oq.id
                        INNER JOIN
                    org_question_options oqo ON oqo.org_question_id = oq.id
                        AND oqr.decimal_value = oqo.option_value
                WHERE
                    oqr.survey_id = :surveyId
                        AND oqr.deleted_at IS NULL
                        AND sq.deleted_at IS NULL
                        AND oq.deleted_at IS NULL
                        AND oqo.deleted_at IS NULL
                        AND oqr.org_id = :organizationId
                        AND oqr.org_question_id = :orgQuestionId
                        AND oqo.option_value != :decimalValue
                        AND oqr.person_id IN (:personIds)
                GROUP BY oqr.org_question_id, decimal_value
                ORDER BY oqr.org_question_id, oqo.sequence";
        $result = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $result;
    }

    /**
     * Gets Descriptive Questions for the given survey based on type and permitted org question ids
     *
     * @param int $surveyId
     * @param int $organizationId
     * @param array $personIds
     * @param string $type
     * @param string $selectedField
     * @param array $permittedQuestionIds
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getDescriptiveISQResponses($surveyId, $organizationId, $personIds, $type, $selectedField, $permittedQuestionIds)
    {
        $parameters = [
            'surveyId' => $surveyId,
            'personIds' => $personIds,
            'organizationId' => $organizationId,
            'type' => $type,
            'permittedQuestionIds' => $permittedQuestionIds
        ];

        $parameterTypes = ['permittedQuestionIds' => Connection::PARAM_INT_ARRAY, 'personIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    sq.id AS survey_questions_id,
                    sq.qnbr AS question_number,
                    oq.question_text,
                    COUNT(oqr.person_id) AS student_count,
                    oq.question_type_id AS question_type,
                    oqr.survey_id,
                    oq.id AS org_question_id,
                    oqr.org_id,
                    response_type,
                    $selectedField
                FROM
                    org_question_response oqr
                        INNER JOIN
                    survey_questions sq ON oqr.org_question_id = sq.org_question_id
                        INNER JOIN
                    org_question oq ON sq.org_question_id = oq.id
                WHERE
                    oqr.survey_id = :surveyId
                        AND oqr.deleted_at IS NULL
                        AND sq.deleted_at IS NULL
                        AND oq.deleted_at IS NULL
                        AND oqr.org_id = :organizationId
                        AND oq.question_type_id = :type
                        AND oq.id IN (:permittedQuestionIds)
                        AND oqr.person_id IN (:personIds)
                GROUP BY char_value, org_question_id
                ORDER BY org_question_id, question_number";
        $result = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $result;
    }

    /**
     * Gets numeric questions for the given survey based on permitted org question ids.
     *
     * @param int $surveyId
     * @param array $personIds
     * @param int $organizationId
     * @param array $permittedQuestionIds
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getNumericISQResponseCounts($surveyId, $personIds, $organizationId, $permittedQuestionIds)
    {
        $parameters = [
            'surveyId' => $surveyId,
            'personIds' => $personIds,
            'organizationId' => $organizationId,
            'permittedQuestionIds' => $permittedQuestionIds
        ];

        $parameterTypes = ['permittedQuestionIds' => Connection::PARAM_INT_ARRAY, 'personIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    sq.id AS survey_question_id,
                    sq.qnbr AS question_number,
                    oq.question_text,
                    oq.question_type_id AS question_type,
                    oq.id AS org_question_id,
                    COUNT(oqr.person_id) AS student_count
                FROM
                    survey_questions sq
                        INNER JOIN
                    org_question oq ON sq.org_question_id = oq.id
                        INNER JOIN
                    org_question_response oqr ON oqr.org_question_id = oq.id
                WHERE
                    sq.survey_id = :surveyId
                        AND sq.deleted_at IS NULL
                        AND oq.deleted_at IS NULL
                        AND oqr.deleted_at IS NULL
                        AND oq.question_type_id = 'NA'
                        AND oqr.org_id = :organizationId
                        AND oqr.person_id IN (:personIds)
                        AND oq.id IN (:permittedQuestionIds)
                GROUP BY question_text, org_question_id
                ORDER BY org_question_id";
        $result = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $result;
    }

    /**
     * Gets Numeric Questions Responses for the given survey based on permitted org question ids
     *
     * @param int $surveyId
     * @param int $organizationId
     * @param array $personIds
     * @param int $orgQuestionsId
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getNumericISQCalculatedResponses($surveyId, $organizationId, $personIds, $orgQuestionsId)
    {
        $parameters = [
            'surveyId' => $surveyId,
            'personIds' => $personIds,
            'organizationId' => $organizationId,
            'orgQuestionsId' => $orgQuestionsId
        ];

        $parameterTypes = ['personIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    sq.id AS survey_questions_id,
                    COUNT(decimal_value) AS responded_count,
                    MIN(decimal_value) AS minimum_value,
                    MAX(decimal_value) AS maximum_value,
                    STD(decimal_value) AS standard_deviation,
                    ROUND(avg(decimal_value), 2) AS mean,
                    oqo.option_value
                FROM
                    org_question_response oqr
                        INNER JOIN
                    survey_questions sq ON oqr.org_question_id = sq.org_question_id
                        INNER JOIN
                    org_question_options oqo ON oqo.org_question_id = sq.org_question_id
                        AND oqr.decimal_value = oqo.option_value
                WHERE
                    oqr.survey_id = :surveyId
                        AND oqr.deleted_at IS NULL
                        AND sq.deleted_at IS NULL
                        AND oqo.deleted_at IS NULL
                        AND oqr.org_id = :organizationId
                        AND oqr.person_id IN (:personIds)
                        AND oqr.org_question_id = :orgQuestionsId
                ORDER BY survey_questions_id";
        $result = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $result;
    }

    /**
     * Gets Numeric Responses for the given survey based on the permitted org question ids
     *
     * @param int $surveyId
     * @param int $organizationId
     * @param array $personIds
     * @param int $orgQuestionId
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getNumericISQResponses($surveyId, $organizationId, $personIds, $orgQuestionId)
    {
        $parameters = [
            'surveyId' => $surveyId,
            'personIds' => $personIds,
            'organizationId' => $organizationId,
            'orgQuestionId' => $orgQuestionId
        ];

        $parameterTypes = ['personIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    oqr.org_question_id,
                    decimal_value
                FROM
                    org_question_response oqr
                        INNER JOIN
                    survey_questions sq ON oqr.org_question_id = sq.org_question_id
                        INNER JOIN
                    org_question_options oqo ON oqo.org_question_id = sq.org_question_id
                        AND oqr.decimal_value = oqo.option_value
                WHERE
                    oqr.survey_id = :surveyId
                        AND oqr.deleted_at IS NULL
                        AND sq.deleted_at IS NULL
                        AND oqo.deleted_at IS NULL
                        AND oqr.org_id = :organizationId
                        AND oqr.person_id IN (:personIds)
                        AND oqr.org_question_id = :orgQuestionId
                ORDER BY oqo.sequence";
        $result = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $result;
    }

    /**
     * Gets Multiple Response Questions for the given survey based on permitted org question ids
     *
     * @param int $surveyId
     * @param array $personIds
     * @param int $organizationId
     * @param array $orgQuestionIds
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getMultiResponseISQCalculatedResponses($surveyId, $personIds, $organizationId, $orgQuestionIds)
    {
        $parameters = [
            'surveyId' => $surveyId,
            'organizationId' => $organizationId,
            'personIds' => $personIds,
            'orgQuestionIds' => $orgQuestionIds,
            'decimalValue' => SurveyBundleConstant::DECIMAL_VALUE_FOR_QUESTION
        ];

        $parameterTypes = ['orgQuestionIds' => Connection::PARAM_INT_ARRAY, 'personIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    oq.id AS org_question_id,
                    sq.id AS survey_question_id,
                    sq.qnbr AS question_number,
                    oq.question_text,
                    oq.question_type_id AS question_type,
                    COUNT(DISTINCT(oqr.person_id)) AS student_count,
                    ROUND(STD(oqr.decimal_value), 2) AS standard_deviation,
                    ROUND(AVG(oqr.decimal_value), 2) AS mean
                FROM
                    survey_questions sq
                        INNER JOIN
                    org_question oq ON sq.org_question_id = oq.id
                        LEFT JOIN
                    org_question_response oqr ON oqr.org_question_id = oq.id
                        AND oqr.org_id = :organizationId
                        AND oqr.person_id IN (:personIds)
                        AND oqr.deleted_at IS NULL
                WHERE
                    oqr.survey_id = :surveyId
                        AND sq.deleted_at IS NULL
                        AND oq.deleted_at IS NULL
                        AND oq.question_type_id = 'MR'
                        AND oq.id IN (:orgQuestionIds)
                        AND oqr.decimal_value = :decimalValue
                GROUP BY question_text
                ORDER BY oq.id";
        $result = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $result;
    }

    /**
     * Gets Multiple Responses for the given survey based on permitted org question ids
     *
     * @param int $surveyId
     * @param int $organizationId
     * @param array $personIds
     * @param int $orgQuestionId
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getMultiResponseISQResponses($surveyId, $organizationId, $personIds, $orgQuestionId)
    {
        $parameters = [
            'surveyId' => $surveyId,
            'organizationId' => $organizationId,
            'personIds' => $personIds,
            'orgQuestionId' => $orgQuestionId,
            'decimalValue' => SurveyBundleConstant::DECIMAL_VALUE_FOR_QUESTION
        ];

        $parameterTypes = ['personIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    sq.id AS survey_questions_id,
                    COUNT(DISTINCT(oqr.person_id)) AS student_count,
                    sq.qnbr AS question_number,
                    oqr.survey_id,
                    oqr.org_id,
                    response_type,
                    decimal_value,
                    char_value,
                    charmax_value,
                    oqo.option_name AS option_text,
                    oqo.option_value,
                    oqr.org_question_options_id AS option_id
                FROM
                    org_question_response oqr
                        INNER JOIN
                    survey_questions sq ON sq.org_question_id = oqr.org_question_id
                        INNER JOIN
                    org_question oq ON oqr.org_question_id = oq.id
                        INNER JOIN
                    org_question_options oqo ON oqo.org_question_id = oq.id
                        AND oqo.id = oqr.org_question_options_id
                WHERE
                    oqr.survey_id = :surveyId
                        AND oqr.deleted_at IS NULL
                        AND sq.deleted_at IS NULL
                        AND oq.deleted_at IS NULL
                        AND oqo.deleted_at IS NULL
                        AND oqr.org_id = :organizationId
                        AND oqr.org_question_id = :orgQuestionId
                        AND oqr.decimal_value = :decimalValue
                        AND oqr.person_id IN (:personIds)
                GROUP BY oqr.org_question_id, oqr.org_question_options_id
                ORDER BY oqr.org_question_id, oqo.sequence";
        $result = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $result;
    }

    /**
     * Gets students Multiple Responses count for the given survey based on the permitted org question ids
     *
     * @param int $surveyId
     * @param int $organizationId
     * @param array $personIds
     * @param int $orgQuestionId
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getMultiResponseISQResponseCount($surveyId, $organizationId, $personIds, $orgQuestionId)
    {
        $parameters = [
            'surveyId' => $surveyId,
            'organizationId' => $organizationId,
            'personIds' => $personIds,
            'orgQuestionId' => $orgQuestionId,
            'decimalValue' => SurveyBundleConstant::DECIMAL_VALUE_FOR_QUESTION
        ];

        $parameterTypes = ['personIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    COUNT(DISTINCT(oqr.person_id)) AS student_count
                FROM
                    org_question_response oqr
                        INNER JOIN
                    survey_questions sq ON sq.org_question_id = oqr.org_question_id
                        INNER JOIN
                    org_question oq ON oqr.org_question_id = oq.id
                        INNER JOIN
                    org_question_options oqo ON oqo.org_question_id = oq.id
                        AND oqo.id = oqr.org_question_options_id
                WHERE
                    oqr.survey_id = :surveyId
                        AND oqr.deleted_at IS NULL
                        AND sq.deleted_at IS NULL
                        AND oq.deleted_at IS NULL
                        AND oqo.deleted_at IS NULL
                        AND oqr.org_id = :organizationId
                        AND oqr.org_question_id = :orgQuestionId
                        AND oqr.decimal_value = :decimalValue
                        AND oqr.person_id IN (:personIds)
                GROUP BY oqr.org_question_id
                ORDER BY oqr.org_question_id, oqo.sequence";
        $result = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $result;
    }

    /**
     * This method will return ISQ GPA data for comparison report.
     *
     * @param int $organizationId
     * @param int $loggedInUserId
     * @param int $yearId
     * @param int $questionId
     * @param array $caseDetails - "Distinguish population decimal_value using 'select case statement SQL' by calling SurveyResponseRepository::createCaseQueryForISQorSurvey
     * @param array $studentIdsToInclude
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getGPAdataForISQ($organizationId, $loggedInUserId, $yearId, $questionId, $caseDetails, $studentIdsToInclude = null)
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

        $parameterTypes = [
            'decimal_value_1' => Connection::PARAM_INT_ARRAY,
            'decimal_value_2' => Connection::PARAM_INT_ARRAY
        ];

        if (!empty($studentIdsToInclude)) {
            $parameters['studentIdsToInclude'] = $studentIdsToInclude;
            $parameterTypes['studentIdsToInclude'] = Connection::PARAM_INT_ARRAY;
            $studentIdsCondition = 'AND oqr.person_id IN (:studentIdsToInclude)';
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
                             org_question_response oqr ON oqr.person_id = pem.person_id 
                             AND oqr.org_question_id = :questionId
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
                          AND oqr.deleted_at IS NULL 
                          AND em.deleted_at IS NULL
                          AND oat.deleted_at IS NULL
                          AND oay.deleted_at IS NULL
                          $studentIdsCondition
                   GROUP BY oat.id DESC, subpopulation_id, pem.person_id
                   HAVING subpopulation_id IS NOT NULL
                   ORDER BY oat.start_date ASC, oat.end_date ASC, subpopulation_id, pem.person_id";
        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $resultSet;
    }

    /**
     * This method will return ISQ Factor data for comparison report.
     *
     * @param int $organizationId
     * @param int $surveyId
     * @param int $cohortId
     * @param int $questionId
     * @param int $loggedInUserId
     * @param string $surveyYearId
     * @param array $caseDetails - "Distinguish population decimal_value using 'select case statement SQL' by calling SurveyResponseRepository::createCaseQueryForISQorSurvey
     * @param int $studentPopulationSurveyId
     * @param array $studentIdsToInclude
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getFactorDataForISQ($organizationId, $surveyId, $cohortId, $questionId, $loggedInUserId, $surveyYearId, $caseDetails, $studentPopulationSurveyId, $studentIdsToInclude = null)
    {
        if (empty($caseDetails)) {
            return [];
        }
        $caseSql = $caseDetails['sql'];
        $caseParameters = $caseDetails['parameters'];

        $parameters = [
            'facultyId' => $loggedInUserId,
            'questionId' => $questionId,
            'cohort' => $cohortId,
            'organizationId' => $organizationId,
            'surveyId' => $surveyId,
            'surveyYearId' => $surveyYearId,
            'studentPopulationSurveyId' => $studentPopulationSurveyId
        ];

        // org_question_response decimal_value column data
        $parameterTypes = [
            'decimal_value_1' => Connection::PARAM_INT_ARRAY,
            'decimal_value_2' => Connection::PARAM_INT_ARRAY
        ];

        if (!empty($studentIdsToInclude)) {
            $parameters['studentIdsToInclude'] = $studentIdsToInclude;
            $parameterTypes['studentIdsToInclude'] = Connection::PARAM_INT_ARRAY;
            $studentIdsCondition = 'AND oqr.person_id IN (:studentIdsToInclude)';
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
                             JOIN
                                org_question_response oqr ON oqr.person_id = pfc.person_id 
                             JOIN
                                org_permissionset_datablock opd ON opd.org_permissionset_id = ofspm.permissionset_id
                             JOIN
                                datablock_questions dq ON dq.datablock_id = opd.datablock_id
                                AND dq.factor_id = pfc.factor_id
                             JOIN
                                org_academic_year oay ON oay.organization_id = ofspm.org_id
                             JOIN
                                org_person_student_cohort opsc ON opsc.person_id = pfc.person_id 
                                AND opsc.org_academic_year_id = oay.id
                             JOIN 
                                factor_lang fl ON fl.id = pfc.factor_id
                      WHERE
                             pfc.organization_id = :organizationId
                             AND ofspm.faculty_id = :facultyId
                             AND pfc.survey_id = :surveyId
                             AND oqr.org_question_id = :questionId
                             AND oqr.survey_id = :studentPopulationSurveyId
                             AND oay.year_id = :surveyYearId
                             AND opsc.cohort = :cohort
                             AND pfc.deleted_at IS NULL
                             AND oqr.deleted_at IS NULL
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
     * This method will return ISQ Retention data for comparison report.
     *
     * @param int $organizationId
     * @param int $loggedInUserId
     * @param int $orgQuestionId
     * @param int $surveyId
     * @param int $retentionTrackingYearId
     * @param array $selectCaseQuery - "Distinguish population decimal_value using 'select case statement SQL' by calling SurveyResponseRepository::createCaseQueryForISQorSurvey
     * @param array $studentIdsToInclude
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getRetentionDataForISQ($organizationId, $loggedInUserId, $orgQuestionId, $surveyId, $retentionTrackingYearId, $selectCaseQuery, $studentIdsToInclude = null)
    {
        if (empty($selectCaseQuery)) {
            return [];
        }

        $caseSql = $selectCaseQuery['sql'];
        $caseParameters = $selectCaseQuery['parameters'];

        $parameters = [
            'organizationId' => $organizationId,
            'personId' => $loggedInUserId,
            'orgQuestionId' => $orgQuestionId,
            'surveyId' => $surveyId,
            'retentionTrackingYearId' => $retentionTrackingYearId,
        ];

        // org_question_response decimal_value column data
        $parameterTypes = [
            'decimal_value_1' => Connection::PARAM_INT_ARRAY,
            'decimal_value_2' => Connection::PARAM_INT_ARRAY
        ];


        if (!empty($studentIdsToInclude)) {
            $parameters['studentIdsToInclude'] = $studentIdsToInclude;
            $parameterTypes['studentIdsToInclude'] = Connection::PARAM_INT_ARRAY;
            $studentIdsCondition = 'AND oqr.person_id IN (:studentIdsToInclude)';
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
                              DISTINCT student_id,
                              permissionset_id
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
                          org_question_response oqr 
                          ON oqr.person_id = ofspm.student_id
                       INNER JOIN 
                          retention_completion_variable_name rcvn 
                          ON opsrwdcv.years_from_retention_track = rcvn.years_from_retention_track
                       INNER JOIN 
                          org_permissionset op 
                          ON ofspm.permissionset_id = op.id
                WHERE   opsrwdcv.retention_tracking_year = :retentionTrackingYearId
                        AND oqr.survey_id = :surveyId
                        AND oqr.org_question_id = :orgQuestionId
                        AND opsrwdcv.organization_id = :organizationId
                        AND op.retention_completion = 1
                        AND oqr.deleted_at IS NULL
                        AND rcvn.deleted_at IS NULL
                        AND op.deleted_at IS NULL
                        $studentIdsCondition
                GROUP BY subpopulation_id, opsrwdcv.person_id, opsrwdcv.retention_tracking_year, rcvn.name_text
                HAVING subpopulation_id IS NOT NULL";
        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $resultSet;
    }

    /**
     * This method will build and execute SQL query for mandatory filters when use has selected an ISQ,
     * to get list of student IDS (person_id) for comparison report.
     *
     * @param int $organizationId
     * @param int $orgQuestionId
     * @param array $whereClause - Include both sub-population's selected values in 'WHERE clause SQL' by calling createWhereClauseForIspOrIsq
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getStudentIdsListBasedOnISQSelection($organizationId, $orgQuestionId, $whereClause)
    {
        $parameters = [
            'orgQuestionId' => $orgQuestionId,
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
                      DISTINCT oqr.person_id
                FROM org_question_response oqr
                     JOIN survey_questions sq ON sq.org_question_id = oqr.org_question_id
                WHERE oqr.org_question_id = :orgQuestionId
                     $whereQuery
                     AND oqr.org_id = :organizationId
                     AND sq.deleted_at IS NULL
                     AND oqr.deleted_at IS NULL";
        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return array_column($resultSet, 'person_id');

    }
}