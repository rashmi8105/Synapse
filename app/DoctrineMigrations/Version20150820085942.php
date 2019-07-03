<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150820085942 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $drop_procedure_query = <<<CDATA
DROP PROCEDURE IF EXISTS IssueCalcTempTables;
DROP PROCEDURE IF EXISTS IssueCalcPermissions;
DROP PROCEDURE IF EXISTS IssueCalcDenominator;
DROP PROCEDURE IF EXISTS IssueCalcNumerator;
DROP PROCEDURE IF EXISTS IssueCalcSet;
DROP PROCEDURE IF EXISTS IssueCalculation;
CDATA;
        $this->addSql($drop_procedure_query);
        
        $calculation_query = <<<CDATA
CREATE PROCEDURE `IssueCalcTempTables`()
BEGIN
DROP TABLE IF EXISTS issues_temp_calc_perm;
CREATE TABLE `issues_temp_calc_perm` (
`org_id` int(11) DEFAULT NULL,
`staff_id` int(11) DEFAULT NULL,
`student_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS issues_temp_calc_num;
CREATE TABLE `issues_temp_calc_num` (
`org_id` int(11) DEFAULT NULL,
`staff_id` int(11) DEFAULT NULL,
`survey_id` int(11) DEFAULT NULL,
`issue_id` int(11) DEFAULT NULL,
`cohort_code` int(11) DEFAULT NULL,
`student_id` int(11) DEFAULT NULL,
`survey_questions_id` int(11) DEFAULT NULL,
`factor_id` int(11) DEFAULT NULL,
`response_type` varchar(50) DEFAULT NULL,
`decimal_value` decimal(10,4) DEFAULT NULL,
`char_value` int(11) DEFAULT NULL,
`charmax_value` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS issues_temp_calc_den;
CREATE TABLE `issues_temp_calc_den` (
`org_id` int(11) DEFAULT NULL,
`issue_id` int(11) DEFAULT NULL,
`count_students` int(11) DEFAULT NULL,
`staff_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS issues_temp_calc_done;
CREATE TABLE `issues_temp_calc_done` (
`org_id` int(11) DEFAULT NULL,
`staff_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
END;
CDATA;
        $this->addSql($calculation_query);
        
        $calculation_query = <<<CDATA
CREATE PROCEDURE `IssueCalcPermissions`(in orgId int,in staffId int)
BEGIN
Insert into issues_temp_calc_perm (org_id,staff_id,student_id)
(select DISTINCT  merged.organization_id,merged.person_id,merged.student_id FROM (
SELECT F.organization_id,F.person_id,S.person_id AS student_id,F.org_permissionset_id AS permissionset_id FROM org_group_students AS S
                                INNER JOIN org_group_faculty AS F ON F.org_group_id = S.org_group_id and F.deleted_at is null
                                WHERE S.deleted_at is null AND F.person_id=@staffId
                UNION ALL
SELECT F.organization_id,F.person_id,S.person_id AS student_id,F.org_permissionset_id AS permissionset_id FROM org_course_student AS S
                                INNER JOIN org_courses AS C ON C.id = S.org_courses_id AND C.deleted_at is null
                                INNER JOIN org_course_faculty AS F ON F.org_courses_id = S.org_courses_id AND F.deleted_at is null
                                INNER JOIN org_academic_terms AS OAT ON OAT.id = C.org_academic_terms_id AND OAT.end_date >= now() AND OAT.deleted_at is null
                                WHERE S.deleted_at is null AND F.person_id=@staffId 
) AS merged
INNER JOIN person AS P ON P.id=merged.student_id AND P.deleted_at IS NULL AND P.organization_id = @orgId
INNER JOIN org_permissionset OPS ON merged.permissionset_id = OPS.id
);
END;
CDATA;
        $this->addSql($calculation_query);
        
        $calculation_query = <<<CDATA
CREATE PROCEDURE `IssueCalcDenominator`(in orgId int,in staffId int)
BEGIN
# get the denominator
Insert into issues_temp_calc_den
(org_id,issue_id,count_students,staff_id)
(
select org_id,issue_id,count(DISTINCT(person_id)) as count_students,@staffId as staff_id
 from (
# for survey_questions_id
select sr.org_id ,iss.id as issue_id,sr.person_id
from issue as iss 
inner join wess_link as wl on iss.survey_id = wl.survey_id and close_date < now()
inner join survey_questions as sq on iss.survey_questions_id = sq.id and sq.ebi_question_id is not null
inner join survey_response as sr on iss.survey_questions_id = sr.survey_questions_id 
where 
(sr.decimal_value is not null or sr.char_value is not null or sr.charmax_value is not null)  
and sr.org_id = @orgId
and sr.person_id in (select student_id from issues_temp_calc_perm where staff_id=@staffId)
union all
# for factors
select sr.org_id ,iss.id as issue_id,sr.person_id
from issue as iss 
inner join wess_link as wl on iss.survey_id = wl.survey_id and close_date < now()
inner join factor_questions as fq on iss.factor_id = fq.id
inner join survey_questions as sq on (sq.ebi_question_id=fq.ebi_question_id or sq.id=fq.survey_questions_id)
inner join survey_response as sr on sq.id = sr.survey_questions_id 
where 
(sr.decimal_value is not null)
and sr.org_id = @orgId
and sr.person_id in (select student_id from issues_temp_calc_perm where staff_id=@staffId)
group by fq.factor_id,iss.id
) as full_data group by issue_id
having count_students>10 
) ;
END;
CDATA;
        $this->addSql($calculation_query);
        
        $calculation_query = <<<CDATA
CREATE PROCEDURE `IssueCalcNumerator`(in orgId int,in staffId int)
BEGIN
# get the numerator
Insert into issues_temp_calc_num
(org_id,survey_id,issue_id,cohort_code,student_id,survey_questions_id,factor_id,response_type,decimal_value,char_value,charmax_value,staff_id)
(
select org_id,survey_id,issue_id,cohort_code,person_id,survey_questions_id,factor_id,response_type,decimal_value,char_value,charmax_value,@staffId as staff_id 
 from (
select sr.org_id ,wl.survey_id,iss.id as issue_id,wl.cohort_code, sr.person_id,sr.survey_questions_id,null as factor_id,sr.response_type,sr.decimal_value,sr.char_value,sr.charmax_value
from issue as iss 
inner join issue_options as iop on iss.id = iop.issue_id
inner join wess_link as wl on iss.survey_id = wl.survey_id and close_date < now()
inner join survey_questions as sq on iss.survey_questions_id = sq.id and sq.ebi_question_id is not null
inner join ebi_question_options as eqo on sq.ebi_question_id = eqo.ebi_question_id and iop.ebi_question_options_id = eqo.id
inner join survey_response as sr on iss.survey_questions_id = sr.survey_questions_id 
where 
(eqo.option_value = sr.decimal_value or eqo.option_value = sr.char_value or eqo.option_value = sr.charmax_value)  
and sr.org_id = @orgId
and sr.person_id in (select student_id from issues_temp_calc_perm where staff_id=@staffId)
union all
select sr.org_id ,wl.survey_id,iss.id as issue_id,wl.cohort_code, sr.person_id,sr.survey_questions_id,null as factor_id,sr.response_type,sr.decimal_value,sr.char_value,sr.charmax_value
from issue as iss 
inner join wess_link as wl on iss.survey_id = wl.survey_id and close_date < now()
inner join survey_questions as sq on iss.survey_questions_id = sq.id 
inner join survey_response as sr on iss.survey_questions_id = sr.survey_questions_id 
where 
((sr.decimal_value between iss.min and iss.max) or (sr.modified_at between iss.start_date and iss.end_date))
and sr.org_id = @orgId
and sr.person_id in (select student_id from issues_temp_calc_perm where staff_id=@staffId)
union all
# for factors
select sr.org_id ,wl.survey_id,iss.id as issue_id,wl.cohort_code
,sr.person_id,sr.survey_questions_id,fq.factor_id,sr.response_type, avg(sr.decimal_value) as decimal_value, sr.char_value,sr.charmax_value
from issue as iss 
inner join wess_link as wl on iss.survey_id = wl.survey_id and close_date < now()
inner join factor_questions as fq on iss.factor_id = fq.id
inner join survey_questions as sq on (sq.ebi_question_id=fq.ebi_question_id or sq.id=fq.survey_questions_id)
inner join survey_response as sr on sq.id = sr.survey_questions_id 
where 
((sr.decimal_value between iss.min and iss.max) or (sr.modified_at between iss.start_date and iss.end_date))
and sr.org_id = @orgId
and sr.person_id in (select student_id from issues_temp_calc_perm where staff_id=@staffId)
group by fq.factor_id,iss.id
) as full_data group by issue_id,person_id,staff_id) ;
END;
CDATA;
        $this->addSql($calculation_query);
        
        $calculation_query = <<<CDATA
CREATE PROCEDURE `IssueCalcSet`()
BEGIN
Insert into org_top5_issues_calculated_values
(organization_id,person_id,survey_id,issue_id,survey_cohort_id,calculated_value_numerator,calculated_value_denominator,number_of_students)
(
select cn.org_id,cn.staff_id,cn.survey_id,cn.issue_id,cn.cohort_code,count(distinct(cn.student_id)) as count_num,cd.count_students as count_den,count(distinct(cp.student_id)) as count_total
from issues_temp_calc_num as cn
inner join issues_temp_calc_den as cd on cn.issue_id = cd.issue_id and cn.staff_id = cd.staff_id and cn.org_id = cd.org_id
inner join issues_temp_calc_perm as cp on cd.staff_id = cp.staff_id and cd.org_id = cp.org_id
#where count_students>10 
group by cn.issue_id 
);

/*
insert the values in table issue_calculated_students for listing students
*/
Replace into issue_calculated_students
(organization_id,issue_id,person_student_id,person_staff_id)
(select org_id,issue_id, student_id,staff_id from issues_temp_calc_num);
END;
CDATA;
        $this->addSql($calculation_query);
        
        $calculation_query = <<<CDATA
CREATE PROCEDURE `IssueCalculation`()
BEGIN
INSERT INTO messages (message,created_at) values('21. --> CreateTemptables',now()) ;
call IssueCalcTempTables();
INSERT INTO messages (message,created_at) values('22. --> get all the staff id and orgId',now()) ;

SELECT person_id into @staffId FROM org_person_faculty where deleted_at is null 
and person_id not in (select staff_id from issues_temp_calc_done) limit 1;
SELECT organization_id into @orgId FROM org_person_faculty where deleted_at is null 
and person_id=@staffId;

INSERT INTO messages (message,created_at) values(concat('23. --> calc start for staff id - ', convert( @staffId,char(10)),' and Org id - ', convert( @orgId,char(10))),now()) ;
#INSERT INTO messages (message,created_at) values(concat('24. --> calc start for Org id - ', convert( @orgId,char(10))),now()) ;
WHILE (@staffId IS NOT NULL) dO
INSERT INTO messages (message,created_at) values('25. --> IssueCalcPermissions',now()) ;
call IssueCalcPermissions(@orgId,@staffId);
INSERT INTO messages (message,created_at) values('26. --> IssueCalcDenominator',now()) ;
call IssueCalcDenominator(@orgId,@staffId);
INSERT INTO messages (message,created_at) values('27. --> IssueCalcNumerator',now()) ;
call IssueCalcNumerator(@orgId,@staffId);
INSERT INTO messages (message,created_at) values('28. --> insert into issues_temp_calc_done',now()) ;
#insert into tmp table to capture org and staff id
insert into issues_temp_calc_done(org_id,staff_id) values(@orgId,@staffId);

#To fetch org_id
set @orgId := null;
set @staffId := null;
SELECT person_id into @staffId FROM org_person_faculty where deleted_at is null 
and person_id not in (select staff_id from issues_temp_calc_done) limit 1;
SELECT organization_id into @orgId FROM org_person_faculty where deleted_at is null 
and person_id=@staffId limit 1;

if @staffId is not null then
INSERT INTO messages (message,created_at) values(concat('29. --> calc start for staff id - ', convert( @staffId,char(10)),' and Org id - ', convert( @orgId,char(10))),now()) ;
else
INSERT INTO messages (message,created_at) values('29. NO MORE Org_Ids for issue calc, so quitting', now()) ;
end if;
END WHILE;
INSERT INTO messages (message,created_at) values('30. --> IssueCalcSet',now()) ;
call IssueCalcSet();
END;
CDATA;
        $this->addSql($calculation_query);
        
        $event_call = '
        CREATE EVENT IssueCalculationEvent
        ON SCHEDULE EVERY 1 hour
        STARTS CURRENT_TIMESTAMP + INTERVAL 15 minute
        DO BEGIN
            CALL IssueCalculation();
        END';
        
        $this->addSQL($event_call);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
