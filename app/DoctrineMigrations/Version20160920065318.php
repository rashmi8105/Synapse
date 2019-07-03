<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * This migration script creates new table alert_notification_referral
 */
class Version20160920065318 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSQL('DROP TABLE IF EXISTS alert_notification_referral;');

        $this->addSql('CREATE TABLE alert_notification_referral (
            alert_notification_id INT NOT NULL,
            referral_history_id INT NOT NULL,
            created_at DATETIME DEFAULT NULL,
            modified_at DATETIME DEFAULT NULL,
            deleted_at DATETIME DEFAULT NULL,
            created_by INT DEFAULT NULL,
            modified_by INT DEFAULT NULL,
            deleted_by INT DEFAULT NULL,
            PRIMARY KEY(alert_notification_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql("ALTER TABLE alert_notification_referral
            ADD CONSTRAINT fk_alert_notification_referral_alert_notification_id FOREIGN KEY (alert_notification_id) REFERENCES alert_notifications (id),
            ADD CONSTRAINT fk_alert_notification_referral_referral_history_id FOREIGN KEY (referral_history_id) REFERENCES referral_history (id),
            ADD CONSTRAINT fk_alert_notification_referral_created_by FOREIGN KEY (created_by) REFERENCES person (id),
            ADD CONSTRAINT fk_alert_notification_referral_modified_by FOREIGN KEY (modified_by) REFERENCES person (id),
            ADD CONSTRAINT fk_alert_notification_referral_deleted_by FOREIGN KEY (deleted_by) REFERENCES person (id);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE alert_notification_referral');

    }
}
