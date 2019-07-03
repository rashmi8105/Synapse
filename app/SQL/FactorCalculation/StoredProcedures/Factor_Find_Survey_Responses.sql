DELIMITER **
DROP PROCEDURE IF EXISTS `Factor_Find_Survey_Responses`**
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Factor_Find_Survey_Responses`(last_update DATETIME)
BEGIN
		SET @lastupdate = last_update;

        -- inserting any new person/survey into tracking table that have occurred since last run
        INSERT IGNORE INTO synapse.risk_calc_tracking_table(org_id, person_id, survey_id)
			(SELECT
				org_id,
				person_id,
				survey_id
			FROM 
				synapse.survey_response
			WHERE 
				synapse.survey_response.modified_at > @lastupdate
			GROUP BY org_id, person_id, survey_id);
        
        SET @maxMod = (select max(modified_at) FROM synapse.survey_response);
        
        -- Finding most recent response and updating everything in table to have the survey question id
        UPDATE 
			synapse.risk_calc_tracking_table 
		SET 
			most_recent_survey_question_id = GET_MOST_RECENT_SURVEY_QUESTION(org_id, person_id, survey_id),
			last_update_ts=@maxMod
        WHERE
			last_update_ts<@maxMod 
            OR last_update_ts IS NULL;
            
		-- If last seen survey question by risk_calc_tracking_table is different than current question
			-- trigger Factor Calculation
            -- set last seen question to most recent
            -- update modified_at date
        UPDATE
			org_calc_flags_factor AS ocff
			INNER JOIN synapse.risk_calc_tracking_table rctt ON rctt.org_id = ocff.org_id AND rctt.person_id = ocff.person_id
				AND (rctt.most_recent_survey_question_id <> rctt.last_seen_survey_question_id 
					OR rctt.last_seen_survey_question_id is null)
        SET 
			rctt.last_seen_survey_question_id = rctt.most_recent_survey_question_id,
			ocff.calculated_at = null,
			ocff.modified_at = CURRENT_TIMESTAMP();
        
        -- Clean up all completed Surveys from risk_calc_tracking table for performance gain
        DELETE 
			rctt
		FROM
			synapse.risk_calc_tracking_table rctt
			INNER JOIN org_person_student_survey_link opssl ON rctt.org_id = opssl.org_id
				AND rctt.person_id = opssl.person_id 
				AND rctt.survey_id = opssl.survey_id
        WHERE 
			opssl.survey_completion_status = 'CompletedAll';

 END**
