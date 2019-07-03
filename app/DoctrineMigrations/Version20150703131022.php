<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150703131022 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Forgot_Password_Staff');

UPDATE `synapse`.`email_template_lang` SET `body`='<html>
 <div style='margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);'>
 <p>Hi \$\$firstname\$\$,<br/></p>
         
 <p>Please use the link below and follow the displayed instructions to create your new password. This link will expire after \$\$Reset_Password_Expiry_Hrs\$\$ hours.
 <br/></p>
 <a href=\"\$\$activation_token\$\$\" target=\"_blank\">\$\$activation_token\$\$</a><br/><br/>
         
 If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <span style='color: #99ccff;'>\$\$Support_Helpdesk_Email_Address\$\$</span>.<br/><br/>
 <p>Thank you from the Skyfactor Mapworks team.</br></p>
 <p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/></p><br/>
 <p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
 </div>
 </html>' WHERE `email_template_id`=@emtid;");

        $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Forgot_Password_Coordinator');

UPDATE `synapse`.`email_template_lang` SET `body`='<html>
 <div style='margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);'>
 <p>Hi \$\$firstname\$\$,<br/></p>
         
 <p>Please use the link below and follow the displayed instructions to create your new password. This link will expire after \$\$Reset_Password_Expiry_Hrs\$\$ hours.
 <br/></p>
 <a href=\"\$\$activation_token\$\$\" target=\"_blank\">\$\$activation_token\$\$</a><br/><br/>
         
 If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <span style='color: #99ccff;\'>\$\$Support_Helpdesk_Email_Address$$</span>.<br/><br/>
 <p>Thank you from the Skyfactor Mapworks team.</br></p>
 <p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/></p><br/>
 <p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
 </div>
 </html>' WHERE `email_template_id`=@emtid;");
     
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}