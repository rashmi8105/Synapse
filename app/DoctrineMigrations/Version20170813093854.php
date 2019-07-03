<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15557 - Create Migration script to store/update values in mapworks_action to send the notifications in cronofy jobs.
 */
class Version20170813093854 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // create mapworks_action with sync, unsync, and event creations.
        $this->addSql("INSERT INTO `mapworks_action` (`event_key`, `action`, `recipient_type`, `event_type`, `receives_email`, `receives_notification`,`created_by`,`created_at`) VALUES
			('external_calendar_initialsync','failed','creator','initialsync',1,1,-25,NOW()),
			('external_calendar_unsync','failed','creator','unsync',1,1,-25,NOW()),
			('external_calendar_event_change','failed','creator','event_change',1,1,-25,NOW())");

        // Update mapworks_action table to set notification_hover_text and notification_body_text for event_key = 'external_calendar_initialsync'
        $eventId = '$$event_id$$';

        $this->addSql("UPDATE mapworks_action
                       SET                            
                           notification_hover_text = 'A calendar sync error occurred',
                           notification_body_text = 'An error occurred during sync while connecting Mapworks to your calendar. Please contact Skyfactor client services. Event id $eventId'
                       WHERE
                           event_key = 'external_calendar_initialsync';");

        // Update mapworks_action table to set notification_hover_text and notification_body_text for event_key = 'external_calendar_unsync'
        $this->addSql("UPDATE mapworks_action
                       SET                            
                           notification_hover_text = 'A calendar sync error occurred',
                           notification_body_text = 'A sync error occurred while disconnecting your calendar from Mapworks. Please contact Skyfactor client services. Event id $eventId'
                       WHERE
                           event_key = 'external_calendar_unsync';");

        // Update mapworks_action table to set notification_hover_text and notification_body_text for event_key = 'external_calendar_event_change'
        $this->addSql("UPDATE mapworks_action
                       SET                            
                           notification_hover_text = 'A calendar sync error occurred',
                           notification_body_text = 'An error occurred while Mapworks was syncing changes to your calendar. Please contact Skyfactor client services. Event id $eventId'
                       WHERE
                           event_key = 'external_calendar_event_change';");

        // add $$event_id$$ in mapworks_action_variable_description
        $this->addSql('INSERT INTO mapworks_action_variable_description(variable,description)VALUES ("$$event_id$$","External calendar event id.");');

        // Insert mapworks_action_variable records for external_calendar_initialsync
        $eventId = '$$event_id$$';
        $this->addSql("SET @mapworksActionId = (SELECT id FROM mapworks_action WHERE event_key ='external_calendar_initialsync');");
        $this->addSql("SET @eventId =(SELECT id FROM mapworks_action_variable_description WHERE variable= '$eventId');");
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES                        
                        (
                            @mapworksActionId,
                            @eventId
                        )
                    ;");

        // Insert mapworks_action_variable records for external_calendar_unsync
        $this->addSql("SET @mapworksActionId = (SELECT id FROM mapworks_action WHERE event_key ='external_calendar_unsync');");
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES                        
                        (
                            @mapworksActionId,
                            @eventId
                        )
                    ;");

        // Insert mapworks_action_variable records for external_calendar_event
        $this->addSql("SET @mapworksActionId = (SELECT id FROM mapworks_action WHERE event_key ='external_calendar_event_change');");
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES                        
                        (
                            @mapworksActionId,
                            @eventId
                        )
                    ;");
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
		
		
