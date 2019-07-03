DELIMITER **

DROP TRIGGER IF EXISTS `org_calc_move`**
CREATE DEFINER=`synapsemaster`@`%` TRIGGER org_calc_move AFTER INSERT ON org_riskval_calc_inputs
                FOR EACH ROW
                BEGIN
                    INSERT IGNORE INTO org_calc_flags_factor (org_id, person_id, calculated_at, created_at, modified_at)
                    VALUES(NEW.org_id, NEW.person_id, '1910-10-10 10:10:10', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

                    INSERT IGNORE INTO org_calc_flags_risk (org_id, person_id, calculated_at, created_at, modified_at)
                    VALUES(NEW.org_id, NEW.person_id, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

                    INSERT IGNORE INTO org_calc_flags_talking_point (org_id, person_id, calculated_at, created_at, modified_at)
                    VALUES(NEW.org_id, NEW.person_id, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

                END**