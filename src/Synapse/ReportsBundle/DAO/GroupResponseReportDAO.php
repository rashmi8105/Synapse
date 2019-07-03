<?php

namespace Synapse\ReportsBundle\DAO;

use Doctrine\DBAL\Connection;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;


/**
 * @DI\Service("group_response_report_dao")
 */
class GroupResponseReportDAO
{
    const DAO_KEY = 'group_response_report_dao';

    /**
     * @var Connection
     */
    private $connection;


    /**
     * GroupResponseReportDAO constructor
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
     * Returns data for the Group Response Report, i.e., aggregate data about survey response rates for all the user's groups,
     * including all subgroups (for the given survey and cohort).
     *
     * @param int $facultyId
     * @param int $orgAcademicYearId
     * @param int $cohort
     * @param int $surveyId
     * @param array $filterCriteria -- associative array, possibly including text to filter on for various columns or whether to include only subgroups or top-level groups
     * @return array
     */
    public function getOverallCountGroupStudentCountAndResponseRateByFaculty($facultyId, $orgAcademicYearId, $cohort, $surveyId, $filterCriteria = [])
    {
        $parameters = [
            'facultyId' => $facultyId,
            'orgAcademicYearId' => $orgAcademicYearId,
            'surveyId' => $surveyId,
            'cohort' => $cohort
        ];


        // Only include subgroups or top-level groups, if requested.
        if (isset($filterCriteria['group_selection'])) {
            switch ($filterCriteria['group_selection']) {
                case 'all_groups':
                    $parentGroupCondition = '';
                    break;
                case 'top_level_groups':
                    $parentGroupCondition = 'AND og_child.parent_group_id IS NULL';
                    break;
                case 'sub_groups':
                    $parentGroupCondition = 'AND og_child.parent_group_id IS NOT NULL';
                    break;
                default:
                    $parentGroupCondition = '';
                    break;
            }
        } else {
            $parentGroupCondition = '';
        }

        // Filter various columns based on text entered by the user.
        if (!empty($filterCriteria['external_id'])) {
            $parameters['externalId'] = '%' . $filterCriteria['external_id'] . '%';
            $externalIdSQLsubstring = "AND og_child.external_id LIKE :externalId";
        } else {
            $externalIdSQLsubstring = '';
        }

        if (!empty($filterCriteria['group_name'])) {
            $parameters['groupName'] = '%' . $filterCriteria['group_name'] . '%';
            $groupNameSQLsubstring = "AND og_child.group_name LIKE :groupName";
        } else {
            $groupNameSQLsubstring = '';
        }

        if (!empty($filterCriteria['parent_group'])) {
            $parameters['parentName'] = '%' . $filterCriteria['parent_group'] . '%';
            $parentNameSQLsubstring = "AND og_parent.group_name LIKE :parentName";
        } else {
            $parentNameSQLsubstring = '';
        }

        // Get overall counts for the top of the report.
        $sql = "SELECT
                        COUNT(person_id) AS student_id_cnt,
                        SUM(CASE WHEN Has_Responses = 'Yes' THEN 1 ELSE 0 END) AS responded
                    FROM
                        (SELECT DISTINCT
                            opssl.person_id, opssl.Has_Responses
                        FROM
                            org_person_student_survey_link opssl
                        INNER JOIN org_person_student_cohort opsc ON opsc.organization_id = opssl.org_id
                            AND opsc.person_id = opssl.person_id
                            AND opsc.cohort = opssl.cohort
                            AND opsc.org_academic_year_id = opssl.org_academic_year_id
                        INNER JOIN org_person_student_survey opss ON opss.organization_id = opssl.org_id
                            AND opss.person_id = opssl.person_id
                            AND opss.survey_id = opssl.survey_id
                        INNER JOIN org_group_faculty_student_permission_map ogfsp ON ogfsp.faculty_id = :facultyId
                            AND ogfsp.student_id = opssl.person_id
                        INNER JOIN org_group og_child on og_child.id = ogfsp.student_group_id
                        LEFT JOIN org_group og_parent on og_parent.id = og_child.parent_group_id
                        WHERE
                            opssl.deleted_at IS NULL
                                AND opsc.deleted_at IS NULL
                                AND opss.deleted_at IS NULL
                                AND opssl.survey_id = :surveyId
                                AND (opss.receive_survey = 1
                                OR opssl.Has_Responses = 'Yes')
                                AND opsc.cohort = :cohort
                                AND opsc.org_academic_year_id = :orgAcademicYearId
                                $parentGroupCondition
                                $externalIdSQLsubstring
                                $groupNameSQLsubstring
                                $parentNameSQLsubstring) AS students
";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters);
            $results = $stmt->fetchAll();

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }


