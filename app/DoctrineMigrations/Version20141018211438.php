<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141018211438 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE person_metadata ADD created_by INT DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD modified_by INT DEFAULT NULL, ADD modified_at DATETIME DEFAULT NULL, ADD deleted_by INT DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL, ADD value VARCHAR(200) DEFAULT NULL, CHANGE Person_id person_id INT DEFAULT NULL, CHANGE Metadata_id metadata_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE person_metadata ADD CONSTRAINT FK_DB529123DC9EE959 FOREIGN KEY (metadata_id) REFERENCES metadata_master (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE person_metadata ADD CONSTRAINT FK_DB5291235A02668E FOREIGN KEY (Metadata_id) REFERENCES metadata_list_values (id)');
        $this->addSql('ALTER TABLE person_metadata ADD PRIMARY KEY (Person_id, Metadata_id)');
    }
}
