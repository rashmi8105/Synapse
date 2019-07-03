-- Links rows with retention completion variable names by years from retention track
CREATE OR REPLACE
    ALGORITHM = MERGE
    DEFINER = `synapsemaster`@`%`
    SQL SECURITY INVOKER
VIEW `org_person_student_retention_completion_names_view` AS
SELECT
    opsrwbtgv.organization_id,
    opsrwbtgv.person_id,
    opsrwbtgv.retention_tracking_year,
    opsrwbtgv.year_id,
    opsrwbtgv.year_name,
    opsrwbtgv.is_enrolled_beginning_year,
    opsrwbtgv.is_enrolled_midyear,
	opsrwbtgv.is_degree_completed,
    opsrwbtgv.years_from_retention_track,
	rcvn.name_text,
	rcvn.variable,
	rcvn.sequence
FROM
    org_person_student_retention_by_tracking_group_view opsrwbtgv
    INNER JOIN retention_completion_variable_name rcvn
        ON opsrwbtgv.years_from_retention_track = rcvn.years_from_retention_track;