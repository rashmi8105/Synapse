<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150403110531 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE survey_lang (survey_id INT NOT NULL, lang_id INT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, name VARCHAR(200) DEFAULT NULL, INDEX IDX_F7E084A9DE12AB56 (created_by), INDEX IDX_F7E084A925F94802 (modified_by), INDEX IDX_F7E084A91F6FA0AF (deleted_by), INDEX fk_survey_lang_survey1_idx (survey_id), INDEX fk_survey_lang_language_master1_idx (lang_id), PRIMARY KEY(survey_id, lang_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE survey_lang ADD CONSTRAINT FK_F7E084A9DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_lang ADD CONSTRAINT FK_F7E084A925F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_lang ADD CONSTRAINT FK_F7E084A91F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE survey_lang ADD CONSTRAINT FK_F7E084A9B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE survey_lang ADD CONSTRAINT FK_F7E084A9B213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE survey_lang');
    }
}
