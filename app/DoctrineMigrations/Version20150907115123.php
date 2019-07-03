<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150907115123 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        
        $this->addSql('SET @aucanfalty := (SELECT id FROM email_template where email_key="Academic_Update_Cancel_to_Faculty");
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

<tr style="background:#fff;border-collapse:collapse;"><td><p>Thank you.</br></p>
 <p><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></p></td></tr>

<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table>
 </body>
</html>\'  WHERE `email_template_id`=@aucanfalty;"
            ');
			
			
			$this->addSql('SET @auremfaculty := (SELECT id FROM email_template where email_key="Academic_Update_Reminder_to_Faculty");
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
        
 style="background:#fff;border-collapse:collapse;"><td>$$custom_message$$</td></tr><tr style="background:#fff;border-collapse:collapse;"><td><p>Thank you.</br></p>
 <p><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></p></td></tr><tr style="background:#fff;border-
        
 collapse:collapse;"><td>This email is an auto-generated message.
        
 Replies to automated messages are not monitored.</td></tr></tbody></table>
</body></html>\'  WHERE `email_template_id`=@auremfaculty;"
            ');
			
			
$this->addSql('SET @aursid := (SELECT id FROM email_template where email_key=\'Academic_Update_Request_Staff\');
        UPDATE `email_template_lang` SET `body`=\'<!DOCTYPE html>
		<html>
		<head>
		<title></title>
		</head>
		<body>
		<p>
		Please submit your academic updates for this request:
		</p>
		<table class=\"table table-bordered\" style=\"border: 1px solid #428BCA !important; border-collapse:collapse; width:40%; margin-top:20px; margin-bottom:20px;\">
			<tr>
				<td style=\" padding:5px; background-color: #4F9BD9; border: 1px solid #428BCA !important; border-collapse:collapse\"><p style=\"font-weight: bold; font-size: 14px; color:#fff;">View and complete this academic update request on Mapworks <a href=\"\$\$updateviewurl\$\$\">\$\$updateviewurl\$\$</a></p></td>
			</tr>
			<tr>
				<td style=\"background-color: #D4EEFF; border: 1px solid #4F9BD9 !important; border-collapse:collapse; padding:15px 5px 15px 5px; vertical-align: middle;\">
					<p style=\"font-size:14px; font-weight: bold;   margin: 0px !important;\">\$\$requestname\$\$ (due <span>\$\$duedate\$\$</span>)</p>
					<p style=\"font-size:14px;   margin: 0px !important;\">\$\$description\$\$</p>
					<p style=\"font-size:14px;   margin: 0px !important;\">Requestor: <span>\$\$requestor\$\$</span>&nbsp;<span>\$\$requestor_email\$\$</span></p>
					<p style=\"display:inline-block; float:left; font-size:14px;   margin: 0px !important;\">Student Updates: <span>\$\$studentupdate\$\$</span></p>
					<a style=\"width: 65px; height:20px; background-color:#ccc; text-align: center; display:inline-block; float: right; border: 1px solid #ccc; margin: 2px; text-decoration: none; color:#000;\" href=\"\$\$updateviewurl\$\$\">Update</a>
				</td>	
			</tr>
		</table>
		<p style=\"width:40%; margin-bottom:20px;\">\$\$optional_message\$\$</p>
		</body>
		</html>\' WHERE `email_template_id`=@aursid;');


$this->addSql('SET @aunotistud := (SELECT id FROM email_template where email_key="Academic_Update_Notification_Student");
            UPDATE `email_template_lang` SET  `body`= \'<html>
    <head>
    <style> body {
        background: none repeat scroll 0 0# f4f4f4;

    }
table {
    padding: 21 px;
    width: 799 px;


    font - family: helvetica,
    arial,
    verdana,
    san - serif;
    font - size: 13 px;
    color: #333;
 }
 	</style>
 </head><body><table cellpadding= "10" style = "background:#eeeeee;" cellspacing = "0"> 
 <tbody> 
 <tr style = "background:#fff;border-collapse:collapse;"> <td> Hi $$studentname$$, </td></tr> 
 <tr style = "background:#fff;border-collapse:collapse;"> <td> 
 You have received an academic update for one or more of your courses. Click here to review your update.</td> </tr>
 
 <tr style = "background:#fff;border-collapse:collapse;">  <td> 
 <a href ="$$student_update_link$$">course view</a></td></tr>
 
 <tr style="background:#fff;border-collapse:collapse;"><td>Thank you.<br/> 
 </td></tr> 
 
 <tr style = "background:#fff;border-collapse:collapse;">  <td> 
 <img src = "$$Skyfactor_Mapworks_logo$$" alt = "Skyfactor Mapworks logo" title = "Skyfactor Mapworks logo" /> 
 </td></tr>
 
 <tr style = "background:#fff;border-collapse:collapse;"> <td> This email is an auto-generated message.Replies to automated messages are not monitored. </td></tr> </tbody>
      </table> </body></html>\'  WHERE `email_template_id`=@aunotistud;"
            ');

$this->addSql("SET @auuploadnoti := (SELECT id FROM email_template where email_key='AcademicUpdate_Upload_Notification');
			
UPDATE `synapse`.`email_template_lang` SET `body`='<html>
    <head>
    <style> body {
        background: none repeat scroll 0 0# f4f4f4;

    }
table {
    padding: 21 px;
    width: 799 px;


    font - family: helvetica,
    arial,
    verdana,
    san - serif;
    font - size: 13 px;
    color: #333;
 }
 	</style>
 </head><body><table cellpadding= \"10\" style = \"background:#eeeeee;\" cellspacing = \"0\"> 
 <tbody> 
 <tr style = \"background:#fff;border-collapse:collapse;\"> <td> Hi \$\$user_first_name\$\$, </td></tr> <tr style = \"background:#fff;border-collapse:collapse;\"> <td> 
 Your academic update upload has finished importing.</td> </tr>
 
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td> 
 \$\$download_failed_log_file\$\$</td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td></td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you.<br/> 
 </td></tr> 
 
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td> 
 <img src = \"\$\$Skyfactor_Mapworks_logo\$\$\" alt = \"Skyfactor Mapworks logo\" title = \"Skyfactor Mapworks logo\" /> 
 </td></tr> 
 
 <tr style = \"background:#fff;border-collapse:collapse;\"> <td> This email is an auto-generated message.Replies to automated messages are not monitored. </td></tr> </tbody>
      </table> </body></html>' WHERE `email_template_id`=@auuploadnoti;");			
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
