DELIMITER **
DROP PROCEDURE `Success_Marker_Calc`**

CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Success_Marker_Calc`(deadline TIMESTAMP, chunksize SMALLINT UNSIGNED)
BEGIN
		DECLARE the_ts TIMESTAMP;
		SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
		
		WHILE(
		NOW() < deadline
		AND (SELECT 1 FROM org_calc_flags_success_marker WHERE calculated_at IS NULL LIMIT 1) > 0
		) DO
			SET the_ts=NOW(); 
		
			UPDATE org_calc_flags_success_marker
			SET calculated_at=the_ts
			WHERE calculated_at IS NULL
			ORDER BY modified_at ASC
			LIMIT chunksize;		
	  
			INSERT IGNORE INTO success_marker_calculated(organization_id,person_id,surveymarker_questions_id, color, created_at, modified_at)
				SELECT 
					orc.org_id,
					pfc.person_id,
					smq.id,
					CASE WHEN pfc.mean_value BETWEEN red_low AND red_high THEN 'red'
						 WHEN pfc.mean_value BETWEEN yellow_low AND yellow_high THEN 'yellow'
						 WHEN pfc.mean_value between green_low AND green_high THEN 'green' end AS color, 
					the_ts,
					the_ts
				FROM 
					surveymarker_questions AS smq
						INNER JOIN person_factor_calculated pfc ON smq.factor_id=pfc.factor_id
							AND (pfc.mean_value BETWEEN red_low AND red_high 
								OR pfc.mean_value BETWEEN yellow_low AND yellow_high 
								OR pfc.mean_value BETWEEN green_low AND green_high) 
							AND smq.survey_id = pfc.survey_id
							AND smq.ebi_question_id IS NULL 
							AND smq.survey_questions_id IS NULL
							AND smq.factor_id IS NOT NULL
						INNER JOIN org_calc_flags_success_marker AS orc ON pfc.person_id=orc.person_id 
							AND pfc.organization_id=orc.org_id
							AND orc.calculated_at = the_ts
							AND pfc.survey_id = get_most_recent_survey(orc.org_id, pfc.person_id)
							AND pfc.modified_at = (
								SELECT 
									modified_at 
								FROM 
									person_factor_calculated AS fc
								WHERE 
									fc.organization_id = pfc.organization_id 
									AND fc.person_id = pfc.person_id 
									AND fc.factor_id = pfc.factor_id 
									AND fc.survey_id = pfc.survey_id 
								ORDER BY modified_at DESC LIMIT 1)
				GROUP BY org_id, person_id, id
			UNION
				SELECT 
					orc.org_id,
					svr.person_id,
					smq.id,
					CASE WHEN svr.decimal_value BETWEEN red_low AND red_high THEN 'red'
						WHEN svr.decimal_value BETWEEN yellow_low AND yellow_high THEN 'yellow'
						WHEN svr.decimal_value BETWEEN green_low AND green_high THEN 'green' END AS color,
					the_ts,
					the_ts
				FROM 
					surveymarker_questions smq 
						INNER JOIN survey_questions svq ON smq.ebi_question_id=svq.ebi_question_id
							AND svq.survey_id = smq.survey_id
						INNER JOIN survey_response svr ON svq.id=svr.survey_questions_id
							AND (svr.decimal_value BETWEEN red_low AND red_high
								OR svr.decimal_value BETWEEN yellow_low AND yellow_high 
                                OR svr.decimal_value BETWEEN green_low AND green_high)
							AND svr.survey_id = svq.survey_id
							AND smq.ebi_question_id IS NOT NULL 
							AND smq.factor_id IS NULL
						INNER JOIN org_calc_flags_success_marker orc ON svr.person_id=orc.person_id
							AND orc.calculated_at = the_ts
				WHERE 
					svr.survey_id = get_most_recent_survey(orc.org_id, svr.person_id)
				GROUP BY 
					orc.org_id, svr.person_id, smq.id;

			UPDATE org_calc_flags_success_marker orc 
				LEFT JOIN success_marker_calculated AS smc ON smc.organization_id = orc.org_id 
					AND smc.person_id = orc.person_id 
			SET 
				orc.calculated_at = '1900-01-01 00:00:00', 
				orc.modified_at = the_ts 
			WHERE 
				(smc.modified_at != the_ts OR smc.modified_at IS NULL) 
				AND orc.calculated_at = the_ts;
			
			UPDATE org_calc_flags_success_marker orc 
				LEFT JOIN success_marker_calculated AS smc ON smc.organization_id = orc.org_id 
					AND smc.person_id = orc.person_id 
			SET 
				orc.calculated_at = the_ts, 
				orc.modified_at = the_ts 
			WHERE 
				smc.modified_at = the_ts;
		END WHILE;
END