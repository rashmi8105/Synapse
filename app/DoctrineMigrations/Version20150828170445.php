<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150828170445 extends AbstractMigration
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
        $personId = '$$personId$$';
        $studentVariable = '$$studentId$$';
        $orgVariable = '$$orgId$$';
        $activityArr = '$$acivityArr$$';
        $facultyVar = '$$faculty$$';
        
        $noteTeamAcc = '$$noteTeamAccess$$';
        $notePublicAcc = '$$notePublicAccess$$';
        $contactTeamAcc = '$$contactTeamAccess$$';
        $contactPubAcc = '$$contactPublicAccess$$';
        
        $emailTeamAcc = '$$emailTeamAccess$$';
        $emailPublicAcc = '$$emailPublicAccess$$';
        
        $appTeamAcc = '$$appointmentTeamAccess$$';
        $appPublicAcc = '$$appointmentPublicAccess$$';
        
        $refTeamAcc = '$$referralTeamAccess$$';
        $refPubAcc = '$$referralPublicAccess$$';
        $teamAcc = '$$teamAccess$$';
        $publicAcc = '$$publicAccess$$';
        $facultIdVar = '$$facultyId$$';
        
           
    
        $query = <<<CDATA
INSERT INTO `ebi_search` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`query_key`,`search_type`,`is_enabled`,`query`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,"Activity_Email","E",1,'SELECT E.id AS activity_id, AL.id AS activity_log_id, E.created_at AS activity_date, E.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, E.email_subject AS activity_description, E.email_subject AS activity_email_subject, E.email_body AS activity_email_body FROM activity_log AS AL LEFT JOIN email AS E ON AL.email_id = E.id LEFT JOIN person AS P ON E.person_id_faculty = P.id LEFT JOIN activity_category AS AC ON E.activity_category_id = AC.id LEFT JOIN email_teams AS ET ON E.id = ET.email_id LEFT JOIN related_activities as RA ON E.id = RA.email_id LEFT JOIN activity_log AL1 ON RA.activity_log_id = AL1.id LEFT JOIN referrals AS R1 ON AL1.referrals_id = R1.id LEFT JOIN note AS N1 ON AL1.note_id = N1.id LEFT JOIN contacts AS C1 ON AL1.contacts_id = C1.id LEFT JOIN Appointments AS A1 ON AL1.appointments_id = A1.id LEFT JOIN email AS E1 ON AL1.appointments_id = E1.id WHERE E.person_id_student = $studentVariable AND E.deleted_at IS NULL AND (CASE WHEN AL1.activity_type IS NOT NULL AND ((AL1.activity_type = "R" AND R1.access_private = 1) OR (AL1.activity_type = "C" AND C1.access_private = 1) OR (AL1.activity_type = "N" AND N1.access_private = 1) OR (AL1.activity_type = "E" AND E1.access_private = 1)) THEN E.person_id_faculty = $facultIdVar ELSE CASE WHEN E.access_team = 1 THEN ET.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $facultIdVar AND teams_id IN (SELECT teams_id from email_teams WHERE email_id = E.id AND deleted_at IS NULL)) AND $teamAcc = 1 ELSE CASE WHEN E.access_private = 1 THEN E.person_id_faculty = $facultIdVar ELSE E.access_public = 1 AND $publicAcc = 1 END END END OR E.person_id_faculty = $facultIdVar) GROUP BY E.id');
CDATA;
        $this->addSql($query);
         
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
