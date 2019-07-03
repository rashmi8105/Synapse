<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150616140732 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE appointment_connection_info (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, type enum(\'G\',\'E\'), server_url VARCHAR(255) DEFAULT NULL, server_port VARCHAR(45) DEFAULT NULL, auth_key VARCHAR(255) DEFAULT NULL, parameter1 VARCHAR(1000) DEFAULT NULL, parameter2 VARCHAR(1000) DEFAULT NULL, parameter3 VARCHAR(1000) DEFAULT NULL, parameter4 VARCHAR(1000) DEFAULT NULL, parameter5 VARCHAR(1000) DEFAULT NULL, INDEX IDX_75A882CDDE12AB56 (created_by), INDEX IDX_75A882CD25F94802 (modified_by), INDEX IDX_75A882CD1F6FA0AF (deleted_by), INDEX fk_appointment_connection_info_organization1_idx (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appointment_connection_info ADD CONSTRAINT FK_75A882CDDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE appointment_connection_info ADD CONSTRAINT FK_75A882CD25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE appointment_connection_info ADD CONSTRAINT FK_75A882CD1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE appointment_connection_info ADD CONSTRAINT FK_75A882CD32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE appointment_connection_info');
    }
}
