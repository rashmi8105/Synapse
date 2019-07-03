<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151201095057 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
       
        $orgSql = 'update `org_permissionset_features` set 
                   reason_referral_public_create =1, reason_referral_public_view =1, 
                   reason_referral_private_create =1, reason_referral_team_create =1, reason_referral_team_view =1

                   where feature_id=1 and deleted_at is null and reason_referral_public_create is null and 
                   reason_referral_public_view is null and reason_referral_private_create is null and
                   reason_referral_team_create is null and reason_referral_team_view is null'; 
        
        $ebiSql = 'update `ebi_permissionset_features` set
                   reason_referral_public_create =1, reason_referral_public_view =1,
                   reason_referral_private_create =1, reason_referral_team_create =1, reason_referral_team_view =1
                   
                   where feature_id=1 and reason_referral_public_create is null and 
                   reason_referral_public_view is null and reason_referral_private_create is null and
                   reason_referral_team_create is null and reason_referral_team_view is null'; 
        
        $this->addSql($orgSql);
        $this->addSql($ebiSql);
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

   }
}
