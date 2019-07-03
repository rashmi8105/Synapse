<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150424094408 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        
        $this->addSql('CREATE TABLE risk_variable (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, ebi_metadata_id INT DEFAULT NULL, org_metadata_id INT DEFAULT NULL, ebi_question_id INT DEFAULT NULL, survey_id INT DEFAULT NULL, org_id INT DEFAULT NULL, org_question_id INT DEFAULT NULL, survey_questions_id INT DEFAULT NULL, factor_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, risk_b_variable VARCHAR(100) DEFAULT NULL, variable_type ENUM(\'continuous\', \'categorical\'), is_calculated TINYINT(1) DEFAULT NULL, calc_type ENUM(\'Most Recent\', \'Sum\', \'Average\', \'Count\', \'Academic Update\'), calculation_start_date DATETIME DEFAULT NULL, calculation_end_date DATETIME DEFAULT NULL, is_archived TINYINT(1) DEFAULT NULL, source ENUM(\'profile\',\'surveyquestion\',\'surveyfactor\',\'isp\',\'isq\',\'questionbank\'), INDEX IDX_2A64C4E2DE12AB56 (created_by), INDEX IDX_2A64C4E225F94802 (modified_by), INDEX IDX_2A64C4E21F6FA0AF (deleted_by), INDEX fk_risk_variable_ebi_metadata1_idx (ebi_metadata_id), INDEX fk_risk_variable_org_metadata1_idx (org_metadata_id), INDEX fk_risk_variable_ebi_question1_idx (ebi_question_id), INDEX fk_risk_variable_survey1_idx (survey_id), INDEX fk_risk_variable_organization1_idx (org_id), INDEX fk_risk_variable_org_question1_idx (org_question_id), INDEX fk_risk_variable_survey_questions1_idx (survey_questions_id), INDEX fk_risk_variable_factor1_idx (factor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE risk_variable_range (bucket_value INT NOT NULL, risk_variable_id INT NOT NULL, min NUMERIC(6, 4) DEFAULT NULL, max NUMERIC(6, 4) DEFAULT NULL, INDEX fk_risk_model_bucket_range_risk_variable1_idx (risk_variable_id), PRIMARY KEY(bucket_value, risk_variable_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE risk_variable ADD CONSTRAINT FK_2A64C4E2DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_variable ADD CONSTRAINT FK_2A64C4E225F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_variable ADD CONSTRAINT FK_2A64C4E21F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE risk_variable ADD CONSTRAINT FK_2A64C4E2BB49FE75 FOREIGN KEY (ebi_metadata_id) REFERENCES ebi_metadata (id)');
        $this->addSql('ALTER TABLE risk_variable ADD CONSTRAINT FK_2A64C4E24012B3BF FOREIGN KEY (org_metadata_id) REFERENCES org_metadata (id)');
        $this->addSql('ALTER TABLE risk_variable ADD CONSTRAINT FK_2A64C4E279F0E193 FOREIGN KEY (ebi_question_id) REFERENCES ebi_question (id)');
        $this->addSql('ALTER TABLE risk_variable ADD CONSTRAINT FK_2A64C4E2B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE risk_variable ADD CONSTRAINT FK_2A64C4E2F4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE risk_variable ADD CONSTRAINT FK_2A64C4E282ABAC59 FOREIGN KEY (org_question_id) REFERENCES org_question (id)');
        $this->addSql('ALTER TABLE risk_variable ADD CONSTRAINT FK_2A64C4E2CC63389E FOREIGN KEY (survey_questions_id) REFERENCES survey_questions (id)');
        $this->addSql('ALTER TABLE risk_variable ADD CONSTRAINT FK_2A64C4E2BC88C1A3 FOREIGN KEY (factor_id) REFERENCES factor (id)');
        $this->addSql('ALTER TABLE risk_variable_range ADD CONSTRAINT FK_DF38E125296E76DF FOREIGN KEY (risk_variable_id) REFERENCES risk_variable (id)');
        
        $this->addSql('ALTER TABLE risk_model_master ADD name VARCHAR(100) DEFAULT NULL, ADD calculation_start_date DATETIME DEFAULT NULL, ADD calculation_end_date DATETIME DEFAULT NULL, ADD enrollment_date DATETIME DEFAULT NULL, DROP effective_from, DROP effective_to, DROP status, CHANGE risk_key model_state enum(\'Archived\', \'Assigned\',\'Unassigned\',\'InProcess\') NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');        
        
        $this->addSql('ALTER TABLE risk_variable_category DROP FOREIGN KEY FK_1B6EF2A7296E76DF');
        $this->addSql('ALTER TABLE risk_variable_range DROP FOREIGN KEY FK_DF38E125296E76DF');        
        $this->addSql('DROP TABLE risk_variable');
        $this->addSql('DROP TABLE risk_variable_category');
        $this->addSql('DROP TABLE risk_variable_range');
        
        $this->addSql('ALTER TABLE risk_model_master ADD effective_from DATETIME DEFAULT NULL, ADD effective_to DATETIME DEFAULT NULL, ADD status VARCHAR(1) DEFAULT NULL COLLATE utf8_unicode_ci, DROP name, DROP calculation_start_date, DROP calculation_end_date, DROP enrollment_date, CHANGE model_state risk_key VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
