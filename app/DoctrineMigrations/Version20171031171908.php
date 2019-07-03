<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-11589-ESPRJ-11403-ESPRJ-11645 Soft Deletion Changes to Views and Functions
 */
class Version20171031171908 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("DROP FUNCTION IF EXISTS `risk_score_aggregated_RV`");
        $this->addSql("CREATE DEFINER = `synapsemaster`@`%` FUNCTION `risk_score_aggregated_RV`(the_org_id INT, the_person_id INT, the_RV_id INT, agg_type VARCHAR(32), the_start_date DATETIME, the_end_date DATETIME) RETURNS varchar(255) CHARSET utf8
                                READS SQL DATA
                                DETERMINISTIC
                                SQL SECURITY INVOKER
                            BEGIN
                                    #--Optimization (use the last value generated if it matches parameters)
                                        IF(the_org_id = @cache_RSaggRV_org_id
                                            AND the_person_id = @cache_RSaggRV_person_id
                                            AND the_RV_id = @cache_RSaggRV_RV_id
                                            AND @cache_RSaggRV_ts = NOW(6) + 0)
                                        THEN
                                            RETURN @cache_RSaggRV_ret;
                                        END IF;
                            
                                        SET
                                            @cache_RSaggRV_org_id = the_org_id,
                                            @cache_RSaggRV_person_id = the_person_id,
                                            @cache_RSaggRV_RV_id = the_RV_id,
                                            @cache_RSaggRV_ts = NOW(6) + 0;
                            
                                    IF(agg_type IS NULL) THEN
                                        SET @cache_RSaggRV_ret=(
                                            SELECT
                                                RD.source_value AS calculated_value
                                            FROM
                                                org_person_riskvariable_datum AS RD
                                            WHERE
                                                RD.org_id = the_org_id
                                                AND RD.person_id = the_person_id
                                                AND RD.risk_variable_id = the_RV_id
                                            ORDER BY modified_at DESC, created_at DESC
                                            LIMIT 1
                                        );
                                    ELSEIF(agg_type='Sum') THEN
                                        SET @cache_RSaggRV_ret=(
                                            SELECT
                                                SUM(RD.source_value) AS calculated_value
                                            FROM
                                                org_person_riskvariable_datum AS RD
                                            WHERE
                                                RD.org_id = the_org_id
                                                AND RD.person_id = the_person_id
                                                AND RD.risk_variable_id = the_RV_id
                                            GROUP BY
                                                RD.person_id,
                                                RD.risk_variable_id
                                        );
                                    ELSEIF(agg_type='Count') THEN
                                        SET @cache_RSaggRV_ret=(
                                            SELECT
                                                COUNT(RD.source_value) AS calculated_value
                                            FROM
                                                org_person_riskvariable_datum AS RD
                                            WHERE
                                                RD.org_id = the_org_id
                                                AND RD.person_id = the_person_id
                                                AND RD.risk_variable_id = the_RV_id
                                            GROUP BY
                                                RD.person_id,
                                                RD.risk_variable_id
                                        );
                                    ELSEIF(agg_type='Average') THEN
                                        SET @cache_RSaggRV_ret=(
                                            SELECT
                                                AVG(RD.source_value) AS calculated_value
                                            FROM
                                                org_person_riskvariable_datum AS RD
                                            WHERE
                                                RD.org_id = the_org_id
                                                AND RD.person_id = the_person_id
                                                AND RD.risk_variable_id = the_RV_id
                                            GROUP BY
                                                RD.person_id,
                                                RD.risk_variable_id
                                        );
                                    ELSEIF(agg_type='Most Recent') THEN
                                        SET @cache_RSaggRV_ret= (
                                            SELECT
                                                step.source_value AS calculated_value
                                            FROM (
                                                SELECT
                                                    RD.source_value,
                                                    COALESCE(oat.end_date, oay.end_date) as end_date,
                                                    COALESCE(DATEDIFF(oat.end_date, oat.start_date), DATEDIFF(oay.end_date, oay.start_date)) as length,
                                                    RD.modified_at,
                                                    RD.created_at
                                                FROM
                                                    org_person_riskvariable_datum AS RD
                                                LEFT JOIN
                                                    org_academic_year oay ON oay.id = RD.org_academic_year_id
                                                        AND oay.deleted_at IS NULL
                                                LEFT JOIN
                                                    org_academic_terms oat ON oat.id = RD.org_academic_terms_id
                                                        AND oat.deleted_at IS NULL
                                                WHERE
                                                    RD.org_id = the_org_id
                                                    AND RD.person_id = the_person_id
                                                    AND RD.risk_variable_id = the_RV_id
                                                    AND (
                                                    (oay.id is null AND oat.id is null) OR
                                                    ((oat.end_date BETWEEN the_start_date AND the_end_date) AND RD.scope = 'T') OR
                                                    ((oay.end_date BETWEEN the_start_date AND the_end_date) AND RD.scope = 'Y')
                                                    )) as step
                                                ORDER BY step.end_date DESC, step.length DESC, step.modified_at DESC, step.created_at DESC
                                                LIMIT 1);
                                    ELSEIF(agg_type='Academic Update') THEN
                                        SET @cache_RSaggRV_ret=(
                                            #--TODO: resolve created_at vs. modified_at to audit/time-series dimensions
                                            SELECT
                                                COUNT(*) AS calculated_value
                                            FROM (
                                                SELECT
                                                    DISTINCT au.org_courses_id,
                                                    au.failure_risk_level,
                                                    au.grade
                                                FROM
                                                    academic_update AS au
                                                INNER JOIN (
                                                    SELECT
                                                        au_in.org_courses_id,
                                                        au_in.org_id,
                                                        au_in.person_id_student,
                                                        MAX(au_in.modified_at) as modified_at
                                                    FROM
                                                        academic_update AS au_in
                                                    INNER JOIN org_person_riskvariable AS RD
                                                        ON RD.org_id = au_in.org_id
                                                        AND RD.person_id = au_in.person_id_student
                                                    LEFT JOIN risk_variable AS RV
                                                        ON RV.id = RD.risk_variable_id
                                                        AND RV.deleted_at IS NULL
                                                    WHERE
                                                        RD.risk_variable_id = the_RV_id
                                                        AND au_in.org_id = the_org_id
                                                        AND au_in.person_id_student = the_person_id
                                                        AND au_in.deleted_at IS NULL
                                                        AND (au_in.failure_risk_level IS NOT NULL
                                                            OR au_in.grade IS NOT NULL)
                                                        AND au_in.modified_at BETWEEN RV.calculation_start_date and RV.calculation_end_date
                                                    GROUP BY
                                                        au_in.org_courses_id
                                                ) AS au_mid
                                                    ON au.org_courses_id = au_mid.org_courses_id
                                                    AND au.modified_at = au_mid.modified_at
                                                    AND au_mid.org_id = au.org_id
                                                    AND au_mid.person_id_student = au.person_id_student
                                            ) AS most_recent
                                            WHERE
                                                UPPER(failure_risk_level) = 'HIGH'
                                                OR UPPER(grade) IN ('D','F','F/No Pass')
                                        );
                            
                                    ELSE
                                        SET @cache_RSaggRV_ret = NULL;
                                    END IF;
                            
                                    RETURN @cache_RSaggRV_ret;
                                END");


        $this->addSql("CREATE OR REPLACE
                                ALGORITHM = MERGE
                                DEFINER = `synapsemaster`@`%`
                                SQL SECURITY INVOKER
                            VIEW synapse.org_person_riskvariable AS
                                SELECT
                                    ocfr.org_id,
                                    rgph.person_id,
                                    rv.id AS risk_variable_id,
                                    rv.source,
                                    rv.variable_type,
                                    rv.calc_type,
                                    rgph.risk_group_id,
                                    rv.calculation_end_date,
                                    rv.calculation_start_date,
                                    rmm.id AS risk_model_id,
                                    rmw.weight,
                                    GREATEST(IFNULL(rgph.assignment_date, 0),
                                            IFNULL(ocfr.modified_at, 0),
                                            IFNULL(ocfr.created_at, 0),
                                            IFNULL(orgm.modified_at, 0),
                                            IFNULL(orgm.created_at, 0),
                                            IFNULL(rmm.modified_at, 0),
                                            IFNULL(rmm.created_at, 0),
                                            IFNULL(rv.modified_at, 0),
                                            IFNULL(rv.created_at, 0)) AS modified_at
                                FROM
                                    synapse.risk_group_person_history rgph
                                INNER JOIN synapse.org_calc_flags_risk ocfr
                                    ON rgph.person_id = ocfr.person_id
                                INNER JOIN synapse.org_risk_group_model orgm
                                    on rgph.risk_group_id = orgm.risk_group_id
                                    AND orgm.org_id = ocfr.org_id
                                INNER JOIN synapse.risk_model_master rmm
                                    ON orgm.risk_model_id = rmm.id
                                    AND NOW() BETWEEN rmm.calculation_start_date AND rmm.calculation_end_date
                                INNER JOIN synapse.risk_model_weights rmw
                                    ON rmw.risk_model_id = rmm.id
                                INNER JOIN synapse.risk_variable rv
                                    ON rmw.risk_variable_id = rv.id
                                WHERE
                                    orgm.deleted_at IS NULL
                                    AND ocfr.deleted_at IS NULL
                                    AND rmm.deleted_at IS NULL
                                    AND rv.deleted_at IS NULL;");

        $this->addSql("CREATE OR REPLACE
                                ALGORITHM = MERGE
                                DEFINER = `synapsemaster`@`%`
                                SQL SECURITY INVOKER
                            VIEW synapse.org_person_riskvariable_datum AS
                                SELECT
                                    rvintersect.org_id,
                                    rvintersect.person_id,
                                    rvintersect.risk_variable_id,
                                    COALESCE( #--The value will be sourced from (the related) one of the following:
                                        pem.metadata_value, #--EBI metadatum
                                        pom.metadata_value, #--ORG metadatum
                                        oqr.decimal_value, #--ORG survey response (decimal)
                                        oqr.char_value, #--ORG survey response (char)
                                        oqr.charmax_value, #--ORG survey response (char) - exception?
                                        sr.decimal_value, #--survey_response (decimal)
                                        sr.char_value, #--survey_response (char)
                                        sr.charmax_value, #--survey_response (char) - exception?
                                        pfc.mean_value		#--person_factor (pre-calculated decimal)
                                    ) AS source_value,
                                    COALESCE( #--The modified timestamp will be sourced from (the related) one of the following:
                                        pem.modified_at,
                                        pom.modified_at,
                                        oqr.modified_at,
                                        sr.modified_at,
                                        pfc.modified_at,
                                        pem.created_at,
                                        pom.created_at,
                                        oqr.created_at,
                                        sr.created_at,
                                        pfc.created_at
                                    ) AS modified_at,
                                    COALESCE( #--The modified timestamp will be sourced from (the related) one of the following:
                                        pem.created_at,
                                        pom.created_at,
                                        oqr.created_at,
                                        sr.created_at,
                                        pfc.created_at
                                    ) AS created_at,
                                    em.scope AS scope,
                                    COALESCE(pem.org_academic_year_id,
                                             pom.org_academic_year_id) AS org_academic_year_id,
                                    COALESCE(pem.org_academic_terms_id,
                                             pom.org_academic_periods_id) AS org_academic_terms_id
                                FROM
                                    synapse.org_person_riskvariable AS rvintersect
                                    LEFT JOIN synapse.risk_variable AS rv
                                        ON rv.id = rvintersect.risk_variable_id
                                           AND rv.deleted_at IS NULL
                                    LEFT JOIN ebi_metadata em
                                        ON em.id = rv.ebi_metadata_id
                                           AND em.deleted_at IS NULL
                                    #--Value sourced from an EBI profile metadatum
                                    LEFT JOIN synapse.person_ebi_metadata pem
                                        ON pem.person_id = rvintersect.person_id
                                           AND pem.ebi_metadata_id = rv.ebi_metadata_id
                                           AND (pem.modified_at > rv.calculation_start_date
                                                OR rv.calculation_start_date IS NULL
                                                OR em.scope IN ('Y' , 'T'))
                                           AND (pem.modified_at < rv.calculation_end_date
                                                OR rv.calculation_end_date IS NULL
                                                OR em.scope IN ('Y' , 'T'))
                                           AND pem.deleted_at IS NULL
                                    #--Value sourced from an ORG profile metadatum
                                    LEFT JOIN org_metadata AS om
                                        ON om.organization_id = rvintersect.org_id
                                           AND om.id = rv.org_metadata_id
                                           AND om.deleted_at IS NULL
                                    LEFT JOIN synapse.person_org_metadata pom
                                        ON pom.org_metadata_id = om.id
                                           AND pom.person_id = rvintersect.person_id
                                           AND (pom.modified_at > rv.calculation_start_date
                                                OR rv.calculation_start_date IS NULL)
                                           AND (pom.modified_at < rv.calculation_end_date
                                                OR rv.calculation_end_date IS NULL)
                                           AND pom.deleted_at IS NULL
                                    #--Value sourced from a survey question (org_question_response)
                                    LEFT JOIN synapse.org_question oq
                                        ON oq.organization_id = rvintersect.org_id
                                           AND oq.id = rv.org_question_id
                                           AND oq.deleted_at IS NULL
                                    LEFT JOIN synapse.org_question_response oqr
                                        ON oqr.org_id = rvintersect.org_id
                                           AND oqr.person_id = rvintersect.person_id
                                           AND oqr.org_question_id = oq.id
                                           AND (oqr.modified_at > rv.calculation_start_date
                                                OR rv.calculation_start_date IS NULL)
                                           AND (oqr.modified_at < rv.calculation_end_date
                                                OR rv.calculation_end_date IS NULL)
                                           AND oqr.deleted_at IS NULL
                                    #--Value sourced from a survey question (survey_response)
                                    LEFT JOIN synapse.survey_questions sq
                                        ON sq.ebi_question_id = rv.ebi_question_id
                                           AND sq.deleted_at IS NULL
                                    LEFT JOIN synapse.survey_response sr
                                        ON sr.org_id = rvintersect.org_id
                                           AND sr.person_id = rvintersect.person_id
                                           AND sr.survey_questions_id = rv.survey_questions_id
                                           AND (sr.modified_at > rv.calculation_start_date
                                                OR rv.calculation_start_date IS NULL)
                                           AND (sr.modified_at < rv.calculation_end_date
                                                OR rv.calculation_end_date IS NULL)
                                           AND sr.deleted_at IS NULL
                                    #--Value sourced from person_factors
                                    LEFT JOIN synapse.person_factor_calculated pfc
                                        ON pfc.organization_id = rvintersect.org_id
                                           AND pfc.person_id = rvintersect.person_id
                                           AND pfc.factor_id = rv.factor_id
                                           AND pfc.survey_id = rv.survey_id
                                           AND (pfc.modified_at > rv.calculation_start_date
                                                OR rv.calculation_start_date IS NULL)
                                           AND (pfc.modified_at < rv.calculation_end_date
                                                OR  rv.calculation_end_date IS NULL)
                                           AND pfc.deleted_at IS NULL;");

        $this->addSql("CREATE OR REPLACE
                                ALGORITHM = MERGE
                                DEFINER = `synapsemaster`@`%`
                                SQL SECURITY INVOKER
                            VIEW cur_org_aggregationcalc_risk_variable AS
                                        SELECT
                                            OPRV.org_id,
                                            OPRV.risk_group_id,
                                            OPRV.person_id,
                                            OPRV.risk_variable_id,
                                            OPRV.risk_model_id,
                                            OPRV.source,
                                            OPRV.variable_type,
                                            OPRV.weight,
                                            risk_score_aggregated_RV(OPRV.org_id, 
                                                OPRV.person_id, 
                                                OPRV.risk_variable_id, 
                                                OPRV.calc_type,
                                                OPRV.calculation_start_date, 
                                                OPRV.calculation_end_date) COLLATE utf8_unicode_ci AS calculated_value,
                                            OPRV.calc_type AS calc_type
                                        FROM
                                            org_person_riskvariable AS OPRV;");

        $this->addSql("CREATE OR REPLACE
                                ALGORITHM = MERGE
                                DEFINER = `synapsemaster`@`%`
                                SQL SECURITY INVOKER
                            VIEW org_calculated_risk_variables AS
                                SELECT
                                    *
                                FROM
                                    org_calculated_risk_variables_history;");

        $this->addSql("CREATE OR REPLACE
                                ALGORITHM = MERGE
                                SQL SECURITY INVOKER
                            VIEW org_calculated_risk_variables_view AS
                                SELECT
                                    RV.org_id,
                                    RV.risk_group_id,
                                    RV.person_id,
                                    RV.risk_variable_id,
                                    RV.risk_model_id,
                                    RV.source,
                                    RV.variable_type,
                                    RV.weight,
                                    RV.calculated_value,
                                    COALESCE(rvr.bucket_value, rvc.bucket_value) AS bucket_value
                                FROM cur_org_aggregationcalc_risk_variable AS RV
                                    #--For levels specified by ranges
                                    LEFT JOIN risk_variable_range rvr
                                        ON rvr.risk_variable_id = RV.risk_variable_id
                                        AND RV.calculated_value >= rvr.min
                                        #--non-inclusive range below, so there are no gaps
                                        AND RV.calculated_value < rvr.max
                                        AND RV.variable_type = 'continuous'
                                        -- rvr has no deleted_at columns
                                    #--For levels specified by categories
                                    LEFT JOIN risk_variable_category rvc
                                        ON rvc.risk_variable_id = RV.risk_variable_id
                                        AND (RV.calculated_value = rvc.option_value OR CAST(RV.calculated_value AS SIGNED) = rvc.option_value)
                                        AND RV.variable_type = 'categorical'
                                        AND rvc.deleted_at IS NULL
                                WHERE
                                    rvr.risk_variable_id IS NOT NULL
                                    OR rvc.risk_variable_id IS NOT NULL;");

        $this->addSql("CREATE OR REPLACE
                                ALGORITHM = MERGE
                                DEFINER = `synapsemaster`@`%`
                                SQL SECURITY INVOKER
                            VIEW person_risk_level_calc AS
                                SELECT
                                    PRC.org_id,
                                    PRC.person_id,
                                    PRC.risk_group_id,
                                    PRC.risk_model_id,
                                    PRC.RS_Numerator AS weighted_value,
                                    PRC.RS_Denominator AS maximum_weight_value,
                                    PRC.risk_score,
                                    RML.risk_level,
                                    RL.risk_text,
                                    RL.image_name,
                                    RL.color_hex
                                FROM
                                    person_riskmodel_calc_view AS PRC
                                LEFT JOIN risk_model_levels AS RML
                                    ON RML.risk_model_id = PRC.risk_model_id
                                    AND PRC.risk_score >= RML.min
                                    #--non-inclusive range below, so there are no gaps
                                    AND PRC.risk_score < RML.max
                                    AND RML.deleted_at IS NULL
                                LEFT JOIN risk_level AS RL FORCE INDEX FOR JOIN (PRIMARY)
                                    ON RL.id = RML.risk_level
                                    AND RL.deleted_at IS NULL;");

        $this->addSql("CREATE OR REPLACE
                                ALGORITHM = MERGE
                                DEFINER = `synapsemaster`@`%`
                                SQL SECURITY INVOKER
                            VIEW person_riskmodel_calc_view AS
                                SELECT
                                    orgm.org_id,
                                    rgph.person_id,
                                    orgm.risk_group_id,
                                    orgm.risk_model_id,
                                    RS_numerator(orgm.org_id, orgm.risk_group_id, rgph.person_id) AS RS_Numerator,
                                    RS_denominator(orgm.org_id, orgm.risk_group_id, rgph.person_id) AS RS_Denominator,
                                    (RS_numerator(orgm.org_id, orgm.risk_group_id, rgph.person_id) / RS_denominator(orgm.org_id, orgm.risk_group_id, rgph.person_id)) AS risk_score
                                FROM
                                    risk_group_person_history AS rgph
                                INNER JOIN person AS p
                                    ON p.id = rgph.person_id
                                INNER JOIN org_risk_group_model orgm
                                    ON rgph.risk_group_id = orgm.risk_group_id
                                    AND orgm.org_id = p.organization_id
                                INNER JOIN risk_model_master rmm
                                    ON orgm.risk_model_id = rmm.id
                                    AND NOW() BETWEEN rmm.calculation_start_date AND rmm.calculation_end_date
                                WHERE
                                    risk_model_id IS NOT NULL
                                    AND orgm.deleted_at IS NULL
                                    AND p.deleted_at IS NULL
                                    AND rmm.deleted_at IS NULL;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
