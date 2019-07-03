<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150910063053 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE alert_notifications ADD reports_running_status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE alert_notifications ADD CONSTRAINT FK_D012795C7FDAE991 FOREIGN KEY (reports_running_status_id) REFERENCES reports_running_status (id)');
        $this->addSql('CREATE INDEX fk_alert_notifications_report_running_status1_idx ON alert_notifications (reports_running_status_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE alert_notifications DROP FOREIGN KEY FK_D012795C7FDAE991');
        $this->addSql('DROP INDEX fk_alert_notifications_report_running_status1_idx ON alert_notifications');
        $this->addSql('ALTER TABLE alert_notifications DROP reports_running_status_id');
    }
}
