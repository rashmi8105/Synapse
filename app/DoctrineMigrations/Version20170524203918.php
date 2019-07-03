<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-14984
 * Adding Missing Pieces for Referral Notification Refactor
 * Each Connected Alert Notification Referral Part stores the related text
 * This allows us to not have to build notifications on the fly
 * This also allows Bulk Actions to work properly
 */
class Version20170524203918 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE `alert_notification_referral` ADD notification_body_text VARCHAR(300) DEFAULT NULL');
        $this->addSql('ALTER TABLE `alert_notification_referral` ADD notification_hover_text VARCHAR(300) DEFAULT NULL');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
