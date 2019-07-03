<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160111070326 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
       
         $dashboardSql1 = 'update ebi_search set query = \'SELECT RL.id AS risk_level, count(DISTINCT P.id) as totalStudentsHighPriority, RL.risk_text, RL.image_name, RL.color_hex FROM risk_level AS RL INNER JOIN person AS P ON P.risk_level=RL.id AND P.deleted_at IS NULL LEFT JOIN org_person_student os on (os.person_id = P.id and os.organization_id = P.organization_id ) INNER JOIN (SELECT S.person_id AS person_id, F.org_permissionset_id AS permissionset_id FROM org_group_students AS S INNER JOIN org_group_faculty AS F ON F.org_group_id = S.org_group_id and F.deleted_at is null WHERE S.deleted_at is null AND F.person_id=$$personId$$ UNION ALL SELECT S.person_id AS student_id, F.org_permissionset_id AS permissionset_id FROM org_course_student AS S INNER JOIN org_courses AS C ON C.id = S.org_courses_id AND C.deleted_at is null INNER JOIN org_course_faculty AS F ON F.org_courses_id = S.org_courses_id AND F.deleted_at is null INNER JOIN org_academic_terms AS OAT ON OAT.id = C.org_academic_terms_id AND OAT.end_date >= now() AND OAT.deleted_at is null WHERE S.deleted_at is null AND F.person_id=$$personId$$) AS merged ON merged.person_id=P.id INNER JOIN org_permissionset OPS ON merged.permissionset_id = OPS.id AND (OPS.accesslevel_ind_agg = 1) AND OPS.risk_indicator = 1 WHERE (os.status is null or os.status = 1) AND os.organization_id = $$orgId$$ and os.deleted_at is null GROUP BY RL.id\' where query_key=\'My_Total_Students_Count_Groupby_Risk\';';
         $this->addSql($dashboardSql1);
         
         $personId = '$$personId$$';
         $risklevel = '$$risklevel$$';
         $orgId = '$$orgId$$';
         $dashboardSql2= <<<CDATA
        update `ebi_search` set `query` = 'select count(distinct p.id) as highCount
        from person p
        LEFT JOIN org_person_student os on (os.person_id = p.id and os.organization_id = p.organization_id )     
        join
        (
           select
           ogs.person_id
           from org_group_students ogs
           join org_group_faculty ogf on ogs.org_group_id = ogf.org_group_id
           and ogf.person_id = $personId
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
           and ocf.person_id = $personId
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
        and p.risk_level in ($risklevel) AND (os.status is null or os.status = 1) AND os.organization_id =  $orgId and os.deleted_at is null'
        where `query_key` = 'My_High_priority_students_Count';
CDATA;
        $this->addSql($dashboardSql2);
          
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
    }
}
