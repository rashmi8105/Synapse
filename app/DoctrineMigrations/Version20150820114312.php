<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150820114312 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
 public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $personId = '$$personId$$';
        $studentVariable = '$$studentId$$';
        $orgVariable = '$$orgId$$';
        $activityArr = '$$acivityArr$$';
        $facultyVar = '$$faculty$$';
        
        $noteTeamAcc = '$$noteTeamAccess$$';
        $notePublicAcc = '$$notePublicAccess$$';
        $contactTeamAcc = '$$contactTeamAccess$$';
        $contactPubAcc = '$$contactPublicAccess$$';
        
        $refTeamAcc = '$$referralTeamAccess$$';
        $refPubAcc = '$$referralPublicAccess$$';
        $teamAcc = '$$teamAccess$$';
        $publicAcc = '$$publicAccess$$';
        $facultIdVar = '$$facultyId$$';
        
        
        $query = <<<CDATA
UPDATE `ebi_search` SET query = 'SELECT N.id AS activity_id, AL.id AS activity_log_id, N.created_at AS activity_date, N.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, N.note AS activity_description FROM activity_log AS AL LEFT JOIN note AS N ON AL.note_id = N.id LEFT JOIN person AS P ON N.person_id_faculty = P.id LEFT JOIN activity_category AS AC ON N.activity_category_id = AC.id LEFT JOIN note_teams AS NT ON N.id = NT.note_id LEFT JOIN related_activities as RA ON N.id  = RA.note_id LEFT JOIN activity_log AL1 ON  RA.activity_log_id = AL1.id LEFT JOIN referrals AS R ON  AL1.referrals_id = R.id LEFT JOIN note AS N1 ON  AL1.note_id = N1.id LEFT JOIN contacts AS C ON  AL1.contacts_id = C.id LEFT JOIN Appointments AS A ON  AL1.appointments_id = A.id WHERE AL.person_id_student = $studentVariable /*Student id in request parameter */ AND AL.deleted_at IS NULL AND N.deleted_at IS NULL AND ( CASE WHEN AL1.activity_type IS NOT NULL AND ((AL1.activity_type = "R" AND R.access_private = 1) OR (AL1.activity_type = "C" AND C.access_private = 1) OR (AL1.activity_type = "N" AND N.access_private = 1)) THEN N.person_id_faculty = $facultyVar ELSE CASE WHEN N.access_team = 1 THEN NT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultyVar AND teams_id IN (SELECT teams_id from note_teams WHERE note_id = N.id AND deleted_at IS NULL))AND $teamAcc = 1 /* logged in person id*/ ELSE CASE WHEN N.access_private = 1 THEN N.person_id_faculty = $facultyVar /* logged in person id*/ ELSE N.access_public = 1 AND $publicAcc = 1 END END END OR N.person_id_faculty = $facultyVar) GROUP BY N.id' WHERE query_key =  'Activity_Note';
CDATA;
        $this->addSql($query);
        
        $query = <<<CDATA
