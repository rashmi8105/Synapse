DELIMITER $$
DROP PROCEDURE IF EXISTS `fix_datum_src_ts`$$
CREATE DEFINER = `synapsemaster`@`%` PROCEDURE `fix_datum_src_ts`()
DETERMINISTIC
    SQL SECURITY INVOKER
    BEGIN
        UPDATE
            survey_response
        SET
            created_at = modified_at
        WHERE
            created_at IS NULL;

        UPDATE
            org_question_response
        SET
            created_at = modified_at
        WHERE
            created_at IS NULL;
    END$$