<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160121062202 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        /**
         *  Script to add person's external Id and emailId in the select statement
         *  Added p.external_id
         */
        $personId = '$$personId$$';
        $fromDate = '"$$fromDate$$"';
        $toDate = '"$$toDate$$"';
        $acaStartDate = '"$$academicStartDate$$"';
        $acaEndDate = '"$$academicEndDate$$"';
        $orgId = '"$$organizationId$$"';
        $teamMembersId = '$$teamMemberId$$';
        $query = <<<CDATA
update ebi_search set query = '
(SELECT "openreferrals" AS activity,al.activity_date AS activity_date, al.person_id_faculty AS 
team_member_id ,pa.external_id as team_member_external_id,pa.firstname AS team_member_firstname, 
pa.lastname AS team_member_lastname,pa.username AS primary_email, al.person_id_student AS 
student_id,( CASE WHEN gs.person_id IS NOT NULL AND gs.deleted_at IS NULL THEN p.firstname ELSE 
"" END ) AS student_firstname,( CASE WHEN gs.person_id IS NOT NULL AND gs.deleted_at IS NULL THEN
 p.lastname ELSE "" END ) AS student_lastname, p.external_id as student_external_id,p.username as
 student_email,"O" AS activity_type, al.referrals_id,al.appointments_id,al.note_id,al.contacts_id,
"" AS reason_id, al.reason AS reason_text, r.status,gs.person_id FROM activity_log al LEFT JOIN 
referrals r ON r.id = al.referrals_id JOIN org_group_students gs ON ( al.person_id_student = gs.person_id
 AND gs.deleted_at IS NULL AND gs.org_group_id IN (SELECT DISTINCT org_group_id FROM 
org_group_faculty WHERE person_id IN ( $personId ) AND deleted_at IS NULL) ) LEFT JOIN person 
pa ON pa.id = al.person_id_faculty LEFT JOIN person p ON p.id = al.person_id_student WHERE al.activity_type
 IN ( "R" ) AND al.activity_date BETWEEN $fromDate AND $toDate AND al.activity_date 
BETWEEN $acaStartDate AND $acaEndDate AND al.person_id_faculty IN ( 
$teamMembersId ) AND al.deleted_at IS NULL AND al.organization_id = $orgId GROUP 
BY al.id) UNION (SELECT "interactions" AS activity,al.activity_date AS activity_date, al.person_id_faculty
 AS team_member_id, pa.external_id as team_member_external_id,pa.firstname AS team_member_firstname
, pa.lastname AS team_member_lastname,pa.username AS primary_email, al.person_id_student AS 
student_id,( CASE WHEN gs.person_id IS NOT NULL AND gs.deleted_at IS NULL THEN p.firstname ELSE 
"" END ) AS student_firstname,( CASE WHEN gs.person_id IS NOT NULL AND gs.deleted_at IS NULL THEN
 p.lastname ELSE "" END ) AS student_lastname, p.external_id as student_external_id,p.username as
 student_email, al.activity_type AS activity_type,al.referrals_id,al.appointments_id,al.note_id, 
al.contacts_id,"" AS reason_id,al.reason AS reason_text,"" AS status,gs.person_id FROM activity_log
 al LEFT OUTER JOIN org_group_students gs ON ( al.person_id_student = gs.person_id AND gs.org_group_id
 IN (SELECT DISTINCT org_group_id FROM org_group_faculty WHERE person_id IN ( $personId ) AND 
deleted_at IS NULL) ) LEFT JOIN person pa ON pa.id = al.person_id_faculty LEFT JOIN person p ON 
p.id = al.person_id_student WHERE al.activity_type IN ( "A", "C", "N", "L" ) AND al.activity_date 
BETWEEN $fromDate AND $toDate AND al.person_id_faculty IN ( $teamMembersId ) AND al.deleted_at
 IS NULL AND al.organization_id = $orgId GROUP BY al.id) [ORDER_BY] [LIMIT]
' where query_key = "My_Team_List_with_OpenReferrals_and_otherActivities";
CDATA;
        $this->addSql($query);
        
        $query1 = <<<CDATA
update `ebi_search` set `query` = '
SELECT 
    "openreferrals" AS activity,
    al.activity_date AS activity_date,
    pa.external_id as team_member_external_id,
    al.person_id_faculty AS team_member_id,
    pa.firstname AS team_member_firstname,
    pa.lastname AS team_member_lastname,
    pa.username AS primary_email,
    al.person_id_student AS student_id,
    (CASE
        WHEN
            gs.person_id IS NOT NULL
                AND gs.deleted_at IS NULL
        THEN
            p.firstname
        ELSE ""
    END) AS student_firstname,
    (CASE
        WHEN
            gs.person_id IS NOT NULL
                AND gs.deleted_at IS NULL
        THEN
            p.lastname
        ELSE ""
    END) AS student_lastname,
    p.external_id as student_external_id,
    p.username as student_email,
    "O" AS activity_type,
    al.referrals_id,
    al.appointments_id,
    al.note_id,
    al.contacts_id,
    "" AS reason_id,
    al.reason AS reason_text,
    r.status,
    gs.person_id
