<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150731131034 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('ALTER IGNORE TABLE `synapse`.`risk_group_person_history` 
        DROP INDEX `fk_risk_group_person_history_person1_idx` ,
        ADD UNIQUE INDEX `fk_risk_group_person_history_person1_idx` (`person_id` ASC, `risk_group_id` ASC);');

        $this->addSQL('ALTER TABLE `synapse`.`risk_model_weights` 
        DROP INDEX `fk_risk_model_bucket_risk_model_master1_idx` ;');

        $this->addSQL('ALTER TABLE `synapse`.`org_risk_group_model` 
        DROP INDEX `fk_orgriskmodel_orgid` ,
        ADD INDEX `fk_orgriskmodel_orgid` (`org_id` ASC, `risk_group_id` ASC, `risk_model_id` ASC);');
        
        $this->addSQL('ALTER TABLE `synapse`.`risk_model_levels` 
        DROP INDEX `fk_risk_model_levels_risk_model_master1_idx` ;');

        $this->addSQL('ALTER TABLE `synapse`.`risk_variable_category` 
		DROP INDEX `fk_risk_model_bucket_category_risk_variable1_idx` ,
		ADD INDEX `fk_risk_model_bucket_category_risk_variable1_idx` (`risk_variable_id` ASC, `option_value`(4) ASC, `bucket_value` ASC);');

        $this->addSQL('drop procedure if exists CreateTemptables;');
        
        $this->addSQL('drop procedure if exists cur_org_calculated_rv;');

        $this->addSQL('drop procedure if exists RiskFactorCalculation;');

        $this->addSQL('ALTER TABLE `synapse`.`org_calculated_risk_variables` 
DROP FOREIGN KEY `FK_93D7B9DDF4837C1B`,
DROP FOREIGN KEY `FK_93D7B9DD9F5CF488`,
DROP FOREIGN KEY `FK_93D7B9DD296E76DF`,
DROP FOREIGN KEY `FK_93D7B9DD217BBB47`;
ALTER TABLE `synapse`.`org_calculated_risk_variables` 
CHANGE COLUMN `created_at` `created_at` DATETIME NOT NULL DEFAULT "1950-01-01 01:01:01" AFTER `risk_model_id`,
CHANGE COLUMN `org_id` `org_id` INT(11) NOT NULL AFTER `created_at`,
ADD COLUMN `risk_group_id` INT NOT NULL AFTER `risk_variable_id`,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`person_id`, `risk_variable_id`, `risk_group_id`, `risk_model_id`, `created_at`),
ADD INDEX `fk_group` (`risk_group_id` ASC),
ADD INDEX `created_at` (`created_at` ASC),
DROP INDEX `fk_org_computed_risk_variables_person1_idx` ;');
        /*
        $this->addSQL('ALTER TABLE `synapse`.`org_calculated_risk_variables` 
        DROP COLUMN `created_at`,
        CHANGE COLUMN `modified_at` `created_at` DATETIME NOT NULL DEFAULT 0 , RENAME TO  `synapse`.`org_calculated_risk_variables_history` ;');
        */
       

       $this->addSQL('ALTER TABLE `synapse`.`org_calculated_risk_variables` 
RENAME TO  `synapse`.`org_calculated_risk_variables_history` ;');


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
