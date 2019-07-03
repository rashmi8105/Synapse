<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-13532 and ESPRJ-13531
 * Found a couple bugs in the Retention Completion Calculation
 * ESPRJ-13532: Rows were duplicated when students had multiple retention tracking groups.  The problem was that
 * the view 'potential_retention_tracking_group_students_view' did not take this into account and assumed a single
 * retention tracking group. Since the JOIN to org_person_student_retention_tracking_group takes place on the other
 * views.  It was not necessary to use it here.   Instead we used org_person_student RATHER than using non-performant
 * GROUP BY or DISTINCT in this view.
 *
 * RENAMED VIEW TO BETTER NAME: 'past_current_years_per_student_view'
 *
 * ESPRJ-13531: view 'org_person_student_retention_by_tracking_group_view' allowed for negative years_from_retention_track.
 * Although this currently isn't causing bugs, it should not have been allowed and opened the door to future bugs.
 *
 */
class Version20170227171349 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("CREATE OR REPLACE
                        ALGORITHM = MERGE
                        DEFINER = `synapsemaster`@`%`
                        SQL SECURITY INVOKER
                    VIEW `past_current_years_per_student_view` AS
                        SELECT
                            ops.organization_id,
                            ops.person_id,
                            oay.year_id,
                            oay.name as year_name
                        FROM
                            org_person_student ops
                                INNER JOIN
                            org_academic_year oay ON ops.organization_id = oay.organization_id
                        WHERE
                            oay.start_date <= DATE(NOW())
                            AND ops.deleted_at IS NULL
                            AND oay.deleted_at IS NULL;
                        ");

        $this->addSql("CREATE OR REPLACE
                            DEFINER = `synapsemaster`@`%`
                            SQL SECURITY INVOKER
                        VIEW `org_person_student_retention_by_tracking_group_view` AS
                        SELECT
                            opsrtgv.organization_id,
                            opsrtgv.person_id,
                            opsrtgv.retention_tracking_year,
                            pcypsv.year_id,
                            pcypsv.year_name,
                            IFNULL(opsrv.is_enrolled_beginning_year, 0) as is_enrolled_beginning_year,
                            IFNULL(opsrv.is_enrolled_midyear, 0) as is_enrolled_midyear,
                            opsrv.is_degree_completed as is_degree_completed,
                            (RIGHT(pcypsv.year_id, 2) - RIGHT(opsrtgv.retention_tracking_year, 2)) as years_from_retention_track
                        FROM
                            past_current_years_per_student_view pcypsv
                            INNER JOIN org_person_student_retention_tracking_group_view opsrtgv
                                ON pcypsv.organization_id = opsrtgv.organization_id
                                AND pcypsv.person_id = opsrtgv.person_id
                            LEFT JOIN org_person_student_retention_view opsrv
                                ON opsrv.person_id = opsrtgv.person_id
                                AND opsrv.organization_id = opsrtgv.organization_id
                                AND opsrv.year_id = pcypsv.year_id
                                AND opsrtgv.retention_tracking_year <= opsrv.year_id
                        WHERE
                            (RIGHT(pcypsv.year_id, 2) - RIGHT(opsrtgv.retention_tracking_year, 2)) >= 0;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
