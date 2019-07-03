<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141223114036 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE activity_log (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, person_id_faculty INT DEFAULT NULL, person_id_student INT DEFAULT NULL, referrals_id INT DEFAULT NULL, appointments_id INT DEFAULT NULL, note_id INT DEFAULT NULL, contacts_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, activity_type VARCHAR(1) DEFAULT NULL, activity_date DATETIME DEFAULT NULL, reason VARCHAR(100) DEFAULT NULL, INDEX IDX_FD06F647DE12AB56 (created_by), INDEX IDX_FD06F64725F94802 (modified_by), INDEX IDX_FD06F6471F6FA0AF (deleted_by), INDEX IDX_FD06F64732C8A3DE (organization_id), INDEX IDX_FD06F647FFB0AA26 (person_id_faculty), INDEX IDX_FD06F6475F056556 (person_id_student), INDEX IDX_FD06F647B24851AE (referrals_id), INDEX IDX_FD06F64723F542AE (appointments_id), INDEX IDX_FD06F64726ED0855 (note_id), INDEX IDX_FD06F647719FB48E (contacts_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity_log ADD CONSTRAINT FK_FD06F647DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE activity_log ADD CONSTRAINT FK_FD06F64725F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE activity_log ADD CONSTRAINT FK_FD06F6471F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE activity_log ADD CONSTRAINT FK_FD06F64732C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE activity_log ADD CONSTRAINT FK_FD06F647FFB0AA26 FOREIGN KEY (person_id_faculty) REFERENCES person (id)');
        $this->addSql('ALTER TABLE activity_log ADD CONSTRAINT FK_FD06F6475F056556 FOREIGN KEY (person_id_student) REFERENCES person (id)');
        $this->addSql('ALTER TABLE activity_log ADD CONSTRAINT FK_FD06F647B24851AE FOREIGN KEY (referrals_id) REFERENCES referrals (id)');
        $this->addSql('ALTER TABLE activity_log ADD CONSTRAINT FK_FD06F64723F542AE FOREIGN KEY (appointments_id) REFERENCES Appointments (id)');
        $this->addSql('ALTER TABLE activity_log ADD CONSTRAINT FK_FD06F64726ED0855 FOREIGN KEY (note_id) REFERENCES note (id)');
        $this->addSql('ALTER TABLE activity_log ADD CONSTRAINT FK_FD06F647719FB48E FOREIGN KEY (contacts_id) REFERENCES contacts (id)');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE activity_log');
 
    }
}
