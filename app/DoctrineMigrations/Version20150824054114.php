<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150824054114 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_person_faculty CHANGE maf_to_pcs_is_active maf_to_pcs_is_active enum(\'y\',\'n\',\'i\'), CHANGE pcs_to_maf_is_active pcs_to_maf_is_active enum(\'y\',\'n\')');        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_person_faculty CHANGE maf_to_pcs_is_active maf_to_pcs_is_active VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE pcs_to_maf_is_active pcs_to_maf_is_active VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');        
    }
}
