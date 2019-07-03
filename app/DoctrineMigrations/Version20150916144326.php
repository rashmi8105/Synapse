<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150916144326 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

		/* Commented as it is throwing syntax error - Devadoss
	$this->addSql('DROP PROCEDURE IF EXISTS \`isq_data_transfer\` ');
        $this->addSql('CREATE DEFINER=\`synapsemaster\`@\`%\` PROCEDURE isq_data_transfer\(\)
			BEGIN
			INSERT IGNORE INTO synapse.org_question_response 
			\(SELECT * FROM etldata.org_question_response WHERE modified_at \>\= \(SELECT MAX\(modified_at\) FROM synapse.org_question_response\)\)\;');
        $this->addSql('CREATE DEFINER=\`synapsemaster\`@\`%\` EVENT \`trigger_isq_data_transfer\` ON SCHEDULE EVERY 1 MINUTE STARTS \'2015-09-14 19:00:00\' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
		CALL isq_data_transfer\(\)\;');
		
		*/
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('');
        
        
       
    }
}
