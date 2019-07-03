<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171109222132 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("DROP PROCEDURE IF EXISTS `risk_calculation_V2`;");
        $this->addSql("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `risk_calculation_V2`(deadline TIMESTAMP, chunksize SMALLINT UNSIGNED)
                            DETERMINISTIC
                                SQL SECURITY INVOKER
                                BEGIN
                                    DECLARE the_timestamp TIMESTAMP;
                            
                                    #--Fix source data timestamps
                                    CALL fix_datum_src_ts();
                            
                                    #--Sacrifice some temporal precision for reduced resource contention
                                    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
                            
                                    WHILE(
                                        #-- For testing, use 1 = 1 instead of NOW() < deadline
                                        #-- 1 = 1
                                        NOW() < deadline
                                        AND ( SELECT
                                                  1
                                              FROM
                                                  org_calc_flags_risk
                                              WHERE
                                                  calculated_at IS NULL
                                                  AND deleted_at IS NULL
                                              LIMIT 1) > 0
                                    ) DO
                            
                                        SET the_timestamp = NOW();
                            
                                        #-- Drop temp table
                                        DROP TABLE IF EXISTS tmp_person_chunk;
                            
                                        #--Carve out a limited chunk to materialize
                                        CREATE TEMPORARY TABLE tmp_person_chunk(person_id INT NOT NULL, queued_at DATETIME DEFAULT NULL);
                            
                                        INSERT IGNORE INTO tmp_person_chunk
                                            SELECT
                                                person_id,
                                                modified_at
                                            FROM
                                                org_calc_flags_risk
                                            WHERE
                                                calculated_at IS NULL
                                                AND deleted_at IS NULL
                                            ORDER BY modified_at ASC
                                            LIMIT chunksize;
                            
                            
                                        #--Update flag table
                                        UPDATE
                                                org_calc_flags_risk ocfr
                                                INNER JOIN tmp_person_chunk tpc ON ocfr.person_id = tpc.person_id
                                        SET
                                            ocfr.calculated_at = the_timestamp
                                        WHERE
                                            ocfr.deleted_at IS NULL;
                            
                                        #-- Drop temp table
                                        DROP TABLE IF EXISTS tmp_org_calculated_risk_variables;
                            
                                        #--Create temporary org_calculated_risk_variables_view table
                                        CREATE TEMPORARY TABLE tmp_org_calculated_risk_variables (
                                            org_id INT(11) NOT NULL,
                                            person_id INT(11) NOT NULL,
                                            risk_variable_id INT(11) NOT NULL,
                                            risk_group_id INT(11) NOT NULL,
                                            risk_model_id INT(11) NOT NULL,
                                            calc_bucket_value INT(11) DEFAULT NULL,
                                            weight DECIMAL(12,4) DEFAULT NULL,
                                            risk_source_value DECIMAL(12,4) DEFAULT NULL,
                                            queued_at DATETIME DEFAULT NULL,
                                            PRIMARY KEY (person_id, org_id, risk_group_id, risk_model_id, risk_variable_id)
                                        ) ENGINE = InnoDB;
                            
                                        #--Insert IGNORE INTO tmp_org_calculated_risk_variables
                                        INSERT IGNORE INTO tmp_org_calculated_risk_variables (org_id, person_id, risk_variable_id, risk_group_id, risk_model_id, calc_bucket_value, weight, risk_source_value, queued_at)
                                            SELECT
                                                OCRVV.org_id,
                                                OCRVV.person_id,
                                                OCRVV.risk_variable_id,
                                                OCRVV.risk_group_id,
                                                OCRVV.risk_model_id,
                                                bucket_value,
                                                weight,
                                                calculated_value AS risk_source_value,
                                                TPC.queued_at
                                            FROM
                                                org_calculated_risk_variables_view OCRVV
                                                INNER JOIN tmp_person_chunk TPC
                                                    ON OCRVV.person_id = TPC.person_id;
                            
                            
                            
                                        INSERT IGNORE INTO org_calculated_risk_variables_history (person_id, risk_variable_id, risk_group_id, risk_model_id, created_at, org_id, calc_bucket_value, calc_weight, risk_source_value)
                                            SELECT
                                                person_id,
                                                risk_variable_id,
                                                risk_group_id,
                                                risk_model_id,
                                                NOW() AS created_at,
                                                org_id,
                                                calc_bucket_value,
                                                calc_bucket_value * weight AS calc_weight,
                                                risk_source_value
                                            FROM
                                                tmp_org_calculated_risk_variables;
                            
                                        #--Clean temp table in time
                                        DROP TABLE IF EXISTS tmp_person_risk_level_history;
                            
                                        #--Materialize the risk score view into the transition table
                                        CREATE TEMPORARY TABLE tmp_person_risk_level_history (
                                            person_id INT(11) NOT NULL,
                                            date_captured DATETIME NOT NULL,
                                            risk_level INT(11) DEFAULT NULL,
                                            risk_model_id INT(11) DEFAULT NULL,
                                            risk_score DECIMAL(6,4) DEFAULT NULL,
                                            weighted_value DECIMAL(12,4) DEFAULT NULL,
                                            maximum_weight_value DECIMAL(12,4) DEFAULT NULL,
                                            queued_at DATETIME DEFAULT NULL,
                                            isUpdated enum('n','y') DEFAULT 'n',
                                            PRIMARY KEY (person_id)
                                        ) ENGINE = InnoDB;
                            
                            
                                        #--Load data in the transition table into person_risk_level_history
                                        INSERT INTO tmp_person_risk_level_history(person_id, date_captured, risk_model_id, risk_level, risk_score, weighted_value, maximum_weight_value, queued_at)
                                            SELECT
                                                PRC.person_id,
                                                the_timestamp AS date_captured,
                                                orgm.risk_model_id,
                                                RML.risk_level,
                                                PRC.risk_score,
                                                PRC.RS_Numerator,
                                                PRC.RS_Denominator,
                                                PRC.queued_at
                                            FROM
                                                (SELECT
                                                     tocrv.org_id,
                                                     tocrv.person_id,
                                                     ROUND(SUM(tocrv.calc_bucket_value*tocrv.weight), 4) AS RS_Numerator,
                                                     ROUND(SUM(tocrv.weight), 4) AS RS_Denominator,
                                                     ROUND(SUM(tocrv.calc_bucket_value * tocrv.weight) / SUM(tocrv.weight), 4) AS risk_score,
                                                     tocrv.queued_at
                                                 FROM
                                                     tmp_org_calculated_risk_variables tocrv
                                                 GROUP BY
                                                     tocrv.person_id,
                                                     tocrv.org_id,
                                                     tocrv.queued_at) PRC
                                                INNER JOIN risk_group_person_history rgph ON PRC.person_id = rgph.person_id
                                                INNER JOIN org_risk_group_model orgm
                                                    ON rgph.risk_group_id = orgm.risk_group_id
                                                       AND orgm.org_id = PRC.org_id
                                                       AND orgm.risk_model_id IS NOT NULL
                                                       AND orgm.deleted_at IS NULL
                                                INNER JOIN risk_model_master rmm
                                                    ON orgm.risk_model_id = rmm.id
                                                       AND NOW() BETWEEN rmm.calculation_start_date AND rmm.calculation_end_date
                                                       AND rmm.deleted_at IS NULL
                                                LEFT JOIN risk_model_levels RML
                                                    ON RML.risk_model_id = orgm.risk_model_id
                                                       AND PRC.risk_score >= RML.min
                                                       AND PRC.risk_score < RML.max
                                                       AND RML.deleted_at IS NULL;
                            
                            
                                        #--Load data from the transition table into person_risk_level_history
                                        INSERT IGNORE INTO synapse.person_risk_level_history(person_id, date_captured, risk_model_id, risk_level, risk_score, weighted_value, maximum_weight_value, created_at, queued_at)
                                            SELECT
                                                person_id,
                                                date_captured,
                                                risk_model_id,
                                                risk_level,
                                                risk_score,
                                                weighted_value,
                                                maximum_weight_value,
                                                NOW() AS created_at,
                                                queued_at
                                            FROM
                                                tmp_person_risk_level_history;
                            
                            
                                        #--Update the redundant person value for risk score
                                        UPDATE
                                            tmp_person_risk_level_history TPRLH
                                        SET
                                            TPRLH.isUpdated = 'y'
                                        WHERE
                                            NOT TPRLH.risk_score <=>
                                                (SELECT
                                                     risk_score
                                                 FROM
                                                     person_risk_level_history prlh
                                                 WHERE
                                                     prlh.person_id = TPRLH.person_id
                                                 ORDER BY date_captured DESC
                                                 LIMIT 1, 1);
                            
                                        #-- WE SHOULD JUST USE THE tmp_person_risk_level_history table...USING THE LATEST VALUE INSTEAD OF STORING IT AGAIN IN THE person TABLE
                                        UPDATE person P
                                            INNER JOIN tmp_person_risk_level_history TPRLH
                                                ON P.id = TPRLH.person_id
                                        SET
                                            P.risk_level = TPRLH.risk_level,
                                            P.risk_update_date = the_timestamp
                                        WHERE
                                            TPRLH.isUpdated = 'y';
                            
                                        #-- Set magic dates for blank scores, soft deletion purposefully not taken into account here
                                        UPDATE
                                                org_calc_flags_risk OCFR
                                                LEFT JOIN tmp_person_risk_level_history TPRLH
                                                    ON TPRLH.person_id = OCFR.person_id
                                        SET
                                            OCFR.calculated_at = '1900-01-01 00:00:00'
                                        WHERE
                                            OCFR.calculated_at = the_timestamp
                                            AND TPRLH.weighted_value IS NULL;
                            
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
