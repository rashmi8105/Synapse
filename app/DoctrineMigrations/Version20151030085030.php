<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151030085030 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $firstName = '$$firstname$$';
        $activationToken = '$$activation_token$$';
        $supportEmail = '$$Support_Helpdesk_Email_Address$$';
        $skyfactorLog = '$$Skyfactor_Mapworks_logo$$';
        
        $query = <<<CDATA
          INSERT INTO `email_template` ( `created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) 
                    VALUES  (NULL, NULL, NULL, NULL, NULL, NULL, "Welcome_Email_Skyfactor_Admin_User",NULL,"no-reply@mapworks.com","SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,MK00361563@TechMahindra.com");
                    
        SET @eTempId := (select id from email_template where `email_key` = "Welcome_Email_Skyfactor_Admin_User");
        SET @langId := (select id from language_master where langcode = 'en_US');
       
INSERT INTO `email_template_lang` (`email_template_id`, `language_id`, `body`, `subject`) VALUES (@eTempId, @langId, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<style>
		body {
    background: none repeat scroll 0 0 #f4f4f4;
	
}
		table {
    padding: 21px;
    width: 799px;
	font-family: helvetica,arial,verdana,san-serif;
	font-size:13px;
	color:#333;
	}
		</style>
	</head>
	<body>
	
		<table cellpadding="10" style="background:#eeeeee;" cellspacing="0">
			<tbody>
			
				<tr style="background:#fff;border-collapse:collapse;"><td>Hi $firstName,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Welcome to Skyfactor. Use the link below to create your password and login to user management. This link will expire in 24 hours.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;">
					<td> <a href="$activationToken">$activationToken</a> </td>
				</tr>
				<tr style="background:#fff;border-collapse:collapse;">
					<td> If you believe that you received this email in error or if you have any questions, please contact Mapworks support at  <a href="mailto:$supportEmail">$supportEmail</a> </td>
				</tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Thank you from the Skyfactor team.<br/><img src="$skyfactorLog" alt="Skyfactor Mapworks logo" title="Skyfactor Mapworks logo" /></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
			
			</tbody>
		</table>
	</body>
</html>', 'Welcome to Skyfactor');
CDATA;
        $this->addSql($query);
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
    }
}
