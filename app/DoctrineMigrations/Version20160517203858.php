<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Renames a previously null column in the ebi_question_options table and populates it.
 */
class Version20160517203858 extends AbstractMigration
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
                    table_name = 'ebi_question_options'
                    AND table_schema = 'synapse'
                    AND column_name = 'option_rpt';";

        $results = $this->connection->executeQuery($sql)->fetchAll();

        if (!empty($results)) {
            $this->addSql("ALTER TABLE ebi_question_options CHANGE COLUMN option_rpt extended_option_text VARCHAR(150) DEFAULT NULL;");
        }

        $this->addSql("UPDATE ebi_question_options
                        SET extended_option_text = CONCAT(SUBSTRING(option_text, 2, 1), ' on a 7 point scale')
                        WHERE extended_option_text IS NULL
                        AND LEFT(option_text, 1) = '(' AND RIGHT(option_text, 1) = ')';");

        $this->addSql("UPDATE ebi_question_options
                        SET extended_option_text = option_text
                        WHERE extended_option_text IS NULL;");
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
