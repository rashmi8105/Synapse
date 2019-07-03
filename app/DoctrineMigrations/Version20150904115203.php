<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150904115203 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE ebi_metadata CHANGE meta_key meta_key VARCHAR(50) DEFAULT NULL');
        
        $this->addSql('ALTER TABLE org_metadata CHANGE meta_key meta_key VARCHAR(50) DEFAULT NULL');
        

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ebi_metadata CHANGE meta_key meta_key VARCHAR(45) DEFAULT NULL COLLATE utf8_unicode_ci');
        
        $this->addSql('ALTER TABLE org_metadata CHANGE meta_key meta_key VARCHAR(45) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
