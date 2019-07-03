<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 *  ESPRJ-15039 - Migration script to update 'email_template_id' in 'mapworks_action'
 * which is incorrect in migration script Version20170512025600
 */
class Version20170529060800 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE mapworks_action
                       SET 
                           email_template_id = (SELECT id FROM email_template WHERE email_key = 'referral_reassign_current_assignee')
                       WHERE
                           event_key = 'referral_reassign_current_assignee';");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
