<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150824103647 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE reports (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, name VARCHAR(25) NOT NULL, description VARCHAR(256) NOT NULL, is_batch_job VARCHAR(3) NOT NULL, is_coordinator_report TINYINT(1) DEFAULT NULL, INDEX IDX_F11FA745DE12AB56 (created_by), INDEX IDX_F11FA74525F94802 (modified_by), INDEX IDX_F11FA7451F6FA0AF (deleted_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_sections (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, report_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, title VARCHAR(100) DEFAULT NULL, sequence SMALLINT DEFAULT NULL, INDEX IDX_2BF6DAE5DE12AB56 (created_by), INDEX IDX_2BF6DAE525F94802 (modified_by), INDEX IDX_2BF6DAE51F6FA0AF (deleted_by), INDEX fk_sections_reports1_idx (report_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA74525F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA7451F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_sections ADD CONSTRAINT FK_2BF6DAE5DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_sections ADD CONSTRAINT FK_2BF6DAE525F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_sections ADD CONSTRAINT FK_2BF6DAE51F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_sections ADD CONSTRAINT FK_2BF6DAE54BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
       
        $this->addSql('DROP TABLE reports');
        $this->addSql('DROP TABLE report_sections');
    
    }
}
