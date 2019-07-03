<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160620182127 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE upload_file_log ADD COLUMN uploaded_file_hash VARCHAR(32) AFTER uploaded_file_path;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE upload_file_log DROP COLUMN uploaded_file_hash;");

    }
}
