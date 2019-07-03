<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-17125 ESPRJ-17128 Eliminating Duplicate Contacts and Adding Unique constraint
 */
class Version20180117223024 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER IGNORE TABLE `synapse`.`person_contact_info` 
        ADD UNIQUE INDEX `uniqueContact` (`person_id` ASC, `contact_id` ASC);");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
