<?php

namespace Synapse\CoreBundle\Repository;

/**
 * PersonOrgMetaDataRepository
 */
use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Exception\ValidationException;

class PersonOrgMetaDataRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:PersonOrgMetadata';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_ORG_METADATA = 'SynapseCoreBundle:PersonOrgMetadata';

    /**
     *
     * @param unknown $metadataId
     * @return boolean
     */
    public function isDataAttched($metadataId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('pem.id');
        $qb->from('SynapseCoreBundle:PersonOrgMetadata', 'pem');
        $qb->where('pem.orgMetadata = :metadataId');
        $qb->setParameters(array(
            'metadataId' => $metadataId
        ));

        $query = $qb->getQuery();
        if (count($query->getArrayResult()) > 0) {
            $value = true;
        } else {
            $value = false;
        }
        return $value;
    }

    /**
     *
     * @param unknown $metadataId
     * @param array $catValArray
     */
    public function getAllStudentsByIsp($queryArray, $type, $orgId)
    {
        $query = implode(" OR ", $queryArray);
        $em = $this->getEntityManager();
        if ($type == 'org') {
            $sql = "select distinct md.person_id FROM person_org_metadata md 
            JOIN person p ON p.id=md.person_id
             left join org_metadata as om
                on om.id = md.org_metadata_id 
            where p.organization_id = $orgId AND  " . $query;
        } else {
            $sql = "select distinct md.person_id FROM person_ebi_metadata md 
            JOIN person p ON p.id=md.person_id  
            left join ebi_metadata as em
                ON em.id  = md.ebi_metadata_id where p.organization_id = $orgId AND " . $query;
        }

        $resultSet = $em->getConnection()->fetchAll($sql);

        return $resultSet;
    }

    /**
     * Function to execule SQL and get resultset
     * @param unknown $sql
     */
    public function getResultset($sql)
    {
        $em = $this->getEntityManager();
        $resultSet = $em->getConnection()->fetchAll($sql);
        return $resultSet;
    }


    /**
     * Returns the metadata values for the given students for the given profile item (org metadata).
     * For year- and term-specific profile items, only returns values for the given academic years/terms.
     * If $countFlag is true, returns a count of the number of records instead of the records themselves.
     *
     * @param int $orgMetadataId
     * @param array $studentIds
     * @param array $orgAcademicYearIds
     * @param array $orgAcademicTermsIds
     * @param int|null $optionValue - used to restrict the query to only a particular metadata_value (intended for categorical items)
     * @param int|float|null $optionMin - used with $optionMax to restrict the metadata_value (intended for numeric items)
     * @param int|float|null $optionMax
     * @param boolean $countFlag
     * @return array|int
     */
    public function getMetadataValuesByOrgMetadataAndStudentIds($orgMetadataId, $studentIds, $orgAcademicYearIds, $orgAcademicTermsIds = [],
                                                                $optionValue = null, $optionMin = null, $optionMax = null, $countFlag = false)
    {
        $parameters = [
            'orgMetadataId' => $orgMetadataId,
            'studentIds' => $studentIds
        ];

        $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

        if (!empty($orgAcademicYearIds)) {
            $yearSQLsubstring = "AND (org_academic_year_id IN (:orgAcademicYearIds) OR org_academic_year_id IS NULL)";
            $parameters['orgAcademicYearIds'] = $orgAcademicYearIds;
            $parameterTypes['orgAcademicYearIds'] = Connection::PARAM_INT_ARRAY;
        } else {
            $yearSQLsubstring = "";
        }

        if (!empty($orgAcademicTermsIds)) {
            $termSQLsubstring = "AND org_academic_periods_id IN (:orgAcademicTermsIds)";
            $parameters['orgAcademicTermsIds'] = $orgAcademicTermsIds;
            $parameterTypes['orgAcademicTermsIds'] = Connection::PARAM_INT_ARRAY;
        } else {
            $termSQLsubstring = "";
        }

        if (isset($optionValue)) {
            $optionSQLsubstring = "AND metadata_value = :optionValue";
            $parameters['optionValue'] = $optionValue;
        } else {
            $optionSQLsubstring = "";
        }

        if (isset($optionMin) && isset($optionMax)) {
            if (!is_numeric($optionMin) || !is_numeric($optionMax)) {
                throw new ValidationException(['Range must be numeric.'], 'Range must be numeric.');
            }
            $optionRangeSQLsubstring = "AND metadata_value >= $optionMin AND metadata_value < $optionMax";
        } else {
            $optionRangeSQLsubstring = "";
        }

        if ($countFlag) {
            $selectSQLsubstring = 'SELECT COUNT(*) AS count';
        } else {
            $selectSQLsubstring = 'SELECT person_id, org_academic_year_id, org_academic_periods_id AS org_academic_terms_id, metadata_value';
        }

        $sql = "$selectSQLsubstring
                FROM person_org_metadata
                WHERE deleted_at IS NULL
                AND org_metadata_id = :orgMetadataId
                $yearSQLsubstring
                $termSQLsubstring
                $optionSQLsubstring
                $optionRangeSQLsubstring
                AND person_id IN (:studentIds);";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $result = $stmt->fetchAll();

        if ($countFlag) {
            $result = $result[0]['count'];
        }

        return $result;
    }


    /**
     * Returns a list of the options for the given profile item (org metadata), along with a count of the students
     * from the given list of students who have each metadata_value (for nonzero counts).
     * For year/term-specific profile items, only returns data for the given academic year/term.
     *
     * @param int $orgMetadataId
     * @param array $studentIds
     * @param int|null $orgAcademicYearId
     * @param int|null $orgAcademicTermsId
     * @param int|null $numberOfDecimals
     * @return array
     */
    public function getGroupedMetadataValuesByOrgMetadataAndStudentIds($orgMetadataId, $studentIds, $orgAcademicYearId = null, $orgAcademicTermsId = null, $numberOfDecimals = null)
    {
        $parameters = [
            'orgMetadataId' => $orgMetadataId,
            'studentIds' => $studentIds
        ];

        $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

        if (isset($orgAcademicYearId)) {
            $yearSQLsubstring = "AND org_academic_year_id = :orgAcademicYearId";
            $parameters['orgAcademicYearId'] = $orgAcademicYearId;
        } else {
            $yearSQLsubstring = "";
        }

        if (isset($orgAcademicTermsId)) {
            $termSQLsubstring = "AND org_academic_periods_id = :orgAcademicTermsId";
            $parameters['orgAcademicTermsId'] = $orgAcademicTermsId;
        } else {
            $termSQLsubstring = "";
        }

        if (isset($numberOfDecimals)) {
            $maxDigits = 12;    // A guess about the maximum number of digits we'll need to allow, including the integer and decimal parts.
            $castSQLsubstring = "CAST(metadata_value AS decimal($maxDigits, :numberOfDecimals)) AS";
            $groupBySQLsubstring = "CAST(metadata_value AS decimal($maxDigits, :numberOfDecimals))";
            $parameters['numberOfDecimals'] = $numberOfDecimals;
            $parameterTypes['numberOfDecimals'] = 'integer';
        } else {
            $castSQLsubstring = "";
            $groupBySQLsubstring = "metadata_value";
        }

        $sql = "SELECT $castSQLsubstring metadata_value, org_academic_year_id, org_academic_periods_id AS org_academic_terms_id, COUNT(*) AS count
                FROM person_org_metadata
                WHERE deleted_at IS NULL
                AND org_metadata_id = :orgMetadataId
                $yearSQLsubstring
                $termSQLsubstring
                AND person_id IN (:studentIds)
                GROUP BY $groupBySQLsubstring, org_academic_year_id, org_academic_terms_id
                ORDER BY metadata_value + 0;";  // order them as numbers, not as strings

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
     * Returns the mean and standard deviation of the metadata values for the given students for the given profile item (org metadata).
     * For year- or term-specific profile items, only returns values for the given academic year/term.
     *
     * @param int $orgMetadataId
     * @param array $studentIds
     * @param int|null $orgAcademicYearId
     * @param int|null $orgAcademicTermsId
     * @return array
     */
    public function getMeanAndStdDevByOrgMetadataAndStudentIds($orgMetadataId, $studentIds, $orgAcademicYearId = null, $orgAcademicTermsId = null)
    {
        $studentPlaceholders = implode(',', array_fill(0, count($studentIds), '?'));
        $parameters = [$orgMetadataId];

        if (!empty($orgAcademicYearId)) {
            $yearSQLsubstring = "and org_academic_year_id = ?";
            $parameters[] = $orgAcademicYearId;
        } else {
            $yearSQLsubstring = "";
        }

        if (!empty($orgAcademicTermsId)) {
            $termSQLsubstring = "and org_academic_periods_id = ?";
            $parameters[] = $orgAcademicTermsId;
        } else {
            $termSQLsubstring = "";
        }

        $sql = "select round(avg(metadata_value), 2) as mean, round(std(metadata_value), 2) as std_dev
                from person_org_metadata
                where deleted_at is null
                and org_metadata_id = ?
                $yearSQLsubstring
                $termSQLsubstring
                and person_id in ($studentPlaceholders);";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $parameters = array_merge($parameters, $studentIds);
            $stmt->execute($parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();
        $results = $results[0];
        return $results;
    }


    /**
     * Returns student names, risk levels, class levels, and profile item values (all the data needed in the Profile Snapshot Report drilldown)
     * for the given students and profile item.
     * This function is intended to return a limited number of results for a paginated list.
     * The results will always be ordered by lastname, firstname, sometimes secondarily to another column ($sortBy).
     *
     * Note to future maintainers of this code: This function will not work as intended if gray risk level is ever indicated
     * by an id (rather than null) in the person table.
     *
     * @param int $orgMetadataId
     * @param int $classLevelMetadataId
     * @param array $studentIds
     * @param int|null $orgAcademicYearId
     * @param int|null $orgAcademicTermsId
     * @param int|null $optionValue - used to restrict the query to only a particular metadata_value (intended for categorical items)
     * @param int|float|null $optionMin - used with $optionMax to restrict the metadata_value (intended for numeric items)
     * @param int|float|null $optionMax
     * @param string|null $sortBy - column to sort by: "risk_color" or "name" or "class_level" or "profile_item_value"
     * @param int|null $recordsPerPage - maximum number of results to return
     * @param int|null $offset - index of the first result to return
     * @return array
     * @throws SynapseDatabaseException
     * @throws SynapseValidationException
     */
    public function getStudentNamesAndRiskLevelsAndClassLevelsAndMetadataValues($orgMetadataId, $classLevelMetadataId, $studentIds, $orgAcademicYearId = null, $orgAcademicTermsId = null, $optionValue = null, $optionMin = null, $optionMax = null, $sortBy = null, $recordsPerPage = null, $offset = null)
    {
        if (empty($studentIds)) {
            return [];
        }

        $parameters = [
            'studentIds' => $studentIds,
            'orgMetadataId' => $orgMetadataId,
            'classLevelMetadataId' => $classLevelMetadataId
        ];

        $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

        if (isset($orgAcademicYearId)) {
            $yearClause = "AND pom.org_academic_year_id = :orgAcademicYearId";
            $parameters['orgAcademicYearId'] = $orgAcademicYearId;
        } else {
            $yearClause = "";
        }

        if (isset($orgAcademicTermsId)) {
            $termClause = "AND pom.org_academic_periods_id = :orgAcademicTermsId";
            $parameters['orgAcademicTermsId'] = $orgAcademicTermsId;
        } else {
            $termClause = "";
        }

        if (isset($optionValue)) {
            $optionClause = "AND pom.metadata_value = :optionValue";
            $parameters['optionValue'] = $optionValue;
        } else {
            $optionClause = "";
        }

        // The usual parameter method won't work here because it tries to insert them as strings, which sometimes causes incorrect comparisons.
        // So, to prevent SQL injection, we'll make sure these parameters are numbers.
        // In the current usage of this function in the Profile Snapshot Report drilldown, they always should be numbers.
        if (isset($optionMin) && isset($optionMax)) {
            if (!is_numeric($optionMin) || !is_numeric($optionMax)) {
                throw new SynapseValidationException('Range must be numeric.');
            }
            $optionRangeClause = "AND pom.metadata_value >= $optionMin AND pom.metadata_value < $optionMax";
        } else {
            $optionRangeClause = "";
        }

        switch ($sortBy) {
            case 'name':
            case '+name':
                $orderByClause = 'ORDER BY p.lastname, p.firstname, p.username, p.id';
                break;
            case '-name':
                $orderByClause = 'ORDER BY p.lastname DESC, p.firstname DESC, p.username DESC, p.id DESC';
                break;
            case 'risk_color':
            case '+risk_color':
                // "Ascending" risk should start with green, which has the highest id in the database.
                $orderByClause = 'ORDER BY p.risk_level DESC, p.lastname, p.firstname, p.username, p.id';
                break;
            case '-risk_color':
                // "Descending" risk should start with red2, which has the lowest id in the database.
                // -{column} DESC sorts the column in ascending order with nulls last
                $orderByClause = 'ORDER BY -p.risk_level DESC, p.lastname, p.firstname, p.username, p.id';
                break;
            case 'class_level':
            case '+class_level':
                // -{column} DESC sorts the column in ascending order with nulls last
                $orderByClause = 'ORDER BY -emlv.list_value DESC, p.lastname, p.firstname, p.username, p.id';
                break;
            case '-class_level':
                $orderByClause = 'ORDER BY emlv.list_value DESC, p.lastname, p.firstname, p.username, p.id';
                break;
            case 'profile_item_value':
            case '+profile_item_value':
                // The profile item values should never be null, so we don't need to use a trick here.
                // The trick may not work anyway, since profile item values may not be numeric.
                $orderByClause = 'ORDER BY profile_item_value, p.lastname, p.firstname, p.username, p.id';
                break;
            case '-profile_item_value':
                $orderByClause = 'ORDER BY profile_item_value DESC, p.lastname, p.firstname, p.username, p.id';
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

        $sql = "SELECT
                    p.id AS student_id,
                    p.firstname,
                    p.lastname,
                    p.external_id,
                    p.username,
                    rl.risk_text AS risk_color,
                    rl.image_name AS risk_image_name,
                    emlv.list_name AS class_level,
                    pom.metadata_value AS profile_item_value
                FROM
                    person p
                        INNER JOIN
                    person_org_metadata pom
                            ON pom.person_id = p.id
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
                    AND pom.deleted_at IS NULL
                    AND p.id IN (:studentIds)
                    AND pom.org_metadata_id = :orgMetadataId
                    $yearClause
                    $termClause
                    $optionClause
                    $optionRangeClause
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
     * Get all institution specific profile items for a student based on permissions
     *
     * @param int $studentId
     * @param array $accessibleISPIds
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getStudentISPProfileInformation($studentId, $accessibleISPIds)
    {
        $parameters = [
            'studentId' => $studentId,
            'accessibleISPIds' => $accessibleISPIds
        ];

        $parameterTypes = [
            'accessibleISPIds' => Connection::PARAM_INT_ARRAY
        ];

        $sql = "
            SELECT
                om.id AS org_metadata_id,
                om.metadata_type,
                om.meta_name,
                pom.metadata_value,
                oay.name AS year_name,
                oat.name AS term_name,
                om.scope
            FROM
                org_metadata om
                    JOIN
                person_org_metadata pom ON pom.org_metadata_id = om.id
                    LEFT JOIN
                org_academic_year oay ON oay.id = pom.org_academic_year_id
                        AND oay.deleted_at IS NULL
                    LEFT JOIN
                org_academic_terms oat ON oat.org_academic_year_id = oay.id
                        AND oat.id = pom.org_academic_periods_id
                        AND oat.deleted_at IS NULL
            WHERE
                pom.person_id = :studentId
                    AND om.id IN (:accessibleISPIds)
                    AND om.deleted_at IS NULL
                    AND pom.deleted_at IS NULL
            ORDER BY om.sequence, oat.start_date DESC, oat.end_date DESC, oay.start_date DESC;
        ";

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
     * Gets all year and term information for an ISP.
     *
     * @param int $orgMetadataId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getYearAndTermByOrgMetadataId($orgMetadataId)
    {
        $parameters = [
            'orgMetadataId' => $orgMetadataId,
        ];
        $parameterTypes = [];

        $sql = "SELECT DISTINCT 
                    pom.org_academic_year_id,
                    oay.name AS year_name,
                    pom.org_academic_periods_id,
                    oat.name AS term_name
                FROM 
                    person_org_metadata pom
                      LEFT JOIN 
                    org_academic_year oay ON oay.id = pom.org_academic_year_id
                      AND oay.deleted_at IS NULL
                      LEFT JOIN 
                    org_academic_terms oat ON oat.id = pom.org_academic_periods_id
                      AND oat.deleted_at IS NULL
                WHERE 
                    pom.org_metadata_id = :orgMetadataId
                    AND pom.deleted_at IS NULL
                ORDER BY pom.org_academic_year_id DESC";
        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $resultSet;
    }

    /**
     * Gets Factor data for ISP
     *
     * @param int $orgMetaDataId
     * @param int $organizationId
     * @param int $personId
     * @param int $firstCohortId
     * @param int $firstSurveyId
     * @param string $surveyYearId
     * @param array $selectCaseQuery
     * @param array $studentIdsToInclude
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getFactorDataForISP($orgMetaDataId, $organizationId, $personId, $firstCohortId, $firstSurveyId, $surveyYearId, $orgAcademicTermId, $orgAcademicYearId, $selectCaseQuery, $studentIdsToInclude = null)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'facultyId' => $personId,
            'firstCohortId' => $firstCohortId,
            'firstSurveyId' => $firstSurveyId,
            'orgMetaDataId' => $orgMetaDataId,
            'surveyYearId' => $surveyYearId
        ];

        $parameterTypes = [
            'subpopulation1_category_values' => Connection::PARAM_INT_ARRAY,
            'subpopulation2_category_values' => Connection::PARAM_INT_ARRAY
        ];

        $termIdSQL = '';
        $yearIdSQL = '';

        if ($orgAcademicTermId != null) {
            $termIdSQL = " AND pom.org_academic_periods_id = :orgAcademicTermId ";
            $parameters['orgAcademicTermId'] = $orgAcademicTermId;
        }
        if ($orgAcademicYearId != null) {
            $yearIdSQL = " AND pom.org_academic_year_id = :orgAcademicYearId ";
            $parameters['orgAcademicYearId'] = $orgAcademicYearId;
        }

        if (!empty($studentIdsToInclude)) {
            $parameters['studentIdsToInclude'] = $studentIdsToInclude;
            $parameterTypes['studentIdsToInclude'] = Connection::PARAM_INT_ARRAY;
            $studentIdsCondition = 'AND pom.person_id IN (:studentIdsToInclude)';
        } else {
            $studentIdsCondition = '';
        }

        $caseQuery = $selectCaseQuery['case_query'];

        $parameters = array_merge($parameters, $selectCaseQuery['parameters']);

        $factorSql = "SELECT 
                        pfc.factor_id,
                        fl.short_name AS factor_name,
                        $caseQuery AS subpopulation_id,
                        pfc.person_id + FLOOR(RAND() * " . SynapseConstant::RANDOM_MULTIPLIER_SALT_FOR_PARTICIPANT_ID . ") + " . SynapseConstant::ADDED_SALT_FOR_PARTICIPANT_ID . " AS participant_id,
                        ROUND(pfc.mean_value, 2) AS factor_value
                    FROM
                        person_factor_calculated pfc
                        JOIN
                            org_faculty_student_permission_map ofspm ON ofspm.student_id = pfc.person_id
                            AND ofspm.faculty_id = :facultyId
                        JOIN
                            person_org_metadata pom ON pom.person_id = pfc.person_id
                            AND pom.org_metadata_id = :orgMetaDataId
                            $termIdSQL
                            $yearIdSQL
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
                            AND opsc.cohort = :firstCohortId
                            AND opsc.org_academic_year_id = oay.id
                        JOIN 
                            factor_lang fl ON fl.id = pfc.factor_id
                    WHERE
                        pfc.organization_id = :organizationId
                            AND pfc.survey_id = :firstSurveyId
                            AND pfc.deleted_at IS NULL
                            AND pom.deleted_at IS NULL
                            AND opd.deleted_at IS NULL
                            AND dq.deleted_at IS NULL
                            AND opsc.deleted_at IS NULL
                            AND fl.deleted_at IS NULL
                            AND oay.deleted_at IS NULL
                            $studentIdsCondition
                    GROUP BY pfc.factor_id, subpopulation_id, pfc.person_id
                    HAVING subpopulation_id IS NOT NULL";
        $resultSet = $this->executeQueryFetchAll($factorSql, $parameters, $parameterTypes);
        return $resultSet;
    }

    /**
     * Returns GPA data for ISP .
     *
     * @param int $orgMetaDataId
     * @param int $organizationId
     * @param int $personId
     * @param string $gpaYearId
     * @param int $orgAcademicTermId
     * @param int $orgAcademicYearId
     * @param array $selectCaseQuery
     * @param array $studentIdsToInclude
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getGpaDataForISP($orgMetaDataId, $organizationId, $personId, $gpaYearId, $orgAcademicTermId, $orgAcademicYearId, $selectCaseQuery, $studentIdsToInclude = null)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'facultyId' => $personId,
            'orgMetaDataId' => $orgMetaDataId,
            'gpaYearId' => $gpaYearId,
            'ebiMetaKey' => 'EndTermGPA'
        ];

        $parameterTypes = [
            'subpopulation1_category_values' => Connection::PARAM_INT_ARRAY,
            'subpopulation2_category_values' => Connection::PARAM_INT_ARRAY
        ];

        $termIdSQL = '';
        $yearIdSQL = '';

        if ($orgAcademicTermId != null) {
            $termIdSQL = " AND pom.org_academic_periods_id = :orgAcademicTermId ";
            $parameters['orgAcademicTermId'] = $orgAcademicTermId;
        }
        if ($orgAcademicYearId != null) {
            $yearIdSQL = " AND pom.org_academic_year_id = :orgAcademicYearId ";
            $parameters['orgAcademicYearId'] = $orgAcademicYearId;
        }

        if (!empty($studentIdsToInclude)) {
            $parameters['studentIdsToInclude'] = $studentIdsToInclude;
            $parameterTypes['studentIdsToInclude'] = Connection::PARAM_INT_ARRAY;
            $studentIdsCondition = 'AND pom.person_id IN (:studentIdsToInclude)';
        } else {
            $studentIdsCondition = '';
        }

        $caseQuery = $selectCaseQuery['case_query'];

        $parameters = array_merge($parameters, $selectCaseQuery['parameters']);
        $gpaSql = "SELECT 
                    oat.id AS org_academic_terms_id,
                    oat.name AS term_name,
                    $caseQuery AS subpopulation_id,
                    pem.person_id + FLOOR(RAND() * " . SynapseConstant::RANDOM_MULTIPLIER_SALT_FOR_PARTICIPANT_ID . ") + " . SynapseConstant::ADDED_SALT_FOR_PARTICIPANT_ID . " AS participant_id,
                    ROUND(pem.metadata_value,2) AS gpa_value
                    FROM
                    person_ebi_metadata pem
                    JOIN
                    org_faculty_student_permission_map ofspm ON ofspm.student_id = pem.person_id
                        AND ofspm.faculty_id = :facultyId
                    JOIN
                    person_org_metadata pom ON pom.person_id = pem.person_id
                        AND pom.org_metadata_id = :orgMetaDataId
                        $termIdSQL
                        $yearIdSQL
                    JOIN
                    ebi_metadata em ON em.id = pem.ebi_metadata_id
                        AND em.meta_key = :ebiMetaKey
                    JOIN
                    org_academic_terms oat ON oat.id = pem.org_academic_terms_id
                        AND oat.organization_id = :organizationId
                    JOIN
                    org_academic_year oay ON oay.id = pem.org_academic_year_id
                        AND year_id = :gpaYearId
                    WHERE pem.deleted_at IS NULL
                        AND pom.deleted_at IS NULL
                        AND em.deleted_at IS NULL
                        AND oat.deleted_at IS NULL
                        AND oay.deleted_at IS NULL
                        $studentIdsCondition
                    GROUP BY oat.id DESC, subpopulation_id, pem.person_id
                    HAVING subpopulation_id IS NOT NULL
                    ORDER BY oat.start_date ASC, oat.end_date ASC, subpopulation_id, pem.person_id";
        $resultSet = $this->executeQueryFetchAll($gpaSql, $parameters, $parameterTypes);
        return $resultSet;
    }

    /**
     * Gets Retention data for ISP .
     *
     * @param int $orgMetaDataId
     * @param int $organizationId
     * @param int $loggedInUserId
     * @param int $retentionTrackingYearId
     * @param int $orgAcademicTermId
     * @param int $orgAcademicYearId
     * @param array $selectCaseQuery
     * @param array $studentIdsToInclude
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getRetentionDataForISP($orgMetaDataId, $organizationId, $loggedInUserId, $retentionTrackingYearId, $orgAcademicTermId, $orgAcademicYearId, $selectCaseQuery, $studentIdsToInclude = null)
    {
        $parameters = [
            'orgMetaDataId' => $orgMetaDataId,
            'organizationId' => $organizationId,
            'personId' => $loggedInUserId,
            'retentionTrackingYearId' => $retentionTrackingYearId
        ];

        $parameterTypes = [
            'subpopulation1_category_values' => Connection::PARAM_INT_ARRAY,
            'subpopulation2_category_values' => Connection::PARAM_INT_ARRAY
        ];

        $termIdSQL = '';
        $yearIdSQL = '';

        if ($orgAcademicTermId != null) {
            $termIdSQL = " AND pom.org_academic_periods_id = :orgAcademicTermId ";
            $parameters['orgAcademicTermId'] = $orgAcademicTermId;
        }

        if ($orgAcademicYearId != null) {
            $yearIdSQL = " AND pom.org_academic_year_id = :orgAcademicYearId ";
            $parameters['orgAcademicYearId'] = $orgAcademicYearId;
        }

        if (isset($selectCaseQuery['parameterTypes'])) {
            $parameterTypes = array_merge($parameterTypes, $selectCaseQuery['parameterTypes']);
        }

        if (!empty($studentIdsToInclude)) {
            $parameters['studentIdsToInclude'] = $studentIdsToInclude;
            $parameterTypes['studentIdsToInclude'] = Connection::PARAM_INT_ARRAY;
            $studentIdsCondition = 'AND pom.person_id IN (:studentIdsToInclude)';
        } else {
            $studentIdsCondition = '';
        }


        $caseQuery = $selectCaseQuery['case_query'];
        $parameters = array_merge($parameters, $selectCaseQuery['parameters']);
        $sql = "SELECT $caseQuery AS subpopulation_id,
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
                          person_org_metadata pom 
                          ON pom.person_id = ofspm.student_id
                       INNER JOIN
                          retention_completion_variable_name rcvn 
                          ON opsrwdcv.years_from_retention_track = rcvn.years_from_retention_track
                       INNER JOIN 
                          org_permissionset op 
                          ON ofspm.permissionset_id = op.id
                WHERE  opsrwdcv.retention_tracking_year = :retentionTrackingYearId
                       AND pom.org_metadata_id = :orgMetaDataId
                       AND opsrwdcv.organization_id = :organizationId
                       AND op.retention_completion = 1
                       $termIdSQL
                       $yearIdSQL
                       AND pom.deleted_at IS NULL
                       AND rcvn.deleted_at IS NULL
                       AND op.deleted_at IS NULL
                       $studentIdsCondition
                GROUP BY subpopulation_id, opsrwdcv.person_id, opsrwdcv.retention_tracking_year, rcvn.name_text
                HAVING subpopulation_id IS NOT NULL";
        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $resultSet;
    }

    /**
     * This method will build and execute SQL query for mandatory filters when use has selected an ISP,
     * to get list of student IDS (person_id) for comparison report.
     *
     * @param int $organizationId
     * @param int $orgMetadataId
     * @param array $whereClause - Includes both sub-population's selected values in WHERE clause SQL by calling createWhereClauseForIspOrIsq
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getStudentIdsListBasedOnISPSelection($organizationId, $orgMetadataId, $whereClause)
    {
        $parameters = [
            'orgMetaDataId' => $orgMetadataId,
            'organizationId' => $organizationId,
        ];

        $parameterTypes = [
            'category_values' => Connection::PARAM_INT_ARRAY,
        ];

        $whereQuery = '';

        if (!empty($whereClause)) {
            $whereQuery = " AND " . $whereClause['where_query'];
            $parameters = array_merge($parameters, $whereClause['parameters']);
        }

        $sql = "SELECT 
                      DISTINCT pom.person_id
                FROM
                    person_org_metadata pom
                    INNER JOIN org_person_student ops ON ops.person_id = pom.person_id
                WHERE
                    ops.organization_id = :organizationId
                    AND pom.org_metadata_id = :orgMetaDataId
                    $whereQuery
                    AND pom.deleted_at IS NULL
                    AND ops.deleted_at IS NULL";
        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return array_column($resultSet, 'person_id');
    }

}
