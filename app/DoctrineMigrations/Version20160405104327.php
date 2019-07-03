<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script: removed org_person_student.receivesurvey and changed to use receive_survey from org_person_student_survey table
 */
class Version20160405104327 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // Removed org_person_student.receivesurvey and changed to use receive_survey from org_person_student_survey table
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP VIEW IF EXISTS `synapse`.`AUDIT_DASHBOARD_0_ReceiveSurvey_Students_With_Survey_Responses`');
        $this->addSql("CREATE VIEW `synapse`.`AUDIT_DASHBOARD_0_ReceiveSurvey_Students_With_Survey_Responses` AS
                       SELECT  `sr`.`org_id` AS `org_id`, 
                             COUNT(DISTINCT `sr`.`person_id`) AS `Number_of_Students`
                      FROM
                            (`survey_response` `sr`
                            JOIN `org_person_student_survey` `opss` ON (((`sr`.`person_id` = `opss`.`person_id`)
                                 AND (`sr`.`org_id` = `opss`.`organization_id`))))
                      WHERE
                            (`opss`.`receive_survey` = 0)
                      GROUP BY `opss`.`organization_id`
                      ORDER BY `opss`.`organization_id`
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
