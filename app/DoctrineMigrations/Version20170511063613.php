<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * This migration script inserts records into mapworks_action_variable for updating the assignee of a referral for different event key
 * Jira ticket no ESPRJ-14137
 *
 */
class Version20170511063613 extends AbstractMigration
{
    /**
     * Create migration script to insert mapworks_action_variable records for updating the assignee of a referral
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        //variable declaration
        $updater_first_name = '$$updater_first_name$$';
        $updater_last_name = '$$updater_last_name$$';
        $current_assignee_first_name = '$$current_assignee_first_name$$';
        $student_first_name = '$$student_first_name$$';
        $student_last_name = '$$student_last_name$$';
        $skyfactor_mapworks_logo = '$$Skyfactor_Mapworks_logo$$';

        // Insert mapworks_action_variable records for updating the assignee of a referral
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reassign_current_assignee'),
                            (SELECT id FROM mapworks_action_variable_description mavd WHERE variable= '$updater_first_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reassign_current_assignee'),
                            (SELECT id FROM mapworks_action_variable_description mavd WHERE variable= '$updater_last_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reassign_current_assignee'),
                            (SELECT id FROM mapworks_action_variable_description mavd WHERE variable= '$current_assignee_first_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reassign_current_assignee'),
                            (SELECT id FROM mapworks_action_variable_description mavd WHERE variable= '$student_first_name')
                        ),
                         (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reassign_current_assignee'),
                            (SELECT id FROM mapworks_action_variable_description mavd WHERE variable= '$student_last_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reassign_current_assignee'),
                            (SELECT id FROM mapworks_action_variable_description mavd WHERE variable= '$skyfactor_mapworks_logo')
                        )
                    ;");

        // Insert mapworks_action_variable records for referral creator
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reassign_creator'),
                            (SELECT id FROM mapworks_action_variable_description mavd WHERE variable= '$updater_first_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reassign_creator'),
                            (SELECT id FROM mapworks_action_variable_description mavd WHERE variable= '$updater_last_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reassign_creator'),
                            (SELECT id FROM mapworks_action_variable_description mavd WHERE variable= '$student_first_name')
                        ),
                         (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reassign_creator'),
                            (SELECT id FROM mapworks_action_variable_description mavd WHERE variable= '$student_last_name')
                        )
                    ;");

        // Insert mapworks_action_variable records for updater who re-assigned the referral
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reassign_updater'),
                            (SELECT id FROM mapworks_action_variable_description mavd WHERE variable= '$student_first_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reassign_updater'),
                            (SELECT id FROM mapworks_action_variable_description mavd WHERE variable= '$student_last_name')
                        )
                    ;");


        // Insert mapworks_action_variable records for updating the assignee of a referral for previous assignee
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reassign_previous_assignee'),
                            (SELECT id FROM mapworks_action_variable_description mavd WHERE variable= '$updater_first_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reassign_previous_assignee'),
                            (SELECT id FROM mapworks_action_variable_description mavd WHERE variable= '$updater_last_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reassign_previous_assignee'),
                            (SELECT id FROM mapworks_action_variable_description mavd WHERE variable= '$student_first_name')
                        ),
                         (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reassign_previous_assignee'),
                            (SELECT id FROM mapworks_action_variable_description mavd WHERE variable= '$student_last_name')
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
