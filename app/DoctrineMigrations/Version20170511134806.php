<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script for adding new record in email_template and email_template_language for referral reopen - # ESPRJ-14146
 */
class Version20170511134806 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $current_assignee_first_name = '$$current_assignee_first_name$$';
        $skyfactor_mapworks_logo = '$$Skyfactor_Mapworks_logo$$';
        $student_first_name = '$$student_first_name$$';
        $student_last_name = '$$student_last_name$$';
        $date_of_creation = '$$date_of_creation$$';
        $coordinator_first_name = '$$coordinator_first_name$$';
        $coordinator_last_name = '$$coordinator_last_name$$';
        $coordinator_email_address = '$$coordinator_email_address$$';

        //referral reopen current assignee
        $this->addSql("INSERT INTO email_template (email_key, from_email_address, is_active) VALUES ('referral_reopen_current_assignee', 'no-reply@mapworks.com', 1)");

        $this->addSql("SET @emailtemplateId = (SELECT id from email_template where email_key = 'referral_reopen_current_assignee')");
        $query = <<<SQL
INSERT INTO `email_template_lang`
SET
    `body` = '
<html>
	<head>
		<style>
			body {
				background: none repeat scroll 0 0 #f4f4f4;
				font-family: helvetica, arial, verdana, sans-serif;
				font-size: 13px;
				color: #333;
			}

			.outer-div {
				padding: 21px;
				width: 799px;
				background-color: #EEEEEE;
			}

			.inner-div{
				background-color: #FFFFFF;
				padding: 10px;
			}
		</style>
	</head>
	<body>
		<div class="outer-div">
			<div class="inner-div">
				<p>Hi $current_assignee_first_name,</p>
				<p>The referral created for $student_first_name $student_last_name on $date_of_creation has been reopened and is assigned to you.</p>
				<p>If you have any questions, please contact $coordinator_first_name $coordinator_last_name at $coordinator_email_address.</p>
				<p>Best regards,</p>
				<p>Skyfactor Mapworks Team</p>
				<p><img width="307" height="89" alt="Skyfactor Mapworks Logo" src="$skyfactor_mapworks_logo"/></p>
				<p>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.
				</p>
			</div>
		</div>
	</body>
</html>' ,
language_id = 1,
email_template_id = @emailtemplateId,
subject = 'Mapworks Referral Reopened'
SQL;
        $this->addSql($query);

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