    /**
     * Returns data for the Group Response Report; counts for the top of the report including all students in any of the user's groups;
     * otherwise, returns response rates by group.
     *
     * Note: The org_faculty_student_permission_map is intentionally not used here because we only want group connections, not course connections.
     *
     * @param int $facultyId
     * @param int $orgAcademicYearId
     * @param int $cohort
     * @param int $surveyId
     * @param array $filterCriteria -- associative array, possibly including text to filter on for various columns or whether to include only subgroups or top-level groups
     * @param string|null $sortBy -- column to sort by
     * @param string|null $sortDirection -- "ASC" or "DESC"
     * @return array
     */
    public function getGroupStudentCountAndResponseRateByFaculty($facultyId, $orgAcademicYearId, $cohort, $surveyId, $filterCriteria = [], $sortBy = null, $sortDirection = null)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'orgAcademicYearId' => $orgAcademicYearId,
            'surveyId' => $surveyId,
            'cohort' => $cohort
        ];

        // Set the column to sort by and the direction.
        // This is done this tedious way to prevent SQL injection.  Unfortunately, "ORDER BY :sortBy" doesn't work.
        switch ($sortBy) {
            case 'external_id':
                $sortSQLsubstring = "ORDER BY external_id $sortDirection, group_name $sortDirection, parent_group $sortDirection, org_group_id $sortDirection";
                break;
            case 'group_name':
                $sortSQLsubstring = "ORDER BY group_name $sortDirection, parent_group $sortDirection, org_group_id $sortDirection";
                break;
            case 'parent_group':
                $sortSQLsubstring = "ORDER BY parent_group $sortDirection, group_name $sortDirection, org_group_id $sortDirection";
                break;
            case 'student_id_cnt':
                $sortSQLsubstring = "ORDER BY student_id_cnt $sortDirection, group_name $sortDirection, parent_group $sortDirection, org_group_id $sortDirection";
                break;
            case 'responded':
                $sortSQLsubstring = "ORDER BY responded $sortDirection, group_name $sortDirection, parent_group $sortDirection, org_group_id $sortDirection";
                break;
            case 'response_rate':
                $sortSQLsubstring = "ORDER BY response_rate $sortDirection, group_name $sortDirection, parent_group $sortDirection, org_group_id $sortDirection";
                break;
            default:
                $sortSQLsubstring = "ORDER BY group_name $sortDirection, parent_group $sortDirection, org_group_id $sortDirection";
                break;
        }

        // Only include subgroups or top-level groups, if requested.
        if (isset($filterCriteria['group_selection'])) {
            switch ($filterCriteria['group_selection']) {
                case 'all_groups':
                    $parentGroupCondition = '';
                    break;
                case 'top_level_groups':
                    $parentGroupCondition = 'AND og_child.parent_group_id IS NULL';
                    break;
                case 'sub_groups':
                    $parentGroupCondition = 'AND og_child.parent_group_id IS NOT NULL';
                    break;
                default:
                    $parentGroupCondition = '';
                    break;
            }
        } else {
            $parentGroupCondition = '';
        }

        // Filter various columns based on text entered by the user.
        if (!empty($filterCriteria['external_id'])) {
            $parameters['externalId'] = '%' . $filterCriteria['external_id'] . '%';
            $externalIdSQLsubstring = "AND og_child.external_id LIKE :externalId";
        } else {
            $externalIdSQLsubstring = '';
        }

        if (!empty($filterCriteria['group_name'])) {
            $parameters['groupName'] = '%' . $filterCriteria['group_name'] . '%';
            $groupNameSQLsubstring = "AND og_child.group_name LIKE :groupName";
        } else {
            $groupNameSQLsubstring = '';
        }

        if (!empty($filterCriteria['parent_group'])) {
            $parameters['parentName'] = '%' . $filterCriteria['parent_group'] . '%';
            $parentNameSQLsubstring = "AND og_parent.group_name LIKE :parentName";
        } else {
            $parentNameSQLsubstring = '';
        }


            // Get the response rate for each of the user's groups.
            $sql = "
SELECT
    og_child.group_name,
    org_group_id,
    og_child.external_id,
    og_parent.group_name AS parent_group,
    student_id_cnt,
    responded,
    ROUND(100 * responded / student_id_cnt) AS response_rate
FROM
    (SELECT
        org_group_id,
            COUNT(DISTINCT person_id) AS student_id_cnt,
            SUM(CASE
                WHEN Has_Responses = 'Yes' THEN 1
                ELSE 0
            END) AS responded
    FROM
        (SELECT DISTINCT
        ogt.ancestor_group_id AS org_group_id,
            opssl.person_id,
            opssl.Has_Responses
    FROM
        org_person_student_survey_link opssl
    INNER JOIN org_person_student_cohort opsc ON opsc.organization_id = opssl.org_id
        AND opsc.person_id = opssl.person_id
        AND opsc.cohort = opssl.cohort
        AND opsc.org_academic_year_id = opssl.org_academic_year_id
    INNER JOIN org_person_student_survey opss ON opss.organization_id = opssl.org_id
        AND opss.person_id = opssl.person_id
        AND opss.survey_id = opssl.survey_id
    INNER JOIN org_group_students ops ON ops.person_id = opssl.person_id
        AND ops.deleted_at IS NULL
    INNER JOIN org_group_tree ogt ON ogt.descendant_group_id = ops.org_group_id
        AND ogt.deleted_at IS NULL
    INNER JOIN org_group_tree ogt2 ON ogt2.descendant_group_id = ogt.ancestor_group_id
        AND ogt2.deleted_at IS NULL
    INNER JOIN org_group_faculty ogf ON ogf.person_id = :facultyId
        AND ogf.org_group_id = ogt2.ancestor_group_id
        AND ogf.deleted_at IS NULL
    WHERE
        opssl.deleted_at IS NULL
            AND opsc.deleted_at IS NULL
            AND opss.deleted_at IS NULL
            AND opssl.survey_id = :surveyId
            AND (opss.receive_survey = 1
            OR opssl.Has_Responses = 'Yes')
            AND opsc.cohort = :cohort
            AND opsc.org_academic_year_id = :orgAcademicYearId) AS students
    GROUP BY org_group_id) AS counts
        LEFT JOIN
    org_group og_child ON og_child.id = counts.org_group_id
        LEFT JOIN
    org_group og_parent ON og_parent.id = og_child.parent_group_id
        WHERE
            1 = 1
            $parentGroupCondition
            $externalIdSQLsubstring
            $groupNameSQLsubstring
            $parentNameSQLsubstring
        $sortSQLsubstring
";


        try {
            $stmt = $this->connection->executeQuery($sql, $parameters);
            $results = $stmt->fetchAll();

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

}