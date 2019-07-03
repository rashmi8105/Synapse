<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150618065429 extends AbstractMigration
{

    /**
     *
     * @param Schema $schema            
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql("INSERT INTO `synapse`.`email_template` (`email_key`, `is_active`, `from_email_address`, `bcc_recipient_list`) VALUES ('Email_Login_Url', '1', 'no-reply@mapworks.com', 'DD00364308@TechMahindra.com,devadoss.poornachari@techmahindra.com');");
        
        $firstName = '$$firstname$$';
        $studentUrl = '$$studentUrl$$';
        
        $query = <<<CDATA
SET @emtid := (SELECT id FROM email_template where email_key='Email_Login_Url'); 
INSERT INTO `email_template_lang` (`email_template_id`, `language_id`, `body`, `subject`) VALUES (@emtid, '1', '<html> <div style="margin: 10px 0px 0px; padding: 0px; color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(255, 255, 255);"> Hi $firstName,<br/><br/> Here is your link to log in to Mapworks: &nbsp;<a class="external-link" href="$studentUrl" rel="nofollow" style="color: rgb(41, 114, 155); text-decoration: underline;">Click Here</a><br/><br/> If the above link does not work, please copy and paste the address below into your browser address bar:<br/><br/> $studentUrl <br/><br/> Thank you from the Mapworks team! </div> </html>', 'Mapworks Login Link');
CDATA;
        $this->addSql($query);
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('Email_Login_Landing_Page', 'https://synapse-qa.mnv-tech.com/#/login');");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        }
}
