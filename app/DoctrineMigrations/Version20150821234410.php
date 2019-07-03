<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150821234410 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSQL('DROP PROCEDURE IF EXISTS `Success_Marker_Calc`;');
        
        $successMarkerUpdate = "CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Success_Marker_Calc`()
            BEGIN


            select count(*) into @countorgid from org_calc_flags_success_marker where calculated_at IS NULL;
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
                inner join org_calc_flags_success_marker orc 
                on pfc.person_id=orc.person_id and pfc.organization_id=orc.org_id
                and orc.calculated_at IS NULL

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
                inner join org_calc_flags_success_marker orc 
                on svr.person_id=orc.person_id and orc.calculated_at IS NULL
                inner join org_person_student ops on orc.person_id=ops.person_id
                inner join org_person_student_survey_link opssl  on ops.surveycohort=opssl.cohort and opssl.survey_id=svr.survey_id and opssl.person_id=svr.person_id

                group by orc.org_id, svr.person_id, smq.id;
            
            end if;

            update org_calc_flags_success_marker set calculated_at = CURRENT_TIMESTAMP();
            END";

            $this->addSQL($successMarkerUpdate);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        // 
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');


    }
}
