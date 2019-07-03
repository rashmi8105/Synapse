<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add migration script to add new column in report_running_json table
 * ESPRJ-15478
 *
 */
class Version20170719082433 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        $this->addSql('ALTER TABLE reports_running_json ADD COLUMN retention_completion_json LONGTEXT DEFAULT NULL AFTER gpa_json');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        $this->addSql('ALTER TABLE reports_running_json DROP COLUMN retention_completion_json');
    }
}