<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150805133325 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $drop_procedure_query = <<<CDATA
DROP PROCEDURE IF EXISTS Talking_Point_Calc;
CDATA;
        $this->addSql($drop_procedure_query);
        
        $calculation_query = <<<CDATA
CREATE PROCEDURE Talking_Point_Calc()
BEGIN
#Talking point calc
# check if there are any org_ids for which talking point calculation has to be done
select count(*) into @countorgid from org_riskval_calc_inputs where is_talking_point_calc_reqd = 'y';
if ((@countorgid is not NULL) and (@countorgid > 0)) then
                
replace into org_talking_points(organization_id,person_id,talking_points_id,survey_id,response)
select orc.org_id,orc.person_id,tp.id as talking_points_id,svr.survey_id,tp.talking_points_type as response 
from  talking_points tp inner join 
survey_questions svq on tp.ebi_question_id=svq.ebi_question_id
inner join survey_response svr on svq.id=svr.survey_questions_id
and (case when svr.response_type='decimal'then svr.decimal_value end) between tp.min_range and tp.max_range
inner join org_riskval_calc_inputs orc on svr.person_id=orc.person_id and svr.org_id=orc.org_id and orc.is_talking_point_calc_reqd='y'
inner join org_person_student ops on orc.person_id=ops.person_id
inner join org_person_student_survey_link opssl  on ops.surveycohort=opssl.cohort and opssl.survey_id=svr.survey_id
#where  orc.org_id = @orgId
union 
select 
orc.org_id,orc.person_id,tp.id as talking_points_id,null as survey_id,tp.talking_points_type as response 
from  talking_points tp inner join 
person_ebi_metadata pem on tp.ebi_metadata_id=pem.ebi_metadata_id
and metadata_value between tp.min_range and tp.max_range
inner join org_riskval_calc_inputs orc on pem.person_id=orc.person_id and orc.is_talking_point_calc_reqd='y'
#where orc.org_id = @orgId
;
end if;
#update input table after calculation
update org_riskval_calc_inputs set is_talking_point_calc_reqd='n';
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
