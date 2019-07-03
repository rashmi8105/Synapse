<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15065 - Migration script for updating notifications for current assignee
 */
class Version20170601122100 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        //variable declaration
        $student_first_name = '$$student_first_name$$';
        $student_last_name = '$$student_last_name$$';

        // Update mapworks_action table to update notification_hover_text and notification_body_text for event_key = 'referral_reopen_current_assignee'
        $this->addSql("UPDATE mapworks_action
                       SET                          
                           notification_hover_text = 'The referral for $student_first_name $student_last_name has been reopened.',
                           notification_body_text = 'The referral for $student_first_name $student_last_name has been reopened.'
                       WHERE
                           event_key = 'referral_reopen_current_assignee';");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
