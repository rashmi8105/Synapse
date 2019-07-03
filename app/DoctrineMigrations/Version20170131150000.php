<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Update cronofy_redirect_uri to api/v1/oauth/cronofy since already it was created by appending system url with this,
 * which may cause an issue in different environments, due to this changing this value with relative path
 *
 */
class Version20170131150000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql("UPDATE ebi_config e SET e.value='api/v1/oauth/cronofy' WHERE e.key='cronofy_redirect_uri'");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
