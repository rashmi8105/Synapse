<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141029101208 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE activity_category (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, short_name VARCHAR(45) DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, display_seq INT DEFAULT NULL, parent_activity_category_id INT DEFAULT 0 NOT NULL, INDEX fk_activity_category_activity_category1_idx (parent_activity_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity_category_lang (id INT AUTO_INCREMENT NOT NULL, activity_category_id INT DEFAULT NULL, language_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, description VARCHAR(100) DEFAULT NULL, INDEX IDX_BB4037E11CC8F7EE (activity_category_id), INDEX IDX_BB4037E182F1BAF4 (language_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, person_id_student INT DEFAULT NULL, person_id_faculty INT DEFAULT NULL, activity_category_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, note LONGTEXT NOT NULL, note_date DATETIME NOT NULL, access_private TINYINT(1) DEFAULT NULL, access_public TINYINT(1) DEFAULT NULL, access_team TINYINT(1) DEFAULT NULL, INDEX IDX_CFBDFA1432C8A3DE (organization_id), INDEX IDX_CFBDFA145F056556 (person_id_student), INDEX IDX_CFBDFA14FFB0AA26 (person_id_faculty), INDEX IDX_CFBDFA141CC8F7EE (activity_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE note_teams (id INT AUTO_INCREMENT NOT NULL, note_id INT DEFAULT NULL, teams_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_F3D2818F26ED0855 (note_id), INDEX IDX_F3D2818FD6365F12 (teams_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity_category_lang ADD CONSTRAINT FK_BB4037E11CC8F7EE FOREIGN KEY (activity_category_id) REFERENCES activity_category (id)');
        $this->addSql('ALTER TABLE activity_category_lang ADD CONSTRAINT FK_BB4037E182F1BAF4 FOREIGN KEY (language_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA1432C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA145F056556 FOREIGN KEY (person_id_student) REFERENCES person (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14FFB0AA26 FOREIGN KEY (person_id_faculty) REFERENCES person (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA141CC8F7EE FOREIGN KEY (activity_category_id) REFERENCES activity_category (id)');
        $this->addSql('ALTER TABLE note_teams ADD CONSTRAINT FK_F3D2818F26ED0855 FOREIGN KEY (note_id) REFERENCES note (id)');
        $this->addSql('ALTER TABLE note_teams ADD CONSTRAINT FK_F3D2818FD6365F12 FOREIGN KEY (teams_id) REFERENCES Teams (id)');        
      
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE activity_category_lang DROP FOREIGN KEY FK_BB4037E11CC8F7EE');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA141CC8F7EE');
        $this->addSql('ALTER TABLE note_teams DROP FOREIGN KEY FK_F3D2818F26ED0855');
        $this->addSql('DROP TABLE activity_category');
        $this->addSql('DROP TABLE activity_category_lang');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE note_teams');
      
    }
}
