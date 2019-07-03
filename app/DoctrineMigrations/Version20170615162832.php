<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15199 - Migration Script for change org_person_student_retention_completion_variables_view column name
 */
class Version20170615162832 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Change org_person_student_retention_completion_variables_view column names
        $this->addSql("CREATE OR REPLACE
                            ALGORITHM = UNDEFINED 
                            DEFINER = `synapsemaster`@`%` 
                            SQL SECURITY INVOKER
                        VIEW `synapse`.`org_person_student_retention_completion_variables_view` AS
                            select 
                                `opsrcpv`.`organization_id` AS `organization_id`,
                                `opsrcpv`.`person_id` AS `person_id`,
                                `opsrcpv`.`retention_tracking_year` AS `Retention Tracking Year`,
                                max(`opsrcpv`.`Retained to Midyear Year 1`) AS `Retained to Midyear Year 1`,
                                max(`opsrcpv`.`Retained to Start of Year 2`) AS `Retained to Start of Year 2`,
                                max(`opsrcpv`.`Retained to Midyear Year 2`) AS `Retained to Midyear Year 2`,
                                max(`opsrcpv`.`Retained to Start of Year 3`) AS `Retained to Start of Year 3`,
                                max(`opsrcpv`.`Retained to Midyear Year 3`) AS `Retained to Midyear Year 3`,
                                max(`opsrcpv`.`Retained to Start of Year 4`) AS `Retained to Start of Year 4`,
                                max(`opsrcpv`.`Retained to Midyear Year 4`) AS `Retained to Midyear Year 4`,
                                max(`opsrcpv`.`Completed Degree in 1 Year or Less`) AS `Completed Degree in 1 Year`,
                                max(`opsrcpv`.`Completed Degree in 2 Years or Less`) AS `Completed Degree in 2 Years`,
                                max(`opsrcpv`.`Completed Degree in 3 Years or Less`) AS `Completed Degree in 3 Years`,
                                max(`opsrcpv`.`Completed Degree in 4 Years or Less`) AS `Completed Degree in 4 Years`,
                                max(`opsrcpv`.`Completed Degree in 5 Years or Less`) AS `Completed Degree in 5 Years`,
                                max(`opsrcpv`.`Completed Degree in 6 Years or Less`) AS `Completed Degree in 6 Years`
                            from
                                `synapse`.`org_person_student_retention_completion_pivot_view` `opsrcpv`
                            group by `opsrcpv`.`organization_id` , `opsrcpv`.`person_id` , `opsrcpv`.`retention_tracking_year`");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
