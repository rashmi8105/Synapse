<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141027121006 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE org_metadata (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, meta_key VARCHAR(45) DEFAULT NULL, meta_name VARCHAR(255) DEFAULT NULL, meta_description LONGTEXT DEFAULT NULL, definition_type VARCHAR(1) DEFAULT NULL, metadata_type VARCHAR(1) DEFAULT NULL, no_of_decimals INT DEFAULT NULL, is_required TINYBLOB DEFAULT NULL, min_range NUMERIC(15, 4) DEFAULT NULL, max_range NUMERIC(15, 4) DEFAULT NULL, entity VARCHAR(10) DEFAULT NULL, sequence INT DEFAULT NULL, meta_group VARCHAR(2) DEFAULT NULL, INDEX IDX_33BBA4F732C8A3DE (organization_id), UNIQUE INDEX unique_index_org_key (organization_id, meta_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_metadata ADD CONSTRAINT FK_33BBA4F732C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
       
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE org_metadata');
       ;
    }
}
