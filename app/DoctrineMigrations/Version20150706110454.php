<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150706110454 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        /*
         * including courses for permission set
        */
        $personId = '$$personId$$';
        $risklevel = '$$risklevel$$';
        $riskLevel = '$$riskLevel$$';
        $acessStudents = '$$acessStudents$$';
        
        /*My_Total_Students_Count_Groupby_Risk*/
        $query = <<<CDATA
UPDATE `ebi_search` SET `query`='select p.risk_level, count(p.id) as totalStudentsHighPriority, rml.risk_text, rml.image_name, rml.color_hex from person p, risk_level rml where p.id in (select distinct person_id from org_group_students where org_group_id in (select org_group_id from org_group_faculty where person_id =
$personId and deleted_at is null and org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg = 1 and risk_indicator = 1 and deleted_at is null)) and deleted_at is null union select distinct person_id from org_course_student where org_courses_id in (select org_courses_id from org_course_faculty where person_id = $personId and deleted_at is null and org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg = 1 and risk_indicator = 1 and deleted_at is null) and org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date >= now()))) and deleted_at is null) and rml.id = p.risk_level and p.deleted_at is null group by p.risk_level' WHERE `query_key`='My_Total_Students_Count_Groupby_Risk';
CDATA;
        $this->addSql($query);
        
        /*My_High_priority_students_Count*/
        $query1 = <<<CDATA
UPDATE `ebi_search` SET `query`='select count(per.id) as highCount from person per where per.id in
(select distinct person_id from org_group_students where org_group_id in (select org_group_id from org_group_faculty where person_id = $personId and deleted_at is null and org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg = 1 and risk_indicator = 1 and deleted_at is null)) and deleted_at is null union select distinct person_id from org_course_student where org_courses_id in (select org_courses_id from org_course_faculty where person_id = $personId and deleted_at is null and org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg = 1 and risk_indicator = 1 and deleted_at is null) and org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date >= now()))) and deleted_at is null) and per.last_contact_date < per.risk_update_date and per.risk_level in ($risklevel) and per.deleted_at is null' WHERE `query_key`='My_High_priority_students_Count';
CDATA;
        $this->addSql($query1);
        
        
        /*My_High_priority_students_List*/
        $query2 = <<<CDATA
UPDATE `ebi_search` SET `query`='select p.id,p.firstname, p.lastname,p.risk_level,il.image_name as intent_imagename,il.text as intent_text,rl.image_name as risk_imagename,rl.risk_text,p.intent_to_leave as intent_leave, lc.cnt as login_cnt, p.cohert, p.last_activity,ps.status, il.color_hex as intent_color, rl.color_hex as risk_color, (CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($personId) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($personId) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date >= now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then "1" else "0" end) as risk_flag ,(CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($personId) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($personId) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date >= now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then "1" else "0" end) as intent_flag from person p join risk_level rl on (p.risk_level = rl.id) left join intent_to_leave il on (p.intent_to_leave = il.id) left join org_person_student as ps on p.id=ps.person_id left outer join Logins_count lc on (lc.person_id = p.id) where (p.id in ( $acessStudents) ) and p.last_contact_date < p.risk_update_date and p.risk_level in ($risklevel) and p.deleted_at is null order by p.risk_level desc ,p.lastname,p.firstname;' WHERE `query_key`='My_High_priority_students_List';
CDATA;
        $this->addSql($query2);
        
        /*My_Total_students_List_By_RiskLevel*/
        $query3 = <<<CDATA
UPDATE `ebi_search` SET `query`='select p.id,p.firstname, p.lastname,p.risk_level,il.image_name as intent_imagename,il.text as intent_text,rl.image_name as risk_imagename,rl.risk_text,p.intent_to_leave as intent_leave, lc.cnt as login_cnt, p.cohert, p.last_activity,ps.status, il.color_hex as intent_color, rl.color_hex as risk_color, (CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($personId) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($personId) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date >= now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then "1" else "0" end) as risk_flag ,(CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($personId) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($personId) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date >= now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then "1" else "0" end) as intent_flag from person p join risk_level rl on (p.risk_level = rl.id) left join intent_to_leave il on (p.intent_to_leave = il.id) left join org_person_student as ps on p.id=ps.person_id left outer join Logins_count lc on (lc.person_id = p.id) where (p.id in ($acessStudents) ) and rl.risk_text = "$riskLevel" and p.deleted_at is null order by p.risk_level desc ,p.lastname,p.firstname;' WHERE `query_key`='My_Total_students_List_By_RiskLevel';
CDATA;
        $this->addSql($query3);
        
        /*My_Total_students_List*/
        $query4 = <<<CDATA
