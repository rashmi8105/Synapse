<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150827112633 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('INSERT INTO `email_template` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,NULL,"Email_PDF_Report_Student",NULL,"no-reply@mapworks.com","SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com");');
        
         $this->addSql('SET @mmid := (SELECT MAX(id) FROM email_template);
         INSERT INTO `email_template_lang` (`id`,`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (NULL,@mmid,1,NULL,NULL,NULL,NULL,NULL,NULL,"<!DOCTYPE html>\r\n<html>\r\n\r\n<body>\r\n<p> Hi $$studentname$$ </p><p>Survey PDF is generated. View it now</body></html>","Student Report");');
         
         $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Email_PDF_Report_Student");
            UPDATE `email_template_lang` SET  `body`= \'<html> <head>
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
             Your Survey has been completed for the Academic Year - $$academicyear$$, Please use the link below to download the Report</td> </tr>
             
             <tr style = "background:#fff;border-collapse:collapse;">  <td> 
             <a href ="$$pdf_report$$">Report view</a></td></tr>
             
             <tr style="background:#fff;border-collapse:collapse;"><td>Thank you from the Skyfactor Mapworks team.<br/> 
             </td></tr> 
             
             <tr style = "background:#fff;border-collapse:collapse;">  <td> 
             <img src = "$$Skyfactor_Mapworks_logo$$" alt = "Skyfactor Mapworks logo" title = "Skyfactor Mapworks logo" /> 
             </td></tr>
             
             <tr style = "background:#fff;border-collapse:collapse;"> <td> This email is an auto-generated message.Replies to automated messages are not monitored. </td></tr> </tbody>
                  </table> </body></html>\',`subject`="Student Report"  WHERE `email_template_id`=@emtid;"
            ');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
        