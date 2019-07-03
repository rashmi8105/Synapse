select
    SQL_CALC_FOUND_ROWS p.id,
    p.firstname,
    p.lastname,
    p.risk_level,
    p.external_id,
    p.username as email,
    itl.image_name as intent_imagename,
    itl.text as intent_text,
    rl.image_name as risk_imagename,
    rl.risk_text,
    p.intent_to_leave as intent_leave,
    al.student_count AS logged_activities,
    p.cohert,
    	( SELECT
        (CASE
            WHEN
                (activity_type = \'N\')
            THEN
                CONCAT(DATE_FORMAT(created_at, \'%m/%d/%y\'),
                        \' - \',
                        \'Note\')
            WHEN
                (activity_type = \'A\')
            THEN
                CONCAT(DATE_FORMAT(created_at, \'%m/%d/%y\'),
                        \' - \',
                        \'Appointment\')
            WHEN
                (activity_type = \'C\')
            THEN
                CONCAT(DATE_FORMAT(created_at, \'%m/%d/%y\'),
                        \' - \',
                        \'Contact\')
            WHEN
                (activity_type = \'E\')
            THEN
                CONCAT(DATE_FORMAT(created_at, \'%m/%d/%y\'),
                        \' - \',
                        \'Email\')
            WHEN
                (activity_type = \'R\')
            THEN
                CONCAT(DATE_FORMAT(created_at, \'%m/%d/%y\'),
                        \' - \',
                        \'Referral\')
            ELSE CONCAT(DATE_FORMAT(created_at, \'%m/%d/%y\'),
                    \' - \',
                    \'Login\')
        END) AS new
    FROM
        activity_log
    WHERE
        activity_log.person_id_student = p.id
        AND activity_log.deleted_at IS NULL
    ORDER BY activity_log.id DESC
    LIMIT 1
    	) AS last_activity ,
    ops.status,
    itl.color_hex as intent_color,
    rl.color_hex as risk_color,
    1 as risk_flag,
    unique_people_this_faculty_member_can_see_risk_for.intent_to_leave as intent_flag,
    elv.list_name as class_level,
    al.student_count AS logged_activities
    from person p
    LEFT JOIN org_person_student os on (os.person_id = p.id and os.organization_id = p.organization_id )
    join
    (
       select person_id, max(intent_to_leave) as intent_to_leave
       from
       (
          select
          ocs.person_id, flags.intent_to_leave
          from org_course_student ocs
          join
          (
             select
             ocf.org_courses_id, op.intent_to_leave
             from org_course_faculty ocf
             join org_courses oc on oc.id = ocf.org_courses_id and oc.deleted_at is null
             join org_academic_terms oat on oat.id = oc.org_academic_terms_id and oat.deleted_at is null
             join org_permissionset op on ocf.org_permissionset_id = op.id and op.deleted_at is null
             where ocf.person_id = $$personId$$
             and ocf.deleted_at is null
             and oat.end_date >= date(now())
             and op.risk_indicator = 1
             and op.accesslevel_ind_agg = 1
          )
          flags on flags.org_courses_id = ocs.org_courses_id and ocs.deleted_at is null
          union
          all
          select
          ogs.person_id, flags.intent_to_leave
          from org_group_students ogs
          join
          (
             select
             ogf.org_group_id, op.intent_to_leave
             from org_group_faculty ogf
             join org_permissionset op on ogf.org_permissionset_id = op.id and op.deleted_at is null
             where ogf.person_id = $$personId$$
             and ogf.deleted_at is null
             and op.risk_indicator = 1
             and op.accesslevel_ind_agg = 1
          )
          flags on flags.org_group_id = ogs.org_group_id and ogs.deleted_at is null
       )
       non_unique_people_this_faculty_member_can_see_risk_for
       group by person_id
    )
    unique_people_this_faculty_member_can_see_risk_for on p.id = unique_people_this_faculty_member_can_see_risk_for.person_id
    left join risk_level rl on p.risk_level = rl.id
    join org_person_student ops on ops.person_id = p.id and ops.deleted_at is null
    left join intent_to_leave itl on itl.id = p.intent_to_leave
    left join person_ebi_metadata pem on ( pem.person_id = p.id and pem.ebi_metadata_id = [EBI_METADATA_CLASSLEVEL_ID] )
    left join ebi_metadata_list_values elv on (elv.list_value = pem.metadata_value and elv.ebi_metadata_id = [EBI_METADATA_CLASSLEVEL_ID] )
    LEFT JOIN
        (SELECT
            person_id_student,
                organization_id,
                COUNT(id) AS student_count
        FROM
            activity_log
        WHERE
            activity_log.deleted_at IS NULL
        GROUP BY person_id_student) AS al ON al.organization_id = p.organization_id
            AND p.id = al.person_id_student
    where (p.risk_level in ($$riskLevel$$) or p.risk_level is null)
    and p.deleted_at is null AND (os.status is null or os.status = 1) AND os.organization_id =  $$orgId$$ and os.deleted_at is null
     group by p.id
    [ORDER_BY]
    [LIMIT]