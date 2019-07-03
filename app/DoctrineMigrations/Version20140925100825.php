<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140925100825 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE IF EXISTS rolelang');
        $this->addSql('DROP TABLE IF EXISTS role');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, status VARCHAR(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_lang (id INT AUTO_INCREMENT NOT NULL, role_id INT DEFAULT NULL, lang_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, role_name VARCHAR(45) DEFAULT NULL, INDEX IDX_8FB6F6F6D60322AC (role_id), INDEX IDX_8FB6F6F6B213FA4 (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE role_lang ADD CONSTRAINT FK_8FB6F6F6D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE role_lang ADD CONSTRAINT FK_8FB6F6F6B213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
       ;
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE IF NOT EXISTS role (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, status VARCHAR(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS rolelang (id INT AUTO_INCREMENT NOT NULL, role_id INT DEFAULT NULL, lang_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, role_name VARCHAR(45) DEFAULT NULL, INDEX IDX_8FB6F6F6D60322AC (role_id), INDEX IDX_8FB6F6F6B213FA4 (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE role_lang DROP FOREIGN KEY FK_8FB6F6F6D60322AC');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE role_lang');
       
    }
}
