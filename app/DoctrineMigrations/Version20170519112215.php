<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170519112215 extends AbstractMigration
{
    /**
     * ESPRJ-14142
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $student_first_name = '$$student_first_name$$';
        $student_last_name = '$$student_last_name$$';

        // get variable ids from mapworks_action_variable_description
        $this->addSql("SET @studentFirstNameVariableDescriptionId = (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_first_name')");
        $this->addSql("SET @studentLastNameVariableDescriptionId = (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_last_name')");

        // Insert mapworks_action_variable records for referral_student_made_participant_current_assignee
        $this->addSql("SET @mapworksActionId = (SELECT id FROM mapworks_action WHERE event_key ='referral_student_made_participant_current_assignee')");
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            @mapworksActionId,
                            @studentFirstNameVariableDescriptionId
                        ),
                        (
                            @mapworksActionId,
                            @studentLastNameVariableDescriptionId
                        )
                    ;");

        // Insert mapworks_action_variable records for referral_student_made_participant_interested_party
        $this->addSql("SET @mapworksActionId = (SELECT id FROM mapworks_action WHERE event_key ='referral_student_made_participant_interested_party')");
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            @mapworksActionId,
                            @studentFirstNameVariableDescriptionId
                        ),
                        (
                            @mapworksActionId,
                            @studentLastNameVariableDescriptionId
                        )
                    ;");

        // Insert mapworks_action_variable records for referral_student_made_participant_creator
        $this->addSql("SET @mapworksActionId = (SELECT id FROM mapworks_action WHERE event_key ='referral_student_made_participant_creator')");
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            @mapworksActionId,
                            @studentFirstNameVariableDescriptionId
                        ),
                        (
                            @mapworksActionId,
                            @studentLastNameVariableDescriptionId
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
