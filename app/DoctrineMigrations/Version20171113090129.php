<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-16278 -  Migration script to add a new job type for AcademicUpdateReport and new mapworks action for the AcademicUpdateReportCsv job failure notification
 */
class Version20171113090129 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("INSERT INTO `synapse`.`job_type` (`created_by`, `modified_by`, `created_at`, `modified_at`, `job_type`) VALUES ('-25', '-25', now(), now(), 'AcademicUpdateReportCSVJob')");

        $this->addSql("INSERT INTO `mapworks_action` (`event_key`, `action`, `recipient_type`, `event_type`, `notification_body_text`, `notification_hover_text`, `receives_email`, `receives_notification`, `created_by`,`created_at`,`modified_at`) VALUES ('academic_update_report_csv_generation_failed_creator', 'csv_generation_failed', 'creator', 'academic_update_report', 'An error occurred while Mapworks was generating academic update report csv. Please contact Skyfactor client services.', 'An error occurred while generating academic update report csv.', '0', '1', '-25',now(),now())");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
