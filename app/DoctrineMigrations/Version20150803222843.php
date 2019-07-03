<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150803222843 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('DROP FUNCTION IF EXISTS `risk_score_aggregated_RV`;');


        $aggFunctions = <<<CDATA

CREATE /* DEFINER=`synapsemaster`@`%` */ FUNCTION `risk_score_aggregated_RV`(the_person_id INT, the_RV_id INT, agg_type VARCHAR(32))
	RETURNS VARCHAR(255) CHARACTER SET UTF8
    READS SQL DATA
    DETERMINISTIC
	SQL SECURITY INVOKER
BEGIN
    SET @sess_iter=@sess_iter+1;

	#--Optimization (use the last value generated if it matches parameters)
		IF(the_person_id=@cache_RSaggRV_person_id AND the_RV_id=@cache_RSaggRV_RV_id) THEN
			RETURN @cache_RSaggRV_ret;
		END IF;
		SET @cache_RSaggRV_person_id=the_person_id, @cache_RSaggRV_RV_id=the_RV_id;
        #--SET @cache_miss=@cache_miss+1;

    IF(agg_type IS NULL) THEN
		SET @cache_RSaggRV_ret=(
			SELECT RD.source_value AS calculated_value
			FROM cur_org_rawdata_risk_variable AS RD
			WHERE
				RD.person_id=the_person_id
                AND RD.risk_variable_id=the_RV_id
			ORDER BY modified_at DESC, created_at DESC
            LIMIT 1
        );
    ELSEIF(agg_type="Sum") THEN
		SET @cache_RSaggRV_ret=(
			SELECT SUM(RD.source_value) AS calculated_value
			FROM cur_org_rawdata_risk_variable AS RD
			WHERE
				RD.person_id=the_person_id
                AND RD.risk_variable_id=the_RV_id
				AND RD.modified_at BETWEEN RD.calculation_start_date and RD.calculation_end_date
			GROUP BY RD.person_id, RD.risk_variable_id
            #LIMIT 1
        );
    ELSEIF(agg_type="Count") THEN
		SET @cache_RSaggRV_ret=(
			SELECT COUNT(RD.source_value) AS calculated_value
			FROM cur_org_rawdata_risk_variable AS RD
			WHERE
				RD.person_id=the_person_id
                AND RD.risk_variable_id=the_RV_id
				AND RD.modified_at BETWEEN RD.calculation_start_date and RD.calculation_end_date
			GROUP BY RD.person_id, RD.risk_variable_id
            #LIMIT 1
        );
    ELSEIF(agg_type="Average") THEN
		SET @cache_RSaggRV_ret=(
			SELECT AVG(RD.source_value) AS calculated_value
			FROM cur_org_rawdata_risk_variable AS RD
			WHERE
				RD.person_id=the_person_id
                AND RD.risk_variable_id=the_RV_id
				AND RD.modified_at BETWEEN RD.calculation_start_date and RD.calculation_end_date
			GROUP BY RD.person_id, RD.risk_variable_id
            #LIMIT 1
        );
    ELSEIF(agg_type="Most Recent") THEN
		SET @cache_RSaggRV_ret=(
			SELECT RD.source_value AS calculated_value
			FROM cur_org_rawdata_risk_variable AS RD
			WHERE
				RD.person_id=the_person_id
                AND RD.risk_variable_id=the_RV_id
			/*
				AND RD.modified_at = (
					SELECT MAX(inRD.modified_at) FROM cur_org_rawdata_risk_variable AS inRD
					WHERE inRD.person_id=RD.person_id AND inRD.risk_variable_id=RD.risk_variable_id
						AND RD.modified_at BETWEEN inRD.calculation_start_date and inRD.calculation_end_date
				)
			*/
				AND RD.modified_at BETWEEN RD.calculation_start_date and RD.calculation_end_date
            ORDER BY modified_at DESC, created_at DESC
            LIMIT 1
        );
    ELSEIF(agg_type="Academic Update") THEN
		SET @cache_RSaggRV_ret=(

            SELECT COUNT(j.org_course_id) AS calculated_value
			FROM
				(SELECT au.org_course_id, MAX(au.modified_at), au.failure_risk_level, au.grade
				FROM academic_updates AS au
				INNER JOIN
				cur_org_rawdata_risk_variable AS RD
                ON RD.person_id = au.person_id_student
			WHERE
				RD.person_id = the_person_id
				AND au.modified_at=(
				 SELECT MAX(au_in.modified_at)
				 FROM academic_updates AS au_in
				 WHERE au_in.person_id_student=au.person_id_student
				  AND au_in.modified_at BETWEEN RD.calculation_start_date and RD.calculation_end_date
				)
			GROUP BY org_course_id) as j
			WHERE (upper(j.failure_risk_level)='HIGH' OR upper(j.grade) IN ('D','D-','D+','F','F+','F-'))

	);


	ELSE
		SET @cache_RSaggRV_ret=NULL;
    END IF;

	RETURN @cache_RSaggRV_ret;
