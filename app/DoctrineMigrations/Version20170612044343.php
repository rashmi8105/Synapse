<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15181 - migration script for creating logs for push notification
 */
class Version20170612044343 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            "CREATE TABLE push_notification_log (
                    id INT AUTO_INCREMENT NOT NULL,
                    organization_id INT DEFAULT NULL,
                    person_id INT DEFAULT NULL,
                    channel_name VARCHAR(100) DEFAULT NULL,
                    event_key enum('channel_created', 'push_notification_to_channels', 'channel_deleted'),
                    data_posted_to_push_server LONGTEXT DEFAULT NULL,
                    response_from_push_server LONGTEXT DEFAULT NULL,
                    created_by INT DEFAULT NULL,
                    modified_by INT DEFAULT NULL,
                    deleted_by INT DEFAULT NULL,
                    created_at DATETIME DEFAULT NULL,
                    modified_at DATETIME DEFAULT NULL,
                    deleted_at DATETIME DEFAULT NULL,
                    INDEX IDX_C87CAC51DE12AB56 (created_by),
                    INDEX IDX_C87CAC5125F94802 (modified_by),
                    INDEX IDX_C87CAC511F6FA0AF (deleted_by),
                    INDEX IDX_C87CAC5132C8A3DE (organization_id),
                    INDEX IDX_C87CAC51217BBB47 (person_id),
                    PRIMARY KEY (id)
            )  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB");

        $this->addSql('ALTER TABLE push_notification_log ADD CONSTRAINT FK_C87CAC51DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE push_notification_log ADD CONSTRAINT FK_C87CAC5125F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE push_notification_log ADD CONSTRAINT FK_C87CAC511F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE push_notification_log ADD CONSTRAINT FK_C87CAC5132C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE push_notification_log ADD CONSTRAINT FK_C87CAC51217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
