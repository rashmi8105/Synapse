<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-6145: fix overflow for the fields of person_risk_level_history.weighted_value, person_risk_level_history.maximum_weight_value,
 *      and org_calculated_risk_variables_history.calc_weight during risk calculation.
 *
 */
class Version20171108161130 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Disable the event to prevent accessing related tables for risk calculation.
        $this->addSql("ALTER EVENT Survey_Risk_Event DISABLE;");

        // Let the interval be 1020 seconds = 17 minutes to guarantee the completion of last execution of the org_RiskFactorCalculation procedure.
        $this->addSql("DO SLEEP(1020);");

        // Increase the lengths of both fields as below to decimal(12,4) from decimal(9,4) to avoid 99999.9999 value due to truncation.
        $this->addSql("ALTER TABLE `synapse`.`person_risk_level_history`
CHANGE COLUMN `weighted_value` `weighted_value` DECIMAL(12,4) NULL DEFAULT NULL,
CHANGE COLUMN `maximum_weight_value` `maximum_weight_value` DECIMAL(12,4) NULL DEFAULT NULL;");

        // Increase the lengths of both fields as below to decimal(12,4) from decimal(8,4) to avoid 9999.9999 value due to truncation..
        $this->addSql("ALTER TABLE `synapse`.`org_calculated_risk_variables_history`
CHANGE COLUMN `calc_weight` `calc_weight` DECIMAL(12,4) NULL DEFAULT NULL;");

        // Enable the event.
        $this->addSql("ALTER EVENT Survey_Risk_Event ENABLE;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
