<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150824193211 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');		
        $this->addSql('CREATE TABLE report_calculated_values (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT NOT NULL, person_id INT DEFAULT NULL, report_id INT DEFAULT NULL, section_id INT DEFAULT NULL, element_bucket_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_CB8DFCDCDE12AB56 (created_by), INDEX IDX_CB8DFCDC25F94802 (modified_by), INDEX IDX_CB8DFCDC1F6FA0AF (deleted_by), INDEX IDX_CB8DFCDCF4837C1B (org_id), INDEX fk_report_calculated_values_reports1_idx (report_id), INDEX fk_report_calculated_values_report_sections1_idx (section_id), INDEX fk_report_calculated_values_report_element_buckets1_idx (element_bucket_id), INDEX fk_report_calculated_values_person1_idx (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_element_buckets (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, element_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, bucket_name VARCHAR(45) DEFAULT NULL, bucket_text VARCHAR(500) DEFAULT NULL, range_min NUMERIC(8, 4) DEFAULT NULL, range_max NUMERIC(8, 4) DEFAULT NULL, INDEX IDX_FD533799DE12AB56 (created_by), INDEX IDX_FD53379925F94802 (modified_by), INDEX IDX_FD5337991F6FA0AF (deleted_by), INDEX fk_report_element_buckets_report_elements1_idx (element_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_section_elements (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, section_id INT DEFAULT NULL, factor_id INT DEFAULT NULL, survey_question_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, title VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, source_type enum(\'F\', \'Q\'), icon_file_name VARCHAR(100) DEFAULT NULL, INDEX IDX_91D6E5F5DE12AB56 (created_by), INDEX IDX_91D6E5F525F94802 (modified_by), INDEX IDX_91D6E5F51F6FA0AF (deleted_by), INDEX fk_report_elements_report_sections1_idx (section_id), INDEX fk_report_elements_factor1_idx (factor_id), INDEX fk_report_elements_survey_questions1_idx (survey_question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_tips (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, section_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, title VARCHAR(25) DEFAULT NULL, description VARCHAR(250) DEFAULT NULL, INDEX IDX_1700ACF3DE12AB56 (created_by), INDEX IDX_1700ACF325F94802 (modified_by), INDEX IDX_1700ACF31F6FA0AF (deleted_by), INDEX fk_report_tips_report_sections1_idx (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE report_calculated_values ADD CONSTRAINT FK_CB8DFCDCDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_calculated_values ADD CONSTRAINT FK_CB8DFCDC25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_calculated_values ADD CONSTRAINT FK_CB8DFCDC1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_calculated_values ADD CONSTRAINT FK_CB8DFCDCF4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE report_calculated_values ADD CONSTRAINT FK_CB8DFCDC217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_calculated_values ADD CONSTRAINT FK_CB8DFCDC4BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');
        $this->addSql('ALTER TABLE report_calculated_values ADD CONSTRAINT FK_CB8DFCDCD823E37A FOREIGN KEY (section_id) REFERENCES report_sections (id)');
        $this->addSql('ALTER TABLE report_calculated_values ADD CONSTRAINT FK_CB8DFCDC2CC57666 FOREIGN KEY (element_bucket_id) REFERENCES report_element_buckets (id)');
        $this->addSql('ALTER TABLE report_element_buckets ADD CONSTRAINT FK_FD533799DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_element_buckets ADD CONSTRAINT FK_FD53379925F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_element_buckets ADD CONSTRAINT FK_FD5337991F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_element_buckets ADD CONSTRAINT FK_FD5337991F1F2A24 FOREIGN KEY (element_id) REFERENCES report_section_elements (id)');
        $this->addSql('ALTER TABLE report_section_elements ADD CONSTRAINT FK_91D6E5F5DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_section_elements ADD CONSTRAINT FK_91D6E5F525F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_section_elements ADD CONSTRAINT FK_91D6E5F51F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_section_elements ADD CONSTRAINT FK_91D6E5F5D823E37A FOREIGN KEY (section_id) REFERENCES report_sections (id)');
        $this->addSql('ALTER TABLE report_section_elements ADD CONSTRAINT FK_91D6E5F5BC88C1A3 FOREIGN KEY (factor_id) REFERENCES factor (id)');
        $this->addSql('ALTER TABLE report_section_elements ADD CONSTRAINT FK_91D6E5F5A6DF29BA FOREIGN KEY (survey_question_id) REFERENCES survey_questions (id)');
        $this->addSql('ALTER TABLE report_tips ADD CONSTRAINT FK_1700ACF3DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_tips ADD CONSTRAINT FK_1700ACF325F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_tips ADD CONSTRAINT FK_1700ACF31F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE report_tips ADD CONSTRAINT FK_1700ACF3D823E37A FOREIGN KEY (section_id) REFERENCES report_sections (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE report_calculated_values DROP FOREIGN KEY FK_CB8DFCDC2CC57666');
        $this->addSql('ALTER TABLE report_element_buckets DROP FOREIGN KEY FK_FD5337991F1F2A24');
        $this->addSql('DROP TABLE report_calculated_values');
        $this->addSql('DROP TABLE report_element_buckets');
        $this->addSql('DROP TABLE report_section_elements');
        $this->addSql('DROP TABLE report_tips');
    }
}
