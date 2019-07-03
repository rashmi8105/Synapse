<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-11589-ESPRJ-11403-ESPRJ-11645 MOVES Survey_Risk_Event to using V2 Risk instead of V1
 */
class Version20171031171930 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("DROP EVENT IF EXISTS `Survey_Risk_Event`;");
        $this->addSql("CREATE DEFINER = `synapsemaster`@`%` EVENT `Survey_Risk_Event` ON SCHEDULE EVERY 15 MINUTE STARTS '2015-10-13 00:01:30' ENABLE DO
                            BEGIN
                                SET @startTime = NOW();
                                CALL Academic_Update_Grade_Fixer();
                                CALL survey_data_transfer();
                                CALL isq_data_transfer();
                                CALL Factor_Calc(DATE_ADD(NOW(), INTERVAL 140 second), 60);
                                CALL Report_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 60);
                                CALL Intent_Leave_Calc();
                                CALL Talking_Point_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 100);
                                CALL risk_calculation_V2(DATE_ADD(@startTime, INTERVAL 14 minute), 30);
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
