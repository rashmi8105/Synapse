<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-8394
 * Making sure the flag table is in the correct state.
 * We are considering everything in the CompletedAll status
 * to be already correctly sent.  Otherwise, we will send
 * thousands of emails to students who completed surveys
 * last semester
 */
class Version20160718201309 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("
        UPDATE
	        org_calc_flags_student_reports AS ocfsr
		INNER JOIN org_person_student_survey_link AS opssl ON ocfsr.org_id = opssl.org_id
            AND ocfsr.person_id = opssl.person_Id
            AND ocfsr.survey_id = opssl.survey_id
        SET ocfsr.completion_email_sent = 1
        WHERE
            ocfsr.calculated_at IS NOT NULL
            AND calculated_at <> '1900-01-01 00:00:00'
            AND ocfsr.file_name IS NOT NULL
            AND ocfsr.survey_id IS NOT NULL
            AND ocfsr.completion_email_sent = 0
            AND opssl.survey_completion_status = 'CompletedAll'
            AND ocfsr.deleted_at IS NULL
            AND opssl.deleted_at IS NULL;");


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
