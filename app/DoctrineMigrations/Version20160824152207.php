<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160824152207 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sql = "CREATE OR REPLACE VIEW person_search
                AS
                  SELECT
                    id as person_id,
                    external_id,
                    firstname,
                    lastname,
                    organization_id,
                    concat(firstname, lastname) as first_and_last_name,
                    concat(lastname, firstname) as last_and_first_name,
                    username
                  FROM synapse.person
                  WHERE deleted_at IS NULL;";
        $this->addSql($sql);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
