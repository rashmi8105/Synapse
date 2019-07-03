<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds a column to the survey table for use in the Executive Summary Report, and populates it for the 4 existing surveys.
 * This column will allow us to restrict which surveys are used in combination with the PersistMidYear variable,
 * so we're not reporting on surveys that come after the students have already persisted to mid-year.
 */
class Version20160211154809 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('alter table survey add column included_in_persist_midyear_reporting tinyint(1);');

        $this->addSql('update survey set included_in_persist_midyear_reporting = 1 where id = 11;');
        $this->addSql('update survey set included_in_persist_midyear_reporting = 1 where id = 12;');
        $this->addSql('update survey set included_in_persist_midyear_reporting = 0 where id = 13;');
        $this->addSql('update survey set included_in_persist_midyear_reporting = 0 where id = 14;');
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
