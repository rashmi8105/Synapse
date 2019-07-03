<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141028120250 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE org_permissionset_datablock DROP FOREIGN KEY FK_E34ECC40F9AE3580');
        $this->addSql('ALTER TABLE org_permissionset_datablock ADD CONSTRAINT FK_E34ECC40F9AE3580 FOREIGN KEY (datablock_id) REFERENCES datablock_master (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE org_permissionset_datablock DROP FOREIGN KEY FK_E34ECC40F9AE3580');
        $this->addSql('ALTER TABLE org_permissionset_datablock ADD CONSTRAINT FK_E34ECC40F9AE3580 FOREIGN KEY (datablock_id) REFERENCES datablock_ui (id)');
     
    }
}
