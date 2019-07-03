<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150710200419 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Welcome_To_Mapworks");
         
            UPDATE `email_template_lang` SET  `body`= \'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
         
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head>
     <title>Email</title>
 </head>
 <body>
 <center>
	
     <table align="center">
		<tr>
			<td><p>Hi $$firstname$$,</p></td>
		</tr>
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
 				If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <a href="mailto:$$Support_Helpdesk_Email_Address$$" style="link:#1e73d5;">$$Support_Helpdesk_Email_Address$$</a>.
 				
 				<div style="text-align:left;font-weight:bold;font-size: 14px;color:#333333" >
 					<p>Thank you from the Skyfactor Mapworks team.</br></p>
					<p><img alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></p>
					<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
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
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
