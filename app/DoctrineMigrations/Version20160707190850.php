<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-10970 and ESPRJ-11206
 * 1. Update Student Report Flag Setting using REPLACE rather than INSERT
 * 2. Splitting the Factor Calculation up into 3 parts
 * 3. Updating Factor Calculation to follow all code standards
 * 4. Removing Success Marker Flag Setting
 */
class Version20160707190850 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSQL('DROP PROCEDURE IF EXISTS `Factor_Find_Survey_Responses`;');
        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Factor_Find_Survey_Responses`(last_update DATETIME)
                        BEGIN
                                SET @lastupdate = last_update;

                                -- inserting any new person/survey into tracking table that have occurred since last run
                                INSERT IGNORE INTO synapse.risk_calc_tracking_table(org_id, person_id, survey_id)
                                    (SELECT
                                        org_id,
                                        person_id,
                                        survey_id
                                    FROM
                                        synapse.survey_response
                                    WHERE
                                        synapse.survey_response.modified_at > @lastupdate
                                    GROUP BY org_id, person_id, survey_id);

                                SET @maxMod = (select max(modified_at) FROM synapse.survey_response);

                                -- Finding most recent response and updating everything in table to have the survey question id
                                UPDATE
                                    synapse.risk_calc_tracking_table
                                SET
                                    most_recent_survey_question_id = GET_MOST_RECENT_SURVEY_QUESTION(org_id, person_id, survey_id),
                                    last_update_ts=@maxMod
                                WHERE
                                    last_update_ts<@maxMod
                                    OR last_update_ts IS NULL;

                                -- If last seen survey question by risk_calc_tracking_table is different than current question
                                    -- trigger Factor Calculation
                                    -- set last seen question to most recent
                                    -- update modified_at date
                                UPDATE
                                    org_calc_flags_factor AS ocff
                                    INNER JOIN synapse.risk_calc_tracking_table rctt ON rctt.org_id = ocff.org_id AND rctt.person_id = ocff.person_id
                                        AND (rctt.most_recent_survey_question_id <> rctt.last_seen_survey_question_id
                                            OR rctt.last_seen_survey_question_id is null)
                                SET
                                    rctt.last_seen_survey_question_id = rctt.most_recent_survey_question_id,
                                    ocff.calculated_at = null,
                                    ocff.modified_at = CURRENT_TIMESTAMP();

                                -- Clean up all completed Surveys from risk_calc_tracking table for performance gain
                                DELETE
                                    rctt
                                FROM
                                    synapse.risk_calc_tracking_table rctt
                                    INNER JOIN org_person_student_survey_link opssl ON rctt.org_id = opssl.org_id
                                        AND rctt.person_id = opssl.person_id
                                        AND rctt.survey_id = opssl.survey_id
                                WHERE
                                    opssl.survey_completion_status = 'CompletedAll';

                         END");
        $this->addSql('DROP PROCEDURE IF EXISTS `Factor_Find_Survey_Responses_ISQ`;');
        $this->addSql("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Factor_Find_Survey_Responses_ISQ`(last_update_ISQ DATETIME)
                        BEGIN
                                SET @lastupdateISQ = last_update_ISQ;

                                -- inserting any new person/survey into tracking table that have occurred since last run
                                INSERT IGNORE INTO synapse.risk_calc_tracking_table_ISQ(org_id, person_id, survey_id)
                                    (SELECT
                                        org_id,
                                        person_id,
                                        survey_id
                                    FROM
                                        synapse.org_question_response
                                    WHERE
                                        synapse.org_question_response.modified_at > @lastupdateISQ
                                    GROUP BY org_id, person_id,survey_id);

                                SET @maxISQ = (SELECT max(modified_at) FROM synapse.org_question_response);

                                -- Finding most recent response and updating everything in table to have the survey question id
                                UPDATE
                                    synapse.risk_calc_tracking_table_ISQ
                                SET
                                    most_recent_org_question_id = GET_MOST_RECENT_ISQ(org_id, person_id, survey_id),
                                    last_update_ts=@maxISQ
                                WHERE
                                    last_update_ts<@maxISQ
                                    OR last_update_ts IS NULL;

                                -- If last seen survey question by risk_calc_tracking_table is different than current question
                                    -- trigger Factor Calculation
                                    -- set last seen question to most recent
                                    -- update modified_at date
                                UPDATE
                                    org_calc_flags_risk ocfr
                                    INNER JOIN synapse.risk_calc_tracking_table_ISQ rctt ON rctt.org_id = ocfr.org_id
                                        AND rctt.person_id = ocfr.person_id
                                        AND (rctt.most_recent_org_question_id <> rctt.last_seen_org_question_id
                                            OR rctt.last_seen_org_question_id IS NULL)
                                SET
                                    rctt.last_seen_org_question_id = rctt.most_recent_org_question_id,
                                    ocfr.calculated_at = NULL,
                                    ocfr.modified_at = CURRENT_TIMESTAMP();

                                -- Clean up all completed Surveys from risk_calc_tracking table for performance gain
                                DELETE
                                    rctt
                                FROM
                                    synapse.risk_calc_tracking_table_ISQ AS rctt
                                    INNER JOIN org_person_student_survey_link opssl ON rctt.org_id = opssl.org_id
                                        AND rctt.person_id = opssl.person_id
                                        AND rctt.survey_id = opssl.survey_id
                                WHERE
                                    opssl.survey_completion_status = 'CompletedAll';

                        END");
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

                                    -- setting student report flags to calculate for chunk
                                    REPLACE INTO org_calc_flags_student_reports(org_id, person_id, survey_id, created_at, modified_at, calculated_at)
                                        SELECT
                                            ocff.org_id,
                                            ocff.person_id,
                                            GET_MOST_RECENT_SURVEY(ocff.org_id, ocff.person_id) as survey_id,
                                            the_ts,
                                            the_ts,
                                            NULL
                                        FROM
                                            org_calc_flags_factor AS ocff
                                        WHERE
                                            ocff.calculated_at = the_ts;

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
