<?php

namespace Synapse\SearchBundle\DAO;

use Doctrine\DBAL\Connection;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;


/**
 * @DI\Service("predefined_search_dao")
 */
class PredefinedSearchDAO
{

    const DAO_KEY = 'predefined_search_dao';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * PredefinedSearchDAO constructor
     *
     * @param $connection
     *
     * @DI\InjectParams({
     *     "connection" = @DI\Inject("database_connection")
     * })
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Returns a list of student ids for the predefined search "All my students".
     * Includes all students the faculty member has individual access to.
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param int $orgAcademicYearId
     * @param bool $onlyIncludeActiveStudents
     * @param bool $requireRiskPermission
     * @return array
     */
    public function getAllMyStudents($facultyId, $organizationId, $orgAcademicYearId, $onlyIncludeActiveStudents = true, $requireRiskPermission = false)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        if ($onlyIncludeActiveStudents) {
            $activeStudentsClause = " AND opsy.is_active = 1";
        } else {
            $activeStudentsClause = "";
        }

        if ($requireRiskPermission) {
            $riskPermissionClause = " AND op.risk_indicator = 1";
        } else {
            $riskPermissionClause = "";
        }

        $sql = "SELECT DISTINCT ofspm.student_id
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                            AND op.organization_id = ofspm.org_id
                        INNER JOIN
                     org_person_student_year opsy
                            ON opsy.person_id =  ofspm.student_id
                            AND opsy.organization_id = ofspm.org_id
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :organizationId
                    AND op.accesslevel_ind_agg = 1
                    $riskPermissionClause
                    $activeStudentsClause
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    AND opsy.deleted_at IS NULL
                    AND op.deleted_at IS NULL;";


        try {
            $stmt = $this->connection->executeQuery($sql, $parameters);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'student_id');

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

    /**
     * Returns a list of student ids for the predefined search "My primary campus connections".
     * Includes all students for which this faculty member is the primary campus connection.
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param int $orgAcademicYearId
     * @param bool $onlyIncludeActiveStudents
     * @return array
     */
    public function getMyPrimaryCampusConnections($facultyId, $organizationId, $orgAcademicYearId, $onlyIncludeActiveStudents = true)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        if ($onlyIncludeActiveStudents) {
            $activeStudentsClause = " AND opsy.is_active = 1";
        } else {
            $activeStudentsClause = "";
        }

        $sql = "SELECT DISTINCT ofspm.student_id
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                            AND op.organization_id = ofspm.org_id
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ofspm.student_id
                            AND opsy.organization_id = ofspm.org_id
                        INNER JOIN
                    org_person_student ops
                            ON ops.person_id = ofspm.student_id
                            AND ops.organization_id = ofspm.org_id
                            AND ops.person_id_primary_connect = ofspm.faculty_id
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :organizationId
                    AND op.accesslevel_ind_agg = 1
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    AND opsy.deleted_at IS NULL
                    $activeStudentsClause
                    AND op.deleted_at IS NULL
                    AND ops.deleted_at IS NULL;";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'student_id');

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

    /**
     * Returns a list of student ids for the predefined search "At-risk students".
     * Includes students whose risk level is red2 or red,
     * and for whom the user has risk access.
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param int $orgAcademicYearId
     * @param bool $onlyIncludeActiveStudents
     * @return array
     */
    public function getAtRiskStudents($facultyId, $organizationId, $orgAcademicYearId, $onlyIncludeActiveStudents = true)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        if ($onlyIncludeActiveStudents) {
            $activeStudentsClause = " AND opsy.is_active = 1";
        } else {
            $activeStudentsClause = "";
        }

        $sql = "SELECT DISTINCT ofspm.student_id
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                            AND op.organization_id = ofspm.org_id
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ofspm.student_id
                            AND opsy.organization_id = ofspm.org_id
                        INNER JOIN
                    person p
                            ON p.id = ofspm.student_id
                            AND p.organization_id = ofspm.org_id
                        INNER JOIN
                    risk_level rl
                            ON rl.id = p.risk_level
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :organizationId
                    AND op.accesslevel_ind_agg = 1
                    AND op.risk_indicator = 1
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    AND opsy.deleted_at IS NULL
                    $activeStudentsClause
                    AND rl.risk_text IN ('red2', 'red')
                    AND op.deleted_at IS NULL
                    AND p.deleted_at IS NULL
                    AND rl.deleted_at IS NULL;";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'student_id');

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

