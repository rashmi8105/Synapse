<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150619082424 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $firstname = '$$firstname$$';
        $logo = '$$Skyfactor_Mapworks_logo$$';
        
        $query = <<<CDATA
INSERT INTO `email_template` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`email_key`,`is_active`,`from_email_address`,`bcc_recipient_list`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Sucessful_Password_Reset_Student',1,'no-reply@mapworks.com','ramesh.kumhar@techmahindra.com,SP00345364@TechMahindra.com,MK00361563@TechMahindra.com,devadoss.poornachari@techmahindra.com,Amith.Kishore@TechMahindra.com');
CDATA;
        $this->addSql($query);

        $this->addSql("SET @tempId := (select id from email_template where email_key = 'Sucessful_Password_Reset_Student');");
        $this->addSql("SET @langId := (select id from language_master where langcode = 'en_US');");
        
        $query1 = <<<CDATA
INSERT INTO `email_template_lang` (`email_template_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`body`,`subject`) VALUES (@tempId,@langId,NULL,NULL,NULL,NULL,NULL,NULL,'<html>
 <div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>
 Hi $firstname,<br/></br>
 Your Mapworks password has been changed. If this was not you or you believe this is an error, please contact Mapworks support at &nbsp;<a class=\'external-link\' href=\'mailto:support@mapworks.com\' rel=\'nofollow\' style=\'color: rgb(41, 114, 155); text-decoration: none;\'>support@mapworks.com</a>
 <br/><br/>
<p>Thank you from the Mapworks team.</br></p>
<p><img width=\"307\" height = \"89\" alt=\"Skyfactor Mapworks Logo\" src=\"$logo\"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
 </div>
 </html>','Mapworks password reset');
CDATA;
          $this->addSql($query1);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

       
    }
}
