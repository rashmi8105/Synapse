<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15754 - Disable all academic update predefined searches
 */
class Version20170828163311 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            UPDATE
                synapse.ebi_search
            SET
                is_enabled = 0,
                modified_by = -25,
                modified_at = NOW()
            WHERE
                category = 'academic_update_search'
                    AND deleted_at IS NULL;
        ");

    }

    public function down(Schema $schema)
    {
        $this->addSql("
            UPDATE
                synapse.ebi_search
            SET
                is_enabled = 1,
                modified_by = -25,
                modified_at = NOW()
            WHERE
                category = 'academic_update_search'
                    AND deleted_at IS NULL;
        ");

    }
}
