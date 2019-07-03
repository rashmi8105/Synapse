<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-16288 Risk Flag Optimization in the Factor_Calc
 */
class Version20171121154931 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("DROP PROCEDURE IF EXISTS `Factor_Calc`;");
        $this->addSql("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Factor_Calc`(deadline TIMESTAMP, chunksize SMALLINT UNSIGNED)
                            BEGIN
                                   
                                    
                                    DECLARE the_timestamp TIMESTAMP;
                                    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
                                    
                                    -- Finding the last time this calculation was ran from the risk_calc_tracking table
                                    IF (SELECT
                                            1
                                        FROM
                                            synapse.risk_calc_tracking_table
                                        LIMIT 1) > 0
                                    THEN
                                        SET @lastupdate = (SELECT MAX(last_update_ts) FROM synapse.risk_calc_tracking_table);
                                        SET @lastupdateISQ = (SELECT MAX(last_update_ts) FROM synapse.risk_calc_tracking_table_ISQ);
                                    ELSE
                                        SET @lastupdate = '1900-01-01 00:00:00';
                                        SET @lastupdateISQ = '1900-01-01 00:00:00';
                                    END IF;
                                    
                            
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
                                        AND (SELECT
                                                1
                                            FROM
                                                org_calc_flags_factor
                                            WHERE
                                                calculated_at IS NULL
                                                AND deleted_at IS NULL
                                            LIMIT 1) > 0)
                                    DO
                                    
                                        SET the_timestamp = NOW();
                                        
                                        -- Setting a chunk to process
                                        UPDATE 
                                            org_calc_flags_factor
                                        SET 
                                            calculated_at = the_timestamp
                                        WHERE 
                                            calculated_at IS NULL
                                            AND deleted_at IS NULL
                                        ORDER BY modified_at ASC
                                        LIMIT chunksize;
                                  
                                        -- calculating factors for chunk
                                        REPLACE INTO person_factor_calculated(organization_id, person_id, factor_id, survey_id, mean_value, created_at, modified_at)
                                            SELECT STRAIGHT_JOIN 
                                                sr.org_id,
                                                sr.person_id,
                                                fq.factor_id,
                                                sr.survey_id,
                                                avg(sr.decimal_value) AS mean_value,
                                                the_timestamp,
                                                the_timestamp
                                            FROM 
                                                org_calc_flags_factor ocff
                                                INNER JOIN survey_response sr ON sr.org_id = ocff.org_id
                                                    AND sr.person_id = ocff.person_id
                                                INNER JOIN survey_questions sq ON sq.id = sr.survey_questions_id
                                                INNER JOIN factor_questions fq ON fq.ebi_question_id = sq.ebi_question_id
                                            WHERE
                                                factor_id IS NOT NULL
                                                AND ocff.deleted_at IS NULL
                                                AND sr.deleted_at IS NULL
                                                AND sq.deleted_at IS NULL
                                                AND fq.deleted_at IS NULL
                                                AND sr.survey_id = GET_MOST_RECENT_SURVEY(sr.org_id, sr.person_id)
                                                AND ocff.calculated_at = the_timestamp
                                                AND FLOOR(sr.decimal_value) != 99
                                            GROUP BY sr.org_id, sr.person_id, fq.factor_id, sr.survey_id;
                                    
                                        -- setting student report flags to calculate for chunk
                                        INSERT INTO org_calc_flags_student_reports(org_id, person_id, survey_id, created_at, modified_at, calculated_at, file_name)
                                            SELECT
                                                ocff.org_id,
                                                ocff.person_id,
                                                GET_MOST_RECENT_SURVEY(ocff.org_id, ocff.person_id) AS survey_id,
                                                the_timestamp,
                                                the_timestamp,
                                                NULL,
                                                NULL
                                            FROM
                                                org_calc_flags_factor ocff
                                            WHERE
                                                ocff.calculated_at = the_timestamp
                                                AND ocff.deleted_at IS NULL
                                        ON DUPLICATE KEY UPDATE calculated_at = NULL, file_name = NULL, modified_at = the_timestamp;
                                    
                                        -- setting talking point flags to calculate for chunk
                                        UPDATE 
                                            org_calc_flags_talking_point ocftp
                                            INNER JOIN org_calc_flags_factor ocff ON ocff.org_id = ocftp.org_id
                                                AND ocff.person_id = ocftp.person_id
                                        SET 
                                            ocftp.calculated_at = NULL,
                                            ocftp.modified_at = the_timestamp
                                        WHERE 
                                            ocff.calculated_at = the_timestamp
                                            AND ocftp.calculated_at IS NOT NULL
                                            AND ocff.deleted_at IS NULL
                                            AND ocftp.deleted_at IS NULL;
                            
                                        -- setting risk flags to calculate for chunk
                                        UPDATE 
                                            org_calc_flags_risk ocfr
                                            INNER JOIN org_calc_flags_factor ocff ON ocff.org_id = ocfr.org_id
                                                AND ocff.person_id = ocfr.person_id
                                            INNER JOIN risk_group_person_history rgph
                                                ON rgph.person_id = ocfr.person_id
                                            INNER JOIN org_risk_group_model orgm
                                                ON orgm.risk_group_id = rgph.risk_group_id
                                                    AND ocfr.org_id = orgm.org_id
                                            INNER JOIN risk_model_master rmm
                                                ON rmm.id = orgm.risk_model_id
                                        SET 
                                            ocfr.calculated_at = NULL,
                                            ocfr.modified_at = the_timestamp
                                        WHERE
                                            ocff.calculated_at = the_timestamp
                                            AND ocfr.calculated_at IS NOT NULL
                                            AND ocfr.deleted_at IS NULL
                                            AND ocff.deleted_at IS NULL
                                            AND orgm.deleted_at IS NULL
                                            AND rmm.deleted_at IS NULL
                                            AND rmm.calculation_end_date > the_timestamp;
                                    
                                        -- setting all successful flags to calculated with chunk timestamp (ie, not 99)
                                        UPDATE
                                            org_calc_flags_factor ocff
                                            INNER JOIN 
                                                (SELECT STRAIGHT_JOIN
                                                    sr.org_id,
                                                    sr.person_id
                                                FROM 
                                                    org_calc_flags_factor ocff
                                                        INNER JOIN survey_response sr ON sr.org_id = ocff.org_id
                                                            AND sr.person_id = ocff.person_id
                                                        INNER JOIN survey_questions sq ON sq.id = sr.survey_questions_id
                                                        INNER JOIN factor_questions fq ON fq.ebi_question_id = sq.ebi_question_id
                                                WHERE
                                                    factor_id IS NOT NULL
                                                    AND ocff.deleted_at IS NULL
                                                    AND sr.deleted_at IS NULL
                                                    AND sq.deleted_at IS NULL
                                                    AND fq.deleted_at IS NULL
                                                    AND ocff.calculated_at = the_timestamp
                                                    AND FLOOR(sr.decimal_value) != 99) AS calc
                                            ON calc.org_id = ocff.org_id
                                                AND calc.person_id = ocff.person_id
                                        SET 
                                            calculated_at = the_timestamp,
                                            modified_at = the_timestamp;
                                        
                                        -- marking all unsuccessful flags to have the 1900 flag for calculated and not valid, purposefully not checking soft deletion
                                        UPDATE 
                                            org_calc_flags_factor ocff
                                        SET 
                                            ocff.calculated_at = '1900-01-01 00:00:00',
                                            ocff.modified_at = the_timestamp
                                        WHERE 
                                            ocff.calculated_at = the_timestamp
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
