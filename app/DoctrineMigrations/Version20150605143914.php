<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150605143914 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_auth_config (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT NOT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, campus_portal_student_enabled TINYINT(1) DEFAULT NULL, campus_portal_student_key VARCHAR(64) DEFAULT NULL, campus_portal_staff_enabled TINYINT(1) DEFAULT NULL, campus_portal_staff_key VARCHAR(64) DEFAULT NULL, ldap_student_enabled TINYINT(1) DEFAULT NULL, ldap_staff_enabled TINYINT(1) DEFAULT NULL, saml_student_enabled TINYINT(1) DEFAULT NULL, saml_staff_enabled TINYINT(1) DEFAULT NULL, INDEX IDX_68A5D278DE12AB56 (created_by), INDEX IDX_68A5D27825F94802 (modified_by), INDEX IDX_68A5D2781F6FA0AF (deleted_by), INDEX fk_org_auth_config_organization1_idx (org_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE org_ldap_config (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT NOT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, type enum(\'AD\', \'LDAP\'), hostname VARCHAR(255) NOT NULL, dn VARCHAR(255) NOT NULL, user_base_domain VARCHAR(255) NOT NULL, username_attribute VARCHAR(255) NOT NULL, INDEX IDX_22545809DE12AB56 (created_by), INDEX IDX_2254580925F94802 (modified_by), INDEX IDX_225458091F6FA0AF (deleted_by), INDEX fk_org_ldap_config_organization1_idx (org_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_auth_config ADD CONSTRAINT FK_68A5D278DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_auth_config ADD CONSTRAINT FK_68A5D27825F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_auth_config ADD CONSTRAINT FK_68A5D2781F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_auth_config ADD CONSTRAINT FK_68A5D278F4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_ldap_config ADD CONSTRAINT FK_22545809DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_ldap_config ADD CONSTRAINT FK_2254580925F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_ldap_config ADD CONSTRAINT FK_225458091F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_ldap_config ADD CONSTRAINT FK_22545809F4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_auth_config');
        $this->addSql('DROP TABLE org_ldap_config');
    }
}
