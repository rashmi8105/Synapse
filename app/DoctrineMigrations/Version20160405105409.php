<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script: removed org_person_student.receivesurvey and changed to use receive_survey from org_person_student_survey table
 */
class Version20160405105409 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // Removed org_person_student.receivesurvey and changed to use receive_survey from org_person_student_survey table
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP VIEW IF EXISTS `synapse`.`DASHBOARD_Student_Surveys_By_Org`');
        $this->addSql("CREATE VIEW `synapse`.`DASHBOARD_Student_Surveys_By_Org` AS
                       SELECT 
                           `organization_lang`.`organization_id` AS `organization_id`,
                           `organization`.`campus_id` AS `campus_id`,
                           `organization_lang`.`organization_name` AS `organization_name`,
                           COUNT(`sr`.`person_id`) AS `Total number of Surveys Taken`,
                           SUM((CASE
                               WHEN (`sr`.`survey_id` = 11) THEN 1
                               ELSE 0
                           END)) AS `Students having taken survey_id: 11`, 
                           SUM((CASE
                               WHEN (`sr`.`survey_id` = 12) THEN 1
                               ELSE 0
                           END)) AS `Students having taken survey_id: 12`,
                           SUM((CASE
                               WHEN (`sr`.`survey_id` = 13) THEN 1
                               ELSE 0
                           END)) AS `Students having taken survey_id: 13`,
                           SUM((CASE
                               WHEN (`sr`.`survey_id` = 14) THEN 1
                               ELSE 0
                           END)) AS `Students having taken survey_id: 14`, 
                           SUM((CASE
                               WHEN
                                   ((`org_person_student_survey`.`receive_survey` = 1)
                                       OR isnull(`org_person_student_survey`.`receive_survey`))
                               THEN
                                   1
                               ELSE 0
                           END)) AS `Student Survey Eligibility`
                       FROM
                           (((`organization_lang`
                           LEFT JOIN `organization` ON ((`organization_lang`.`organization_id` = `organization`.`id`)))
                           LEFT JOIN `org_person_student_survey` ON (((`organization`.`id` = `org_person_student_survey`.`organization_id`)
                               AND (`organization_lang`.`organization_id` = `org_person_student_survey`.`organization_id`)
                               AND (isnull(`org_person_student_survey`.`receive_survey`)
                               OR (`org_person_student_survey`.`receive_survey` = 1)))))
                           LEFT JOIN `DASHBOARD_Students_With_Intent_To_Leave` `sr` ON (((`sr`.`person_id` = `org_person_student_survey`.`person_id`)
                               AND (`organization`.`id` = `sr`.`org_id`))))
                       WHERE
                           ((`organization`.`campus_id` IS NOT NULL)
                               AND (`organization_lang`.`organization_id` <> 181)
                               AND (`organization_lang`.`organization_id` <> 195)
                               AND (`organization_lang`.`organization_id` <> 196)
                               AND (`organization_lang`.`organization_id` <> 198)
                               AND (`organization_lang`.`organization_id` <> 200)
                               AND (`organization_lang`.`organization_id` <> 201)
                               AND (`organization_lang`.`organization_id` <> 2)
                               AND (`organization_lang`.`organization_id` <> 199)
                               AND (`organization_lang`.`organization_id` <> 3)
                               AND (`organization_lang`.`organization_id` <> 194)
                               AND (`organization_lang`.`organization_id` <> 197))
                       GROUP BY `organization`.`id`
                       ORDER BY `organization_lang`.`organization_name` , `sr`.`survey_id`
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
