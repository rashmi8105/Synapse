<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150923134042 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('CREATE OR REPLACE
    ALGORITHM=MERGE
    #--DEFINER=`synapsemaster`@`%`
    SQL SECURITY INVOKER
VIEW synapse.org_person_riskvariable AS 
#--EXPLAIN
    SELECT
        orgc.org_id,    
        rgph.person_id, 
        rv.id AS risk_variable_id,
               
        #-- Data at this intersection:
        rv.`source`,
        rv.variable_type,
        rv.calc_type,
        rgph.risk_group_id,
        rmm.id AS risk_model_id,
        rmw.weight,
        
        #-- Latter view data
        GREATEST(
            IFNULL(rgph.assignment_date,0), 
            IFNULL(orgc.modified_at,0), IFNULL(orgc.created_at,0),
            IFNULL(ps.modified_at,0), IFNULL(ps.created_at,0),
            IFNULL(orgm.modified_at,0), IFNULL(orgm.created_at,0),
            IFNULL(rmm.modified_at,0), IFNULL(rmm.created_at,0),
            #--IFNULL(rmw.modified_at,0), IFNULL(rmw.created_at,0),
            IFNULL(rv.modified_at,0), IFNULL(rv.created_at,0)
        ) AS modified_at
    FROM synapse.risk_group_person_history AS rgph 
    INNER JOIN synapse.org_riskval_calc_inputs orgc 
        on rgph.person_id=orgc.person_id
    INNER JOIN synapse.person AS ps
        on ps.id = rgph.person_id 
    INNER JOIN synapse.org_risk_group_model orgm 
        on rgph.risk_group_id=orgm.risk_group_id 
        AND orgm.org_id = ps.organization_id
    INNER JOIN synapse.risk_model_master rmm 
        on orgm.risk_model_id=rmm.id
        AND NOW() BETWEEN rmm.calculation_start_date AND rmm.calculation_end_date
    INNER JOIN synapse.risk_model_weights rmw
        on rmw.risk_model_id=rmm.id 
    INNER JOIN synapse.risk_variable rv
        ON rmw.risk_variable_id=rv.id
;');

    $this->addSQL('CREATE OR REPLACE
    ALGORITHM=MERGE
    #--DEFINER=`synapsemaster`@`127.0.0.1`
    SQL SECURITY INVOKER
VIEW synapse.org_person_riskvariable_datum AS 
    SELECT 
        rvintersect.org_id,
        rvintersect.person_id,
        rvintersect.risk_variable_id,
        #--rvintersect.source,
        #--rv.variable_type,
        #--rv.calc_type,
        COALESCE( #--The value will be sourced from (the related) one of the following:
            emd.metadata_value, #--EBI metadatum
            omd.metadata_value, #--ORG metadatum
            oqr.decimal_value, #--ORG survey response (decimal)
            oqr.char_value, #--ORG survey response (char)
            oqr.charmax_value, #--ORG survey response (char) - exception?
            svr.decimal_value, #--survey_response (decimal)
            svr.char_value, #--survey_response (char)
            svr.charmax_value, #--survey_response (char) - exception?
            pfc.mean_value      #--person_factor (pre-calculated decimal)
        ) AS source_value,
        COALESCE( #--The modified timestamp will be sourced from (the related) one of the following:
            emd.modified_at,
            omd.modified_at,
            oqr.modified_at,
            svr.modified_at,
            pfc.modified_at,
        #--also do the created ats so this doctrine invokes less disease on our calculation
            emd.created_at,
            omd.created_at,
            oqr.created_at,
            svr.created_at,
            pfc.created_at
        ) AS modified_at,
        COALESCE( #--The modified timestamp will be sourced from (the related) one of the following:
            emd.created_at,
            omd.created_at,
            oqr.created_at,
            svr.created_at,
            pfc.created_at
        ) AS created_at
    FROM synapse.org_person_riskvariable AS rvintersect
    LEFT JOIN synapse.risk_variable AS rv
        ON rv.id=rvintersect.risk_variable_id
    
    #--Risk variable value sources:
        #--Value sourced from an EBI profile metadatum
            LEFT JOIN synapse.person_ebi_metadata emd
                ON (emd.person_id,          emd.ebi_metadata_id)
                = (rvintersect.person_id,   rv.ebi_metadata_id)
                AND ( emd.created_at > rv.calculation_start_date  OR  rv.calculation_start_date IS NULL)
                AND ( emd.created_at < rv.calculation_end_date  OR  rv.calculation_end_date IS NULL )
            
        #--Value sourced from an ORG profile metadatum
            LEFT JOIN org_metadata AS omddef
                ON (omddef.organization_id,     omddef.id)
                = (rvintersect.org_id,          rv.org_metadata_id)
            LEFT JOIN synapse.person_org_metadata omd
                ON (omd.org_metadata_id,    omd.person_id)
                = (omddef.id,   rvintersect.person_id)
                    AND ( omd.created_at > rv.calculation_start_date  OR  rv.calculation_start_date IS NULL)
                    AND ( omd.created_at < rv.calculation_end_date  OR  rv.calculation_end_date IS NULL )
            
        #--Value sourced from a survey question (org_question_response)
            LEFT JOIN synapse.org_question oq 
                ON (oq.organization_id, oq.id) 
                = (rvintersect.org_id, rv.org_question_id)
            LEFT JOIN synapse.org_question_response oqr 
                ON (oqr.org_id,         oqr.person_id,          oqr.org_question_id)
                = (rvintersect.org_id,  rvintersect.person_id,  oq.id)
                    AND ( oqr.created_at > rv.calculation_start_date  OR  rv.calculation_start_date IS NULL)
                    AND ( oqr.created_at < rv.calculation_end_date  OR  rv.calculation_end_date IS NULL )
                
        #--Value sourced from a survey question (survey_response)
            LEFT JOIN synapse.survey_questions svq 
                ON svq.ebi_question_id=rv.ebi_question_id
            LEFT JOIN synapse.survey_response svr 
                ON (svr.org_id,         svr.person_id,          svr.survey_questions_id)
                = (rvintersect.org_id,  rvintersect.person_id,  rv.survey_questions_id)
                    AND ( svr.created_at > rv.calculation_start_date  OR  rv.calculation_start_date IS NULL)
                    AND ( svr.created_at < rv.calculation_end_date  OR  rv.calculation_end_date IS NULL )
                
        #--Value sourced from person_factors
            LEFT JOIN synapse.person_factor_calculated pfc 
                ON (pfc.organization_id,    pfc.person_id,          pfc.factor_id,  pfc.survey_id)
                = (rvintersect.org_id,      rvintersect.person_id,  rv.factor_id,   rv.survey_id)
                    AND ( pfc.created_at > rv.calculation_start_date  OR  rv.calculation_start_date IS NULL)
                    AND ( pfc.created_at < rv.calculation_end_date  OR  rv.calculation_end_date IS NULL )
;');

    $this->addSQL('CREATE OR REPLACE
    ALGORITHM=MERGE 
    #--DEFINER=`synapsemaster`@`%`
    SQL SECURITY INVOKER
VIEW cur_org_aggregationcalc_risk_variable AS 
        #--No calc (single value?)
            SELECT 
                OPRV.org_id,
                OPRV.risk_group_id,
                OPRV.person_id,
                OPRV.risk_variable_id,
                OPRV.risk_model_id,
                OPRV.source,
                OPRV.variable_type,
                OPRV.weight,
                risk_score_aggregated_RV(OPRV.org_id, OPRV.person_id, OPRV.risk_variable_id, OPRV.calc_type) COLLATE utf8_unicode_ci AS calculated_value,
                OPRV.calc_type AS calc_type #--We should normalize the underlying table so that rv.calc_type=NULL always implies ISCalculated=0 (and eliminate ISCalculated)
            FROM org_person_riskvariable AS OPRV
;');

    $this->addSQL("CREATE OR REPLACE
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
        #--(RV.weight*COALESCE(rvr.bucket_value, rvc.bucket_value)) AS calc_weight,
        COALESCE(rvr.bucket_value, rvc.bucket_value) AS bucket_value
    FROM cur_org_aggregationcalc_risk_variable AS RV
    #--Risk level matching:
        #--For levels specified by ranges
            LEFT JOIN risk_variable_range rvr
                ON rvr.risk_variable_id=RV.risk_variable_id
                AND cast(RV.calculated_value AS decimal(13,4)) BETWEEN rvr.min AND rvr.max
                AND RV.variable_type='continuous'
        #--For levels specified by categories
            LEFT JOIN risk_variable_category rvc
                ON rvc.risk_variable_id=RV.risk_variable_id
                AND (RV.calculated_value=rvc.option_value OR CAST(RV.calculated_value AS SIGNED)=rvc.option_value)
                AND RV.variable_type='categorical'
    WHERE rvr.risk_variable_id IS NOT NULL OR rvc.risk_variable_id IS NOT NULL
;");

    $this->addSQL('CREATE OR REPLACE
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
        RS_denominator(orgm.org_id, orgm.risk_group_id, rgph.person_id) AS RS_Denominator,
        (RS_numerator(orgm.org_id, orgm.risk_group_id, rgph.person_id)/RS_denominator(orgm.org_id, orgm.risk_group_id, rgph.person_id)) AS risk_score
    FROM risk_group_person_history AS rgph 
    INNER JOIN person AS ps
        on ps.id = rgph.person_id 
    INNER JOIN org_risk_group_model orgm 
        on rgph.risk_group_id=orgm.risk_group_id 
        AND orgm.org_id = ps.organization_id
    INNER JOIN risk_model_master rmm
        on orgm.risk_model_id=rmm.id
        AND NOW() BETWEEN rmm.calculation_start_date AND rmm.calculation_end_date
    WHERE risk_model_id IS NOT NULL
;');

    $this->addSQL('CREATE OR REPLACE
    ALGORITHM=MERGE
    #--DEFINER=`synapsemaster`@`%`
    SQL SECURITY INVOKER
VIEW person_risk_level_calc AS 
    SELECT
        PRC.org_id,
        PRC.person_id,
        PRC.risk_group_id,
        PRC.risk_model_id,
        PRC.RS_Numerator AS weighted_value,
        PRC.RS_Denominator AS maximum_weight_value,
        #--PRC.RS_Numerator/PRC.RS_Denominator AS risk_score,
        PRC.risk_score,
        RML.risk_level,
        RL.risk_text, RL.image_name, RL.color_hex
    FROM person_riskmodel_calc_view AS PRC
    LEFT JOIN risk_model_levels AS RML
        ON RML.risk_model_id=PRC.risk_model_id
        AND ROUND(PRC.risk_score,4) BETWEEN RML.min AND RML.max
    LEFT JOIN risk_level AS RL FORCE INDEX FOR JOIN (PRIMARY)
        ON RL.id=RML.risk_level
;');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
