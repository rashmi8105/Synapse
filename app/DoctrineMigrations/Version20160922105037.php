<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adding a feature toggle for Coordinator Omniscience.
 * When it has value 0, coordinators will be governed by their permissions like any other faculty.
 * When it has value 1, coordinators will be able to see data for all students in their organization.
 */
class Version20160922105037 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $sql = "SELECT 1 FROM ebi_config WHERE `key` = 'Coordinator_Omniscience';";

        $results = $this->connection->executeQuery($sql)->fetchAll();

        if (empty($results)) {
            $this->addSql("INSERT INTO ebi_config (`key`, value)
                        VALUES ('Coordinator_Omniscience', 0);");
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');
    }
}
