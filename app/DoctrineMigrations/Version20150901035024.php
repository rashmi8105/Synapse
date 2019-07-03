<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150901035024 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        //Modifying Trigger so it only affect talking points and risk for changes to a student upload
        $this->addSQL('DROP TRIGGER IF EXISTS `org_calc_update`;');
        $this->addSQL('CREATE DEFINER=`synapsemaster`@`%` TRIGGER org_calc_update AFTER UPDATE ON org_riskval_calc_inputs
          FOR EACH ROW
          BEGIN
            UPDATE org_calc_flags_risk SET calculated_at = NULL, modified_at = CURRENT_TIMESTAMP WHERE org_id = NEW.org_id AND person_id = NEW.person_id;
            UPDATE org_calc_flags_talking_point SET calculated_at = NULL, modified_at = CURRENT_TIMESTAMP  WHERE org_id = NEW.org_id AND person_id = NEW.person_id;
          END');

        //Adding Indexes Removed Because of Doctrine Bug, adding Fresh Indexes this time
		$this->addSQL('ALTER TABLE `synapse`.`org_group_faculty` 
		ADD INDEX `org_person_delete_idx` (`org_permissionset_id` ASC, `person_id` ASC, `deleted_at` ASC);');
		
		$this->addSQL('ALTER TABLE `synapse`.`org_course_faculty`
			ADD INDEX `org_person_delete_idx` (`org_permissionset_id` ASC, `person_id` ASC, `deleted_at` ASC);');


		//Removing Duplicates in the flag tables and guaranteeing unique records
        $this->addSQL("ALTER IGNORE TABLE `synapse`.`org_calc_flags_factor` 
		DROP INDEX `org_person_idx` ,
		ADD UNIQUE INDEX `org_person_idx` (`org_id` ASC, `person_id` ASC);");

		$this->addSQL("ALTER IGNORE TABLE `synapse`.`org_calc_flags_risk` 
		DROP INDEX `org_person_idx` ,
		ADD UNIQUE INDEX `org_person_idx` (`org_id` ASC, `person_id` ASC);");

		$this->addSQL("ALTER IGNORE TABLE `synapse`.`org_calc_flags_talking_point` 
		DROP INDEX `org_person_idx` ,
		ADD UNIQUE INDEX `org_person_idx` (`org_id` ASC, `person_id` ASC);");

		$this->addSQL("ALTER IGNORE TABLE `synapse`.`org_calc_flags_success_marker` 
		DROP INDEX `org_person_idx` ,
		ADD UNIQUE INDEX `org_person_idx` (`org_id` ASC, `person_id` ASC);");

		$this->addSQL("ALTER IGNORE TABLE `synapse`.`org_riskval_calc_inputs` 
		DROP INDEX `fk_org_riskval_calc_inputs_organization1_idx` ,
		ADD UNIQUE INDEX `fk_org_riskval_calc_inputs_organization1_idx` (`org_id` ASC, `person_id` ASC);");
		

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
