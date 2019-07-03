<?php

namespace Synapse\ReportsBundle\DAO;

use Doctrine\DBAL\Connection;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;


/**
 * @DI\Service("our_students_report_dao")
 */
class OurStudentsReportDAO
{

    const DAO_KEY = 'our_students_report_dao';

    /**
     * @var Connection
     */
    private $connection;


    /**
     * OurStudentsReportDAO constructor
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
     * For elements in the Our Students Report which are based on factors,
     * returns counts of how many of the given students have a value for the factor (denominator_count)
     * and how many have a value in a system-specified range (numerator_count).
     * Excludes students from these counts if the user does not have the appropriate survey block permissions.
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param int $surveyId
     * @param array $studentIds
     * @return array
     */
    public function getFactorResponseCounts($facultyId, $organizationId, $surveyId, $studentIds)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'surveyId' => $surveyId,
            'studentIds' => $studentIds
        ];

        $parameterTypes = [
            'studentIds' => Connection::PARAM_INT_ARRAY
        ];

        $sql = "SELECT
                    rse.section_id,
                    rs.title AS section_name,
                    reb.element_id,
                    rse.title AS element_name,
                    SUM(IF(reb.bucket_name = 'Numerator', 1, 0)) AS numerator_count,
                    SUM(IF(reb.bucket_name = 'Denominator', 1, 0)) AS denominator_count
                FROM
                    reports r
                        INNER JOIN
                    report_sections rs ON rs.report_id = r.id
                        INNER JOIN
                    report_section_elements rse ON rse.section_id = rs.id
                        INNER JOIN
                    report_element_buckets reb ON reb.element_id = rse.id
                        INNER JOIN
                    (
                        SELECT DISTINCT
                            pfc.person_id,
                            pfc.factor_id,
                            pfc.mean_value
                        FROM
                            org_faculty_student_permission_map ofspm
                                INNER JOIN
                            org_permissionset_datablock opd ON opd.org_permissionset_id = ofspm.permissionset_id AND opd.organization_id = ofspm.org_id
                                INNER JOIN
                            datablock_questions dq ON dq.datablock_id = opd.datablock_id
                                INNER JOIN
                            person_factor_calculated pfc ON pfc.factor_id = dq.factor_id AND pfc.person_id = ofspm.student_id and pfc.organization_id = ofspm.org_id
                        WHERE
                            ofspm.faculty_id = :facultyId
                            AND ofspm.org_id = :organizationId
                            AND pfc.survey_id = :surveyId
                            AND pfc.person_id IN (:studentIds)
                            AND opd.deleted_at IS NULL
                            AND dq.deleted_at IS NULL
                            AND pfc.deleted_at IS NULL
                    ) AS response
                            ON response.factor_id = rse.factor_id
                            AND response.mean_value BETWEEN reb.range_min AND reb.range_max
                WHERE
                    r.short_code = 'OSR'
                    AND r.deleted_at IS NULL
                    AND rs.deleted_at IS NULL
                    AND rse.deleted_at IS NULL
                    AND reb.deleted_at IS NULL
                GROUP BY reb.element_id;";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }


    /**
     * For elements in the Our Students Report which are based on survey questions,
     * returns counts of how many of the given students answered the question (denominator_count)
     * and how many have a value in a system-specified range (numerator_count).
     * Excludes students from these counts if the user does not have the appropriate survey block permissions.
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param int $surveyId
     * @param array $studentIds
     * @return array
     */
    public function getSurveyResponseCounts($facultyId, $organizationId, $surveyId, $studentIds)
    {
        $parameters = [
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'surveyId' => $surveyId,
            'studentIds' => $studentIds
        ];

        $parameterTypes = [
            'studentIds' => Connection::PARAM_INT_ARRAY
        ];

        $sql = "SELECT
                    rse.section_id,
                    rs.title AS section_name,
                    reb.element_id,
                    rse.title AS element_name,
                    SUM(IF(reb.bucket_name = 'Numerator', 1, 0)) AS numerator_count,
                    SUM(IF(reb.bucket_name = 'Denominator', 1, 0)) AS denominator_count
                FROM
                    reports r
                        INNER JOIN
                    report_sections rs ON rs.report_id = r.id
                        INNER JOIN
                    report_section_elements rse ON rse.section_id = rs.id
                        INNER JOIN
                    report_element_buckets reb ON reb.element_id = rse.id
                        INNER JOIN
                    (
                        SELECT DISTINCT
                            sr.person_id,
                            qbm.question_bank_id,
                            sr.decimal_value
                        FROM
                            org_faculty_student_permission_map ofspm
                                INNER JOIN
                            org_permissionset_datablock opd ON opd.org_permissionset_id = ofspm.permissionset_id AND opd.organization_id = ofspm.org_id
                                INNER JOIN
                            datablock_questions dq ON dq.datablock_id = opd.datablock_id
                                INNER JOIN
                            question_bank_map qbm ON qbm.question_bank_id = dq.question_bank_id
                                INNER JOIN
                            survey_response sr FORCE INDEX(fk_survey_response_person1) ON sr.survey_questions_id = qbm.survey_question_id AND sr.survey_id = qbm.survey_id AND sr.person_id = ofspm.student_id AND sr.org_id = ofspm.org_id
                        WHERE
                            ofspm.faculty_id = :facultyId
                            AND ofspm.org_id = :organizationId
                            AND sr.survey_id = :surveyId
                            AND sr.person_id IN (:studentIds)
                            AND opd.deleted_at IS NULL
                            AND dq.deleted_at IS NULL
                            AND qbm.deleted_at IS NULL
                            AND sr.deleted_at IS NULL
                    ) AS response
                            ON response.question_bank_id = rse.question_bank_id
                            AND response.decimal_value BETWEEN reb.range_min AND reb.range_max
                WHERE
                    r.short_code = 'OSR'
                    AND r.deleted_at IS NULL
                    AND rs.deleted_at IS NULL
                    AND rse.deleted_at IS NULL
                    AND reb.deleted_at IS NULL
                GROUP BY reb.element_id;";

        try {
            $stmt = $this->connection->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }

}