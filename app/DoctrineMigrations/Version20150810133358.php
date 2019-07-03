<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150810133358 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE appointments_teams (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, appointments_id INT DEFAULT NULL, teams_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_53B92262DE12AB56 (created_by), INDEX IDX_53B9226225F94802 (modified_by), INDEX IDX_53B922621F6FA0AF (deleted_by), INDEX fk_appointments_teams_appointments1_idx (appointments_id), INDEX fk_appointments_teams_teams1_idx (teams_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appointments_teams ADD CONSTRAINT FK_53B92262DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE appointments_teams ADD CONSTRAINT FK_53B9226225F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE appointments_teams ADD CONSTRAINT FK_53B922621F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE appointments_teams ADD CONSTRAINT FK_53B9226223F542AE FOREIGN KEY (appointments_id) REFERENCES Appointments (id)');
        $this->addSql('ALTER TABLE appointments_teams ADD CONSTRAINT FK_53B92262D6365F12 FOREIGN KEY (teams_id) REFERENCES Teams (id)');
        $this->addSql('ALTER TABLE Appointments ADD access_private TINYINT(1) DEFAULT NULL, ADD access_public TINYINT(1) DEFAULT NULL, ADD access_team TINYINT(1) DEFAULT NULL, CHANGE source source enum(\'S\', \'G\', \'E\')');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE appointments_teams');       
        $this->addSql('ALTER TABLE Appointments DROP access_private, DROP access_public, DROP access_team, CHANGE source source VARCHAR(255) DEFAULT \'S\' COLLATE utf8_unicode_ci');
       
    }
}
