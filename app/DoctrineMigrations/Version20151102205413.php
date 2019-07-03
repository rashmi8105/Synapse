<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151102205413 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs


	

	/*$this->addSql('ALTER TABLE synapse.org_person_student_survey_link
			ADD COLUMN `Has_Responses` enum(\'Yes\', \'No\') NOT NULL DEFAULT \'No\';');*/

	$this->addSql('DROP PROCEDURE IF EXISTS `survey_data_transfer`');

	$this->addSql("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `survey_data_transfer`()
			BEGIN 
                        INSERT IGNORE INTO synapse.survey_response 
                        (SELECT * FROM etldata.survey_response WHERE modified_at > (SELECT MAX(modified_at) FROM synapse.survey_response));
                        
                        UPDATE synapse.org_person_student_survey_link opssl
			LEFT JOIN synapse.survey_response sr ON sr.person_id = opssl.person_id AND sr.survey_id = opssl.survey_id
			SET opssl.Has_Responses = 'Yes'
			WHERE sr.id IS NOT NULL AND opssl.Has_Responses = 'No';

                        UPDATE synapse.survey_response sr
                       	LEFT OUTER JOIN
                        etldata.survey_response ers ON 
                        (ers.person_id , ers.org_id, ers.survey_id, ers.survey_questions_id) = 
                        (sr.person_id , sr.org_id, sr.survey_id, sr.survey_questions_id) 
                        SET 
                            sr.decimal_value = ers.decimal_value,
                            sr.char_value = ers.char_value,
                            sr.charmax_value = ers.charmax_value, 
                            sr.modified_at = NOW()
                        WHERE
                            (sr.decimal_value <> ers.decimal_value
                                OR sr.char_value <> ers.char_value
                                OR sr.charmax_value <> ers.charmax_value)
                                AND sr.modified_at <= ers.modified_at
                                AND ers.modified_at > (SELECT MAX(last_update_ts) FROM etldata.last_response_update LIMIT 1);


                        UPDATE etldata.last_response_update SET last_update_ts = NOW();
                        END");



    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
