<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150721163753 extends AbstractMigration
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
        
        $this->addSql('SET @etid := (SELECT id FROM email_template where email_key=\'Referral_Assign_to_staff\');
		UPDATE `email_template_lang` SET subject =\'New Mapworks referral\' where `email_template_id`=@etid;
		');
		
		$this->addSql('SET @etid := (SELECT id FROM email_template where email_key=\'Referral_InterestedParties_Staff\');
		UPDATE `email_template_lang` SET subject =\'Interested party for a Mapworks referral\' where `email_template_id`=@etid;
		');
		
		$this->addSql('SET @etid := (SELECT id FROM email_template where email_key=\'Referral_InterestedParties_Staff_Closed\');
		UPDATE `email_template_lang` SET subject =\'Interested party for a Mapworks referral\' where `email_template_id`=@etid;
		');
    }

    /**
     *
     * @param Schema $schema            
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
    }
}
