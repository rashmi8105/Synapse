<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150810131546 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE issue (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, survey_id INT DEFAULT NULL, survey_questions_id INT DEFAULT NULL, factor_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, min NUMERIC(8, 4) DEFAULT NULL, max NUMERIC(8, 4) DEFAULT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, thumbnail VARCHAR(255) DEFAULT NULL, INDEX IDX_12AD233EDE12AB56 (created_by), INDEX IDX_12AD233E25F94802 (modified_by), INDEX IDX_12AD233E1F6FA0AF (deleted_by), INDEX fk_issue_survey1_idx (survey_id), INDEX fk_issue_survey_questions1_idx (survey_questions_id), INDEX fk_issue_factor1_idx (factor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233EDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233EB3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233ECC63389E FOREIGN KEY (survey_questions_id) REFERENCES survey_questions (id)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233EBC88C1A3 FOREIGN KEY (factor_id) REFERENCES factor (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE issue');
    }
}
