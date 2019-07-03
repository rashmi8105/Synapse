<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150703120922 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

         $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Accept_Change_Request");
		 
		 UPDATE `synapse`.`email_template_lang` SET `subject`=\'Home campus change request approved\' WHERE `email_template_id`=@emtid;
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
 <title>Home Campus Change Request Approved </title>
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
 										Your home campus change request for $$firstname$$ $$lastname$$ has been approved.
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
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr style=\"background:#fff;border-collapse:collapse;\"><td></td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.<br/> 
 </td></tr> 
 
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td> 
 <img src = \"\$\$Skyfactor_Mapworks_logo\$\$\" alt = \"Skyfactor Mapworks logo\" title = \"Skyfactor Mapworks logo\" /> 
 </td></tr> 
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
        
 			    </td>
 			</tr>
			<tr style = \"background:#fff;border-collapse:collapse;\"> <td> This email is an auto-generated message.Replies to automated messages are not monitored. </td></tr>
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
			
			
			$this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Deny_Change_Request");
			UPDATE `synapse`.`email_template_lang` SET `subject`=\'Home campus change request denied\' WHERE `email_template_id`=@emtid;
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
 <title>Home Campus Change Request Denied</title>
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
 										Your home campus change request for $$firstname$$ $$lastname$$ has been denied.
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
 					<table cellpadding="0" cellspacing="0" border="0">
 					  <tbody>
 						<tr>
 						  <td style="width:800px; max-width:800px; padding-bottom:20px;">
 						  <table width="100%" cellpadding="0" cellspacing="0" border="0">
 							  <tbody>
 								<tr style=\"background:#fff;border-collapse:collapse;\"><td></td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.<br/> 
 </td></tr> 
 
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td> 
 <img src = \"\$\$Skyfactor_Mapworks_logo\$\$\" alt = \"Skyfactor Mapworks logo\" title = \"Skyfactor Mapworks logo\" /> 
 </td></tr> 
 							  </tbody>
 							</table>
 							</td>
 						</tr>
 					  </tbody>
 					</table>
        
 			    </td>
 			</tr>
			<tr style = \"background:#fff;border-collapse:collapse;\"> <td> This email is an auto-generated message.Replies to automated messages are not monitored. </td></tr>
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
  
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}