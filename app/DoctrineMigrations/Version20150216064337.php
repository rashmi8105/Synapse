<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150216064337 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE IF NOT EXISTS org_academic_terms (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, org_academic_year_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, name VARCHAR(120) DEFAULT NULL, short_code VARCHAR(10) DEFAULT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, INDEX IDX_75DF84EFDE12AB56 (created_by), INDEX IDX_75DF84EF25F94802 (modified_by), INDEX IDX_75DF84EF1F6FA0AF (deleted_by), INDEX IDX_75DF84EF32C8A3DE (organization_id), INDEX IDX_75DF84EFF3B0CE4A (org_academic_year_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS org_academic_year (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, organization_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, name VARCHAR(120) DEFAULT NULL, short_code VARCHAR(10) DEFAULT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, INDEX IDX_A4C0972DDE12AB56 (created_by), INDEX IDX_A4C0972D25F94802 (modified_by), INDEX IDX_A4C0972D1F6FA0AF (deleted_by), INDEX IDX_A4C0972D32C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS person_ebi_metadata (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, person_id INT DEFAULT NULL, ebi_metadata_id INT DEFAULT NULL, org_academic_year_id INT DEFAULT NULL, org_academic_terms_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, metadata_value VARCHAR(2000) DEFAULT NULL, INDEX IDX_8ABD58A3DE12AB56 (created_by), INDEX IDX_8ABD58A325F94802 (modified_by), INDEX IDX_8ABD58A31F6FA0AF (deleted_by), INDEX IDX_8ABD58A3217BBB47 (person_id), INDEX IDX_8ABD58A3BB49FE75 (ebi_metadata_id), INDEX IDX_8ABD58A3F3B0CE4A (org_academic_year_id), INDEX IDX_8ABD58A38D7CC0D2 (org_academic_terms_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS person_org_metadata (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, person_id INT DEFAULT NULL, org_metadata_id INT DEFAULT NULL, org_academic_year_id INT DEFAULT NULL, org_academic_periods_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, metadata_value VARCHAR(2000) DEFAULT NULL, INDEX IDX_D0B544BADE12AB56 (created_by), INDEX IDX_D0B544BA25F94802 (modified_by), INDEX IDX_D0B544BA1F6FA0AF (deleted_by), INDEX IDX_D0B544BA217BBB47 (person_id), INDEX IDX_D0B544BA4012B3BF (org_metadata_id), INDEX IDX_D0B544BAF3B0CE4A (org_academic_year_id), INDEX IDX_D0B544BADF88FD95 (org_academic_periods_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_academic_terms ADD CONSTRAINT FK_75DF84EFDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_academic_terms ADD CONSTRAINT FK_75DF84EF25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_academic_terms ADD CONSTRAINT FK_75DF84EF1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_academic_terms ADD CONSTRAINT FK_75DF84EF32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_academic_terms ADD CONSTRAINT FK_75DF84EFF3B0CE4A FOREIGN KEY (org_academic_year_id) REFERENCES org_academic_year (id)');
        $this->addSql('ALTER TABLE org_academic_year ADD CONSTRAINT FK_A4C0972DDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_academic_year ADD CONSTRAINT FK_A4C0972D25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_academic_year ADD CONSTRAINT FK_A4C0972D1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_academic_year ADD CONSTRAINT FK_A4C0972D32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE person_ebi_metadata ADD CONSTRAINT FK_8ABD58A3DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person_ebi_metadata ADD CONSTRAINT FK_8ABD58A325F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person_ebi_metadata ADD CONSTRAINT FK_8ABD58A31F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person_ebi_metadata ADD CONSTRAINT FK_8ABD58A3217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person_ebi_metadata ADD CONSTRAINT FK_8ABD58A3BB49FE75 FOREIGN KEY (ebi_metadata_id) REFERENCES ebi_metadata (id)');
        $this->addSql('ALTER TABLE person_ebi_metadata ADD CONSTRAINT FK_8ABD58A3F3B0CE4A FOREIGN KEY (org_academic_year_id) REFERENCES org_academic_year (id)');
        $this->addSql('ALTER TABLE person_ebi_metadata ADD CONSTRAINT FK_8ABD58A38D7CC0D2 FOREIGN KEY (org_academic_terms_id) REFERENCES org_academic_terms (id)');
        $this->addSql('ALTER TABLE person_org_metadata ADD CONSTRAINT FK_D0B544BADE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person_org_metadata ADD CONSTRAINT FK_D0B544BA25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person_org_metadata ADD CONSTRAINT FK_D0B544BA1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person_org_metadata ADD CONSTRAINT FK_D0B544BA217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person_org_metadata ADD CONSTRAINT FK_D0B544BA4012B3BF FOREIGN KEY (org_metadata_id) REFERENCES org_metadata (id)');
        $this->addSql('ALTER TABLE person_org_metadata ADD CONSTRAINT FK_D0B544BAF3B0CE4A FOREIGN KEY (org_academic_year_id) REFERENCES org_academic_year (id)');
        $this->addSql('ALTER TABLE person_org_metadata ADD CONSTRAINT FK_D0B544BADF88FD95 FOREIGN KEY (org_academic_periods_id) REFERENCES org_academic_terms (id)');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE person_ebi_metadata DROP FOREIGN KEY FK_8ABD58A38D7CC0D2');
        $this->addSql('ALTER TABLE person_org_metadata DROP FOREIGN KEY FK_D0B544BADF88FD95');
        $this->addSql('ALTER TABLE org_academic_terms DROP FOREIGN KEY FK_75DF84EFF3B0CE4A');
        $this->addSql('ALTER TABLE person_ebi_metadata DROP FOREIGN KEY FK_8ABD58A3F3B0CE4A');
        $this->addSql('ALTER TABLE person_org_metadata DROP FOREIGN KEY FK_D0B544BAF3B0CE4A');
        
        $this->addSql('DROP TABLE IF EXISTS org_academic_terms');
        $this->addSql('DROP TABLE IF EXISTS org_academic_year');
        $this->addSql('DROP TABLE IF EXISTS person_ebi_metadata');
        $this->addSql('DROP TABLE IF EXISTS person_org_metadata');
        
    }
}
