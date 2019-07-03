<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150610142420 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_question_response (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT DEFAULT NULL, person_id INT DEFAULT NULL, survey_id INT DEFAULT NULL, org_academic_year_id INT DEFAULT NULL, org_academic_terms_id INT DEFAULT NULL, org_question_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, response_type ENUM(\'decimal\',\'char\',\'charmax\'), decimal_value NUMERIC(9, 2) DEFAULT NULL, char_value VARCHAR(500) DEFAULT NULL, charmax_value VARCHAR(5000) DEFAULT NULL, INDEX IDX_BC4BDC15DE12AB56 (created_by), INDEX IDX_BC4BDC1525F94802 (modified_by), INDEX IDX_BC4BDC151F6FA0AF (deleted_by), INDEX fk_org_question_response_org_question1_idx (org_question_id), INDEX fk_org_question_response_organization1_idx (org_id), INDEX fk_org_question_response_person1_idx (person_id), INDEX fk_org_question_response_survey1_idx (survey_id), INDEX fk_org_question_response_org_academic_year1_idx (org_academic_year_id), INDEX fk_org_question_response_org_academic_terms1_idx (org_academic_terms_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_question_response ADD CONSTRAINT FK_BC4BDC15DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_question_response ADD CONSTRAINT FK_BC4BDC1525F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_question_response ADD CONSTRAINT FK_BC4BDC151F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_question_response ADD CONSTRAINT FK_BC4BDC15F4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_question_response ADD CONSTRAINT FK_BC4BDC15217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_question_response ADD CONSTRAINT FK_BC4BDC15B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE org_question_response ADD CONSTRAINT FK_BC4BDC15F3B0CE4A FOREIGN KEY (org_academic_year_id) REFERENCES org_academic_year (id)');
        $this->addSql('ALTER TABLE org_question_response ADD CONSTRAINT FK_BC4BDC158D7CC0D2 FOREIGN KEY (org_academic_terms_id) REFERENCES org_academic_terms (id)');
        $this->addSql('ALTER TABLE org_question_response ADD CONSTRAINT FK_BC4BDC1582ABAC59 FOREIGN KEY (org_question_id) REFERENCES org_question (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_question_response');
    }
}
