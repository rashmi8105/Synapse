<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script for sending notification email to the users when the profile is disconnected from external calendar.
 */
class Version20170406092151 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $firstName = '$$first_name$$';
        $calendarName = '$$calendar_name$$';
        $calendarEmail = '$$calendar_email$$';
        $subject = "Please relink your $calendarName calendar account with Mapworks";

        $profileDisconnectedEmailHTML = <<<CDATA
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
                            <tr style="background:#fff;border-collapse:collapse;"><td>$firstName,</td></tr>
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>You are receiving this email because you are using Mapworks calendar sync, and the link to your
                                $calendarName calendar got disconnected. This can happen for all sorts for reasons. Often, for example, calendar providers will expire the connection when you change your calendar or email password. You will need to relink your account for 
                                $calendarEmail so that Mapworks can continue to access your calendar.</td>
                            </tr> 				
                            <tr style="background:#fff;border-collapse:collapse;">
                                <td>To relink, please log into Mapworks, go to the Appointments tab, and select Settings. Select the sync directions, press Save, and enter your calendar credentials again.</td>
                            </tr>            
                        </tbody>
                    </table>
                </body>
            </html>
CDATA;

        $this->addSql("INSERT INTO `email_template` (`email_key`, `from_email_address`,`is_active`) VALUES ('Relink_External_calendar', 'no-reply@mapworks.com', 1)");
        $sql = "SET @templateid := (SELECT id from email_template where email_key = 'Relink_External_calendar');
        INSERT INTO `email_template_lang` (`email_template_id`,`subject`,`language_id`,`body`) VALUES (@templateid, '$subject', 1, '$profileDisconnectedEmailHTML')";
        $this->addSql($sql);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
