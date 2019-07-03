-- =============================================
-- Author:      Chris McGowan, Josh Stark, Hai Deng
-- Create date: 10/20/2017
-- Description: 1.0 with Soft Deletion
-- =============================================
DELIMITER $$
DROP PROCEDURE IF EXISTS `risk_calculation_V1`$$

CREATE DEFINER = `synapsemaster`@`%` PROCEDURE `risk_calculation_V1`(deadline TIMESTAMP, chunksize SMALLINT UNSIGNED)
DETERMINISTIC
    SQL SECURITY INVOKER
    BEGIN
        DECLARE the_ts TIMESTAMP;

        #--Fix source data timestamps
        CALL fix_datum_src_ts();

        #--Sacrifice some temporal precision for reduced resource contention
        SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

        WHILE(
            NOW() < deadline
            AND (
                    SELECT
                        1
                    FROM
                        org_calc_flags_risk
                    WHERE
                        calculated_at IS NULL
                        AND deleted_at IS NULL
                    LIMIT 1) > 0)
        DO
            SET the_ts = NOW();

            #--Carve out a limited chunk to materialize
            TRUNCATE etldata.tmp_ocfr_chunk;

            INSERT IGNORE INTO etldata.tmp_ocfr_chunk
                SELECT
                    id
                FROM
                    org_calc_flags_risk
                WHERE
                    calculated_at IS NULL
                    AND deleted_at IS NULL
                ORDER BY modified_at ASC
                LIMIT chunksize;

            UPDATE
                    org_calc_flags_risk ocfr
                    INNER JOIN etldata.tmp_ocfr_chunk toc ON ocfr.id = toc.id
            SET
                calculated_at = the_ts
            WHERE
                ocfr.deleted_at IS NULL;

            #--Materialize the intermediate view
            INSERT IGNORE INTO org_calculated_risk_variables_history(person_id, risk_variable_id, risk_group_id, risk_model_id, created_at, org_id, calc_bucket_value, calc_weight, risk_source_value)
                SELECT
                    OCRV.person_id,
                    OCRV.risk_variable_id,
                    OCRV.risk_group_id,
                    OCRV.risk_model_id,
                    the_ts AS created_at,
                    OCRV.org_id,
                    bucket_value AS calc_bucket_value,
                    bucket_value * weight AS calc_weight,
                    calculated_value AS risk_source_value
                FROM
                    org_calculated_risk_variables_view OCRV
                    INNER JOIN (
                                   SELECT
                                       person_id
                                   FROM
                                       org_calc_flags_risk
                                   WHERE
                                       calculated_at = the_ts
                                       AND deleted_at IS NULL
                               ) AS stale
                        ON stale.person_id = OCRV.person_id;

            #--Materialize the risk score view
            INSERT IGNORE INTO person_risk_level_history(person_id, date_captured, risk_model_id, risk_level, risk_score, weighted_value, maximum_weight_value, created_at, queued_at)
                SELECT
                    prlc.person_id,
                    the_ts,
                    prlc.risk_model_id,
                    prlc.risk_level,
                    prlc.risk_score,
                    prlc.weighted_value,
                    prlc.maximum_weight_value
                    NOW() AS created_at,
                    stale.modified_at AS queued_at
                FROM person_risk_level_calc AS prlc
                    INNER JOIN (
                                   SELECT
                                       person_id,
                                       modified_at
                                   FROM
                                       org_calc_flags_risk
                                   WHERE
                                       calculated_at = the_ts
                                       AND deleted_at IS NULL
                               ) AS stale
                        ON stale.person_id = prlc.person_id;


            #--Update the redundant person value for risk score
            UPDATE person P
                INNER JOIN person_risk_level_history AS PRH
                    ON P.id = PRH.person_id
                       AND PRH.date_captured = the_ts
            SET
                P.risk_level = PRH.risk_level,
                P.risk_update_date = the_ts
            WHERE P.deleted_at IS NULL
                  AND NOT PRH.risk_score <=> (SELECT
                                                  risk_score
                                              FROM
                                                  person_risk_level_history pr
                                              WHERE
                                                  pr.person_id = P.id
                                              ORDER BY date_captured DESC
                                              LIMIT 1, 1
            );

            #--Set magic dates for blank scores, soft deletion purposefully not taken into account here
            UPDATE org_calc_flags_risk AS OCFR
                LEFT JOIN person_risk_level_history AS PRLH
                    ON PRLH.person_id = OCFR.person_id
                       AND PRLH.date_captured = the_ts
            SET
                OCFR.calculated_at = '1900-01-01 00:00:00'
            WHERE
                OCFR.calculated_at = the_ts
                AND PRLH.weighted_value IS NULL;
        END WHILE;

    END$$