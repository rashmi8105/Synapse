<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160126162502 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("update metadata_list_values set list_name = 'CentralCanada' where list_value = 'Canada/Central'");
        $this->addSql("update metadata_list_values set list_name = 'EasternCanada' where list_value = 'Canada/Eastern'");
        $this->addSql("update metadata_list_values set list_name = 'MountainCanada' where list_value = 'Canada/Mountain'");
        $this->addSql("update metadata_list_values set list_name = 'PacificCanada' where list_value = 'Canada/Pacific';");
        $this->addSql("update organization set time_zone = 'EasternCanada' where subdomain = 'uwaterloo'");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

    }
}
