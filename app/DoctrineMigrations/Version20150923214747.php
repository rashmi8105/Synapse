<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150923214747 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSQL('ALTER EVENT intent_leave_calc
        disable;');

        $this->addSQL('DROP PROCEDURE IF EXISTS `Intent_Leave_Null_Fixer`;');

        $this->addSQL('CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Intent_Leave_Null_Fixer`()
        BEGIN
 
          WHILE (select 1 from person p INNER JOIN org_person_student ops On p.organization_id = ops.organization_id AND p.id = ops.person_id where intent_to_leave is null LIMIT 1) = 1 DO
          update person as per INNER JOIN (select p.id as person_id FROM person p INNER JOIN org_person_student ops 
            On p.organization_id = ops.organization_id AND p.id = ops.person_id where p.intent_to_leave is null ORDER BY p.id LIMIT 1000) as t On per.id = t.person_id  SET per.intent_to_leave = 5, per.intent_to_leave_update_date = CURRENT_TIMESTAMP();
            
          END WHILE;


        END');

        $this->addSQL('DROP PROCEDURE IF EXISTS `Intent_Leave_Calc`;');

        $this->addSQL('CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Intent_Leave_Calc`()
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
            
            Call `Intent_Leave_Null_Fixer`();

            END');

            $this->addSQL("update intent_to_leave SET text = 'gray', min_value = 99, max_value = 99 where id = 4;");

            $this->addSQL("update intent_to_leave SET text = 'dark gray', min_value = null, max_value = null, color_hex = '#626161', image_name = 'intent-to-leave-icons_dark_gray_large.png' where id = 5;");

            $this->addSQL("update person p INNER JOIN org_person_student ops On p.organization_id = ops.organization_id AND p.id = ops.person_id SET intent_to_leave = null where intent_to_leave = 5 OR intent_to_leave = 4;");


            //Reapplying 4 values
            $this->addSQL("update person p
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
            set p.intent_to_leave = itl.id, p.intent_to_leave_update_date = person_intent_to_leave_value.modified_at;");

            //Mass Bulk upload of 5 flag.  May take several minutes. Who knows...
            $this->addSQL('update person p INNER JOIN org_person_student ops On p.organization_id = ops.organization_id AND p.id = ops.person_id SET intent_to_leave = 5, intent_to_leave_update_date = CURRENT_TIMESTAMP() where intent_to_leave is null;');

            $this->addSQL("ALTER EVENT intent_leave_calc enable;");



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
