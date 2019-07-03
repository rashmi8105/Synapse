<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150921115016 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('update ebi_search set query = \'select
p.id,
p.firstname,
p.lastname,
p.risk_level,
itl.image_name as intent_imagename,
itl.text as intent_text,
rl.image_name as risk_imagename,
rl.risk_text,
p.intent_to_leave as intent_leave,
lc.cnt as login_cnt,
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
         where ocf.person_id = $$personId$$
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
         where ogf.person_id = $$personId$$
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
left join risk_level rl on p.risk_level = rl.id
left join Logins_count lc on lc.person_id = p.id
join org_person_student ops on ops.person_id = p.id and ops.deleted_at is null
left join intent_to_leave itl on itl.id = p.intent_to_leave
where p.risk_level in ($$risklevel$$)
and p.deleted_at is null
order by p.risk_level desc ,p.lastname,p.firstname\' where query_key = \'My_Total_students_List\';
update ebi_search set query = \'select
p.id,
p.firstname,
p.lastname,
p.risk_level,
itl.image_name as intent_imagename,
itl.text as intent_text,
rl.image_name as risk_imagename,
rl.risk_text,
p.intent_to_leave as intent_leave,
lc.cnt as login_cnt,
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
         where ocf.person_id = $$personId$$
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
         where ogf.person_id = $$personId$$
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
left join risk_level rl on p.risk_level = rl.id
left join Logins_count lc on lc.person_id = p.id
join org_person_student ops on ops.person_id = p.id and ops.deleted_at is null
left join intent_to_leave itl on itl.id = p.intent_to_leave
where p.deleted_at is null AND rl.risk_text = "$$riskLevel$$" order by p.lastname,p.firstname\' where query_key = \'My_Total_students_List_By_RiskLevel\';
update ebi_search set query = \'SELECT RL.id AS risk_level, count(DISTINCT P.id) as totalStudentsHighPriority, RL.risk_text, RL.image_name, RL.color_hex FROM risk_level AS RL INNER JOIN person AS P ON P.risk_level=RL.id AND P.deleted_at IS NULL INNER JOIN (SELECT S.person_id AS person_id, F.org_permissionset_id AS permissionset_id FROM org_group_students AS S INNER JOIN org_group_faculty AS F ON F.org_group_id = S.org_group_id and F.deleted_at is null WHERE S.deleted_at is null AND F.person_id=$$personId$$ UNION ALL SELECT S.person_id AS student_id, F.org_permissionset_id AS permissionset_id FROM org_course_student AS S INNER JOIN org_courses AS C ON C.id = S.org_courses_id AND C.deleted_at is null INNER JOIN org_course_faculty AS F ON F.org_courses_id = S.org_courses_id AND F.deleted_at is null INNER JOIN org_academic_terms AS OAT ON OAT.id = C.org_academic_terms_id AND OAT.end_date >= now() AND OAT.deleted_at is null WHERE S.deleted_at is null AND F.person_id=$$personId$$) AS merged ON merged.person_id=P.id INNER JOIN org_permissionset OPS ON merged.permissionset_id = OPS.id AND (OPS.accesslevel_ind_agg = 1) AND OPS.risk_indicator = 1 GROUP BY RL.id\' where query_key=\'My_Total_Students_Count_Groupby_Risk\';
update ebi_search set query = \'select
p.id,
p.firstname,
p.lastname,
p.risk_level,
itl.image_name as intent_imagename,
itl.text as intent_text,
rl.image_name as risk_imagename,
rl.risk_text,
p.intent_to_leave as intent_leave,
lc.cnt as login_cnt,
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
         where ocf.person_id = $$personId$$
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
         where ogf.person_id = $$personId$$
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
left join risk_level rl on p.risk_level = rl.id
left join Logins_count lc on lc.person_id = p.id
join org_person_student ops on ops.person_id = p.id and ops.deleted_at is null
left join intent_to_leave itl on itl.id = p.intent_to_leave
where p.risk_level in ($$risklevel$$)
AND (p.last_contact_date < p.risk_update_date OR p.last_contact_date IS NULL)
and p.deleted_at is null
order by p.risk_level desc ,p.lastname,p.firstname\' where query_key = \'My_High_priority_students_List\';
update ebi_search set query = \'select
count(distinct id) as highCount
from person p
join
(
   select
   ogs.person_id
   from org_group_students ogs
   join org_group_faculty ogf on ogs.org_group_id = ogf.org_group_id
   and ogf.person_id = $$personId$$
   and ogf.deleted_at is null
   join org_permissionset op on op.id = ogf.org_permissionset_id
   and op.deleted_at is null
   and op.risk_indicator = 1
   and op.accesslevel_ind_agg = 1
   where ogs.deleted_at is null
   union
   all
   select
   ocs.person_id
   from org_course_student ocs
   join org_course_faculty ocf on ocs.org_courses_id = ocf.org_courses_id
   and ocf.person_id = $$personId$$
   and ocf.deleted_at is null
   join org_permissionset op on op.id = ocf.org_permissionset_id
   and op.deleted_at is null
   and op.risk_indicator = 1
   and op.accesslevel_ind_agg = 1
   join org_courses oc on oc.id = ocf.org_courses_id
   and oc.deleted_at is null
   join org_academic_terms oat on oat.id = oc.org_academic_terms_id
   and oat.deleted_at is null
   and oat.end_date >= date(now())
   where ocs.deleted_at is null
)
people_faculty_can_see_risk_for on p.id = people_faculty_can_see_risk_for.person_id
where (p.last_contact_date <= p.risk_update_date or p.last_contact_date is null)
and p.deleted_at is null
and p.risk_level in ($$risklevel$$)\' where query_key = \'My_High_priority_students_Count\';');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
