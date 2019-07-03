<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-14928
 * Migration script for mapworks_action to update receives_notification =1 for event_key 'referral_add_interested_party_interested_party'.
 */
class Version20170518050400 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        $this->addSql("UPDATE mapworks_action 
                          SET 
                            receives_notification = 1
                          WHERE
                            action = 'add_interested_party'
                          AND recipient_type = 'interested_party'
                          AND event_type = 'referral';");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
