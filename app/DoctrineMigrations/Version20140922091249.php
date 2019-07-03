<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140922091249 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE organization_lang (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, lang_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, organization_name VARCHAR(255) DEFAULT NULL, nick_name VARCHAR(45) DEFAULT NULL, INDEX IDX_800BAFF32C8A3DE (organization_id), INDEX IDX_800BAFFB213FA4 (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE organization_lang ADD CONSTRAINT FK_800BAFF32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE organization_lang ADD CONSTRAINT FK_800BAFFB213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('DROP TABLE organizationlang');
        $this->addSql('ALTER TABLE org_group_faculty CHANGE is_invisible is_invisible TINYINT(1) DEFAULT NULL');
       
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE organizationlang (orglangid INT AUTO_INCREMENT NOT NULL, organizationid INT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, langid INT NOT NULL, organizationname VARCHAR(255) NOT NULL, nickname VARCHAR(45) NOT NULL, INDEX IDX_76EFC27BE808A0A6 (organizationid), PRIMARY KEY(orglangid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE organizationlang ADD CONSTRAINT FK_76EFC27BE808A0A6 FOREIGN KEY (organizationid) REFERENCES organization (id)');
        $this->addSql('DROP TABLE organization_lang');
        $this->addSql('ALTER TABLE org_group_faculty CHANGE is_invisible is_invisible LONGBLOB DEFAULT NULL');
       
    }
}
