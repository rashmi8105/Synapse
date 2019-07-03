<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-11548-ESPRJ-13299
 */
class Version20171108210733 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("CREATE OR REPLACE
                                ALGORITHM = MERGE
                                DEFINER = `synapsemaster`@`%`
                                SQL SECURITY INVOKER
                            VIEW `person_MD_talking_points_calculated` AS
                                SELECT
                                    `ocftp`.`org_id` AS `org_id`,
                                    `ocftp`.`person_id` AS `person_id`,
                                    `tp`.`id` AS `talking_points_id`,
                                    `pem`.`ebi_metadata_id` AS `ebi_metadata_id`,
                                    `pem`.`org_academic_year_id` AS `org_academic_year_id`,
                                    `pem`.`org_academic_terms_id` AS `org_academic_terms_id`,
                                    `tp`.`talking_points_type` AS `response`,
                                    `pem`.`modified_at` AS `source_modified_at`
                                FROM
                                    `talking_points` `tp`
                                    INNER JOIN `person_ebi_metadata` AS `pem`
                                        ON `tp`.`ebi_metadata_id` = `pem`.`ebi_metadata_id`
                                           AND `pem`.`metadata_value` BETWEEN `tp`.`min_range` AND `tp`.`max_range`
                                    INNER JOIN `org_calc_flags_talking_point` `ocftp`
                                        ON `pem`.`person_id` = `ocftp`.`person_id`
                                WHERE
                                    `tp`.`deleted_at` IS NULL
                                    AND `pem`.`deleted_at` IS NULL
                                    AND `ocftp`.`deleted_at` IS NULL;");

        $this->addSql("CREATE OR REPLACE
                                ALGORITHM = MERGE
                                DEFINER = `synapsemaster`@`%`
                                SQL SECURITY INVOKER
                            VIEW `person_survey_talking_points_calculated` AS
                                SELECT
                                    `ocftp`.`org_id` AS `org_id`,
                                    `ocftp`.`person_id` AS `person_id`,
                                    `tp`.`id` AS `talking_points_id`,
                                    `tp`.`ebi_question_id` AS `ebi_question_id`,
                                    `sr`.`survey_id` AS `survey_id`,
                                    `tp`.`talking_points_type` AS `response`,
                                    `sr`.`modified_at` AS `source_modified_at`
                                FROM
                                    `talking_points` `tp`
                                    INNER JOIN `survey_questions` AS `sq`
                                        ON `tp`.`ebi_question_id` = `sq`.`ebi_question_id`
                                    INNER JOIN `survey_response` AS `sr`
                                        ON `sq`.`id` = `sr`.`survey_questions_id`
                                           AND CASE
                                               WHEN `sr`.`response_type` = 'decimal'
                                                   THEN `sr`.`decimal_value`
                                               END
                                           BETWEEN `tp`.`min_range` AND `tp`.`max_range`
                                    INNER JOIN `org_calc_flags_talking_point` AS `ocftp`
                                        ON `sr`.`person_id` = `ocftp`.`person_id`
                                           AND `sr`.`org_id` = `ocftp`.`org_id`
                                WHERE
                                    `tp`.`deleted_at` IS NULL
                                    AND `sq`.`deleted_at` IS NULL
                                    AND `sr`.`deleted_at` IS NULL
                                    AND `ocftp`.`deleted_at` IS NULL;");

        $this->addSql("DROP PROCEDURE IF EXISTS `Talking_Point_Calc`;");
        $this->addSql("CREATE DEFINER =`synapsemaster`@`%` PROCEDURE `Talking_Point_Calc`(deadline TIMESTAMP, chunksize SMALLINT UNSIGNED)
                            DETERMINISTIC
                                SQL SECURITY INVOKER
                                BEGIN
                                    DECLARE timeVariable DATETIME;
                                    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
                            
                            
                                    WHILE (
                                        NOW() < deadline
                                        AND
                                        (
                                            SELECT 1
                                            FROM
                                                org_calc_flags_talking_point
                                            WHERE
                                                calculated_at IS NULL
                                                AND deleted_at IS NULL
                                            LIMIT 1
                                        ) > 0
                                    ) DO
                            
                                        SET timeVariable = CURRENT_TIMESTAMP();
                            
                                        #--Carve out a chunk of work to do
                                        UPDATE
                                            org_calc_flags_talking_point
                                        SET
                                            calculated_at = timeVariable
                                        WHERE
                                            calculated_at IS NULL
                                            AND deleted_at IS NULL
                                        LIMIT chunksize;
                            
                                        #--Sourced from surveys
                                        INSERT INTO org_talking_points (organization_id, person_id, talking_points_id, survey_id, response, source_modified_at, created_at, modified_at)
                                            SELECT
                                                pstpc.org_id,
                                                pstpc.person_id,
                                                pstpc.talking_points_id,
                                                pstpc.survey_id,
                                                pstpc.response,
                                                pstpc.source_modified_at,
                                                timeVariable,
                                                timeVariable
                                            FROM
                                                person_survey_talking_points_calculated pstpc
                                                INNER JOIN org_calc_flags_talking_point ocftp
                                                    ON ocftp.org_id = pstpc.org_id
                                                       AND ocftp.person_id = pstpc.person_id
                                                LEFT JOIN org_talking_points otp_out
                                                    ON otp_out.organization_id = pstpc.org_id
                                                       AND otp_out.person_id = pstpc.person_id
                                                       AND otp_out.talking_points_id = pstpc.talking_points_id
                                                       AND otp_out.survey_id = pstpc.survey_id
                                                       AND pstpc.response <=> otp_out.response
                                                       AND otp_out.deleted_at IS NULL
                                                       AND otp_out.source_modified_at =
                                                           (
                                                               SELECT MAX(otp_in.source_modified_at)
                                                               FROM
                                                                   org_talking_points otp_in
                                                                   INNER JOIN talking_points tp ON otp_in.talking_points_id = tp.id
                                                               WHERE
                                                                   otp_out.organization_id = otp_in.organization_id
                                                                   AND otp_out.person_id = otp_in.person_id
                                                                   AND pstpc.ebi_question_id = tp.ebi_question_id
                                                                   AND otp_out.survey_id = otp_in.survey_id
                                                                   AND tp.deleted_at IS NULL
                                                                   AND otp_in.deleted_at IS NULL
                            
                                                           )
                                            WHERE
                                                otp_out.organization_id IS NULL #--Get only pstpc entries with no corresponding otp_out
                                                AND pstpc.response IS NOT NULL
                                                AND ocftp.calculated_at = timeVariable
                                                AND ocftp.deleted_at IS NULL;
                            
                                        #--Sourced from metadata
                                        INSERT INTO org_talking_points (organization_id, person_id, talking_points_id, org_academic_year_id, org_academic_terms_id, response, source_modified_at, created_at, modified_at)
                                            SELECT
                                                pstpc.org_id,
                                                pstpc.person_id,
                                                pstpc.talking_points_id,
                                                pstpc.org_academic_year_id,
                                                pstpc.org_academic_terms_id,
                                                pstpc.response,
                                                pstpc.source_modified_at,
                                                timeVariable,
                                                timeVariable
                                            FROM
                                                person_MD_talking_points_calculated pstpc
                                                INNER JOIN org_calc_flags_talking_point ocftp
                                                    ON ocftp.org_id = pstpc.org_id
                                                       AND ocftp.person_id = pstpc.person_id
                                                LEFT JOIN org_talking_points otp_out
                                                    ON otp_out.organization_id = pstpc.org_id
                                                       AND otp_out.person_id = pstpc.person_id
                                                       AND otp_out.talking_points_id = pstpc.talking_points_id
                                                       AND pstpc.response <=> otp_out.response
                                                       AND ((pstpc.org_academic_year_id = otp_out.org_academic_year_id
                                                            AND pstpc.org_academic_terms_id = otp_out.org_academic_terms_id)
                                                            OR (pstpc.org_academic_year_id IS NULL AND otp_out.org_academic_year_id IS NULL AND
                                                                pstpc.org_academic_terms_id IS NULL AND otp_out.org_academic_terms_id IS NULL)
                                                            OR (pstpc.org_academic_terms_id IS NULL AND otp_out.org_academic_terms_id IS NULL AND
                                                                pstpc.org_academic_year_id = otp_out.org_academic_year_id))
                                                       AND otp_out.deleted_at IS NULL
                                                       AND otp_out.modified_at =
                                                           (
                                                               SELECT MAX(otp_in.source_modified_at)
                                                               FROM
                                                                   org_talking_points otp_in
                                                                   INNER JOIN talking_points tp ON otp_in.talking_points_id = tp.id
                                                               WHERE
                                                                   otp_out.organization_id = otp_in.organization_id
                                                                   AND otp_out.person_id = otp_in.person_id
                                                                   AND pstpc.ebi_metadata_id = tp.ebi_metadata_id
                                                                   AND otp_out.talking_points_id = otp_in.talking_points_id
                                                                   AND otp_out.org_academic_year_id <=> otp_in.org_academic_year_id
                                                                   AND otp_out.org_academic_terms_id <=> otp_in.org_academic_terms_id
                                                                   AND tp.deleted_at is null
                                                                   AND otp_in.deleted_at IS NULL
                                                           )
                                            WHERE
                                                otp_out.organization_id IS NULL #--Get only pstpc entries with no corresponding otp_out
                                                AND pstpc.response IS NOT NULL
                                                AND ocftp.calculated_at = timeVariable
                                                AND ocftp.deleted_at IS NULL;
                            
                                        #-- Purposefully Not checking soft deletion
                                        UPDATE
                                                org_calc_flags_talking_point ocftp
                                                LEFT JOIN org_talking_points otp
                                                    ON otp.organization_id = ocftp.org_id
                                                       AND otp.person_id = ocftp.person_id
                                                       AND otp.modified_at = timeVariable
                                        SET
                                            ocftp.calculated_at = '1900-01-01 00:00:00'
                                        WHERE
                                            ocftp.calculated_at = timeVariable
                                            AND otp.organization_id IS NULL #--These got no value out of calculation
                                    ;
                            
                                    END WHILE;
                            
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
