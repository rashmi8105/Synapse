<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15557 - Migration script for updating the values in job_type, job_status_description, job_type_blocked_mapping tables.
 */
class Version20170809061543 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO `synapse`.`job_type` (`job_type`) VALUES ('InitialSyncJob');
INSERT INTO `synapse`.`job_type` (`job_type`) VALUES ('RemoveEventJob');
INSERT INTO `synapse`.`job_type` (`job_type`) VALUES ('RecurrentEventJob');
INSERT INTO `synapse`.`job_type` (`job_type`) VALUES ('BulkOfficeHourSeriesJob');");

        $this->addSql("INSERT INTO `synapse`.`job_status_description` (`job_status_description`) VALUES ('In Progress');
INSERT INTO `synapse`.`job_status_description` (`job_status_description`) VALUES ('Failure');
INSERT INTO `synapse`.`job_status_description` (`job_status_description`) VALUES ('Cancelled');
INSERT INTO `synapse`.`job_status_description` (`job_status_description`) VALUES ('Success');
INSERT INTO `synapse`.`job_status_description` (`job_status_description`) VALUES ('Queued');
");
        $this->addSql("SET @initialSyncJobId := (SELECT id FROM job_type where job_type='InitialSyncJob');
SET @removeEventJobId := (SELECT id FROM job_type where job_type='RemoveEventJob');
SET @recurrentEventJobId := (SELECT id FROM job_type where job_type='RecurrentEventJob');
SET @bulkOfficeHourSeriesJobId := (SELECT id FROM job_type where job_type='BulkOfficeHourSeriesJob');
");

        $this->addSql('INSERT INTO `synapse`.`job_type_blocked_mapping` (`job_type_id`, `blocked_by_job_type_id`) VALUES (@initialSyncJobId, @initialSyncJobId);
INSERT INTO `synapse`.`job_type_blocked_mapping` (`job_type_id`, `blocked_by_job_type_id`) VALUES (@initialSyncJobId, @removeEventJobId);');

        $this->addSql("INSERT INTO `synapse`.`job_type_blocked_mapping` (`job_type_id`, `blocked_by_job_type_id`) VALUES (@removeEventJobId, @removeEventJobId);
INSERT INTO `synapse`.`job_type_blocked_mapping` (`job_type_id`, `blocked_by_job_type_id`) VALUES (@removeEventJobId, @initialSyncJobId);");

        $this->addSql("INSERT INTO `synapse`.`job_type_blocked_mapping` (`job_type_id`, `blocked_by_job_type_id`) VALUES (@removeEventJobId, @recurrentEventJobId);
INSERT INTO `synapse`.`job_type_blocked_mapping` (`job_type_id`, `blocked_by_job_type_id`) VALUES (@removeEventJobId, @bulkOfficeHourSeriesJobId);");
    }


    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
	