<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150818150438 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_auth_config ADD campus_portal_login_url VARCHAR(255) DEFAULT NULL, ADD campus_portal_logout_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE org_ldap_config CHANGE type type enum(\'AD\', \'LDAP\')');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_auth_config DROP campus_portal_login_url, DROP campus_portal_logout_url');
        $this->addSql('ALTER TABLE org_ldap_config CHANGE type type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
