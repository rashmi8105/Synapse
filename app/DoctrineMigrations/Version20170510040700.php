<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170510040700 extends AbstractMigration
{
    /**
     * Jira Ticket - ESPRJ-14059
     * 
     * Migration script for updating Mapworks Action Table with updating referral content information
     *
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

        // Update mapworks_action table to set notification_hover_text and notification_body_text for event_key = 'referral_update_content_current_assignee'
        $this->addSql("UPDATE mapworks_action
                       SET                            
                           notification_hover_text = 'The referral for $student_first_name $student_last_name has been updated.',
                           notification_body_text = '$updater_first_name $updater_last_name has updated the referral for $student_first_name $student_last_name.'
                       WHERE
                           event_key = 'referral_update_content_current_assignee';");

        // Update mapworks_action table to set notification_hover_text and notification_body_text for event_key = 'referral_update_content_creator'
        $this->addSql("UPDATE mapworks_action
                       SET                            
                           notification_hover_text = 'The referral for $student_first_name $student_last_name has been updated.',
                           notification_body_text = '$updater_first_name $updater_last_name has updated the referral for $student_first_name $student_last_name.'
                       WHERE
                           event_key = 'referral_update_content_creator';");

        // Update mapworks_action table to set notification_hover_text and notification_body_text for event_key = 'referral_update_content_updater'
        $this->addSql("UPDATE mapworks_action
                       SET                            
                           notification_hover_text = 'The referral for $student_first_name $student_last_name has been updated.',
                           notification_body_text = '$updater_first_name $updater_last_name has updated the referral for $student_first_name $student_last_name.'
                       WHERE
                           event_key = 'referral_update_content_updater';");

        // Update mapworks_action table to set notification_hover_text and notification_body_text for event_key = 'referral_update_content_interested_party'
        $this->addSql("UPDATE mapworks_action
                       SET                            
                           notification_hover_text = 'The referral for $student_first_name $student_last_name has been updated.',
                           notification_body_text = '$updater_first_name $updater_last_name has updated the referral for $student_first_name $student_last_name.'
                       WHERE
                           event_key = 'referral_update_content_interested_party';");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
