<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150810214640 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //$this->addSQL('truncate `person_factor_calculated`;');

        $this->addSQL('ALTER IGNORE TABLE `synapse`.`person_factor_calculated` 
        DROP INDEX `org_person_factor_uniq_idx` ,
        ADD UNIQUE INDEX `org_person_factor_uniq_idx` (`organization_id` ASC, `person_id` ASC, `factor_id` ASC, `modified_at` ASC);');

        $this->addSQL('DROP procedure IF EXISTS `Factor_Calc`;');


        $addFactorInsert = "

        CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Factor_Calc`()
        BEGIN


        select count(*) into @countorgid from org_riskval_calc_inputs where is_factor_calc_reqd = 'y';
        if ((@countorgid is not NULL) and (@countorgid > 0)) then
        insert into person_factor_calculated(organization_id,person_id,factor_id,mean_value, created_at, modified_at)
        select svr.org_id,svr.person_id,fq.factor_id,avg(svr.decimal_value) as mean_value, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP()
        from factor_questions fq 
        inner join survey_questions svq on (svq.ebi_question_id=fq.ebi_question_id or svq.id=fq.survey_questions_id)
        inner join survey_response svr on svr.survey_questions_id=svq.id
        inner join org_riskval_calc_inputs orc on svr.person_id=orc.person_id and svr.org_id=orc.org_id and is_factor_calc_reqd='y'

        group by fq.factor_id,svr.person_id;
        end if;

        update org_riskval_calc_inputs set is_factor_calc_reqd='n';
        END";

        $this->addSQL($addFactorInsert);

        //$this->addSQL()  truncate?

        $this->addSQL('ALTER IGNORE TABLE `synapse`.`success_marker_calculated` 
        DROP INDEX `org_person_marker_uniq_idx` ,
        ADD UNIQUE INDEX `org_person_marker_uniq_idx` (`organization_id` ASC, `person_id` ASC, `surveymarker_questions_id` ASC, `modified_at` ASC);');


        $this->addSQL('DROP procedure IF EXISTS `Success_Marker_Calc`;');

        $fixSuccessMarker = "
            CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Success_Marker_Calc`()
            BEGIN


                            select count(*) into @countorgid from org_riskval_calc_inputs where is_success_marker_calc_reqd = 'y';
            if ((@countorgid is not NULL) and (@countorgid > 0)) then
            insert into success_marker_calculated(organization_id,person_id,surveymarker_questions_id, color, created_at, modified_at)
            select orc.org_id,pfc.person_id,smq.id,
            case when pfc.mean_value between red_low and red_high then 'red'
                     when pfc.mean_value between yellow_low and yellow_high then 'yellow'
                     when pfc.mean_value between green_low and green_high then 'green' end as color, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP()

            from surveymarker_questions smq inner join person_factor_calculated pfc on smq.factor_id=pfc.factor_id
            and (pfc.mean_value between red_low and red_high or
                            pfc.mean_value between yellow_low and yellow_high or
                            pfc.mean_value between green_low and green_high) 
            and smq.ebi_question_id is null and smq.survey_questions_id is null and smq.factor_id is not null
            inner join org_riskval_calc_inputs orc on pfc.person_id=orc.person_id and pfc.organization_id=orc.org_id
            and orc.is_success_marker_calc_reqd='y'

            group by org_id, person_id, id
            union
            select orc.org_id,svr.person_id,smq.id,
            case when svr.decimal_value between red_low and red_high then 'red'
                     when svr.decimal_value between yellow_low and yellow_high then 'yellow'
                     when svr.decimal_value between green_low and green_high then 'green' end as color, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP()

            from  surveymarker_questions smq inner join 
            survey_questions svq on smq.ebi_question_id=svq.ebi_question_id
            inner join survey_response svr on svq.id=svr.survey_questions_id
            and (svr.decimal_value between red_low and red_high or
                            svr.decimal_value between yellow_low and yellow_high or
                            svr.decimal_value between green_low and green_high)
            and smq.ebi_question_id is not null and smq.factor_id is null
            inner join org_riskval_calc_inputs orc on svr.person_id=orc.person_id and orc.is_success_marker_calc_reqd='y'
            inner join org_person_student ops on orc.person_id=ops.person_id
            inner join org_person_student_survey_link opssl  on ops.surveycohort=opssl.cohort and opssl.survey_id=svr.survey_id and opssl.person_id=svr.person_id

            group by orc.org_id, svr.person_id, smq.id;
            
            end if;

            update org_riskval_calc_inputs set is_success_marker_calc_reqd='n';
            END


        ";

        $this->addSQL($fixSuccessMarker);



        $this->addSQL('DROP procedure IF EXISTS `Talking_Point_Calc`;');

        $talkingPoints = "
        CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Talking_Point_Calc`()
        BEGIN


        select count(*) into @countorgid from org_riskval_calc_inputs where is_talking_point_calc_reqd = 'y';
        if ((@countorgid is not NULL) and (@countorgid > 0)) then
                        
        insert into org_talking_points(organization_id,person_id,talking_points_id,survey_id,response, created_at, modified_at)
        select orc.org_id,orc.person_id,tp.id as talking_points_id,svr.survey_id,tp.talking_points_type as response, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() 
        from  talking_points tp inner join 
        survey_questions svq on tp.ebi_question_id=svq.ebi_question_id
        inner join survey_response svr on svq.id=svr.survey_questions_id
        and (case when svr.response_type='decimal'then svr.decimal_value end) between tp.min_range and tp.max_range
        inner join org_riskval_calc_inputs orc on svr.person_id=orc.person_id and svr.org_id=orc.org_id and orc.is_talking_point_calc_reqd='y'
        inner join org_person_student ops on orc.person_id=ops.person_id
        inner join org_person_student_survey_link opssl  on ops.surveycohort=opssl.cohort and opssl.survey_id=svr.survey_id and opssl.person_id=svr.person_id

        union 
        select 
        orc.org_id,orc.person_id,tp.id as talking_points_id,null as survey_id,tp.talking_points_type as response, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() 
        from  talking_points tp inner join 
        person_ebi_metadata pem on tp.ebi_metadata_id=pem.ebi_metadata_id
        and metadata_value between tp.min_range and tp.max_range
        inner join org_riskval_calc_inputs orc on pem.person_id=orc.person_id and orc.is_talking_point_calc_reqd='y'

        ;
        end if;

        update org_riskval_calc_inputs set is_talking_point_calc_reqd='n';
        END";


        $this->addSQL($talkingPoints);






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
