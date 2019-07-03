<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * This migration script updates mapworks_action table, and inserts records into mapworks_action_variable with the given values.
 * Jira ticket no ESPRJ-14064,ESPRJ-14141
 */
class Version20170509111639 extends AbstractMigration
{
    /*
     *
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        // Migration script for updating mapworks action table with remove interested parties information
        $this->addSql('UPDATE mapworks_action
                       SET
                          notification_hover_text = "$$updater_first_name$$ $$updater_last_name$$ has removed you as an interested party on a referral.",
                          notification_body_text =  "$$updater_first_name$$ $$updater_last_name$$ has removed you as an interested party on a referral."
                       WHERE 
                          event_key = "referral_remove_interested_party_interested_party";');

        // Migration script to insert mapworks_action_variable records for removing an interested party
        $this->addSql('INSERT INTO mapworks_action_variable

                       SET mapworks_action_id =
                       
                        (SELECT 
                            id
                         FROM
                            mapworks_action
                         WHERE
                            event_key IN ("referral_remove_interested_party_interested_party")),
                        
                        mapworks_action_variable_description_id =
                        
                        (SELECT 
                            id
                         FROM
                            mapworks_action_variable_description
                         WHERE
                            variable= "$$updater_first_name$$")');


        $this->addSql('INSERT INTO mapworks_action_variable

                       SET mapworks_action_id =
                       
                        (SELECT 
                            id
                         FROM
                            mapworks_action
                         WHERE
                            event_key IN ("referral_remove_interested_party_interested_party")),
                        
                        mapworks_action_variable_description_id =
                        
                        (SELECT 
                             id
                         FROM
                            mapworks_action_variable_description
                         WHERE
                            variable= "$$updater_last_name$$")');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
