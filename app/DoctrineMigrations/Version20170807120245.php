<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script for removing unique constraint on org_courses.
 * ESPRJ-15503
 */
class Version20170807120245 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        $this->addSql("ALTER TABLE org_courses DROP INDEX `uniquecoursesectionid`");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
