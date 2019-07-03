<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150804145947 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $riskMaterializer = <<<HEREDOC

DROP PROCEDURE IF EXISTS org_RiskFactorCalculation;
CREATE PROCEDURE org_RiskFactorCalculation(limiter INT UNSIGNED)
DETERMINISTIC
SQL SECURITY INVOKER
BEGIN
    DECLARE the_ts TIMESTAMP;

    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

    SET the_ts=NOW(); 

#--Carve out a limited chunk to materialize
    UPDATE org_riskval_calc_inputs
    SET modified_at=the_ts, is_riskval_calc_required='n'
    WHERE is_riskval_calc_required='y'
    ORDER BY modified_at DESC
    LIMIT limiter;


#--Materialize the intermediate view
    INSERT IGNORE INTO org_calculated_risk_variables_history (person_id, risk_variable_id, risk_group_id, risk_model_id, created_at, org_id, calc_bucket_value, calc_weight, risk_source_value)
    SELECT 
        OCRV.person_id,
        OCRV.risk_variable_id,
        OCRV.risk_group_id,
        OCRV.risk_model_id,
        the_ts AS created_at,
        OCRV.org_id,
        bucket_value AS calc_bucket_value,
        calc_weight,
        calculated_value AS risk_source_value
    FROM org_calculated_risk_variables_view AS OCRV
    INNER JOIN (
        SELECT person_id FROM org_riskval_calc_inputs
        WHERE 
            is_riskval_calc_required='n'
            AND modified_at=the_ts
    ) AS stale 
        ON stale.person_id=OCRV.person_id
    ;

#--Materialize the risk score view
    INSERT IGNORE INTO person_risk_level_history(person_id,date_captured,risk_model_id,risk_level,risk_score,weighted_value,maximum_weight_value)
    SELECT 
        prlc.person_id,
        the_ts,
        prlc.risk_model_id,
        prlc.risk_level,
        prlc.risk_score,
        prlc.weighted_value,
        prlc.maximum_weight_value 
    FROM person_risk_level_calc AS prlc
    INNER JOIN (
        SELECT person_id FROM org_riskval_calc_inputs
        WHERE 
            is_riskval_calc_required='n'
            AND modified_at=the_ts
    ) AS stale 
        ON stale.person_id=prlc.person_id
    #--WHERE prlc.risk_score IS NOT NULL
    ;


#--Update the redundant person value for risk score
    #-- WE SHOULD JUST USE THE person_risk_level_history table...USING THE LATEST VALUE INSTEAD OF STORING IT AGAIN IN THE person TABLE
    UPDATE person P 
    INNER JOIN person_risk_level_history AS PRH 
        ON P.id=PRH.person_id
        AND PRH.date_captured=the_ts
    SET P.risk_level=PRH.risk_level 
    ;

/*
    #-- WE SHOULD JUST USE THE VIEWS AND HISTORY TABLE...DIRTY FLAGS ARE UNNECESSARY AND CAN GET OUT-OF-SYNC
    UPDATE org_riskval_calc_inputs orgc 
    INNER JOIN person_risk_level_history AS PRH
        ON PRH.person_id=orgc.person_id
        AND PRH.date_captured=the_ts
    SET is_riskval_calc_required='n', modified_at=the_ts;
*/

END;

HEREDOC;

$this->addSQL($riskMaterializer);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('DROP PROCEDURE IF EXISTS org_RiskFactorCalculation');


    }
}
