<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150514142302 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE alert_notifications ADD org_announcements_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE alert_notifications ADD CONSTRAINT FK_D012795C52CCF843 FOREIGN KEY (org_announcements_id) REFERENCES org_announcements (id)');
        $this->addSql('CREATE INDEX IDX_D012795C52CCF843 ON alert_notifications (org_announcements_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE alert_notifications DROP FOREIGN KEY FK_D012795C52CCF843');
    }
}
