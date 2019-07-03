<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script for updating Email templates for the email sent to faculty for appointment cancellation when the student is marked as archived
 */
class Version20170125082700 extends AbstractMigration
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
        $skyFactorMapworksLogo = '$$Skyfactor_Mapworks_logo$$';
        $appDateTime = '$$app_datetime$$';

        $archivedCancelAppointmentStaffEmailHTML = <<<CDATA
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

 				<tr style="background:#fff;border-collapse:collapse;"><td>Dear $staffName:</td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>Your booked appointment with
 				$studentName on $appDateTime has been cancelled because your Mapworks coordinator marked the student as no longer participating in Mapworks.
If you have any questions, please contact your Mapworks coordinator.</td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>Thank you.<br/><img src="$skyFactorMapworksLogo" alt="Skyfactor Mapworks logo" title="Skyfactor Mapworks logo" /></td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>

 			</tbody>
 		</table>
</body>
</html>
CDATA;

        $this->addSql("UPDATE `email_template_lang` SET `body` = ' $archivedCancelAppointmentStaffEmailHTML '  WHERE `email_template_id` = (SELECT id FROM email_template where email_key='Archived_Cancel_Appointment_Staff');");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
