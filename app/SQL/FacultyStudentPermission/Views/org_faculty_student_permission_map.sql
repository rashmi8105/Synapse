# Access permissionset between facullty and student through group and course (Version20151203144626.php)
# Following change made in Version20160425144400.php
#1. add drop view statement
#2. replace group_id as faculty_group_id according the change of view: org_group_faculty_student_permission_map
# End for change in Version20160425144400.php

DROP VIEW IF EXISTS org_faculty_student_permission_map;
CREATE
            ALGORITHM = UNDEFINED
            DEFINER = synapsemaster@%
            SQL SECURITY DEFINER
        VIEW org_faculty_student_permission_map AS
            SELECT
                OPF.organization_id AS org_id,
                OPF.person_id AS faculty_id,
                COALESCE(OGM.student_id, OCM.student_id) AS student_id,
                OGM.faculty_group_id,
                OGM.student_group_id,
                OCM.course_id AS course_id,
                COALESCE(OGM.permissionset_id,
                    OCM.permissionset_id) AS permissionset_id
            FROM
            (((org_person_faculty OPF
                JOIN group_course_discriminator GCD)
                LEFT JOIN org_group_faculty_student_permission_map OGM ON ((((OGM.org_id , OGM.faculty_id) = (OPF.organization_id , OPF.person_id))
                    AND (GCD.association = 'group'))))
                LEFT JOIN org_course_faculty_student_permission_map OCM ON ((((OCM.org_id , OCM.faculty_id) = (OPF.organization_id , OPF.person_id))
                    AND (GCD.association = 'course'))))
            WHERE
            (((OGM.faculty_group_id IS NOT NULL)
                    OR (OCM.course_id IS NOT NULL))
                    AND ISNULL(OPF.deleted_at))
		;