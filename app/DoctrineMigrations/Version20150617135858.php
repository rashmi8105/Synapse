<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150617135858 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        /*
         * including courses for permission set
        */
        $personId = '$$personId$$';
        $query = <<<CDATA
UPDATE `ebi_search` SET `query`='select p.risk_level, count(p.id) as totalStudentsHighPriority, rml.risk_text, rml.image_name, rml.color_hex from person p, risk_level rml where p.id in (select distinct person_id from org_group_students ogs where ogs.org_group_id in (select org_group_id from org_group_faculty where person_id = $personId and deleted_at is null and org_permissionset_id in (select id from org_permissionset where risk_indicator = 1 and deleted_at is null)) and ogs.deleted_at is null union select distinct person_id from org_course_student ocs where ocs.org_courses_id in (select org_courses_id from org_course_faculty where person_id = $personId and deleted_at is null and org_permissionset_id in (select id from org_permissionset where risk_indicator = 1 and deleted_at is null)) and ocs.deleted_at is null) and rml.id = p.risk_level and p.deleted_at is null group by p.risk_level' WHERE `query_key`='My_Total_Students_Count_Groupby_Risk';
CDATA;
        $this->addSql($query);
        
        $risklevel = '$$risklevel$$';
        $query1 = <<<CDATA
UPDATE `ebi_search` SET `query`='select count(per.id) as highCount from person per where per.id in (select distinct person_id from org_group_students ogs where ogs.org_group_id in (select org_group_id from org_group_faculty where person_id = $personId and deleted_at is null and org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null)) and ogs.deleted_at is null union select distinct person_id from org_course_student ocs where ocs.org_courses_id in (select org_courses_id from org_course_faculty where person_id = $personId and deleted_at is null and org_permissionset_id in (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null)) and ocs.deleted_at is null) and per.last_contact_date < per.risk_update_date and per.risk_level in ($risklevel) and per.deleted_at is null' WHERE `query_key`='My_High_priority_students_Count';
CDATA;
        $this->addSql($query1);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
