<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Update existing email templates with mapworks_action_variables for referral closing - ESPRJ-14867
 */
class Version20170516073323 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $current_assignee_first_name = '$$current_assignee_first_name$$';
        $current_assignee_last_name = '$$current_assignee_last_name$$';
        $creator_first_name = '$$creator_first_name$$';
        $creator_last_name = '$$creator_last_name$$';
        $skyfactor_mapworks_logo = '$$Skyfactor_Mapworks_logo$$';
        $student_first_name = '$$student_first_name$$';
        $student_last_name = '$$student_last_name$$';
        $interested_party_first_name = '$$interested_party_first_name$$';
        $interested_party_last_name = '$$interested_party_last_name$$';
        $date_of_creation = '$$date_of_creation$$';
        $coordinator_first_name = '$$coordinator_first_name$$';
        $coordinator_last_name = '$$coordinator_last_name$$';
        $coordinator_title = '$$coordinator_title$$';
        $coordinator_email_address = '$$coordinator_email_address$$';

        //Referral_Closed_Assignee
        $this->addSql('SET @emailtemplateId = (SELECT id from email_template where email_key = "Referral_Closed_Assignee")');
        $query = <<<SQL
UPDATE `email_template_lang`
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

        .inner-div {
            background-color: #FFFFFF;
            padding: 10px;
        }
    </style>
</head>
<body>
<div class="outer-div">
    <div class="inner-div">
        <p>$current_assignee_first_name $current_assignee_last_name,</p>
        <p>The referral you were assigned for $student_first_name $student_last_name on $date_of_creation has
            been closed.</p>
        <p>If you have any questions, please contact $coordinator_first_name $coordinator_last_name,
            $coordinator_title
            ($coordinator_email_address).</p>
        <p>Best regards,</p>
        <p>Skyfactor Mapworks Team</p>
        <p><img width="307" height="89" alt="Skyfactor Mapworks Logo" src="$skyfactor_mapworks_logo"/></p>
        <p>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</p>
    </div>
</div>
</body>
</html>'
WHERE
    `email_template_id` = @emailtemplateId;
SQL;
        $this->addSql($query);


        //Referral_Closed_Creator
        $this->addSql('SET @emailtemplateId = (SELECT id from email_template where email_key = "Referral_Closed_Creator")');
        $query = <<<SQL
UPDATE `email_template_lang`
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

        .inner-div {
            background-color: #FFFFFF;
            padding: 10px;
        }
    </style>
</head>
<body>
<div class="outer-div">
    <div class="inner-div">
        <p>$creator_first_name $creator_last_name,</p>
        <p>The referral you created for $student_first_name $student_last_name on $date_of_creation has been
            closed.</p>
        <p>If you have any questions, please contact $coordinator_first_name $coordinator_last_name,
            $coordinator_title
            ($coordinator_email_address).</p>
        <p>Best regards,</p>
        <p>Skyfactor Mapworks Team</p>
        <p><img width="307" height="89" alt="Skyfactor Mapworks Logo" src="$skyfactor_mapworks_logo"/></p>
        <p>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</p>
    </div>
</div>
</body>
</html>'
WHERE
    `email_template_id` = @emailtemplateId;
SQL;
        $this->addSql($query);

        //Referral_InterestedParties_Staff_Closed
        $this->addSql('SET @emailtemplateId = (SELECT id from email_template where email_key = "Referral_InterestedParties_Staff_Closed")');
        $query = <<<SQL
UPDATE `email_template_lang`
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

        .inner-div {
            background-color: #FFFFFF;
            padding: 10px;
        }
    </style>
</head>
<body>
<div class="outer-div">
    <div class="inner-div">
        <p>$interested_party_first_name $interested_party_last_name,</p>
        <p>A referral for $student_first_name $student_last_name on $date_of_creation that you were an interested party for has been
            closed.</p>
        <p>If you have any questions, please contact $coordinator_first_name $coordinator_last_name, $coordinator_title
            ($coordinator_email_address).</p>
        <p>Best regards,</p>
        <p>Skyfactor Mapworks Team</p>
        <p><img width="307" height="89" alt="Skyfactor Mapworks Logo" src="$skyfactor_mapworks_logo"/></p>
        <p>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</p>
    </div>
</div>
</body>
</html>'
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
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
