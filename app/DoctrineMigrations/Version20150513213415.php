<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150513213415 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE org_conflict ADD owning_org_tier_code enum(\'0\',\'3\'), CHANGE record_type record_type enum(\'master\', \'home\',\'other\'), CHANGE status status enum(\'conflict\', \'merged\')');
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_conflict DROP owning_org_tier_code, CHANGE record_type record_type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        
    }
}
