<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150907113556 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $fullName = '$$fullname$$';
        $delegatorName = '$$delegater_name$$';
        $logo = '$$Skyfactor_Mapworks_logo$$';
        
        $query1 = <<<CDATA
        SET @emtid := (SELECT id FROM email_template where email_key="Add_Delegate");
        UPDATE `email_template_lang` SET  `body`= '<html>
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
        
				<tr style="background:#fff;border-collapse:collapse;"><td>Hi $fullName,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>You have been added as a delegate user for $delegatorName calendar.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Thank you.</td></tr>
				<tr><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$logo"/><br/></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>'  WHERE `email_template_id`=@emtid;
CDATA;
        
        $this->addSql($query1);
        
        $query = <<<CDATA
        SET @emtid := (SELECT id FROM email_template where email_key="Remove_Delegate");
        UPDATE `email_template_lang` SET  `body`= '<html>
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
        
				<tr style="background:#fff;border-collapse:collapse;"><td>Hi $fullName,</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>You have been removed as a delegate user for $delegatorName calendar.</td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>Thank you.</td></tr>
				<tr><td><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$logo"/><br/></td></tr>
				<tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
			</tbody>
		</table>
	</body>
</html>'  WHERE `email_template_id`=@emtid;
CDATA;
        $this->addSql($query);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
