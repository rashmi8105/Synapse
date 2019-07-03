<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150714143559 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
		
		$this->addSql('SET @aursid := (SELECT id FROM email_template where email_key=\'Academic_Update_Request_Staff\');
        UPDATE `email_template_lang` SET `body`=\'<!DOCTYPE html>
		<html>
		<head>
		<title></title>
		</head>
		<body>
		<p>
		Please submit your academic updates for this request:
		</p>
		<table class=\"table table-bordered\" style=\"border: 1px solid #428BCA !important; border-collapse:collapse; width:40%; margin-top:20px; margin-bottom:20px;\">
			<tr>
				<td style=\" padding:5px; background-color: #4F9BD9; border: 1px solid #428BCA !important; border-collapse:collapse\"><p style=\"font-weight: bold; font-size: 14px; color:#fff;">View and complete this academic update request on Mapworks <a href=\"\$\$updateviewurl\$\$\">\$\$updateviewurl\$\$</a></p></td>
			</tr>
			<tr>
				<td style=\"background-color: #D4EEFF; border: 1px solid #4F9BD9 !important; border-collapse:collapse; padding:15px 5px 15px 5px; vertical-align: middle;\">
					<p style=\"font-size:14px; font-weight: bold;   margin: 0px !important;\">\$\$requestname\$\$ (due <span>\$\$duedate\$\$)</span>)</p>
					<p style=\"font-size:14px;   margin: 0px !important;\">\$\$description\$\$</p>
					<p style=\"font-size:14px;   margin: 0px !important;\">Requestor: <span>\$\$requestor\$\$</span>&nbsp;<span>\$\$requestor_email\$\$</span></p>
					<p style=\"display:inline-block; float:left; font-size:14px;   margin: 0px !important;\">Student Updates: <span>\$\$studentupdate\$\$</span></p>
					<a style=\"width: 65px; height:20px; background-color:#ccc; text-align: center; display:inline-block; float: right; border: 1px solid #ccc; margin: 2px; text-decoration: none; color:#000;\" href=\"\$\$updateviewurl\$\$\">Update</a>
				</td>	
			</tr>
		</table>
		<p style=\"width:40%; margin-bottom:20px;\">\$\$optional_message\$\$</p>
		</body>
		</html>\' WHERE `email_template_id`=@aursid;');
		
		$this->addSql('SET @auctfid := (SELECT id FROM email_template where email_key=\'Academic_Update_Cancel_to_Faculty\');
        UPDATE `email_template_lang` SET `body`=\'<!DOCTYPE html>
		<html>
		<head>
		<title></title>
		</head>
		<body>
		<p>
		Thank you. This request is now closed.
		</p>
		<table class=\"table table-bordered\" style=\"border: 1px solid #428BCA !important; border-collapse:collapse; width:40%; margin-top:20px; margin-bottom:20px;\">
			<tr>
				<td style=\"background-color: #D4EEFF; border: 1px solid #4F9BD9 !important; border-collapse:collapse; padding:15px 5px 15px 5px; vertical-align: middle;\">
					<p style=\"font-size:14px; font-weight: bold;   margin: 0px !important;\">\$\$request_name\$\$ (due <span>$$due_date$$)</span>)</p>
					<p style=\"font-size:14px;   margin: 0px !important;\">\$\$request_description\$\$</p>
					<p style=\"font-size:14px;   margin: 0px !important;\">Requestor: <span>\$\$requestor_name\$\$ \$\$requestor_email\$\$</span></p>
					<p style=\"display:inline-block; float:left; font-size:14px;   margin: 0px !important;\">Student Updates: <span>\$\$student_update_count\$\$</span></p>

				</td>	
			</tr>
		</table>
		<p style=\"width:40%; margin-bottom:20px;\">\$\$custom_message\$\$</p>
		</body>
		</html>\' WHERE `email_template_id`=@auctfid;');

        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
    }
}
