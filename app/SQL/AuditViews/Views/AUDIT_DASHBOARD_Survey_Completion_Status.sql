CREATE OR REPLACE
  ALGORITHM = UNDEFINED
  DEFINER = `synapsemaster`@`%`
  SQL SECURITY DEFINER
VIEW `AUDIT_DASHBOARD_Survey_Completion_Status` AS
  SELECT
    survey_completion_status,
    survey_opt_out_status,
    Has_Responses,
    CASE WHEN (survey_completion_status = 'Assigned' AND survey_opt_out_status = 'Yes' AND Has_Responses = 'No') THEN 'Yes'
    WHEN (survey_completion_status = 'Assigned' AND survey_opt_out_status = 'No'  AND Has_Responses = 'No') THEN 'Yes'
    WHEN (survey_completion_status = 'CompletedMandatory' AND survey_opt_out_status = 'No'   AND Has_Responses = 'Yes') THEN 'Yes'
    WHEN (survey_completion_status = 'CompletedMandatory' AND survey_opt_out_status = 'Yes'  AND Has_Responses = 'Yes') THEN 'Yes'
    WHEN (survey_completion_status = 'CompletedAll' AND survey_opt_out_status = 'No'   AND Has_Responses = 'Yes') THEN 'Yes'
    WHEN (survey_completion_status = 'CompletedAll' AND survey_opt_out_status = 'Yes'  AND Has_Responses = 'Yes') THEN 'Yes'
    ELSE 'No'
    END as valid_combination,
    CASE WHEN (survey_completion_status = 'Assigned' AND survey_opt_out_status = 'Yes' AND Has_Responses = 'No') THEN 'No'
    WHEN (survey_completion_status = 'Assigned' AND survey_opt_out_status = 'No'  AND Has_Responses = 'No') THEN 'No'
    WHEN (survey_completion_status = 'CompletedMandatory' AND survey_opt_out_status = 'No'   AND Has_Responses = 'Yes') THEN 'No'
    WHEN (survey_completion_status = 'CompletedMandatory' AND survey_opt_out_status = 'Yes'  AND Has_Responses = 'Yes') THEN 'No'
    WHEN (survey_completion_status = 'CompletedAll' AND survey_opt_out_status = 'No'   AND Has_Responses = 'Yes') THEN 'No'
    WHEN (survey_completion_status = 'CompletedAll' AND survey_opt_out_status = 'Yes'  AND Has_Responses = 'Yes') THEN 'No'
    WHEN modified_at < NOW() - INTERVAL 1 HOUR THEN 'Yes'
    ELSE 'No'
    END as needs_manual_intervention,
    COUNT(*) as student_survey_link_count,
    GROUP_CONCAT(DISTINCT org_id ORDER BY org_id ASC) as org_id,
    MAX(modified_at) AS date_last_updated
  FROM synapse.org_person_student_survey_link
  GROUP BY survey_completion_status, survey_opt_out_status, Has_Responses;