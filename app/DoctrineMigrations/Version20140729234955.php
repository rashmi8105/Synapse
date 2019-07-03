<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140729234955 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("CREATE TABLE person_metadata (id INT AUTO_INCREMENT NOT NULL, Person_id INT NOT NULL, Metadata_id INT NOT NULL, INDEX IDX_DB529123A38A39E4 (Person_id), INDEX IDX_DB5291235A02668E (Metadata_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE person_metadata ADD CONSTRAINT FK_DB529123A38A39E4 FOREIGN KEY (Person_id) REFERENCES person (personid)");
        $this->addSql("ALTER TABLE person_metadata ADD CONSTRAINT FK_DB5291235A02668E FOREIGN KEY (Metadata_id) REFERENCES metadatalistvalues (metadatalistid)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("DROP TABLE person_metadata");
    }
}
