<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150810121832 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intent_to_leave ADD min_value NUMERIC(8, 4) DEFAULT NULL, ADD max_value NUMERIC(8, 4) DEFAULT NULL');
        
        $drop_procedure_query = <<<CDATA
DROP PROCEDURE IF EXISTS Intent_Leave_Calc;
CDATA;
        $this->addSql($drop_procedure_query);
        
        $update_query = <<<CDATA
CREATE PROCEDURE Intent_Leave_Calc()
BEGIN
#Intent_Leave_Calc
UPDATE person
        JOIN
    survey_response AS SR on SR.person_id = person.id
        JOIN
    ebi_questions_lang ON ebi_questions_lang.ebi_question_id = SR.survey_questions_id
        JOIN
    survey_questions ON survey_questions.ebi_question_id = SR.survey_questions_id
        JOIN
    intent_to_leave ON SR.decimal_value BETWEEN intent_to_leave.min_value AND intent_to_leave.max_value
SET person.intent_to_leave = intent_to_leave.id
WHERE
 SR.created_at =
 ( #--Get only the latest survey_response for that question/person intersection
  SELECT MAX(created_at)
  FROM survey_response AS SRin 
        WHERE SRin.survey_questions_id=SR.survey_questions_id AND SRin.person_id=SR.person_id
 )
    AND ebi_questions_lang.question_text LIKE '%intent to return%'
    AND survey_questions.sequence = 4;
END;
CDATA;
        $this->addSql($update_query);
        
        $event_query = <<<CDATA
drop EVENT if exists event_risk_calc;
        
SET GLOBAL event_scheduler = on;
CREATE EVENT event_risk_calc
    ON SCHEDULE EVERY 1 hour
	STARTS CURRENT_TIMESTAMP
	DO
       CALL Factor_Calc();
       CALL Talking_Point_Calc();
       CALL Success_Marker_Calc();
       CALL Intent_Leave_Calc();
       CALL org_RiskFactorCalculation();     
		
CDATA;
        $this->addSql($event_query); 
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE intent_to_leave DROP min_value, DROP max_value');
        
    }
}
