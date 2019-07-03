<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150406152225 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_documents (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, org_id INT NOT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, title VARCHAR(80) DEFAULT NULL, description VARCHAR(140) DEFAULT NULL, type enum(\'link\', \'file\'), link VARCHAR(400) DEFAULT NULL, file_path VARCHAR(200) DEFAULT NULL, display_filename VARCHAR(200) DEFAULT NULL, INDEX IDX_9BCF6EDADE12AB56 (created_by), INDEX IDX_9BCF6EDA25F94802 (modified_by), INDEX IDX_9BCF6EDA1F6FA0AF (deleted_by), INDEX fk_org_documents_organization1_idx (org_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_documents ADD CONSTRAINT FK_9BCF6EDADE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_documents ADD CONSTRAINT FK_9BCF6EDA25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_documents ADD CONSTRAINT FK_9BCF6EDA1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_documents ADD CONSTRAINT FK_9BCF6EDAF4837C1B FOREIGN KEY (org_id) REFERENCES organization (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_documents');
    }
}
