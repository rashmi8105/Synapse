<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150618101525 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

       
        $this->addSql('ALTER TABLE org_person_faculty ADD maf_to_pcs_is_active enum(\'y\',\'n\'), ADD pcs_to_maf_is_active enum(\'y\',\'n\'), ADD google_client_id VARCHAR(50) DEFAULT NULL');
        
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE org_person_faculty DROP maf_to_pcs_is_active, DROP pcs_to_maf_is_active, DROP google_client_id');       
        
    }
}
