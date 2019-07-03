<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-6109
 * Improving Performance of Risk Calculation by removing extra rounding
 * and working with the ART team to develop new models with non-inclusive
 * endpoints so there are no gaps between risk levels in risk variables
 * or risk indicators
 *
 * This script updates risk to use the new non-inclusive risk models
 */
class Version20160720190943 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("CREATE OR REPLACE
                            ALGORITHM=MERGE
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
                            #--Risk level matching:
                                #--For levels specified by ranges
                                LEFT JOIN risk_variable_range rvr
                                    ON rvr.risk_variable_id=RV.risk_variable_id
                                    AND RV.calculated_value >= rvr.min
                                    #--non-inclusive range below, so there are no gaps
                                    AND RV.calculated_value < rvr.max
                                    AND RV.variable_type='continuous'
                                #--For levels specified by categories
                                LEFT JOIN risk_variable_category rvc
                                    ON rvc.risk_variable_id=RV.risk_variable_id
                                    AND (RV.calculated_value=rvc.option_value OR CAST(RV.calculated_value AS SIGNED)=rvc.option_value)
                                    AND RV.variable_type='categorical'
                            WHERE
                                rvr.risk_variable_id IS NOT NULL
                                OR rvc.risk_variable_id IS NOT NULL;");

        $this->addSql("CREATE OR REPLACE
                            ALGORITHM=MERGE
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
                                RL.risk_text, RL.image_name, RL.color_hex
                            FROM person_riskmodel_calc_view AS PRC
                            LEFT JOIN risk_model_levels AS RML
                                ON RML.risk_model_id=PRC.risk_model_id
                                AND PRC.risk_score >= RML.min
                                #--non-inclusive range below, so there are no gaps
                                AND PRC.risk_score < RML.max
                            LEFT JOIN risk_level AS RL FORCE INDEX FOR JOIN (PRIMARY)
                                ON RL.id=RML.risk_level;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
