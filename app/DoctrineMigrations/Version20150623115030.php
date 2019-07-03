<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150623115030 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        /* drop if the SP exists */
        $drop_procedure_query = <<<CDATA
DROP PROCEDURE IF EXISTS cur_org_calculated_rv;
CDATA;
        $this->addSql($drop_procedure_query);
        
        /* source file - cur_org_calculated_rv_proc.sql
         * union all risk variable types both iscalculated=1 and iscalculated=0
        */
        $variables_query = <<<CDATA
Create procedure cur_org_calculated_rv(in cur_org_id int)
Begin
insert into cur_org_calculated_risk_variable
(
# Profile Query
#Variable Type Continuous & ISCalculated=0
select * from (
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type,rmw.weight,rv.ebi_metadata_id as source_id,emd.metadata_value as source_value,
rvr.bucket_value,
orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id
inner join risk_model_weights rmw on rmw.risk_model_id=rmm.id
inner join risk_variable rv on rmw.risk_variable_id=rv.id and  rv.is_calculated=0 and rv.variable_type='continuous'
inner join person_ebi_metadata emd on emd.ebi_metadata_id=rv.ebi_metadata_id and emd.person_id=orgc.person_id
inner join risk_variable_range rvr on rvr.risk_variable_id=rv.id and emd.metadata_value between rvr.min and rvr.max
where orgm.org_id=@cur_org_id
        
union
        
#Variable Type Categorical & ISCalculated=0
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type,rmw.weight,rv.ebi_metadata_id as source_id,emd.metadata_value as source_value,
rvr.bucket_value,
orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id
inner join risk_model_weights rmw on rmw.risk_model_id=rmm.id
inner join risk_variable rv on rmw.risk_variable_id=rv.id and  rv.is_calculated=0 and rv.variable_type='categorical'
inner join person_ebi_metadata emd on emd.ebi_metadata_id=rv.ebi_metadata_id and emd.person_id=orgc.person_id
inner join risk_variable_category rvr on rvr.risk_variable_id=rv.id and emd.metadata_value=rvr.option_value
where orgm.org_id=@cur_org_id
        
union all
        
#ISP Query
#Variable Type Continuous & ISCalculated=0
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type,rmw.weight,rv.org_metadata_id as source_id,emd.metadata_value as source_value,
rvr.bucket_value,orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id
inner join risk_model_weights rmw on rmw.risk_model_id=rmm.id
inner join risk_variable rv on rmw.risk_variable_id=rv.id and  rv.is_calculated=0 and rv.variable_type='continuous'
inner join person_org_metadata emd on emd.org_metadata_id=rv.org_metadata_id and emd.person_id=orgc.person_id
inner join risk_variable_range rvr on rvr.risk_variable_id=rv.id
and emd.metadata_value between rvr.min and rvr.max
where orgm.org_id=@cur_org_id
        
union
#Variable Type Categorical & ISCalculated=0
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type,rmw.weight,rv.org_metadata_id as source_id,emd.metadata_value as source_value,
rvr.bucket_value,orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id
inner join risk_model_weights rmw on rmw.risk_model_id=rmm.id
inner join risk_variable rv on rmw.risk_variable_id=rv.id and  rv.is_calculated=0 and rv.variable_type='categorical'
inner join person_org_metadata emd on emd.org_metadata_id=rv.org_metadata_id and emd.person_id=orgc.person_id
inner join risk_variable_category rvr on rvr.risk_variable_id=rv.id
and emd.metadata_value=rvr.option_value
where orgm.org_id=@cur_org_id
        
union all
#ISQ query
#Variable Type Continuous & ISCalculated=0
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type,rmw.weight,rv.org_question_id as source_id
,(case when oqr.response_type='decimal'then oqr.decimal_value  end) as source_value,rvr.bucket_value,orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id
inner join risk_model_weights rmw on rmw.risk_model_id=rmm.id
inner join risk_variable rv on rmw.risk_variable_id=rv.id and  rv.is_calculated=0  and rv.variable_type='continuous'
inner join risk_variable_range rvr on rvr.risk_variable_id=rv.id
inner join org_question oq on oq.id=rv.org_question_id
inner join org_question_response oqr on oqr.survey_id=rv.survey_id and oqr.person_id=orgc.person_id
and oqr.org_question_id=rv.org_question_id
and (case when oqr.response_type='decimal'then oqr.decimal_value  end) between rvr.min and rvr.max
where orgm.org_id=@cur_org_id
        
union
#Variable Type Categorical & ISCalculated=0
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type,rmw.weight,rv.org_question_id as source_id
,(case  when oqr.response_type='char' then oqr.char_value else
                                           oqr.charmax_value end)  as source_value,
rvr.bucket_value,orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id
inner join risk_model_weights rmw on rmw.risk_model_id=rmm.id
inner join risk_variable rv on rmw.risk_variable_id=rv.id and  rv.is_calculated=0 and rv.variable_type='categorical'
inner join risk_variable_category rvr on rvr.risk_variable_id=rv.id
inner join org_question oq on oq.id=rv.org_question_id
inner join org_question_response oqr on oqr.survey_id=rv.survey_id and oqr.person_id=orgc.person_id
and oqr.org_question_id=rv.org_question_id
and (case  when oqr.response_type='char' then oqr.char_value else
                                 oqr.charmax_value end) =rvr.option_value
where orgm.org_id=@cur_org_id
        
union all
#Survey Question query
#Variable Type Continuous & ISCalculated=0
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type,rmw.weight,rv.survey_questions_id as source_id
,(case when svr.response_type='decimal'then svr.decimal_value end) as source_value,rvr.bucket_value,orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id
inner join risk_model_weights rmw on rmw.risk_model_id=rmm.id
inner join risk_variable rv on rmw.risk_variable_id=rv.id and  rv.is_calculated=0 and rv.variable_type='continuous'
inner join risk_variable_range rvr on rvr.risk_variable_id=rv.id
inner join survey_questions svq on svq.ebi_question_id=rv.ebi_question_id
inner join survey_response svr on svr.survey_id=rv.survey_id and svr.person_id=orgc.person_id
and svr.survey_questions_id=rv.survey_questions_id
and (case when svr.response_type='decimal'then svr.decimal_value end) between rvr.min and rvr.max
where orgm.org_id=@cur_org_id
        
union
#Variable Type Categorical & ISCalculated=0
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type,rmw.weight,rv.survey_questions_id as source_id
,(case  when svr.response_type='char' then svr.char_value else
                                           svr.charmax_value end)  as source_value,
rvr.bucket_value,orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id
inner join risk_model_weights rmw on rmw.risk_model_id=rmm.id
inner join risk_variable rv on rmw.risk_variable_id=rv.id and  rv.is_calculated=0 and rv.variable_type='categorical'
inner join risk_variable_category rvr on rvr.risk_variable_id=rv.id
inner join survey_questions svq on svq.ebi_question_id=rv.ebi_question_id
inner join survey_response svr on svr.survey_id=rv.survey_id and svr.person_id=orgc.person_id
and svr.survey_questions_id=rv.survey_questions_id
and (case  when svr.response_type='char' then svr.char_value else
                                              svr.charmax_value end)=rvr.option_value
where orgm.org_id=@cur_org_id
        
union all
#Survey Factor query
#Variable Type Continuous & ISCalculated=0
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type,rmw.weight,fq.survey_questions_id as source_id,
(case when svr.response_type='decimal'then svr.decimal_value end) as source_value,rvr.bucket_value,orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id
inner join risk_model_weights rmw on rmw.risk_model_id=rmm.id
inner join risk_variable rv on rmw.risk_variable_id=rv.id and  rv.is_calculated=0 and rv.variable_type='continuous'
inner join risk_variable_range rvr on rvr.risk_variable_id=rv.id
#inner join survey sv on sv.id=rv.survey_id
inner join factor_questions fq on  fq.id=rv.factor_id
inner join survey_questions svq on svq.ebi_question_id=fq.ebi_question_id
inner join survey_response svr on svr.survey_id=rv.survey_id  and svr.person_id=orgc.person_id
and svr.survey_questions_id=fq.survey_questions_id
and (case when svr.response_type='decimal'then svr.decimal_value end) between rvr.min and rvr.max
where orgm.org_id=@cur_org_id
      
union all
# Profile Query
#Variable Type Continuous & ISCalculated=1
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type as variable_type,rmw.weight as weight,rv.ebi_metadata_id As Source_id
,(case when rv.variable_type='continuous' and rv.is_calculated=1
and emd.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
case when rv.calc_type ='Sum' then
(select sum(pem.metadata_value) from person_ebi_metadata pem where pem.ebi_metadata_id=rv.ebi_metadata_id and pem.person_id=orgc.person_id and pem.modified_at between rv.calculation_start_date and rv.calculation_end_date and pem.modified_at is not null order by pem.modified_at desc )
when rv.calc_type ='Average' then
(select avg(pem.metadata_value) from person_ebi_metadata pem where pem.ebi_metadata_id=rv.ebi_metadata_id and pem.person_id=orgc.person_id  and pem.modified_at between rv.calculation_start_date and rv.calculation_end_date and pem.modified_at is not null order by pem.modified_at desc )
when rv.calc_type ='Count' then
(select count(pem.metadata_value) from person_ebi_metadata pem where pem.ebi_metadata_id=rv.ebi_metadata_id and pem.person_id=orgc.person_id and pem.modified_at between rv.calculation_start_date and rv.calculation_end_date and pem.modified_at is not null order by pem.modified_at desc )
when rv.calc_type='Most Recent' then
(select pem.metadata_value from person_ebi_metadata pem where pem.ebi_metadata_id=rv.ebi_metadata_id and pem.person_id=orgc.person_id and pem.modified_at between rv.calculation_start_date and rv.calculation_end_date and pem.modified_at is not null order by pem.modified_at desc limit 1)
when rv.calc_type='Academic Update' then
(select count(au.id) from academic_update au where au.person_id_student=orgc.person_id and (au.modified_at between rv.calculation_start_date and rv.calculation_end_date) and (upper(au.failure_risk_level)='HIGH' or upper(au.grade) in('D','D-','D+','F','F+','F-')))
end
end ) as source_value,
(select bucket_value from risk_variable_range where
(case when rv.variable_type='continuous' and rv.is_calculated=1
and emd.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
case when rv.calc_type ='Sum' then
(select sum(pem.metadata_value) from person_ebi_metadata pem where pem.ebi_metadata_id=rv.ebi_metadata_id and pem.person_id=orgc.person_id and pem.modified_at between rv.calculation_start_date and rv.calculation_end_date and pem.modified_at is not null order by pem.modified_at desc )
when rv.calc_type ='Average' then
(select avg(pem.metadata_value) from person_ebi_metadata pem where pem.ebi_metadata_id=rv.ebi_metadata_id and pem.person_id=orgc.person_id and pem.modified_at between rv.calculation_start_date and rv.calculation_end_date and pem.modified_at is not null order by pem.modified_at desc )
when rv.calc_type ='Count' then
(select count(pem.metadata_value) from person_ebi_metadata pem where pem.ebi_metadata_id=rv.ebi_metadata_id and pem.person_id=orgc.person_id and pem.modified_at between rv.calculation_start_date and rv.calculation_end_date and pem.modified_at is not null order by pem.modified_at desc )
when rv.calc_type='Most Recent' then
(select pem.metadata_value from person_ebi_metadata pem where pem.ebi_metadata_id=rv.ebi_metadata_id and pem.person_id=orgc.person_id and pem.modified_at between rv.calculation_start_date and rv.calculation_end_date and pem.modified_at is not null order by pem.modified_at desc limit 1)
when rv.calc_type='Academic Update' then
(select count(au.id) from academic_update au where au.person_id_student=orgc.person_id and (au.modified_at between rv.calculation_start_date and rv.calculation_end_date) and (upper(au.failure_risk_level)='HIGH' or upper(au.grade) in('D','D-','D+','F','F+','F-')))
end
end ) between min and max and risk_variable_id=rv.id
) as
bucket_value ,
orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id #and rgph.person_id=student_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id
inner join risk_model_weights rmw on rmw.risk_model_id=rmm.id
inner join risk_variable rv on rmw.risk_variable_id=rv.id
and  rv.is_calculated=1 and rv.variable_type='continuous'
inner join person_ebi_metadata emd on emd.ebi_metadata_id=rv.ebi_metadata_id #and emd.person_id=student_id
and emd.modified_at between rv.calculation_start_date and rv.calculation_end_date
where orgm.org_id=@cur_org_id
group by orgc.person_id,rv.id
        
        
union
#Variable Type Categorical & ISCalculated=1
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type as variable_type,rmw.weight as weight,rv.ebi_metadata_id As Source_id
,(case when rv.variable_type='categorical' and rv.is_calculated=1
and emd.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
case when rv.calc_type ='Count' then
(select count(pem.metadata_value) from person_ebi_metadata pem where pem.ebi_metadata_id=rv.ebi_metadata_id and pem.person_id=orgc.person_id and pem.modified_at between rv.calculation_start_date and rv.calculation_end_date and pem.modified_at is not null order by pem.modified_at desc )
when rv.calc_type='Most Recent' then
(select pem.metadata_value from person_ebi_metadata pem where pem.ebi_metadata_id=rv.ebi_metadata_id and pem.person_id=orgc.person_id and pem.modified_at between rv.calculation_start_date and rv.calculation_end_date and pem.modified_at is not null order by pem.modified_at desc limit 1)
end
end ) as source_value,
(select bucket_value from risk_variable_category where
(case when rv.variable_type='categorical' and rv.is_calculated=1
and emd.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
case when rv.calc_type ='Count' then
(select count(pem.metadata_value) from person_ebi_metadata pem where pem.ebi_metadata_id=rv.ebi_metadata_id and pem.person_id=orgc.person_id and pem.modified_at between rv.calculation_start_date and rv.calculation_end_date and pem.modified_at is not null order by pem.modified_at desc )
when rv.calc_type='Most Recent' then
(select pem.metadata_value from person_ebi_metadata pem where pem.ebi_metadata_id=rv.ebi_metadata_id and pem.person_id=orgc.person_id and pem.modified_at between rv.calculation_start_date and rv.calculation_end_date and pem.modified_at is not null order by pem.modified_at desc limit 1)
end
end ) = option_value and risk_variable_id=rv.id
) as
bucket_value,
orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id #and rgph.person_id=student_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id
inner join risk_model_weights rmw on rmw.risk_model_id=rmm.id
inner join risk_variable rv on rmw.risk_variable_id=rv.id and  rv.is_calculated=1 and rv.variable_type='categorical'
inner join person_ebi_metadata emd on emd.ebi_metadata_id=rv.ebi_metadata_id
and emd.modified_at between rv.calculation_start_date and rv.calculation_end_date
where orgm.org_id=@cur_org_id
group by orgc.person_id,rv.id
        
union all
        
#ISP Query
#Variable Type Continuous & ISCalculated=1
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type as variable_type,rmw.weight as weight,rv.org_metadata_id As Source_id
,(case when rv.variable_type='continuous' and rv.is_calculated=1
and org.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
case when rv.calc_type ='Sum' then
(select sum(pom.metadata_value) from person_org_metadata pom where pom.org_metadata_id=rv.org_metadata_id and pom.person_id=orgc.person_id and pom.modified_at between rv.calculation_start_date and rv.calculation_end_date and pom.modified_at is not null order by pom.modified_at desc )
when rv.calc_type ='Average' then
(select avg(pom.metadata_value) from person_org_metadata pom where pom.org_metadata_id=rv.org_metadata_id and pom.person_id=orgc.person_id and pom.modified_at between rv.calculation_start_date and rv.calculation_end_date and pom.modified_at is not null order by pom.modified_at desc )
when rv.calc_type ='Count' then
(select count(pom.metadata_value) from person_org_metadata pom where pom.org_metadata_id=rv.org_metadata_id  and pom.person_id=orgc.person_id and pom.modified_at between rv.calculation_start_date and rv.calculation_end_date and pom.modified_at is not null order by pom.modified_at desc )
when rv.calc_type='Most Recent' then
(select pom.metadata_value from person_org_metadata pom where pom.org_metadata_id=rv.org_metadata_id and pom.person_id=orgc.person_id and pom.modified_at between rv.calculation_start_date and rv.calculation_end_date and pom.modified_at is not null order by pom.modified_at desc limit 1)
when rv.calc_type='Academic Update' then
(select count(au.id) from academic_update au where au.person_id_student=orgc.person_id and (au.modified_at between rv.calculation_start_date and rv.calculation_end_date) and (upper(au.failure_risk_level)='HIGH' or upper(au.grade) in('D','D-','D+','F','F+','F-')))
end
end ) as source_value,
(select bucket_value from risk_variable_range where
(case when rv.variable_type='continuous' and rv.is_calculated=1
and org.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
case when rv.calc_type ='Sum' then
(select sum(pom.metadata_value) from person_org_metadata pom where pom.org_metadata_id=rv.org_metadata_id and pom.person_id=orgc.person_id and pom.modified_at between rv.calculation_start_date and rv.calculation_end_date and pom.modified_at is not null order by pom.modified_at desc )
when rv.calc_type ='Average' then
(select avg(pom.metadata_value) from person_org_metadata pom where pom.org_metadata_id=rv.org_metadata_id  and pom.person_id=orgc.person_id and pom.modified_at between rv.calculation_start_date and rv.calculation_end_date and pom.modified_at is not null order by pom.modified_at desc )
when rv.calc_type ='Count' then
(select count(pom.metadata_value) from person_org_metadata pom where pom.org_metadata_id=rv.org_metadata_id  and pom.person_id=orgc.person_id and pom.modified_at between rv.calculation_start_date and rv.calculation_end_date and pom.modified_at is not null order by pom.modified_at desc )
when rv.calc_type='Most Recent' then
(select pom.metadata_value from person_org_metadata pom where pom.org_metadata_id=rv.org_metadata_id and pom.person_id=orgc.person_id and pom.modified_at between rv.calculation_start_date and rv.calculation_end_date and pom.modified_at is not null order by pom.modified_at desc limit 1)
when rv.calc_type='Academic Update' then
(select count(au.id) from academic_update au where au.person_id_student=orgc.person_id and (au.modified_at between rv.calculation_start_date and rv.calculation_end_date) and (upper(au.failure_risk_level)='HIGH' or upper(au.grade) in('D','D-','D+','F','F+','F-')))
end
end )between min and max and risk_variable_id=rv.id
) as
bucket_value ,
orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id
inner join risk_model_weights rmw on rmw.risk_model_id=rmm.id
inner join risk_variable rv on rmw.risk_variable_id=rv.id and  rv.is_calculated=1 and rv.variable_type='continuous'
inner join person_org_metadata org on org.org_metadata_id=rv.org_metadata_id
and org.modified_at between rv.calculation_start_date and rv.calculation_end_date
where orgm.org_id=@cur_org_id
group by orgc.person_id,rv.id
        
union
#Variable Type Categorical & ISCalculated=1
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type as variable_type,rmw.weight as weight,rv.org_metadata_id As source_id
,(case when rv.variable_type='categorical' and rv.is_calculated=1
and org.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
case when rv.calc_type ='Count' then
(select count(pom.metadata_value) from person_org_metadata pom where pom.org_metadata_id=rv.org_metadata_id and pom.person_id=orgc.person_id and pom.modified_at between rv.calculation_start_date and rv.calculation_end_date and pom.modified_at is not null order by pom.modified_at desc )
when rv.calc_type='Most Recent' then
(select pom.metadata_value from person_org_metadata pom where pom.org_metadata_id=rv.org_metadata_id and pom.person_id=orgc.person_id and pom.modified_at between rv.calculation_start_date and rv.calculation_end_date and pom.modified_at is not null order by pom.modified_at desc limit 1)
end
end ) as source_value,
(select bucket_value from risk_variable_category where
(case when rv.variable_type='categorical' and rv.is_calculated=1
and org.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
case when rv.calc_type ='Count' then
(select count(pom.metadata_value) from person_org_metadata pom where pom.org_metadata_id=rv.org_metadata_id and pom.person_id=orgc.person_id and pom.modified_at between rv.calculation_start_date and rv.calculation_end_date and pom.modified_at is not null order by pom.modified_at desc )
when rv.calc_type='Most Recent' then
(select pom.metadata_value from person_org_metadata pom where pom.org_metadata_id=rv.org_metadata_id and pom.person_id=orgc.person_id and pom.modified_at between rv.calculation_start_date and rv.calculation_end_date and pom.modified_at is not null order by pom.modified_at desc limit 1)
end
end )= option_value and risk_variable_id=rv.id
) as
bucket_value ,
orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id
inner join risk_model_weights rmw on rmw.risk_model_id=rmm.id
inner join risk_variable rv on rmw.risk_variable_id=rv.id and  rv.is_calculated=1 and rv.variable_type='categorical'
inner join person_org_metadata org on org.org_metadata_id=rv.org_metadata_id
and org.modified_at between rv.calculation_start_date and rv.calculation_end_date
where orgm.org_id=@cur_org_id
group by orgc.person_id,rv.id
        
Union all
        
#ISQ Query
#Variable Type Continuous & ISCalculated=1
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type as variable_type,rmw.weight as weight,rv.org_question_id As Source_id
#orgc.person_id,oqr.decimal_value,oqr.response_type
,(case when rv.variable_type='continuous' and rv.is_calculated=1
and oqr.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
case when oqr.response_type='decimal' then
(case when rv.calc_type ='Sum' then
(select sum(oqrs.decimal_value) from org_question_response oqrs where oqrs.org_question_id=rv.org_question_id and oqrs.person_id=orgc.person_id and oqrs.modified_at between rv.calculation_start_date and rv.calculation_end_date and oqrs.modified_at is not null order by oqrs.modified_at desc )
when rv.calc_type ='Average' then
(select avg(oqrs.decimal_value) from org_question_response oqrs where oqrs.org_question_id=rv.org_question_id  and oqrs.person_id=orgc.person_id and oqrs.modified_at between rv.calculation_start_date and rv.calculation_end_date and oqrs.modified_at is not null order by oqrs.modified_at desc )
when rv.calc_type ='Count' then
(select count(oqrs.decimal_value) from org_question_response oqrs where oqrs.org_question_id=rv.org_question_id  and oqrs.person_id=orgc.person_id and oqrs.modified_at between rv.calculation_start_date and rv.calculation_end_date and oqrs.modified_at is not null order by oqrs.modified_at desc )
when rv.calc_type='Most Recent' then
(select oqrs.decimal_value from org_question_response oqrs where oqrs.org_question_id=rv.org_question_id and oqrs.person_id=orgc.person_id and oqrs.modified_at between rv.calculation_start_date and rv.calculation_end_date and oqrs.modified_at is not null order by oqrs.modified_at desc limit 1)
when rv.calc_type='Academic Update' then
(select count(au.id) from academic_update au where au.person_id_student=orgc.person_id and (au.modified_at between rv.calculation_start_date and rv.calculation_end_date) and (upper(au.failure_risk_level)='HIGH' or upper(au.grade) in('D','D-','D+','F','F+','F-')))
end)
end
end ) as source_value,
(select bucket_value from risk_variable_range where
(case when rv.variable_type='continuous' and rv.is_calculated=1
and oqr.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
case when oqr.response_type='decimal' then
(case when rv.calc_type ='Sum' then
(select sum(oqr.decimal_value) from org_question_response oqr where oqr.org_question_id=rv.org_question_id and oqr.person_id=orgc.person_id and modified_at between rv.calculation_start_date and rv.calculation_end_date and oqr.modified_at is not null order by oqr.modified_at desc )
when rv.calc_type ='Average' then
(select avg(oqr.decimal_value) from org_question_response oqr where oqr.org_question_id=rv.org_question_id  and oqr.person_id=orgc.person_id and modified_at between rv.calculation_start_date and rv.calculation_end_date and oqr.modified_at is not null order by oqr.modified_at desc )
when rv.calc_type ='Count' then
(select count(oqr.decimal_value) from org_question_response oqr where oqr.org_question_id=rv.org_question_id  and oqr.person_id=orgc.person_id and modified_at between rv.calculation_start_date and rv.calculation_end_date and oqr.modified_at is not null order by oqr.modified_at desc )
when rv.calc_type='Most Recent' then
(select oqr.decimal_value from org_question_response oqr where oqr.org_question_id=rv.org_question_id and oqr.person_id=orgc.person_id and modified_at between rv.calculation_start_date and rv.calculation_end_date and oqr.modified_at is not null order by oqr.modified_at desc limit 1)
when rv.calc_type='Academic Update' then
(select count(au.id) from academic_update au where au.person_id_student=orgc.person_id and (au.modified_at between rv.calculation_start_date and rv.calculation_end_date) and (upper(au.failure_risk_level)='HIGH' or upper(au.grade) in('D','D-','D+','F','F+','F-')))
end)
end
end )between min and max and risk_variable_id=rv.id
) as
bucket_value
,orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id #and rgph.person_id=student_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id
inner join risk_model_weights rmw on rmw.risk_model_id=rmm.id
inner join risk_variable rv on rmw.risk_variable_id=rv.id and  rv.is_calculated=1 and rv.variable_type='continuous'
inner join org_question oq on oq.id=rv.org_question_id
inner join org_question_response oqr on oqr.survey_id=rv.survey_id #and svr.person_id=student_id
and oqr.org_question_id=rv.org_question_id
and oqr.modified_at between rv.calculation_start_date and rv.calculation_end_date
where orgm.org_id=@cur_org_id
group by orgc.person_id,rv.id
        
union
#Variable Type Categorical & ISCalculated=1
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type as variable_type,rmw.weight as weight,rv.org_question_id As source_id
,(case when rv.variable_type='categorical' and rv.is_calculated=1 and oqr.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
(case when rv.calc_type ='Count' then
(select count(oqrs.char_value) from org_question_response oqrs where oqrs.org_question_id=rv.org_question_id  and oqrs.person_id=orgc.person_id and oqrs.modified_at between rv.calculation_start_date and rv.calculation_end_date and oqrs.modified_at is not null order by oqrs.modified_at desc )
else(
# Most recent
case when  oqr.response_type='char'
then (select oqrs.char_value from org_question_response oqrs where oqrs.org_question_id=rv.org_question_id and oqrs.person_id=orgc.person_id and oqrs.modified_at between rv.calculation_start_date and rv.calculation_end_date and oqrs.modified_at is not null order by oqrs.modified_at desc limit 1)
when oqr.response_type='charmax'
then (select charmax_value from org_question_response oqrs where oqrs.org_question_id=rv.org_question_id and oqrs.person_id=orgc.person_id and oqrs.modified_at between rv.calculation_start_date and rv.calculation_end_date and oqrs.modified_at is not null order by oqrs.modified_at desc limit 1)
end)
end)
end)
as source_value,
(select bucket_value from risk_variable_category where
(case when rv.variable_type='categorical' and rv.is_calculated=1 and oqr.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
(case when rv.calc_type ='Count' then
(select count(oqrs.char_value) from org_question_response oqrs where oqrs.org_question_id=rv.org_question_id  and oqrs.person_id=orgc.person_id and oqrs.modified_at between rv.calculation_start_date and rv.calculation_end_date and oqrs.modified_at is not null order by oqrs.modified_at desc )
else(
# Most recent
case when  oqr.response_type='char'
then (select char_value from org_question_response oqrs where oqrs.org_question_id=rv.org_question_id and oqrs.person_id=orgc.person_id and oqrs.modified_at between rv.calculation_start_date and rv.calculation_end_date and oqrs.modified_at is not null order by oqrs.modified_at desc limit 1)
when oqr.response_type='charmax'
then (select oqrs.charmax_value from org_question_response oqrs where oqrs.org_question_id=rv.org_question_id and oqrs.person_id=orgc.person_id and oqrs.modified_at between rv.calculation_start_date and rv.calculation_end_date and oqrs.modified_at is not null order by oqrs.modified_at desc limit 1)
end)
end)
end)= option_value and risk_variable_id=rv.id
) as
bucket_value ,
orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id #and rgph.person_id=student_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id
inner join risk_model_weights rmw on rmw.risk_model_id=rmm.id
inner join risk_variable rv on rmw.risk_variable_id=rv.id and  rv.is_calculated=1 and rv.variable_type='categorical'
inner join org_question oq on oq.id=rv.org_question_id
inner join org_question_response oqr on oqr.survey_id=rv.survey_id #and svr.person_id=student_id
and oqr.org_question_id=rv.org_question_id
and oqr.modified_at between rv.calculation_start_date and rv.calculation_end_date
where orgm.org_id=@cur_org_id
group by orgc.person_id,rv.id
        
union all
        
#Survey Question query
#Variable type continuous & iscalculated=1
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type,rmw.weight,rv.survey_questions_id as source_id,
(case when rv.variable_type='continuous' and rv.is_calculated=1 and svr.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
case when svr.response_type='decimal' then
(case when rv.calc_type ='Sum' then
(select sum(sr.decimal_value) from survey_response sr where sr.survey_questions_id=rv.survey_questions_id and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc )
when rv.calc_type ='Average' then
(select avg(sr.decimal_value) from survey_response sr where sr.survey_questions_id=rv.survey_questions_id  and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc )
when rv.calc_type ='Most Recent' then
(select sr.decimal_value from survey_response sr where sr.survey_questions_id=rv.survey_questions_id and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc limit 1)
when calc_type='Academic Update' then
(select count(au.id) from academic_update au where au.person_id_student=orgc.person_id and (au.modified_at between rv.calculation_start_date and rv.calculation_end_date) and (upper(au.failure_risk_level)='HIGH' or upper(au.grade) in('D','D-','D+','F','F+','F-')))
else
#count
(select count(sr.decimal_value) from survey_response sr where sr.survey_questions_id=rv.survey_questions_id  and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc )
end)
end
end      )
as source_value,
(select bucket_value from risk_variable_range where
(case when rv.variable_type='continuous' and rv.is_calculated=1 and svr.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
case when svr.response_type='decimal' then
(case when rv.calc_type ='Sum' then
(select sum(sr.decimal_value) from survey_response sr where sr.survey_questions_id=rv.survey_questions_id and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc )
when rv.calc_type ='Average' then
(select avg(sr.decimal_value) from survey_response sr where sr.survey_questions_id=rv.survey_questions_id  and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc )
when rv.calc_type ='Most Recent' then
(select sr.decimal_value from survey_response sr where sr.survey_questions_id=rv.survey_questions_id and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc limit 1)
when calc_type='Academic Update' then
(select count(au.id) from academic_update au where au.person_id_student=orgc.person_id and (au.modified_at between rv.calculation_start_date and rv.calculation_end_date) and (upper(au.failure_risk_level)='HIGH' or upper(au.grade) in('D','D-','D+','F','F+','F-')))
else
#count
(select count(sr.decimal_value) from survey_response sr where sr.survey_questions_id=rv.survey_questions_id and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc )
end)
end
end) between min and max and risk_variable_id=rv.id
) as
bucket_value
,orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id #and rgph.person_id=student_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id
inner join risk_model_weights rmw on rmw.risk_model_id=rmm.id
inner join risk_variable rv on rmw.risk_variable_id=rv.id
inner join survey_questions svq on svq.ebi_question_id=rv.ebi_question_id
inner join survey_response svr on svr.survey_id=rv.survey_id
and svr.survey_questions_id=rv.survey_questions_id #and svr.person_id=student_id
and svr.modified_at between rv.calculation_start_date and rv.calculation_end_date
and rv.variable_type='continuous' and rv.is_calculated=1
where orgm.org_id=@cur_org_id
group by orgc.person_id,rv.id
        
union
        
#variable type categorical iscalculated=1
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type,rmw.weight,rv.survey_questions_id as source_id,
(case when rv.variable_type='categorical' and rv.is_calculated=1 and svr.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
(case when rv.calc_type ='Count' then (select count(char_value) from survey_response sr where sr.survey_questions_id=rv.survey_questions_id  and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc )
else(
# most recent
case when  svr.response_type='char'
then (select sr.char_value from survey_response sr where sr.survey_questions_id=rv.survey_questions_id and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc limit 1)
when svr.response_type='charmax'
then (select sr.charmax_value from survey_response sr where sr.survey_questions_id=rv.survey_questions_id and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc limit 1)
end
)
end)
end)
as source_value,
(select bucket_value from risk_variable_category where
(case when rv.is_calculated=1 and svr.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
(case when rv.calc_type ='Count' then (select count(char_value) from survey_response sr where sr.survey_questions_id=rv.survey_questions_id  and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by modified_at desc )
else(
# calc_type-most recent
case when svr.response_type='char'
then (select sr.char_value from survey_response sr where sr.survey_questions_id=rv.survey_questions_id and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc limit 1)
when svr.response_type='charmax'
then (select sr.charmax_value from survey_response sr where sr.survey_questions_id=rv.survey_questions_id and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc limit 1)
end
)
end)
end)
= option_value and risk_variable_id=rv.id
) as
bucket_value
,orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id #and rgph.person_id=student_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id
inner join risk_model_weights rmw on rmw.risk_model_id=rmm.id
inner join risk_variable rv on rmw.risk_variable_id=rv.id
inner join survey_questions svq on svq.ebi_question_id=rv.ebi_question_id
inner join survey_response svr on svr.survey_id=rv.survey_id
and svr.survey_questions_id=rv.survey_questions_id #and svr.person_id=student_id
and svr.modified_at between rv.calculation_start_date and rv.calculation_end_date
and rv.variable_type='categorical' and rv.is_calculated=1
where orgm.org_id=@cur_org_id
group by orgc.person_id,rv.id
        
union all
#Survey Factor Query
# Variable Type continuous & is calculated=1
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type,rmw.weight,sq.id as source_id,
(case when rv.variable_type='continuous' and rv.is_calculated=1 and svr.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
case when svr.response_type='decimal' then
(case when rv.calc_type ='Sum' then
(select sum(sr.decimal_value) from survey_response sr where sr.survey_questions_id=rv.survey_questions_id and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc )
when rv.calc_type ='Average' then
(select avg(sr.decimal_value) from survey_response sr where sr.survey_questions_id=rv.survey_questions_id  and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc )
when rv.calc_type ='Most Recent' then
(select sr.decimal_value from survey_response sr where sr.survey_questions_id=rv.survey_questions_id and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc limit 1)
when calc_type='Academic Update' then
(select count(au.id) from academic_update au where au.person_id_student=orgc.person_id and (au.modified_at between rv.calculation_start_date and rv.calculation_end_date) and (upper(au.failure_risk_level)='HIGH' or upper(au.grade) in('D','D-','D+','F','F+','F-')))
else
#count
(select count(sr.decimal_value) from survey_response sr where sr.survey_questions_id=rv.survey_questions_id  and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc )
end)
end
end      )
as source_value,
(select bucket_value from risk_variable_range where
(case when rv.variable_type='continuous' and rv.is_calculated=1 and svr.modified_at between rv.calculation_start_date and rv.calculation_end_date
then
case when svr.response_type='decimal' then
(case when rv.calc_type ='Sum' then
(select sum(sr.decimal_value) from survey_response sr where sr.survey_questions_id=rv.survey_questions_id and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc )
when rv.calc_type ='Average' then
(select avg(sr.decimal_value) from survey_response sr where sr.survey_questions_id=rv.survey_questions_id  and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc )
when rv.calc_type ='Most Recent' then
(select sr.decimal_value from survey_response sr where sr.survey_questions_id=rv.survey_questions_id and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc limit 1)
when calc_type='Academic Update' then
(select count(au.id) from academic_update au where au.person_id_student=orgc.person_id and (au.modified_at between rv.calculation_start_date and rv.calculation_end_date) and (upper(au.failure_risk_level)='HIGH' or upper(au.grade) in('D','D-','D+','F','F+','F-')))
else
#count
(select count(sr.decimal_value) from survey_response sr where sr.survey_questions_id=rv.survey_questions_id  and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc )
end)
end
end      ) between min and max and risk_variable_id=rv.id
) as
bucket_value
,orgc.person_id
from risk_group_person_history rgph
inner join org_riskval_calc_inputs orgc on rgph.person_id=orgc.person_id
inner join org_risk_group_model orgm on rgph.risk_group_id=orgm.risk_group_id #and rgph.person_id=student_id
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id inner join
risk_model_weights rmw
on rmw.risk_model_id=rmm.id inner join
risk_variable rv on rmw.risk_variable_id=rv.id inner join
factor_questions fq on fq.factor_id=rv.factor_id inner join
survey_questions sq on sq.survey_id=rv.survey_id and (sq.ebi_question_id=fq.ebi_question_id or sq.id=fq.survey_questions_id ) inner join
survey_response svr on svr.survey_id=rv.survey_id
and svr.survey_questions_id=sq.id #and svr.person_id=student_id
and svr.modified_at between rv.calculation_start_date and rv.calculation_end_date
and rv.variable_type='continuous' and rv.is_calculated=1
where orgm.org_id=@cur_org_id
group by orgc.person_id,rv.id
)cur_org
);
end;
    
CDATA;
        $this->addSql($variables_query);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
    }
}
