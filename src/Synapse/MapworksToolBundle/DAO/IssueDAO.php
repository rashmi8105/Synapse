<?php

namespace Synapse\MapworksToolBundle\DAO;

use Doctrine\DBAL\Connection;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\SynapseConstant;


/**
 * @DI\Service("issue_dao")
 */
class IssueDAO
{
    const DAO_KEY = 'issue_dao';

    /**
     * @var Connection
     */
    private $connection;


    /**
     * IssueDAO constructor
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
     * generates Temporary Table student_issues
     * Activates Stored Procedure StudentIssuesCalculation
     *
     * @param integer $organizationId
     * @param integer $facultyId
     * @param integer $orgAcademicYearId
     * @param integer $surveyId
     * @param integer $cohort
     * @return array
     */
    public function generateStudentIssuesTemporaryTable($organizationId, $facultyId, $orgAcademicYearId, $surveyId, $cohort)
    {

        $statement = $this->connection->prepare('CALL StudentIssuesCalculation(:organizationId, :facultyId, :orgAcademicYearId, :surveyId, :cohort)');
        try {
            $statement->execute([
                'organizationId' => $organizationId,
                'facultyId' => $facultyId,
                'orgAcademicYearId' => $orgAcademicYearId,
                'surveyId' => $surveyId,
                'cohort' => $cohort
            ]);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return true;
    }


    /**
     * gets the top Issues and percentage of given population
     * Note: This code is dependent upon the stored procedure being run, triggered by IssueDAO::generateStudentIssuesTemporaryTable
     *
     * @param integer $numberOfIssues
     * @param array $studentIds
     * @return array
     */
    public function getTopIssuesFromStudentIssues($numberOfIssues, $studentIds, $minimumStudentsForTopIssues = SynapseConstant::MINIMUM_THRESHOLD_OF_STUDENTS_FOR_TOP_ISSUES)
    {

        $parameters = [
            'numberOfIssues' => $numberOfIssues,
            'minimumStudentsForTopIssues' => $minimumStudentsForTopIssues
        ];

        $parameterTypes = [
            'numberOfIssues' => 'integer',
            'minimumStudentsForTopIssues' => 'integer'
        ];
        $condition = ' ';
        if (!empty($studentIds)) {
            $parameters['studentIds'] = $studentIds;
            $parameterTypes['studentIds'] = Connection::PARAM_INT_ARRAY;
            $condition = " WHERE si.student_id IN (:studentIds) ";
        }

        $sql = "SELECT
                    si.issue_id,
                    il.name AS issue_name,
                    SUM(si.has_issue) AS numerator,
                    COUNT(*) AS denominator,
                    ROUND((SUM(has_issue)/COUNT(*)) * 100, 1) AS percent,
                    si.icon
                FROM
                    synapse.student_issues AS si
                INNER JOIN
                    issue_lang il ON il.issue_id = si.issue_id
                        AND il.deleted_at IS NULL
                $condition
                GROUP BY issue_id
                HAVING denominator > :minimumStudentsForTopIssues
                ORDER BY percent DESC, denominator DESC, issue_id DESC
                LIMIT :numberOfIssues;";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results;
    }


    /**
     * get List of Students given a set of Issues from student_issues
     * Note: This code is dependent upon the stored procedure being run, triggered by IssueDAO::generateStudentIssuesTemporaryTable
     *
     * @param array $issueIds
     * @return array
     */
    public function getStudentListFromStudentIssues($issueIds)
    {
        $parameters = [
            'issueIds' => $issueIds,
        ];
        $parameterTypes['issueIds'] = Connection::PARAM_INT_ARRAY;

        $sql = "SELECT DISTINCT
                    si.student_id
                FROM
                    student_issues AS si
                WHERE
                    si.has_issue = 1
                    AND si.issue_id IN (:issueIds);";

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
     * gets count of unique students in temporary table student_issues.
     * Note: This code is dependent upon the stored procedure being run, triggered by IssueDAO::generateStudentIssuesTemporaryTable
     *
     * @param array $issueIds
     * @return int
     */
    public function getDistinctStudentPopulationCount($issueIds)
    {
        $parameters = [
            'issueIds' => $issueIds,
        ];
        $parameterTypes['issueIds'] = Connection::PARAM_INT_ARRAY;


        $sql = "SELECT
                    COUNT(DISTINCT si.student_id) AS totalStudentCount
                FROM
                    student_issues AS si
                WHERE
                    si.issue_id IN (:issueIds)";
        try {
            $stmt = $this->connection->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results[0]['totalStudentCount'];
    }

    /**
     * gets count of unique students in temporary table student_issues.
     * Note: This code is dependent upon the stored procedure being run, triggered by IssueDAO::generateStudentIssuesTemporaryTable
     *
     * @param array $issueIds
     * @param int $orgAcademicYearId
     * @param int $hasIssue - 1 or 0
     * @return int
     */
    public function getDistinctParticipantStudentPopulationCount($issueIds, $orgAcademicYearId, $hasIssue = null)
    {
        $parameters = [
            'issueIds' => $issueIds,
            'orgAcademicYearId' => $orgAcademicYearId
        ];
        $parameterTypes['issueIds'] = Connection::PARAM_INT_ARRAY;

        $hasIssueString = '';
        if ($hasIssue) {
            $parameters['hasIssue'] = $hasIssue;
            $hasIssueString = " AND si.has_issue = :hasIssue ";
        }


        $sql = "SELECT
                    COUNT(DISTINCT si.student_id) AS totalStudentCount
                FROM
                    student_issues AS si
                    INNER JOIN org_person_student_year opsy ON opsy.person_id = si.student_id
                        AND opsy.org_academic_year_id = :orgAcademicYearId
                WHERE
                    opsy.deleted_at IS NULL
                    AND si.issue_id IN (:issueIds)
                    $hasIssueString";
        try {
            $stmt = $this->connection->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results[0]['totalStudentCount'];
    }
}