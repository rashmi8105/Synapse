<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151008211516 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('DROP PROCEDURE IF EXISTS`ReportCalculation`;');
        $this->addSQL('DROP PROCEDURE IF EXISTS`Report_Calc`;');
        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Report_Calc`(deadline TIMESTAMP, chunksize SMALLINT UNSIGNED)
        BEGIN
        DECLARE the_ts TIMESTAMP;
        SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
        
        
        
        WHILE(NOW() < deadline 
        AND (SELECT 1 FROM org_calc_flags_student_reports WHERE calculated_at IS NULL LIMIT 1) > 0)
        DO
            
            SET the_ts=NOW();       
            
            UPDATE org_calc_flags_student_reports
            SET calculated_at=the_ts
            WHERE calculated_at IS NULL
            ORDER BY modified_at ASC
            LIMIT chunksize;
            
            
            
                
                    REPLACE INTO report_calculated_values (org_id,person_id,report_id,section_id,element_id,element_bucket_id,survey_id,calculated_value,created_at,modified_at)
                              select pfc.organization_id, pfc.person_id, rs.report_id, rse.section_id,reb.element_id,reb.id as element_bucket_id,pfc.survey_id,pfc.mean_value as calc_value, the_ts, the_ts
                                  from reports r
                                    inner join report_sections rs on rs.report_id = r.id
                                    inner join report_section_elements rse on rse.section_id = rs.id and rse.source_type='F' and rse.factor_id is not null
                                    inner join report_element_buckets reb on reb.element_id = rse.id
                                    inner join person_factor_calculated pfc on pfc.factor_id = rse.factor_id
                                    inner join org_calc_flags_student_reports orc ON pfc.person_id = orc.person_id and pfc.organization_id = orc.org_id
                                    where pfc.survey_id = get_most_recent_survey(orc.org_id, orc.person_id) 
                                    and r.name='student-report'
                                    and pfc.mean_value between reb.range_min and reb.range_max 
                                    and orc.calculated_at = the_ts
                                  group by pfc.organization_id, person_id,report_id,section_id,element_id,survey_id
                                union all
                                select sr.org_id, sr.person_id, rs.report_id, rse.section_id,reb.element_id,reb.id as element_bucket_id,sr.survey_id,sr.decimal_value as calc_value, the_ts, the_ts
                                  from reports r
                                    inner join report_sections rs on rs.report_id = r.id
                                    inner join report_section_elements rse on rse.section_id = rs.id and rse.source_type='Q' and rse.survey_question_id is not null
                                    inner join report_element_buckets reb on reb.element_id = rse.id
                                    inner join survey_response sr on sr.survey_questions_id=rse.survey_question_id and sr.decimal_value is not null
                                    inner join org_calc_flags_student_reports orc ON sr.person_id = orc.person_id and sr.org_id = orc.org_id
                                    and sr.survey_id = get_most_recent_survey(orc.org_id, orc.person_id) 
                                    and r.name='student-report'
                                    and sr.decimal_value between reb.range_min and reb.range_max
                                    and orc.calculated_at = the_ts
                                  group by sr.org_id, sr.person_id,rs.report_id,rse.section_id,reb.element_id,sr.survey_id;
                              
                            
                    set @reportId := (select id from reports where name='student-report');
            
            
                    UPDATE org_calc_flags_student_reports SET survey_id = get_most_recent_survey(org_id, person_id), report_id = @reportId, modified_at = the_ts 
                      where calculated_at = the_ts;
                                                            
                        insert into report_calc_history (report_id,org_id,person_id,survey_id,created_at,modified_at) 
                        (select report_id, org_id, person_id, survey_id, the_ts, the_ts from org_calc_flags_student_reports where calculated_at = the_ts);     
            
                update org_calc_flags_student_reports orcf
                LEFT JOIN report_calculated_values rcv ON rcv.org_id = orcf.org_id 
                AND rcv.person_id = orcf.person_id  AND rcv.survey_id = orcf.survey_id AND rcv.report_id = orcf.report_id
                set orcf.calculated_at = '1900-01-01 00:00:00', orcf.modified_at = the_ts 
                WHERE orcf.calculated_at = the_ts AND (rcv.survey_id is null OR rcv.person_id is null OR rcv.report_id is null OR rcv.section_id is null OR rcv.element_id is null);
                
                    
            set @reportID := null;
            
            
            

        end WHILE;
                
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
