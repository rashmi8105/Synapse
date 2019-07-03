<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15253 -  Migration script for populating mapworks_action_id and mapworks_action_variable_description_id  into mapworks_action_variable table for the events "referral_student_made_nonparticipant_current_assignee" AND "referral_student_made_nonparticipant_creator"
 */
class Version20170619061541 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs



        $this->addSql('SET @referral_student_made_nonparticipant_current_assignee := (SELECT id FROM mapworks_action WHERE event_key = "referral_student_made_nonparticipant_current_assignee")');
        $this->addSql('SET @referral_student_made_nonparticipant_creator := (SELECT id FROM mapworks_action where event_key = "referral_student_made_nonparticipant_creator")');

        $this->addSql('SET @current_assignee_first_name := (SELECT id FROM mapworks_action_variable_description WHERE  variable = "$$current_assignee_first_name$$")');
        $this->addSql('SET @date_of_creation := (SELECT id FROM mapworks_action_variable_description WHERE  variable = "$$date_of_creation$$")');;
        $this->addSql('SET @coordinator_first_name := (SELECT id FROM mapworks_action_variable_description WHERE  variable = "$$coordinator_first_name$$")');
        $this->addSql('SET @coordinator_last_name := (SELECT id FROM mapworks_action_variable_description WHERE  variable = "$$coordinator_last_name$$")');
        $this->addSql('SET @coordinator_email_address := (SELECT id FROM mapworks_action_variable_description WHERE  variable = "$$coordinator_email_address$$")');
        $this->addSql('SET @Skyfactor_Mapworks_logo := (SELECT id FROM mapworks_action_variable_description WHERE  variable = "$$Skyfactor_Mapworks_logo$$")');
        $this->addSql('SET @creator_first_name := (SELECT id FROM mapworks_action_variable_description WHERE  variable = "$$creator_first_name$$")');


        $this->addSql("INSERT INTO `mapworks_action_variable` (`mapworks_action_id`, `mapworks_action_variable_description_id`) VALUES (@referral_student_made_nonparticipant_current_assignee, @current_assignee_first_name)");
        $this->addSql("INSERT INTO `mapworks_action_variable` (`mapworks_action_id`, `mapworks_action_variable_description_id`) VALUES (@referral_student_made_nonparticipant_current_assignee, @date_of_creation)");
        $this->addSql("INSERT INTO `mapworks_action_variable` (`mapworks_action_id`, `mapworks_action_variable_description_id`) VALUES (@referral_student_made_nonparticipant_current_assignee, @coordinator_first_name)");
        $this->addSql("INSERT INTO `mapworks_action_variable` (`mapworks_action_id`, `mapworks_action_variable_description_id`) VALUES (@referral_student_made_nonparticipant_current_assignee, @coordinator_last_name)");
        $this->addSql("INSERT INTO `mapworks_action_variable` (`mapworks_action_id`, `mapworks_action_variable_description_id`) VALUES (@referral_student_made_nonparticipant_current_assignee, @coordinator_email_address)");
        $this->addSql("INSERT INTO `mapworks_action_variable` (`mapworks_action_id`, `mapworks_action_variable_description_id`) VALUES (@referral_student_made_nonparticipant_current_assignee, @Skyfactor_Mapworks_logo)");
        
        $this->addSql("INSERT INTO `mapworks_action_variable` (`mapworks_action_id`, `mapworks_action_variable_description_id`) VALUES (@referral_student_made_nonparticipant_creator, @date_of_creation)");
        $this->addSql("INSERT INTO `mapworks_action_variable` (`mapworks_action_id`, `mapworks_action_variable_description_id`) VALUES (@referral_student_made_nonparticipant_creator, @coordinator_first_name)");
        $this->addSql("INSERT INTO `mapworks_action_variable` (`mapworks_action_id`, `mapworks_action_variable_description_id`) VALUES (@referral_student_made_nonparticipant_creator, @coordinator_last_name)");
        $this->addSql("INSERT INTO `mapworks_action_variable` (`mapworks_action_id`, `mapworks_action_variable_description_id`) VALUES (@referral_student_made_nonparticipant_creator, @coordinator_email_address)");
        $this->addSql("INSERT INTO `mapworks_action_variable` (`mapworks_action_id`, `mapworks_action_variable_description_id`) VALUES (@referral_student_made_nonparticipant_creator, @Skyfactor_Mapworks_logo)");
        $this->addSql("INSERT INTO `mapworks_action_variable` (`mapworks_action_id`, `mapworks_action_variable_description_id`) VALUES (@referral_student_made_nonparticipant_creator, @creator_first_name)");


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
