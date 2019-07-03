<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150618123735 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        /* drop if the SP exists */
        $drop_procedure_query = <<<CDATA
DROP PROCEDURE IF EXISTS CreateTemptables;DROP PROCEDURE IF EXISTS cur_org_calculated_rv;DROP PROCEDURE IF EXISTS RiskFactorCalculation;
CDATA;
        $this->addSql($drop_procedure_query);
        
        /*
        source file - temptablesproc.sql
        cur_org_calculated_risk_variable : table to store the intermediate risk values on a per org basis.
        This will also help improve performance, since org_calculated_risk_variable can have large volume of data
        
        riskfactorcalc : temp table to store the calculated risk values on a per org basis
        */
        
        $temptables_query = <<<CDATA
CREATE PROCEDURE CreateTemptables() BEGIN DROP TABLE IF EXISTS riskfactorcalc; CREATE TABLE `riskfactorcalc` ( `person_id` bigint(20) DEFAULT NULL, `risk_model_id` bigint(20) DEFAULT NULL, `Numerator` decimal(40,4) DEFAULT NULL, `Denominator` decimal(30,4) DEFAULT NULL, `Risk_Score` decimal(48,8) DEFAULT NULL, `risk_level` int(11), `risk_text` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `image_name` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `color_hex` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8; DROP TABLE IF EXISTS cur_org_calculated_risk_variable; CREATE TABLE `cur_org_calculated_risk_variable` ( `org_id` int(11) DEFAULT NULL, `risk_model_id` bigint(20) DEFAULT NULL, `source` varchar(14) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `risk_variable_id` bigint(20) DEFAULT NULL, `variable_type` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `weight` decimal(8,4) DEFAULT NULL, `source_id` int(11) DEFAULT NULL, `SourceValue` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci, `bucket_value` int(11) DEFAULT NULL, `person_id` bigint(20) DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
END;
CDATA;
        $this->addSql($temptables_query);
        
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

union
#Variable Type Categorical & ISCalculated=0
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type,rmw.weight,fq.survey_questions_id as source_id
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
inner join factor_questions fq on  fq.id=rv.factor_id 
inner join survey_questions svq on svq.ebi_question_id=fq.ebi_question_id
inner join survey_response svr on svr.survey_id=rv.survey_id and svr.person_id=orgc.person_id
and svr.survey_questions_id=fq.survey_questions_id
and(case  when svr.response_type='char' then svr.char_value else  
                                 svr.charmax_value end) =rvr.option_value
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

union 
# Variable Type categorical & is calculated=1
select orgm.org_id,rmm.id as risk_model_id,rv.source,rv.id as risk_variable_id,rv.variable_type,rmw.weight,sq.id as source_id,
(case when rv.variable_type='categorical' and rv.is_calculated=1 and svr.modified_at between rv.calculation_start_date and rv.calculation_end_date 
then
(case when rv.calc_type ='Count' then (select count(sr.char_value) from survey_response sr where sr.survey_questions_id=rv.survey_questions_id  and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc )
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
(case when rv.calc_type ='Count' then (select count(sr.char_value) from survey_response sr where sr.survey_questions_id=rv.survey_questions_id  and sr.person_id=orgc.person_id and sr.modified_at between rv.calculation_start_date and rv.calculation_end_date and sr.modified_at is not null order by sr.modified_at desc )
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
inner join risk_model_master rmm on orgm.risk_model_id=rmm.id inner join 
risk_model_weights rmw 
on rmw.risk_model_id=rmm.id inner join
risk_variable rv on rmw.risk_variable_id=rv.id inner join 
factor_questions fq on fq.factor_id=rv.factor_id inner join 
survey_questions sq on sq.survey_id=rv.survey_id and (sq.ebi_question_id=fq.ebi_question_id or sq.id=fq.survey_questions_id ) 
inner join survey_response svr on svr.survey_id=rv.survey_id
and svr.survey_questions_id=sq.id #and svr.person_id=student_id 
and svr.modified_at between rv.calculation_start_date and rv.calculation_end_date 
and rv.variable_type='categorical' and rv.is_calculated=1
where orgm.org_id=@cur_org_id
group by orgc.person_id,rv.id
)cur_org
);
end;   
       
CDATA;
        $this->addSql($variables_query);
        
        /* source file - RiskFactorCalculation_V1.sql
         -> Loop/get List of distinct org id's where Flag= Y
        -> Get list of students per Org for whom risk calculation to be done.
        -> Sort students list based on Risk group (so that the number of round trips to DB for fetching risk model for each student can be reduced)
        -> Store the intermediate risk values on per org basis into a temp table
        -> Calculate the risk values and update all relevant tables
        -> Update the flag='N' to indicate that the processing is done
        -> Truncate the per org intermediate risk values data
        */
        $calculation_query = <<<CDATA
Create Procedure RiskFactorCalculation()
Begin
INSERT INTO messages (message,created_at) values('2. --> CreateTemptables2',now()) ;
#Create Temporary Tables used for updations
call CreateTemptables();
INSERT INTO messages (message,created_at) values('3. <-- CreateTemptables2',now()) ;
select count(*) into @countorgid from org_riskval_calc_inputs;
INSERT INTO messages (message,created_at) values(concat('4. Count Got from org_riskval_calc_inputs = ' , convert(@countorgid,char(10))),now()) ;
if @countorgid is not NULL then
SELECT org_id into @cur_org_id FROM org_riskval_calc_inputs where is_riskval_calc_required = 'y' group by org_id order by org_id limit 1;
INSERT INTO messages (message,created_at) values(concat('5. Org_Id for which risk calc to be done = ', convert( @cur_org_id,char(10))), now()) ;
while (@cur_org_id IS NOT NULL) dO
#Inserts data into temporary table to update cur_org_calculated_risk_variable table
INSERT INTO messages (message,created_at) values(concat('6. --> cur_org_calculated_rv2 with org_id = ', convert( @cur_org_id,char(10))),now()) ;
CALL cur_org_calculated_rv(@cur_org_id);
INSERT INTO messages (message,created_at) values(concat('7. <-- cur_org_calculated_rv2 with org_id = ' , convert(@cur_org_id,char(10)), '; inserting data into riskfactorcalc table'), now()) ;
Insert into riskfactorcalc(person_id,risk_model_id,Numerator,Denominator,Risk_Score,risk_level,risk_text,image_name,color_hex)
(SELECT
               all_models.person_id,
				all_models.risk_model_id,
				all_models.Numerator,
				all_models.Denominator,
				((all_models.Numerator)/(all_models.Denominator))As Risk_Score,
				RML.risk_level,
                RL.risk_text, RL.image_name, RL.color_hex
				#,all_models.source
FROM
(
SELECT
					WRV.person_id,
					SUM(WRV.weight*WRV.bucket_value) AS Numerator,
					SUM(WRV.weight) AS Denominator,
					WRV.risk_model_id,
					WRV.risk_variable_id,
					WRV.source
                FROM cur_org_calculated_risk_variable
AS WRV
                GROUP BY person_id, risk_model_id
) AS all_models
LEFT JOIN risk_model_levels AS RML
                ON RML.risk_model_id=all_models.risk_model_id
    AND ((all_models.Numerator)/(all_models.Denominator)) BETWEEN RML.min AND RML.max
LEFT JOIN risk_level AS RL
                ON RL.id=RML.risk_level
#WHERE person_id=@person
ORDER BY risk_score DESC
LIMIT 50);-- #Get the 50 most at-risk students
        
INSERT INTO messages (message,created_at) values('8. Updating person_risk_level_history', now()) ;
        
/*
-> Update person_risk_level_history,person,org_calculated_risk_variable,org_calculated_risk_variables and org_riskval_calc_inputs tables
*/
SET @modified_dt := current_date;
# update table person_risk_level_history
Replace into person_risk_level_history(person_id,date_captured,risk_model_id,risk_level,risk_score,weighted_value,maximum_weight_value)
select person_id,@modified_dt,risk_model_id,risk_level,risk_score,numerator,denominator from riskfactorcalc;
        
#update table person
INSERT INTO messages (message,created_at) values('9. Updating person', now()) ;
SET SQL_SAFE_UPDATES = 0;
update person p inner join riskfactorcalc rfc on p.id=rfc.person_id  set p.risk_level=rfc.risk_level;
# update table orgriskval_calc_inputs
INSERT INTO messages (message,created_at) values('10. Updating org_riskval_calc_inputs', now()) ;
update org_riskval_calc_inputs orgc inner join riskfactorcalc rfc on orgc.person_id=rfc.person_id set is_riskval_calc_required='n', modified_at=@modified_dt;
# update table org_calculated_risk_variables
INSERT INTO messages (message,created_at) values('11. Updating org_calculated_risk_variables', now()) ;
insert into org_calculated_risk_variables
(select org_id,person_id,risk_variable_id,bucket_value,(bucket_value*weight) as calc_weight,risk_model_id,SourceValue from cur_org_calculated_risk_variable);
#To fetch org_id
set @cur_org_id := null;
SELECT org_id into @cur_org_id FROM org_riskval_calc_inputs where is_riskval_calc_required = 'y' group by org_id order by org_id limit 1;
if @cur_org_id is not null then
INSERT INTO messages (message,created_at) values(concat('12. Next Org_Id for which risk calc to be done = ', convert( @cur_org_id,char(10))), now()) ;
else
INSERT INTO messages (message,created_at) values('12. NO MORE Org_Ids for risk calc, so quitting', now()) ;
end if;
truncate table cur_org_calculated_risk_variable;
INSERT INTO messages (message,created_at) values('13. truncating cur_org_calculated_risk_variable done', now()) ;
END WHILE;
#Drop Temporary Tables
INSERT INTO messages (message,created_at) values('14. Dropping temp tables', now()) ;
Drop table riskfactorcalc;
Drop table cur_org_calculated_risk_variable;
#else
#return 1;
end if;
end ;
CDATA;
        $this->addSql($calculation_query);
        
        /* source file - RiskCalcScheduledEventCreation.sql
        -- Script to create scheduled event for triggering risk calculation periodically.
                -- This has to be run only once to create the event.
                -- Periodicity should be given in the event creation statement
                */
                $event_query = <<<CDATA
                drop EVENT if exists event_risk_calc;
        
                -- Create temp table for debug purposes, containing the debug messages
                CREATE TABLE IF NOT EXISTS messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    message VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL
);
SET GLOBAL event_scheduler = on;
CREATE EVENT event_risk_calc
    ON SCHEDULE EVERY 1 hour
	STARTS CURRENT_TIMESTAMP
	DO
		INSERT INTO messages (message,created_at) values('1. --> event_risk_calcinsert',now()) ;
      CALL RiskFactorCalculation();
INSERT INTO messages (message,created_at) values('15. <-- event_risk_calcinsert', now()) ;
CDATA;
        $this->addSql($event_query);
                
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
    }
}
