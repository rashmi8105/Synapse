<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script for adding sections to Compare in report_sections table
 * ESPRJ-15431
 *
 */
class Version20170713023610 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        $this->addSql('SET @compare_report_id := (SELECT id FROM reports WHERE deleted_at IS NULL AND short_code = "SUB-COM")');
        $this->addSql("INSERT INTO report_sections(created_by, modified_by, deleted_by, report_id, created_at, modified_at, deleted_at, title, sequence, section_query, retention_tracking_type, survey_contingent, academic_term_contingent, risk_contingent)
                           VALUES(-25,-25,NULL, @compare_report_id, NOW(), NOW(),NULL, 'Survey Factors', '1', '', 'none', NULL, NULL, NULL)");
        $this->addSql("INSERT INTO report_sections (created_by, modified_by, deleted_by, report_id, created_at, modified_at, deleted_at, title, sequence, section_query, retention_tracking_type, survey_contingent, academic_term_contingent, risk_contingent)
                           VALUES(-25, -25, NULL, @compare_report_id, NOW(), NOW(), NULL, 'GPA by Term', '2', '','none', NULL, NULL, NULL)");
        $this->addSql("INSERT INTO report_sections (created_by, modified_by, deleted_by, report_id, created_at, modified_at, deleted_at, title, sequence, section_query, retention_tracking_type, survey_contingent, academic_term_contingent, risk_contingent)
                           VALUES(-25, -25, NULL, @compare_report_id, NOW(), NOW(), NULL, 'Retention/Completion', '3', '', 'required', NULL, NULL, NULL)");
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
    }
}