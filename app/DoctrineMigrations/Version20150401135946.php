<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150401135946 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE datablock_questions DROP FOREIGN KEY FK_BD00028D51DCB924');
        $this->addSql('DROP INDEX fk_datablock_questions_ind_question1_idx ON datablock_questions');
        $this->addSql('ALTER TABLE datablock_questions CHANGE ind_question_id survey_questions_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE datablock_questions ADD CONSTRAINT FK_BD00028DCC63389E FOREIGN KEY (survey_questions_id) REFERENCES survey_questions (id)');
        $this->addSql('CREATE INDEX fk_datablock_questions_survey_questions1_idx ON datablock_questions (survey_questions_id)');
        $this->addSql('ALTER TABLE surveymarker_questions DROP FOREIGN KEY FK_223450D951DCB924');
        $this->addSql('DROP INDEX fk_surveymarker_questions_ind_question1_idx ON surveymarker_questions');
        $this->addSql('ALTER TABLE surveymarker_questions CHANGE ind_question_id survey_questions_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE surveymarker_questions ADD CONSTRAINT FK_223450D9CC63389E FOREIGN KEY (survey_questions_id) REFERENCES survey_questions (id)');
        $this->addSql('CREATE INDEX fk_surveymarker_questions_survey_questions1_idx ON surveymarker_questions (survey_questions_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE datablock_questions DROP FOREIGN KEY FK_BD00028DCC63389E');
        $this->addSql('DROP INDEX fk_datablock_questions_survey_questions1_idx ON datablock_questions');
        $this->addSql('ALTER TABLE datablock_questions CHANGE survey_questions_id ind_question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE datablock_questions ADD CONSTRAINT FK_BD00028D51DCB924 FOREIGN KEY (ind_question_id) REFERENCES ind_question (id)');
        $this->addSql('CREATE INDEX fk_datablock_questions_ind_question1_idx ON datablock_questions (ind_question_id)');
        $this->addSql('ALTER TABLE surveymarker_questions DROP FOREIGN KEY FK_223450D9CC63389E');
        $this->addSql('DROP INDEX fk_surveymarker_questions_survey_questions1_idx ON surveymarker_questions');
        $this->addSql('ALTER TABLE surveymarker_questions CHANGE survey_questions_id ind_question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE surveymarker_questions ADD CONSTRAINT FK_223450D951DCB924 FOREIGN KEY (ind_question_id) REFERENCES ind_question (id)');
        $this->addSql('CREATE INDEX fk_surveymarker_questions_ind_question1_idx ON surveymarker_questions (ind_question_id)');
    }
}
