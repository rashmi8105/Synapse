<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150922190009 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('DROP FUNCTION IF EXISTS `RS_denominator`;');
        $this->addSQL('DROP FUNCTION IF EXISTS `RS_numerator`;');
        $this->addSQL('DROP FUNCTION IF EXISTS `RS_setcache`;');
        $this->addSQL('DROP FUNCTION IF EXISTS `risk_score_aggregated_RV`;');
    	$this->addSQL('DROP VIEW IF EXISTS `cur_org_rawdata_risk_variable`;');

    	$this->addSQL("
CREATE /* DEFINER=`synapsemaster`@`%` */ FUNCTION `risk_score_aggregated_RV`(the_org_id INT, the_person_id INT, the_RV_id INT, agg_type VARCHAR(32))
	RETURNS VARCHAR(255) CHARACTER SET UTF8
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
		SET @cache_RSaggRV_ret=(
			SELECT RD.source_value AS calculated_value
			FROM org_person_riskvariable_datum AS RD
			WHERE
				RD.org_id=the_org_id 
				AND RD.person_id=the_person_id 
                AND RD.risk_variable_id=the_RV_id
			/*
				AND RD.modified_at = (
					SELECT MAX(inRD.modified_at) FROM cur_org_rawdata_risk_variable AS inRD 
					WHERE inRD.person_id=RD.person_id AND inRD.risk_variable_id=RD.risk_variable_id 
						AND RD.modified_at BETWEEN inRD.calculation_start_date and inRD.calculation_end_date 
				)
			*/
            ORDER BY modified_at DESC, created_at DESC
            LIMIT 1
        );      
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
						ON (RD.org_id,	RD.person_id) 
                        = (au_in.org_id, au_in.person_id_student)
					LEFT JOIN risk_variable AS RV
						ON RV.id=RD.risk_variable_id
					WHERE 
						RD.risk_variable_id = the_RV_id
						AND (au_in.org_id,	au_in.person_id_student)
							= (the_org_id,	the_person_id)
						AND (au_in.failure_risk_level IS NOT NULL OR au_in.grade IS NOT NULL)
						AND au_in.modified_at BETWEEN RV.calculation_start_date and RV.calculation_end_date
					GROUP BY au_in.org_courses_id
				) AS au_mid
					ON au.org_courses_id = au_mid.org_courses_id
					AND au.modified_at = au_mid.modified_at
					AND (au_mid.org_id,	au_mid.person_id_student)
						= (au.org_id,	au.person_id_student)
			) AS most_recent
			WHERE upper(failure_risk_level)='HIGH' OR upper(grade) IN ('D','D-','D+','F','F+','F-')
		);
/*
			SELECT COUNT(j.org_courses_id) AS calculated_value
			FROM (
				SELECT au.org_courses_id, au.failure_risk_level, au.grade
				FROM academic_update AS au
				INNER JOIN cur_org_rawdata_risk_variable AS RD
					ON RD.person_id = au.person_id_student
				WHERE
					RD.org_id = the_org_id
                    AND RD.person_id = the_person_id
					AND au.created_at=(
						SELECT MAX(au_in.created_at)
						FROM academic_update AS au_in
						WHERE 
							au_in.org_id=org_id=au.org_id
							AND au_in.person_id_student=au.person_id_student
							AND au_in.created_at BETWEEN RD.calculation_start_date and RD.calculation_end_date
			)
			GROUP BY org_courses_id) as j
			WHERE (upper(j.failure_risk_level)='HIGH' OR upper(j.grade) IN ('D','D-','D+','F','F+','F-'))
        );    
*/
	ELSE 
		SET @cache_RSaggRV_ret=NULL;
    END IF;
    
	RETURN @cache_RSaggRV_ret;
END

"

);



$this->addSQL('CREATE /* DEFINER=`synapsemaster`@`%` */ FUNCTION `RS_setcache`(the_org_id INT, the_group_id INT, the_person_id INT)
	RETURNS BOOLEAN
    READS SQL DATA
    DETERMINISTIC 
	SQL SECURITY INVOKER
BEGIN  

	#--Optimization (use the last value generated if it matches parameters)
		IF(the_person_id=@cache_RS_person_id AND the_org_id=@cache_RS_org_id AND the_group_id=@cache_RS_group_id AND @cache_RS_ts=NOW(6)+0) THEN
			RETURN TRUE;
		END IF;
		SET @cache_RS_person_id=the_person_id, @cache_RS_org_id=the_org_id, @cache_RS_group_id=the_group_id, @cache_RS_ts=NOW(6)+0;

		SELECT SUM(bucket_value*weight),  SUM(weight)
        INTO @cache_RSnumer_ret, @cache_RSdenom_ret
        FROM org_calculated_risk_variables_view 
        WHERE 
			org_id=the_org_id
            AND risk_group_id=the_group_id
            AND person_id=the_person_id
		;
	RETURN FALSE;
END
');

$this->addSQL('CREATE /* DEFINER=`synapsemaster`@`%` */ FUNCTION `RS_numerator`(the_org_id INT, the_group_id INT, the_person_id INT)
	RETURNS DECIMAL(18,9)
    READS SQL DATA
    DETERMINISTIC 
	SQL SECURITY INVOKER
BEGIN  
	DECLARE RShitormiss BOOL;
	#--Optimization (use the last value generated if it matches parameters)
		SET RShitormiss=RS_setcache(the_org_id, the_group_id, the_person_id);
	
		RETURN @cache_RSnumer_ret;
END
'

);

$this->addSQL('CREATE /* DEFINER=`synapsemaster`@`%` */ FUNCTION `RS_denominator`(the_org_id INT, the_group_id INT, the_person_id INT)
	RETURNS DECIMAL(18,9)
    READS SQL DATA
    DETERMINISTIC 
	SQL SECURITY INVOKER
BEGIN  
	DECLARE RShitormiss BOOL;
	#--Optimization (use the last value generated if it matches parameters)
		SET RShitormiss=RS_setcache(the_org_id, the_group_id, the_person_id);
    
		RETURN @cache_RSdenom_ret;
END');


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
