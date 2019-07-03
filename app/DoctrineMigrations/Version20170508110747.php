<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration Script for updating mapworks_action table for referral closer
 * This migration script is for ESPRJ-14061
 */
class Version20170508110747 extends AbstractMigration
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

        // Update mapworks_action table to set email_template_id , notification_hover_text and notification_body_text for event_key = 'referral_close_creator'
        $this->addSql("UPDATE mapworks_action
                       SET 
                           email_template_id = (SELECT 
                                   id
                               FROM
                                   email_template
                               WHERE
                                   email_key = 'Referral_Closed_Creator'),
                           notification_hover_text = 'Closed referral for $student_first_name $student_last_name.',
                           notification_body_text = 'Closed referral for $student_first_name $student_last_name.'
                       WHERE
                           event_key = 'referral_close_creator';");

        // Update mapworks_action table to set email_template_id , notification_hover_text and notification_body_text for event_key = 'referral_close_current_assignee'
        $this->addSql("UPDATE mapworks_action
                        SET 
                            email_template_id = (SELECT 
                                    id
                                FROM
                                    email_template
                                WHERE
                                    email_key = 'Referral_Closed_Assignee'),
                            notification_hover_text = 'Closed referral for $student_first_name $student_last_name.',
                            notification_body_text = 'Closed referral for $student_first_name $student_last_name.'
                        WHERE
                            event_key = 'referral_close_current_assignee';");

        // Update mapworks_action table to set email_template_id , notification_hover_text and notification_body_text for event_key = 'referral_close_interested_party'
        $this->addSql("UPDATE mapworks_action
                        SET 
                            email_template_id = (SELECT 
                                    id
                                FROM
                                    email_template
                                WHERE
                                    email_key = 'Referral_InterestedParties_Staff_Closed'),
                            notification_hover_text = 'Closed referral for $student_first_name $student_last_name.',
                            notification_body_text = 'Closed referral for $student_first_name $student_last_name.'
                        WHERE
                            event_key = 'referral_close_interested_party';");

        // Update mapworks_action table to set notification_hover_text and notification_body_text for event_key = 'referral_close_closer'
        $this->addSql("UPDATE mapworks_action 
                        SET     
                            notification_hover_text = 'You have closed the referral for $student_first_name $student_last_name.',
                            notification_body_text = 'You have closed the referral for $student_first_name $student_last_name.'
                        WHERE
                            event_key = 'referral_close_closer';");
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
