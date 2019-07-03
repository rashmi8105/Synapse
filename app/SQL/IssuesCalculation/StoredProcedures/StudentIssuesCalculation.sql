DROP procedure IF EXISTS StudentIssuesCalculation;

DELIMITER $$
USE `synapse`$$
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE StudentIssuesCalculation(p_orgid SMALLINT UNSIGNED, p_facultyid INT UNSIGNED, p_orgacademicyearid INT UNSIGNED, p_surveyid SMALLINT UNSIGNED, p_cohortid SMALLINT UNSIGNED)
    DETERMINISTIC
    SQL SECURITY INVOKER
    BEGIN

        DROP TEMPORARY TABLE IF EXISTS student_permissionset;
        CREATE TEMPORARY TABLE student_permissionset AS
        SELECT DISTINCT student_id, permissionset_id
        FROM (
          SELECT
              DISTINCT
              student_id,
              permissionset_id
          FROM
            org_faculty_student_permission_map
          WHERE
            faculty_id = p_facultyid
            ) AS ofspm
        JOIN org_person_student_survey_link opssl ON opssl.person_id = ofspm.student_id
            AND opssl.deleted_at IS NULL
            AND opssl.survey_id = p_surveyid
            AND opssl.cohort = p_cohortid
        JOIN org_person_student_cohort opsc ON opsc.person_id = ofspm.student_id
            AND opsc.cohort = p_cohortid
            AND opsc.org_academic_year_id = p_orgacademicyearid
            AND opsc.deleted_at IS NULL
        WHERE
            (
              SELECT 1
              FROM wess_link wl
              WHERE wl.survey_id = p_surveyid
                  AND wl.cohort_code = p_cohortid
                  AND wl.status = 'closed'
                  AND wl.deleted_at IS NULL
              LIMIT 1
            );

        DROP TEMPORARY TABLE IF EXISTS synapse.student_issues;

        CREATE TEMPORARY TABLE synapse.student_issues (
          student_id INT NOT NULL,
          issue_id INT(11) NOT NULL,
          has_issue TINYINT(1) NULL,
          name VARCHAR(300) NULL,
          icon VARCHAR(300) NULL,
          PRIMARY KEY (student_id, issue_id));

        INSERT INTO synapse.student_issues(student_id, issue_id, has_issue, icon)
        SELECT
            pfc.person_id AS student_id,
            iss.id AS issue_id,
            MAX(pfc.mean_value BETWEEN iss.min AND iss.max) AS has_issue,
                iss.icon
        FROM
            student_permissionset
        JOIN person_factor_calculated pfc ON student_permissionset.student_id = pfc.person_id
            AND pfc.survey_id = p_surveyid
        JOIN issue iss ON iss.factor_id = pfc.factor_id
            AND iss.survey_id = p_surveyid
            AND iss.deleted_at IS NULL
        JOIN datablock_questions dq ON dq.factor_id = pfc.factor_id
            AND dq.survey_id = p_surveyid
            AND dq.deleted_at IS NULL
        JOIN org_permissionset_datablock opd ON opd.organization_id = p_orgid
            AND opd.org_permissionset_id = student_permissionset.permissionset_id
            AND dq.datablock_id = opd.datablock_id
            AND opd.deleted_at IS NULL
        GROUP BY
            pfc.person_id,
            iss.id;

        INSERT INTO synapse.student_issues(student_id, issue_id, has_issue, icon)
        SELECT
            sr.person_id AS student_id,
            iss.id AS issue_id,
            MAX(CAST(sr.decimal_value AS UNSIGNED) = eqo.option_value) AS has_issue,
            iss.icon
        FROM
            student_permissionset
        JOIN survey_response sr ON student_permissionset.student_id = sr.person_id
            AND sr.org_academic_year_id = p_orgacademicyearid
            AND sr.survey_id = p_surveyid
            AND sr.deleted_at IS NULL
            AND sr.decimal_value IS NOT NULL
        JOIN survey_questions sq ON sr.survey_questions_id = sq.id
            AND sq.survey_id = p_surveyid
            AND sq.deleted_at IS NULL
        JOIN issue iss ON iss.survey_questions_id = sr.survey_questions_id
            AND iss.survey_id = p_surveyid
            AND iss.deleted_at IS NULL
        JOIN ebi_question eq ON sq.ebi_question_id = eq.id
            AND eq.deleted_at IS NULL
        JOIN datablock_questions dq USE INDEX(PERMFUNC) ON dq.ebi_question_id = eq.id
            AND dq.deleted_at IS NULL
        JOIN org_permissionset_datablock opd ON opd.organization_id = p_orgid
            AND opd.org_permissionset_id = student_permissionset.permissionset_id
            AND dq.datablock_id = opd.datablock_id
            AND opd.deleted_at IS NULL
        LEFT JOIN issue_options issO ON iss.id = issO.issue_id
        LEFT JOIN ebi_question_options eqo ON eqo.id = issO.ebi_question_options_id
        GROUP BY
            sr.person_id,
            iss.id;
    END$$

DELIMITER ;
