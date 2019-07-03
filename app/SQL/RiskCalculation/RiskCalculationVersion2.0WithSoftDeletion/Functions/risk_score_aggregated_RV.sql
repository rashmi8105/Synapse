DELIMITER $$
DROP FUNCTION IF EXISTS `risk_score_aggregated_RV`$$
CREATE DEFINER = `synapsemaster`@`%` FUNCTION `risk_score_aggregated_RV`(the_org_id INT, the_person_id INT, the_RV_id INT, agg_type VARCHAR(32), the_start_date DATETIME, the_end_date DATETIME) RETURNS varchar(255) CHARSET utf8
READS SQL DATA
DETERMINISTIC
    SQL SECURITY INVOKER
    BEGIN
        #--Optimization (use the last value generated if it matches parameters)
        IF(the_org_id = @cache_RSaggRV_org_id
           AND the_person_id = @cache_RSaggRV_person_id
           AND the_RV_id = @cache_RSaggRV_RV_id
           AND @cache_RSaggRV_ts = NOW(6) + 0)
        THEN
            RETURN @cache_RSaggRV_ret;
        END IF;

        SET
        @cache_RSaggRV_org_id = the_org_id,
        @cache_RSaggRV_person_id = the_person_id,
        @cache_RSaggRV_RV_id = the_RV_id,
        @cache_RSaggRV_ts = NOW(6) + 0;

        IF(agg_type IS NULL) THEN
            SET @cache_RSaggRV_ret=(
                SELECT
                    RD.source_value AS calculated_value
                FROM
                    org_person_riskvariable_datum AS RD
                WHERE
                    RD.org_id = the_org_id
                    AND RD.person_id = the_person_id
                    AND RD.risk_variable_id = the_RV_id
                ORDER BY modified_at DESC, created_at DESC
                LIMIT 1
            );
        ELSEIF(agg_type='Sum') THEN
            SET @cache_RSaggRV_ret=(
                SELECT
                    SUM(RD.source_value) AS calculated_value
                FROM
                    org_person_riskvariable_datum AS RD
                WHERE
                    RD.org_id = the_org_id
                    AND RD.person_id = the_person_id
                    AND RD.risk_variable_id = the_RV_id
                GROUP BY
                    RD.person_id,
                    RD.risk_variable_id
            );
        ELSEIF(agg_type='Count') THEN
            SET @cache_RSaggRV_ret=(
                SELECT
                    COUNT(RD.source_value) AS calculated_value
                FROM
                    org_person_riskvariable_datum AS RD
                WHERE
                    RD.org_id = the_org_id
                    AND RD.person_id = the_person_id
                    AND RD.risk_variable_id = the_RV_id
                GROUP BY
                    RD.person_id,
                    RD.risk_variable_id
            );
        ELSEIF(agg_type='Average') THEN
            SET @cache_RSaggRV_ret=(
                SELECT
                    AVG(RD.source_value) AS calculated_value
                FROM
                    org_person_riskvariable_datum AS RD
                WHERE
                    RD.org_id = the_org_id
                    AND RD.person_id = the_person_id
                    AND RD.risk_variable_id = the_RV_id
                GROUP BY
                    RD.person_id,
                    RD.risk_variable_id
            );
        ELSEIF(agg_type='Most Recent') THEN
            SET @cache_RSaggRV_ret= (
                SELECT
                    step.source_value AS calculated_value
                FROM (
                         SELECT
                             RD.source_value,
                             COALESCE(oat.end_date, oay.end_date) as end_date,
                             COALESCE(DATEDIFF(oat.end_date, oat.start_date), DATEDIFF(oay.end_date, oay.start_date)) as length,
                             RD.modified_at,
                             RD.created_at
                         FROM
                             org_person_riskvariable_datum AS RD
                             LEFT JOIN
                             org_academic_year oay
                                 ON oay.id = RD.org_academic_year_id
                                    AND oay.deleted_at IS NULL
                             LEFT JOIN
                             org_academic_terms oat
                                     ON oat.id = RD.org_academic_terms_id
                                        AND oat.deleted_at IS NULL
                         WHERE
                             RD.org_id = the_org_id
                             AND RD.person_id = the_person_id
                             AND RD.risk_variable_id = the_RV_id
                             AND (
                                 (oay.id is null AND oat.id is null) OR
                                 ((oat.end_date BETWEEN the_start_date AND the_end_date) AND RD.scope = 'T') OR
                                 ((oay.end_date BETWEEN the_start_date AND the_end_date) AND RD.scope = 'Y')
                             )) as step
                ORDER BY step.end_date DESC, step.length DESC, step.modified_at DESC, step.created_at DESC
                LIMIT 1);
        ELSEIF(agg_type='Academic Update') THEN
            SET @cache_RSaggRV_ret=(
                #--TODO: resolve created_at vs. modified_at to audit/time-series dimensions
                SELECT
                    COUNT(*) AS calculated_value
                FROM (
                         SELECT
                             DISTINCT au.org_courses_id,
                             au.failure_risk_level,
                             au.grade
                         FROM
                             academic_update AS au
                             INNER JOIN (
                                            SELECT
                                                au_in.org_courses_id,
                                                au_in.org_id,
                                                au_in.person_id_student,
                                                MAX(au_in.modified_at) as modified_at
                                            FROM
                                                academic_update AS au_in
                                                INNER JOIN org_person_riskvariable AS RD
                                                    ON RD.org_id = au_in.org_id
                                                       AND RD.person_id = au_in.person_id_student
                                                LEFT JOIN risk_variable AS RV
                                                    ON RV.id = RD.risk_variable_id
                                                       AND RV.deleted_at IS NULL
                                            WHERE
                                                RD.risk_variable_id = the_RV_id
                                                AND au_in.org_id = the_org_id
                                                AND au_in.person_id_student = the_person_id
                                                AND au_in.deleted_at IS NULL
                                                AND au_in.`status` <> 'saved'
                                                AND (au_in.failure_risk_level IS NOT NULL
                                                     OR au_in.grade IS NOT NULL)
                                                AND au_in.modified_at BETWEEN RV.calculation_start_date and RV.calculation_end_date
                                            GROUP BY
                                                au_in.org_courses_id
                                        ) AS au_mid
                                 ON au.org_courses_id = au_mid.org_courses_id
                                    AND au.modified_at = au_mid.modified_at
                                    AND au_mid.org_id = au.org_id
                                    AND au_mid.person_id_student = au.person_id_student
                     ) AS most_recent
                WHERE
                    UPPER(failure_risk_level) = 'HIGH'
                    OR UPPER(grade) IN ('D','F','F/No Pass')
            );

        ELSE
            SET @cache_RSaggRV_ret = NULL;
        END IF;

        RETURN @cache_RSaggRV_ret;
    END$$