<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150612132312 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Welcome_Email_Staff");
            UPDATE `email_template_lang` SET  `body`= "<html>
<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">
Hi $$firstname$$,<br/><br/>
        
A Mapworks password was successfully created for your account.If this was not you or you believe this is an error,
please contact Mapworks support at &nbsp;<a class=\"external-link\" href=\"mailto:$$Support_Helpdesk_Email_Address$$\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">$$Support_Helpdesk_Email_Address$$</a><br/><br/>
        
We\'re very happy to have you on board, and are here to support you!<br/><br/>
Thank you from the Mapworks team!
        
</div>
</html>"  WHERE `email_template_id`=@emtid;
            ');
        
        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Forgot_Password_Staff');
        
UPDATE `email_template_lang` SET `body`='<html>
<div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>
Hi \$\$firstname\$\$,<br/></br>
        
Please use the link below and follow the displayed instructions to create your new password. This link will expire after \$\$Reset_Password_Expiry_Hrs\$\$ hours.<br />
<br/>
\$\$activation_token\$\$<br/><br/>
        
If you believe that you received this email in error or if you have any questions,please contact Mapworks support at <span style=\'color: #99ccff;\'>\$\$Support_Helpdesk_Email_Address\$\$</span>.<br/><br/>
<p>Thank you from the Mapworks team.</br></p>
<p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
</div>
</html>' WHERE `email_template_id`=@emtid;");
        
        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Sucessful_Password_Reset_Staff');
UPDATE `email_template_lang` SET `body`='<html>
 <div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>
 Hi \$\$firstname\$\$,<br/><br/>
        
 Your Mapworks password has been changed. If this was not you or you believe this is an error, please contact Mapworks support at &nbsp;<a class=\'external-link\' href=\'mailto:support@mapworks.com\' rel=\'nofollow\' style=\'color: rgb(41, 114, 155); text-decoration: none;\'>support@mapworks.com</a>
 <br/><br/>
<p>Thank you from the Mapworks team.</br></p>
<p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
        
 </div>
 </html>' WHERE `email_template_id`=@emtid;");
        
        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Sucessful_Password_Reset_Coordinator');
UPDATE `email_template_lang` SET `body`='<html>
 <div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>
 Hi \$\$firstname\$\$,<br/><br/>
        
 Your Mapworks password has been changed. If this was not you or you believe this is an error, please contact Mapworks support at &nbsp;<a class=\'external-link\' href=\'mailto:support@mapworks.com\' rel=\'nofollow\' style=\'color: rgb(41, 114, 155); text-decoration: none;\'>support@mapworks.com</a>
 <br/><br/>
<p>Thank you from the Mapworks team.</br></p>
<p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
        
 </div>
 </html>' WHERE `email_template_id`=@emtid;");
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="MyAccount_Updated_Staff");
            UPDATE `email_template_lang` SET  `body`= \'<html>
        
 		<div style="margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);">
 			Hi $$firstname$$,<br/><br/>
 	
 			An update to your Mapworks account was successfully made. The following information was updated: <br/><br/>
 	
 			$$Updated_MyAccount_fields$$
 		<br/>
 	
 			If this was not you or you believe this is an error, please contact Mapworks support at&nbsp;<a class="external-link" href="mailto:support@map-works.com" rel="nofollow" style="color: rgb(41, 114, 155); text-decoration: underline;">support@map-works.com</a></p>
 	
 			<br>Thank you from the Mapworks team!</br>
 	</div>
 </html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Create_Password_Coordinator");
            UPDATE `email_template_lang` SET  `body`= \'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Email</title>
</head>
<body>
<center>
    <table align="center">
        <tr>
            <th style="padding:0px;margin:0px;">
        
               <table  style="font-family:helvetica,arial,verdana,san-serif;font-weight:normal;width:800px; height=337px;text-align:center;padding:0px;">
               <tr bgcolor="#eeeeee" style="width:800px;padding:0px;height:337px;">
               <td style="width:800px;padding:0px;height:337px;">
               <table style="text-align:center;width:100%">
               <tr>
                    <td style="padding:0px;">
                    <table style="margin-top:56px;width:100%">
		<tr>
		<td style="text-align:center;padding:0px;font-size:33px;height:80px;width:800px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;color:#000000">
					<br>Welcome to Mapworks.
		</td>
		</tr>
		</table>
                    </td>
               </tr>
               <tr style="margin:0px;padding:0px;">
		<td style="text-align:center;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;color:#333333;font-size: 16px;height:16px;padding-top:8px;">
        			Use the link below to create your password and start using Mapworks.
        
		</td></tr>
           <tr style="margin:0px;padding:0px;"><td style="margin:0px;padding:0px;">
        
<table align="center">
  <tr style="margin:0px;padding:0px;">
    <th style="margin:0px;padding:0px;">
           <table cellpadding="36" style="width:100%">
        <tr>
		<td align="center" style="text-align:center;color:#000000;font-weight:normal;font-size: 20px;">
		          <table style="border-radius:2px;width:175px;font-size:20px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;text-align:center;display: block;margin: 0 auto;padding:0px 0px">
		<tr>
        <td style="background-color:#4673a7; height:58px;border-radius:2px;line-height:21px;text-decoration:none ;vertical-align:middle;">
        <a href="$$activation_token$$" style="outline-offset:19px !important;background-color: #4673a7; color: #ffffff;display: block;text-decoration: none;width:175px "target="_blank"><span style="text-decoration: none !important;">Sign In Now</span></a>
        </td></tr>
        <tr valign="top" style="height:33px;">
        <td style="margin-left:auto; margin-right:auto;width:100%;font-size: 14px;height:14px;padding-bottom:7px;font-family:helvetica,arial,verdana,san-serif;font-weight:medium;color:#333333;link:#1e73d5;padding-top:8px;">
				<span>Use this link to <a target="_blank" style="link:#1e73d5;" href="$$activation_token$$">sign in.</a></span>
        
        </td></tr>
        
        </table>
		</td></tr>
        
        </table>
        </th>
        
  </tr>
        
</table>
       </td></tr>
</table>
               </td>
               </tr>
               <tr valign="top">
