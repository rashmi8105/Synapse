-- Gets all students combined with all possible past and current (non-future) years
CREATE OR REPLACE
    ALGORITHM = MERGE 
    DEFINER = `synapsemaster`@`%` 
    SQL SECURITY INVOKER
VIEW `past_current_years_per_student_view` AS
    SELECT 
        ops.organization_id,
        ops.person_id,
        oay.year_id,
        oay.name AS year_name
    FROM
        org_person_student ops
            INNER JOIN
        org_academic_year oay ON ops.organization_id = oay.organization_id
    WHERE
        oay.start_date <= DATE(NOW())
        AND ops.deleted_at IS NULL
        AND oay.deleted_at IS NULL;
    
