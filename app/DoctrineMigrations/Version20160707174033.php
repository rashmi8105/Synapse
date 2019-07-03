<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * In the org_question (ISQ) table, renames the "surveycohort" column to "cohort" and adds the column "survey_id".
 * Then populates these columns.
 */
class Version20160707174033 extends AbstractMigration
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
                    TABLE_NAME = 'org_question'
                    AND TABLE_SCHEMA = 'synapse'
                    AND COLUMN_NAME = 'surveycohort';";

        $results = $this->connection->executeQuery($sql)->fetchAll();


        if (!empty($results)) {
            $this->addSql("ALTER TABLE org_question DROP COLUMN surveycohort;");
        }


        $sql = "SELECT 1
                FROM
                    INFORMATION_SCHEMA.COLUMNS
                WHERE
                    TABLE_NAME = 'org_question'
                    AND TABLE_SCHEMA = 'synapse'
                    AND COLUMN_NAME = 'survey_id';";

        $results = $this->connection->executeQuery($sql)->fetchAll();

        if (empty($results)) {
            $this->addSql("ALTER TABLE org_question ADD COLUMN survey_id INT AFTER question_type_id;");
            $this->addSql("ALTER TABLE org_question ADD CONSTRAINT fk_org_question_survey_id FOREIGN KEY (survey_id) REFERENCES synapse.survey (id);");
        }


        $sql = "SELECT 1
                FROM
                    INFORMATION_SCHEMA.COLUMNS
                WHERE
                    TABLE_NAME = 'org_question'
                    AND TABLE_SCHEMA = 'synapse'
                    AND COLUMN_NAME = 'cohort';";

        $results = $this->connection->executeQuery($sql)->fetchAll();

        if (empty($results)) {
            $this->addSql("ALTER TABLE org_question ADD COLUMN cohort INT AFTER survey_id;");
        }


        // Reorder columns.
        $this->addSql("ALTER TABLE org_question CHANGE COLUMN question_text question_text LONGTEXT AFTER cohort;");

        $this->addSql("ALTER TABLE org_question CHANGE COLUMN external_id external_id VARCHAR(45) AFTER question_text;");


        // Populate the columns.
        $this->addSql("SET SQL_SAFE_UPDATES = 0;");

        $this->addSql("UPDATE org_question oq
                        INNER JOIN survey_questions sq
                            ON sq.org_question_id = oq.id
                        SET oq.survey_id = sq.survey_id
                        WHERE oq.survey_id IS NULL;");

        $this->addSql("UPDATE org_question oq
                        INNER JOIN survey_questions sq
                            ON sq.org_question_id = oq.id
                        SET oq.cohort = sq.cohort_code
                        WHERE oq.cohort IS NULL;");

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
