<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160726170444 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP PROCEDURE IF EXISTS `Factor_Calc`;');
        $this->addSql("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Factor_Calc`(deadline TIMESTAMP, chunksize SMALLINT UNSIGNED)
                        BEGIN


                                DECLARE the_ts TIMESTAMP;
                                SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

                                -- Finding the last time this calculation was ran from the risk_calc_tracking table
                                IF (SELECT 1 from synapse.risk_calc_tracking_table LIMIT 1) > 0 THEN
                                    SET @lastupdate = (SELECT max(last_update_ts) FROM synapse.risk_calc_tracking_table);
                                    SET @lastupdateISQ = (select max(last_update_ts) FROM synapse.risk_calc_tracking_table_ISQ);
                                ELSE
                                    SET @lastupdate = '1900-01-01 00:00:00';
                                    SET @lastupdateISQ = '1900-01-01 00:00:00';
                                end IF;


                                -- STARTING PROCESS OF FINDING NEW SURVEY RESPONSES
                                -- SETS THE APPROPRIATE PEOPLE TO CALCULATE FACTORS
                                CALL Factor_Find_Survey_Responses(@lastupdate);

                                -- STARTING PROCESS OF FINDING NEW SURVEY RESPONSES ISQs
                                -- SETS THE APPROPRIATE PEOPLE TO CALCULATE FACTORS
                                CALL Factor_Find_Survey_Responses_ISQ(@lastupdateISQ);

                                -- START ACTUAL FACTOR CALCULATION PROCESS
                                -- CYCLE THROUGH UNTIL DEADLINE OR NO REMAINING NULL FLAGS
                                WHILE
                                    (NOW() < deadline
                                    AND (SELECT 1
                                        FROM org_calc_flags_factor
                                        WHERE calculated_at IS NULL
                                        LIMIT 1) > 0)
                                DO

                                    SET the_ts=NOW();

                                    -- Setting a chunk to process
                                    UPDATE
                                        org_calc_flags_factor
                                    SET
                                        calculated_at=the_ts
                                    WHERE
                                        calculated_at IS NULL
                                    ORDER BY modified_at ASC
                                    LIMIT chunksize;

                                    -- calculating factors for chunk
                                    REPLACE INTO person_factor_calculated(organization_id, person_id, factor_id, survey_id, mean_value, created_at, modified_at)
                                        SELECT straight_join
                                            sr.org_id,
                                            sr.person_id,
                                            fq.factor_id,
                                            sr.survey_id,
                                            avg(sr.decimal_value) as mean_value,
                                            the_ts,
                                            the_ts
                                        FROM
                                            org_calc_flags_factor ocff
                                            INNER JOIN survey_response AS sr ON sr.org_id = ocff.org_id
                                                AND sr.person_id = ocff.person_id
                                            INNER JOIN survey_questions sq ON sq.id = sr.survey_questions_id
                                            INNER JOIN factor_questions fq ON fq.ebi_question_id = sq.ebi_question_id
                                        WHERE
                                            factor_id IS NOT NULL
                                            AND sr.survey_id = GET_MOST_RECENT_SURVEY(sr.org_id, sr.person_id)
                                            AND ocff.calculated_at = the_ts
                                            AND FLOOR(sr.decimal_value) != 99
                                        GROUP BY sr.org_id, sr.person_id, fq.factor_id, sr.survey_id;

                                    -- inserting student report flags to calculate for chunk
                                    INSERT INTO org_calc_flags_student_reports(org_id, person_id, survey_id, created_at, modified_at, calculated_at, file_name)
                                        SELECT
                                            ocff.org_id,
                                            ocff.person_id,
                                            GET_MOST_RECENT_SURVEY(ocff.org_id, ocff.person_id) as survey_id,
                                            the_ts,
                                            the_ts,
                                            NULL,
                                            NULL
                                        FROM
                                            org_calc_flags_factor AS ocff
                                        WHERE
                                            ocff.calculated_at = the_ts
                                    ON DUPLICATE KEY UPDATE calculated_at = NULL, file_name = NULL, modified_at = the_ts;

                                    -- setting talking point flags to calculate for chunk
                                    UPDATE
                                        org_calc_flags_talking_point ocftp
                                        INNER JOIN org_calc_flags_factor ocff ON ocff.org_id = ocftp.org_id
                                            AND ocff.person_id = ocftp.person_id
                                    SET
                                        ocftp.calculated_at= NULL,
                                        ocftp.modified_at = the_ts
                                    WHERE
                                        ocff.calculated_at = the_ts;

                                    -- setting risk flags to calculate for chunk
                                    UPDATE
                                        org_calc_flags_risk ocfr
                                        INNER JOIN org_calc_flags_factor ocff ON ocff.org_id = ocfr.org_id
                                            AND ocff.person_id = ocfr.person_id
                                    SET
                                        ocfr.calculated_at= NULL,
                                        ocfr.modified_at = the_ts
                                    WHERE
                                        ocff.calculated_at = the_ts;

                                    -- setting all successful flags to calculated with chunk timestamp (ie, not 99)
                                    UPDATE
                                        org_calc_flags_factor ocff
                                        INNER JOIN
                                            (SELECT straight_join
                                                sr.org_id,
                                                sr.person_id
                                            FROM
                                                org_calc_flags_factor ocff
                                                    INNER JOIN survey_response AS sr ON sr.org_id = ocff.org_id
                                                        AND sr.person_id = ocff.person_id
                                                    INNER JOIN survey_questions svq ON svq.id=sr.survey_questions_id
                                                    INNER JOIN factor_questions fq ON fq.ebi_question_id=svq.ebi_question_id
                                            WHERE
                                                factor_id IS NOT NULL
                                                AND ocff.calculated_at = the_ts
                                                AND FLOOR(sr.decimal_value) != 99) AS calc
                                        ON calc.org_id = ocff.org_id
                                            AND calc.person_id = ocff.person_id
                                    SET
                                        calculated_at = the_ts,
                                        modified_at = the_ts;

                                    -- marking all unsuccesful flags to have the 1900 flag for calculated and not valid
                                    UPDATE
                                        org_calc_flags_factor ocff
                                    SET
                                        ocff.calculated_at = '1900-01-01 00:00:00',
                                        ocff.modified_at = the_ts
                                    WHERE
                                        ocff.calculated_at = the_ts
                                        AND ocff.modified_at <> ocff.calculated_at;

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
