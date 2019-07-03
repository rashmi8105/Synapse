<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Creates and populates the tables question_bank and question_bank_map.
 * With the existence of these tables, we can gradually move away from having to add records to many system tables for each new survey.
 * It will also make it so that qnbr is replaced by question_bank_id and will eventually be present in most survey-related tables
 * rather than being buried in survey_questions, which will often reduce the number of joins needed.
 */
class Version20160920052243 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql('DROP TABLE IF EXISTS synapse.question_bank_map;');

        $this->addSql('DROP TABLE IF EXISTS synapse.question_bank;');

        $this->addSql('CREATE TABLE synapse.question_bank (
                            id INT NOT NULL AUTO_INCREMENT,
                            intro_text LONGTEXT DEFAULT NULL,
                            text LONGTEXT NOT NULL,
                            question_type VARCHAR(15) NOT NULL,
                            on_success_marker_page TINYINT NOT NULL,
                            created_at DATETIME DEFAULT NULL,
                            modified_at DATETIME DEFAULT NULL,
                            deleted_at DATETIME DEFAULT NULL,
                            created_by INT DEFAULT NULL,
                            modified_by INT DEFAULT NULL,
                            deleted_by INT DEFAULT NULL,
                            PRIMARY KEY (id),
                            CONSTRAINT fk_question_bank_created_by FOREIGN KEY (created_by) REFERENCES synapse.person (id),
                            CONSTRAINT fk_question_bank_modified_by FOREIGN KEY (modified_by) REFERENCES synapse.person (id),
                            CONSTRAINT fk_question_bank_deleted_by FOREIGN KEY (deleted_by) REFERENCES synapse.person (id)
                        );');

        $this->addSql('CREATE TABLE synapse.question_bank_map (
                            id INT NOT NULL AUTO_INCREMENT,
                            survey_id INT NOT NULL,
                            question_bank_id INT NOT NULL,
                            ebi_question_id INT DEFAULT NULL,
                            survey_question_id INT DEFAULT NULL,
                            external_id INT NOT NULL,
                            created_at DATETIME DEFAULT NULL,
                            modified_at DATETIME DEFAULT NULL,
                            deleted_at DATETIME DEFAULT NULL,
                            created_by INT DEFAULT NULL,
                            modified_by INT DEFAULT NULL,
                            deleted_by INT DEFAULT NULL,
                            PRIMARY KEY (id),
                            CONSTRAINT fk_question_bank_map_survey_id FOREIGN KEY (survey_id) REFERENCES synapse.survey (id),
                            CONSTRAINT fk_question_bank_map_question_bank_id FOREIGN KEY (question_bank_id) REFERENCES synapse.question_bank (id),
                            CONSTRAINT fk_question_bank_map_ebi_question_id FOREIGN KEY (ebi_question_id) REFERENCES synapse.ebi_question (id),
                            CONSTRAINT fk_question_bank_map_survey_question_id FOREIGN KEY (survey_question_id) REFERENCES synapse.survey_questions (id),
                            CONSTRAINT fk_question_bank_map_created_by FOREIGN KEY (created_by) REFERENCES synapse.person (id),
                            CONSTRAINT fk_question_bank_map_modified_by FOREIGN KEY (modified_by) REFERENCES synapse.person (id),
                            CONSTRAINT fk_question_bank_map_deleted_by FOREIGN KEY (deleted_by) REFERENCES synapse.person (id),
                            UNIQUE INDEX qbm_unique (survey_id, question_bank_id)
                        );');

        $this->addSql("INSERT INTO question_bank (id, intro_text, text, question_type, on_success_marker_page, created_at, modified_at, created_by, modified_by)
                        SELECT
                            sq.qnbr,
                            eql.question_text,
                            eql.question_rpt,
                            CASE
                                WHEN eq.question_type_id = 'D' THEN 'categorical'
                                WHEN eq.question_type_id = 'Q' THEN 'scaled'
                                WHEN eq.question_type_id = 'NA' THEN 'numeric'
                                WHEN eq.question_type_id = 'SA' THEN 'short answer'
                                WHEN eq.question_type_id = 'LA' THEN 'long answer'
                                WHEN eq.question_type_id = 'MR' THEN 'multi-response'
                            END AS question_type,
                            eq.on_success_marker_page,
                            NOW(),
                            NOW(),
                            -25,
                            -25
                        FROM
                            ebi_question eq
                                INNER JOIN
                            ebi_questions_lang eql ON eql.ebi_question_id = eq.id
                                INNER JOIN
                            survey_questions sq ON sq.ebi_question_id = eq.id
                        GROUP BY qnbr
                        ORDER BY sq.qnbr + 0;");

        // This question currently has different text for different surveys.  I was told this is the preferred text.
        // It's easier to set this directly than to overcomplicate the previous insertion query.
        $this->addSql("UPDATE question_bank
                        SET text = 'Next academic term?'
                        WHERE text = 'Next term?';");

        $this->addSql('INSERT INTO question_bank_map (survey_id, question_bank_id, ebi_question_id, survey_question_id, external_id, created_at, modified_at, created_by, modified_by)
                        SELECT DISTINCT
                            sq.survey_id,
                            sq.qnbr,
                            sq.ebi_question_id,
                            sq.id,
                            eq.external_id,
                            NOW(),
                            NOW(),
                            -25,
                            -25
                        FROM survey_questions sq
                        INNER JOIN ebi_question eq ON eq.id = sq.ebi_question_id
                        WHERE sq.qnbr IS NOT NULL
                            AND sq.deleted_at IS NULL
                            AND eq.deleted_at IS NULL
                        ORDER BY sq.survey_id, sq.qnbr + 0;');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql('DROP TABLE IF EXISTS synapse.question_bank_map;');

        $this->addSql('DROP TABLE IF EXISTS synapse.question_bank;');
    }
}
