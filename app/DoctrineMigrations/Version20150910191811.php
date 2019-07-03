<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150910191811 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('DROP PROCEDURE IF EXISTS `Intent_Leave_Calc`;');

        $this->addSQL("
            CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Intent_Leave_Calc`()
            BEGIN
        
                        
            SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
            update person p
            join
            (
               select
               sr.person_id, sr.decimal_value, sr.org_id, sr.modified_at
               from survey_response sr
               join survey_questions sq on sr.survey_questions_id = sq.id
               where sq.qnbr = 4
               AND sr.modified_at =
               (
                  SELECT
                  MAX(modified_at)
                  FROM survey_response AS SRin
                  WHERE SRin.survey_questions_id = sr.survey_questions_id
                  AND SRin.person_id = sr.person_id
                  AND SRin.org_id = sr.org_id
               )
            )
            as person_intent_to_leave_value on p.id = person_intent_to_leave_value.person_id
            join org_person_student ops on ops.person_id = p.id
            and ops.organization_id = person_intent_to_leave_value.org_id
            join intent_to_leave itl ON person_intent_to_leave_value.decimal_value BETWEEN itl.min_value
            AND itl.max_value 
            set p.intent_to_leave = itl.id, p.intent_to_leave_update_date = person_intent_to_leave_value.modified_at;

            END");
            
            $this->addSQL('ALTER TABLE `synapse`.`survey_questions` ADD INDEX `qnbr` (`qnbr` ASC);');
            $this->addSQL("insert into intent_to_leave (text, image_name, color_hex, min_value, max_value)
            VALUES('gray', 'leave-intent-not-stated.png', '#cccccc', 99, 99);");


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
