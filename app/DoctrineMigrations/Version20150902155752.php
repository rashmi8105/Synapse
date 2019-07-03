<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150902155752 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_ldap_config ADD student_hostname LONGTEXT NOT NULL, CHANGE hostname staff_hostname LONGTEXT DEFAULT NULL, CHANGE staff_user_base_domain staff_user_base_domain LONGTEXT DEFAULT NULL, CHANGE student_initial_user student_initial_user VARCHAR(255) DEFAULT NULL, CHANGE student_initial_password student_initial_password VARCHAR(255) DEFAULT NULL, CHANGE student_user_base_domain student_user_base_domain LONGTEXT DEFAULT NULL, CHANGE student_username_attribute student_username_attribute VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_ldap_config CHANGE staff_hostname hostname VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP student_hostname, CHANGE type type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE student_initial_user student_initial_user VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE student_initial_password student_initial_password VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE student_user_base_domain student_user_base_domain VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE student_username_attribute student_username_attribute VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE staff_user_base_domain staff_user_base_domain VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
