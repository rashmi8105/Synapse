<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140826144814 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE system_alerts (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, title VARCHAR(200) DEFAULT NULL, description VARCHAR(5000) DEFAULT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, is_enabled TINYINT(1) DEFAULT NULL, INDEX IDX_E7F475AA217BBB47 (person_id), INDEX IDX_E7F475AA32C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("DROP TABLE organizationrole");
        $this->addSql("ALTER TABLE AccessToken DROP FOREIGN KEY FK_B39617F5A76ED395");
        $this->addSql("ALTER TABLE AuthCode DROP FOREIGN KEY FK_F1D7D177A76ED395");
        $this->addSql("ALTER TABLE RefreshToken DROP FOREIGN KEY FK_7142379EA76ED395");
        $this->addSql("ALTER TABLE person_entity DROP FOREIGN KEY FK_928D74DEA38A39E4");
        $this->addSql("ALTER TABLE person_metadata DROP FOREIGN KEY FK_DB529123A38A39E4");
        $this->addSql("ALTER TABLE personcontactinfo DROP FOREIGN KEY FK_2E28DAF837886FBE");
        $this->addSql("ALTER TABLE organizationlang DROP FOREIGN KEY FK_76EFC27BE808A0A6");

        $this->addSql("ALTER TABLE person CHANGE personid id INT AUTO_INCREMENT NOT NULL");
        $this->addSql("ALTER TABLE AccessToken ADD CONSTRAINT FK_B39617F5A76ED395 FOREIGN KEY (user_id) REFERENCES person (id)");
        $this->addSql("ALTER TABLE AuthCode ADD CONSTRAINT FK_F1D7D177A76ED395 FOREIGN KEY (user_id) REFERENCES person (id)");
        $this->addSql("ALTER TABLE contactinfo ADD created_by INT DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD modified_by INT DEFAULT NULL, ADD modified_at DATETIME DEFAULT NULL, ADD deleted_by INT DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE entity ADD created_by INT DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD modified_by INT DEFAULT NULL, ADD modified_at DATETIME DEFAULT NULL, ADD deleted_by INT DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE organization ADD parent_organization_id INT DEFAULT NULL, ADD time_zone VARCHAR(45) DEFAULT NULL, ADD logo_file_name VARCHAR(100) DEFAULT NULL, ADD primary_color VARCHAR(45) DEFAULT NULL, ADD secondary_color VARCHAR(45) DEFAULT NULL, ADD ebi_confidentiality_statement VARCHAR(5000) DEFAULT NULL, ADD irb_confidentiality_statement VARCHAR(5000) DEFAULT NULL, DROP parentorganizationid, DROP timezone, CHANGE subdomain subdomain VARCHAR(45) DEFAULT NULL, CHANGE status status VARCHAR(1) DEFAULT NULL, CHANGE website website VARCHAR(100) DEFAULT NULL, CHANGE organizationid id INT AUTO_INCREMENT NOT NULL");
        $this->addSql("ALTER TABLE organizationlang ADD CONSTRAINT FK_76EFC27BE808A0A6 FOREIGN KEY (organizationid) REFERENCES organization (id)");
        $this->addSql("ALTER TABLE system_alerts ADD CONSTRAINT FK_E7F475AA217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)");
        $this->addSql("ALTER TABLE system_alerts ADD CONSTRAINT FK_E7F475AA32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)");
        $this->addSql("ALTER TABLE person_entity ADD CONSTRAINT FK_928D74DEA38A39E4 FOREIGN KEY (Person_id) REFERENCES person (id)");
        $this->addSql("ALTER TABLE person_metadata ADD CONSTRAINT FK_DB529123A38A39E4 FOREIGN KEY (Person_id) REFERENCES person (id)");
        $this->addSql("ALTER TABLE personcontactinfo ADD CONSTRAINT FK_2E28DAF837886FBE FOREIGN KEY (personid) REFERENCES person (id)");
        $this->addSql("ALTER TABLE RefreshToken ADD CONSTRAINT FK_7142379EA76ED395 FOREIGN KEY (user_id) REFERENCES person (id)");
        $this->addSql("ALTER TABLE role ADD created_by INT DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD modified_by INT DEFAULT NULL, ADD modified_at DATETIME DEFAULT NULL, ADD deleted_by INT DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE rolelang ADD created_by INT DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD modified_by INT DEFAULT NULL, ADD modified_at DATETIME DEFAULT NULL, ADD deleted_by INT DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE upload_file_log ADD created_by INT DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD modified_by INT DEFAULT NULL, ADD modified_at DATETIME DEFAULT NULL, ADD deleted_by INT DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE organizationrole (orgroleid INT AUTO_INCREMENT NOT NULL, roleid INT DEFAULT NULL, personid INT DEFAULT NULL, organizationid INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_108FCC7337886FBE (personid), INDEX IDX_108FCC73E808A0A6 (organizationid), INDEX IDX_108FCC732D46D92A (roleid), PRIMARY KEY(orgroleid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE organizationrole ADD CONSTRAINT FK_108FCC732D46D92A FOREIGN KEY (roleid) REFERENCES role (roleid)");
        $this->addSql("ALTER TABLE organizationrole ADD CONSTRAINT FK_108FCC7337886FBE FOREIGN KEY (personid) REFERENCES person (personid)");
        $this->addSql("ALTER TABLE organizationrole ADD CONSTRAINT FK_108FCC73E808A0A6 FOREIGN KEY (organizationid) REFERENCES organization (organizationid)");
        $this->addSql("DROP TABLE system_alerts");
        $this->addSql("ALTER TABLE AccessToken DROP FOREIGN KEY FK_B39617F5A76ED395");
        $this->addSql("ALTER TABLE AccessToken ADD CONSTRAINT FK_B39617F5A76ED395 FOREIGN KEY (user_id) REFERENCES person (personid)");
        $this->addSql("ALTER TABLE AuthCode DROP FOREIGN KEY FK_F1D7D177A76ED395");
        $this->addSql("ALTER TABLE AuthCode ADD CONSTRAINT FK_F1D7D177A76ED395 FOREIGN KEY (user_id) REFERENCES person (personid)");
        $this->addSql("ALTER TABLE RefreshToken DROP FOREIGN KEY FK_7142379EA76ED395");
        $this->addSql("ALTER TABLE RefreshToken ADD CONSTRAINT FK_7142379EA76ED395 FOREIGN KEY (user_id) REFERENCES person (personid)");
        $this->addSql("ALTER TABLE contactinfo DROP created_by, DROP created_at, DROP modified_by, DROP modified_at, DROP deleted_by, DROP deleted_at");
        $this->addSql("ALTER TABLE entity DROP created_by, DROP created_at, DROP modified_by, DROP modified_at, DROP deleted_by, DROP deleted_at");
        $this->addSql("ALTER TABLE organization DROP PRIMARY KEY");
        $this->addSql("ALTER TABLE organization ADD parentorganizationid INT NOT NULL, ADD timezone VARCHAR(45) NOT NULL, DROP parent_organization_id, DROP time_zone, DROP logo_file_name, DROP primary_color, DROP secondary_color, DROP ebi_confidentiality_statement, DROP irb_confidentiality_statement, CHANGE subdomain subdomain VARCHAR(45) NOT NULL, CHANGE status status VARCHAR(1) NOT NULL, CHANGE website website VARCHAR(100) NOT NULL, CHANGE id organizationid INT AUTO_INCREMENT NOT NULL");
        $this->addSql("ALTER TABLE organization ADD PRIMARY KEY (organizationid)");
        $this->addSql("ALTER TABLE organizationlang DROP FOREIGN KEY FK_76EFC27BE808A0A6");
        $this->addSql("ALTER TABLE organizationlang ADD CONSTRAINT FK_76EFC27BE808A0A6 FOREIGN KEY (organizationid) REFERENCES organization (organizationid)");
        $this->addSql("ALTER TABLE person DROP PRIMARY KEY");
        $this->addSql("ALTER TABLE person CHANGE id personid INT AUTO_INCREMENT NOT NULL");
        $this->addSql("ALTER TABLE person ADD PRIMARY KEY (personid)");
        $this->addSql("ALTER TABLE person_entity DROP FOREIGN KEY FK_928D74DEA38A39E4");
        $this->addSql("ALTER TABLE person_entity ADD CONSTRAINT FK_928D74DEA38A39E4 FOREIGN KEY (Person_id) REFERENCES person (personid)");
        $this->addSql("ALTER TABLE person_metadata DROP FOREIGN KEY FK_DB529123A38A39E4");
        $this->addSql("ALTER TABLE person_metadata ADD CONSTRAINT FK_DB529123A38A39E4 FOREIGN KEY (Person_id) REFERENCES person (personid)");
        $this->addSql("ALTER TABLE personcontactinfo DROP FOREIGN KEY FK_2E28DAF837886FBE");
        $this->addSql("ALTER TABLE personcontactinfo ADD CONSTRAINT FK_2E28DAF837886FBE FOREIGN KEY (personid) REFERENCES person (personid)");
        $this->addSql("ALTER TABLE role DROP created_by, DROP created_at, DROP modified_by, DROP modified_at, DROP deleted_by, DROP deleted_at");
        $this->addSql("ALTER TABLE rolelang DROP created_by, DROP created_at, DROP modified_by, DROP modified_at, DROP deleted_by, DROP deleted_at");
        $this->addSql("ALTER TABLE upload_file_log DROP created_by, DROP created_at, DROP modified_by, DROP modified_at, DROP deleted_by, DROP deleted_at");
    }
}
