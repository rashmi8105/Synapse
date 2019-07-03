<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150213082352 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
		$this->addSql('UPDATE `person` SET `intent_to_leave` = NULL');		
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176CB20FE3A FOREIGN KEY (intent_to_leave) REFERENCES intent_to_leave (id)');
        $this->addSql('CREATE INDEX IDX_34DCD176CB20FE3A ON person (intent_to_leave)');        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');    
		$this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176CB20FE3A');		
        
    }
}