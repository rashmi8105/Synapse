<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150810132818 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE issue_lang (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, issue_id INT NOT NULL, lang_id INT NOT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, name VARCHAR(200) DEFAULT NULL, INDEX IDX_69EE4CAEDE12AB56 (created_by), INDEX IDX_69EE4CAE25F94802 (modified_by), INDEX IDX_69EE4CAE1F6FA0AF (deleted_by), INDEX fk_issue_lang_issue1_idx (issue_id), INDEX fk_issue_lang_language_master1_idx (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE issue_options (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, issue_id INT DEFAULT NULL, ebi_question_options_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_75CDBF66DE12AB56 (created_by), INDEX IDX_75CDBF6625F94802 (modified_by), INDEX IDX_75CDBF661F6FA0AF (deleted_by), INDEX fk_issue_options_issue1_idx (issue_id), INDEX fk_issue_options_ebi_question_options1_idx (ebi_question_options_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE issue_lang ADD CONSTRAINT FK_69EE4CAEDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE issue_lang ADD CONSTRAINT FK_69EE4CAE25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE issue_lang ADD CONSTRAINT FK_69EE4CAE1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE issue_lang ADD CONSTRAINT FK_69EE4CAE5E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id)');
        $this->addSql('ALTER TABLE issue_lang ADD CONSTRAINT FK_69EE4CAEB213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE issue_options ADD CONSTRAINT FK_75CDBF66DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE issue_options ADD CONSTRAINT FK_75CDBF6625F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE issue_options ADD CONSTRAINT FK_75CDBF661F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE issue_options ADD CONSTRAINT FK_75CDBF665E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id)');
        $this->addSql('ALTER TABLE issue_options ADD CONSTRAINT FK_75CDBF667586C8EA FOREIGN KEY (ebi_question_options_id) REFERENCES ebi_question_options (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE issue_lang');
        $this->addSql('DROP TABLE issue_options');
        
    }
}
