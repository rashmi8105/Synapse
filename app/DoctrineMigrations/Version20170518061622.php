<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-14147 and ESPRJ-14143
 */
class Version20170518061622 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // alter table email_template to hold data upto 100 char length
        $this->addSql('ALTER TABLE email_template CHANGE email_key email_key VARCHAR(100) DEFAULT NULL');

        // update the email_key 'referral_student_made_nonparticipant_assignee' to 'referral_student_made_nonparticipant_current_assignee'
        $this->addSql("SET @emailtemplateId = (SELECT id from email_template where email_key = 'referral_student_made_nonparticipant_assignee')");
        $this->addSql("UPDATE 
                             email_template
                        SET
                             email_key = 'referral_student_made_nonparticipant_current_assignee'
                        WHERE
                             id = @emailtemplateId;");

        // Create records for mapworks_action_variable table
        $student_first_name = '$$student_first_name$$';
        $student_last_name = '$$student_last_name$$';

        // get variable ids from mapworks_action_variable_description
        $this->addSql("SET @studentFirstNameVariableDescriptionId = (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_first_name')");
        $this->addSql("SET @studentLastNameVariableDescriptionId = (SELECT id FROM mapworks_action_variable_description WHERE variable= '$student_last_name')");

        // Insert mapworks_action_variable records for referral_student_made_nonparticipant_current_assignee
        $this->addSql("SET @mapworksActionId = (SELECT id FROM mapworks_action WHERE event_key ='referral_student_made_nonparticipant_current_assignee')");
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

        // Insert mapworks_action_variable records for referral_student_made_nonparticipant_interested_party
        $this->addSql("SET @mapworksActionId = (SELECT id FROM mapworks_action WHERE event_key ='referral_student_made_nonparticipant_interested_party')");
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

        // Insert mapworks_action_variable records for referral_student_made_nonparticipant_creator
        $this->addSql("SET @mapworksActionId = (SELECT id FROM mapworks_action WHERE event_key ='referral_student_made_nonparticipant_creator')");
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
