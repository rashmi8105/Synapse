<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160108085242 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('SET @appbookstudstaff := (SELECT id FROM email_template where email_key="Appointment_Book_Student_to_Staff");
            UPDATE `email_template_lang` SET  `body`= \'<html>
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
        
 				<tr style="background:#fff;border-collapse:collapse;"><td>Dear $$staff_name$$:</td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>An appointment has been booked with $$student_name$$
 				on $$app_datetime$$. To view the appointment details,
 				please log in to your Mapworks dashboard and visit $$staff_dashboard$$ .</td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>Thank you.<br/><img src="$$Skyfactor_Mapworks_logo$$" alt="Skyfactor Mapworks logo" title="Skyfactor Mapworks logo" /></td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
 			</tbody>
 		</table>
</body>
</html>\'  WHERE `email_template_id`=@appbookstudstaff;"
            ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET @appbookstudstaff := (SELECT id FROM email_template where email_key="Appointment_Book_Student_to_Staff");
            UPDATE `email_template_lang` SET  `body`= \'<html>
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
        
 				<tr style="background:#fff;border-collapse:collapse;"><td>Dear $$staff_name$$:</td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>An appointment has been booked with $$student_name$$
 				on $$app_datetime$$. To view the appointment details,
 				please log in to your Mapworks dashboard and visit <a class="external-link" href="$$staff_dashboard$$" target="_blank" style="color: rgb(41, 114, 155);text-decoration: underline;">Mapworks student dashboard view appointment module</a>.</td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>Thank you.<br/><img src="$$Skyfactor_Mapworks_logo$$" alt="Skyfactor Mapworks logo" title="Skyfactor Mapworks logo" /></td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
 			</tbody>
 		</table>
</body>
</html>\'  WHERE `email_template_id`=@appbookstudstaff;"
            ');
    }
}
