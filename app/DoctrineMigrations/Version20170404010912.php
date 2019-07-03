<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add a new column 'cronofy_channel_id' to store the channel id received from cronofy push notification.
 */
class Version20170404010912 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE org_cronofy_calendar ADD COLUMN cronofy_channel_id VARCHAR(45) NULL  AFTER cronofy_provider_name");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE org_cronofy_calendar DROP cronofy_channel_id');


    }
}
