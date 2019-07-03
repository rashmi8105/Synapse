<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15087 - Migration script for updating notifications for interested party
 */
class Version20170607055200 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE OR REPLACE
                            ALGORITHM = UNDEFINED 
                            DEFINER = `synapsemaster`@`%` 
                            SQL SECURITY INVOKER
                        VIEW `org_person_student_retention_completion_pivot_view` AS
                            SELECT 
                                `opsrcnv`.`organization_id` AS `organization_id`,
                                `opsrcnv`.`person_id` AS `person_id`,
                                `opsrcnv`.`retention_tracking_year` AS `retention_tracking_year`,
                                (CASE
                                    WHEN (`opsrcnv`.`name_text` = \'Retained to Midyear Year 1\') THEN `opsrcnv`.`is_enrolled_midyear`
                                    ELSE NULL
                                END) AS `Retained to Midyear Year 1`,
                                (CASE
                                    WHEN (`opsrcnv`.`name_text` = \'Retained to Start of Year 2\') THEN `opsrcnv`.`is_enrolled_beginning_year`
                                    ELSE NULL
                                END) AS `Retained to Start of Year 2`,
                                (CASE
                                    WHEN (`opsrcnv`.`name_text` = \'Retained to Midyear Year 2\') THEN `opsrcnv`.`is_enrolled_midyear`
                                    ELSE NULL
                                END) AS `Retained to Midyear Year 2`,
                                (CASE
                                    WHEN (`opsrcnv`.`name_text` = \'Retained to Start of Year 3\') THEN `opsrcnv`.`is_enrolled_beginning_year`
                                    ELSE NULL
                                END) AS `Retained to Start of Year 3`,
                                (CASE
                                    WHEN (`opsrcnv`.`name_text` = \'Retained to Midyear Year 3\') THEN `opsrcnv`.`is_enrolled_midyear`
                                    ELSE NULL
                                END) AS `Retained to Midyear Year 3`,
                                (CASE
                                    WHEN (`opsrcnv`.`name_text` = \'Retained to Start of Year 4\') THEN `opsrcnv`.`is_enrolled_beginning_year`
                                    ELSE NULL
                                END) AS `Retained to Start of Year 4`,
                                (CASE
                                    WHEN (`opsrcnv`.`name_text` = \'Retained to Midyear Year 4\') THEN `opsrcnv`.`is_enrolled_midyear`
                                    ELSE NULL
                                END) AS `Retained to Midyear Year 4`,
                                (CASE
                                    WHEN (`opsrcnv`.`name_text` = \'Completed Degree in 1 Year or Less\') THEN `opsrcnv`.`is_degree_completed`
                                    ELSE NULL
                                END) AS `Completed Degree in 1 Year or Less`,
                                (CASE
                                    WHEN (`opsrcnv`.`name_text` = \'Completed Degree in 2 Years or Less\') THEN `opsrcnv`.`is_degree_completed`
                                    ELSE NULL
                                END) AS `Completed Degree in 2 Years or Less`,
                                (CASE
                                    WHEN (`opsrcnv`.`name_text` = \'Completed Degree in 3 Years or Less\') THEN `opsrcnv`.`is_degree_completed`
                                    ELSE NULL
                                END) AS `Completed Degree in 3 Years or Less`,
                                (CASE
                                    WHEN (`opsrcnv`.`name_text` = \'Completed Degree in 4 Years or Less\') THEN `opsrcnv`.`is_degree_completed`
                                    ELSE NULL
                                END) AS `Completed Degree in 4 Years or Less`,
                                (CASE
                                    WHEN (`opsrcnv`.`name_text` = \'Completed Degree in 5 Years or Less\') THEN `opsrcnv`.`is_degree_completed`
                                    ELSE NULL
                                END) AS `Completed Degree in 5 Years or Less`,
                                (CASE
                                    WHEN (`opsrcnv`.`name_text` = \'Completed Degree in 6 Years or Less\') THEN `opsrcnv`.`is_degree_completed`
                                    ELSE NULL
                                END) AS `Completed Degree in 6 Years or Less`
                            FROM
                                `org_person_student_retention_completion_names_view` `opsrcnv`');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
