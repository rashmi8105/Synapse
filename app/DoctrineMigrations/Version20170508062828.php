<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Updating Email templates as per ESPRJ-13964
 */
class Version20170508062828 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        $current_assignee_first_name = '$$current_assignee_first_name$$';
        $current_assignee_last_name = '$$current_assignee_last_name$$';
        $creator_first_name = '$$creator_first_name$$';
        $creator_last_name = '$$creator_last_name$$';
        $Skyfactor_Mapworks_logo = '$$Skyfactor_Mapworks_logo$$';
        $student_first_name = '$$student_first_name$$';
        $student_last_name = '$$student_last_name$$';
        $interested_party_first_name = '$$interested_party_first_name$$';
        $date_of_creation = '$$date_of_creation$$';
        $coordinator_first_name = '$$coordinator_first_name$$';
        $coordinator_last_name = '$$coordinator_last_name$$';
        $coordinator_email_address = '$$coordinator_email_address$$';
        $student_dashboard = '$$student_dashboard$$';

        //Referral_Assign_to_staff

        $this->addSql('SET @emailtemplateId = (SELECT id from email_template where email_key = "Referral_Assign_to_staff")');
        $query = <<<SQL

UPDATE `email_template_lang`
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
				<tr style="background:#fff;border-collapse:collapse;"><td>$creator_first_name $creator_last_name has assigned you a new referral for $student_first_name $student_last_name in Mapworks. Please sign in to your account to view and take action on this referral.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Best Regards,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Skyfactor Mapworks Team.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$Skyfactor_Mapworks_logo"/><br/></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
			</tbody>
		</table>
	</body>
</html>'
WHERE
    `email_template_id` = @emailtemplateId;
SQL;
        $this->addSql($query);

        //Referral_InterestedParties_Staff

        $this->addSql('SET @emailtemplateId = (SELECT id from email_template where email_key = "Referral_InterestedParties_Staff")');

        $query = <<<SQL
UPDATE `email_template_lang`
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
                  <tr style="background:#fff;border-collapse:collapse;"><td>You have been added as an interested party on a referral for $student_first_name $student_last_name on $date_of_creation.</td></tr>
                  <tr style="background:#fff;border-collapse:collapse;"><td>If you have any questions, please contact $coordinator_first_name $coordinator_last_name at $coordinator_email_address.</td></tr>
                  <tr style="background:#fff;border-collapse:collapse;"><td>Best Regards,</td></tr>
                  <tr style="background:#fff;border-collapse:collapse;"><td>Skyfactor Mapworks Team.</td></tr>
                  <tr style="background:#fff;border-collapse:collapse;"><td><img src="$Skyfactor_Mapworks_logo" title="Skyfactor Mapworks logo" alt="Skyfactor Mapworks logo"/></td></tr>
                  <tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
              </tbody>
         </table>
      </body>
</html>'
WHERE
    `email_template_id` = @emailtemplateId;
SQL;

        $this->addSql($query);

        //student notification

        $this->addSql("SET @emailtemplateId = (SELECT id from email_template where email_key = 'Referral_Student_Notification')");
        $query = <<<SQL
UPDATE `email_template_lang`
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
				<tr style="background:#fff;border-collapse:collapse;"><td>Hi $student_first_name:</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>A faculty/staff member has referred you to a campus resource through the Mapworks system. To view the referral details, please log in to your Mapworks homepage and visit $student_dashboard.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>If you have any questions, please contact $coordinator_first_name $coordinator_last_name at $coordinator_email_address.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Best Regards,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Skyfactor Mapworks Team.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$Skyfactor_Mapworks_logo"/><br/></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
			</tbody>
		</table>
	</body>
</html>'
WHERE
    `email_template_id` = @emailtemplateId;
SQL;
        $this->addSql($query);


        //referral create creator
        $this->addSql("INSERT INTO `synapse`.`email_template` (`email_key`, `from_email_address`) VALUES ('referral_create_creator', 'no-reply@mapworks.com')");
        $this->addSql("SET @emailtemplateId = (SELECT id from email_template where email_key = 'referral_create_creator')");

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
				<tr style="background:#fff;border-collapse:collapse;"><td>Your referral for $student_first_name $student_last_name has been sent to $current_assignee_first_name $current_assignee_last_name in Mapworks.</td></tr>
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
subject = 'New Mapworks Referral'

SQL;
        $this->addSql($query);

        //updating the mapworks action table  for referral_create_current_assignee

        $this->addSql("UPDATE 
                             mapworks_action ma
                        SET
                             email_template_id = (SELECT id FROM email_template WHERE email_key = 'Referral_Assign_to_staff'),
                             notification_hover_text = '$creator_first_name $creator_last_name has assigned you a new referral.',
                             notification_body_text = '$creator_first_name $creator_last_name has assigned you a new referral.'
                        WHERE
                             event_key = 'referral_create_current_assignee';");


        //updating the mapworks action table  for Referral_InterestedParties_Staff

        $this->addSql("UPDATE 
                             mapworks_action ma
                        SET
                             email_template_id = (SELECT id FROM email_template WHERE email_key = 'Referral_InterestedParties_Staff'),
                             notification_hover_text = '$creator_first_name $creator_last_name added you as an interested party on a referral.',
                             notification_body_text = '$creator_first_name $creator_last_name added you as an interested party on a referral.'
                        WHERE
                             event_key = 'referral_create_interested_party';");

        //updating the mapworks action table  for referral_create_creator

        $this->addSql("UPDATE 
                             mapworks_action ma
                        SET
                             email_template_id = (SELECT id FROM email_template WHERE email_key = 'referral_create_creator'),
                             notification_hover_text = 'Your referral has been assigned to $current_assignee_first_name $current_assignee_last_name.',
                             notification_body_text = 'Your referral for $student_first_name $student_last_name has been assigned to $current_assignee_first_name $current_assignee_last_name.'
                        WHERE
                             event_key = 'referral_create_creator';");

        //updating the mapworks action table  for referral_create_student , student only receives  email notification

        $this->addSql("UPDATE 
                             mapworks_action ma
                        SET
                             email_template_id = (SELECT id FROM email_template WHERE email_key = 'Referral_Student_Notification')
                        WHERE
                             event_key = 'referral_create_student';");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
