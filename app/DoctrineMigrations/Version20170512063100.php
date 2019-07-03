<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-14145
 *
 * Migration script for creating new email template referral_reassign_new_assignee
 */
class Version20170512063100 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //creating email key for referral_reassign_new_assignee
        $this->addSql('INSERT INTO email_template (email_key, from_email_address) VALUES ("referral_reassign_current_assignee", "no-reply@mapworks.com")');

        //Inserting email template for referral_reassign_new_assignee
        $this->addSql('SET @email_template_id := (SELECT id FROM email_template where email_key="referral_reassign_current_assignee");
            INSERT INTO `email_template_lang` (email_template_id, language_id, body, subject) VALUES (@email_template_id, 1, \'<html>
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
        
				<tr style="background:#fff;border-collapse:collapse;"><td>Hi $$current_assignee_first_name$$,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>$$updater_first_name$$ $$updater_last_name$$ assigned you a referral for $$student_first_name$$ $$student_last_name$$ in Mapworks. Please sign in to your account to view and take action on this referral.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>Skyfactor Mapworks Team</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>\',\'Mapworks Referral Reassigned\')');



    }


    public function down(Schema $schema)
    {

    }
}
