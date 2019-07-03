<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-13964
 * Fixing a Merge problem with another pull request #429
 */
class Version20170519203650 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
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
        $this->addSql("SET @emailtemplateId = (SELECT id from email_template where email_key = 'referral_create_creator')");
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
WHERE
    `email_template_id` = @emailtemplateId;
SQL;
        $this->addSql($query);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
