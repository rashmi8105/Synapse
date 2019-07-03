<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151215044048 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("ALTER TABLE synapse.org_question_response DROP INDEX KEY_unique_responses;");

        $this->addSql("ALTER TABLE `synapse`.`org_question_response`
                        ADD COLUMN multi_response_id VARCHAR(100);");

        $this->addSql("ALTER TABLE `etldata`.`org_question_response`
                        ADD COLUMN multi_response_id VARCHAR(100);");

        $this->addSql("ALTER TABLE `etldata`.`org_question_response`
                        ADD COLUMN org_question_options_id INT;");

        $this->addSql("ALTER TABLE `synapse`.`org_question_response`
                        ADD UNIQUE INDEX `unique_response_key` (`org_id` ASC, `survey_id` ASC, `person_id` ASC, `org_question_id` ASC, `multi_response_id` ASC);");

        $this->addSql("DROP PROCEDURE IF EXISTS isq_data_transfer;");

        $this->addSql("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `isq_data_transfer`()
                        BEGIN
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

                        UPDATE synapse.org_question_response oqr
                        JOIN etldata.org_question_response eoqr ON
                        (oqr.person_id, oqr.org_id, oqr.survey_id, oqr.org_question_id, oqr.multi_response_id) =
                        (eoqr.person_id, eoqr.org_id, eoqr.survey_id, eoqr.org_question_id, eoqr.multi_response_id)
                        SET
                        oqr.decimal_value = eoqr.decimal_value,
                        oqr.char_value = eoqr.char_value,
                        oqr.charmax_value = eoqr.charmax_value,
                        oqr.modified_at = NOW()
                        WHERE (oqr.decimal_value <> eoqr.decimal_value OR
                        oqr.char_value <> eoqr.char_value OR
                        oqr.charmax_value <> eoqr.charmax_value )
                        AND oqr.modified_at < eoqr.modified_at
                         AND eoqr.modified_at > (SELECT MAX(last_update_ts) FROM etldata.last_response_update LIMIT 1);

                        END");

        $this->addSql("DROP EVENT IF EXISTS Survey_Risk_Event;");

        $this->addSql("CREATE DEFINER=`synapsemaster`@`%` EVENT `Survey_Risk_Event` ON SCHEDULE EVERY 15 MINUTE STARTS '2015-10-13 00:01:30' ON COMPLETION NOT PRESERVE DISABLE DO BEGIN
                       SET @startTime = NOW();
                       CALL survey_data_transfer();
                       CALL isq_data_transfer();
                       CALL Factor_Calc(DATE_ADD(NOW(), INTERVAL 140 second), 60);
                       CALL Success_Marker_Calc(DATE_ADD(NOW(), INTERVAL 100 second), 60);
                       CALL Report_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 60);
                       CALL Intent_Leave_Calc();
                       CALL Talking_Point_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 100);
                       CALL org_RiskFactorCalculation(DATE_ADD(@startTime, INTERVAL 14 minute), 30);
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