UPDATE `ebi_search` SET `query`='select p.id,p.firstname, p.lastname,p.risk_level,il.image_name as intent_imagename,il.text as intent_text,rl.image_name as risk_imagename,rl.risk_text,p.intent_to_leave as intent_leave, lc.cnt as login_cnt, p.cohert, p.last_activity,ps.status, il.color_hex as intent_color, rl.color_hex as risk_color, (CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($personId) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($personId) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date >= now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then "1" else "0" end) as risk_flag ,(CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($personId) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($personId) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date >= now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then "1" else "0" end) as intent_flag from person p join risk_level rl on (p.risk_level = rl.id) left join intent_to_leave il on (p.intent_to_leave = il.id) left join org_person_student as ps on p.id=ps.person_id left outer join Logins_count lc on (lc.person_id = p.id) where (p.id in ($acessStudents) ) and p.risk_level in ($risklevel) and p.deleted_at is null order by p.risk_level desc ,p.lastname,p.firstname;' WHERE `query_key`='My_Total_students_List';
CDATA;
        $this->addSql($query4);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        /*
         * including courses for permission set
        */
        $personId = '$$personId$$';
        $risklevel = '$$risklevel$$';
        $riskLevel = '$$riskLevel$$';
        
        /*My_Total_Students_Count_Groupby_Risk*/
        $query = <<<CDATA
UPDATE `ebi_search` SET `query`='select p.risk_level, count(p.id) as totalStudentsHighPriority, rml.risk_text, rml.image_name, rml.color_hex from person p, risk_level rml where p.id in (select distinct person_id from org_group_students where org_group_id in (select org_group_id from org_group_faculty where person_id = $personId and deleted_at is null and org_permissionset_id in (select id from org_permissionset where risk_indicator = 1 and deleted_at is null)) and deleted_at is null union select distinct person_id from org_course_student where org_courses_id in (select org_courses_id from org_course_faculty where person_id = $personId and deleted_at is null and org_permissionset_id in (select id from org_permissionset where risk_indicator = 1 and deleted_at is null) and org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now()))) and deleted_at is null) and rml.id = p.risk_level and p.deleted_at is null group by p.risk_level' WHERE `query_key`='My_Total_Students_Count_Groupby_Risk';
CDATA;
        $this->addSql($query);
        
        /*My_High_priority_students_Count*/
        $query1 = <<<CDATA
UPDATE `ebi_search` SET `query`='select count(per.id) as highCount from person per where per.id in
(select distinct person_id from org_group_students where org_group_id in (select org_group_id from org_group_faculty where person_id = $personId and deleted_at is null and org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null)) and deleted_at is null union select distinct person_id from org_course_student where org_courses_id in (select org_courses_id from org_course_faculty where person_id = $personId and deleted_at is null and org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null) and org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now()))) and deleted_at is null) and per.last_contact_date < per.risk_update_date and per.risk_level in ($risklevel) and per.deleted_at is null' WHERE `query_key`='My_High_priority_students_Count';
CDATA;
        $this->addSql($query1);
        
        
        /*My_High_priority_students_List*/
        $query2 = <<<CDATA
UPDATE `ebi_search` SET `query`='select p.id,p.firstname, p.lastname,p.risk_level,il.image_name as intent_imagename,il.text as intent_text,rl.image_name as risk_imagename,rl.risk_text,p.intent_to_leave as intent_leave, lc.cnt as login_cnt, p.cohert, p.last_activity,ps.status, il.color_hex as intent_color, rl.color_hex as risk_color, (CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($personId) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($personId) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then "1" else "0" end) as risk_flag ,(CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($personId) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($personId) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then "1" else "0" end) as intent_flag from person p join risk_level rl on (p.risk_level = rl.id) left join intent_to_leave il on (p.intent_to_leave = il.id) left join org_person_student as ps on p.id=ps.person_id left outer join Logins_count lc on (lc.person_id = p.id) where (p.id in ( select distinct person_id from org_group_students ogs where ogs.org_group_id in (select org_group_id from org_group_faculty where person_id = ($personId) and deleted_at is null and org_permissionset_id in(select id from org_permissionset op where accesslevel_ind_agg = 1 and deleted_at is null) ) and ogs.deleted_at is null UNION select distinct person_id from org_course_student ocs where ocs.org_courses_id in (select org_courses_id from org_course_faculty where person_id = ($personId) and deleted_at is null and org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now()))) and ocs.deleted_at is null ) ) and p.last_contact_date < p.risk_update_date and p.risk_level in ($risklevel) and p.deleted_at is null order by p.risk_level desc ,p.lastname,p.firstname;' WHERE `query_key`='My_High_priority_students_List';
CDATA;
        $this->addSql($query2);
        
        /*My_Total_students_List_By_RiskLevel*/
        $query3 = <<<CDATA
