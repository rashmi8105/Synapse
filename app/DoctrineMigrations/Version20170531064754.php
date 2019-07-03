<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15062 - Migration script for updating notifications for students
 */
class Version20170531064754 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $creator_first_name = '$$creator_first_name$$';
        $creator_last_name = '$$creator_last_name$$';
        $coordinator_first_name = '$$coordinator_first_name$$';
        $coordinator_last_name = '$$coordinator_last_name$$';
        $coordinator_email_address = '$$coordinator_email_address$$';

        
        $this->addSql("UPDATE `synapse`.`mapworks_action` 
                            SET 
                                `notification_body_text` = '$creator_first_name $creator_last_name has referred you to a campus resource in Mapworks. If you have any questions, please contact $coordinator_first_name $coordinator_last_name at $coordinator_email_address.',
                                `notification_hover_text` = '$creator_first_name $creator_last_name has referred you to a campus resource.',
                                `receives_notification` = '1'
                            WHERE
                                `event_key` = 'referral_create_student';
");


        $this->addSql('SET @mapworksActionId = (SELECT id FROM mapworks_action WHERE event_key = "referral_create_student")');
        $this->addSql('SET @creatorFirstNameId = (SELECT id FROM mapworks_action_variable_description WHERE variable = "$$creator_first_name$$")');
        $this->addSql('SET @creatorLastNameId = (SELECT id FROM mapworks_action_variable_description WHERE variable = "$$creator_last_name$$")');

        $this->addSql('INSERT INTO `mapworks_action_variable` (`mapworks_action_id`, `mapworks_action_variable_description_id`) VALUES (@mapworksActionId, @creatorFirstNameId)');
        $this->addSql('INSERT INTO `mapworks_action_variable` (`mapworks_action_id`, `mapworks_action_variable_description_id`) VALUES (@mapworksActionId, @creatorlastNameId )');



    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
