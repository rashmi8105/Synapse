<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-16191 - Migration script to update the value to job_type_blocked_mapping
 */
class Version20171016091116 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE job_type_blocked_mapping COMMENT = 'Determines what action happens to the requested Resque job based on the type of the currently running Resque jobs';");

        $this->addSql("ALTER TABLE `synapse`.`job_type_blocked_mapping` ADD COLUMN `action` VARCHAR(45) NULL  AFTER `deleted_at` ;");

        $this->addSql("SET @initialSyncJobId := (SELECT id FROM job_type where job_type='InitialSyncJob');SET @removeEventJobId := (SELECT id FROM job_type where job_type='RemoveEventJob');
SET @recurrentEventJobId := (SELECT id FROM job_type where job_type='RecurrentEventJob');SET @switchOrgCalendarJobId := (SELECT id FROM job_type where job_type='SwitchOrgCalendarJob');
SET @bulkOfficeHourSeriesJob := (SELECT id FROM job_type where job_type='BulkOfficeHourSeriesJob');");

        $this->addSql("UPDATE `synapse`.`job_type_blocked_mapping` SET `action`='blocked' WHERE  job_type_id=@initialSyncJobId and blocked_by_job_type_id=@initialSyncJobId;");

        $this->addSql("UPDATE `synapse`.`job_type_blocked_mapping` SET `action`='blocked' WHERE job_type_id=@initialSyncJobId and blocked_by_job_type_id=@removeEventJobId;");

        $this->addSql("UPDATE `synapse`.`job_type_blocked_mapping` SET `action`='blocked' WHERE job_type_id=@removeEventJobId and blocked_by_job_type_id=@removeEventJobId;");

        $this->addSql("UPDATE `synapse`.`job_type_blocked_mapping` SET `action`='queued' WHERE job_type_id=@removeEventJobId and blocked_by_job_type_id=@initialSyncJobId;");

        $this->addSql("UPDATE `synapse`.`job_type_blocked_mapping` SET `action`='queued' WHERE job_type_id=@removeEventJobId and blocked_by_job_type_id=@recurrentEventJobId;");

        $this->addSql("UPDATE `synapse`.`job_type_blocked_mapping` SET `action`='blocked' WHERE job_type_id=@switchOrgCalendarJobId and blocked_by_job_type_id=@switchOrgCalendarJobId;");

        $this->addSql("UPDATE `synapse`.`job_type_blocked_mapping` SET `action`='blocked' WHERE job_type_id=@switchOrgCalendarJobId and blocked_by_job_type_id=@initialSyncJobId;");

        $this->addSql("UPDATE `synapse`.`job_type_blocked_mapping` SET `action`='blocked' WHERE job_type_id=@switchOrgCalendarJobId and blocked_by_job_type_id=@removeEventJobId;");

        $this->addSql("UPDATE `synapse`.`job_type_blocked_mapping` SET `action`='blocked' WHERE job_type_id=@switchOrgCalendarJobId and blocked_by_job_type_id=@recurrentEventJobId;");

        $this->addSql("INSERT INTO `synapse`.`job_type_blocked_mapping` (`job_type_id`, `blocked_by_job_type_id`, `action`) VALUES (@recurrentEventJobId, @recurrentEventJobId, 'queued');");

        $this->addSql("INSERT INTO `synapse`.`job_type_blocked_mapping` (`job_type_id`, `blocked_by_job_type_id`, `action`) VALUES (@recurrentEventJobId, @removeEventJobId, 'queued');");

        $this->addSql("INSERT INTO `synapse`.`job_type_blocked_mapping` (`job_type_id`, `blocked_by_job_type_id`, `action`) VALUES (@recurrentEventJobId, @initialSyncJobId, 'queued');");

        $this->addSql("DELETE FROM `synapse`.`job_type_blocked_mapping` WHERE job_type_id=@removeEventJobId and blocked_by_job_type_id=@bulkOfficeHourSeriesJob;");

        $this->addSql("DELETE FROM `synapse`.`job_type_blocked_mapping` WHERE job_type_id=@initialSyncJobId and blocked_by_job_type_id=@switchOrgCalendarJobId;");

        $this->addSql("DELETE FROM `synapse`.`job_type_blocked_mapping` WHERE job_type_id=@removeEventJobId and blocked_by_job_type_id=@switchOrgCalendarJobId;");
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
	