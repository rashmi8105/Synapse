<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150703093010 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
			
			$this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='AcademicUpdate_Upload_Notification');
			UPDATE `synapse`.`email_template_lang` SET `subject`='Academic update upload finished importing' WHERE `email_template_id`=@emtid;
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
 Your academic update upload has finished importing. Click here to download error file.</td> </tr>
 
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td> 
 \$\$download_failed_log_file\$\$</td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td></td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.<br/> 
 </td></tr> 
 
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td> 
 <img src = \"\$\$Skyfactor_Mapworks_logo\$\$\" alt = \"Skyfactor Mapworks logo\" title = \"Skyfactor Mapworks logo\" /> 
 </td></tr> 
 
 <tr style = \"background:#fff;border-collapse:collapse;\"> <td> This email is an auto-generated message.Replies to automated messages are not monitored. </td></tr> </tbody>
      </table> </body></html>' WHERE `email_template_id`=@emtid;");     
	  
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
  </head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">
  <tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi \$\$user_first_name\$\$,</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>
  Your course upload has finished importing. Click here to download error file. </td></tr>
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td> 
 \$\$download_failed_log_file\$\$</td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td></td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.<br/> 
 </td></tr> 
 
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td> 
 <img src = \"\$\$Skyfactor_Mapworks_logo\$\$\" alt = \"Skyfactor Mapworks logo\" title = \"Skyfactor Mapworks logo\" /> 
 </td></tr> 
 
 <tr style = \"background:#fff;border-collapse:collapse;\"> <td> This email is an auto-generated message.Replies to automated messages are not monitored. </td></tr> </tbody>
      </table> </body></html>' WHERE `email_template_id`=@emtid;");
	  
	  
	  
	  $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Course_Faculty_Upload_Notification');
        
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
  </head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">
  <tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi \$\$user_first_name\$\$,</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>
  Your faculty upload has finished importing. Click here to download error file. </td></tr>
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td> 
 \$\$download_failed_log_file\$\$</td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td></td></tr>
 <tr style=\"background:#fff;border-collapse:collapse;\"><td>Thank you from the Skyfactor Mapworks team.<br/> 
 </td></tr> 
 
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td> 
 <img src = \"\$\$Skyfactor_Mapworks_logo\$\$\" alt = \"Skyfactor Mapworks logo\" title = \"Skyfactor Mapworks logo\" /> 
 </td></tr> 
 
 <tr style = \"background:#fff;border-collapse:collapse;\"> <td> This email is an auto-generated message.Replies to automated messages are not monitored. </td></tr> </tbody>
      </table> </body></html>' WHERE `email_template_id`=@emtid;");
	  
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
  </head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\">
  <tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Hi \$\$user_first_name\$\$,</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>
  Your student upload has finished importing. Click here to download error file. </td></tr>
 <tr style = \"background:#fff;border-collapse:collapse;\">  <td> 
 \$\$download_failed_log_file\$\$</td></tr>
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