<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150101105233 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE ebi_metadata (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, `key` VARCHAR(45) DEFAULT NULL, definition_type VARCHAR(1) DEFAULT NULL, metadata_type VARCHAR(1) DEFAULT NULL, no_of_decimals INT NOT NULL, is_required TINYINT(1) DEFAULT NULL, min_range NUMERIC(15, 4) DEFAULT NULL, max_range NUMERIC(15, 4) DEFAULT NULL, entity VARCHAR(10) DEFAULT NULL, sequence INT DEFAULT NULL, meta_group VARCHAR(2) DEFAULT NULL, INDEX IDX_69B3B8EEDE12AB56 (created_by), INDEX IDX_69B3B8EE25F94802 (modified_by), INDEX IDX_69B3B8EE1F6FA0AF (deleted_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE OrgTalkingPoints (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, person_id INT DEFAULT NULL, talking_points_id INT DEFAULT NULL, survey_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, response VARCHAR(45) DEFAULT NULL, INDEX IDX_E88CCF18DE12AB56 (created_by), INDEX IDX_E88CCF1825F94802 (modified_by), INDEX IDX_E88CCF181F6FA0AF (deleted_by), INDEX IDX_E88CCF1832C8A3DE (organization_id), INDEX IDX_E88CCF18217BBB47 (person_id), INDEX IDX_E88CCF18CDC12E8B (talking_points_id), INDEX IDX_E88CCF18B3FE509D (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE talking_points (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, ebi_question_id INT DEFAULT NULL, ebi_metadata_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, type VARCHAR(1) DEFAULT NULL, talking_points_type VARCHAR(1) DEFAULT NULL, min_range NUMERIC(15, 4) DEFAULT NULL, max_range NUMERIC(15, 4) DEFAULT NULL, INDEX IDX_8F281C4DDE12AB56 (created_by), INDEX IDX_8F281C4D25F94802 (modified_by), INDEX IDX_8F281C4D1F6FA0AF (deleted_by), INDEX IDX_8F281C4D79F0E193 (ebi_question_id), INDEX IDX_8F281C4DBB49FE75 (ebi_metadata_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE talking_points_lang (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, talking_points_id INT DEFAULT NULL, language_master_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_5CC5137ADE12AB56 (created_by), INDEX IDX_5CC5137A25F94802 (modified_by), INDEX IDX_5CC5137A1F6FA0AF (deleted_by), INDEX IDX_5CC5137ACDC12E8B (talking_points_id), INDEX IDX_5CC5137AD5D3A0FB (language_master_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ebi_metadata ADD CONSTRAINT FK_69B3B8EEDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_metadata ADD CONSTRAINT FK_69B3B8EE25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_metadata ADD CONSTRAINT FK_69B3B8EE1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE OrgTalkingPoints ADD CONSTRAINT FK_E88CCF18DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE OrgTalkingPoints ADD CONSTRAINT FK_E88CCF1825F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE OrgTalkingPoints ADD CONSTRAINT FK_E88CCF181F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE OrgTalkingPoints ADD CONSTRAINT FK_E88CCF1832C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE OrgTalkingPoints ADD CONSTRAINT FK_E88CCF18217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE OrgTalkingPoints ADD CONSTRAINT FK_E88CCF18CDC12E8B FOREIGN KEY (talking_points_id) REFERENCES talking_points (id)');
        $this->addSql('ALTER TABLE OrgTalkingPoints ADD CONSTRAINT FK_E88CCF18B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE talking_points ADD CONSTRAINT FK_8F281C4DDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE talking_points ADD CONSTRAINT FK_8F281C4D25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE talking_points ADD CONSTRAINT FK_8F281C4D1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE talking_points ADD CONSTRAINT FK_8F281C4D79F0E193 FOREIGN KEY (ebi_question_id) REFERENCES ebi_question (id)');
        $this->addSql('ALTER TABLE talking_points ADD CONSTRAINT FK_8F281C4DBB49FE75 FOREIGN KEY (ebi_metadata_id) REFERENCES ebi_metadata (id)');
        $this->addSql('ALTER TABLE talking_points_lang ADD CONSTRAINT FK_5CC5137ADE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE talking_points_lang ADD CONSTRAINT FK_5CC5137A25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE talking_points_lang ADD CONSTRAINT FK_5CC5137A1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE talking_points_lang ADD CONSTRAINT FK_5CC5137ACDC12E8B FOREIGN KEY (talking_points_id) REFERENCES talking_points (id)');
        $this->addSql('ALTER TABLE talking_points_lang ADD CONSTRAINT FK_5CC5137AD5D3A0FB FOREIGN KEY (language_master_id) REFERENCES language_master (id)');
       
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE talking_points DROP FOREIGN KEY FK_8F281C4DBB49FE75');
        $this->addSql('ALTER TABLE OrgTalkingPoints DROP FOREIGN KEY FK_E88CCF18B3FE509D');
        $this->addSql('ALTER TABLE OrgTalkingPoints DROP FOREIGN KEY FK_E88CCF18CDC12E8B');
        $this->addSql('ALTER TABLE talking_points_lang DROP FOREIGN KEY FK_5CC5137ACDC12E8B');
        $this->addSql('DROP TABLE ebi_metadata');
        $this->addSql('DROP TABLE OrgTalkingPoints');
        $this->addSql('DROP TABLE survey');
        $this->addSql('DROP TABLE talking_points');
        $this->addSql('DROP TABLE talking_points_lang');
        
    }
}
