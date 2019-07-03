<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-16517 - Migration script to add new mapworks_action row for action "student_report_create_student"
 */
class Version20171123091538 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("INSERT INTO `mapworks_action` (`event_key`, `action`, `recipient_type`, `event_type`, `receives_email`, `receives_notification`,`created_by`,`created_at`) 
                       VALUES ('student_report_create_student', 'create', 'student', 'student_report', 1, 0, -25, NOW())");

        $this->addSql("UPDATE 
                             mapworks_action ma
                        SET
                             email_template_id = (SELECT id FROM email_template where email_key = 'Email_PDF_Report_Student')                             
                        WHERE
                             event_key = 'student_report_create_student';");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
