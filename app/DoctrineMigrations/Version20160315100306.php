<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160315100306 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $firstName = '$$user_first_name$$';
        $downloadFileLogFile = '$$download_failed_log_file$$';
        $skyFactorLogo = '$$Skyfactor_Mapworks_logo$$';
        
        $query = <<<CDATA
SET @etId := (select id from email_template where email_key = "Faculty_Upload_Notification");

UPDATE 
	`email_template_lang`
SET
	`body` = 
    '
		<html><head><style>body {background: none repeat scroll 0 0 #f4f4f4;} table {padding: 21px; width: 799px; font-family: helvetica,arial,verdana,san-serif; font-size:13px; color:#333; }</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $firstName,</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Your faculty upload has finished importing. $downloadFileLogFile </td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Thank you.<br/>
 </td></tr>
<tr style = "background:#fff;border-collapse:collapse;">  <td>
 <img src = "$skyFactorLogo" alt = "Skyfactor Mapworks logo" title = "Skyfactor Mapworks logo" />
 </td></tr>
<tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>
    ',
    `subject` = 'Faculty upload finished importing'
WHERE
	`email_template_id` = @etId;
CDATA;
        $this->addSql($query);
        
        $query1 = <<<CDATA
SET @etId := (select id from email_template where email_key = "Student_Upload_Notification");
        
UPDATE
	`email_template_lang`
SET
	`body` =
    '<html><head><style>body {background: none repeat scroll 0 0 #f4f4f4;} table {padding: 21px; width: 799px; font-family: helvetica,arial,verdana,san-serif; font-size:13px; color:#333; }</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $firstName:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Your student upload has finished importing. $downloadFileLogFile </td></tr>

<tr style="background:#fff;border-collapse:collapse;"><td>Thank you.<br/>
 </td></tr>
<tr style = "background:#fff;border-collapse:collapse;">  <td>
 <img src = "$skyFactorLogo" alt = "Skyfactor Mapworks logo" title = "Skyfactor Mapworks logo" />
 </td></tr>

<tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>
    ',
    `subject` = 'Student upload finished importing'
WHERE
	`email_template_id` = @etId;
CDATA;
        $this->addSql($query1);
        
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
