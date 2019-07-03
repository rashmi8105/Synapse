<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150825211142 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        // 
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSQL('DROP TRIGGER IF EXISTS org_calc_move;');

        $this->addSQL('CREATE TRIGGER org_calc_move AFTER INSERT ON org_riskval_calc_inputs
              FOR EACH ROW
              BEGIN
                INSERT INTO org_calc_flags_factor (org_id, person_id, calculated_at, created_at, modified_at) 
                VALUES(NEW.org_id, NEW.person_id, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
                INSERT INTO org_calc_flags_risk (org_id, person_id, calculated_at, created_at, modified_at) 
                VALUES(NEW.org_id, NEW.person_id, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
                INSERT INTO org_calc_flags_talking_point (org_id, person_id, calculated_at, created_at, modified_at) 
                VALUES(NEW.org_id, NEW.person_id, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
                INSERT INTO org_calc_flags_success_marker (org_id, person_id, calculated_at, created_at, modified_at) 
                VALUES(NEW.org_id, NEW.person_id, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
              END');

        $this->addSQL('DROP TRIGGER IF EXISTS org_calc_update;');

        $this->addSQL('CREATE TRIGGER org_calc_update AFTER UPDATE ON org_riskval_calc_inputs
          FOR EACH ROW
          BEGIN
            UPDATE org_calc_flags_factor f SET calculated_at = NULL, modified_at = CURRENT_TIMESTAMP WHERE f.org_id = NEW.org_id AND f.person_id = NEW.person_id;
            UPDATE org_calc_flags_risk SET calculated_at = NULL, modified_at = CURRENT_TIMESTAMP WHERE org_id = NEW.org_id AND person_id = NEW.person_id;
            UPDATE org_calc_flags_talking_point SET calculated_at = NULL, modified_at = CURRENT_TIMESTAMP  WHERE org_id = NEW.org_id AND person_id = NEW.person_id;
            UPDATE org_calc_flags_success_marker SET calculated_at = NULL, modified_at = CURRENT_TIMESTAMP  WHERE org_id = NEW.org_id AND person_id = NEW.person_id;
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
