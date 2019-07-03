<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15809 -  Creating migration script for  creating contact_info_search view
 */
class Version20170904044616 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        $this->addSql("CREATE
                            ALGORITHM = MERGE
                            DEFINER = `synapsemaster`@`%`
                            SQL SECURITY DEFINER
                        VIEW `contact_info_search` AS
                        SELECT
                            ci.id AS contact_id,
                            pci.person_id,
                            ci.address_1,
                            ci.address_2,
                            ci.city,
                            ci.state,
                            ci.zip,
                            ci.country,
                            ci.primary_mobile,
                            ci.alternate_mobile,
                            ci.home_phone,
                            ci.office_phone,
                            ci.alternate_email,
                            ci.primary_mobile_provider,
                            ci.alternate_mobile_provider,
                            CONCAT(IFNULL(ci.address_1, ''), IFNULL(ci.city, ''), IFNULL(ci.state, ''), IFNULL(ci.zip, ''), IFNULL(ci.country, '')) AS full_address_1,
                            CONCAT(IFNULL(ci.address_2, ''), IFNULL(ci.city, ''), IFNULL(ci.state, ''), IFNULL(ci.zip, ''), IFNULL(ci.country, '')) AS full_address_2
                        FROM
                            synapse.contact_info ci
                                JOIN
                            synapse.person_contact_info pci
                                    ON pci.contact_id = ci.id
                        WHERE
                            ci.deleted_at IS NULL
                            AND pci.deleted_at IS NULL
                        ORDER BY pci.person_id, ci.id;"
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
