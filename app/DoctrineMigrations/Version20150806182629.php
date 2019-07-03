<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150806182629 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $event_query = <<<CDATA
drop EVENT if exists event_risk_calc;
        
SET GLOBAL event_scheduler = on;
CREATE EVENT event_risk_calc
    ON SCHEDULE EVERY 1 hour
	STARTS CURRENT_TIMESTAMP
	DO
       CALL Factor_Calc();
       CALL Talking_Point_Calc();
       CALL Success_Marker_Calc();
       CALL org_RiskFactorCalculation();     
		
CDATA;
        $this->addSql($event_query);
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
