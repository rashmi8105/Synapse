<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150625230922 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');        
  
  $this->addSql("SET @emtid := (SELECT id FROM email_template where email_key='Send_Invitation_to_User');
UPDATE `synapse`.`email_template_lang` SET `body`='<html>
  <div style='margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);'>
  <p>Hi \$\$firstname\$\$,<br/></p>
         
  <p>Welcome to Mapworks. Use the link below to create your password and start using Mapworks.<br/></p>
  <p><a href=\"\$\$Coordinator_ResetPwd_URL_Prefix\$\$\">\$\$Coordinator_ResetPwd_URL_Prefix\$\$</a><br/></p>
  
  <p>If you believe that you received this email in error or if you have any questions, please contact Mapworks support at <a href=\"mailto:\$\$Support_Helpdesk_Email_Address\$\$\" class=\"external-link\" rel=\"nofollow\">\$\$Support_Helpdesk_Email_Address\$\$</a><br/></p>
  
 <p>Thank you from the Skyfactor Mapworks team.</br></p>
 <p><img alt=\"Skyfactor Mapworks Logo\" src=\"\$\$Skyfactor_Mapworks_logo\$\$\"/><br/></p><br/>
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