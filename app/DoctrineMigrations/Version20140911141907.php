<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140911141907 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE team_members (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, teams_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, is_team_leader TINYBLOB DEFAULT NULL, INDEX IDX_BAD9A3C8217BBB47 (person_id), INDEX IDX_BAD9A3C832C8A3DE (organization_id), INDEX IDX_BAD9A3C8D6365F12 (teams_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Teams (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, team_name VARCHAR(100) DEFAULT NULL, team_description VARCHAR(500) DEFAULT NULL, INDEX IDX_57030D5C32C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE team_members ADD CONSTRAINT FK_BAD9A3C8217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE team_members ADD CONSTRAINT FK_BAD9A3C832C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE team_members ADD CONSTRAINT FK_BAD9A3C8D6365F12 FOREIGN KEY (teams_id) REFERENCES Teams (id)');
        $this->addSql('ALTER TABLE Teams ADD CONSTRAINT FK_57030D5C32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
       
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE team_members DROP FOREIGN KEY FK_BAD9A3C8D6365F12');
        $this->addSql('DROP TABLE team_members');
        $this->addSql('DROP TABLE Teams');
       
    }
}
