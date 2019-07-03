<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150907112956 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql("SET @succpassresstaff := (SELECT id FROM email_template where email_key='Sucessful_Password_Reset_Staff');
UPDATE `email_template_lang` SET `body`='<html>
 <div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>
 Hi \$\$firstname\$\$,<br/><br/>
        
 Your Mapworks password has been changed. If you believe this is an error, please contact Mapworks support at &nbsp;<a href=\"mailto:\$\$Support_Helpdesk_Email_Address\$\$\" class=\"external-link\" rel=\"nofollow\">\$\$Support_Helpdesk_Email_Address\$\$</a>
 <br/><br/>
<p>Thank you from the Skyfactor Mapworks team.</br></p>
<p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
        
 </div>
 </html>' WHERE `email_template_id`=@succpassresstaff;");
        
        $this->addSql("SET @succpassrestcoord := (SELECT id FROM email_template where email_key='Sucessful_Password_Reset_Coordinator');
UPDATE `email_template_lang` SET `body`='<html>
 <div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>
 Hi \$\$firstname\$\$,<br/><br/>
        
 Your Mapworks password has been changed. If you believe this is an error, please contact Mapworks support at &nbsp;<a href=\"mailto:\$\$Support_Helpdesk_Email_Address\$\$\" class=\"external-link\" rel=\"nofollow\">\$\$Support_Helpdesk_Email_Address\$\$</a>
 <br/><br/>
<p>Thank you.</br></p>
<p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
        
 </div>
 </html>' WHERE `email_template_id`=@succpassrestcoord;");
        
        $this->addSql("SET @appbkstaffstud := (SELECT id FROM email_template where email_key='Appointment_Book_Staff_to_Student');
        
UPDATE `email_template_lang` SET `body`='<html>
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
  </head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\"><tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi \$\$student_name\$\$:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>An appointment has been booked with \$\$staff_name\$\$
 				on \$\$app_datetime\$\$. To view the appointment details,
 				please log in to your Mapworks dashboard and visit <a class=\"external-link\" href=\"\$\$student_dashboard\$\$\" target=\"_blank\" style=\"color: rgb(41, 114, 155);text-decoration: underline;\">Mapworks student dashboard view appointment module</a>.</td></tr>
        
  <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you.<br/><img alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>' WHERE `email_template_id`=@appbkstaffstud;");
        
        $this->addSql("SET @appupstffstud := (SELECT id FROM email_template where email_key='Appointment_Update_Staff_to_Student');
        
			UPDATE `email_template_lang` SET `body`='<html>
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
			  </head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\"><tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi \$\$student_name\$\$:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>A booked appointment with \$\$staff_name\$\$ has been modified. The appointment is now scheduled for
							\$\$app_datetime\$\$. To view the appointment details,
							please log in to your Mapworks dashboard and visit <a class=\"external-link\" href=\"\$\$student_dashboard\$\$\" target=\"_blank\" style=\"color: rgb(41, 114, 155);text-decoration: underline;\">Mapworks student dashboard view appointment module</a>.</td></tr>
			
			  <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you.<br/><img alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>' WHERE `email_template_id`=@appupstffstud;");
        
        $this->addSql("SET @appcanstafftostud := (SELECT id FROM email_template where email_key='Appointment_Cancel_Staff_to_Student');
        
		UPDATE `email_template_lang` SET `body`='<html>
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
     
 		<table cellpadding=\"10\" style=\"background:#ffffff;\" cellspacing=\"0\">
 			<tbody>
     
 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi \$\$student_name\$\$,</td></tr>
 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Your booked appointment with
 				\$\$staff_name\$\$ on \$\$app_datetime\$\$ has been cancelled.
 				To book a new appointment, please log in to your Mapworks dashboard and visit <a class=\"external-link\" href=\"$\$student_dashboard\$\$\" target=\"_blank\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">Mapworks student dashboard book appointment module</a>.</td></tr>
 				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you.<br/><img alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>' WHERE `email_template_id`=@appcanstafftostud;");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
