<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151123114307 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $personId = '$$personId$$';
        $fromDate = '$$fromDate$$';
        $toDate = '$$toDate$$';
        $teamMembersId = '$$teamMemberId$$';
        $organizationId = '$$organizationId$$';
        
        $query = <<<CDATA
UPDATE `ebi_search`
 SET `query` = "
select 'openreferrals' as activity,al.activity_date as activity_date,al.person_id_faculty as team_member_id,pa.firstname as team_member_firstname,pa.lastname as team_member_lastname, pa.username as primary_email,al.person_id_student as student_id, (CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.firstname ELSE '' END) as student_firstname,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.lastname ELSE '' END) as student_lastname,'O' as activity_type, al.referrals_id, al.appointments_id, al.note_id, al.contacts_id, '' as reason_id, al.reason as reason_text,r.status,gs.person_id from activity_log al left join referrals r on r.id = al.referrals_id left outer join org_group_students gs on (al.person_id_student = gs.person_id and gs.org_group_id in (select distinct org_group_id from org_group_faculty where person_id in ($personId) and deleted_at IS NULL )) left join person pa on pa.id = al.person_id_faculty left join person p on p.id = al.person_id_student where al.activity_type in ('R') and r.status = 'O' and al.activity_date between '$fromDate' and '$toDate' and al.person_id_faculty in ($teamMembersId) and al.deleted_at IS NULL and al.organization_id = '$organizationId' group by al.id [ORDER_BY] [LIMIT] -- maxscale route to server slave1
" WHERE `query_key` = "My_Team_List_with_only_OpenReferrals";
CDATA;
        $this->addSql($query);
        
        $query1 = <<<CDATA
UPDATE `ebi_search`
 SET `query` = "
select 'logins' as activity,al.activity_date as activity_date,al.person_id_faculty as team_member_id,pa.firstname as team_member_firstname,pa.lastname as team_member_lastname, ci.primary_email, '' as student_id, '' as student_firstname,'' as student_lastname,al.activity_type as activity_type, al.referrals_id, al.appointments_id, al.note_id, al.contacts_id, '' as activity_id, '' as reason_id, '-' as reason_text,'' as status from activity_log al left join person pa on pa.id = al.person_id_faculty left join person_contact_info pci on pci.person_id = al.person_id_faculty left join contact_info ci on ci.id = pci.contact_id where al.activity_type in ('L') and al.activity_date between '$fromDate' and '$toDate' and al.person_id_faculty in ($teamMembersId) and al.deleted_at IS NULL and al.organization_id = '$organizationId' [ORDER_BY] [LIMIT] -- maxscale route to server slave1
" WHERE `query_key` = "My_Team_List_with_only_Logins";
CDATA;
        $this->addSql($query1);
        
        $query2 = <<<CDATA
UPDATE `ebi_search`
 SET `query` = "
select 'interactions' as activity,al.activity_date as activity_date,al.person_id_faculty as team_member_id,pa.firstname as team_member_firstname,pa.lastname as team_member_lastname, pa.username as primary_email,al.person_id_student as student_id, (CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.firstname ELSE '' END) as student_firstname,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.lastname ELSE '' END) as student_lastname,al.activity_type as activity_type, al.referrals_id, al.appointments_id, al.note_id, al.contacts_id, '' as reason_id, al.reason as reason_text,r.status,gs.person_id from activity_log al left join referrals r on r.id = al.referrals_id left outer join org_group_students gs on (al.person_id_student = gs.person_id and gs.org_group_id in (select distinct org_group_id from org_group_faculty where person_id in ($personId) and deleted_at IS NULL )) left join person pa on pa.id = al.person_id_faculty left join person p on p.id = al.person_id_student where al.activity_type in ('A','C','N','R') and al.activity_date between '$fromDate' and '$toDate' and al.person_id_faculty in ($teamMembersId) and al.deleted_at IS NULL and al.organization_id = '$organizationId'  group by al.id [ORDER_BY] [LIMIT] -- maxscale route to server slave1
" WHERE `query_key` = "My_Team_List_without_Referrals";
CDATA;
        $this->addSql($query2);
        
        $query3 = <<<CDATA
UPDATE `ebi_search`
 SET `query` = "
(select 'openreferrals' as activity,al.activity_date as activity_date,al.person_id_faculty as team_member_id,pa.firstname as team_member_firstname,pa.lastname as team_member_lastname, pa.username as primary_email,al.person_id_student as student_id,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.firstname ELSE '' END) as student_firstname,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.lastname ELSE '' END) as student_lastname,'O' as activity_type, al.referrals_id, al.appointments_id, al.note_id, al.contacts_id, '' as reason_id, al.reason as reason_text,r.status,gs.person_id from activity_log al left join referrals r on r.id = al.referrals_id left outer join org_group_students gs on (al.person_id_student = gs.person_id and gs.org_group_id in (select distinct org_group_id from org_group_faculty where person_id in ($personId) and deleted_at IS NULL )) left join person pa on pa.id = al.person_id_faculty left join person p on p.id = al.person_id_student where al.activity_type in ('R') and al.activity_date between '$fromDate' and '$toDate' and al.person_id_faculty in ($teamMembersId) and al.deleted_at IS NULL and al.organization_id = '$organizationId' group by al.id) union (select 'interactions' as activity,al.activity_date as activity_date,al.person_id_faculty as team_member_id, pa.firstname as team_member_firstname,pa.lastname as team_member_lastname, pa.username as primary_email,al.person_id_student as student_id,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.firstname ELSE '' END) as student_firstname,(CASE WHEN gs.person_id IS NOT NULL and gs.deleted_at IS NULL then p.lastname ELSE '' END) as student_lastname, al.activity_type as activity_type, al.referrals_id, al.appointments_id, al.note_id, al.contacts_id, '' as reason_id, al.reason as reason_text,'' as status,gs.person_id from activity_log al left outer join org_group_students gs on (al.person_id_student = gs.person_id and gs.org_group_id in (select distinct org_group_id from org_group_faculty where person_id in ($personId) and deleted_at IS NULL )) left join person pa on pa.id = al.person_id_faculty left join person p on p.id = al.person_id_student where al.activity_type in ('A','C','N','L') and al.activity_date between '$fromDate' and '$toDate' and al.person_id_faculty in ($teamMembersId) and al.deleted_at IS NULL and al.organization_id = '$organizationId' group by al.id) [ORDER_BY] [LIMIT] -- maxscale route to server slave1
" WHERE `query_key` = "My_Team_List_with_OpenReferrals_and_otherActivities";
CDATA;
        $this->addSql($query3);
        
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
