<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-13301: Migration Script to update the trigger on org_riskval_calc_inputs trigger org_calc_move
 * ESPRJ-13300: Migration Script to drop stored procedure Success_Marker_Calc
 * ESPRJ-11205: Migration Script to drop table org_calc_flags_success_marker
 */
class Version20170530120343 extends AbstractMigration
{
    /**
     * Migration Script to update trigger org_calc_move, drop stored procedure Success_Marker_Calc ,trigger org_riskval_calc_inputs and table org_calc_flags_success_marker
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSQL('DROP TRIGGER IF EXISTS `synapse`.`org_calc_move`;');
        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` TRIGGER org_calc_move AFTER INSERT ON org_riskval_calc_inputs
              FOR EACH ROW
              BEGIN
                INSERT INTO org_calc_flags_factor (org_id, person_id, calculated_at, created_at, modified_at) 
                VALUES(NEW.org_id, NEW.person_id, '1910-10-10 10:10:10', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
                INSERT INTO org_calc_flags_risk (org_id, person_id, calculated_at, created_at, modified_at) 
                VALUES(NEW.org_id, NEW.person_id, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
                INSERT INTO org_calc_flags_talking_point (org_id, person_id, calculated_at, created_at, modified_at) 
                VALUES(NEW.org_id, NEW.person_id, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
              END");
        $this->addSQL('DROP PROCEDURE IF EXISTS `synapse`.`Success_Marker_Calc`;');
        $this->addSQL('DROP TABLE IF EXISTS `synapse`.`org_calc_flags_success_marker`;');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
