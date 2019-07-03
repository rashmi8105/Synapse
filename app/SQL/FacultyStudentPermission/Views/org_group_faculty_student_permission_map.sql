# Access permissionset between facullty and student through group(Version20151203144626.php)
# Following change made in Version20160425144400.php
#1. add drop view statement
#2. add group hierarchy with closure table org_group_tree in
#3. replace group_id in the selection as faculty_group_id
#4. add student_group_id in the selection
# End for change in Version20160425144400.php

DROP VIEW IF EXISTS org_group_faculty_student_permission_map;
CREATE
            ALGORITHM = MERGE
            DEFINER = synapsemaster@%
            SQL SECURITY DEFINER
        VIEW org_group_faculty_student_permission_map AS
            SELECT
                OG.id AS faculty_group_id,
                OGS.org_group_id AS student_group_id,
                OG.organization_id AS org_id,
                OGF.person_id AS faculty_id,
                OGS.person_id AS student_id,
                OGF.org_permissionset_id AS permissionset_id
            FROM
                org_group_faculty OGF FORCE INDEX (PG_PERM)
                JOIN org_group OG ON (((OG.id = OGF.org_group_id)
                AND (OG.organization_id = OGF.organization_id)
                AND ISNULL(OG.deleted_at)))
                JOIN org_group_tree OGT ON (OGT.ancestor_group_id = OG.id
                AND ISNULL(OGT.deleted_at))
                JOIN org_group OG2 ON (OG2.id = OGT.descendant_group_id
                AND ISNULL(OG2.deleted_at))
                JOIN org_group_students OGS FORCE INDEX (GROUP-STUDENT) FORCE INDEX (STUDENT-GROUP) ON (OGS.org_group_id = OG2.id
                AND (OGS.organization_id = OG2.organization_id)
                AND ISNULL(OGS.deleted_at))
            WHERE
                ISNULL(OGF.deleted_at)"
		;