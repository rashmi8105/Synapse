<?php
namespace Synapse\ReportsBundle\Repository;

use Facile\DoctrineMySQLComeBack\Doctrine\DBAL\Connection;
use Symfony\Component\Config\Definition\Exception\Exception;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\ReportsBundle\Entity\OrgPersonStudentRetentionTrackingGroup;

class OrgPersonStudentRetentionTrackingGroupRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseReportsBundle:OrgPersonStudentRetentionTrackingGroup';


    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return OrgPersonStudentRetentionTrackingGroup|null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return OrgPersonStudentRetentionTrackingGroup[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     *
     * @return OrgPersonStudentRetentionTrackingGroup|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * Returns the retention and completion values for students for a given organization
     *
     * @param int $organizationId
     * @param int|null $yearId
     * @param array|null $studentIds
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getRetentionCompletionVariablesByOrganization($organizationId, $yearId = null, $studentIds = [])
    {
        $yearIdString = '';
        $studentIdsString = '';

        $parameters = ['organizationId' => $organizationId];

        $parameterTypes = [];

        if ($yearId) {
            $parameters['yearId'] = $yearId;
            $yearIdString = ' AND opsrcpv.retention_tracking_year = :yearId ';
        }

        if (!empty($studentIds)) {
            $parameters['studentIds'] = $studentIds;
            $parameterTypes['studentIds'] = Connection::PARAM_INT_ARRAY;
            $studentIdsString = ' AND ps.person_id IN ( :studentIds ) ';
        }

        $sql = "SELECT
                    external_id,
                    firstname,
                    lastname,
                    primary_email,
                    retention_tracking_year,
                    retention_tracking_year_name,
                    retained_to_midyear_year_1,
                    retained_to_start_of_year_2,
                    retained_to_midyear_year_2,
                    retained_to_start_of_year_3,
                    retained_to_midyear_year_3,
                    retained_to_start_of_year_4,
                    retained_to_midyear_year_4,
                    completed_degree_in_1_year_or_less,
                    CASE
                        WHEN completed_degree_in_2_years_or_less IS NULL THEN NULL
                        WHEN completed_degree_in_1_year_or_less = 1 THEN 1
                        ELSE completed_degree_in_2_years_or_less
                    END AS completed_degree_in_2_years_or_less,
                    CASE
                        WHEN completed_degree_in_3_years_or_less IS NULL THEN NULL
                        WHEN completed_degree_in_1_year_or_less = 1 THEN 1
                        WHEN completed_degree_in_2_years_or_less = 1 THEN 1
                        ELSE completed_degree_in_3_years_or_less
                    END AS completed_degree_in_3_years_or_less,
                    CASE
                        WHEN completed_degree_in_4_years_or_less IS NULL THEN NULL
                        WHEN completed_degree_in_1_year_or_less = 1 THEN 1
                        WHEN completed_degree_in_2_years_or_less = 1 THEN 1
                        WHEN completed_degree_in_3_years_or_less = 1 THEN 1
                        ELSE completed_degree_in_4_years_or_less
                    END AS completed_degree_in_4_years_or_less,
                    CASE
                        WHEN completed_degree_in_5_years_or_less IS NULL THEN NULL
                        WHEN completed_degree_in_1_year_or_less = 1 THEN 1
                        WHEN completed_degree_in_2_years_or_less = 1 THEN 1
                        WHEN completed_degree_in_3_years_or_less = 1 THEN 1
                        WHEN completed_degree_in_4_years_or_less = 1 THEN 1
                        ELSE completed_degree_in_5_years_or_less
                    END AS completed_degree_in_5_years_or_less,
                    CASE
                        WHEN completed_degree_in_6_years_or_less IS NULL THEN NULL
                        WHEN completed_degree_in_1_year_or_less = 1 THEN 1
                        WHEN completed_degree_in_2_years_or_less = 1 THEN 1
                        WHEN completed_degree_in_3_years_or_less = 1 THEN 1
                        WHEN completed_degree_in_4_years_or_less = 1 THEN 1
                        WHEN completed_degree_in_5_years_or_less = 1 THEN 1
                        ELSE completed_degree_in_6_years_or_less
                    END AS completed_degree_in_6_years_or_less
                FROM
                    (SELECT
                        ps.external_id,
                        ps.firstname,
                        ps.lastname,
                        ps.username AS primary_email,
                        opsrcpv.retention_tracking_year,
                        oay.name AS retention_tracking_year_name,
                        MAX(opsrcpv.retained_to_midyear_year_1) AS retained_to_midyear_year_1,
                        MAX(opsrcpv.retained_to_start_of_year_2) AS retained_to_start_of_year_2,
                        MAX(opsrcpv.retained_to_midyear_year_2) AS retained_to_midyear_year_2,
                        MAX(opsrcpv.retained_to_start_of_year_3) AS retained_to_start_of_year_3,
                        MAX(opsrcpv.retained_to_midyear_year_3) AS retained_to_midyear_year_3,
                        MAX(opsrcpv.retained_to_start_of_year_4) AS retained_to_start_of_year_4,
                        MAX(opsrcpv.retained_to_midyear_year_4) AS retained_to_midyear_year_4,
                        MAX(opsrcpv.completed_degree_in_1_year_or_less) AS completed_degree_in_1_year_or_less,
                        MAX(opsrcpv.completed_degree_in_2_years_or_less) AS completed_degree_in_2_years_or_less,
                        MAX(opsrcpv.completed_degree_in_3_years_or_less) AS completed_degree_in_3_years_or_less,
                        MAX(opsrcpv.completed_degree_in_4_years_or_less) AS completed_degree_in_4_years_or_less,
                        MAX(opsrcpv.completed_degree_in_5_years_or_less) AS completed_degree_in_5_years_or_less,
                        MAX(opsrcpv.completed_degree_in_6_years_or_less) AS completed_degree_in_6_years_or_less
                    FROM
                        person_search ps
                            INNER JOIN
                        org_person_student_retention_completion_pivot_view opsrcpv ON ps.person_id = opsrcpv.person_id
                            INNER JOIN
                        org_academic_year oay ON oay.year_id = opsrcpv.retention_tracking_year
                            AND oay.organization_id = ps.organization_id
                    WHERE
                        ps.organization_id = :organizationId
                        $yearIdString
                        $studentIdsString
                    GROUP BY opsrcpv.organization_id, opsrcpv.person_id, opsrcpv.retention_tracking_year) as var_query;";

        $result = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);

        return $result;
    }

    /**
     * Gets Retention tracking groups for an organization
     *
     * @param integer $organizationId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getRetentionTrackingGroupsForOrganization($organizationId)
    {
        $parameters = [
            'organizationId' => $organizationId
        ];

        $sql = "
                SELECT
                     retention_tracking_year as year_id,
                     year_name
                FROM
                    org_person_student_retention_tracking_group_view
                WHERE
                    organization_id = :organizationId
                GROUP BY
                    retention_tracking_year,
                    year_name
                ";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
            $result = $stmt->fetchAll();
            return $result;
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }

    /**
     * Gets the retention and completion variables based on the retention tracking group
     *
     * @param integer $organizationId
     * @param string $retentionTrackGroup
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getRetentionAndCompletionVariables($organizationId, $retentionTrackGroup)
    {

        $parameters = [
            'retentionTrackingGroup' => $retentionTrackGroup,
            'organizationId' => $organizationId
        ];

        $sql = "
                SELECT 
                  DISTINCT
                    year_id,
                    year_name, 
                    name_text AS retention_completion_name_text
                FROM
                    org_person_student_retention_completion_names_view
                WHERE
                    retention_tracking_year = :retentionTrackingGroup
                        AND organization_id = :organizationId
                ORDER BY years_from_retention_track ASC, retention_completion_name_text DESC
                ";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
            $result = $stmt->fetchAll();
            return $result;
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

    }


    /**
     * Returns true/false based on if students are associated with the retention tracking group
     *
     * @param integer $organizationId
     * @param integer $orgAcademicYearId
     * @return bool
     */
    public function areStudentsAssignedToThisRetentionTrackingYear($organizationId, $orgAcademicYearId)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'orgAcademicYearId' => $orgAcademicYearId,
        ];
        $sql = "SELECT
                    1
                FROM
                    org_person_student_retention_tracking_group opsrtg
                WHERE
                    opsrtg.organization_id = :organizationId
                        AND opsrtg.org_academic_year_id = :orgAcademicYearId
                LIMIT 1";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
            $result = $stmt->fetchAll();
            if (!empty($result[0])) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }


    /**
     * Gets the RetentionTracking group for an organization
     *
     * @param integer $organizationId
     * @param string $yearLimit - e.g "201617" -  The query would find out all the retention tracking year on or before 201617
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getRetentionTrackingOrgAcademicYearIdsForOrganization($organizationId, $yearLimit)
    {

        $parameters = [
            'organizationId' => $organizationId,
            'yearLimit' => $yearLimit
        ];

        $sql = "SELECT 
                  DISTINCT opsrtg.org_academic_year_id
                FROM 
                  org_person_student_retention_tracking_group opsrtg
                INNER JOIN 
                  org_academic_year oay 
                    ON oay.id = opsrtg.org_academic_year_id
                WHERE 
                  opsrtg.organization_id = :organizationId
                    AND oay.year_id <= :yearLimit
                    AND oay.deleted_at IS NULL
                    AND opsrtg.deleted_at IS NULL
                ORDER BY oay.year_id DESC";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
            $records = $stmt->fetchAll();
            $orgAcademicYearIds = array_column($records, 'org_academic_year_id');
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $orgAcademicYearIds;
    }


    /*
     * Gets the list of students for a given retention tracking year for an organization
     *
     * @param integer $organizationId
     * @param integer $retentionTrackingYearId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getRetentionTrackingGroupStudents($organizationId, $retentionTrackingYearId)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'retentionTrackingYearId' => $retentionTrackingYearId
        ];

        $sql = "
            SELECT     
              person_id
            FROM
                org_person_student_retention_tracking_group
            WHERE
                organization_id = :organizationId
                    AND org_academic_year_id = :retentionTrackingYearId
                    AND deleted_at IS NULL";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
            $result = $stmt->fetchAll();
            if (!empty($result)) {
                $retentionTrackingGroupStudents = array_column($result, "person_id");
                return $retentionTrackingGroupStudents;
            } else {
                return [];
            }
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

    }

}
