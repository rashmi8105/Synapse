<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151015183553 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
                $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
           $this->addSQL('DROP EVENT IF EXISTS `Survey_Risk_Event`;');
       
       $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` EVENT `Survey_Risk_Event` ON SCHEDULE EVERY 15 MINUTE STARTS '2015-10-13 00:01:30' ON COMPLETION NOT PRESERVE DO BEGIN
           SET @startTime = NOW();
           CALL survey_data_transfer();
           CALL isq_data_transfer();
           CALL Factor_Calc(DATE_ADD(NOW(), INTERVAL 140 second), 60);
           CALL Success_Marker_Calc(DATE_ADD(NOW(), INTERVAL 100 second), 60);
           CALL Report_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 60);
           CALL Intent_Leave_Calc();
           CALL Talking_Point_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 100);
           CALL org_RiskFactorCalculation(DATE_ADD(@startTime, INTERVAL 14 minute), 30);
        END");
        // 
        

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
