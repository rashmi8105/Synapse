<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150331101820 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE survey_pages (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, survey_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, sequence INT DEFAULT NULL, set_completed TINYINT(1) DEFAULT NULL, must_branch TINYINT(1) DEFAULT NULL, external_id VARCHAR(45) DEFAULT NULL, INDEX IDX_2C04174DDE12AB56 (created_by), INDEX IDX_2C04174D25F94802 (modified_by), INDEX IDX_2C04174D1F6FA0AF (deleted_by), INDEX fk_survey_pages_survey1_idx (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_pages_lang (survey_pages_id INT NOT NULL, lang_id INT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, description VARCHAR(200) DEFAULT NULL, INDEX IDX_3FB9C4D5DE12AB56 (created_by), INDEX IDX_3FB9C4D525F94802 (modified_by), INDEX IDX_3FB9C4D51F6FA0AF (deleted_by), INDEX fk_survey_pages_lang_survey_pages1_idx (survey_pages_id), INDEX fk_survey_pages_lang_language_master1_idx (lang_id), PRIMARY KEY(survey_pages_id, lang_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_questions (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, survey_id INT DEFAULT NULL, ebi_question_id INT DEFAULT NULL, ind_question_id INT DEFAULT NULL, survey_sections_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, sequence INT DEFAULT NULL, qnbr VARCHAR(3) DEFAULT NULL, INDEX IDX_2F8A16F8DE12AB56 (created_by), INDEX IDX_2F8A16F825F94802 (modified_by), INDEX IDX_2F8A16F81F6FA0AF (deleted_by), INDEX fk_survey_questions_survey1_idx (survey_id), INDEX fk_survey_questions_ebi_question1_idx (ebi_question_id), INDEX fk_survey_questions_independent_question1_idx (ind_question_id), INDEX fk_survey_questions_survey_sections1_idx (survey_sections_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_sections (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, survey_id INT DEFAULT NULL, survey_pages_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, sequence INT DEFAULT NULL, external_id VARCHAR(45) DEFAULT NULL, INDEX IDX_776105BFDE12AB56 (created_by), INDEX IDX_776105BF25F94802 (modified_by), INDEX IDX_776105BF1F6FA0AF (deleted_by), INDEX fk_survey_sections_survey1_idx (survey_id), INDEX fk_survey_sections_survey_pages1_idx (survey_pages_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_sections_lang (survey_sections_id INT NOT NULL, lang_id INT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, description_hdr VARCHAR(2000) DEFAULT NULL, description_dtl VARCHAR(2000) DEFAULT NULL, INDEX IDX_E00ED5A8DE12AB56 (created_by), INDEX IDX_E00ED5A825F94802 (modified_by), INDEX IDX_E00ED5A81F6FA0AF (deleted_by), INDEX fk_survey_sections_lang_survey_sections1_idx (survey_sections_id), INDEX fk_survey_sections_lang_language_master1_idx (lang_id), PRIMARY KEY(survey_sections_id, lang_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE survey_pages ADD CONSTRAINT FK_2C04174DDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_pages ADD CONSTRAINT FK_2C04174D25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_pages ADD CONSTRAINT FK_2C04174D1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_pages ADD CONSTRAINT FK_2C04174DB3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE survey_pages_lang ADD CONSTRAINT FK_3FB9C4D5DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_pages_lang ADD CONSTRAINT FK_3FB9C4D525F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_pages_lang ADD CONSTRAINT FK_3FB9C4D51F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_pages_lang ADD CONSTRAINT FK_3FB9C4D51CED9B00 FOREIGN KEY (survey_pages_id) REFERENCES survey_pages (id)');
        $this->addSql('ALTER TABLE survey_pages_lang ADD CONSTRAINT FK_3FB9C4D5B213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE survey_questions ADD CONSTRAINT FK_2F8A16F8DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_questions ADD CONSTRAINT FK_2F8A16F825F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_questions ADD CONSTRAINT FK_2F8A16F81F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_questions ADD CONSTRAINT FK_2F8A16F8B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE survey_questions ADD CONSTRAINT FK_2F8A16F879F0E193 FOREIGN KEY (ebi_question_id) REFERENCES ebi_question (id)');
        $this->addSql('ALTER TABLE survey_questions ADD CONSTRAINT FK_2F8A16F851DCB924 FOREIGN KEY (ind_question_id) REFERENCES ind_question (id)');
        $this->addSql('ALTER TABLE survey_questions ADD CONSTRAINT FK_2F8A16F8EF81D9E1 FOREIGN KEY (survey_sections_id) REFERENCES survey_sections (id)');
        $this->addSql('ALTER TABLE survey_sections ADD CONSTRAINT FK_776105BFDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_sections ADD CONSTRAINT FK_776105BF25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_sections ADD CONSTRAINT FK_776105BF1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_sections ADD CONSTRAINT FK_776105BFB3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE survey_sections ADD CONSTRAINT FK_776105BF1CED9B00 FOREIGN KEY (survey_pages_id) REFERENCES survey_pages (id)');
        $this->addSql('ALTER TABLE survey_sections_lang ADD CONSTRAINT FK_E00ED5A8DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_sections_lang ADD CONSTRAINT FK_E00ED5A825F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_sections_lang ADD CONSTRAINT FK_E00ED5A81F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_sections_lang ADD CONSTRAINT FK_E00ED5A8EF81D9E1 FOREIGN KEY (survey_sections_id) REFERENCES survey_sections (id)');
        $this->addSql('ALTER TABLE survey_sections_lang ADD CONSTRAINT FK_E00ED5A8B213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE survey_pages_lang DROP FOREIGN KEY FK_3FB9C4D51CED9B00');
        $this->addSql('ALTER TABLE survey_sections DROP FOREIGN KEY FK_776105BF1CED9B00');
        $this->addSql('ALTER TABLE survey_questions DROP FOREIGN KEY FK_2F8A16F8EF81D9E1');
        $this->addSql('ALTER TABLE survey_sections_lang DROP FOREIGN KEY FK_E00ED5A8EF81D9E1');
        $this->addSql('DROP TABLE survey_pages');
        $this->addSql('DROP TABLE survey_pages_lang');
        $this->addSql('DROP TABLE survey_questions');
        $this->addSql('DROP TABLE survey_sections');
        $this->addSql('DROP TABLE survey_sections_lang');
    }
}
