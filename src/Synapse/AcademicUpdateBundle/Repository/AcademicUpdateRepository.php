<?php
namespace Synapse\AcademicUpdateBundle\Repository;

use Doctrine\DBAL\Connection;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdate;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CoreBundle\Util\Constants\AcademicUpdateConstant;
use Synapse\CoreBundle\Util\Constants\SearchConstant;
use Synapse\RestBundle\Exception\ValidationException;

class AcademicUpdateRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseAcademicUpdateBundle:AcademicUpdate';

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
     * @return AcademicUpdate| null
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
     * @return AcademicUpdate| null
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
     * @return AcademicUpdate[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $exception = null)
    {
        $objectArray = parent::findBy($criteria, $orderBy, $limit, $offset);
        return $this->doObjectsExist($objectArray, $exception);
    }


    /**
     * Gets the faculty within the academic update request that have not submitted all of their assigned academic updates.
     *
     * @param int $orgId
     * @param int $requestId
     * @param string $currentDate
     * @return array
     */
    public function getFacultyWithIncompleteAcademicUpdatesInRequest($orgId, $requestId, $currentDate)
    {
        $parameters = [
            'organizationId' => $orgId,
            'academicUpdateRequestId' => $requestId,
            'currentDate' => $currentDate
        ];

        $sql = "
            SELECT 
                auaf.person_id_faculty_assigned, 
                assignee.firstname AS faculty_firstname, 
                assignee.lastname AS faculty_lastname, 
                assignee.username AS faculty_email, 
                COUNT(au.id) AS total_updates, 
                aur.name AS request_name, 
                aur.due_date AS request_due_date, 
                aur.description AS request_description, 
                aur.person_id AS requester_person_id, 
                aur.subject AS request_email_subject, 
                aur.email_optional_msg AS request_email_optional_message, 
                creator.firstname AS requester_firstname, 
                creator.lastname AS requester_lastname, 
                creator.username AS requester_email
            FROM 
                academic_update au
                    JOIN 
                academic_update_assigned_faculty auaf ON au.id = auaf.academic_update_id
                    JOIN 
                academic_update_request aur ON aur.id = au.academic_update_request_id 
                    JOIN 
                person creator ON creator.id = aur.person_id
                    JOIN
                person assignee ON assignee.id = auaf.person_id_faculty_assigned
            WHERE 
                au.deleted_at IS NULL 
                AND aur.deleted_at IS NULL 
                AND creator.deleted_at IS NULL 
                AND assignee.deleted_at IS NULL
                AND aur.id = :academicUpdateRequestId
                AND au.status <> 'closed'
                AND aur.status = 'open'
                AND au.org_id = :organizationId
                AND aur.due_date >= :currentDate
            GROUP BY auaf.person_id_faculty_assigned
        ";

        $result = $this->executeQueryFetchAll($sql, $parameters);
        
        return $result;
    }

    public function updateAcademicUpdateStatus($orgId, $requestId, $status = 'closed')
    {
        try {
            $em = $this->getEntityManager();
            
            $qb = $em->createQuery("update " . AcademicUpdateConstant::AU_UPDATE_REPO . " au set au.status='" . $status . "'
            where au.academicUpdateRequest='" . $requestId . "' and au.org='" . $orgId . "'");
            $numUpdated = $qb->execute();
        } catch (\Exception $e) {          
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
        return $numUpdated;
    }

    public function getAcademicUpdatesByOrg($orgId)
    {
        $sql = "SELECT
                    oc.externalId AS UniqueCourseSectionID,
                    p.external_id AS StudentID,
                    au.failure_risk_level AS FailureRisk,
                    au.absence AS Absences,
                    au.`comment` AS Comments,
                    au.send_to_student AS SentToStudent,
                    au.final_grade AS FinalGrade,
                    au.grade AS InProgressGrade,
                    au.refer_for_assistance AS referForAssistance
                FROM
                    academic_update au
                        INNER JOIN
                    person p ON au.person_id_student = p.id
                        INNER JOIN
                    org_courses oc ON au.org_courses_id = oc.id
                WHERE
                    au.org_id = :orgId
                    AND au.deleted_at IS NULL
                	AND p.deleted_at IS NULL
                    AND oc.deleted_at IS NULL";

        try {
            $em = $this->getEntityManager ();
            $stmt = $em->getConnection()->executeQuery($sql, ['orgId'=>$orgId],[]);

        } catch ( \Exception $e ) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $result = $stmt->fetchAll();
        return $result;
    }

    /**
     * List the Academic update details based on the filter params
     *
     * @param string $userType
     * @param int $orgId
     * @param string $currentDate
     * @param int $facultyId
     * @param array $filters
     * @return array
     */
    public function getAcademicUpdateRequestsByUser($userType, $orgId, $currentDate, $facultyId, $filters)
    {
        $parameters = ['orgId' => $orgId, 'currentDate' => $currentDate];

        if ($userType == 'faculty') {
            $assignedFacultyJoin = "
                INNER JOIN
            academic_update_assigned_faculty fa
                ON fa.academic_update_id = au.id
                AND fa.org_id = au.org_id
            ";
            $assignedFacultyIdCondition = " AND fa.person_id_faculty_assigned = :facultyId ";
            $parameters['facultyId'] = $facultyId;
        } else {
            $assignedFacultyJoin = "";
            $assignedFacultyIdCondition = "";
        }

        if ($userType == "coordinator" && $filters['request'] != "all") {
            $personIdCondition = " AND aur.person_id = :facultyId ";
            $parameters['facultyId'] = $facultyId;
        } else {
            $personIdCondition = "";
        }

        if ($filters['filter'] != "") {
            $requestNameCondition = " AND aur.name LIKE :requestName ";
            $parameters['requestName'] = "%{$filters['filter']}%";
        } else {
            $requestNameCondition = "";
        }

        $sql = "
            SELECT
                aur.id AS requestId,
                aur.name AS name,
                aur.description AS description,
                aur.request_date AS requestCreated,
                COUNT(au.id) AS totalUpdates,
                aur.due_date AS requestDue,
                SUM(CASE
                        WHEN au.status = 'closed'
                        THEN 1
                        ELSE 0
                    END) AS completedTotal,
                aur.status AS status,
                aur.person_id AS requesterId,
                p.firstname AS requesterFirst,
                p.lastname AS requesterLast,
                (CASE
                    WHEN (aur.due_date < :currentDate) THEN true
                    ELSE false
                END) AS pastDueDate
            FROM
                academic_update au
                    INNER JOIN
                academic_update_request aur
                        ON aur.id = au.academic_update_request_id
                        AND aur.org_id = au.org_id
                    INNER JOIN
                person p
                        ON p.id = aur.person_id
                        AND p.organization_id = aur.org_id
                $assignedFacultyJoin
            WHERE
              au.org_id = :orgId
              $personIdCondition
              $assignedFacultyIdCondition
              $requestNameCondition
              AND au.deleted_at IS NULL
              AND aur.deleted_at IS NULL
              AND p.deleted_at IS NULL
            GROUP BY aur.id
            ORDER BY aur.due_date DESC, aur.request_date DESC, aur.name ASC, aur.id DESC
        ";

        $results = $this->executeQueryFetchAll($sql, $parameters);

        return $results;
    }

    public function getAUFacultyAssigned($userType, $orgId, $facultyId)
    {
        $param[AcademicUpdateConstant::KEY_ORGID] = $orgId;
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('aur.id as requestId, p.firstname, p.lastname');
        $qb->from(AcademicUpdateConstant::AU_UPDATE_REPO, 'au');
        $qb->Join(AcademicUpdateConstant::AU_ASSIGNED_FACULTY_REPO, 'fa', \Doctrine\ORM\Query\Expr\Join::WITH, AcademicUpdateConstant::AU_FACULTY_ASSIGNED_JOIN);
        $qb->Join(AcademicUpdateConstant::AU_REQUEST_REPO, 'aur', \Doctrine\ORM\Query\Expr\Join::WITH, AcademicUpdateConstant::AU_AUR_LINK);
        $qb->Join(AcademicUpdateConstant::PERSON_REPO, 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = fa.personFacultyAssigned');
        $qb->where(AcademicUpdateConstant::AU_ORGID);
        $qb->andWhere(AcademicUpdateConstant::AUR_ORGID);
        $qb->andWhere('p.organization = :orgId');
        if ($userType == "faculty" && $facultyId != "") {
            $param['facultyId'] = $facultyId;
            $qb->andWhere('fa.personFacultyAssigned != :facultyId');
        }
        $qb->setParameters($param);
        $qb->orderBy('p.lastname', 'asc');
        $qb->addOrderBy('p.firstname', 'asc');
        $qb->groupBy('fa.personFacultyAssigned, aur.id');
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }

    public function getAssingedFacultyInfoByRequest($academicRequest)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p.id as personid, p.firstname, p.lastname');
        $qb->from(AcademicUpdateConstant::AU_UPDATE_REPO, 'au');
        $qb->Join(AcademicUpdateConstant::AU_ASSIGNED_FACULTY_REPO, 'fa', \Doctrine\ORM\Query\Expr\Join::WITH, AcademicUpdateConstant::AU_FACULTY_ASSIGNED_JOIN);
        $qb->Join(AcademicUpdateConstant::AU_REQUEST_REPO, 'aur', \Doctrine\ORM\Query\Expr\Join::WITH, AcademicUpdateConstant::AU_AUR_LINK);
        $qb->Join(AcademicUpdateConstant::PERSON_REPO, 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = fa.personFacultyAssigned');
        
        $qb->where('au.academicUpdateRequest = :aurequest');
        $qb->setParameters([
            'aurequest' => $academicRequest
        ]);
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }

    /**
     * Finds the academic update details for the given academic update ids
     *
     * @param $academicUpdateIds
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getAcademicUpdateDetailsByIds($academicUpdateIds)
    {
        $parameters = [
            'academicUpdateIds' => $academicUpdateIds
        ];
        $parameterTypes = ['academicUpdateIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT 
                    au.is_submitted_without_change AS is_bypassed,
                    au.id AS academic_update_id,
                    student.id AS student_id,
                    student.firstname AS student_firstname,
                    student.lastname AS student_lastname,
                    au.status AS academic_update_status,
                    au.failure_risk_level AS student_risk,
                    au.grade AS student_grade,
                    au.absence AS student_absences,
                    au.comment AS student_comments,
                    au.refer_for_assistance AS student_refer,
                    au.send_to_student AS student_send,
                    oc.course_section_id AS course_section_id,
                    oc.id AS course_id,
                    oc.subject_code,
                    oc.course_number,
                    oc.course_name AS course_name,
                    oc.section_number AS course_section_name,
                    oc.dept_code AS department_name,
                    oay.name AS academic_year_name,
                    oat.name AS academic_term_name,
                    opsy.is_active AS student_status
                FROM
                    academic_update au
                        INNER JOIN
                    person student ON student.id = au.person_id_student
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = student.id
                            AND opsy.organization_id = student.organization_id
                        INNER JOIN
                    org_courses oc
                            ON oc.id = au.org_courses_id
                            AND opsy.org_academic_year_id = oc.org_academic_year_id
                        INNER JOIN
                    org_academic_year oay ON oay.id = oc.org_academic_year_id
                        INNER JOIN
                    org_academic_terms oat ON oat.id = oc.org_academic_terms_id
                WHERE au.id IN (:academicUpdateIds)
                    AND au.deleted_at IS NULL
                    AND student.deleted_at IS NULL
                    AND opsy.deleted_at IS NULL
                    AND oc.deleted_at IS NULL
                    AND oay.deleted_at IS NULL
                    AND oat.deleted_at IS NULL;";

        try {
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
            $result = $stmt->fetchAll();
            return $result;
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

    }

    /**
     * Gets the academic update history for student
     *
     * @param int $organizationId
     * @param int $courseId
     * @param int $studentId
     * @param int $orgAcademicYearId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getAcademicUpdateStudentHistory($organizationId, $courseId, $studentId, $orgAcademicYearId)
    {
        $parameters = ['organizationId' => $organizationId, 'courseId' => $courseId, 'studentId' => $studentId, 'orgAcademicYearId' => $orgAcademicYearId];

        $sql = "SELECT 
                    au.id,
                    au.failure_risk_level,
                    au.update_date,
                    au.grade,
                    au.absence,
                    au.comment,
                    au.refer_for_assistance,
                    au.send_to_student
                FROM
                    academic_update au
                    INNER JOIN
	                org_person_student_year opsy 
	                    ON au.person_id_student = opsy.person_id
	                    AND au.org_id = opsy.organization_id
                WHERE
                    au.org_courses_id = :courseId
                        AND ((au.failure_risk_level IS NOT NULL
                        OR au.grade IS NOT NULL
                        OR au.absence IS NOT NULL
                        OR au.comment IS NOT NULL
                        OR au.final_grade IS NOT NULL)
                        AND au.status = 'closed')
                        AND au.person_id_student = :studentId
                        AND opsy.org_academic_year_id = :orgAcademicYearId
                        AND au.org_id = :organizationId
                        AND au.deleted_at IS NULL
                        AND opsy.deleted_at IS NULL
                ORDER BY au.update_date DESC";

        try {
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql, $parameters);
            $result = $stmt->fetchAll();
            return $result;
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

    }

    public function getAcademicUpdateUploadCount($organization)
    {
        $em = $this->getEntityManager();
        $count = $em->createQueryBuilder()
            ->select('count(t.id)')
            ->from(AcademicUpdateConstant::AU_UPDATE_REPO, 't')
            ->where('t.org = :org')
            ->setParameters([
            'org' => $organization
        ])
            ->getQuery()
            ->getSingleScalarResult();
        
        return $count;
    }

    public function getAssignedFacultiesByAcademicUpdate($auId, $person)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('Identity(auaf.personFacultyAssigned) as person_id');
        $qb->from('SynapseAcademicUpdateBundle:AcademicUpdateAssignedFaculty', 'auaf');
        $qb->where('auaf.academicUpdate = :updateId');
        $qb->andWhere('auaf.personFacultyAssigned = :facultyAssigned');
        $qb->setParameters([
            'updateId' => $auId,
            'facultyAssigned' => $person
        ]);
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }

    /**
     * Gets student IDs for the specified academic update request
     *
     * @param int $organizationId
     * @param int $academicUpdateRequestId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getStudentIdsForAcademicUpdate($organizationId, $academicUpdateRequestId)
    {
        $parameters = [
            'academicUpdateRequestId' => $academicUpdateRequestId,
            'organizationId' => $organizationId,
        ];

        $sql = "SELECT 
                    DISTINCT person_id_student
                FROM
                    academic_update 
                WHERE 
                    academic_update_request_id = :academicUpdateRequestId
                    AND org_id = :organizationId
                    AND deleted_at IS NULL";

        try {
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql, $parameters);
            $studentIds = $stmt->fetchAll();

            if (!empty($studentIds)) {
                $studentIds = array_column($studentIds, 'person_id_student');
            }
            return $studentIds;
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage().": ".$e->getTraceAsString());
        }
    }

    /**
     * Get latest academic updates for a course based on specific/all students in the organization
     *
     * @param int $courseId - Internal course id
     * @param int $organizationId - Organization id
     * @param array $studentIds - List of student Id, if it is NULL then this will consider all students from that organization.
     * @param bool $isInternalIds - identify what id should be return in the result set.
     *                              'true' - return student/faculty internal id
     *                              'false' - return student/faculty external id.
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getLatestAcademicUpdatesForCourse($courseId, $organizationId, $studentIds, $isInternalIds = false)
    {
        $parameterTypes = [];
        $studentCondition = '';

        $parameters = [
            'courseId' => $courseId,
            'organizationId' => $organizationId,
        ];

        if (!empty($studentIds)) {
            $studentCondition = "  AND person_id_student IN (:studentIds) ";
            $parameters['studentIds'] = $studentIds;
            $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];
        }

        if ($isInternalIds) {
            $studentIDColumn = 'student.id';
            $facultyIDColumn = 'faculty.id';
        } else {
            $studentIDColumn = 'student.external_id';
            $facultyIDColumn = 'faculty.external_id';
        }

        $sql = "SELECT 
                $studentIDColumn AS student_id,
                IF(ISNULL($facultyIDColumn), 'FTP', $facultyIDColumn) AS faculty_id,
                oc.course_section_id AS course_id,
                au.update_date AS date_submitted,
                au.failure_risk_level,
                au.grade AS in_progress_grade,
                au.absence AS absences,
                au.comment,
                au.refer_for_assistance,
                au.send_to_student,
                au.final_grade,
                au.id AS academic_update_id
            FROM
                academic_update au
                    JOIN
                  (   
                    SELECT 
                        au1.modified_at, au1.id
                    FROM
                        academic_update au1
                    WHERE
                        au1.org_courses_id = :courseId
                        AND au1.org_id = :organizationId
                        AND au1.status = 'closed'
                        $studentCondition
                        AND au1.deleted_at IS NULL
                        AND au1.id = (
                            SELECT 
                                au2.id
                            FROM 
                                academic_update au2
                            WHERE 
                                au1.person_id_student = au2.person_id_student
                                AND au1.org_id = au2.org_id
                                AND au1.org_courses_id = au2.org_courses_id
                                AND au1.status = au2.status
                                AND au2.deleted_at IS NULL
                            ORDER BY au2.modified_at DESC
                            LIMIT 1 
                        )                        
                    ) AS latest ON latest.id = au.id AND au.modified_at = latest.modified_at
                    JOIN
                person student ON student.id = au.person_id_student
                    JOIN
                org_courses oc ON oc.id = au.org_courses_id
                    LEFT JOIN
                person faculty ON faculty.id = au.person_id_faculty_responded
                    AND faculty.deleted_at IS NULL
            WHERE
                au.deleted_at IS NULL
                    AND student.deleted_at IS NULL
                    AND oc.deleted_at IS NULL
            ORDER BY au.id DESC";

        try {
            $statement = $this->getEntityManager()->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
            $academicUpdates = $statement->fetchAll();
            return $academicUpdates;
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }
}
