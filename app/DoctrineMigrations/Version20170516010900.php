<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-14063
 *
 * Migration script for updating mapworks_action fields email_template_id, notification_hover_text and notification_body_text
 * for below keys
 *
 * referral_add_interested_party_interested_party
 */
class Version20170516010900 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');


        //variable declaration
        $updater_first_name = '$$updater_first_name$$';
        $updater_last_name = '$$updater_last_name$$';

        $this->addSql("UPDATE mapworks_action
                       SET 
                           email_template_id = (SELECT id FROM email_template WHERE email_key = 'Referral_InterestedParties_Staff'),
                           notification_hover_text = '$updater_first_name $updater_last_name has added you as an interested party on a referral.',
                           notification_body_text = '$updater_first_name $updater_last_name has added you as an interested party on a referral.'
                       WHERE
                           event_key = 'referral_add_interested_party_interested_party';");

    }


    public function down(Schema $schema)
    {

    }
}
