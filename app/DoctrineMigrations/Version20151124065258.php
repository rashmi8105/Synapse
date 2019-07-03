<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151124065258 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ebi_permissionset_features ADD reason_referral_private_create TINYINT(1) DEFAULT NULL, ADD reason_referral_team_create TINYINT(1) DEFAULT NULL, ADD reason_referral_team_view TINYINT(1) DEFAULT NULL, ADD reason_referral_public_create TINYINT(1) DEFAULT NULL, ADD reason_referral_public_view TINYINT(1) DEFAULT NULL');
   
        $this->addSql('ALTER TABLE org_permissionset_features ADD reason_referral_private_create TINYINT(1) DEFAULT NULL, ADD reason_referral_team_create TINYINT(1) DEFAULT NULL, ADD reason_referral_team_view TINYINT(1) DEFAULT NULL, ADD reason_referral_public_create TINYINT(1) DEFAULT NULL, ADD reason_referral_public_view TINYINT(1) DEFAULT NULL');
       
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');


        $this->addSql('ALTER TABLE ebi_permissionset_features DROP reason_referral_private_create, DROP reason_referral_team_create, DROP reason_referral_team_view, DROP reason_referral_public_create, DROP reason_referral_public_view');
     
        $this->addSql('ALTER TABLE org_permissionset_features DROP reason_referral_private_create, DROP reason_referral_team_create, DROP reason_referral_team_view, DROP reason_referral_public_create, DROP reason_referral_public_view');
       
        
    }
}