<td >
<table>
<tr>
<td valign="top" align="center">
<div style="text-align:left;margin-left:30px;font-family:helvetica,arial,verdana,san-serif;font-weight:normal;
			margin-right:18px;font-size: 13px;color: #333333;margin-top:30px;link:#1e73d5;font-weight:normal;" >
				Thank you for participating in the spring 2015 pilot. We look forward to hearing your feedback as
				it will inform future releases of our new student retention and success solution.
        
				<br><br>
				If you have any questions, please contact us here.<br>
				<a href="mailto:$$Support_Helpdesk_Email_Address$$" style="link:#1e73d5;">$$Support_Helpdesk_Email_Address$$</a>
				<br><br>
				Sincerely,
				<div style="text-align:left;font-weight:bold;font-size: 14px;color:#333333" >
					<b>The EBI Mapworks Client Services Team</b>
		
				</div>
                </div>
</td>
</tr>
</table>
</td>
</tr>
               </table>
        
            </th>
        
        </tr>
        
    </table>
    </center>
</body>
</html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Welcome_Email_Coordinator");
            UPDATE `email_template_lang` SET  `body`= "<html>
<div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">
Hi $$firstname$$,<br/><br/>
        
A Mapworks password was successfully created for your account. If this was not you or you believe this is an error,
please contact Mapworks support at &nbsp;<a class=\"external-link\" href=\"mailto:support@map-works.com\" rel=\"nofollow\" style=\"color: rgb(41, 114, 155); text-decoration: underline;\">support@map-works.com</a><br/><br/>
        
We\'re very happy to have you on board, and are here to support you!<br/><br/>
Thank you from the Mapworks team!
        
</div>
</html>" WHERE `email_template_id`=@emtid;
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Appointment_Book_Staff_to_Student");
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
        
				<tr style="background:#fff;border-collapse:collapse;"><td>Dear $$student_name$$:</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>An appointment has been booked with $$staff_name$$
				on $$app_datetime$$. To view the appointment details,
				please log in to your Mapworks dashboard and visit <a class="external-link" href="$$student_dashboard$$" target="_blank" style="color: rgb(41, 114, 155);text-decoration: underline;">Mapworks student dashboard view appointment module</a>.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI Mapworks</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Appointment_Update_staff_to_Student");
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
        
				<tr style="background:#fff;border-collapse:collapse;"><td>Dear $$student_name$$:</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>A booked appointment with $$staff_name$$
				has been modified. The appointment is now scheduled for $$app_datetime$$. To view the modified appointment details,
				please log in to your Mapworks dashboard and visit <a class="external-link" href="$$student_dashboard$$" target="_blank" style="color: rgb(41, 114, 155); text-decoration: underline;">Mapworks student dashboard view appointment module</a>.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI Mapworks</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Appointment_Cancel_Staff_to_Student");
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
        
				<tr style="background:#fff;border-collapse:collapse;"><td>Dear $$student_name$$:</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Your booked appointment with
				$$staff_name$$ on $$app_datetime$$ has been cancelled.
				To book a new appointment, please log in to your Mapworks dashboard and visit <a class="external-link" href="$$student_dashboard$$" target="_blank" style="color: rgb(41, 114, 155); text-decoration: underline;">Mapworks student dashboard view appointment module</a>.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI Mapworks</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Remove_Delegate");
            UPDATE `email_template_lang` SET  `body`= "<html>
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
        
		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">
			<tbody>
        
				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$fullname$$:</td></tr>
				<tr style=\"background:#fff;border-collapse:collapse;\"><td>You have been removed as a delegate user for $$delegater_name$$\'s calendar.</td></tr>
				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI Mapworks</td></tr>
				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>"  WHERE `email_template_id`=@emtid;
            ');
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Referral_Assign_to_staff");
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
        
				<tr style="background:#fff;border-collapse:collapse;"><td>Hi $$firstname$$:</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>A referral was recently assigned to you in Mapworks. Please sign in to your account to view and take action on this referral.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Thank you from the Mapworks team!</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Appointment_Reminder_Staff_to_Student");
            UPDATE `email_template_lang` SET  `body`= \'<html>
    <head>
        <style>
			 body {
				background: none repeat scroll 0 0;
        
			}
			table {
				padding: 21px;
				width: 799px;
				font-family: Helvetica,Arial,Verdana,San-serif;
				font-size:13px;
				color:#333;
			}
   </style>
    </head>
    <body>
        <table cellpadding="10" cellspacing="0">
            <tbody>
                <tr style="background:#fff;border-collapse:collapse;">
                    <td>Dear $$student_name$$:</td>
                </tr>
                <tr style="background:#fff;border-collapse:collapse;">
                    <td style="line-height: 1.6;">This is a reminder that you have an appointment with $$staff_name$$ on $$app_datetime$$. <br/><br/> To view the appointment details, please log in to your Mapworks dashboard and visit
					<a style="color: #0033CC;" href="$$student_dashboard$$">Mapworks student dashboard view appointment module</a>.
					</td>
                </tr>
                <tr style="background:#fff;border-collapse:collapse;">
                    <td>Best regards,
                        <br/>EBI Mapworks
                    </td>
                </tr>
                <tr style="background:#fff;border-collapse:collapse;">
                    <td><span style="font-size:11px; color: #575757; line-height: 120%; text-decoration: none;">This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</span></td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
            \'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Referral_Student_Notification");
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
        
				<tr style="background:#fff;border-collapse:collapse;"><td>Dear $$firstname$$:</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>A faculty/staff member has referred you to a campus resource through the Mapworks system. To view the referral details, please log in to your Mapworks homepage and visit $$dashboard$$.</td></tr>
			<tr style="background:#fff;border-collapse:collapse;"><td>If you have any questions, please contact $$coordinator_details$$</td></tr>
		<tr style="background:#fff;border-collapse:collapse;"><td>Best regards,</td></tr>
	<tr style="background:#fff;border-collapse:collapse;"><td>Skyfactor Mapworks Team</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Referral_InterestedParties_Staff");
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
        
 				<tr style="background:#fff;border-collapse:collapse;"><td>Dear $$staff_firstname$$,</td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>A faculty/staff member has referred $$student_name$$ to a campus resource through the Mapworks system and added you as an interested party. </tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>To view the referral details, please log in to Mapworks and visit <a class="external-link" href="$$staff_referralpage$$" target="_blank" style="color: rgb(41, 114, 155); text-decoration: underline;">Mapworks student dashboard view referral module</a>. If you have any questions, please contact ($$coordinator_name$$,$$coordinator_title$$,$$coordinator_email$$ ). </td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>Thank you from the Skyfactor Mapworks team.</td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td><img src="$$Skyfactor_Mapworks_logo$$" title="Skyfactor Mapworks logo" alt="Skyfactor Mapworks logo"/></td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
 			</tbody>
 		</table>
 	</body>
