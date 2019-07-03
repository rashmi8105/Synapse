<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-16169 Removing Profile Block Permissions and lang table references to obsolete Retention
 */
class Version20171012155618 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE synapse.datablock_metadata dm
                                INNER JOIN ebi_metadata_lang eml ON dm.ebi_metadata_id = eml.ebi_metadata_id
                            SET
                                dm.deleted_at = NOW(),
                                dm.deleted_by = -25,
                                eml.deleted_at = NOW(),
                                eml.deleted_by = -25
                            WHERE meta_name in (
                                    'PersistMidYear',
                                    'RetainYear2',
                                    'RetentionTrack',
                                    'RetainYear3',
                                    'Complete1yrsorless',
                                    'Complete2yrsorless',
                                    'Complete3yrsorless',
                                    'Complete4yrsorless',
                                    'Complete5yrsorless',
                                    'Complete6yrsorless');");



    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
