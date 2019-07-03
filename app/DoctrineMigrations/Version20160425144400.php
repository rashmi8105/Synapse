<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script for creating group closure table 
 */
class Version20160425144400 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //not user create or replace for considering the dependency order of these two view
        $this->addSql('DROP VIEW IF EXISTS `org_faculty_student_permission_map`');
        $this->addSql('DROP VIEW IF EXISTS `org_group_faculty_student_permission_map`');

        /* @org_group_faculty_student_permission_map
         1. add group hierarchy with closure table org_group_tree in
         2. replace group_id in the selection as faculty_group_id
         3. add student_group_id in the selection
         * */
        $this->addSql("CREATE
            ALGORITHM = MERGE
            DEFINER = `synapsemaster`@`%`
            SQL SECURITY DEFINER
        VIEW `org_group_faculty_student_permission_map` AS
            SELECT
                `OG`.`id` AS `faculty_group_id`,
                `OGS`.`org_group_id` AS `student_group_id`,
                `OG`.`organization_id` AS `org_id`,
                `OGF`.`person_id` AS `faculty_id`,
                `OGS`.`person_id` AS `student_id`,
                `OGF`.`org_permissionset_id` AS `permissionset_id`
            FROM
                `org_group_faculty` `OGF` FORCE INDEX (`PG_PERM`)
                JOIN `org_group` `OG` ON (((`OG`.`id` = `OGF`.`org_group_id`)
                AND (`OG`.`organization_id` = `OGF`.`organization_id`)
                AND ISNULL(`OG`.`deleted_at`)))
                JOIN `org_group_tree` `OGT` ON (`OGT`.`ancestor_group_id` = `OG`.`id`
                AND ISNULL(`OGT`.`deleted_at`))
                JOIN `org_group` `OG2` ON (`OG2`.`id` = `OGT`.`descendant_group_id`
                AND ISNULL(`OG2`.`deleted_at`))
                JOIN `org_group_students` `OGS` FORCE INDEX (`GROUP-STUDENT`) FORCE INDEX (`STUDENT-GROUP`) ON (`OGS`.`org_group_id` = `OG2`.`id`
                AND (`OGS`.`organization_id` = `OG2`.`organization_id`)
                AND ISNULL(`OGS`.`deleted_at`))
            WHERE
                ISNULL(`OGF`.`deleted_at`)");

         /* @org_faculty_student_permission_map
         * replace group_id as faculty_group_id according the change 2. of @org_group_faculty_student_permission_map
         * */
        $this->addSql("CREATE
            ALGORITHM = UNDEFINED
            DEFINER = `synapsemaster`@`%`
            SQL SECURITY DEFINER
        VIEW `org_faculty_student_permission_map` AS
            SELECT
                `OPF`.`organization_id` AS `org_id`,
                `OPF`.`person_id` AS `faculty_id`,
                COALESCE(`OGM`.`student_id`, `OCM`.`student_id`) AS `student_id`,
                `OGM`.`faculty_group_id`,
                `OGM`.`student_group_id`,
                `OCM`.`course_id` AS `course_id`,
                COALESCE(`OGM`.`permissionset_id`,
                    `OCM`.`permissionset_id`) AS `permissionset_id`
            FROM
            (((`org_person_faculty` `OPF`
                JOIN `group_course_discriminator` `GCD`)
                LEFT JOIN `org_group_faculty_student_permission_map` `OGM` ON ((((`OGM`.`org_id` , `OGM`.`faculty_id`) = (`OPF`.`organization_id` , `OPF`.`person_id`))
                    AND (`GCD`.`association` = 'group'))))
                LEFT JOIN `org_course_faculty_student_permission_map` `OCM` ON ((((`OCM`.`org_id` , `OCM`.`faculty_id`) = (`OPF`.`organization_id` , `OPF`.`person_id`))
                    AND (`GCD`.`association` = 'course'))))
            WHERE
            (((`OGM`.`faculty_group_id` IS NOT NULL)
                    OR (`OCM`.`course_id` IS NOT NULL))
                    AND ISNULL(`OPF`.`deleted_at`))");

            }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
