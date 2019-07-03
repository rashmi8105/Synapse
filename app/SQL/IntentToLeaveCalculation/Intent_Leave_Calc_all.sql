DELIMITER **
DROP PROCEDURE IF EXISTS `Intent_Leave_Calc_all`**

CREATE DEFINER =`synapsemaster`@`%` PROCEDURE `Intent_Leave_Calc_all`()
    BEGIN

        SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

        TRUNCATE etldata.`person_intent_to_leave`;

        INSERT IGNORE INTO etldata.`person_intent_to_leave`
            SELECT
                sr.person_id,
                itl.id,
                sr.modified_at
            FROM
                survey_response sr
                INNER JOIN survey_questions sq
                    ON sr.survey_questions_id = sq.id
                       AND sq.qnbr = 4
                INNER JOIN (SELECT
                                person_id,
                                MAX(SRin.modified_at) AS modified_at
                            FROM
                                survey_response AS SRin
                                INNER JOIN survey_questions SQin
                                    ON SRin.survey_questions_id = SQin.id
                                       AND SQin.qnbr = 4
                            GROUP BY
                                person_id) sm
                    ON sr.person_id = sm.person_id
                       AND sr.modified_at = sm.modified_at
                INNER JOIN intent_to_leave itl
                    ON sr.decimal_value BETWEEN itl.min_value AND itl.max_value
            WHERE
                itl.deleted_at IS NULL
                AND sq.deleted_at IS NULL
                AND sr.deleted_at IS NULL;

        UPDATE person p
            INNER JOIN etldata.`person_intent_to_leave` pitl
                ON p.id = pitl.person_id
        SET
            p.intent_to_leave             = pitl.intent_to_leave,
            p.intent_to_leave_update_date = pitl.intent_to_leave_update_date
        WHERE
            p.deleted_at IS NULL;

        CALL `Intent_Leave_Null_Fixer`();
    END**






