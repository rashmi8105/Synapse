-- Creates rows for years with no retention data, replaces nulls with zeros, and calculates years from retention track
CREATE OR REPLACE
    ALGORITHM = MERGE
    DEFINER = `synapsemaster`@`%` 
    SQL SECURITY INVOKER
VIEW `org_person_student_retention_by_tracking_group_view` AS
SELECT
    opsrtgv.organization_id,
    opsrtgv.person_id,
    opsrtgv.retention_tracking_year,
    pcypsv.year_id,
    pcypsv.year_name,
    IFNULL(opsrv.is_enrolled_beginning_year, 0) AS is_enrolled_beginning_year,
    IFNULL(opsrv.is_enrolled_midyear, 0) AS is_enrolled_midyear,
    IFNULL(opsrv.is_degree_completed, 0) AS is_degree_completed,
    (RIGHT(pcypsv.year_id, 2) - RIGHT(opsrtgv.retention_tracking_year, 2)) AS years_from_retention_track
FROM
    past_current_years_per_student_view pcypsv
    INNER JOIN org_person_student_retention_tracking_group_view opsrtgv
        ON pcypsv.organization_id = opsrtgv.organization_id
        AND pcypsv.person_id = opsrtgv.person_id
    LEFT JOIN org_person_student_retention_view opsrv
        ON opsrv.person_id = opsrtgv.person_id
        AND opsrv.organization_id = opsrtgv.organization_id
        AND opsrv.year_id = pcypsv.year_id
        AND opsrtgv.retention_tracking_year <= opsrv.year_id
WHERE
    (RIGHT(pcypsv.year_id, 2) - RIGHT(opsrtgv.retention_tracking_year, 2)) >= 0;