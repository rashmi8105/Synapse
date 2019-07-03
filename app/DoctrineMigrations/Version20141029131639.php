<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141029131639 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE datablock_questions (id INT AUTO_INCREMENT NOT NULL, datablock_id INT DEFAULT NULL, ebi_question_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_BD00028DF9AE3580 (datablock_id), INDEX IDX_BD00028D79F0E193 (ebi_question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ebi_question (id INT AUTO_INCREMENT NOT NULL, question_type_id INT DEFAULT NULL, question_category_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, question_key VARCHAR(45) DEFAULT NULL, INDEX IDX_9050C5B4CB90598E (question_type_id), INDEX IDX_9050C5B4F142426F (question_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ebi_question_options (id INT AUTO_INCREMENT NOT NULL, ebi_question_id INT DEFAULT NULL, lang_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, option_name VARCHAR(45) DEFAULT NULL, option_value VARCHAR(5000) DEFAULT NULL, sequence SMALLINT DEFAULT NULL, INDEX IDX_B56C5C6C79F0E193 (ebi_question_id), INDEX IDX_B56C5C6CB213FA4 (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ebi_questions_lang (id INT AUTO_INCREMENT NOT NULL, ebi_question_id INT DEFAULT NULL, lang_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, question_text LONGTEXT DEFAULT NULL, INDEX IDX_CA5C3279F0E193 (ebi_question_id), INDEX IDX_CA5C32B213FA4 (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE org_permissionset_question (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, org_permissionset_id INT DEFAULT NULL, org_question_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_5AC5069E32C8A3DE (organization_id), INDEX IDX_5AC5069E7ABB76BC (org_permissionset_id), INDEX IDX_5AC5069E82ABAC59 (org_question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE org_question (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, question_type_id INT DEFAULT NULL, question_category_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, question_key VARCHAR(45) DEFAULT NULL, question_text LONGTEXT DEFAULT NULL, INDEX IDX_CA58D9AD32C8A3DE (organization_id), INDEX IDX_CA58D9ADCB90598E (question_type_id), INDEX IDX_CA58D9ADF142426F (question_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE org_question_options (id INT AUTO_INCREMENT NOT NULL, org_question_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, option_name VARCHAR(45) DEFAULT NULL, option_value VARCHAR(5000) DEFAULT NULL, sequence SMALLINT DEFAULT NULL, INDEX IDX_E816D5D182ABAC59 (org_question_id), INDEX IDX_E816D5D132C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_category (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_category_lang (id INT AUTO_INCREMENT NOT NULL, question_category_id INT DEFAULT NULL, lang_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, description VARCHAR(100) DEFAULT NULL, INDEX IDX_22D33462F142426F (question_category_id), INDEX IDX_22D33462B213FA4 (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_type (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_type_lang (id INT AUTO_INCREMENT NOT NULL, question_type_id INT DEFAULT NULL, lang_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, description VARCHAR(100) DEFAULT NULL, INDEX IDX_EFD305F1CB90598E (question_type_id), INDEX IDX_EFD305F1B213FA4 (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE datablock_questions ADD CONSTRAINT FK_BD00028DF9AE3580 FOREIGN KEY (datablock_id) REFERENCES datablock_master (id)');
        $this->addSql('ALTER TABLE datablock_questions ADD CONSTRAINT FK_BD00028D79F0E193 FOREIGN KEY (ebi_question_id) REFERENCES ebi_question (id)');
        $this->addSql('ALTER TABLE ebi_question ADD CONSTRAINT FK_9050C5B4CB90598E FOREIGN KEY (question_type_id) REFERENCES question_type (id)');
        $this->addSql('ALTER TABLE ebi_question ADD CONSTRAINT FK_9050C5B4F142426F FOREIGN KEY (question_category_id) REFERENCES question_category (id)');
        $this->addSql('ALTER TABLE ebi_question_options ADD CONSTRAINT FK_B56C5C6C79F0E193 FOREIGN KEY (ebi_question_id) REFERENCES ebi_question (id)');
        $this->addSql('ALTER TABLE ebi_question_options ADD CONSTRAINT FK_B56C5C6CB213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE ebi_questions_lang ADD CONSTRAINT FK_CA5C3279F0E193 FOREIGN KEY (ebi_question_id) REFERENCES ebi_question (id)');
        $this->addSql('ALTER TABLE ebi_questions_lang ADD CONSTRAINT FK_CA5C32B213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE org_permissionset_question ADD CONSTRAINT FK_5AC5069E32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_permissionset_question ADD CONSTRAINT FK_5AC5069E7ABB76BC FOREIGN KEY (org_permissionset_id) REFERENCES org_permissionset (id)');
        $this->addSql('ALTER TABLE org_permissionset_question ADD CONSTRAINT FK_5AC5069E82ABAC59 FOREIGN KEY (org_question_id) REFERENCES org_question (id)');
        $this->addSql('ALTER TABLE org_question ADD CONSTRAINT FK_CA58D9AD32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_question ADD CONSTRAINT FK_CA58D9ADCB90598E FOREIGN KEY (question_type_id) REFERENCES question_type (id)');
        $this->addSql('ALTER TABLE org_question ADD CONSTRAINT FK_CA58D9ADF142426F FOREIGN KEY (question_category_id) REFERENCES question_category (id)');
        $this->addSql('ALTER TABLE org_question_options ADD CONSTRAINT FK_E816D5D182ABAC59 FOREIGN KEY (org_question_id) REFERENCES org_question (id)');
        $this->addSql('ALTER TABLE org_question_options ADD CONSTRAINT FK_E816D5D132C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE question_category_lang ADD CONSTRAINT FK_22D33462F142426F FOREIGN KEY (question_category_id) REFERENCES question_category (id)');
        $this->addSql('ALTER TABLE question_category_lang ADD CONSTRAINT FK_22D33462B213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE question_type_lang ADD CONSTRAINT FK_EFD305F1CB90598E FOREIGN KEY (question_type_id) REFERENCES question_type (id)');
        $this->addSql('ALTER TABLE question_type_lang ADD CONSTRAINT FK_EFD305F1B213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE datablock_questions DROP FOREIGN KEY FK_BD00028D79F0E193');
        $this->addSql('ALTER TABLE ebi_question_options DROP FOREIGN KEY FK_B56C5C6C79F0E193');
        $this->addSql('ALTER TABLE ebi_questions_lang DROP FOREIGN KEY FK_CA5C3279F0E193');
        $this->addSql('ALTER TABLE org_permissionset_question DROP FOREIGN KEY FK_5AC5069E82ABAC59');
        $this->addSql('ALTER TABLE org_question_options DROP FOREIGN KEY FK_E816D5D182ABAC59');
        $this->addSql('ALTER TABLE ebi_question DROP FOREIGN KEY FK_9050C5B4F142426F');
        $this->addSql('ALTER TABLE org_question DROP FOREIGN KEY FK_CA58D9ADF142426F');
        $this->addSql('ALTER TABLE question_category_lang DROP FOREIGN KEY FK_22D33462F142426F');
        $this->addSql('ALTER TABLE ebi_question DROP FOREIGN KEY FK_9050C5B4CB90598E');
        $this->addSql('ALTER TABLE org_question DROP FOREIGN KEY FK_CA58D9ADCB90598E');
        $this->addSql('ALTER TABLE question_type_lang DROP FOREIGN KEY FK_EFD305F1CB90598E');
        $this->addSql('DROP TABLE datablock_questions');
        $this->addSql('DROP TABLE ebi_question');
        $this->addSql('DROP TABLE ebi_question_options');
        $this->addSql('DROP TABLE ebi_questions_lang');
        $this->addSql('DROP TABLE org_permissionset_question');
        $this->addSql('DROP TABLE org_question');
        $this->addSql('DROP TABLE org_question_options');
        $this->addSql('DROP TABLE question_category');
        $this->addSql('DROP TABLE question_category_lang');
        $this->addSql('DROP TABLE question_type');
        $this->addSql('DROP TABLE question_type_lang');
       
    }
}
