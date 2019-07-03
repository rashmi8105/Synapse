<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141202065337 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
              
        $this->addSql('CREATE TABLE org_metadata_list_values (id INT AUTO_INCREMENT NOT NULL, org_metadata_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, list_name VARCHAR(255) DEFAULT NULL, list_value VARCHAR(255) DEFAULT NULL, sequence INT DEFAULT NULL, INDEX IDX_EDD17EBE4012B3BF (org_metadata_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_metadata_list_values ADD CONSTRAINT FK_EDD17EBE4012B3BF FOREIGN KEY (org_metadata_id) REFERENCES org_metadata (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE org_metadata_list_values');
        
    }
}
