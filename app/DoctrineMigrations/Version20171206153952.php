<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-16213 Modifications to alert notifications view indicators
 */
class Version20171206153952 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        //Rename is_viewed
        $this->addSql("
            ALTER TABLE synapse.alert_notifications CHANGE COLUMN `is_viewed` `is_read` TINYINT(1) DEFAULT 0;
        ");

        //Add is_seen
        $this->addSql("
            ALTER TABLE synapse.alert_notifications ADD COLUMN `is_seen` TINYINT(1) DEFAULT 0 AFTER `is_read`;
        ");

        //Set the values to the same thing.
        $this->addSql("
            UPDATE
                synapse.alert_notifications
            SET
                is_seen = is_read,
                modified_at = NOW(),
                modified_by = -25
            WHERE 
                is_seen <> is_read;
        ");


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
