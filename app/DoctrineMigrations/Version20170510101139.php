<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-14144 -  Updating Email templates
 */
class Version20170510101139 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $current_assignee_first_name = '$$current_assignee_first_name$$';
        $Skyfactor_Mapworks_logo = '$$Skyfactor_Mapworks_logo$$';
        $interested_party_first_name = '$$interested_party_first_name$$';
        $date_of_creation = '$$date_of_creation$$';
        $coordinator_first_name = '$$coordinator_first_name$$';
        $coordinator_last_name = '$$coordinator_last_name$$';
        $coordinator_email_address = '$$coordinator_email_address$$';
        $referral_student_count = '$$referral_student_count$$';
        $creator_first_name = '$$creator_first_name$$';
        $creator_last_name = '$$creator_last_name$$';
        $current_assignee_notification_hover_text = "$creator_first_name $creator_last_name has assigned you $referral_student_count new referrals.";
        $interested_party_notification_hover_text = "$creator_first_name $creator_last_name has added you as interested party for $referral_student_count new referrals.";
        $creator_notification_hover_text = "You have created new referrals for $referral_student_count students";


        //referral_bulk_action_assignee


        $this->addSql("INSERT INTO `synapse`.`email_template` (`email_key`, `from_email_address`) VALUES ('referral_bulk_action_assignee', 'no-reply@mapworks.com')");
        $this->addSql("SET @emailtemplateId = (SELECT id from email_template where email_key = 'referral_bulk_action_assignee')");

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
				<tr style="background:#fff;border-collapse:collapse;"><td>You have been has assigned new referrals for $referral_student_count students in Mapworks. Please sign in to your account to view and take action on these referrals.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Best regards,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Skyfactor Mapworks Team.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$Skyfactor_Mapworks_logo"/><br/></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
			</tbody>
		</table>
	</body>
</html>' ,
language_id = 1,
email_template_id = @emailtemplateId,
subject = 'Mapworks Referral Created'

SQL;
        $this->addSql($query);


        //referral_bulk_action_interested_party


        $this->addSql("INSERT INTO `synapse`.`email_template` (`email_key`, `from_email_address`) VALUES ('referral_bulk_action_interested_party', 'no-reply@mapworks.com')");
        $this->addSql("SET @emailtemplateId = (SELECT id from email_template where email_key = 'referral_bulk_action_interested_party')");

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
				<tr style="background:#fff;border-collapse:collapse;"><td>Hi $interested_party_first_name,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>You have been added as an interested party for $referral_student_count referrals on $date_of_creation.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>If you have any questions, please contact $coordinator_first_name $coordinator_last_name at $coordinator_email_address.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Best Regards,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Skyfactor Mapworks Team.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$Skyfactor_Mapworks_logo"/><br/></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
			</tbody>
		</table>
	</body>
</html>' ,
language_id = 1,
email_template_id = @emailtemplateId,
subject = 'Mapworks Referral Created'

SQL;
        $this->addSql($query);

        // updating  the mapworks action table for referral_bulk_action_assignee

        $this->addSql("UPDATE 
                             mapworks_action ma
                        SET
                             email_template_id = (SELECT id FROM email_template WHERE email_key = 'referral_bulk_action_assignee'),
                             notification_hover_text = '$current_assignee_notification_hover_text',
                             notification_body_text = '$current_assignee_notification_hover_text'
                        WHERE
                             event_key = 'referral_bulk_action_current_assignee';"
        );

        // updating  the mapworks action table for referral_bulk_action_interested_party

        $this->addSql("UPDATE 
                             mapworks_action ma
                        SET
                             email_template_id = (SELECT id FROM email_template WHERE email_key = 'referral_bulk_action_interested_party'),
                             notification_hover_text = '$interested_party_notification_hover_text',
                             notification_body_text = '$interested_party_notification_hover_text'
                        WHERE
                             event_key = 'referral_bulk_action_interested_party';"
        );


        // updating  the mapworks action table for referral_bulk_action_creator , only notification would be populated here

        $this->addSql("UPDATE 
                             mapworks_action ma
                        SET
                             notification_hover_text = '$creator_notification_hover_text',
                             notification_body_text = '$creator_notification_hover_text'
                        WHERE
                             event_key = 'referral_bulk_action_creator';"
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
