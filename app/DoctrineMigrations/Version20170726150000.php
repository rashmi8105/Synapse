<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script to create "mapworks_tool" and "org_permissionset_tool" tables for functional testing and inserting test data.
 *
 * ESPRJ-15180
 */
class Version20170726150000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        $this->addSql('DROP TABLE IF EXISTS `org_permissionset_tool`');
        $this->addSql('DROP TABLE IF EXISTS `mapworks_tool`');
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
              KEY `IDX_34DCD17632C8A3GH` (`created_by`),
              KEY `IDX_34DCD176DE12AB59` (`modified_by`),
              KEY `IDX_34DCD17625F94813` (`deleted_by`),
              CONSTRAINT `FK_34DCD17632C8A3GH` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
              CONSTRAINT `FK_34DCD176DE12AB59` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
              CONSTRAINT `FK_34DCD17625F94813` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`)
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
            KEY `idx_34dcd17632c8a3ef` (`created_by`),
            KEY `idx_34dcd17625f94815` (`deleted_by`),
            KEY `idx_34dcd17625f94810` (`organization_id`),
            KEY `idx_34dcd17625f94811` (`mapworks_tool_id`),
            KEY `idx_34dcd176de12ab68` (`modified_by`),
            KEY `idx_34dcd17625f94812` (`org_permissionset_id`),
            CONSTRAINT `fk_34dcd17625f94810` FOREIGN KEY (`organization_id`)
                REFERENCES `organization` (`id`),
            CONSTRAINT `fk_34dcd17625f94811` FOREIGN KEY (`mapworks_tool_id`)
                REFERENCES `mapworks_tool` (`id`),
            CONSTRAINT `fk_34dcd17625f94812` FOREIGN KEY (`org_permissionset_id`)
                REFERENCES `org_permissionset` (`id`),
            CONSTRAINT `fk_34dcd17632c8a3ef` FOREIGN KEY (`created_by`)
                REFERENCES `person` (`id`),
            CONSTRAINT `fk_34dcd176de12ab68` FOREIGN KEY (`modified_by`)
                REFERENCES `person` (`id`),
            CONSTRAINT `fk_34dcd17625f94815` FOREIGN KEY (`deleted_by`)
                REFERENCES `person` (`id`)
        )  ENGINE=INNODB DEFAULT CHARSET=utf8");
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        $this->addSql("DROP TABLE IF EXISTS org_permissionset_tool");
        $this->addSql("DROP TABLE IF EXISTS mapworks_tool");
    }
}
