<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151008204141 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSQL('ALTER TABLE `synapse`.`survey_response`
        DROP INDEX `fk_survey_response_organization1` ,
        ADD INDEX `fk_survey_response_organization1` (`org_id` ASC, `person_id` ASC, `survey_questions_id` ASC, `modified_at` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`org_question_response`
        DROP INDEX `fk_org_question_response_organization1_idx`,
        ADD INDEX `fk_org_question_response_organization1_idx` (`org_id` ASC, `person_id` ASC, `org_question_id` ASC, `modified_at` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`survey_response`
        DROP INDEX `latestsurvey`, 
        ADD INDEX `latestsurvey` (`org_id` ASC, `person_id` ASC, `modified_at` ASC, `survey_id` ASC, `survey_questions_id` ASC);');
        
        $this->addSQL('ALTER TABLE `synapse`.`org_question_response` 
        ADD INDEX `latestsurvey` (`org_id` ASC, `person_id` ASC, `modified_at` ASC, `survey_id` ASC, `org_question_id` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`success_marker_calculated` 
        ADD INDEX `org_person_modified` (`modified_at` ASC,`organization_id` ASC, `person_id` ASC);');

        $this->addSQL('ALTER IGNORE TABLE `synapse`.`report_calculated_values` 
        ADD UNIQUE INDEX `unique_key` ( `org_id` ASC, `person_id` ASC, `survey_id` ASC, `report_id` ASC, `section_id` ASC, `element_id` ASC, `element_bucket_id` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`org_person_student_survey_link` 
        ADD INDEX `completion_status` (`survey_completion_status` ASC)');


        $this->addSQL('DROP FUNCTION IF EXISTS `get_most_recent_survey`;');
        $this->addSQL('CREATE FUNCTION `get_most_recent_survey` (the_org_id INT, the_person_id INT)
           RETURNS INTEGER
           READS SQL DATA
           DETERMINISTIC
           SQL SECURITY INVOKER
           BEGIN 
           
            RETURN  (SELECT survey_id 
                FROM survey_response sr
                WHERE
                (sr.org_id, sr.person_id)
                = (the_org_id, the_person_id)
                ORDER BY modified_at DESC
                LIMIT 1);
            END');
        $this->addSQL('DROP FUNCTION IF EXISTS `get_most_recent_survey_ISQ`;');
        $this->addSQL('CREATE FUNCTION `get_most_recent_survey_ISQ` (the_org_id INT, the_person_id INT)
         RETURNS INTEGER
           READS SQL DATA
             DETERMINISTIC
             SQL SECURITY INVOKER
         BEGIN
            
            RETURN  (SELECT survey_id 
                FROM org_question_response oqr
                WHERE
                (oqr.org_id, oqr.person_id)
                = (the_org_id, the_person_id)
                ORDER BY modified_at DESC
                LIMIT 1);
        END');
        $this->addSQL('DROP FUNCTION IF EXISTS `get_most_recent_survey_question`;');
        $this->addSQL('CREATE FUNCTION `get_most_recent_survey_question` (the_org_id INT, the_person_id INT, the_survey_id INT)
         RETURNS INTEGER
           READS SQL DATA
             DETERMINISTIC
             SQL SECURITY INVOKER
             
            BEGIN

            RETURN  (SELECT survey_questions_id 
                FROM survey_response sr
                WHERE
                (sr.org_id, sr.person_id, sr.survey_id)
                = (the_org_id, the_person_id, the_survey_id)
                ORDER BY modified_at DESC
                LIMIT 1);
         END');
        $this->addSQL('DROP FUNCTION IF EXISTS `get_most_recent_ISQ`;');
        $this->addSQL('CREATE FUNCTION `get_most_recent_ISQ` (the_org_id INT, the_person_id INT, the_survey_id INT)
         RETURNS INTEGER
           READS SQL DATA
             DETERMINISTIC
             SQL SECURITY INVOKER
         BEGIN
            
            RETURN  (SELECT org_question_id 
                FROM org_question_response oqr
                WHERE
                (oqr.org_id, oqr.person_id, oqr.survey_id)
                = (the_org_id, the_person_id, the_survey_id)
                ORDER BY modified_at DESC
                LIMIT 1);
        END');

                $this->addSQL('
        CREATE TABLE IF NOT EXISTS `synapse`.`risk_calc_tracking_table_ISQ` (
          `person_id` int(11) NOT NULL,
          `last_update_ts` datetime DEFAULT NULL,
          `most_recent_org_question_id` int(11) DEFAULT NULL,
          `last_seen_org_question_id` int(11) DEFAULT NULL,
          `survey_id` int(11) NOT NULL,
          `org_id` int(11) NOT NULL,
          PRIMARY KEY (`org_id`,`person_id`,`survey_id`),
          KEY `org_person` (`org_id`,`person_id`),
          KEY `person` (`person_id`),
          KEY `survey_id` (`survey_id`),
          KEY `last_seen` (`last_seen_org_question_id`),
          KEY `most_recent` (`most_recent_org_question_id`),
          KEY `last_update` (`last_update_ts`)
        );');

        $this->addSQL('
        CREATE TABLE IF NOT EXISTS `synapse`.`risk_calc_tracking_table` (
          `person_id` int(11) NOT NULL,
          `last_update_ts` datetime DEFAULT NULL,
          `most_recent_survey_question_id` int(11) DEFAULT NULL,
          `last_seen_survey_question_id` int(11) DEFAULT NULL,
          `survey_id` int(11) NOT NULL,
          `org_id` int(11) NOT NULL,
          PRIMARY KEY (`org_id`,`person_id`,`survey_id`),
          KEY `org_person` (`org_id`,`person_id`),
          KEY `person` (`person_id`),
          KEY `survey_id` (`survey_id`),
          KEY `last_seen` (`last_seen_survey_question_id`),
          KEY `most_recent` (`most_recent_survey_question_id`),
          KEY `last_update` (`last_update_ts`)
        );');

     
        $this->addSQL('insert ignore into synapse.risk_calc_tracking_table(org_id, person_id, survey_id)
        (select org_id, person_id, survey_id from synapse.survey_response GROUP BY org_id, person_id, survey_id);');

        $this->addSQL('update synapse.risk_calc_tracking_table ops set ops.most_recent_survey_question_id = get_most_recent_survey_question(ops.org_id, ops.person_id, ops.survey_id),
        last_update_ts=(select max(modified_at) from synapse.survey_response);');

        $this->addSQL('insert ignore into synapse.risk_calc_tracking_table_ISQ(org_id, person_id, survey_id)
        (select org_id, person_id, survey_id from synapse.org_question_response GROUP BY org_id, person_id, survey_id);');

        $this->addSQL('update synapse.risk_calc_tracking_table_ISQ ops set ops.most_recent_org_question_id = get_most_recent_ISQ(ops.org_id, ops.person_id, ops.survey_id),
        last_update_ts=(select max(modified_at) from synapse.survey_response);');

        $this->addSQL('update synapse.risk_calc_tracking_table ops set ops.last_seen_survey_question_id = ops.most_recent_survey_question_id;');
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
