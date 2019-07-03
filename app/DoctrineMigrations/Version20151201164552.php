<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151201164552 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('INSERT INTO `email_template` (email_key, from_email_address, is_active, bcc_recipient_list) VALUES (\'Group_Student_Upload_Notification\',\'no-reply@mapworks.com\',1,null)');
        $this->addSql('INSERT INTO `email_template` (email_key, from_email_address, is_active, bcc_recipient_list) VALUES (\'Group_Faculty_Upload_Notification\',\'no-reply@mapworks.com\',1,null)');
        $this->addSql('INSERT INTO `email_template` (email_key, from_email_address, is_active, bcc_recipient_list) VALUES (\'Group_Upload_Notification\',\'no-reply@mapworks.com\',1,null)');
        $this->addSql('INSERT INTO `email_template` (email_key, from_email_address, is_active, bcc_recipient_list) VALUES (\'Static_List_Upload_Notification\',\'no-reply@mapworks.com\',1,null)');

        $this->addSql('SET @sunid := (SELECT id FROM email_template where email_key=\'Group_Student_Upload_Notification\'); INSERT INTO `email_template_lang` (email_template_id, language_id, body, subject) VALUES (@sunid,1,\'<html><head><style>body {background: none repeat scroll 0 0 #f4f4f4;} table {padding: 21px; width: 799px; font-family: helvetica,arial,verdana,san-serif; font-size:13px; color:#333; }</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$user_first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Your student group upload has finished importing. $$download_failed_log_file$$ </td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>\',\'Mapworks student group upload has finished\')');
        $this->addSql('SET @funid := (SELECT id FROM email_template where email_key=\'Group_Faculty_Upload_Notification\'); INSERT INTO `email_template_lang` (email_template_id, language_id, body, subject) VALUES (@funid,1,\'<html><head><style>body {background: none repeat scroll 0 0 #f4f4f4;} table {padding: 21px; width: 799px; font-family: helvetica,arial,verdana,san-serif; font-size:13px; color:#333; }</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$user_first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Your faculty group upload has finished importing. $$download_failed_log_file$$ </td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>\',\'Mapworks faculty group upload has finished\')');
        $this->addSql('SET @funid := (SELECT id FROM email_template where email_key=\'Group_Upload_Notification\'); INSERT INTO `email_template_lang` (email_template_id, language_id, body, subject) VALUES (@funid,1,\'<html><head><style>body {background: none repeat scroll 0 0 #f4f4f4;} table {padding: 21px; width: 799px; font-family: helvetica,arial,verdana,san-serif; font-size:13px; color:#333; }</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$user_first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Your group upload has finished importing. $$download_failed_log_file$$ </td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>\',\'Mapworks group upload has finished\')');
        $this->addSql('SET @funid := (SELECT id FROM email_template where email_key=\'Static_List_Upload_Notification\'); INSERT INTO `email_template_lang` (email_template_id, language_id, body, subject) VALUES (@funid,1,\'<html><head><style>body {background: none repeat scroll 0 0 #f4f4f4;} table {padding: 21px; width: 799px; font-family: helvetica,arial,verdana,san-serif; font-size:13px; color:#333; }</style></head><body><table cellpadding="10" style="background:#eeeeee;" cellspacing="0"><tbody><tr style="background:#fff;border-collapse:collapse;"><td>Dear $$user_first_name$$:</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Your static list upload has finished importing. $$download_failed_log_file$$ </td></tr><tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style="background:#fff;border-collapse:collapse;"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>\',\'Mapworks static list upload has finished\')');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
