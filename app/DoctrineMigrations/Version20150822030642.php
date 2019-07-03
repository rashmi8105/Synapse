<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150822030642 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSQL('DROP PROCEDURE IF EXISTS `org_RiskFactorCalculation`;'); 
        
        $riskUpdate = "
            CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `org_RiskFactorCalculation`(limiter INT UNSIGNED)
    DETERMINISTIC
    SQL SECURITY INVOKER
    BEGIN
    DECLARE the_ts TIMESTAMP;

    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

    SET the_ts=NOW(); 


    UPDATE org_calc_flags_risk
    SET modified_at=the_ts, calculated_at=the_ts
    WHERE calculated_at IS NULL
    ORDER BY modified_at DESC
    LIMIT limiter;



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
        SELECT person_id FROM org_calc_flags_risk
        WHERE 
            calculated_at=the_ts
            #--AND modified_at=the_ts
    ) AS stale 
        ON stale.person_id=OCRV.person_id
    ;


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
        SELECT person_id FROM org_calc_flags_risk
        WHERE 
            calculated_at=the_ts
            #--AND modified_at=the_ts
    ) AS stale 
        ON stale.person_id=prlc.person_id
    
    ;

    
    UPDATE person P 
    INNER JOIN person_risk_level_history AS PRH 
        ON P.id=PRH.person_id
        AND PRH.date_captured=the_ts
    SET P.risk_level=PRH.risk_level;

    END";

    $this->addSQL($riskUpdate);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');


    }
}
