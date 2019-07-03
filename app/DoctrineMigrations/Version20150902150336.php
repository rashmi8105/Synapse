<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150902150336 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE report_calc_history CHANGE file_name file_name VARCHAR(255) DEFAULT NULL');
        
        $drop_procedure_query = <<<CDATA
DROP PROCEDURE IF EXISTS ReportCalculation;
CDATA;
        $this->addSql($drop_procedure_query);
        
        $calculation_query = <<<CDATA
CREATE PROCEDURE `ReportCalculation`()
BEGIN
INSERT INTO messages (message,created_at) values('51. --> get all the student id and orgId',now()) ;
        
select count(distinct(person_id)) into @count_records from org_calc_flags_student_reports where calculated_at is null limit 1;
if @count_records is not NULL then
select person_id into @studentId FROM org_calc_flags_student_reports where calculated_at is null limit 1;
select org_id into @orgId FROM org_calc_flags_student_reports where calculated_at is null and person_id=@studentId limit 1;
        
WHILE (@studentId IS NOT NULL) dO
INSERT INTO messages (message,created_at) values(concat('52. --> calc start for student id - ', convert( @studentId,char(10)),' and Org id - ', convert( @orgId,char(10))),now()) ;
set @student_survey_count := 1;
#get the recent survey taken by student
select count(distinct(survey_id)) into @count_survey from  survey_response where org_id=@orgId and person_id=@studentId and decimal_value is not null
and survey_id not in (select survey_id from report_calculated_values where org_id=@orgId and person_id=@studentId);
        
if @count_survey is not NULL then
select distinct(survey_id) into @surveyId from survey_response where org_id=@orgId and person_id=@studentId and decimal_value is not null
and survey_id not in (select survey_id from report_calculated_values where org_id=@orgId and person_id=@studentId)
order by id desc limit 1;
        
WHILE (@surveyId IS NOT NULL and @student_survey_count<=@count_survey) do
INSERT INTO messages (message,created_at) values(concat('53. --> calc start for student id - ', convert( @studentId,char(10)),' and surveyId - ', convert( @surveyId,char(10))),now()) ;

INSERT INTO messages (message,created_at) values(concat('54. --> calc start for student id - ', convert( @studentId,char(10)),' and student_survey_count - ', convert( @student_survey_count,char(10))),now()) ;
Replace into report_calculated_values
(org_id,person_id,report_id,section_id,element_id,element_bucket_id,survey_id,calculated_value)
(select organization_id, person_id, report_id, section_id, element_id, element_bucket_id, survey_id, mean_value from (
select pfc.organization_id, pfc.person_id, rs.report_id, rse.section_id,reb.element_id,reb.id as element_bucket_id,pfc.survey_id,pfc.mean_value
from reports r
inner join report_sections rs on rs.report_id = r.id
inner join report_section_elements rse on rse.section_id = rs.id and rse.source_type='F' and rse.factor_id is not null
inner join report_element_buckets reb on reb.element_id = rse.id
inner join person_factor_calculated pfc on pfc.factor_id = rse.factor_id
inner join factor ft on ft.id = pfc.factor_id
where pfc.person_id = @studentId and pfc.organization_id = @orgId and pfc.survey_id = @surveyId and r.name='student-report'
and pfc.mean_value between reb.range_min and reb.range_max
group by person_id,report_id,section_id,element_id,survey_id
union all
select sr.org_id, sr.person_id, rs.report_id, rse.section_id,reb.element_id,reb.id as element_bucket_id,sr.survey_id,sr.decimal_value
from reports r
inner join report_sections rs on rs.report_id = r.id
inner join report_section_elements rse on rse.section_id = rs.id and rse.source_type='Q' and rse.survey_question_id is not null
inner join report_element_buckets reb on reb.element_id = rse.id
inner join survey_response sr on sr.survey_questions_id=rse.survey_question_id and sr.decimal_value is not null
where sr.person_id = @studentId and sr.org_id = @orgId and sr.survey_id = @surveyId and r.name='student-report'
and sr.decimal_value between reb.range_min and reb.range_max
group by person_id,report_id,section_id,element_id,survey_id
) merged) ;
        
set @student_survey_count := @student_survey_count + 1;

#insert and update tables for pdf mapping
set @reportId := (select id from reports where name='student-report'); 
update org_calc_flags_student_reports set survey_id = @surveyId where person_id=@studentId and org_id = @orgId and calculated_at is null and survey_id is null;
update org_calc_flags_student_reports set report_id = @reportId where person_id=@studentId and org_id = @orgId and calculated_at is null and survey_id = @surveyId;
insert into report_calc_history (report_id,org_id,person_id,survey_id) values('1',convert(@orgId,char(10)),convert(@studentId,char(10)),convert(@surveyId,char(10))) ;

INSERT INTO messages (message,created_at) values('55. Calc done for one survey id, check for next survey id', now()) ;
        
set @surveyId := null;
select distinct(survey_id) into @surveyId from survey_response where org_id=@orgId and person_id=@studentId and decimal_value is not null
and survey_id not in (select survey_id from report_calculated_values where org_id=@orgId and person_id=@studentId)
order by id desc limit 1;
        
end WHILE;
    
INSERT INTO messages (message,created_at) values('56. Calc done for one student id, check for next student id', now()) ;
  
#survey loop over
        
end if;
        
INSERT INTO messages (message,created_at) values(concat('57. --> update org_calc_flags_student_reports for student id - ', convert( @studentId,char(10)),' and Org id - ', convert( @orgId,char(10))),now()) ;
update org_calc_flags_student_reports set calculated_at = now() where person_id=@studentId and org_id = @orgId;
        
set @student_survey_count := 0;
#To fetch org_id
set @orgId := null;
set @studentId := null;
select person_id into @studentId FROM org_calc_flags_student_reports where calculated_at is null limit 1;
select org_id into @orgId FROM org_calc_flags_student_reports where calculated_at is null and person_id=@studentId limit 1;
        
if @studentId is not null then
INSERT INTO messages (message,created_at) values(concat('58. --> calc start for student id - ', convert( @studentId,char(10)),' and Org id - ', convert( @orgId,char(10))),now()) ;
else
INSERT INTO messages (message,created_at) values('59. no more student for report calc, so quitting', now()) ;
end if;
        
end WHILE;
        
end if;
end;
CDATA;
        $this->addSql($calculation_query);
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
