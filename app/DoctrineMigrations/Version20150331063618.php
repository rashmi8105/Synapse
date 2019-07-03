<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150331063618 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE factor (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, survey_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, INDEX IDX_ED38EC00DE12AB56 (created_by), INDEX IDX_ED38EC0025F94802 (modified_by), INDEX IDX_ED38EC001F6FA0AF (deleted_by), INDEX fk_factor_survey1_idx (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE factor_lang (factor_id INT NOT NULL, lang_id INT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, name VARCHAR(200) DEFAULT NULL, INDEX IDX_312D3370DE12AB56 (created_by), INDEX IDX_312D337025F94802 (modified_by), INDEX IDX_312D33701F6FA0AF (deleted_by), INDEX IDX_312D3370B213FA4 (lang_id), INDEX fk_factors_lang_factors1_idx (factor_id), PRIMARY KEY(factor_id, lang_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE factor_questions (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, factor_id INT DEFAULT NULL, ebi_question_id INT DEFAULT NULL, ind_question_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_2F1D0828DE12AB56 (created_by), INDEX IDX_2F1D082825F94802 (modified_by), INDEX IDX_2F1D08281F6FA0AF (deleted_by), INDEX fk_factor_questions_factor1 (factor_id), INDEX fk_factor_questions_ebi_question1_idx (ebi_question_id), INDEX fk_factor_questions_ind_question1_idx (ind_question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ind_question (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, question_type_id INT DEFAULT NULL, question_category_id INT DEFAULT NULL, survey_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, has_other TINYINT(1) DEFAULT NULL, external_id VARCHAR(45) DEFAULT NULL, INDEX IDX_6ABE9142DE12AB56 (created_by), INDEX IDX_6ABE914225F94802 (modified_by), INDEX IDX_6ABE91421F6FA0AF (deleted_by), INDEX fk_ind_question_question_type1_idx (question_type_id), INDEX fk_ind_question_question_category1_idx (question_category_id), INDEX fk_ind_question_survey1_idx (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ind_questions_lang (ind_question_id INT NOT NULL, lang_id INT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, question_text VARCHAR(3000) DEFAULT NULL, question_rpt VARCHAR(3000) DEFAULT NULL, INDEX IDX_80610F30DE12AB56 (created_by), INDEX IDX_80610F3025F94802 (modified_by), INDEX IDX_80610F301F6FA0AF (deleted_by), INDEX fk_survey_questions_lang_language_master1_idx (lang_id), INDEX fk_survey_questions_lang_ind_question1_idx (ind_question_id), PRIMARY KEY(ind_question_id, lang_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE surveymarker (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, sequence INT DEFAULT NULL, INDEX IDX_4873E5ADE12AB56 (created_by), INDEX IDX_4873E5A25F94802 (modified_by), INDEX IDX_4873E5A1F6FA0AF (deleted_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE surveymarker_lang (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, lang_id INT DEFAULT NULL, surveymarker_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, name VARCHAR(100) DEFAULT NULL, INDEX IDX_8E6D6480DE12AB56 (created_by), INDEX IDX_8E6D648025F94802 (modified_by), INDEX IDX_8E6D64801F6FA0AF (deleted_by), INDEX fk_survey_marker_lang_survey_marker1_idx (surveymarker_id), INDEX fk_survey_marker_lang_language_master1_idx (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE surveymarker_questions (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, surveymarker_id INT DEFAULT NULL, ebi_question_id INT DEFAULT NULL, ind_question_id INT DEFAULT NULL, survey_id INT DEFAULT NULL, factor_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, sequence INT DEFAULT NULL, red_low NUMERIC(6, 3) DEFAULT NULL, red_high NUMERIC(6, 3) DEFAULT NULL, yellow_low NUMERIC(6, 3) DEFAULT NULL, yellow_high NUMERIC(6, 3) DEFAULT NULL, green_low NUMERIC(6, 3) DEFAULT NULL, green_high NUMERIC(6, 3) DEFAULT NULL, INDEX IDX_223450D9DE12AB56 (created_by), INDEX IDX_223450D925F94802 (modified_by), INDEX IDX_223450D91F6FA0AF (deleted_by), INDEX fk_surveymarker_questions_surveymarker1_idx (surveymarker_id), INDEX fk_surveymarker_questions_ebi_question1_idx (ebi_question_id), INDEX fk_surveymarker_questions_ind_question1_idx (ind_question_id), INDEX fk_surveymarker_questions_survey1_idx (survey_id), INDEX fk_surveymarker_questions_factor1_idx (factor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE factor ADD CONSTRAINT FK_ED38EC00DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE factor ADD CONSTRAINT FK_ED38EC0025F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE factor ADD CONSTRAINT FK_ED38EC001F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE factor ADD CONSTRAINT FK_ED38EC00B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE factor_lang ADD CONSTRAINT FK_312D3370DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE factor_lang ADD CONSTRAINT FK_312D337025F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE factor_lang ADD CONSTRAINT FK_312D33701F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE factor_lang ADD CONSTRAINT FK_312D3370BC88C1A3 FOREIGN KEY (factor_id) REFERENCES factor (id)');
        $this->addSql('ALTER TABLE factor_lang ADD CONSTRAINT FK_312D3370B213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE factor_questions ADD CONSTRAINT FK_2F1D0828DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE factor_questions ADD CONSTRAINT FK_2F1D082825F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE factor_questions ADD CONSTRAINT FK_2F1D08281F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE factor_questions ADD CONSTRAINT FK_2F1D0828BC88C1A3 FOREIGN KEY (factor_id) REFERENCES factor (id)');
        $this->addSql('ALTER TABLE factor_questions ADD CONSTRAINT FK_2F1D082879F0E193 FOREIGN KEY (ebi_question_id) REFERENCES ebi_question (id)');
        $this->addSql('ALTER TABLE factor_questions ADD CONSTRAINT FK_2F1D082851DCB924 FOREIGN KEY (ind_question_id) REFERENCES ind_question (id)');
        $this->addSql('ALTER TABLE ind_question ADD CONSTRAINT FK_6ABE9142DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ind_question ADD CONSTRAINT FK_6ABE914225F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ind_question ADD CONSTRAINT FK_6ABE91421F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ind_question ADD CONSTRAINT FK_6ABE9142CB90598E FOREIGN KEY (question_type_id) REFERENCES question_type (id)');
        $this->addSql('ALTER TABLE ind_question ADD CONSTRAINT FK_6ABE9142F142426F FOREIGN KEY (question_category_id) REFERENCES question_category (id)');
        $this->addSql('ALTER TABLE ind_question ADD CONSTRAINT FK_6ABE9142B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE ind_questions_lang ADD CONSTRAINT FK_80610F30DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ind_questions_lang ADD CONSTRAINT FK_80610F3025F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ind_questions_lang ADD CONSTRAINT FK_80610F301F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ind_questions_lang ADD CONSTRAINT FK_80610F3051DCB924 FOREIGN KEY (ind_question_id) REFERENCES ind_question (id)');
        $this->addSql('ALTER TABLE ind_questions_lang ADD CONSTRAINT FK_80610F30B213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE surveymarker ADD CONSTRAINT FK_4873E5ADE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE surveymarker ADD CONSTRAINT FK_4873E5A25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE surveymarker ADD CONSTRAINT FK_4873E5A1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE surveymarker_lang ADD CONSTRAINT FK_8E6D6480DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE surveymarker_lang ADD CONSTRAINT FK_8E6D648025F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE surveymarker_lang ADD CONSTRAINT FK_8E6D64801F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE surveymarker_lang ADD CONSTRAINT FK_8E6D6480B213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE surveymarker_lang ADD CONSTRAINT FK_8E6D6480357363AC FOREIGN KEY (surveymarker_id) REFERENCES surveymarker (id)');
        $this->addSql('ALTER TABLE surveymarker_questions ADD CONSTRAINT FK_223450D9DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE surveymarker_questions ADD CONSTRAINT FK_223450D925F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE surveymarker_questions ADD CONSTRAINT FK_223450D91F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE surveymarker_questions ADD CONSTRAINT FK_223450D9357363AC FOREIGN KEY (surveymarker_id) REFERENCES surveymarker (id)');
        $this->addSql('ALTER TABLE surveymarker_questions ADD CONSTRAINT FK_223450D979F0E193 FOREIGN KEY (ebi_question_id) REFERENCES ebi_question (id)');
        $this->addSql('ALTER TABLE surveymarker_questions ADD CONSTRAINT FK_223450D951DCB924 FOREIGN KEY (ind_question_id) REFERENCES ind_question (id)');
        $this->addSql('ALTER TABLE surveymarker_questions ADD CONSTRAINT FK_223450D9B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE surveymarker_questions ADD CONSTRAINT FK_223450D9BC88C1A3 FOREIGN KEY (factor_id) REFERENCES factor (id)');
        $this->addSql('ALTER TABLE datablock_master DROP FOREIGN KEY FK_C5DA18E94351304E');
        $this->addSql('ALTER TABLE datablock_master ADD status VARCHAR(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE datablock_master ADD CONSTRAINT FK_C5DA18E9DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE datablock_master ADD CONSTRAINT FK_C5DA18E925F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE datablock_master ADD CONSTRAINT FK_C5DA18E91F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_C5DA18E9DE12AB56 ON datablock_master (created_by)');
        $this->addSql('CREATE INDEX IDX_C5DA18E925F94802 ON datablock_master (modified_by)');
        $this->addSql('CREATE INDEX IDX_C5DA18E91F6FA0AF ON datablock_master (deleted_by)');
        $this->addSql('DROP INDEX idx_c5da18e94351304e ON datablock_master');
        $this->addSql('CREATE INDEX datablockr_datablockuiid_idx ON datablock_master (datablock_ui_id)');
        $this->addSql('ALTER TABLE datablock_master ADD CONSTRAINT FK_C5DA18E94351304E FOREIGN KEY (datablock_ui_id) REFERENCES datablock_ui (id)');
        $this->addSql('ALTER TABLE datablock_questions DROP FOREIGN KEY FK_BD00028D79F0E193');
        $this->addSql('ALTER TABLE datablock_questions DROP FOREIGN KEY FK_BD00028DF9AE3580');
        $this->addSql('ALTER TABLE datablock_questions ADD survey_id INT DEFAULT NULL, ADD ind_question_id INT DEFAULT NULL, ADD factor_id INT DEFAULT NULL, ADD type VARCHAR(255) DEFAULT NULL, ADD red_low NUMERIC(6, 3) DEFAULT NULL, ADD red_high NUMERIC(6, 3) DEFAULT NULL, ADD yellow_low NUMERIC(6, 3) DEFAULT NULL, ADD yellow_high NUMERIC(6, 3) DEFAULT NULL, ADD green_low NUMERIC(6, 3) DEFAULT NULL, ADD green_high NUMERIC(6, 3) DEFAULT NULL');
        $this->addSql('ALTER TABLE datablock_questions ADD CONSTRAINT FK_BD00028DDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE datablock_questions ADD CONSTRAINT FK_BD00028D25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE datablock_questions ADD CONSTRAINT FK_BD00028D1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE datablock_questions ADD CONSTRAINT FK_BD00028DB3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE datablock_questions ADD CONSTRAINT FK_BD00028D51DCB924 FOREIGN KEY (ind_question_id) REFERENCES ind_question (id)');
        $this->addSql('ALTER TABLE datablock_questions ADD CONSTRAINT FK_BD00028DBC88C1A3 FOREIGN KEY (factor_id) REFERENCES factor (id)');
        $this->addSql('CREATE INDEX IDX_BD00028DDE12AB56 ON datablock_questions (created_by)');
        $this->addSql('CREATE INDEX IDX_BD00028D25F94802 ON datablock_questions (modified_by)');
        $this->addSql('CREATE INDEX IDX_BD00028D1F6FA0AF ON datablock_questions (deleted_by)');
        $this->addSql('CREATE INDEX fk_datablock_questions_survey1_idx ON datablock_questions (survey_id)');
        $this->addSql('CREATE INDEX fk_datablock_questions_ind_question1_idx ON datablock_questions (ind_question_id)');
        $this->addSql('CREATE INDEX fk_datablock_questions_factor1_idx ON datablock_questions (factor_id)');
        $this->addSql('DROP INDEX idx_bd00028df9ae3580 ON datablock_questions');
        $this->addSql('CREATE INDEX fk_datablock_questions_datablock_master1_idx ON datablock_questions (datablock_id)');
        $this->addSql('DROP INDEX idx_bd00028d79f0e193 ON datablock_questions');
        $this->addSql('CREATE INDEX fk_datablock_questions_ebi_question1_idx ON datablock_questions (ebi_question_id)');
        $this->addSql('ALTER TABLE datablock_questions ADD CONSTRAINT FK_BD00028D79F0E193 FOREIGN KEY (ebi_question_id) REFERENCES ebi_question (id)');
        $this->addSql('ALTER TABLE datablock_questions ADD CONSTRAINT FK_BD00028DF9AE3580 FOREIGN KEY (datablock_id) REFERENCES datablock_master (id)');
        $this->addSql('ALTER TABLE ebi_question DROP FOREIGN KEY FK_9050C5B4F142426F');
        $this->addSql('ALTER TABLE ebi_question DROP FOREIGN KEY FK_9050C5B4CB90598E');
        $this->addSql('ALTER TABLE ebi_question ADD has_other TINYINT(1) DEFAULT NULL, ADD external_id VARCHAR(45) DEFAULT NULL');
        $this->addSql('ALTER TABLE ebi_question ADD CONSTRAINT FK_9050C5B4DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_question ADD CONSTRAINT FK_9050C5B425F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_question ADD CONSTRAINT FK_9050C5B41F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_9050C5B4DE12AB56 ON ebi_question (created_by)');
        $this->addSql('CREATE INDEX IDX_9050C5B425F94802 ON ebi_question (modified_by)');
        $this->addSql('CREATE INDEX IDX_9050C5B41F6FA0AF ON ebi_question (deleted_by)');
        $this->addSql('DROP INDEX idx_9050c5b4cb90598e ON ebi_question');
        $this->addSql('CREATE INDEX fk_ebi_question_question_type1_idx ON ebi_question (question_type_id)');
        $this->addSql('DROP INDEX idx_9050c5b4f142426f ON ebi_question');
        $this->addSql('CREATE INDEX fk_ebi_question_question_category1_idx ON ebi_question (question_category_id)');
        $this->addSql('ALTER TABLE ebi_question ADD CONSTRAINT FK_9050C5B4F142426F FOREIGN KEY (question_category_id) REFERENCES question_category (id)');
        $this->addSql('ALTER TABLE ebi_question ADD CONSTRAINT FK_9050C5B4CB90598E FOREIGN KEY (question_type_id) REFERENCES question_type (id)');
        $this->addSql('ALTER TABLE ebi_questions_lang DROP FOREIGN KEY FK_CA5C32B213FA4');
        $this->addSql('ALTER TABLE ebi_questions_lang DROP FOREIGN KEY FK_CA5C3279F0E193');
        $this->addSql('ALTER TABLE ebi_questions_lang ADD question_rpt LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE ebi_questions_lang ADD CONSTRAINT FK_CA5C32DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_questions_lang ADD CONSTRAINT FK_CA5C3225F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_questions_lang ADD CONSTRAINT FK_CA5C321F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_CA5C32DE12AB56 ON ebi_questions_lang (created_by)');
        $this->addSql('CREATE INDEX IDX_CA5C3225F94802 ON ebi_questions_lang (modified_by)');
        $this->addSql('CREATE INDEX IDX_CA5C321F6FA0AF ON ebi_questions_lang (deleted_by)');
        $this->addSql('DROP INDEX idx_ca5c3279f0e193 ON ebi_questions_lang');
        $this->addSql('CREATE INDEX fk_ebi_questions_lang_ebi_question1_idx ON ebi_questions_lang (ebi_question_id)');
        $this->addSql('DROP INDEX idx_ca5c32b213fa4 ON ebi_questions_lang');
        $this->addSql('CREATE INDEX fk_ebi_questions_lang_language_master1_idx ON ebi_questions_lang (lang_id)');
        $this->addSql('ALTER TABLE ebi_questions_lang ADD CONSTRAINT FK_CA5C32B213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE ebi_questions_lang ADD CONSTRAINT FK_CA5C3279F0E193 FOREIGN KEY (ebi_question_id) REFERENCES ebi_question (id)');
        $this->addSql('ALTER TABLE survey ADD external_id VARCHAR(45) DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE datablock_questions DROP FOREIGN KEY FK_BD00028DBC88C1A3');
        $this->addSql('ALTER TABLE factor_lang DROP FOREIGN KEY FK_312D3370BC88C1A3');
        $this->addSql('ALTER TABLE factor_questions DROP FOREIGN KEY FK_2F1D0828BC88C1A3');
        $this->addSql('ALTER TABLE surveymarker_questions DROP FOREIGN KEY FK_223450D9BC88C1A3');
        $this->addSql('ALTER TABLE datablock_questions DROP FOREIGN KEY FK_BD00028D51DCB924');
        $this->addSql('ALTER TABLE factor_questions DROP FOREIGN KEY FK_2F1D082851DCB924');
        $this->addSql('ALTER TABLE ind_questions_lang DROP FOREIGN KEY FK_80610F3051DCB924');
        $this->addSql('ALTER TABLE surveymarker_questions DROP FOREIGN KEY FK_223450D951DCB924');
        $this->addSql('ALTER TABLE surveymarker_lang DROP FOREIGN KEY FK_8E6D6480357363AC');
        $this->addSql('ALTER TABLE surveymarker_questions DROP FOREIGN KEY FK_223450D9357363AC');
        $this->addSql('DROP TABLE factor');
        $this->addSql('DROP TABLE factor_lang');
        $this->addSql('DROP TABLE factor_questions');
        $this->addSql('DROP TABLE ind_question');
        $this->addSql('DROP TABLE ind_questions_lang');
        $this->addSql('DROP TABLE surveymarker');
        $this->addSql('DROP TABLE surveymarker_lang');
        $this->addSql('DROP TABLE surveymarker_questions');
        $this->addSql('ALTER TABLE datablock_master DROP FOREIGN KEY FK_C5DA18E9DE12AB56');
        $this->addSql('ALTER TABLE datablock_master DROP FOREIGN KEY FK_C5DA18E925F94802');
        $this->addSql('ALTER TABLE datablock_master DROP FOREIGN KEY FK_C5DA18E91F6FA0AF');
        $this->addSql('DROP INDEX IDX_C5DA18E9DE12AB56 ON datablock_master');
        $this->addSql('DROP INDEX IDX_C5DA18E925F94802 ON datablock_master');
        $this->addSql('DROP INDEX IDX_C5DA18E91F6FA0AF ON datablock_master');
        $this->addSql('ALTER TABLE datablock_master DROP FOREIGN KEY FK_C5DA18E94351304E');
        $this->addSql('ALTER TABLE datablock_master DROP status');
        $this->addSql('DROP INDEX datablockr_datablockuiid_idx ON datablock_master');
        $this->addSql('CREATE INDEX IDX_C5DA18E94351304E ON datablock_master (datablock_ui_id)');
        $this->addSql('ALTER TABLE datablock_master ADD CONSTRAINT FK_C5DA18E94351304E FOREIGN KEY (datablock_ui_id) REFERENCES datablock_ui (id)');
        $this->addSql('ALTER TABLE datablock_master_lang DROP FOREIGN KEY FK_EAD6BD4ADE12AB56');
        $this->addSql('ALTER TABLE datablock_master_lang DROP FOREIGN KEY FK_EAD6BD4A25F94802');
        $this->addSql('ALTER TABLE datablock_master_lang DROP FOREIGN KEY FK_EAD6BD4A1F6FA0AF');
        $this->addSql('DROP INDEX IDX_EAD6BD4ADE12AB56 ON datablock_master_lang');
        $this->addSql('DROP INDEX IDX_EAD6BD4A25F94802 ON datablock_master_lang');
        $this->addSql('DROP INDEX IDX_EAD6BD4A1F6FA0AF ON datablock_master_lang');
        $this->addSql('ALTER TABLE datablock_master_lang DROP FOREIGN KEY FK_EAD6BD4AF9AE3580');
        $this->addSql('ALTER TABLE datablock_master_lang DROP FOREIGN KEY FK_EAD6BD4AB213FA4');
        $this->addSql('DROP INDEX fk_datablocklang_datablockid_idx ON datablock_master_lang');
        $this->addSql('CREATE INDEX IDX_EAD6BD4AF9AE3580 ON datablock_master_lang (datablock_id)');
        $this->addSql('DROP INDEX fk_datablocklang_langid_idx ON datablock_master_lang');
        $this->addSql('CREATE INDEX IDX_EAD6BD4AB213FA4 ON datablock_master_lang (lang_id)');
        $this->addSql('ALTER TABLE datablock_master_lang ADD CONSTRAINT FK_EAD6BD4AF9AE3580 FOREIGN KEY (datablock_id) REFERENCES datablock_master (id)');
        $this->addSql('ALTER TABLE datablock_master_lang ADD CONSTRAINT FK_EAD6BD4AB213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('DROP INDEX IDX_BD00028DDE12AB56 ON datablock_questions');
        $this->addSql('DROP INDEX IDX_BD00028D25F94802 ON datablock_questions');
        $this->addSql('DROP INDEX IDX_BD00028D1F6FA0AF ON datablock_questions');
        $this->addSql('DROP INDEX fk_datablock_questions_survey1_idx ON datablock_questions');
        $this->addSql('DROP INDEX fk_datablock_questions_ind_question1_idx ON datablock_questions');
        $this->addSql('DROP INDEX fk_datablock_questions_factor1_idx ON datablock_questions');
        $this->addSql('ALTER TABLE datablock_questions DROP FOREIGN KEY FK_BD00028DF9AE3580');
        $this->addSql('ALTER TABLE datablock_questions DROP FOREIGN KEY FK_BD00028D79F0E193');
        $this->addSql('ALTER TABLE datablock_questions DROP survey_id, DROP ind_question_id, DROP factor_id, DROP type, DROP red_low, DROP red_high, DROP yellow_low, DROP yellow_high, DROP green_low, DROP green_high');
        $this->addSql('DROP INDEX fk_datablock_questions_datablock_master1_idx ON datablock_questions');
        $this->addSql('CREATE INDEX IDX_BD00028DF9AE3580 ON datablock_questions (datablock_id)');
        $this->addSql('DROP INDEX fk_datablock_questions_ebi_question1_idx ON datablock_questions');
        $this->addSql('CREATE INDEX IDX_BD00028D79F0E193 ON datablock_questions (ebi_question_id)');
        $this->addSql('ALTER TABLE datablock_questions ADD CONSTRAINT FK_BD00028DF9AE3580 FOREIGN KEY (datablock_id) REFERENCES datablock_master (id)');
        $this->addSql('ALTER TABLE datablock_questions ADD CONSTRAINT FK_BD00028D79F0E193 FOREIGN KEY (ebi_question_id) REFERENCES ebi_question (id)');
        $this->addSql('ALTER TABLE ebi_question DROP FOREIGN KEY FK_9050C5B4DE12AB56');
        $this->addSql('ALTER TABLE ebi_question DROP FOREIGN KEY FK_9050C5B425F94802');
        $this->addSql('ALTER TABLE ebi_question DROP FOREIGN KEY FK_9050C5B41F6FA0AF');
        $this->addSql('DROP INDEX IDX_9050C5B4DE12AB56 ON ebi_question');
        $this->addSql('DROP INDEX IDX_9050C5B425F94802 ON ebi_question');
        $this->addSql('DROP INDEX IDX_9050C5B41F6FA0AF ON ebi_question');
        $this->addSql('ALTER TABLE ebi_question DROP FOREIGN KEY FK_9050C5B4CB90598E');
        $this->addSql('ALTER TABLE ebi_question DROP FOREIGN KEY FK_9050C5B4F142426F');
        $this->addSql('ALTER TABLE ebi_question DROP has_other, DROP external_id');
        $this->addSql('DROP INDEX fk_ebi_question_question_type1_idx ON ebi_question');
        $this->addSql('CREATE INDEX IDX_9050C5B4CB90598E ON ebi_question (question_type_id)');
        $this->addSql('DROP INDEX fk_ebi_question_question_category1_idx ON ebi_question');
        $this->addSql('CREATE INDEX IDX_9050C5B4F142426F ON ebi_question (question_category_id)');
        $this->addSql('ALTER TABLE ebi_question ADD CONSTRAINT FK_9050C5B4CB90598E FOREIGN KEY (question_type_id) REFERENCES question_type (id)');
        $this->addSql('ALTER TABLE ebi_question ADD CONSTRAINT FK_9050C5B4F142426F FOREIGN KEY (question_category_id) REFERENCES question_category (id)');
        $this->addSql('ALTER TABLE ebi_questions_lang DROP FOREIGN KEY FK_CA5C32DE12AB56');
        $this->addSql('ALTER TABLE ebi_questions_lang DROP FOREIGN KEY FK_CA5C3225F94802');
        $this->addSql('ALTER TABLE ebi_questions_lang DROP FOREIGN KEY FK_CA5C321F6FA0AF');
        $this->addSql('DROP INDEX IDX_CA5C32DE12AB56 ON ebi_questions_lang');
        $this->addSql('DROP INDEX IDX_CA5C3225F94802 ON ebi_questions_lang');
        $this->addSql('DROP INDEX IDX_CA5C321F6FA0AF ON ebi_questions_lang');
        $this->addSql('ALTER TABLE ebi_questions_lang DROP FOREIGN KEY FK_CA5C3279F0E193');
        $this->addSql('ALTER TABLE ebi_questions_lang DROP FOREIGN KEY FK_CA5C32B213FA4');
        $this->addSql('ALTER TABLE ebi_questions_lang DROP question_rpt');
        $this->addSql('DROP INDEX fk_ebi_questions_lang_ebi_question1_idx ON ebi_questions_lang');
        $this->addSql('CREATE INDEX IDX_CA5C3279F0E193 ON ebi_questions_lang (ebi_question_id)');
        $this->addSql('DROP INDEX fk_ebi_questions_lang_language_master1_idx ON ebi_questions_lang');
        $this->addSql('CREATE INDEX IDX_CA5C32B213FA4 ON ebi_questions_lang (lang_id)');
        $this->addSql('ALTER TABLE ebi_questions_lang ADD CONSTRAINT FK_CA5C3279F0E193 FOREIGN KEY (ebi_question_id) REFERENCES ebi_question (id)');
        $this->addSql('ALTER TABLE ebi_questions_lang ADD CONSTRAINT FK_CA5C32B213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE survey DROP external_id');
    }
}
