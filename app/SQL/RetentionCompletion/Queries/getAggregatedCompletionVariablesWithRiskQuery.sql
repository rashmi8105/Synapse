SELECT
                    merged.years_from_retention_track,
                    CASE
                        WHEN merged.risk_level THEN rl.risk_text
                        ELSE 'gray'
                    END AS risk_level_text,
                    merged.risk_level,
                    SUM(merged.is_degree_completed) AS numerator_count,
                    COUNT(DISTINCT merged.person_id) AS denominator_count
                FROM
                    risk_level rl
                        LEFT JOIN
                    (SELECT
                        opsrwdcv.person_id,
                        NULL AS risk_level,
                        '' AS date_captured,
                        opsrwdcv.is_degree_completed,
                        opsrwdcv.years_from_retention_track
                    FROM
                        (SELECT 
                            opsrbtgv.organization_id,
                            opsrbtgv.person_id,
                            opsrbtgv.retention_tracking_year,
                            opsrbtgv.year_id,
                            opsrbtgv.year_name,
                            opsrbtgv.is_enrolled_beginning_year,
                            opsrbtgv.is_enrolled_midyear,
                            (CASE
                                WHEN (opsrbtgv.is_degree_completed = 1) THEN 1
                                WHEN
                                    ((SELECT 
                                            opsr1.year_id
                                        FROM
                                            org_person_student_retention_view opsr1
                                        WHERE
                                            ((opsr1.person_id = opsrbtgv.person_id)
                                                AND (opsr1.organization_id = opsrbtgv.organization_id)
                                                AND (opsr1.is_degree_completed = 1)
                                                AND (opsr1.year_id >= opsrbtgv.retention_tracking_year))
                                        ORDER BY opsr1.year_id
                                        LIMIT 1) <= opsrbtgv.year_id)
                                THEN
                                    1
                                ELSE 0
                            END) AS is_degree_completed,
                            opsrbtgv.years_from_retention_track AS years_from_retention_track
                        FROM
                            org_person_student_retention_by_tracking_group_view opsrbtgv
                        WHERE
                            retention_tracking_year = :retentionTrackingYear
                                AND organization_id = :organizationId) AS opsrwdcv
                            LEFT JOIN
                        person_risk_level_history prlh ON opsrwdcv.person_id = prlh.person_id
                            AND DATE(prlh.date_captured) BETWEEN :riskStartDate AND :riskEndDate
                    WHERE
                        opsrwdcv.organization_id = :organizationId
                            AND opsrwdcv.year_id <= :currentYearId
                            AND prlh.person_id IS NULL
                            AND opsrwdcv.retention_tracking_year = :retentionTrackingYear
                            AND opsrwdcv.person_id IN (:filteredStudents)
                    UNION ALL
                    SELECT
                        prlh.person_id,
                        prlh.risk_level,
                        prlh.date_captured,
                        opsrwdcv.is_degree_completed,
                        opsrwdcv.years_from_retention_track
                    FROM
                        (SELECT
                            person_id,
                            MAX(date_captured) AS date_captured
                        FROM
                            person_risk_level_history
                        WHERE
                            DATE(date_captured) BETWEEN :riskStartDate AND :riskEndDate
                                AND person_id IN (:filteredStudents)
                        GROUP BY person_id) AS person_risk_date
                            INNER JOIN
                        person_risk_level_history prlh ON prlh.person_id = person_risk_date.person_id
                            AND prlh.date_captured = person_risk_date.date_captured
                            INNER JOIN
                        (SELECT
                            opsrbtgv.organization_id,
                            opsrbtgv.person_id,
                            opsrbtgv.retention_tracking_year,
                            opsrbtgv.year_id,
                            opsrbtgv.year_name ,
                            opsrbtgv.is_enrolled_beginning_year,
                            opsrbtgv.is_enrolled_midyear,
                            (CASE
                                WHEN (opsrbtgv.is_degree_completed = 1) THEN 1
                                WHEN
                                    ((SELECT
                                            opsr1.year_id
                                        FROM
                                            org_person_student_retention_view opsr1
                                        WHERE
                                            ((opsr1.person_id = opsrbtgv.person_id)
                                                AND (opsr1.organization_id = opsrbtgv.organization_id)
                                                AND (opsr1.is_degree_completed = 1)
                                                AND (opsr1.year_id >= opsrbtgv.retention_tracking_year))
                                        ORDER BY opsr1.year_id
                                        LIMIT 1) <= opsrbtgv.year_id)
                                THEN
                                    1
                                ELSE 0
                            END) AS is_degree_completed,
                            opsrbtgv.years_from_retention_track AS years_from_retention_track
                        FROM
                            org_person_student_retention_by_tracking_group_view opsrbtgv
                        WHERE
                            retention_tracking_year = :retentionTrackingYear
                                AND organization_id = :organizationId) AS opsrwdcv ON prlh.person_id = opsrwdcv.person_id
                    WHERE
                        opsrwdcv.organization_id = :organizationId
                            AND opsrwdcv.year_id <= :currentYearId
                            AND opsrwdcv.retention_tracking_year = :retentionTrackingYear
                            AND opsrwdcv.person_id IN (:filteredStudents)) AS merged ON merged.risk_level = rl.id
                        OR (merged.risk_level IS NULL
                        AND rl.risk_text = 'gray')
                GROUP BY merged.years_from_retention_track , merged.risk_level
                HAVING denominator_count > 0
                ORDER BY merged.years_from_retention_track , merged.risk_level DESC