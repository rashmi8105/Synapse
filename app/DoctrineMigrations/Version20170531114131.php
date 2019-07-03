<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script to insert date_of_creation field in mapworks_action_variable for closing a referral - ESPRJ-15057
 */
class Version20170531114131 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $date_of_creation = '$$date_of_creation$$';

        // Insert date_of_creation records for closing a referral for current assignee
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            (SELECT id FROM mapworks_action WHERE event_key = 'referral_close_current_assignee'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$date_of_creation')
                        );
                      ");

        // Insert date_of_creation records for closing a referral for interested party
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            (SELECT id FROM mapworks_action WHERE event_key = 'referral_close_interested_party'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$date_of_creation')
                        )
                    ;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        
    }
}
