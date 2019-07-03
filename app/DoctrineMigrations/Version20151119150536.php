<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151119150536 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        
        // this up() migration is auto-generated, please modify it to your needs
        // 
        $this->addSQL('DROP procedure IF EXISTS `org_RiskFactorCalculation`');
        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `org_RiskFactorCalculation`(deadline TIMESTAMP, chunksize SMALLINT UNSIGNED)
    DETERMINISTIC
    SQL SECURITY INVOKER
	BEGIN
            DECLARE the_ts TIMESTAMP;
            #--DECLARE chunksize INT UNSIGNED DEFAULT 25;
            
            #--Fix source data timestamps
            CALL fix_datum_src_ts(); 

            #--Sacrifice some temporal precision for reduced resource contention
            SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
            
            WHILE(
                NOW() < deadline
                AND (SELECT 1 FROM org_calc_flags_risk WHERE calculated_at IS NULL LIMIT 1) > 0
            ) DO
                SET the_ts=NOW(); 

            #--Carve out a limited chunk to materialize
                UPDATE org_calc_flags_risk
                SET calculated_at=the_ts,
                modified_at = the_ts
                WHERE calculated_at IS NULL
                ORDER BY modified_at ASC
                LIMIT chunksize;


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
                    bucket_value*weight AS calc_weight,
                    calculated_value AS risk_source_value
                FROM org_calculated_risk_variables_view AS OCRV
                INNER JOIN (
                    SELECT person_id FROM org_calc_flags_risk
                    WHERE 
                        calculated_at=the_ts
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
                    SELECT person_id FROM org_calc_flags_risk
                    WHERE 
                        calculated_at=the_ts
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
                SET 
                    P.risk_level=PRH.risk_level,    
                    P.risk_update_date=the_ts
                where not PRH.risk_score <=> (select risk_score from person_risk_level_history pr where pr.person_id = P.id ORDER BY date_captured DESC LIMIT 1, 1) ;
                
            
            #--Set magic dates for blank scores (lame)
                UPDATE org_calc_flags_risk AS OCFR
                LEFT JOIN person_risk_level_history AS PRLH
                    ON PRLH.person_id=OCFR.person_id
                    AND PRLH.date_captured=the_ts
                SET OCFR.calculated_at='1900-01-01 00:00:00'
                WHERE OCFR.calculated_at=the_ts AND PRLH.weighted_value IS NULL
                ;                
            END WHILE;


        END");
      
        
        

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
