<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15557 - Migration script updating email_template_id in mapworks_action table for bulk_office_hour_failed_creator and calender_sync_organization_sync_failed_creator
 * Updating mapworks_action table with the new values
 */
class Version20170825125539 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // Update mapworks_action table to set email_template_id for bulk_office_hour_failed_creator
        $this->addSql("UPDATE mapworks_action
	   SET 
		   email_template_id = (SELECT id FROM email_template WHERE email_key = 'bulk_office_hour_failed_creator')                           
	   WHERE
		   event_key = 'bulk_office_hour_failed_creator'");

        // Update mapworks_action table to set email_template_id for calender_sync_organization_sync_failed_creator
        $this->addSql("UPDATE mapworks_action
			   SET 
				   email_template_id = (SELECT id FROM email_template WHERE email_key = 'calender_sync_organization_sync_failed_creator')                           
			   WHERE
				   event_key = 'calender_sync_organization_sync_failed_creator'");

        // Rename event_key, action, event_type for external_calendar_initialsync
        $this->addSql("UPDATE mapworks_action 
				SET 
					event_key = 'calendar_sync_initial_sync_failed_creator',
					action = 'initial_sync_failed',	
					recipient_type = 'creator',
					event_type = 'calendar_sync'
				WHERE
					event_key = 'external_calendar_initialsync'");
        // Rename email_key for external_calendar_initialsync
        $this->addSql("UPDATE email_template 
				SET 
					email_key = 'calendar_sync_initial_sync_failed_creator'
				WHERE
					email_key = 'external_calendar_initialsync'");

        // Rename event_key, action, event_type for external_calendar_event_change
        $this->addSql("update mapworks_action 
				SET 
					event_key = 'calendar_sync_event_sync_failed_creator',
					action = 'event_sync_failed',
					recipient_type = 'creator',
					event_type = 'calendar_sync'
				WHERE
					event_key = 'external_calendar_event_change'");
        // Rename email_key for external_calendar_event_change
        $this->addSql("UPDATE email_template 
				SET 
					email_key = 'calendar_sync_event_sync_failed_creator'
				WHERE
					email_key = 'external_calendar_event_change'");


        // Rename event_key, action, event_type for calendar_sync_desync_failed_creator
        $this->addSql("UPDATE mapworks_action 
				SET 
					event_key = 'calendar_sync_desync_failed_creator',
					action = 'desync_failed',
					recipient_type = 'creator',
					event_type = 'calendar_sync'
				WHERE
					event_key = 'external_calendar_unsync'");

        // Rename email_key for external_calendar_unsync
        $this->addSql("UPDATE email_template 
				SET 
					email_key = 'calendar_sync_desync_failed_creator'
				WHERE
					email_key = 'external_calendar_unsync'");
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