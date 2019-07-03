<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160106200727 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            update synapse.org_permissionset_features
            set 	reason_referral_private_create = private_create,
                reason_referral_team_create = team_create,
                reason_referral_team_view = team_view,
                reason_referral_public_create = public_create,
                reason_referral_public_view = public_view;
            update synapse.ebi_permissionset_features
            set 	reason_referral_private_create = private_create,
                reason_referral_team_create = team_create,
                reason_referral_team_view = team_view,
                reason_referral_public_create = public_create,
                reason_referral_public_view = public_view;
        ");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
