<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration Script for updating email template for email_key "Referral_InterestedParties_Staff"
 * This migration script is for ESPRJ-14765
 */
class Version20170517035500 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        $this->addSql('
            UPDATE `email_template_lang` SET body = \'<html>
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
        
                          <tr style="background:#fff;border-collapse:collapse;"><td>Hi $$interested_party_first_name$$,</td></tr>
                          <tr style="background:#fff;border-collapse:collapse;"><td>You have been added as an interested party on a referral for $$student_first_name$$ $$student_last_name$$ on $$date_of_creation$$.</tr>
                          <tr style="background:#fff;border-collapse:collapse;"><td> If you have any questions, please contact $$coordinator_first_name$$ $$coordinator_last_name$$ at $$coordinator_email_address$$.</td></tr>
                          <tr style="background:#fff;border-collapse:collapse;"><td>Best regards,<br/>Skyfactor Mapworks Team</td></tr>
                          <tr style="background:#fff;border-collapse:collapse;"><td><img src="$$Skyfactor_Mapworks_logo$$" title="Skyfactor Mapworks logo" alt="Skyfactor Mapworks logo"/></td></tr>
                          <tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
                     </tbody>
             </table>
      </body>
</html>	\' WHERE email_template_id = (SELECT id FROM email_template where email_key="Referral_InterestedParties_Staff")');
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
