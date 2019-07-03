<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140930140236 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE notification_log (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, sent_date DATE DEFAULT NULL, email_key VARCHAR(45) DEFAULT NULL, recipient_list VARCHAR(500) DEFAULT NULL, cc_list VARCHAR(500) DEFAULT NULL, bcc_list VARCHAR(500) DEFAULT NULL, subject VARCHAR(1000) DEFAULT NULL, body VARCHAR(5000) DEFAULT NULL, status VARCHAR(1) DEFAULT NULL, no_of_retries INT DEFAULT NULL, server_response VARCHAR(500) DEFAULT NULL, INDEX IDX_ED15DF232C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notification_log ADD CONSTRAINT FK_ED15DF232C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
       
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE notification_log');
    }
}
