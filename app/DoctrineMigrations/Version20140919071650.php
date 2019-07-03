<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140919071650 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE email_template_lang (id INT AUTO_INCREMENT NOT NULL, email_template_id INT DEFAULT NULL, language_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, body LONGTEXT DEFAULT NULL, subject VARCHAR(500) DEFAULT NULL, INDEX IDX_16F0BDA7131A730F (email_template_id), INDEX IDX_16F0BDA782F1BAF4 (language_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_template (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, email_key VARCHAR(45) DEFAULT NULL, is_active TINYBLOB DEFAULT NULL, from_email_address VARCHAR(255) DEFAULT NULL, bcc_recipient_list VARCHAR(500) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE email_template_lang ADD CONSTRAINT FK_16F0BDA7131A730F FOREIGN KEY (email_template_id) REFERENCES email_template (id)');
        $this->addSql('ALTER TABLE email_template_lang ADD CONSTRAINT FK_16F0BDA782F1BAF4 FOREIGN KEY (language_id) REFERENCES language_master (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE email_template_lang DROP FOREIGN KEY FK_16F0BDA7131A730F');
        $this->addSql('DROP TABLE email_template_lang');
        $this->addSql('DROP TABLE email_template');
       
    }
}
