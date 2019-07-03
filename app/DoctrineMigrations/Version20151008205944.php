<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151008205944 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('DROP PROCEDURE IF EXISTS `Factor_Calc`;');
        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Factor_Calc`(deadline TIMESTAMP, chunksize SMALLINT UNSIGNED)
        BEGIN
       
        
        DECLARE the_ts TIMESTAMP;
        SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
        
        if (SELECT 1 from synapse.risk_calc_tracking_table LIMIT 1) > 0 then
            SET @lastupdate = (select max(last_update_ts) from synapse.risk_calc_tracking_table);
            SET @lastupdateISQ = (select max(last_update_ts) from synapse.risk_calc_tracking_table_ISQ);
        else
            SET @lastupdate = '1900-01-01 00:00:00';
            SET @lastupdateISQ = '1900-01-01 00:00:00';
        end if;
        
        INSERT IGNORE INTO synapse.risk_calc_tracking_table(org_id, person_id, survey_id)
        (select org_id, person_id, survey_id from synapse.survey_response
        where synapse.survey_response.modified_at > @lastupdate
        GROUP BY org_id, person_id, survey_id);
        
        SET @maxMod = (select max(modified_at) from synapse.survey_response);
        
        update synapse.risk_calc_tracking_table set most_recent_survey_question_id = get_most_recent_survey_question(org_id, person_id, survey_id),
        last_update_ts=@maxMod where last_update_ts<@maxMod or last_update_ts is null
        ;
    
        update org_calc_flags_factor f INNER JOIN synapse.risk_calc_tracking_table rctt 
        ON rctt.org_id = f.org_id AND rctt.person_id = f.person_id 
        AND (rctt.most_recent_survey_question_id <> rctt.last_seen_survey_question_id OR 
        rctt.last_seen_survey_question_id is null)
        SET rctt.last_seen_survey_question_id = rctt.most_recent_survey_question_id,
        f.calculated_at = null, f.modified_at = CURRENT_TIMESTAMP();
        
        DELETE rctt FROM synapse.risk_calc_tracking_table rctt INNER JOIN org_person_student_survey_link opssl 
        On rctt.org_id = opssl.org_id and rctt.person_id = opssl.person_id and rctt.survey_id = opssl.survey_id
        where opssl.survey_completion_status = 'CompletedAll';

        INSERT IGNORE INTO synapse.risk_calc_tracking_table_ISQ(org_id, person_id, survey_id)
        (select org_id, person_id, survey_id from synapse.org_question_response
        where synapse.org_question_response.modified_at > @lastupdateISQ
        GROUP BY org_id, person_id, survey_id);
        
        SET @maxISQ = (select max(modified_at) from synapse.org_question_response);
        
        update synapse.risk_calc_tracking_table_ISQ set most_recent_org_question_id = get_most_recent_ISQ(org_id, person_id, survey_id),
        last_update_ts=@maxISQ where last_update_ts<@maxISQ or last_update_ts is null;
        
        UPDATE org_calc_flags_risk r
        INNER JOIN
        synapse.risk_calc_tracking_table_ISQ rctt ON rctt.org_id = r.org_id
        AND rctt.person_id = r.person_id
        AND (rctt.most_recent_org_question_id <> rctt.last_seen_org_question_id
        OR rctt.last_seen_org_question_id IS NULL) 
        SET 
        rctt.last_seen_org_question_id = rctt.most_recent_org_question_id,
        r.calculated_at = NULL,
        r.modified_at = CURRENT_TIMESTAMP();
        
        DELETE rctt FROM synapse.risk_calc_tracking_table_ISQ rctt INNER JOIN org_person_student_survey_link opssl 
        On rctt.org_id = opssl.org_id and rctt.person_id = opssl.person_id and rctt.survey_id = opssl.survey_id
        where opssl.survey_completion_status = 'CompletedAll';
        
         WHILE(
                NOW() < deadline
                AND (SELECT 1 FROM org_calc_flags_factor WHERE calculated_at IS NULL LIMIT 1) > 0
            ) DO
                SET the_ts=NOW(); 

       
        
              UPDATE org_calc_flags_factor
                SET calculated_at=the_ts
                WHERE calculated_at IS NULL
                ORDER BY modified_at ASC
                LIMIT chunksize;
      
        REPLACE INTO person_factor_calculated(organization_id, person_id, factor_id, survey_id, mean_value, created_at, modified_at)
       select straight_join
        svr.org_id,
        svr.person_id,
        fq.factor_id,
        svr.survey_id,
        avg(svr.decimal_value) as mean_value,
        the_ts,
        the_ts
        FROM org_calc_flags_factor ofc
        INNER JOIN survey_response AS svr
        ON (svr.org_id, svr.person_id)
        = (ofc.org_id, ofc.person_id
        )
        inner join survey_questions svq
        ON svq.id=svr.survey_questions_id
        inner join factor_questions fq
        ON fq.ebi_question_id=svq.ebi_question_id
        WHERE
        factor_id IS NOT NULL
        AND svr.survey_id =
        GET_MOST_RECENT_SURVEY(svr.org_id, svr.person_id)
        AND ofc.calculated_at = the_ts
        AND FLOOR(svr.decimal_value) != 99
        group by svr.org_id, svr.person_id, fq.factor_id, svr.survey_id;
        
        
        update org_calc_flags_success_marker sm 
        INNER JOIN org_calc_flags_factor off ON off.org_id = sm.org_id AND off.person_id = sm.person_id
        set sm.calculated_at= NULL,
        sm.modified_at = the_ts
        WHERE off.calculated_at = the_ts;

        update org_calc_flags_talking_point tp
        INNER JOIN org_calc_flags_factor off ON off.org_id = tp.org_id AND off.person_id = tp.person_id
        set tp.calculated_at= NULL,
        tp.modified_at = the_ts
        WHERE off.calculated_at = the_ts;

        update org_calc_flags_risk fr
        INNER JOIN org_calc_flags_factor off ON off.org_id = fr.org_id AND off.person_id = fr.person_id
        set fr.calculated_at= NULL,
        fr.modified_at = the_ts 
        WHERE off.calculated_at = the_ts;

        insert into org_calc_flags_student_reports(org_id, person_id, created_at, modified_at, calculated_at)
        SELECT off.org_id, off.person_id, the_ts, the_ts, NULL FROM org_calc_flags_factor off 
        INNER JOIN org_person_student_survey_link opssl 
        ON opssl.org_id = off.org_id 
        AND opssl.person_id = off.person_id
        WHERE off.calculated_at = the_ts
        GROUP BY off.org_id, off.person_id;
       
        
        update org_calc_flags_factor f 
        INNER JOIN (select straight_join svr.org_id,svr.person_id
        FROM org_calc_flags_factor ofc
        INNER JOIN survey_response AS svr
        ON (svr.org_id, svr.person_id)
        = (ofc.org_id, ofc.person_id
        )
        inner join survey_questions svq
        ON svq.id=svr.survey_questions_id
        inner join factor_questions fq
        ON fq.ebi_question_id=svq.ebi_question_id
        WHERE
        factor_id IS NOT NULL
        AND ofc.calculated_at = the_ts
        AND FLOOR(svr.decimal_value) != 99) as calc On calc.org_id = f.org_id AND calc.person_id = f.person_id
        SET calculated_at = the_ts, modified_at = the_ts;

    
        
        update org_calc_flags_factor off 
        SET off.calculated_at = '1900-01-01 00:00:00', off.modified_at = the_ts
        WHERE off.calculated_at = the_ts AND off.modified_at <> off.calculated_at;
        
       
        END WHILE;
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
