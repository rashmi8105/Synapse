<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160216124059 extends AbstractMigration
{

    /**
     *
     * @param Schema $schema            
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        /*
         * Migration script to remove risk_model_id and its related join statements
         * to fix gateway timed out issue in case of query_key='In-progress_Grade_Of_C_Or_Below since'
         * since it is not being used in UI
         */
        $personIds = '$$personIds$$';
        $startDate = '$$startDate$$';
        $endDate = '$$endDate$$';
        $termsId = '$$termsId$$';
        
        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id , P.id AS student,  P.firstname, P.lastname,
        P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, 
        IL.image_name AS intent_to_leave_image,P.last_activity, OPS.surveycohort AS student_cohort, 
        (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM person P
        LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN 
        ( Select id from ebi_metadata where meta_key ="ClassLevel"))  
        LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id 
        LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id 
        LEFT JOIN activity_log acl on (acl.person_id_student = P.id) 
        LEFT JOIN org_course_student OCS ON OCS.person_id = P.id 
        LEFT JOIN org_courses OC ON OC.id = OCS.org_courses_id 
        LEFT JOIN academic_update AU ON (AU.person_id_student = P.id) 
        LEFT JOIN risk_level AS RL ON P.risk_level = RL.id 
        where OC.deleted_at IS NULL AND OCS.deleted_at IS NULL AND OC.org_academic_terms_id IN ($termsId) 
        AND P.id IN ($personIds ) AND AU.grade IN ("C","D", "F", "No Pass","F/No Pass") 
        AND AU.update_date BETWEEN "$startDate" AND "$endDate" GROUP BY P.id' WHERE  `query_key` = 'In-progress_Grade_Of_C_Or_Below';
CDATA;
        $this->addSql($query);
    }

    /**
     *
     * @param Schema $schema            
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
    }
}
