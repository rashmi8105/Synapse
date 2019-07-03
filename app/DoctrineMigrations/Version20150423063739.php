<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150423063739 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE factor_questions DROP FOREIGN KEY FK_2F1D082851DCB924');
        $this->addSql('DROP INDEX fk_factor_questions_ind_question1_idx ON factor_questions');
        $this->addSql('ALTER TABLE factor_questions CHANGE ind_question_id survey_questions_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE factor_questions ADD CONSTRAINT FK_2F1D0828CC63389E FOREIGN KEY (survey_questions_id) REFERENCES survey_questions (id)');
        $this->addSql('CREATE INDEX fk_factor_questions_survey_questions1_idx ON factor_questions (survey_questions_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE factor_questions DROP FOREIGN KEY FK_2F1D0828CC63389E');
        $this->addSql('DROP INDEX fk_factor_questions_survey_questions1_idx ON factor_questions');
        $this->addSql('ALTER TABLE factor_questions CHANGE survey_questions_id ind_question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE factor_questions ADD CONSTRAINT FK_2F1D082851DCB924 FOREIGN KEY (ind_question_id) REFERENCES ind_question (id)');
        $this->addSql('CREATE INDEX fk_factor_questions_ind_question1_idx ON factor_questions (ind_question_id)');
    }
}
