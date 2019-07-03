<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141028095528 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE ebi_permissionset_datablock (id INT AUTO_INCREMENT NOT NULL, ebi_permissionset_id INT DEFAULT NULL, datablock_id INT DEFAULT NULL, timeframe_all TINYINT(1) DEFAULT NULL, current_calendar TINYINT(1) DEFAULT NULL, previous_period TINYINT(1) DEFAULT NULL, block_type VARCHAR(255) DEFAULT NULL, INDEX IDX_1156882127C1FF01 (ebi_permissionset_id), INDEX IDX_11568821F9AE3580 (datablock_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ebi_permissionset_features (id INT AUTO_INCREMENT NOT NULL, ebi_permissionset_id INT DEFAULT NULL, feature_id INT DEFAULT NULL, private_create TINYINT(1) DEFAULT NULL, team_create TINYINT(1) DEFAULT NULL, team_view TINYINT(1) DEFAULT NULL, public_create TINYINT(1) DEFAULT NULL, public_view TINYINT(1) DEFAULT NULL, timeframe_all TINYINT(1) DEFAULT NULL, current_calendar TINYINT(1) DEFAULT NULL, previous_period TINYINT(1) DEFAULT NULL, next_period TINYINT(1) DEFAULT NULL, receive_referral TINYINT(1) DEFAULT NULL, INDEX IDX_29B6BC9027C1FF01 (ebi_permissionset_id), INDEX IDX_29B6BC9060E4B879 (feature_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ebi_permissionset (id INT AUTO_INCREMENT NOT NULL, access_level VARCHAR(10) DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, risk_indicator TINYINT(1) DEFAULT NULL, intent_to_leave TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ebi_permissionset_lang (id INT AUTO_INCREMENT NOT NULL, language_id INT DEFAULT NULL, ebi_permissionset_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, permissionset_name VARCHAR(100) DEFAULT NULL, INDEX IDX_6C7D6BF982F1BAF4 (language_id), INDEX IDX_6C7D6BF927C1FF01 (ebi_permissionset_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ebi_permissionset_datablock ADD CONSTRAINT FK_1156882127C1FF01 FOREIGN KEY (ebi_permissionset_id) REFERENCES ebi_permissionset (id)');
        $this->addSql('ALTER TABLE ebi_permissionset_datablock ADD CONSTRAINT FK_11568821F9AE3580 FOREIGN KEY (datablock_id) REFERENCES datablock_master (id)');
        $this->addSql('ALTER TABLE ebi_permissionset_features ADD CONSTRAINT FK_29B6BC9027C1FF01 FOREIGN KEY (ebi_permissionset_id) REFERENCES ebi_permissionset (id)');
        $this->addSql('ALTER TABLE ebi_permissionset_features ADD CONSTRAINT FK_29B6BC9060E4B879 FOREIGN KEY (feature_id) REFERENCES feature_master (id)');
        $this->addSql('ALTER TABLE ebi_permissionset_lang ADD CONSTRAINT FK_6C7D6BF982F1BAF4 FOREIGN KEY (language_id) REFERENCES language_master (id)');
        $this->addSql('ALTER TABLE ebi_permissionset_lang ADD CONSTRAINT FK_6C7D6BF927C1FF01 FOREIGN KEY (ebi_permissionset_id) REFERENCES ebi_permissionset (id)');
        $this->addSql('ALTER TABLE org_permissionset ADD accesslevel_ind_agg TINYINT(1) DEFAULT NULL, ADD accesslevel_agg TINYINT(1) DEFAULT NULL, ADD risk_indicator TINYINT(1) DEFAULT NULL, ADD intent_to_leave TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE org_permissionset_features ADD private_create TINYINT(1) DEFAULT NULL, ADD team_create TINYINT(1) DEFAULT NULL, ADD team_view TINYINT(1) DEFAULT NULL, ADD public_create TINYINT(1) DEFAULT NULL, ADD public_view TINYINT(1) DEFAULT NULL, ADD receive_referral TINYINT(1) DEFAULT NULL, DROP private_access, DROP team_access, DROP connected_access');
       
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE ebi_permissionset_datablock DROP FOREIGN KEY FK_1156882127C1FF01');
        $this->addSql('ALTER TABLE ebi_permissionset_features DROP FOREIGN KEY FK_29B6BC9027C1FF01');
        $this->addSql('ALTER TABLE ebi_permissionset_lang DROP FOREIGN KEY FK_6C7D6BF927C1FF01');
        $this->addSql('DROP TABLE ebi_permissionset_datablock');
        $this->addSql('DROP TABLE ebi_permissionset_features');
        $this->addSql('DROP TABLE ebi_permissionset');
        $this->addSql('DROP TABLE ebi_permissionset_lang');
        $this->addSql('ALTER TABLE org_permissionset DROP accesslevel_ind_agg, DROP accesslevel_agg, DROP risk_indicator, DROP intent_to_leave');
        $this->addSql('ALTER TABLE org_permissionset_features ADD private_access VARCHAR(20) DEFAULT NULL, ADD team_access VARCHAR(20) DEFAULT NULL, ADD connected_access VARCHAR(20) DEFAULT NULL, DROP private_create, DROP team_create, DROP team_view, DROP public_create, DROP public_view, DROP receive_referral');
       
    }
}
