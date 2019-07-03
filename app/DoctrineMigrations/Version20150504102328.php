<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150504102328 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

       
        $this->addSql('ALTER TABLE risk_model_levels ADD CONSTRAINT FK_A07B4CECEB88056C FOREIGN KEY (risk_level) REFERENCES risk_levels (id)');
        $this->addSql('CREATE INDEX IDX_A07B4CECEB88056C ON risk_model_levels (risk_level)');
        
       
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    
        $this->addSql('ALTER TABLE risk_model_levels DROP FOREIGN KEY FK_A07B4CECEB88056C');
        $this->addSql('DROP INDEX IDX_A07B4CECEB88056C ON risk_model_levels');
        
        
    }
}
