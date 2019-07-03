DELIMITER **
DROP PROCEDURE IF EXISTS `Factor_Find_Survey_Responses_ISQ`**
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Factor_Find_Survey_Responses_ISQ`(last_update_ISQ DATETIME)
BEGIN
		SET @lastupdateISQ = last_update_ISQ;
	
        -- inserting any new person/survey into tracking table that have occurred since last run
		INSERT IGNORE INTO synapse.risk_calc_tracking_table_ISQ(org_id, person_id, survey_id)
			(SELECT
				org_id,
				person_id,
				survey_id
			FROM 
				synapse.org_question_response
			WHERE 
				synapse.org_question_response.modified_at > @lastupdateISQ
			GROUP BY org_id, person_id,survey_id);
        
        SET @maxISQ = (SELECT max(modified_at) FROM synapse.org_question_response);
        
		-- Finding most recent response and updating everything in table to have the survey question id
        UPDATE 
			synapse.risk_calc_tracking_table_ISQ
		SET 
			most_recent_org_question_id = GET_MOST_RECENT_ISQ(org_id, person_id, survey_id),
            last_update_ts=@maxISQ 
        WHERE 
			last_update_ts<@maxISQ 
            OR last_update_ts IS NULL;
        
        -- If last seen survey question by risk_calc_tracking_table is different than current question
			-- trigger Factor Calculation
            -- set last seen question to most recent
            -- update modified_at date
        UPDATE 
			org_calc_flags_risk ocfr
			INNER JOIN synapse.risk_calc_tracking_table_ISQ rctt ON rctt.org_id = ocfr.org_id
				AND rctt.person_id = ocfr.person_id
				AND (rctt.most_recent_org_question_id <> rctt.last_seen_org_question_id
					OR rctt.last_seen_org_question_id IS NULL) 
        SET 
			rctt.last_seen_org_question_id = rctt.most_recent_org_question_id,
			ocfr.calculated_at = NULL,
			ocfr.modified_at = CURRENT_TIMESTAMP();
            
		-- Clean up all completed Surveys from risk_calc_tracking table for performance gain
        DELETE 
			rctt
		FROM
			synapse.risk_calc_tracking_table_ISQ AS rctt
            INNER JOIN org_person_student_survey_link opssl ON rctt.org_id = opssl.org_id
				AND rctt.person_id = opssl.person_id 
				AND rctt.survey_id = opssl.survey_id
        WHERE
			opssl.survey_completion_status = 'CompletedAll';

END**