<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150818145735 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_ldap_config ADD initial_user VARCHAR(255) NOT NULL, ADD initial_password VARCHAR(255) NOT NULL, CHANGE type type enum(\'AD\', \'LDAP\')');
        $this->addSql('ALTER TABLE org_saml_config ADD logout_url VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_ldap_config DROP initial_user, DROP initial_password, CHANGE type type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE org_saml_config DROP logout_url');

    }
}
