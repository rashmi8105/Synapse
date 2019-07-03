<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150602202230 extends AbstractMigration
{
    public function up(Schema $schema)
    {
	
	
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("
		SET @emtid := (SELECT c.value FROM synapse.ebi_config c where c.key =\"System_URL\");
UPDATE `synapse`.`ebi_config` SET `value`=CONCAT(@emtid,'#/resetPassword/') WHERE `key`='Coordinator_ResetPwd_URL_Prefix';
UPDATE `synapse`.`ebi_config` SET `value`=CONCAT(@emtid,'#/resetPassword/') WHERE `key`='Staff_ResetPwd_URL_Prefix';
");     
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}