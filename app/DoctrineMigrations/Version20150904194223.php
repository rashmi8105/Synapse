<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150904194223 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //Talking Points View
        $this->addSQL("   
            CREATE OR REPLACE
    
                SQL SECURITY INVOKER
            VIEW synapse.person_talking_points_calculated AS 

            select orc.org_id,orc.person_id,tp.id as talking_points_id,svr.survey_id,tp.talking_points_type as response, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() 
            from  talking_points tp inner join 
            survey_questions svq on tp.ebi_question_id=svq.ebi_question_id
            inner join survey_response svr on svq.id=svr.survey_questions_id
            and (case when svr.response_type='decimal' then svr.decimal_value end) between tp.min_range and tp.max_range
            inner join org_calc_flags_talking_point orc 
            on svr.person_id=orc.person_id and svr.org_id=orc.org_id and orc.calculated_at IS NULL
            inner join org_person_student ops on orc.person_id=ops.person_id
            inner join org_person_student_survey_link opssl  on ops.surveycohort=opssl.cohort and opssl.survey_id=svr.survey_id and opssl.person_id=svr.person_id

            union 
            select 
            orc.org_id,orc.person_id,tp.id as talking_points_id,null as survey_id,tp.talking_points_type as response, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP()
            from  talking_points tp inner join 
            person_ebi_metadata pem on tp.ebi_metadata_id=pem.ebi_metadata_id
            and metadata_value between tp.min_range and tp.max_range
            inner join org_calc_flags_talking_point orc 
            on pem.person_id=orc.person_id and orc.calculated_at IS NULL;");



        
       

        //Talking Points Procedure
        $this->addSQL('DROP PROCEDURE IF EXISTS `Talking_Point_Calc`;');

        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Talking_Point_Calc`()
        BEGIN
       
        DECLARE timeVar DATETIME;
        SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
        
        SET timeVar = CURRENT_TIMESTAMP();
        
        select count(*) into @countorgid from org_calc_flags_talking_point where calculated_at IS NULL;
        if ((@countorgid is not NULL) and (@countorgid > 0)) then
        
        insert into org_talking_points(organization_id,person_id,talking_points_id,survey_id,response, created_at, modified_at)
        SELECT 
            pc.org_id,
            pc.person_id,
            pc.talking_points_id,
            pc.survey_id,
            pc.response,
            timeVar,
            timeVar
        FROM
            person_talking_points_calculated pc
        LEFT JOIN
            (SELECT organization_id, person_id, talking_points_id, response
            FROM org_talking_points otp_out
            WHERE modified_at = 
                (SELECT MAX(modified_at)
                FROM org_talking_points otp_in
                WHERE otp_out.organization_id=otp_in.organization_id 
                AND otp_out.person_id=otp_in.person_id
                AND otp_out.talking_points_id=otp_in.talking_points_id)) AS otp
            ON pc.org_id = otp.organization_id
            AND pc.person_id = otp.person_id
            AND pc.talking_points_id = otp.talking_points_id
        WHERE
            otp.person_id IS NULL
            OR pc.response <> otp.response;
        
        
        
        update org_calc_flags_talking_point orf LEFT JOIN org_talking_points as tp ON tp.organization_id = orf.org_id AND tp.person_id = orf.person_id set orf.calculated_at = '1900-01-01 00:00:00', orf.modified_at = timeVar WHERE (tp.modified_at != timeVar OR tp.modified_at IS NULL) AND orf.calculated_at IS NULL;
        update org_calc_flags_talking_point orf LEFT JOIN org_talking_points as tp ON tp.organization_id = orf.org_id AND tp.person_id = orf.person_id set orf.calculated_at = timeVar, orf.modified_at = timeVar WHERE tp.modified_at = timeVar;
         end if;
        END");

        

        //Fixing Small Syntax error with org_id on Success Markers
        $this->addSQL('DROP PROCEDURE IF EXISTS `Success_Marker_Calc`;');

        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Success_Marker_Calc`()
        BEGIN
            DECLARE timeVar DATETIME;
            SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
            SET timeVar = CURRENT_TIMESTAMP();

            select count(*) into @countorgid from org_calc_flags_success_marker where calculated_at IS NULL;
            if ((@countorgid is not NULL) and (@countorgid > 0)) then
                insert into success_marker_calculated(organization_id,person_id,surveymarker_questions_id, color, created_at, modified_at)
                select orc.org_id,pfc.person_id,smq.id,
                case when pfc.mean_value between red_low and red_high then 'red'
                         when pfc.mean_value between yellow_low and yellow_high then 'yellow'
                         when pfc.mean_value between green_low and green_high then 'green' end as color, timeVar, timeVar

                from surveymarker_questions smq inner join person_factor_calculated pfc on smq.factor_id=pfc.factor_id
                and (pfc.mean_value between red_low and red_high or
                                pfc.mean_value between yellow_low and yellow_high or
                                pfc.mean_value between green_low and green_high) 
                
                
                and smq.survey_id = pfc.survey_id
                
                
                and smq.ebi_question_id is null and smq.survey_questions_id is null and smq.factor_id is not null
                inner join org_calc_flags_success_marker orc 
                on pfc.person_id=orc.person_id and pfc.organization_id=orc.org_id
                and orc.calculated_at IS NULL

                group by org_id, person_id, id
                union
                select orc.org_id,svr.person_id,smq.id,
                case when svr.decimal_value between red_low and red_high then 'red'
                         when svr.decimal_value between yellow_low and yellow_high then 'yellow'
                         when svr.decimal_value between green_low and green_high then 'green' end as color, timeVar, timeVar

                from  surveymarker_questions smq inner join 
                survey_questions svq on smq.ebi_question_id=svq.ebi_question_id
                and svq.survey_id = smq.survey_id
                
                inner join survey_response svr on svq.id=svr.survey_questions_id
                and (svr.decimal_value between red_low and red_high or
                                svr.decimal_value between yellow_low and yellow_high or
                                svr.decimal_value between green_low and green_high)
                
                and svr.survey_id = svq.survey_id
                and smq.ebi_question_id is not null and smq.factor_id is null
                inner join org_calc_flags_success_marker orc 
                on svr.person_id=orc.person_id and orc.calculated_at IS NULL
                inner join org_person_student ops on orc.person_id=ops.person_id
                inner join org_person_student_survey_link opssl  on ops.surveycohort=opssl.cohort and opssl.survey_id=svr.survey_id and opssl.person_id=svr.person_id

                group by orc.org_id, svr.person_id, smq.id;
            
            

            update org_calc_flags_success_marker orc LEFT JOIN success_marker_calculated as smc ON smc.organization_id = orc.org_id AND smc.person_id = orc.person_id set orc.calculated_at = '1900-01-01 00:00:00', orc.modified_at = timeVar WHERE (smc.modified_at != timeVar OR smc.modified_at IS NULL) AND orc.calculated_at is NULL;
            update org_calc_flags_success_marker orc LEFT JOIN success_marker_calculated as smc ON smc.organization_id = orc.org_id AND smc.person_id = orc.person_id set orc.calculated_at = timeVar, orc.modified_at = timeVar WHERE smc.modified_at = timeVar;
            end if;
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
