-- org_person_student_retention_tracking_group with year_id and year_name added
CREATE OR REPLACE
    ALGORITHM = MERGE 
    DEFINER = `synapsemaster`@`%` 
    SQL SECURITY INVOKER
VIEW `org_person_student_retention_tracking_group_view` AS
    SELECT 
        opsrtg.organization_id,
        opsrtg.person_id,
        oay.year_id AS retention_tracking_year,
        oay.name AS year_name
    FROM
        org_person_student_retention_tracking_group opsrtg
            INNER JOIN
        org_academic_year oay ON opsrtg.org_academic_year_id = oay.id
    WHERE
        opsrtg.deleted_at IS NULL
        AND oay.deleted_at IS NULL;
