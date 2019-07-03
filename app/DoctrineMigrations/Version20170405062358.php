<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * org_cronofy_history table
 */
class Version20170405062358 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_cronofy_history (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, person_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, reason VARCHAR(100) NOT NULL, cronofy_profile_name VARCHAR(100) DEFAULT NULL, cronofy_provider_name VARCHAR(100) DEFAULT NULL, INDEX IDX_EEC47B5ADE12AB56 (created_by), INDEX IDX_EEC47B5A25F94802 (modified_by), INDEX IDX_EEC47B5A1F6FA0AF (deleted_by), INDEX IDX_EEC47B5A32C8A3DE (organization_id), INDEX IDX_EEC47B5A217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('ALTER TABLE org_cronofy_history ADD CONSTRAINT FK_EEC47B5ADE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_cronofy_history ADD CONSTRAINT FK_EEC47B5A25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_cronofy_history ADD CONSTRAINT FK_EEC47B5A1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_cronofy_history ADD CONSTRAINT FK_EEC47B5A32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_cronofy_history ADD CONSTRAINT FK_EEC47B5A217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_cronofy_history');
    }
}
