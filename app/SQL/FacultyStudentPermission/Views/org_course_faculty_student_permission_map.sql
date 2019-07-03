# Access permissionset between facullty and student through group and course (Version20151203144626.php)

CREATE OR REPLACE
		ALGORITHM = MERGE
		DEFINER = `synapsemaster`@`%`
		SQL SECURITY DEFINER
		VIEW org_course_faculty_student_permission_map AS
		SELECT #--STRAIGHT_JOIN
			OC.id AS course_id,
	        OC.organization_id AS org_id,
	        OCF.person_id as faculty_id,
	        OCS.person_id AS student_id,
	        OCF.org_permissionset_id AS permissionset_id
		FROM org_course_faculty AS OCF USE INDEX (`person-course`)
	    INNER JOIN org_courses AS OC #--USE INDEX (PRIMARY)
			ON OC.id = OCF.org_courses_id
			AND OC.organization_id = OCF.organization_id
			AND OC.deleted_at IS NULL
		INNER JOIN org_academic_terms AS OAT FORCE INDEX (last_term, PRIMARY)
			ON OAT.id = OC.org_academic_terms_id
			AND OAT.organization_id = OC.organization_id
			AND OAT.end_date >= DATE(now())
			AND OAT.start_date <= DATE(now())
			AND OAT.deleted_at IS NULL
		INNER JOIN org_course_student AS OCS FORCE INDEX (`course-person`, `person-course`)
			ON OCS.org_courses_id = OC.id
			AND OCS.organization_id = OC.organization_id
			AND OCS.deleted_at IS NULL
		WHERE OCF.deleted_at IS NULL
		;