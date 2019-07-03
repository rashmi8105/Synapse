<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150701140621 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $drop_procedure_query = <<<CDATA
DROP PROCEDURE IF EXISTS RiskFactorCalculation;
CDATA;
        $this->addSql($drop_procedure_query);
        
        $calculation_query = <<<CDATA
Create Procedure RiskFactorCalculation()
Begin
INSERT INTO messages (message,created_at) values('2. --> CreateTemptables2',now()) ;
#Create Temporary Tables used for updations
call CreateTemptables();
INSERT INTO messages (message,created_at) values('3. <-- CreateTemptables2',now()) ;
select count(*) into @countorgid from org_riskval_calc_inputs;
INSERT INTO messages (message,created_at) values(concat('4. Count Got from org_riskval_calc_inputs = ' , convert(@countorgid,char(10))),now()) ;
if @countorgid is not NULL then
SELECT org_id into @cur_org_id FROM org_riskval_calc_inputs where is_riskval_calc_required = 'y' group by org_id order by org_id limit 1;
INSERT INTO messages (message,created_at) values(concat('5. Org_Id for which risk calc to be done = ', convert( @cur_org_id,char(10))), now()) ;
while (@cur_org_id IS NOT NULL) dO
#Inserts data into temporary table to update cur_org_calculated_risk_variable table
INSERT INTO messages (message,created_at) values(concat('6. --> cur_org_calculated_rv2 with org_id = ', convert( @cur_org_id,char(10))),now()) ;
CALL cur_org_calculated_rv(@cur_org_id);
INSERT INTO messages (message,created_at) values(concat('7. <-- cur_org_calculated_rv2 with org_id = ' , convert(@cur_org_id,char(10)), '; inserting data into riskfactorcalc table'), now()) ;
Insert into riskfactorcalc(person_id,risk_model_id,Numerator,Denominator,Risk_Score,risk_level,risk_text,image_name,color_hex)
(SELECT
               all_models.person_id,
				all_models.risk_model_id,
				all_models.Numerator,
				all_models.Denominator,
				((all_models.Numerator)/(all_models.Denominator))As Risk_Score,
				RML.risk_level,
                RL.risk_text, RL.image_name, RL.color_hex
				#,all_models.source
FROM
(
SELECT
					WRV.person_id,
					SUM(WRV.weight*WRV.bucket_value) AS Numerator,
					SUM(WRV.weight) AS Denominator,
					WRV.risk_model_id,
					WRV.risk_variable_id,
					WRV.source
                FROM cur_org_calculated_risk_variable
AS WRV
                GROUP BY person_id, risk_model_id
) AS all_models
LEFT JOIN risk_model_levels AS RML
                ON RML.risk_model_id=all_models.risk_model_id
    AND ((all_models.Numerator)/(all_models.Denominator)) BETWEEN RML.min AND RML.max
LEFT JOIN risk_level AS RL
                ON RL.id=RML.risk_level
#WHERE person_id=@person
ORDER BY risk_score DESC
LIMIT 50);-- #Get the 50 most at-risk students
        
INSERT INTO messages (message,created_at) values('8. Updating person_risk_level_history', now()) ;
        
/*
-> Update person_risk_level_history,person,org_calculated_risk_variable,org_calculated_risk_variables and org_riskval_calc_inputs tables
*/
SET @modified_dt := current_date;
# update table person_risk_level_history
Replace into person_risk_level_history(person_id,date_captured,risk_model_id,risk_level,risk_score,weighted_value,maximum_weight_value)
select person_id,@modified_dt,risk_model_id,risk_level,risk_score,numerator,denominator from riskfactorcalc;
        
#update table person
INSERT INTO messages (message,created_at) values('9. Updating person', now()) ;
SET SQL_SAFE_UPDATES = 0;
update person p inner join riskfactorcalc rfc on p.id=rfc.person_id  set p.risk_level=rfc.risk_level;
# update table orgriskval_calc_inputs
INSERT INTO messages (message,created_at) values('10. Updating org_riskval_calc_inputs', now()) ;
update org_riskval_calc_inputs orgc inner join riskfactorcalc rfc on orgc.person_id=rfc.person_id set is_riskval_calc_required='n', modified_at=@modified_dt;
# update table org_calculated_risk_variables
INSERT INTO messages (message,created_at) values('11. Updating org_calculated_risk_variables', now()) ;
insert into org_calculated_risk_variables
(select org_id,person_id,risk_variable_id,risk_model_id,bucket_value,(bucket_value*weight) as calc_weight,SourceValue, now() from cur_org_calculated_risk_variable);
#To fetch org_id
set @cur_org_id := null;
SELECT org_id into @cur_org_id FROM org_riskval_calc_inputs where is_riskval_calc_required = 'y' group by org_id order by org_id limit 1;
if @cur_org_id is not null then
INSERT INTO messages (message,created_at) values(concat('12. Next Org_Id for which risk calc to be done = ', convert( @cur_org_id,char(10))), now()) ;
else
INSERT INTO messages (message,created_at) values('12. NO MORE Org_Ids for risk calc, so quitting', now()) ;
end if;
truncate table cur_org_calculated_risk_variable;
INSERT INTO messages (message,created_at) values('13. truncating cur_org_calculated_risk_variable done', now()) ;
END WHILE;
#Drop Temporary Tables
INSERT INTO messages (message,created_at) values('14. Dropping temp tables', now()) ;
Drop table riskfactorcalc;
Drop table cur_org_calculated_risk_variable;
#else
#return 1;
end if;
end ;
CDATA;
        $this->addSql($calculation_query);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
    }
}
