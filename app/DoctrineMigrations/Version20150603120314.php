<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150603120314 extends AbstractMigration
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
        
        $studentVariable = '$$studentId$$';
        $orgVariable = '$$orgId$$';
        $facultyVariable = '$$faculty$$';
        $activityVar = '$$acivityArr$$';
        
        $query = <<<CDATA
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Activity_All_Interaction','E',1,'SELECT A.id as AppointmentId, N.id as NoteId,R.id as ReferralId,C.id as ContactId,AL.id as activity_log_id,AL.created_at as activity_date,AL.activity_type as activity_type,AL.person_id_faculty as activity_created_by_id,P.firstname as activity_created_by_first_name,P.lastname as activity_created_by_last_name,AC.id as activity_reason_id,AC.short_name as activity_reason_text,C.contact_types_id as activity_contact_type_id,CTL.description as activity_contact_type_text,R.status as activity_referral_status,C.note as contactDescription,R.note as referralDescription,A.description as appointmentDescription,N.note as noteDescription FROM activity_log as AL LEFT JOIN Appointments as A ON AL.appointments_id = A.id LEFT JOIN note as N ON AL.note_id = N.id LEFT JOIN note_teams as NT ON N.id = NT.note_id LEFT JOIN contacts as C ON AL.contacts_id = C.id LEFT JOIN contacts_teams as CT ON C.id = CT.contacts_id LEFT JOIN referrals as R ON AL.referrals_id = R.id LEFT JOIN referrals_teams as RT ON R.id = RT.referrals_id LEFT JOIN activity_category as AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN person as P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN contact_types as CONT ON C.contact_types_id = CONT.id WHERE AL.person_id_student = $studentVariable AND AL.organization_id = $orgVariable AND AL.activity_type IN ($activityVar) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL AND CASE WHEN AL.activity_type = "C" THEN CONT.parent_contact_types_id = 1 OR CONT.id =1 ELSE 1=1 END AND AL.id NOT IN( SELECT ALOG.id FROM related_activities as related LEFT JOIN activity_log as ALOG ON related.note_id = ALOG.note_id where related.note_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND AL.id NOT IN( SELECT ALOG.id FROM related_activities as related LEFT JOIN activity_log as ALOG ON related.contacts_id = ALOG.contacts_id where related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND CASE WHEN AL.activity_type = "N" THEN CASE WHEN N.access_team = 1 THEN NT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultyVariable) ELSE CASE WHEN N.access_private = 1 THEN N.person_id_faculty = $facultyVariable ELSE N.access_public = 1 END END ELSE CASE WHEN AL.activity_type = "C" THEN CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultyVariable) ELSE CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $facultyVariable ELSE C.access_public = 1 END END ELSE CASE WHEN AL.activity_type = "R" THEN CASE WHEN R.access_team = 1 THEN RT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultyVariable) ELSE CASE WHEN R.access_private = 1 THEN R.person_id_faculty = $facultyVariable ELSE R.access_public = 1 END END ELSE CASE WHEN AL.activity_type = "A" THEN 1 = 1 ELSE 1 =1 END END END END GROUP BY AL.id ORDER BY AL.created_at desc');
CDATA;
        
        $this->addSql($query);
        
        $query = <<<CDATA
        INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Coordinator_Activity_All_Interaction','E',1,'SELECT A.id as AppointmentId, N.id as NoteId,R.id as ReferralId,C.id as ContactId,AL.id as activity_log_id,AL.created_at as activity_date,AL.activity_type as activity_type,AL.person_id_faculty as activity_created_by_id,P.firstname as activity_created_by_first_name,P.lastname as activity_created_by_last_name,AC.id as activity_reason_id,AC.short_name as activity_reason_text,C.contact_types_id as activity_contact_type_id,CTL.description as activity_contact_type_text,R.status as activity_referral_status,C.note as contactDescription,R.note as referralDescription,A.description as appointmentDescription,N.note as noteDescription FROM activity_log as AL LEFT JOIN Appointments as A ON AL.appointments_id = A.id LEFT JOIN note as N ON AL.note_id = N.id LEFT JOIN note_teams as NT ON N.id = NT.note_id LEFT JOIN contacts as C ON AL.contacts_id = C.id LEFT JOIN contacts_teams as CT ON C.id = CT.contacts_id LEFT JOIN referrals as R ON AL.referrals_id = R.id LEFT JOIN referrals_teams as RT ON R.id = RT.referrals_id LEFT JOIN activity_category as AC ON A.activity_category_id = AC.id OR N.activity_category_id = AC.id OR R.activity_category_id = AC.id OR C.activity_category_id = AC.id LEFT JOIN person as P ON AL.person_id_faculty = P.id LEFT JOIN contact_types_lang as CTL ON C.contact_types_id = CTL.contact_types_id LEFT JOIN contact_types as CONT ON C.contact_types_id = CONT.id WHERE AL.person_id_student = $studentVariable AND AL.organization_id = $orgVariable AND AL.activity_type IN ($activityVar) AND AL.deleted_at IS NULL AND A.deleted_at IS NULL AND N.deleted_at IS NULL AND C.deleted_at IS NULL AND R.deleted_at IS NULL AND CASE WHEN AL.activity_type = "C" THEN CONT.parent_contact_types_id = 1 OR CONT.id =1 ELSE 1=1 END AND AL.id NOT IN( SELECT ALOG.id FROM related_activities as related LEFT JOIN activity_log as ALOG ON related.note_id = ALOG.note_id where related.note_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) AND AL.id NOT IN( SELECT ALOG.id FROM related_activities as related LEFT JOIN activity_log as ALOG ON related.contacts_id = ALOG.contacts_id where related.contacts_id IS NOT NULL AND related.deleted_at IS NULL AND ALOG.deleted_at IS NULL) GROUP BY AL.id ORDER BY AL.created_at desc');
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
