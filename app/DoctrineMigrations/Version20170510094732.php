<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * This migration script inserts records into mapworks_action_variable table which will be used for adding an interested party.
 * Jira ticket no ESPRJ-14140
 */
class Version20170510094732 extends AbstractMigration
{
    /**
     * Migration script to insert mapworks_action_variable records for adding an interested party.
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        //variable declaration
        $interested_party_first_name = '$$interested_party_first_name$$';
        $student_first_name = '$$student_first_name$$';
        $student_last_name = '$$student_last_name$$';
        $date_of_creation = '$$date_of_creation$$';
        $coordinator_first_name = '$$coordinator_first_name$$';
        $coordinator_last_name = '$$coordinator_last_name$$';
        $coordinator_email_address = '$$coordinator_email_address$$';
        $updater_first_name = '$$updater_first_name$$';
        $updater_last_name = '$$updater_last_name$$';
        $skyfactor_mapworks_logo = '$$Skyfactor_Mapworks_logo$$';

        // Insert mapworks_action_variable records for adding an interested party
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_add_interested_party_interested_party'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$interested_party_first_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_add_interested_party_interested_party'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_first_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_add_interested_party_interested_party'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_last_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_add_interested_party_interested_party'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$date_of_creation')
                        ),
                         (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_add_interested_party_interested_party'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_first_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_add_interested_party_interested_party'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_last_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_add_interested_party_interested_party'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_email_address')
                        ),
                         (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_add_interested_party_interested_party'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$updater_first_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_add_interested_party_interested_party'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$updater_last_name')
                        ),
                         (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_add_interested_party_interested_party'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$skyfactor_mapworks_logo')
                        )
                    ;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
