<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration Script to insert mapworks_action_variable table for create bulk referral
 * This migration script is for ESPRJ-14135
 */
class Version20170518182854 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $skyfactor_mapworks_logo = '$$Skyfactor_Mapworks_logo$$';
        $date_of_creation = '$$date_of_creation$$';
        $coordinator_first_name = '$$coordinator_first_name$$';
        $coordinator_last_name = '$$coordinator_last_name$$';
        $coordinator_email_address = '$$coordinator_email_address$$';
        $current_assignee_first_name = '$$current_assignee_first_name$$';
        $interested_party_first_name = '$$interested_party_first_name$$';
        $referral_student_count = '$$referral_student_count$$';
        $creator_first_name = '$$creator_first_name$$';
        $creator_last_name = '$$creator_last_name$$';

        // Variables for referral_bulk_action_current_assignee
        $this->addSql(" SET @mapworksActionIdForAssignee = (SELECT id FROM mapworks_action where event_key ='referral_bulk_action_current_assignee')");
        $this->addSql(" INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) 
                            VALUES 
                            (
                                (@mapworksActionIdForAssignee),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$current_assignee_first_name')
                            ),
                            (
                                (@mapworksActionIdForAssignee),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$referral_student_count')
                            ),
                            (
                                (@mapworksActionIdForAssignee),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$skyfactor_mapworks_logo')
                            ),
                            (
                                (@mapworksActionIdForAssignee),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$creator_first_name')
                            ),
                            (
                                (@mapworksActionIdForAssignee),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$creator_last_name')
                            );"
        );

        // Variables for referral_bulk_action_interested_party
        $this->addSql(" SET @mapworksActionIdForInterestedParty = (SELECT id FROM mapworks_action where event_key ='referral_bulk_action_interested_party')");
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) 
                            VALUES 
                            (
                                (@mapworksActionIdForInterestedParty),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$interested_party_first_name')
                            ),
                            (
                                (@mapworksActionIdForInterestedParty),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$date_of_creation')
                            ),
                            (
                                (@mapworksActionIdForInterestedParty),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$coordinator_first_name')
                            ),
                            (
                                (@mapworksActionIdForInterestedParty),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$coordinator_last_name')
                            ),
                            (
                                (@mapworksActionIdForInterestedParty),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$coordinator_email_address')
                            ),                            
                            (
                                (@mapworksActionIdForInterestedParty),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$skyfactor_mapworks_logo')
                            ),
                            (
                                (@mapworksActionIdForInterestedParty),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$creator_first_name')
                            ),
                            (
                                (@mapworksActionIdForInterestedParty),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$creator_last_name')
                            ),
                            (
                                (@mapworksActionIdForInterestedParty),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$referral_student_count')
                            );"
        );

        // Variables for referral_bulk_action_creator
        $this->addSql(" SET @mapworksActionIdForCreator = (SELECT id FROM mapworks_action where event_key ='referral_bulk_action_creator')");
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) 
                            VALUES 
                            (
                                (@mapworksActionIdForCreator),
                                (SELECT id FROM mapworks_action_variable_description WHERE variable = '$referral_student_count')
                            );"
        );

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
