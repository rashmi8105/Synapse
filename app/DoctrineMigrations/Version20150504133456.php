<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150504133456 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_campus_resource (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT NOT NULL, person_id INT NOT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, name VARCHAR(100) NOT NULL, phone VARCHAR(45) NOT NULL, email VARCHAR(120) NOT NULL, location VARCHAR(45) DEFAULT NULL, url VARCHAR(500) DEFAULT NULL, description VARCHAR(300) DEFAULT NULL, visible_to_student enum(\'1\', \'0\'), receive_referals enum(\'1\', \'0\'), INDEX IDX_E234A1AEDE12AB56 (created_by), INDEX IDX_E234A1AE25F94802 (modified_by), INDEX IDX_E234A1AE1F6FA0AF (deleted_by), INDEX fk_campus_resource_organization1_idx (org_id), INDEX fk_campus_resource_person1_idx (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_campus_resource ADD CONSTRAINT FK_E234A1AEDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_campus_resource ADD CONSTRAINT FK_E234A1AE25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_campus_resource ADD CONSTRAINT FK_E234A1AE1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_campus_resource ADD CONSTRAINT FK_E234A1AEF4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_campus_resource ADD CONSTRAINT FK_E234A1AE217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE org_campus_resource');
    }
}
