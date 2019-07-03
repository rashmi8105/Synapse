<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150806181744 extends AbstractMigration
{
 public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $drop_procedure_query = <<<CDATA
DROP PROCEDURE IF EXISTS Factor_Calc;
CDATA;
        $this->addSql($drop_procedure_query);
        
        $calculation_query = <<<CDATA
CREATE PROCEDURE Factor_Calc()
BEGIN
#Factor Calc
# check if there are any org_ids for which Factor calculation has to be done
select count(*) into @countorgid from org_riskval_calc_inputs where is_factor_calc_reqd = 'y';
if ((@countorgid is not NULL) and (@countorgid > 0)) then
replace into person_factor_calculated(organization_id,person_id,factor_id,mean_value)
select svr.org_id,svr.person_id,fq.factor_id,avg(svr.decimal_value) as mean_value
from factor_questions fq 
inner join survey_questions svq on (svq.ebi_question_id=fq.ebi_question_id or svq.id=fq.survey_questions_id)
inner join survey_response svr on svr.survey_questions_id=svq.id
inner join org_riskval_calc_inputs orc on svr.person_id=orc.person_id and svr.org_id=orc.org_id and is_factor_calc_reqd='y'
# where orc.org_id = @orgId
group by fq.factor_id,svr.person_id;
end if;
#update input table after calculation
update org_riskval_calc_inputs set is_factor_calc_reqd='n';
END;
CDATA;
        $this->addSql($calculation_query);
        
        $drop_procedure_query = <<<CDATA
DROP PROCEDURE IF EXISTS Success_Marker_Calc;
CDATA;
        $this->addSql($drop_procedure_query);
        
        $calculation_query = <<<CDATA
CREATE PROCEDURE Success_Marker_Calc()
BEGIN
#Success Marker calc
# check if there are any org_ids for which Success Marker calculation has to be done
                select count(*) into @countorgid from org_riskval_calc_inputs where is_success_marker_calc_reqd = 'y';
if ((@countorgid is not NULL) and (@countorgid > 0)) then
replace into success_marker_calculated(organization_id,person_id,marker_id,color)
select orc.org_id,pfc.person_id,smq.surveymarker_id,
case when pfc.mean_value between red_low and red_high then 'red'
         when pfc.mean_value between yellow_low and yellow_high then 'yellow'
         when pfc.mean_value between green_low and green_high then 'green' end as color 
#,  smq.factor_id, pfc.mean_value
from surveymarker_questions smq inner join person_factor_calculated pfc on smq.factor_id=pfc.factor_id
and (pfc.mean_value between red_low and red_high or
                pfc.mean_value between yellow_low and yellow_high or
                pfc.mean_value between green_low and green_high) 
and smq.ebi_question_id is null and smq.survey_questions_id is null and smq.factor_id is not null
inner join org_riskval_calc_inputs orc on pfc.person_id=orc.person_id and pfc.organization_id=orc.org_id
and orc.is_success_marker_calc_reqd='y'
# where orc.org_id = @orgId
group by smq.surveymarker_id
union
select orc.org_id,svr.person_id,smq.surveymarker_id,
case when svr.decimal_value between red_low and red_high then 'red'
         when svr.decimal_value between yellow_low and yellow_high then 'yellow'
         when svr.decimal_value between green_low and green_high then 'green' end as color 
#,  smq.ebi_question_id, svr.decimal_value
from  surveymarker_questions smq inner join 
survey_questions svq on smq.ebi_question_id=svq.ebi_question_id
inner join survey_response svr on svq.id=svr.survey_questions_id
and (svr.decimal_value between red_low and red_high or
                svr.decimal_value between yellow_low and yellow_high or
                svr.decimal_value between green_low and green_high) 
and smq.ebi_question_id is not null and smq.survey_questions_id is null and smq.factor_id is null
inner join org_riskval_calc_inputs orc on svr.person_id=orc.person_id and orc.is_success_marker_calc_reqd='y'
inner join org_person_student ops on orc.person_id=ops.person_id
inner join org_person_student_survey_link opssl  on ops.surveycohort=opssl.cohort and opssl.survey_id=svr.survey_id
# where orc.org_id = @orgId
group by smq.surveymarker_id
union
select orc.org_id,svr.person_id,smq.surveymarker_id,
case when svr.decimal_value between red_low and red_high then 'red'
         when svr.decimal_value between yellow_low and yellow_high then 'yellow'
         when svr.decimal_value between green_low and green_high then 'green' end as color 
#,  smq.survey_questions_id, svr.decimal_value
from  surveymarker_questions smq inner join 
 survey_response svr on smq.survey_questions_id=svr.survey_questions_id
and (svr.decimal_value between red_low and red_high or
                svr.decimal_value between yellow_low and yellow_high or
                svr.decimal_value between green_low and green_high) 
and smq.ebi_question_id is  null and smq.survey_questions_id is not null and smq.factor_id is null
inner join org_riskval_calc_inputs orc on svr.person_id=orc.person_id and orc.is_success_marker_calc_reqd='y'
inner join org_person_student ops on orc.person_id=ops.person_id
inner join org_person_student_survey_link opssl  on ops.surveycohort=opssl.cohort and opssl.survey_id=svr.survey_id
# where orc.org_id = @orgId
group by smq.surveymarker_id;
end if;
#update input table after calculation
update org_riskval_calc_inputs set is_success_marker_calc_reqd='n';
END;
CDATA;
        $this->addSql($calculation_query);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
