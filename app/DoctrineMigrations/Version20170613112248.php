<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration Script for updating mapworks_action table for referral reopen - ESPRJ-15123
 */
class Version20170613112248 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Update mapworks_action table to set email_template_id for event_key = 'referral_reopen_current_assignee'
        $this->addSql("UPDATE mapworks_action
                       SET 
                           email_template_id = (SELECT
                                   id
                               FROM
                                   email_template
                               WHERE
                                   email_key = 'referral_reopen_current_assignee')
                       WHERE
                           event_key = 'referral_reopen_current_assignee'");
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
