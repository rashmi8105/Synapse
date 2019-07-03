<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-14060
 *
 * Migration script for updating mapworks_action fields email_template_id, notification_hover_text and notification_body_text
 * for below keys
 *
 * referral_reassign_current_assignee
 * referral_reassign_creator
 * referral_reassign_updater
 * referral_reassign_previous_assignee
 */
class Version20170512025600 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //variable declaration
        $student_first_name = '$$student_first_name$$';
        $student_last_name = '$$student_last_name$$';
        $updater_first_name = '$$updater_first_name$$';
        $updater_last_name = '$$updater_last_name$$';

        // Update mapworks_action table to set email_template_id, notification_hover_text and notification_body_text for key 'referral_reassign_current_assignee'
        $this->addSql("UPDATE mapworks_action
                       SET 
                           email_template_id = (SELECT id FROM email_template WHERE email_key = 'Referral_Assign_to_staff'),
                           notification_hover_text = '$updater_first_name $updater_last_name has assigned you a new referral.',
                           notification_body_text = '$updater_first_name $updater_last_name has assigned you a new referral.'
                       WHERE
                           event_key = 'referral_reassign_current_assignee';");

        // Update mapworks_action table to set notification_hover_text and notification_body_text for key 'referral_reassign_creator'
        $this->addSql("UPDATE mapworks_action
                       SET                            
                           notification_hover_text = '$updater_first_name $updater_last_name updated the referral for $student_first_name $student_last_name.',
                           notification_body_text = '$updater_first_name $updater_last_name updated the referral for $student_first_name $student_last_name.'
                       WHERE
                           event_key = 'referral_reassign_creator';");

        // Update mapworks_action table to set notification_hover_text and notification_body_text for key 'referral_reassign_updater'
        $this->addSql("UPDATE mapworks_action
                       SET                            
                           notification_hover_text = 'You have updated the referral for $student_first_name $student_last_name.',
                           notification_body_text = 'You have updated the referral for $student_first_name $student_last_name.'
                       WHERE
                           event_key = 'referral_reassign_updater';");

        // Update mapworks_action table to set notification_hover_text and notification_body_text for key 'referral_reassign_previous_assignee'
        $this->addSql("UPDATE mapworks_action
                       SET                            
                           notification_hover_text = '$updater_first_name $updater_last_name removed you as the assignee of a referral.',
                           notification_body_text = '$updater_first_name $updater_last_name removed you as the assignee of a referral for $student_first_name $student_last_name.'
                       WHERE
                           event_key = 'referral_reassign_previous_assignee';");
    }


    public function down(Schema $schema)
    {

    }
}
