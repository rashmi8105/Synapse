<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140729224036 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(255) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE contactinfo (contactid INT AUTO_INCREMENT NOT NULL, address1 VARCHAR(100) DEFAULT NULL, address2 VARCHAR(100) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, zip VARCHAR(20) DEFAULT NULL, state VARCHAR(100) DEFAULT NULL, country VARCHAR(100) DEFAULT NULL, primarymobile VARCHAR(15) DEFAULT NULL, alternatemobile VARCHAR(15) DEFAULT NULL, homephone VARCHAR(15) DEFAULT NULL, officephone VARCHAR(15) DEFAULT NULL, primaryemail VARCHAR(100) DEFAULT NULL, alternateemail VARCHAR(100) DEFAULT NULL, PRIMARY KEY(contactid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE organization (organizationid INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, subdomain VARCHAR(45) NOT NULL, parentorganizationid INT NOT NULL, status VARCHAR(1) NOT NULL, timezone VARCHAR(45) NOT NULL, website VARCHAR(100) NOT NULL, PRIMARY KEY(organizationid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE languagemaster (langid INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, langcode VARCHAR(10) DEFAULT NULL, langdescription VARCHAR(45) DEFAULT NULL, issystemdefault TINYINT(1) DEFAULT NULL, PRIMARY KEY(langid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE metadatalistvalues (metadatalistid INT AUTO_INCREMENT NOT NULL, metadataid INT NOT NULL, langid INT NOT NULL, listname VARCHAR(30) NOT NULL, listvalue VARCHAR(30) NOT NULL, sequence INT NOT NULL, INDEX IDX_7E26BE74B0230BA4 (metadataid), PRIMARY KEY(metadatalistid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE metadatamaster (metadataid INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, `key` VARCHAR(30) NOT NULL, definitiontype VARCHAR(1) NOT NULL, metadatatype VARCHAR(1) NOT NULL, noofdecimals INT NOT NULL, isrequired TINYINT(1) NOT NULL, minrange INT NOT NULL, maxrange INT NOT NULL, organizationid INT NOT NULL, PRIMARY KEY(metadataid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE organizationrole (orgroleid INT AUTO_INCREMENT NOT NULL, personid INT DEFAULT NULL, organizationid INT DEFAULT NULL, roleid INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_108FCC7337886FBE (personid), INDEX IDX_108FCC73E808A0A6 (organizationid), INDEX IDX_108FCC732D46D92A (roleid), PRIMARY KEY(orgroleid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE organizationlang (orglangid INT AUTO_INCREMENT NOT NULL, organizationid INT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, langid INT NOT NULL, organizationname VARCHAR(255) NOT NULL, nickname VARCHAR(45) NOT NULL, INDEX IDX_76EFC27BE808A0A6 (organizationid), PRIMARY KEY(orglangid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE person (personid INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, firstname VARCHAR(45) DEFAULT NULL, lastname VARCHAR(45) DEFAULT NULL, title VARCHAR(45) DEFAULT NULL, PRIMARY KEY(personid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE personcontactinfo (personcontactinfoid INT AUTO_INCREMENT NOT NULL, contactid INT DEFAULT NULL, personid INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, status VARCHAR(1) DEFAULT NULL, INDEX IDX_2E28DAF8E1CFD084 (contactid), INDEX IDX_2E28DAF837886FBE (personid), PRIMARY KEY(personcontactinfoid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE role (roleid INT AUTO_INCREMENT NOT NULL, status VARCHAR(1) DEFAULT NULL, PRIMARY KEY(roleid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE rolelang (rolelangid INT AUTO_INCREMENT NOT NULL, langid INT DEFAULT NULL, roleid INT DEFAULT NULL, rolename VARCHAR(45) DEFAULT NULL, INDEX IDX_C80019EA2271845 (langid), INDEX IDX_C80019EA2D46D92A (roleid), PRIMARY KEY(rolelangid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE metadatalistvalues ADD CONSTRAINT FK_7E26BE74B0230BA4 FOREIGN KEY (metadataid) REFERENCES metadatamaster (metadataid)");
        $this->addSql("ALTER TABLE organizationrole ADD CONSTRAINT FK_108FCC7337886FBE FOREIGN KEY (personid) REFERENCES person (personid)");
        $this->addSql("ALTER TABLE organizationrole ADD CONSTRAINT FK_108FCC73E808A0A6 FOREIGN KEY (organizationid) REFERENCES organization (organizationid)");
        $this->addSql("ALTER TABLE organizationrole ADD CONSTRAINT FK_108FCC732D46D92A FOREIGN KEY (roleid) REFERENCES role (roleid)");
        $this->addSql("ALTER TABLE organizationlang ADD CONSTRAINT FK_76EFC27BE808A0A6 FOREIGN KEY (organizationid) REFERENCES organization (organizationid)");
        $this->addSql("ALTER TABLE personcontactinfo ADD CONSTRAINT FK_2E28DAF8E1CFD084 FOREIGN KEY (contactid) REFERENCES contactinfo (contactid)");
        $this->addSql("ALTER TABLE personcontactinfo ADD CONSTRAINT FK_2E28DAF837886FBE FOREIGN KEY (personid) REFERENCES person (personid)");
        $this->addSql("ALTER TABLE rolelang ADD CONSTRAINT FK_C80019EA2271845 FOREIGN KEY (langid) REFERENCES languagemaster (langid)");
        $this->addSql("ALTER TABLE rolelang ADD CONSTRAINT FK_C80019EA2D46D92A FOREIGN KEY (roleid) REFERENCES role (roleid)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE personcontactinfo DROP FOREIGN KEY FK_2E28DAF8E1CFD084");
        $this->addSql("ALTER TABLE organizationrole DROP FOREIGN KEY FK_108FCC73E808A0A6");
        $this->addSql("ALTER TABLE organizationlang DROP FOREIGN KEY FK_76EFC27BE808A0A6");
        $this->addSql("ALTER TABLE rolelang DROP FOREIGN KEY FK_C80019EA2271845");
        $this->addSql("ALTER TABLE metadatalistvalues DROP FOREIGN KEY FK_7E26BE74B0230BA4");
        $this->addSql("ALTER TABLE organizationrole DROP FOREIGN KEY FK_108FCC7337886FBE");
        $this->addSql("ALTER TABLE personcontactinfo DROP FOREIGN KEY FK_2E28DAF837886FBE");
        $this->addSql("ALTER TABLE organizationrole DROP FOREIGN KEY FK_108FCC732D46D92A");
        $this->addSql("ALTER TABLE rolelang DROP FOREIGN KEY FK_C80019EA2D46D92A");
        $this->addSql("DROP TABLE ext_translations");
        $this->addSql("DROP TABLE ext_log_entries");
        $this->addSql("DROP TABLE contactinfo");
        $this->addSql("DROP TABLE organization");
        $this->addSql("DROP TABLE languagemaster");
        $this->addSql("DROP TABLE metadatalistvalues");
        $this->addSql("DROP TABLE metadatamaster");
        $this->addSql("DROP TABLE organizationrole");
        $this->addSql("DROP TABLE organizationlang");
        $this->addSql("DROP TABLE person");
        $this->addSql("DROP TABLE personcontactinfo");
        $this->addSql("DROP TABLE role");
        $this->addSql("DROP TABLE rolelang");
    }
}
