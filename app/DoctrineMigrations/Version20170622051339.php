<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15310  -  migration script to move the push notification constants to ebi_config table
 */
class Version20170622051339 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('PUSH_NOTIFICATION_API_KEY', 'CO6vUS')");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('PUSH_NOTIFICATION_API_URL', 'https://ortc-developers-useast1-s0001.realtime.co/sendbatch')");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('PUSH_NOTIFICATION_API_TOKEN', 'SOMETOKEN')");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('PUSH_NOTIFICATION_NUMBER_OF_CHANNELS_PER_REQUEST', '99')");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
