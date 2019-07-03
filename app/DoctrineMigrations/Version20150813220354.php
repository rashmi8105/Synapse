<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150813220354 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //ROUNDING FIX WITH CAST()
        $roundingFix = "
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
    AND CAST(RV.calculated_value AS DECIMAL(13,4)) BETWEEN rvr.min AND rvr.max #--The cast ensures we use precision math to compare, and also round properly
    AND RV.variable_type='continuous'
  #--For levels specified by categories
   LEFT JOIN risk_variable_category rvc
    ON rvc.risk_variable_id=RV.risk_variable_id
    AND (RV.calculated_value=rvc.option_value OR CAST(RV.calculated_value AS SIGNED)=rvc.option_value)
    AND RV.variable_type='categorical'
 WHERE rvr.risk_variable_id IS NOT NULL OR rvc.risk_variable_id IS NOT NULL
    #-- AND person_id=265494 #--Test rig
;";

    $this->addSQL($roundingFix);

    $this->addSQL('DROP FUNCTION IF EXISTS `risk_score_aggregated_RV`;');

    $academicUpdateFix = <<<HEREDOC
    CREATE DEFINER=`synapsemaster`@`%` FUNCTION `risk_score_aggregated_RV`(the_person_id INT, the_RV_id INT, agg_type VARCHAR(32)) RETURNS varchar(255) CHARSET utf8
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

            SELECT COUNT(j.org_courses_id) AS calculated_value
      FROM
        (SELECT au.org_courses_id, MAX(au.modified_at), au.failure_risk_level, au.grade
        FROM academic_update AS au
        INNER JOIN
        cur_org_rawdata_risk_variable AS RD
                ON RD.person_id = au.person_id_student
      WHERE
        RD.person_id = the_person_id
        AND au.modified_at=(
         SELECT MAX(au_in.modified_at)
         FROM academic_update AS au_in
         WHERE au_in.person_id_student=au.person_id_student
          AND au_in.modified_at BETWEEN RD.calculation_start_date and RD.calculation_end_date
        )
      GROUP BY org_courses_id) as j
      WHERE (upper(j.failure_risk_level)='HIGH' OR upper(j.grade) IN ('D','D-','D+','F','F+','F-'))

  );


  ELSE
    SET @cache_RSaggRV_ret=NULL;
    END IF;

  RETURN @cache_RSaggRV_ret;
END
HEREDOC;


    $this->addSQL($academicUpdateFix);



    $this->addSQL('ALTER TABLE survey_questions
    ADD INDEX `idx_sequence` (sequence ASC);');



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
