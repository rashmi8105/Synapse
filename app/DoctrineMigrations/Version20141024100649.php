<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141024100649 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE org_permissionset_features (id INT AUTO_INCREMENT NOT NULL, feature_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, org_permissionset_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, private_access VARCHAR(20) DEFAULT NULL, team_access VARCHAR(20) DEFAULT NULL, connected_access VARCHAR(20) DEFAULT NULL, timeframe_all TINYINT(1) DEFAULT NULL, current_calendar TINYINT(1) DEFAULT NULL, previous_period TINYINT(1) DEFAULT NULL, next_period TINYINT(1) DEFAULT NULL, INDEX IDX_53F293C360E4B879 (feature_id), INDEX IDX_53F293C332C8A3DE (organization_id), INDEX IDX_53F293C37ABB76BC (org_permissionset_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_permissionset_features ADD CONSTRAINT FK_53F293C360E4B879 FOREIGN KEY (feature_id) REFERENCES feature_master (id)');
        $this->addSql('ALTER TABLE org_permissionset_features ADD CONSTRAINT FK_53F293C332C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_permissionset_features ADD CONSTRAINT FK_53F293C37ABB76BC FOREIGN KEY (org_permissionset_id) REFERENCES org_permissionset (id)');
       
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE org_permissionset_features');
        
    }
}
