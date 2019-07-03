<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15562 Performance Increases to Retention Completion Calculation
 */
class Version20170823202207 extends AbstractMigration
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
                            VIEW `org_person_student_retention_by_tracking_group_view` AS
                            SELECT
                                opsrtgv.organization_id,
                                opsrtgv.person_id,
                                opsrtgv.retention_tracking_year,
                                pcypsv.year_id,
                                pcypsv.year_name,
                                IFNULL(opsrv.is_enrolled_beginning_year, 0) AS is_enrolled_beginning_year,
                                IFNULL(opsrv.is_enrolled_midyear, 0) AS is_enrolled_midyear,
                                IFNULL(opsrv.is_degree_completed, 0) AS is_degree_completed,
                                (RIGHT(pcypsv.year_id, 2) - RIGHT(opsrtgv.retention_tracking_year, 2)) AS years_from_retention_track
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

        $this->addSql("DROP VIEW IF EXISTS `org_person_student_retention_with_degree_completion_view`;");

        $this->addSql("CREATE OR REPLACE
                                ALGORITHM = MERGE
                                DEFINER = `synapsemaster`@`%`
                                SQL SECURITY INVOKER
                            VIEW `org_person_student_retention_completion_names_view` AS
                            SELECT
                                opsrwbtgv.organization_id,
                                opsrwbtgv.person_id,
                                opsrwbtgv.retention_tracking_year,
                                opsrwbtgv.year_id,
                                opsrwbtgv.year_name,
                                opsrwbtgv.is_enrolled_beginning_year,
                                opsrwbtgv.is_enrolled_midyear,
                                opsrwbtgv.is_degree_completed,
                                opsrwbtgv.years_from_retention_track,
                                rcvn.name_text,
                                rcvn.variable,
                                rcvn.sequence
                            FROM
                                org_person_student_retention_by_tracking_group_view opsrwbtgv
                                INNER JOIN retention_completion_variable_name rcvn
                                    ON opsrwbtgv.years_from_retention_track = rcvn.years_from_retention_track;");

        $this->addSql("CREATE OR REPLACE
                                ALGORITHM = MERGE
                                DEFINER = `synapsemaster`@`%`
                                SQL SECURITY INVOKER
                            VIEW `org_person_student_retention_completion_pivot_view` AS
                                SELECT
                                    `opsrcnv`.`organization_id`,
                                    `opsrcnv`.`person_id`,
                                    `opsrcnv`.`retention_tracking_year`,
                                    (CASE
                                        WHEN (`opsrcnv`.`variable` = 'retained_to_midyear_year_1') THEN `opsrcnv`.`is_enrolled_midyear`
                                        ELSE NULL
                                    END) AS `retained_to_midyear_year_1`,
                                    (CASE
                                        WHEN (`opsrcnv`.`variable` = 'retained_to_start_of_year_2') THEN `opsrcnv`.`is_enrolled_beginning_year`
                                        ELSE NULL
                                    END) AS `retained_to_start_of_year_2`,
                                    (CASE
                                        WHEN (`opsrcnv`.`variable` = 'retained_to_midyear_year_2') THEN `opsrcnv`.`is_enrolled_midyear`
                                        ELSE NULL
                                    END) AS `retained_to_midyear_year_2`,
                                    (CASE
                                        WHEN (`opsrcnv`.`variable` = 'retained_to_start_of_year_3') THEN `opsrcnv`.`is_enrolled_beginning_year`
                                        ELSE NULL
                                    END) AS `retained_to_start_of_year_3`,
                                    (CASE
                                        WHEN (`opsrcnv`.`variable` = 'retained_to_midyear_year_3') THEN `opsrcnv`.`is_enrolled_midyear`
                                        ELSE NULL
                                    END) AS `retained_to_midyear_year_3`,
                                    (CASE
                                        WHEN (`opsrcnv`.`variable` = 'retained_to_start_of_year_4') THEN `opsrcnv`.`is_enrolled_beginning_year`
                                        ELSE NULL
                                    END) AS `retained_to_start_of_year_4`,
                                    (CASE
                                        WHEN (`opsrcnv`.`variable` = 'retained_to_midyear_year_4') THEN `opsrcnv`.`is_enrolled_midyear`
                                        ELSE NULL
                                    END) AS `retained_to_midyear_year_4`,
                                    (CASE
                                        WHEN (`opsrcnv`.`variable` = 'completed_degree_in_1_year_or_less') THEN `opsrcnv`.`is_degree_completed`
                                        ELSE NULL
                                    END) AS `completed_degree_in_1_year_or_less`,
                                    (CASE
                                        WHEN (`opsrcnv`.`variable` = 'completed_degree_in_2_years_or_less') THEN `opsrcnv`.`is_degree_completed`
                                        ELSE NULL
                                    END) AS `completed_degree_in_2_years_or_less`,
                                    (CASE
                                        WHEN (`opsrcnv`.`variable` = 'completed_degree_in_3_years_or_less') THEN `opsrcnv`.`is_degree_completed`
                                        ELSE NULL
                                    END) AS `completed_degree_in_3_years_or_less`,
                                    (CASE
                                        WHEN (`opsrcnv`.`variable` = 'completed_degree_in_4_years_or_less') THEN `opsrcnv`.`is_degree_completed`
                                        ELSE NULL
                                    END) AS `completed_degree_in_4_years_or_less`,
                                    (CASE
                                        WHEN (`opsrcnv`.`variable` = 'completed_degree_in_5_years_or_less') THEN `opsrcnv`.`is_degree_completed`
                                        ELSE NULL
                                    END) AS `completed_degree_in_5_years_or_less`,
                                    (CASE
                                        WHEN (`opsrcnv`.`variable` = 'completed_degree_in_6_years_or_less') THEN `opsrcnv`.`is_degree_completed`
                                        ELSE NULL
                                    END) AS `completed_degree_in_6_years_or_less`
                                FROM
                                    `org_person_student_retention_completion_names_view` `opsrcnv`;");

        $this->addSql("DROP VIEW IF EXISTS `org_person_student_retention_completion_variables_view`;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
