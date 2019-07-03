<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141117071030 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE office_hours (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, person_id INT DEFAULT NULL, person_id_proxy_created INT DEFAULT NULL, office_hours_series_id INT DEFAULT NULL, appointments_id INT DEFAULT NULL, person_id_proxy_cancelled INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, slot_type VARCHAR(1) DEFAULT NULL, location VARCHAR(45) DEFAULT NULL, slot_start DATETIME DEFAULT NULL, slot_end DATETIME DEFAULT NULL, meeting_length INT DEFAULT NULL, standing_instructions VARCHAR(255) DEFAULT NULL, is_cancelled TINYINT(1) DEFAULT NULL, INDEX IDX_83411E0D32C8A3DE (organization_id), INDEX IDX_83411E0D217BBB47 (person_id), INDEX IDX_83411E0D7D2061B4 (person_id_proxy_created), INDEX IDX_83411E0DD2D1B0CE (office_hours_series_id), INDEX IDX_83411E0D23F542AE (appointments_id), INDEX IDX_83411E0D3AF00C37 (person_id_proxy_cancelled), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE office_hours_series (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, person_id INT DEFAULT NULL, person_id_proxy INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, days VARCHAR(7) DEFAULT NULL, location VARCHAR(45) DEFAULT NULL, slot_start DATETIME DEFAULT NULL, slot_end DATETIME DEFAULT NULL, meeting_length INT DEFAULT NULL, standing_instructions VARCHAR(255) DEFAULT NULL, repeat_pattern VARCHAR(1) DEFAULT NULL, repeat_every INT DEFAULT NULL, repetition_range VARCHAR(1) DEFAULT NULL, repetition_occurrence INT DEFAULT NULL, INDEX IDX_1578CA932C8A3DE (organization_id), INDEX IDX_1578CA9217BBB47 (person_id), INDEX IDX_1578CA99B12DB9 (person_id_proxy), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE office_hours ADD CONSTRAINT FK_83411E0D32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE office_hours ADD CONSTRAINT FK_83411E0D217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE office_hours ADD CONSTRAINT FK_83411E0D7D2061B4 FOREIGN KEY (person_id_proxy_created) REFERENCES person (id)');
        $this->addSql('ALTER TABLE office_hours ADD CONSTRAINT FK_83411E0DD2D1B0CE FOREIGN KEY (office_hours_series_id) REFERENCES office_hours_series (id)');
        $this->addSql('ALTER TABLE office_hours ADD CONSTRAINT FK_83411E0D23F542AE FOREIGN KEY (appointments_id) REFERENCES Appointments (id)');
        $this->addSql('ALTER TABLE office_hours ADD CONSTRAINT FK_83411E0D3AF00C37 FOREIGN KEY (person_id_proxy_cancelled) REFERENCES person (id)');
        $this->addSql('ALTER TABLE office_hours_series ADD CONSTRAINT FK_1578CA932C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE office_hours_series ADD CONSTRAINT FK_1578CA9217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE office_hours_series ADD CONSTRAINT FK_1578CA99B12DB9 FOREIGN KEY (person_id_proxy) REFERENCES person (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE office_hours DROP FOREIGN KEY FK_83411E0DD2D1B0CE');
        $this->addSql('DROP TABLE office_hours');
        $this->addSql('DROP TABLE office_hours_series');
        
    }
}
