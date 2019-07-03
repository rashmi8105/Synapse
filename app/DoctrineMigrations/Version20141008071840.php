<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141008071840 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        
        $this->addSql('ALTER TABLE activity_reference_unassigned ADD is_primary_coordinator TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE person ADD welcome_email_sent_date DATE DEFAULT NULL');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        
        $this->addSql('ALTER TABLE activity_reference_unassigned DROP is_primary_coordinator');
        $this->addSql('ALTER TABLE person DROP welcome_email_sent_date');
        
    }
}
