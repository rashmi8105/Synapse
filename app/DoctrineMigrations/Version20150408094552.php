<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150408094552 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE survey_response (id BIGINT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT DEFAULT NULL, person_id INT DEFAULT NULL, survey_id INT DEFAULT NULL, org_academic_year_id INT DEFAULT NULL, org_academic_terms_id INT DEFAULT NULL, survey_questions_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, response_type ENUM(\'decimal\',\'char\',\'charmax\'), decimal_value NUMERIC(9, 2) DEFAULT NULL, char_value VARCHAR(500) DEFAULT NULL, charmax_value VARCHAR(5000) DEFAULT NULL, INDEX IDX_628C4DDCDE12AB56 (created_by), INDEX IDX_628C4DDC25F94802 (modified_by), INDEX IDX_628C4DDC1F6FA0AF (deleted_by), INDEX fk_survey_response_organization1 (org_id), INDEX fk_survey_response_person1 (person_id), INDEX fk_survey_response_survey1_idx (survey_id), INDEX fk_survey_response_org_academic_year1_idx (org_academic_year_id), INDEX fk_survey_response_org_academic_terms1_idx (org_academic_terms_id), INDEX fk_survey_response_survey_questions1_idx (survey_questions_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wess_link (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT DEFAULT NULL, survey_id INT DEFAULT NULL, org_academic_year_id INT DEFAULT NULL, org_academic_terms_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, cohort_code INT DEFAULT NULL, wess_survey_id INT DEFAULT NULL, wess_cohort_id INT DEFAULT NULL, wess_order_id INT DEFAULT NULL, wess_launchedflag INT DEFAULT NULL, wess_maporder_key INT DEFAULT NULL, wess_prod_year INT DEFAULT NULL, wess_cust_id INT DEFAULT NULL, status ENUM(\'open\',\'ready\',\'launched\',\'closed\'), open_date DATETIME DEFAULT NULL, close_date DATETIME DEFAULT NULL, INDEX IDX_175DBF43DE12AB56 (created_by), INDEX IDX_175DBF4325F94802 (modified_by), INDEX IDX_175DBF431F6FA0AF (deleted_by), INDEX fk_wess_link_organization1 (org_id), INDEX fk_wess_link_survey1_idx (survey_id), INDEX fk_wess_link_org_academic_year1_idx (org_academic_year_id), INDEX fk_wess_link_org_academic_terms1_idx (org_academic_terms_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE survey_response ADD CONSTRAINT FK_628C4DDCDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_response ADD CONSTRAINT FK_628C4DDC25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_response ADD CONSTRAINT FK_628C4DDC1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_response ADD CONSTRAINT FK_628C4DDCF4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE survey_response ADD CONSTRAINT FK_628C4DDC217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_response ADD CONSTRAINT FK_628C4DDCB3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE survey_response ADD CONSTRAINT FK_628C4DDCF3B0CE4A FOREIGN KEY (org_academic_year_id) REFERENCES org_academic_year (id)');
        $this->addSql('ALTER TABLE survey_response ADD CONSTRAINT FK_628C4DDC8D7CC0D2 FOREIGN KEY (org_academic_terms_id) REFERENCES org_academic_terms (id)');
        $this->addSql('ALTER TABLE survey_response ADD CONSTRAINT FK_628C4DDCCC63389E FOREIGN KEY (survey_questions_id) REFERENCES survey_questions (id)');
        $this->addSql('ALTER TABLE wess_link ADD CONSTRAINT FK_175DBF43DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE wess_link ADD CONSTRAINT FK_175DBF4325F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE wess_link ADD CONSTRAINT FK_175DBF431F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE wess_link ADD CONSTRAINT FK_175DBF43F4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE wess_link ADD CONSTRAINT FK_175DBF43B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE wess_link ADD CONSTRAINT FK_175DBF43F3B0CE4A FOREIGN KEY (org_academic_year_id) REFERENCES org_academic_year (id)');
        $this->addSql('ALTER TABLE wess_link ADD CONSTRAINT FK_175DBF438D7CC0D2 FOREIGN KEY (org_academic_terms_id) REFERENCES org_academic_terms (id)');
        $this->addSql('ALTER TABLE ebi_question_options DROP FOREIGN KEY FK_B56C5C6CB213FA4');
        $this->addSql('ALTER TABLE ebi_question_options DROP FOREIGN KEY FK_B56C5C6C79F0E193');
        $this->addSql('ALTER TABLE ebi_question_options CHANGE option_name option_text VARCHAR(45) DEFAULT NULL');
        $this->addSql('ALTER TABLE ebi_question_options ADD CONSTRAINT FK_B56C5C6CDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_question_options ADD CONSTRAINT FK_B56C5C6C25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE ebi_question_options ADD CONSTRAINT FK_B56C5C6C1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_B56C5C6CDE12AB56 ON ebi_question_options (created_by)');
        $this->addSql('CREATE INDEX IDX_B56C5C6C25F94802 ON ebi_question_options (modified_by)');
        $this->addSql('CREATE INDEX IDX_B56C5C6C1F6FA0AF ON ebi_question_options (deleted_by)');
        $this->addSql('DROP INDEX idx_b56c5c6c79f0e193 ON ebi_question_options');
        $this->addSql('CREATE INDEX fk_ebi_question_options_ebi_question1_idx ON ebi_question_options (ebi_question_id)');
        $this->addSql('DROP INDEX idx_b56c5c6cb213fa4 ON ebi_question_options');
        $this->addSql('CREATE INDEX fk_ebi_question_options_language_master1_idx ON ebi_question_options (lang_id)');
        $this->addSql('ALTER TABLE ebi_question_options ADD CONSTRAINT FK_B56C5C6CB213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE ebi_question_options ADD CONSTRAINT FK_B56C5C6C79F0E193 FOREIGN KEY (ebi_question_id) REFERENCES ebi_question (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE survey_response');
        $this->addSql('DROP TABLE wess_link');
        $this->addSql('ALTER TABLE ebi_question_options DROP FOREIGN KEY FK_B56C5C6CDE12AB56');
        $this->addSql('ALTER TABLE ebi_question_options DROP FOREIGN KEY FK_B56C5C6C25F94802');
        $this->addSql('ALTER TABLE ebi_question_options DROP FOREIGN KEY FK_B56C5C6C1F6FA0AF');
        $this->addSql('DROP INDEX IDX_B56C5C6CDE12AB56 ON ebi_question_options');
        $this->addSql('DROP INDEX IDX_B56C5C6C25F94802 ON ebi_question_options');
        $this->addSql('DROP INDEX IDX_B56C5C6C1F6FA0AF ON ebi_question_options');
        $this->addSql('ALTER TABLE ebi_question_options DROP FOREIGN KEY FK_B56C5C6C79F0E193');
        $this->addSql('ALTER TABLE ebi_question_options DROP FOREIGN KEY FK_B56C5C6CB213FA4');
        $this->addSql('ALTER TABLE ebi_question_options CHANGE option_text option_name VARCHAR(45) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('DROP INDEX fk_ebi_question_options_ebi_question1_idx ON ebi_question_options');
        $this->addSql('CREATE INDEX IDX_B56C5C6C79F0E193 ON ebi_question_options (ebi_question_id)');
        $this->addSql('DROP INDEX fk_ebi_question_options_language_master1_idx ON ebi_question_options');
        $this->addSql('CREATE INDEX IDX_B56C5C6CB213FA4 ON ebi_question_options (lang_id)');
        $this->addSql('ALTER TABLE ebi_question_options ADD CONSTRAINT FK_B56C5C6C79F0E193 FOREIGN KEY (ebi_question_id) REFERENCES ebi_question (id)');
        $this->addSql('ALTER TABLE ebi_question_options ADD CONSTRAINT FK_B56C5C6CB213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
    }
}
