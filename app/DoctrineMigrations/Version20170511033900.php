<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-14136
 *
 * Migration script to insert mapworks_action_variable for the event_key
 * referral_update_content_current_assignee
 * referral_update_content_creator
 * referral_update_content_updater
 * referral_update_content_interested_party
 */
class Version20170511033900 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //Variable declaration
        $student_first_name = '$$student_first_name$$';
        $student_last_name = '$$student_last_name$$';
        $updater_first_name = '$$updater_first_name$$';
        $updater_last_name = '$$updater_last_name$$';

        //Insert mapworks_action_variable for the event_key  referral_update_content_current_assignee
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) 
                            VALUES 
                            (
                                (SELECT id FROM mapworks_action where event_key ='referral_update_content_current_assignee'),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$student_first_name')
                            ),
                            (
                                (SELECT id FROM mapworks_action where event_key ='referral_update_content_current_assignee'),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$student_last_name')
                            ),
                            (
                                (SELECT id FROM mapworks_action where event_key ='referral_update_content_current_assignee'),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$updater_first_name')
                            ),
                            (
                                (SELECT id FROM mapworks_action where event_key ='referral_update_content_current_assignee'),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$updater_last_name')
                            );");

        //Insert mapworks_action_variable for the event_key referral_update_content_creator
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) 
                            VALUES 
                            (
                                (SELECT id FROM mapworks_action where event_key ='referral_update_content_creator'),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$student_first_name')
                            ),
                            (
                                (SELECT id FROM mapworks_action where event_key ='referral_update_content_creator'),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$student_last_name')
                            ),
                            (
                                (SELECT id FROM mapworks_action where event_key ='referral_update_content_creator'),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$updater_first_name')
                            ),
                            (
                                (SELECT id FROM mapworks_action where event_key ='referral_update_content_creator'),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$updater_last_name')
                            );");

        //Insert mapworks_action_variable for the event_key referral_update_content_updater
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) 
                            VALUES 
                            (
                                (SELECT id FROM mapworks_action where event_key ='referral_update_content_updater'),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$student_first_name')
                            ),
                            (
                                (SELECT id FROM mapworks_action where event_key ='referral_update_content_updater'),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$student_last_name')
                            ),
                            (
                                (SELECT id FROM mapworks_action where event_key ='referral_update_content_updater'),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$updater_first_name')
                            ),
                            (
                                (SELECT id FROM mapworks_action where event_key ='referral_update_content_updater'),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$updater_last_name')
                            );");

        //Insert mapworks_action_variable for the event_key referral_update_content_interested_party
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) 
                            VALUES 
                            (
                                (SELECT id FROM mapworks_action where event_key ='referral_update_content_interested_party'),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$student_first_name')
                            ),
                            (
                                (SELECT id FROM mapworks_action where event_key ='referral_update_content_interested_party'),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$student_last_name')
                            ),
                            (
                                (SELECT id FROM mapworks_action where event_key ='referral_update_content_interested_party'),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$updater_first_name')
                            ),
                            (
                                (SELECT id FROM mapworks_action where event_key ='referral_update_content_interested_party'),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$updater_last_name')
                            );");
    }


    public function down(Schema $schema)
    {

    }
}
