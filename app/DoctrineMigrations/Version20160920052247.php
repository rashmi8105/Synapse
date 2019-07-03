<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds the column question_bank_id to the tables report_section_elements and datablock_questions.
 * This reduces the number of joins needed.
 */
class Version20160920052247 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $sql = "SELECT 1
                FROM
                    INFORMATION_SCHEMA.COLUMNS
                WHERE
                    TABLE_NAME = 'report_section_elements'
                    AND TABLE_SCHEMA = 'synapse'
                    AND COLUMN_NAME = 'question_bank_id';";

        $results = $this->connection->executeQuery($sql)->fetchAll();

        if (empty($results)) {
            $this->addSql("ALTER TABLE report_section_elements ADD COLUMN question_bank_id INT AFTER factor_id;");
            $this->addSql("ALTER TABLE report_section_elements ADD CONSTRAINT fk_report_section_elements_question_bank_id FOREIGN KEY (question_bank_id) REFERENCES synapse.question_bank (id);");
        }

        $this->addSql('UPDATE report_section_elements rse
                        INNER JOIN question_bank_map qbm ON qbm.ebi_question_id = rse.ebi_question_id
                        SET rse.question_bank_id = qbm.question_bank_id
                        WHERE rse.question_bank_id IS NULL;');

        $this->addSql('UPDATE report_section_elements rse
                        INNER JOIN question_bank_map qbm ON qbm.survey_question_id = rse.survey_question_id
                        SET rse.question_bank_id = qbm.question_bank_id
                        WHERE rse.question_bank_id IS NULL;');


        $sql = "SELECT 1
                FROM
                    INFORMATION_SCHEMA.COLUMNS
                WHERE
                    TABLE_NAME = 'datablock_questions'
                    AND TABLE_SCHEMA = 'synapse'
                    AND COLUMN_NAME = 'question_bank_id';";

        $results = $this->connection->executeQuery($sql)->fetchAll();

        if (empty($results)) {
            $this->addSql("ALTER TABLE datablock_questions ADD COLUMN question_bank_id INT AFTER datablock_id;");
            $this->addSql("ALTER TABLE datablock_questions ADD CONSTRAINT fk_datablock_questions_question_bank_id FOREIGN KEY (question_bank_id) REFERENCES synapse.question_bank (id);");
        }

        $this->addSql('UPDATE datablock_questions dq
                        INNER JOIN question_bank_map qbm ON qbm.ebi_question_id = dq.ebi_question_id
                        SET dq.question_bank_id = qbm.question_bank_id
                        WHERE dq.question_bank_id IS NULL;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');
    }
}
