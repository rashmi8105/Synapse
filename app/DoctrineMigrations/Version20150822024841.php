<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150822024841 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSQL('DROP PROCEDURE IF EXISTS `Talking_Point_Calc`;');
        
        $talkingPointsUpdate = "
        CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Talking_Point_Calc`()
        BEGIN


        select count(*) into @countorgid from org_calc_flags_talking_point where calculated_at IS NULL;
        if ((@countorgid is not NULL) and (@countorgid > 0)) then
                        
            insert into org_talking_points(organization_id,person_id,talking_points_id,survey_id,response, created_at, modified_at)
            select orc.org_id,orc.person_id,tp.id as talking_points_id,svr.survey_id,tp.talking_points_type as response, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() 
            from  talking_points tp inner join 
            survey_questions svq on tp.ebi_question_id=svq.ebi_question_id
            inner join survey_response svr on svq.id=svr.survey_questions_id
            and (case when svr.response_type='decimal'then svr.decimal_value end) between tp.min_range and tp.max_range
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
            on pem.person_id=orc.person_id and orc.calculated_at IS NULL;
        end if;

        update org_calc_flags_talking_point set calculated_at = CURRENT_TIMESTAMP();
        END";

        $this->addSQL($talkingPointsUpdate);
        

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
