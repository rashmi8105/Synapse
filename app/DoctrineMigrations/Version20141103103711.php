<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141103103711 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE contacts (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, person_id_student INT DEFAULT NULL, person_id_faculty INT DEFAULT NULL, contact_types_id INT DEFAULT NULL, activity_category_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, contact_date DATETIME NOT NULL, note LONGTEXT NOT NULL, is_discussed TINYINT(1) DEFAULT NULL, is_high_priority TINYINT(1) DEFAULT NULL, is_reveal TINYINT(1) DEFAULT NULL, is_leaving TINYINT(1) DEFAULT NULL, access_private TINYINT(1) DEFAULT NULL, access_public TINYINT(1) DEFAULT NULL, access_team TINYINT(1) DEFAULT NULL, INDEX IDX_3340157332C8A3DE (organization_id), INDEX IDX_334015735F056556 (person_id_student), INDEX IDX_33401573FFB0AA26 (person_id_faculty), INDEX IDX_334015731497AFC0 (contact_types_id), INDEX IDX_334015731CC8F7EE (activity_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contacts_teams (id INT AUTO_INCREMENT NOT NULL, contacts_id INT DEFAULT NULL, teams_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_8475AAAD719FB48E (contacts_id), INDEX IDX_8475AAADD6365F12 (teams_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact_types (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, display_seq INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact_types_lang (id INT AUTO_INCREMENT NOT NULL, contact_types_id INT DEFAULT NULL, language_master_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, heading VARCHAR(100) DEFAULT NULL, description VARCHAR(100) DEFAULT NULL, INDEX IDX_D5F7EF541497AFC0 (contact_types_id), INDEX IDX_D5F7EF54D5D3A0FB (language_master_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contacts ADD CONSTRAINT FK_3340157332C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE contacts ADD CONSTRAINT FK_334015735F056556 FOREIGN KEY (person_id_student) REFERENCES person (id)');
        $this->addSql('ALTER TABLE contacts ADD CONSTRAINT FK_33401573FFB0AA26 FOREIGN KEY (person_id_faculty) REFERENCES person (id)');
        $this->addSql('ALTER TABLE contacts ADD CONSTRAINT FK_334015731497AFC0 FOREIGN KEY (contact_types_id) REFERENCES contact_types (id)');
        $this->addSql('ALTER TABLE contacts ADD CONSTRAINT FK_334015731CC8F7EE FOREIGN KEY (activity_category_id) REFERENCES activity_category (id)');
        $this->addSql('ALTER TABLE contacts_teams ADD CONSTRAINT FK_8475AAAD719FB48E FOREIGN KEY (contacts_id) REFERENCES contacts (id)');
        $this->addSql('ALTER TABLE contacts_teams ADD CONSTRAINT FK_8475AAADD6365F12 FOREIGN KEY (teams_id) REFERENCES Teams (id)');
        $this->addSql('ALTER TABLE contact_types_lang ADD CONSTRAINT FK_D5F7EF541497AFC0 FOREIGN KEY (contact_types_id) REFERENCES contact_types (id)');
        $this->addSql('ALTER TABLE contact_types_lang ADD CONSTRAINT FK_D5F7EF54D5D3A0FB FOREIGN KEY (language_master_id) REFERENCES language_master (id)');        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE contacts_teams DROP FOREIGN KEY FK_8475AAAD719FB48E');
        $this->addSql('ALTER TABLE contacts DROP FOREIGN KEY FK_334015731497AFC0');
        $this->addSql('ALTER TABLE contact_types_lang DROP FOREIGN KEY FK_D5F7EF541497AFC0');
        $this->addSql('DROP TABLE contacts');
        $this->addSql('DROP TABLE contacts_teams');
        $this->addSql('DROP TABLE contact_types');
        $this->addSql('DROP TABLE contact_types_lang');        
    }
}
