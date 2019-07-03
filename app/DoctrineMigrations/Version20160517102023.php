<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Moves the existing success_marker_calculated table so its data can be preserved despite any upcoming changes.
 * Removes the success marker calculation from the Survey_Risk_Event.
 */
class Version20160517102023 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER EVENT Survey_Risk_Event DISABLE;');

        $this->addSql('RENAME TABLE success_marker_calculated TO success_marker_calculated_legacy;');

        $this->addSQL("DROP EVENT IF EXISTS `Survey_Risk_Event`;");

        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` EVENT `Survey_Risk_Event` ON SCHEDULE EVERY 15 MINUTE STARTS '2015-10-13 00:01:30' ENABLE DO
                        BEGIN
                           SET @startTime = NOW();
                           CALL Academic_Update_Grade_Fixer();
                           CALL survey_data_transfer();
                           CALL isq_data_transfer();
                           CALL Factor_Calc(DATE_ADD(NOW(), INTERVAL 140 second), 60);
                           CALL Report_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 60);
                           CALL Intent_Leave_Calc();
                           CALL Talking_Point_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 100);
                           CALL org_RiskFactorCalculation(DATE_ADD(@startTime, INTERVAL 14 minute), 30);
                        END");

        $this->addSql('ALTER EVENT Survey_Risk_Event ENABLE;');
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
