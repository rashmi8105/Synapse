<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140924095105 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE access_log (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, person_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, event VARCHAR(45) DEFAULT NULL, event_id INT DEFAULT NULL, date_time DATETIME DEFAULT NULL, source_ip VARCHAR(20) DEFAULT NULL, browser VARCHAR(255) DEFAULT NULL, user_token VARCHAR(255) DEFAULT NULL, api_token VARCHAR(255) DEFAULT NULL, INDEX IDX_EF7F351032C8A3DE (organization_id), INDEX IDX_EF7F3510217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE access_log ADD CONSTRAINT FK_EF7F351032C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE access_log ADD CONSTRAINT FK_EF7F3510217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
      
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE access_log');
        
    }
}
