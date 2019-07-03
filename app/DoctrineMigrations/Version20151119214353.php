<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151119214353 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        
        $this->addSQL('CREATE OR REPLACE
    ALGORITHM = MERGE 
    DEFINER = `synapsemaster`@`%` 
    SQL SECURITY INVOKER
    VIEW `org_person_riskvariable` AS
        SELECT 
            `orgc`.`org_id` AS `org_id`,
            `rgph`.`person_id` AS `person_id`,
            `rv`.`id` AS `risk_variable_id`,
            `rv`.`source` AS `source`,
            `rv`.`variable_type` AS `variable_type`,
            `rv`.`calc_type` AS `calc_type`,
            `rgph`.`risk_group_id` AS `risk_group_id`,
            `rv`.`calculation_end_date`,
            `rv`.`calculation_start_date`,
            `rmm`.`id` AS `risk_model_id`,
            `rmw`.`weight` AS `weight`,
            GREATEST(IFNULL(`rgph`.`assignment_date`, 0),
                    IFNULL(`orgc`.`modified_at`, 0),
                    IFNULL(`orgc`.`created_at`, 0),
                    IFNULL(`orgm`.`modified_at`, 0),
                    IFNULL(`orgm`.`created_at`, 0),
                    IFNULL(`rmm`.`modified_at`, 0),
                    IFNULL(`rmm`.`created_at`, 0),
                    IFNULL(`rv`.`modified_at`, 0),
                    IFNULL(`rv`.`created_at`, 0)) AS `modified_at`
        FROM
            (((((`risk_group_person_history` `rgph`
            JOIN `org_calc_flags_risk` `orgc` ON ((`rgph`.`person_id` = `orgc`.`person_id`)))
            JOIN `org_risk_group_model` `orgm` ON (((`rgph`.`risk_group_id` = `orgm`.`risk_group_id`)
                AND (`orgm`.`org_id` = `orgc`.`org_id`))))
            JOIN `risk_model_master` `rmm` ON (((`orgm`.`risk_model_id` = `rmm`.`id`)
                AND (NOW() BETWEEN `rmm`.`calculation_start_date` AND `rmm`.`calculation_end_date`))))
            JOIN `risk_model_weights` `rmw` ON ((`rmw`.`risk_model_id` = `rmm`.`id`)))
            JOIN `risk_variable` `rv` ON ((`rmw`.`risk_variable_id` = `rv`.`id`)));');

        $this->addSQL("CREATE OR REPLACE
    ALGORITHM = MERGE 
    DEFINER = `synapsemaster`@`%` 
    SQL SECURITY INVOKER
VIEW `org_person_riskvariable_datum` AS
    SELECT 
        `rvintersect`.`org_id` AS `org_id`,
        `rvintersect`.`person_id` AS `person_id`,
        `rvintersect`.`risk_variable_id` AS `risk_variable_id`,
        COALESCE(`emd`.`metadata_value`,
                `omd`.`metadata_value`,
                `oqr`.`decimal_value`,
                `oqr`.`char_value`,
                `oqr`.`charmax_value`,
                `svr`.`decimal_value`,
                `svr`.`char_value`,
                `svr`.`charmax_value`,
                `pfc`.`mean_value`) AS `source_value`,
        COALESCE(`emd`.`modified_at`,
                `omd`.`modified_at`,
                `oqr`.`modified_at`,
                `svr`.`modified_at`,
                `pfc`.`modified_at`,
                `emd`.`created_at`,
                `omd`.`created_at`,
                `oqr`.`created_at`,
                `svr`.`created_at`,
                `pfc`.`created_at`) AS `modified_at`,
        COALESCE(`emd`.`created_at`,
                `omd`.`created_at`,
                `oqr`.`created_at`,
                `svr`.`created_at`,
                `pfc`.`created_at`) AS `created_at`,
        `ebidef`.`scope` AS `scope`,
        COALESCE(`emd`.`org_academic_year_id`,
                `omd`.`org_academic_year_id`) AS `org_academic_year_id`,
        COALESCE(`emd`.`org_academic_terms_id`,
                `omd`.`org_academic_periods_id`) AS `org_academic_terms_id`
    FROM synapse.org_person_riskvariable AS rvintersect
    LEFT JOIN synapse.risk_variable AS rv
        ON rv.id=rvintersect.risk_variable_id
    LEFT JOIN `ebi_metadata` `ebidef` ON `ebidef`.`id` = `rv`.`ebi_metadata_id`
    #--Risk variable value sources:
        #--Value sourced from an EBI profile metadatum
            LEFT JOIN synapse.person_ebi_metadata emd
                ON (emd.person_id,          emd.ebi_metadata_id)
                = (rvintersect.person_id,   rv.ebi_metadata_id)
                AND ( emd.modified_at > rv.calculation_start_date  OR  rv.calculation_start_date IS NULL OR `ebidef`.`scope` IN ('Y' , 'T'))
                AND ( emd.modified_at < rv.calculation_end_date  OR  rv.calculation_end_date IS NULL OR `ebidef`.`scope` IN ('Y' , 'T') )
                
            
        #--Value sourced from an ORG profile metadatum
            LEFT JOIN org_metadata AS omddef
                ON (omddef.organization_id,     omddef.id)
                = (rvintersect.org_id,          rv.org_metadata_id)
            LEFT JOIN synapse.person_org_metadata omd
                ON (omd.org_metadata_id,    omd.person_id)
                = (omddef.id,   rvintersect.person_id)
                    AND ( omd.modified_at > rv.calculation_start_date  OR  rv.calculation_start_date IS NULL)
                    AND ( omd.modified_at < rv.calculation_end_date  OR  rv.calculation_end_date IS NULL )
            
        #--Value sourced from a survey question (org_question_response)
            LEFT JOIN synapse.org_question oq 
                ON (oq.organization_id, oq.id) 
                = (rvintersect.org_id, rv.org_question_id)
            LEFT JOIN synapse.org_question_response oqr 
                ON (oqr.org_id,         oqr.person_id,          oqr.org_question_id)
                = (rvintersect.org_id,  rvintersect.person_id,  oq.id)
                    AND ( oqr.modified_at > rv.calculation_start_date  OR  rv.calculation_start_date IS NULL)
                    AND ( oqr.modified_at < rv.calculation_end_date  OR  rv.calculation_end_date IS NULL )
                
        #--Value sourced from a survey question (survey_response)
            LEFT JOIN synapse.survey_questions svq 
                ON svq.ebi_question_id=rv.ebi_question_id
            LEFT JOIN synapse.survey_response svr 
                ON (svr.org_id,         svr.person_id,          svr.survey_questions_id)
                = (rvintersect.org_id,  rvintersect.person_id,  rv.survey_questions_id)
                    AND ( svr.modified_at > rv.calculation_start_date  OR  rv.calculation_start_date IS NULL)
                    AND ( svr.modified_at < rv.calculation_end_date  OR  rv.calculation_end_date IS NULL )
                
        #--Value sourced from person_factors
            LEFT JOIN synapse.person_factor_calculated pfc 
                ON (pfc.organization_id,    pfc.person_id,          pfc.factor_id,  pfc.survey_id)
                = (rvintersect.org_id,      rvintersect.person_id,  rv.factor_id,   rv.survey_id)
                    AND ( pfc.modified_at > rv.calculation_start_date  OR  rv.calculation_start_date IS NULL)
                    AND ( pfc.modified_at < rv.calculation_end_date  OR  rv.calculation_end_date IS NULL );

");

    $this->addSQL('DROP FUNCTION IF EXISTS `risk_score_aggregated_RV`;');

    $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` FUNCTION `risk_score_aggregated_RV`(the_org_id INT, the_person_id INT, the_RV_id INT, agg_type VARCHAR(32), the_start_date DATETIME, the_end_date DATETIME) RETURNS varchar(255) CHARSET utf8
    READS SQL DATA
    DETERMINISTIC
    SQL SECURITY INVOKER
    BEGIN  
        #--Optimization (use the last value generated if it matches parameters)
            IF(the_org_id=@cache_RSaggRV_org_id AND the_person_id=@cache_RSaggRV_person_id AND the_RV_id=@cache_RSaggRV_RV_id AND @cache_RSaggRV_ts=NOW(6)+0) THEN
                RETURN @cache_RSaggRV_ret;
            END IF;
            SET @cache_RSaggRV_org_id=the_org_id, @cache_RSaggRV_person_id=the_person_id, @cache_RSaggRV_RV_id=the_RV_id, @cache_RSaggRV_ts=NOW(6)+0;
            #--SET @cache_miss=@cache_miss+1;
        
        IF(agg_type IS NULL) THEN
            SET @cache_RSaggRV_ret=(
                SELECT RD.source_value AS calculated_value
                FROM org_person_riskvariable_datum AS RD
                WHERE
                    RD.org_id=the_org_id 
                    AND RD.person_id=the_person_id 
                    AND RD.risk_variable_id=the_RV_id
                ORDER BY modified_at DESC, created_at DESC
                LIMIT 1
            );
        ELSEIF(agg_type='Sum') THEN
            SET @cache_RSaggRV_ret=(
                SELECT SUM(RD.source_value) AS calculated_value
                FROM org_person_riskvariable_datum AS RD
                WHERE
                    RD.org_id=the_org_id 
                    AND RD.person_id=the_person_id 
                    AND RD.risk_variable_id=the_RV_id
                GROUP BY RD.person_id, RD.risk_variable_id 
                #LIMIT 1
            );
        ELSEIF(agg_type='Count') THEN
            SET @cache_RSaggRV_ret=(
                SELECT COUNT(RD.source_value) AS calculated_value
                FROM org_person_riskvariable_datum AS RD
                WHERE
                    RD.org_id=the_org_id 
                    AND RD.person_id=the_person_id 
                    AND RD.risk_variable_id=the_RV_id
                GROUP BY RD.person_id, RD.risk_variable_id 
                #LIMIT 1
            );
        ELSEIF(agg_type='Average') THEN
            SET @cache_RSaggRV_ret=(
                SELECT AVG(RD.source_value) AS calculated_value
                FROM org_person_riskvariable_datum AS RD
                WHERE
                    RD.org_id=the_org_id 
                    AND RD.person_id=the_person_id 
                    AND RD.risk_variable_id=the_RV_id
                GROUP BY RD.person_id, RD.risk_variable_id 
                #LIMIT 1
            );
        ELSEIF(agg_type='Most Recent') THEN
            
            SET @cache_RSaggRV_ret= (SELECT step.source_value AS calculated_value FROM (
                    SELECT RD.source_value, COALESCE(oat.end_date, oay.end_date) as end_date,
                    COALESCE(DATEDIFF(oat.end_date, oat.start_date), DATEDIFF(oay.end_date, oay.start_date)) as length, RD.modified_at, RD.created_at
                    FROM org_person_riskvariable_datum AS RD
                    LEFT JOIN org_academic_year oay ON oay.id = RD.org_academic_year_id
                    LEFT JOIN org_academic_terms oat ON oat.id = RD.org_academic_terms_id
                    WHERE
                        RD.org_id=the_org_id 
                        AND RD.person_id=the_person_id 
                        AND RD.risk_variable_id=the_RV_id
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
                SELECT COUNT(*) AS calculated_value
                FROM (
                    SELECT DISTINCT au.org_courses_id, au.failure_risk_level, au.grade
                    FROM academic_update AS au
                    INNER JOIN (
                        SELECT au_in.org_courses_id, au_in.org_id, au_in.person_id_student, max(au_in.modified_at) as modified_at
                        FROM academic_update AS au_in
                        INNER JOIN org_person_riskvariable AS RD
                            ON (RD.org_id,  RD.person_id) 
                            = (au_in.org_id, au_in.person_id_student)
                        LEFT JOIN risk_variable AS RV
                            ON RV.id=RD.risk_variable_id
                        WHERE 
                            RD.risk_variable_id = the_RV_id
                            AND (au_in.org_id,  au_in.person_id_student)
                                = (the_org_id,  the_person_id)
                            AND (au_in.failure_risk_level IS NOT NULL OR au_in.grade IS NOT NULL)
                            AND au_in.modified_at BETWEEN RV.calculation_start_date and RV.calculation_end_date
                        GROUP BY au_in.org_courses_id
                    ) AS au_mid
                        ON au.org_courses_id = au_mid.org_courses_id
                        AND au.modified_at = au_mid.modified_at
                        AND (au_mid.org_id, au_mid.person_id_student)
                            = (au.org_id,   au.person_id_student)
                ) AS most_recent
                WHERE upper(failure_risk_level)='HIGH' OR upper(grade) IN ('D','F','F/No Pass')
            );

        ELSE 
            SET @cache_RSaggRV_ret=NULL;
        END IF;
        
        RETURN @cache_RSaggRV_ret;
    END");

        $this->addSQL('CREATE OR REPLACE
        ALGORITHM = MERGE 
        DEFINER = `synapsemaster`@`%`
        SQL SECURITY INVOKER
        VIEW `cur_org_aggregationcalc_risk_variable` AS
            SELECT 
                `OPRV`.`org_id` AS `org_id`,
                `OPRV`.`risk_group_id` AS `risk_group_id`,
                `OPRV`.`person_id` AS `person_id`,
                `OPRV`.`risk_variable_id` AS `risk_variable_id`,
                `OPRV`.`risk_model_id` AS `risk_model_id`,
                `OPRV`.`source` AS `source`,
                `OPRV`.`variable_type` AS `variable_type`,
                `OPRV`.`weight` AS `weight`,
                (RISK_SCORE_AGGREGATED_RV(`OPRV`.`org_id`,
                        `OPRV`.`person_id`,
                        `OPRV`.`risk_variable_id`,
                        `OPRV`.`calc_type`, `OPRV`.`calculation_start_date`, `OPRV`.`calculation_end_date`) COLLATE utf8_unicode_ci) AS `calculated_value`,
                `OPRV`.`calc_type` AS `calc_type`
            FROM
                `org_person_riskvariable` `OPRV`;');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
