<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140731191946 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE metadatamasterlang (metadatalangid INT AUTO_INCREMENT NOT NULL, langid INT DEFAULT NULL, metadataid INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, metaname VARCHAR(255) DEFAULT NULL, metadescription VARCHAR(255) DEFAULT NULL, INDEX IDX_6A3FEC2F2271845 (langid), INDEX IDX_6A3FEC2FB0230BA4 (metadataid), PRIMARY KEY(metadatalangid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE metadatamasterlang ADD CONSTRAINT FK_6A3FEC2F2271845 FOREIGN KEY (langid) REFERENCES languagemaster (langid)");
        $this->addSql("ALTER TABLE metadatamasterlang ADD CONSTRAINT FK_6A3FEC2FB0230BA4 FOREIGN KEY (metadataid) REFERENCES metadatamaster (metadataid)");
        $this->addSql("ALTER TABLE metadatalistvalues CHANGE metadataid metadataid INT DEFAULT NULL");
        $this->addSql("ALTER TABLE metadatamaster CHANGE `key` meta_key VARCHAR(30) NOT NULL");
        $this->addSql("ALTER TABLE person ADD external_id VARCHAR(255) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE metadatamasterlang");
        $this->addSql("ALTER TABLE metadatalistvalues CHANGE metadataid metadataid INT NOT NULL");
        $this->addSql("ALTER TABLE metadatamaster CHANGE meta_key `key` VARCHAR(30) NOT NULL");
        $this->addSql("ALTER TABLE person DROP external_id");
    }
}
