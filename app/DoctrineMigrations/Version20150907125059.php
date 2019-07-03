<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150907125059 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $firstName = '$$firstname$$';
        $studentUrl = '$$studentUrl$$';
        
        $query = <<<CDATA
SET @emtid := (SELECT id FROM email_template where email_key='Email_Login_Url'); 
UPDATE `email_template_lang` SET `body`= '<html> <div style="margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);"> Hi $firstName,<br/><br/> Here is your link to log in to Mapworks: &nbsp;<a class="external-link" href="$studentUrl" rel="nofollow" style="color: rgb(41, 114, 155); text-decoration: underline;">Click Here</a><br/><br/> If the above link does not work, please copy and paste the address below into your browser address bar:<br/><br/> $studentUrl <br/><br/> Thank you. </div> </html>' WHERE `email_template_id`=@emtid;;
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
