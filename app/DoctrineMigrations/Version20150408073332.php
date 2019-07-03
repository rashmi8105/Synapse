<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150408073332 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE alert_notifications ADD academic_update_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE alert_notifications ADD CONSTRAINT FK_D012795C567A5FFE FOREIGN KEY (academic_update_id) REFERENCES academic_update (id)');
        $this->addSql('CREATE INDEX fk_alert_notifications_academic_update1_idx ON alert_notifications (academic_update_id)');
     }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE alert_notifications DROP FOREIGN KEY FK_D012795C567A5FFE');
        $this->addSql('DROP INDEX fk_alert_notifications_academic_update1_idx ON alert_notifications');
        $this->addSql('ALTER TABLE alert_notifications DROP academic_update_id');
    }
}
