<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160315193409 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        /**
         * This adds in four rows into the upload_file_log table.
         * These rows will track the the number of rows that have
         * been updated, created, deleted and left unchanged
         * within the upload.
         */
        $this->addSql("alter table upload_file_log
                               add column `created_row_count`   int(11) AFTER `valid_row_count`;");
        $this->addSql("alter table upload_file_log
                               add column `updated_row_count`   int(11) AFTER `created_row_count`;");
        $this->addSql("alter table upload_file_log
                               add column `deleted_row_count`   int(11) AFTER `updated_row_count`;");
        $this->addSql("alter table upload_file_log
                               add column `unchanged_row_count` int(11) AFTER `deleted_row_count`;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
