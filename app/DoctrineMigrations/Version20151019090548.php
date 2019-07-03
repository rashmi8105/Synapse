<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151019090548 extends AbstractMigration
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
        $roleIds = '$$roleIds$$';
        
        $query = <<<CDATA
   update ebi_search SET `query` = 'SELECT A.id AS AppointmentId, N.id AS NoteId, R.id AS ReferralId, C.id AS ContactId, AL.id AS activity_log_id, AL.created_at AS activity_date, AL.activity_type AS activity_type, AL.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, C.contact_types_id AS activity_contact_type_id, CTL.description AS activity_contact_type_text, R.status AS activity_referral_status, C.note AS contactDescription, R.note AS referralDescription, A.description AS appointmentDescription, N.note AS noteDescription, AL.created_at as created_date, A.start_date_time as app_created_date, C.contact_date as contact_created_date,AL.activity_date as act_date FROM activity_log AS AL LEFT JOIN Appointments AS A ON AL.appointments_id = A.id LEFT JOIN note AS N ON AL.note_id = N.id LEFT JOIN note_teams AS NT ON N.id = NT.note_id LEFT JOIN contacts AS C ON AL.contacts_id = C.id LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id LEFT JOIN referrals AS R ON AL.referrals_id = R.id LEFT JOIN referrals_teams AS RT ON R.id = RT.referrals_id LEFT JOIN activity_category AS AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN person AS P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN contact_types AS CONT ON C.contact_types_id = CONT.id LEFT JOIN organization_role as orgr on orgr.organization_id = AL.organization_id LEFT JOIN referral_routing_rules as rr on rr.activity_category_id = R.activity_category_id WHERE AL.person_id_student = $studentVariable AND AL.organization_id = $orgVariable AND AL.activity_type IN ($activityArr) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL AND CASE WHEN AL.activity_type = "C" THEN CONT.parent_contact_types_id = 1 OR CONT.id = 1 ELSE 1 = 1 END AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.appointment_id = ALOG.appointments_id WHERE related.appointment_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.referral_id = ALOG.referrals_id WHERE related.referral_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.note_id = ALOG.note_id WHERE related.note_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND AL.id NOT IN (SELECT ALOG.id FROM related_activities AS related LEFT JOIN activity_log AS ALOG ON related.contacts_id = ALOG.contacts_id WHERE related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND CASE WHEN AL.activity_type = "N" THEN CASE WHEN N.access_team = 1 THEN NT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultyVar AND teams_id IN (SELECT teams_id FROM note_teams WHERE note_id = N.id AND deleted_at IS NULL)) AND $noteTeamAcc = 1 ELSE CASE WHEN N.access_private = 1 THEN N.person_id_faculty = $facultyVar ELSE N.access_public = 1 AND $notePublicAcc = 1 END END ELSE CASE WHEN AL.activity_type = "C" THEN CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultyVar AND teams_id IN (SELECT teams_id FROM contacts_teams WHERE contacts_id = C.id AND deleted_at IS NULL)) AND $contactTeamAcc = 1 ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $facultyVar ELSE C.access_public = 1 AND $contactPubAcc = 1 END END ELSE CASE WHEN AL.activity_type = "R" THEN CASE WHEN R.access_team = 1 THEN RT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultyVar AND teams_id IN (SELECT teams_id FROM referrals_teams WHERE referrals_id = R.id AND deleted_at IS NULL)) AND $refTeamAcc = 1 ELSE CASE WHEN R.access_private = 1 THEN R.person_id_faculty = $facultyVar ELSE R.access_public = 1 AND $refPubAcc = 1 END END OR R.person_id_assigned_to = $facultyVar OR R.person_id_faculty = $facultyVar OR orgr.person_id = $facultyVar and R.person_id_assigned_to is null AND orgr.role_id IN ($roleIds) AND (rr.is_primary_coordinator = 1 AND rr.person_id IS NULL) ELSE CASE WHEN AL.activity_type = "A" THEN 1 = 1 ELSE 1 = 1 END END END END GROUP BY AL.id ORDER BY act_date DESC'
 WHERE  `query_key` = 'Activity_All_Interaction';
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
