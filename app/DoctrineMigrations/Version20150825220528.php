<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150825220528 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_ldap_config CHANGE initial_user staff_initial_user VARCHAR(255) DEFAULT NULL, CHANGE initial_password staff_initial_password VARCHAR(255) DEFAULT NULL, CHANGE user_base_domain staff_user_base_domain VARCHAR(255) DEFAULT NULL, CHANGE username_attribute staff_username_attribute VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE org_ldap_config ADD student_initial_user VARCHAR(255) NOT NULL, ADD student_initial_password VARCHAR(255) NOT NULL, ADD student_user_base_domain VARCHAR(255) NOT NULL, ADD student_username_attribute VARCHAR(255) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_ldap_config ADD user_base_domain VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD username_attribute VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD initial_user VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD initial_password VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP student_initial_user, DROP student_initial_password, DROP student_user_base_domain, DROP student_username_attribute, DROP staff_initial_user, DROP staff_initial_password, DROP staff_user_base_domain, DROP staff_username_attribute, CHANGE type type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
