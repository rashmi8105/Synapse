<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150626235220 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Forgot_Password_Staff');
UPDATE `synapse`.`email_template_lang` SET `subject`='How to reset your Mapworks password' WHERE `email_template_id`=@emtid;
UPDATE `synapse`.`email_template_lang` SET `body`='<html>
 <div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>
 <p>Hi \$\$firstname\$\$,<br/></p>
         
 <p>Please use the link below and follow the displayed instructions to create your new password. This link will expire after \$\$Reset_Password_Expiry_Hrs\$\$ hours.
 <br/></p>
 \$\$activation_token\$\$<br/><br/>
         
 If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <span style=\'color: #99ccff;\'>\$\$Support_Helpdesk_Email_Address\$\$</span>.<br/><br/>
 <p>Thank you from the Skyfactor Mapworks team.</br></p>
 <p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/></p><br/>
 <p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
 </div>
 </html>' WHERE `email_template_id`=@emtid;");

        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Forgot_Password_Coordinator');
UPDATE `synapse`.`email_template_lang` SET `subject`='How to reset your Mapworks password' WHERE `email_template_id`=@emtid;
UPDATE `synapse`.`email_template_lang` SET `body`='<html>
 <div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>
 <p>Hi \$\$firstname\$\$,<br/></p>
         
 <p>Please use the link below and follow the displayed instructions to create your new password. This link will expire after \$\$Reset_Password_Expiry_Hrs\$\$ hours.
 <br/></p>
 \$\$activation_token\$\$<br/><br/>
         
 If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <span style=\'color: #99ccff;\'>\$\$Support_Helpdesk_Email_Address\$\$</span>.<br/><br/>
 <p>Thank you from the Skyfactor Mapworks team.</br></p>
 <p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/></p><br/>
 <p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
 </div>
 </html>' WHERE `email_template_id`=@emtid;");
 
        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Sucessful_Password_Reset_Staff');
UPDATE `synapse`.`email_template_lang` SET `body`='<html>
  <div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>
  <p>Hi \$\$firstname\$\$,<br/></p>
         
  <p>Your Mapworks password has been changed. If you believe this is an error, please contact Mapworks support at &nbsp;<a class='external-link' href=\'mailto:support@mapworks.com\' rel='nofollow' style='color: rgb(41, 114, 155); text-decoration: none;'>support@mapworks.com</a>
  <br/></p>
 <p>Thank you from the Skyfactor Mapworks team.</br></p>
 <p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></p><br/>
 <p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
         
  </div>
  </html>' WHERE `email_template_id`=@emtid;");
  
        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Sucessful_Password_Reset_Coordinator');
UPDATE `synapse`.`email_template_lang` SET `body`='<html>
  <div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>
  <p>Hi \$\$firstname\$\$,<br/></p>
         
  <p>Your Mapworks password has been changed. If you believe this is an error, please contact Mapworks support at &nbsp;<a class='external-link' href=\'mailto:support@mapworks.com\' rel='nofollow' style='color: rgb(41, 114, 155); text-decoration: none;'>support@mapworks.com</a>
  <br/></p>
 <p>Thank you from the Skyfactor Mapworks team.</br></p>
 <p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></p><br/>
 <p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
         
  </div>
  </html>' WHERE `email_template_id`=@emtid;");
  
  $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='AcademicUpdate_Upload_Notification');
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
 <tr style = \"background:#fff;border-collapse:collapse;\"> <td> Hi \$\$studentname\$\$, </td></tr> <tr style = \"background:#fff;border-collapse:collapse;\"> <td> 
 Your academic update upload has finished importing. Click here to download error file.</td> </tr>
 
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td> 
 <a href =\"#\">\$\$download_failed_log_file\$\$</a></td></tr>
 
 <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.<br/> 
 </td></tr> 
 
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td> 
 <img src = \"\$\$Skyfactor_Mapworks_logo\$\$\" alt = \"Skyfactor Mapworks logo\" title = \"Skyfactor Mapworks logo\" /> 
 </td></tr> 
 
 <tr style = \"background:#fff;border-collapse:collapse;\"> <td> This email is an auto - generated message.Replies to automated messages are not monitored. </td></tr> </tbody>
      </table> </body></html>' WHERE `email_template_id`=@emtid;");     
        
    }

	
	
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}