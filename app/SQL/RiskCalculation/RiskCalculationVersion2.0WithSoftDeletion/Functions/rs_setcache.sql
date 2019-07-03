DELIMITER $$
DROP FUNCTION IF EXISTS `RS_setcache`$$
CREATE DEFINER = `synapsemaster`@`%`  FUNCTION `RS_setcache`(the_org_id INT, the_group_id INT, the_person_id INT)
    RETURNS BOOLEAN
READS SQL DATA
DETERMINISTIC
    SQL SECURITY INVOKER
    BEGIN

        #--Optimization (use the last value generated if it matches parameters)
        IF (the_person_id = @cache_RS_person_id
            AND the_org_id = @cache_RS_org_id
            AND the_group_id = @cache_RS_group_id
            AND @cache_RS_ts = NOW(6) + 0)
        THEN
            RETURN TRUE;
        END IF;

        SET
        @cache_RS_person_id = the_person_id,
        @cache_RS_org_id = the_org_id,
        @cache_RS_group_id = the_group_id,
        @cache_RS_ts = NOW(6) + 0;

        SELECT
            SUM(bucket_value * weight),
            SUM(weight)
        INTO
            @cache_RSnumer_ret,
            @cache_RSdenom_ret
        FROM
            org_calculated_risk_variables_view
        WHERE
            org_id = the_org_id
            AND risk_group_id = the_group_id
            AND person_id = the_person_id;

        RETURN FALSE;
    END$$
