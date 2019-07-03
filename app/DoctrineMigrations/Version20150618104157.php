<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150618104157 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $firstname = '$$firstname$$';
        $resetPass = '$$Reset_Password_Expiry_Hrs$$';
        $actToken = '$$activation_token$$';
        $support = '$$Support_Helpdesk_Email_Address$$';
        $logo = '$$Skyfactor_Mapworks_logo$$';
        $query = <<<CDATA
INSERT INTO `email_template` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Forgot_Password_Student',NULL,'no-reply@mapworks.com','ramesh.kumhar@techmahindra.com,SP00345364@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');
CDATA;
        $this->addSql($query);
        
         $this->addSql("SET @tempId := (select id from email_template where email_key = 'Forgot_Password_Student');");
          $this->addSql("SET @langId := (select id from language_master where langcode = 'en_US');");
          
          $query1 = <<<CDATA
INSERT INTO `email_template_lang` (`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (@tempId,@langId,NULL,NULL,NULL,NULL,NULL,NULL,'<html>
<div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>
Hi $firstname,<br/></br>
        
Please use the link below and follow the displayed instructions to create your new password. This link will expire after $resetPass hours.<br />
<br/>
$actToken<br/><br/>
        
If you believe that you received this email in error or if you have any questions,please contact Mapworks support at <span style=\'color: #99ccff;\'>$support</span>.<br/><br/>
<p>Thank you from the Mapworks team.</br></p>
<p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"$logo\"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
</div>
</html>','How to reset your Mapworks password');
CDATA;
          $this->addSql($query1);
          
          $query2 = <<<CDATA
          INSERT INTO  `ebi_config`(`created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_at`,`deleted_by`, `key`, `value`) VALUES
(NULL, NULL, NULL,NULL, NULL, NULL, 'Student_Support_Helpdesk_Email_Address', 'support@map-works.com');
CDATA;
          $this->addSql($query2);
          
          $this->addSql("SET @sys_url := (SELECT value FROM synapse.ebi_config where `key` = 'System_URL');");
          
          $sysUrl = "'#/resetPassword/'";
          $query3 = <<<CDATA
          INSERT INTO `ebi_config`(`created_by`, `created_at`, `modified_by`, `modified_at`, `deleted_at`,`deleted_by`, `key`, `value`) VALUES
(NULL, NULL, NULL,NULL, NULL, NULL, 'Student_ResetPwd_URL_Prefix', CONCAT(@sys_url, $sysUrl));
CDATA;
          $this->addSql($query3);
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
