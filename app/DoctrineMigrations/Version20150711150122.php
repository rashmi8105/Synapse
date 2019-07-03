<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150711150122 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

		       $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Sucessful_Password_Reset_Staff');
UPDATE `email_template_lang` SET `body`='<html>
 <div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>
 Hi \$\$firstname\$\$,<br/><br/>
        
 Your Mapworks password has been changed. If you believe this is an error, please contact Mapworks support at &nbsp;<a href=\"mailto:\$\$Support_Helpdesk_Email_Address\$\$\" class=\"external-link\" rel=\"nofollow\">\$\$Support_Helpdesk_Email_Address\$\$</a>
 <br/><br/>
<p>Thank you from the Skyfactor Mapworks team.</br></p>
<p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
        
 </div>
 </html>' WHERE `email_template_id`=@emtid;");
        
        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Sucessful_Password_Reset_Coordinator');
UPDATE `email_template_lang` SET `body`='<html>
 <div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>
 Hi \$\$firstname\$\$,<br/><br/>
        
 Your Mapworks password has been changed. If you believe this is an error, please contact Mapworks support at &nbsp;<a href=\"mailto:\$\$Support_Helpdesk_Email_Address\$\$\" class=\"external-link\" rel=\"nofollow\">\$\$Support_Helpdesk_Email_Address\$\$</a>
 <br/><br/>
<p>Thank you from the Skyfactor Mapworks team.</br></p>
<p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
        
 </div>
 </html>' WHERE `email_template_id`=@emtid;");
 
 $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Sucessful_Password_Reset_Student');
UPDATE `email_template_lang` SET `body`='<html>
 <div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>
 Hi \$\$firstname\$\$,<br/><br/>
        
 Your Mapworks password has been changed. If you believe this is an error, please contact Mapworks support at &nbsp;<a href=\"mailto:\$\$Support_Helpdesk_Email_Address\$\$\" class=\"external-link\" rel=\"nofollow\">\$\$Support_Helpdesk_Email_Address\$\$</a>
 <br/><br/>
<p>Thank you from the Skyfactor Mapworks team.</br></p>
<p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
        
 </div>
 </html>' WHERE `email_template_id`=@emtid;");
 
  $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="MyAccount_Updated_Staff");
            UPDATE `email_template_lang` SET  `body`= \'<html>
        
 		<div style="margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);">
 			Hi $$firstname$$,<br/><br/>
        
 			An update to your Mapworks account was successfully made. The following information was updated: <br/><br/>
        
 			$$Updated_MyAccount_fields$$
 		<br/>
        
 			If you believe this is an error, please contact Mapworks support at&nbsp;<a href=\"mailto:\$\$Support_Helpdesk_Email_Address\$\$\" class=\"external-link\" rel=\"nofollow\">\$\$Support_Helpdesk_Email_Address\$\$</a></p>
         <br/><br/>
 			<p>Thank you from the Skyfactor Mapworks team.</br></p>
<p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
 	</div>
 </html>\'  WHERE `email_template_id`=@emtid;"
            ');
        
  
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}