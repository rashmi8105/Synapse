<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15557 - Migration script for email templates to send while cronofy related jobs are failed.
 */
class Version20170813104645 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        //external_calendar_initialsync - failure happens during initial calendar sync
        $eventId = '$$event_id$$';
        $this->addSql("INSERT INTO `synapse`.`email_template` (`email_key`, `from_email_address`, `is_active`) VALUES ('external_calendar_initialsync', 'no-reply@mapworks.com', '1')");
        $this->addSql("SET @emailtemplateId = (SELECT id from email_template where email_key = 'external_calendar_initialsync')");
        $query = <<<SQL
INSERT INTO `email_template_lang`
SET
    `body` = '
<html>
	<head>
		<style>
			body {
				background: none repeat scroll 0 0 #f4f4f4;
			}
			table {
				padding: 21px;
				width: 799px;
				font-family: helvetica,arial,verdana,san-serif;
				font-size:13px;
				color:#333;
			}
		</style>
	</head>
	<body>
		<table cellpadding="10" style="background:#eeeeee;" cellspacing="0">
			<tbody>
				<tr style="background:#fff;border-collapse:collapse;"><td>An error occurred during sync while connecting Mapworks to your calendar. Please contact Skyfactor client services. Event id $eventId</td></tr>
			</tbody>
		</table>
	</body>
</html>' ,
language_id = 1,
email_template_id = @emailtemplateId,
subject = 'Mapworks calendar sync error'
SQL;
        $this->addSql($query);


        // external_calendar_unsync - failure happens during calendar UNsync
        $this->addSql("INSERT INTO `synapse`.`email_template` (`email_key`, `from_email_address`, `is_active`) VALUES ('external_calendar_unsync', 'no-reply@mapworks.com', '1')");
        $this->addSql("SET @emailtemplateId = (SELECT id from email_template where email_key = 'external_calendar_unsync')");
        $query = <<<SQL
INSERT INTO `email_template_lang`
SET
    `body` = '
<html>
	<head>
		<style>
			body {
				background: none repeat scroll 0 0 #f4f4f4;
			}
			table {
				padding: 21px;
				width: 799px;
				font-family: helvetica,arial,verdana,san-serif;
				font-size:13px;
				color:#333;
			}
		</style>
	</head>
	<body>
		<table cellpadding="10" style="background:#eeeeee;" cellspacing="0">
			<tbody>
				<tr style="background:#fff;border-collapse:collapse;"><td>A sync error occurred while disconnecting your calendar from Mapworks. Please contact Skyfactor client services. Event id $eventId</td></tr>
			</tbody>
		</table>
	</body>
</html>' ,
language_id = 1,
email_template_id = @emailtemplateId,
subject = 'Mapworks calendar sync error'
SQL;
        $this->addSql($query);


        // external_calendar_event - failure happens during a "one-off" (create/edit/delete of appointment/office hour/series)
        $this->addSql("INSERT INTO `synapse`.`email_template` (`email_key`, `from_email_address`, `is_active`) VALUES ('external_calendar_event_change', 'no-reply@mapworks.com', '1')");
        $this->addSql("SET @emailtemplateId = (SELECT id from email_template where email_key = 'external_calendar_event_change')");
        $query = <<<SQL
INSERT INTO `email_template_lang`
SET
    `body` = '
<html>
	<head>
		<style>
			body {
				background: none repeat scroll 0 0 #f4f4f4;
			}
			table {
				padding: 21px;
				width: 799px;
				font-family: helvetica,arial,verdana,san-serif;
				font-size:13px;
				color:#333;
			}
		</style>
	</head>
	<body>
		<table cellpadding="10" style="background:#eeeeee;" cellspacing="0">
			<tbody>
				<tr style="background:#fff;border-collapse:collapse;"><td>An error occurred while Mapworks was syncing changes to your calendar. Please contact Skyfactor client services. Event id $eventId</td></tr>
			</tbody>
		</table>
	</body>
</html>' ,
language_id = 1,
email_template_id = @emailtemplateId,
subject = 'Mapworks calendar sync error'
SQL;
        $this->addSql($query);


        // Update mapworks_action table to set email_template_id for external_calendar_initialsync
        $this->addSql("UPDATE mapworks_action
                       SET 
                           email_template_id = (SELECT id FROM email_template WHERE email_key = 'external_calendar_initialsync')                           
                       WHERE
                           event_key = 'external_calendar_initialsync';");

        // Update mapworks_action table to set email_template_id for external_calendar_unsync
        $this->addSql("UPDATE mapworks_action
                       SET 
                           email_template_id = (SELECT id FROM email_template WHERE email_key = 'external_calendar_unsync')                           
                       WHERE
                           event_key = 'external_calendar_unsync';");

        // Update mapworks_action table to set email_template_id for external_calendar_event_change
        $this->addSql("UPDATE mapworks_action
                       SET 
                           email_template_id = (SELECT id FROM email_template WHERE email_key = 'external_calendar_event_change')                           
                       WHERE
                           event_key = 'external_calendar_event_change';");

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