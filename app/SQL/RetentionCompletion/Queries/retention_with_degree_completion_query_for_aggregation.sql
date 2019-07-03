SELECT
                            opsrbtgv.organization_id,
                            opsrbtgv.person_id,
                            opsrbtgv.retention_tracking_year,
                            opsrbtgv.year_id,
                            opsrbtgv.year_name,
                            opsrbtgv.is_enrolled_beginning_year,
                            opsrbtgv.is_enrolled_midyear,
                            (CASE
                                WHEN opsrbtgv.is_degree_completed = 1 THEN 1
                                WHEN
                                    ((SELECT
                                            opsr1.year_id
                                        FROM
                                            org_person_student_retention_view opsr1
                                        WHERE
                                            opsr1.person_id = opsrbtgv.person_id
                                                AND opsr1.organization_id = opsrbtgv.organization_id
                                                AND opsr1.is_degree_completed = 1
                                                AND opsr1.year_id >= opsrbtgv.retention_tracking_year
                                        ORDER BY opsr1.year_id
                                        LIMIT 1) <= opsrbtgv.year_id)
                                THEN 1
                                ELSE 0
                            END) AS is_degree_completed,
                            opsrbtgv.years_from_retention_track AS years_from_retention_track
                        FROM
                            org_person_student_retention_by_tracking_group_view opsrbtgv
                        WHERE
                            retention_tracking_year = :retentionTrackingYear
                                AND organization_id = :organizationId
