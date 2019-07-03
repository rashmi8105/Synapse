<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150803094443 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $fullName = '$$fullname$$';
        $delegatorName = '$$delegater_name$$';
        $logo = '$$Skyfactor_Mapworks_logo$$';
      
        $query = <<<CDATA
        SET @emtid := (SELECT id FROM email_template where email_key="Remove_Delegate");
        UPDATE `email_template_lang` SET  `body`= '<html>
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
        
				<tr style="background:#fff;border-collapse:collapse;"><td>Hi $fullName,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>You have been removed as a delegate user for $delegatorName calendar.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Thank you from the Skyfactor Mapworks team.</td></tr>				
				<tr><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$logo"/><br/></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>'  WHERE `email_template_id`=@emtid;
CDATA;
        $this->addSql($query);
        
        $query1 = <<<CDATA
        SET @emtid := (SELECT id FROM email_template where email_key="Add_Delegate");
        UPDATE `email_template_lang` SET  `body`= '<html>
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
        
				<tr style="background:#fff;border-collapse:collapse;"><td>Hi $fullName,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>You have been added as a delegate user for $delegatorName calendar.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Thank you from the Skyfactor Mapworks team.</td></tr>
				<tr><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$logo"/><br/></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>'  WHERE `email_template_id`=@emtid;
CDATA;
        
        $this->addSql($query1);
                
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Welcome_Email_Coordinator");
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
        
				<tr style="background:#fff;border-collapse:collapse;"><td>Hi $$firstname$$,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>A Mapworks password was successfully created for your account.<br/><br/>If you believe that you received this email in error or if you have any questions, please contact Mapworks support at &nbsp;<a class="external-link" href="mailto:support@mapworks.com" rel="nofollow" style="color: rgb(41, 114, 155); text-decoration: underline;">support@mapworks.com</a><br/></td></tr>

<tr style="background:#fff;border-collapse:collapse;"><td>Thank you from the Skyfactor Mapworks team.</td></tr>				
				<tr><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="MyAccount_Updated_Staff");
            UPDATE `email_template_lang` SET  `body`= \'<html>
         
  		<div style="margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);">
  			Hi $$firstname$$,<br/><br/>
         
  			An update to your Mapworks account was successfully made. The following information was updated: <br/><br/>
         
  			$$Updated_MyAccount_fields$$
  		<br/>
         
  			If you believe that you received this email in error or if you have any questions, please contact Mapworks support at &nbsp;<a href="mailto:$$Support_Helpdesk_Email_Address$$" class="external-link" rel="nofollow">$$Support_Helpdesk_Email_Address$$</a></p>
          <br/>
  			<p>Thank you from the Skyfactor Mapworks team.</br></p>
 <p><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></p>
 <p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
  	</div>
  </html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
        

        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Academic_Update_Cancel_to_Faculty");
            UPDATE `email_template_lang` SET  `body`= \'<html>
 <head>
 <style>body{background: none repeat scroll 0 0 #f4f4f4;}
 table{
 padding: 21px; width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;
 color:#333;}</style>
 </head>
 <body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0">
 <tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$faculty_name$$,</td></tr>
 <tr style="background:#fff;border-collapse:collapse;"><td>This academic update request has been cancelled and removed from your queue:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>$$request_name$$ ($$due_date$$)</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>$$request_description$$</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Requestor: $$requestor_name$$ ($$requestor_email$$)</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Student Updates: $$student_update_count$$</td></tr>
 <tr style="background:#fff;border-collapse:collapse;"><td>$$custom_message$$</td></tr>

<tr style="background:#fff;border-collapse:collapse;"><td><p>Thank you from the Skyfactor Mapworks team.</br></p>
 <p><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></p></td></tr>

<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table>
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
        
 style="background:#fff;border-collapse:collapse;"><td>Dear $$faculty_name$$,</td></tr>
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
        
 style="background:#fff;border-collapse:collapse;"><td>$$custom_message$$</td></tr><tr style="background:#fff;border-collapse:collapse;"><td><p>Thank you from the Skyfactor Mapworks team.</br></p>
 <p><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></p></td></tr><tr style="background:#fff;border-
        
 collapse:collapse;"><td>This email is an auto-generated message.
        
 Replies to automated messages are not monitored.</td></tr></tbody></table>
</body></html>\'  WHERE `email_template_id`=@emtid;"
            ');
         
         }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

       
    }
}
