<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150626220702 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE `synapse`.`email_template_lang` SET `body`="<html><head><style>body {background: none repeat scroll 0 0 #f4f4f4; } table {padding: 21px;width: 799px;font-family: helvetica,arial,verdana,san-serif;font-size:13px;color:#333;}</style> </head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\"><tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$user_first_name$$:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Your course upload has finished importing. $$download_failed_log_file$$ </td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>" WHERE `id` = (SELECT id FROM synapse.email_template where email_key="Course_Upload_Notification" LIMIT 1)');

        $this->addSql('UPDATE `synapse`.`email_template_lang` SET `body`="<html><head><style>body {background: none repeat scroll 0 0 #f4f4f4;}table {padding: 21px; width: 799px; font-family: helvetica,arial,verdana,san-serif; font-size:13px; color:#333; } </style></head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\"><tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$user_first_name$$:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Your faculty upload has finished importing. $$download_failed_log_file$$ </td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>" WHERE `id` = (SELECT id FROM synapse.email_template where email_key="Course_Faculty_Upload_Notification" limit 1)');

        $this->addSql('UPDATE `synapse`.`email_template_lang` SET `body`="<html><head><style>body {background: none repeat scroll 0 0 #f4f4f4;}table {padding: 21px;width: 799px; font-family: helvetica,arial,verdana,san-serif; font-size:13px; color:#333; } </style> </head><body><table cellpadding=\"10\" style=\"background:#eeeeee;\" cellspacing=\"0\"><tbody><tr style=\"background:#fff;border-collapse:collapse;\"><td>Dear $$user_first_name$$:</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Your student upload has finished importing. $$download_failed_log_file$$ </td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>Best regards,<br/>EBI Mapworks</td></tr><tr style=\"background:#fff;border-collapse:collapse;\"><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr></tbody></table></body></html>" WHERE `id` = (SELECT id FROM synapse.email_template where email_key="Course_Student_Upload_Notification" limit 1);');

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
