<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140909132358 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE organization CHANGE irb_confidentiality_statement custom_confidentiality_statement VARCHAR(5000) DEFAULT NULL');
        $this->addSql('ALTER TABLE org_permissionset CHANGE is_archived is_archived TINYINT(1) DEFAULT NULL');
       
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE org_permissionset CHANGE is_archived is_archived LONGBLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE organization CHANGE custom_confidentiality_statement irb_confidentiality_statement VARCHAR(5000) DEFAULT NULL');
       
    }
}
