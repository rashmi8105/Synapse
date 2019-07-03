<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 *  This migration script would delete all the predefined refernces from DB.
 */
class Version20160419150700 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // This migration script would delete all the predefined refernces from DB.

        $this->addSql("UPDATE  ebi_search SET query = null  WHERE search_type = 'P'");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
