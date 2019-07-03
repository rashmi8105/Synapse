<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150701100911 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP INDEX IDX_block_type ON org_permissionset_datablock');
        $this->addSql('DROP INDEX IDX_is_viewed ON alert_notifications');
        
        $this->addSql("ALTER TABLE `org_permissionset_datablock` ADD INDEX `block_type_idx` (`block_type` ASC)");
        $this->addSql("ALTER TABLE `alert_notifications` ADD INDEX `is_viewed_idx` (`is_viewed` ASC)") ;
    
    }
    
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    
        $this->addSql('DROP INDEX block_type_idx ON org_permissionset_datablock');
        $this->addSql('DROP INDEX is_viewed_idx ON alert_notifications');
    }
}
