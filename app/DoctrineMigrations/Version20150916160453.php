<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150916160453 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $studentId = '$$studentId$$';
        $orgId = '$$orgId$$';
        $faculty = '$$faculty$$';
        $teamAccess = '$$teamAccess$$';
        $publicAccess = '$$publicAccess$$';
        $facultyId = '$$facultyId$$';
        $query = <<<CDATA
UPDATE `ebi_search` SET `query` = '
SELECT R.id AS activity_id, AL.id AS activity_log_id, R.referral_date AS activity_date, R.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, R.note AS activity_description, R.status AS activity_referral_status FROM activity_log AS AL LEFT JOIN referrals AS R ON AL.referrals_id = R.id LEFT JOIN person AS P ON R.person_id_faculty = P.id LEFT JOIN activity_category AS AC ON R.activity_category_id = AC.id LEFT JOIN referrals_teams AS RT ON R.id = RT.referrals_id WHERE R.person_id_student = $studentId /* Student id in request parameter */ AND R.organization_id = $orgId AND R.deleted_at IS NULL AND (CASE WHEN access_team = 1 THEN RT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $faculty AND teams_id IN (SELECT teams_id from referrals_teams WHERE referrals_id = R.id AND deleted_at IS NULL))AND $teamAccess = 1 ELSE CASE WHEN access_private = 1 THEN R.person_id_faculty = $faculty ELSE R.access_public = 1 AND $publicAccess = 1 END END OR R.person_id_assigned_to = $faculty OR R.person_id_faculty = $faculty) OR (R.id IN (select rip.referrals_id from referrals_interested_parties as rip left join referrals as R2 on R2.id = rip.referrals_id where rip.person_id = $faculty and R2.person_id_student = $studentId and rip.deleted_at is null)) GROUP BY R.id order by R.referral_date desc
    ' WHERE `query_key` = 'Activity_Referral'
CDATA;
        $this->addSql($query);
        
        $query1 = <<<CDATA
UPDATE `ebi_search` SET `query` = '
SELECT N.id AS activity_id, AL.id AS activity_log_id, N.note_date AS activity_date, N.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, N.note AS activity_description FROM activity_log AS AL LEFT JOIN note AS N ON AL.note_id = N.id LEFT JOIN person AS P ON N.person_id_faculty = P.id LEFT JOIN activity_category AS AC ON N.activity_category_id = AC.id LEFT JOIN note_teams AS NT ON N.id = NT.note_id LEFT JOIN related_activities as RA ON N.id  = RA.note_id LEFT JOIN activity_log AL1 ON  RA.activity_log_id = AL1.id LEFT JOIN referrals AS R ON  AL1.referrals_id = R.id LEFT JOIN note AS N1 ON  AL1.note_id = N1.id LEFT JOIN contacts AS C ON  AL1.contacts_id = C.id LEFT JOIN Appointments AS A ON  AL1.appointments_id = A.id WHERE AL.person_id_student = $studentId /*Student id in request parameter */ AND AL.deleted_at IS NULL AND N.deleted_at IS NULL AND ( CASE WHEN AL1.activity_type IS NOT NULL AND ((AL1.activity_type = "R" AND R.access_private = 1) OR (AL1.activity_type = "C" AND C.access_private = 1) OR (AL1.activity_type = "N" AND N.access_private = 1)) THEN N.person_id_faculty = $faculty ELSE CASE WHEN N.access_team = 1 THEN NT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $faculty AND teams_id IN (SELECT teams_id from note_teams WHERE note_id = N.id AND deleted_at IS NULL))AND $teamAccess = 1 /* logged in person id*/ ELSE CASE WHEN N.access_private = 1 THEN N.person_id_faculty = $faculty /* logged in person id*/ ELSE N.access_public = 1 AND $publicAccess = 1 END END END OR N.person_id_faculty = $faculty) GROUP BY N.id order by N.note_date desc
    ' 
    WHERE `query_key` = 'Activity_Note'
CDATA;
        $this->addSql($query1);
        
        $query2 = <<<CDATA
UPDATE `ebi_search` SET `query` = '
SELECT C.id AS activity_id, AL.id AS activity_log_id, C.contact_date AS activity_date, C.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, C.note AS activity_description, C.contact_types_id AS activity_contact_type_id, CTL.description AS activity_contact_type_text , C.contact_date as contact_created_date  FROM activity_log AS AL LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN person AS P ON C.person_id_faculty = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category AS AC ON C.activity_category_id = AC.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN related_activities as RA ON C.id = RA.contacts_id LEFT JOIN activity_log AL1 ON RA.activity_log_id = AL1.id LEFT JOIN referrals AS R1 ON AL1.referrals_id = R1.id LEFT JOIN note AS N1 ON AL1.note_id = N1.id LEFT JOIN contacts AS C1 ON AL1.contacts_id = C1.id LEFT JOIN Appointments AS A1 ON AL1.appointments_id = A1.id WHERE C.person_id_student = $studentId AND C.deleted_at IS NULL AND ( CASE WHEN AL1.activity_type IS NOT NULL AND ((AL1.activity_type = "R" AND R1.access_private = 1) OR (AL1.activity_type = "C" AND C1.access_private = 1) OR (AL1.activity_type = "N" AND N1.access_private = 1)) THEN C.person_id_faculty = $facultyId ELSE CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultyId AND teams_id IN (SELECT teams_id from contacts_teams WHERE contacts_id = C.id AND deleted_at IS NULL)) AND $teamAccess = 1 ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $facultyId ELSE C.access_public = 1 AND $publicAccess = 1 END END END OR C.person_id_faculty = $facultyId) GROUP BY C.id order by C.contact_date desc
    '
    WHERE `query_key` = 'Activity_Contact'
