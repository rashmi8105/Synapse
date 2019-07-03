<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141224093848 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE related_activities (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, activity_log_id INT DEFAULT NULL, contacts_id INT DEFAULT NULL, note_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_on DATETIME DEFAULT NULL, INDEX IDX_3F3CA755DE12AB56 (created_by), INDEX IDX_3F3CA75525F94802 (modified_by), INDEX IDX_3F3CA7551F6FA0AF (deleted_by), INDEX IDX_3F3CA75532C8A3DE (organization_id), INDEX IDX_3F3CA755B811BD86 (activity_log_id), INDEX IDX_3F3CA755719FB48E (contacts_id), INDEX IDX_3F3CA75526ED0855 (note_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE related_activities ADD CONSTRAINT FK_3F3CA755DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE related_activities ADD CONSTRAINT FK_3F3CA75525F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE related_activities ADD CONSTRAINT FK_3F3CA7551F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE related_activities ADD CONSTRAINT FK_3F3CA75532C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE related_activities ADD CONSTRAINT FK_3F3CA755B811BD86 FOREIGN KEY (activity_log_id) REFERENCES activity_log (id)');
        $this->addSql('ALTER TABLE related_activities ADD CONSTRAINT FK_3F3CA755719FB48E FOREIGN KEY (contacts_id) REFERENCES contacts (id)');
        $this->addSql('ALTER TABLE related_activities ADD CONSTRAINT FK_3F3CA75526ED0855 FOREIGN KEY (note_id) REFERENCES note (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE related_activities');
     }
}
