<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150804144844 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $riskStoredProcedure = 
<<<CDATA
SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

CREATE OR REPLACE
    ALGORITHM=MERGE
    #--DEFINER=`synapsemaster`@`%`
    SQL SECURITY INVOKER
VIEW cur_org_rawdata_risk_variable AS 
    SELECT 
        orgm.org_id,
        #--rgph.id AS id,
        rgph.person_id,
        rgph.risk_group_id,
        rmm.id as risk_model_id,
        rv.id as risk_variable_id,
        #--orgc.id AS org_riskval_calc_input_id,
        #--orgm.id AS org_risk_group_model_id,
        rv.source,
        rv.variable_type,
        rmw.weight,
        COALESCE( #--The value will be sourced from (the related) one of the following:
            emd.metadata_value, #--EBI metadatum
            omd.metadata_value, #--ORG metadatum
            oqr.decimal_value, #--ORG survey response (decimal)
            oqr.char_value, #--ORG survey response (char)
            oqr.charmax_value, #--ORG survey response (char) - exception?
            svr.decimal_value, #--survey_response (decimal)
            svr.char_value, #--survey_response (char)
            svr.charmax_value, #--survey_response (char) - exception?
            fsvr.decimal_value #--survey_response via factor_questions (decimal)
        ) AS source_value,
        COALESCE( #--The modified timestamp will be sourced from (the related) one of the following:
            emd.modified_at,
            omd.modified_at,
            oqr.modified_at,
            svr.modified_at,
            fsvr.modified_at
        ) AS modified_at,
        COALESCE( #--The modified timestamp will be sourced from (the related) one of the following:
            emd.created_at,
            omd.created_at,
            oqr.created_at,
            svr.created_at,
            fsvr.created_at
        ) AS created_at,
        #--IF(rv.is_calculated=0, NULL, rv.calc_type) AS calc_type, #--We should normalize the underlying table so that rv.calc_type=NULL always implies ISCalculated=0 (and eliminate ISCalculated) 
        rv.calc_type,
        rv.calculation_start_date,
        rv.calculation_end_date
    FROM risk_group_person_history AS rgph 
    INNER JOIN org_riskval_calc_inputs orgc 
        on rgph.person_id=orgc.person_id
    INNER JOIN person AS ps
        on ps.id = rgph.person_id 
    INNER JOIN org_risk_group_model orgm 
        on rgph.risk_group_id=orgm.risk_group_id 
        AND orgm.org_id = ps.organization_id
    INNER JOIN risk_model_master rmm 
        on orgm.risk_model_id=rmm.id  
    INNER JOIN risk_model_weights rmw 
        on rmw.risk_model_id=rmm.id 
    INNER JOIN risk_variable rv
        ON rmw.risk_variable_id=rv.id
    
    #--Risk variable value sources:
        #--Value sourced from an EBI profile metadatum
            LEFT JOIN person_ebi_metadata emd
                ON emd.ebi_metadata_id=rv.ebi_metadata_id
                AND emd.person_id=rgph.person_id #--added this for optimization
            
        #--Value sourced from an ORG profile metadatum
            LEFT JOIN person_org_metadata omd
                ON omd.org_metadata_id=rv.org_metadata_id
                AND omd.person_id=rgph.person_id #--added this for optimization
            
        #--Value sourced from a survey question (org_question_response)
            LEFT JOIN org_question oq 
                ON oq.id=rv.org_question_id
            LEFT JOIN org_question_response oqr 
                on oqr.org_question_id=rv.org_question_id
                and oqr.person_id=orgc.person_id
                #--and oqr.survey_id=rv.survey_id #--I think this is not necessary and may slow the query...use PK postfix
                
        #--Value sourced from a survey question (survey_response)
            LEFT JOIN survey_questions svq 
                ON svq.ebi_question_id=rv.ebi_question_id
            LEFT JOIN survey_response svr 
                ON svr.survey_questions_id=rv.survey_questions_id 
                AND svr.person_id=orgc.person_id
                #--AND svr.survey_id=rv.survey_id #--I think this is not necessary and may slow the query...use PK postfix
    
        #--Value sourced from a factor question (survey_response via factor_questions)
            LEFT JOIN factor_questions fq 
                ON fq.id=rv.factor_id 
            LEFT JOIN survey_questions fsvq 
                ON fsvq.ebi_question_id=fq.ebi_question_id
            LEFT JOIN survey_response fsvr 
                ON fsvr.survey_questions_id=fq.survey_questions_id
                AND fsvr.person_id=orgc.person_id
                #--AND svr.survey_id=rv.survey_id  #--I think this is not necessary and may slow the query...use PK postfix
