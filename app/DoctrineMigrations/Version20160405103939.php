<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script: removed org_person_student.receivesurvey and changed to use receive_survey from org_person_student_survey table and joined org_person_student_cohort table 
 */
class Version20160405103939 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // Removed org_person_student.receivesurvey and changed to use receive_survey from org_person_student_survey table and joined org_person_student_cohort table
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP VIEW IF EXISTS `synapse`.`AUDIT_DASHBOARD_Organization_Survey_Cohort_Groupings`');
        $this->addSql("CREATE VIEW `synapse`.`AUDIT_DASHBOARD_Organization_Survey_Cohort_Groupings` AS
                       SELECT 
                         `ol`.`organization_id` AS `organization_id`,
                         `o`.`campus_id` AS `campus_id`,
                         `wl`.`status` AS `status`,
                         `wl`.`cohort_code` AS `cohort_code`,
                         `wl`.`survey_id` AS `survey_id`,
                         `wl`.`wess_order_id` AS `wess_order_id`,
                         `ol`.`organization_name` AS `organization_name`,
                         `wl`.`open_date` AS `open_date`,
                         `wl`.`close_date` AS `close_date`,
                         COUNT(DISTINCT `opss`.`person_id`) AS `People_in_Cohort`
                      FROM
                          (((`organization_lang` `ol`
                          JOIN `wess_link` `wl` ON ((`wl`.`org_id` = `ol`.`organization_id`)))
                          JOIN `organization` `o` ON ((`o`.`id` = `ol`.`organization_id`)))
                          JOIN `org_person_student_cohort` `opsc` ON (((`wl`.`cohort_code` = `opsc`.`cohort`)
                              AND (`wl`.`year_id` = `opsc`.`org_academic_year_id`)
                              AND (`o`.`id` = `opsc`.`organization_id`)))
        				 JOIN `org_person_student_survey` `opss` ON (((`opsc`.`person_id` = `opss`.`person_id`)
                              AND (`o`.`id` = `opss`.`organization_id`))))
                      WHERE
                          (`opss`.`receive_survey` <> 0)
                      GROUP BY `wl`.`wess_order_id`
                      ORDER BY `ol`.`organization_id`
                 ");
       
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        
    }
}
