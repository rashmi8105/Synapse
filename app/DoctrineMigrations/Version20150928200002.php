<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150928200002 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
         $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('DROP PROCEDURE IF EXISTS `Factor_Calc`;');
        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Factor_Calc`()
		BEGIN
       
        DECLARE timeVar DATETIME;
        SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

        SET timeVar = CURRENT_TIMESTAMP();
        
        select count(*) into @countorgid from org_calc_flags_factor where calculated_at IS NULL;
        if ((@countorgid is not NULL) and (@countorgid > 0)) then
        insert into person_factor_calculated(organization_id, person_id, factor_id, survey_id, mean_value, created_at, modified_at)
        select 
            svr.org_id,
            svr.person_id,
            fq.factor_id, 
            svr.survey_id, 
            avg(svr.decimal_value) as mean_value,
            timeVar, 
            timeVar
        from factor_questions fq 
        inner join survey_questions svq 
            on svq.ebi_question_id=fq.ebi_question_id
        inner join survey_response svr 
            on svr.survey_questions_id=svq.id
        inner join org_calc_flags_factor ofc 
            on svr.person_id=ofc.person_id 
            and svr.org_id=ofc.org_id
        WHERE 
            factor_id IS NOT NULL 
            AND ofc.calculated_at IS NULL 
            AND FLOOR(svr.decimal_value) != 99
            AND svr.survey_id =
            (
                SELECT survey_id
                FROM survey_response sr
                WHERE 
                    (sr.org_id, sr.person_id)
                    = (svr.org_id, svr.person_id)
                ORDER BY modified_at DESC
                LIMIT 1
            )
        group by fq.factor_id,svr.person_id
        order by ofc.modified_at ASC;
        
        
        update org_calc_flags_success_marker sm 
        INNER JOIN org_calc_flags_factor off ON off.org_id = sm.org_id AND off.person_id = sm.person_id
        set sm.calculated_at= NULL,
        sm.modified_at = timeVar 
        WHERE off.calculated_at is NULL;

        update org_calc_flags_talking_point tp
        INNER JOIN org_calc_flags_factor off ON off.org_id = tp.org_id AND off.person_id = tp.person_id
        set tp.calculated_at= NULL,
        tp.modified_at = timeVar
        WHERE off.calculated_at is NULL;

        update org_calc_flags_risk fr
        INNER JOIN org_calc_flags_factor off ON off.org_id = fr.org_id AND off.person_id = fr.person_id
        set fr.calculated_at= NULL,
        fr.modified_at = timeVar 
        WHERE off.calculated_at is NULL;

        insert into org_calc_flags_student_reports(org_id, person_id, created_at, modified_at, calculated_at)
        SELECT off.org_id, off.person_id, timeVar, timeVar, NULL FROM org_calc_flags_factor off 
        INNER JOIN org_person_student_survey_link opssl 
        ON opssl.org_id = off.org_id 
        AND opssl.person_id = off.person_id
        WHERE off.calculated_at is NULL
        GROUP BY off.org_id, off.person_id;
       

        update org_calc_flags_factor f 
        INNER JOIN (select svr.org_id,svr.person_id
        from factor_questions fq 
        inner join survey_questions svq on svq.ebi_question_id=fq.ebi_question_id
        inner join survey_response svr on svr.survey_questions_id=svq.id
        inner join org_calc_flags_factor ofc on svr.person_id=ofc.person_id and svr.org_id=ofc.org_id
        WHERE factor_id IS NOT NULL AND ofc.calculated_at IS NULL AND FLOOR(svr.decimal_value) != 99) as orff 
        On orff.org_id = f.org_id AND orff.person_id = f.person_id 
        SET calculated_at = timeVar, modified_at = timeVar;

        update org_calc_flags_factor off 
        SET calculated_at = '1900-01-01 00:00:00', modified_at = timeVar 
        WHERE off.calculated_at is NULL;

        end if;
        END");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
         $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
