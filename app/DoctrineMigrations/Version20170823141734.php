<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15557 - Create Migration script to store/update values in mapworks_action to send the notifications when bulk office hour series is failed.
 */
class Version20170823141734 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // create mapworks_action for bulk_office_hour_failed_creator
        $this->addSql("INSERT INTO `mapworks_action` (`event_key`, `action`, `recipient_type`, `event_type`, `receives_email`, `receives_notification`,`created_by`,`created_at`) VALUES
			('bulk_office_hour_failed_creator','failed','creator','bulk_office_hour',1,1,-25,NOW())");

        // Update mapworks_action table to set notification_hover_text and notification_body_text for event_key = 'bulk_office_hour_failed_creator'
        $eventId = '$$event_id$$';

        $this->addSql("UPDATE mapworks_action
                       SET                            
                           notification_hover_text = 'A calendar sync error occurred',
                           notification_body_text = 'An error occurred while Mapworks was syncing changes to your calendar. Please contact Skyfactor client services. Event id $eventId'
                       WHERE
                           event_key = 'bulk_office_hour_failed_creator';");


        // Insert mapworks_action_variable records for bulk_office_hour_failed_creator
        $eventId = '$$event_id$$';
        $this->addSql("SET @mapworksActionId = (SELECT id FROM mapworks_action WHERE event_key ='bulk_office_hour_failed_creator');");
        $this->addSql("SET @eventId =(SELECT id FROM mapworks_action_variable_description WHERE variable= '$eventId');");
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
		
		
