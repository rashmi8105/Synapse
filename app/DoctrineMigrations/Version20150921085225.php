<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150921085225 extends AbstractMigration
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
        $termsId = '$$termsId$$';

        $query = <<<CDATA
   update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id , P.id AS student,  P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM person P LEFT JOIN org_course_student OCS ON OCS.person_id = P.id LEFT JOIN org_courses OC ON OC.id = OCS.org_courses_id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN academic_update AU ON (AU.person_id_student = P.id) LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) where OC.deleted_at IS NULL AND OCS.deleted_at IS NULL AND OC.org_academic_terms_id IN ($termsId) AND P.id IN ($personIds ) AND AU.grade IN ("C","D", "F", "No Pass","F/No Pass") AND AU.update_date BETWEEN "$startDate" AND "$endDate" GROUP BY P.id' WHERE  `query_key` = 'In-progress_Grade_Of_C_Or_Below';
CDATA;
        $this->addSql($query);
        
        $query = <<<CDATA
   update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id , P.id AS student,  P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM person P LEFT JOIN org_course_student OCS ON OCS.person_id = P.id LEFT JOIN org_courses OC ON OC.id = OCS.org_courses_id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN academic_update AU ON (AU.person_id_student = P.id) LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) where OC.deleted_at IS NULL AND OCS.deleted_at IS NULL AND OC.org_academic_terms_id IN ($termsId) AND P.id IN ($personIds ) AND AU.final_grade IN ("C", "C-", "D+", "D","D-","F", "No Pass","F/No Pass") AND AU.update_date BETWEEN "$startDate" AND "$endDate" GROUP BY P.id' WHERE  `query_key` = 'Final_Grade_Of_C_Or_Below';
CDATA;
        $this->addSql($query);
        
        $query = <<<CDATA
   update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id , P.id AS student,  P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM person P LEFT JOIN org_course_student OCS ON OCS.person_id = P.id LEFT JOIN org_courses OC ON OC.id = OCS.org_courses_id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN academic_update AU ON (AU.person_id_student = P.id) LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) where OC.deleted_at IS NULL AND OCS.deleted_at IS NULL AND OC.org_academic_terms_id IN ($termsId) AND P.id IN ($personIds ) AND  AU.grade IN ("D", "F", "No Pass","F/No Pass") AND AU.update_date BETWEEN "$startDate" AND "$endDate" GROUP BY P.id' WHERE  `query_key` = 'In-progress_Grade_Of_D_Or_Below';
CDATA;
        $this->addSql($query);
        
        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id , P.id AS student,  P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM person P LEFT JOIN org_course_student OCS ON OCS.person_id = P.id LEFT JOIN org_courses OC ON OC.id = OCS.org_courses_id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN academic_update AU ON (AU.person_id_student = P.id) LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) where OC.deleted_at IS NULL AND OCS.deleted_at IS NULL AND OC.org_academic_terms_id IN ($termsId) AND P.id IN ($personIds ) AND AU.final_grade IN ("D" ,"D-","F", "No Pass", "F/No Pass") AND AU.update_date BETWEEN "$startDate" AND "$endDate" GROUP BY P.id' WHERE  `query_key` = 'Final_Grade_Of_D_Or_Below';
CDATA;
        $this->addSql($query);
        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS T.student ,T.* , count(T.student) as t , (select count(*) from activity_log where person_id_student = T.student AND deleted_at IS NULL group BY person_id_student) as cnt from ( SELECT P.id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, OPS.status, OPS.photo_url, pem.metadata_value AS class_level FROM synapse.academic_update AS AU LEFT JOIN person as P ON AU.person_id_student = P.id LEFT JOIN org_courses OC ON OC.id = AU.org_courses_id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl ON (acl.person_id_student = P.id) LEFT JOIN person_ebi_metadata AS pem ON (pem.person_id = P.id AND pem.ebi_metadata_id IN (SELECT id FROM ebi_metadata WHERE meta_key ="ClassLevel")) where AU.person_id_student IN($personIds) AND (AU.grade IS NOT NULL) AND AU.deleted_at IS NULL AND OC.deleted_at IS NULL AND OC.org_academic_terms_id IN ($termsId) AND AU.update_date BETWEEN "$startDate" AND "$endDate" AND AU.grade IN ("D", "F", "No Pass","F/No Pass") GROUP BY AU.org_courses_id,P.id ) AS T GROUP BY T.student HAVING T > 1' WHERE  `query_key` = 'Students_With_More_Than_One_In-progress_Grade_Of_D_Or_Below';
CDATA;
        $this->addSql($query);
        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS T.student ,T.* , count(T.student) as t , (select count(*) from activity_log where person_id_student = T.student AND deleted_at IS NULL group BY person_id_student) as cnt from ( SELECT P.id AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, OPS.status, OPS.photo_url, pem.metadata_value AS class_level FROM synapse.academic_update AS AU LEFT JOIN person as P ON AU.person_id_student = P.id LEFT JOIN org_courses OC ON OC.id = AU.org_courses_id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl ON (acl.person_id_student = P.id) LEFT JOIN person_ebi_metadata AS pem ON (pem.person_id = P.id AND pem.ebi_metadata_id IN (SELECT id FROM ebi_metadata WHERE meta_key ="ClassLevel")) where AU.person_id_student IN($personIds) AND (AU.final_grade IS NOT NULL) AND AU.deleted_at IS NULL AND OC.deleted_at IS NULL  AND OC.org_academic_terms_id IN ($termsId) AND  AU.update_date BETWEEN "$startDate" AND "$$endDate" AND AU.final_grade IN  ("D" ,"D-","F", "No Pass","F/No Pass") GROUP BY AU.org_courses_id,P.id ) AS T GROUP BY T.student HAVING T > 1' WHERE  `query_key` = 'Students_With_More_Than_One_Final_Grade_Of_D_Or_Below';
CDATA;
        $this->addSql($query);
         
    
    }
    
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    
    }
}
