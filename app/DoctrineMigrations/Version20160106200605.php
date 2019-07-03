<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160106200605 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        // 
        // ADDING INDEXES FOR TALKING POINTS CALCULATION
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL(
        "SET @myquery = safe_index_builder('talking_points', 'tp_type', '(`talking_points_type` ASC, `ebi_metadata_id` ASC, `deleted_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");

        $this->addSQL(
        "SET @myquery = safe_index_builder('org_talking_points', 'source_modified_at', '(`source_modified_at` ASC)', true, false, false);
        PREPARE stmt1 FROM @myquery;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;");


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your need
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');


    }
}