UPDATE `ebi_search` SET query = 'SELECT AL.activity_type AS activity_type FROM activity_log AS AL LEFT JOIN Appointments AS A ON AL.appointments_id = A.id LEFT JOIN note AS N ON AL.note_id = N.id LEFT JOIN note_teams AS NT ON N.id = NT.note_id LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN referrals AS R ON AL.referrals_id = R.id LEFT JOIN referrals_teams AS RT ON R.id = RT.referrals_id LEFT JOIN activity_category AS AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN related_activities as RA ON (N.id = RA.note_id OR C.id = RA.contacts_id) LEFT JOIN activity_log AL1 ON RA.activity_log_id = AL1.id LEFT JOIN referrals AS R1 ON AL1.referrals_id = R1.id LEFT JOIN note AS N1 ON AL1.note_id = N1.id LEFT JOIN contacts AS C1 ON AL1.contacts_id = C1.id LEFT JOIN Appointments AS A1 ON AL1.appointments_id = A1.id LEFT JOIN person AS P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id WHERE AL.person_id_student = $studentVariable AND AL.organization_id = $orgVariable AND AL.activity_type IN ($activityArr) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL AND (CASE WHEN AL.activity_type = "N" THEN CASE WHEN AL1.activity_type IS NOT NULL AND ((AL1.activity_type = "R" AND R1.access_private = 1) OR (AL1.activity_type = "C" AND C1.access_private = 1) OR (AL1.activity_type = "N" AND N1.access_private = 1)) THEN N.person_id_faculty = $facultyVar ELSE CASE WHEN N.access_team = 1 THEN NT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = 3231 AND teams_id IN (SELECT teams_id from note_teams WHERE note_id = N.id AND deleted_at IS NULL)) AND $noteTeamAcc = 1 ELSE CASE WHEN N.access_private = 1 THEN N.person_id_faculty = $facultyVar ELSE N.access_public = 1 AND $notePublicAcc = 1 END END END OR N.person_id_faculty = $facultyVar ELSE CASE WHEN AL.activity_type = "C" THEN CASE WHEN AL1.activity_type IS NOT NULL AND ((AL1.activity_type = "R" AND R1.access_private = 1) OR (AL1.activity_type = "C" AND C1.access_private = 1) OR (AL1.activity_type = "N" AND N1.access_private = 1)) THEN C.person_id_faculty = $facultyVar ELSE CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultyVar AND teams_id IN (SELECT teams_id from contacts_teams WHERE contacts_id = C.id AND deleted_at IS NULL)) AND $contactTeamAcc = 1 ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $facultyVar ELSE C.access_public = 1 AND $contactPubAcc = 1 END END END OR C.person_id_faculty = $facultyVar ELSE CASE WHEN AL.activity_type = "R" THEN CASE WHEN R.access_team = 1 THEN RT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultyVar AND teams_id IN (SELECT teams_id from referrals_teams WHERE referrals_id = R.id AND deleted_at IS NULL)) AND $refTeamAcc = 1 ELSE CASE WHEN R.access_private = 1 THEN R.person_id_faculty = $facultyVar ELSE R.access_public = 1 AND $refPubAcc = 1 END END OR R.person_id_assigned_to = $facultyVar OR R.person_id_faculty = $facultyVar ELSE CASE WHEN AL.activity_type = "A" THEN 1 = 1 ELSE 1 =1 END END END END) GROUP BY AL.id UNION ALL (select "R" as activity_type from referrals_interested_parties as rip left join referrals as R2 on R2.id = rip.referrals_id where rip.person_id = $facultyVar and R2.person_id_student = $studentVariable and rip.deleted_at is null)' WHERE query_key = 'Activity_Count' ;
CDATA;
        $this->addSql($query);

        $query = <<<CDATA
UPDATE `ebi_search` SET query = 'SELECT C.id AS activity_id, AL.id AS activity_log_id, C.created_at AS activity_date, C.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, C.note AS activity_description, C.contact_types_id AS activity_contact_type_id, CTL.description AS activity_contact_type_text FROM activity_log AS AL LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN person AS P ON C.person_id_faculty = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category AS AC ON C.activity_category_id = AC.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN related_activities as RA ON C.id = RA.contacts_id LEFT JOIN activity_log AL1 ON RA.activity_log_id = AL1.id LEFT JOIN referrals AS R1 ON AL1.referrals_id = R1.id LEFT JOIN note AS N1 ON AL1.note_id = N1.id LEFT JOIN contacts AS C1 ON AL1.contacts_id = C1.id LEFT JOIN Appointments AS A1 ON AL1.appointments_id = A1.id WHERE C.person_id_student = $studentVariable AND C.deleted_at IS NULL AND ( CASE WHEN AL1.activity_type IS NOT NULL AND ((AL1.activity_type = "R" AND R1.access_private = 1) OR (AL1.activity_type = "C" AND C1.access_private = 1) OR (AL1.activity_type = "N" AND N1.access_private = 1)) THEN C.person_id_faculty = $facultIdVar ELSE CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultIdVar AND teams_id IN (SELECT teams_id from contacts_teams WHERE contacts_id = C.id AND deleted_at IS NULL)) AND $teamAcc = 1 ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $facultIdVar ELSE C.access_public = 1 AND $publicAcc = 1 END END END OR C.person_id_faculty = $facultIdVar) GROUP BY C.id' WHERE query_key = 'Activity_Contact' ;
CDATA;
        $this->addSql($query);
        
        
        $query = <<<CDATA