;
    
#--SELECT * FROM cur_org_rawdata_risk_variable ORDER BY person_id LIMIT 1000;

#--SELECT * FROM cur_org_rawdata_risk_variable WHERE person_id=265494 AND risk_variable_id=6;

CREATE OR REPLACE
    ALGORITHM=MERGE #--TECH DEBT: will have to optimize for temp table (criteria push-down trick) here
    #--DEFINER=`synapsemaster`@`%`
    SQL SECURITY INVOKER
VIEW cur_org_aggregationcalc_risk_variable AS 
        #--No calc (single value?)
            SELECT 
                orgm.org_id,
                orgm.risk_group_id,
                rgph.person_id,
                rv.id as risk_variable_id,
                rmm.id as risk_model_id,
                rv.source,
                rv.variable_type,
                rmw.weight,
                risk_score_aggregated_RV(rgph.person_id, rv.id, IF(rv.is_calculated=0, NULL, rv.calc_type)) COLLATE utf8_unicode_ci AS calculated_value,
                #--risk_score_aggregated_RVR(rgph.person_id, rv.id, IF(rv.is_calculated=0, NULL, rv.calc_type)) AS range_calculated_value,
                #--risk_score_aggregated_RVC(rgph.person_id, rv.id, IF(rv.is_calculated=0, NULL, rv.calc_type)) AS cat_calculated_value,
                rv.calc_type AS calc_type #--We should normalize the underlying table so that rv.calc_type=NULL always implies ISCalculated=0 (and eliminate ISCalculated)
            FROM risk_group_person_history AS rgph
            INNER JOIN person AS ps
                on ps.id = rgph.person_id
            INNER JOIN org_risk_group_model orgm
                on rgph.risk_group_id=orgm.risk_group_id
                AND orgm.org_id = ps.organization_id
            INNER JOIN risk_model_master rmm
                on orgm.risk_model_id=rmm.id
            INNER JOIN risk_model_weights rmw
                on rmw.risk_model_id=rmm.id
            INNER JOIN risk_variable rv
                ON rmw.risk_variable_id=rv.id
            #--WHERE person_id=265494 #--test rig
;

#--This view may perform poorly without the criteria push-down trick
CREATE OR REPLACE
    ALGORITHM=MERGE
    #--DEFINER=`synapsemaster`@`%`
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
        RV.calculated_value, #--use this where source_value was used
        (RV.weight*COALESCE(rvr.bucket_value, rvc.bucket_value)) AS calc_weight,
        COALESCE(rvr.bucket_value, rvc.bucket_value) AS bucket_value
    FROM cur_org_aggregationcalc_risk_variable AS RV
    #--Risk level matching:
        #--For levels specified by ranges
            LEFT JOIN risk_variable_range rvr
                ON rvr.risk_variable_id=RV.risk_variable_id
                AND ROUND(RV.calculated_value,4) BETWEEN rvr.min AND rvr.max
                AND RV.variable_type='continuous'
        #--For levels specified by categories
            LEFT JOIN risk_variable_category rvc
                ON rvc.risk_variable_id=RV.risk_variable_id
                AND (RV.calculated_value=rvc.option_value OR CAST(RV.calculated_value AS SIGNED)=rvc.option_value)
                AND RV.variable_type='categorical'
    WHERE rvr.risk_variable_id IS NOT NULL OR rvc.risk_variable_id IS NOT NULL
    #-- AND person_id=265494 #--Test rig
;

CREATE OR REPLACE
    ALGORITHM=MERGE
    #--DEFINER=`synapsemaster`@`%`
    SQL SECURITY INVOKER
