<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151014164308 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('CREATE OR REPLACE
    ALGORITHM = MERGE 
    DEFINER = `synapsemaster`@`%` 
    SQL SECURITY INVOKER
VIEW `org_person_riskvariable` AS
    SELECT 
        `orgc`.`org_id` AS `org_id`,
        `rgph`.`person_id` AS `person_id`,
        `rv`.`id` AS `risk_variable_id`,
        `rv`.`source` AS `source`,
        `rv`.`variable_type` AS `variable_type`,
        `rv`.`calc_type` AS `calc_type`,
        `rgph`.`risk_group_id` AS `risk_group_id`,
        `rmm`.`id` AS `risk_model_id`,
        `rmw`.`weight` AS `weight`,
        GREATEST(IFNULL(`rgph`.`assignment_date`, 0),
                IFNULL(`orgc`.`modified_at`, 0),
                IFNULL(`orgc`.`created_at`, 0),
                IFNULL(`orgm`.`modified_at`, 0),
                IFNULL(`orgm`.`created_at`, 0),
                IFNULL(`rmm`.`modified_at`, 0),
                IFNULL(`rmm`.`created_at`, 0),
                IFNULL(`rv`.`modified_at`, 0),
                IFNULL(`rv`.`created_at`, 0)) AS `modified_at`
    FROM
        ((((((`risk_group_person_history` `rgph`
        JOIN `org_calc_flags_risk` `orgc` ON ((`rgph`.`person_id` = `orgc`.`person_id`))))
        JOIN `org_risk_group_model` `orgm` ON (((`rgph`.`risk_group_id` = `orgm`.`risk_group_id`)
            AND (`orgm`.`org_id` = `orgc`.`org_id`))))
        JOIN `risk_model_master` `rmm` ON (((`orgm`.`risk_model_id` = `rmm`.`id`)
            AND (NOW() BETWEEN `rmm`.`calculation_start_date` AND `rmm`.`calculation_end_date`))))
        JOIN `risk_model_weights` `rmw` ON ((`rmw`.`risk_model_id` = `rmm`.`id`)))
        JOIN `risk_variable` `rv` ON ((`rmw`.`risk_variable_id` = `rv`.`id`)));
');

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
