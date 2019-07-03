<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160407195738 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        /**
         * Edited the My_High_priority_students_List within the ebi_search table.
         * The changes in the query will fix the activity log count issue and the
         * last activity issue.
         */
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
		$personId = '$personId';
		$risklevel  = '$risklevel';
		$orgId = '$orgId';
        $query = <<<Query
UPDATE ebi_search
SET
    query = '
     SELECT SQL_CALC_FOUND_ROWS
    	p.id,
    	p.external_id,
    	p.firstname,
     	p.lastname,
     	p.username as email,
     	p.risk_level,
     	itl.image_name as intent_imagename,
    	itl.text as intent_text,
     	rl.image_name as risk_imagename,
    	rl.risk_text,
    	p.intent_to_leave as intent_leave,
    	(COUNT(DISTINCT (lc.id))) as login_cnt,
    	p.cohert,
    	( SELECT
        (CASE
            WHEN
                (activity_type = \'N\')
            THEN
                CONCAT(DATE_FORMAT(created_at, \'%m/%d/%y\'),
                        \' - \',
                        \'Note\')
            WHEN
                (activity_type = \'A\')
            THEN
                CONCAT(DATE_FORMAT(created_at, \'%m/%d/%y\'),
                        \' - \',
                        \'Appointment\')
            WHEN
                (activity_type = \'C\')
            THEN
                CONCAT(DATE_FORMAT(created_at, \'%m/%d/%y\'),
                        \' - \',
                        \'Contact\')
            WHEN
                (activity_type = \'E\')
            THEN
                CONCAT(DATE_FORMAT(created_at, \'%m/%d/%y\'),
                        \' - \',
                        \'Email\')
            WHEN
                (activity_type = \'R\')
            THEN
                CONCAT(DATE_FORMAT(created_at, \'%m/%d/%y\'),
                        \' - \',
                        \'Referral\')
            ELSE CONCAT(DATE_FORMAT(created_at, \'%m/%d/%y\'),
                    \' - \',
                    \'Login\')
        END) AS new
    FROM
        activity_log
    WHERE
        activity_log.person_id_student = p.id
        AND activity_log.deleted_at IS NULL
    ORDER BY activity_log.created_at DESC
    LIMIT 1
    	) AS last_activity ,
    	ops.status,
    	itl.color_hex AS intent_color,
    	rl.color_hex AS risk_color,
    	 1 AS risk_flag,
    	unique_people_this_faculty_member_can_see_risk_for.intent_to_leave AS intent_flag,
    	elv.list_name AS class_level,
    	al.student_count AS logged_activities
    FROM
    	person p
    LEFT JOIN
        org_person_student os on (os.person_id = p.id and os.organization_id = p.organization_id )
    JOIN (
    	SELECT
    		person_id,
    		max(intent_to_leave) AS intent_to_leave
    	FROM    (
    			 SELECT
    				ocs.person_id,
    				flags.intent_to_leave
    			FROM
    				org_course_student ocs
    					JOIN
    				(
    					SELECT
    						ocf.org_courses_id,
    						op.intent_to_leave
    					FROM
    						org_course_faculty ocf
    							JOIN
    						org_courses oc on oc.id = ocf.org_courses_id and oc.deleted_at is null
    							JOIN
    						org_academic_terms oat on oat.id = oc.org_academic_terms_id and oat.deleted_at is null
    							JOIN org_permissionset op on ocf.org_permissionset_id = op.id and op.deleted_at is null
    					WHERE
    						ocf.person_id = $$personId$$
    							AND ocf.deleted_at is null
    							AND oat.end_date >= date(now())
    							AND op.risk_indicator = 1
    							AND op.accesslevel_ind_agg = 1
    				)       flags ON flags.org_courses_id = ocs.org_courses_id AND ocs.deleted_at is null
                UNION ALL
                	SELECt
                	    ogs.person_id,
                	    flags.intent_to_leave
                	FROM
                	    org_group_students ogs
                	JOIN (
                	    SELECT
                	        ogf.org_group_id,
                	        op.intent_to_leave
                	    FROM org_group_faculty ogf
                	        JOIN
                	    org_permissionset op ON ogf.org_permissionset_id = op.id and op.deleted_at IS NULL
                	    WHERE
                	        ogf.person_id = $$personId$$
                	            AND ogf.deleted_at is null
                	            AND op.risk_indicator = 1
                	            AND op.accesslevel_ind_agg = 1
                	     ) flags ON flags.org_group_id = ogs.org_group_id and ogs.deleted_at is null
                )    non_unique_people_this_faculty_member_can_see_risk_for    group by person_id
        ) unique_people_this_faculty_member_can_see_risk_for on p.id = unique_people_this_faculty_member_can_see_risk_for.person_id
            INNER JOIN
        org_person_student ops on ops.person_id = p.id and ops.deleted_at is null
            LEFT JOIN risk_level rl on p.risk_level = rl.id
        LEFT JOIN
            activity_log lc ON (lc.person_id_student = p.id and lc.deleted_at is null) and lc.activity_type in (\'R\',\'A\',\'C\',\'N\',\'E\')
        LEFT JOIN
            intent_to_leave itl on itl.id = p.intent_to_leave
        LEFT JOIN
            person_ebi_metadata pem on (pem.person_id = p.id and pem.ebi_metadata_id= [EBI_METADATA_CLASSLEVEL_ID] )
        LEFT JOIN
            ebi_metadata_list_values elv on (elv.list_value = pem.metadata_value and elv.ebi_metadata_id = [EBI_METADATA_CLASSLEVEL_ID] )
    	LEFT JOIN
        (SELECT
            person_id_student,
                organization_id,
                COUNT(id) AS student_count
        FROM
            activity_log
        WHERE
            activity_log.deleted_at IS NULL
        GROUP BY person_id_student) AS al ON al.organization_id = p.organization_id
            AND p.id = al.person_id_student
        WHERE
            p.risk_level in ($$risklevel$$)
                AND (p.last_contact_date < p.risk_update_date OR p.last_contact_date IS NULL)
                and p.deleted_at is null
                AND (os.status is null or os.status = 1)
                AND os.organization_id =  $$orgId$$
                AND os.deleted_at is null
        group by p.id
        [ORDER_BY]
        [LIMIT]
    '
WHERE
    query_key = 'My_High_priority_students_List';

Query;

        $this->addSql($query);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
