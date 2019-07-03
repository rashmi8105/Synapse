<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Correcting data for ESPRJ-11786 (https://jira-mnv.atlassian.net/browse/ESPRJ-11786).
 * Sets the value in the `max` column of risk model 26 level 3 to 4.5 instead of 4.45.
 */
class Version20160927115634 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("
            UPDATE `synapse`.`risk_model_levels` SET `max` = '4.5000' WHERE `risk_model_id` = '26' AND `risk_level` = '3';
        ");

    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

    }
}