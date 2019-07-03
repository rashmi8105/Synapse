<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160510070203 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        /**
         *  Added hyperlink to the Applicatio URL in Referral_Student_Noticication
         *  change - "<a href='$$dashboard$$'>$$dashboard$$</a>"
         */
        $firstName = '$$firstname$$';
        $coordinatorDetails = '$$coordinator_details$$';
        $dashboard = '$$dashboard$$';
        $skyfactorMapworksLogo = '$$Skyfactor_Mapworks_logo$$';

        $query = <<<CDATA
SET @templateId := (SELECT id FROM `email_template` WHERE `email_key` = "Referral_Student_Notification");

UPDATE `email_template_lang`
SET `body` = "

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
        
		<table cellpadding='10' style='background:#eeeeee;' cellspacing='0'>
			<tbody>
        
				<tr style='background:#fff;border-collapse:collapse;'><td>Dear $firstName:</td></tr>
				<tr style='background:#fff;border-collapse:collapse;'><td>A faculty/staff member has referred you to a campus resource through the Mapworks system. To view the referral details, please log in to your Mapworks homepage and visit <a href='$dashboard'>$dashboard</a>.</td></tr>
			<tr style='background:#fff;border-collapse:collapse;'><td>If you have any questions, please contact $coordinatorDetails</td></tr>
		<tr style='background:#fff;border-collapse:collapse;'><td>Best regards,</td></tr>
	<tr style='background:#fff;border-collapse:collapse;'><td><img width='307' height = '89' alt='Skyfactor Mapworks Logo' src='$skyfactorMapworksLogo'/>
				<tr style='background:#fff;border-collapse:collapse;'><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>
    
"
WHERE `email_template_id` = @templateId;
CDATA;
        $this->addSql($query);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

    }
}
