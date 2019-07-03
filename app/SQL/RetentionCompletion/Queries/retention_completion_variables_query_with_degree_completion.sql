SELECT
	external_id,
	firstname,
	lastname,
	primary_email,
	retention_tracking_year,
	retention_tracking_year_name,
	retained_to_midyear_year_1,
	retained_to_start_of_year_2,
	retained_to_midyear_year_2,
	retained_to_start_of_year_3,
	retained_to_midyear_year_3,
	retained_to_start_of_year_4,
	retained_to_midyear_year_4,
    completed_degree_in_1_year_or_less,
    CASE
        WHEN completed_degree_in_2_years_or_less IS NULL THEN NULL
		WHEN completed_degree_in_1_year_or_less = 1 THEN 1
        ELSE completed_degree_in_2_years_or_less
	END AS completed_degree_in_2_years_or_less,
    CASE
        WHEN completed_degree_in_3_years_or_less IS NULL THEN NULL
		WHEN completed_degree_in_1_year_or_less = 1 THEN 1
        WHEN completed_degree_in_2_years_or_less = 1 THEN 1
        ELSE completed_degree_in_3_years_or_less
	END AS completed_degree_in_3_years_or_less,
    CASE
        WHEN completed_degree_in_4_years_or_less IS NULL THEN NULL
		WHEN completed_degree_in_1_year_or_less = 1 THEN 1
        WHEN completed_degree_in_2_years_or_less = 1 THEN 1
        WHEN completed_degree_in_3_years_or_less = 1 THEN 1
        ELSE completed_degree_in_4_years_or_less
	END AS completed_degree_in_4_years_or_less,
    CASE
        WHEN completed_degree_in_5_years_or_less IS NULL THEN NULL
		WHEN completed_degree_in_1_year_or_less = 1 THEN 1
        WHEN completed_degree_in_2_years_or_less = 1 THEN 1
        WHEN completed_degree_in_3_years_or_less = 1 THEN 1
        WHEN completed_degree_in_4_years_or_less = 1 THEN 1
        ELSE completed_degree_in_5_years_or_less
	END AS completed_degree_in_5_years_or_less,
    CASE
        WHEN completed_degree_in_6_years_or_less IS NULL THEN NULL
		WHEN completed_degree_in_1_year_or_less = 1 THEN 1
        WHEN completed_degree_in_2_years_or_less = 1 THEN 1
        WHEN completed_degree_in_3_years_or_less = 1 THEN 1
        WHEN completed_degree_in_4_years_or_less = 1 THEN 1
        WHEN completed_degree_in_5_years_or_less = 1 THEN 1
        ELSE completed_degree_in_6_years_or_less
	END AS completed_degree_in_6_years_or_less
FROM
	(SELECT
		p.external_id,
		p.firstname,
		p.lastname,
		p.username AS primary_email,
		opsrcpv.retention_tracking_year,
		oay.name AS retention_tracking_year_name,
		MAX(opsrcpv.retained_to_midyear_year_1) AS retained_to_midyear_year_1,
		MAX(opsrcpv.retained_to_start_of_year_2) AS retained_to_start_of_year_2,
		MAX(opsrcpv.retained_to_midyear_year_2) AS retained_to_midyear_year_2,
		MAX(opsrcpv.retained_to_start_of_year_3) AS retained_to_start_of_year_3,
		MAX(opsrcpv.retained_to_midyear_year_3) AS retained_to_midyear_year_3,
		MAX(opsrcpv.retained_to_start_of_year_4) AS retained_to_start_of_year_4,
		MAX(opsrcpv.retained_to_midyear_year_4) AS retained_to_midyear_year_4,
		MAX(opsrcpv.completed_degree_in_1_year_or_less) AS completed_degree_in_1_year_or_less,
		MAX(opsrcpv.completed_degree_in_2_years_or_less) AS completed_degree_in_2_years_or_less,
		MAX(opsrcpv.completed_degree_in_3_years_or_less) AS completed_degree_in_3_years_or_less,
		MAX(opsrcpv.completed_degree_in_4_years_or_less) AS completed_degree_in_4_years_or_less,
		MAX(opsrcpv.completed_degree_in_5_years_or_less) AS completed_degree_in_5_years_or_less,
		MAX(opsrcpv.completed_degree_in_6_years_or_less) AS completed_degree_in_6_years_or_less
	FROM
		person p
			INNER JOIN
		org_person_student_retention_completion_pivot_view opsrcpv ON p.id = opsrcpv.person_id
			INNER JOIN
		org_academic_year oay ON oay.year_id = opsrcpv.retention_tracking_year
			AND oay.organization_id = p.organization_id
	WHERE
		p.organization_id = 59
	GROUP BY opsrcpv.organization_id, opsrcpv.person_id, opsrcpv.retention_tracking_year) as var_query
