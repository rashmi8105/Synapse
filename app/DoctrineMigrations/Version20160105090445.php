<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160105090445 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $loggedUserId = '"$$loggedUserId$$"';
        $organizationId = '"$$organizationId$$"';
        $fromDate = '"$$fromDate$$"';
        $toDate = '"$$toDate$$"';
        $academicStartDate = '"$$academicStartDate$$"';
        $academicEndDate =  '"$$academicEndDate$$"';
        
        $personId = '$$personId$$';
        $teamMembersId = '$$teamMemberId$$';
        
        $query1 = <<<CDATA
          UPDATE `synapse`.`ebi_search`
        SET
        `query` ='
    select
    tm.teams_id,
    t.team_name,
    count(al.id) as numbers,
    "openreferrals" as activity
from
    Teams t,
    team_members tm,
    activity_log al,
    referrals r
where
    tm.teams_id = t.id
        and tm.organization_id = t.organization_id
        and al.organization_id = tm.organization_id
        and al.person_id_faculty = tm.person_id
        and al.activity_type = "R"
        and r.id = al.referrals_id
        and al.person_id_student in (select person_id from org_group_students where deleted_at is null and organization_id = $organizationId)
        and r.status = "O"
         and al.activity_date between $fromDate and $toDate
        and al.activity_date between $academicStartDate and $academicEndDate
        and tm.teams_id in (SELECT
            teams_id
        FROM
            team_members
        where
            is_team_leader = 1
                 and person_id = $loggedUserId
                and deleted_at IS NULL)
        and tm.organization_id = $organizationId
        and t.deleted_at IS NULL
        and tm.deleted_at IS NULL
        and al.deleted_at IS NULL
group by t.team_name ' WHERE
        `query_key`="My_Team_Open_Referrals_count_Groupby_Teams"
CDATA;
        $this->addSql($query1);

        $query2 = <<<CDATA
UPDATE `ebi_search`
 SET `query` = '
select "openreferrals" as activity,al.activity_date as activity_date,al.person_id_faculty as team_member_id,pa.firstname as team_member_firstname,
pa.lastname as team_member_lastname, pa.username as primary_email,
al.person_id_student as student_id, (CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.firstname ELSE "" END) 
as student_firstname,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.lastname ELSE "" END) as student_lastname,"O" as activity_type, 
al.referrals_id, al.appointments_id, al.note_id, al.contacts_id, "" as reason_id, al.reason as reason_text,r.status,gs.person_id from activity_log al 
left join referrals r on r.id = al.referrals_id join org_group_students gs on (al.person_id_student = gs.person_id and gs.deleted_at is null and 
gs.org_group_id in (select distinct org_group_id from org_group_faculty where person_id in ($personId) and deleted_at IS NULL )) 
left join person pa on pa.id = al.person_id_faculty left join person p on p.id = al.person_id_student where al.activity_type in ("R") 
and r.status = "O" and al.activity_date between $fromDate and $toDate and al.activity_date between $academicStartDate and $academicEndDate and al.person_id_faculty in ($teamMembersId) and al.deleted_at IS NULL 
and al.organization_id = $organizationId group by al.id [ORDER_BY] [LIMIT]
' WHERE `query_key` = "My_Team_List_with_only_OpenReferrals";
CDATA;
        $this->addSql($query2);
        
        $query3 = <<<CDATA
UPDATE `ebi_search`
 SET `query` = '
(select "openreferrals" as activity,al.activity_date as activity_date,al.person_id_faculty as team_member_id,pa.firstname as team_member_firstname,
pa.lastname as team_member_lastname, pa.username as primary_email,al.person_id_student as student_id,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL 
then p.firstname ELSE "" END) as student_firstname,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.lastname ELSE "" END) as student_lastname,
"O" as activity_type, al.referrals_id, al.appointments_id, al.note_id, al.contacts_id, "" as reason_id, al.reason as reason_text,r.status,gs.person_id 
from activity_log al left join referrals r on r.id = al.referrals_id join org_group_students gs on (al.person_id_student = gs.person_id and gs.deleted_at is null and gs.org_group_id in 
(select distinct org_group_id from org_group_faculty where person_id in ($personId) and deleted_at IS NULL )) 
left join person pa on pa.id = al.person_id_faculty left join person p on p.id = al.person_id_student where al.activity_type in ("R") 
and al.activity_date between $fromDate and $toDate and al.activity_date between $academicStartDate and $academicEndDate and al.person_id_faculty in ($teamMembersId) and al.deleted_at IS NULL and al.organization_id = $organizationId 
group by al.id) union (select "interactions" as activity,al.activity_date as activity_date,al.person_id_faculty as team_member_id, pa.firstname as team_member_firstname,
pa.lastname as team_member_lastname, pa.username as primary_email,al.person_id_student as student_id,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL 
then p.firstname ELSE "" END) as student_firstname,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.lastname ELSE "" END) as student_lastname, 
al.activity_type as activity_type, al.referrals_id, al.appointments_id, al.note_id, al.contacts_id, "" as reason_id, al.reason as reason_text,"" as status,
gs.person_id from activity_log al left outer join org_group_students gs on (al.person_id_student = gs.person_id and gs.org_group_id in 
(select distinct org_group_id from org_group_faculty where person_id in ($personId) and deleted_at IS NULL )) left join person pa on pa.id = al.person_id_faculty 
left join person p on p.id = al.person_id_student where al.activity_type in ("A","C","N","L") and al.activity_date between $fromDate and $toDate 
and al.person_id_faculty in ($teamMembersId) and al.deleted_at IS NULL and al.organization_id = $organizationId group by al.id) [ORDER_BY] [LIMIT]
' WHERE `query_key` = "My_Team_List_with_OpenReferrals_and_otherActivities";
CDATA;
        $this->addSql($query3);
        
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
    }
}
