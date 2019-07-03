<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 *  Migration Script for updating mapworks_action table for referral reopen - ESPRJ-14062
 */
class Version20170511065451 extends AbstractMigration
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

        // Update mapworks_action table to set email_template_id , notification_hover_text and notification_body_text for event_key = 'referral_reopen_current_assignee'
        $this->addSql("UPDATE mapworks_action
                       SET 
                           email_template_id = (SELECT
                                   id
                               FROM
                                   email_template
                               WHERE
                                   email_key = 'referral_reopen_current_assignee'),
                           notification_hover_text = 'Your referral for $student_first_name $student_last_name has been reopened.',
                           notification_body_text = 'Your referral for $student_first_name $student_last_name has been reopened.'
                       WHERE
                           event_key = 'referral_reopen_current_assignee';");

        // Update mapworks_action table to set notification_hover_text and notification_body_text for event_key = 'referral_reopen_interested_party'
        $this->addSql("UPDATE mapworks_action
                        SET 
                            notification_hover_text = 'The referral for $student_first_name $student_last_name has been reopened.',
                            notification_body_text = 'The referral for $student_first_name $student_last_name, which you are an interested party for, has been reopened.'
                        WHERE
                            event_key = 'referral_reopen_interested_party';");

        // Update mapworks_action table to set notification_hover_text and notification_body_text for event_key = 'referral_reopen_reopener'
        $this->addSql("UPDATE mapworks_action
                        SET 
                            notification_hover_text = 'You have reopened the referral for $student_first_name $student_last_name.',
                            notification_body_text = 'You have reopened the referral for $student_first_name $student_last_name.'
                        WHERE
                            event_key = 'referral_reopen_reopener';");

        // Update mapworks_action table to set notification_hover_text and notification_body_text for event_key = 'referral_reopen_creator'
        $this->addSql("UPDATE mapworks_action
                        SET 
                            notification_hover_text = 'Your referral for $student_first_name $student_last_name has been reopened.',
                            notification_body_text = 'Your referral for $student_first_name $student_last_name has been reopened. '
                        WHERE
                            event_key = 'referral_reopen_creator';");
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
