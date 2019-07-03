<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-11083
 * Creating Migration script to remove all but one flag per person per survey
 * Saving Only the most recent student report flag
 */
class Version20160706201841 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSQL('DROP TABLE IF EXISTS `org_calc_flags_student_reports_temporary`;');

        $this->addSQL("CREATE TABLE `org_calc_flags_student_reports_temporary` LIKE `org_calc_flags_student_reports`;");

        $this->addSQL("INSERT INTO `org_calc_flags_student_reports_temporary` (
                          `created_by`,
                          `modified_by`,
                          `deleted_by`,
                          `org_id`,
                          `person_id`,
                          `created_at`,
                          `modified_at`,
                          `deleted_at`,
                          `calculated_at`,
                          `report_id`,
                          `survey_id`,
                          `file_name`,
                          `is_email_sent`)
                        select
                          `oldocfsr`.`created_by`,
                          `oldocfsr`.`modified_by`,
                          `oldocfsr`.`deleted_by`,
                          `oldocfsr`.`org_id`,
                          `oldocfsr`.`person_id`,
                          `oldocfsr`.`created_at`,
                          `oldocfsr`.`modified_at`,
                          `oldocfsr`.`deleted_at`,
                          `oldocfsr`.`calculated_at`,
                          `oldocfsr`.`report_id`,
                          `oldocfsr`.`survey_id`,
                          `oldocfsr`.`file_name`,
                          `oldocfsr`.`is_email_sent`
                        FROM
                            org_calc_flags_student_reports AS oldocfsr
                              INNER JOIN organization AS org ON oldocfsr.org_id = org.id
                        WHERE
                          (org.status <> 'I'
                          OR org.status IS NULL)
                          AND oldocfsr.modified_at = (SELECT
                                                        modified_at
                                                     FROM
                                                        org_calc_flags_student_reports AS newocfsr
                                                     WHERE
                                                        newocfsr.org_id = oldocfsr.org_id
                                                        AND newocfsr.person_id = oldocfsr.person_id
                                                        AND newocfsr.survey_id = oldocfsr.survey_id
                                                     ORDER BY modified_at DESC LIMIT 1);");

        $this->addSql('TRUNCATE org_calc_flags_student_reports;');

        //Removed modified_by because of constraint failure
        $this->addSql('INSERT INTO `org_calc_flags_student_reports` (
                          `created_by`,
                          `deleted_by`,
                          `org_id`,
                          `person_id`,
                          `created_at`,
                          `modified_at`,
                          `deleted_at`,
                          `calculated_at`,
                          `report_id`,
                          `survey_id`,
                          `file_name`,
                          `is_email_sent`)
                        SELECT
                          `created_by`,
                          `deleted_by`,
                          `org_id`,
                          `person_id`,
                          `created_at`,
                          `modified_at`,
                          `deleted_at`,
                          `calculated_at`,
                          `report_id`,
                          `survey_id`,
                          `file_name`,
                          `is_email_sent`
                        FROM
                            `org_calc_flags_student_reports_temporary`;');

        $this->addSql('DROP TABLE IF EXISTS `org_calc_flags_student_reports_temporary`;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
