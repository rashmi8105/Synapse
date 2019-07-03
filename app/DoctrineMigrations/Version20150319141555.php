<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150319141555 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE alert_notifications ADD org_search_id INT DEFAULT NULL');   
        $this->addSql('ALTER TABLE alert_notifications ADD CONSTRAINT FK_D012795C5C787CFB FOREIGN KEY (org_search_id) REFERENCES org_search (id)');      
        $this->addSql('CREATE INDEX fk_alert_notifications_org_search1_idx ON alert_notifications (org_search_id)');           
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE alert_notifications DROP FOREIGN KEY FK_D012795C5C787CFB');      
        $this->addSql('DROP INDEX fk_alert_notifications_org_search1_idx ON alert_notifications');
        $this->addSql('ALTER TABLE alert_notifications DROP org_search_id');            
    }
}
