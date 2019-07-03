<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150603152914 extends AbstractMigration
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
         * ESPRJ- 3071
         */
        $studentVariable = '$$studentId$$';
        $orgVariable = '$$orgId$$';
        $facultyVariable = '$$faculty$$';
        $facultyIdVariable = '$$facultyId$$';
        
        $query = <<<CDATA
UPDATE `ebi_search` SET `query`='SELECT N.id as activity_id,AL.id as activity_log_id,  N.created_at as  activity_date,  N.person_id_faculty as activity_created_by_id , P.firstname as activity_created_by_first_name, P.lastname as activity_created_by_last_name, AC.id as activity_reason_id, AC.short_name as activity_reason_text, N.note as activity_description FROM activity_log as AL  LEFT JOIN note as N ON  AL.note_id = N.id LEFT JOIN person as P ON  N.person_id_faculty = P.id LEFT JOIN activity_category as AC ON N.activity_category_id  = AC.id LEFT JOIN note_teams  as NT ON N.id = NT.note_id WHERE AL.person_id_student = $studentVariable /*Student id in request parameter */ AND AL.deleted_at IS NULL AND N.deleted_at IS NULL AND related.deleted_at IS NULL 	AND ALOG.deleted_at IS NULL) AND CASE WHEN N.access_team = 1 THEN NT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultyVariable) /* logged in person id*/ ELSE CASE WHEN N.access_private = 1 THEN N.person_id_faculty = $facultyVariable /* logged in person id*/ ELSE N.access_public = 1 END END GROUP BY N.id' WHERE `query_key`='Activity_Note';
CDATA;
        $this->addSql($query);
        $query = <<<CDATA
UPDATE `ebi_search` SET `query`='SELECT  C.id as activity_id, AL.id as activity_log_id, C.created_at as  activity_date, C.person_id_faculty as activity_created_by_id , P.firstname as activity_created_by_first_name, P.lastname as activity_created_by_last_name, AC.id as activity_reason_id, AC.short_name as activity_reason_text, C.note as activity_description, C.contact_types_id  as activity_contact_type_id,CTL.description  as activity_contact_type_text FROM activity_log as AL LEFT JOIN contacts as C ON  AL.contacts_id = C.id LEFT JOIN person as P  ON C.person_id_faculty = P.id  LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category as AC ON C.activity_category_id  =  AC.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id  WHERE C.person_id_student  = $studentVariable AND C.deleted_at  IS NULL AND CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultyIdVariable) /* logged in person id*/ ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $facultyIdVariable /* logged in person id*/ ELSE C.access_public = 1 END END GROUP BY C.id' WHERE `query_key`='Activity_Contact';
CDATA;
        $this->addSql($query);
        $query = <<<CDATA
UPDATE `ebi_search` SET `query`='SELECT C.id as activity_id, AL.id as activity_log_id,C.created_at as  activity_date,C.person_id_faculty as activity_created_by_id ,P.firstname as activity_created_by_first_name,P.lastname as activity_created_by_last_name,AC.id as activity_reason_id,AC.short_name as activity_reason_text,C.note as activity_description,C.contact_types_id  as activity_contact_type_id,CTL.description  as activity_contact_type_text FROM activity_log as AL LEFT JOIN contacts as C ON  AL.contacts_id = C.id LEFT JOIN person as P  ON C.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category as AC ON C.activity_category_id  =  AC.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id LEFT JOIN contact_types  as CONT ON C.contact_types_id = CONT.id WHERE C.person_id_student  = $studentVariable /* Student id in request parameter */ AND (CONT.parent_contact_types_id = 1 OR CONT.parent_contact_types_id IS NULL) /* is interaction */ AND C.deleted_at  IS NULL  AND CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultyIdVariable) /* logged in person id*/ ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $facultyIdVariable /* logged in person id*/ ELSE C.access_public = 1 END END GROUP BY C.id' WHERE `query_key`='Activity_Contact_Interaction';
CDATA;
        $this->addSql($query);
        
        $query = <<<CDATA
UPDATE `ebi_search` SET `query`='SELECT N.id as activity_id,AL.id as activity_log_id,  N.created_at as  activity_date,  N.person_id_faculty as activity_created_by_id , P.firstname as activity_created_by_first_name, P.lastname as activity_created_by_last_name, AC.id as activity_reason_id, AC.short_name as activity_reason_text, N.note as activity_description FROM activity_log as AL  LEFT JOIN note as N ON  AL.note_id = N.id LEFT JOIN person as P ON  N.person_id_faculty = P.id LEFT JOIN activity_category as AC ON N.activity_category_id  = AC.id LEFT JOIN note_teams  as NT ON N.id = NT.note_id WHERE AL.person_id_student = $studentVariable /*Student id in request parameter */ AND AL.deleted_at IS NULL AND N.deleted_at IS NULL AND AL.activity_type = "N"  GROUP BY N.id' WHERE `query_key`='Coordinator_Activity_Note';
CDATA;
        $this->addSql($query);
        $query = <<<CDATA
UPDATE `ebi_search` SET `query`='SELECT  C.id as activity_id, AL.id as activity_log_id, C.created_at as  activity_date, C.person_id_faculty as activity_created_by_id , P.firstname as activity_created_by_first_name, P.lastname as activity_created_by_last_name, AC.id as activity_reason_id, AC.short_name as activity_reason_text, C.note as activity_description, C.contact_types_id  as activity_contact_type_id,CTL.description  as activity_contact_type_text FROM activity_log as AL LEFT JOIN contacts as C ON  AL.contacts_id = C.id LEFT JOIN person as P  ON C.person_id_faculty = P.id  LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category as AC ON C.activity_category_id  =  AC.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id  WHERE C.person_id_student  = $studentVariable AND C.deleted_at  IS NULL  GROUP BY C.id' WHERE `query_key`='Coordinator_Activity_Contact';
CDATA;
        $this->addSql($query);
        $query = <<<CDATA
UPDATE `ebi_search` SET `query`='SELECT C.id as activity_id, AL.id as activity_log_id,C.created_at as  activity_date,C.person_id_faculty as activity_created_by_id ,P.firstname as activity_created_by_first_name,P.lastname as activity_created_by_last_name,AC.id as activity_reason_id,AC.short_name as activity_reason_text,C.note as activity_description,C.contact_types_id  as activity_contact_type_id,CTL.description  as activity_contact_type_text FROM activity_log as AL LEFT JOIN contacts as C ON  AL.contacts_id = C.id LEFT JOIN person as P  ON C.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category as AC ON C.activity_category_id  =  AC.id LEFT JOIN contacts_teams  as CT ON C.id = CT.contacts_id LEFT JOIN contact_types  as CONT ON C.contact_types_id = CONT.id WHERE C.person_id_student  = $studentVariable /* Student id in request parameter */ AND (CONT.parent_contact_types_id = 1 OR CONT.parent_contact_types_id IS NULL) /* is interaction */ AND C.deleted_at  IS NULL GROUP BY C.id' WHERE `query_key`='Coordinator_Activity_Contact_Interaction';
CDATA;
        $this->addSql($query);
        
        /*
         * ESPRJ- 3440
         */
        $this->addSql("SET @Final_Grade_Of_C_Or_Below := (select id from  ebi_search where query_key ='Final_Grade_Of_C_Or_Below');
        UPDATE `ebi_search_lang` SET `ebi_search_id` = @Final_Grade_Of_C_Or_Below WHERE `sub_category_name` = 'Final grade of C or below';");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
