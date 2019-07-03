<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150707133432 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Academic_Update_Notification_Student");
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
 
 <tr style="background:#fff;border-collapse:collapse;"><td>Thank you from the Skyfactor Mapworks team.<br/> 
 </td></tr> 
 
 <tr style = "background:#fff;border-collapse:collapse;">  <td> 
 <img src = "$$Skyfactor_Mapworks_logo$$" alt = "Skyfactor Mapworks logo" title = "Skyfactor Mapworks logo" /> 
 </td></tr>
 
 <tr style = "background:#fff;border-collapse:collapse;"> <td> This email is an auto-generated message.Replies to automated messages are not monitored. </td></tr> </tbody>
      </table> </body></html>\',`subject`="Mapworks academic update notification"  WHERE `email_template_id`=@emtid;"
            ');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

       
    }
}
