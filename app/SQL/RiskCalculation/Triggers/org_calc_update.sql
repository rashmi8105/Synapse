DELIMITER **

DROP TRIGGER IF EXISTS `org_calc_update`**
CREATE DEFINER=`synapsemaster`@`%` TRIGGER org_calc_update AFTER UPDATE ON org_riskval_calc_inputs
          FOR EACH ROW
          BEGIN
            UPDATE
                org_calc_flags_risk
            SET
                calculated_at = NULL,
                modified_at = CURRENT_TIMESTAMP
            WHERE
                org_id = NEW.org_id
                AND person_id = NEW.person_id
                AND calculated_at IS NOT NULL;

            UPDATE
                org_calc_flags_talking_point
            SET
                calculated_at = NULL,
                modified_at = CURRENT_TIMESTAMP
            WHERE
                org_id = NEW.org_id
                AND person_id = NEW.person_id
                AND calculated_at IS NOT NULL;
          END**