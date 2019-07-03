<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151211185035 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //ADDING INDEX fk_survey_questions_ebi_question1_idx
        $this->addSQL(
        "SET @myquery = safe_index_builder('survey_questions', 'fk_survey_questions_ebi_question1_idx', '(`ebi_question_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');


    }
}
