<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15557 - Migration script to create job_type, job_status_description, job_type_blocked_mapping and org_person_job_status. Which would be used to store the details about resque job status.
 */
class Version20170809054534 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE job_status_description (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, job_status_description VARCHAR(50) DEFAULT NULL, INDEX IDX_A4ED3584DE12AB56 (created_by), INDEX IDX_A4ED358425F94802 (modified_by), INDEX IDX_A4ED35841F6FA0AF (deleted_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_type (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, job_type VARCHAR(300) DEFAULT NULL, INDEX IDX_B122168DE12AB56 (created_by), INDEX IDX_B12216825F94802 (modified_by), INDEX IDX_B1221681F6FA0AF (deleted_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_type_blocked_mapping (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, job_type_id INT DEFAULT NULL, blocked_by_job_type_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_9696134CDE12AB56 (created_by), INDEX IDX_9696134C25F94802 (modified_by), INDEX IDX_9696134C1F6FA0AF (deleted_by), INDEX IDX_9696134C5FA33B08 (job_type_id), INDEX IDX_9696134C8BE4A44B (blocked_by_job_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE org_person_job_status (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, person_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, job_status_id INT DEFAULT NULL, job_type_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, job_id VARCHAR(50) DEFAULT NULL, INDEX IDX_FC874C6ADE12AB56 (created_by), INDEX IDX_FC874C6A25F94802 (modified_by), INDEX IDX_FC874C6A1F6FA0AF (deleted_by), INDEX IDX_FC874C6AAC47EFAC (job_status_id), INDEX IDX_FC874C6A5FA33B08 (job_type_id), INDEX fk_org_person_job_status_organization1 (organization_id), INDEX fk_org_person_job_status_person1 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        
        $this->addSql('ALTER TABLE job_status_description ADD CONSTRAINT FK_A4ED3584DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE job_status_description ADD CONSTRAINT FK_A4ED358425F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE job_status_description ADD CONSTRAINT FK_A4ED35841F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE job_type ADD CONSTRAINT FK_B122168DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE job_type ADD CONSTRAINT FK_B12216825F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE job_type ADD CONSTRAINT FK_B1221681F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE job_type_blocked_mapping ADD CONSTRAINT FK_9696134CDE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE job_type_blocked_mapping ADD CONSTRAINT FK_9696134C25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE job_type_blocked_mapping ADD CONSTRAINT FK_9696134C1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE job_type_blocked_mapping ADD CONSTRAINT FK_9696134C5FA33B08 FOREIGN KEY (job_type_id) REFERENCES job_type (id)');
        $this->addSql('ALTER TABLE job_type_blocked_mapping ADD CONSTRAINT FK_9696134C8BE4A44B FOREIGN KEY (blocked_by_job_type_id) REFERENCES job_type (id)');
        $this->addSql('ALTER TABLE org_person_job_status ADD CONSTRAINT FK_FC874C6ADE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_job_status ADD CONSTRAINT FK_FC874C6A25F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_job_status ADD CONSTRAINT FK_FC874C6A1F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_job_status ADD CONSTRAINT FK_FC874C6A217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_job_status ADD CONSTRAINT FK_FC874C6A32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_person_job_status ADD CONSTRAINT FK_FC874C6AAC47EFAC FOREIGN KEY (job_status_id) REFERENCES job_status_description (id)');
        $this->addSql('ALTER TABLE org_person_job_status ADD CONSTRAINT FK_FC874C6A5FA33B08 FOREIGN KEY (job_type_id) REFERENCES job_type (id)');
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE org_person_job_status DROP FOREIGN KEY FK_FC874C6AAC47EFAC');
        $this->addSql('ALTER TABLE job_type_blocked_mapping DROP FOREIGN KEY FK_9696134C5FA33B08');
        $this->addSql('ALTER TABLE job_type_blocked_mapping DROP FOREIGN KEY FK_9696134C8BE4A44B');
        $this->addSql('ALTER TABLE org_person_job_status DROP FOREIGN KEY FK_FC874C6A5FA33B08');
		$this->addSql('DROP TABLE job_status_description');
        $this->addSql('DROP TABLE job_type');
        $this->addSql('DROP TABLE job_type_blocked_mapping');
        $this->addSql('DROP TABLE org_person_job_status');
        
        
    }
}
