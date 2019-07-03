<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150812130913 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("UPDATE `synapse`.`intent_to_leave` SET `min_value`='1', `max_value`='3' WHERE `text`='red'");
        $this->addSql("UPDATE `synapse`.`intent_to_leave` SET `min_value`='6', `max_value`='7' WHERE `text`='green'");
        $this->addSql("UPDATE `synapse`.`intent_to_leave` SET `min_value`='4', `max_value`='5' WHERE `text`='yellow'");
        
        
    }
    
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
