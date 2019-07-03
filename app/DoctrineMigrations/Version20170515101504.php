<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-14147 and ESPRJ-14066
 */
class Version20170515101504 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $current_assignee_first_name = '$$current_assignee_first_name$$';
        $creator_first_name = '$$creator_first_name$$';
        $creator_last_name = '$$creator_last_name$$';
        $skyfactor_mapworks_logo = '$$Skyfactor_Mapworks_logo$$';
        $student_first_name = '$$student_first_name$$';
        $student_last_name = '$$student_last_name$$';
        $date_of_creation = '$$date_of_creation$$';
        $coordinator_first_name = '$$coordinator_first_name$$';
        $coordinator_last_name = '$$coordinator_last_name$$';
        $coordinator_email_address = '$$coordinator_email_address$$';

        //referral student_made_nonparticipant_assignee
        $this->addSql("INSERT INTO `synapse`.`email_template` (`email_key`, `from_email_address`, `is_active`) VALUES ('referral_student_made_nonparticipant_assignee', 'no-reply@mapworks.com', '1')");
        $this->addSql("SET @emailtemplateId = (SELECT id from email_template where email_key = 'referral_student_made_nonparticipant_assignee')");
        $query = <<<SQL
INSERT INTO `email_template_lang`
SET
    `body` = '
<html>
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
				<tr style="background:#fff;border-collapse:collapse;"><td>Hi $current_assignee_first_name,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>The referral you were assigned for $student_first_name $student_last_name on</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>$date_of_creation is no longer visible as they are no longer participating in Mapworks.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>If you have any questions, please contact $coordinator_first_name $coordinator_last_name at</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>$coordinator_email_address</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Best regards,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Skyfactor Mapworks Team</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$skyfactor_mapworks_logo"/><br/></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
			</tbody>
		</table>
	</body>
</html>' ,
language_id = 1,
email_template_id = @emailtemplateId,
subject = 'New Mapworks Referral'
SQL;
        $this->addSql($query);

        //referral student made nonparticipant creator
        $this->addSql("INSERT INTO `synapse`.`email_template` (`email_key`, `from_email_address`, `is_active`) VALUES ('referral_student_made_nonparticipant_creator', 'no-reply@mapworks.com', '1')");
        $this->addSql("SET @emailtemplateId = (SELECT id from email_template where email_key = 'referral_student_made_nonparticipant_creator')");
        $query = <<<SQL
INSERT INTO `email_template_lang`
SET
    `body` = '
<html>
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
				<tr style="background:#fff;border-collapse:collapse;"><td>Hi $creator_first_name,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>The referral you created for $student_first_name $student_last_name on $date_of_creation is</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>no longer visible as they are no longer participating in Mapworks.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>If you have any questions, please contact $coordinator_first_name $coordinator_last_name at</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>$coordinator_email_address</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Best regards,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Skyfactor Mapworks Team</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$skyfactor_mapworks_logo"/><br/></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
			</tbody>
		</table>
	</body>
</html>' ,
language_id = 1,
email_template_id = @emailtemplateId,
subject = 'New Mapworks Referral'
SQL;
        $this->addSql($query);

        //update mapworks action for referral_student_made_nonparticipant_current_assignee
        $this->addSql("UPDATE 
                             mapworks_action ma
                        SET
                             email_template_id = (SELECT id FROM email_template WHERE email_key = 'referral_student_made_nonparticipant_assignee'),
                             notification_hover_text = 'Your referral for $student_first_name $student_last_name is no longer visible.',
                             notification_body_text = 'Your referral for $student_first_name $student_last_name is no longer visible. This student is no longer participating in Mapworks.'
                        WHERE
                             event_key = 'referral_student_made_nonparticipant_current_assignee';");

        //update mapworks action for referral_student_made_nonparticipant_interested_party
        $this->addSql("UPDATE 
                             mapworks_action ma
                        SET
                             notification_hover_text = 'Your referral for $student_first_name $student_last_name is no longer visible.',
                             notification_body_text = 'Your referral for $student_first_name $student_last_name is no longer visible. This student is no longer participating in Mapworks.'
                        WHERE
                             event_key = 'referral_student_made_nonparticipant_interested_party';");

        //update mapworks action for referral_student_made_nonparticipant_creator
        $this->addSql("UPDATE 
                             mapworks_action ma
                        SET
                             email_template_id = (SELECT id FROM email_template WHERE email_key = 'referral_student_made_nonparticipant_creator'),
                             notification_hover_text = 'Your referral for $student_first_name $student_last_name is no longer visible.',
                             notification_body_text = 'Your referral for $student_first_name $student_last_name is no longer visible. This student is no longer participating in Mapworks.'
                        WHERE
                             event_key = 'referral_student_made_nonparticipant_creator';");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
