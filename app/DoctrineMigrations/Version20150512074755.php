<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150512074755 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_announcements (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT NOT NULL, creator_person_id INT NOT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, display_type ENUM(\'banner\', \'alert bell\'), start_datetime DATETIME NOT NULL, stop_datetime DATETIME NOT NULL, INDEX IDX_8CD85A91DE12AB56 (created_by), INDEX IDX_8CD85A9125F94802 (modified_by), INDEX IDX_8CD85A911F6FA0AF (deleted_by), INDEX fk_org_announcements_organization1_idx (org_id), INDEX fk_org_announcements_person1_idx (creator_person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE org_announcements_lang (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_announcements_id INT NOT NULL, lang_id INT NOT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, message VARCHAR(300) DEFAULT NULL, INDEX IDX_3056A6D0DE12AB56 (created_by), INDEX IDX_3056A6D025F94802 (modified_by), INDEX IDX_3056A6D01F6FA0AF (deleted_by), INDEX fk_org_announcements_lang_org_announcements1_idx (org_announcements_id), INDEX fk_org_announcements_lang_language_master1_idx (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_announcements ADD CONSTRAINT FK_8CD85A91DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_announcements ADD CONSTRAINT FK_8CD85A9125F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_announcements ADD CONSTRAINT FK_8CD85A911F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_announcements ADD CONSTRAINT FK_8CD85A91F4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_announcements ADD CONSTRAINT FK_8CD85A91D895820F FOREIGN KEY (creator_person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_announcements_lang ADD CONSTRAINT FK_3056A6D0DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_announcements_lang ADD CONSTRAINT FK_3056A6D025F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_announcements_lang ADD CONSTRAINT FK_3056A6D01F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_announcements_lang ADD CONSTRAINT FK_3056A6D052CCF843 FOREIGN KEY (org_announcements_id) REFERENCES org_announcements (id)');
        $this->addSql('ALTER TABLE org_announcements_lang ADD CONSTRAINT FK_3056A6D0B213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');

        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_announcements_lang DROP FOREIGN KEY FK_3056A6D052CCF843');
        $this->addSql('DROP TABLE org_announcements');
        $this->addSql('DROP TABLE org_announcements_lang');
    }
}
