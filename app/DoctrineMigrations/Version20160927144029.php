<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Soft deleting unused email_template keys 'Create_Password_Coordinator' and 'Welcome_Email_Coordinator'
 */
class Version20160927144029 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            "UPDATE
              email_template_lang etl
                  JOIN
              email_template et
                      ON etl.email_template_id = et.id
            SET etl.deleted_at = NOW(),
                etl.deleted_by = -25
            WHERE et.email_key IN ('Create_Password_Coordinator', 'Welcome_Email_Coordinator');"
        );

        $this->addSql(
            "UPDATE email_template
            SET deleted_at = NOW(),
                deleted_by = -25
            WHERE email_key IN ('Create_Password_Coordinator', 'Welcome_Email_Coordinator');"
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