CDATA;
        $this->addSql($query2);
        
        $query3 = <<<CDATA
UPDATE `ebi_search` SET `query` = '
SELECT C.id AS activity_id, AL.id AS activity_log_id, C.contact_date AS activity_date, C.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, C.note AS activity_description, C.contact_types_id AS activity_contact_type_id, CTL.description AS activity_contact_type_text ,C.contact_date as contact_created_date  FROM activity_log AS AL LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN person AS P ON C.person_id_faculty = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN activity_category AS AC ON C.activity_category_id = AC.id LEFT JOIN related_activities as RA ON C.id = RA.contacts_id LEFT JOIN activity_log AL1 ON RA.activity_log_id = AL1.id LEFT JOIN referrals AS R1 ON AL1.referrals_id = R1.id LEFT JOIN note AS N1 ON AL1.note_id = N1.id LEFT JOIN contacts AS C1 ON AL1.contacts_id = C1.id LEFT JOIN Appointments AS A1 ON AL1.appointments_id = A1.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN contact_types AS CONT ON C.contact_types_id = CONT.id WHERE C.person_id_student = $studentId /* Student id in request parameter */ AND (CONT.parent_contact_types_id = 1 OR CONT.parent_contact_types_id IS NULL) AND C.deleted_at IS NULL AND ( CASE WHEN AL1.activity_type IS NOT NULL AND ((AL1.activity_type = "R" AND R1.access_private = 1) OR (AL1.activity_type = "C" AND C1.access_private = 1) OR (AL1.activity_type = "N" AND N1.access_private = 1)) THEN C.person_id_faculty = $facultyId ELSE CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultyId AND teams_id IN (SELECT teams_id from contacts_teams WHERE contacts_id = C.id AND deleted_at IS NULL)) AND $teamAccess = 1 ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $facultyId ELSE C.access_public = 1 AND $publicAccess = 1 END END END OR C.person_id_faculty = $facultyId) GROUP BY C.id order by C.contact_date desc
' 
    WHERE `query_key` = 'Activity_Contact_Interaction'
CDATA;
        $this->addSql($query3);
        
        
        
        
        $query4 = <<<CDATA
UPDATE `ebi_search` SET `query` = '
SELECT E.id AS activity_id, AL.id AS activity_log_id, E.created_at AS activity_date, E.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, E.email_subject AS activity_description, E.email_subject AS activity_email_subject, E.email_body AS activity_email_body FROM activity_log AS AL LEFT JOIN email AS E ON AL.email_id = E.id LEFT JOIN person AS P ON E.person_id_faculty = P.id LEFT JOIN activity_category AS AC ON E.activity_category_id = AC.id LEFT JOIN email_teams AS ET ON E.id = ET.email_id LEFT JOIN related_activities as RA ON E.id = RA.email_id LEFT JOIN activity_log AL1 ON RA.activity_log_id = AL1.id LEFT JOIN referrals AS R1 ON AL1.referrals_id = R1.id LEFT JOIN note AS N1 ON AL1.note_id = N1.id LEFT JOIN contacts AS C1 ON AL1.contacts_id = C1.id LEFT JOIN Appointments AS A1 ON AL1.appointments_id = A1.id LEFT JOIN email AS E1 ON AL1.appointments_id = E1.id WHERE E.person_id_student = $studentId AND E.deleted_at IS NULL AND (CASE WHEN AL1.activity_type IS NOT NULL AND ((AL1.activity_type = "R" AND R1.access_private = 1) OR (AL1.activity_type = "C" AND C1.access_private = 1) OR (AL1.activity_type = "N" AND N1.access_private = 1) OR (AL1.activity_type = "E" AND E1.access_private = 1)) THEN E.person_id_faculty = $facultyId ELSE CASE WHEN E.access_team = 1 THEN ET.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultyId AND teams_id IN (SELECT teams_id from email_teams WHERE email_id = E.id AND deleted_at IS NULL)) AND $teamAccess = 1 ELSE CASE WHEN E.access_private = 1 THEN E.person_id_faculty = $facultyId ELSE E.access_public = 1 AND $publicAccess = 1 END END END OR E.person_id_faculty = $facultyId) GROUP BY E.id order by E.created_at desc
'
    WHERE `query_key` = 'Activity_Email'
CDATA;
        $this->addSql($query4);
        
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
