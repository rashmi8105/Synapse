<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150825155523 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $drop_procedure_query = <<<CDATA
DROP PROCEDURE IF EXISTS ReportCalculation;
CDATA;
        $this->addSql($drop_procedure_query);
        
        $calculation_query = <<<CDATA
CREATE PROCEDURE ReportCalculation()
BEGIN
INSERT INTO messages (message,created_at) values('51. --> get all the student id and orgId',now()) ;

select count(*) into @count_records from org_calc_flag_report where calculated_at is null ;
if @count_records is not NULL then
select person_id into @studentId FROM org_calc_flag_report where calculated_at is null limit 1;
select organization_id into @orgId FROM org_calc_flag_report where calculated_at is null and person_id=@studentId limit 1;
INSERT INTO messages (message,created_at) values(concat('52. --> calc start for student id - ', convert( @studentId,char(10)),' and Org id - ', convert( @orgId,char(10))),now()) ;
WHILE (@studentId IS NOT NULL) dO
Replace into report_calculated_values
(org_id,person_id,report_id,section_id,element_id,element_bucket_id)
(select * from (
select pfc.organization_id, pfc.person_id, rs.report_id, rse.section_id,reb.element_id,reb.id as element_bucket_id
from reports r
inner join report_sections rs on rs.report_id = r.id
inner join report_section_elements rse on rse.section_id = rs.id and rse.source_type='F' and rse.factor_id is not null
inner join report_element_buckets reb on reb.element_id = rse.id
inner join person_factor_calculated pfc on pfc.factor_id = rse.factor_id 
where pfc.person_id = @studentId and pfc.organization_id = @orgId
and pfc.mean_value between reb.range_min and reb.range_max 
union all
select sr.org_id, sr.person_id, rs.report_id, rse.section_id,reb.element_id,reb.id as element_bucket_id
from reports r
inner join report_sections rs on rs.report_id = r.id
inner join report_section_elements rse on rse.section_id = rs.id and rse.source_type='Q' and rse.survey_question_id is not null
inner join report_element_buckets reb on reb.element_id = rse.id
inner join survey_response sr on sr.survey_questions_id=rse.survey_question_id
where sr.person_id = @studentId and sr.org_id = @orgId
and sr.decimal_value between reb.range_min and reb.range_max 
) merged) ;

INSERT INTO messages (message,created_at) values(concat('53. --> update org_calc_flag_report for student id - ', convert( @studentId,char(10)),' and Org id - ', convert( @orgId,char(10))),now()) ;
update org_calc_flag_report set calculated_at = now() where person_id=@studentId and organization_id = @orgId;

#To fetch org_id
set @orgId := null;
set @studentId := null;
select person_id into @studentId FROM org_calc_flag_report where calculated_at is null limit 1;
select organization_id into @orgId FROM org_calc_flag_report where calculated_at is null and person_id=@studentId limit 1;

if @studentId is not null then
INSERT INTO messages (message,created_at) values(concat('54. --> calc start for student id - ', convert( @studentId,char(10)),' and Org id - ', convert( @orgId,char(10))),now()) ;
else
INSERT INTO messages (message,created_at) values('55. no more student for report calc, so quitting', now()) ;
end if;

end WHILE;
end if;
END;
CDATA;
        $this->addSql($calculation_query);
        
        $event_query = <<<CDATA
drop EVENT if exists event_report_calc;
SET GLOBAL event_scheduler = on;
CREATE EVENT event_report_calc
    ON SCHEDULE EVERY 5 minute
	STARTS CURRENT_TIMESTAMP
	DO
       CALL ReportCalculation();
CDATA;
        $this->addSql($event_query);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
