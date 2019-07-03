<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-16191 - Create org_person_job_queue table
 */
class Version20171016092602 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE org_person_job_queue (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, modified_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, person_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, job_type_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, job_id VARCHAR(50) DEFAULT NULL, job_queued_info LONGTEXT DEFAULT NULL, queued_status INT NOT NULL, INDEX IDX_F077DBF2DE12AB56 (created_by), INDEX IDX_F077DBF225F94802 (modified_by), INDEX IDX_F077DBF21F6FA0AF (deleted_by), INDEX IDX_F077DBF25FA33B08 (job_type_id), INDEX fk_org_person_job_queue_organization1 (organization_id), INDEX fk_org_person_job_queue_person1 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('ALTER TABLE org_person_job_queue ADD CONSTRAINT FK_F077DBF2DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_job_queue ADD CONSTRAINT FK_F077DBF225F94802 FOREIGN KEY (modified_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_job_queue ADD CONSTRAINT FK_F077DBF21F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_job_queue ADD CONSTRAINT FK_F077DBF2217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_person_job_queue ADD CONSTRAINT FK_F077DBF232C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_person_job_queue ADD CONSTRAINT FK_F077DBF25FA33B08 FOREIGN KEY (job_type_id) REFERENCES job_type (id)');

        $this->addSql("ALTER TABLE org_person_job_queue COMMENT = 'Newly initiated jobs will be queued in this table when some jobs are in progress for the faculty.';");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE org_person_job_queue');
    }
}
