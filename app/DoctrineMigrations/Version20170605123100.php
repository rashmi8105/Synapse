<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15087 - Migration script for updating notifications for interested party
 */
class Version20170605123100 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        //variable declaration
        $creator_first_name = '$$creator_first_name$$';
        $creator_last_name = '$$creator_last_name$$';
        $referral_student_count = '$$referral_student_count$$';

        // updating  the mapworks action table for referral_bulk_action_interested_party
        $this->addSql("UPDATE 
                             mapworks_action
                        SET                            
                             notification_hover_text = '$creator_first_name $creator_last_name has added you as an interested party on $referral_student_count referrals.',
                             notification_body_text = '$creator_first_name $creator_last_name has added you as an interested party on $referral_student_count referrals.'
                        WHERE
                             event_key = 'referral_bulk_action_interested_party';"
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
