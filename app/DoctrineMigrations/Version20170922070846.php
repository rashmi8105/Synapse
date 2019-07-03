<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Initial population of data into academic_record table from academic_update table. ESPRJ-16052
 */
class Version20170922070846 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("
                INSERT IGNORE INTO synapse.academic_record (
                        created_by,
                        created_at,
                        modified_by,
                        modified_at,
                        person_id_student,
                        org_courses_id,
                        organization_id,
                        failure_risk_level,
                        in_progress_grade,
                        `comment`,
                        final_grade,
                        absence,
                        update_date,
                        failure_risk_level_update_date,
                        in_progress_grade_update_date,
                        comment_update_date,
                        final_grade_update_date,
                        absence_update_date
                )
                SELECT
                    -25,
                    NOW(),
                    -25,
                    NOW(),
                    person_id_student,
                    org_courses_id,
                    org_id,
                    substring_index(GROUP_CONCAT(failure_risk_level ORDER BY modified_at DESC), \",\", 1) AS failure_risk_level,
                    substring_index(GROUP_CONCAT(grade ORDER BY modified_at DESC), \",\", 1) AS in_progress_grade,
                    substring_index(GROUP_CONCAT(`comment` ORDER BY modified_at DESC), \",\", 1) AS `comment`,
                    substring_index(GROUP_CONCAT(final_grade ORDER BY modified_at DESC), \",\", 1) AS final_grade,
                    substring_index(GROUP_CONCAT(absence ORDER BY modified_at DESC), \",\", 1) AS absence,
                    substring_index(GROUP_CONCAT(update_date ORDER BY modified_at DESC), \",\", 1) AS update_date,
                    substring_index(GROUP_CONCAT(update_date ORDER BY modified_at DESC), \",\", 1) AS failure_risk_level_update_date,
                    substring_index(GROUP_CONCAT(update_date ORDER BY modified_at DESC), \",\", 1) AS in_progress_grade_update_date,
                    substring_index(GROUP_CONCAT(update_date ORDER BY modified_at DESC), \",\", 1) AS comment_update_date,
                    substring_index(GROUP_CONCAT(update_date ORDER BY modified_at DESC), \",\", 1) AS final_grade_update_date,
                    substring_index(GROUP_CONCAT(update_date ORDER BY modified_at DESC), \",\", 1) AS absence_update_date
                FROM
                 
                    synapse.academic_update
                WHERE
                    deleted_at IS NULL
                    AND status = 'closed'
                GROUP BY person_id_student, org_courses_id;
        ");


        $this->addSql("INSERT INTO job_type (created_by, created_at, modified_by, modified_at, job_type) VALUES (-25, NOW(), -25, NOW(), 'CreateAcademicUpdateHistoryJob')");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
