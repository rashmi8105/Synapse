<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150623112606 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
		$this->addSql("INSERT INTO `email_template` (`email_key`, `is_active`, `from_email_address`, `bcc_recipient_list`) VALUES ('Welcome_To_Mapworks', '1', 'no-reply@mapworks.com', 'ramesh.kumhar@techmahindra.com,SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');");
		
		$firstName = '$$firstname$$';
        $activationToken = '$$activation_token$$';
		$supportEmail = '$$Support_Helpdesk_Email_Address$$';
		$skyfactorLog = '$$Skyfactor_Mapworks_logo$$';
                
        $query = <<<CDATA
SET @wtmid := (SELECT id FROM email_template where email_key='Welcome_To_Mapworks'); 
INSERT INTO `email_template_lang` (`email_template_id`, `language_id`, `body`, `subject`) VALUES (@wtmid, '1', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
				<tr style="background:#fff;border-collapse:collapse;"><td>Welcome to Mapworks. Use the link below to create your password and start using Mapworks.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;">
					<td> <a href="$activationToken">$activationToken</a> </td>
				</tr>
				<tr style="background:#fff;border-collapse:collapse;">
					<td> If you believe this email is an error or if you have any questions, please contact Mapworks support at  <a href="mailto:$supportEmail">$supportEmail</a> </td>
				</tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Thank you from the Skyfactor Mapworks Team.<br/><img src="$skyfactorLog" alt="Skyfactor Mapworks logo" title="Skyfactor Mapworks logo" /></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
			
			</tbody>
		</table>
	</body>
</html>', 'Welcome to Mapworks');
CDATA;
        $this->addSql($query);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
