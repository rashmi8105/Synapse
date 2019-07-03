<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150821200618 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSQL('ALTER TABLE synapse.person_factor_calculated
        	ADD survey_id INT(11) AFTER factor_id;');

        $this->addSQL('ALTER TABLE synapse.person_factor_calculated
        	ADD FOREIGN KEY (survey_id)
        	REFERENCES survey(id);');
        

        $this->addSQL('DROP PROCEDURE IF EXISTS `Factor_Calc`;');


        $this->addSQL("
        CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Factor_Calc`()
        BEGIN
        DECLARE timeVar DATETIME;
        
        select count(*) into @countorgid from org_calc_flags_factor where calculated_at IS NULL;
        if ((@countorgid is not NULL) and (@countorgid > 0)) then
        insert into person_factor_calculated(organization_id,person_id,factor_id,survey_id, mean_value, created_at, modified_at)
        select svr.org_id,svr.person_id,fq.factor_id, svr.survey_id, avg(svr.decimal_value) as mean_value, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP()
        from factor_questions fq 
        inner join survey_questions svq on svq.id=fq.survey_questions_id
        inner join survey_response svr on svr.survey_questions_id=svq.id
        inner join org_calc_flags_factor ofc on svr.person_id=ofc.person_id and svr.org_id=ofc.org_id
		WHERE factor_id IS NOT NULL AND ofc.calculated_at IS NULL

        group by fq.factor_id,svr.person_id
        order by ofc.modified_at ASC;
        end if;
        
        SET timeVar = CURRENT_TIMESTAMP();

        update org_calc_flags_factor set calculated_at= timeVar;

        update org_calc_flags_success_marker sm 
        INNER JOIN org_calc_flags_factor off ON off.org_id = sm.org_id AND off.person_id = sm.person_id
        set sm.calculated_at= NULL 
        WHERE off.calculated_at = timeVar;


        update org_calc_flags_talking_point tp
        INNER JOIN org_calc_flags_factor off ON off.org_id = tp.org_id AND off.person_id = tp.person_id
        set tp.calculated_at= NULL 
        WHERE off.calculated_at = timeVar;

        update org_calc_flags_risk fr
        INNER JOIN org_calc_flags_factor off ON off.org_id = fr.org_id AND off.person_id = fr.person_id
        set fr.calculated_at= NULL 
        WHERE off.calculated_at = timeVar;


        END");

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