UPDATE `ebi_search` SET query = 'SELECT C.id AS activity_id, AL.id AS activity_log_id, C.created_at AS activity_date, C.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, C.note AS activity_description, C.contact_types_id AS activity_contact_type_id, CTL.description AS activity_contact_type_text FROM activity_log AS AL LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN person AS P ON C.person_id_faculty = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category AS AC ON C.activity_category_id = AC.id LEFT JOIN related_activities as RA ON C.id = RA.contacts_id LEFT JOIN activity_log AL1 ON RA.activity_log_id = AL1.id LEFT JOIN referrals AS R1 ON AL1.referrals_id = R1.id LEFT JOIN note AS N1 ON AL1.note_id = N1.id LEFT JOIN contacts AS C1 ON AL1.contacts_id = C1.id LEFT JOIN Appointments AS A1 ON AL1.appointments_id = A1.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN contact_types AS CONT ON C.contact_types_id = CONT.id WHERE C.person_id_student = $studentVariable /* Student id in request parameter */ AND (CONT.parent_contact_types_id = 1 OR CONT.parent_contact_types_id IS NULL)/* is interaction */ AND C.deleted_at IS NULL AND ( CASE WHEN AL1.activity_type IS NOT NULL AND ((AL1.activity_type = "R" AND R1.access_private = 1) OR (AL1.activity_type = "C" AND C1.access_private = 1) OR (AL1.activity_type = "N" AND N1.access_private = 1)) THEN C.person_id_faculty = $facultIdVar ELSE CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultIdVar AND teams_id IN (SELECT teams_id from contacts_teams WHERE contacts_id = C.id AND deleted_at IS NULL)) AND $teamAcc = 1 ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $facultIdVar ELSE C.access_public = 1 AND $publicAcc = 1 END END END OR C.person_id_faculty = $facultIdVar) GROUP BY C.id'  WHERE query_key = 'Activity_Contact_Interaction' ;
CDATA;
        $this->addSql($query);
        
        
        $query = <<<CDATA
UPDATE `ebi_search` SET query = 'SELECT COUNT(DISTINCT(C.id)) AS cnt FROM activity_log AS AL LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN person AS P ON C.person_id_faculty = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category AS AC ON C.activity_category_id = AC.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN related_activities as RA ON C.id = RA.contacts_id LEFT JOIN activity_log AL1 ON RA.activity_log_id = AL1.id LEFT JOIN referrals AS R1 ON AL1.referrals_id = R1.id LEFT JOIN note AS N1 ON AL1.note_id = N1.id LEFT JOIN contacts AS C1 ON AL1.contacts_id = C1.id LEFT JOIN Appointments AS A1 ON AL1.appointments_id = A1.id LEFT JOIN contact_types AS CONT ON C.contact_types_id = CONT.id WHERE C.person_id_student = $studentVariable /* Student id in request parameter */ AND (CONT.parent_contact_types_id = 1 OR CONT.parent_contact_types_id IS NULL)/* is interaction */ AND C.deleted_at IS NULL AND (CASE WHEN AL1.activity_type IS NOT NULL AND ((AL1.activity_type = "R" AND R1.access_private = 1) OR (AL1.activity_type = "C" AND C1.access_private = 1) OR (AL1.activity_type = "N" AND N1.access_private = 1)) THEN C.person_id_faculty = $facultIdVar ELSE CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultIdVar AND teams_id IN (SELECT teams_id FROM contacts_teams WHERE contacts_id = C.id AND deleted_at IS NULL)) AND $teamAcc = 1 ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $facultIdVar  ELSE C.access_public = 1 AND $publicAcc = 1 END END END OR C.person_id_faculty = $facultIdVar)' WHERE query_key = 'Activity_Contact_Int_Count' ;
CDATA;
        $this->addSql($query);
        
        
        }
   

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    
    }

}
