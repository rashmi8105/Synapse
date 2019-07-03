DELIMITER **


CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `survey_data_transfer`()
BEGIN

                        SELECT NOW() INTO @current;

                        -- Main insert of new survey responses
                        INSERT IGNORE INTO synapse.survey_response
                        (SELECT * FROM etldata.survey_response WHERE modified_at > (SELECT MAX(modified_at) FROM synapse.survey_response));

                        -- Cleanup insert for responses missed in down windows
                        INSERT IGNORE INTO synapse.survey_response (survey_questions_id, org_id, person_id, survey_id, org_academic_year_id, org_academic_terms_id, modified_at, response_type, decimal_value, char_value, charmax_value) (
                        SELECT ers.survey_questions_id, ers.org_id, ers.person_id, ers.survey_id, ers.org_academic_year_id, ers.org_academic_terms_id, @current, ers.response_type, ers.decimal_value, ers.char_value, ers.charmax_value
                        FROM etldata.survey_response ers
                            LEFT OUTER JOIN synapse.survey_response sr ON
                            (ers.person_id , ers.org_id, ers.survey_id, ers.survey_questions_id) =
                            (sr.person_id , sr.org_id, sr.survey_id, sr.survey_questions_id)
                        WHERE
                            sr.id IS NULL
                            AND ers.modified_at >= (@current - INTERVAL 12 HOUR)
                        );

                        -- Update Has_Responses values to match Synapse survey responses
                        UPDATE synapse.org_person_student_survey_link opssl
                        LEFT JOIN synapse.survey_response sr ON sr.person_id = opssl.person_id AND sr.survey_id = opssl.survey_id

                        SET opssl.Has_Responses = 'Yes'
                        WHERE sr.id IS NOT NULL AND opssl.Has_Responses = 'No';

                        -- Update questions that have been reanswered since the last update time
                        -- Set org_calc_flags_factor to trigger all calculations
                        -- if questions are re-answered
                        -- IF YOU DO NOT WANT TO TRIGGER FLAGS COMMENT OUT BELOW
                        UPDATE synapse.survey_response sr
                        INNER JOIN org_calc_flags_factor ocff 
                        ON sr.person_id = ocff.person_id
                        AND sr.org_id = ocff.org_id
						INNER JOIN etldata.survey_response ers 
                        ON ers.person_id = sr.person_id
                        AND ers.org_id = sr.org_id
                        AND ers.survey_id = sr.survey_id
                        AND ers.survey_questions_id = sr.survey_questions_id
                        SET
                            sr.decimal_value = ers.decimal_value,
                            sr.char_value = ers.char_value,
                            sr.charmax_value = ers.charmax_value,
                            sr.modified_at = @current,
                            ocff.calculated_at = NULL,
                            ocff.modified_at = @current
                        WHERE
                            (sr.decimal_value <> ers.decimal_value
                                OR sr.char_value <> ers.char_value
                                OR sr.charmax_value <> ers.charmax_value)
                                AND sr.modified_at <= ers.modified_at
                                AND ers.modified_at > (SELECT MAX(last_update_ts) FROM etldata.last_response_update LIMIT 1);

                        -- Update the last update time to now
                        UPDATE etldata.last_response_update SET last_update_ts = @current;
                        END**
                        
                        
                        update org_calc_flags_factor SET calculated_at= NULL where person_id = 4876508;
                       
                        