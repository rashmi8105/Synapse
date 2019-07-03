<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151016064427 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $drop_procedure_query = <<<CDATA
DROP PROCEDURE IF EXISTS IssueCalcPermissions;
DROP PROCEDURE IF EXISTS IssueCalcDenominator;
DROP PROCEDURE IF EXISTS IssueCalcNumerator;
CDATA;
        $this->addSql($drop_procedure_query);
        
        
        $calculation_query = <<<CDATA
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `IssueCalcPermissions`(in orgId int(11),in staffId int(11))
BEGIN
Insert into issues_temp_calc_perm (org_id,staff_id,student_id)
(select DISTINCT  merged.organization_id,merged.person_id,merged.student_id FROM (
SELECT F.organization_id,F.person_id,S.person_id AS student_id,F.org_permissionset_id AS permissionset_id FROM org_group_students AS S
                                INNER JOIN org_group_faculty AS F ON F.org_group_id = S.org_group_id and F.deleted_at is null
                                WHERE S.deleted_at is null AND F.person_id= staffId
                UNION ALL
SELECT F.organization_id,F.person_id,S.person_id AS student_id,F.org_permissionset_id AS permissionset_id FROM org_course_student AS S
                                INNER JOIN org_courses AS C ON C.id = S.org_courses_id AND C.deleted_at is null
                                INNER JOIN org_course_faculty AS F ON F.org_courses_id = S.org_courses_id AND F.deleted_at is null
                                INNER JOIN org_academic_terms AS OAT ON OAT.id = C.org_academic_terms_id AND OAT.end_date >= now() AND OAT.deleted_at is null
                                WHERE S.deleted_at is null AND F.person_id= staffId
) AS merged
INNER JOIN person AS P ON P.id=merged.student_id AND P.deleted_at IS NULL AND P.organization_id = orgId
INNER JOIN org_permissionset OPS ON merged.permissionset_id = OPS.id
);
END;
CDATA;
        $this->addSql($calculation_query);
        
        $calculation_query = <<<CDATA
        CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `IssueCalcDenominator`(in orgId int(11),in staffId int(11))
BEGIN
# get the denominator
Insert into issues_temp_calc_den
(org_id,issue_id,count_students,staff_id)
(
select org_id,issue_id,count(DISTINCT(person_id)) as count_students, staffId as staff_id
 from (
# for survey_questions_id
select sr.org_id ,iss.id as issue_id,sr.person_id
from issue as iss
inner join wess_link as wl on iss.survey_id = wl.survey_id and close_date < now()
inner join survey_questions as sq on iss.survey_questions_id = sq.id and sq.ebi_question_id is not null
inner join survey_response as sr on iss.survey_questions_id = sr.survey_questions_id
where
(sr.decimal_value is not null or sr.char_value is not null or sr.charmax_value is not null)
and sr.org_id = orgId
and sr.person_id in (select student_id from issues_temp_calc_perm where staff_id= staffId)
union all
# for factors
select pfc.organization_id ,iss.id as issue_id,pfc.person_id
from issue as iss
inner join wess_link as wl on iss.survey_id = wl.survey_id and close_date < now()
inner join person_factor_calculated as pfc on pfc.factor_id = iss.factor_id
where
(pfc.mean_value is not null)
and pfc.organization_id = orgId
and pfc.person_id in (select student_id from issues_temp_calc_perm where staff_id = staffId)
group by pfc.factor_id,iss.id
) as full_data group by issue_id
having count_students>10
) ;
END;
CDATA;
        $this->addSql($calculation_query);
        
            $calculation_query = <<<CDATA
    	CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `IssueCalcNumerator`(in orgId int(11),in staffId int(11))
BEGIN
# get the numerator
Insert into issues_temp_calc_num
(org_id,survey_id,issue_id,cohort_code,student_id,survey_questions_id,factor_id,response_type,decimal_value,char_value,charmax_value,staff_id)
(
select org_id,survey_id,issue_id,cohort_code,person_id,survey_questions_id,factor_id,response_type,decimal_value,char_value,charmax_value, staffId as staff_id
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
and sr.org_id = orgId
and sr.person_id in (select student_id from issues_temp_calc_perm where staff_id= staffId)
union all
select sr.org_id ,wl.survey_id,iss.id as issue_id,wl.cohort_code, sr.person_id,sr.survey_questions_id,null as factor_id,sr.response_type,sr.decimal_value,sr.char_value,sr.charmax_value
from issue as iss
inner join wess_link as wl on iss.survey_id = wl.survey_id and close_date < now()
inner join survey_questions as sq on iss.survey_questions_id = sq.id
inner join survey_response as sr on iss.survey_questions_id = sr.survey_questions_id
where
((sr.decimal_value between iss.min and iss.max) or (sr.modified_at between iss.start_date and iss.end_date))
and sr.org_id = orgId
and sr.person_id in (select student_id from issues_temp_calc_perm where staff_id= staffId)
union all
# for factors
select pfc.organization_id ,wl.survey_id,iss.id as issue_id,wl.cohort_code
,pfc.person_id,null as survey_questions_id,pfc.factor_id, null as response_type, pfc.mean_value, null as char_value,null as charmax_value
from issue as iss
inner join wess_link as wl on iss.survey_id = wl.survey_id and close_date < now()
inner join person_factor_calculated as pfc on pfc.factor_id = iss.factor_id
where
(pfc.mean_value between iss.min and iss.max)
and pfc.organization_id = orgId
and pfc.person_id in (select student_id from issues_temp_calc_perm where staff_id= staffId)
group by pfc.factor_id,iss.id
) as full_data group by issue_id,person_id,staff_id) ;
END;
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
