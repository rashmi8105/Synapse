<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170102145734 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO `ebi_config` (`key`, `value`) VALUES ('cronofy_client_id', 'm7rDoibgv9WCeETBNHeztBSClLEDgZCv')");
        $this->addSql("INSERT INTO `ebi_config` (`key`, `value`) VALUES ('cronofy_client_secret', '3vLq7rFEUb2FFb6-3h0F0vcDVFbRKuuQuqyW3opDE7P4GHjWz_kJBsyKuLu6BFo8XkjwI9A1Ly1_8Rafa9H6qA')");
		$this->addSql("INSERT INTO `ebi_config` (`key`, `value`) VALUES ('cronofy_scope','read_events,create_event, delete_event,read_free_busy')");
		$this->addSql("INSERT INTO `ebi_config` (`key`, `value`) VALUES ('cronofy_redirect_uri', 'https://mapworks-qa-api.skyfactor.com/api/v1/calendar/enable')");
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
    }
}