END
CDATA;
		$this->addSQL($aggFunctions);

		$this->addSQL('DROP FUNCTION IF EXISTS `RS_numerator`;');

		$numerator = <<<CDATA
CREATE /* DEFINER=`synapsemaster`@`%` */ FUNCTION `RS_numerator`(the_org_id INT, the_group_id INT, the_person_id INT)
	RETURNS DECIMAL(18,9)
    READS SQL DATA
    DETERMINISTIC
	SQL SECURITY INVOKER
BEGIN
	#--Optimization (use the last value generated if it matches parameters)
		IF(the_person_id=@cache_RSnumer_person_id AND the_org_id=@cache_RSnumer_org_id AND the_group_id=@cache_RSnumer_group_id) THEN
			RETURN @cache_RSnumer_ret;
		END IF;
		SET @cache_RSnumer_person_id=the_person_id, @cache_RSnumer_org_id=the_org_id, @cache_RSnumer_group_id=the_group_id;
        #--SET @cache_numer_miss=@cache_numer_miss+1;

	SET @cache_RSnumer_ret=(
		SELECT SUM(calc_weight)
        FROM org_calculated_risk_variables_view
        WHERE
			org_id=the_org_id
            AND risk_group_id=the_group_id
            AND person_id=the_person_id
	);
    RETURN @cache_RSnumer_ret;
END
CDATA;

		$this->addSQL($numerator);



		/*  DENOMINATOR */

        $this->addSQL('DROP FUNCTION IF EXISTS `RS_denominator`;');

		$denominator = <<<CDATA
CREATE /* DEFINER=`synapsemaster`@`%` */ FUNCTION `RS_denominator`(the_org_id INT, the_group_id INT, the_person_id INT)
	RETURNS DECIMAL(18,9)
    READS SQL DATA
    DETERMINISTIC 
	SQL SECURITY INVOKER
BEGIN  
	#--Optimization (use the last value generated if it matches parameters)
		IF(the_person_id=@cache_RSdenom_person_id AND the_org_id=@cache_RSdenom_org_id AND the_group_id=@cache_RSdenom_group_id) THEN
			RETURN @cache_RSdenom_ret;
		END IF;
		SET @cache_RSdenom_person_id=the_person_id, @cache_RSdenom_org_id=the_org_id, @cache_RSdenom_group_id=the_group_id;
        #--SET @cache_denom_miss=@cache_denom_miss+1;
        
	SET @cache_RSdenom_ret=(
		SELECT SUM(weight) 
        FROM org_calculated_risk_variables_view 
        WHERE 
			org_id=the_org_id
            AND risk_group_id=the_group_id
            AND person_id=the_person_id
	);
    
    RETURN @cache_RSdenom_ret;
END
CDATA;


$this->addSQL($denominator);


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('DROP FUNCTION IF EXISTS `risk_score_aggregated_RV`');
        $this->addSQL('DROP FUNCTION IF EXISTS `RS_numerator`');
        $this->addSQL('DROP FUNCTION IF EXISTS `RS_denominator`');
    }
}
