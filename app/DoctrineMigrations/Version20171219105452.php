<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15342 -  Migration script for job used for cancelling appointments and sending referral communication when a student is marked non participant
 */
class Version20171219105452 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO `synapse`.`job_type` (`created_by`, `modified_by`, `created_at`, `modified_at`, `job_type`) VALUES ('-25', '-25', now(), now(), 'NonParticipantStudentAppointmentCancellationAndReferralCommunicationsJob')");
        $this->addSql("INSERT INTO `mapworks_action` (`event_key`, `action`, `recipient_type`, `event_type`, `notification_body_text`, `notification_hover_text`, `receives_email`, `receives_notification`, `created_by`,`created_at`,`modified_at`) VALUES ('appointment_cancellation_and_referral_communication_failed_creator', 'failed', 'creator', 'appointment_cancellation_and_referral_communication', 'An error occurred while Mapworks was changing participation status for student. Please contact Skyfactor client services.', 'An error occurred while changing participation status of a student.', '0', '1', '-25',now(),now())");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
