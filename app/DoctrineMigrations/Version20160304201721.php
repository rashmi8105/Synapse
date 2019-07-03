<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160304201721 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        // 
        /**
         * Slight reordering of events.  ISQ needs to go before Survey because of
         * last_update column which limits the number of rows views in isq_data_transfer
         * and survey_data_transfer
         */
        $this->addSQL('ALTER EVENT `Survey_Risk_Event` DISABLE;');
        $this->addSQL("DROP EVENT `Survey_Risk_Event`;");
        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` EVENT `Survey_Risk_Event` ON SCHEDULE EVERY 15 MINUTE STARTS '2015-10-13 00:01:30' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
                       SET @startTime = NOW();
                       CALL isq_data_transfer();
                       CALL survey_data_transfer();
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
