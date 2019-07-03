<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141117054130 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE Appointments (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, person_id INT DEFAULT NULL, activity_category_id INT DEFAULT NULL, person_id_proxy INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, type VARCHAR(1) DEFAULT NULL, location VARCHAR(45) DEFAULT NULL, title VARCHAR(1000) DEFAULT NULL, description VARCHAR(5000) DEFAULT NULL, start_date_time DATETIME DEFAULT NULL, end_date_time DATETIME DEFAULT NULL, attendees LONGTEXT DEFAULT NULL, occurrence_id VARCHAR(255) DEFAULT NULL, master_occurrence_id VARCHAR(255) DEFAULT NULL, match_status TINYINT(1) DEFAULT NULL, last_synced DATETIME DEFAULT NULL, is_free_standing TINYINT(1) DEFAULT NULL, INDEX IDX_7270A98232C8A3DE (organization_id), INDEX IDX_7270A982217BBB47 (person_id), INDEX IDX_7270A9821CC8F7EE (activity_category_id), INDEX IDX_7270A9829B12DB9 (person_id_proxy), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
		$this->addSql('CREATE TABLE appointment_recepient_and_status (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, appointments_id INT DEFAULT NULL, person_id_faculty INT DEFAULT NULL, person_id_student INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, has_attended TINYINT(1) DEFAULT NULL, INDEX IDX_CF90B68332C8A3DE (organization_id), INDEX IDX_CF90B68323F542AE (appointments_id), INDEX IDX_CF90B683FFB0AA26 (person_id_faculty), INDEX IDX_CF90B6835F056556 (person_id_student), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
   
        $this->addSql('ALTER TABLE Appointments ADD CONSTRAINT FK_7270A98232C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE Appointments ADD CONSTRAINT FK_7270A982217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE Appointments ADD CONSTRAINT FK_7270A9821CC8F7EE FOREIGN KEY (activity_category_id) REFERENCES activity_category (id)');
        $this->addSql('ALTER TABLE Appointments ADD CONSTRAINT FK_7270A9829B12DB9 FOREIGN KEY (person_id_proxy) REFERENCES person (id)');
		
        $this->addSql('ALTER TABLE appointment_recepient_and_status ADD CONSTRAINT FK_CF90B68332C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE appointment_recepient_and_status ADD CONSTRAINT FK_CF90B68323F542AE FOREIGN KEY (appointments_id) REFERENCES Appointments (id)');
        $this->addSql('ALTER TABLE appointment_recepient_and_status ADD CONSTRAINT FK_CF90B683FFB0AA26 FOREIGN KEY (person_id_faculty) REFERENCES person (id)');
        $this->addSql('ALTER TABLE appointment_recepient_and_status ADD CONSTRAINT FK_CF90B6835F056556 FOREIGN KEY (person_id_student) REFERENCES person (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE Appointments');
		$this->addSql('ALTER TABLE appointment_recepient_and_status DROP FOREIGN KEY FK_CF90B68323F542AE');
		$this->addSql('DROP TABLE appointment_recepient_and_status');
        
              
    }
}