    /**
     * Returns a list of student ids for students who have the specified risk color
     * and for whom the user has risk access.
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param string $riskColor
     * @param int $orgAcademicYearId
     * @param boolean $onlyIncludeActiveStudents
     * @return array
     */
    public function getStudentsWithSpecifiedRiskColor($facultyId, $organizationId, $riskColor, $orgAcademicYearId, $onlyIncludeActiveStudents = true)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'riskColor' => $riskColor,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        if ($onlyIncludeActiveStudents) {
            $activeStudentsClause = " AND opsy.is_active = 1";
        } else {
            $activeStudentsClause = "";
        }

        $sql = "SELECT DISTINCT ofspm.student_id
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                            AND op.organization_id = ofspm.org_id
                        INNER JOIN
                    person p
                            ON p.id = ofspm.student_id
                            AND p.organization_id = ofspm.org_id
                        INNER JOIN
                    risk_level rl
                            ON rl.id = p.risk_level
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ofspm.student_id
                            AND opsy.organization_id = ofspm.org_id
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :organizationId
                    AND op.accesslevel_ind_agg = 1
                    AND op.risk_indicator = 1
                    AND rl.risk_text = :riskColor
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    $activeStudentsClause
                    AND op.deleted_at IS NULL
                    AND opsy.deleted_at IS NULL
                    AND p.deleted_at IS NULL
                    AND rl.deleted_at IS NULL;";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'student_id');

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

    /**
     * Returns a list of student ids for students who have gray risk (not enough data to calculate risk or not on a risk model)
     * and for whom the user has risk access.
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param int $orgAcademicYearId
     * @param boolean $onlyIncludeActiveStudents
     * @return array
     */
    public function getStudentsWithGrayRisk($facultyId, $organizationId, $orgAcademicYearId, $onlyIncludeActiveStudents = true)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        if ($onlyIncludeActiveStudents) {
            $activeStudentsClause = " AND opsy.is_active = 1";
        } else {
            $activeStudentsClause = "";
        }

        $sql = "SELECT DISTINCT ofspm.student_id
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                            AND op.organization_id = ofspm.org_id
                        INNER JOIN
                    person p
                            ON p.id = ofspm.student_id
                            AND p.organization_id = ofspm.org_id
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ofspm.student_id
                            AND opsy.organization_id = ofspm.org_id
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :organizationId
                    AND op.accesslevel_ind_agg = 1
                    AND op.risk_indicator = 1
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    $activeStudentsClause
                    AND p.risk_level IS NULL
                    AND op.deleted_at IS NULL
                    AND opsy.deleted_at IS NULL
                    AND p.deleted_at IS NULL;";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'student_id');

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

    /**
     * Returns a list of student ids for the predefined search "Students with a high intent to leave".
     * Includes students whose intent to leave level is red,
     * and for whom the user has intent to leave access.
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param int $orgAcademicYearId
     * @param bool $onlyIncludeActiveStudents
     * @return array
     */
    public function getStudentsWithHighIntentToLeave($facultyId, $organizationId, $orgAcademicYearId, $onlyIncludeActiveStudents = true)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        if ($onlyIncludeActiveStudents) {
            $activeStudentsClause = " AND opsy.is_active = 1";
        } else {
            $activeStudentsClause = "";
        }

        $sql = "SELECT DISTINCT ofspm.student_id
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                            AND op.organization_id = ofspm.org_id
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ofspm.student_id
                            AND opsy.organization_id = ofspm.org_id
                        INNER JOIN
                    person p
                            ON p.id = ofspm.student_id
                            AND p.organization_id = ofspm.org_id
                        INNER JOIN
                    intent_to_leave itl
                            ON itl.id = p.intent_to_leave
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :organizationId
                    AND op.accesslevel_ind_agg = 1
                    AND op.intent_to_leave = 1
                    AND itl.text = 'red'
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    AND opsy.deleted_at IS NULL
                    $activeStudentsClause
                    AND op.deleted_at IS NULL
                    AND p.deleted_at IS NULL
                    AND itl.deleted_at IS NULL;";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'student_id');

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

