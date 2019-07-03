<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15027 - Migration script to insert record in mapworks_action_variable table for event_key ='referral_create_interested_party'
 */
class Version20170526101315 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $creator_first_name = '$$creator_first_name$$';
        $creator_last_name = '$$creator_last_name$$';

        // get variable ids from mapworks_action_variable_description
        $this->addSql("SET @creatorFirstNameVariableDescriptionId = (SELECT id FROM mapworks_action_variable_description WHERE variable= '$creator_first_name')");
        $this->addSql("SET @creatorLastNameVariableDescriptionId = (SELECT id FROM mapworks_action_variable_description WHERE variable= '$creator_last_name')");

        // Insert mapworks_action_variable records for referral_create_interested_party
        $this->addSql("SET @mapworksActionId = (SELECT id FROM mapworks_action WHERE event_key ='referral_create_interested_party')");
        $this->addSql("INSERT INTO mapworks_action_variable (mapworks_action_id, mapworks_action_variable_description_id) VALUES
                        (
                            @mapworksActionId,
                            @creatorFirstNameVariableDescriptionId
                        ),
                        (
                            @mapworksActionId,
                            @creatorLastNameVariableDescriptionId
                        )
                    ;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
