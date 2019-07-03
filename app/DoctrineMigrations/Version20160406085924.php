<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script to store the receive survey cols and survey external id mapping
 */
class Version20160406085924 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('TransitionOneReceiveSurvey', '1647')");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('CheckupOneReceiveSurvey', '1648')");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('TransitionTwoReceiveSurvey', '1649')");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('CheckupTwoReceiveSurvey', '1650')");
        
    }
    
    public function down(Schema $schema) {
       // this down() migration is auto-generated, please modify it to your needs
       $this->abortIf ( $this->connection->getDatabasePlatform ()->getName () != 'mysql', 'Migration can only be executed safely on \'mysql\'.' );
       
    }
}
