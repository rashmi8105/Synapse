<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150703110010 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
	
$this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Deactivate_Email');			
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
 <tr style = \"background:#fff;border-collapse:collapse;\"> <td> Hi \$\$firstname\$\$, </td></tr> <tr style = \"background:#fff;border-collapse:collapse;\"> <td> 
 Your Mapworks accounts have been merged, and this account address has been deactivated. You will receive an email notification at your main account email address with instructions to reset your password.</td> </tr>

								<tr>
 								  <td style=\"font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;\" align=\"left\" valign=\"top\">
 										If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <a href=\"mailto:\$\$Support_Helpdesk_Email_Address\$\$\" class=\"external-link\" rel=\"nofollow\">\$\$Support_Helpdesk_Email_Address\$\$</a>
 								  </td>
 								</tr>
 
 <tr style=\"background:#fff;border-collapse:collapse;\"><td></td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.<br/> 
 </td></tr> 
 
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td> 
 <img src = \"\$\$Skyfactor_Mapworks_logo\$\$\" alt = \"Skyfactor Mapworks logo\" title = \"Skyfactor Mapworks logo\" /> 
 </td></tr> 
 
 <tr style = \"background:#fff;border-collapse:collapse;\"> <td> This email is an auto-generated message.Replies to automated messages are not monitored. </td></tr> </tbody>
      </table> </body></html>' WHERE `email_template_id`=@emtid;"); 
	  
	  $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Activate_Email');			
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
 <tr style = \"background:#fff;border-collapse:collapse;\"> <td> Hi \$\$firstname\$\$, </td></tr>
 <tr>
 								  <td style=\"font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;\" align=\"left\" valign=\"top\">
 										Your Mapworks accounts have been merged, and this email has been set as your master account address and log-in. You may use the link below to reset your password and resume using Mapworks.
 								  </td>
 								</tr>								
									<tr>
 								  <td style=\"font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;\" align=\"left\" valign=\"top\">
 										<a href=\"\$\$Coordinator_ResetPwd_URL_Prefix\$\$\">\$\$Coordinator_ResetPwd_URL_Prefix\$\$\</a>
 								  </td>
 								</tr>

								<tr>
 								  <td style=\"font-family: helvetica neue, helvetica, arial, verdana, san-serif; font-size: 14px; color: #262727; line-height:18px;\" align=\"left\" valign=\"top\">
 										If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <a href=\"mailto:\$\$Support_Helpdesk_Email_Address\$\$\" class=\"external-link\" rel=\"nofollow\">\$\$Support_Helpdesk_Email_Address\$\$</a>
 								  </td>
 								</tr>
 
 <tr style=\"background:#fff;border-collapse:collapse;\"><td></td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.<br/> 
 </td></tr> 
 
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td> 
 <img src = \"\$\$Skyfactor_Mapworks_logo\$\$\" alt = \"Skyfactor Mapworks logo\" title = \"Skyfactor Mapworks logo\" /> 
 </td></tr> 
 
 <tr style = \"background:#fff;border-collapse:collapse;\"> <td> This email is an auto-generated message.Replies to automated messages are not monitored. </td></tr> </tbody>
      </table> </body></html>' WHERE `email_template_id`=@emtid;"); 	

 
        
      
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
    }
} 