<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Decrease the following field varchar size.
 * person_ebi_metadata.metadata_value - varchar(255)
 * person_org_metadata.metadata_value - varchar(1024).
 */
class Version20160302153300 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE person_ebi_metadata MODIFY COLUMN metadata_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL;');
        $this->addSql('ALTER TABLE person_org_metadata MODIFY COLUMN metadata_value varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
