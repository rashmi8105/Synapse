<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160429194142 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        /**
         * Creating Temporary Fix for bad academic grade entries
         * Converting "F/No Pass" to "F" and "Pass" to "P" to match
         * the reporting mechanisms in the code.  ESPRJ-9937
         */


        // this up() migration is auto-generated, please modify it to your needs
        $this->addSQL("UPDATE academic_update SET grade = 'F', modified_at = NOW(), modified_by = -25 WHERE grade = 'F/No Pass';");
        $this->addSQL("UPDATE academic_update SET grade = 'P', modified_at = NOW(), modified_by = -25 WHERE grade = 'Pass';");
        $this->addSQL("DROP PROCEDURE IF EXISTS `Academic_Update_Grade_Fixer`;");
        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Academic_Update_Grade_Fixer`()
                        BEGIN
                            UPDATE academic_update SET grade = 'F', modified_at = NOW(), modified_by = -25 WHERE grade = 'F/No Pass';
                            UPDATE academic_update SET grade = 'P', modified_at = NOW(), modified_by = -25 WHERE grade = 'Pass';
                        END");
        $this->addSQL("DROP EVENT IF EXISTS `Survey_Risk_Event`;");
        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` EVENT `Survey_Risk_Event` ON SCHEDULE EVERY 15 MINUTE STARTS '2015-10-13 00:01:30' ENABLE DO BEGIN
                       SET @startTime = NOW();
                       CALL Academic_Update_Grade_Fixer();
                       CALL survey_data_transfer();
                       CALL isq_data_transfer();
                       CALL Factor_Calc(DATE_ADD(NOW(), INTERVAL 140 second), 60);
                       CALL Success_Marker_Calc(DATE_ADD(NOW(), INTERVAL 100 second), 60);
                       CALL Report_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 60);
                       CALL Intent_Leave_Calc();
                       CALL Talking_Point_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 100);
                       CALL org_RiskFactorCalculation(DATE_ADD(@startTime, INTERVAL 14 minute), 30);
                       END");
    }   


    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