</html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Course_Upload_Notification');
        
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
  </head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\"><tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi \$\$user_first_name\$\$:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Your course upload has finished importing.
  Click <a href=\"\$\$download_failed_log_file\$\$\">here</a> to download error file. </td></tr>
    <tr style=\"background:#fff;border-collapse:collapse;\"><td></td></tr>
  <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.<br/><img alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>' WHERE `email_template_id`=@emtid;");
        
        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Course_Student_Upload_Notification');
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
  </head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\"><tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi \$\$user_first_name\$\$:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Your student upload has finished importing. Click <a href=\"\$\$download_failed_log_file\$\$\">here</a> to download error file. </td></tr>
    <tr style=\"background:#fff;border-collapse:collapse;\"><td></td></tr>
  <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.<br/><img alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>' WHERE `email_template_id`=@emtid;");
        
        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Course_Faculty_Upload_Notification');
        
UPDATE `synapse`.`email_template_lang` SET `body`='<html>
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
  </head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\"><tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi \$\$user_first_name\$\$:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Your faculty upload has finished importing. Click here to download error file. </td></tr>
  <tr style=\"background:#fff;border-collapse:collapse;\"><td><a href=\"\$\$download_failed_log_file\$\$\"></a></td></tr>
  <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.<br/><img alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>' WHERE `email_template_id`=@emtid;");
        
        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='AcademicUpdate_Upload_Notification');
UPDATE `email_template_lang` SET `body`='<html>
        
 <body>
 <p> Hi \$\$studentname\$\$ </p>
 <p>Your academic update upload has finished importing.Click <a href=\"\$\$download_failed_log_file\$\$\">here </a> to download error file.</p>
 <p>Thank you from the Skyfactor Mapworks team.</br></p>
<p><img alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
 </body></html>' WHERE `email_template_id`=@emtid;");
        
        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Send_Invitation_to_User');
