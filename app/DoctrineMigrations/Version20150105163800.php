<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150105163800 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('RENAME TABLE AlertNotifications to alert_notifications');

        $this->addSql('ALTER TABLE person_contact_info ADD created_by INT DEFAULT NULL, ADD modified_by INT DEFAULT NULL, ADD deleted_by INT DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD modified_at DATETIME DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL, CHANGE id personcontactinfoid INT AUTO_INCREMENT NOT NULL');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('RENAME TABLE alert_notifications to AlertNotifications');

        $this->addSql('ALTER TABLE person_contact_info DROP created_by, DROP modified_by, DROP deleted_by, DROP created_at, DROP modified_at, DROP deleted_at, CHANGE personcontactinfoid id INT AUTO_INCREMENT NOT NULL');

    }
}
