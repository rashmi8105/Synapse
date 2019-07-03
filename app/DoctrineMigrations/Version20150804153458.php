<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150804153458 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $riskEvent = <<<HEREDOC
#-- Script to create scheduled event for triggering risk calculation periodically.
#-- This has to be run only once to create the event.
#-- Periodicity should be given in the event creation statement
DROP EVENT IF EXISTS event_risk_calc;

#--SET GLOBAL event_scheduler = on$$
CREATE EVENT event_risk_calc
    ON SCHEDULE EVERY 1 minute
    STARTS CURRENT_TIMESTAMP    
    DISABLE ON SLAVE
    DO BEGIN
        CALL org_RiskFactorCalculation(50);'
HEREDOC;


$this->addSQL($riskEvent);




        

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('DROP EVENT IF EXISTS event_risk_calc;');

    }
}
