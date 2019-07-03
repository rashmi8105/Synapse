<?php
namespace Synapse\AcademicUpdateBundle\Repository;

use Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CoreBundle\Util\Constants\AcademicUpdateConstant;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;

class AcademicUpdateRequestRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseAcademicUpdateBundle:AcademicUpdateRequest';

    /**
     * Finds an entity by its primary key / identifier.
     * Override added to inform PhpStorm about the return type.
     *
     * @param int $id The identifier.
     * @param SynapseException | null $exception
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return AcademicUpdateRequest| null
     */
    public function find($id, $exception = null, $lockMode = null, $lockVersion = null)
    {
        $object = parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($object, $exception);
    }


    /**
     * @param array $criteria
     * @param SynapseException | null $exception
     * @param array|null $orderBy
     * @return AcademicUpdateRequest| null
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null)
    {
        $object = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($object, $exception);
    }

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @param
     *
     * @return AcademicUpdateRequest[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $exception = null)
    {
        $objectArray = parent::findBy($criteria, $orderBy, $limit, $offset);
        return $this->doObjectsExist($objectArray, $exception);
    }

    public function getMaxId()
    {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder();
        $query->select('MAX(s.id) AS max_id');
        $query->from('SynapseAcademicUpdateBundle:AcademicUpdateRequest', 's');

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Get all academic update request details based on $auRequestId and given filter values
     *
     * @param int $academicUpdateRequestId
     * @param string $filter
     * @param int $rowsReturned
     * @param int|null $offset
     * @param string|null $outputFormat
     * @param int $participationOrgAcademicYearId
     * @param string $currentDate - Y-m-d H:i:s formatted current datetime.
     * @return array|int
     * @throws SynapseDatabaseException
     */
    public function getAllAcademicUpdateRequestDetailsById($academicUpdateRequestId, $participationOrgAcademicYearId, $currentDate, $filter = 'all', $offset = null, $rowsReturned = null, $outputFormat = null)
    {
        $parameters = [
            'requestId' => $academicUpdateRequestId,
            'participationOrgAcademicYearId' => $participationOrgAcademicYearId,
            'currentDate' => $currentDate
        ];

        $parameterTypes = [];

        if ($filter == 'datasubmitted') {
            $filterCondition = " AND (au.failure_risk_level IS NOT NULL
                                OR au.grade IS NOT NULL
                                OR au.absence IS NOT NULL
                                OR au.comment IS NOT NULL
                                OR au.refer_for_assistance = 1)
                                AND au.status = 'closed' ";
        } elseif ($filter == 'nodata') {
            $filterCondition = " AND ((au.failure_risk_level IS NULL
                                AND au.grade IS NULL
                                AND au.absence IS NULL
                                AND au.comment IS NULL
                                AND (au.refer_for_assistance IS NULL OR au.refer_for_assistance = 0))
                                OR au.status <> 'closed') ";
        } else {
            $filterCondition = "";
        }

        if ($outputFormat != 'csv') {
            $limitStatement = ' LIMIT :offset , :rowsReturned ';
            $parameters['rowsReturned'] = $rowsReturned;
            $parameters['offset'] = $offset;
            $parameterTypes['rowsReturned'] = \PDO::PARAM_INT;
            $parameterTypes['offset'] = \PDO::PARAM_INT;

            $academicUpdateInformation = "
                au.status AS academic_update_status,
                au.failure_risk_level AS student_risk,
                au.grade AS student_grade,
                au.absence AS student_absences,
                au.comment AS student_comments,
                au.refer_for_assistance AS student_refer,
                au.send_to_student AS student_send,
            ";
        } else {
            $limitStatement = '';

            $academicUpdateInformation = "
                IF(aur.status = 'open' AND aur.due_date >= :currentDate, au.status, 'closed') AS academic_update_status,
                IF(au.status = 'closed', au.failure_risk_level, null) AS student_risk,
                IF(au.status = 'closed', au.grade, null) AS student_grade,
                IF(au.status = 'closed', au.absence, null) AS student_absences,
                IF(au.status = 'closed', au.comment, null) AS student_comments,
                IF(au.status = 'closed', au.refer_for_assistance, null) AS student_refer,
                IF(au.status = 'closed', au.send_to_student, null) AS student_send,
            ";
        }

        $sql = "
            SELECT 
                aur.id AS request_id,
                aur.name AS request_name,
                aur.description AS request_description,
                DATE_FORMAT(aur.request_date, '%m/%d/%Y') AS request_created,
                DATE_FORMAT(aur.due_date, '%m/%d/%Y') AS request_due,
                IF(aur.status = 'open' AND aur.due_date >= :currentDate, 'open', 'closed') AS request_status,
                p.firstname AS request_from_firstname,
                p.lastname AS request_from_lastname,
                CONCAT(p.firstname, ' ', p.lastname) AS request_from,
                au.is_submitted_without_change AS is_bypassed,
                au.id AS academic_update_id,
                student.id AS student_id,
                student.external_id AS student_external_id,
                student.firstname AS student_firstname,
                student.lastname AS student_lastname,
                opsy.is_active AS student_status,
                $academicUpdateInformation
                oc.course_section_id,
                oc.id AS course_id,
                oc.subject_code,
                oc.course_number,
                oc.course_name,
                oc.section_number  AS course_section_name,
                oc.dept_code AS department_name,
                oay.name AS academic_year_name,
                oat.name AS academic_term_name 
            FROM
                academic_update_request aur
                  INNER JOIN
                person p ON p.id = aur.person_id
                  INNER JOIN
                academic_update au ON aur.id = au.academic_update_request_id
                  INNER JOIN
                person student ON student.id = au.person_id_student
                  INNER JOIN
                org_person_student ops ON ops.organization_id = student.organization_id
                  AND ops.person_id = student.id
                  INNER JOIN
                org_courses oc ON oc.id = au.org_courses_id
                  INNER JOIN
                org_academic_year oay ON oay.id = oc.org_academic_year_id
                  INNER JOIN
                org_academic_terms oat ON oat.id = oc.org_academic_terms_id 
                  INNER JOIN
                org_person_student_year opsy ON opsy.organization_id = ops.organization_id
                    AND ops.person_id = opsy.person_id
            WHERE
                aur.id = :requestId
                AND opsy.org_academic_year_id = :participationOrgAcademicYearId
                AND aur.deleted_at IS NULL
                AND p.deleted_at IS NULL
                AND au.deleted_at IS NULL
                AND student.deleted_at IS NULL
                AND ops.deleted_at IS NULL
                AND oc.deleted_at IS NULL
                AND oay.deleted_at IS NULL
                AND oat.deleted_at IS NULL
                AND opsy.deleted_at IS NULL
                $filterCondition
            ORDER BY oc.course_name, oc.course_section_id, student.lastname, student.firstname, student.id
            $limitStatement ";

        $records = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $records;
    }

    /**
     * Gets the count of academic updates for the specific academic update request ID & count criteria
     *
     * @param int $academicUpdateRequestId
     * @param int $participationOrgAcademicYearId
     * @param bool $facultyRelatedStudentsFlag
     * @param null|int $facultyId
     * @param string $filter
     * @param bool $nonParticipantFlag
     * @param bool $academicUpdateCountFlag
     * @return int
     * @throws SynapseDatabaseException
     */
    public function getAcademicUpdatesCountByRequestId($academicUpdateRequestId, $participationOrgAcademicYearId, $facultyRelatedStudentsFlag = false, $facultyId = null, $filter = 'all', $nonParticipantFlag = false, $academicUpdateCountFlag = false)
    {
        $parameters = [
            'academicUpdateRequestId' => $academicUpdateRequestId,
            'participationOrgAcademicYearId' => $participationOrgAcademicYearId
        ];

        if ($filter == 'datasubmitted') {
            $filterClause = " AND (au.failure_risk_level IS NOT NULL
                       OR au.grade IS NOT NULL
                       OR au.absence IS NOT NULL
                       OR au.comment IS NOT NULL
                       OR au.refer_for_assistance = 1)
                       AND au.status = 'closed' ";
        } elseif ($filter == 'nodata') {
            $filterClause = " AND ((au.failure_risk_level IS NULL
                       AND au.grade IS NULL
                       AND au.absence IS NULL
                       AND au.comment IS NULL
                       AND (au.refer_for_assistance IS NULL OR au.refer_for_assistance = 0))
                       OR au.status <> 'closed') ";
        } else {
            $filterClause = '';
        }

        if ($nonParticipantFlag) {
            $selectStatement = ' COUNT(DISTINCT au.person_id_student) AS count ';
            $multiyearJoin = ' LEFT JOIN ';
            $multiyearWhereCondition = 'AND opsy.id IS NULL';
        } else {
            $selectStatement = ' COUNT(DISTINCT au.person_id_student) AS count ';
            $multiyearJoin = ' INNER JOIN ';
            $multiyearWhereCondition = '';
        }

        //Reset the select statement if the count of academic updates is requested instead of the student counts.
        if ($academicUpdateCountFlag) {
            $selectStatement = ' COUNT(DISTINCT au.id) AS count ';
        }


        //If the count should only include academic updates to which the faculty is related, include the join criteria.
        if ($facultyRelatedStudentsFlag) {
            $facultyJoin = '
                        INNER JOIN
                    academic_update_assigned_faculty auaf ON auaf.academic_update_id = au.id
                        ';
            $facultyWhereCondition = ' AND auaf.person_id_faculty_assigned = :facultyId ';
            $parameters['facultyId'] = $facultyId;
        } else {
            $facultyJoin = '';
            $facultyWhereCondition = '';
        }


        $sql = "SELECT
                    $selectStatement
                FROM
                    academic_update au
                       INNER JOIN
                    org_courses oc ON oc.id = au.org_courses_id
                        $multiyearJoin
                    org_person_student_year opsy
                        ON opsy.person_id = au.person_id_student
                        AND opsy.org_academic_year_id = :participationOrgAcademicYearId
                        AND opsy.deleted_at IS NULL
                        $facultyJoin
                WHERE
                    au.academic_update_request_id = :academicUpdateRequestId
                    $filterClause
                    $facultyWhereCondition
                    $multiyearWhereCondition
                    AND au.deleted_at IS NULL
                    AND oc.deleted_at IS NULL;
        ";

        $records = $this->executeQueryFetchAll($sql, $parameters);
        return (int)$records[0]['count'];
    }


    /**
     * Get all academic update request details based on $auRequestId, faculty id, and given filter for participating students.
     *
     * @param int $academicUpdateRequestId
     * @param int $facultyId
     * @param string $filter => defaults to 'all' academic Updates. Options include: 'datasubmitted' and 'nodata' **CASE SENSITIVE**
     * @param int|null $startPoint
     * @param int|null $limit
     * @param string|null $outputFormat
     * @param int $participationOrgAcademicYearId
     * @param string $currentDate - Y-m-d H:i:s formatted current datetime.
     * @throws SynapseDatabaseException
     * @return array|int
     */
    public function getAllAcademicUpdateRequestDetailsByIdForFaculty($academicUpdateRequestId, $facultyId, $participationOrgAcademicYearId, $currentDate, $filter = 'all', $startPoint = null, $limit = null, $outputFormat = null)
    {
        $parameters = [
            'academicUpdateRequestId' => $academicUpdateRequestId,
            'facultyId' => $facultyId,
            'participationOrgAcademicYearId' => $participationOrgAcademicYearId,
            'currentDate' => $currentDate
        ];

        $parameterTypes = [];

        if ($filter == 'datasubmitted') {
            $filterClause = " AND (au.failure_risk_level IS NOT NULL
                       OR au.grade IS NOT NULL
                       OR au.absence IS NOT NULL
                       OR au.comment IS NOT NULL
                       OR au.refer_for_assistance = 1)
                       AND au.status = 'closed' ";
        } elseif ($filter == 'nodata') {
            $filterClause = " AND ((au.failure_risk_level IS NULL
                       AND au.grade IS NULL
                       AND au.absence IS NULL
                       AND au.comment IS NULL
                       AND (au.refer_for_assistance IS NULL OR au.refer_for_assistance = 0))
                       OR au.status <> 'closed') ";
        } else {
            $filterClause = '';
        }

        if ($outputFormat != 'csv') {
            $limitClause = " LIMIT :limit OFFSET :startPoint ";
            $parameters['startPoint'] = (int)$startPoint;
            $parameters['limit'] = (int)$limit;
            $parameterTypes['startPoint'] = \PDO::PARAM_INT;
            $parameterTypes['limit'] = \PDO::PARAM_INT;

            $academicUpdateInformation = "
                au.status AS academic_update_status,
                au.failure_risk_level AS student_risk,
                au.grade AS student_grade,
                au.absence AS student_absences,
                au.comment AS student_comments,
                au.refer_for_assistance AS student_refer,
                au.send_to_student AS student_send,
            ";
        } else {
            $limitClause = '';

            $academicUpdateInformation = "
                IF(aur.status = 'open' AND aur.due_date >= :currentDate, au.status, 'closed') AS academic_update_status,
                IF(au.status = 'closed', au.failure_risk_level, null) AS student_risk,
                IF(au.status = 'closed', au.grade, null) AS student_grade,
                IF(au.status = 'closed', au.absence, null) AS student_absences,
                IF(au.status = 'closed', au.comment, null) AS student_comments,
                IF(au.status = 'closed', au.refer_for_assistance, null) AS student_refer,
                IF(au.status = 'closed', au.send_to_student, null) AS student_send,
            ";
        }

        $sql = "SELECT DISTINCT
                    aur.id AS request_id,
                    aur.name AS request_name,
                    aur.description AS request_description,
                    DATE_FORMAT(aur.request_date, '%m/%d/%Y') AS request_created,
                    DATE_FORMAT(aur.due_date, '%m/%d/%Y') AS request_due,
                    IF(aur.status = 'open' AND aur.due_date >= :currentDate, 'open', 'closed') AS request_status,
                    requester.firstname AS request_from_firstname,
                    requester.lastname AS request_from_lastname,
                    CONCAT(requester.firstname, ' ', requester.lastname) AS request_from,
                    au.is_submitted_without_change AS is_bypassed,
                    au.id AS academic_update_id,
                    student.id AS student_id,
                    student.external_id AS student_external_id,
                    student.firstname AS student_firstname,
                    student.lastname AS student_lastname,
                    opsy.is_active AS student_status,
                    $academicUpdateInformation
                    oc.course_section_id AS course_section_id,
                    oc.id AS course_id,
                    oc.subject_code AS subject_code,
                    oc.course_number AS course_number,
                    oc.course_name AS course_name,
                    oc.section_number AS course_section_name,
                    oc.dept_code AS department_name,
                    oay.name AS academic_year_name,
                    oat.name AS academic_term_name
                FROM
                    academic_update_request aur
                        INNER JOIN
                    academic_update au ON aur.id = au.academic_update_request_id
                        INNER JOIN
                    person requester ON aur.person_id = requester.id
                        INNER JOIN
                    academic_update_assigned_faculty auaf ON auaf.academic_update_id = au.id
                        INNER JOIN
                    person student ON student.id = au.person_id_student
                        INNER JOIN
                    org_courses oc ON oc.id = au.org_courses_id
                        INNER JOIN
                    org_academic_year oay ON oc.org_academic_year_id = oay.id
                        INNER JOIN
                    org_academic_terms oat ON oc.org_academic_terms_id = oat.id
                        INNER JOIN
                    org_person_student_year opsy ON student.id = opsy.person_id
                WHERE
                    auaf.person_id_faculty_assigned = :facultyId
                    AND aur.id = :academicUpdateRequestId
                    AND opsy.org_academic_year_id = :participationOrgAcademicYearId
                    $filterClause
                    AND aur.deleted_at IS NULL
                    AND au.deleted_at IS NULL
                    AND requester.deleted_at IS NULL
                    AND student.deleted_at IS NULL
                    AND oc.deleted_at IS NULL
                    AND oay.deleted_at IS NULL
                    AND oat.deleted_at IS NULL
                    AND opsy.deleted_at IS NULL
                    ORDER BY oc.course_name, oc.course_section_id, student.lastname, student.firstname, student.id
                    $limitClause
        ";

        $records = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $records;
    }


    /**
     * Gets the counts per status of academic updates associated with a specific request.
     *
     * @param int $academicUpdateRequestId
     * @param int $orgAcademicYearId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getAcademicUpdateStatusCountsByRequest($academicUpdateRequestId, $orgAcademicYearId)
    {

        $parameters = [
            'academicUpdateRequestId' => $academicUpdateRequestId,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        $sql = "
            SELECT
                au.status,
                COUNT(au.id) AS count
            FROM
                academic_update au
                  INNER JOIN
                org_person_student_year opsy
                    ON au.person_id_student = opsy.person_id
            WHERE
                au.deleted_at IS NULL
                AND opsy.deleted_at IS NULL
                AND academic_update_request_id = :academicUpdateRequestId
                AND opsy.org_academic_year_id = :orgAcademicYearId
            GROUP BY au.status
            ORDER BY count DESC;
        ";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $records = $stmt->fetchAll();

        //Format the records into an array of key-value pairs
        $result = [];
        foreach ($records as $record) {
            $status = $record['status'];
            $count = (int)$record['count'];

            $result[$status] = $count;
        }
        return $result;
    }

    /**
     * gets all saved academic updates for the student faculty course combination.
     *
     * @param int $courseId
     * @param int $organizationId
     * @param int $studentId
     * @return array|null
     */
    public function getSavedAcademicUpdateForStudentFacultyCourse($courseId, $organizationId, $studentId)
    {
        $parameters = [
            'courseId' => $courseId,
            'orgId' => $organizationId,
            'studentId' => $studentId
        ];
        $sql = "
            SELECT DISTINCT
                au.id AS academic_update_id,
                aur.id AS academic_update_request_id
            FROM
                academic_update au
                  INNER JOIN
                academic_update_request aur ON aur.id = au.academic_update_request_id
            WHERE
                au.org_id = :orgId
                AND au.person_id_student = :studentId
                AND au.org_courses_id = :courseId
                AND au.status = 'saved'
                AND aur.status = 'open'
                AND au.deleted_at IS NULL
                AND aur.deleted_at IS NULL;
        ";

        $resultSet = $this->executeQueryFetchAll($sql, $parameters);

        return $resultSet;
    }

    public function getSelectedStudentsByRequest($requestId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('Identity(aurs.person) as person_id');
        $qb->from('SynapseAcademicUpdateBundle:AcademicUpdateRequestStudent', 'aurs');
        $qb->where(AcademicUpdateConstant::ACADEMIC_UPDATE_REQUEST_EQUALS_REQUESTID);
        $qb->setParameters(array(

            AcademicUpdateConstant::REQUESTID => $requestId
        ));
        return $qb->getQuery()->getArrayResult();
    }

    public function getSelectedFacultyByRequest($requestId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('Identity(aurs.person) as person_id');
        $qb->from('SynapseAcademicUpdateBundle:AcademicUpdateRequestFaculty', 'aurs');
        $qb->where(AcademicUpdateConstant::ACADEMIC_UPDATE_REQUEST_EQUALS_REQUESTID);
        $qb->setParameters(array(

            AcademicUpdateConstant::REQUESTID => $requestId
        ));
        return $qb->getQuery()->getArrayResult();
    }

    public function getSelectedCourseByRequest($requestId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('Identity(aurs.orgCourses) as person_id');
        $qb->from('SynapseAcademicUpdateBundle:AcademicUpdateRequestCourse', 'aurs');
        $qb->where(AcademicUpdateConstant::ACADEMIC_UPDATE_REQUEST_EQUALS_REQUESTID);
        $qb->setParameters(array(

            AcademicUpdateConstant::REQUESTID => $requestId
        ));
        return $qb->getQuery()->getArrayResult();
    }

    public function getSelectedGroupByRequest($requestId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('Identity(aurs.orgGroup) as person_id');
        $qb->from('SynapseAcademicUpdateBundle:AcademicUpdateRequestGroup', 'aurs');
        $qb->where(AcademicUpdateConstant::ACADEMIC_UPDATE_REQUEST_EQUALS_REQUESTID);
        $qb->setParameters(array(

            AcademicUpdateConstant::REQUESTID => $requestId
        ));
        return $qb->getQuery()->getArrayResult();
    }

    public function getSelectedProfileByRequest($requestId, $type)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('Identity(aurs.' . $type . 'Metadata) as person_id');
        $qb->from('SynapseAcademicUpdateBundle:AcademicUpdateRequestMetadata', 'aurs');
        $qb->where('aurs.academicUpdateRequest = :requestid AND aurs.' . $type . 'Metadata IS NOT NULL');
        $qb->setParameters(array(

            AcademicUpdateConstant::REQUESTID => $requestId
        ));
        return $qb->getQuery()->getArrayResult();
    }

    public function getSelectedStaticListByRequest($requestId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('Identity(aurs.orgStaticList) as person_id');
        $qb->from('SynapseAcademicUpdateBundle:AcademicUpdateRequestStaticList', 'aurs');
        $qb->where(AcademicUpdateConstant::ACADEMIC_UPDATE_REQUEST_EQUALS_REQUESTID);
        $qb->setParameters(array(

            AcademicUpdateConstant::REQUESTID => $requestId
        ));
        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Get all academic updates within open academic update requests for a student and course combination
     *
     * @param int $courseId
     * @param int $organizationId
     * @param int $studentId
     * @param string $datetimeString - 'Y:m:d H:i:s' format
     * @param int|null $facultyId - If present, limits the result set to just academic updates that are assigned to the passed faculty ID.
     * @return array
     */
    public function getAcademicUpdatesInOpenRequestsForStudent($courseId, $organizationId, $studentId, $datetimeString, $facultyId = null)
    {
        $parameters = [
            'courseId' => $courseId,
            'orgId' => $organizationId,
            'studentId' => $studentId,
            'datetimeString' => $datetimeString
        ];

        if ($facultyId) {
            $facultyJoin = '
                        INNER JOIN
                    academic_update_assigned_faculty auaf ON auaf.academic_update_id = au.id
                        ';
            $facultyWhereCondition = ' AND auaf.person_id_faculty_assigned = :facultyId ';
            $parameters['facultyId'] = $facultyId;
        } else {
            $facultyJoin = "";
            $facultyWhereCondition = "";
        }

        $sql = "
            SELECT DISTINCT
                au.id AS academic_update_id,
                aur.id AS academic_update_request_id
            FROM
                academic_update au
                  INNER JOIN
                academic_update_request aur ON aur.id = au.academic_update_request_id
                  $facultyJoin
            WHERE
                au.org_id = :orgId
                AND au.person_id_student = :studentId
                AND au.org_courses_id = :courseId
                AND aur.due_date > :datetimeString
                $facultyWhereCondition
                AND aur.status = 'open'
                AND au.status = 'open'
                AND au.deleted_at IS NULL
                AND aur.deleted_at IS NULL;
        ";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $resultSet = $stmt->fetchAll();

        return $resultSet;
    }

    /**
     * Gets the completion statistics of the specified academic update request.
     *
     * @param int $academicUpdateRequestId
     * @param int $facultyId
     * @return array
     */
    public function getAcademicUpdateRequestCompletionStatistics($academicUpdateRequestId, $facultyId = null)
    {
        $parameters = [
            'academicUpdateRequestId' => $academicUpdateRequestId
        ];


        if ($facultyId) {
            $assignedFacultyJoin = "
                INNER JOIN
            academic_update_assigned_faculty fa
                ON fa.academic_update_id = au.id
                AND fa.org_id = au.org_id
            ";
            $assignedFacultyIdCondition = " AND fa.person_id_faculty_assigned = :facultyId ";
            $parameters['facultyId'] = $facultyId;
        } else {
            $assignedFacultyIdCondition = "";
            $assignedFacultyJoin = "";
        }


        $sql = "
            SELECT 
              closed_academic_updates, 
              open_academic_updates,
              saved_academic_updates,
              total_academic_updates, 
              ROUND((closed_academic_updates / total_academic_updates) * 100, 2) AS completion_percentage
            FROM 
            (
                SELECT
                    SUM(IF(au.status = 'closed', 1, 0)) AS closed_academic_updates, 
                    SUM(IF(au.status = 'open', 1, 0)) AS open_academic_updates,
                    SUM(IF(au.status = 'saved', 1, 0)) AS saved_academic_updates,
                    COUNT(*) AS total_academic_updates
                FROM 
                    academic_update au
                    $assignedFacultyJoin
                WHERE 
                    au.deleted_at IS NULL
                    AND au.academic_update_request_id = :academicUpdateRequestId
                    $assignedFacultyIdCondition
            ) AS counts
            ;
        ";

        $records = $this->executeQueryFetchAll($sql, $parameters);

        return $records[0];
    }
}