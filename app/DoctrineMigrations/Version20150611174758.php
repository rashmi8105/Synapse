<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150611174758 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_saml_config (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT NOT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, entity_id VARCHAR(255) NOT NULL, federation_metadata VARCHAR(255) NOT NULL, sso_url VARCHAR(255) DEFAULT NULL, public_key_file VARCHAR(255) DEFAULT NULL, INDEX IDX_34537430DE12AB56 (created_by), INDEX IDX_3453743025F94802 (modified_by), INDEX IDX_345374301F6FA0AF (deleted_by), INDEX fk_org_saml_config_organization1_idx (org_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE saml_sso_state (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_saml_config ADD CONSTRAINT FK_34537430DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_saml_config ADD CONSTRAINT FK_3453743025F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_saml_config ADD CONSTRAINT FK_345374301F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_saml_config ADD CONSTRAINT FK_34537430F4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_ldap_config CHANGE type type enum(\'AD\', \'LDAP\')');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_saml_config');
        $this->addSql('DROP TABLE saml_sso_state');

    }
}
