<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Performance Enhancing Migration Script.
 *
 * ESPRJ-15630
 */
class Version20170802161812 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        $this->addSQL(
            "SET @myquery = safe_index_builder('datablock_questions', 'fk_datablock_questions_survey1_idx', '(`survey_id` ASC, `factor_id` ASC, `deleted_at` ASC)', true, false, false);
                PREPARE stmt1 FROM @myquery;
                EXECUTE stmt1;
                DEALLOCATE PREPARE stmt1;"
        );
    }

    public function down(Schema $schema)
    {
    }
}
