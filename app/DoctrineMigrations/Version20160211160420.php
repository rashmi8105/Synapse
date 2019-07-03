<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160211160420 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("ALTER EVENT Survey_Risk_Event DISABLE;");

        $this->addSql("DROP PROCEDURE IF EXISTS `survey_data_transfer`");

        $this->addSql("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `survey_data_transfer`()
                        BEGIN

                        SELECT NOW() INTO @current;

                        -- Main insert of new survey responses
                        INSERT IGNORE INTO synapse.survey_response
                        (SELECT * FROM etldata.survey_response WHERE modified_at > (SELECT MAX(modified_at) FROM synapse.survey_response));

                        -- Cleanup insert for responses missed in down windows
                        INSERT IGNORE INTO synapse.survey_response (survey_questions_id, org_id, person_id, survey_id, org_academic_year_id, org_academic_terms_id, modified_at, response_type, decimal_value, char_value, charmax_value) (
                        SELECT ers.survey_questions_id, ers.org_id, ers.person_id, ers.survey_id, ers.org_academic_year_id, ers.org_academic_terms_id, @current, ers.response_type, ers.decimal_value, ers.char_value, ers.charmax_value
                        FROM etldata.survey_response ers
                            LEFT OUTER JOIN synapse.survey_response sr ON
                            (ers.person_id , ers.org_id, ers.survey_id, ers.survey_questions_id) =
                            (sr.person_id , sr.org_id, sr.survey_id, sr.survey_questions_id)
                        WHERE
                            sr.id IS NULL
                            AND ers.modified_at >= (@current - INTERVAL 12 HOUR)
                        );

                        -- Update Has_Responses values to match Synapse survey responses
                        UPDATE synapse.org_person_student_survey_link opssl
                        LEFT JOIN synapse.survey_response sr ON sr.person_id = opssl.person_id AND sr.survey_id = opssl.survey_id

                        SET opssl.Has_Responses = 'Yes'
                        WHERE sr.id IS NOT NULL AND opssl.Has_Responses = 'No';

                        -- Update questions that have been reanswered since the last update time
                        UPDATE synapse.survey_response sr
                        LEFT OUTER JOIN
                        etldata.survey_response ers ON
                        (ers.person_id , ers.org_id, ers.survey_id, ers.survey_questions_id) =
                        (sr.person_id , sr.org_id, sr.survey_id, sr.survey_questions_id)
                        SET
                            sr.decimal_value = ers.decimal_value,
                            sr.char_value = ers.char_value,
                            sr.charmax_value = ers.charmax_value,
                            sr.modified_at = @current
                        WHERE
                            (sr.decimal_value <> ers.decimal_value
                                OR sr.char_value <> ers.char_value
                                OR sr.charmax_value <> ers.charmax_value)
                                AND sr.modified_at <= ers.modified_at
                                AND ers.modified_at > (SELECT MAX(last_update_ts) FROM etldata.last_response_update LIMIT 1);

                        -- Update the last update time to now
                        UPDATE etldata.last_response_update SET last_update_ts = @current;
                        END");

        $this->addSql("ALTER EVENT Survey_Risk_Event ENABLE;");

        $this->addSql("CREATE
                            ALGORITHM = UNDEFINED
                            DEFINER = `synapsemaster`@`%`
                            SQL SECURITY DEFINER
                        VIEW `AUDIT_DASHBOARD_Mismatching_Survey_Ids_For_Questions` AS
                        SELECT p.id AS person_id, p.external_id AS person_external_id, ol.organization_id AS organization_id,  p.firstname, p.lastname, ol.organization_name, bsl.name AS survey_name_in_responses, sl.name AS survey_name_should_be, COUNT(*) as response_count
                        FROM synapse.survey_response sr
                        JOIN synapse.survey_questions sq ON sq.id = sr.survey_questions_id
                        JOIN synapse.organization_lang ol ON ol.organization_id = sr.org_id
                        JOIN synapse.person p ON p.id = sr.person_id
                        JOIN synapse.survey_lang bsl ON bsl.survey_id = sr.survey_id
                        JOIN synapse.survey_lang sl ON sq.survey_id = sl.survey_id
                        WHERE sr.survey_id <> sq.survey_id
                        GROUP BY ol.organization_id, p.id, sl.survey_id, bsl.survey_id;");

        $this->addSql("CREATE
                            ALGORITHM = UNDEFINED
                            DEFINER = `synapsemaster`@`%`
                            SQL SECURITY DEFINER
                        VIEW `AUDIT_DASHBOARD_Students_With_Responses_Not_In_Synapse` AS
                        SELECT p.id AS person_id, p.external_id AS person_external_id, ol.organization_id AS organization_id, sl.survey_id AS survey_id, p.firstname, p.lastname, ol.organization_name, sl.name AS survey_name,  COUNT(DISTINCT ers.survey_questions_id) as missing_response_count, MAX(ers.modified_at) AS most_recent_response_in_group
                        FROM etldata.survey_response ers
                            LEFT OUTER JOIN synapse.survey_response sr ON
                            (ers.person_id , ers.org_id, ers.survey_id, ers.survey_questions_id) =
                            (sr.person_id , sr.org_id, sr.survey_id, sr.survey_questions_id)
                            JOIN synapse.person p ON ers.person_id = p.id
                            JOIN synapse.organization_lang ol ON ol.organization_id = ers.org_id
                            JOIN synapse.survey_lang sl ON sl.survey_id = ers.survey_id
                        WHERE
                            sr.id IS NULL
                            AND ers.modified_at >= '2016-01-01 00:00:00'
                        GROUP BY
                        p.id, ol.organization_id, sl.survey_id;");



    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
