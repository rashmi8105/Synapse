<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141028112856 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE org_permissionset_metadata (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, org_permissionset_id INT DEFAULT NULL, org_metadata_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_A3267BC432C8A3DE (organization_id), INDEX IDX_A3267BC47ABB76BC (org_permissionset_id), INDEX IDX_A3267BC44012B3BF (org_metadata_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_permissionset_metadata ADD CONSTRAINT FK_A3267BC432C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_permissionset_metadata ADD CONSTRAINT FK_A3267BC47ABB76BC FOREIGN KEY (org_permissionset_id) REFERENCES org_permissionset (id)');
        $this->addSql('ALTER TABLE org_permissionset_metadata ADD CONSTRAINT FK_A3267BC44012B3BF FOREIGN KEY (org_metadata_id) REFERENCES org_metadata (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE org_permissionset_metadata');
       
    }
}
