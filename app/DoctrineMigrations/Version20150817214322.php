<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150817214322 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSQL('ALTER TABLE `synapse`.`org_riskval_calc_inputs` 
		ADD INDEX `stale_risk` (`is_riskval_calc_required` ASC, `modified_at` ASC),
		ADD INDEX `stale_smark` (`is_success_marker_calc_reqd` ASC, `modified_at` ASC),
		ADD INDEX `stale_tpoint` (`is_talking_point_calc_reqd` ASC, `modified_at` ASC),
		ADD INDEX `stale_factor` (`is_factor_calc_reqd` ASC, `modified_at` ASC);');

		$this->addSQL('DROP EVENT IF EXISTS event_risk_calc;');


        $riskEvent = '
        CREATE EVENT event_risk_calc
        ON SCHEDULE EVERY 1 minute
        STARTS CURRENT_TIMESTAMP + INTERVAL 11 minute + INTERVAL 30 second    
        DO BEGIN
            CALL org_RiskFactorCalculation(50);
        END';
        


        $this->addSQL($riskEvent);

        $this->addSQL('DROP EVENT IF EXISTS survey_factors_calc;');

        $surveyFactor = '
        CREATE EVENT survey_factors_calc
        ON SCHEDULE EVERY 15 minute
        STARTS CURRENT_TIMESTAMP      
        DO BEGIN
            CALL Factor_Calc();
        END';

        $this->addSQL($surveyFactor);

        $this->addSQL('DROP EVENT IF EXISTS survey_talking_points_calc;');

        $talkingPoints = '
        CREATE EVENT survey_talking_points_calc
        ON SCHEDULE EVERY 15 minute
        STARTS CURRENT_TIMESTAMP + INTERVAL 5 minute   
        DO BEGIN
            CALL Talking_Point_Calc();
        END';

        $this->addSQL($talkingPoints);

        $this->addSQL('DROP EVENT IF EXISTS survey_markers_calc;');

        $surveyMarkers = '
        CREATE EVENT survey_markers_calc
        ON SCHEDULE EVERY 15 minute
        STARTS CURRENT_TIMESTAMP + INTERVAL 7 minute      
        DO BEGIN
            CALL Success_Marker_Calc();
        END';

        $this->addSQL($surveyMarkers);

        $this->addSQL('DROP EVENT IF EXISTS intent_leave_calc;');
       

        $intentToLeave = '
        CREATE EVENT intent_leave_calc
        ON SCHEDULE EVERY 15 minute
        STARTS CURRENT_TIMESTAMP + INTERVAL 10 minute      
        DO BEGIN
            CALL Intent_Leave_Calc();
        END';

        $this->addSQL($intentToLeave);



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