FROM
    activity_log al
        LEFT JOIN
    referrals r ON r.id = al.referrals_id
        JOIN
    org_group_students gs ON (al.person_id_student = gs.person_id
        AND gs.deleted_at IS NULL
        AND gs.org_group_id IN (SELECT DISTINCT
            org_group_id
        FROM
            org_group_faculty
        WHERE
            person_id IN ($personId)
                AND deleted_at IS NULL))
        LEFT JOIN
    person pa ON pa.id = al.person_id_faculty
        LEFT JOIN
    person p ON p.id = al.person_id_student
WHERE
    al.activity_type IN ("R")
        AND r.status = "O"
        AND al.activity_date BETWEEN $fromDate AND $toDate
        AND al.activity_date BETWEEN $acaStartDate AND $acaEndDate
        AND al.person_id_faculty IN ($teamMembersId)
        AND al.deleted_at IS NULL
        AND al.organization_id = $orgId
GROUP BY al.id
[ORDER_BY] [LIMIT]
' where `query_key` = 'My_Team_List_with_only_OpenReferrals';
CDATA;
        
        $this->addSql($query1);
        
        $query2 = <<<CDATA
update `ebi_search` set `query` = '
SELECT 
    "interactions" AS activity,
    al.activity_date AS activity_date,
    pa.external_id as team_member_external_id,
    al.person_id_faculty AS team_member_id,
    pa.firstname AS team_member_firstname,
    pa.lastname AS team_member_lastname,
    pa.username AS primary_email,
    al.person_id_student AS student_id,
    (CASE
        WHEN
            gs.person_id IS NOT NULL
                AND gs.deleted_at IS NULL
        THEN
            p.firstname
        ELSE ""
    END) AS student_firstname,
    (CASE
        WHEN
            gs.person_id IS NOT NULL
                AND gs.deleted_at IS NULL
        THEN
            p.lastname
        ELSE ""
    END) AS student_lastname,
    p.external_id as student_external_id,
    p.username as student_email,
    al.activity_type AS activity_type,
    al.referrals_id,
    al.appointments_id,
    al.note_id,
    al.contacts_id,
    "" AS reason_id,
    al.reason AS reason_text,
    r.status,
    gs.person_id
FROM
    activity_log al
        LEFT JOIN
    referrals r ON r.id = al.referrals_id
        LEFT OUTER JOIN
    org_group_students gs ON (al.person_id_student = gs.person_id
        AND gs.org_group_id IN (SELECT DISTINCT
            org_group_id
        FROM
            org_group_faculty
        WHERE
            person_id IN ($personId)
                AND deleted_at IS NULL))
        LEFT JOIN
    person pa ON pa.id = al.person_id_faculty
        LEFT JOIN
    person p ON p.id = al.person_id_student
WHERE
    al.activity_type IN ("A" , "C", "N", "R")
        AND al.activity_date BETWEEN $fromDate AND $toDate
        AND al.person_id_faculty IN ($teamMembersId)
        AND al.deleted_at IS NULL
        AND al.organization_id = $orgId
GROUP BY al.id
[ORDER_BY] [LIMIT]
' where `query_key` = 'My_Team_List_without_Referrals';
CDATA;
        $this->addSql($query2);
        
        $query3 = <<<CDATA
update `ebi_search` set `query` = '

SELECT 
    "logins" AS activity,
    al.activity_date AS activity_date,
    pa.external_id as team_member_external_id,
    al.person_id_faculty AS team_member_id,
    pa.firstname AS team_member_firstname,
    pa.lastname AS team_member_lastname,
    ci.primary_email,
    "" AS student_id,
    "" AS student_firstname,
    "" AS student_lastname,
    "" AS student_external_id,
    "" AS student_email,
    al.activity_type AS activity_type,
    al.referrals_id,
    al.appointments_id,
    al.note_id,
    al.contacts_id,
    "" AS activity_id,
    "" AS reason_id,
    "-" AS reason_text,
    "" AS status
FROM
    activity_log al
        LEFT JOIN
    person pa ON pa.id = al.person_id_faculty
        LEFT JOIN
    person_contact_info pci ON pci.person_id = al.person_id_faculty
        LEFT JOIN
    contact_info ci ON ci.id = pci.contact_id
WHERE
    al.activity_type IN ("L")
        AND al.activity_date BETWEEN $fromDate AND $toDate
        AND al.person_id_faculty IN ($teamMembersId)
        AND al.deleted_at IS NULL
        AND al.organization_id = $orgId
[ORDER_BY] [LIMIT]
' where `query_key` = 'My_Team_List_with_only_Logins';
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
