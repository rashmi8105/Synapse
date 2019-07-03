<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script to create "mapworks_tool" and "org_permissionset_tool" tables for accommodating "Follow-Up" & "Rank" in permission sets.
 *
 * ESPRJ-15146
 */
class Version20170629021738 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        $this->addSql("CREATE TABLE `mapworks_tool` (
              `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `tool_name` varchar(300) NOT NULL,
              `short_code` varchar(100) NOT NULL,
              `can_access_with_aggregate_only_permission` tinyint(1) UNSIGNED NOT NULL,
              `created_by` int(11) DEFAULT NULL,
              `created_at` datetime DEFAULT NULL,
              `modified_by` int(11) DEFAULT NULL,
              `modified_at` datetime DEFAULT NULL,
              `deleted_by` int(11) DEFAULT NULL,
              `deleted_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `IDX_34DCD17632C8A3DF` (`created_by`),
              KEY `IDX_34DCD176DE12AB57` (`modified_by`),
              KEY `IDX_34DCD17625F94803` (`deleted_by`),
              CONSTRAINT `FK_34DCD17632C8A3DF` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
              CONSTRAINT `FK_34DCD176DE12AB57` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
              CONSTRAINT `FK_34DCD17625F94803` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8");


        $this->addSql("CREATE TABLE `org_permissionset_tool` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `mapworks_tool_id` INT(11) UNSIGNED NOT NULL,
            `organization_id` INT(11) NOT NULL,
            `org_permissionset_id` INT(11) NOT NULL,
            `created_by` INT(11) DEFAULT NULL,
            `created_at` DATETIME DEFAULT NULL,
            `modified_by` INT(11) DEFAULT NULL,
            `modified_at` DATETIME DEFAULT NULL,
            `deleted_by` INT(11) DEFAULT NULL,
            `deleted_at` DATETIME DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_34dcd17632c8a3dg` (`created_by`),
            KEY `idx_34dcd17625f94804` (`deleted_by`),
            KEY `idx_34dcd17625f94805` (`organization_id`),
            KEY `idx_34dcd17625f94806` (`mapworks_tool_id`),
            KEY `idx_34dcd176de12ab58` (`modified_by`),
            KEY `idx_34dcd17625f94807` (`org_permissionset_id`),
            CONSTRAINT `fk_34dcd17625f94805` FOREIGN KEY (`organization_id`)
                REFERENCES `organization` (`id`),
            CONSTRAINT `fk_34dcd17625f94806` FOREIGN KEY (`mapworks_tool_id`)
                REFERENCES `mapworks_tool` (`id`),
            CONSTRAINT `fk_34dcd17625f94807` FOREIGN KEY (`org_permissionset_id`)
                REFERENCES `org_permissionset` (`id`),
            CONSTRAINT `fk_34dcd17632c8a3dg` FOREIGN KEY (`created_by`)
                REFERENCES `person` (`id`),
            CONSTRAINT `fk_34dcd176de12ab58` FOREIGN KEY (`modified_by`)
                REFERENCES `person` (`id`),
            CONSTRAINT `fk_34dcd17625f94804` FOREIGN KEY (`deleted_by`)
                REFERENCES `person` (`id`)
        )  ENGINE=INNODB DEFAULT CHARSET=utf8");
        $this->addSql("
            INSERT INTO `mapworks_tool`
              (`tool_name`, `short_code`, `can_access_with_aggregate_only_permission`, `created_by`, `created_at`, `modified_by`, `modified_at`)
            VALUES
              ('Top Issues', 'T-I', 1, -25, NOW(), -25, NOW());");
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        $this->addSql("DROP TABLE org_permissionset_tool");
        $this->addSql("DROP TABLE mapworks_tool");
    }
}
