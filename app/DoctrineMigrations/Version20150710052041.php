<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150710052041 extends AbstractMigration
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
        update ebi_search SET `query` = 'SELECT P.id AS student, OC.org_academic_year_id, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, LC.cnt, OPS.status, OPS.photo_url FROM person P LEFT JOIN org_course_student OCS ON OCS.person_id = P.id LEFT JOIN org_courses OC ON OC.id = OCS.org_courses_id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN Logins_count LC ON (LC.person_id = P.id) LEFT JOIN person_ebi_metadata AS PEM ON PEM.person_id = P.id where OC.deleted_At IS NULL AND OCS.deleted_at IS NULL AND OC.org_academic_year_id = $yearId AND PEM.ebi_metadata_id IN (SELECT id FROM ebi_metadata WHERE meta_key="ClassLevel") AND P.id IN ($personIds ) GROUP BY P.id' WHERE  `query_key` = 'Class_Level';
CDATA;
        $this->addSql($query);
        
    }
    
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    
    }
}
