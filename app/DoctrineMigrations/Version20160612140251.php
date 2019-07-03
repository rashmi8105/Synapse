<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds a column to the ebi_question table to keep track of which free-response questions
 * are included on the main success marker page on the Student Survey Dashboard.
 */
class Version20160612140251 extends AbstractMigration
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
                    table_name = 'ebi_question'
                    AND table_schema = 'synapse'
                    AND column_name = 'on_success_marker_page';";

        $results = $this->connection->executeQuery($sql)->fetchAll();

        if (empty($results)) {
            $this->addSql("ALTER TABLE ebi_question ADD COLUMN on_success_marker_page TINYINT(1);");
        }

        $this->addSql("UPDATE ebi_question eq
                        INNER JOIN survey_questions sq
                            ON sq.ebi_question_id = eq.id
                        SET eq.on_success_marker_page = 1
                        WHERE eq.on_success_marker_page IS NULL
                            AND sq.qnbr IN (206, 207, 208)
                            AND eq.deleted_at IS NULL
                            AND sq.deleted_at IS NULL;");

        $this->addSql("UPDATE ebi_question
                        SET on_success_marker_page = 0
                        WHERE on_success_marker_page IS NULL
                            AND deleted_at IS NULL;");

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
