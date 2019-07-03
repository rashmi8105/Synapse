DELIMITER $$
DROP FUNCTION IF EXISTS `RS_denominator`$$
CREATE DEFINER = `synapsemaster`@`%`  FUNCTION `RS_denominator`(the_org_id INT, the_group_id INT, the_person_id INT)
    RETURNS DECIMAL(18,9)
READS SQL DATA
DETERMINISTIC
    SQL SECURITY INVOKER
    BEGIN
        DECLARE RShitormiss BOOL;
        #--Optimization (use the last value generated if it matches parameters)
        SET RShitormiss = RS_setcache(the_org_id, the_group_id, the_person_id);

        RETURN @cache_RSdenom_ret;
    END$$