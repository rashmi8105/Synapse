<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150711064303 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $firstName = '$$firstname$$';
        $resetPassExp = '$$Reset_Password_Expiry_Hrs$$';
        $actToken = '$$activation_token$$';
        $supporthelp = '$$Support_Helpdesk_Email_Address$$';
        $skyFactorLogo = '$$Skyfactor_Mapworks_logo$$';
        
        $query = <<<CDATA
SET @emlId = (select id from email_template where email_key='Forgot_Password_Student');

UPDATE `email_template_lang` SET `body` = '<html>
<div style=\'margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);\'>
Hi $firstName,<br/></br>
        
Please use the link below and follow the displayed instructions to create your new password. This link will expire after $resetPassExp hours.<br />
<br/>
<a href=\'$actToken\'>$actToken</a><br/><br/>
        
If you believe that you received this email in error or if you have any questions,please contact Mapworks support at <span style=\'color: #99ccff;\'>$supporthelp</span>.<br/><br/>
<p>Thank you from the Mapworks team.</br></p>
<p><img width="307" height = "89" alt="Skyfactor Mapworks Logo" src="$skyFactorLogo"/><br/></p>
<p>This email is an auto-generated message. Replies to automated messages are not monitored.</p>
</div>
</html>'
WHERE `email_template_id` = @emlId;
CDATA;
        $this->addSql($query);
        
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
