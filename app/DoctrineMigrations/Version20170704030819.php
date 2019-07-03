<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Database changes for Webapp> System Alerts> Changing the Archived vs Scheduled notifications
 * ESPRJ-9152
 *
 */
class Version20170704030819 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        $this->addSql("ALTER TABLE org_announcements CHANGE display_type display_type ENUM('banner','alert bell','notification bell') NULL DEFAULT NULL");
        $this->addSql("UPDATE org_announcements SET display_type = 'notification bell' WHERE display_type = 'alert bell'");
        $this->addSql("ALTER TABLE org_announcements CHANGE display_type display_type ENUM('banner','notification bell') NULL DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
    }
}