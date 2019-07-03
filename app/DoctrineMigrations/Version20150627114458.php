<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150627114458 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('SET @emtid := (SELECT id FROM email_template where email_key="Referral_InterestedParties_Staff");
            UPDATE `email_template_lang` SET  `body`= \'<html>
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
        
                           <tr style="background:#fff;border-collapse:collapse;"><td>Hi $$staff_firstname$$,</td></tr>
                          <tr style="background:#fff;border-collapse:collapse;"><td>A faculty or staff member has referred $$student_name$$ to a campus resource through the Mapworks system and added you as an interested party. To view the referral details, please log in to Mapworks and visit <a class="external-link" href="$$staff_referralpage$$" target="_blank" style="color: rgb(41, 114, 155); text-decoration: underline;">Mapworks student dashboard view referral module</a>.</tr>
                          <tr style="background:#fff;border-collapse:collapse;"><td> If you have any questions, please contact ($$coordinator_name$$,$$coordinator_title$$,$$coordinator_email$$ ).</td></tr>
                          <tr style="background:#fff;border-collapse:collapse;"><td>Thank you from the Skyfactor Mapworks team.</td></tr>
                          <tr style="background:#fff;border-collapse:collapse;"><td><img src="$$Skyfactor_Mapworks_logo$$" title="Skyfactor Mapworks logo" alt="Skyfactor Mapworks logo"/></td></tr>
                          <tr style="background:#fff;border-collapse:collapse;"><td>This email is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
        
                     </tbody>
             </table>
      </body>
</html>\'  WHERE `email_template_id`=@emtid;"
            ');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
    }
}
