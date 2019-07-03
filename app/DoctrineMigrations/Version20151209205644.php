<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151209205644 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("ALTER TABLE `synapse`.`org_question_response`
                        ADD COLUMN org_question_options_id INT;
                      ");

        $this->addSql("ALTER TABLE `synapse`.`org_question_response`
                        ADD FOREIGN KEY (org_question_options_id)
                        REFERENCES synapse.org_question_options(id);
                      ");

        $this->addSql("ALTER TABLE `synapse`.`org_question_response`
                        ADD UNIQUE INDEX `KEY_unique_responses` (`org_id` ASC, `survey_id` ASC, `person_id` ASC, `org_question_id` ASC, `org_question_options_id` ASC);
                      ");

        $this->addSql("DROP PROCEDURE IF EXISTS isq_data_transfer;");

        $this->addSql("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `isq_data_transfer`()
                        BEGIN
                        INSERT IGNORE INTO synapse.org_question_response
                        (SELECT * FROM etldata.org_question_response WHERE modified_at >= (SELECT MAX(modified_at) FROM synapse.org_question_response));

                        UPDATE synapse.org_question_response oqr
                        JOIN etldata.org_question_response eoqr ON
                        (oqr.person_id, oqr.org_id, oqr.survey_id, oqr.org_question_id) =
                        (eoqr.person_id, eoqr.org_id, eoqr.survey_id, eoqr.org_question_id)
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

                        END
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
