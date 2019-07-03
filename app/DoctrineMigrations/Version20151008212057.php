<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151008212057 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('DROP PROCEDURE IF EXISTS `Success_Marker_Calc`;');
        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Success_Marker_Calc`(deadline TIMESTAMP, chunksize SMALLINT UNSIGNED)
        BEGIN
            DECLARE the_ts TIMESTAMP;
            SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
            
            
            
            
            WHILE(
            NOW() < deadline
            AND (SELECT 1 FROM org_calc_flags_success_marker WHERE calculated_at IS NULL LIMIT 1) > 0
            ) DO
                SET the_ts=NOW(); 
            
                   UPDATE org_calc_flags_success_marker
                    SET calculated_at=the_ts
                    WHERE calculated_at IS NULL
                    ORDER BY modified_at ASC
                    LIMIT chunksize;
          
                    
             
                    insert ignore into success_marker_calculated(organization_id,person_id,surveymarker_questions_id, color, created_at, modified_at)
                    select orc.org_id,pfc.person_id,smq.id,
                    case when pfc.mean_value between red_low and red_high then 'red'
                             when pfc.mean_value between yellow_low and yellow_high then 'yellow'
                             when pfc.mean_value between green_low and green_high then 'green' end as color, the_ts, the_ts

                    from surveymarker_questions smq inner join person_factor_calculated pfc on smq.factor_id=pfc.factor_id
                    and (pfc.mean_value between red_low and red_high or
                                    pfc.mean_value between yellow_low and yellow_high or
                                    pfc.mean_value between green_low and green_high) 
                    
                    
                    and smq.survey_id = pfc.survey_id
                    
                    
                    and smq.ebi_question_id is null and smq.survey_questions_id is null and smq.factor_id is not null
                    inner join org_calc_flags_success_marker orc 
                    on pfc.person_id=orc.person_id and pfc.organization_id=orc.org_id
                    and orc.calculated_at = the_ts
                    and pfc.survey_id = get_most_recent_survey(orc.org_id, pfc.person_id)
                        and pfc.modified_at = (select modified_at from person_factor_calculated as fc
                                where fc.organization_id = pfc.organization_id 
                                AND fc.person_id = pfc.person_id 
                                AND fc.factor_id = pfc.factor_id 
                                AND fc.survey_id = pfc.survey_id 
                                ORDER BY modified_at DESC LIMIT 1)
                    group by org_id, person_id, id
                    union
                    select orc.org_id,svr.person_id,smq.id,
                    case when svr.decimal_value between red_low and red_high then 'red'
                             when svr.decimal_value between yellow_low and yellow_high then 'yellow'
                             when svr.decimal_value between green_low and green_high then 'green' end as color, the_ts, the_ts

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
                    on svr.person_id=orc.person_id and orc.calculated_at = the_ts
                    inner join org_person_student ops on orc.person_id=ops.person_id
                    inner join org_person_student_survey_link opssl  on ops.surveycohort=opssl.cohort and opssl.survey_id=svr.survey_id and opssl.person_id=svr.person_id
                    and svr.survey_id = get_most_recent_survey(orc.org_id, svr.person_id)
                    group by orc.org_id, svr.person_id, smq.id;
                
                

                update org_calc_flags_success_marker orc 
                LEFT JOIN success_marker_calculated as smc ON smc.organization_id = orc.org_id 
                AND smc.person_id = orc.person_id 
                set orc.calculated_at = '1900-01-01 00:00:00', orc.modified_at = the_ts 
                WHERE (smc.modified_at != the_ts OR smc.modified_at IS NULL) AND orc.calculated_at = the_ts;
                
                update org_calc_flags_success_marker orc 
                LEFT JOIN success_marker_calculated as smc 
                ON smc.organization_id = orc.org_id 
                AND smc.person_id = orc.person_id 
                set orc.calculated_at = the_ts, orc.modified_at = the_ts 
                WHERE smc.modified_at = the_ts;
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
