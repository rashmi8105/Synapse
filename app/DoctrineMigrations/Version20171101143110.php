<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-16263 Fixing Risk Queue Bug in Triggers
 */
class Version20171101143110 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("DROP TRIGGER IF EXISTS `org_calc_update`;");
        $this->addSql("CREATE DEFINER=`synapsemaster`@`%` TRIGGER org_calc_update AFTER UPDATE ON org_riskval_calc_inputs
                              FOR EACH ROW
                              BEGIN
                                UPDATE
                                    org_calc_flags_risk
                                SET
                                    calculated_at = NULL,
                                    modified_at = CURRENT_TIMESTAMP
                                WHERE
                                    org_id = NEW.org_id
                                    AND person_id = NEW.person_id
                                    AND calculated_at IS NOT NULL;
                    
                                UPDATE
                                    org_calc_flags_talking_point
                                SET
                                    calculated_at = NULL,
                                    modified_at = CURRENT_TIMESTAMP
                                WHERE
                                    org_id = NEW.org_id
                                    AND person_id = NEW.person_id
                                    AND calculated_at IS NOT NULL;
                              END");

        $this->addSql("DROP TRIGGER IF EXISTS `org_calc_move`;");
        $this->addSql("CREATE DEFINER=`synapsemaster`@`%` TRIGGER org_calc_move AFTER INSERT ON org_riskval_calc_inputs
                            FOR EACH ROW
                            BEGIN
                                INSERT IGNORE INTO org_calc_flags_factor (org_id, person_id, calculated_at, created_at, modified_at)
                                VALUES(NEW.org_id, NEW.person_id, '1910-10-10 10:10:10', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
            
                                INSERT IGNORE INTO org_calc_flags_risk (org_id, person_id, calculated_at, created_at, modified_at)
                                VALUES(NEW.org_id, NEW.person_id, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
            
                                INSERT IGNORE INTO org_calc_flags_talking_point (org_id, person_id, calculated_at, created_at, modified_at)
                                VALUES(NEW.org_id, NEW.person_id, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
            
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
