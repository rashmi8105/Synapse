<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160303224638 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        // 
        /**
         * Creating Migration Script for edge case ESPRJ-9158:
         * The changes to the last update statement here ensure that
         * calculations are still triggered even if the student goes
         * back and re-answers a single question
         * org_calc_flags_risk is updated because it is the only influenced by ISQ 
         */
        $this->addSQL('ALTER EVENT `Survey_Risk_Event` DISABLE;');
        $this->addSQL('DROP PROCEDURE `isq_data_transfer`;');
        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `isq_data_transfer`()
                        BEGIN
                        -- Main insert of new survey responses
                        INSERT IGNORE INTO synapse.org_question_response
                        (org_id,
                        person_id,
                        survey_id,
                        org_academic_year_id,
                        org_academic_terms_id,
                        org_question_id,
                        modified_at,
                        response_type,
                        decimal_value,
                        char_value,
                        charmax_value,
                        multi_response_id,
                        org_question_options_id)
                        (SELECT
                        org_id,
                        person_id,
                        survey_id,
                        org_academic_year_id,
                        org_academic_terms_id,
                        org_question_id,
                        modified_at,
                        response_type,
                        decimal_value,
                        char_value,
                        charmax_value,
                        multi_response_id,
                        org_question_options_id
                        FROM etldata.org_question_response WHERE modified_at >= (SELECT MAX(modified_at) FROM synapse.org_question_response));

                        -- Update questions that have been reanswered since the last update time
                        -- Set org_calc_flags_risk 
                        -- if questions are re-answered
                        -- IF YOU DO NOT WANT TO TRIGGER FLAGS COMMENT OUT BELOW
                        UPDATE synapse.org_question_response oqr
                        INNER JOIN synapse.org_calc_flags_risk ocr
                        ON oqr.person_id  = ocr.person_id
                        and oqr.org_id = ocr.org_id
                        INNER JOIN etldata.org_question_response eoqr 
                        ON oqr.person_id = eoqr.person_id
                        AND oqr.org_id = eoqr.org_id
                        AND oqr.survey_id = eoqr.survey_id
                        AND oqr.org_question_id = eoqr.org_question_id
                        AND oqr.multi_response_id = eoqr.multi_response_id
                        SET
                        oqr.decimal_value = eoqr.decimal_value,
                        oqr.char_value = eoqr.char_value,
                        oqr.charmax_value = eoqr.charmax_value,
                        oqr.modified_at = NOW(),
                        ocr.calculated_at = NULL,
                        ocr.modified_at = NOW()
                        WHERE (oqr.decimal_value <> eoqr.decimal_value OR
                        oqr.char_value <> eoqr.char_value OR
                        oqr.charmax_value <> eoqr.charmax_value )
                        AND oqr.modified_at <= eoqr.modified_at
                        AND eoqr.modified_at > (SELECT MAX(last_update_ts) FROM etldata.last_response_update LIMIT 1);

                        END");
        $this->addSQL('ALTER EVENT `Survey_Risk_Event` ENABLE;');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
