<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140912123326 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE IF NOT EXISTS org_features (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, feature_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, private TINYINT(1) DEFAULT NULL, connected TINYINT(1) DEFAULT NULL, team TINYINT(1) DEFAULT NULL, default_access VARCHAR(45) DEFAULT NULL, INDEX IDX_C36F4CF032C8A3DE (organization_id), INDEX IDX_C36F4CF060E4B879 (feature_id), PRIMARY KEY(id),KEY organizationfeatures_organizationid (organization_id),KEY organizationfeature_featureid (feature_id), CONSTRAINT organizationfeature_featureid FOREIGN KEY (feature_id) REFERENCES feature_master (id) ON DELETE NO ACTION ON UPDATE NO ACTION,CONSTRAINT organizationfeatures_organizationid FOREIGN KEY (organization_id) REFERENCES organization (id) ON DELETE NO ACTION ON UPDATE NO ACTION) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
       
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE org_features');
       
    }
}