VIEW org_calculated_risk_variables AS 
    SELECT * FROM org_calculated_risk_variables_history
;
#--SELECT * FROM org_calculated_risk_variables;

/*
#--This view may perform poorly without the criteria push-down trick
CREATE OR REPLACE
    ALGORITHM=MERGE
    #--DEFINER=`synapsemaster`@`%`
    SQL SECURITY INVOKER
VIEW person_riskmodel_calc AS 
    SELECT
        WRV.org_id,
        WRV.person_id,
        WRV.risk_variable_id,
        WRV.risk_model_id,
        WRV.source,
        SUM(calc_weight) AS RS_Numerator,
        SUM(WRV.weight) AS RS_Denominator
        #--SUM(WRV.weight*WRV.bucket_value)/SUM(WRV.weight) AS risk_score
    FROM org_calculated_risk_variables AS WRV
    GROUP BY person_id, risk_model_id
;
*/
CREATE OR REPLACE
    ALGORITHM=MERGE
    #--DEFINER=`synapsemaster`@`%`
    SQL SECURITY INVOKER
VIEW person_riskmodel_calc_view AS 
    SELECT
        #--orgm.id AS orgm, rgph.id AS rgph, #--debug
        orgm.org_id,
        rgph.person_id,
        orgm.risk_group_id,
        orgm.risk_model_id,
        RS_numerator(orgm.org_id, orgm.risk_group_id, rgph.person_id) AS RS_Numerator,
        RS_denominator(orgm.org_id, orgm.risk_group_id, rgph.person_id) AS RS_Denominator
        #--SUM(WRV.weight*WRV.bucket_value)/SUM(WRV.weight) AS risk_score
    FROM risk_group_person_history AS rgph 
    INNER JOIN person AS ps
        on ps.id = rgph.person_id 
    INNER JOIN org_risk_group_model orgm 
        on rgph.risk_group_id=orgm.risk_group_id 
        AND orgm.org_id = ps.organization_id
    WHERE risk_model_id IS NOT NULL
;

#--This view may perform poorly without the criteria push-down trick
CREATE OR REPLACE
    ALGORITHM=MERGE
    #--DEFINER=`synapsemaster`@`127.0.0.1`
    SQL SECURITY INVOKER
VIEW person_risk_level_calc AS 
    SELECT
        PRC.org_id,
        PRC.person_id,
        PRC.risk_group_id,
        PRC.risk_model_id,
        PRC.RS_Numerator AS weighted_value,
        PRC.RS_Denominator AS maximum_weight_value,
        PRC.RS_Numerator/PRC.RS_Denominator AS risk_score,
        #--PRC.risk_score
        RML.risk_level,
        RL.risk_text, RL.image_name, RL.color_hex
    FROM person_riskmodel_calc_view AS PRC
    LEFT JOIN risk_model_levels AS RML
        ON RML.risk_model_id=PRC.risk_model_id
        AND (PRC.RS_Numerator/PRC.RS_Denominator) BETWEEN RML.min AND RML.max
    LEFT JOIN risk_level AS RL FORCE INDEX FOR JOIN (PRIMARY)
        ON RL.id=RML.risk_level
;
SET @sess_iter=0, @cache_miss=0, @cache_numer_miss=0, @cache_denom_miss=0;
CDATA;

$this->addSQL($riskStoredProcedure);

}

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('DROP VIEW IF EXISTS cur_org_rawdata_risk_variable;');
        $this->addSQL('DROP VIEW IF EXISTS cur_org_aggregationcalc_risk_variable;');
        $this->addSQL('DROP VIEW IF EXISTS org_calculated_risk_variables_view;');

        $this->addSQL('DROP VIEW IF EXISTS org_calculated_risk_variables;');
        $this->addSQL('DROP VIEW IF EXISTS person_riskmodel_calc;');
        $this->addSQL('DROP VIEW IF EXISTS person_riskmodel_calc_view;');
        $this->addSQL('DROP VIEW IF EXISTS person_risk_level_calc');



    }
}
