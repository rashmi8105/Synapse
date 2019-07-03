<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170518085015 extends AbstractMigration
{
    /**
     * ESPRJ-14065
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $student_first_name = '$$student_first_name$$';
        $student_last_name = '$$student_last_name$$';

        //update mapworks action for referral_student_made_participant_current_assignee
        $this->addSql("UPDATE 
                             mapworks_action
                        SET
                             email_template_id = (SELECT id FROM email_template WHERE email_key = 'referral_reopen_current_assignee'),
                             notification_hover_text = 'Your referral for $student_first_name $student_last_name has been reopened.',
                             notification_body_text = 'Your referral for $student_first_name $student_last_name has been reopened.'
                        WHERE
                             event_key = 'referral_student_made_participant_current_assignee';");

        //update mapworks action for referral_student_made_participant_interested_party
        $this->addSql("UPDATE 
                             mapworks_action
                        SET
                             notification_hover_text = 'Your referral for $student_first_name $student_last_name has been reopened.',
                             notification_body_text = 'Your referral for $student_first_name $student_last_name has been reopened.'
                        WHERE
                             event_key = 'referral_student_made_participant_interested_party';");

        //update mapworks action for referral_student_made_participant_creator
        $this->addSql("UPDATE 
                             mapworks_action
                        SET
                             notification_hover_text = 'Your referral for $student_first_name $student_last_name has been reopened.',
                             notification_body_text = 'Your referral for $student_first_name $student_last_name has been reopened.'
                        WHERE
                             event_key = 'referral_student_made_participant_creator';");
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
