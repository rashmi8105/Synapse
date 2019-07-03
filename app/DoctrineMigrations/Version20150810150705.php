<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150810150705 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $updateView = <<<HEREDOC

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
            pfc.mean_value		#--person_factor (pre-calculated decimal)
		) AS source_value,
		COALESCE( #--The modified timestamp will be sourced from (the related) one of the following:
			emd.modified_at,
			omd.modified_at,
			oqr.modified_at,
			svr.modified_at,
            pfc.modified_at
		) AS modified_at,
		COALESCE( #--The modified timestamp will be sourced from (the related) one of the following:
			emd.created_at,
			omd.created_at,
			oqr.created_at,
			svr.created_at,
            pfc.created_at
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

		#--Value sourced from person_factors
			LEFT JOIN person_factor_calculated pfc 
				ON pfc.factor_id=rv.factor_id
                AND pfc.person_id=orgc.person_id
                AND pfc.organization_id=ps.organization_id


HEREDOC;

	$this->addSQL($updateView);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSQL('DROP VIEW IF EXISTS cur_org_rawdata_risk_variable');

    }
}
