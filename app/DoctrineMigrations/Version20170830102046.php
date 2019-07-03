<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15557 - Updating notification_body_text when sync failed.
 */
class Version20170830102046 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Update mapworks_action table to set notification_body_text for event_key = 'calendar_sync_initial_sync_failed_creator'

        $this->addSql("UPDATE mapworks_action
                       SET                      
                           notification_body_text = 'An error occurred during sync while connecting Mapworks to your calendar. Please contact Skyfactor client services.'
                       WHERE
                           event_key = 'calendar_sync_initial_sync_failed_creator';");

        // Update mapworks_action table to set  notification_body_text for event_key = 'calendar_sync_desync_failed_creator'
        $this->addSql("UPDATE mapworks_action
                       SET                 
                           notification_body_text = 'A sync error occurred while disconnecting your calendar from Mapworks. Please contact Skyfactor client services.'
                       WHERE
                           event_key = 'calendar_sync_desync_failed_creator';");

        // Update mapworks_action table to set notification_body_text for event_key = 'calendar_sync_event_sync_failed_creator'
        $this->addSql("UPDATE mapworks_action
                       SET                
                           notification_body_text = 'An error occurred while Mapworks was syncing changes to your calendar. Please contact Skyfactor client services.'
                       WHERE
                           event_key = 'calendar_sync_event_sync_failed_creator';");

        // Update mapworks_action table to set notification_body_text for event_key = 'bulk_office_hour_failed_creator'
        $this->addSql("UPDATE mapworks_action
                       SET           
                           notification_body_text = 'An error occurred while Mapworks was syncing changes to your calendar. Please contact Skyfactor client services.'
                       WHERE
                           event_key = 'bulk_office_hour_failed_creator';");

        // Update mapworks_action table to set notification_body_text for event_key = 'calender_sync_organization_sync_failed_creator'
        $this->addSql("UPDATE mapworks_action
                       SET                  
                           notification_body_text = 'An error occurred during sync while connecting Mapworks to your calendar. Please contact Skyfactor client services.'
                       WHERE
                           event_key = 'calender_sync_organization_sync_failed_creator';");
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
		
		
