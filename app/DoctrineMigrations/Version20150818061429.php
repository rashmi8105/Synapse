<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150818061429 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $facultyIds = '$$facultyIds$$';
        $facultyId = '$$facultyId$$';
        $orgId = '$$orgId$$';
        $yearId = '$$yearId$$';
        $yearText = '$$yearText$$';
        $personIds = '$$personIds$$';
        $startDate = '$$startDate$$';
        $endDate = '$$endDate$$';
        
     
        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id , P.id AS student,  P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM person P LEFT JOIN  risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) where   P.id IN ($personIds ) GROUP BY P.id' WHERE  `query_key` = 'All_My_Student';
CDATA;
        $this->addSql($query);
        
        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id , P.id AS student,  P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM person P LEFT JOIN  risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) where   OPS.person_id_primary_connect = $facultyId AND P.id IN ($personIds ) GROUP BY P.id' WHERE  `query_key` = 'My_Primary_Campus_Connection';
CDATA;
        $this->addSql($query);
        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id , P.id AS student,  P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM person P LEFT JOIN  risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN person_ebi_metadata AS PEM ON PEM.person_id = P.id LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) where   PEM.ebi_metadata_id IN (SELECT id FROM ebi_metadata WHERE meta_key="ClassLevel") AND P.id IN ($personIds ) GROUP BY P.id' WHERE  `query_key` = 'Class_Level';
CDATA;
        $this->addSql($query);
        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id , P.id AS student,  P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM person P LEFT JOIN  risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) where   P.risk_level IN(1,2) AND P.id IN ($personIds ) GROUP BY P.id' WHERE  `query_key` = 'At_Risk';
CDATA;
        $this->addSql($query);
        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id , P.id AS student,  P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM person P LEFT JOIN  risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) where   P.risk_level IN(1,2) AND (P.risk_update_date IS NOT NULL AND P.risk_update_date > P.last_contact_date) AND P.id IN ($personIds ) GROUP BY P.id' WHERE  `query_key` = 'High_Priority_Students';
CDATA;
        $this->addSql($query);
        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id , P.id AS student,  P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM person P LEFT JOIN  risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN survey_response AS SR ON SR.person_id = P.id LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) where   P.id IN ($personIds ) AND SR.survey_id IN (SELECT DISTINCT(survey_id) FROM wess_link WHERE year_id = $yearText AND status= "launched" AND org_id = $orgId) GROUP BY P.id ' WHERE  `query_key` = 'Respondents_To_Current_Survey';
CDATA;
        $this->addSql($query);
        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id , P.id AS student,  P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM person P LEFT JOIN  risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN survey_response AS SR ON SR.person_id = P.id LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) where   P.id IN ($personIds ) AND P.id NOT IN (SELECT DISTINCT(person_id) FROM survey_response AS SR WHERE SR.org_id = $orgId AND SR.org_academic_year_id = $yearId AND SR.survey_id IN (SELECT DISTINCT(survey_id) FROM wess_link WHERE year_id = $yearText AND status= "launched" AND org_id = $orgId)) GROUP BY P.id' WHERE  `query_key` = 'Non_Respondents_To_Current_Survey';
CDATA;
        $this->addSql($query);
        
        $query = <<<CDATA
   update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id , P.id AS student,  P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM person P LEFT JOIN  risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN org_survey_report_access_history AS SR ON SR.person_id = P.id LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) where   P.id IN ($personIds ) AND SR.survey_id IN (SELECT DISTINCT(survey_id) FROM wess_link WHERE year_id = $yearText AND status= "launched" AND org_id = $orgId) AND SR.deleted_at IS NULL GROUP BY P.id' WHERE  `query_key` = 'Accessed_Current_Survey_Report';
CDATA;
        $this->addSql($query);
        
        $query = <<<CDATA
   update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id ,  P.id AS student,  P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM person P LEFT JOIN  risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) where   P.id IN ($personIds ) AND P.id NOT IN (SELECT DISTINCT(person_id) FROM org_survey_report_access_history AS SR WHERE SR.org_id = $orgId AND SR.year_id = $yearText AND SR.survey_id IN (SELECT DISTINCT(survey_id) FROM wess_link WHERE year_id = $yearText AND status= "launched" AND org_id = $orgId) AND SR.deleted_at IS NULL) GROUP BY P.id' WHERE  `query_key` = 'Not_Accessed_Current_Survey_Report';
