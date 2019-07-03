<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141024072511 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE datablock_master (id INT AUTO_INCREMENT NOT NULL, datablock_ui_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, block_type VARCHAR(10) DEFAULT NULL, INDEX IDX_C5DA18E94351304E (datablock_ui_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE datablock_master_lang (id INT AUTO_INCREMENT NOT NULL, datablock_id INT DEFAULT NULL, lang_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, datablock_desc VARCHAR(100) DEFAULT NULL, INDEX IDX_EAD6BD4AF9AE3580 (datablock_id), INDEX IDX_EAD6BD4AB213FA4 (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE datablock_ui (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, `key` VARCHAR(45) DEFAULT NULL, ui_feature_name VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE org_permissionset_datablock (id INT AUTO_INCREMENT NOT NULL, org_permissionset_id INT DEFAULT NULL, datablock_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, block_type VARCHAR(10) DEFAULT NULL, timeframe_all TINYINT(1) DEFAULT NULL, current_calendar TINYINT(1) DEFAULT NULL, previous_period TINYINT(1) DEFAULT NULL, next_period TINYINT(1) DEFAULT NULL, INDEX IDX_E34ECC407ABB76BC (org_permissionset_id), INDEX IDX_E34ECC40F9AE3580 (datablock_id), INDEX IDX_E34ECC4032C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE datablock_master ADD CONSTRAINT FK_C5DA18E94351304E FOREIGN KEY (datablock_ui_id) REFERENCES datablock_ui (id)');
        $this->addSql('ALTER TABLE datablock_master_lang ADD CONSTRAINT FK_EAD6BD4AF9AE3580 FOREIGN KEY (datablock_id) REFERENCES datablock_master (id)');
        $this->addSql('ALTER TABLE datablock_master_lang ADD CONSTRAINT FK_EAD6BD4AB213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE org_permissionset_datablock ADD CONSTRAINT FK_E34ECC407ABB76BC FOREIGN KEY (org_permissionset_id) REFERENCES org_permissionset (id)');
        $this->addSql('ALTER TABLE org_permissionset_datablock ADD CONSTRAINT FK_E34ECC40F9AE3580 FOREIGN KEY (datablock_id) REFERENCES datablock_ui (id)');
        $this->addSql('ALTER TABLE org_permissionset_datablock ADD CONSTRAINT FK_E34ECC4032C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE datablock_master_lang DROP FOREIGN KEY FK_EAD6BD4AF9AE3580');
        $this->addSql('ALTER TABLE datablock_master DROP FOREIGN KEY FK_C5DA18E94351304E');
        $this->addSql('ALTER TABLE org_permissionset_datablock DROP FOREIGN KEY FK_E34ECC40F9AE3580');
        $this->addSql('DROP TABLE datablock_master');
        $this->addSql('DROP TABLE datablock_master_lang');
        $this->addSql('DROP TABLE datablock_ui');
        $this->addSql('DROP TABLE org_permissionset_datablock');
       
    }
}
