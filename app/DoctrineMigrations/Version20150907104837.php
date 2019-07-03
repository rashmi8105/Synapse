<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150907104837 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('SET @welemstaff := (SELECT id FROM email_template where email_key="Welcome_Email_Staff");
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
				<tr style="background:#fff;border-collapse:collapse;"><td>A Mapworks password was successfully created for your account.<br/><br/>If you believe that you received this email in error or if you have any questions, please contact Mapworks support at &nbsp;<a class="external-link" href="mailto:$$Support_Helpdesk_Email_Address$$" rel="nofollow" style="color: rgb(41, 114, 155); text-decoration: underline;">$$Support_Helpdesk_Email_Address$$</a><br/></td></tr>
        
<tr style="background:#fff;border-collapse:collapse;"><td>Thank you.</td></tr>
				<tr><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>\'  WHERE `email_template_id`=@welemstaff;"
            ');
        
        $this->addSql("SET @forgotpassstaff := (SELECT id FROM email_template where email_key='Forgot_Password_Staff');
        
UPDATE `synapse`.`email_template_lang` SET `body`='<html>
  <div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">
  <p>Hi \$\$firstname\$\$,<br/></p>
        
  <p>Please use the link below and follow the displayed instructions to create your new password. This link will expire after \$\$Reset_Password_Expiry_Hrs\$\$ hours.
  <br/></p>
  <a href=\"\$\$activation_token\$\$\" target=\"_blank\">\$\$activation_token\$\$</a><br/><br/>
        
  If you believe that you received this email in error or if you have any questions, please contact Mapworks support at
 <span style=\"color: #99ccff;\">\$\$Support_Helpdesk_Email_Address\$\$</span>.<br/><br/>
  <p>Thank you.</br></p>
  <p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\" /></p><br/>
  <p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
  </div>
  </html>
' WHERE `email_template_id`=@forgotpassstaff;");
        
        $this->addSql('SET @myaccountUpStaff := (SELECT id FROM email_template where email_key="MyAccount_Updated_Staff");
            UPDATE `email_template_lang` SET  `body`= \'<html>
     
  		<div style="margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);">
  			Hi $$firstname$$,<br/><br/>
     
  			An update to your Mapworks account was successfully made. The following information was updated: <br/><br/>
     
  			$$Updated_MyAccount_fields$$
  		<br/>
     
  			If you believe that you received this email in error or if you have any questions, please contact Mapworks support at &nbsp;<a href="mailto:$$Support_Helpdesk_Email_Address$$" class="external-link" rel="nofollow">$$Support_Helpdesk_Email_Address$$</a></p>
          <br/>
  			<p>Thank you.</br></p>
 <p><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></p>
 <p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
  	</div>
  </html>\'  WHERE `email_template_id`=@myaccountUpStaff;"
            ');
        
        $this->addSql("SET @forpasscoord:= (SELECT id FROM email_template where email_key='Forgot_Password_Coordinator');
        
UPDATE `synapse`.`email_template_lang` SET `body`='<html>
  <div style=\"margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\">
  <p>Hi \$\$firstname\$\$,<br/></p>
        
  <p>Please use the link below and follow the displayed instructions to create your new password. This link will expire after \$\$Reset_Password_Expiry_Hrs\$\$ hours.
  <br/></p>
  <a href=\"\$\$activation_token\$\$\" target=\"_blank\">\$\$activation_token\$\$</a><br/><br/>
        
  If you believe that you received this email in error or if you have any questions, please contact Mapworks support at
 <span style=\"color: #99ccff;\">\$\$Support_Helpdesk_Email_Address\$\$</span>.<br/><br/>
  <p>Thank you.</br></p>
  <p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\" /></p><br/>
  <p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
  </div>
  </html>
' WHERE `email_template_id`=@forpasscoord;");
        
        $this->addSql('SET @welcemailcoord := (SELECT id FROM email_template where email_key="Welcome_Email_Coordinator");
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
        
<tr style="background:#fff;border-collapse:collapse;"><td>Thank you.</td></tr>
				<tr><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>\'  WHERE `email_template_id`=@welcemailcoord;"
            ');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
