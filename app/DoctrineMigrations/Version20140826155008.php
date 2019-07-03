<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140826155008 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE metadatamasterlang DROP FOREIGN KEY FK_6A3FEC2F2271845");
        $this->addSql("ALTER TABLE person_metadata DROP FOREIGN KEY FK_DB5291235A02668E");
        $this->addSql("ALTER TABLE metadatalistvalues DROP FOREIGN KEY FK_7E26BE74B0230BA4");
        $this->addSql("ALTER TABLE metadatamasterlang DROP FOREIGN KEY FK_6A3FEC2FB0230BA4");
        $this->addSql("ALTER TABLE person_metadata DROP FOREIGN KEY FK_DB529123A38A39E4");
        $this->addSql("ALTER TABLE rolelang DROP FOREIGN KEY FK_C80019EA2271845");
        $this->addSql("CREATE TABLE language_master (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, langcode VARCHAR(10) DEFAULT NULL, langdescription VARCHAR(45) DEFAULT NULL, issystemdefault TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE metadata_list_values (id INT AUTO_INCREMENT NOT NULL, metadata_id INT DEFAULT NULL, lang_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, list_name VARCHAR(255) DEFAULT NULL, list_value VARCHAR(255) DEFAULT NULL, sequence INT DEFAULT NULL, INDEX IDX_45093F6FDC9EE959 (metadata_id), INDEX IDX_45093F6FB213FA4 (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE metadata_master (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, entity_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, meta_key VARCHAR(30) DEFAULT NULL, definition_type VARCHAR(1) DEFAULT NULL, metadata_type VARCHAR(1) DEFAULT NULL, no_of_decimals INT DEFAULT NULL, is_required TINYINT(1) DEFAULT NULL, min_range INT DEFAULT NULL, max_range INT DEFAULT NULL, sequence INT DEFAULT NULL, INDEX IDX_8E57C01A32C8A3DE (organization_id), INDEX IDX_8E57C01A81257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE metadata_master_lang (id INT AUTO_INCREMENT NOT NULL, metadata_id INT DEFAULT NULL, lang_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, meta_name VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, INDEX IDX_FCC218ACDC9EE959 (metadata_id), INDEX IDX_FCC218ACB213FA4 (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE metadata_list_values ADD CONSTRAINT FK_45093F6FDC9EE959 FOREIGN KEY (metadata_id) REFERENCES metadata_master (id)");
        $this->addSql("ALTER TABLE metadata_list_values ADD CONSTRAINT FK_45093F6FB213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)");
        $this->addSql("ALTER TABLE metadata_master ADD CONSTRAINT FK_8E57C01A32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)");
        $this->addSql("ALTER TABLE metadata_master ADD CONSTRAINT FK_8E57C01A81257D5D FOREIGN KEY (entity_id) REFERENCES entity (id)");
        $this->addSql("ALTER TABLE metadata_master_lang ADD CONSTRAINT FK_FCC218ACDC9EE959 FOREIGN KEY (metadata_id) REFERENCES metadata_master (id)");
        $this->addSql("ALTER TABLE metadata_master_lang ADD CONSTRAINT FK_FCC218ACB213FA4 FOREIGN KEY (lang_id) REFERENCES language_master (id)");
        $this->addSql("DROP TABLE languagemaster");
        $this->addSql("DROP TABLE metadatalistvalues");
        $this->addSql("DROP TABLE metadatamaster");
        $this->addSql("DROP TABLE metadatamasterlang");
        $this->addSql("ALTER TABLE person_metadata ADD CONSTRAINT FK_DB5291235A02668E FOREIGN KEY (Metadata_id) REFERENCES metadata_list_values (id)");
        $this->addSql("ALTER TABLE rolelang ADD CONSTRAINT FK_C80019EA2271845 FOREIGN KEY (langid) REFERENCES language_master (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE metadata_list_values DROP FOREIGN KEY FK_45093F6FB213FA4");
        $this->addSql("ALTER TABLE metadata_master_lang DROP FOREIGN KEY FK_FCC218ACB213FA4");
        $this->addSql("ALTER TABLE rolelang DROP FOREIGN KEY FK_C80019EA2271845");
        $this->addSql("ALTER TABLE person_metadata DROP FOREIGN KEY FK_DB5291235A02668E");
        $this->addSql("ALTER TABLE metadata_list_values DROP FOREIGN KEY FK_45093F6FDC9EE959");
        $this->addSql("ALTER TABLE metadata_master_lang DROP FOREIGN KEY FK_FCC218ACDC9EE959");
        $this->addSql("CREATE TABLE languagemaster (langid INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, langcode VARCHAR(10) DEFAULT NULL, langdescription VARCHAR(45) DEFAULT NULL, issystemdefault TINYINT(1) DEFAULT NULL, PRIMARY KEY(langid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE metadatalistvalues (metadatalistid INT AUTO_INCREMENT NOT NULL, metadataid INT DEFAULT NULL, langid INT NOT NULL, listname VARCHAR(30) NOT NULL, listvalue VARCHAR(30) NOT NULL, sequence INT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_7E26BE74B0230BA4 (metadataid), PRIMARY KEY(metadatalistid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE metadatamaster (metadataid INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, meta_key VARCHAR(30) NOT NULL, definitiontype VARCHAR(1) NOT NULL, metadatatype VARCHAR(1) NOT NULL, noofdecimals INT NOT NULL, isrequired TINYINT(1) NOT NULL, minrange INT NOT NULL, maxrange INT NOT NULL, organizationid INT NOT NULL, sequence INT NOT NULL, PRIMARY KEY(metadataid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE metadatamasterlang (metadatalangid INT AUTO_INCREMENT NOT NULL, metadataid INT DEFAULT NULL, langid INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, metaname VARCHAR(255) DEFAULT NULL, metadescription VARCHAR(255) DEFAULT NULL, INDEX IDX_6A3FEC2F2271845 (langid), INDEX IDX_6A3FEC2FB0230BA4 (metadataid), PRIMARY KEY(metadatalangid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE metadatalistvalues ADD CONSTRAINT FK_7E26BE74B0230BA4 FOREIGN KEY (metadataid) REFERENCES metadatamaster (metadataid)");
        $this->addSql("ALTER TABLE metadatamasterlang ADD CONSTRAINT FK_6A3FEC2FB0230BA4 FOREIGN KEY (metadataid) REFERENCES metadatamaster (metadataid)");
        $this->addSql("ALTER TABLE metadatamasterlang ADD CONSTRAINT FK_6A3FEC2F2271845 FOREIGN KEY (langid) REFERENCES languagemaster (langid)");
        $this->addSql("DROP TABLE language_master");
        $this->addSql("DROP TABLE metadata_list_values");
        $this->addSql("DROP TABLE metadata_master");
        $this->addSql("DROP TABLE metadata_master_lang");
        $this->addSql("ALTER TABLE person_metadata DROP FOREIGN KEY FK_DB5291235A02668E");
        $this->addSql("ALTER TABLE person_metadata ADD CONSTRAINT FK_DB5291235A02668E FOREIGN KEY (Metadata_id) REFERENCES metadatalistvalues (metadatalistid)");
        $this->addSql("ALTER TABLE rolelang DROP FOREIGN KEY FK_C80019EA2271845");
        $this->addSql("ALTER TABLE rolelang ADD CONSTRAINT FK_C80019EA2271845 FOREIGN KEY (langid) REFERENCES languagemaster (langid)");
    }
}
