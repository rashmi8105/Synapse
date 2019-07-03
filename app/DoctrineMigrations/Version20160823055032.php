<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 *  Email templates for the email sent to faculty and student for appoint cancellation when the student is marked as archived
 */
class Version20160823055032 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $studentName = '$$student_name$$';
        $staffName = '$$staff_name$$';
        $logo = '$$Skyfactor_Mapworks_logo$$';
        $appDateTime = '$$app_datetime$$';

        $sql = <<<SQL

INSERT INTO `email_template` (`email_key`, `from_email_address`) VALUES ('Archived_Cancel_Appointment_Staff', 'no-reply@mapworks.com');
INSERT INTO `email_template` (`email_key`, `from_email_address`) VALUES ('Archived_Cancel_Appointment_Student', 'no-reply@mapworks.com');

SET @templateid := (SELECT id from email_template where email_key = 'Archived_Cancel_Appointment_Staff');

INSERT INTO `email_template_lang` (`email_template_id`,`subject`,`language_id`,`body`) VALUES (@templateid ,'Appointment cancelled via Mapworks',1, '<html>
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

 				<tr style="background:#fff;border-collapse:collapse;"><td>Dear $staffName:</td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>Your booked appointment with
 				$studentName on $appDateTime has been cancelled.</td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>Thank you.<br/><img src="$logo" alt="Skyfactor Mapworks logo" title="Skyfactor Mapworks logo" /></td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>

 			</tbody>
 		</table>
</body>
</html>');

SET @templateid := (SELECT id from email_template where email_key = 'Archived_Cancel_Appointment_Student');
INSERT INTO `email_template_lang` (`email_template_id`,`subject`,`language_id`,`body`) VALUES (@templateid ,'Appointment cancelled via Mapworks',1, '<html>
			<head>
		 <style>body {
			   background: none repeat scroll 0 0 #f4f4f4;

		   }table {
			   padding: 21px;
			   width: 799px;
			font-family: helvetica,arial,verdana,san-serif;
			font-size:13px;
			color:#333;
		  }
			</style>
			</head>
			<body>

 		<table cellpadding="10" style="background:#ffffff;" cellspacing="0">
 			<tbody>

 				<tr style="background:#fff;border-collapse:collapse;"><td>Hi $studentName,</td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>Your booked appointment with
 				$staffName on $appDateTime has been cancelled.
 				</td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>Thank you.<br/><img alt="Skyfactor Mapworks Logo" src="$logo"/><br/></td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>');
SQL;
        $this->addSql($sql);
    }


    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}