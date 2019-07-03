<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150605180022 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Course_Upload_Notification');
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
  </head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\"><tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear \$\$user_first_name\$\$:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Your course upload has finished importing. \$\$download_failed_log_file\$\$ </td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>' WHERE `email_template_id`=@emtid;");
  
  
  $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Course_Student_Upload_Notification');
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
  </head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\"><tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear \$\$user_first_name\$\$:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Your student upload has finished importing. click here to download error file. </td></tr>
    <tr style=\"background:#fff;border-collapse:collapse;\"><td><a href=\"#\">link downloads an upload error file. </a></td></tr>
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
  </head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\"><tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear \$\$user_first_name\$\$:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Your faculty upload has finished importing. click here to download error file. </td></tr>
  <tr style=\"background:#fff;border-collapse:collapse;\"><td><a href=\"#\">link downloads an upload error file. </a></td></tr>
  <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.<br/><img alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>' WHERE `email_template_id`=@emtid;");
  
  
   $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='AcademicUpdate_Upload_Notification');
UPDATE `synapse`.`email_template_lang` SET `body`='<html>
 
 <body>
 <p> Hi \$\$studentname\$\$ </p> 
 <p>Your academic update upload has finished importing.click here to download error file.</p>
 <p><a href=\"#\">link downloads an upload error file</a></p>
 <p>Thank you from the Skyfactor Mapworks team.</br></p>
<p><img alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
 </body></html>' WHERE `email_template_id`=@emtid;");
  
  $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Send_Invitation_to_User');
UPDATE `synapse`.`email_template_lang` SET `body`='<o:AllowPNG/>
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
 										<a href=\"\$\$Coordinator_ResetPwd_URL_Prefix\$\$\">password reset link here</a>
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
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}