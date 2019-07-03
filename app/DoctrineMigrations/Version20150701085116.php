<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150701085116 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql("ALTER TABLE `org_permissionset_datablock` ADD INDEX `IDX_block_type` (`block_type` ASC)");
        $this->addSql("ALTER TABLE `alert_notifications` ADD INDEX `IDX_is_viewed` (`is_viewed` ASC)") ;
    
    }
    
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    
        $this->addSql('DROP INDEX IDX_block_type ON org_permissionset_datablock');
        $this->addSql('DROP INDEX IDX_is_viewed ON alert_notifications');
    }
}
