<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15662  -  migration script to insert cronofy API related configurations.
 */
class Version20170811132405 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('cronofy_allowed_request_per_second_count', '50')");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('cronofy_delayed_seconds', '2')");
        $this->addSql("INSERT INTO `synapse`.`ebi_config` (`key`, `value`) VALUES ('cronofy_default_batch_size', '30')");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
