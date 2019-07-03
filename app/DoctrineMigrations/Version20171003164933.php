<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-16063 - Removing Obsolete completion values
 */
class Version20171003164933 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE ebi_metadata 
                       SET 
                          deleted_at = NOW()
                       WHERE
                          meta_key IN ('Complete1yrsorless' , 'Complete2yrsorless', 'Complete3yrsorless', 'Complete4yrsorless', 'Complete5yrsorless', 'Complete6yrsorless')");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
