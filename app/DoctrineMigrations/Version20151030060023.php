<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151030060023 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $risklevel = '$$risklevel$$';
        $personId = '$$personId$$';
        
        $query = <<<CDATA
update ebi_search set query = "select
p.id,
p.firstname,
p.lastname,
p.risk_level,
itl.image_name as intent_imagename,
itl.text as intent_text,
rl.image_name as risk_imagename,
rl.risk_text,
p.intent_to_leave as intent_leave,
(count(distinct (lc.id))) as login_cnt,
p.cohert,
(
	select
	(case when (activity_type='N')
		then concat(DATE_FORMAT(activity_date,'%m/%d/%y'), ' - ','Note')
		when (activity_type='A')
	then concat(DATE_FORMAT(activity_date,'%m/%d/%y'), ' - ','Appointment')
		when (activity_type='C')
				then concat(DATE_FORMAT(activity_date,'%m/%d/%y'), ' - ','Contact')
		when (activity_type='E')
				then concat(DATE_FORMAT(activity_date,'%m/%d/%y'), ' - ','Email')
		when (activity_type='R')
				then concat(DATE_FORMAT(activity_date,'%m/%d/%y'), ' - ','Referral')
				else
					concat(DATE_FORMAT(activity_date,'%m/%d/%y'), ' - ','Login')
	END) as new
    from activity_log where id =  max(lc.id)
) as last_activity ,
ops.status,
itl.color_hex as intent_color,
rl.color_hex as risk_color,
1 as risk_flag,
unique_people_this_faculty_member_can_see_risk_for.intent_to_leave as intent_flag
from person p
join
(
   select person_id, max(intent_to_leave) as intent_to_leave
   from
   (
      select
      ocs.person_id, flags.intent_to_leave
      from org_course_student ocs
      join
      (
         select
         ocf.org_courses_id, op.intent_to_leave
         from org_course_faculty ocf
         join org_courses oc on oc.id = ocf.org_courses_id and oc.deleted_at is null
         join org_academic_terms oat on oat.id = oc.org_academic_terms_id and oat.deleted_at is null
         join org_permissionset op on ocf.org_permissionset_id = op.id and op.deleted_at is null
         where ocf.person_id = $personId
         and ocf.deleted_at is null
         and oat.end_date >= date(now())
         and op.risk_indicator = 1
         and op.accesslevel_ind_agg = 1
      )
      flags on flags.org_courses_id = ocs.org_courses_id and ocs.deleted_at is null
      union
      all
      select
      ogs.person_id, flags.intent_to_leave
      from org_group_students ogs
      join
      (
         select
         ogf.org_group_id, op.intent_to_leave
         from org_group_faculty ogf
         join org_permissionset op on ogf.org_permissionset_id = op.id and op.deleted_at is null
         where ogf.person_id = $personId
         and ogf.deleted_at is null
         and op.risk_indicator = 1
         and op.accesslevel_ind_agg = 1
      )
      flags on flags.org_group_id = ogs.org_group_id and ogs.deleted_at is null
   )
   non_unique_people_this_faculty_member_can_see_risk_for
   group by person_id
)
unique_people_this_faculty_member_can_see_risk_for on p.id = unique_people_this_faculty_member_can_see_risk_for.person_id
inner join org_person_student ops on ops.person_id = p.id and ops.deleted_at is null
left join risk_level rl on p.risk_level = rl.id
left join activity_log lc ON (lc.person_id_student = p.id and lc.deleted_at is null) and lc.activity_type in ('R','A','C','N','E')
left join intent_to_leave itl on itl.id = p.intent_to_leave
where p.risk_level in ($risklevel)
AND (p.last_contact_date < p.risk_update_date OR p.last_contact_date IS NULL)
and p.deleted_at is null
group by p.id
order by p.risk_level desc ,p.lastname,p.firstname" where query_key = 'My_High_priority_students_List';
CDATA;
        $this->addSql($query);
        
        
        
    }
    
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    
        $risklevel = '$$risklevel$$';
        $personId = '$$personId$$';
        
        $query = <<<CDATA
update ebi_search set query = "select
p.id,
p.firstname,
p.lastname,
p.risk_level,
itl.image_name as intent_imagename,
itl.text as intent_text,
rl.image_name as risk_imagename,
rl.risk_text,
p.intent_to_leave as intent_leave,
(count(distinct (lc.id))) as login_cnt,
p.cohert,
p.last_activity,
ops.status,
itl.color_hex as intent_color,
rl.color_hex as risk_color,
1 as risk_flag,
unique_people_this_faculty_member_can_see_risk_for.intent_to_leave as intent_flag
from person p
join
(
   select person_id, max(intent_to_leave) as intent_to_leave
   from
   (
      select
      ocs.person_id, flags.intent_to_leave
      from org_course_student ocs
      join
      (
         select
         ocf.org_courses_id, op.intent_to_leave
         from org_course_faculty ocf
         join org_courses oc on oc.id = ocf.org_courses_id and oc.deleted_at is null
         join org_academic_terms oat on oat.id = oc.org_academic_terms_id and oat.deleted_at is null
         join org_permissionset op on ocf.org_permissionset_id = op.id and op.deleted_at is null
         where ocf.person_id = $personId
         and ocf.deleted_at is null
         and oat.end_date >= date(now())
         and op.risk_indicator = 1
         and op.accesslevel_ind_agg = 1
      )
      flags on flags.org_courses_id = ocs.org_courses_id and ocs.deleted_at is null
      union
      all
      select
      ogs.person_id, flags.intent_to_leave
      from org_group_students ogs
      join
      (
         select
         ogf.org_group_id, op.intent_to_leave
         from org_group_faculty ogf
         join org_permissionset op on ogf.org_permissionset_id = op.id and op.deleted_at is null
         where ogf.person_id = $personId
         and ogf.deleted_at is null
         and op.risk_indicator = 1
         and op.accesslevel_ind_agg = 1
      )
      flags on flags.org_group_id = ogs.org_group_id and ogs.deleted_at is null
   )
   non_unique_people_this_faculty_member_can_see_risk_for
   group by person_id
)
unique_people_this_faculty_member_can_see_risk_for on p.id = unique_people_this_faculty_member_can_see_risk_for.person_id
inner join org_person_student ops on ops.person_id = p.id and ops.deleted_at is null
left join risk_level rl on p.risk_level = rl.id
left join activity_log lc ON lc.person_id_student = p.id and lc.activity_type in ('R','A','C','N','E')
left join intent_to_leave itl on itl.id = p.intent_to_leave
where p.risk_level in ($risklevel)
AND (p.last_contact_date < p.risk_update_date OR p.last_contact_date IS NULL)
and p.deleted_at is null
group by p.id
order by p.risk_level desc ,p.lastname,p.firstname" where query_key = 'My_High_priority_students_List';
CDATA;
        $this->addSql($query);
        
        
    }
    
}
