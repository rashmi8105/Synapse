<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140902041125 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE activity_reference (id INT AUTO_INCREMENT NOT NULL, short_name VARCHAR(45) DEFAULT NULL, is_active TINYBLOB DEFAULT NULL, display_seq INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE activity_reference_lang (id INT AUTO_INCREMENT NOT NULL, language_master_id INT DEFAULT NULL, activity_reference_id INT DEFAULT NULL, heading VARCHAR(100) DEFAULT NULL, description VARCHAR(100) DEFAULT NULL, INDEX IDX_DAC2A8A2D5D3A0FB (language_master_id), INDEX IDX_DAC2A8A21FFD1CE8 (activity_reference_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE activity_reference_unassigned (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, activity_reference_id INT DEFAULT NULL, INDEX IDX_18D1E598217BBB47 (person_id), INDEX IDX_18D1E59832C8A3DE (organization_id), INDEX IDX_18D1E5981FFD1CE8 (activity_reference_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE activity_reference_lang ADD CONSTRAINT FK_DAC2A8A2D5D3A0FB FOREIGN KEY (language_master_id) REFERENCES language_master (id)");
        $this->addSql("ALTER TABLE activity_reference_lang ADD CONSTRAINT FK_DAC2A8A21FFD1CE8 FOREIGN KEY (activity_reference_id) REFERENCES activity_reference (id)");
        $this->addSql("ALTER TABLE activity_reference_unassigned ADD CONSTRAINT FK_18D1E598217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)");
        $this->addSql("ALTER TABLE activity_reference_unassigned ADD CONSTRAINT FK_18D1E59832C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)");
        $this->addSql("ALTER TABLE activity_reference_unassigned ADD CONSTRAINT FK_18D1E5981FFD1CE8 FOREIGN KEY (activity_reference_id) REFERENCES activity_reference (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE activity_reference_lang DROP FOREIGN KEY FK_DAC2A8A21FFD1CE8");
        $this->addSql("ALTER TABLE activity_reference_unassigned DROP FOREIGN KEY FK_18D1E5981FFD1CE8");
        $this->addSql("DROP TABLE activity_reference");
        $this->addSql("DROP TABLE activity_reference_lang");
        $this->addSql("DROP TABLE activity_reference_unassigned");
    }
}
