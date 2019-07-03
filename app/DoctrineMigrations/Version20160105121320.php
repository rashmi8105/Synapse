<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160105121320 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');


        $facultyIds = '$$facultyIds$$';
        $facultyId = '$$facultyId$$';
        $orgId = '$$orgId$$';
        $yearId = '$$yearId$$';
        $yearText = '$$yearText$$';
        $personIds = '$$personIds$$';
        $startDate = '$$startDate$$';
        $endDate = '$$endDate$$';
        
        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id , P.id AS student,  P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM person P LEFT JOIN  risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) where   P.id IN ($personIds ) and OPS.surveycohort IS NOT NULL and OPS.receivesurvey = 1 and OPS.status = 1 and OPS.deleted_at IS NULL AND P.id NOT IN (SELECT DISTINCT(person_id) FROM survey_response AS SR WHERE SR.org_id = $orgId AND SR.org_academic_year_id = $yearId AND SR.survey_id IN (SELECT DISTINCT(survey_id) FROM wess_link WHERE year_id = $yearText AND status= "launched" AND org_id = $orgId)) GROUP BY P.id' WHERE  `query_key` = 'Non_Respondents_To_Current_Survey';
CDATA;
        $this->addSql($query);
        
    }
    
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    
    }
}
