<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141105065017 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE contact_types ADD parent_contact_types_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contact_types ADD CONSTRAINT FK_741A993FA00BDDF3 FOREIGN KEY (parent_contact_types_id) REFERENCES contact_types (id)');
        $this->addSql('CREATE INDEX IDX_741A993FA00BDDF3 ON contact_types (parent_contact_types_id)');
        $this->addSql('ALTER TABLE contact_types_lang DROP heading');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE contact_types DROP FOREIGN KEY FK_741A993FA00BDDF3');
        $this->addSql('DROP INDEX IDX_741A993FA00BDDF3 ON contact_types');
        $this->addSql('ALTER TABLE contact_types DROP parent_contact_types_id');
        $this->addSql('ALTER TABLE contact_types_lang ADD heading VARCHAR(100) DEFAULT NULL');        
    }
}