CDATA;
        $this->addSql($query);
        
        $query = <<<CDATA
   update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id , P.id AS student,  P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM person P LEFT JOIN  risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) where   P.id IN ($personIds ) AND P.intent_to_leave = 1 GROUP BY P.id' WHERE  `query_key` = 'High_Intent_To_Leave';
CDATA;
        $this->addSql($query);
        
        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS C.person_id_student , C.person_id_faculty AS faculty, C.person_id_student AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM activity_log AS AL LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN person AS P ON C.person_id_student = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category AS AC ON C.activity_category_id = AC.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN contact_types AS CONT ON C.contact_types_id = CONT.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = C.person_id_student LEFT JOIN org_course_student AS OCS ON OCS.person_id = C.person_id_student LEFT JOIN org_courses AS OC ON (OCS.org_courses_id = OC.id) LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) WHERE (CONT.parent_contact_types_id = 1 OR CONT.id = 1) AND C.deleted_at IS NULL AND  C.person_id_student IN ($personIds) AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.contacts_id = ALOG.contacts_id WHERE related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultyId) ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $facultyId  ELSE C.access_public = 1 END END GROUP BY C.id' WHERE  `query_key` = 'Interaction_Activity';
CDATA;
        $this->addSql($query);
        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS C.person_id_student , C.person_id_faculty AS faculty, C.person_id_student AS student, P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM activity_log AS AL LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN person AS P ON C.person_id_student = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category AS AC ON C.activity_category_id = AC.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN contact_types AS CONT ON C.contact_types_id = CONT.id LEFT JOIN risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = C.person_id_student LEFT JOIN org_course_student AS OCS ON OCS.person_id = C.person_id_student LEFT JOIN org_courses AS OC ON (OCS.org_courses_id = OC.id) LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) WHERE (CONT.parent_contact_types_id = 2 OR CONT.id = 2) AND C.deleted_at IS NULL AND  C.person_id_student IN ($personIds) AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.contacts_id = ALOG.contacts_id WHERE related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultyId) ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $facultyId ELSE C.access_public = 1 END END GROUP BY C.id' WHERE  `query_key` = 'Non-interaction_Activity';
CDATA;
        $this->addSql($query);
        $query = <<<CDATA
        update ebi_search SET `query` = 'SELECT SQL_CALC_FOUND_ROWS P.id , P.id AS student,  P.firstname, P.lastname, P.risk_level, RL.risk_text, RL.image_name, IL.text AS intent_to_leave_text, IL.image_name AS intent_to_leave_image, RML.risk_model_id, P.last_activity, OPS.surveycohort AS student_cohort, (count(distinct (acl.id))) as cnt, OPS.status, OPS.photo_url,pem.metadata_value as class_level FROM person P LEFT JOIN  risk_level AS RL ON P.risk_level = RL.id LEFT JOIN risk_model_levels AS RML ON RML.risk_level = RL.id LEFT JOIN intent_to_leave AS IL ON P.intent_to_leave = IL.id LEFT JOIN org_person_student AS OPS ON OPS.person_id = P.id LEFT JOIN activity_log acl on (acl.person_id_student = P.id) LEFT JOIN student_db_view_log SDV ON (SDV.person_id_student = P.id) LEFT JOIN person_ebi_metadata as pem on (pem.person_id = P.id and pem.ebi_metadata_id IN ( Select id from ebi_metadata where meta_key ="ClassLevel")) where   P.id IN ($personIds ) AND SDV.organization_id = $orgId AND SDV.person_id_faculty = $facultyIds AND SDV.last_viewed_on > P.risk_update_date group by P.id' WHERE  `query_key` = 'Have_Not_Been_Reviewed';
CDATA;
        $this->addSql($query);
    }
    
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    
    }
}
