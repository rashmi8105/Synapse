<?php
namespace Synapse\SurveyBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;


class WessLinkRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSurveyBundle:WessLink';

    // TODO: Remove this function when the /surveys/deprecated API is removed.
    public function getStudentSurveyList($cohortCode, $orgId, $studentView = false, $personId = false)
    {
        $em = $this->getEntityManager();
        $sql = "select sl.name as survey_name, wl.survey_id as survey_id, opssl.survey_completion_status as status, wl.open_date as open_date, wl.close_date as close_date, opssl.survey_link
		from wess_link wl
		left join survey s on s.id = wl.survey_id
		left join survey_lang sl on sl.survey_id = s.id
		left join org_person_student_survey_link opssl on opssl.survey_id = wl.survey_id			
		where wl.cohort_code IN ( $cohortCode )
		and wl.org_id = $orgId and opssl.person_id = $personId  ";
        if ($studentView) {
            $sql .= " and opssl.org_academic_year_id IN (select id from org_academic_year where start_date <= now() and end_date >= now() and organization_id = $orgId)";
        }

        //TODO: Figure out when to add this back in
        // $sql .= " and (opssl.survey_completion_status  IS NULL or opssl.survey_completion_status not in ('CompletedAll'))";
        $sql .= " order by wl.open_date desc";


        $resultSet = $em->getConnection()->fetchAll($sql);
        return $resultSet;
    }


    public function getLatestSurvey($organization)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('wl.id as id', 'IDENTITY(wl.survey) as survey_id');
        $qb->from('SynapseSurveyBundle:WessLink', 'wl');
        $qb->where('wl.organization = :organization');
        $qb->andWhere('wl.cohortCode = :cohortCode');
        $qb->andWhere('wl.closeDate IS NOT NULL');
        $qb->setParameters(array(
            'organization' => $organization,
            'cohortCode' => 1,
        ));
        $qb->orderBy('wl.closeDate', 'DESC');
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }


    public function getCohortDetails($orgId, $cohortCodeId, $yearId, $pageNo = NULL, $offset = NULL)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('distinct(sl.name) as survey_name', 'wl.id as id', 'IDENTITY(wl.survey) as survey_id', 'wl.cohortCode as cohort_code', 'wl.openDate as open_date', 'wl.closeDate as close_date', 'wl.wessAdminLink as wess_admin_link', 'wl.status as status');
        $qb->from('SynapseSurveyBundle:WessLink', 'wl');
        $qb->LEFTJoin('SynapseCoreBundle:Survey', 's', \Doctrine\ORM\Query\Expr\Join::WITH, 'wl.survey = s.id');
        $qb->LEFTJoin('SynapseCoreBundle:SurveyLang', 'sl', \Doctrine\ORM\Query\Expr\Join::WITH, 'sl.survey = s.id');
        $qb->where('wl.organization = :orgId');
        //$qb->andWhere("wl.status = 'closed'");
        if ($yearId != '' && $cohortCodeId != '') {
            $qb->andWhere('wl.year = :yearId');
            $qb->andWhere('wl.cohortCode = :cohortCodeId');
            $qb->setParameters(array(
                'yearId' => $yearId,
                'cohortCodeId' => $cohortCodeId,
                'orgId' => $orgId
            ));
        } elseif ($yearId != '' && $cohortCodeId == '') {
            $qb->andWhere('wl.year = :yearId');
            $qb->setParameters(array(
                'yearId' => $yearId,
                'orgId' => $orgId
            ));
        } elseif ($yearId == '' && $cohortCodeId != '') {
            $qb->andWhere('wl.cohortCode = :cohortCodeId');
            $qb->setParameters(array(
                'cohortCodeId' => $cohortCodeId,
                'orgId' => $orgId
            ));
        } else {
            $qb->setParameters(array(
                'orgId' => $orgId
            ));
        }
        if ($pageNo != NULL || $offset != NULL) {
            $startPoint = ($pageNo * $offset) - $offset;
            $qb->setFirstResult($startPoint);
            $qb->setMaxResults($offset);
        }
        $qb->groupBy('cohort_code');
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }

    // TODO: Remove this function once we're sure the API /surveys/{surveyId}/responses/data isn't being called anymore.
    public function getSurveyCohortDetailsSurveyAll($yearId, $orgId, $studentsArr = null, $surveyId)
    {
        try {
            $em = $this->getEntityManager();
            if (count($studentsArr) > 0) {
                $query = "
SELECT
    ws.cohort_code AS cohort_code,
    sl.name AS name,
    ws.org_id AS org,
    ws.survey_id AS survey,
    count(osl.person_id) AS total_students,
    ws.open_date AS open_date,
    ws.close_date AS close_date
FROM
    org_person_student_survey_link osl
        LEFT JOIN
    wess_link ws ON (ws.org_id = osl.org_id
        AND ws.survey_id = osl.survey_id
        AND ws.cohort_code = osl.cohort)
        LEFT JOIN
    survey_lang sl ON osl.survey_id = sl.survey_id
        LEFT JOIN
    org_person_student ops ON (ops.organization_id = ws.org_id
        AND ops.person_id = osl.person_id)
        LEFT JOIN
    org_person_student_survey opss ON (opss.person_id = ops.person_id
        AND opss.survey_id = :surveyId
        AND opss.organization_id = :orgId)
WHERE
    ws.org_id = :orgId AND ws.survey_id = :surveyId
        AND ws.year_id = :yearId
        AND ops.person_id IN (:students)
        AND osl.survey_completion_status IN ('InProgress' , 'Assigned',
        'CompletedAll',
        'CompletedMandatory')
        AND ((osl.Has_Responses IN ('Yes' , 'No')
        AND opss.receive_survey = 1)
        OR (osl.Has_Responses = 'Yes'
        AND opss.receive_survey = 0))
        AND osl.deleted_at IS NULL
        AND ops.deleted_at IS NULL
        AND ws.deleted_at IS NULL
        AND sl.deleted_at IS NULL
GROUP BY ws.cohort_code
        ";
            } else {
                $query = "
SELECT
    ws.cohort_code AS cohort_code,
    sl.name AS name,
    ws.org_id AS org,
    ws.survey_id AS survey,
    count(osl.person_id) AS total_students,
    ws.open_date AS open_date,
    ws.close_date AS close_date
FROM
    org_person_student_survey_link osl
        LEFT JOIN
    wess_link ws ON (ws.org_id = osl.org_id
        AND ws.survey_id = osl.survey_id
        AND ws.cohort_code = osl.cohort)
        LEFT JOIN
    survey_lang sl ON osl.survey_id = sl.survey_id
        LEFT JOIN
    org_person_student_survey opss ON (opss.person_id = osl.person_id
        AND opss.survey_id = :surveyId
        AND opss.organization_id = :orgId)
WHERE
    ws.org_id = :orgId AND ws.survey_id = :surveyId
        AND ws.year_id = :yearId
        AND osl.survey_completion_status IN ('InProgress' , 'Assigned',
        'CompletedAll',
        'CompletedMandatory')
        AND ((osl.Has_Responses IN ('Yes' , 'No')
        AND opss.receive_survey = 1)
        OR (osl.Has_Responses = 'Yes'
        AND opss.receive_survey = 0))
        AND osl.deleted_at IS NULL
        AND ws.deleted_at IS NULL
        AND sl.deleted_at IS NULL
GROUP BY ws.cohort_code
        ";
            }
            $parameters = [
                'surveyId' => $surveyId,
                'orgId' => $orgId,
                'yearId' => "$yearId",
                'students' => $studentsArr
            ];
            $personIdStudents = array('students' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY);
            $stmt = $em->getConnection()->executeQuery($query, $parameters, $personIdStudents);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $stmt->fetchAll();
    }


    /**
     * Get the list of surveys in the given cohort and year.
     * @param int $orgId
     * @param int $cohortId
     * @param int|string $yearId - The externally facing ID for the given year (Ex: "201516"). (***NOT*** org academic year ID.)
     * @return array
     */
    public function getSurveysForCohortAndYear($orgId, $cohortId, $yearId)
    {
        $sql = "SELECT
                    wl.survey_id
                FROM
                    wess_link wl
                WHERE
                    wl.org_id = :organization
                    AND wl.cohort_code = :cohortCode
                    AND wl.year_id = :yearId
                    AND wl.status IN ('launched','closed')
                    AND wl.deleted_at IS NULL
                ORDER BY wl.survey_id ASC";
        try {
            $parameters = ['cohortCode' => $cohortId, 'organization' => $orgId, 'yearId' => $yearId];
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $results = $stmt->fetchAll();
        return $results;
    }

    public function getFacultySurveyDetails($orgId, $studentsArr, $dashboard = false)
    {
        if (count($studentsArr) > 0)
            $students = implode(",", $studentsArr);
        else
            $students = "-1";

        if ($dashboard)
            $whereStatus = "AND ws.`status` IN ('closed','launched')";
        else
            $whereStatus = "";

        $em = $this->getEntityManager();
        $query = "
		SELECT
		group_concat(distinct(ws.cohort_code) ORDER BY ws.cohort_code) as all_cohorts,
		sl.name as survey_name,
		sl.lang_id,
		ws.survey_id as survey_id,
		ws.status as survey_status,
		ws.year_id,
		ws.open_date as open_date,
		ws.close_date as close_date
		FROM org_person_student_survey_link osl
		LEFT JOIN wess_link ws ON (ws.org_id = osl.org_id AND ws.survey_id = osl.survey_id AND ws.cohort_code = osl.cohort)
		LEFT JOIN survey_lang sl ON osl.survey_id = sl.survey_id		
		LEFT JOIN org_person_student_cohort opsc ON (opsc.cohort = ws.cohort_code and opsc.organization_id = ws.org_id and opsc.person_id = osl.person_id and opsc.org_academic_year_id = (select id from org_academic_year where year_id = ws.year_id and organization_id = $orgId and deleted_at is null))
		WHERE ws.org_id = $orgId
		AND opsc.person_id IN ($students)
		AND osl.survey_completion_status IN ('Assigned', 'CompletedAll', 'CompletedMandatory')
		$whereStatus
		group by ws.survey_id
		order by ws.open_date desc
		";

        $resultSet = $em->getConnection()->fetchAll($query);
        return $resultSet;
    }


    /**
     * Returns a lookup table of surveys for the given organization, where the keys are the survey_ids and the values are
     * associative arrays with keys 'survey_name' and 'included_in_persist_midyear_reporting'.
     * If $yearId and/or $status are set, uses these values to restrict the data returned.
     *
     * @param int $orgId
     * @param int|null $yearId - an identifier such as 201516
     * @param string|null $status - 'open' or 'closed' or 'ready' or 'launched'
     * @return array
     */
    public function getSurveysAndNamesForOrganization($orgId, $yearId = null, $status = null)
    {
        $parameters = [':orgId' => $orgId];

        if ($yearId) {
            $yearString = 'and wl.year_id = :yearId';
            $parameters[':yearId'] = $yearId;
        } else {
            $yearString = '';
        }

        if ($status) {
            $statusString = 'and wl.status = :status';
            $parameters[':status'] = $status;
        } else {
            $statusString = '';
        }

        $sql = "select distinct wl.survey_id, sl.name, survey.included_in_persist_midyear_reporting
                from wess_link wl
                inner join survey_lang sl on sl.survey_id = wl.survey_id
                inner join survey on survey.id = wl.survey_id
                where wl.org_id = :orgId
                $yearString
                $statusString;";


        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute($parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $records = $stmt->fetchAll();
        $lookupTable = [];
        foreach ($records as $record) {
            $lookupTable[$record['survey_id']] = [
                'survey_name' => $record['name'],
                'included_in_persist_midyear_reporting' => $record['included_in_persist_midyear_reporting']
            ];
        }

        return $lookupTable;
    }


    /**
     * gets all survey and cohorts information for a coordinator of an organization. This ignores the permission checks
     * for a person.
     *
     * @param int $orgId
     * @param int $orgAcademicYearId
     * @param string $surveyStatus
     * @param int $surveyId
     * @return array
     */
    public function getSurveysAndCohortsForOrganizationWithoutPermissionCheck($orgId, $orgAcademicYearId, $surveyStatus, $surveyId)
    {

        $parameters = ['orgId' => $orgId];

        if ($orgAcademicYearId) {
            $parameters['orgAcademicYearId'] = $orgAcademicYearId;
            $orgAcademicYearString = ' AND oay.id = :orgAcademicYearId ';
        } else {
            $orgAcademicYearString = '';
        }

        if ($surveyStatus) {
            $parameters['status'] = $surveyStatus;
            $surveyStatusString = ' AND wl.status IN (:status) ';
            $parameterTypes = ['status' => Connection::PARAM_STR_ARRAY];
        } else {
            $surveyStatusString = '';
            $parameterTypes = [];
        }

        if ($surveyId) {
            $parameters['surveyId'] = $surveyId;
            $surveyIdString = ' AND wl.survey_id = :surveyId ';
        } else {
            $surveyIdString = '';
        }


        $sql = "SELECT
                    oay.id AS org_academic_year_id,
                    wl.year_id,
                    oay.name AS year_name,
                    wl.survey_id,
                    sl.name AS survey_name,
                    wl.cohort_code AS cohort,
                    ocn.cohort_name,
                    wl.status,
                    wl.open_date,
                    wl.close_date
                FROM
                    wess_link wl
                        JOIN
                    survey_lang sl
                            ON wl.survey_id = sl.survey_id
                        JOIN
                    org_academic_year oay
                            ON oay.organization_id = wl.org_id
                            AND wl.year_id = oay.year_id
                        JOIN
                    org_cohort_name ocn
                            ON ocn.organization_id = wl.org_id
                            AND ocn.cohort = wl.cohort_code
                            AND oay.id = ocn.org_academic_year_id
                WHERE
                    wl.org_id = :orgId
                    AND wl.deleted_at IS NULL
                    AND sl.deleted_at IS NULL
                    AND oay.deleted_at IS NULL
                    AND ocn.deleted_at IS NULL
                    $orgAcademicYearString
                    $surveyStatusString
                    $surveyIdString
                ORDER BY wl.survey_id DESC, wl.cohort_code ASC;";

        $records = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);

        return $records;
    }

    /**
     * This gets survey information without returning any completion data. It uses completion data.
     *
     * @param int $orgId
     * @param int $loggedInUserId
     * @param int $orgAcademicYearId
     * @param string $surveyStatus
     * @param int $surveyId
     *
     * Note: Does not have "hasCoordinatorAccess" flag as getSurveysAndCohortsForOrganizationWithoutPermissionCheck
     *       should be called if the user has coordinator access.
     *
     * @return array
     */
    public function getSurveysAndCohortsForOrganizationWithoutCompletionData($orgId, $loggedInUserId, $orgAcademicYearId, $surveyStatus, $surveyId)
    {
        $parameters = ['orgId' => $orgId, 'loggedInUserId' => $loggedInUserId];

        if ($orgAcademicYearId) {
            $parameters['orgAcademicYearId'] = $orgAcademicYearId;
            $orgAcademicYearString = ' AND oay.id = :orgAcademicYearId ';
        } else {
            $orgAcademicYearString = '';
        }

        if ($surveyStatus) {
            $parameters['status'] = $surveyStatus;
            $surveyStatusString = ' AND wl.status IN (:status) ';
            $parameterTypes = ['status' => Connection::PARAM_STR_ARRAY];
        } else {
            $surveyStatusString = '';
            $parameterTypes = [];
        }

        if ($surveyId) {
            $parameters['surveyId'] = $surveyId;
            $surveyIdString = ' AND wl.survey_id = :surveyId ';
        } else {
            $surveyIdString = '';
        }


        $sql = "
            SELECT
                oay.id AS org_academic_year_id,
                wl.year_id,
                oay.name AS year_name,
                wl.survey_id,
                sl.name AS survey_name,
                wl.cohort_code AS cohort,
                ocn.cohort_name,
                wl.status,
                wl.open_date,
                wl.close_date
            FROM
                wess_link wl
                    JOIN
                survey_lang sl ON wl.survey_id = sl.survey_id
                    JOIN
                org_academic_year oay ON oay.organization_id = wl.org_id
                    AND oay.year_id = wl.year_id
                    JOIN
                org_cohort_name ocn ON ocn.cohort = wl.cohort_code
                    AND ocn.org_academic_year_id = oay.id
            WHERE
                wl.org_id =  :orgId
                    AND wl.status IN ('launched' , 'closed')
                    AND wl.deleted_at IS NULL
                    AND sl.deleted_at IS NULL
                    AND oay.deleted_at IS NULL
                    AND ocn.deleted_at IS NULL
                    $surveyStatusString
                    $surveyIdString
                    $orgAcademicYearString
                    AND EXISTS(
                    SELECT
                        1
                    FROM
                        org_person_student_cohort opsc
                            JOIN
                        org_person_student_survey_link opssl ON opssl.person_id = opsc.person_id
                            AND opssl.cohort = opsc.cohort
                            JOIN org_faculty_student_permission_map ofspm ON ofspm.student_id = opssl.person_id
                                                                               AND ofspm.faculty_id = :loggedInUserId
                            JOIN
                        org_person_student_survey opss ON opss.person_id = opssl.person_id
                            JOIN
                        org_person_student_year opsy ON opsy.person_id = opssl.person_id
                            AND opsc.deleted_at IS NULL
                            AND opssl.deleted_at IS NULL
                            AND opss.deleted_at IS NULL
                            AND opsy.deleted_at IS NULL
                            AND (opss.receive_survey = 1
                            OR opssl.Has_Responses = 'Yes')
                    WHERE
                        opsc.cohort = wl.cohort_code
                            AND opsc.org_academic_year_id = oay.id
                            AND opssl.survey_id = wl.survey_id
                            AND opssl.org_academic_year_id = oay.id
                            AND opss.survey_id = wl.survey_id
                            AND opsy.org_academic_year_id = oay.id)
            GROUP BY year_id, survey_id, cohort
            ORDER BY survey_id DESC, cohort ASC";


        $records = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);


        return $records;
    }

    /**
     * Gets the survey data with completion data. This query should only be called if you need completion data.
     * Permission checks are optional.
     *
     * If you don't need completion data
     * use getSurveysAndCohortsForOrganizationWithoutCompletionData if you need permission checks
     * or use getSurveysAndCohortsForOrganizationWithoutPermissionCheck if the user is a coordinator.
     *
     * @param int $orgId
     * @param int $loggedInUserId
     * @param null|int $orgAcademicYearId
     * @param null|array $surveyStatus
     * @param null|int $surveyId
     * @param boolean $hasCoordinatorAccess
     * @return array
     */
    public function getSurveysAndCohortsForOrganizationWithCompletionData($orgId, $loggedInUserId, $orgAcademicYearId = null, $surveyStatus = null, $surveyId = null, $hasCoordinatorAccess = false)
    {
        $parameters = ['organizationId' => $orgId];

        if ($orgAcademicYearId) {
            $parameters['orgAcademicYearId'] = $orgAcademicYearId;
            $orgAcademicYearString = ' AND oay.id = :orgAcademicYearId ';
        } else {
            $orgAcademicYearString = '';
        }

        if ($surveyStatus) {
            $parameters['status'] = $surveyStatus;
            $surveyStatusString = ' AND wl.status IN (:status) ';
            $parameterTypes = ['status' => Connection::PARAM_STR_ARRAY];
        } else {
            $surveyStatusString = '';
            $parameterTypes = [];
        }

        if ($surveyId) {
            $parameters['surveyId'] = $surveyId;
            $surveyIdString = ' AND wl.survey_id = :surveyId ';
        } else {
            $surveyIdString = '';
        }

        if (!$hasCoordinatorAccess) {
            $parameters['loggedInUserId'] = $loggedInUserId;
            $permissionJoinString = 'JOIN org_faculty_student_permission_map ofspm ON ofspm.student_id = opsc.person_id
        AND ofspm.faculty_id = :loggedInUserId';
        } else {
            $permissionJoinString = '';
        }
        $sql = "
            SELECT
                surveyCohortInformation.org_academic_year_id,
                surveyCohortInformation.year_id,
                surveyCohortInformation.year_name,
                surveyCohortInformation.survey_id,
                surveyCohortInformation.survey_name,
                surveyCohortInformation.cohort,
                surveyCohortInformation.cohort_name,
                surveyCohortInformation.status,
                surveyCohortInformation.open_date,
                surveyCohortInformation.close_date,
                SUM(CASE
                    WHEN (Has_Responses = 'Yes' AND survey_completion_status IN ('CompletedMandatory','CompletedAll')) THEN 1
                    ELSE 0
                END) AS students_responded_count,
                COUNT(student_count) AS student_count
            FROM
                (SELECT
                    oay.id AS org_academic_year_id,
                        wl.year_id,
                        oay.name AS year_name,
                        wl.survey_id,
                        sl.name AS survey_name,
                        wl.cohort_code AS cohort,
                        ocn.cohort_name,
                        wl.status,
                        wl.open_date,
                        wl.close_date
                FROM
                    wess_link wl
                INNER JOIN survey_lang sl ON wl.survey_id = sl.survey_id
                INNER JOIN org_academic_year oay ON oay.organization_id = wl.org_id
                    AND oay.year_id = wl.year_id
                INNER JOIN org_cohort_name ocn ON ocn.cohort = wl.cohort_code
                    AND ocn.org_academic_year_id = oay.id
                WHERE
                    wl.org_id = :organizationId
                        $surveyStatusString
                        $surveyIdString
                        $orgAcademicYearString
                        AND wl.deleted_at IS NULL
                        AND sl.deleted_at IS NULL
                        AND oay.deleted_at IS NULL
                        AND ocn.deleted_at IS NULL) AS surveyCohortInformation
                    JOIN
                (SELECT DISTINCT
                    opsc.org_academic_year_id,
                        opssl.survey_id,
                        opsc.cohort,
                        Has_Responses,
                         opsc.person_id AS student_count,
                         opssl.survey_completion_status
                FROM
                    org_person_student_cohort opsc
                    $permissionJoinString
                INNER JOIN org_person_student_survey_link opssl ON opssl.person_id = opsc.person_id
                    AND opssl.cohort = opsc.cohort
                INNER JOIN org_person_student_survey opss ON opss.person_id = opssl.person_id
                    AND opss.survey_id = opssl.survey_id
                INNER JOIN org_person_student_year opsy ON opsy.person_id = opssl.person_id
                    AND opsy.org_academic_year_id = opsc.org_academic_year_id
                    AND opsc.deleted_at IS NULL
                    AND opssl.deleted_at IS NULL
                    AND opss.deleted_at IS NULL
                    AND opsy.deleted_at IS NULL
                    AND (opss.receive_survey = 1
                    OR opssl.Has_Responses = 'Yes')) AS studentCount ON surveyCohortInformation.org_academic_year_id = studentCount.org_academic_year_id
                    AND surveyCohortInformation.survey_id = studentCount.survey_id
                    AND surveyCohortInformation.cohort = studentCount.cohort
                GROUP BY surveyCohortInformation.org_academic_year_id, surveyCohortInformation.survey_id, surveyCohortInformation.cohort
                         ORDER BY surveyCohortInformation.survey_id DESC, surveyCohortInformation.cohort ASC;";

        $records = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);

        return $records;
    }


    /**
     * Returns basic survey data (survey ids, names, and years) for the given organization,
     * ordered by year_id and then by survey_id, both with the most recent listed first.
     * If the statusArray parameter is used, surveys will be included if they have one of the listed statuses
     * for at least one cohort in the organization.
     *
     * @param int $orgId
     * @param int|null $orgAcademicYearId
     * @param array $statusArray
     * @return array
     */
    public function getSurveysForOrganization($orgId, $orgAcademicYearId = null, $statusArray = [])
    {
        $parameters = [':orgId' => $orgId];
        $parameterTypes = [];

        if ($orgAcademicYearId) {
            $yearString = 'AND oay.id = :orgAcademicYearId';
            $parameters[':orgAcademicYearId'] = $orgAcademicYearId;
        } else {
            $yearString = '';
        }

        if ($statusArray) {
            $statusString = 'AND wl.status IN (:statusArray)';
            $parameters[':statusArray'] = $statusArray;
            $parameterTypes[':statusArray'] = Connection::PARAM_STR_ARRAY;
        } else {
            $statusString = '';
        }

        $sql = "SELECT DISTINCT oay.id AS org_academic_year_id, oay.year_id, wl.survey_id, sl.name AS survey_name
                FROM wess_link wl
                INNER JOIN survey_lang sl
                    ON sl.survey_id = wl.survey_id
                INNER JOIN org_academic_year oay
                    ON oay.year_id = wl.year_id
                    AND oay.organization_id = wl.org_id
                WHERE wl.deleted_at IS NULL
                    AND sl.deleted_at IS NULL
                    AND oay.deleted_at IS NULL
                    AND wl.org_id = :orgId
                    $yearString
                    $statusString
                ORDER BY wl.year_id DESC, wl.survey_id DESC;";

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
     * Returns data about each cohort-survey combination for the given organization.
     * If $purpose is "isq", only returns cohort and survey combinations which have ISQs.
     *
     * @param int $organizationId
     * @param string $purpose -- "isq" or "survey_setup"
     * @param int|null $orgAcademicYearId
     * @param array|null $surveyStatus - if set, typically is ["launched", "closed"]
     * @return array
     */
    public function getCohortsAndSurveysForOrganizationForSetup($organizationId, $purpose, $orgAcademicYearId = null, $surveyStatus = null)
    {
        $parameters = ['orgId' => $organizationId];

        $parameterTypes = [];

        if ($purpose == 'isq') {

            $isqJoin = 'INNER JOIN
                    org_question oq
                            ON oq.organization_id = wl.org_id
                            AND oq.survey_id = wl.survey_id
                            AND oq.cohort = wl.cohort_code';

            $isqDeletedAt = 'AND oq.deleted_at IS NULL';

        } else {
            $isqJoin = '';
            $isqDeletedAt = '';
        }

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

        $orderByString = 'ORDER BY wl.year_id, ocn.cohort, wl.survey_id;';

        $sql = "SELECT DISTINCT
                    wl.year_id,
                    ocn.org_academic_year_id,
                    oay.name AS year_name,
                    ocn.cohort,
                    ocn.cohort_name,
                    wl.survey_id,
                    sl.name AS survey_name,
                    wl.status,
                    wl.open_date,
                    wl.close_date,
                    wl.wess_admin_link
                FROM
                    wess_link wl
                        INNER JOIN
                    survey_lang sl
                            ON sl.survey_id = wl.survey_id
                        INNER JOIN
                    org_cohort_name ocn
                            ON ocn.cohort = wl.cohort_code
                            AND ocn.organization_id = wl.org_id
                        INNER JOIN
                    org_academic_year oay
                            ON oay.id = ocn.org_academic_year_id
                            AND oay.year_id = wl.year_id
                    $isqJoin
                WHERE wl.org_id = :orgId
                    $yearString
                    $statusString
                    AND wl.deleted_at IS NULL
                    AND sl.deleted_at IS NULL
                    AND ocn.deleted_at IS NULL
                    AND oay.deleted_at IS NULL
                    $isqDeletedAt
                    $orderByString";
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
     * Returns all survey and cohort combinations for which the given permission sets allow access to ISQs.
     * The data returned also includes some extra information, such as the name and status of the survey.
     *
     * If $isAggregateReporting is true (e.g., in report filters), all permission sets will be included for determining ISQ access.
     * If $isAggregateReporting is false (e.g., in custom search), only individual permission sets will be included for determining ISQ access.
     *
     * @param int $orgId
     * @param array $permissionSetIds
     * @param bool $isAggregateReporting
     * @param int|null $orgAcademicYearId
     * @param array|null $surveyStatus - if set, typically is ["launched", "closed"]
     * @return array
     */
    public function getSurveysAndCohortsHavingAccessibleISQs($orgId, $permissionSetIds, $isAggregateReporting, $orgAcademicYearId = null, $surveyStatus = null)
    {
        $parameters = [
            'orgId' => $orgId,
            'permissionSetIds' => $permissionSetIds
        ];

        $parameterTypes = ['permissionSetIds' => Connection::PARAM_INT_ARRAY];

        if ($isAggregateReporting) {
            $accessLevelString = '';
        } else {
            $accessLevelString = 'AND op.accesslevel_ind_agg = 1';
        }

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

        $sql = "SELECT DISTINCT
                    wl.year_id,
                    ocn.org_academic_year_id,
                    oay.name AS year_name,
                    ocn.cohort,
                    ocn.cohort_name,
                    wl.survey_id,
                    sl.name AS survey_name,
                    wl.status,
                    wl.open_date,
                    wl.close_date
                FROM
                    wess_link wl
                        INNER JOIN
                    survey_lang sl
                            ON sl.survey_id = wl.survey_id
                        INNER JOIN
                    org_cohort_name ocn
                            ON ocn.cohort = wl.cohort_code
                            AND ocn.organization_id = wl.org_id
                        INNER JOIN
                    org_academic_year oay
                            ON oay.id = ocn.org_academic_year_id
                            AND oay.year_id = wl.year_id
                        INNER JOIN
                    org_permissionset_question opq
                            ON opq.organization_id = wl.org_id
                            AND opq.survey_id = wl.survey_id
                            AND opq.cohort_code = wl.cohort_code
                        INNER JOIN
                    org_permissionset op
                            ON op.organization_id = opq.organization_id
                            AND op.id = opq.org_permissionset_id
                WHERE wl.org_id = :orgId
                    AND opq.org_permissionset_id IN (:permissionSetIds)
                    $accessLevelString
                    $yearString
                    $statusString
                    AND wl.deleted_at IS NULL
                    AND sl.deleted_at IS NULL
                    AND ocn.deleted_at IS NULL
                    AND oay.deleted_at IS NULL
                    AND opq.deleted_at IS NULL
                    AND op.deleted_at IS NULL
                ORDER BY wl.survey_id DESC, ocn.cohort;";

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
     * Gets the latest survey close date for a faculty
     *
     * @param int $facultyId
     * @param int $organizationId
     * @return DateTime
     * @throws SynapseDatabaseException
     */
    public function getSurveyClosedDateForFaculty($facultyId, $organizationId)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
        ];

        $sql = "SELECT
                    wl.close_date
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op ON op.id = ofspm.permissionset_id
                        INNER JOIN
                    org_person_student_year opsy ON opsy.person_id = ofspm.student_id
                        AND opsy.organization_id = op.organization_id
                        INNER JOIN
                    org_person_student_survey_link opssl ON opssl.org_id = ofspm.org_id
                        AND opssl.person_id = ofspm.student_id
                        INNER JOIN
                    wess_link wl ON wl.org_id = op.organization_id
                        AND wl.survey_id = opssl.survey_id
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :organizationId
                    AND opssl.Has_Responses = 'Yes'
                    AND op.accesslevel_ind_agg = 1
                    AND wl.status = 'closed'
                    AND opsy.deleted_at IS NULL
                    AND opssl.deleted_at IS NULL
                    AND op.deleted_at IS NULL
                    AND wl.deleted_at IS NULL
                ORDER BY wl.close_date DESC 
                LIMIT 0, 1";
        $resultSet = $this->executeQueryFetchAll($sql, $parameters);
        if (!empty($resultSet)) {
            $result = $resultSet[0];
        } else {
            $result = $resultSet;
        }
        return $result;
    }

}