<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150903024315 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

		$this->addSQL('DROP TRIGGER IF EXISTS org_calc_move;');        

        $this->addSQL('CREATE TRIGGER org_calc_move AFTER INSERT ON org_riskval_calc_inputs
	  	FOR EACH ROW
	  BEGIN
	    INSERT INTO org_calc_flags_factor (org_id, person_id, calculated_at, created_at, modified_at) 
	    VALUES(NEW.org_id, NEW.person_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
	    INSERT INTO org_calc_flags_risk (org_id, person_id, calculated_at, created_at, modified_at) 
	    VALUES(NEW.org_id, NEW.person_id, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
	    INSERT INTO org_calc_flags_talking_point (org_id, person_id, calculated_at, created_at, modified_at) 
	    VALUES(NEW.org_id, NEW.person_id, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
	    INSERT INTO org_calc_flags_success_marker (org_id, person_id, calculated_at, created_at, modified_at) 
	    VALUES(NEW.org_id, NEW.person_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
	  END');

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
