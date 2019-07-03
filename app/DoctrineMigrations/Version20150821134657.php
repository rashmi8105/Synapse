<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150821134657 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
         
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Welcome_Email_Coordinator");
            UPDATE `email_template_lang` SET  `body`= \'<html>
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
        
				<tr style="background:#fff;border-collapse:collapse;"><td>Hi $$firstname$$,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>A Mapworks password was successfully created for your account.<br/><br/>If you believe that you received this email in error or if you have any questions, please contact Mapworks support at &nbsp;<a class="external-link" href="mailto:support@map-works.com" rel="nofollow" style="color: rgb(41, 114, 155); text-decoration: underline;">support@map-works.com</a><br/></td></tr>
        
<tr style="background:#fff;border-collapse:collapse;"><td>Thank you from the Skyfactor Mapworks team.</td></tr>
				<tr><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
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
