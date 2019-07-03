<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140828205229 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE personcontactinfo DROP FOREIGN KEY FK_2E28DAF8E1CFD084");
        $this->addSql("CREATE TABLE contact_info (id INT AUTO_INCREMENT NOT NULL, address_1 VARCHAR(100) DEFAULT NULL, address_2 VARCHAR(100) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, zip VARCHAR(20) DEFAULT NULL, state VARCHAR(100) DEFAULT NULL, country VARCHAR(100) DEFAULT NULL, primary_mobile VARCHAR(15) DEFAULT NULL, alternate_mobile VARCHAR(15) DEFAULT NULL, home_phone VARCHAR(15) DEFAULT NULL, office_phone VARCHAR(15) DEFAULT NULL, primary_email VARCHAR(100) DEFAULT NULL, alternate_email VARCHAR(100) DEFAULT NULL, primary_mobile_provider VARCHAR(45) DEFAULT NULL, alternate_mobile_provider VARCHAR(45) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE feature_master (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, category VARCHAR(45) DEFAULT NULL, feature_name VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE org_features (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, feature_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, private TINYINT(1) DEFAULT NULL, connected TINYINT(1) DEFAULT NULL, team TINYINT(1) DEFAULT NULL, default_access VARCHAR(45) DEFAULT NULL, INDEX IDX_C36F4CF032C8A3DE (organization_id), INDEX IDX_C36F4CF060E4B879 (feature_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE person_contact_info (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, contact_id INT DEFAULT NULL, status VARCHAR(1) DEFAULT NULL, INDEX IDX_7853E5217BBB47 (person_id), INDEX IDX_7853E5E7A1254A (contact_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE org_features ADD CONSTRAINT FK_C36F4CF032C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)");
        $this->addSql("ALTER TABLE org_features ADD CONSTRAINT FK_C36F4CF060E4B879 FOREIGN KEY (feature_id) REFERENCES feature_master (id)");
        $this->addSql("ALTER TABLE person_contact_info ADD CONSTRAINT FK_7853E5217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)");
        $this->addSql("ALTER TABLE person_contact_info ADD CONSTRAINT FK_7853E5E7A1254A FOREIGN KEY (contact_id) REFERENCES contact_info (id)");
        $this->addSql("DROP TABLE contactinfo");
        $this->addSql("DROP TABLE personcontactinfo");
        $this->addSql("ALTER TABLE person ADD activation_token VARCHAR(500) DEFAULT NULL, ADD confidentiality_stmt_accept_date DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE person_metadata ADD CONSTRAINT FK_DB529123A38A39E4 FOREIGN KEY (Person_id) REFERENCES person (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE person_contact_info DROP FOREIGN KEY FK_7853E5E7A1254A");
        $this->addSql("ALTER TABLE org_features DROP FOREIGN KEY FK_C36F4CF060E4B879");
        $this->addSql("CREATE TABLE contactinfo (contactid INT AUTO_INCREMENT NOT NULL, address1 VARCHAR(100) DEFAULT NULL, address2 VARCHAR(100) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, zip VARCHAR(20) DEFAULT NULL, state VARCHAR(100) DEFAULT NULL, country VARCHAR(100) DEFAULT NULL, primarymobile VARCHAR(15) DEFAULT NULL, alternatemobile VARCHAR(15) DEFAULT NULL, homephone VARCHAR(15) DEFAULT NULL, officephone VARCHAR(15) DEFAULT NULL, primaryemail VARCHAR(100) DEFAULT NULL, alternateemail VARCHAR(100) DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(contactid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE personcontactinfo (personcontactinfoid INT AUTO_INCREMENT NOT NULL, personid INT DEFAULT NULL, contactid INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, status VARCHAR(1) DEFAULT NULL, INDEX IDX_2E28DAF8E1CFD084 (contactid), INDEX IDX_2E28DAF837886FBE (personid), PRIMARY KEY(personcontactinfoid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE personcontactinfo ADD CONSTRAINT FK_2E28DAF837886FBE FOREIGN KEY (personid) REFERENCES person (id)");
        $this->addSql("ALTER TABLE personcontactinfo ADD CONSTRAINT FK_2E28DAF8E1CFD084 FOREIGN KEY (contactid) REFERENCES contactinfo (contactid)");
        $this->addSql("DROP TABLE contact_info");
        $this->addSql("DROP TABLE feature_master");
        $this->addSql("DROP TABLE org_features");
        $this->addSql("DROP TABLE person_contact_info");
        $this->addSql("ALTER TABLE person DROP activation_token, DROP confidentiality_stmt_accept_date");
        $this->addSql("ALTER TABLE person_metadata DROP FOREIGN KEY FK_DB529123A38A39E4");
    }
}
