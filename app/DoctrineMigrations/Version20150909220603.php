<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150909220603 extends AbstractMigration
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
             SR.modified_at =
             ( #--Get only the latest survey_response for that question/person intersection
              SELECT MAX(modified_at)
              FROM survey_response AS SRin 
                    WHERE SRin.survey_questions_id=SR.survey_questions_id AND SRin.person_id=SR.person_id
             )
                AND survey_questions.sequence = 4;
            END");

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
