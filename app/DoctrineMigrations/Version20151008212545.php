<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151008212545 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
         $this->addSQL('DROP EVENT IF EXISTS `survey_markers_calc`;');
        $this->addSQL('DROP EVENT IF EXISTS `event_report_calc`;');
        $this->addSQL('DROP EVENT IF EXISTS `event_risk_calc`;');
        $this->addSQL('DROP EVENT IF EXISTS `intent_leave_calc`;');
        $this->addSQL('DROP EVENT IF EXISTS `ReportCalculation_event`;');
        $this->addSQL('DROP EVENT IF EXISTS `Factor_Calc`;');
        $this->addSQL('DROP EVENT IF EXISTS `Success_Marker_Calc_Event`;');
        $this->addSQL('DROP EVENT IF EXISTS `Factor_Calc_Event`;');
        $this->addSQL('DROP EVENT IF EXISTS `Risk_Calc_Event`;');
        $this->addSQL('DROP EVENT IF EXISTS `Intent_Leave_Calc_Event`;');
        $this->addSQL('DROP EVENT IF EXISTS `Talking_Point_Calc_Event`;');
        $this->addSQL('DROP EVENT IF EXISTS `Report_Calc_Event`;');
        $this->addSQL('DROP EVENT IF EXISTS `trigger_survey_data_transfer`;');
        $this->addSQL('DROP EVENT IF EXISTS `trigger_isq_data_transfer`;');

        $this->addSQL("CALL Factor_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 30);");

        /*
        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` EVENT `trigger_isq_data_transfer` ON SCHEDULE EVERY 15 MINUTE STARTS '2015-10-13 00:01:02' ON COMPLETION NOT PRESERVE DO BEGIN
            CALL isq_data_transfer();
        END");

        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` EVENT `trigger_survey_data_transfer` ON SCHEDULE EVERY 15 MINUTE STARTS '2015-10-13 00:01:01' ON COMPLETION NOT PRESERVE DO BEGIN
            CALL survey_data_transfer();
        END");

        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` EVENT `Factor_Calc_Event` ON SCHEDULE EVERY 15 MINUTE STARTS '2015-10-13 00:02:03' ON COMPLETION NOT PRESERVE DO BEGIN
            CALL Factor_Calc(DATE_ADD(NOW(), INTERVAL 140 second), 15);
        END");

         $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` EVENT `Report_Calc_Event` ON SCHEDULE EVERY 15 MINUTE STARTS '2015-10-13 00:05:04' ON COMPLETION NOT PRESERVE DO BEGIN
            CALL ReportCalculation(DATE_ADD(NOW(), INTERVAL 50 second), 15);
        END");

        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` EVENT `Success_Marker_Calc_Event` ON SCHEDULE EVERY 15 MINUTE STARTS '2015-10-13 00:06:05' ON COMPLETION NOT PRESERVE DO BEGIN
            CALL Success_Marker_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 15);
        END");

          $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` EVENT `Intent_Leave_Calc_Event` ON SCHEDULE EVERY 15 MINUTE STARTS '2015-10-13 00:07:06' ON COMPLETION NOT PRESERVE DO BEGIN
            CALL Intent_Leave_Calc_Event();
        END");

        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` EVENT `Talking_Point_Calc_Event` ON SCHEDULE EVERY 15 MINUTE STARTS '2015-10-13 00:08:07' ON COMPLETION NOT PRESERVE DO BEGIN
            CALL Talking_Point_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 500);
        END");

        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` EVENT `Risk_Calc_Event` ON SCHEDULE EVERY 15 MINUTE STARTS '2015-10-13 00:09:08' ON COMPLETION NOT PRESERVE DO BEGIN
            CALL org_RiskFactorCalculation(DATE_ADD(NOW(), INTERVAL 280 second), 30);
        END");
        */

       $this->addSQL('DROP EVENT IF EXISTS `Survey_Risk_Event`;');
       
       $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` EVENT `Survey_Risk_Event` ON SCHEDULE EVERY 15 MINUTE STARTS '2015-10-13 00:01:30' ON COMPLETION NOT PRESERVE DO BEGIN
           SET @startTime = NOW();
           CALL survey_data_transfer();
           CALL isq_data_transfer();
           CALL Factor_Calc(DATE_ADD(NOW(), INTERVAL 140 second), 60);
           CALL Report_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 60);
           CALL Success_Marker_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 60);
           CALL Intent_Leave_Calc();
           CALL Talking_Point_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 300);
           CALL org_RiskFactorCalculation(DATE_ADD(@startTime, INTERVAL 14 minute), 30);
        END");
        


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
