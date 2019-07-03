<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script to insert mapworks_action_variable records for reopening a referral - ESPRJ-14139
 */
class Version20170515093252 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //variable declaration
        $current_assignee_first_name = '$$current_assignee_first_name$$';
        $student_first_name = '$$student_first_name$$';
        $student_last_name = '$$student_last_name$$';
        $date_of_creation = '$$date_of_creation$$';
        $coordinator_first_name = '$$coordinator_first_name$$';
        $coordinator_last_name = '$$coordinator_last_name$$';
        $coordinator_email_address = '$$coordinator_email_address$$';
        $Skyfactor_Mapworks_logo = '$$Skyfactor_Mapworks_logo$$';

        // Insert mapworks_action_variable records for reopening a referral for current assignee
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reopen_current_assignee'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$current_assignee_first_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reopen_current_assignee'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_first_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reopen_current_assignee'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_last_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reopen_current_assignee'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$date_of_creation')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reopen_current_assignee'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_first_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reopen_current_assignee'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_last_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reopen_current_assignee'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_email_address')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reopen_current_assignee'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$Skyfactor_Mapworks_logo')
                        )
                    ;");

        // Insert mapworks_action_variable records for reopening a referral for creator
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reopen_creator'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_first_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reopen_creator'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_last_name')
                        )
                    ;");

        // Insert mapworks_action_variable records for reopening a referral for re-opener
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reopen_reopener'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_first_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reopen_reopener'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_last_name')
                        )
                    ;");

        // Insert mapworks_action_variable records for reopening a referral for interested party
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reopen_interested_party'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_first_name')
                        ),
                        (
                            (SELECT id FROM mapworks_action WHERE event_key ='referral_reopen_interested_party'),
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_last_name')
                        )
                    ;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');


    }
}
