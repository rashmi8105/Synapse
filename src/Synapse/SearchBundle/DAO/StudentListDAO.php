<?php

namespace Synapse\SearchBundle\DAO;

use Doctrine\DBAL\Connection;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\SynapseConstant;


/**
 * @DI\Service("student_list_dao")
 */
class StudentListDAO
{

    const DAO_KEY = 'student_list_dao';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * StudentListDAO constructor
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
     * Given a list of $studentIds, returns the list, or possibly the requested portion of it (as determined by $offset and $recordsPerPage if not null),
     * with additional data, including name, external id, email, status, risk, intent to leave, class level, activity count, and date and type of last activity.
     *
     * @param array $studentIds
     * @param int $classLevelEbiMetadataId
     * @param string $timeZoneString -- a standardized string such as "US/Central" representing the organization's timezone
     * @param string $sortBy
     * @param int|null $offset
     * @param int|null $recordsPerPage
     * @return array
     */
    public function getAdditionalStudentData($studentIds, $classLevelEbiMetadataId, $timeZoneString, $sortBy, $offset = null, $recordsPerPage = null, $academicYearDateTimeString = null)
    {
        if (is_null($academicYearDateTimeString)) {
            $academicYearDateTimeObject = new \DateTime();
            $academicYearDateTimeString = $academicYearDateTimeObject->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
        }

        if (empty($studentIds)) {
            return [];
        }

        if (empty($timeZoneString)) {
            $timeZoneString = 'UTC';
        }

        $parameters = [
            'studentIds' => $studentIds,
            'classLevelEbiMetadataId' => $classLevelEbiMetadataId,
            'timeZoneString' => $timeZoneString,
            'academicYearDateTime' => $academicYearDateTimeString
        ];

        $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

        if (isset($recordsPerPage)) {
            $parameters['recordsPerPage'] = (int)$recordsPerPage;
            $parameters['offset'] = $offset;
            $parameterTypes['recordsPerPage'] = 'integer';
            $parameterTypes['offset'] = 'integer';
            $limitClause = 'LIMIT :recordsPerPage OFFSET :offset';
        } else {
            $limitClause = '';
        }

        switch ($sortBy) {
            case 'student_last_name':
            case '+student_last_name':
                $orderByClause = 'ORDER BY student_last_name, student_first_name, student_primary_email, student_id';
                break;
            case '-student_last_name':
                $orderByClause = 'ORDER BY student_last_name DESC, student_first_name DESC, student_primary_email DESC, student_id DESC';
                break;
            case 'student_risk_status':
            case '+student_risk_status':
                // "Ascending" risk should start with green, which has the highest id (aside from gray) in the database.
                //Report Sequence Order fits with this particular order, with gray risk last
                $orderByClause = 'ORDER BY rl.report_sequence ASC, student_last_name, student_first_name, student_primary_email, student_id';
                break;
            case '-student_risk_status':
                // "Descending" risk should start with red2, which has the lowest id in the database.
                // Natural risk level id order should suffice, with red2 first and gray last
                $orderByClause = 'ORDER BY rl.id ASC, student_last_name, student_first_name, student_primary_email, student_id';
                break;
            case 'student_intent_to_leave':
            case '+student_intent_to_leave':
                $orderByClause = "ORDER BY FIELD(itl.text, 'green', 'yellow', 'red', 'gray', 'dark gray'), student_last_name, student_first_name, student_primary_email, student_id";
                break;
            case '-student_intent_to_leave':
                $orderByClause = "ORDER BY FIELD(itl.text, 'red', 'yellow', 'green', 'gray', 'dark gray'), student_last_name, student_first_name, student_primary_email, student_id";
                break;
            case 'student_classlevel':
            case '+student_classlevel':
                // -{column} DESC sorts the column in ascending order with nulls last
                $orderByClause = 'ORDER BY -emlv.list_value DESC, student_last_name, student_first_name, student_primary_email, student_id';
                break;
            case '-student_classlevel':
                $orderByClause = 'ORDER BY emlv.list_value DESC, student_last_name, student_first_name, student_primary_email, student_id';
                break;
            case 'student_logins':
            case '+student_logins':
                $orderByClause = 'ORDER BY student_logins, student_last_name, student_first_name, student_primary_email, student_id';
                break;
            case '-student_logins':
                $orderByClause = 'ORDER BY student_logins DESC, student_last_name, student_first_name, student_primary_email, student_id';
                break;
            case 'last_activity':
            case '+last_activity':
                // nulls (no activity) will be first
                $orderByClause = 'ORDER BY last_activity_date, student_last_name, student_first_name, student_primary_email, student_id';
                break;
            case '-last_activity':
                // nulls will be last
                $orderByClause = 'ORDER BY last_activity_date DESC, student_last_name, student_first_name, student_primary_email, student_id';
                break;
            default:
                $orderByClause = 'ORDER BY student_last_name, student_first_name, student_primary_email, student_id';
        }

        $sql = "SELECT
                    pwrid.person_id AS student_id,
                    pwrid.firstname AS student_first_name,
                    pwrid.lastname AS student_last_name,
                    pwrid.external_id,
                    pwrid.username as student_primary_email,
                    opsy.is_active AS student_status,
                    rl.risk_text AS student_risk_status,
                    rl.image_name AS student_risk_image_name,
                    itl.text AS student_intent_to_leave,
                    itl.image_name AS student_intent_to_leave_image_name,
                    emlv.list_name AS student_classlevel,
                    COUNT(al.id) AS student_logins,         -- really a count of activities, poorly named to match what the front end is expecting
                    DATE_FORMAT(CONVERT_TZ(MAX(al.activity_date), 'UTC', :timeZoneString), '" . SynapseConstant::STUDENT_LIST_DATE_FORMAT . "') AS last_activity_date,
                    (
                        SELECT
                            CASE
                                WHEN activity_type = 'A' THEN 'Appointment'
                                WHEN activity_type = 'C' THEN 'Contact'
                                WHEN activity_type = 'E' THEN 'Email'
                                WHEN activity_type = 'N' THEN 'Note'
                                WHEN activity_type = 'R' THEN 'Referral'
                            END
                        FROM activity_log
                        WHERE
                            activity_log.person_id_student = pwrid.person_id
                            AND activity_log.deleted_at IS NULL
                        ORDER BY activity_log.activity_date DESC
                        LIMIT 1
                    ) AS last_activity
                FROM
                    person_with_risk_intent_denullifier pwrid
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = pwrid.person_id
                        INNER JOIN
                    org_academic_year oay
                            ON oay.id = opsy.org_academic_year_id
                        LEFT JOIN
                    intent_to_leave itl
                            ON itl.id = pwrid.intent_to_leave
                            AND itl.deleted_at IS NULL
                        LEFT JOIN
                    risk_level rl
                            ON rl.id = pwrid.risk_level
                            AND rl.deleted_at IS NULL
                        LEFT JOIN
                    person_ebi_metadata pem
                            ON pem.person_id = pwrid.person_id
                            AND pem.ebi_metadata_id = :classLevelEbiMetadataId
                            AND pem.deleted_at IS NULL
                        LEFT JOIN
                    ebi_metadata_list_values emlv
                            ON emlv.ebi_metadata_id = pem.ebi_metadata_id
                            AND emlv.list_value = pem.metadata_value
                            AND emlv.deleted_at IS NULL
                        LEFT JOIN
                    activity_log al
                            ON al.person_id_student = pwrid.person_id
                            AND al.deleted_at IS NULL
                WHERE
                    pwrid.person_id IN (:studentIds)
                    AND :academicYearDateTime BETWEEN oay.start_date AND oay.end_date
                    AND opsy.deleted_at IS NULL
                    AND oay.deleted_at IS NULL
                GROUP BY pwrid.person_id
                $orderByClause
                $limitClause;";
        try {
            $stmt = $this->connection->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results;
    }

}