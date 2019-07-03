<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * This view person_with_risk_intent_denullifier is to have appropriate values instead NULL from person table
 * ESPRJ-15512
 *
 */
class Version20170711124200 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE OR REPLACE 
                            ALGORITHM = UNDEFINED 
                            DEFINER = `synapsemaster`@`%` 
                            SQL SECURITY DEFINER
                        VIEW `person_with_risk_intent_denullifier` AS
                            SELECT 
                                p.id AS person_id,
                                p.external_id,
                                p.firstname,
                                p.lastname,
                                p.username,
                                p.organization_id,
                                p.created_at,
                                CASE
                                    WHEN p.risk_level IS NULL THEN 6
                                    ELSE p.risk_level
                                END AS risk_level,
                                CASE
                                    WHEN p.risk_update_date IS NULL THEN p.created_at
                                    ELSE p.risk_update_date
                                END AS risk_updated_date,
                                CASE
                                    WHEN p.intent_to_leave IS NULL THEN 5
                                    ELSE p.intent_to_leave
                                END AS intent_to_leave,
                                CASE
                                    WHEN p.intent_to_leave_update_date IS NULL THEN p.created_at
                                    ELSE p.intent_to_leave_update_date
                                END AS intent_to_leave_updated_date
                            FROM
                                person p
                            WHERE
                                p.deleted_at IS NULL");
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
    }
}