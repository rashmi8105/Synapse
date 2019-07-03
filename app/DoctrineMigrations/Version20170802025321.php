<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Database changes for Analysis module
 *
 * Add migration script to add new column in mapworks_tool table
 * and create new table mapworks_tool_last_run
 *
 * Ticket id - ESPRJ-15626
 *
 */
class Version20170802025321 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        $this->addSql('ALTER TABLE `mapworks_tool` ADD COLUMN `tool_order` INTEGER DEFAULT NULL AFTER `can_access_with_aggregate_only_permission`');
        $this->addSQL("ALTER TABLE `mapworks_tool` ADD UNIQUE(`tool_order`)");
        $this->addSQL("UPDATE `mapworks_tool` SET `tool_order` = 1 WHERE `short_code` = 'T-I'");

        $this->addSQL("
        CREATE TABLE IF NOT EXISTS `synapse`.`mapworks_tool_last_run` (
          `id` int(11) PRIMARY KEY AUTO_INCREMENT,
          `tool_id` int(11) UNSIGNED NOT NULL,
          `person_id` int(11) NOT NULL,
          `last_run` DATETIME NOT NULL,
          `created_by` int(11) DEFAULT NULL,
          `created_at` DATETIME,
          `modified_by` int(11) DEFAULT NULL,
          `modified_at` DATETIME,
          `deleted_by` int(11) DEFAULT NULL,
          `deleted_at` DATETIME,
          FOREIGN KEY (tool_id) REFERENCES synapse.mapworks_tool(id),
          FOREIGN KEY (person_id) REFERENCES synapse.person(id),
          FOREIGN KEY (created_by) REFERENCES synapse.person(id),
          FOREIGN KEY (modified_by) REFERENCES synapse.person(id), 
          FOREIGN KEY (deleted_by) REFERENCES synapse.person(id)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        $this->addSql('ALTER TABLE `mapworks_tool` DROP COLUMN `tool_order`');
        $this->addSql('DROP TABLE IF EXISTS `mapworks_tool_last_run`');
    }
}
