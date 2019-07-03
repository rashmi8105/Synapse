<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15797 - Allowing existing service accounts to utilize refresh token and client credential authentication methods.
 */
class Version20170825173712 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $sql =
        <<<HEREDOC
            UPDATE synapse.Client
            SET
                allowed_grant_types = 'a:3:{i:0;s:18:"authorization_code";i:1;s:13:"refresh_token";i:2;s:18:"client_credentials";}',
                modified_at = NOW(),
                modified_by = - 25
            WHERE
                person_id IS NOT NULL
                AND organization_id IS NOT NULL
                AND deleted_at IS NULL;
HEREDOC;


        $this->addSql($sql);


    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