UPDATE `synapse`.`email_template_lang` SET `body`='
<!-- Versie 2.0 !-->
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">
<head>
<!--[if gte mso 9]>
<xml>
<o:OfficeDocumentSettings>
<o:AllowPNG/>
 <o:PixelsPerInch>96</o:PixelsPerInch>
 </o:OfficeDocumentSettings>
 </xml>
 <![endif]-->
 <title>MAPworks Faculty Invitation</title>
 <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
 <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />
 <style type=\"text/css\">
 /* e-mail bugfixes */
 #outlook a {padding: 0 0 0 0;}
 .ReadMsgBody {width: 100%;}
 .ExternalClass {width: 100%; line-height: 100%;}
 .ExternalClass * {line-height: 100%}
 sup, sub {vertical-align: baseline; position: relative; top: -0.4em;}
 sub {top: 0.4em;}
 .applelinks a {color:#262727; text-decoration: none;}
        
        
 /* General classes */
 body {width: 100% !important; margin: 0; padding: 0; -webkit-text-size-adjust:none; -ms-text-size-adjust:100%; font-size:13px; color:#333333; font-family: helvetica neue, helvetica, arial, verdana, san-serif; }
 img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic; border: none;}
 .bodytemplate, td { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; mso-line-height-rule:exactly }
 .bodytemplate { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; }
 .bodytemplate a, .bodytemplate a:hover { color: #F48C00; text-decoration: underline; }
 .main_table td{font-size:14px; color:#333333; text-align:left; font-family: helvetica neue, helvetica, arial, verdana, san-serif; padding:5px 10px;}
        
 <!-- NOTE: Remove this css-code to make te email scalable instead of responsive -->
 /* Smartphones (portrait and landscape) ----------- */
 @media only screen and (max-width:800px) {
 *[class=mHide] {display: none !important;}
 *[class=mWidth100] {width:100% !important; max-width: 100% !important;}
 *[class=mPaddingbottom] {padding-bottom:12px !important;}
 *[class=mGmail] {width:100% !important; min-width: 100% !important; padding: 0 10px !important;}
 *[class=titeloranje] {-webkit-border-radius: 15px; -moz-border-radius: 15px; border-radius: 15px; background-color:#F48C00;}
 *[class=nieuwsbrieftitel] { padding-top: 20px !important;}
 *[class=openhtml] { padding: 10px !important; }
 }
 <!-- NOTE: End CSS-code to remove if scalable email -->
 </style>
 </head>
 <body bgcolor=\"#ffffff\">
 <table bgcolor=\"#ffffff\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" style=\"table-layout: fixed\">
   <tbody>
     <tr>
       <td class=\"bodytemplate\" align=\"center\" valign=\"top\">
 	  <table align=\"center\" class=\"mGmail\" style=\"width:800px; min-width:800px;  \" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\">
           <tbody>
             <tr>
               <td style=\"width:800px; max-width:800px; padding-bottom:20px;\">
 		
 			  </td>
             </tr>
 			 <tr>
 			    <td>
 				<!--Paragraph content---->
 					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 					  <tbody>
 						<tr>
 						  <td style=\"width:800px; max-width:800px; padding-bottom:20px;\">
 						  <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 							  <tbody>
 								<tr>
 								  <td style=\"font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;\" align=\"left\" valign=\"top\">
 									Hi \$\$firstname\$\$,
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
        
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 					  <tbody>
 						<tr>
 						  <td style=\"width:800px; max-width:800px; padding-bottom:20px;\">
 						  <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 							  <tbody>
 								<tr>
 								  <td style=\"font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;\" align=\"left\" valign=\"top\">
 										Welcome to Mapworks. Use the link below to create your password and start using Mapworks.
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<!--Paragraph content---->
 					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 					  <tbody>
 						<tr>
 						  <td style=\"width:800px; max-width:800px; padding-bottom:20px;\">
 						  <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 							  <tbody>
 								<tr>
 								  <td style=\"font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;\" align=\"left\" valign=\"top\">
 										<a href=\"\$\$Coordinator_ResetPwd_URL_Prefix\$\$\">\$\$Coordinator_ResetPwd_URL_Prefix\$\$\</a>
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<!--Paragraph content---->
 					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 					  <tbody>
 						<tr>
 						  <td style=\"width:800px; max-width:800px; padding-bottom:20px;\">
 						  <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 							  <tbody>
 								<tr>
 								  <td style=\"font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;\" align=\"left\" valign=\"top\">
 										If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <a href=\"mailto:\$\$Support_Helpdesk_Email_Address\$\$\" class=\"external-link\" rel=\"nofollow\">\$\$Support_Helpdesk_Email_Address\$\$</a>
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 					  <tbody>
 						<tr>
 						  <td style=\"width:800px; max-width:800px; padding-bottom:20px;\">
 						  <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 							  <tbody>
 								<tr>
 								  <td style=\"font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;\" align=\"left\" valign=\"top\">
 									Thank you from the Skyfactor Mapworks team.</br></td></tr>
									<tr><td>
 									<img alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/ ><br/>
 									This email is an auto-generated message. Replies to automated messages are not monitored.
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
        
 			    </td>
 			</tr>
           </tbody>
         </table>
 		</td>
     </tr>
     <tr>
       <td>
 	
 	  </td>
     </tr>
   </tbody>
 </table>
 </body>
 </html>' WHERE `email_template_id`=@emtid;");
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Academic_Update_Cancel_to_Faculty");
            UPDATE `email_template_lang` SET  `body`= \'<html>
 <head>
 <style>body{background: none repeat scroll 0 0 #f4f4f4;}
 table{
 padding: 21px; width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;
 color:#333;}</style>
 </head>
 <body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0">
 <tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$faculty_name$$:</td></tr>
 <tr style="background:#fff;border-collapse:collapse;"><td>This academic update request has been cancelled and removed from your queue:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>$$request_name$$ ($$due_date$$)</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>$$request_description$$</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Requestor: $$requestor_name$$ ($$requestor_email$$)</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Student Updates: $$student_update_count$$</td></tr>
 <tr style="background:#fff;border-collapse:collapse;"><td>$$custom_message$$</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table>
 </body>
</html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Academic_Update_Reminder_to_Faculty");
            UPDATE `email_template_lang` SET  `body`= \'<html><head>
<style>body{background: none repeat scroll 0 0 #f4f4f4;}
 table{
        
        
 padding: 21px; width: 799px;font-family: helvetica,arial,verdana,san-serif;font-
        
 size:13px;
 color:#333;}</style>
</head><body><table cellpadding="10"
        
 style="background:#eeeeee;" cellspacing="0">
 <tbody><tr
        
 style="background:#fff;border-collapse:collapse;"><td>Dear $$faculty_name$$:</td></tr>
 <tr style="background:#fff;border-collapse:collapse;"><td>Please submit your academic updates for this request:</td></tr><tr
        
 style="background:#fff;border-collapse:collapse;"><td><a class="external-link"
        
 href="$$faculty_au_submission_page$$" target="_blank" style="color: rgb(41, 114,
        
 155);text-decoration: underline;">View and complete this academic update request
        
 on Mapworks</a></td></tr><tr style="background:#fff;border-
        
 collapse:collapse;"><td>$$request_name$$ ($$due_date$$)</td></tr><tr
        
 style="background:#fff;border-collapse:collapse;"><td>$$request_description$$</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Requestor: $$requestor_name$$ ($$requestor_email$$)</td></tr><tr
        
 style="background:#fff;border-collapse:collapse;"><td>Student Updates: $$student_update_count$$</td></tr>
 <tr style="background:#fff;border-collapse:collapse;"><td><a class="external-
        
 link" href="$$faculty_au_submission_page$$" target="_blank" style="color: rgb(41,
        
 114, 155);text-decoration: underline;">Update</a></td></tr><tr
        
 style="background:#fff;border-collapse:collapse;"><td>$$custom_message$$</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Best
        
 regards,<br/>EBI Mapworks</td></tr><tr style="background:#fff;border-
        
 collapse:collapse;"><td>This email confirmation is an auto-generated message.
        
 Replies to automated messages are not monitored.</td></tr></tbody></table>
</body></html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Academic_Update_Request_Staff");
            UPDATE `email_template_lang` SET  `body`= \'<!DOCTYPE html>
<html>
                <head>
                                <title></title>
                </head>
<body>
<table class="table table-bordered" style="border: 1px solid #428BCA !important; border-collapse:collapse; width:40%; margin-top:20px; margin-bottom:20px;">
	<tr>
		<td style=" padding:5px; background-color: #4F9BD9; border: 1px solid #428BCA !important; border-collapse:collapse"><p style="font-weight: bold; font-size: 14px; color:#fff;">View and complete this academic update request on Mapworks &gt;</p></td>
	</tr>
	<tr>
		<td style="background-color: #D4EEFF; border: 1px solid #4F9BD9 !important; border-collapse:collapse; padding:15px 5px 15px 5px; vertical-align: middle;">
			<p style="font-size:14px; font-weight: bold;   margin: 0px !important;">$$requestname$$ (due <span>$$duedate$$</span>)</p>
			<p style="font-size:14px;   margin: 0px !important;">$$description$$</p>
			<p style="font-size:14px;   margin: 0px !important;">Requestor: <span>$$requestor$$.</span></p>
			<p style="display:inline-block; float:left; font-size:14px;   margin: 0px !important;">Student Updates: <span>$$studentupdate$$</span></p>
			<a style="width: 65px; height:20px; background-color:#ccc; text-align: center; display:inline-block; float: right; border: 1px solid #ccc; margin: 2px; text-decoration: none; color:#000;" href="$$updateviewurl$$">update</a>
		</td>
	</tr>
</table>
<p style="width:40%; margin-bottom:20px;">$$optional_message$$</p>
        
        
</body>
</html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Academic_Update_Notification_Student");
            UPDATE `email_template_lang` SET  `body`= \'<!DOCTYPE html>
<html>
        
<body>
<p> Hi $$studentname$$ </p>
<p>An Academic Update was created for you. View it now</body></html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Referral_InterestedParties_Staff_Closed");
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
        
				<tr style="background:#fff;border-collapse:collapse;"><td>Dear $$staff_firstname$$:</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>A referral that you were watching in Mapworks has recently been closed. Please sign in to your account to view this referral.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Thank you from the Mapworks team!</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>\'  WHERE `email_template_id`=@emtid;"
            ');
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Appointment_Book_Student_to_Staff");
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
 				<tr style="background:#fff;border-collapse:collapse;"><td>Thank you from the Skyfactor Mapworks team.<br/><img src="$$Skyfactor_Mapworks_logo$$" alt="Skyfactor Mapworks logo" title="Skyfactor Mapworks logo" /></td></tr>
 				<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
 			</tbody>
 		</table>
</body>
</html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Appointment_Cancel_Student_to_Staff");
            UPDATE `email_template_lang` SET  `body`= \'<html> <head>
 <style>
 body{background: none repeat scroll 0 0 #f4f4f4;}table {padding: 21px;width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;color:#333;}
 </style>
 </head>
 <body>
 <table cellpadding="10" style="background:#eeeeee;" cellspacing="0">
 <tbody>
 <tr style="background:#fff;border-collapse:collapse;"><td>Dear $$staff_name$$:</td></tr>
 <tr style="background:#fff;border-collapse:collapse;"><td>Your booked appointment with $$student_name$$ on $$app_datetime$$ has been cancelled. To book a new appointment, please log in to your Mapworks dashboard and visit
 <a class="external-link" href="$$staff_dashboard$$" target="_blank" style="color: rgb(41, 114, 155);text-decoration: underline;">
 Mapworks faculty dashboard view appointment module</a>.</td></tr>
 <tr style="background:#fff;border-collapse:collapse;"><td>Thank you from the Skyfactor Mapworks team.<br/><img src="$$Skyfactor_Mapworks_logo$$" alt="Skyfactor Mapworks logo" title="Skyfactor Mapworks logo" /></td></tr>
 <tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr></table></body></html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Send_Invitation_to_User');
UPDATE `email_template_lang` SET `body`='
<!-- Versie 2.0 !-->
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">
<head>
<!--[if gte mso 9]>
<xml>
<o:OfficeDocumentSettings>
<o:AllowPNG/>
 <o:PixelsPerInch>96</o:PixelsPerInch>
 </o:OfficeDocumentSettings>
 </xml>
 <![endif]-->
 <title>MAPworks Faculty Invitation</title>
 <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
 <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />
 <style type=\"text/css\">
 /* e-mail bugfixes */
 #outlook a {padding: 0 0 0 0;}
 .ReadMsgBody {width: 100%;}
 .ExternalClass {width: 100%; line-height: 100%;}
 .ExternalClass * {line-height: 100%}
 sup, sub {vertical-align: baseline; position: relative; top: -0.4em;}
 sub {top: 0.4em;}
 .applelinks a {color:#262727; text-decoration: none;}
        
        
 /* General classes */
 body {width: 100% !important; margin: 0; padding: 0; -webkit-text-size-adjust:none; -ms-text-size-adjust:100%; font-size:13px; color:#333333; font-family: helvetica neue, helvetica, arial, verdana, san-serif; }
 img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic; border: none;}
 .bodytemplate, td { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; mso-line-height-rule:exactly }
 .bodytemplate { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; }
 .bodytemplate a, .bodytemplate a:hover { color: #F48C00; text-decoration: underline; }
 .main_table td{font-size:14px; color:#333333; text-align:left; font-family: helvetica neue, helvetica, arial, verdana, san-serif; padding:5px 10px;}
        
 <!-- NOTE: Remove this css-code to make te email scalable instead of responsive -->
 /* Smartphones (portrait and landscape) ----------- */
 @media only screen and (max-width:800px) {
 *[class=mHide] {display: none !important;}
 *[class=mWidth100] {width:100% !important; max-width: 100% !important;}
 *[class=mPaddingbottom] {padding-bottom:12px !important;}
 *[class=mGmail] {width:100% !important; min-width: 100% !important; padding: 0 10px !important;}
 *[class=titeloranje] {-webkit-border-radius: 15px; -moz-border-radius: 15px; border-radius: 15px; background-color:#F48C00;}
 *[class=nieuwsbrieftitel] { padding-top: 20px !important;}
 *[class=openhtml] { padding: 10px !important; }
 }
 <!-- NOTE: End CSS-code to remove if scalable email -->
 </style>
 </head>
 <body bgcolor=\"#ffffff\">
 <table bgcolor=\"#ffffff\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" style=\"table-layout: fixed\">
   <tbody>
     <tr>
       <td class=\"bodytemplate\" align=\"center\" valign=\"top\">
 	  <table align=\"center\" class=\"mGmail\" style=\"width:800px; min-width:800px;  \" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\">
           <tbody>
             <tr>
               <td style=\"width:800px; max-width:800px; padding-bottom:20px;\">
 	
 			  </td>
             </tr>
 			 <tr>
 			    <td>
 				<!--Paragraph content---->
 					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 					  <tbody>
 						<tr>
 						  <td style=\"width:800px; max-width:800px; padding-bottom:20px;\">
 						  <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 							  <tbody>
 								<tr>
 								  <td style=\"font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;\" align=\"left\" valign=\"top\">
 									Hi \$\$firstname\$\$,
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
        
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 					  <tbody>
 						<tr>
 						  <td style=\"width:800px; max-width:800px; padding-bottom:20px;\">
 						  <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 							  <tbody>
 								<tr>
 								  <td style=\"font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;\" align=\"left\" valign=\"top\">
 										Welcome to Mapworks. Use the link below to create your password and start using Mapworks.
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<!--Paragraph content---->
 					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 					  <tbody>
 						<tr>
 						  <td style=\"width:800px; max-width:800px; padding-bottom:20px;\">
 						  <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 							  <tbody>
 								<tr>
 								  <td style=\"font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;\" align=\"left\" valign=\"top\">
 										<a href=\"\$\$Coordinator_ResetPwd_URL_Prefix\$\$\">\$\$Coordinator_ResetPwd_URL_Prefix\$\$\</a>
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<!--Paragraph content---->
 					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 					  <tbody>
 						<tr>
 						  <td style=\"width:800px; max-width:800px; padding-bottom:20px;\">
 						  <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 							  <tbody>
 								<tr>
 								  <td style=\"font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;\" align=\"left\" valign=\"top\">
 										If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <a href=\"mailto:\$\$Support_Helpdesk_Email_Address\$\$\" class=\"external-link\" rel=\"nofollow\">\$\$Support_Helpdesk_Email_Address\$\$</a>
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 					  <tbody>
 						<tr>
 						  <td style=\"width:800px; max-width:800px; padding-bottom:20px;\">
 						  <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
 							  <tbody>
 								<tr>
 								  <td style=\"font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;\" align=\"left\" valign=\"top\">
 									Thank you from the Skyfactor Mapworks team.</br></td></tr>
									<tr><td>
 									<img alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/ ><br/>
 									This email is an auto-generated message. Replies to automated messages are not monitored.
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
        
 			    </td>
 			</tr>
           </tbody>
         </table>
 		</td>
     </tr>
     <tr>
       <td>
        
 	  </td>
     </tr>
   </tbody>
 </table>
 </body>
 </html>' WHERE `email_template_id`=@emtid;");
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Accept_Change_Request");
            UPDATE `email_template_lang` SET  `body`= \'<!-- Versie 2.0 !-->
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
 <head>
 <!--[if gte mso 9]>
 <xml>
 <o:OfficeDocumentSettings>
 <o:AllowPNG/>
 <o:PixelsPerInch>96</o:PixelsPerInch>
 </o:OfficeDocumentSettings>
 </xml>
 <![endif]-->
 <title>MAP-Works Faculty Invitation</title>
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <meta http-equiv="X-UA-Compatible" content="IE=edge" />
 <style type="text/css">
 /* e-mail bugfixes */
 #outlook a {padding: 0 0 0 0;}
 .ReadMsgBody {width: 100%;}
 .ExternalClass {width: 100%; line-height: 100%;}
 .ExternalClass * {line-height: 100%}
 sup, sub {vertical-align: baseline; position: relative; top: -0.4em;}
 sub {top: 0.4em;}
 .applelinks a {color:#262727; text-decoration: none;}
        
        
 /* General classes */
 body {width: 100% !important; margin: 0; padding: 0; -webkit-text-size-adjust:none; -ms-text-size-adjust:100%; font-size:13px; color:#333333; font-family: helvetica neue, helvetica, arial, verdana, san-serif; }
 img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic; border: none;}
 .bodytemplate, td { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; mso-line-height-rule:exactly }
 .bodytemplate { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; }
 .bodytemplate a, .bodytemplate a:hover { color: #F48C00; text-decoration: underline; }
 .main_table td{font-size:14px; color:#333333; text-align:left; font-family: helvetica neue, helvetica, arial, verdana, san-serif; padding:5px 10px;}
        
 <!-- NOTE: Remove this css-code to make te email scalable instead of responsive -->
 /* Smartphones (portrait and landscape) ----------- */
 @media only screen and (max-width:800px) {
 *[class=mHide] {display: none !important;}
 *[class=mWidth100] {width:100% !important; max-width: 100% !important;}
 *[class=mPaddingbottom] {padding-bottom:12px !important;}
 *[class=mGmail] {width:100% !important; min-width: 100% !important; padding: 0 10px !important;}
 *[class=titeloranje] {-webkit-border-radius: 15px; -moz-border-radius: 15px; border-radius: 15px; background-color:#F48C00;}
 *[class=nieuwsbrieftitel] { padding-top: 20px !important;}
 *[class=openhtml] { padding: 10px !important; }
 }
 <!-- NOTE: End CSS-code to remove if scalable email -->
 </style>
 </head>
 <body bgcolor="#ffffff">
 <table bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" width="100%" style="table-layout: fixed">
   <tbody>
     <tr>
       <td class="bodytemplate" align="center" valign="top">
 	  <table align="center" class="mGmail" style="width:800px; min-width:800px;  " cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
           <tbody>
             <tr>
               <td style="width:800px; max-width:800px; padding-bottom:20px;">
 	
 			  </td>
             </tr>
 			 <tr>
 			    <td>
 				<!--Paragraph content---->
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr>
 								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
 									Hi $$firstname$$ ,
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
        
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr>
 								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
 										Your home campus change request for [$$firstname$$ $$lastname$$] has been approved.
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 										<!--Paragraph content---->
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr>
 								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
 										If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <a href="mailto:support@mapworks.com" class="external-link" rel="nofollow">support@mapworks.com</a>
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr>
 								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
 									Thank you from the Skyfactor Mapworks team.</br>
 								<img src="https://synapse-qa.mnv-tech.com/images/Skyfactor-Mapworks-login.png"/><br/>
 									This email is an auto-generated message. Replies to automated messages are not monitored.
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
        
 			    </td>
 			</tr>
           </tbody>
         </table>
 		</td>
     </tr>
     <tr>
       <td>
        
 	  </td>
     </tr>
   </tbody>
 </table>
</body>
 </html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Add_Delegate");
            UPDATE `email_template_lang` SET  `body`= "<html>
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
        
		<table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">
			<tbody>
        
				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$fullname$$:</td></tr>
				<tr style=\"background:#fff;border-collapse:collapse;\"><td>You have been added as a delegate user for $$delegater_name$$\'s calendar.</td></tr>
				<tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI Mapworks</td></tr>
				<tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>"  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Deny_Change_Request");
            UPDATE `email_template_lang` SET  `body`= \'<!-- Versie 2.0 !-->
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
 <head>
 <!--[if gte mso 9]>
 <xml>
 <o:OfficeDocumentSettings>
 <o:AllowPNG/>
 <o:PixelsPerInch>96</o:PixelsPerInch>
 </o:OfficeDocumentSettings>
 </xml>
 <![endif]-->
 <title>MAP-Works Faculty Invitation</title>
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <meta http-equiv="X-UA-Compatible" content="IE=edge" />
 <style type="text/css">
 /* e-mail bugfixes */
 #outlook a {padding: 0 0 0 0;}
 .ReadMsgBody {width: 100%;}
 .ExternalClass {width: 100%; line-height: 100%;}
 .ExternalClass * {line-height: 100%}
 sup, sub {vertical-align: baseline; position: relative; top: -0.4em;}
 sub {top: 0.4em;}
 .applelinks a {color:#262727; text-decoration: none;}
        
        
 /* General classes */
 body {width: 100% !important; margin: 0; padding: 0; -webkit-text-size-adjust:none; -ms-text-size-adjust:100%; font-size:13px; color:#333333; font-family: helvetica neue, helvetica, arial, verdana, san-serif; }
 img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic; border: none;}
 .bodytemplate, td { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; mso-line-height-rule:exactly }
 .bodytemplate { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; }
 .bodytemplate a, .bodytemplate a:hover { color: #F48C00; text-decoration: underline; }
 .main_table td{font-size:14px; color:#333333; text-align:left; font-family: helvetica neue, helvetica, arial, verdana, san-serif; padding:5px 10px;}
        
 <!-- NOTE: Remove this css-code to make te email scalable instead of responsive -->
 /* Smartphones (portrait and landscape) ----------- */
 @media only screen and (max-width:800px) {
 *[class=mHide] {display: none !important;}
 *[class=mWidth100] {width:100% !important; max-width: 100% !important;}
 *[class=mPaddingbottom] {padding-bottom:12px !important;}
 *[class=mGmail] {width:100% !important; min-width: 100% !important; padding: 0 10px !important;}
 *[class=titeloranje] {-webkit-border-radius: 15px; -moz-border-radius: 15px; border-radius: 15px; background-color:#F48C00;}
 *[class=nieuwsbrieftitel] { padding-top: 20px !important;}
 *[class=openhtml] { padding: 10px !important; }
 }
 <!-- NOTE: End CSS-code to remove if scalable email -->
 </style>
 </head>
 <body bgcolor="#ffffff">
 <table bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" width="100%" style="table-layout: fixed">
   <tbody>
     <tr>
       <td class="bodytemplate" align="center" valign="top">
 	  <table align="center" class="mGmail" style="width:800px; min-width:800px;  " cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
           <tbody>
             <tr>
               <td style="width:800px; max-width:800px; padding-bottom:20px;">
 	
 			  </td>
             </tr>
 			 <tr>
 			    <td>
 				<!--Paragraph content---->
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr>
 								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
 									Hi $$firstname$$,
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
        
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr>
 								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
 										Your home campus change request for [$$firstname$$ $$lastname$$] has been denied.
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 										<!--Paragraph content---->
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr>
 								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
 										If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <a href="mailto:support@mapworks.com" class="external-link" rel="nofollow">support@mapworks.com</a>
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr>
 								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
 									Thank you from the Skyfactor Mapworks team.</br>
 									<img src="https://synapse-qa.mnv-tech.com/images/Skyfactor-Mapworks-login.png"/><br/>
 									This email is an auto-generated message. Replies to automated messages are not monitored.
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
        
 			    </td>
 			</tr>
           </tbody>
         </table>
 		</td>
     </tr>
     <tr>
       <td>
        
 	  </td>
     </tr>
   </tbody>
 </table>
 </body>
</html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Activate_Email");
            UPDATE `email_template_lang` SET  `body`= \'<!-- Versie 2.0 !-->
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
 <head>
 <!--[if gte mso 9]>
 <xml>
 <o:OfficeDocumentSettings>
 <o:AllowPNG/>
 <o:PixelsPerInch>96</o:PixelsPerInch>
 </o:OfficeDocumentSettings>
 </xml>
 <![endif]-->
 <title>MAP-Works Faculty Invitation</title>
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <meta http-equiv="X-UA-Compatible" content="IE=edge" />
 <style type="text/css">
 /* e-mail bugfixes */
 #outlook a {padding: 0 0 0 0;}
 .ReadMsgBody {width: 100%;}
 .ExternalClass {width: 100%; line-height: 100%;}
 .ExternalClass * {line-height: 100%}
 sup, sub {vertical-align: baseline; position: relative; top: -0.4em;}
 sub {top: 0.4em;}
 .applelinks a {color:#262727; text-decoration: none;}
        
        
 /* General classes */
 body {width: 100% !important; margin: 0; padding: 0; -webkit-text-size-adjust:none; -ms-text-size-adjust:100%; font-size:13px; color:#333333; font-family: helvetica neue, helvetica, arial, verdana, san-serif; }
 img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic; border: none;}
 .bodytemplate, td { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; mso-line-height-rule:exactly }
 .bodytemplate { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; }
 .bodytemplate a, .bodytemplate a:hover { color: #F48C00; text-decoration: underline; }
 .main_table td{font-size:14px; color:#333333; text-align:left; font-family: helvetica neue, helvetica, arial, verdana, san-serif; padding:5px 10px;}
        
 <!-- NOTE: Remove this css-code to make te email scalable instead of responsive -->
 /* Smartphones (portrait and landscape) ----------- */
 @media only screen and (max-width:800px) {
 *[class=mHide] {display: none !important;}
 *[class=mWidth100] {width:100% !important; max-width: 100% !important;}
 *[class=mPaddingbottom] {padding-bottom:12px !important;}
 *[class=mGmail] {width:100% !important; min-width: 100% !important; padding: 0 10px !important;}
 *[class=titeloranje] {-webkit-border-radius: 15px; -moz-border-radius: 15px; border-radius: 15px; background-color:#F48C00;}
 *[class=nieuwsbrieftitel] { padding-top: 20px !important;}
 *[class=openhtml] { padding: 10px !important; }
 }
 <!-- NOTE: End CSS-code to remove if scalable email -->
 </style>
 </head>
 <body bgcolor="#ffffff">
 <table bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" width="100%" style="table-layout: fixed">
   <tbody>
     <tr>
       <td class="bodytemplate" align="center" valign="top">
 	  <table align="center" class="mGmail" style="width:800px; min-width:800px;  " cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
           <tbody>
             <tr>
               <td style="width:800px; max-width:800px; padding-bottom:20px;">
 	
 			  </td>
             </tr>
 			 <tr>
 			    <td>
 				<!--Paragraph content---->
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr>
 								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
 									Hi $$firstname$$ ,
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
        
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr>
 								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
 										Your Mapworks accounts have been merged, and this email has been set as your master account address and log-in. You may use the link below to reset your password and resume using Mapworks.
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<!--Paragraph content---->
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr>
 								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
 										<a href="#">[$$activation_token$$]</a>
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<!--Paragraph content---->
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr>
 								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
 										If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <a href="mailto:support@mapworks.com" class="external-link" rel="nofollow">support@mapworks.com</a>
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr>
 								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
 									Thank you from the Skyfactor Mapworks team.</br>
 								<img src="https://synapse-qa.mnv-tech.com/images/Skyfactor-Mapworks-login.png"/><br/>
 									This email is an auto-generated message. Replies to automated messages are not monitored.
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
        
 			    </td>
 			</tr>
           </tbody>
         </table>
 		</td>
     </tr>
     <tr>
       <td>
        
 	  </td>
     </tr>
   </tbody>
 </table>
 </body></html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Deactivate_Email");
            UPDATE `email_template_lang` SET  `body`= \'<!-- Versie 2.0 !-->
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
 <head>
 <!--[if gte mso 9]>
 <xml>
 <o:OfficeDocumentSettings>
 <o:AllowPNG/>
 <o:PixelsPerInch>96</o:PixelsPerInch>
 </o:OfficeDocumentSettings>
 </xml>
 <![endif]-->
 <title>MAP-Works Faculty Invitation</title>
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <meta http-equiv="X-UA-Compatible" content="IE=edge" />
 <style type="text/css">
 /* e-mail bugfixes */
 #outlook a {padding: 0 0 0 0;}
 .ReadMsgBody {width: 100%;}
 .ExternalClass {width: 100%; line-height: 100%;}
 .ExternalClass * {line-height: 100%}
 sup, sub {vertical-align: baseline; position: relative; top: -0.4em;}
 sub {top: 0.4em;}
 .applelinks a {color:#262727; text-decoration: none;}
        
        
 /* General classes */
 body {width: 100% !important; margin: 0; padding: 0; -webkit-text-size-adjust:none; -ms-text-size-adjust:100%; font-size:13px; color:#333333; font-family: helvetica neue, helvetica, arial, verdana, san-serif; }
 img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic; border: none;}
 .bodytemplate, td { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; mso-line-height-rule:exactly }
 .bodytemplate { font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 12px; color: #333333; }
 .bodytemplate a, .bodytemplate a:hover { color: #F48C00; text-decoration: underline; }
 .main_table td{font-size:14px; color:#333333; text-align:left; font-family: helvetica neue, helvetica, arial, verdana, san-serif; padding:5px 10px;}
        
 <!-- NOTE: Remove this css-code to make te email scalable instead of responsive -->
 /* Smartphones (portrait and landscape) ----------- */
 @media only screen and (max-width:800px) {
 *[class=mHide] {display: none !important;}
 *[class=mWidth100] {width:100% !important; max-width: 100% !important;}
 *[class=mPaddingbottom] {padding-bottom:12px !important;}
 *[class=mGmail] {width:100% !important; min-width: 100% !important; padding: 0 10px !important;}
 *[class=titeloranje] {-webkit-border-radius: 15px; -moz-border-radius: 15px; border-radius: 15px; background-color:#F48C00;}
 *[class=nieuwsbrieftitel] { padding-top: 20px !important;}
 *[class=openhtml] { padding: 10px !important; }
 }
 <!-- NOTE: End CSS-code to remove if scalable email -->
 </style>
 </head>
 <body bgcolor="#ffffff">
 <table bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" width="100%" style="table-layout: fixed">
   <tbody>
     <tr>
       <td class="bodytemplate" align="center" valign="top">
 	  <table align="center" class="mGmail" style="width:800px; min-width:800px;  " cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
           <tbody>
             <tr>
               <td style="width:800px; max-width:800px; padding-bottom:20px;">
 	
 			  </td>
             </tr>
 			 <tr>
 			    <td>
 				<!--Paragraph content---->
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr>
 								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
 									Hi $$firstname$$ ,
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
        
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr>
 								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
 										Your Mapworks accounts have been merged, and this account address has been deactivated. You will receive an email notification at your main account email address with instructions to reset your password.
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
        
 					<!--Paragraph content---->
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr>
 								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
 										If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <a href="mailto:support@mapworks.com" class="external-link" rel="nofollow">support@mapworks.com</a>
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
 					<!--Paragraph content---->
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr>
 								  <td style="font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;" align="left" valign="top">
 									Thank you from the Skyfactor Mapworks team.</br>
 									<img src="https://synapse-qa.mnv-tech.com/images/Skyfactor-Mapworks-login.png"/><br/>
 									This email is an auto-generated message. Replies to automated messages are not monitored.
 								  </td>
 								</tr>
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
        
 			    </td>
 			</tr>
           </tbody>
         </table>
 		</td>
     </tr>
     <tr>
       <td>
        
 	  </td>
     </tr>
   </tbody>
 </table>
 </body>
</html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Student_Upload_Notification");
            UPDATE `email_template_lang` SET  `body`= \'<html><head><style>body {background: none repeat scroll 0 0 #f4f4f4;} table {padding: 21px; width: 799px; font-family: helvetica,arial,verdana,san-serif; font-size:13px; color:#333; }</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$user_first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Your student upload has finished importing. $$download_failed_log_file$$ </td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Faculty_Upload_Notification");
            UPDATE `email_template_lang` SET  `body`= \'<html><head><style>body {background: none repeat scroll 0 0 #f4f4f4;} table {padding: 21px; width: 799px; font-family: helvetica,arial,verdana,san-serif; font-size:13px; color:#333; }</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$user_first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Your faculty upload has finished importing. $$download_failed_log_file$$ </td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Forgot_Password_Coordinator');
        
UPDATE `email_template_lang` SET `body`='<html>
<div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>
Hi \$\$firstname\$\$,<br/></br>
        
Please use the link below and follow the displayed instructions to create your new password. This link will expire after \$\$Reset_Password_Expiry_Hrs\$\$ hours.<br />
<br/>
\$\$activation_token\$\$<br/><br/>
        
If you believe that you received this email in error or if you have any questions,please contact Mapworks support at <span style=\'color: #99ccff;\'>\$\$Support_Helpdesk_Email_Address\$\$</span>.<br/><br/>
<p>Thank you from the Mapworks team.</br></p>
<p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
</div>
</html>' WHERE `email_template_id`=@emtid;");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
