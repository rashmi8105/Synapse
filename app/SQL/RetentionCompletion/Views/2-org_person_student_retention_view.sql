-- org_person_student_retention with year_id and year_name added
CREATE OR REPLACE
    ALGORITHM = MERGE 
    DEFINER = `synapsemaster`@`%` 
    SQL SECURITY INVOKER
VIEW `org_person_student_retention_view` AS
SELECT
    opsr.organization_id,
    opsr.person_id,
    oay.year_id AS year_id,
    oay.name AS year_name,
    opsr.is_enrolled_beginning_year,
    opsr.is_enrolled_midyear,
    opsr.is_degree_completed
FROM
    org_person_student_retention opsr
    INNER JOIN org_academic_year oay
        ON opsr.org_academic_year_id = oay.id
WHERE
    opsr.deleted_at IS NULL
    AND oay.deleted_at IS NULL;