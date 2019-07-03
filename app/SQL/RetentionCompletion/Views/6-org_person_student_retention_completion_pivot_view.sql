-- Pivots the data so retention completion variables are column names
CREATE OR REPLACE
            ALGORITHM = MERGE
            DEFINER = `synapsemaster`@`%`
            SQL SECURITY INVOKER
        VIEW `org_person_student_retention_completion_pivot_view` AS
            SELECT
                `opsrcnv`.`organization_id`,
                `opsrcnv`.`person_id`,
                `opsrcnv`.`retention_tracking_year`,
                (CASE
                    WHEN (`opsrcnv`.`variable` = 'retained_to_midyear_year_1') THEN `opsrcnv`.`is_enrolled_midyear`
                    ELSE NULL
                END) AS `retained_to_midyear_year_1`,
                (CASE
                    WHEN (`opsrcnv`.`variable` = 'retained_to_start_of_year_2') THEN `opsrcnv`.`is_enrolled_beginning_year`
                    ELSE NULL
                END) AS `retained_to_start_of_year_2`,
                (CASE
                    WHEN (`opsrcnv`.`variable` = 'retained_to_midyear_year_2') THEN `opsrcnv`.`is_enrolled_midyear`
                    ELSE NULL
                END) AS `retained_to_midyear_year_2`,
                (CASE
                    WHEN (`opsrcnv`.`variable` = 'retained_to_start_of_year_3') THEN `opsrcnv`.`is_enrolled_beginning_year`
                    ELSE NULL
                END) AS `retained_to_start_of_year_3`,
                (CASE
                    WHEN (`opsrcnv`.`variable` = 'retained_to_midyear_year_3') THEN `opsrcnv`.`is_enrolled_midyear`
                    ELSE NULL
                END) AS `retained_to_midyear_year_3`,
                (CASE
                    WHEN (`opsrcnv`.`variable` = 'retained_to_start_of_year_4') THEN `opsrcnv`.`is_enrolled_beginning_year`
                    ELSE NULL
                END) AS `retained_to_start_of_year_4`,
                (CASE
                    WHEN (`opsrcnv`.`variable` = 'retained_to_midyear_year_4') THEN `opsrcnv`.`is_enrolled_midyear`
                    ELSE NULL
                END) AS `retained_to_midyear_year_4`,
                (CASE
                    WHEN (`opsrcnv`.`variable` = 'completed_degree_in_1_year_or_less') THEN `opsrcnv`.`is_degree_completed`
                    ELSE NULL
                END) AS `completed_degree_in_1_year_or_less`,
                (CASE
                    WHEN (`opsrcnv`.`variable` = 'completed_degree_in_2_years_or_less') THEN `opsrcnv`.`is_degree_completed`
                    ELSE NULL
                END) AS `completed_degree_in_2_years_or_less`,
                (CASE
                    WHEN (`opsrcnv`.`variable` = 'completed_degree_in_3_years_or_less') THEN `opsrcnv`.`is_degree_completed`
                    ELSE NULL
                END) AS `completed_degree_in_3_years_or_less`,
                (CASE
                    WHEN (`opsrcnv`.`variable` = 'completed_degree_in_4_years_or_less') THEN `opsrcnv`.`is_degree_completed`
                    ELSE NULL
                END) AS `completed_degree_in_4_years_or_less`,
                (CASE
                    WHEN (`opsrcnv`.`variable` = 'completed_degree_in_5_years_or_less') THEN `opsrcnv`.`is_degree_completed`
                    ELSE NULL
                END) AS `completed_degree_in_5_years_or_less`,
                (CASE
                    WHEN (`opsrcnv`.`variable` = 'completed_degree_in_6_years_or_less') THEN `opsrcnv`.`is_degree_completed`
                    ELSE NULL
                END) AS `completed_degree_in_6_years_or_less`
            FROM
                `org_person_student_retention_completion_names_view` `opsrcnv`;