    /**
     * Returns a list of student ids for the predefined search "High priority students".
     * These are students with high risk (red2 or red) who have not had an interaction contact since their risk score was last updated.
     *
     * @param int $facultyId
     * @param int $orgAcademicYearId
     * @param bool $onlyIncludeActiveStudents
     *
     * @return array
     */
    public function getHighPriorityStudents($facultyId, $orgAcademicYearId, $onlyIncludeActiveStudents = true)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        if ($onlyIncludeActiveStudents) {
            $activeStudentsClause = " AND opsy.is_active = 1";
        } else {
            $activeStudentsClause = "";
        }

        $sql = "SELECT DISTINCT ofspm.student_id
                FROM
                    (
                        SELECT DISTINCT
                            student_id, permissionset_id
                        FROM
                            org_faculty_student_permission_map
                        WHERE
                            faculty_id = :facultyId 
                    ) ofspm
                        INNER JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ofspm.student_id
                         INNER JOIN
                    person p
                            ON p.id = ofspm.student_id
                        INNER JOIN
                    risk_level rl
                            ON rl.id = p.risk_level
                        INNER JOIN
                    org_person_student ops
                            ON ops.person_id = ofspm.student_id
                WHERE
                    op.accesslevel_ind_agg = 1
                    AND op.risk_indicator = 1
                    AND rl.risk_text IN ('red2', 'red')
                    AND (p.last_contact_date < p.risk_update_date OR p.last_contact_date IS NULL)
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    AND opsy.deleted_at IS NULL
                    $activeStudentsClause
                    AND op.deleted_at IS NULL
                    AND p.deleted_at IS NULL
                    AND rl.deleted_at IS NULL
                    AND ops.deleted_at IS NULL;";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'student_id');

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results;
    }

    /**
     * Returns a list of student ids for the predefined search "High risk of failure".
     * These are students who were marked as having high failure_risk_level in an academic update in one of the given terms (typically a current term).
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param array $courseOrgAcademicTermIds
     * @param int $participationOrgAcademicYearId
     * @param bool $onlyIncludeActiveStudents
     * @return array
     */
    public function getStudentsAtRiskOfFailure($facultyId, $organizationId, $courseOrgAcademicTermIds, $participationOrgAcademicYearId, $onlyIncludeActiveStudents = true)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'courseOrgAcademicTermIds' => $courseOrgAcademicTermIds,
            'participationOrgAcademicYearId' => $participationOrgAcademicYearId
        ];

        $parameterTypes = ['courseOrgAcademicTermIds' => Connection::PARAM_INT_ARRAY];

        if ($onlyIncludeActiveStudents) {
            $activeStudentsClause = " AND opsy.is_active = 1";
        } else {
            $activeStudentsClause = "";
        }

        $sql = "SELECT DISTINCT ofspm.student_id
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                            AND op.organization_id = ofspm.org_id
                         INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ofspm.student_id
                            AND opsy.organization_id = ofspm.org_id
                         INNER JOIN
                    org_course_student ocs
                            ON ocs.person_id = ofspm.student_id
                            AND ocs.organization_id = ofspm.org_id
                        INNER JOIN
                    org_courses oc
                            ON oc.id = ocs.org_courses_id
                            AND oc.organization_id = ofspm.org_id
                        INNER JOIN
                    academic_record ar
                            ON ar.org_courses_id = oc.id
                            AND ar.person_id_student = ofspm.student_id
                            AND ar.organization_id = ofspm.org_id
                        LEFT JOIN
                    org_course_faculty ocf
                            ON ocf.org_courses_id = oc.id
                            AND ocf.organization_id = ofspm.org_id
                            AND ocf.deleted_at IS NULL
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :organizationId
                    AND op.accesslevel_ind_agg = 1
                    AND
                    (
                        (op.view_courses = 1 AND op.view_all_academic_update_courses = 1)
                            OR
                        (op.create_view_academic_update = 1 AND ocf.person_id = :facultyId)
                    )
                    AND oc.org_academic_terms_id IN (:courseOrgAcademicTermIds)
                    AND ar.failure_risk_level = 'high'
                    AND opsy.org_academic_year_id = :participationOrgAcademicYearId
                    AND opsy.deleted_at IS NULL
                    $activeStudentsClause
                    AND op.deleted_at IS NULL
                    AND ocs.deleted_at IS NULL
                    AND oc.deleted_at IS NULL
                    AND ar.deleted_at IS NULL;";
        try {
            $stmt = $this->connection->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'student_id');

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

    /**
     * Returns a list of student ids for the predefined search "Four or more absences".
     * These are students who have an academic update in one of the given terms (typically a current term) which reports at least $absenceThreshold absences in a class.
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param array $courseOrgAcademicTermIds
     * @param int $participationOrgAcademicYearID
     * @param int $absenceThreshold
     * @param bool $onlyIncludeActiveStudents
     * @return array
     */
    public function getStudentsWithExcessiveAbsences($facultyId, $organizationId, $courseOrgAcademicTermIds, $participationOrgAcademicYearID, $absenceThreshold, $onlyIncludeActiveStudents = true)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'courseOrgAcademicTermIds' => $courseOrgAcademicTermIds,
            'absenceThreshold' => $absenceThreshold,
            'participationOrgAcademicYearID' => $participationOrgAcademicYearID
        ];

        $parameterTypes = ['courseOrgAcademicTermIds' => Connection::PARAM_INT_ARRAY];

        if ($onlyIncludeActiveStudents) {
            $activeStudentsClause = " AND opsy.is_active = 1";
        } else {
            $activeStudentsClause = "";
        }

        $sql = "SELECT DISTINCT ofspm.student_id
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                            AND op.organization_id = ofspm.org_id
                         INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ofspm.student_id
                            AND opsy.organization_id = ofspm.org_id
                         INNER JOIN
                    org_course_student ocs
                            ON ocs.person_id = ofspm.student_id
                            AND ocs.organization_id = ofspm.org_id
                        INNER JOIN
                    org_courses oc
                            ON oc.id = ocs.org_courses_id
                            AND oc.organization_id = ofspm.org_id
                        INNER JOIN
                    academic_record ar
                            ON ar.org_courses_id = oc.id
                            AND ar.person_id_student = ofspm.student_id
                            AND ar.organization_id = ofspm.org_id
                        LEFT JOIN
                    org_course_faculty ocf
                            ON ocf.org_courses_id = oc.id
                            AND ocf.organization_id = ofspm.org_id
                            AND ocf.deleted_at IS NULL
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :organizationId
                    AND op.accesslevel_ind_agg = 1
                    AND
                    (
                        (op.view_courses = 1 AND op.view_all_academic_update_courses = 1)
                            OR
                        (op.create_view_academic_update = 1 AND ocf.person_id = :facultyId)
                    )
                    AND oc.org_academic_terms_id IN (:courseOrgAcademicTermIds)
                    AND ar.absence >= :absenceThreshold
                    AND opsy.org_academic_year_id = :participationOrgAcademicYearID
                    AND opsy.deleted_at IS NULL
                    $activeStudentsClause
                    AND op.deleted_at IS NULL
                    AND ocs.deleted_at IS NULL
                    AND oc.deleted_at IS NULL
                    AND ar.deleted_at IS NULL;";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'student_id');

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

    /**
     * Returns a list of student ids for the predefined searches "In-progress grade of C or below" and "In-progress grade of D or below".
     * These are students who have an academic update in one of the given terms (typically a current term) which reports a grade in the list $gradesIncluded.
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param array $orgAcademicTermIds
     * @param int $orgAcademicYearId
     * @param array $gradesIncluded
     * @param  bool $onlyIncludeActiveStudents
     * @return array
     */
    public function getStudentsWithLowInProgressGrades($facultyId, $organizationId, $orgAcademicTermIds, $orgAcademicYearId, $gradesIncluded, $onlyIncludeActiveStudents = true)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'orgAcademicTermIds' => $orgAcademicTermIds,
            'gradesIncluded' => $gradesIncluded,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        $parameterTypes = [
            'orgAcademicTermIds' => Connection::PARAM_INT_ARRAY,
            'gradesIncluded' => Connection::PARAM_STR_ARRAY
        ];

        if ($onlyIncludeActiveStudents) {
            $activeStudentsClause = " AND opsy.is_active = 1";
        } else {
            $activeStudentsClause = "";
        }

        $sql = "SELECT DISTINCT ofspm.student_id
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                            AND op.organization_id = ofspm.org_id
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ofspm.student_id
                            AND opsy.organization_id = ofspm.org_id
                        INNER JOIN
                    org_course_student ocs
                            ON ocs.person_id = ofspm.student_id
                            AND ocs.organization_id = ofspm.org_id
                        INNER JOIN
                    org_courses oc
                            ON oc.id = ocs.org_courses_id
                            AND oc.organization_id = ofspm.org_id
                        INNER JOIN
                    academic_record ar
                            ON ar.org_courses_id = oc.id
                            AND ar.person_id_student = ofspm.student_id
                            AND ar.organization_id = ofspm.org_id
                        LEFT JOIN
                    org_course_faculty ocf
                            ON ocf.org_courses_id = oc.id
                            AND ocf.organization_id = ofspm.org_id
                            AND ocf.deleted_at IS NULL 
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :organizationId
                    AND op.accesslevel_ind_agg = 1
                    AND
                    (
                        (op.view_courses = 1 AND op.view_all_academic_update_courses = 1)
                            OR
                        (op.create_view_academic_update = 1 AND ocf.person_id = :facultyId)
                    )
                    AND oc.org_academic_terms_id IN (:orgAcademicTermIds)
                    AND ar.in_progress_grade IN (:gradesIncluded)
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    AND opsy.deleted_at IS NULL
                    $activeStudentsClause
                    AND op.deleted_at IS NULL
                    AND ocs.deleted_at IS NULL
                    AND oc.deleted_at IS NULL
                    AND ar.deleted_at IS NULL;";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'student_id');

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

    /**
     * Returns a list of student ids for the predefined search "Two or more in-progress grades of D or below".
     * These are students who have academic updates for multiple courses in the given terms (typically current terms) which report a grade in the list $gradesIncluded.
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param array $courseOrgAcademicTermsId
     * @param int $participatingOrgAcademicYearId
     * @param array $gradesIncluded
     * @param bool $onlyIncludeActiveStudents
     * @return array
     */
    public function getStudentsWithMultipleLowInProgressGrades($facultyId, $organizationId, $courseOrgAcademicTermsId, $participatingOrgAcademicYearId, $gradesIncluded, $onlyIncludeActiveStudents = true )
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'courseOrgAcademicTermsId' => $courseOrgAcademicTermsId,
            'gradesIncluded' => $gradesIncluded,
            'participatingOrgAcademicYearId' => $participatingOrgAcademicYearId
        ];

        $parameterTypes = [
            'courseOrgAcademicTermsId' => Connection::PARAM_INT_ARRAY,
            'gradesIncluded' => Connection::PARAM_STR_ARRAY
        ];

        if ($onlyIncludeActiveStudents) {
            $activeStudentsClause = " AND opsy.is_active = 1";
        } else {
            $activeStudentsClause = "";
        }

        $sql = "SELECT DISTINCT ofspm.student_id
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                            AND op.organization_id = ofspm.org_id
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ofspm.student_id
                            AND opsy.organization_id = ofspm.org_id
                        INNER JOIN
                    org_course_student ocs
                            ON ocs.person_id = ofspm.student_id
                            AND ocs.organization_id = ofspm.org_id
                        INNER JOIN
                    org_courses oc
                            ON oc.id = ocs.org_courses_id
                            AND oc.organization_id = ofspm.org_id
                        INNER JOIN
                    academic_record ar
                            ON ar.org_courses_id = oc.id
                            AND ar.person_id_student = ofspm.student_id
                            AND ar.organization_id = ofspm.org_id
                        LEFT JOIN
                    org_course_faculty ocf
                            ON ocf.org_courses_id = oc.id
                            AND ocf.organization_id = ofspm.org_id
                            AND ocf.deleted_at IS NULL
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :organizationId
                    AND op.accesslevel_ind_agg = 1
                    AND
                    (
                        (op.view_courses = 1 AND op.view_all_academic_update_courses = 1)
                            OR
                        (op.create_view_academic_update = 1 AND ocf.person_id = :facultyId)
                    )
                    AND oc.org_academic_terms_id IN (:courseOrgAcademicTermsId)
                    AND ar.in_progress_grade IN (:gradesIncluded)
                    AND opsy.org_academic_year_id = :participatingOrgAcademicYearId
                    AND opsy.deleted_at IS NULL
                    $activeStudentsClause
                    AND op.deleted_at IS NULL
                    AND ocs.deleted_at IS NULL
                    AND oc.deleted_at IS NULL
                    AND ar.deleted_at IS NULL
                GROUP BY ofspm.student_id
                HAVING COUNT(DISTINCT oc.id) > 1;";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'student_id');

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

    /**
     * Returns a list of student ids for the predefined searches "Final grade of C or below" and "Final grade of D or below".
     * These are students who have an academic update in one of the given terms (typically a term from the current academic year) which reports a final grade in the list $gradesIncluded.
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param array $courseOrgAcademicTermIds
     * @param int $participationOrgAcademicYearID
     * @param array $gradesIncluded
     * @param bool $onlyIncludeActiveStudents
     * @return array
     */
    public function getStudentsWithLowFinalGrades($facultyId, $organizationId, $courseOrgAcademicTermIds, $participationOrgAcademicYearID, $gradesIncluded, $onlyIncludeActiveStudents = true)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'courseOrgAcademicTermIds' => $courseOrgAcademicTermIds,
            'gradesIncluded' => $gradesIncluded,
            'participationOrgAcademicYearID' => $participationOrgAcademicYearID
        ];

        $parameterTypes = [
            'courseOrgAcademicTermIds' => Connection::PARAM_INT_ARRAY,
            'gradesIncluded' => Connection::PARAM_STR_ARRAY
        ];

        if ($onlyIncludeActiveStudents) {
            $activeStudentsClause = " AND opsy.is_active = 1";
        } else {
            $activeStudentsClause = "";
        }

        $sql = "SELECT DISTINCT ofspm.student_id
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                            AND op.organization_id = ofspm.org_id
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ofspm.student_id
                            AND opsy.organization_id = ofspm.org_id
                        INNER JOIN
                    org_course_student ocs
                            ON ocs.person_id = ofspm.student_id
                            AND ocs.organization_id = ofspm.org_id
                        INNER JOIN
                    org_courses oc
                            ON oc.id = ocs.org_courses_id
                            AND oc.organization_id = ofspm.org_id
                        INNER JOIN
                    academic_record ar
                            ON ar.org_courses_id = oc.id
                            AND ar.person_id_student = ofspm.student_id
                            AND ar.organization_id = ofspm.org_id
                        LEFT JOIN
                    org_course_faculty ocf
                            ON ocf.org_courses_id = oc.id
                            AND ocf.organization_id = ofspm.org_id
                            AND ocf.deleted_at IS NULL
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :organizationId
                    AND op.accesslevel_ind_agg = 1
                    AND
                    (
                        (op.view_courses = 1 AND op.view_all_final_grades = 1)
                            OR
                        (op.create_view_academic_update = 1 AND ocf.person_id = :facultyId)
                    )
                    AND oc.org_academic_terms_id IN (:courseOrgAcademicTermIds)
                    AND ar.final_grade IN (:gradesIncluded)
                    AND opsy.org_academic_year_id = :participationOrgAcademicYearID
                    AND opsy.deleted_at IS NULL
                    $activeStudentsClause
                    AND op.deleted_at IS NULL
                    AND ocs.deleted_at IS NULL
                    AND oc.deleted_at IS NULL
                    AND ar.deleted_at IS NULL;";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'student_id');

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

    /**
     * Returns a list of student ids for the predefined search "Two or more final grades of D or below".
     * These are students who have academic updates for multiple courses in the given terms (typically terms from the current academic year) which report a final grade in the list $gradesIncluded.
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param array $courseOrgAcademicTermIds
     * @param int $participationOrgAcademicYearId
     * @param array $gradesIncluded
     * @param bool $onlyIncludeActiveStudents
     * @return array
     */
    public function getStudentsWithMultipleLowFinalGrades($facultyId, $organizationId, $courseOrgAcademicTermIds, $participationOrgAcademicYearId, $gradesIncluded, $onlyIncludeActiveStudents = true)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'courseOrgAcademicTermIds' => $courseOrgAcademicTermIds,
            'gradesIncluded' => $gradesIncluded,
            'participationOrgAcademicYearId' => $participationOrgAcademicYearId
        ];

        $parameterTypes = [
            'courseOrgAcademicTermIds' => Connection::PARAM_INT_ARRAY,
            'gradesIncluded' => Connection::PARAM_STR_ARRAY
        ];

        if ($onlyIncludeActiveStudents) {
            $activeStudentsClause = " AND opsy.is_active = 1";
        } else {
            $activeStudentsClause = "";
        }

        $sql = "SELECT DISTINCT ofspm.student_id
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                            AND op.organization_id = ofspm.org_id
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ofspm.student_id
                            AND opsy.organization_id = ofspm.org_id
                        INNER JOIN
                    org_course_student ocs
                            ON ocs.person_id = ofspm.student_id
                            AND ocs.organization_id = ofspm.org_id
                        INNER JOIN
                    org_courses oc
                            ON oc.id = ocs.org_courses_id
                            AND oc.organization_id = ofspm.org_id
                        INNER JOIN
                    academic_record ar
                            ON ar.org_courses_id = oc.id
                            AND ar.person_id_student = ofspm.student_id
                            AND ar.organization_id = ofspm.org_id
                        LEFT JOIN
                    org_course_faculty ocf
                            ON ocf.org_courses_id = oc.id
                            AND ocf.organization_id = ofspm.org_id
                            AND ocf.deleted_at IS NULL
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :organizationId
                    AND op.accesslevel_ind_agg = 1
                    AND
                    (
                        (op.view_courses = 1 AND op.view_all_final_grades = 1)
                            OR
                        (op.create_view_academic_update = 1 AND ocf.person_id = :facultyId)
                    )
                    AND oc.org_academic_terms_id IN (:courseOrgAcademicTermIds)
                    AND ar.final_grade IN (:gradesIncluded)
                    AND opsy.org_academic_year_id = :participationOrgAcademicYearId
                    AND opsy.deleted_at IS NULL
                    $activeStudentsClause
                    AND op.deleted_at IS NULL
                    AND ocs.deleted_at IS NULL
                    AND oc.deleted_at IS NULL
                    AND ar.deleted_at IS NULL
                GROUP BY ofspm.student_id
                HAVING COUNT(DISTINCT oc.id) > 1;";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'student_id');

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

    /**
     * Returns a list of student ids for the predefined search "Students with interaction contacts".
     * These are students who have had an interaction contact in any of the given terms (typically current terms).
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param array $orgAcademicTermIds
     * @param int $orgAcademicYearId
     * @param bool $onlyIncludeActiveStudents
     * @return array
     */
    public function getStudentsHavingInteractionContacts($facultyId, $organizationId, $orgAcademicTermIds, $orgAcademicYearId, $onlyIncludeActiveStudents = true)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'orgAcademicTermIds' => $orgAcademicTermIds,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        $parameterTypes = ['orgAcademicTermIds' => Connection::PARAM_INT_ARRAY];

        if ($onlyIncludeActiveStudents) {
            $activeStudentsClause = " AND opsy.is_active = 1";
        } else {
            $activeStudentsClause = "";
        }

        $sql = "SELECT DISTINCT ofspm.student_id
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                            AND op.organization_id = ofspm.org_id
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ofspm.student_id
                            AND opsy.organization_id = ofspm.org_id
                        INNER JOIN
                    contacts c
                            ON c.person_id_student = ofspm.student_id
                            AND c.organization_id = ofspm.org_id
                        INNER JOIN
                    contact_types ct
                            ON ct.id = c.contact_types_id
                        INNER JOIN
                    contact_types_lang ctl
                            ON ctl.contact_types_id = ct.parent_contact_types_id
                        INNER JOIN
                    org_academic_terms oat
                            ON c.contact_date BETWEEN oat.start_date AND oat.end_date
                            AND oat.organization_id = ofspm.org_id
                WHERE
                    op.deleted_at IS NULL
                    AND c.deleted_at IS NULL
                    AND ct.deleted_at IS NULL
                    AND ctl.deleted_at IS NULL
                    AND oat.deleted_at IS NULL
                    AND ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :organizationId
                    AND op.accesslevel_ind_agg = 1
                    AND ctl.description = 'Interaction'
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    AND opsy.deleted_at IS NULL
                    $activeStudentsClause
                    AND oat.id IN (:orgAcademicTermIds);";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'student_id');

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

    /**
     * Returns a list of student ids for the predefined search "Students without any interaction contacts".
     * These are students who have not had an interaction contact in any of the given terms (typically current terms).
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param array $orgAcademicTermIds
     * @param int $orgAcademicYearId
     * @param bool $onlyIncludeActiveStudents
     * @return array
     */
    public function getStudentsNotHavingInteractionContacts($facultyId, $organizationId, $orgAcademicTermIds, $orgAcademicYearId, $onlyIncludeActiveStudents = true)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'orgAcademicTermIds' => $orgAcademicTermIds,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        $parameterTypes = ['orgAcademicTermIds' => Connection::PARAM_INT_ARRAY];

        if ($onlyIncludeActiveStudents) {
            $activeStudentsClause = " AND opsy.is_active = 1";
        } else {
            $activeStudentsClause = "";
        }

        $sql = "SELECT DISTINCT ofspm.student_id
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                            AND op.organization_id = ofspm.org_id
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ofspm.student_id
                            AND opsy.organization_id = ofspm.org_id
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :organizationId
                    AND op.accesslevel_ind_agg = 1
                    AND op.deleted_at IS NULL
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    AND opsy.deleted_at IS NULL
                    $activeStudentsClause
                    AND NOT EXISTS (
                        SELECT 1
                        FROM
                            contacts c
                                INNER JOIN
                            contact_types ct
                                    ON ct.id = c.contact_types_id
                                INNER JOIN
                            contact_types_lang ctl
                                    ON ctl.contact_types_id = ct.parent_contact_types_id
                                INNER JOIN
                            org_academic_terms oat
                                    ON c.contact_date BETWEEN oat.start_date AND oat.end_date
                        WHERE
                            c.deleted_at IS NULL
                            AND ct.deleted_at IS NULL
                            AND ctl.deleted_at IS NULL
                            AND oat.deleted_at IS NULL
                            AND ctl.description = 'Interaction'
                            AND oat.id IN (:orgAcademicTermIds)
                            AND c.person_id_student = ofspm.student_id
                            AND c.organization_id = ofspm.org_id
                            AND oat.organization_id = ofspm.org_id
                    );";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'student_id');

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

    /**
     * Returns a list of student ids for the predefined search "Students who have not been reviewed by me since their risk changed".
     * These are students whose profile page the user has never viewed or whose risk score has changed since the user has viewed their profile page.
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param int $orgAcademicYearId
     * @param bool $onlyIncludeActiveStudents
     * @return array
     */
    public function getUnreviewedStudents($facultyId, $organizationId, $orgAcademicYearId, $onlyIncludeActiveStudents = true)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        if ($onlyIncludeActiveStudents) {
            $activeStudentsClause = " AND opsy.is_active = 1";
        } else {
            $activeStudentsClause = "";
        }

        $sql = "SELECT DISTINCT ofspm.student_id
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op
                            ON op.id = ofspm.permissionset_id
                            AND op.organization_id = ofspm.org_id
                         INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ofspm.student_id
                            AND opsy.organization_id = ofspm.org_id
                         INNER JOIN
                    person p
                            ON p.id = ofspm.student_id
                            AND p.organization_id = ofspm.org_id
                        LEFT JOIN
                    student_db_view_log sdvl
                            ON sdvl.person_id_student = ofspm.student_id
                            AND sdvl.person_id_faculty = ofspm.faculty_id
                            AND sdvl.organization_id = ofspm.org_id
                            AND sdvl.deleted_at IS NULL
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :organizationId
                    AND op.accesslevel_ind_agg = 1
                    AND (sdvl.last_viewed_on < p.risk_update_date OR sdvl.person_id_faculty IS NULL)
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    AND opsy.deleted_at IS NULL
                    $activeStudentsClause
                    AND op.deleted_at IS NULL
                    AND p.deleted_at IS NULL;";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'student_id');

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

}