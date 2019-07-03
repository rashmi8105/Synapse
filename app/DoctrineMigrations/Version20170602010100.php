<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15065 - Migration script for updating notifications text for interested parties
 */
class Version20170602010100 extends AbstractMigration
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

        // Update mapworks_action table to update notification_hover_text and notification_body_text for event_key = 'referral_reopen_interested_party'
        $this->addSql("UPDATE mapworks_action
                       SET                          
                           notification_hover_text = 'Your referral for $student_first_name $student_last_name has been reopened.',
                           notification_body_text = 'Your referral for $student_first_name $student_last_name, which you are an interested party for, has been reopened.'
                       WHERE
                           event_key = 'referral_reopen_interested_party';");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
