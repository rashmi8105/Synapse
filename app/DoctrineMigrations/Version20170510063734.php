<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script to insert mapworks_action_variable records for closing a referral - ESPRJ-14138
 */
class Version20170510063734 extends AbstractMigration
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
        $current_assignee_last_name = '$$current_assignee_last_name$$';
        $student_first_name = '$$student_first_name$$';
        $student_last_name = '$$student_last_name$$';
        $coordinator_first_name = '$$coordinator_first_name$$';
        $coordinator_last_name = '$$coordinator_last_name$$';
        $coordinator_title = '$$coordinator_title$$';
        $coordinator_email_address = '$$coordinator_email_address$$';
        $skyfactor_mapworks_logo = '$$Skyfactor_Mapworks_logo$$';
        $date_of_creation = '$$date_of_creation$$';
        $interested_party_first_name = '$$interested_party_first_name$$';
        $interested_party_last_name = '$$interested_party_last_name$$';
        $creator_first_name = '$$creator_first_name$$';
        $creator_last_name = '$$creator_last_name$$';


        // Insert mapworks_action_variable records for closing a referral for current assignee
        $this->addSql("SET @mapworksActionId = (SELECT id FROM mapworks_action WHERE event_key = 'referral_close_current_assignee')");
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$current_assignee_first_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$current_assignee_last_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_first_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_last_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_first_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_last_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_title')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_email_address')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$skyfactor_mapworks_logo')
                        )
                    ;");

        // Insert mapworks_action_variable records for closing a referral for interested party
        $this->addSql("SET @mapworksActionId = (SELECT id FROM mapworks_action WHERE event_key = 'referral_close_interested_party')");
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$interested_party_first_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$interested_party_last_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_first_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_last_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_first_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_last_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_title')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_email_address')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$skyfactor_mapworks_logo')
                        )
                    ;");

        // Insert mapworks_action_variable records for closing a referral for creator
        $this->addSql("SET @mapworksActionId = (SELECT id FROM mapworks_action WHERE event_key = 'referral_close_creator')");
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$creator_first_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$creator_last_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_first_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_last_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$date_of_creation')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_first_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_last_name')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_title')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_email_address')
                        ),
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$skyfactor_mapworks_logo')
                        )          
                    ;");

        // Insert mapworks_action_variable records for closing a referral for closer
        $this->addSql("SET @mapworksActionId = (SELECT id FROM mapworks_action WHERE event_key = 'referral_close_closer')");
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            @mapworksActionId,
                            (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_first_name')
                        ),
                        (
                            @mapworksActionId,
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
