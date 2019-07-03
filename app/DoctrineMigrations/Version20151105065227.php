<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151105065227 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $query = <<<CDATA
          
                    SET @ebiValue := (SELECT c.value FROM synapse.ebi_config c where c.key ="System_Admin_URL");
                    
                    SET @ebiId := (SELECT c.id FROM synapse.ebi_config c where c.key ="Skyfactor_Admin_Activation_URL_Prefix");
            
                    UPDATE `ebi_config` set `value` = CONCAT(@ebiValue,'#/createPassword/') where id = @ebiId;
CDATA;
        $this->addSql($query);

    }
       

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
