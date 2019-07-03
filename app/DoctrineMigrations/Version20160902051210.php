<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Makes ebi_search_history truly a history table by removing the existing primary key on person_id and ebi_search_id
 * and adding an id column as the primary key.
 *
 * Going forward, if a faculty member runs the same predefined search multiple times, rather than updating the last_run column,
 * the last such record will be soft-deleted and a new one will be inserted.  We'll start using the created_at column rather than last_run.
 */
class Version20160902051210 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql("ALTER TABLE ebi_search_history DROP PRIMARY KEY;
                      ALTER TABLE ebi_search_history ADD COLUMN id INT PRIMARY KEY AUTO_INCREMENT FIRST;");

        $this->addSql("UPDATE ebi_search_history SET created_at = last_run WHERE created_at IS NULL;");

        $this->addSql("UPDATE ebi_search_history SET modified_at = last_run WHERE modified_at IS NULL;");
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
