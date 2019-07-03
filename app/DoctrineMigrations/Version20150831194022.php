<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150831194022 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('ALTER EVENT event_risk_calc
        disable;');

        //Setting All STudents to most recent date for risk calculation for HIgh Priority Status
        $this->addSQL('UPDATE person p
        INNER JOIN
    (SELECT 
        person_id, MAX(date_captured) AS date_captured
    FROM
        person_risk_level_history
    GROUP BY person_id) AS u 
    ON p.id = u.person_id
    SET 
    p.risk_update_date = u.date_captured');

        $this->addSQL('DROP PROCEDURE IF EXISTS org_RiskFactorCalculation;');

        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `org_RiskFactorCalculation`(limiter INT UNSIGNED)
    DETERMINISTIC
    SQL SECURITY INVOKER
    BEGIN
    DECLARE the_ts TIMESTAMP;

    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

    SET the_ts=NOW(); 


    UPDATE org_calc_flags_risk
    SET modified_at=the_ts, calculated_at=the_ts
    WHERE calculated_at IS NULL
    ORDER BY modified_at DESC
    LIMIT limiter;



    INSERT IGNORE INTO org_calculated_risk_variables_history (person_id, risk_variable_id, risk_group_id, risk_model_id, created_at, org_id, calc_bucket_value, calc_weight, risk_source_value)
    SELECT 
        OCRV.person_id,
        OCRV.risk_variable_id,
        OCRV.risk_group_id,
        OCRV.risk_model_id,
        the_ts AS created_at,
        OCRV.org_id,
        bucket_value AS calc_bucket_value,
        calc_weight,
        calculated_value AS risk_source_value
    FROM org_calculated_risk_variables_view AS OCRV
    INNER JOIN (
        SELECT person_id FROM org_calc_flags_risk
        WHERE 
            calculated_at=the_ts
            #--AND modified_at=the_ts
    ) AS stale 
        ON stale.person_id=OCRV.person_id
    ;


    INSERT IGNORE INTO person_risk_level_history(person_id,date_captured,risk_model_id,risk_level,risk_score,weighted_value,maximum_weight_value)
    SELECT 
        prlc.person_id,
        the_ts,
        prlc.risk_model_id,
        prlc.risk_level,
        prlc.risk_score,
        prlc.weighted_value,
        prlc.maximum_weight_value 
    FROM person_risk_level_calc AS prlc
    INNER JOIN (
        SELECT person_id FROM org_calc_flags_risk
        WHERE 
            calculated_at=the_ts
            #--AND modified_at=the_ts
    ) AS stale 
        ON stale.person_id=prlc.person_id
    
    ;

    
    UPDATE person P 
    INNER JOIN person_risk_level_history AS PRH 
        ON P.id=PRH.person_id
        AND PRH.date_captured=the_ts
    SET P.risk_level=PRH.risk_level,
        P.risk_update_date=the_ts;

    END");

    $queryIntro = "UPDATE synapse.ebi_search SET query = ";

    $queryBody1 = "\"SELECT SQL_CALC_FOUND_ROWS
    P.id,
    P.id AS student,
    P.firstname,
    P.lastname,
    P.risk_level,
    RL.risk_text,
    RL.image_name,
    IL.text AS intent_to_leave_text,
    IL.image_name AS intent_to_leave_image,
    RML.risk_model_id,
    P.last_activity,
    OPS.surveycohort AS student_cohort,
    (COUNT(DISTINCT (acl.id))) AS cnt,
    OPS.status,
    OPS.photo_url,
    pem.metadata_value AS class_level
FROM
    person P
        LEFT JOIN
    risk_level AS RL ON P.risk_level = RL.id
        LEFT JOIN
    risk_model_levels AS RML ON RML.risk_level = RL.id
        LEFT JOIN
    intent_to_leave AS IL ON P.intent_to_leave = IL.id
        LEFT JOIN
    org_person_student AS OPS ON OPS.person_id = P.id
        LEFT JOIN
    activity_log acl ON (acl.person_id_student = P.id)
        LEFT JOIN
    person_ebi_metadata AS pem ON (pem.person_id = P.id
        AND pem.ebi_metadata_id IN (SELECT 
            id
        FROM
            ebi_metadata
        WHERE
            meta_key = 'ClassLevel'))
WHERE
    P.risk_level IN (1 , 2)
        AND (P.risk_update_date > P.last_contact_date
        OR P.last_contact_date IS NULL)
        AND P.id IN (\$\$personIds\$\$)
GROUP BY P.id\"";

    $queryEnd1 = " WHERE query_key = 'High_Priority_Students'";


    $this->addSQL($queryIntro . $queryBody1 . $queryEnd1);


    $queryBody2 = "\"SELECT 
    p.id,
    p.firstname,
    p.lastname,
    p.risk_level,
    il.image_name AS intent_imagename,
    il.text AS intent_text,
    rl.image_name AS risk_imagename,
    rl.risk_text,
    p.intent_to_leave AS intent_leave,
    lc.cnt AS login_cnt,
    p.cohert,
    p.last_activity,
    ps.status,
    il.color_hex AS intent_color,
    rl.color_hex AS risk_color,
    (CASE
        WHEN
            (((SELECT DISTINCT
                    (ogs.person_id)
                FROM
                    org_group_students ogs,
                    org_group_faculty ogf,
                    person ip
                WHERE
                    ogs.org_group_id = ogf.org_group_id
                        AND ogf.person_id = (\$\$personId\$\$)
                        AND ogs.person_id = p.id
                        AND ogs.person_id = ip.id
                        AND ogf.org_permissionset_id IN (SELECT 
                            id
                        FROM
                            org_permissionset
                        WHERE
                            accesslevel_ind_agg = 1
                                AND risk_indicator = 1
                                AND deleted_at IS NULL)
                ORDER BY ip.risk_level DESC , ip.lastname , ip.firstname) = p.id)
                OR ((SELECT DISTINCT
                    (ocs.person_id)
                FROM
                    org_course_student ocs,
                    org_course_faculty ocf,
                    person ip
                WHERE
                    ocs.org_courses_id = ocf.org_courses_id
                        AND ocf.person_id = (\$\$personId\$\$)
                        AND ocs.person_id = p.id
                        AND ocs.person_id = ip.id
                        AND ocf.org_permissionset_id IN (SELECT 
                            id
                        FROM
                            org_permissionset
                        WHERE
                            accesslevel_ind_agg = 1
                                AND risk_indicator = 1
                                AND deleted_at IS NULL)
                        AND ocf.org_courses_id IN (SELECT 
                            id
                        FROM
                            org_courses
                        WHERE
                            deleted_at IS NULL
                                AND org_academic_terms_id IN (SELECT 
                                    id
                                FROM
                                    org_academic_terms
                                WHERE
                                    deleted_at IS NULL AND end_date > NOW()))
                ORDER BY ip.risk_level DESC , ip.lastname , ip.firstname) = p.id))
        THEN
            '1'
        ELSE '0'
    END) AS risk_flag,
    (CASE
        WHEN
            (((SELECT DISTINCT
                    (ogs.person_id)
                FROM
                    org_group_students ogs,
                    org_group_faculty ogf,
                    person ip
                WHERE
                    ogs.org_group_id = ogf.org_group_id
                        AND ogf.person_id = (\$\$personId\$\$)
                        AND ogs.person_id = p.id
                        AND ogs.person_id = ip.id
                        AND ogf.org_permissionset_id IN (SELECT 
                            id
                        FROM
                            org_permissionset
                        WHERE
                            accesslevel_ind_agg = 1
                                AND intent_to_leave = 1
                                AND deleted_at IS NULL)
                ORDER BY ip.risk_level DESC , ip.lastname , ip.firstname) = p.id)
                OR ((SELECT DISTINCT
                    (ocs.person_id)
                FROM
                    org_course_student ocs,
                    org_course_faculty ocf,
                    person ip
                WHERE
                    ocs.org_courses_id = ocf.org_courses_id
                        AND ocf.person_id = (\$\$personId\$\$)
                        AND ocs.person_id = p.id
                        AND ocs.person_id = ip.id
                        AND ocf.org_permissionset_id IN (SELECT 
                            id
                        FROM
                            org_permissionset
                        WHERE
                            accesslevel_ind_agg = 1
                                AND intent_to_leave = 1
                                AND deleted_at IS NULL)
                        AND ocf.org_courses_id IN (SELECT 
                            id
                        FROM
                            org_courses
                        WHERE
                            deleted_at IS NULL
                                AND org_academic_terms_id IN (SELECT 
                                    id
                                FROM
                                    org_academic_terms
                                WHERE
                                    deleted_at IS NULL AND end_date > NOW()))
                ORDER BY ip.risk_level DESC , ip.lastname , ip.firstname) = p.id))
        THEN
            '1'
        ELSE '0'
    END) AS intent_flag
FROM
    person p
        JOIN
    risk_level rl ON (p.risk_level = rl.id)
        LEFT JOIN
    intent_to_leave il ON (p.intent_to_leave = il.id)
        LEFT JOIN
    org_person_student AS ps ON p.id = ps.person_id
        LEFT OUTER JOIN
    Logins_count lc ON (lc.person_id = p.id)
WHERE
    (p.id IN (SELECT DISTINCT
            person_id
        FROM
            org_group_students ogs
        WHERE
            ogs.org_group_id IN (SELECT 
                    org_group_id
                FROM
                    org_group_faculty
                WHERE
                    person_id = (\$\$personId\$\$)
                        AND deleted_at IS NULL
                        AND org_permissionset_id IN (SELECT 
                            id
                        FROM
                            org_permissionset op
                        WHERE
                            accesslevel_ind_agg = 1
                                AND deleted_at IS NULL))
                AND ogs.deleted_at IS NULL UNION SELECT DISTINCT
            person_id
        FROM
            org_course_student ocs
        WHERE
            ocs.org_courses_id IN (SELECT 
                    org_courses_id
                FROM
                    org_course_faculty
                WHERE
                    person_id = (\$\$personId\$\$)
                        AND deleted_at IS NULL
                        AND org_courses_id IN (SELECT 
                            id
                        FROM
                            org_courses
                        WHERE
                            deleted_at IS NULL
                                AND org_academic_terms_id IN (SELECT 
                                    id
                                FROM
                                    org_academic_terms
                                WHERE
                                    deleted_at IS NULL AND end_date > NOW())))
                AND ocs.deleted_at IS NULL))
        AND (p.last_contact_date < p.risk_update_date OR p.last_contact_date IS NULL)
        AND p.risk_level IN (\$\$risklevel\$\$)
        AND p.deleted_at IS NULL
ORDER BY p.risk_level DESC , p.lastname , p.firstname;
    \"";

    $queryEnd2 = " WHERE query_key = 'My_High_priority_students_List';";

    $this->addSQL($queryIntro . $queryBody2 . $queryEnd2);


    $queryBody3 = "\"SELECT 
    COUNT(per.id) AS highCount
FROM
    person per
WHERE
    per.id IN (SELECT DISTINCT
            person_id
        FROM
            org_group_students
        WHERE
            org_group_id IN (SELECT 
                    org_group_id
                FROM
                    org_group_faculty
                WHERE
                    person_id = \$\$personId\$\$
                        AND deleted_at IS NULL
                        AND org_permissionset_id IN (SELECT 
                            id
                        FROM
                            org_permissionset
                        WHERE
                            accesslevel_ind_agg = 1
                                AND deleted_at IS NULL))
                AND deleted_at IS NULL UNION SELECT DISTINCT
            person_id
        FROM
            org_course_student
        WHERE
            org_courses_id IN (SELECT 
                    org_courses_id
                FROM
                    org_course_faculty
                WHERE
                    person_id = \$\$personId\$\$
                        AND deleted_at IS NULL
                        AND org_permissionset_id IN (SELECT 
                            id
                        FROM
                            org_permissionset
                        WHERE
                            accesslevel_ind_agg = 1
                                AND deleted_at IS NULL)
                        AND org_courses_id IN (SELECT 
                            id
                        FROM
                            org_courses
                        WHERE
                            deleted_at IS NULL
                                AND org_academic_terms_id IN (SELECT 
                                    id
                                FROM
                                    org_academic_terms
                                WHERE
                                    deleted_at IS NULL AND end_date > NOW())))
                AND deleted_at IS NULL)
        AND (per.last_contact_date < per.risk_update_date OR per.last_contact_date is NULL)
        AND per.risk_level IN (\$\$risklevel\$\$)
        AND per.deleted_at IS NULL\" 
        ";

    $queryEnd3 = " WHERE query_key = 'My_High_priority_students_Count';";



    $this->addSQL($queryIntro . $queryBody3 . $queryEnd3);

    $this->addSQL('ALTER EVENT event_risk_calc
    enable;');


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
