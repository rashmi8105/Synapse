<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15118
 */
class Version20170607072214 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $current_assignee_first_name = '$$current_assignee_first_name$$';
        $date_of_creation = '$$date_of_creation$$';
        $coordinator_first_name = '$$coordinator_first_name$$';
        $coordinator_last_name = '$$coordinator_last_name$$';
        $coordinator_email_address = '$$coordinator_email_address$$';


        // get variable ids from mapworks_action_variable_description
        $this->addSql("SET @current_assignee_first_name_variable_description_id = (SELECT id FROM mapworks_action_variable_description WHERE variable= '$current_assignee_first_name')");
        $this->addSql("SET @date_of_creation_variable_description_id = (SELECT id FROM mapworks_action_variable_description WHERE variable= '$date_of_creation')");
        $this->addSql("SET @coordinator_first_name_variable_description_id = (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_first_name')");
        $this->addSql("SET @coordinator_last_name_variable_description_id = (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_last_name')");
        $this->addSql("SET @coordinator_email_address_variable_description_id = (SELECT id FROM mapworks_action_variable_description WHERE variable= '$coordinator_email_address')");

        // Insert mapworks_action_variable records for referral_student_made_participant_current_assignee
        $this->addSql("SET @mapworks_action_id = (SELECT id FROM mapworks_action WHERE event_key ='referral_student_made_participant_current_assignee')");
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            @mapworks_action_id,
                            @current_assignee_first_name_variable_description_id
                        ),
                        (
                            @mapworks_action_id,
                            @date_of_creation_variable_description_id
                        ),
                        (
                            @mapworks_action_id,
                            @coordinator_first_name_variable_description_id
                        ),
                        (
                            @mapworks_action_id,
                            @coordinator_last_name_variable_description_id
                        ),
                        (
                            @mapworks_action_id,
                            @coordinator_email_address_variable_description_id
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
