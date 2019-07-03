<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15557 - Migration script for updating the values in job_type, job_type_blocked_mapping tables with SwitchOrgCalendarJob.
 */
class Version20170819143544 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO `synapse`.`job_type` (`job_type`) VALUES ('SwitchOrgCalendarJob');");

        $this->addSql("SET @initialSyncJobId := (SELECT id FROM job_type where job_type='InitialSyncJob');
SET @removeEventJobId := (SELECT id FROM job_type where job_type='RemoveEventJob');
SET @recurrentEventJobId := (SELECT id FROM job_type where job_type='RecurrentEventJob');
SET @switchOrgCalendarJobId := (SELECT id FROM job_type where job_type='SwitchOrgCalendarJob');
");

        $this->addSql('INSERT INTO `synapse`.`job_type_blocked_mapping` (`job_type_id`, `blocked_by_job_type_id`) VALUES (@initialSyncJobId, @switchOrgCalendarJobId);');

        $this->addSql("INSERT INTO `synapse`.`job_type_blocked_mapping` (`job_type_id`, `blocked_by_job_type_id`) VALUES (@removeEventJobId, @switchOrgCalendarJobId);");

        $this->addSql("INSERT INTO `synapse`.`job_type_blocked_mapping` (`job_type_id`, `blocked_by_job_type_id`) VALUES (@switchOrgCalendarJobId, @switchOrgCalendarJobId);
INSERT INTO `synapse`.`job_type_blocked_mapping` (`job_type_id`, `blocked_by_job_type_id`) VALUES (@switchOrgCalendarJobId, @initialSyncJobId);
INSERT INTO `synapse`.`job_type_blocked_mapping` (`job_type_id`, `blocked_by_job_type_id`) VALUES (@switchOrgCalendarJobId, @removeEventJobId);
INSERT INTO `synapse`.`job_type_blocked_mapping` (`job_type_id`, `blocked_by_job_type_id`) VALUES (@switchOrgCalendarJobId, @recurrentEventJobId);");

        $this->addSql("ALTER TABLE `synapse`.`org_person_job_status` ADD COLUMN `failure_description` TEXT NULL  AFTER `job_id`");

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
	