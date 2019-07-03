<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160411171822 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        /**
         * This adds a row in upload file log that will count how
         * many rows had an error in it,
         * dropping columns that are no longer used
         */
        $this->addSql("alter table upload_file_log
                               drop column `deleted_row_count`;");
        $this->addSql("alter table upload_file_log
                               drop column `unchanged_row_count`;");
        $this->addSql("alter table upload_file_log
                               add column `error_row_count` int(11) AFTER `updated_row_count`;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