UPDATE `ebi_search` SET `query`='select p.id,p.firstname, p.lastname,p.risk_level,il.image_name as intent_imagename,il.text as intent_text,rl.image_name as risk_imagename,rl.risk_text,p.intent_to_leave as intent_leave, lc.cnt as login_cnt, p.cohert, p.last_activity,ps.status, il.color_hex as intent_color, rl.color_hex as risk_color, (CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($personId) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($personId) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then "1" else "0" end) as risk_flag ,(CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($personId) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($personId) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then "1" else "0" end) as intent_flag from person p join risk_level rl on (p.risk_level = rl.id) left join intent_to_leave il on (p.intent_to_leave = il.id) left join org_person_student as ps on p.id=ps.person_id left outer join Logins_count lc on (lc.person_id = p.id) where (p.id in ( select distinct person_id from org_group_students ogs where ogs.org_group_id in (select org_group_id from org_group_faculty where person_id = ($personId) and deleted_at is null and org_permissionset_id in(select id from org_permissionset op where accesslevel_ind_agg = 1 and deleted_at is null) ) and ogs.deleted_at is null UNION select distinct person_id from org_course_student ocs where ocs.org_courses_id in (select org_courses_id from org_course_faculty where person_id = ($personId) and deleted_at is null and org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now()))) and ocs.deleted_at is null ) ) and rl.risk_text = "$riskLevel" and p.deleted_at is null order by p.risk_level desc ,p.lastname,p.firstname;' WHERE `query_key`='My_Total_students_List_By_RiskLevel';
CDATA;
        $this->addSql($query3);
        
        /*My_Total_students_List*/
        $query4 = <<<CDATA
UPDATE `ebi_search` SET `query`='select p.id,p.firstname, p.lastname,p.risk_level,il.image_name as intent_imagename,il.text as intent_text,rl.image_name as risk_imagename,rl.risk_text,p.intent_to_leave as intent_leave, lc.cnt as login_cnt, p.cohert, p.last_activity,ps.status, il.color_hex as intent_color, rl.color_hex as risk_color, (CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($personId) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($personId) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and risk_indicator= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then "1" else "0" end) as risk_flag ,(CASE when (( (select distinct(ogs.person_id) from org_group_students ogs, org_group_faculty ogf, person ip where ogs.org_group_id=ogf.org_group_id and ogf.person_id=($personId) and ogs.person_id=p.id and ogs.person_id = ip.id and ogf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) order by ip.risk_level desc ,ip.lastname,ip.firstname) = p.id) or ((select distinct(ocs.person_id) from org_course_student ocs, org_course_faculty ocf, person ip where ocs.org_courses_id=ocf.org_courses_id and ocf.person_id=($personId) and ocs.person_id=p.id and ocs.person_id = ip.id and ocf.org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg=1 and intent_to_leave= 1 and deleted_at is null) and ocf.org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now())) order by ip.risk_level desc ,ip.lastname,ip.firstname )= p.id)) then "1" else "0" end) as intent_flag from person p join risk_level rl on (p.risk_level = rl.id) left join intent_to_leave il on (p.intent_to_leave = il.id) left join org_person_student as ps on p.id=ps.person_id left outer join Logins_count lc on (lc.person_id = p.id) where (p.id in ( select distinct person_id from org_group_students ogs where ogs.org_group_id in (select org_group_id from org_group_faculty where person_id = ($personId) and deleted_at is null and org_permissionset_id in(select id from org_permissionset op where accesslevel_ind_agg = 1 and deleted_at is null) ) and ogs.deleted_at is null UNION select distinct person_id from org_course_student ocs where ocs.org_courses_id in (select org_courses_id from org_course_faculty where person_id = ($personId) and deleted_at is null and org_courses_id in (select id from org_courses where deleted_at is null and org_academic_terms_id in (select id from org_academic_terms where deleted_at is null and end_date > now()))) and ocs.deleted_at is null ) ) and p.risk_level in ($risklevel) and p.deleted_at is null order by p.risk_level desc ,p.lastname,p.firstname;' WHERE `query_key`='My_Total_students_List';
CDATA;
        $this->addSql($query4);
    }
}
